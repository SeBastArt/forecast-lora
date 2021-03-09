<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class Company extends Model
{
    use HasFactory;

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['facilities'];

    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function getErrorLevel()
    {
        $errorLevel = 0; //everything is fine
        foreach ($this->facilities as $key => $facility) {
            $errorLevel = max($facility->getErrorLevel(), $errorLevel);
        }
        return $errorLevel;
    }


    public function users_name()
    {
        $users = $this->getAttribute('users');
        $users_name = collect();

        if ($users->pluck('id')->contains(Auth::user()->id)){
            $users_name->push('You');
        } 

        foreach ($users as $user) {
            if ($user->id != Auth::user()->id) {
                $users_name->push($user->name);
            }
        }
        return $users_name;
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
        'name', 'city', 'country'
    ];

    public static function boot()
    {
        parent::boot();
        static::deleting(function($company){
            foreach ($company->facilities as $facility) {
                $facility->delete();
            }
        });
    }
}
