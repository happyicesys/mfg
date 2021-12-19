<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'theme_url',
        'theme_background_url',
        'theme_sidebar_background_color',
        'theme_sidebar_font_color',
        'vmmfg_job_batch_no_title',
        'vmmfg_unit_vend_id_title',
    ];

    // relationships
    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
