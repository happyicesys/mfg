<?php

namespace App\Models;

use App\Traits\HasSearch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VmmfgTitle extends Model
{
    use HasFactory, HasSearch;

    protected $fillable = [
        'sequence',
        'name',
        'vmmfg_scope_id',
        'vmmfg_title_category_id',
    ];

    //relationships
    public function vmmfgItems()
    {
        return $this->hasMany(VmmfgItem::class)->orderBy('sequence', 'asc')->orderBy('created_at', 'desc');
    }

    public function vmmfgTitleCategory()
    {
        return $this->belongsTo(VmmfgTitleCategory::class)->orderBy('name', 'asc');
    }

    // // getter
    // public function getSequenceAttribute($value)
    // {
    //     return $value + 0;
    // }
}
