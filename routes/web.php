<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttractionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CartController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Tourist routes - public
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/attractions', [AttractionController::class, 'index'])->name('attractions.index');
Route::get('/attractions/{slug}', [AttractionController::class, 'show'])->name('attractions.show');
Route::get('/categories/{category}', [AttractionController::class, 'byCategory'])->name('attractions.category');
Route::get('/search', [AttractionController::class, 'search'])->name('attractions.search');


Route::get('/trip-plan', [CartController::class, 'index'])->name('cart.index');
Route::post('/trip-plan/add/{slug}', [CartController::class, 'add'])->name('cart.add');
Route::post('/trip-plan/update/{slug}', [CartController::class, 'update'])->name('cart.update');
Route::get('/trip-plan/remove/{slug}', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/trip-plan/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::get('/trip-plan/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
Route::post('/trip-plan/process-checkout', [CartController::class, 'processCheckout'])->name('cart.process-checkout');
Route::get('/trip-plan/confirmation', [CartController::class, 'confirmation'])->name('cart.confirmation');
// Booking routes
Route::get('/booking/{attraction}', [AttractionController::class, 'bookingForm'])->name('booking.form');
Route::post('/booking/{attraction}/payment', [AttractionController::class, 'paymentForm'])->name('booking.payment');
Route::post('/booking/{attraction}/process', [AttractionController::class, 'processBooking'])->name('booking.process');

// Dashboard route
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');

// Auth routes
require __DIR__.'/auth.php';

