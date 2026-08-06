<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
    protected $fillable = [
        'judul_film',
        'durasi',
        'rating',
        'deskripsi',
        'tahun_rilis',
        'poster',
        'genre_id',
        'sutradara',
        'slug'
    ];

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }
}
