<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\NodeType;

class Node extends Model
{
     public function fields(){
        return $this->hasMany(Field::class, 'node_id');
    }

    public function type(){ 
        return $this->belongsTo(NodeType::class, 'node_type_id', 'id');
    }

    public function city(){ 
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public function nodeData(){
        return $this->hasMany(NodeData::class, 'node_id');
    }

    public function facility(){
        return $this->belongsTo(Facility::class);
    }

    public function primaryField(){
        return $this->fields->sortBy('position')->first();
    }

    public function getErrorLevel(){
        return $this->ErrorLevel;
    }

    public function getRSSI(){
        return (random_int(50, 120) - 120);
    }

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'dev_eui', 'node_type_id', 'facility_id', 'city_id', 'errorLevel'
    ];
}
