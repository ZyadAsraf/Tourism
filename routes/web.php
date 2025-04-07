<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttractionController;
use App\Http\Controllers\HomeController;

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

// Booking routes
Route::get('/booking/{attraction}', [AttractionController::class, 'bookingForm'])->name('booking.form');
Route::post('/booking/{attraction}/payment', [AttractionController::class, 'paymentForm'])->name('booking.payment');
Route::post('/booking/{attraction}/process', [AttractionController::class, 'processBooking'])->name('booking.process');

// Auth routes
require __DIR__.'/auth.php';

