<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Actor;
use App\Models\Director;
use App\Models\Genre;
use Illuminate\Http\Request;
use App\Models\Comment;

class FilmsController extends Controller
{
    public function index(Request $request)
    {
        $input = $request->all();
        if ($request->genre_id)
        {
            $films = Film::join('film_genres', 'films.id', '=', 'film_genres.film_id')
                            ->where('film_genres.genre_id', '=', $request->genre_id)
                            //->select('films.id', 'films.title', 'films.year', 'films.poster', 'films.description', 'films.duration')
                            ->select('films.*')
                            ->get();
        }
        else{
            $films = Film::all();
        }

        

        $genres = Genre::join('film_genres', 'genres.id', '=', 'film_genres.genre_id')
                        ->select('genres.genre', 'genres.id', 'film_genres.film_id')
                        ->get();

        return response()->json([
            'films' => $films,
            'genres' => $genres,
            'input' => $input,
        ]);

        // return Film::all();
    }

    public function show($id)
    {
        $film = Film::find($id);
        if (!$film) {
            return response()->json([
                "status" => false,
                "message" => "Film not found"
            ])->setStatusCode(404);
        }

        $actors = Actor::join('roles', 'actors.id', '=', 'roles.actor_id')
                        ->where('roles.film_id', '=', $id)
                        ->select('actors.name', 'actors.id')
                        ->get();

        $directors = Director::join('film_directors', 'directors.id', '=', 'film_directors.director_id')
                            ->where('film_directors.film_id', '=', $id)
                            ->select('directors.name', 'directors.id')
                            ->get();

        $genres = Genre::join('film_genres', 'genres.id', '=', 'film_genres.genre_id')
                            ->where('film_genres.film_id', '=', $id)
                            ->select('genres.genre', 'genres.id')
                            ->get();

        $comments = Comment::join('users', 'comments.user_id', '=', 'users.id')
                            ->select('comments.comment', 'comments.id', 'comments.created_at', 'users.name', 'users.image_path')
                            ->where('comments.film_id', '=', $id)
                            ->get();

        return response()->json([
            'film' => $film,
            'actors' => $actors,
            'directors' => $directors,
            'genres' => $genres,
            'comments' => $comments
        ]);
    }
}
