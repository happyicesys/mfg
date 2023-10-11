<?php

use App\Http\Controllers\UnitTransferController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('login', 'ClientController@login');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('unit-transfer')->group(function() {
    Route::post('/', [UnitTransferController::class, 'store']);
    Route::post('/delete/{vmmfgUnitId}', [UnitTransferController::class, 'delete']);
});

