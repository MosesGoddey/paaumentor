<?php
// ============================================================
//  PAAUMENTOR — All Eloquent Models
//  Place each class in app/Models/
// ============================================================

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// ================================================================
// FILE: app/Models/User.php
// ================================================================
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'student_id',
        'password', 'role', 'department', 'level', 'bio',
        'phone', 'avatar', 'is_verified', 'is_active', 'availability',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_verified'       => 'boolean',
        'is_active'         => 'boolean',
        'password'          => 'hashed',
    ];

    // ---- Accessors ----
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getInitialsAttribute(): string
    {
        return strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar ? asset('storage/' . $this->avatar) : null;
    }

    // ---- Relationships ----
    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'skill_user')
                    ->withPivot('type')
                    ->withTimestamps();
    }

    public function hasSkills()
    {
        return $this->skills()->wherePivot('type', 'has');
    }

    public function wantedSkills()
    {
        return $this->skills()->wherePivot('type', 'wants');
    }

    // Mentorships where this user is the mentor
    public function mentorMentorships()
    {
        return $this->hasMany(Mentorship::class, 'mentor_id');
    }

    // Mentorships where this user is the mentee
    public function menteeMentorships()
    {
        return $this->hasMany(Mentorship::class, 'mentee_id');
    }

    public function learningPathsAsmentor()
    {
        return $this->hasMany(LearningPath::class, 'mentor_id');
    }

    public function learningPathsAsMentee()
    {
        return $this->hasMany(LearningPath::class, 'mentee_id');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'ratee_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // ---- Helpers ----
    public function isMentor(): bool { return in_array($this->role, ['mentor', 'alumni']); }
    public function isMentee(): bool { return $this->role === 'mentee'; }
    public function isAdmin(): bool  { return $this->role === 'admin'; }

    public function getAverageRatingAttribute(): float
    {
        return round($this->ratings()->avg('score') ?? 0, 1);
    }

    public function getTotalSessionsAttribute(): int
    {
        return $this->mentorMentorships()
                    ->withCount(['sessions' => fn($q) => $q->where('status', 'completed')])
                    ->get()->sum('sessions_count');
    }

    /**
     * Smart Match Score (0–100) against a mentee
     * Weights: skill overlap 50 | department 20 | level gap 20 | availability 10
     */
    public function matchScore(User $mentee): int
    {
        $score = 0;

        // Skill overlap (50 pts)
        $mentorSkills  = $this->hasSkills()->pluck('skills.id')->toArray();
        $menteeWants   = $mentee->wantedSkills()->pluck('skills.id')->toArray();
        if (count($menteeWants) > 0) {
            $overlap = count(array_intersect($mentorSkills, $menteeWants));
            $score  += (int) min(50, ($overlap / count($menteeWants)) * 50);
        }

        // Same department (20 pts)
        if ($this->department === $mentee->department) $score += 20;

        // Level gap 1–2 years above (20 pts)
        $levels = ['100L'=>1,'200L'=>2,'300L'=>3,'400L'=>4,'500L'=>5,'Alumni'=>6];
        $ml = $levels[$this->level]  ?? 0;
        $el = $levels[$mentee->level] ?? 0;
        $gap = $ml - $el;
        if ($gap >= 1 && $gap <= 2) $score += 20;
        elseif ($gap === 3)         $score += 10;

        // Has availability set (10 pts)
        if ($this->availability) $score += 10;

        return $score;
    }
}

// ================================================================
// FILE: app/Models/Skill.php
// ================================================================
class Skill extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory;
    protected $fillable = ['name', 'category'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'skill_user')->withPivot('type');
    }
}

// ================================================================
// FILE: app/Models/Mentorship.php
// ================================================================
class Mentorship extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory;

    protected $fillable = [
        'mentor_id', 'mentee_id', 'status', 'goal',
        'topic', 'session_type', 'started_at', 'ended_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at'   => 'datetime',
    ];

    public function mentor()   { return $this->belongsTo(User::class, 'mentor_id'); }
    public function mentee()   { return $this->belongsTo(User::class, 'mentee_id'); }
    public function sessions() { return $this->hasMany(Session::class); }
    public function conversation() { return $this->hasOne(Conversation::class); }
    public function learningPaths() { return $this->hasMany(LearningPath::class); }
    public function ratings()  { return $this->hasMany(Rating::class); }

    public function getCompletedSessionsCountAttribute(): int
    {
        return $this->sessions()->where('status', 'completed')->count();
    }
}

// ================================================================
// FILE: app/Models/Session.php  (rename to MentorSession to avoid conflict)
// ================================================================
class MentorSession extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'sessions';
    use HasFactory;

    protected $fillable = [
        'mentorship_id', 'title', 'description', 'type',
        'status', 'scheduled_at', 'started_at', 'ended_at', 'duration_minutes',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at'   => 'datetime',
        'ended_at'     => 'datetime',
    ];

    public function mentorship() { return $this->belongsTo(Mentorship::class); }
}

// ================================================================
// FILE: app/Models/LearningPath.php
// ================================================================
class LearningPath extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory;

    protected $fillable = [
        'mentor_id', 'mentee_id', 'title', 'description', 'status', 'due_date',
    ];

    protected $casts = ['due_date' => 'date'];

    public function mentor()  { return $this->belongsTo(User::class, 'mentor_id'); }
    public function mentee()  { return $this->belongsTo(User::class, 'mentee_id'); }
    public function modules() { return $this->hasMany(LearningModule::class)->orderBy('order'); }
    public function certificate() { return $this->hasOne(Certificate::class); }

    public function getProgressAttribute(): int
    {
        $total = $this->modules()->withCount('tasks')->get()->sum('tasks_count');
        if ($total === 0) return 0;
        $done = $this->modules()
                     ->with(['tasks.submissions' => fn($q) =>
                         $q->where('user_id', $this->mentee_id)
                           ->where('status', 'graded')])
                     ->get()
                     ->flatMap(fn($m) => $m->tasks)
                     ->filter(fn($t) => $t->submissions->isNotEmpty())
                     ->count();
        return (int) round(($done / $total) * 100);
    }

    public function isComplete(): bool { return $this->progress === 100; }
}

// ================================================================
// FILE: app/Models/LearningModule.php
// ================================================================
class LearningModule extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory;
    protected $fillable = ['learning_path_id', 'title', 'order'];

    public function learningPath() { return $this->belongsTo(LearningPath::class); }
    public function tasks()        { return $this->hasMany(LearningTask::class)->orderBy('order'); }
}

// ================================================================
// FILE: app/Models/LearningTask.php
// ================================================================
class LearningTask extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory;
    protected $fillable = [
        'learning_module_id', 'title', 'description', 'order', 'max_score', 'is_locked',
    ];

    protected $casts = ['is_locked' => 'boolean'];

    public function module()      { return $this->belongsTo(LearningModule::class, 'learning_module_id'); }
    public function submissions() { return $this->hasMany(TaskSubmission::class); }
}

// ================================================================
// FILE: app/Models/TaskSubmission.php
// ================================================================
class TaskSubmission extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory;
    protected $fillable = [
        'learning_task_id', 'user_id', 'notes', 'file_path', 'status', 'score', 'feedback',
    ];

    public function task() { return $this->belongsTo(LearningTask::class, 'learning_task_id'); }
    public function user() { return $this->belongsTo(User::class); }
}

// ================================================================
// FILE: app/Models/Conversation.php
// ================================================================
class Conversation extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory;
    protected $fillable = ['mentorship_id', 'last_message_at'];
    protected $casts    = ['last_message_at' => 'datetime'];

    public function mentorship() { return $this->belongsTo(Mentorship::class); }
    public function messages()   { return $this->hasMany(Message::class)->orderBy('created_at'); }

    public function getUnreadCountForAttribute(int $userId): int
    {
        return $this->messages()
                    ->where('sender_id', '!=', $userId)
                    ->whereNull('read_at')
                    ->count();
    }
}

// ================================================================
// FILE: app/Models/Message.php
// ================================================================
class Message extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory;
    protected $fillable = [
        'conversation_id', 'sender_id', 'body', 'file_path', 'file_name', 'type', 'read_at',
    ];
    protected $casts = ['read_at' => 'datetime'];

    public function conversation() { return $this->belongsTo(Conversation::class); }
    public function sender()       { return $this->belongsTo(User::class, 'sender_id'); }
}

// ================================================================
// FILE: app/Models/Resource.php
// ================================================================
class Resource extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory;
    protected $fillable = [
        'uploaded_by', 'mentorship_id', 'title', 'file_path', 'url', 'type', 'file_size',
    ];

    public function uploader()   { return $this->belongsTo(User::class, 'uploaded_by'); }
    public function mentorship() { return $this->belongsTo(Mentorship::class); }

    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size ?? 0;
        if ($bytes < 1024) return "{$bytes} B";
        if ($bytes < 1048576) return round($bytes/1024) . ' KB';
        return round($bytes/1048576, 1) . ' MB';
    }
}

// ================================================================
// FILE: app/Models/Rating.php
// ================================================================
class Rating extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory;
    protected $fillable = ['mentorship_id', 'rater_id', 'ratee_id', 'score', 'review'];

    public function rater()      { return $this->belongsTo(User::class, 'rater_id'); }
    public function ratee()      { return $this->belongsTo(User::class, 'ratee_id'); }
    public function mentorship() { return $this->belongsTo(Mentorship::class); }
}

// ================================================================
// FILE: app/Models/Certificate.php
// ================================================================
class Certificate extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'learning_path_id', 'certificate_id', 'file_path', 'qr_code', 'issued_at',
    ];
    protected $casts = ['issued_at' => 'datetime'];

    public function user()         { return $this->belongsTo(User::class); }
    public function learningPath() { return $this->belongsTo(LearningPath::class); }
}

// ================================================================
// FILE: app/Models/Notification.php
// ================================================================
class Notification extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'type', 'title', 'body', 'data', 'read_at'];
    protected $casts    = ['data' => 'array', 'read_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }
    public function isRead(): bool { return $this->read_at !== null; }
}
