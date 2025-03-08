<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api'); // Protege el controlador con JWT
    }

    public function home(Request $request)
    {
        // Obtener el usuario autenticado desde el token
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Usuario no autenticado. Verifique el token enviado.',
            ], 401);
        }

        // Verificar si el usuario tiene una tarjeta asignada
        if (!$user->card_number) { // Suponiendo que la columna en la DB se llama 'card_number'
            return response()->json([
                'status' => 'NO_CARD',
                'message' => 'El usuario no tiene una tarjeta asignada.',
            ], 200);
        }

        // Consumir la API de bromas (JokeAPI)
        $response = Http::get('https://v2.jokeapi.dev/joke/Any');

        if ($response->successful()) {
            return response()->json([
                'status' => 'SUCCESS',
                'joke' => $response->json(),
            ], 200);
        }

        return response()->json([
            'status' => 'ERROR',
            'message' => 'No se pudo obtener una broma en este momento.',
        ], 500);
    }
}
