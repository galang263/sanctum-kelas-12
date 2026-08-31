<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Aktor;

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
        return $this->belongsTo(Genre::class, 'id_genre');
    }

    public function aktors()
    {
        return $this->belongsToMany(
            Aktor::class,
            'aktor__films',
            'film_id',
            'aktor_id'
        );
    }
}
