<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;

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


Route::post('/tokens', [AuthController::class, 'loginUser']);
Route::delete('/token/{id}',[AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/token/{id}',[AuthController::class, 'show']);
Route::put('/token/{id}',[AuthController::class, 'update']);


Route::resource('users', UserController::class)->middleware('auth:sanctum');
Route::resource('rooms', RoomController::class);
Route::post('/room/{id}/users', [ParticipantsController::class, 'store']);
Route::get('/room/{id}/users', [ParticipantsController::class, 'index']);
Route::post('/room/{room_id}/{user_id}/messages', [MessagesController::class, 'store']);
Route::get('/room/{room_id}/{user_id}/messages', [MessagesController::class, 'index']);
Route::get('/room/{room_id}/messages', [MessagesController::class, 'show']);
