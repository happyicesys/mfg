<?php

use App\Http\Livewire\Admin;
use App\Http\Livewire\AppSetting;
use App\Http\Livewire\Customer;
use App\Http\Livewire\Home;
use App\Http\Livewire\Product;
use App\Http\Livewire\Profile;
use App\Http\Livewire\SelfSetting;
use App\Http\Livewire\Setting;
use App\Http\Livewire\VmmfgOps;
use App\Http\Livewire\VmmfgSetting\Job as VmmfgSettingJob;
use App\Http\Livewire\VmmfgSetting\Unit as VmmfgSettingUnit;
use App\Http\Livewire\VmmfgSetting\Scope as VmmfgSettingScope;
use Illuminate\Support\Facades\Route;


Auth::routes();
// Auth::loginUsingId(1);
Route::middleware(['auth'])->group(function () {
    Route::get('/', Home::class)->name('home');
    Route::get('/admin', Admin::class)->name('admin');
    Route::get('/customer', Customer::class)->name('customer');
    Route::get('/product', Product::class)->name('product');
    Route::get('/profile', Profile::class)->name('profile');
    Route::get('/app-setting', AppSetting::class)->name('app-setting');
    Route::get('/self-setting', SelfSetting::class)->name('self-setting');
    Route::get('/setting', Setting::class)->name('setting');
    Route::get('/vmmfg-ops', VmmfgOps::class)->name('vmmfg-ops');
    Route::get('/vmmfg-setting-job', VmmfgSettingJob::class)->name('vmmfg-setting-job');
    Route::get('/vmmfg-setting-unit', VmmfgSettingUnit::class)->name('vmmfg-setting-unit');
    Route::get('/vmmfg-setting-scope', VmmfgSettingScope::class)->name('vmmfg-setting-scope');
});


