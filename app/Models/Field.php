<?php

namespace App\Models;

use Gurgentil\LaravelEloquentSequencer\Traits\Sequenceable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Field extends Model
{
    //use Sequenceable, HasFactory;
    use HasFactory;

    protected $fillable = [
        'name', 'visible', 'unit', //'node_id',
        'primary_color', 'secondary_color',
        'is_dashed', 'is_filled',
        'error_level', 
        'check_lower_limit', 'lower_limit',
        'check_upper_limit', 'upper_limit'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'primary_color' => 'string',
        'secondary_color' => 'string',
    ];

    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }

    public function isExceeded(){
        return ($this->getErrorLevel() > 0) ? true : false;
    }

    public function getErrorLevel(){
        $errorLevel = 0;
        foreach ($this->alerts()->get() as $key => $alert) {
            $errorLevel = max($errorLevel, $alert->error_level);
        }
        return $errorLevel;
    }

    public function nodes()
    {
        return $this->morphedByMany(Node::class, 'fieldable');
    }

    public function presets()
    {
        return $this->morphedByMany(Preset::class, 'fieldable');
    }
}
