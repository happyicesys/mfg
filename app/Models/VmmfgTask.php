<?php

namespace App\Models;

use App\Traits\HasSearch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VmmfgTask extends Model
{
    use HasFactory, HasSearch;

    protected $fillable = [
        'vmmfg_item_id',
        'vmmfg_unit_id',
        'is_done',
        'is_checked',
        'done_by',
        'checked_by',
        'done_time',
        'checked_time',
    ];

    //relationships
    public function vmmfgUnit()
    {
        return $this->belongsTo(VmmfgUnit::class);
    }

    public function vmmfgItem()
    {
        return $this->belongsTo(VmmfgItem::class);
    }

    public function doneBy()
    {
        return $this->belongsTo(User::class, 'done_by');
    }

    public function checkedBy()
    {
        return $this->belongsTo(User::class, 'checked_by');
    }


}
