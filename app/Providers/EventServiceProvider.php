<?php

namespace App\Providers;

use App\Events\ServiceOrderCompleted;
use App\Listeners\NotifyClientOfCompletion;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ServiceOrderCompleted::class => [
            NotifyClientOfCompletion::class,
        ],
    ];

    public function boot(): void
    {
        //
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
