<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['facilities'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function getErrorLevel(){
        $errorLevel = 1; //everything is fine
        foreach ($this->facilities as $key => $facility) {
            $errorLevel = max($facility->getErrorLevel(), $errorLevel);
        }
        return $errorLevel;
    }

    public function facilities()
    {
        return $this->hasMany(Facility::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'city', 'country', 'user_id'
    ];
}
