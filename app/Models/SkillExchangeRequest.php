<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkillExchangeRequest extends Model
{
    protected $fillable = ['exchange_id', 'requester_id', 'message', 'status'];

    public function exchange()
    {
        return $this->belongsTo(SkillExchange::class, 'exchange_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function conversation()
    {
        return $this->hasOne(Conversation::class, 'skill_exchange_request_id');
    }
}
