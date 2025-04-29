<?php

use Illuminate\Support\Facades\Route;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\CRUD.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin'),
        ['role:admin'], // aquí sí aplicas role:admin
    ),
    'namespace'  => 'App\Http\Controllers\Admin',
], function () {
    // CRUD de Salas (solo admins)
    Route::crud('sala', 'SalaCrudController');
    // Exportar reservas (solo admins)
    Route::get('reserva/export', 'ReservaCrudController@exportExcel')->name('reserva.export');
});

// 2) Rutas para TODOS los usuarios autenticados (admins + clientes)
Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        ['auth'], // basta con estar logueado
    ),
    'namespace'  => 'App\Http\Controllers\Admin',
], function () {
    // CRUD de Reservas (index, create, store siempre disponibles; update/delete
    // se restringen dentro del controller con denyAccess/allowAccess según rol)
    Route::crud('reserva', 'ReservaCrudController');
});
/**
 * DO NOT ADD ANYTHING HERE.
 */
