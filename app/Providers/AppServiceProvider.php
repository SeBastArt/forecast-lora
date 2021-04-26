<?php

namespace App\Providers;

use App\Helpers\UserRole;
use App\Models\User;
use App\Services\AlertService;
use App\Services\CompanyService;
use App\Services\FacilityService;
use App\Services\FieldService;
use App\Services\ForecastService;
use App\Services\NodeService;
use App\Services\PresetService;
use App\Services\UserService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
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
            //Telescope - doesn't work in testing
            if (!$this->app->environment('testing')) {
                $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
                $this->app->register(TelescopeServiceProvider::class);
            }

            $this->app->bind(
                'App\Repositories\Contracts\UserRepository',
                'App\Repositories\Eloquent\EloquentUserRepository'
            );

            $this->app->bind(
                'App\Repositories\Contracts\CompanyRepository',
                'App\Repositories\Eloquent\EloquentCompanyRepository'
            );
    
            $this->app->bind(
                'App\Repositories\Contracts\FacilityRepository',
                'App\Repositories\Eloquent\EloquentFacilityRepository'
            );
    
            $this->app->bind(
                'App\Repositories\Contracts\NodeRepository',
                'App\Repositories\Eloquent\EloquentNodeRepository'
            );
    
            $this->app->bind(
                'App\Repositories\Contracts\FieldRepository',
                'App\Repositories\Eloquent\EloquentFieldRepository'
            );
    
            $this->app->bind(
                'App\Repositories\Contracts\PresetRepository',
                'App\Repositories\Eloquent\EloquentPresetRepository'
            );

            $this->app->bind(
                'App\Repositories\Contracts\AlertRepository',
                'App\Repositories\Eloquent\EloquentAlertRepository'
            );

            $this->app->bind(
                'App\Repositories\Contracts\ForecastRepository',
                'App\Repositories\Eloquent\EloquentForecastRepository'
            );
    
            $this->app->bind(
                'App\Repositories\Contracts\WeatherRepository',
                'App\Repositories\Eloquent\EloquentWeatherRepository'
            );

            $this->app->singleton(AlertService::class, function($app) {
                $alertRepository = $this->app->make('App\Repositories\Contracts\AlertRepository');
                return new AlertService($alertRepository);
            });

            $this->app->singleton(PresetService::class, function($app) {
                $presetRepository = $this->app->make('App\Repositories\Contracts\PresetRepository');
                $nodeService = $this->app->make('App\Services\NodeService');
                return new PresetService($presetRepository, $nodeService);
            });

            $this->app->singleton(UserService::class, function($app) {
                $userRepository = $this->app->make('App\Repositories\Contracts\UserRepository');
                return new UserService($userRepository);
            });

          
            $this->app->singleton(FieldService::class, function($app) {
                $fieldRepository = $this->app->make('App\Repositories\Contracts\FieldRepository');
                return new FieldService($fieldRepository);
            });

          
            $this->app->singleton(NodeService::class, function($app) {
                $nodeRepository = $this->app->make('App\Repositories\Contracts\NodeRepository');
                $fieldService = $this->app->make('App\Services\FieldService');
                $alertService = $this->app->make('App\Services\AlertService');
                return new NodeService($nodeRepository, $fieldService, $alertService);
            });

            $this->app->singleton(CompanyService::class, function($app) {
                $companyRepository = $this->app->make('App\Repositories\Contracts\CompanyRepository');
                return new CompanyService($companyRepository);
            });
    
            $this->app->singleton(FacilityService::class, function($app) {
                $facilityRepository = $this->app->make('App\Repositories\Contracts\FacilityRepository');
                $nodeService = $this->app->make('App\Services\NodeService');
                $forecastService = $this->app->make('App\Services\ForecastService');
                return new FacilityService($facilityRepository, $nodeService, $forecastService);
            });

            $this->app->singleton(Forecast::class, function($app) {
                $forecastRepository = $this->app->make('App\Repositories\Contracts\ForecastRepository');
                $weatherRepository = $this->app->make('App\Repositories\Contracts\WeatherRepository');
                return new ForecastService($forecastRepository, $weatherRepository);
            });
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
            else if($user->hasRole(UserRole::ROLE_FINANCE)) {
                $managementRoles = UserRole::getAllowedRoles(UserRole::ROLE_FINANCE);
    
                if (in_array($role, $managementRoles)) {
                    return true;
                }
            }
            else if($user->hasRole(UserRole::ROLE_ACCOUNT_MANAGER)) {
                $managementRoles = UserRole::getAllowedRoles(UserRole::ROLE_ACCOUNT_MANAGER);
    
                if (in_array($role, $managementRoles)) {
                    return true;
                }
            }
            return $user->hasRole($role);
        });
    }
}
