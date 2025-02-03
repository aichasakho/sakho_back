<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        
        if (Auth::attempt($credentials)) {
            $user = Auth::user();  
            $token = $user->createToken('YourAppName')->plainTextToken;  
            
           
            return response()->json([
                'message' => 'Connexion réussie',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'nom_complet' => $user->nom_complet,
                    'email' => $user->email,
                    'role' => $user->role  
                ]
            ]);
        }

        return response()->json([
            'message' => 'Identifiants invalides'
        ], 401);
    }

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'nom_complet' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role' => 'in:user,admin,super_admin',
        ]);

        $user = User::create([
            'nom_complet' => $validatedData['nom_complet'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
            'role' => $validatedData['role'] ?? 'user',

        ]);

        $token = $user->createToken('MyApp')->plainTextToken;

        return response()->json([
            'message' => 'Utilisateur enregistré avec succès',
            'token' => $token,
        ], 201);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->user()->tokens->each(function ($token) {
            $token->delete();
        });

        return response()->json(['message' => 'Déconnexion réussie'], 200);
    }

}
