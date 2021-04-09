<?php // Code within app\Facades\Trace.php

namespace App\Helpers;

use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Jaeger\Config;
use OpenTracing\Formats;

class Trace
{
    /**
     * @throws \Exception
     */
    public function Init()
    {
          // Setup a unique ID for each request. This will allow us to find
        // the request trace in the jaeger ui
        app()->instance('context.uuid', Str::uuid());

        // Get the base config object
        $config = Config::getInstance();

        // If in development or testing, you can use this to change
        // the tracer to a mocked one (NoopTracer)
        //
        // if (!app()->environment('production')) {
        //     $config->setDisabled(true);
        // }

        // Start the tracer with a service name and the jaeger address
        $tracer = $config->initTracer(config('app.name'), 'localhost:6831');

        // Set the tracer as a singleton in the IOC container
        app()->instance('context.tracer', $tracer);

        $parentContext = $tracer->extract(Formats\TEXT_MAP, request()->header());

        //error_log( print_r($tracer->extract(Formats\TEXT_MAP, request()->header()), TRUE) );
        // Start the global span, it'll wrap the request/console lifecycle

        $globalSpan = ($parentContext == null) ? $tracer->startSpan('StartApp') : $tracer->startSpan('StartApp', ['child_of' => $parentContext]);
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
        app()->instance('context.tracer.globalSpan', $globalSpan);

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
    }

    public function StartSpan(string $name){
        $tracer = app('context.tracer');
        $activeSpan = ($tracer->getActiveSpan() == null) ? app('context.tracer.globalSpan') : $tracer->getActiveSpan();
        $newScope = $tracer->startActiveSpan($name, [
            'child_of' => $activeSpan
        ]);
    }

    public function setTag(string $key, string $value)
    {
        $tracer = app('context.tracer');
        $tracer->getScopeManager()->getActive()->getSpan()->setTag($key, $value);
    }

    public function EndSpan()
    {
        $tracer = app('context.tracer');
        $tracer->getScopeManager()->getActive()->close();
    }
}
