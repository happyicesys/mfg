<?php

namespace App\Models;

use App\Traits\HasSearch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryMovementItem extends Model
{
    use HasFactory, HasSearch;

    const STATUSES = [
        1 => 'Pending',
        2 => 'Confirmed',
        99 => 'Void',
    ];

    protected $fillable = [
        'bom_item_id',
        'inventory_movement_id',
        'supplier_quote_price_id',
        'remarks',
        'status',
        'qty',
        'amount',
        'unit_price',
        'created_by',
        'updated_by',
    ];

    // getter
    public function getAmountAttribute($value)
    {
        return round($value/ 100, 2);
    }

    public function getUnitPriceAttribute($value)
    {
        return round($value/ 100, 2);
    }

    // setter
    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = $value * 100;
    }

    public function setUnitPriceAttribute($value)
    {
        $this->attributes['unit_price'] = $value * 100;
    }

    public function setSupplierQuotePriceIdAttribute($value)
    {
        if($value) {
            $this->attributes['supplier_quote_price_id'] = $value;
        }
    }

    // relationships
    public function bomItem()
    {
        return $this->belongsTo(BomItem::class);
    }

    public function inventoryMovement()
    {
        return $this->belongsTo(InventoryMovement::class);
    }

    public function supplierQuotePrice()
    {
        return $this->belongsTo(SupplierQuotePrice::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
