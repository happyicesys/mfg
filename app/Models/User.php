<?php

namespace App\Models;

use App\Traits\HasSearch;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
// use Laravel\Scout\Searchable;

class User extends Authenticatable
{
    use HasFactory, HasRoles, HasSearch, Notifiable;

    const STATUSES = [
        1 => 'Active',
        2 => 'Inactive'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime:Y-m-d',
    ];

    // algolia
    public function toSearchableArray()
    {
        $array = $this->toArray();

        return $array;
    }

    // setter
    public function setPasswordAttribute($value)
    {
        if($value) {
            $this->attributes['password'] = bcrypt($value);
        }
    }

    // getter
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d h:ia');
    }

    // relationships
    public function profiles()
    {
        return $this->hasMany(Profile::class);
    }

}
