<?php

namespace App\Models;

use App\Traits\HasSearch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BomItemType extends Model
{
    use HasFactory, HasSearch;

    protected $fillable = [
        'name',
        'desc',
    ];

    public function bomItems()
    {
        return $this->hasMany(BomItem::class);
    }
}
