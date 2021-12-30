<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BomItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'remarks',
        'bom_group_id',
        'bom_sub_category_id',
        'bom_item_type_id',
    ];

    // relationships
    public function bomGroups()
    {
        return $this->belongsToMany(BomGroup::class);
    }

    public function bomSubCategory()
    {
        return $this->belongsTo(BomSubCategory::class);
    }

    public function bomItemType()
    {
        return $this->belongsTo(BomItemType::class);
    }
}
