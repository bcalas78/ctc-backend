<?php

namespace App\Http\Controllers;

use App\Models\Sport;
use Illuminate\Http\Request;

class SportController extends Controller
{
    public function getAllSports(Request $request)
    {
        try{
            $sports = Sport::all();

            return response()->json($sports);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Une erreur est survenue lors de la récupération des sports.'], 500);
        } 
    }
}