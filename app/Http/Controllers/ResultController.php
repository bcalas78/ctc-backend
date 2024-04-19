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

class ResultController extends Controller
{
    public function getResultById($resultId) {
        try{      
            $result = Result::findOrFail($resultId);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Une erreur s\'est produite lors de la récupération du score du match.'], 500);
        }
    }

    public function createResult(Request $request) {
        try{      
            $userId = Auth::id();

            $validatedData = $request->validate([
                'team_id' => 'required|integer',
                'team_score' => 'required|integer',
                'note' => 'nullable|string',
                'game_id' => 'required|integer',
            ]);

            $gameId = $validatedData['game_id'];

            $game = Game::findOrFail($gameId);

            if (!$game) {
                return response()->json(['error' => "Le match n'a pas été trouvé."], 404);
            }

            $result = Result::create([
                'game_id' => $game->id,
                'team_id' => $validatedData['team_id'],
                'team_score' => $validatedData['team_score'],
                'note' => $validatedData['note'],
                'user_id' => $userId
            ]);
            
            return response()->json(['message' => 'Résultat ajouté avec succès.', 'result' => $result], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la création du résultat', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateResult(Request $request, $id) {
        try{            
            $userId = Auth::id();

            $validatedData = $request->validate([
                'team_id' => 'required|integer',
                'team_score' => 'required|integer',
                'note' => 'nullable|string',
                'game_id' => 'required|integer',
            ]);

            $result = Result::findOrFail($id);

            if (!$result) {
                return response()->json(['error' => "Le résultat n'a pas été trouvé."], 404);
            }

            $result->update($validatedData);
            
            return response()->json(['message' => 'Résultat mis à jour avec succès.', 'result' => $result], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la mise à jour du résultat.', 'error' => $e->getMessage()], 500);
        }
    }

    public function deleteResultById($id) {
        try {
            $result = Result::findOrFail($id);

            $result->delete();

            return response()->json(['message' => 'Le résultat a été supprimé avec succès !'], 204); 
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Résultat non trouvé', 'error'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la suppression du résultat'], 500);
        }
    }
}