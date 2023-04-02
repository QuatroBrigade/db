<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/auth', [\App\Http\Controllers\AuthController::class, 'authenticate']);

Route::get('/community/post', [\App\Http\Controllers\CommunityController::class, 'getCommunityPost']);
Route::get('/community', [\App\Http\Controllers\CommunityController::class, 'getCommunities']);
Route::get('/community/{id}', [\App\Http\Controllers\CommunityController::class, 'getCommunityById']);
Route::get('/post/{postId}', [\App\Http\Controllers\PostController::class, 'getPostById']);
Route::get('/post/{postId}/votes', [\App\Http\Controllers\PostController::class, 'getWithVotes']);
Route::post('/post/promote/{postId}', [\App\Http\Controllers\PostController::class, 'postPromote']);
Route::post('/post/new', [\App\Http\Controllers\PostController::class, 'newPost']);
Route::post('/user/new', [\App\Http\Controllers\UserController::class, 'newUser']);
Route::get('/user/{userId}', [\App\Http\Controllers\UserController::class, 'getUserById']);
Route::post('/post/{postId}/vote/{userId}', [\App\Http\Controllers\PostController::class, 'userPostVote']);
Route::post('/poi', [\App\Http\Controllers\POIController::class, 'poi']);
Route::get('/poi/filterlist', [\App\Http\Controllers\POIController::class, 'poiFilterlist']);

