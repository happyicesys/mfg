<?php

namespace App\Models;

use App\Traits\HasSearch;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VmmfgUnit extends Model
{
    use HasFactory, HasSearch;

    protected $fillable = [
        'current',
        'destination',
        'master_unit_id',
        'origin',
        'origin_ref_id',
        'origin_vmmfg_job_json',
        'origin_vmmfg_scope_json',
        'progress_json',
        'status_datetime',
        'unit_no',
        'vmmfg_job_id',
        'vmmfg_job_json',
        'sequence',
        'serial_no',
        'vmmfg_scope_id',
        'vmmfg_scope_json',
        'vend_id',
        'completion_date',
        'model',
        'order_date',
        'refer_completion_unit_id',
        'code'
    ];

    protected $casts = [
        'origin_vmmfg_job_json' => 'array',
        'origin_vmmfg_scope_json' => 'array',
        'progress_json' => 'array',
        'vmmfg_job_json' => 'array',
        'vmmfg_scope_json' => 'array',
    ];

    protected $with = [
        'vmmfgJob',
    ];

    //relationships
    public function masterUnit()
    {
        return $this->belongsTo(MasterUnit::class);
    }

    public function vmmfgJob()
    {
        return $this->belongsTo(VmmfgJob::class)->orderBy('batch_no', 'desc');
    }

    public function vmmfgScope()
    {
        return $this->belongsTo(VmmfgScope::class)->orderBy('created_at', 'desc');
    }

    public function vmmfgTasks()
    {
        return $this->hasMany(VmmfgTask::class);
    }

    public function referCompletionUnit()
    {
        return $this->belongsTo(VmmfgUnit::class, 'refer_completion_unit_id');
    }

    public function bindedCompletionUnit()
    {
        return $this->hasOne(VmmfgUnit::class, 'refer_completion_unit_id');
    }

    // getter
    public function getCompletionDateAttribute($value)
    {
        if($value) {
            return Carbon::parse($value)->format('Y-m-d');
        }
    }

    public function getOrderDateAttribute($value)
    {
        if($value) {
            return Carbon::parse($value)->format('Y-m-d');
        }
    }

    // setter
    public function setReferCompletionUnitIdAttribute($value)
    {
        $this->attributes['refer_completion_unit_id'] = $value ? $value : null;
    }

    public function setCompletionDateAttribute($value)
    {
        $this->attributes['completion_date'] = $value ? $value : null;
    }
}
