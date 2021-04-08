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
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Jaeger\Config;

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

        // Setup a unique ID for each request. This will allow us to find
        // the request trace in the jaeger ui
        $this->app->instance('context.uuid', Str::uuid());

        // Get the base config object
        $config = Config::getInstance();

        // If in development or testing, you can use this to change
        // the tracer to a mocked one (NoopTracer)
        //
        // if (!app()->environment('production')) {
        //     $config->setDisabled(true);
        // }

        // Start the tracer with a service name and the jaeger address
        $tracer = $config->initTracer('starter-kit', 'localhost:6831');

        // Set the tracer as a singleton in the IOC container
        $this->app->instance('context.tracer', $tracer);

        // Start the global span, it'll wrap the request/console lifecycle
        $globalSpan = $tracer->startSpan('app');
        // Set the uuid as a tag for this trace
        $globalSpan->setTag('uuid', app('context.uuid')->toString());

        // If running in console (a.k.a a job or a command) set the
        // type tag accordingly
        $type = 'http';
        if (app()->runningInConsole()) {
            $type = 'console';
        }
        $globalSpan->setTag('type', $type);

        // Save the global span as a singleton too
        $this->app->instance('context.tracer.globalSpan', $globalSpan);

        // When the app terminates we must finish the global span
        // and send the trace to the jaeger agent.
        app()->terminating(function () {
            app('context.tracer.globalSpan')->finish();
            app('context.tracer')->flush();
        });

        // Listen for each logged message and attach it to the global span
        Event::listen(MessageLogged::class, function (MessageLogged $e) {
            $tracer = app('context.tracer');
            $activeSpan = ($tracer->getActiveSpan() == null) ? app('context.tracer.globalSpan') : $tracer->getActiveSpan();
            $activeSpan->log((array) $e);
        });

        // Listen for the request handled event and set more tags for the trace
        Event::listen(RequestHandled::class, function (RequestHandled $e) {
            app('context.tracer.globalSpan')->setTag('user_id', auth()->user()->id ?? "-");
            app('context.tracer.globalSpan')->setTag('request_host',$e->request->getHost());
            app('context.tracer.globalSpan')->setTag('request_path', $path = $e->request->path());
            app('context.tracer.globalSpan')->setTag('request_method', $e->request->method());
            app('context.tracer.globalSpan')->setTag('api', str_contains($path, 'api'));
            app('context.tracer.globalSpan')->setTag('response_status', $e->response->getStatusCode());
            app('context.tracer.globalSpan')->setTag('error', !$e->response->isSuccessful());
        });

         // Also listen for queries and log then,
        // it also receives the log in the MessageLogged event above
        DB::listen(function ($query) {
            Log::debug("[DB Query] {$query->connection->getName()}", [
                'query' => str_replace('"', "'", $query->sql),
                'time' => $query->time.'ms',
            ]);
        });

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
