<?php

namespace App\Models;

use App\Traits\HasSearch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VmmfgItem extends Model
{
    use HasFactory, HasSearch;

    protected $fillable = [
        'sequence',
        'name',
        'vmmfg_title_id',
        'is_required_upload',
        'is_required',
    ];

    //relationships
    public function vmmfgTasks()
    {
        return $this->hasMany(VmmfgTask::class);
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'modelable');
    }

    // getter
    public function getSequenceAttribute($value)
    {
        return $value + 0;
    }
}
