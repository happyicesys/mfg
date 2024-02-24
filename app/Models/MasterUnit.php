<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch',
        'code',
        'created_by',
        'container',
        'name',
        'remarks',
        'updated_by',
    ];

    public function vmmfgUnits()
    {
        return $this->hasMany(VmmfgUnit::class);
    }
}
