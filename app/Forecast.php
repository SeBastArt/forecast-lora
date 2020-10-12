<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Forecast extends Model
{
    public function forecastItems(){
        return $this->hasMany(ForecastItem::class);
    }

    public function city(){ 
        return $this->belongsTo(City::class);
    }

    protected $fillable = [
        'city_id',
        'sunrise',
        'sunset'
    ];
}
