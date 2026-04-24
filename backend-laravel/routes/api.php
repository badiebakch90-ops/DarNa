<?php

use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Api\ReservationController;
use Illuminate\Support\Facades\Route;

Route::get('/home', HomeController::class);
Route::get('/properties', [PropertyController::class, 'index']);
Route::get('/properties/{slug}', [PropertyController::class, 'show']);
Route::get('/properties/{slug}/availability', [PropertyController::class, 'availability']);
Route::middleware(['web', 'auth', 'admin'])->get('/reservations', [ReservationController::class, 'index']);
Route::post('/reservations', [ReservationController::class, 'store'])->middleware('throttle:reservation-store');
