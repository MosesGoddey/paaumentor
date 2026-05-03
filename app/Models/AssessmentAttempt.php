<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentAttempt extends Model
{
    protected $fillable = [
        'assessment_id', 'user_id', 'certificate_request_id',
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

    public function assessment()          { return $this->belongsTo(Assessment::class); }
    public function user()                { return $this->belongsTo(User::class); }
    public function certificateRequest()  { return $this->belongsTo(CertificateRequest::class); }

    public function isCompleted(): bool { return $this->completed_at !== null; }
}
