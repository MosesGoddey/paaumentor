<?php

use App\Http\Controllers\{
    AuthController,
    AiAssistantController,
    DashboardController,
    MentorController,
    LearningPathController,
    ChatController,
    AdminController,
    ProfileController,
    StudyGroupController,
    SharedResourceController,
    SessionController,
    CertificateController,
    SkillExchangeController,
    MentorUpgradeRequestController,
    VerifierController,
    AssessmentController,
    CertificateRequestController,
    UpgradeAssessmentController,
    HackathonController,
};
use Illuminate\Support\Facades\{Route, Auth};

// ----------------------------------------------------------------
// PUBLIC ROUTES
// ----------------------------------------------------------------
Route::get('/', fn() => view('welcome'))->name('home');


// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AuthController::class, 'login']);
    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Forgot / reset password
    Route::get('/forgot-password',        [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password',       [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [\App\Http\Controllers\Auth\NewPasswordController::class,       'create'])->name('password.reset');
    Route::post('/reset-password',        [\App\Http\Controllers\Auth\NewPasswordController::class,       'store'])->name('password.store');
});

Route::post('/logout', [AuthController::class, 'logout'])
     ->middleware('auth')
     ->name('logout');

// Public certificate verification (no auth required)
Route::get('/certificates/verify/{certificateId}', [CertificateController::class, 'verify'])
     ->name('certificates.verify');

// ----------------------------------------------------------------
// AUTHENTICATED ROUTES
// ----------------------------------------------------------------
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ---- Mentors ----
    Route::prefix('mentors')->name('mentors.')->group(function () {
        Route::get('/',                                   [MentorController::class, 'index'])->name('index');
        Route::get('/my-mentors',                         [MentorController::class, 'myMentors'])->name('my');
        Route::get('/my-mentees',                         [MentorController::class, 'myMentees'])->name('mentees');
        Route::get('/{mentor}',                           [MentorController::class, 'show'])->name('show');
        Route::post('/{mentor}/request',                  [MentorController::class, 'requestMentorship'])->name('request');
        Route::patch('/mentorships/{mentorship}/respond', [MentorController::class, 'respond'])->name('respond');
        Route::post('/mentorships/{mentorship}/rate',    [MentorController::class, 'rate'])->name('rate');
    });

    // ---- Learning Paths ----
    Route::prefix('learning')->name('learning.')->group(function () {
        Route::get('/',                               [LearningPathController::class, 'index'])->name('index');
        Route::get('/create',                         [LearningPathController::class, 'create'])->name('create');
        Route::post('/',                              [LearningPathController::class, 'store'])->name('store');
        Route::get('/{learningPath}',                 [LearningPathController::class, 'show'])->name('show');
        Route::get('/{learningPath}/edit',            [LearningPathController::class, 'edit'])->name('edit');
        Route::put('/{learningPath}',                 [LearningPathController::class, 'update'])->name('update');
        Route::delete('/{learningPath}',              [LearningPathController::class, 'destroy'])->name('destroy');
        Route::get('/{learningPath}/grade',           [LearningPathController::class, 'grade'])->name('grade');
        Route::post('/tasks/{task}/submit',           [LearningPathController::class, 'submitTask'])->name('submit');
        Route::post('/submissions/{submission}/grade',[LearningPathController::class, 'gradeSubmission'])->name('grade-submission');
    });

    // ---- Chat / Messages ----
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/',                           [ChatController::class, 'index'])->name('index');
        Route::get('/{conversation}',             [ChatController::class, 'show'])->name('show');
        Route::post('/{conversation}/send',       [ChatController::class, 'sendMessage'])->name('send');
        Route::post('/{conversation}/call',       [ChatController::class, 'notifyCall'])->name('call');
        Route::post('/{conversation}/typing',     [ChatController::class, 'typing'])->name('typing');
        Route::get('/{conversation}/is-typing',   [ChatController::class, 'isTyping'])->name('isTyping');
        Route::get('/{conversation}/read-status', [ChatController::class, 'readStatus'])->name('readStatus');
    });

    // ---- Profile ----
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/edit',       [ProfileController::class, 'edit'])->name('edit');
        Route::post('/update',    [ProfileController::class, 'update'])->name('update');
        Route::post('/password',  [ProfileController::class, 'updatePassword'])->name('password');
        Route::get('/{user}',     [ProfileController::class, 'show'])->name('show');
    });

    // ---- Admin ----
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard',             [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users',                 [AdminController::class, 'users'])->name('users');
        Route::patch('/users/{user}/verify', [AdminController::class, 'verifyUser'])->name('verify');
        Route::patch('/users/{user}/toggle', [AdminController::class, 'suspendUser'])->name('suspend');
        Route::post('/verifier/create',      [AdminController::class, 'createVerifier'])->name('createVerifier');
    });

    // ---- Skill Exchange ----
    Route::prefix('skill-exchange')->name('skill-exchange.')->group(function () {
        Route::get('/',                                       [SkillExchangeController::class, 'index'])->name('index');
        Route::get('/my',                                     [SkillExchangeController::class, 'my'])->name('my');
        Route::get('/create',                                 [SkillExchangeController::class, 'create'])->name('create');
        Route::post('/',                                      [SkillExchangeController::class, 'store'])->name('store');
        Route::post('/{exchange}/request',                    [SkillExchangeController::class, 'sendRequest'])->name('request');
        Route::post('/requests/{exchangeRequest}/respond',    [SkillExchangeController::class, 'respond'])->name('respond');
        Route::post('/{exchange}/toggle',                     [SkillExchangeController::class, 'toggleActive'])->name('toggle');
        Route::delete('/{exchange}',                          [SkillExchangeController::class, 'destroy'])->name('destroy');
        Route::get('/requests/{exchangeRequest}/chat',        [SkillExchangeController::class, 'openChat'])->name('chat');
    });

    // ---- Study Groups ----
    Route::prefix('study-groups')->name('study-groups.')->group(function () {
        Route::get('/',                               [StudyGroupController::class, 'index'])->name('index');
        Route::post('/',                              [StudyGroupController::class, 'store'])->name('store');
        Route::get('/{studyGroup}',                  [StudyGroupController::class, 'show'])->name('show');
        Route::post('/{studyGroup}/join',             [StudyGroupController::class, 'join'])->name('join');
        Route::delete('/{studyGroup}/leave',          [StudyGroupController::class, 'leave'])->name('leave');
        Route::post('/{studyGroup}/message',          [StudyGroupController::class, 'sendMessage'])->name('message');
        Route::post('/{studyGroup}/call',             [StudyGroupController::class, 'notifyCall'])->name('call');
    });

    // ---- Resources ----
    Route::prefix('resources')->name('resources.')->group(function () {
        Route::get('/',                               [SharedResourceController::class, 'index'])->name('index');
        Route::post('/',                              [SharedResourceController::class, 'store'])->name('store');
        Route::delete('/{resource}',                  [SharedResourceController::class, 'destroy'])->name('destroy');
    });

    // ---- Sessions ----
    Route::prefix('sessions')->name('sessions.')->group(function () {
        Route::get('/',                               [SessionController::class, 'index'])->name('index');
        Route::post('/',                              [SessionController::class, 'store'])->name('store');
        Route::post('/{session}/complete',            [SessionController::class, 'complete'])->name('complete');
    });

    // ---- Mentor Upgrade ----
    Route::prefix('upgrade')->name('upgrade.')->group(function () {
        Route::get('/',                                    [MentorUpgradeRequestController::class, 'show'])->name('show');
        Route::post('/apply',                              [MentorUpgradeRequestController::class, 'apply'])->name('apply');
        Route::get('/{upgradeRequest}/recommend',          [MentorUpgradeRequestController::class, 'recommendForm'])->name('recommend.form');
        Route::post('/{upgradeRequest}/recommend',         [MentorUpgradeRequestController::class, 'recommend'])->name('recommend');
        Route::post('/{upgradeRequest}/approve',           [MentorUpgradeRequestController::class, 'approve'])->name('approve');
        Route::post('/{upgradeRequest}/reject',            [MentorUpgradeRequestController::class, 'reject'])->name('reject');
        Route::get('/admin',                               [MentorUpgradeRequestController::class, 'adminIndex'])->name('admin');
    });

    // ---- Verifier ----
    Route::prefix('verifier')->name('verifier.')->group(function () {
        Route::get('/',                              [VerifierController::class, 'index'])->name('index');
        Route::post('/mentor/{user}/approve',        [VerifierController::class, 'approve'])->name('approve');
        Route::post('/mentor/{user}/reject',         [VerifierController::class, 'reject'])->name('reject');
        Route::post('/cert/{certRequest}/approve',   [VerifierController::class, 'approveCert'])->name('cert.approve');
        Route::post('/cert/{certRequest}/reject',    [VerifierController::class, 'rejectCert'])->name('cert.reject');
    });

    // ---- Upgrade Assessment ----
    Route::prefix('upgrade-assessment')->name('upgrade-assessment.')->group(function () {
        Route::get('/{upgradeRequest}',                        [UpgradeAssessmentController::class, 'show'])->name('show');
        Route::post('/{upgradeRequest}/start',                 [UpgradeAssessmentController::class, 'start'])->name('start');
        Route::post('/attempt/{attempt}/tab-switch',           [UpgradeAssessmentController::class, 'tabSwitch'])->name('tabSwitch');
        Route::post('/attempt/{attempt}/submit',               [UpgradeAssessmentController::class, 'submit'])->name('submit');
        Route::get('/attempt/{attempt}/result',                [UpgradeAssessmentController::class, 'result'])->name('result');
        Route::post('/{upgradeRequest}/retry-generate',        [UpgradeAssessmentController::class, 'retryGenerate'])->name('retryGenerate');
    });

    // ---- Certificate Requests (mentor reflection) ----
    Route::prefix('cert-request')->name('cert-request.')->group(function () {
        Route::get('/{certRequest}/reflect',   [CertificateRequestController::class, 'showReflect'])->name('reflect');
        Route::post('/{certRequest}/reflect',  [CertificateRequestController::class, 'submitReflect'])->name('reflect.submit');
    });

    // ---- Assessments ----
    Route::prefix('assessment')->name('assessment.')->group(function () {
        Route::get('/{certRequest}',                       [AssessmentController::class, 'show'])->name('show');
        Route::post('/{certRequest}/start',                [AssessmentController::class, 'start'])->name('start');
        Route::post('/attempt/{attempt}/tab-switch',       [AssessmentController::class, 'tabSwitch'])->name('tabSwitch');
        Route::post('/attempt/{attempt}/submit',           [AssessmentController::class, 'submit'])->name('submit');
        Route::get('/attempt/{attempt}/result',            [AssessmentController::class, 'result'])->name('result');
        Route::post('/{certRequest}/regenerate',           [AssessmentController::class, 'regenerate'])->name('regenerate');
    });

    // ---- Hackathons ----
    Route::prefix('hackathons')->name('hackathons.')->group(function () {
        Route::get('/',                                        [HackathonController::class, 'index'])->name('index');
        Route::get('/create',                                  [HackathonController::class, 'create'])->name('create');
        Route::post('/',                                       [HackathonController::class, 'store'])->name('store');
        Route::get('/{hackathon}',                             [HackathonController::class, 'show'])->name('show');
        Route::post('/{hackathon}/team/create',                [HackathonController::class, 'createTeam'])->name('team.create');
        Route::post('/{hackathon}/team/join',                  [HackathonController::class, 'joinTeam'])->name('team.join');
        Route::get('/{hackathon}/team',                        [HackathonController::class, 'myTeam'])->name('team');
        Route::post('/{hackathon}/submit',                     [HackathonController::class, 'submitProject'])->name('submit');
        Route::get('/{hackathon}/judge',                       [HackathonController::class, 'judgePanel'])->name('judge');
        Route::post('/{hackathon}/status',                     [HackathonController::class, 'updateStatus'])->name('status');
        Route::post('/{hackathon}/assign-judge',               [HackathonController::class, 'assignJudge'])->name('assignJudge');
        Route::post('/{hackathon}/publish-results',            [HackathonController::class, 'publishResults'])->name('publishResults');
        Route::get('/{hackathon}/leaderboard',                 [HackathonController::class, 'leaderboard'])->name('leaderboard');
        Route::post('/submissions/{submission}/score',         [HackathonController::class, 'score'])->name('score');
        Route::post('/teams/{team}/volunteer-coach',           [HackathonController::class, 'volunteerCoach'])->name('volunteerCoach');
        Route::post('/teams/{team}/respond-coach',             [HackathonController::class, 'respondCoach'])->name('respondCoach');
    });

    // ---- Certificates ----
    Route::prefix('certificates')->name('certificates.')->group(function () {
        Route::get('/',                               [CertificateController::class, 'index'])->name('index');
        Route::get('/{certificate}/download',         [CertificateController::class, 'download'])->name('download');
        Route::post('/{certificate}/rate-mentor',     [CertificateController::class, 'rateMentor'])->name('rateMentor');
    });

    // ---- Notifications ----
    Route::get('/notifications', function () {
        $notifs = Auth::user()->notifications()->latest()->paginate(20);
        return view('notifications.index', compact('notifs'));
    })->name('notifications.index');

    Route::post('/notifications/{notification}/read', function (\App\Models\Notification $n) {
        $n->update(['read_at' => now()]);
        return back();
    })->name('notifications.read');

    Route::post('/notifications/read-all', function () {
        Auth::user()->notifications()->whereNull('read_at')->update(['read_at' => now()]);
        return back();
    })->name('notifications.readAll');

    // ---- AI Features ----
    Route::prefix('ai')->name('ai.')->group(function () {
        Route::get('/assistant',          [AiAssistantController::class, 'index'])->name('assistant');
        Route::post('/assistant/chat',    [AiAssistantController::class, 'chat'])->name('chat');
        Route::post('/assistant/clear',   [AiAssistantController::class, 'clear'])->name('clear');
        Route::post('/learning/generate', [LearningPathController::class, 'aiGenerate'])->name('learning.generate');
        Route::post('/mentors/match',     [MentorController::class, 'aiMatch'])->name('mentors.match');
    });

    // ---- Session call outcomes ----
    Route::post('/sessions/{session}/answered', function (\App\Models\MentorSession $session) {
        $session->load(['mentorship', 'skillExchangeRequest.exchange']);
        abort_unless(in_array(Auth::id(), $session->participantIds()), 403);
        if ($session->call_outcome === null) {
            $session->update(['call_outcome' => 'answered']);
        }
        return response()->json(['ok' => true]);
    })->name('sessions.answered');

    Route::post('/sessions/{session}/missed', function (\App\Models\MentorSession $session) {
        $session->load(['mentorship', 'skillExchangeRequest.exchange']);
        abort_unless(in_array(Auth::id(), $session->participantIds()), 403);
        if ($session->call_outcome === null) {
            $session->update(['call_outcome' => 'missed', 'status' => 'cancelled']);
        }
        return response()->json(['ok' => true]);
    })->name('sessions.missed');

    Route::get('/sessions/{session}/status', function (\App\Models\MentorSession $session) {
        $session->load(['mentorship', 'skillExchangeRequest.exchange']);
        abort_unless(in_array(Auth::id(), $session->participantIds()), 403);
        return response()->json([
            'status'       => $session->status,
            'call_outcome' => $session->call_outcome,
        ]);
    })->name('sessions.status');

    Route::get('/notifications/pending-call', function () {
        $notif = Auth::user()->notifications()
            ->where('type', 'call')
            ->whereNull('read_at')
            ->where('created_at', '>=', now()->subMinutes(2))
            ->latest()
            ->first();
        return response()->json(['call' => $notif]);
    })->name('notifications.pendingCall');
});
