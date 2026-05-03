<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = ['mentorship_id', 'skill_exchange_request_id', 'last_message_at'];
    protected $casts    = ['last_message_at' => 'datetime'];

    public function mentorship()           { return $this->belongsTo(Mentorship::class); }
    public function skillExchangeRequest() { return $this->belongsTo(SkillExchangeRequest::class); }
    public function messages()             { return $this->hasMany(Message::class)->orderBy('created_at'); }

    public function otherUser(int $forUserId): ?User
    {
        if ($this->mentorship_id && $this->mentorship) {
            return $this->mentorship->mentor_id === $forUserId
                ? $this->mentorship->mentee
                : $this->mentorship->mentor;
        }
        if ($this->skillExchangeRequest) {
            $req = $this->skillExchangeRequest;
            return $req->requester_id === $forUserId
                ? $req->exchange->user
                : $req->requester;
        }
        return null;
    }

    public function getSubtitleAttribute(): string
    {
        if ($this->mentorship_id && $this->mentorship) {
            return $this->mentorship->topic ?? '';
        }
        if ($this->skillExchangeRequest) {
            $req = $this->skillExchangeRequest;
            return ($req->exchange->offering ?? '') . ' ↔ ' . ($req->exchange->seeking ?? '');
        }
        return '';
    }

    public function participantIds(): array
    {
        if ($this->mentorship_id && $this->mentorship) {
            return [$this->mentorship->mentor_id, $this->mentorship->mentee_id];
        }
        if ($this->skillExchangeRequest) {
            $req = $this->skillExchangeRequest;
            return [$req->requester_id, $req->exchange->user_id];
        }
        return [];
    }

    public function getUnreadCountForAttribute(int $userId): int
    {
        return $this->messages()
                    ->where('sender_id', '!=', $userId)
                    ->whereNull('read_at')
                    ->count();
    }
}
