<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'learning_task_id', 'user_id', 'notes', 'file_path', 'status', 'score', 'feedback',
    ];

    public function task() { return $this->belongsTo(LearningTask::class, 'learning_task_id'); }
    public function user() { return $this->belongsTo(User::class); }
}
