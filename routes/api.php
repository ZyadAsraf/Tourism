<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Route::get('/test',function(){
//     return response([
//         'message' => 'Hello, World!'
//     ], 200);
// });


use App\Http\Controllers\Api\AttractionController;

Route::group(['middleware'=>'api'], function () {
    Route::get('/get-attractions', [AttractionController::class, 'getAttractions']);
    // More routes can go here later
});
