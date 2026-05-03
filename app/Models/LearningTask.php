<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'learning_module_id', 'title', 'description', 'order', 'max_score', 'is_locked',
    ];

    protected $casts = ['is_locked' => 'boolean'];

    public function module()      { return $this->belongsTo(LearningModule::class, 'learning_module_id'); }
    public function submissions() { return $this->hasMany(TaskSubmission::class); }
}
