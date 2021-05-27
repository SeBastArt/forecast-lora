<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $array)
 */
class GatewayData extends Model
{
    use HasFactory;

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
