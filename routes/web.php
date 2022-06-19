<?php

use Illuminate\Support\Facades\Route;

// AJAX routes.
Route::group([
    'namespace' => '\App\Http\Controllers',
], static function () {
    Route::get('api/v1/settings', 'Settings');
    Route::get('api/v1/address/{address}', 'GetAddress');
    Route::post('api/v1/address', 'UpdateAddress');
    Route::post('api/v1/login', 'Login');
    Route::get('api/v1/logout', 'Logout');
    Route::get('api/v1/getpromo', 'GetPromo');
    Route::get('{any}', 'App')->where('any', '.*');
});
