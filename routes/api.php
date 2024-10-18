<?php

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MainController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::middleware('auth:api')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

    Route::post('register',[UserController::class,'registration']);
    Route::post('login',[UserController::class,'login']);
    Route::post('add-review',[UserController::class,'reviewMovie'])->middleware('auth:api');
    Route::post('search-title',[UserController::class,'movieSearchByTitle']);
    Route::post('search-genre',[UserController::class,'movieSearchBygenre']);

    Route::patch('approve-user',[AdminController::class,'approveUser'])->middleware('auth:api');
    Route::post('add-movie',[AdminController::class,'addmovie'])->middleware('auth:api');
    Route::post('update-movie',[AdminController::class,'updateMovie'])->middleware('auth:api');
    Route::delete('delete-movie/{id}',[AdminController::class,'deleteMovie'])->middleware('auth:api');
    
    Route::get('average-rating/{id}',[MainController::class,'ratingAggregation']);
    Route::get('fetch-movies',[MainController::class,'fetchMovies']);