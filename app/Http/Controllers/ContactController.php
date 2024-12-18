<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use Illuminate\Support\Facades\Mail;


class ContactController extends Controller
{
    public function sendMessage(Request $request)
    {
        // Validation des données
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'message' => 'required|string',
            ]);

            // Sauvegarder dans la base de données (optionnel)
            $contact = Contact::create($request->all());

            // Envoyer un email (optionnel)
            Mail::raw($request->message, function ($message) use ($request) {
                $message->to('sakhoaichatou11@gmail.com')
                ->subject('Nouveau message de contact de ' . $request->name);
                $message->from($request->email, $request->name);
            });

            return response()->json(['message' => 'Message envoyé avec succès!'], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }
}
