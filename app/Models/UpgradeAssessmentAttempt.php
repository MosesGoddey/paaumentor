<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpgradeAssessmentAttempt extends Model
{
    protected $fillable = [
        'upgrade_assessment_id', 'user_id', 'upgrade_request_id',
        'question_ids', 'answers', 'score', 'max_score',
        'passed', 'tab_switches', 'started_at', 'completed_at',
    ];

    protected $casts = [
        'question_ids' => 'array',
        'answers'      => 'array',
        'passed'       => 'boolean',
        'started_at'   => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function upgradeAssessment() { return $this->belongsTo(UpgradeAssessment::class); }
    public function upgradeRequest()    { return $this->belongsTo(MentorUpgradeRequest::class); }
    public function user()              { return $this->belongsTo(User::class); }

    public function isCompleted(): bool { return $this->completed_at !== null; }
}
