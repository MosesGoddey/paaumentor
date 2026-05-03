<?php

namespace App\Http\Controllers;

use App\Models\{User, MentorSession};
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $mentorships = $user->isMentee()
            ? $user->menteeMentorships()->with('mentor')->where('status', 'active')->get()
            : $user->mentorMentorships()->with('mentee')->where('status', 'active')->get();

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

        $learningPaths = $user->learningPathsAsMentee()
                              ->with(['modules.tasks'])
                              ->where('status', '!=', 'archived')
                              ->get()
                              ->map(fn($lp) => ['path' => $lp, 'progress' => $lp->progress]);

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
            'notifications', 'engagement', 'pendingRequests'
        ));
    }
}
