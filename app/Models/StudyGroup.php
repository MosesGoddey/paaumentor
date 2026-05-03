<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyGroup extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'topic', 'description', 'created_by', 'max_members', 'is_open'];

    public function creator()  { return $this->belongsTo(User::class, 'created_by'); }
    public function members()  { return $this->hasMany(StudyGroupMember::class); }
    public function users()    { return $this->belongsToMany(User::class, 'study_group_members')->withPivot('role')->withTimestamps(); }
    public function messages() { return $this->hasMany(StudyGroupMessage::class); }

    public function isMember(User $user): bool
    {
        return $this->members()->where('user_id', $user->id)->exists();
    }

    public function isAdmin(User $user): bool
    {
        return $this->members()->where('user_id', $user->id)->where('role', 'admin')->exists();
    }

    public function roomName(): string
    {
        return 'paaumentor-group-' . $this->id . '-' . substr(md5(config('app.key')), 0, 12);
    }
}
