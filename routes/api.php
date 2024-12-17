<?php

use App\Http\Controllers\BienController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

//Route::post('/login', [UserController::class, 'login']);
//Route::middleware('auth:sanctum')->post('/logout', [UserController::class, 'logout']);
//Route::post('/register', [UserController::class, 'register']);

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
;


//Biens
Route::apiResource('biens', BienController::class);
Route::put('/biens/{id}', [BienController::class, 'update']);
Route::post('/biens', [BienController::class, 'store']);
Route::delete('/biens/{id}', [BienController::class, 'destroy']);

// Rooutes for users
Route::get('users', [\App\Http\Controllers\UserController::class, 'index']);
Route::get('users/{id}', [\App\Http\Controllers\UserController::class, 'show']);
Route::put('users/{id}', [\App\Http\Controllers\UserController::class, 'update']);
Route::delete('users/{id}', [\App\Http\Controllers\UserController::class, 'delete']);
Route::post('utilisateur/{id}/bloquer', [UserController::class, 'bloquer']);
Route::post('utilisateur/{id}/debloquer', [UserController::class, 'debloquer']);

//Reservation
Route::post('reservations', [ReservationController::class, 'store']);

//contact
Route::post('/biens/{bien}/appeler', [BienController::class, 'appeler'])->name('biens.appeler');
Route::post('/biens/{bien}/contacter', [BienController::class, 'contacter'])->name('biens.contacter');
