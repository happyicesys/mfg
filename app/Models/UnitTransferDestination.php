<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitTransferDestination extends Model
{
    use HasFactory;

    const OPTIONS = [
        'SG' => 'https://mfg.happyice.com.sg',
        'JB' => 'https://jbmfg.happyice.com.sg',
        'IDN' => 'https://idn-mfg.happyice.net',
        // 'MFG' => 'http://mfg.test',
        // 'MFG2' => 'http://mfg2.test',
    ];

    const CREATE_DIRECTORY = '/api/unit-transfer';
    const DELETE_DIRECTORY = '/api/unit-transfer/delete';

    protected $fillable = [
        'name',
        'base_url',
        'directory',
        'is_local'
    ];
}
