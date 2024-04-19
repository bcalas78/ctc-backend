<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChampionshipController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\SportController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\TeamController;
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

Route::controller(GameController::class)->group(function () {
    Route::get('/games/{id}', 'getGamesTeamsResultsByChampionship');
    Route::get('/games/game/{id}', 'getGameById');
    Route::get('/games/team_results/{id}', 'getTeamsResultsByGame');
    Route::post('/game', 'createGame')->middleware('auth:sanctum', 'role:dirigeant', 'permission:dirigeant_create_game');
    Route::patch('/game/{id}', 'updateGame')->middleware('auth:sanctum', 'role:dirigeant', 'permission:dirigeant_update_game');
    Route::delete('/game/{id}', 'deleteGameById')->middleware('auth:sanctum', 'role:dirigeant', 'permission:dirigeant_delete_game');
});

Route::controller(ResultController::class)->group(function () {
    Route::middleware('auth:sanctum', 'role:dirigeant')->group(function () {
        Route::get('/result/{id}', 'getResultById');
        Route::post('/result', 'createResult')->middleware('permission:dirigeant_add_result');
        Route::patch('/result/{id}', 'updateResult')->middleware('permission:dirigeant_update_result');
        Route::delete('/result/{id}', 'deleteResultById')->middleware('permission:dirigeant_delete_result');
    });
});

Route::controller(TeamController::class)->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/teams', 'getAllTeams')->middleware('role:joueur', 'permission:joueur_research_team');
        Route::get('/championship-teams/{id}', 'getTeamsByChampionship');
        Route::get('/team-players/{id}', 'getPlayersByTeam');
        Route::get('/user-teams', 'getTeamsByUser');
        Route::get('/status', 'getAllStatusByUser')->middleware('role:joueur');
        Route::get('/status-pending', 'getAllStatusPending')->middleware('role:dirigeant');
        Route::post('/join-team/{id}', 'joinTeam')->middleware('role:joueur', 'permission:joueur_request_to_join_team');
        Route::post('/accept-player/{playerId}/team/{teamId}', 'acceptPlayerRequestInTeam')->middleware('role:dirigeant', 'permission:dirigeant_accept_player_in_team');
        Route::post('/reject-player/{playerId}/team/{teamId}', 'rejectPlayerRequestInTeam')->middleware('role:dirigeant', 'permission:dirigeant_rejected_player_in_team');
        Route::post('/team', 'createTeam')->middleware('role:dirigeant', 'permission:dirigeant_create_team');
        Route::patch('/team/{id}', 'updateTeam')->middleware('role:dirigeant', 'permission:dirigeant_update_team');
        Route::patch('/team/{teamId}/player/{playerId}', 'updatePlayerStatusInTeam')->middleware('role:dirigeant', 'permission:dirigeant_delete_player_in_team');
        Route::delete('/team/{id}', 'deleteTeamById')->middleware('role:dirigeant', 'permission:dirigeant_delete_team');
    });
});

