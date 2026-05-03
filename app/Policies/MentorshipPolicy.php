<?php

namespace App\Policies;

use App\Models\{User, Mentorship};

class MentorshipPolicy
{
    public function respond(User $user, Mentorship $mentorship): bool
    {
        return $user->id === $mentorship->mentor_id;
    }

    public function view(User $user, Mentorship $mentorship): bool
    {
        return $user->id === $mentorship->mentor_id
            || $user->id === $mentorship->mentee_id
            || $user->isAdmin();
    }
}
