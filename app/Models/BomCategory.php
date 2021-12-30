<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BomCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    // relationships
    public function bomSubCategories()
    {
        return $this->hasMany(BomSubCategory::class);
    }
}
