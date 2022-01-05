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
    public function bomGroups()
    {
        return $this->hasMany(BomGroup::class);
    }

    public function bomItems()
    {
        return $this->hasMany(BomItem::class);
    }

    public function bomSubCategories()
    {
        return $this->hasMany(BomSubCategory::class);
    }
}
