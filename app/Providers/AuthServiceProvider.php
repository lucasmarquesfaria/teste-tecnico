<?php

namespace App\Providers;

use App\Models\ServiceOrder;
use App\Policies\ServiceOrderPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        ServiceOrder::class => ServiceOrderPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('create-service-order', function ($user) {
            return $user->role === 'technician';
        });
    }
}
