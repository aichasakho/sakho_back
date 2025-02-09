<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UpdateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * la methode store est utiliser ici pour effectuer la modification des bien
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $bien = Bien::findOrFail($request->id);
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

        $bien->update($validatedData);
        return response()->json($bien,201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
