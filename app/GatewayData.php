<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GatewayData extends Model
{
        /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'node_data_id', 
        'gateway_id', 
        'snr',
        'rssi'
    ];

    public function gateway(){
        return $this->belongsTo(Gateway::class);
    }

    public function nodeData(){
        return $this->belongsTo(NodeData::class);
    }
}
