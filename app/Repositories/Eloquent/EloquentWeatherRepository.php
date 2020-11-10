<?php

namespace App\Repositories\Eloquent;

use App\Weather;
use App\Repositories\Contracts\WeatherRepository;

class EloquentWeatherRepository extends AbstractEloquentRepository implements WeatherRepository
{
    /**
     * Create new eloquent Weather repository instance.
     *
     * @param \App\Weather $Weather
     */
    public function __construct(Weather $Weather)
    {
        $this->model = $Weather;
    }
}