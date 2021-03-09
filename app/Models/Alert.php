<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    const ERROR_LEVEL_OK = '0';
    const ERROR_LEVEL_WARNING = '1';
    const ERROR_LEVEL_ERROR = '2';

    use HasFactory;

    public function field(){
        return $this->belongsTo(Field::class);
    }

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'field_id', 'exceed_timestamp', 'send', 'confirm', 'error_level'
    ];
}
