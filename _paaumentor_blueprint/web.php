<?php
// ============================================================
//  PAAUMENTOR — routes/web.php
//  Replace the contents of your routes/web.php with this
// ============================================================

use App\Http\Controllers\{
    AuthController,
    DashboardController,
    MentorController,
    LearningPathController,
    ChatController,
    AdminController,
    ProfileController,
};
use Illuminate\Support\Facades\Route;

// ----------------------------------------------------------------
// PUBLIC ROUTES
// ----------------------------------------------------------------
Route::get('/', fn() => view('welcome'))->name('home');

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])
     ->middleware('auth')
     ->name('logout');

// ----------------------------------------------------------------
// AUTHENTICATED ROUTES
// ----------------------------------------------------------------
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ---- Mentors ----
    Route::prefix('mentors')->name('mentors.')->group(function () {
        Route::get('/',                                    [MentorController::class, 'index'])->name('index');
        Route::get('/{mentor}',                            [MentorController::class, 'show'])->name('show');
        Route::post('/{mentor}/request',                   [MentorController::class, 'requestMentorship'])->name('request');
        Route::patch('/mentorships/{mentorship}/respond',  [MentorController::class, 'respond'])->name('respond');
    });

    // ---- Learning Paths ----
    Route::prefix('learning')->name('learning.')->group(function () {
        Route::get('/',                          [LearningPathController::class, 'index'])->name('index');
        Route::get('/{learningPath}',            [LearningPathController::class, 'show'])->name('show');
        Route::post('/tasks/{task}/submit',      [LearningPathController::class, 'submitTask'])->name('submit');
    });

    // ---- Chat / Messages ----
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/',                                        [ChatController::class, 'index'])->name('index');
        Route::get('/{conversation}',                          [ChatController::class, 'show'])->name('show');
        Route::post('/{conversation}/send',                    [ChatController::class, 'sendMessage'])->name('send');
    });

    // ---- Profile ----
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/{user}',   [ProfileController::class, 'show'])->name('show');
        Route::get('/edit',     [ProfileController::class, 'edit'])->name('edit');
        Route::post('/update',  [ProfileController::class, 'update'])->name('update');
    });

    // ---- Admin (separate middleware check inside controller) ----
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard',             [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users',                 [AdminController::class, 'users'])->name('users');
        Route::patch('/users/{user}/verify', [AdminController::class, 'verifyUser'])->name('verify');
        Route::patch('/users/{user}/toggle', [AdminController::class, 'suspendUser'])->name('suspend');
    });

    // ---- Notifications (inline) ----
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
});
