<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpgradeAssessment extends Model
{
    protected $fillable = [
        'upgrade_request_id', 'passing_score', 'time_per_question',
        'questions_per_attempt', 'questions_ready', 'questions_generated_at',
    ];

    protected $casts = [
        'questions_ready'        => 'boolean',
        'questions_generated_at' => 'datetime',
    ];

    public function upgradeRequest() { return $this->belongsTo(MentorUpgradeRequest::class); }
    public function questions()      { return $this->hasMany(UpgradeAssessmentQuestion::class); }
    public function attempts()       { return $this->hasMany(UpgradeAssessmentAttempt::class); }
}
