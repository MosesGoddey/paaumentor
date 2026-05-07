<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HackathonScore extends Model
{
    protected $fillable = [
        'submission_id', 'judge_id',
        'innovation', 'execution', 'impact', 'presentation', 'notes',
    ];

    public function submission() { return $this->belongsTo(HackathonSubmission::class, 'submission_id'); }
    public function judge()      { return $this->belongsTo(User::class, 'judge_id'); }

    public function getTotalAttribute(): int
    {
        return $this->innovation + $this->execution + $this->impact + $this->presentation;
    }
}
