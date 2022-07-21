<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Route;

// AJAX routes.
Route::group([
    'namespace' => '\App\Http\Controllers',
], static function () {
    Route::get('api/v1/settings', 'Settings');
    Route::get('api/v1/address/{address}', 'GetAddress');
    Route::post('api/v1/address', 'UpdateAddress');
    Route::post('api/v1/name', 'UpdateName');
    Route::post('api/v1/social', 'UpdateSocial');
    Route::post('api/v1/image', 'UpdateImage');
    Route::post('api/v1/login', 'Login');
    Route::get('api/v1/logout', 'Logout');
    Route::post('api/v1/getpromo', 'GetPromo');
    Route::post('api/v1/updatetoken', 'UpdateToken');
    Route::post('api/v1/vault', 'UpdateVault');
    Route::get('404', static function () {
        return response('not found', 404);
    });
    Route::get('{any}', 'App')->where('any', '.*');
});
