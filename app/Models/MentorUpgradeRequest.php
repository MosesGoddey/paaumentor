<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MentorUpgradeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'mentee_id', 'mentor_id', 'status',
        'mentor_note', 'mentor_recommended_at',
        'admin_id', 'admin_note', 'reviewed_at',
    ];

    protected $casts = [
        'mentor_recommended_at' => 'datetime',
        'reviewed_at'           => 'datetime',
    ];

    public function mentee() { return $this->belongsTo(User::class, 'mentee_id'); }
    public function mentor() { return $this->belongsTo(User::class, 'mentor_id'); }
    public function admin()  { return $this->belongsTo(User::class, 'admin_id'); }

    public function isPendingAssessment(): bool { return $this->status === 'pending_assessment'; }
    public function isPending():           bool { return $this->status === 'pending'; }
    public function isRecommended():       bool { return $this->status === 'recommended'; }
    public function isApproved():          bool { return $this->status === 'approved'; }
    public function isRejected():          bool { return $this->status === 'rejected'; }

    public function upgradeAssessment() { return $this->hasOne(\App\Models\UpgradeAssessment::class); }
}
