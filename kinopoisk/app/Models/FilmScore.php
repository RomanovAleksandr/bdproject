<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FilmScore extends Model
{
    use HasFactory;
    protected $fillable = [
        'score', 
        'film_id',
        'user_id'
    ];
}
