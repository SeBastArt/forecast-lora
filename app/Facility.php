<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    public function company(){
        return $this->belongsTo(Company::class);
    }

    public function nodes()
    {
        return $this->hasMany(Node::class);
    }

    public function getErrorLevel(){
        $errorLevel = 1;
        foreach ($this->nodes as $key => $node) {
            $errorLevel = max($node->getErrorLevel(), $errorLevel);
        }
        return $errorLevel;
    }

    public function getWorstRSSI(){
        $worstRSSI = 0;
        foreach ($this->nodes as $key => $node) {
            $worstRSSI = min($node->getRSSI(), $worstRSSI);
        }
        return $worstRSSI;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'location', 'company_id'
    ];
}
