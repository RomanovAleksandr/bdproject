<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Actor;

class ActorsController extends Controller
{
    public function show($id)
    {
        $actor = Actor::find($id);
        if (!$actor) {
            return response()->json([
                "status" => false,
                "message" => "Actor not found"
            ])->setStatusCode(404);
        }
        $films = Film::join('roles', 'films.id', '=', 'roles.film_id')
                        ->where('roles.actor_id', '=', $id)
                        ->select('films.id', 'films.title')
                        ->get();

        return response()->json([
            'films' => $films,
            'actor' => $actor,
        ]);
    }
}
