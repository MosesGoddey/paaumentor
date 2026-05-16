<?php

namespace App\Http\Controllers;

use App\Models\{Hackathon, HackathonTeam, HackathonSubmission, HackathonScore, Certificate, Notification, Mentorship};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class HackathonController extends Controller
{
    // ── Listing ────────────────────────────────────────────────────────────────

    public function index()
    {
        $user = Auth::user();
        $query = Hackathon::latest();

        if (!$user->isAdmin() && !$user->isVerifier()) {
            $query->where('status', '!=', 'draft');
        }

        $hackathons = $query->withCount('teams')->get()->map(function ($h) use ($user) {
            $myTeam = $h->teams()->whereHas('users', fn($q) => $q->where('user_id', $user->id))->first();
            return ['hackathon' => $h, 'myTeam' => $myTeam];
        });

        return view('hackathons.index', compact('hackathons', 'user'));
    }

    // ── Admin: Create / Store ──────────────────────────────────────────────────

    public function create()
    {
        abort_unless(Auth::user()->isAdmin() || Auth::user()->isVerifier(), 403);
        return view('hackathons.create');
    }

    public function store(Request $request)
    {
        abort_unless(Auth::user()->isAdmin() || Auth::user()->isVerifier(), 403);

        $data = $request->validate([
            'title'                   => 'required|string|max:255',
            'description'             => 'nullable|string',
            'theme'                   => 'nullable|string|max:255',
            'rules'                   => 'nullable|string',
            'tracks'                  => 'nullable|string',
            'prizes'                  => 'nullable|string',
            'registration_deadline'   => 'nullable|date',
            'start_date'              => 'nullable|date',
            'end_date'                => 'nullable|date',
            'max_team_size'           => 'required|integer|min:1|max:10',
        ]);

        $data['tracks']     = $data['tracks']
            ? array_values(array_filter(array_map('trim', explode(',', $data['tracks']))))
            : null;
        $data['created_by'] = Auth::id();

        Hackathon::create($data);

        return redirect()->route('hackathons.index')->with('success', 'Hackathon created! Publish it when ready.');
    }

    // ── Show ───────────────────────────────────────────────────────────────────

    public function show(Hackathon $hackathon)
    {
        $user = Auth::user();
        abort_if(
            $hackathon->status === 'draft' && !$user->isAdmin() && !$user->isVerifier(),
            404
        );

        $hackathon->load(['teams.users', 'teams.submission', 'teams.coach', 'creator']);

        $myTeam  = $hackathon->teams->first(fn($t) => $t->users->contains('id', $user->id));
        $isJudge = $hackathon->isJudge($user);
        $isAdmin = $user->isAdmin() || $user->isVerifier();

        // Mentors: show teams that need a coach
        $teamsNeedingCoach = [];
        if ($user->isMentor()) {
            $teamsNeedingCoach = $hackathon->teams->filter(
                fn($t) => $t->coach_id === null && !$t->users->contains('id', $user->id)
            )->values();
        }

        // List of verified mentors/alumni for judge assignment (admin only)
        $potentialJudges = [];
        if ($isAdmin) {
            $potentialJudges = \App\Models\User::whereIn('role', ['mentor', 'alumni', 'verifier'])
                ->where('is_verified', true)->orderBy('first_name')->get();
        }

        return view('hackathons.show', compact(
            'hackathon', 'myTeam', 'isJudge', 'isAdmin', 'teamsNeedingCoach', 'potentialJudges', 'user'
        ));
    }

    // ── Team: Create ───────────────────────────────────────────────────────────

    public function createTeam(Request $request, Hackathon $hackathon)
    {
        $user = Auth::user();
        abort_unless(in_array($hackathon->status, ['open', 'ongoing']), 422, 'Team creation is not open.');

        $alreadyIn = $hackathon->teams()->whereHas('users', fn($q) => $q->where('user_id', $user->id))->exists();
        if ($alreadyIn) return back()->with('error', 'You are already in a team for this hackathon.');

        $data = $request->validate([
            'name'  => 'required|string|max:100',
            'track' => 'nullable|string|max:100',
        ]);

        $team = HackathonTeam::create([
            'hackathon_id' => $hackathon->id,
            'name'         => $data['name'],
            'track'        => $data['track'] ?? null,
            'join_code'    => Str::upper(Str::random(6)),
        ]);

        $team->users()->attach($user->id, ['is_lead' => true]);

        // Auto-assign coach from existing active mentorship
        $mentorship = Mentorship::where('mentee_id', $user->id)->where('status', 'active')->first();
        if ($mentorship) {
            $team->update(['coach_id' => $mentorship->mentor_id, 'coach_status' => 'accepted']);

            Notification::create([
                'user_id' => $mentorship->mentor_id,
                'type'    => 'hackathon_coach',
                'title'   => 'You\'ve been assigned as a hackathon coach!',
                'body'    => "Your mentee {$user->full_name} formed team \"{$team->name}\" in {$hackathon->title}. You are their coach.",
                'data'    => ['hackathon_id' => $hackathon->id, 'team_id' => $team->id],
            ]);
        }

        return redirect()->route('hackathons.team', $hackathon)
                         ->with('success', 'Team created! Share your join code with teammates.');
    }

    // ── Team: Join ─────────────────────────────────────────────────────────────

    public function joinTeam(Request $request, Hackathon $hackathon)
    {
        $user = Auth::user();
        abort_unless(in_array($hackathon->status, ['open', 'ongoing']), 422);

        $alreadyIn = $hackathon->teams()->whereHas('users', fn($q) => $q->where('user_id', $user->id))->exists();
        if ($alreadyIn) return back()->with('error', 'You are already in a team.');

        $code = strtoupper(trim($request->join_code ?? ''));
        $team = HackathonTeam::where('hackathon_id', $hackathon->id)->where('join_code', $code)->first();

        if (!$team)                       return back()->with('error', 'Invalid join code.');
        if ($team->is_locked)             return back()->with('error', 'This team is locked.');
        if ($team->users()->count() >= $hackathon->max_team_size) {
            return back()->with('error', 'This team is full (max ' . $hackathon->max_team_size . ' members).');
        }

        $team->users()->attach($user->id, ['is_lead' => false]);

        $lead = $team->users()->wherePivot('is_lead', true)->first();
        if ($lead) {
            Notification::create([
                'user_id' => $lead->id,
                'type'    => 'hackathon_join',
                'title'   => 'New teammate joined!',
                'body'    => "{$user->full_name} joined your team \"{$team->name}\" for {$hackathon->title}.",
                'data'    => ['hackathon_id' => $hackathon->id, 'team_id' => $team->id],
            ]);
        }

        return redirect()->route('hackathons.team', $hackathon)
                         ->with('success', "You joined \"{$team->name}\"!");
    }

    // ── Team: Workspace ────────────────────────────────────────────────────────

    public function myTeam(Hackathon $hackathon)
    {
        $user   = Auth::user();
        $myTeam = $hackathon->teams()
            ->whereHas('users', fn($q) => $q->where('user_id', $user->id))
            ->with(['users', 'coach', 'submission.scores.judge'])
            ->first();

        abort_unless($myTeam, 403, 'You are not in a team for this hackathon.');

        $isLead = (bool) $myTeam->users->firstWhere('id', $user->id)?->pivot->is_lead;

        return view('hackathons.team', compact('hackathon', 'myTeam', 'isLead', 'user'));
    }

    // ── Submit Project ─────────────────────────────────────────────────────────

    public function submitProject(Request $request, Hackathon $hackathon)
    {
        $user = Auth::user();
        $team = $hackathon->teams()
            ->whereHas('users', fn($q) => $q->where('user_id', $user->id))
            ->with('users')
            ->first();

        abort_unless($team, 403);
        $isLead = (bool) $team->users->firstWhere('id', $user->id)?->pivot->is_lead;
        abort_unless($isLead, 403, 'Only the team lead can submit the project.');
        abort_unless($hackathon->status === 'ongoing', 422, 'Submissions are not open.');

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string|max:3000',
            'github_url'  => 'nullable|url|max:255',
            'demo_url'    => 'nullable|url|max:255',
            'deck_url'    => 'nullable|url|max:255',
            'action'      => 'required|in:save,submit',
        ]);

        $status = $data['action'] === 'submit' ? 'submitted' : 'draft';

        HackathonSubmission::updateOrCreate(
            ['hackathon_id' => $hackathon->id, 'team_id' => $team->id],
            [
                'title'        => $data['title'],
                'description'  => $data['description'],
                'github_url'   => $data['github_url'] ?? null,
                'demo_url'     => $data['demo_url'] ?? null,
                'deck_url'     => $data['deck_url'] ?? null,
                'status'       => $status,
                'submitted_at' => $status === 'submitted' ? now() : null,
            ]
        );

        $msg = $status === 'submitted'
            ? 'Project submitted successfully! Good luck.'
            : 'Draft saved.';

        return back()->with('success', $msg);
    }

    // ── Judge Panel ────────────────────────────────────────────────────────────

    public function judgePanel(Hackathon $hackathon)
    {
        $user = Auth::user();
        abort_unless($hackathon->isJudge($user) || $user->isAdmin() || $user->isVerifier(), 403);

        $submissions = $hackathon->submissions()
            ->where('status', 'submitted')
            ->with(['team.users', 'scores.judge'])
            ->get();

        $myScores = HackathonScore::where('judge_id', $user->id)
            ->whereIn('submission_id', $submissions->pluck('id'))
            ->keyBy('submission_id');

        return view('hackathons.judge', compact('hackathon', 'submissions', 'myScores', 'user'));
    }

    // ── Score a Submission ─────────────────────────────────────────────────────

    public function score(Request $request, HackathonSubmission $submission)
    {
        $user      = Auth::user();
        $hackathon = $submission->hackathon;

        abort_unless($hackathon->isJudge($user) || $user->isAdmin() || $user->isVerifier(), 403);
        abort_unless($hackathon->status === 'judging', 422, 'Judging is not open yet.');

        $submission->load('team.users');
        abort_if($submission->team->users->contains('id', $user->id), 403, 'You cannot score your own team.');

        $data = $request->validate([
            'innovation'   => 'required|integer|min:1|max:10',
            'execution'    => 'required|integer|min:1|max:10',
            'impact'       => 'required|integer|min:1|max:10',
            'presentation' => 'required|integer|min:1|max:10',
            'notes'        => 'nullable|string|max:1000',
        ]);

        HackathonScore::updateOrCreate(
            ['submission_id' => $submission->id, 'judge_id' => $user->id],
            $data
        );

        return back()->with('success', 'Score saved!');
    }

    // ── Leaderboard ────────────────────────────────────────────────────────────

    public function leaderboard(Hackathon $hackathon)
    {
        $user = Auth::user();

        $submissions = $hackathon->submissions()
            ->where('status', 'submitted')
            ->with(['team.users', 'scores'])
            ->get()
            ->sortByDesc(fn($s) => $s->average_score)
            ->values();

        $myTeam = $hackathon->teams()
            ->whereHas('users', fn($q) => $q->where('user_id', $user->id))
            ->first();

        return view('hackathons.leaderboard', compact('hackathon', 'submissions', 'myTeam', 'user'));
    }

    // ── Admin: Status Transitions ──────────────────────────────────────────────

    public function updateStatus(Request $request, Hackathon $hackathon)
    {
        abort_unless(Auth::user()->isAdmin() || Auth::user()->isVerifier(), 403);

        $allowed = ['draft', 'open', 'ongoing', 'judging'];
        $next = $request->validate(['status' => 'required|in:' . implode(',', $allowed)])['status'];

        $hackathon->update(['status' => $next]);

        if ($next === 'open') {
            // Notify all verified users
            \App\Models\User::where('is_verified', true)->where('is_active', true)
                ->chunk(100, function ($users) use ($hackathon) {
                    foreach ($users as $u) {
                        Notification::create([
                            'user_id' => $u->id,
                            'type'    => 'hackathon_open',
                            'title'   => 'New Hackathon Open!',
                            'body'    => "\"{$hackathon->title}\" is now open for registration. Form your team!",
                            'data'    => ['hackathon_id' => $hackathon->id],
                        ]);
                    }
                });
        }

        return back()->with('success', 'Status updated to ' . $next . '.');
    }

    // ── Admin: Assign Judge ────────────────────────────────────────────────────

    public function assignJudge(Request $request, Hackathon $hackathon)
    {
        abort_unless(Auth::user()->isAdmin() || Auth::user()->isVerifier(), 403);

        $data   = $request->validate(['user_id' => 'required|exists:users,id']);
        $judges = $hackathon->judge_ids ?? [];

        if (!in_array($data['user_id'], $judges)) {
            $judges[] = (int) $data['user_id'];
            $hackathon->update(['judge_ids' => $judges]);

            Notification::create([
                'user_id' => $data['user_id'],
                'type'    => 'hackathon_judge',
                'title'   => 'You\'ve been assigned as a judge!',
                'body'    => "You are a judge for \"{$hackathon->title}\". You can score submissions during the judging phase.",
                'data'    => ['hackathon_id' => $hackathon->id],
            ]);
        }

        return back()->with('success', 'Judge assigned.');
    }

    // ── Admin: Publish Results ─────────────────────────────────────────────────

    public function publishResults(Hackathon $hackathon)
    {
        $user = Auth::user();
        abort_unless($user->isAdmin() || $user->isVerifier(), 403);
        abort_unless($hackathon->status === 'judging', 422, 'Move hackathon to judging phase first.');

        $submissions = $hackathon->submissions()
            ->where('status', 'submitted')
            ->with(['team.users', 'scores'])
            ->get()
            ->sortByDesc(fn($s) => $s->average_score)
            ->values();

        $placements = [0 => '1st', 1 => '2nd', 2 => '3rd'];

        foreach ($submissions as $index => $submission) {
            $placement = $placements[$index] ?? 'participant';

            foreach ($submission->team->users as $member) {
                // Avoid duplicate certs
                $exists = Certificate::where('user_id', $member->id)
                                     ->where('hackathon_team_id', $submission->team_id)
                                     ->exists();
                if ($exists) continue;

                $seq    = str_pad(Certificate::where('type', 'hackathon')->count() + 1, 5, '0', STR_PAD_LEFT);
                $certId = 'PAAU-HK-' . date('Y') . '-' . $seq;

                Certificate::create([
                    'user_id'          => $member->id,
                    'hackathon_team_id'=> $submission->team_id,
                    'type'             => 'hackathon',
                    'certificate_id'   => $certId,
                    'placement'        => $placement,
                    'issued_at'        => now(),
                ]);

                $placementMsg = $placement === 'participant'
                    ? 'Thanks for participating in'
                    : "Congratulations on placing {$placement} in";

                Notification::create([
                    'user_id' => $member->id,
                    'type'    => 'hackathon_cert',
                    'title'   => 'Hackathon Certificate Ready!',
                    'body'    => "{$placementMsg} {$hackathon->title}! Your certificate is available for download.",
                    'data'    => ['hackathon_id' => $hackathon->id],
                ]);
            }
        }

        $hackathon->update(['status' => 'completed']);

        return redirect()->route('hackathons.leaderboard', $hackathon)
                         ->with('success', 'Results published! Certificates issued to all participants.');
    }

    // ── Mentor: Volunteer as Coach ─────────────────────────────────────────────

    public function volunteerCoach(HackathonTeam $team)
    {
        $user = Auth::user();
        abort_unless($user->isMentor(), 403);
        abort_if($team->coach_id !== null, 422, 'This team already has a coach.');
        abort_if($team->users->contains('id', $user->id), 403, 'You cannot coach a team you are in.');

        $team->update(['coach_id' => $user->id, 'coach_status' => 'pending']);

        $lead = $team->users()->wherePivot('is_lead', true)->first();
        if ($lead) {
            Notification::create([
                'user_id' => $lead->id,
                'type'    => 'coach_request',
                'title'   => 'Coach Request!',
                'body'    => "{$user->full_name} wants to coach your team \"{$team->name}\".",
                'data'    => ['team_id' => $team->id],
            ]);
        }

        return back()->with('success', 'Coach request sent! The team lead will be notified.');
    }

    // ── Team Lead: Respond to Coach Request ────────────────────────────────────

    public function respondCoach(Request $request, HackathonTeam $team)
    {
        $user   = Auth::user();
        $isLead = $team->users()->wherePivot('is_lead', true)->where('user_id', $user->id)->exists();
        abort_unless($isLead, 403);

        $action = $request->validate(['action' => 'required|in:accept,decline'])['action'];

        if ($action === 'accept') {
            $team->update(['coach_status' => 'accepted']);
            $msg = 'Coach accepted!';
        } else {
            $team->update(['coach_id' => null, 'coach_status' => null]);
            $msg = 'Coach request declined.';
        }

        return back()->with('success', $msg);
    }
}
