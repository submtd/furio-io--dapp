<?php

use Illuminate\Support\Facades\Route;

// AJAX routes.
Route::group([
    'prefix' => 'api/v1',
    'namespace' => '\App\Http\Controllers',
], static function () {
    Route::get('settings', 'Settings');
    Route::post('address', 'UpdateAddress');
    Route::post('login', 'Login');
    Route::get('logout', 'Logout');
    Route::get('presalesignature', 'PresaleSignature');
    Route::get('updatepresale', 'UpdatePresale');
    Route::get('getpromo', 'GetPromo');
});

// SPA route catchall.
Route::get('{any}', function () {
    return view('app');
})->where('any', '.*');
