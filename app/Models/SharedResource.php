<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SharedResource extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'file_path', 'file_name', 'file_size',
        'mime_type', 'uploader_id', 'study_group_id', 'mentorship_id', 'is_public',
    ];

    public function uploader()   { return $this->belongsTo(User::class, 'uploader_id'); }
    public function studyGroup() { return $this->belongsTo(StudyGroup::class); }
    public function mentorship() { return $this->belongsTo(Mentorship::class); }

    public function isPreviewable(): bool
    {
        $ext = strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf']);
    }
}
