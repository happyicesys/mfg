<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryMovementItemQuantity extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_movement_item_id',
        'date',
        'qty',
        'remarks',
        'is_incomplete_qty',
        'created_by',
    ];

    // relationships
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'modelable');
    }

    public function inventoryMovementItem()
    {
        return $this->belongsTo(InventoryMovementItem::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // getter
    public function getDateAttribute($value)
    {
        if($value) {
            return Carbon::parse($value)->toDateString();
        }
    }

    public function getQtyAttribute($value)
    {
        if(is_numeric($value)) {
            return $value + 0;
        }
    }

    // setter
    public function setIsIncompleteQtyAttribute($value)
    {
        if($value) {
            $this->attributes['is_incomplete_qty'] = $value;
        }else {
            $this->attributes['is_incomplete_qty'] = false;
        }
    }

}
