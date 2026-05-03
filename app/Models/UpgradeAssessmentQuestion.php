<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpgradeAssessmentQuestion extends Model
{
    protected $fillable = [
        'upgrade_assessment_id', 'question', 'options', 'correct_answer', 'points', 'order',
    ];

    protected $casts = [
        'options'        => 'array',
        'correct_answer' => 'integer',
    ];

    public function upgradeAssessment() { return $this->belongsTo(UpgradeAssessment::class); }
}
