<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupSession extends Model
{
    protected $fillable = [
        'host_id', 'title', 'description', 'type', 'room',
        'status', 'max_participants', 'scheduled_at', 'started_at', 'ended_at', 'duration_minutes',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at'   => 'datetime',
        'ended_at'     => 'datetime',
    ];

    public function host()    { return $this->belongsTo(User::class, 'host_id'); }
    public function members() { return $this->belongsToMany(User::class, 'group_session_members')->withPivot('role', 'joined_at')->withTimestamps(); }

    public function isParticipant(int $userId): bool
    {
        return $this->host_id === $userId
            || $this->members()->where('user_id', $userId)->exists();
    }

    public function participantCount(): int
    {
        return $this->members()->count() + 1; // +1 for host
    }
}
