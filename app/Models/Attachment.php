<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable =[
        'url',
        'full_url',
        'is_primary',
        'sequence',
        'modelable_id',
        'modelable_type',
        'filename',
    ];

    // relationships
    public function modelable()
    {
        return $this->morphTo();
    }
}
