<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NodeData extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'node_id', 'payload',
        'latitude', 'longitude',
        'snr', 'rssi',
        'created_at'
    ];

    public function fieldData(){
        return $this->hasMany(FieldData::class);
    }

    public function node(){
        return $this->belongsTo(Node::class);
    }

    public function gatewayData(){
        return $this->hasMany(GatewayData::class);
    }
}
