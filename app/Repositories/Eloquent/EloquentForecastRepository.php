<?php

namespace App\Repositories\Eloquent;

use App\Forecast;
use App\Repositories\Contracts\ForecastRepository;

class EloquentForecastRepository extends AbstractEloquentRepository implements ForecastRepository
{
    /**
     * Create new eloquent Forecast repository instance.
     *
     * @param \App\Forecast $Forecast
     */
    public function __construct(Forecast $Forecast)
    {
        $this->model = $Forecast;
    }

    public function getSortedForecastItems($id) 
    { 
        return $this->find($id)->forecastItems->sortBy('valid_from')->all();    
    }

    public function findByCityId($cityId)
    {
        return $this->model->where('city_id', $cityId)->first();
    }

    public function getFirstForecastItem($id){
        return $this->find($id)->forecastItems->sortBy('valid_from')->first();
    }
}