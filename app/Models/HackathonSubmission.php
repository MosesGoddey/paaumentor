<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HackathonSubmission extends Model
{
    protected $fillable = [
        'hackathon_id', 'team_id', 'title', 'description',
        'github_url', 'demo_url', 'deck_url', 'status', 'submitted_at',
    ];

    protected $casts = ['submitted_at' => 'datetime'];

    public function team()      { return $this->belongsTo(HackathonTeam::class, 'team_id'); }
    public function hackathon() { return $this->belongsTo(Hackathon::class); }
    public function scores()    { return $this->hasMany(HackathonScore::class, 'submission_id'); }

    public function getAverageScoreAttribute(): float
    {
        if ($this->scores->isEmpty()) return 0;
        return $this->scores->avg(fn($s) => $s->innovation + $s->execution + $s->impact + $s->presentation);
    }

    public function getScoreSummaryAttribute(): array
    {
        if ($this->scores->isEmpty()) return ['innovation' => 0, 'execution' => 0, 'impact' => 0, 'presentation' => 0];
        return [
            'innovation'   => round($this->scores->avg('innovation'), 1),
            'execution'    => round($this->scores->avg('execution'), 1),
            'impact'       => round($this->scores->avg('impact'), 1),
            'presentation' => round($this->scores->avg('presentation'), 1),
        ];
    }
}
