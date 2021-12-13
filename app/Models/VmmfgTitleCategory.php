<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VmmfgTitleCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'desc',
    ];

    public function vmmfgTitles()
    {
        return $this->hasMany(VmmfgTitle::class);
    }
}
