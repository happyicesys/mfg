<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'postcode',
        'city',
        'is_city',
        'country_id',
        'state_id',
        'modelable_id',
        'modelable_type',
        'is_primary',
    ];

    // relationships
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function modelable()
    {
        return $this->morphTo();
    }

    // setter
    // public function setIsCityAttribute($value)
    // {
    //     $this->attributes['is_city'] = $this->country->is_city;
    // }



}
