<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Actor;
use App\Models\Director;
use App\Models\Genre;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\FilmScore;

class FilmsController extends Controller
{
    public function index(Request $request)
    {
        $films = Film::query();

        if($request->genre_id && $request->genre_id != "all")
        {
            $films->join('film_genres', 'films.id', '=', 'film_genres.film_id')
                    ->where('film_genres.genre_id', '=', $request->genre_id);
        }
        if($request->year && $request->year != "all")
        {
            $films->where('year', '=', $request->year);
        }
        if($request->search)
        {
            $films->where('title', 'like', '%'.$request->search.'%');
        }

        $films->select('films.*');


        $genres = Genre::join('film_genres', 'genres.id', '=', 'film_genres.genre_id')
                        ->select('genres.genre', 'genres.id', 'film_genres.film_id')
                        ->get();

        $allGenres = Genre::all();

        return response()->json([
            'films' => $films->get(),
            'genres' => $genres,
            'allGenres' => $allGenres
        ]);
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
                            ->select('comments.comment', 'comments.id', 'comments.created_at', 'comments.updated_at', 'users.name', 'users.image_path')
                            ->where('comments.film_id', '=', $id)
                            ->get();

        $score = FilmScore::where('film_id', '=', $id)
                            ->avg('score');

        if(!$score)
        {
            $score = 0;
        }

        return response()->json([
            'film' => $film,
            'actors' => $actors,
            'directors' => $directors,
            'genres' => $genres,
            'comments' => $comments,
            'score' => $score
        ]);
    }
}
