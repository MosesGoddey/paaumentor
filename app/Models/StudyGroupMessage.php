<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyGroupMessage extends Model
{
    protected $fillable = ['study_group_id', 'sender_id', 'body', 'file_path', 'file_name', 'type'];

    public function group()  { return $this->belongsTo(StudyGroup::class, 'study_group_id'); }
    public function sender() { return $this->belongsTo(User::class, 'sender_id'); }
}
