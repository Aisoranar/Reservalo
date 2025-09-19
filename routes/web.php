<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\MembershipController;
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

// Middleware global para verificar sistema activo
Route::middleware(['system.active'])->group(function () {
    // Rutas públicas
    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/propiedades', [PropertyController::class, 'index'])->name('properties.index');
Route::get('/propiedades/{property}', [PropertyController::class, 'show'])->name('properties.show');

// Rutas para ubicaciones (Colombia)
Route::get('/api/departments', [LocationController::class, 'getDepartments'])->name('api.departments');
Route::get('/api/cities', [LocationController::class, 'getAllCities'])->name('api.cities');
Route::get('/api/cities/search', [LocationController::class, 'searchCities'])->name('api.cities.search');
Route::get('/api/cities/by-department', [LocationController::class, 'getCitiesByDepartment'])->name('api.cities.by-department');

// API para vista rápida (pública)
Route::get('/api/properties/{property}/quick-view', [PropertyController::class, 'quickView'])->name('api.properties.quick-view');

// API para fechas ocupadas (pública)
Route::get('/api/properties/{property}/occupied-dates', [PropertyController::class, 'getOccupiedDates'])->name('api.properties.occupied-dates');



// Rutas de autenticación (se generarán automáticamente con Breeze)
Route::middleware(['auth', 'active.user', 'must.change.password'])->group(function () {
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

    // Las membresías son gestionadas solo por administradores
    // Los usuarios regulares no tienen acceso a gestión de membresías

    // Panel de superadmin (accesible por superadmin y admin)
    Route::middleware(['roles:superadmin,admin', 'audit.logging'])->prefix('superadmin')->name('superadmin.')->group(function () {
        Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [SuperAdminController::class, 'users'])->name('users');
        Route::get('/users/create', [SuperAdminController::class, 'createUser'])->name('users.create');
        Route::post('/users', [SuperAdminController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{user}', [SuperAdminController::class, 'showUser'])->name('users.show');
        Route::get('/users/{user}/edit', [SuperAdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{user}', [SuperAdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [SuperAdminController::class, 'destroyUser'])->name('users.destroy');
        Route::post('/users/{user}/toggle-status', [SuperAdminController::class, 'toggleUserStatus'])->name('users.toggle-status');
        Route::post('/users/{user}/assign-role', [SuperAdminController::class, 'assignRole'])->name('users.assign-role');
        Route::delete('/users/{user}/remove-role/{role}', [SuperAdminController::class, 'removeRole'])->name('users.remove-role');
        Route::get('/roles', [SuperAdminController::class, 'roles'])->name('roles');
        Route::get('/roles/create', [SuperAdminController::class, 'createRole'])->name('roles.create');
        Route::post('/roles', [SuperAdminController::class, 'storeRole'])->name('roles.store');
        Route::get('/roles/{role}', [SuperAdminController::class, 'showRole'])->name('roles.show');
        Route::get('/roles/{role}/edit', [SuperAdminController::class, 'editRole'])->name('roles.edit');
        Route::put('/roles/{role}', [SuperAdminController::class, 'updateRole'])->name('roles.update');
        Route::delete('/roles/{role}', [SuperAdminController::class, 'destroyRole'])->name('roles.destroy');
        Route::get('/permissions', [SuperAdminController::class, 'permissions'])->name('permissions');
        Route::get('/permissions/create', [SuperAdminController::class, 'createPermission'])->name('permissions.create');
        Route::post('/permissions', [SuperAdminController::class, 'storePermission'])->name('permissions.store');
        Route::get('/permissions/{permission}', [SuperAdminController::class, 'showPermission'])->name('permissions.show');
        Route::get('/permissions/{permission}/edit', [SuperAdminController::class, 'editPermission'])->name('permissions.edit');
        Route::put('/permissions/{permission}', [SuperAdminController::class, 'updatePermission'])->name('permissions.update');
        Route::delete('/permissions/{permission}', [SuperAdminController::class, 'destroyPermission'])->name('permissions.destroy');
        Route::post('/permissions/{permission}/toggle-status', [SuperAdminController::class, 'togglePermissionStatus'])->name('permissions.toggle-status');
        Route::get('/membership-plans', [SuperAdminController::class, 'membershipPlans'])->name('membership-plans');
        Route::get('/membership-plans/create', [SuperAdminController::class, 'createMembershipPlan'])->name('membership-plans.create');
        Route::post('/membership-plans', [SuperAdminController::class, 'storeMembershipPlan'])->name('membership-plans.store');
        Route::get('/membership-plans/{plan}', [SuperAdminController::class, 'showMembershipPlan'])->name('membership-plans.show');
        Route::get('/membership-plans/{plan}/edit', [SuperAdminController::class, 'editMembershipPlan'])->name('membership-plans.edit');
        Route::put('/membership-plans/{plan}', [SuperAdminController::class, 'updateMembershipPlan'])->name('membership-plans.update');
        Route::delete('/membership-plans/{plan}', [SuperAdminController::class, 'destroyMembershipPlan'])->name('membership-plans.destroy');
        Route::post('/membership-plans/{plan}/toggle-status', [SuperAdminController::class, 'toggleMembershipPlanStatus'])->name('membership-plans.toggle-status');
        Route::post('/membership-plans/{plan}/set-default', [SuperAdminController::class, 'setDefaultMembershipPlan'])->name('membership-plans.set-default');
        // Gestión de membresías
        Route::get('/memberships', [SuperAdminController::class, 'memberships'])->name('memberships');
        Route::get('/memberships/create', [SuperAdminController::class, 'createMembership'])->name('memberships.create');
        Route::post('/memberships', [SuperAdminController::class, 'storeMembership'])->name('memberships.store');
        Route::get('/memberships/{membership}', [SuperAdminController::class, 'showMembership'])->name('memberships.show');
        Route::get('/memberships/{membership}/edit', [SuperAdminController::class, 'editMembership'])->name('memberships.edit');
        Route::put('/memberships/{membership}', [SuperAdminController::class, 'updateMembership'])->name('memberships.update');
        Route::delete('/memberships/{membership}', [SuperAdminController::class, 'destroyMembership'])->name('memberships.destroy');
        Route::post('/memberships/{membership}/toggle-status', [SuperAdminController::class, 'toggleMembershipStatus'])->name('memberships.toggle-status');
        
        // Gestión de reservas
        Route::get('/reservations', [SuperAdminController::class, 'reservations'])->name('reservations');
        Route::get('/reservations/pending', [SuperAdminController::class, 'pendingReservations'])->name('reservations.pending');
        Route::get('/reservations/create', [SuperAdminController::class, 'createManualReservation'])->name('reservations.create');
        Route::post('/reservations/store-manual', [SuperAdminController::class, 'storeManualReservation'])->name('reservations.store-manual');
        Route::get('/reservations/occupied-dates', [SuperAdminController::class, 'getOccupiedDates'])->name('reservations.occupied-dates');
        Route::get('/reservations/{reservation}', [SuperAdminController::class, 'showReservation'])->name('reservations.show');
        Route::post('/reservations/{reservation}/approve', [SuperAdminController::class, 'approveReservation'])->name('reservations.approve');
        Route::post('/reservations/{reservation}/reject', [SuperAdminController::class, 'rejectReservation'])->name('reservations.reject');
        Route::post('/reservations/{reservation}/delete', [SuperAdminController::class, 'deleteReservation'])->name('reservations.delete');
        Route::post('/reservations/{reservation}/send-email', [SuperAdminController::class, 'sendEmail'])->name('reservations.send-email');
        
        // Notificaciones
        Route::get('/notifications', [SuperAdminController::class, 'notifications'])->name('notifications');
        Route::post('/notifications/{notification}/mark-read', [SuperAdminController::class, 'markNotificationAsRead'])->name('notifications.mark-read');
        Route::post('/notifications/mark-all-read', [SuperAdminController::class, 'markAllNotificationsAsRead'])->name('notifications.mark-all-read');
        
        // Plantillas de correo
        Route::get('/email-templates', [App\Http\Controllers\EmailTemplateController::class, 'index'])->name('email-templates');
        Route::get('/email-templates/create', [App\Http\Controllers\EmailTemplateController::class, 'create'])->name('email-templates.create');
        Route::post('/email-templates', [App\Http\Controllers\EmailTemplateController::class, 'store'])->name('email-templates.store');
        Route::get('/email-templates/{emailTemplate}', [App\Http\Controllers\EmailTemplateController::class, 'show'])->name('email-templates.show');
        Route::get('/email-templates/{emailTemplate}/edit', [App\Http\Controllers\EmailTemplateController::class, 'edit'])->name('email-templates.edit');
        Route::put('/email-templates/{emailTemplate}', [App\Http\Controllers\EmailTemplateController::class, 'update'])->name('email-templates.update');
        Route::delete('/email-templates/{emailTemplate}', [App\Http\Controllers\EmailTemplateController::class, 'destroy'])->name('email-templates.destroy');
        Route::post('/email-templates/{emailTemplate}/toggle-status', [App\Http\Controllers\EmailTemplateController::class, 'toggleStatus'])->name('email-templates.toggle-status');
        Route::post('/email-templates/{emailTemplate}/preview', [App\Http\Controllers\EmailTemplateController::class, 'preview'])->name('email-templates.preview');
        Route::post('/email-templates/{emailTemplate}/send-test', [App\Http\Controllers\EmailTemplateController::class, 'sendTest'])->name('email-templates.send-test');
        Route::post('/email-templates/create-defaults', [App\Http\Controllers\EmailTemplateController::class, 'createDefaults'])->name('email-templates.create-defaults');
        Route::get('/settings', [SuperAdminController::class, 'settings'])->name('settings');
        Route::post('/settings', [SuperAdminController::class, 'updateSettings'])->name('settings.update');
        Route::post('/settings/reset', [SuperAdminController::class, 'resetSettings'])->name('settings.reset');
        Route::post('/toggle-system', [SuperAdminController::class, 'toggleSystemStatus'])->name('toggle-system');
        Route::post('/toggle-maintenance', [SuperAdminController::class, 'toggleMaintenanceMode'])->name('toggle-maintenance');
        Route::get('/reports', [SuperAdminController::class, 'reports'])->name('reports');
        Route::get('/reports/export', [SuperAdminController::class, 'exportReports'])->name('reports.export');
        Route::get('/audit-logs', [SuperAdminController::class, 'auditLogs'])->name('audit-logs');
        Route::get('/audit-logs/{log}', [SuperAdminController::class, 'showAuditLog'])->name('audit-logs.show');
        Route::get('/audit-logs/export', [SuperAdminController::class, 'exportAuditLogs'])->name('audit-logs.export');
        Route::post('/audit-logs/cleanup', [SuperAdminController::class, 'cleanupAuditLogs'])->name('audit-logs.cleanup');
        
        // Precios globales
        Route::get('/pricing', [App\Http\Controllers\GlobalPricingController::class, 'index'])->name('pricing');
        Route::get('/pricing/create', [App\Http\Controllers\GlobalPricingController::class, 'create'])->name('pricing.create');
        Route::post('/pricing', [App\Http\Controllers\GlobalPricingController::class, 'store'])->name('pricing.store');
        Route::get('/pricing/{pricing}/edit', [App\Http\Controllers\GlobalPricingController::class, 'edit'])->name('pricing.edit');
        Route::put('/pricing/{pricing}', [App\Http\Controllers\GlobalPricingController::class, 'update'])->name('pricing.update');
        Route::post('/pricing/{pricing}/activate', [App\Http\Controllers\GlobalPricingController::class, 'activate'])->name('pricing.activate');
        Route::post('/pricing/{pricing}/deactivate', [App\Http\Controllers\GlobalPricingController::class, 'deactivate'])->name('pricing.deactivate');
        Route::delete('/pricing/{pricing}', [App\Http\Controllers\GlobalPricingController::class, 'destroy'])->name('pricing.destroy');
        Route::get('/api/pricing/active', [App\Http\Controllers\GlobalPricingController::class, 'getActivePricing'])->name('api.pricing.active');
    });

    // Panel de administración
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/properties', [AdminController::class, 'properties'])->name('properties.index');
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
});

// Rutas de autenticación (se generarán automáticamente con Breeze)
require __DIR__.'/auth.php';
