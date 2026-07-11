<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Certificate;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    public function mentors()
    {
        $mentors = User::query()
            ->whereIn('role', ['mentor', 'alumni'])
            ->where('is_active', true)
            ->where('mentor_status', 'active')
            ->withCount([
                'mentorMentorships as active_mentees_count' => fn($q) => $q->where('status', 'active'),
                'mentoredCertificates as certs_issued_count' => fn($q) => $q->where('certificates.type', 'mentee'),
            ])
            ->withAvg('ratings', 'score')
            ->get()
            ->map(function ($mentor) {
                $rating = round($mentor->ratings_avg_score ?? 0, 1);
                return (object) [
                    'id'              => $mentor->id,
                    'full_name'       => $mentor->full_name,
                    'role'            => $mentor->role,
                    'department'      => $mentor->department,
                    'tier'            => $mentor->mentor_tier,
                    'tier_label'      => $mentor->mentor_tier_label,
                    'avatar_url'      => $mentor->avatar_url,
                    'avatar_initials' => $mentor->initials,
                    'rating'          => $rating,
                    'active_mentees'  => $mentor->active_mentees_count,
                    'certs_issued'    => $mentor->certs_issued_count,
                    'score'           => $rating * 1.5 + $mentor->active_mentees_count + $mentor->certs_issued_count * 2,
                ];
            })
            ->sortByDesc('score')
            ->values();

        return view('leaderboards.mentors', compact('mentors'));
    }

    public function certificates()
    {
        $byDept = Certificate::query()
            ->join('users', 'certificates.user_id', '=', 'users.id')
            ->whereNotNull('users.department')
            ->select('users.department', DB::raw('COUNT(*) as total'))
            ->groupBy('users.department')
            ->orderByDesc('total')
            ->get();

        $byLevel = Certificate::query()
            ->join('users', 'certificates.user_id', '=', 'users.id')
            ->whereNotNull('users.level')
            ->select('users.level', DB::raw('COUNT(*) as total'))
            ->groupBy('users.level')
            ->orderByDesc('total')
            ->get();

        $recent = Certificate::query()
            ->with('user')
            ->orderByDesc('issued_at')
            ->take(15)
            ->get();

        return view('leaderboards.certificates', compact('byDept', 'byLevel', 'recent'));
    }
}
