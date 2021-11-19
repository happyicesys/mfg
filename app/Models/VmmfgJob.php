<?php

namespace App\Models;

use App\Traits\HasSearch;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VmmfgJob extends Model
{
    use HasFactory, HasSearch;

    protected $fillable = [
        'batch_no',
        'model',
        'racking_name',
        'order_date',
        'due_date',
    ];

    // getter
    public function getOrderDateAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d');
    }

    //relationships
    public function vmmfgUnits()
    {
        return $this->hasMany(VmmfgUnit::class);
    }
}
