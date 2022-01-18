<?php

namespace App\Models;

use App\Traits\HasSearch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierQuotePrice extends Model
{
    use HasFactory, HasSearch;

    protected $fillable = [
        'bom_item_id',
        'country_id',
        'currency_rate_id',
        'supplier_id',
        'remarks',
        'unit_price',
        'base_price',
    ];

    // relationships
    public function bomItem()
    {
        return $this->belongsTo(BomItem::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function currencyRate()
    {
        return $this->belongsTo(CurrencyRate::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // getter
    public function getBasePriceAttribute($value)
    {
        return round($value/ 100, 2);
    }

    public function getUnitPriceAttribute($value)
    {
        return round($value/ 100, 2);
    }

    // setter
    public function setBasePriceAttribute($value)
    {
        $this->attributes['base_price'] = $value * 100;
    }

    public function setUnitPriceAttribute($value)
    {
        $this->attributes['unit_price'] = $value * 100;
    }

}
