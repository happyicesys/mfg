<?php

namespace App\Models;

use App\Traits\HasSearch;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VmmfgTask extends Model
{
    use HasFactory, HasSearch;

    const STATUS_NEW = 0;
    const STATUS_DONE = 1;
    const STATUS_CHECKED = 2;
    const STATUS_UNDONE = 99;
    const STATUS_CANCELLED = 98;


    protected $fillable = [
        'vmmfg_item_id',
        'vmmfg_unit_id',
        'is_done',
        'is_checked',
        'done_by',
        'checked_by',
        'done_time',
        'checked_time',
        'status',
        'undo_done_by',
        'undo_done_time',
        'cancelled_by',
        'cancelled_time',
        'remarks',
    ];

    // protected $casts = [
    //     'done_time' => 'datetime:Y-m-d h:ia',
    //     'checked_time' => 'datetime:Y-m-d h:ia',
    //     'undo_done_time' => 'datetime:Y-m-d h:ia',
    // ];

    //relationships
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'modelable');
    }

    public function vmmfgUnit()
    {
        return $this->belongsTo(VmmfgUnit::class)->orderBy('unit_no', 'asc');
    }

    public function vmmfgItem()
    {
        return $this->belongsTo(VmmfgItem::class)->orderBy('sequence', 'asc');
    }

    public function doneBy()
    {
        return $this->belongsTo(User::class, 'done_by');
    }

    public function checkedBy()
    {
        return $this->belongsTo(User::class, 'checked_by');
    }

    public function undoDoneBy()
    {
        return $this->belongsTo(User::class, 'undo_done_by');
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    // getter
    // public function getDoneTimeAttribute($value)
    // {
    //     return Carbon::parse($value)->format('Y-m-d h:ia');
    // }

    // public function getCheckedTimeAttribute($value)
    // {
    //     return Carbon::parse($value)->format('Y-m-d h:ia');
    // }

    // public function getUndoDoneTimeAttribute($value)
    // {
    //     return Carbon::parse($value)->format('Y-m-d h:ia');
    // }

}
