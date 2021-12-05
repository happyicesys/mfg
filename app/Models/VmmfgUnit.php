<?php

namespace App\Models;

use App\Traits\HasSearch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VmmfgUnit extends Model
{
    use HasFactory, HasSearch;

    protected $fillable = [
        'unit_no',
        'vmmfg_job_id',
        'serial_no',
        'vmmfg_scope_id',
    ];

    //relationships
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
}
