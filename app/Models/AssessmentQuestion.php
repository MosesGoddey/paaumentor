<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentQuestion extends Model
{
    protected $fillable = [
        'assessment_id', 'question', 'options', 'correct_answer', 'points', 'order',
    ];

    protected $casts = [
        'options'        => 'array',
        'correct_answer' => 'integer',
        'points'         => 'integer',
    ];

    public function assessment() { return $this->belongsTo(Assessment::class); }
}
