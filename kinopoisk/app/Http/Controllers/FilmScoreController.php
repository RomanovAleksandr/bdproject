<?php

namespace App\Http\Controllers;

use App\Models\FilmScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FilmScoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        if (FilmScore::where('user_id', '=', Auth::id())->where('film_id', '=', $id)->exists())
        {
            return FilmScore::select('score')->where('user_id', '=', Auth::id())->where('film_id', '=', $id)->get()[0]->score;
        }
        else
        {
            return 0;
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (FilmScore::where('user_id', '=', Auth::id())->where('film_id', '=', $request->film_id)->exists())
        {
            $scoreId = FilmScore::select('id')
                                ->where('user_id', '=', Auth::id())
                                ->where('film_id', '=', $request->film_id)
                                ->get();
            return $this->update($request, $scoreId[0]->id);
        }

        $request->validate([
            'score' => ['required'],
            'film_id' => ['required']
        ]);

        $score = new FilmScore([
            'score' => $request->input('score'),
            'film_id' => $request->input('film_id'),
            'user_id' => Auth::id()
        ]);
        $score->save();
        $score = FilmScore::avg('score');

        return $score;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'score' => ['required']
        ]);

        $score = FilmScore::find($id);
        $score->score = $request->score;
        $score->save();

        $score = FilmScore::selectRaw('AVG(score) as score')
                            ->where('film_id', '=', $request->film_id)
                            ->groupBy('film_id')
                            ->get();

        return $score[0]->score;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
