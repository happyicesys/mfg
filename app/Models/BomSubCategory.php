<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BomSubCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    // relationships
    public function bomCategory()
    {
        return $this->belongsTo(BomCategory::class);
    }
}
