<?php

namespace App\Http\Controllers;

use App\Models\{User, MentorSession};
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Operators have their own home screens — the participant dashboard
        // (mentors, sessions, learning paths) is meaningless for them.
        if ($user->isAdmin())    return redirect()->route('admin.dashboard');
        if ($user->isVerifier()) return redirect()->route('verifier.index');

        $mentorships = $user->isMentee()
            ? $user->menteeMentorships()->with('mentor')->where('status', 'active')->get()
            : $user->mentorMentorships()
                   ->with(['mentee', 'sessions' => fn($q) => $q->where('status', 'completed')->orderByDesc('scheduled_at')])
                   ->where('status', 'active')->get();

        $matches = [];
        if ($user->isMentee()) {
            $mentors = User::where('role', '!=', 'mentee')
                           ->where('role', '!=', 'admin')
                           ->where('is_verified', true)
                           ->where('is_active', true)
                           ->with(['hasSkills', 'ratings'])
                           ->get();

            $matches = $mentors->map(fn($m) => [
                'user'  => $m,
                'score' => $m->matchScore($user),
            ])->sortByDesc('score')->take(3)->values();
        }

        $pathsRelation = $user->isMentor()
            ? $user->learningPathsAsmentor()->with(['modules.tasks', 'mentee'])
            : $user->learningPathsAsMentee()->with(['modules.tasks']);

        $learningPaths = $pathsRelation
                              ->where('status', '!=', 'archived')
                              ->get()
                              ->map(fn($lp) => ['path' => $lp, 'progress' => $lp->progress]);

        // Mentee-id → path progress, used to differentiate mentee rows on mentor dashboards
        $pathProgressByMentee = $user->isMentor()
            ? collect($learningPaths)->mapWithKeys(fn($lp) => [$lp['path']->mentee_id => $lp['progress']])
            : collect();

        $upcomingSessions = MentorSession::whereHas('mentorship', function ($q) use ($user) {
            $q->where('mentor_id', $user->id)->orWhere('mentee_id', $user->id);
        })->where('status', 'scheduled')
          ->where('scheduled_at', '>=', now())
          ->orderBy('scheduled_at')
          ->with('mentorship.mentor', 'mentorship.mentee')
          ->take(3)
          ->get();

        $sessionCount = MentorSession::whereHas('mentorship', function ($q) use ($user) {
            $q->where('mentor_id', $user->id)->orWhere('mentee_id', $user->id);
        })->where('status', 'completed')->count();

        $sessionsByType = MentorSession::whereHas('mentorship', function ($q) use ($user) {
            $q->where('mentor_id', $user->id)->orWhere('mentee_id', $user->id);
        })->where('status', 'completed')
          ->selectRaw('type, COUNT(*) as count')
          ->groupBy('type')
          ->pluck('count', 'type');

        $certificates = $user->certificates()->with('learningPath')->get();

        // "+N this month" trend hints for the KPI cards
        $monthStart = now()->startOfMonth();
        $kpiTrends = [
            'mentorships'  => ($user->isMentee() ? $user->menteeMentorships() : $user->mentorMentorships())
                                  ->where('status', 'active')->where('created_at', '>=', $monthStart)->count(),
            'sessions'     => MentorSession::whereHas('mentorship', function ($q) use ($user) {
                                  $q->where('mentor_id', $user->id)->orWhere('mentee_id', $user->id);
                              })->where('status', 'completed')->where('scheduled_at', '>=', $monthStart)->count(),
            'paths'        => ($user->isMentor() ? $user->learningPathsAsmentor() : $user->learningPathsAsMentee())
                                  ->where('created_at', '>=', $monthStart)->count(),
            'certificates' => $user->certificates()->where('issued_at', '>=', $monthStart)->count(),
        ];

        $pendingRequests = $user->isMentor()
            ? $user->mentorMentorships()->with('mentee')->where('status', 'pending')->latest()->get()
            : collect();

        $notifications = $user->notifications()->latest()->take(10)->get();

        $engagement = MentorSession::whereHas('mentorship', function ($q) use ($user) {
            $q->where('mentor_id', $user->id)->orWhere('mentee_id', $user->id);
        })->where('status', 'completed')
          ->where('created_at', '>=', now()->subMonths(6))
          ->selectRaw("DATE_FORMAT(scheduled_at, '%b') as month, COUNT(*) as count")
          ->groupByRaw("DATE_FORMAT(scheduled_at, '%b'), MONTH(scheduled_at)")
          ->orderByRaw("MONTH(scheduled_at)")
          ->pluck('count', 'month');

        return view('dashboard.index', compact(
            'user', 'mentorships', 'matches', 'learningPaths',
            'upcomingSessions', 'sessionCount', 'sessionsByType', 'certificates',
            'notifications', 'engagement', 'pendingRequests',
            'kpiTrends', 'pathProgressByMentee'
        ));
    }
}
