<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Championship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChampionshipController extends Controller
{
    public function getAllChampionships(Request $request)
    {
        try{
            $championships = Championship::with('sport:id,name')->get(['id', 'name', 'sport_id']);

            return response()->json($championships);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Une erreur est survenue lors de la récupération des championnats.'], 500);
        } 
    }

    public function getChampionshipById($id)
    {
        try {
            $championship = Championship::findOrFail($id);

            $sportName = DB::table('sports')
                ->where('id', $championship->sport_id)
                ->value('name');

            return response()->json([
                'championship' => [
                    'id' => $championship->id,
                    'name' => $championship->name,
                    'sport_id' => $championship->sport_id,
                    'sport' => $sportName,
                    'start_date' => $championship->start_date,
                    'end_date' => $championship->end_date,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Une erreur s\'est produite lors de la récupération du championnat.'], 500);
        }
    }

    public function getChampionshipsByUser(Request $request)
    {
        $user = Auth::user();
        $role = $user->roles()->first();

        if ($role) {
            if ($role->name === 'dirigeant') {
                $championships = Championship::where('user_id', $user->id)->get();
            } else if ($role->name === 'joueur') {
                $teamIds = $user->teams()->pluck('id')->toArray();
                $championships = Championship::whereIn('id', function($query) use ($teamIds) {
                    $query->select('championship_id')
                          ->from('teams')
                          ->whereIn('id', $teamIds);
                })->get();
            } else {
                $championships = [];
            }

            foreach($championships as $championship) {
                $championship->sport = $championship->sport()->first()->name;
            }

        } else {
            $championships = [];
        }
       
        return response()->json($championships);
    }

    public function createChampionship(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'start_date' => 'date',
            'end_date' => 'date|after:start_date',
            'sport_id' => 'required|exists:sports,id',
        ]);

        try {
            $championship = new Championship();
            $championship->name = $validatedData['name'];
            $championship->start_date = $validatedData['start_date'];
            $championship->end_date = $validatedData['end_date'];
            $championship->sport_id = $validatedData['sport_id'];
            $championship->user_id = $request->user()->id;
            $championship->save();

            return response()->json(['message' => 'Championnat créé avec succès', 'championship' => $championship], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la création du championnat', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateChampionship(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'start_date' => 'date',
                'end_date' => 'date|after:start_date',
                'sport_id' => 'required|integer|exists:sports,id',
            ]);

            $championship = Championship::findOrFail($id);

            $championship->name = $request->input('name');
            $championship->start_date = $request->input('start_date');
            $championship->end_date = $request->input('end_date');
            $championship->sport_id = $request->input('sport_id');

            $championship->save();

            return response()->json([
                'message' => 'Vos informations ont été mises à jour avec succès !', $championship
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la mise à jour du championnat', 'error' => $e->getMessage()], 500);
        }
    }

    public function deleteChampionshipById($id)
    {
        try {
            $championship = Championship::findOrFail($id);

            $championship->delete();

            return response()->json(['message' => 'Le championnat a été supprimé avec succès !'], 204); 
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la suppression du championnat', 'error' => $e->getMessage()], 500);
        }
    }   
}