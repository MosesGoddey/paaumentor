<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateRequest extends Model
{
    protected $fillable = [
        'learning_path_id', 'mentee_id', 'mentor_id',
        'status', 'assessment_score', 'assessment_passed_at',
        'mentor_reflection', 'mentor_reflection_submitted_at',
        'verifier_id', 'verifier_note', 'verified_at',
    ];

    protected $casts = [
        'assessment_passed_at'           => 'datetime',
        'mentor_reflection_submitted_at' => 'datetime',
        'verified_at'                    => 'datetime',
    ];

    public function learningPath()  { return $this->belongsTo(LearningPath::class); }
    public function mentee()        { return $this->belongsTo(User::class, 'mentee_id'); }
    public function mentor()        { return $this->belongsTo(User::class, 'mentor_id'); }
    public function verifier()      { return $this->belongsTo(User::class, 'verifier_id'); }
    public function attempts()      { return $this->hasMany(AssessmentAttempt::class); }

    public function isPendingAssessment(): bool       { return $this->status === 'pending_assessment'; }
    public function isPendingMentorReflection(): bool { return $this->status === 'pending_mentor_reflection'; }
    public function isPendingVerifier(): bool         { return $this->status === 'pending_verifier'; }
    public function isApproved(): bool                { return $this->status === 'approved'; }
    public function isRejected(): bool                { return $this->status === 'rejected'; }
    public function hasReflection(): bool             { return !empty($this->mentor_reflection); }
}
