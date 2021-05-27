<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $array)
 */
class NodeData extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'node_id', 'payload',
        'latitude', 'longitude',
        'snr', 'rssi',
        'created_at',
    ];

    public function node(){
        return $this->belongsTo(Node::class);
    }

    public function gatewayData(){
        return $this->hasMany(GatewayData::class);
    }

    public static function boot()
    {
        parent::boot();
        static::deleting(function($nodeData){
            foreach ($nodeData->gatewayData as $gtwData) {
                $gtwData->delete();
            }
        });
    }
}
