<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AttractionController;
use App\Http\Controllers\Api\HomeController;

Route::middleware('auth:sanctum')->post('/tokens/create', function (Request $request) {
    $token = $request->user()->createToken($request->token_name);
    return ['token' => $token->plainTextToken];
});


Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);


Route::group(['middleware'=>'api'], function () {
    Route::get('/get-attractions', [AttractionController::class, 'getAttractions']);


    Route::get('/categories', [AttractionController::class, 'getCategories']);
    Route::get('/home', [AttractionController::class, 'homeApi']);
    Route::get('/attractions', [AttractionController::class, 'indexApi']);
    Route::get('/attractions/{slug}', [AttractionController::class, 'showApi']);
    Route::get('/category/{category}', [AttractionController::class, 'byCategoryApi']);
    Route::get('/search', [AttractionController::class, 'searchApi']);
    Route::get('/booking/{attraction}', [AttractionController::class, 'bookingFormApi']);
    Route::post('/payment/{attraction}', [AttractionController::class, 'paymentFormApi']);
    Route::post('/process-booking/{attraction}', [AttractionController::class, 'processBookingApi']);
    Route::get('/attractions/{slug}/reviews', [AttractionController::class, 'getReviews']);

    // Add a new review to an attraction
    Route::post('/attractions/{slug}/reviews', [AttractionController::class, 'addReview']);
    Route::get('/home', [HomeController::class, 'indexApi']);

});
