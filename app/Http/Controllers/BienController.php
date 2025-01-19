<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppelBien;
use App\Models\Contact;

class BienController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
        return Bien::all();
    }

    public function store(Request $request) {
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'prix' => 'required|numeric',
            'disponible' => 'boolean',
            'type' => 'required|string|in:appartement,studio,magasin,terrain,maison',
            'imagePath' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'nombre_douches' => 'nullable|integer',
            'nombre_chambres' => 'nullable|integer',
            'superficie' => 'nullable|numeric',
        ]);

        $data = $request->only('titre', 'description', 'prix', 'disponible', 'type', 'nombre_douches', 'nombre_chambres', 'superficie');


        if ($request->hasFile('imagePath')) {
            $data['imagePath'] = $request->file('imagePath')->store('images', 'public');
        }

        $bien = Bien::create($data);
        return response()->json($bien, 201);
    }


    public function show(Bien $bien) {
        return $bien;
    }

    public function update(Request $request, $id)
    {
        $bien = Bien::find($id);
        if (!$bien) {
            return response()->json(['message' => 'Bien non trouvé'], 404);
        }

        $request->validate([
            'titre' => 'required|string',
            'description' => 'required|string',
            'prix' => 'required|numeric',
            'type' => 'required|string',
            'imagePath' => 'nullable|image',
            'nombre_douches' => 'nullable|integer',
            'nombre_chambres' => 'nullable|integer',
            'superficie' => 'nullable|numeric',
        ]);

        $bien->titre = $request->input('titre');
        $bien->description = $request->input('description');
        $bien->prix = $request->input('prix');
        $bien->type = $request->input('type');
        $bien->disponible = $request->input('disponible', false);
        $bien->nombre_douches = $request->input('nombre_douches');
        $bien->nombre_chambres = $request->input('nombre_chambres');
        $bien->superficie = $request->input('superficie');

        if ($request->hasFile('imagePath')) {
            if ($bien->imagePath) {
                \Storage::delete('public/' . $bien->imagePath);
            }
            $bien->imagePath = $request->file('imagePath')->store('images', 'public');
        }

        $bien->save();

        return response()->json(['message' => 'Bien mis à jour avec succès'], 200);
    }

    public function destroy($id)
    {
        $bien = Bien::find($id);
        if ($bien) {
            $bien->delete();
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

        return response()->json(['message' => 'Demande d\'appel envoyée avec succès']);}

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

        $bien = Bien::all()->where('type_annonce','location')->get();
        return response()->json($bien);
    }

    public function vente()
    {

        $bien = Bien::all()->where('type_annonce','vente')->get();
        return response()->json($bien);
    }

}
