<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WashingReservation extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Attributes
    |--------------------------------------------------------------------------
    */

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'cluster_id', 'starts_on', 'ends_on'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
