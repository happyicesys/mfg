<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BomItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'remarks',
        'bom_item_type_id',
        'is_inventory',
        'is_consumable',
        'available_qty',
        'is_header',
        'is_sub_header',
        'is_part',
    ];

    // relationships
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'modelable');
    }

    public function bomCategory()
    {
        return $this->belongsTo(BomCategory::class);
    }

    public function bomContents()
    {
        return $this->hasMany(bomContent::class);
    }

    public function bomHeaders()
    {
        return $this->hasMany(BomHeader::class);
    }

    public function bomSubCategory()
    {
        return $this->belongsTo(BomSubCategory::class);
    }

    public function bomItemType()
    {
        return $this->belongsTo(BomItemType::class);
    }

    // getter
    public function getCodeAttribute($value)
    {
        if($value) {
            return $value;
        }
    }

    // setter
    // public function setIsInventoryAttribute($value)
    // {
    //     if($value) {
    //         $this->attributes['is_inventory'] = $value;
    //     }
    // }

    // public function setIsConsumableAttribute($value)
    // {
    //     if($value) {
    //         $this->attributes['is_consumable'] = $value;
    //     }
    // }
}
