<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Facility extends Model
{
    use HasFactory;

    public function company(){
        return $this->belongsTo(Company::class);
    }

    public function nodes()
    {
        return $this->hasMany(Node::class);
    }

    public function file()
    {
        return $this->hasOne(File::class);
    }

    /**
     * Get all of the nodes for the facility.
     */
    //public function nodes()
    //{
    //    return $this->morphToMany(Node::class, 'nodeable');
    //}

    public function getErrorLevel(){
        $errorLevel = 0;
        foreach ($this->nodes as $key => $node) {
            $errorLevel = max($node->getErrorLevel(), $errorLevel);
            //echo $node->name. 'with: '. $node->getErrorLevel() . '<br>';
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

    public static function boot()
    {
        parent::boot();
        static::deleting(function($facility){
            if($facility->file != null)
            {
                Storage::delete($facility->file->file_path);
                $facility->file->delete;
            }
            foreach ($facility->nodes as $node) 
            {
                $node->delete();
            }
        });
    }
}
