<?php

namespace App\Policies;

use App\Models\{User, Conversation};

class ConversationPolicy
{
    public function view(User $user, Conversation $conversation): bool
    {
        return $user->id === $conversation->mentorship->mentor_id
            || $user->id === $conversation->mentorship->mentee_id
            || $user->isAdmin();
    }

    public function participate(User $user, Conversation $conversation): bool
    {
        return $user->id === $conversation->mentorship->mentor_id
            || $user->id === $conversation->mentorship->mentee_id;
    }
}
