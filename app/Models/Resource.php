<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;

    protected $fillable = [
        'uploaded_by', 'mentorship_id', 'title', 'file_path', 'url', 'type', 'file_size',
    ];

    public function uploader()   { return $this->belongsTo(User::class, 'uploaded_by'); }
    public function mentorship() { return $this->belongsTo(Mentorship::class); }

    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size ?? 0;
        if ($bytes < 1024)    return "{$bytes} B";
        if ($bytes < 1048576) return round($bytes / 1024) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }
}
