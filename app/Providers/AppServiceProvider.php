<?php

namespace App\Providers;

use App\Helpers\UserRole;
use App\User;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Repositories\Eloquent\EloquentNodeRepository;
use App\Repositories\Contracts\NodeRepository;
use App\Repositories\Eloquent\EloquentFieldRepository;
use App\Repositories\Contracts\FieldRepository;
use App\Services\CompanyService;
use App\Services\FacilityService;
use App\Services\NodeService;
use App\Services\FieldService;
use App\Services\ForecastService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
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
            'App\Repositories\Contracts\ForecastRepository',
            'App\Repositories\Eloquent\EloquentForecastRepository'
        );

        $this->app->bind(
            'App\Repositories\Contracts\WeatherRepository',
            'App\Repositories\Eloquent\EloquentWeatherRepository'
        );


        $this->app->singleton(NodeService::class, function($app) {
            $nodeRepository = $this->app->make('App\Repositories\Contracts\NodeRepository');
            $fieldService = $this->app->make('App\Services\FieldService');
            return new NodeService($nodeRepository, $fieldService);
        });

        $this->app->singleton(Forecast::class, function($app) {
            $forecastRepository = $this->app->make('App\Repositories\Contracts\ForecastRepository');
            $weatherRepository = $this->app->make('App\Repositories\Contracts\WeatherRepository');
            return new ForecastService($forecastRepository, $weatherRepository);
        });

        $this->app->singleton(FieldService::class, function($app) {
            $fieldRepository = $this->app->make('App\Repositories\Contracts\FieldRepository');
            $forecastService = $this->app->make('App\Services\ForecastService');
            return new FieldService($fieldRepository, $forecastService);
        });

        $this->app->singleton(CompanyService::class, function($app) {
            $companyRepository = $this->app->make('App\Repositories\Contracts\CompanyRepository');
            return new CompanyService($companyRepository);
        });

        $this->app->singleton(FacilityService::class, function($app) {
            $facilityRepository = $this->app->make('App\Repositories\Contracts\FacilityRepository');
            return new FacilityService($facilityRepository);
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
            return $user->hasRole($role);
        });
    }
}
