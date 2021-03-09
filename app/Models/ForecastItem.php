<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForecastItem extends Model
{
    public function weather(){ 
        return $this->belongsTo(Weather::class, 'weather_id', 'id');
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'temp', 'humidity', 'weather_id', 'valid_from', 'forecast_id'
    ];
}
