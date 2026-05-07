<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HackathonTeam extends Model
{
    protected $fillable = [
        'hackathon_id', 'name', 'join_code', 'track',
        'is_locked', 'coach_id', 'coach_status',
    ];

    protected $casts = ['is_locked' => 'boolean'];

    public function hackathon()  { return $this->belongsTo(Hackathon::class); }
    public function coach()      { return $this->belongsTo(User::class, 'coach_id'); }
    public function submission() { return $this->hasOne(HackathonSubmission::class, 'team_id'); }

    public function users()
    {
        return $this->belongsToMany(User::class, 'hackathon_team_members', 'team_id', 'user_id')
                    ->withPivot('is_lead')
                    ->withTimestamps();
    }

    public function lead()
    {
        return $this->users()->wherePivot('is_lead', true);
    }

    public function hasMember(int $userId): bool
    {
        return $this->users->contains('id', $userId);
    }

    public function isFull(): bool
    {
        return $this->users->count() >= $this->hackathon->max_team_size;
    }
}
