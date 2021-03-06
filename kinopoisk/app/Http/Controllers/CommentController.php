<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $reverse = '';
        if($request->reverse == "true")
        {
            $reverse = 'asc';
        }
        else
        {
            $reverse = 'desc';
        }

        return Comment::join('users', 'comments.user_id', '=', 'users.id')
                        ->select('comments.comment', 'comments.id', 'comments.created_at', 'comments.updated_at', 'comments.user_id', 'users.name', 'users.image_path')
                        ->where('comments.film_id', '=', $request->film_id)
                        ->orderBy('comments.created_at', $reverse)
                        ->get();
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
        $request->validate([
            'comment' => ['required'],
            'film_id' => ['required']
        ]);

        $comment = new Comment([
            'comment' => $request->input('comment'),
            'film_id' => $request->input('film_id'),
            'user_id' => Auth::id()
        ]);
        $comment->save();

        return $comment;
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
            'comment' => ['required']
        ]);

        $comment = Comment::find($id);
        if($comment->user_id != Auth::id())
        {
            return response()->json('Error!');
        }
        $comment->comment = $request->comment;
        $comment->save();

        return response()->json('Product updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $comment = Comment::find($id);
        if($comment->user_id != Auth::id())
        {
            return response()->json('Error!');
        }
        $comment->delete();

        return response()->json('The post successfully deleted');
    }
}
