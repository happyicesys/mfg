<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Upgrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'date_from',
        'date_to',
        'vmmfg_scope_id',
        'vmmfg_unit_id',

    ];

    protected $casts = [
        'date_from' => 'datetime',
        'date_to' => 'datetime',
    ];

    public function vmmfgScope()
    {
        return $this->belongsTo(VmmfgScope::class);
    }

    public function vmmfgUnit()
    {
        return $this->belongsTo(VmmfgUnit::class);
    }
}
