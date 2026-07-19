<?php

namespace App\Providers;

use App\Models\{Mentorship, Conversation, LearningPath};
use App\Policies\{MentorshipPolicy, ConversationPolicy, LearningPathPolicy};
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Gate::policy(Mentorship::class,    MentorshipPolicy::class);
        Gate::policy(Conversation::class,  ConversationPolicy::class);
        Gate::policy(LearningPath::class,  LearningPathPolicy::class);

        Paginator::defaultView('vendor.pagination.paaumentor');
        Paginator::defaultSimpleView('vendor.pagination.paaumentor');
    }
}
