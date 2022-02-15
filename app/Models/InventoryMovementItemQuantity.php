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

}
