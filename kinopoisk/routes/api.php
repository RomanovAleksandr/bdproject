<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth:sanctum')->post('/upload-image', [App\Http\Controllers\UserController::class, 'setImage']);
Route::middleware('auth:sanctum')->get('/delete-image', [App\Http\Controllers\UserController::class, 'deleteImage']);
Route::post('/register', [App\Http\Controllers\RegisterController::class, 'register']);
Route::post('/login', [App\Http\Controllers\LoginController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\LoginController::class, 'logout']);
Route::resource('films', App\Http\Controllers\FilmsController::class)->except([
    'index'
]);
Route::get('/films', [App\Http\Controllers\FilmsController::class, 'index']);
Route::resource('actors', App\Http\Controllers\ActorsController::class);
Route::resource('directors', App\Http\Controllers\DirectorController::class);
Route::get('/comments', [App\Http\Controllers\CommentController::class, 'index']);
Route::middleware('auth:sanctum')->resource('comments', App\Http\Controllers\CommentController::class)->except([
    'index'
]);
Route::middleware('auth:sanctum')->get('/scores/{id?}', [App\Http\Controllers\FilmScoreController::class, 'index']);
Route::middleware('auth:sanctum')->resource('scores', App\Http\Controllers\FilmScoreController::class)->except([
    'index'
]);
