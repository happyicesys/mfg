<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('login', 'ClientController@login');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('unit-transfer', 'UnitTransferController@store');
