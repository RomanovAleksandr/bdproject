<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Director;

class DirectorController extends Controller
{
    public function show($id)
    {
        $director = Director::find($id);
        if (!$director) {
            return response()->json([
                "status" => false,
                "message" => "Director not found"
            ])->setStatusCode(404);
        }
        $films = Film::join('film_directors', 'films.id', '=', 'film_directors.film_id')
                        ->where('film_directors.director_id', '=', $id)
                        ->select('films.id', 'films.title')
                        ->get();

        return response()->json([
            'films' => $films,
            'director' => $director,
        ]);
    }
}
