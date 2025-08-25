<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\DashboardController;
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
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
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
        Route::get('/reservations', [App\Http\Controllers\Admin\ReservationController::class, 'index'])->name('reservations.index');
        Route::get('/reservations/{reservation}', [App\Http\Controllers\Admin\ReservationController::class, 'show'])->name('reservations.show');
        Route::post('/reservations/{reservation}/approve', [App\Http\Controllers\Admin\ReservationController::class, 'approve'])->name('reservations.approve');
        Route::post('/reservations/{reservation}/reject', [App\Http\Controllers\Admin\ReservationController::class, 'reject'])->name('reservations.reject');
        Route::post('/reservations/{reservation}/cancel', [App\Http\Controllers\Admin\ReservationController::class, 'cancel'])->name('reservations.cancel');
        Route::post('/reservations/{reservation}/payment', [App\Http\Controllers\Admin\ReservationController::class, 'updatePayment'])->name('reservations.payment');
        Route::post('/reservations/bulk-action', [App\Http\Controllers\Admin\ReservationController::class, 'bulkAction'])->name('reservations.bulk-action');
        Route::get('/properties/{property}/availability', [App\Http\Controllers\Admin\ReservationController::class, 'getAvailability'])->name('properties.availability');

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
        
        // Gestión de precios y descuentos
        Route::prefix('pricing')->name('pricing.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\PricingController::class, 'index'])->name('index');
            
            // Precios por noche
            Route::get('/nightly-prices/create', [App\Http\Controllers\Admin\PricingController::class, 'createNightlyPrice'])->name('nightly-prices.create');
            Route::post('/nightly-prices', [App\Http\Controllers\Admin\PricingController::class, 'storeNightlyPrice'])->name('nightly-prices.store');
            Route::get('/nightly-prices/{nightlyPrice}/edit', [App\Http\Controllers\Admin\PricingController::class, 'editNightlyPrice'])->name('nightly-prices.edit');
            Route::put('/nightly-prices/{nightlyPrice}', [App\Http\Controllers\Admin\PricingController::class, 'updateNightlyPrice'])->name('nightly-prices.update');
            Route::delete('/nightly-prices/{nightlyPrice}', [App\Http\Controllers\Admin\PricingController::class, 'destroyNightlyPrice'])->name('nightly-prices.destroy');
            Route::post('/nightly-prices/{nightlyPrice}/toggle-status', [App\Http\Controllers\Admin\PricingController::class, 'toggleNightlyPriceStatus'])->name('nightly-prices.toggle-status');
            
            // Descuentos
            Route::get('/discounts/create', [App\Http\Controllers\Admin\PricingController::class, 'createDiscount'])->name('discounts.create');
            Route::post('/discounts', [App\Http\Controllers\Admin\PricingController::class, 'storeDiscount'])->name('discounts.store');
            Route::get('/discounts/{discount}/edit', [App\Http\Controllers\Admin\PricingController::class, 'editDiscount'])->name('discounts.edit');
            Route::put('/discounts/{discount}', [App\Http\Controllers\Admin\PricingController::class, 'updateDiscount'])->name('discounts.update');
            Route::delete('/discounts/{discount}', [App\Http\Controllers\Admin\PricingController::class, 'destroyDiscount'])->name('discounts.destroy');
            Route::post('/discounts/{discount}/toggle-status', [App\Http\Controllers\Admin\PricingController::class, 'toggleDiscountStatus'])->name('discounts.toggle-status');
            
            // API para cálculos y aplicación de descuentos
            Route::post('/calculate-price', [App\Http\Controllers\Admin\PricingController::class, 'calculatePrice'])->name('calculate-price');
            Route::post('/get-available-discounts', [App\Http\Controllers\Admin\PricingController::class, 'getAvailableDiscounts'])->name('get-available-discounts');
            Route::post('/apply-discount', [App\Http\Controllers\Admin\PricingController::class, 'applyDiscountToReservation'])->name('apply-discount');
        });
    });
});

// Rutas de autenticación (se generarán automáticamente con Breeze)
require __DIR__.'/auth.php';
