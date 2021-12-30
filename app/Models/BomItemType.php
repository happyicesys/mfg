<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BomItemType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'desc',
    ];

    public function bomItems()
    {
        return $this->hasMany(BomItem::class);
    }
}
