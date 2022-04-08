<?php

namespace App\Models;

use App\Traits\HasSearch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VmmfgItem extends Model
{
    use HasFactory, HasSearch;

    const FLAGS = [
        1 => 'New',
        2 => 'Updated',
    ];

    protected $fillable = [
        'sequence',
        'name',
        'remarks',
        'vmmfg_title_id',
        'is_required_upload',
        'is_required',
        'status',
        'flag_id',
    ];

    //relationships
    public function vmmfgTasks()
    {
        return $this->hasMany(VmmfgTask::class);
    }

    public function vmmfgTitle()
    {
        return $this->belongsTo(VmmfgTitle::class)->orderBy('sequence', 'asc');
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'modelable');
    }

    // getter
    // public function getSequenceAttribute($value)
    // {
    //     return $value + 0;
    // }

}
