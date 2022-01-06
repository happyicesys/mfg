<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BomHeader extends Model
{
    use HasFactory;

    protected $fillable = [
        'sequence',
        'qty',
        'bom_item_id',
        'bom_id',
        'bom_category_id',
        'vmmfg_item_id',
    ];

    // relationships
    public function bom()
    {
        return $this->belongsTo(Bom::class);
    }

    public function bomCategory()
    {
        return $this->belongsTo(BomCategory::class);
    }

    public function bomContents()
    {
        return $this->hasMany(BomContent::class)->orderBy('sequence');
    }

    public function bomItem()
    {
        return $this->belongsTo(BomItem::class);
    }

    public function vmmfgItem()
    {
        return $this->belongsTo(VmmfgItem::class);
    }
}
