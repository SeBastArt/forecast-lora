<?php

namespace App\Providers;

use App\Role\UserRole;
use App\User;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191); 

        Blade::if('isAllowed', function (User $user, string $role)
        {
            // Admin has everything
            if ($user->hasRole(UserRole::ROLE_ADMIN)) {
                return true;
            }
            else if($user->hasRole(UserRole::ROLE_MANAGEMENT)) {
                $managementRoles = UserRole::getAllowedRoles(UserRole::ROLE_MANAGEMENT);
    
                if (in_array($role, $managementRoles)) {
                    return true;
                }
            }
    
            return $user->hasRole($role);
        });
    }
}
