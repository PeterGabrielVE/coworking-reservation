<?php

use Illuminate\Support\Facades\Route;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\CRUD.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin'),
        ['role:admin']
    ),
    'namespace' => 'App\Http\Controllers\Admin',
], function () {
    // CRUD para administradores
    Route::crud('sala', 'SalaCrudController');
    Route::crud('reserva', 'ReservaCrudController');
    Route::get('reserva/export', 'ReservaCrudController@exportExcel')->name('reserva.export');
});

/**
 * DO NOT ADD ANYTHING HERE.
 */
