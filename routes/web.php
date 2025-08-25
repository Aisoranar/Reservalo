<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\FavoriteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Rutas públicas
Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/propiedades', [PropertyController::class, 'index'])->name('properties.index');
Route::get('/propiedades/{property}', [PropertyController::class, 'show'])->name('properties.show');

// Rutas para ubicaciones (Colombia)
Route::get('/api/departments', [LocationController::class, 'getDepartments'])->name('api.departments');
Route::get('/api/cities', [LocationController::class, 'getAllCities'])->name('api.cities');
Route::get('/api/cities/search', [LocationController::class, 'searchCities'])->name('api.cities.search');
Route::get('/api/cities/by-department', [LocationController::class, 'getCitiesByDepartment'])->name('api.cities.by-department');

// API para vista rápida (pública)
Route::get('/api/properties/{property}/quick-view', [PropertyController::class, 'quickView'])->name('api.properties.quick-view');



// Rutas de autenticación (se generarán automáticamente con Breeze)
Route::middleware(['auth', 'active.user'])->group(function () {
    // Dashboard del usuario
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // Perfil del usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/deactivate', [ProfileController::class, 'deactivate'])->name('profile.deactivate');
    Route::post('/profile/reactivate', [ProfileController::class, 'reactivate'])->name('profile.reactivate');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Reservas del usuario
    Route::get('/reservas', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservas/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
    Route::post('/reservas/{property}', [ReservationController::class, 'store'])->name('reservations.store');
    Route::delete('/reservas/{reservation}', [ReservationController::class, 'cancel'])->name('reservations.cancel');

    // Favoritos del usuario
    Route::get('/favoritos', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favoritos/{property}/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::get('/favoritos/{property}/check', [FavoriteController::class, 'check'])->name('favorites.check');

    // Panel de administración
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/properties', [AdminController::class, 'properties'])->name('properties.index');
        Route::get('/reservations', [AdminController::class, 'reservations'])->name('reservations');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
        Route::get('/export', [AdminController::class, 'exportReservations'])->name('export');
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');

        // Gestión de propiedades
        Route::get('/properties/create', [PropertyController::class, 'create'])->name('properties.create');
        Route::post('/properties', [PropertyController::class, 'store'])->name('properties.store');
        Route::get('/properties/{property}/edit', [PropertyController::class, 'edit'])->name('properties.edit');
        Route::put('/properties/{property}', [PropertyController::class, 'update'])->name('properties.update');
        Route::delete('/properties/{property}', [PropertyController::class, 'destroy'])->name('properties.destroy');
        Route::patch('/properties/{property}/toggle', [PropertyController::class, 'toggleStatus'])->name('properties.toggle');

        // Gestión de reservas
        Route::get('/reservations', [ReservationController::class, 'adminIndex'])->name('reservations.index');
        Route::patch('/reservations/{reservation}/approve', [ReservationController::class, 'approve'])->name('reservations.approve');
        Route::patch('/reservations/{reservation}/reject', [ReservationController::class, 'reject'])->name('reservations.reject');
        Route::post('/properties/{property}/block-dates', [ReservationController::class, 'blockDates'])->name('properties.block-dates');

        // Gestión de usuarios
        Route::get('/users', [AdminController::class, 'users'])->name('users.index');

        // Gestión de usuarios desactivados
        Route::get('/deactivated-users', [AdminController::class, 'deactivatedUsers'])->name('deactivated-users.index');
        Route::get('/deactivated-users/{deactivatedUser}', [AdminController::class, 'showDeactivatedUser'])->name('deactivated-users.show');
        Route::post('/deactivated-users/{deactivatedUser}/reactivate', [AdminController::class, 'reactivateUser'])->name('deactivated-users.reactivate');
        Route::post('/users/{user}/suspend', [AdminController::class, 'suspendUser'])->name('users.suspend');
        Route::get('/users/{email}/history', [AdminController::class, 'userHistory'])->name('users.history');
        Route::get('/deactivated-users/export', [AdminController::class, 'exportDeactivatedUsers'])->name('deactivated-users.export');
        Route::post('/deactivated-users/cleanup', [AdminController::class, 'cleanupDeactivatedUsers'])->name('deactivated-users.cleanup');
    });
});

// Rutas de autenticación (se generarán automáticamente con Breeze)
require __DIR__.'/auth.php';
