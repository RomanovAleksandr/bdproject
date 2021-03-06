<?php

namespace App\Http\Controllers;

use App\Models\FilmScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class UserController extends Controller
{
    public function setImage(Request $request)
    {
        $this->validate($request, [
            'input_img' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        if ($request->hasFile('input_img'))
        {
            $path = $request->file('input_img')->store('users','public');
            if (Auth::check()) {
                $user = User::find(Auth::id());
                if ($user->image_path != null)
                {
                    Storage::disk('public')->delete($user->image_path);
                }
                $user->image_path = $path;
                $user->save();
                $user->refresh();
            }
            return response()->json([
                'path' => $path,
            ]);
        }
    }

    public function deleteImage(Request $request)
    {
        if (Auth::check()) {
            $user = User::find(Auth::id());
            Storage::disk('public')->delete($user->image_path);
            $user->image_path = null;
            $user->save();
            $user->refresh();
        }
    }

    public function userData()
    {
        if (Auth::check()) {
            $userData = FilmScore::where('user_id', '=', Auth::id())
                                    ->select(FilmScore::raw('DATE(created_at) as date'), FilmScore::raw('count(*) as scores'))
                                    ->groupBy('date')
                                    ->get();
            return response()->json(
                $userData,
            );
        }
    }
}
