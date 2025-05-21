<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AttractionController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\CartController;

// Route::middleware('auth:sanctum')->post('/tokens/create', function (Request $request) {
//     $token = $request->user()->createToken($request->token_name);
//     return ['token' => $token->plainTextToken];
// });


Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);


Route::group(['middleware'=>'api'], function () {
    Route::get('/get-attractions', [AttractionController::class, 'getAttractions']);

    // Article routes
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::get('/articles/{id}', [ArticleController::class, 'show']);

    Route::get('/categories', [AttractionController::class, 'getCategories']);
    Route::get('/home', [AttractionController::class, 'homeApi']);
    Route::get('/attractions', [AttractionController::class, 'indexApi']);
    Route::get('/attractions/{slug}', [AttractionController::class, 'showApi']);
    Route::get('/category/{category}', [AttractionController::class, 'byCategoryApi']);
    Route::get('/search', [AttractionController::class, 'searchApi']);
    Route::get('/booking/{attraction}', [AttractionController::class, 'bookingFormApi']);
    Route::post('/payment/{attraction}', [AttractionController::class, 'paymentFormApi']);
    Route::post('/process-booking/{attraction}', [AttractionController::class, 'processBookingApi']);
    
    Route::get('/attractions/{slug}/reviews', [AttractionController::class, 'reviews']);
    Route::get('/home', [HomeController::class, 'indexApi']);

    
});

// Routes that require authentication
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/attractions/{slug}/reviews', [AttractionController::class, 'addReview']);
    // Ticket routes
    Route::get('/tickets', [TicketController::class, 'index']);
    Route::get('/tickets/{id}', [TicketController::class, 'show']);
    Route::get('/ticket-types', [TicketController::class, 'getTicketTypes']);
    
    // Cart routes
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'add']);
    Route::put('/cart/{id}', [CartController::class, 'update']);
    Route::delete('/cart/{id}', [CartController::class, 'remove']);
    Route::delete('/cart', [CartController::class, 'clear']);
    Route::post('/cart/checkout', [CartController::class, 'checkout']);
    Route::get('/cart/count', [CartController::class, 'getCount']);
    // Protected article routes
    Route::post('/articles', [ArticleController::class, 'store']);
    Route::put('/articles/{id}', [ArticleController::class, 'update']);
    Route::delete('/articles/{id}', [ArticleController::class, 'destroy']);

    // If you want only authenticated users to see tokens/create:
    Route::post('/tokens/create', function (Request $request) {
        $token = $request->user()->createToken($request->token_name);
        return ['token' => $token->plainTextToken];
    });
});
