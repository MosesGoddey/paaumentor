<?php
// ============================================================
//  PAAUMENTOR — All Controllers
//  Place each class in app/Http/Controllers/
// ============================================================

namespace App\Http\Controllers;

use App\Models\{User, Mentorship, LearningPath, LearningTask, TaskSubmission,
                Message, Conversation, Resource, Rating, Certificate,
                Notification, MentorSession, Skill};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash, Storage, DB};
use Illuminate\Validation\Rule;

// ================================================================
// FILE: app/Http/Controllers/AuthController.php
// ================================================================
class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        // Allow login by email OR student_id
        $field = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL)
                 ? 'email' : 'student_id';

        if (!Auth::attempt([$field => $credentials['login'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            return back()->withErrors(['login' => 'Invalid credentials.'])->withInput();
        }

        $user = Auth::user();
        if (!$user->is_active) {
            Auth::logout();
            return back()->withErrors(['login' => 'Your account has been suspended.']);
        }

        $request->session()->regenerate();
        return redirect()->intended(
            $user->isAdmin() ? route('admin.dashboard') : route('dashboard')
        );
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:60',
            'last_name'  => 'required|string|max:60',
            'email'      => 'required|email|unique:users',
            'student_id' => 'required|string|unique:users',
            'department' => 'required|string',
            'level'      => 'required|string',
            'role'       => ['required', Rule::in(['mentee', 'mentor', 'alumni'])],
            'password'   => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            ...$data,
            'password'    => $data['password'],
            'is_verified' => false,
        ]);

        Auth::login($user);
        return redirect()->route('dashboard')
                         ->with('success', 'Welcome to PAAUMENTOR! Your account is pending verification.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}

// ================================================================
// FILE: app/Http/Controllers/DashboardController.php
// ================================================================
class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Active mentorships
        $mentorships = $user->isMentee()
            ? $user->menteeMentorships()->with('mentor')->where('status', 'active')->get()
            : $user->mentorMentorships()->with('mentee')->where('status', 'active')->get();

        // Smart match suggestions (top 3 mentors by score)
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

        // Learning paths
        $learningPaths = $user->learningPathsAsMentee()
                              ->with(['modules.tasks'])
                              ->where('status', '!=', 'archived')
                              ->get()
                              ->map(fn($lp) => ['path' => $lp, 'progress' => $lp->progress]);

        // Upcoming sessions
        $upcomingSessions = MentorSession::whereHas('mentorship', function ($q) use ($user) {
            $q->where('mentor_id', $user->id)->orWhere('mentee_id', $user->id);
        })->where('status', 'scheduled')
          ->where('scheduled_at', '>=', now())
          ->orderBy('scheduled_at')
          ->with('mentorship.mentor', 'mentorship.mentee')
          ->take(3)
          ->get();

        // Completed session count
        $sessionCount = MentorSession::whereHas('mentorship', function ($q) use ($user) {
            $q->where('mentor_id', $user->id)->orWhere('mentee_id', $user->id);
        })->where('status', 'completed')->count();

        // Certificates
        $certificates = $user->certificates()->with('learningPath')->get();

        // Recent notifications
        $notifications = $user->notifications()->latest()->take(10)->get();

        // Monthly session engagement (last 6 months)
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
            'upcomingSessions', 'sessionCount', 'certificates',
            'notifications', 'engagement'
        ));
    }
}

// ================================================================
// FILE: app/Http/Controllers/MentorController.php
// ================================================================
class MentorController extends Controller
{
    /**
     * Mentor listing / browse page
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = User::where(fn($q) => $q->where('role', 'mentor')->orWhere('role', 'alumni'))
                     ->where('is_active', true)
                     ->where('is_verified', true)
                     ->with(['hasSkills', 'ratings', 'mentorMentorships']);

        // Filters
        if ($request->filled('skill')) {
            $query->whereHas('hasSkills', fn($q) => $q->where('name', $request->skill));
        }
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(fn($qb) =>
                $qb->where('first_name', 'like', "%$q%")
                   ->orWhere('last_name', 'like', "%$q%")
                   ->orWhere('bio', 'like', "%$q%")
            );
        }

        $mentors = $query->get()->map(fn($m) => [
            'mentor' => $m,
            'score'  => $m->matchScore($user),
            'rating' => $m->average_rating,
        ])->sortByDesc(fn($m) => $m['score'])->values();

        $skills = Skill::orderBy('name')->get();

        return view('mentors.index', compact('mentors', 'skills', 'user'));
    }

    /**
     * Single mentor profile
     */
    public function show(User $mentor)
    {
        $mentor->load(['hasSkills', 'ratings.rater', 'certificates']);
        $reviews = $mentor->ratings()->with('rater')->latest()->take(5)->get();

        $ratingBreakdown = [];
        for ($i = 5; $i >= 1; $i--) {
            $ratingBreakdown[$i] = $mentor->ratings()->where('score', $i)->count();
        }

        return view('mentors.show', compact('mentor', 'reviews', 'ratingBreakdown'));
    }

    /**
     * Submit a mentorship request
     */
    public function requestMentorship(Request $request, User $mentor)
    {
        $mentee = Auth::user();

        $request->validate([
            'topic'        => 'required|string|max:255',
            'goal'         => 'nullable|string|max:1000',
            'session_type' => ['required', Rule::in(['video', 'voice', 'chat'])],
        ]);

        // Prevent duplicate active request
        $exists = Mentorship::where('mentor_id', $mentor->id)
                            ->where('mentee_id', $mentee->id)
                            ->whereIn('status', ['pending', 'active'])
                            ->exists();

        if ($exists) {
            return back()->with('error', 'You already have an active or pending request with this mentor.');
        }

        $mentorship = Mentorship::create([
            'mentor_id'    => $mentor->id,
            'mentee_id'    => $mentee->id,
            'topic'        => $request->topic,
            'goal'         => $request->goal,
            'session_type' => $request->session_type,
            'status'       => 'pending',
        ]);

        // Create notification for mentor
        Notification::create([
            'user_id' => $mentor->id,
            'type'    => 'mentorship_request',
            'title'   => 'New mentorship request',
            'body'    => "{$mentee->full_name} wants to be your mentee — Topic: {$request->topic}",
            'data'    => ['mentorship_id' => $mentorship->id],
        ]);

        return redirect()->route('chat.index')
                         ->with('success', 'Mentorship request sent! You will be notified when they respond.');
    }

    /**
     * Accept / Reject a pending mentorship request (mentor side)
     */
    public function respond(Request $request, Mentorship $mentorship)
    {
        $this->authorize('respond', $mentorship);

        $request->validate(['action' => Rule::in(['accept', 'reject'])]);

        if ($request->action === 'accept') {
            $mentorship->update(['status' => 'active', 'started_at' => now()]);

            // Auto-create conversation
            Conversation::firstOrCreate(['mentorship_id' => $mentorship->id]);

            // Notify mentee
            Notification::create([
                'user_id' => $mentorship->mentee_id,
                'type'    => 'mentorship_accepted',
                'title'   => 'Mentorship request accepted!',
                'body'    => "{$mentorship->mentor->full_name} accepted your mentorship request.",
                'data'    => ['mentorship_id' => $mentorship->id],
            ]);
        } else {
            $mentorship->update(['status' => 'rejected']);
        }

        return back()->with('success', 'Response recorded.');
    }
}

// ================================================================
// FILE: app/Http/Controllers/LearningPathController.php
// ================================================================
class LearningPathController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $paths = $user->learningPathsAsMentee()
                      ->with(['modules.tasks.submissions' => fn($q) => $q->where('user_id', $user->id),
                              'mentor', 'certificate'])
                      ->where('status', '!=', 'archived')
                      ->get()
                      ->map(fn($lp) => ['path' => $lp, 'progress' => $lp->progress]);

        return view('learning.index', compact('user', 'paths'));
    }

    public function show(LearningPath $learningPath)
    {
        $this->authorize('view', $learningPath);

        $user = Auth::user();
        $learningPath->load([
            'modules.tasks.submissions' => fn($q) => $q->where('user_id', $user->id),
            'mentor',
            'certificate',
        ]);

        return view('learning.show', [
            'path'     => $learningPath,
            'user'     => $user,
            'progress' => $learningPath->progress,
        ]);
    }

    public function submitTask(Request $request, LearningTask $task)
    {
        $user = Auth::user();
        $request->validate([
            'notes' => 'nullable|string|max:2000',
            'file'  => 'nullable|file|max:10240',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('submissions', 'public');
        }

        TaskSubmission::updateOrCreate(
            ['learning_task_id' => $task->id, 'user_id' => $user->id],
            ['notes' => $request->notes, 'file_path' => $filePath, 'status' => 'submitted']
        );

        // Check if path is now complete → issue certificate
        $path = $task->module->learningPath;
        if ($path->isComplete()) {
            $this->issueCertificate($path, $user);
        }

        return back()->with('success', 'Task submitted successfully!');
    }

    private function issueCertificate(LearningPath $path, User $user): void
    {
        if ($path->certificate) return; // already issued

        $certId = 'PAAU-' . date('Y') . '-' . str_pad($path->id, 5, '0', STR_PAD_LEFT);

        Certificate::create([
            'user_id'          => $user->id,
            'learning_path_id' => $path->id,
            'certificate_id'   => $certId,
            'issued_at'        => now(),
        ]);

        $path->update(['status' => 'completed']);

        Notification::create([
            'user_id' => $user->id,
            'type'    => 'certificate_issued',
            'title'   => '🏅 Certificate Issued!',
            'body'    => "Congratulations! Your certificate for '{$path->title}' is ready.",
            'data'    => ['certificate_id' => $certId],
        ]);
    }
}

// ================================================================
// FILE: app/Http/Controllers/ChatController.php
// ================================================================
class ChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $conversations = Conversation::whereHas('mentorship', function ($q) use ($user) {
            $q->where('mentor_id', $user->id)->orWhere('mentee_id', $user->id);
        })->with([
            'mentorship.mentor',
            'mentorship.mentee',
            'messages' => fn($q) => $q->latest()->take(1),
        ])->latest('last_message_at')->get();

        $activeConversation = $conversations->first();
        $messages = $activeConversation
                    ? $activeConversation->messages()->with('sender')->orderBy('created_at')->get()
                    : collect();

        // Mark as read
        if ($activeConversation) {
            $activeConversation->messages()
                               ->where('sender_id', '!=', $user->id)
                               ->whereNull('read_at')
                               ->update(['read_at' => now()]);
        }

        return view('chat.index', compact('user', 'conversations', 'activeConversation', 'messages'));
    }

    public function show(Conversation $conversation)
    {
        $this->authorize('view', $conversation);
        $user = Auth::user();

        $messages = $conversation->messages()->with('sender')->orderBy('created_at')->get();

        // Mark as read
        $conversation->messages()
                     ->where('sender_id', '!=', $user->id)
                     ->whereNull('read_at')
                     ->update(['read_at' => now()]);

        $conversations = Conversation::whereHas('mentorship', function ($q) use ($user) {
            $q->where('mentor_id', $user->id)->orWhere('mentee_id', $user->id);
        })->with(['mentorship.mentor', 'mentorship.mentee', 'messages' => fn($q) => $q->latest()->take(1)])
          ->latest('last_message_at')->get();

        return view('chat.index', compact('user', 'conversations', 'messages'))
               ->with('activeConversation', $conversation);
    }

    public function sendMessage(Request $request, Conversation $conversation)
    {
        $this->authorize('participate', $conversation);

        $request->validate([
            'body' => 'required_without:file|nullable|string|max:5000',
            'file' => 'nullable|file|max:20480',
        ]);

        $filePath = $fileName = null;
        $type = 'text';

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('chat-files', 'public');
            $fileName = $request->file('file')->getClientOriginalName();
            $type = 'file';
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => Auth::id(),
            'body'            => $request->body,
            'file_path'       => $filePath,
            'file_name'       => $fileName,
            'type'            => $type,
        ]);

        $conversation->update(['last_message_at' => now()]);

        // For AJAX requests return JSON
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message->load('sender'),
            ]);
        }

        return back();
    }
}

// ================================================================
// FILE: app/Http/Controllers/AdminController.php
// ================================================================
class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(fn($req, $next) =>
            Auth::user()?->isAdmin() ? $next($req) : abort(403)
        );
    }

    public function dashboard()
    {
        $stats = [
            'users'        => User::count(),
            'mentorships'  => Mentorship::where('status', 'active')->count(),
            'sessions'     => MentorSession::where('status', 'completed')->count(),
            'certificates' => Certificate::count(),
        ];

        $pendingMentors    = User::where('is_verified', false)
                                 ->whereIn('role', ['mentor', 'alumni'])
                                 ->latest()->get();

        $pendingMentees    = User::where('is_verified', false)
                                 ->where('role', 'mentee')
                                 ->latest()->get();

        $recentUsers       = User::latest()->take(10)->get();

        // Monthly sessions (last 6 months)
        $monthlyData = MentorSession::where('status', 'completed')
                                    ->where('created_at', '>=', now()->subMonths(6))
                                    ->selectRaw("DATE_FORMAT(scheduled_at,'%b') as m, COUNT(*) as c")
                                    ->groupByRaw("DATE_FORMAT(scheduled_at,'%b'), MONTH(scheduled_at)")
                                    ->orderByRaw("MONTH(scheduled_at)")
                                    ->pluck('c', 'm');

        // Role distribution
        $roleData = User::selectRaw('role, COUNT(*) as count')
                        ->groupBy('role')
                        ->pluck('count', 'role');

        // Top skills requested
        $topSkills = DB::table('skill_user')
                       ->join('skills', 'skills.id', '=', 'skill_user.skill_id')
                       ->where('skill_user.type', 'wants')
                       ->select('skills.name', DB::raw('COUNT(*) as total'))
                       ->groupBy('skills.name')
                       ->orderByDesc('total')
                       ->take(6)->get();

        // Top mentors
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
            $query->where(fn($qb) => $qb->where('first_name','like',"%$q%")
                                        ->orWhere('last_name','like',"%$q%")
                                        ->orWhere('email','like',"%$q%")
                                        ->orWhere('student_id','like',"%$q%"));
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
            'title'   => 'Account Verified ✓',
            'body'    => 'Your PAAUMENTOR account has been verified. You can now access all platform features.',
        ]);

        return back()->with('success', "{$user->full_name} verified successfully.");
    }

    public function suspendUser(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        $action = $user->is_active ? 'activated' : 'suspended';
        return back()->with('success', "User {$action}.");
    }
}

// ================================================================
// FILE: app/Http/Controllers/ProfileController.php
// ================================================================
class ProfileController extends Controller
{
    public function show(User $user)
    {
        $user->load(['hasSkills', 'ratings.rater', 'certificates.learningPath']);
        return view('profile.show', compact('user'));
    }

    public function edit()
    {
        $user   = Auth::user();
        $skills = Skill::orderBy('name')->get();
        return view('profile.edit', compact('user', 'skills'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'first_name'   => 'required|string|max:60',
            'last_name'    => 'required|string|max:60',
            'bio'          => 'nullable|string|max:500',
            'phone'        => 'nullable|string|max:20',
            'availability' => 'nullable|string|max:100',
            'avatar'       => 'nullable|image|max:2048',
            'skill_ids'    => 'nullable|array',
            'skill_ids.*'  => 'exists:skills,id',
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) Storage::disk('public')->delete($user->avatar);
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update(\Arr::except($data, ['skill_ids', 'avatar']) + ['avatar' => $data['avatar'] ?? $user->avatar]);

        if ($request->has('skill_ids')) {
            $user->skills()->sync(collect($request->skill_ids)->mapWithKeys(fn($id) => [$id => ['type' => 'has']]));
        }

        return back()->with('success', 'Profile updated!');
    }
}
