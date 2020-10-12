<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gateway extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'dev_eui', 
        'latitude', 'longitude',
        'lastseen'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'lastseen' => 'datetime',
    ];
}
