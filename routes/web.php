<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AttractionController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Auth\VerifyEmailController;

Route::get('/', [HomeController::class, 'index'])->name('home'); 

Route::middleware(['auth', 'verified'])->group(function () {
    

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Attractions
    Route::get('/attractions', [AttractionController::class, 'index'])->name('attractions.index');
    Route::get('/attractions/{slug}', [AttractionController::class, 'show'])->name('attractions.show');
    Route::get('/attractions/{slug}/reviews', [AttractionController::class, 'reviews'])->name('attractions.reviews');
    Route::post('/attractions/{slug}/reviews', [AttractionController::class, 'addReview'])->name('attractions.reviews.store');
    Route::get('/categories/{category}', [AttractionController::class, 'byCategory'])->name('attractions.category');
    Route::get('/search', [AttractionController::class, 'search'])->name('attractions.search');

    // Cart & Trip Plan
    Route::get('/trip-plan', [CartController::class, 'index'])->name('cart.index');
    Route::post('/trip-plan/add/{slug}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/trip-plan/update/{slug}', [CartController::class, 'update'])->name('cart.update');
    Route::get('/trip-plan/remove/{slug}', [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/trip-plan/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::get('/trip-plan/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    Route::post('/trip-plan/process-checkout', [CartController::class, 'processCheckout'])->name('cart.process-checkout');
    Route::post('/trip-plan/confirmation', [CartController::class, 'store'])->name('cart.store');
    Route::get('/trip-plan/confirmation', [CartController::class, 'confirmation'])->name('cart.confirmation');

    // Stripe 
    Route::post('/trip-plan/confirmation', [CartController::class, 'store'])->name('cart.store');
});

// Laravel Auth Routes 
require __DIR__.'/auth.php';
