<?php

namespace App\Models;

use App\Traits\HasSearch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory, HasSearch;

    protected $fillable = [
        'company_name',
        'attn_name',
        'attn_contact',
        'email',
        'url',
        'payment_term_id',
        'country_id',
    ];

    // relationship
    public function bomItems()
    {
        return $this->hasMany(BomItem::class);
    }

    public function transactedCurrency()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function paymentTerm()
    {
        return $this->belongsTo(PaymentTerm::class);
    }

    public function supplierQuotePrices()
    {
        return $this->hasMany(SuppierQuotePrice::class);
    }
}
