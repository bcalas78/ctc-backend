<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\PasswordResetRequest;
use App\Http\Requests\RegisterRequest;
use App\Mail\PasswordResetMail;
use App\Models\User;
use App\Models\Role;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        //Validation des données
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:60',
            'lastname' => 'required|string|max:80',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'min:12',
                'max:255',
                'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{12,}$/',
            ],
            'avatar' => 'string',
            'role' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status' => 422
            ], 422);
        }

        //Hachage du mot de passe
        $hashedPassword = Hash::make($request->input('password'));

        //Création du user
        $newUser = User::create([
            'firstname' => $request->input('firstname'),
            'lastname' => $request->input('lastname'),
            'email' => $request->input('email'),
            'password' => $hashedPassword,
            'avatar' => $request->input('avatar'),
            "created_at" => now(),
            // 'email_verified_token' => Str::random(60),
        ]);

        $role = $request->input('role');

        $newUser->assignRole($role);

        //Réponse JSON en cas de succès
        return response()->json([
            'status' => 201,
            'message' => "Compte créé avec succès !",
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $authToken = $user->createToken('authToken')->plainTextToken;

            return response()->json([
                'status' => 200,
                'user' => $user,
                'authToken' => $authToken,
                'message' => 'Connexion réussie !',
            ]);
        }
        return response()->json([
            'error' => 'Email ou mot de passe invalide'
        ], 401);
    }

    public function sendPasswordResetMail(Request $request)
    {
        $validatedData = $request->validate(['email' => 'required|email']);

        $registeredUser = User::where('email', $validatedData['email'])->first();
        
        if ($registeredUser) {
            $token = Str::random(60);
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $registeredUser->email],
                ['token' => Hash::make($token), 'created_at' => now()]
            );

            Mail::to($registeredUser->email)->send(new PasswordResetMail($token));
            
            return response()->json([
                'status' => 250,
                'message' => 'Email envoyé',
                'email' => $registeredUser->email
            ], 250);
        }
        return response()->json(['error' => 'Email non trouvé.'], 404);
    }

    public function resetPassword(PasswordResetRequest $request)
    {
        $data = $request->validated();

        $passwordReset = DB::table('password_reset_tokens')->where('email', $data['email'])->first();

        if (!$passwordReset || !Hash::check($data['token'], $passwordReset->token)) {
            return response()->json(['error' => 'Token non valide.'], 400);
        }

        $user = User::where('email', $data['email'])->first();
        $user->password = Hash::make($data['password']);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $request['email'])->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Votre mot de passe a été réinitialisé avec succès !'
        ]);
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens->each(function ($token) {
            $token->delete();
        });
        return response()->json([
            'status' => 200,
            'message' => 'Déconnexion réussie !',
        ]);
    }
}
