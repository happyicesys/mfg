<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BomGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'remarks',
        'bom_id',
        'bom_sub_category_id',
    ];

    // relationships
    public function bomItems()
    {
        return $this->belongsToMany(BomItem::class);
    }

    public function bomSubCategory()
    {
        return $this->belongsTo(BomSubCategory::class);
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'modelable');
    }

}
