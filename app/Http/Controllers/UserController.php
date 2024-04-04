<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function getUserRole(Request $request)
    {
        $user = Auth::user();

        if ($user && $user->roles()->exists()) {
            $role = $user->roles()->firstOrFail();
            return response()->json(['role' => $role->name]);
        }

        return response()->json(['error' => 'L\'utilisateur n\'a pas de rôle défini'], 404);
    }

    public function getUserInfos(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Utilisateur non authentifié'], 401);
        }

        return response()->json([
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'email' => $user->email,
            'avatar' => $user->avatar,
        ]);
    }

    public function updateUserInfos(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'firstname' => 'required|string|max:60',
            'lastname' => 'required|string|max:80',
            'avatar' => 'string',
        ]);

        $user->update($validatedData);

        return response()->json([
            'message' => 'Vos informations ont été mises à jour avec succès !'
        ], 200);
    }
}