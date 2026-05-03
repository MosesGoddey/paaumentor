<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mentorship extends Model
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

    public function mentor()      { return $this->belongsTo(User::class, 'mentor_id'); }
    public function mentee()      { return $this->belongsTo(User::class, 'mentee_id'); }
    public function sessions()    { return $this->hasMany(MentorSession::class); }
    public function conversation(){ return $this->hasOne(Conversation::class); }
    public function learningPaths(){ return $this->hasMany(LearningPath::class); }
    public function ratings()     { return $this->hasMany(Rating::class); }

    public function getCompletedSessionsCountAttribute(): int
    {
        return $this->sessions()->where('status', 'completed')->count();
    }
}
