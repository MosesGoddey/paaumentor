<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkillExchange extends Model
{
    protected $fillable = ['user_id', 'offering', 'seeking', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function requests()
    {
        return $this->hasMany(SkillExchangeRequest::class, 'exchange_id');
    }

    public function pendingRequests()
    {
        return $this->hasMany(SkillExchangeRequest::class, 'exchange_id')->where('status', 'pending');
    }
}
