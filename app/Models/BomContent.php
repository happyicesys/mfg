<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BomContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'sequence',
        'qty',
        'bom_item_id',
        'bom_header_id',
        'bom_sub_category_id',
        'is_group',
        'vmmfg_item_id',
    ];

    // relationships
    public function bomItem()
    {
        return $this->belongsTo(BomItem::class);
    }

    public function bomHeader()
    {
        return $this->belongsTo(BomHeader::class);
    }

    public function bomSubCategory()
    {
        return $this->belongsTo(BomSubCategory::class);
    }

    public function vmmfgItem()
    {
        return $this->belongsTo(VmmfgItem::class);
    }
}
