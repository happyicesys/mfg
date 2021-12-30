<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BomSubCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'bom_category_id',
    ];

    // relationships
    public function bomCategory()
    {
        return $this->belongsTo(BomCategory::class);
    }
}
