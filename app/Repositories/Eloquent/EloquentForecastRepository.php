<?php

namespace App\Repositories\Eloquent;

use App\Models\City;
use App\Models\Forecast;
use App\Repositories\Contracts\ForecastRepository;

class EloquentForecastRepository extends AbstractEloquentRepository implements ForecastRepository
{
    /**
     * Create new eloquent Forecast repository instance.
     *
     * @param \App\Models\Forecast $Forecast
     */
    public function __construct(Forecast $Forecast)
    {
        $this->model = $Forecast;
    }

    public function getSortedForecastItems($id) 
    { 
        return $this->find($id)->forecastItems->sortBy('valid_from')->all();    
    }

    public function findByCity(City $city)
    {
        return $this->model->where('city_id', $city->id)->first();
    }

    public function getFirstForecastItem($id){
        return $this->find($id)->forecastItems->sortBy('valid_from')->first();
    }
}