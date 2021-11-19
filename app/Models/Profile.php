<?php

namespace App\Models;

use App\Traits\HasSearch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory, HasSearch;

    protected $fillable = [
        'name',
        'symbol',
        'reg_no',
        'address_id',
        'country_id',
    ];

    // relationships
    public function addresses()
    {
        return $this->morphMany(Address::class, 'modelable');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function primaryAddress()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
