<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChampionshipController;
use App\Http\Controllers\SportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

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

Route::middleware('auth:sanctum')->post('/tokens/create', function (Request $request) {
    $token = $request->user()->createToken($request->token_name);
    return ['authToken' => $token->plainTextToken];
});

Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login')->middleware('guest');
    Route::get('/login', 'login')->middleware('guest')->name('login');
    Route::post('/forgot-password', 'sendPasswordResetMail')->middleware('guest');
    Route::post('/reset-password', 'resetPassword')->middleware('guest');
    Route::post('/logout', 'logout')->middleware('auth:sanctum');
});

Route::controller(UserController::class)->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user-role', 'getUserRole');
        Route::get('/user-infos', 'getUserInfos');
        Route::patch('/user-infos-update', 'updateUserInfos');
    });
});

Route::controller(ChampionshipController::class)->group(function () {
    Route::get('/championships', 'getAllChampionships');
    Route::get('/championships/championship/{id}', 'getChampionshipById');
    Route::get('/user-championships', 'getChampionshipsByUser')->middleware('auth:sanctum');
    Route::post('/championship', 'createChampionship')->middleware('auth:sanctum', 'role:dirigeant', 'permission:dirigeant_create_championship');
    Route::patch('/championship/{id}', 'updateChampionship')->middleware('auth:sanctum', 'role:dirigeant', 'permission:dirigeant_update_championship');
    Route::delete('/championship/{id}', 'deleteChampionshipById')->middleware('auth:sanctum', 'role:dirigeant', 'permission:dirigeant_delete_championship');
});

Route::get('/sports', [SportController::class, 'getAllSports']);


