<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningPath extends Model
{
    use HasFactory;

    protected $fillable = [
        'mentor_id', 'mentee_id', 'title', 'description', 'status', 'due_date',
    ];

    protected $casts = ['due_date' => 'date'];

    public function mentor()      { return $this->belongsTo(User::class, 'mentor_id'); }
    public function mentee()      { return $this->belongsTo(User::class, 'mentee_id'); }
    public function modules()     { return $this->hasMany(LearningModule::class)->orderBy('order'); }
    public function certificate()  { return $this->hasOne(Certificate::class)->where('type', 'mentee'); }
    public function certificates() { return $this->hasMany(Certificate::class); }

    public function getProgressAttribute(): int
    {
        $total = $this->modules()->withCount('tasks')->get()->sum('tasks_count');
        if ($total === 0) return 0;
        $done = $this->modules()
                     ->with(['tasks.submissions' => fn($q) =>
                         $q->where('user_id', $this->mentee_id)
                           ->where('status', 'graded')])
                     ->get()
                     ->flatMap(fn($m) => $m->tasks)
                     ->filter(fn($t) => $t->submissions->isNotEmpty())
                     ->count();
        return (int) round(($done / $total) * 100);
    }

    public function isComplete(): bool { return $this->progress === 100; }
}
