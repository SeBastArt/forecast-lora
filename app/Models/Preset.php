<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Preset extends Model
{
    use HasFactory;

    /**
     * Get all of the fields for the preset.
     */
    public function fields()
    {
        return $this->morphToMany(Field::class, 'fieldable');
    }

    /**
     * Get owner for the preset.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'user_id'
    ];
}
