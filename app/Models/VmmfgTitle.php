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
    ];

    //relationships
    public function vmmfgItems()
    {
        return $this->hasMany(VmmfgItem::class)->orderBy('sequence', 'asc')->orderBy('created_at', 'desc');
    }

    // getter
    public function getSequenceAttribute($value)
    {
        return $value + 0;
    }
}
