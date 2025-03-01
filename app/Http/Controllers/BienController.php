<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Mail\AppelBien;
use App\Models\Contact;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class BienController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ip = request()->ip();  // Récupère l'IP locale du serveur (ex: 192.168.1.100)

        $bien = Bien::all();

        return response()->json($bien->map(function ($bien) use ($ip) {
            if (filter_var($bien->imagePath, FILTER_VALIDATE_URL)) {
                $bien->imageUrl = $bien->imagePath; // Si c'est une URL externe, on ne change rien
            } else {
                $bien->imageUrl = "http://192.168.43.172:8000/images/" . $bien->imagePath; // Ajout de l'IP locale
            }
            return $bien;
        }), 200);
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'prix' => 'required|numeric',
            'disponible' => 'required',
            //'type' => 'required',
            'type_annonce' => 'required',
            'imagePath' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'nombre_douches' => 'nullable|integer',
            'nombre_chambres' => 'nullable|integer',
            'superficie' => 'nullable',
        ]);

        if ($request->hasFile('imagePath')) {
            $file = $request->file('imagePath');
            Log::info('Fichier reçu : ' . $file->getClientOriginalName());
            $extension = $file->getClientOriginalExtension();
            $fileName = time() . '.' . $extension;
            $file->move(public_path('images'), $fileName);
            $validatedData['imagePath'] = $fileName;
        } else {
            Log::error('Aucun fichier reçu dans la requête');
            return response()->json(['message' => 'Photo non fournie'], 422);
        }

        $bien = Bien::create($validatedData);

        return response()->json($bien, 201);
    }


    public function show(string $id)
    {
        $bien = Bien::findOrFail($id);
        // Vérifiez si l'imagePath est une URL externe ou un chemin local
        if (filter_var($bien->imagePath, FILTER_VALIDATE_URL)) {
            // URL externe
            $bien->imageUrl = $bien->imagePath;
        } else {
            // URL locale (préfixée avec le chemin des assets)
            $bien->imageUrl = asset('images/' . $bien->imagePath);
        }

        return response()->json($bien, 200);
    }

    public function update(Request $request, $id)
    {
        // voir UpdateController@store
    }

    public function destroy($id)
    {
        $bien = Bien::find($id);
        if ($bien) {
            $bien->delete();
            if ($bien->imagePath){
                Storage::delete('public/' . $bien->imagePath);
            }
            return response()->json(['message' => 'Bien supprimé avec succès'], 200);
        } else {
            return response()->json(['message' => 'Bien non trouvé'], 404);
        }
    }



    public function appeler(Request $request, Bien $bien)
    {
        $agent = $bien->agent;
        $utilisateur = [
            'nom' => $request->input('nom'),
            'email' => $request->input('email'),
            'telephone' => $request->input('telephone')
        ];

        Mail::to($agent->email)->send(new AppelBien($bien, $utilisateur));

        return response()->json(['message' => 'Demande d\'appel envoyée avec succès']);
    }

    public function contacter(Request $request, Bien $bien)
    {
        $contact = Contact::create([
            'bien_id' => $bien->id,
            'nom' => $request->input('nom'),
            'email' => $request->input('email'),
            'telephone' => $request->input('telephone'),
            'message' => $request->input('message')
        ]);

        return response()->json(['message' => 'Demande de contact envoyée avec succès']);
    }

    public function location()
    {

        $bien = Bien::where('type_annonce', 'location')->get();
        return response()->json($bien->map(function ($bien) {

            // Vérifiez si l'imagePath est une URL externe
            if (filter_var($bien->imagePath, FILTER_VALIDATE_URL)) {
                // Gardez l'URL telle quelle si elle est externe
                $bien->imageUrl = $bien->imagePath;
            } else {
                // Préfixez avec le chemin local pour les images internes
                $bien->imageUrl = asset('images/'. $bien->imagePath);
            }
            return $bien;
        }),200);    }

    public function vente()
    {

        $bien = Bien::where('type_annonce', 'vente')->get();
        return response()->json($bien->map(function ($bien) {

            // Vérifiez si l'imagePath est une URL externe
            if (filter_var($bien->imagePath, FILTER_VALIDATE_URL)) {
                // Gardez l'URL telle quelle si elle est externe
                $bien->imageUrl = $bien->imagePath;
            } else {
                // Préfixez avec le chemin local pour les images internes
                $bien->imageUrl = asset('images/'. $bien->imagePath);
            }
            return $bien;
        }),200);
    }
}
