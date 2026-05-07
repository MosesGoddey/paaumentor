<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hackathon extends Model
{
    protected $fillable = [
        'title', 'description', 'theme', 'rules', 'tracks', 'judge_ids',
        'status', 'registration_deadline', 'start_date', 'end_date',
        'max_team_size', 'prizes', 'created_by',
    ];

    protected $casts = [
        'tracks'                  => 'array',
        'judge_ids'               => 'array',
        'registration_deadline'   => 'date',
        'start_date'              => 'date',
        'end_date'                => 'date',
    ];

    public function teams()       { return $this->hasMany(HackathonTeam::class); }
    public function submissions() { return $this->hasMany(HackathonSubmission::class); }
    public function creator()     { return $this->belongsTo(User::class, 'created_by'); }

    public function isJudge(User $user): bool
    {
        return in_array($user->id, $this->judge_ids ?? []);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'open'      => '#16a34a',
            'ongoing'   => '#2563eb',
            'judging'   => '#ea580c',
            'completed' => '#d97706',
            default     => '#6b7280',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft'     => 'Draft',
            'open'      => 'Registration Open',
            'ongoing'   => 'In Progress',
            'judging'   => 'Under Judging',
            'completed' => 'Completed',
        };
    }
}
