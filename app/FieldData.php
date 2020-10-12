<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FieldData extends Model
{
    public function nodeData(){
        return $this->belongsTo(NodeData::class);
    }

    public function field(){
        return $this->belongsTo(Field::class);
    }

    protected $fillable = [
        'node_data_id',
        'field_id',
        'value',
        'created_at'
    ];
}
