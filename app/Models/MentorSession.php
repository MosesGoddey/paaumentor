<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MentorSession extends Model
{
    use HasFactory;

    protected $table = 'sessions';

    protected $fillable = [
        'mentorship_id', 'skill_exchange_request_id', 'title', 'description', 'type', 'room',
        'status', 'call_outcome', 'scheduled_at', 'started_at', 'ended_at', 'duration_minutes',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at'   => 'datetime',
        'ended_at'     => 'datetime',
    ];

    public function mentorship()           { return $this->belongsTo(Mentorship::class); }
    public function skillExchangeRequest() { return $this->belongsTo(SkillExchangeRequest::class); }

    public function participantIds(): array
    {
        if ($this->mentorship_id && $this->mentorship) {
            return [$this->mentorship->mentor_id, $this->mentorship->mentee_id];
        }
        if ($this->skill_exchange_request_id && $this->skillExchangeRequest) {
            $req = $this->skillExchangeRequest;
            return [$req->requester_id, $req->exchange->user_id];
        }
        return [];
    }
}
