<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed facility
 * @property mixed name
 */
class Node extends Model
{
    use HasFactory;

    //public function fields(){
    //    return $this->hasMany(Field::class, 'node_id');
    //}

    /**
     * Get all of the fields for the node.
     */
    public function fields()
    {
        return $this->morphToMany(Field::class, 'fieldable');
    }

    public function type()
    {
        return $this->belongsTo(NodeType::class, 'node_type_id', 'id');
    }

    public function nodeData()
    {
        return $this->hasMany(NodeData::class, 'node_id');
    }

    public function getErrorLevel()
    {
        $errorLevel = 0;
        foreach ($this->fields()->get() as $key => $field) {
            $errorLevel = max($field->getErrorLevel(), $errorLevel);
        }
        return $errorLevel;
    }

    public function getRSSI()
    {
        return (random_int(50, 120) - 120);
    }

    public function facility(){
        return $this->belongsTo(Facility::class);
    }

    public function preset(){
        return $this->belongsTo(Preset::class);
    }

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'dev_eui', 'node_type_id', 'facility_id', 'preset_id', 'show_forecast'
    ];

    protected $attributes=[
        'show_forecast' => false
    ];



    public static function boot()
    {
        parent::boot();
        static::deleting(function($node){
            foreach ($node->fields()->get() as $field) {
                $field->delete();
            }
            foreach ($node->nodeData as $data) {
                $data->delete();
            }
        });
    }

}
