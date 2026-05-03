<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningModule extends Model
{
    use HasFactory;

    protected $fillable = ['learning_path_id', 'title', 'order'];

    public function learningPath() { return $this->belongsTo(LearningPath::class); }
    public function tasks()        { return $this->hasMany(LearningTask::class)->orderBy('order'); }
}
