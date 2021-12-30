<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bom extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'remarks',
    ];

    // relationships
    public function bomGroups()
    {
        return $this->hasMany(BomGroup::class);
    }
}
