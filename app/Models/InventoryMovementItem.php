<?php

namespace App\Models;

use App\Traits\HasSearch;
use Carbon\Carbon;
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

    const RECEIVING_STATUSES = [
        1 => 'New',
        2 => 'Ordered',
        4 => 'Received',
        99 => 'Void',
    ];

    const OUTGOING_STATUSES = [
        1 => 'Planned',
        2 => 'Delivered',
        99 => 'Void',
    ];

    protected $fillable = [
        'bom_item_id',
        'date',
        'inventory_movement_id',
        'supplier_quote_price_id',
        'remarks',
        'status',
        'qty',
        'amount',
        'unit_price',
        'created_by',
        'updated_by',
        'previous_status',
        'is_incomplete_qty',
    ];

    // getter
    public function getAmountAttribute($value)
    {
        return round($value/ 100, 2);
    }

    public function getDateAttribute($value)
    {
        return Carbon::parse($value)->format('ymd');
    }

    public function getQtyAttribute($value)
    {
        if(is_numeric($value)) {
            return $value + 0;
        }
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

    public function setIsIncompleteQtyAttribute($value)
    {
        if($value) {
            $this->attributes['is_incomplete_qty'] = $value;
        }else {
            $this->attributes['is_incomplete_qty'] = false;
        }
    }

    // relationships
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'modelable');
    }

    public function bomItem()
    {
        return $this->belongsTo(BomItem::class);
    }

    public function inventoryMovement()
    {
        return $this->belongsTo(InventoryMovement::class);
    }

    public function inventoryMovementItemQuantities()
    {
        return $this->hasMany(InventoryMovementItemQuantity::class);
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
