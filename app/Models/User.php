<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'student_id',
        'password', 'role', 'department', 'level', 'bio',
        'phone', 'avatar', 'is_verified', 'is_active', 'availability',
        'mentor_status', 'github_url', 'linkedin_url',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_verified'       => 'boolean',
        'is_active'         => 'boolean',
        'password'          => 'hashed',
    ];

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

    public function mentorMentorships()
    {
        return $this->hasMany(Mentorship::class, 'mentor_id');
    }

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

    public function assessmentAttempts()
    {
        return $this->hasMany(\App\Models\AssessmentAttempt::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'ratee_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function studyGroups()
    {
        return $this->belongsToMany(StudyGroup::class, 'study_group_members')->withPivot('role')->withTimestamps();
    }

    public function isMentor(): bool   { return in_array($this->role, ['mentor', 'alumni']); }
    public function isMentee(): bool   { return $this->role === 'mentee'; }
    public function isAdmin(): bool    { return $this->role === 'admin'; }
    public function isVerifier(): bool { return $this->role === 'verifier'; }

    /** Mentor whose portfolio has been approved — can appear in listings and accept mentees */
    public function isActiveMentor(): bool { return $this->isMentor() && $this->mentor_status === 'active'; }

    /** Mentor registered but not yet reviewed by a verifier */
    public function isPendingVerification(): bool { return $this->isMentor() && $this->mentor_status === 'pending'; }

    public function getAverageRatingAttribute(): float
    {
        return round($this->ratings()->avg('score') ?? 0, 1);
    }

    public function getCompletedMenteesCountAttribute(): int
    {
        return \App\Models\Certificate::whereHas('learningPath', fn($q) => $q->where('mentor_id', $this->id))
            ->where('type', 'mentee')
            ->count();
    }

    public function getMentorTierAttribute(): string
    {
        if (!$this->isMentor()) return '';
        $count = $this->completed_mentees_count;
        if ($count >= 15) return 'lead';
        if ($count >= 5)  return 'senior';
        return 'junior';
    }

    public function getMentorTierLabelAttribute(): string
    {
        return match($this->mentor_tier) {
            'lead'   => 'Lead Mentor',
            'senior' => 'Senior Mentor',
            default  => 'Junior Mentor',
        };
    }

    public function getMentorTierIconAttribute(): string
    {
        return match($this->mentor_tier) {
            'lead'   => '👑',
            'senior' => '⭐',
            default  => '🔵',
        };
    }

    public function getTotalSessionsAttribute(): int
    {
        return $this->mentorMentorships()
                    ->withCount(['sessions' => fn($q) => $q->where('status', 'completed')])
                    ->get()->sum('sessions_count');
    }

    public function matchScore(User $mentee): int
    {
        $score = 0;

        $mentorSkills = $this->hasSkills()->pluck('skills.id')->toArray();
        $menteeWants  = $mentee->wantedSkills()->pluck('skills.id')->toArray();
        if (count($menteeWants) > 0) {
            $overlap = count(array_intersect($mentorSkills, $menteeWants));
            $score  += (int) min(50, ($overlap / count($menteeWants)) * 50);
        }

        if ($this->department === $mentee->department) $score += 20;

        $levels = ['100L'=>1,'200L'=>2,'300L'=>3,'400L'=>4,'500L'=>5,'Alumni'=>6];
        $ml  = $levels[$this->level]   ?? 0;
        $el  = $levels[$mentee->level] ?? 0;
        $gap = $ml - $el;
        if ($gap >= 1 && $gap <= 2) $score += 20;
        elseif ($gap === 3)         $score += 10;

        if ($this->availability) $score += 10;

        return $score;
    }
}
