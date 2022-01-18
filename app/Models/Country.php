<?php

namespace App\Models;

use App\Traits\HasSearch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory, HasSearch;

    protected $fillable = [
        'name',
        'currency_name',
        'currency_symbol',
        'phone_code',
        'is_city',
        'is_state',
    ];

    // relationships
    public function currencyRates()
    {
        return $this->hasMany(CurrencyRate::class);
    }
}
