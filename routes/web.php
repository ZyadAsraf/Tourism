<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AttractionController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ItineraryController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\ArticlesController;
use App\Http\Controllers\TicketController;

Route::get('/', [HomeController::class, 'index'])->name('home'); 
// Public Articles routes
Route::resource('articles', ArticlesController::class)->only(['index', 'show']);

// Public itineraries
Route::get('/itineraries', [ItineraryController::class, 'index'])->name('itineraries.index');
Route::get('/itineraries/{uuid}', [ItineraryController::class, 'show'])->name('itineraries.show');

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
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{slug}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/add-itinerary', [CartController::class, 'addItinerary'])->name('cart.add-itinerary');
    Route::post('/cart/update/{slug}', [CartController::class, 'update'])->name('cart.update');
    Route::get('/cart/remove/{slug}', [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::get('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    Route::post('/cart/process-checkout', [CartController::class, 'processCheckout'])->name('cart.process-checkout');
    Route::post('/cart/confirmation', [CartController::class, 'store'])->name('cart.store');
    Route::get('/cart/confirmation', [CartController::class, 'confirmation'])->name('cart.confirmation');

    // Stripe 
    Route::post('/cart/confirmation', [CartController::class, 'store'])->name('cart.store');
    
    // Ticket Routes
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{id}', [TicketController::class, 'show'])->name('tickets.show');
    Route::get('/tickets/{id}/qr', [TicketController::class, 'showQR'])->name('tickets.qr');
    
    // Itinerary Designer Routes
    Route::get('/itinerary/designer', [ItineraryController::class, 'createNewItinerary'])->name('itinerary.newItinerary');
    Route::get('/itinerary/designer/{uuid}', [ItineraryController::class, 'designer'])->name('itinerary.designer');
    Route::post('/itinerary/add-attraction', [ItineraryController::class, 'addAttraction'])->name('itinerary.add-attraction');
    Route::post('/itinerary/update-attraction/{uuid}', [ItineraryController::class, 'updateAttraction'])->name('itinerary.update-attraction');
    Route::delete('/itinerary/remove-attraction/{uuid}', [ItineraryController::class, 'removeAttraction'])->name('itinerary.remove-attraction');
    Route::post('/itinerary/update', [ItineraryController::class, 'updateItinerary'])->name('itinerary.update');
    Route::get('/itinerary/copy/{uuid}', [ItineraryController::class, 'copyItinerary'])->name('itinerary.copy');
    
    // Public Itineraries
    Route::get('/itineraries', [ItineraryController::class, 'index'])->name('itineraries.index');
    Route::get('/my-itineraries', [ItineraryController::class, 'userItineraries'])->name('itineraries.my-itineraries');
    Route::get('/itineraries/{uuid}', [ItineraryController::class, 'show'])->name('itineraries.show');
});

// Laravel Auth Routes 
require __DIR__.'/auth.php';