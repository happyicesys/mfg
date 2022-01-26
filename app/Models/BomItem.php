<?php

namespace App\Models;

use App\Traits\HasSearch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BomItem extends Model
{
    use HasFactory, HasSearch;

    protected $fillable = [
        'code',
        'name',
        'remarks',
        'bom_item_type_id',
        'is_inventory',
        'available_qty',
        'is_header',
        'is_sub_header',
        'is_part',
        'order_by',
        'supplier_id',
        'base_avg_price',
        'ordered_qty',
        'planned_qty',
    ];

    // relationships
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'modelable');
    }

    public function baseCurrency()
    {
        return $this->belongsTo(Country::class, 'base_currency');
    }

    public function bomContents()
    {
        return $this->hasMany(BomContent::class);
    }

    public function bomHeaders()
    {
        return $this->hasMany(BomHeader::class);
    }

    public function bomItemType()
    {
        return $this->belongsTo(BomItemType::class);
    }

    public function inventoryMovementItems()
    {
        return $this->hasMany(InventoryMovementItem::class);
    }

    public function orderBy()
    {
        return $this->belongsTo(User::class, 'order_by');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function supplierQuotePrices()
    {
        return $this->hasMany(SupplierQuotePrice::class);
    }

    // setter
    public function setBaseAvgPriceAttribute($value)
    {
        $this->attributes['base_avg_price'] = $value * 100;
    }

    // getter
    public function getBaseAvgPriceAttribute($value)
    {
        return round($value/ 100, 2);
    }

    public function getCodeAttribute($value)
    {
        if($value) {
            return $value;
        }
    }

    public function getAvailableQtyAttribute($value)
    {
        return $value + 0;
    }

    public function getOrderedQtyAttribute($value)
    {
        return $value + 0;
    }

    public function getPlannedQtyAttribute($value)
    {
        return $value + 0;
    }

    // public function getUnitPriceAttribute($value)
    // {
    //     return round($value/ 100, 2);
    // }

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
