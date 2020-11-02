<?php

namespace App;

use Gurgentil\LaravelEloquentSequencer\Traits\Sequenceable;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    use Sequenceable;

    protected $fillable = [
        'name', 'visible', 'unit', 'node_id',
        'primarycolor', 'secondarycolor',
        'isdashed', 'isfilled',
        'position'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'primarycolor' => 'string',
        'secondarycolor' => 'string',
    ];

    protected static $sequenceableKeys = [
        'node_id',
    ];

    public function data(){
        return $this->hasMany(FieldData::class);
    }

    public function last(){
        return $this->hasMany(FieldData::class);
    }

    public function node(){
        return $this->belongsTo(Node::class);
    }
}
