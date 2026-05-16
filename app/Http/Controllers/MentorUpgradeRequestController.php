<?php

namespace App\Http\Controllers;

use App\Models\{MentorUpgradeRequest, MentorSession, Mentorship, Notification, UpgradeAssessment};
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MentorUpgradeRequestController extends Controller
{
    // Mentee: show apply page with requirements checklist
    public function show()
    {
        $user = Auth::user();
        abort_unless($user->isMentee(), 403);

        $completedSessions = MentorSession::whereHas('mentorship', fn($q) => $q->where('mentee_id', $user->id))
            ->where('status', 'completed')
            ->count();

        $completedPaths = $user->learningPathsAsMentee()->get()->filter(fn($p) => $p->isComplete())->count();

        $activeMentor = Mentorship::where('mentee_id', $user->id)
            ->where('status', 'active')
            ->with('mentor')
            ->first();

        $accountAgeDays = $user->created_at->diffInDays(now());

        $certificates = $user->certificates()->count();

        $requirements = [
            'sessions'     => ['met' => $completedSessions >= 5,  'label' => 'Complete at least 5 mentoring sessions',    'current' => $completedSessions, 'target' => 5],
            'paths'        => ['met' => $completedPaths >= 1,      'label' => 'Complete at least 1 learning path',         'current' => $completedPaths,    'target' => 1],
            'certificates' => ['met' => $certificates >= 1,        'label' => 'Earn at least 1 certificate',               'current' => $certificates,      'target' => 1],
            'account_age'  => ['met' => $accountAgeDays >= 1,      'label' => 'Account must be at least 1 day old',        'current' => $accountAgeDays,    'target' => 1],
            'mentor'       => ['met' => $activeMentor !== null,    'label' => 'Have an active mentor to recommend you',    'current' => $activeMentor ? 1 : 0, 'target' => 1],
        ];

        $allMet = collect($requirements)->every(fn($r) => $r['met']);

        $existing = MentorUpgradeRequest::where('mentee_id', $user->id)
            ->whereIn('status', ['pending_assessment', 'pending', 'recommended'])
            ->first();

        return view('upgrade.apply', compact('requirements', 'allMet', 'existing', 'activeMentor'));
    }

    // Mentee: submit application
    public function apply(Request $request)
    {
        $user = Auth::user();
        abort_unless($user->isMentee(), 403);

        // Block duplicate active requests
        $existing = MentorUpgradeRequest::where('mentee_id', $user->id)
            ->whereIn('status', ['pending_assessment', 'pending', 'recommended'])
            ->exists();
        abort_if($existing, 422, 'You already have a pending upgrade request.');

        $completedSessions = MentorSession::whereHas('mentorship', fn($q) => $q->where('mentee_id', $user->id))
            ->where('status', 'completed')->count();

        $completedPaths = $user->learningPathsAsMentee()->get()->filter(fn($p) => $p->isComplete())->count();

        $activeMentor = Mentorship::where('mentee_id', $user->id)->where('status', 'active')->first();

        $accountAgeDays = $user->created_at->diffInDays(now());

        $certificates = $user->certificates()->count();

        if ($completedSessions < 5 || $completedPaths < 1 || $certificates < 1 || $accountAgeDays < 1 || !$activeMentor) {
            return back()->with('error', 'You do not meet all requirements yet.');
        }

        // Save portfolio fields to the user's profile
        $portfolio = $request->validate([
            'github_url'    => 'nullable|url|max:255',
            'linkedin_url'  => 'nullable|url|max:255',
            'portfolio_bio' => 'nullable|string|max:1000',
        ]);
        $user->update(array_filter([
            'github_url'   => $portfolio['github_url']   ?? null,
            'linkedin_url' => $portfolio['linkedin_url'] ?? null,
            'bio'          => $portfolio['portfolio_bio'] ?? $user->bio,
        ]));

        $upgradeRequest = MentorUpgradeRequest::create([
            'mentee_id' => $user->id,
            'mentor_id' => $activeMentor->mentor_id,
            'status'    => 'pending_assessment',
        ]);

        // Create the upgrade assessment and generate questions via Gemini
        $assessment = UpgradeAssessment::create([
            'upgrade_request_id'   => $upgradeRequest->id,
            'passing_score'        => 70,
            'time_per_question'    => 90,
            'questions_per_attempt'=> 15,
        ]);

        $gemini = new GeminiService();
        $gemini->generateUpgradeQuestions($assessment);

        // Notify the mentee to take the assessment
        Notification::create([
            'user_id' => $user->id,
            'type'    => 'upgrade_assessment_ready',
            'title'   => 'Assessment Ready — Step 1 of Your Upgrade',
            'body'    => 'Your upgrade application has been received. Complete the knowledge assessment to proceed to the recommendation stage.',
            'data'    => ['upgrade_request_id' => $upgradeRequest->id],
        ]);

        return redirect()->route('upgrade-assessment.show', $upgradeRequest)
            ->with('success', 'Application submitted! Complete the knowledge assessment below to continue.');
    }

    // Mentor: show recommendation form
    public function recommendForm(MentorUpgradeRequest $upgradeRequest)
    {
        abort_unless(Auth::id() === $upgradeRequest->mentor_id, 403);
        abort_unless($upgradeRequest->isPending(), 422);
        return view('upgrade.recommend', compact('upgradeRequest'));
    }

    // Mentor: submit recommendation
    public function recommend(Request $request, MentorUpgradeRequest $upgradeRequest)
    {
        abort_unless(Auth::id() === $upgradeRequest->mentor_id, 403);
        abort_unless($upgradeRequest->isPending(), 422);

        $data = $request->validate([
            'mentor_note' => 'required|string|min:20|max:1000',
        ]);

        $upgradeRequest->update([
            'status'                 => 'recommended',
            'mentor_note'            => $data['mentor_note'],
            'mentor_recommended_at'  => now(),
        ]);

        // Notify admins and verifiers
        \App\Models\User::whereIn('role', ['admin', 'verifier'])->each(function ($admin) use ($upgradeRequest) {
            Notification::create([
                'user_id' => $admin->id,
                'type'    => 'upgrade_pending_review',
                'title'   => 'Mentor Upgrade Request',
                'body'    => "{$upgradeRequest->mentee->full_name} has been recommended for a mentor upgrade by {$upgradeRequest->mentor->full_name}.",
                'data'    => ['upgrade_request_id' => $upgradeRequest->id],
            ]);
        });

        return redirect()->route('sessions.index')->with('success', 'Recommendation submitted. An admin will review the request.');
    }

    // Admin/Verifier: list all requests
    public function adminIndex()
    {
        abort_unless(Auth::user()->isAdmin() || Auth::user()->isVerifier(), 403);

        $requests = MentorUpgradeRequest::with('mentee', 'mentor', 'admin')
            ->orderByRaw("FIELD(status, 'recommended', 'pending', 'approved', 'rejected')")
            ->orderByDesc('updated_at')
            ->get();

        return view('upgrade.admin', compact('requests'));
    }

    // Admin/Verifier: approve
    public function approve(Request $request, MentorUpgradeRequest $upgradeRequest)
    {
        abort_unless(Auth::user()->isAdmin() || Auth::user()->isVerifier(), 403);
        abort_unless($upgradeRequest->isRecommended(), 422);

        $data = $request->validate([
            'admin_note' => 'nullable|string|max:500',
        ]);

        $upgradeRequest->update([
            'status'      => 'approved',
            'admin_id'    => Auth::id(),
            'admin_note'  => $data['admin_note'] ?? null,
            'reviewed_at' => now(),
        ]);

        // Flip the mentee's role to mentor and mark portfolio as verified
        $upgradeRequest->mentee->update(['role' => 'mentor', 'mentor_status' => 'active']);

        // Notify the mentee
        Notification::create([
            'user_id' => $upgradeRequest->mentee_id,
            'type'    => 'upgrade_approved',
            'title'   => 'Upgrade Approved! ',
            'body'    => 'Congratulations! Your application to become a mentor has been approved. You are now a mentor.',
            'data'    => ['upgrade_request_id' => $upgradeRequest->id],
        ]);

        return back()->with('success', "{$upgradeRequest->mentee->full_name} has been upgraded to mentor.");
    }

    // Admin/Verifier: reject
    public function reject(Request $request, MentorUpgradeRequest $upgradeRequest)
    {
        abort_unless(Auth::user()->isAdmin() || Auth::user()->isVerifier(), 403);
        abort_unless(in_array($upgradeRequest->status, ['pending', 'recommended']), 422);

        $data = $request->validate([
            'admin_note' => 'required|string|max:500',
        ]);

        $upgradeRequest->update([
            'status'      => 'rejected',
            'admin_id'    => Auth::id(),
            'admin_note'  => $data['admin_note'],
            'reviewed_at' => now(),
        ]);

        // Notify the mentee
        Notification::create([
            'user_id' => $upgradeRequest->mentee_id,
            'type'    => 'upgrade_rejected',
            'title'   => 'Upgrade Request Update',
            'body'    => 'Your mentor upgrade request was not approved at this time. Check the admin note for details.',
            'data'    => ['upgrade_request_id' => $upgradeRequest->id, 'admin_note' => $data['admin_note']],
        ]);

        return back()->with('success', 'Request rejected and mentee has been notified.');
    }
}
