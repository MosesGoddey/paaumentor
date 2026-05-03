<?php

namespace App\Policies;

use App\Models\{User, LearningPath};

class LearningPathPolicy
{
    public function view(User $user, LearningPath $learningPath): bool
    {
        return $user->id === $learningPath->mentor_id
            || $user->id === $learningPath->mentee_id
            || $user->isAdmin();
    }

    public function manage(User $user, LearningPath $learningPath): bool
    {
        return $user->id === $learningPath->mentor_id || $user->isAdmin();
    }
}
