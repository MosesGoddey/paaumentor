<?php

namespace App\Http\Controllers;

use App\Mail\AppNotificationMail;
use App\Models\{User, Mentorship, MentorSession, Certificate, Notification};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Mail};

class AdminController extends Controller
{

    public function dashboard()
    {
        $stats = [
            'users'        => User::count(),
            'mentorships'  => Mentorship::where('status', 'active')->count(),
            'sessions'     => MentorSession::where('status', 'completed')->count(),
            'certificates' => Certificate::count(),
        ];

        $pendingMentors = User::where('is_verified', false)
                              ->whereIn('role', ['mentor', 'alumni'])
                              ->latest()->get();

        $pendingMentees = User::where('is_verified', false)
                              ->where('role', 'mentee')
                              ->latest()->get();

        $recentUsers = User::latest()->take(10)->get();

        $monthlyData = MentorSession::where('status', 'completed')
                                    ->where('created_at', '>=', now()->subMonths(6))
                                    ->selectRaw("DATE_FORMAT(scheduled_at,'%b') as m, COUNT(*) as c")
                                    ->groupByRaw("DATE_FORMAT(scheduled_at,'%b'), MONTH(scheduled_at)")
                                    ->orderByRaw("MONTH(scheduled_at)")
                                    ->pluck('c', 'm');

        $roleData = User::selectRaw('role, COUNT(*) as count')
                        ->groupBy('role')
                        ->pluck('count', 'role');

        $topSkills = DB::table('skill_user')
                       ->join('skills', 'skills.id', '=', 'skill_user.skill_id')
                       ->where('skill_user.type', 'wants')
                       ->select('skills.name', DB::raw('COUNT(*) as total'))
                       ->groupBy('skills.name')
                       ->orderByDesc('total')
                       ->take(6)->get();

        $topMentors = User::whereIn('role', ['mentor', 'alumni'])
                          ->withCount(['mentorMentorships as session_count' => fn($q) =>
                              $q->where('status', 'active')])
                          ->with('ratings')
                          ->orderByDesc('session_count')
                          ->take(5)->get();

        return view('admin.dashboard', compact(
            'stats', 'pendingMentors', 'pendingMentees', 'recentUsers',
            'monthlyData', 'roleData', 'topSkills', 'topMentors'
        ));
    }

    public function users(Request $request)
    {
        $query = User::with('ratings')->latest();

        if ($request->filled('role'))   $query->where('role', $request->role);
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(fn($qb) => $qb->where('first_name', 'like', "%$q%")
                                        ->orWhere('last_name', 'like', "%$q%")
                                        ->orWhere('email', 'like', "%$q%")
                                        ->orWhere('student_id', 'like', "%$q%"));
        }
        if ($request->filled('status') && $request->status === 'pending') {
            $query->where('is_verified', false);
        }

        $users = $query->paginate(20);
        return view('admin.users', compact('users'));
    }

    public function verifyUser(User $user)
    {
        $user->update(['is_verified' => true]);

        Notification::create([
            'user_id' => $user->id,
            'type'    => 'account_verified',
            'title'   => 'Account Verified',
            'body'    => 'Your PAAUMENTOR account has been verified. You can now access all platform features.',
        ]);

        try {
            Mail::to($user->email)->send(new AppNotificationMail(
                title: 'Your Account Has Been Verified ',
                body:  "Hi {$user->first_name},\n\nYour PAAUMENTOR account has been verified by our admin team. You now have full access to the platform.\n\nAs a " . ucfirst($user->role) . ", you can now " . ($user->isMentee() ? 'find and connect with mentors.' : 'receive mentorship requests from students.'),
                actionText: 'Go to Dashboard',
                actionUrl:  url('/dashboard'),
            ));
        } catch (\Exception) {}

        return back()->with('success', "{$user->full_name} verified successfully.");
    }

    public function suspendUser(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        $action = $user->is_active ? 'activated' : 'suspended';
        return back()->with('success', "User {$action}.");
    }
}
