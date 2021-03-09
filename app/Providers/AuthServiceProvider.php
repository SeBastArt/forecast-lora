<?php

namespace App\Providers;

use App\Helpers\RoleChecker;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //ADMIN can do what he want -> everytime
        Gate::before(function ($user, $ability) {
            if (RoleChecker::check($user, 'ROLE_ADMIN')) {
                return true;
            }
        });

        Passport::routes();
        //
    }
}
