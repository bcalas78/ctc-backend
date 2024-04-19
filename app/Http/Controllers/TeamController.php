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

class TeamController extends Controller
{
    public function getAllTeams() {
        try{            
            $teams = Team::with('championship')->get();

            return response()->json(['teams' => $teams], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la récupération de toutes les équipes', 'error' => $e->getMessage()], 500);
        }
    }

    public function getTeamsByChampionship($id) {
        try{            
            $teams = Team::whereHas('championship', function ($query) use ($id) {
                $query->where('championship_id', $id);
            })->get();

            return response()->json(['teams' => $teams], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la récupération des équipes du championnat', 'error' => $e->getMessage()], 500);
        }
    }

    public function getPlayersByTeam($teamId) {
        try {
            $team = Team::findOrFail($teamId);

            $teamName = $team->name;

            $players = $team->players()
                            ->wherePivot('status', 'approved')
                            ->select('users.id', 'users.firstname', 'users.lastname')
                            ->get();

            return response()->json(['team_name' => $teamName, 'team_id' => $teamId, 'players' => $players]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Une erreur est survenue lors de la récupération des joueurs de l\'équipe.', 'error' => $e->getMessage()], 500);
        }
    }

    public function getTeamsByUser(Request $request) {
        try {
            $user = Auth::user();
            $userRole = $user->roles()->first();
            $teams = [];
            $userId = $user->id;
    
            if ($userRole) {
                if ($userRole->name === 'dirigeant') {
                    $teams = Team::with('championship')
                            ->where('user_id', $userId)
                            ->get();
                } else if ($userRole->name === 'joueur') {
                    $teams = Team::with('championship')
                        ->whereHas('players', function($query) use ($userId) {
                        $query->where('users.id', $userId)
                              ->where('user_team.status', '=', 'approved');
                    })->get();

                }
            } else {
                $teams = [];
            }
           
            return response()->json($teams);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Une erreur est survenue lors de la récupération des équipes de l\'utilisateur.', 'error' => $e->getMessage()], 500);
        }
    }

    public function getAllStatusByUser()
    {
        try {
            $user = Auth::user();

            $teams = $user->teams()->withPivot('status')->with('championship')->get();

            $statusList = [];

            foreach ($teams as $team) {
                $statusList[] = [
                    'team_id' => $team->id,
                    'team_name' => $team->name,
                    'championship_name' => $team->championship->name,
                    'status' => $team->pivot->status
                ];
            }

            return response()->json($statusList);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Une erreur est survenue lors de la récupération de l\'historique des demandes d\'adhésion à une équipe par le joueur.', 'error' => $e->getMessage()], 500);
        }
    }

    public function getAllStatusPending()
    {
        try {
            $user = Auth::user();

            $teams = $user->teams;

            $pendingRequests = [];

            foreach ($teams as $team) {
                $pendingPlayers = $team->players()->wherePivot('status', 'pending')->get();
                

                foreach ($pendingPlayers as $player) {
                    $championshipName = $team->championship->name;

                    $pendingRequests[] = [
                        'team_id' => $team->id,
                        'team_name' => $team->name,
                        'championship_name' => $championshipName,
                        'player_id' => $player->id,
                        'player_firstname' => $player->firstname,
                        'player_lastname' => $player->lastname,
                        'player_email' => $player->email,
                    ];
                }
            }

            return response()->json($pendingRequests);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Une erreur est survenue lors de la récupération de toutes les demandes en attente pour les équipes créées par le dirigeant.', 'error' => $e->getMessage()], 500);
        }
    }

    public function joinTeam(Request $request, $teamId)
    {
        try {
            $user = Auth::user();
            $team = Team::findOrFail($teamId);

            if ($user->teams->contains($team)) {
                return response()->json(['message' => 'Vous êtes déjà membre de cette équipe.'], 400);
            }

            $user->teams()->attach($team, ['status' => 'pending']);
            return response()->json(['message' => 'Votre demande pour rejoindre l\'équipe a été envoyée.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Une erreur est survenue lors de la tentative de rejoindre l\'équipe.', 'error' => $e->getMessage()], 500);
        }
    }

    public function acceptPlayerRequestInTeam($playerId, $teamId)
    {
        try {
            $team = Team::findOrFail($teamId);
            $player = User::findOrFail($playerId);

            $request = $team->players()->where('user_id', $playerId)->wherePivot('status', 'pending')->first();

            if ($request) {
                $team->players()->updateExistingPivot($playerId, ['status' => 'approved']);

                return response()->json(['message' => 'La demande du joueur a été acceptée avec succès.']);
            } else {
                return response()->json(['message' => 'Aucune demande en attente trouvée pour ce joueur dans cette équipe.'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Une erreur est survenue lors de l\'acceptation de la demande du joueur.', 'error' => $e->getMessage()], 500);
        }
    }

    public function rejectPlayerRequestInTeam($playerId, $teamId)
    {
        try {
            $team = Team::findOrFail($teamId);
            $player = User::findOrFail($playerId);

            $request = $team->players()->where('user_id', $playerId)->wherePivot('status', 'pending')->first();

            if ($request) {
                $team->players()->updateExistingPivot($playerId, ['status' => 'rejected']);

                return response()->json(['message' => 'La demande du joueur a été refusée avec succès.']);
            } else {
                return response()->json(['message' => 'Aucune demande en attente trouvée pour ce joueur dans cette équipe.'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Une erreur est survenue lors du refus de la demande du joueur.', 'error' => $e->getMessage()], 500);
        }
    }

    public function createTeam(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'championship_id' => 'required|exists:sports,id',
        ]);

        try {
            $team = new Team();
            $team->name = $validatedData['name'];
            $team->championship_id = $validatedData['championship_id'];
            $team->user_id = $request->user()->id;
            $team->save();

            return response()->json(['message' => 'Equipe créé avec succès', 'team' => $team], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la création de l\'équipe', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateTeam(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:70',
            ]);

            $team = Team::findOrFail($id);

            $team->name = $request->input('name');
            $team->save();

            return response()->json([
                'message' => 'Le nom de l\'équipe a été mise à jour avec succès !', $team
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la mise à jour du nom de l\'équipe', 'error' => $e->getMessage()], 500);
        }
    }

    public function updatePlayerStatusInTeam(Request $request, $teamId, $playerId)
    {
        try {
            $team = Team::findOrFail($teamId);

            $player = User::findOrFail($playerId);

            if ($team->players->contains($player)) {

                $team->players()->updateExistingPivot($playerId, ['status' => 'rejected']);

                return response()->json(['message' => 'Le joueur a été retiré de l\'équipe avec succès.']); 
            } else {
                return response()->json(['message' => 'Le joueur ne fait pas partie de cette équipe.'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la suppression du joueur de l\'équipe', 'error' => $e->getMessage()], 500);
        }
    } 

    public function deleteTeamById($id)
    {
        try {
            $team = Team::findOrFail($id);

            $team->delete();

            return response()->json(['message' => 'L\'équipe a été supprimée avec succès !'], 204); 
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la suppression de l\'équipe', 'error' => $e->getMessage()], 500);
        }
    }   
}