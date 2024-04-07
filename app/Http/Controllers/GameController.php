<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Game;
use App\Models\Team;
use App\Models\Result;
use App\Models\Championship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{
    public function getGamesTeamsResultsByChampionship($championshipId)
    {
        try {
            $games = Game::where('games.championship_id', $championshipId)
                ->join('team_game', 'games.id', '=', 'team_game.game_id')
                ->join('teams', 'team_game.team_id', '=', 'teams.id')
                ->leftJoin('results', function ($join) {
                    $join->on('results.game_id', '=', 'team_game.game_id')
                         ->on('results.team_id', '=', 'team_game.team_id');
                })
                ->select(
                    'games.id as gameId',
                    'games.game_date',
                    'games.game_time',
                    'games.number_teams',
                    'teams.name as team_name',
                    'results.team_score',
                    'results.note'
                )
                ->get();

                return response()->json($games);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Une erreur est survenue lors de la récupération des matchs du championnat.','error' => $e->getMessage()], 500);
        } 

        // try{
        //     $championships = Championship::findOrFail($championshipId);

        //     $games = Game::where('championship_id', $championshipId)->get();

        //     return response()->json($games);

        // } catch (\Exception $e) {
        //     return response()->json(['error' => 'Une erreur est survenue lors de la récupération des matchs du championnat.'], 500);
        // } 
    }

    public function getGameById($gameId)
    {
        try {
            $game = Game::findOrFail($gameId);

            return response()->json($game);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Une erreur s\'est produite lors de la récupération du match.'], 500);
        }
    }

    public function createGame(Request $request)
    {
        try {
            $userId = Auth::id();

            $validatedData = $request->validate([
                'number_teams' => 'required|integer',
                'game_date' => 'required|date',
                'game_time' => 'required|date_format:H:i',
                'place' => 'required|string',
                'address' => 'nullable|string',
                'postal_code' => 'nullable|string',
                'city' => 'required|string',
                'championship_id' => 'required|integer',
            ]);

            $game = Game::create([
                'number_teams' => $validatedData['number_teams'],
                'game_date' => $validatedData['game_date'],
                'game_time' => $validatedData['game_time'],
                'place' => $validatedData['place'],
                'address' => $validatedData['address'] ?? null,
                'postal_code' => $validatedData['postal_code'] ?? null,
                'city' => $validatedData['city'],
                'user_id' => $userId,
                'championship_id' => $validatedData['championship_id'],
            ]);

            return response()->json(['message' => 'Match créé avec succès', 'game' => $game], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la création du match', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateGame(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'number_teams' => 'required|integer',
                'game_date' => 'required|date',
                'game_time' => 'required|date_format:H:i',
                'place' => 'required|string',
                'address' => 'nullable|string',
                'postal_code' => 'nullable|string',
                'city' => 'required|string',
            ]);

            $game = Game::findOrFail($id);

            $game->update($validatedData);

            return response()->json([
                'message' => 'Le match a été mis à jour avec succès !', $game
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Match non trouvé', 'error'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la mise à jour du match', 'error' => $e->getMessage()], 500);
        }
    }

    public function deleteGameById($id)
    {
        try {
            $game = Game::findOrFail($id);

            $game->delete();

            return response()->json(['message' => 'Le match a été supprimé avec succès !'], 204); 
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Match non trouvé', 'error'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la suppression du match', 'error' => $e->getMessage()], 500);
        }
    }   
}