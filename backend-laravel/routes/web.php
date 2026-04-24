<?php

use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Host\HostingController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SiteController::class, 'home'])->name('site.home');
Route::get('/stays/{slug}', [SiteController::class, 'property'])->name('site.property');
Route::get('/reservation/{slug}', [SiteController::class, 'reservation'])->name('site.reservation');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'createLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'storeLogin'])
        ->middleware('throttle:auth-login')
        ->name('login.store');

    if (config('security.allow_public_registration')) {
        Route::get('/register', [AuthController::class, 'createRegister'])->name('register');
        Route::post('/register', [AuthController::class, 'storeRegister'])
            ->middleware('throttle:auth-register')
            ->name('register.store');
    }

    Route::get('/forgot-password', [PasswordResetController::class, 'createForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'storeForgotPassword'])
        ->middleware('throttle:password-reset-links')
        ->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'createResetPassword'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'storeResetPassword'])->name('password.update');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/backoffice/reservations', [SiteController::class, 'reservations'])->name('site.reservations');
    Route::get('/backoffice/users', [UserManagementController::class, 'index'])->name('admin.users.index');
    Route::get('/backoffice/users/{user}/edit', [UserManagementController::class, 'edit'])->name('admin.users.edit');
    Route::put('/backoffice/users/{user}', [UserManagementController::class, 'update'])->name('admin.users.update');
    Route::delete('/backoffice/users/{user}', [UserManagementController::class, 'destroy'])->name('admin.users.destroy');
    Route::get('/hosting', [HostingController::class, 'index'])->name('hosting.index');
    Route::get('/hosting/listings/create', [HostingController::class, 'create'])->name('hosting.create');
    Route::post('/hosting/listings', [HostingController::class, 'store'])->name('hosting.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
});
