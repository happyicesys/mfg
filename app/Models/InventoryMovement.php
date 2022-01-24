<?php

namespace App\Models;

use App\Traits\HasSearch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    use HasFactory, HasSearch;

    const ACTIONS = [
        1 => 'Receiving',
        2 => 'Outgoing',
    ];

    const STATUSES = [
        1 => 'Pending',
        2 => 'Confirmed',
        3 => 'Partially',
        4 => 'Completed',
        99 => 'Cancelled',
    ];

    protected $fillable = [
        'batch',
        'remarks',
        'action',
        'status',
        'total_amount',
        'total_qty',
        'bom_id',
        'created_by',
        'updated_by',
        'country_id',
    ];

    // getter
    public function getTotalQtyAttribute($value)
    {
        return $value + 0;
    }

    public function getTotalAmountAttribute($value)
    {
        return round($value/ 100, 2);
    }

    // setter
    public function setTotalAmountAttribute($value)
    {
        $this->attributes['total_amount'] = $value * 100;
    }

    // relationships
    public function bom()
    {
        return $this->belongsTo(Bom::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function inventoryMovementItems()
    {
        return $this->hasMany(InventoryMovementItem::class);
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
