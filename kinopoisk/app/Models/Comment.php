<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Comment extends Model
{
    use HasFactory;
    protected $fillable = [
        'comment', 
        'film_id',
        'user_id'
    ];

    public function getCreatedAtAttribute($date)
    {
        return Carbon::parse($date)->format('d-M-Y H:i:s');
    }

    public function getUpdatedAtAttribute($date)
    {
        return Carbon::parse($date)->format('d-M-Y H:i:s');
    }
}
