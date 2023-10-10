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
        'completion_date',
        'remarks',
        'vend_id',
    ];

    // getter
    public function getOrderDateAttribute($value)
    {
        if($value) {
            return Carbon::parse($value)->format('Y-m-d');
        }
    }

    public function getCompletionDateAttribute($value)
    {
        if($value) {
            return Carbon::parse($value)->format('Y-m-d');
        }
    }

    //relationships
    public function vmmfgUnits()
    {
        return $this->hasMany(VmmfgUnit::class);
    }
}
