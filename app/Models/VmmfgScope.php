<?php

namespace App\Models;

use App\Models\VmmfgItem;
use App\Traits\HasSearch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VmmfgScope extends Model
{
    use HasFactory, HasSearch;

    protected $fillable = [
        'name',
        'remarks',
    ];

    //relationships
    public function vmmfgTitles()
    {
        return $this->hasMany(VmmfgTitle::class)->orderBy('sequence', 'asc')->orderBy('created_at', 'desc');
    }
}
