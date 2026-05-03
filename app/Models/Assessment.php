<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    protected $fillable = [
        'learning_path_id', 'passing_score', 'time_per_question',
        'questions_per_attempt', 'questions_ready', 'questions_generated_at',
    ];

    protected $casts = [
        'questions_ready'        => 'boolean',
        'questions_generated_at' => 'datetime',
    ];

    public function learningPath() { return $this->belongsTo(LearningPath::class); }
    public function questions()    { return $this->hasMany(AssessmentQuestion::class); }
    public function attempts()     { return $this->hasMany(AssessmentAttempt::class); }
}
