<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BienController;

use App\Http\Controllers\ContactController;

use App\Http\Controllers\ReservationController;
use App\Http\Controllers\SuperAdminController;
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



Route::middleware('auth:sanctum')->get('/user/profile', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('admin/dashboard', [AdminController::class, 'dashboard']);
    Route::apiResource('users', UserController::class);
});

Route::middleware(['auth:sanctum', 'role:super_admin'])->group(function () {
    Route::get('super-admin/dashboard', [SuperAdminController::class, 'dashboard']);
});

Route::middleware(['auth:sanctum', 'role:user'])->group(function () {
    Route::post('reservations', [ReservationController::class, 'store']);
});


Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('logout', [\App\Http\Controllers\AuthController::class, 'logout'])->middleware('auth:sanctum');


//Biens
Route::apiResource('biens', BienController::class);
//Route::put('/biens/{id}', [BienController::class, 'update']);
//Route::post('/biens', [BienController::class, 'store']);
//Route::delete('/biens/{id}', [BienController::class, 'destroy']);
Route::apiResource('update',\App\Http\Controllers\UpdateController::class);

Route::get('location', [\App\Http\Controllers\BienController::class, 'location']);
Route::get('vente', [\App\Http\Controllers\BienController::class, 'vente']);

// Routes for users
Route::get('users', [\App\Http\Controllers\UserController::class, 'index']);
Route::get('users/{id}', [\App\Http\Controllers\UserController::class, 'show']);
Route::put('users/{id}', [\App\Http\Controllers\UserController::class, 'update']);
Route::delete('users/{id}', [\App\Http\Controllers\UserController::class, 'delete']);
Route::post('utilisateur/{id}/bloquer', [UserController::class, 'bloquer']);
Route::post('utilisateur/{id}/debloquer', [UserController::class, 'debloquer']);

//Reservation
Route::post('reservations', [ReservationController::class, 'store']);

//contacter
Route::post('/biens/{bien}/appeler', [BienController::class, 'appeler'])->name('biens.appeler');
Route::post('/biens/{bien}/contacter', [BienController::class, 'contacter'])->name('biens.contacter');


Route::post('/contact', [ContactController::class, 'sendMessage']);

