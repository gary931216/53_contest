<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\CommentController;

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

Route::post('/auth/login', [UserController::class, 'login']);
Route::post('/auth/register', [UserController::class, 'register']);
Route::post('/auth/logout', [UserController::class, 'logout']);

Route::get('/images/search', [ImageController::class, 'searchImages']);
Route::get('/images/popular', [ImageController::class, 'getPopular']);
Route::get('/users/{id}/images', [ImageController::class, 'getUserImages']);

Route::post('/images/upload', [ImageController::class, 'upload']);
Route::put('/images/{id}', [ImageController::class, 'update']);
Route::get('/images/{id}', [ImageController::class, 'get']);
Route::delete('/images/{id}', [ImageController::class, 'softdelete']);

Route::get('/images/{id}/comments', [CommentController::class, 'get']);
Route::post('/images/{id}/comments', [CommentController::class, 'post']);
Route::delete('/comments/{id}', [CommentController::class, 'delete']);

Route::get('/time', [CommentController::class, 'getTime']); 