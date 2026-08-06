<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Film;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Exception;

class FilmController extends Controller
{
    public function index()
    {
        try {
            $film = Film::with('genre')->latest()->get();
            return response()->json([
                'status' => true,
                'message' => 'data film berhasil diambil',
                'data' => $film
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'judul_film' => 'required|string',
                'durasi' => 'required|integer',
                'rating' => 'required|numeric|between:0,10',
                'deskripsi' => 'required|string',
                'tahun_rilis' => 'required|date_format:Y',
                'poster' => 'nullable|image|max:2048',
                'genre_id' => 'required|exists:genres,id',
                'sutradara' => 'required|string'
            ]);

            $film = new Film();
            $film->judul_film = $request->judul_film;
            $film->durasi = $request->durasi;
            $film->rating = $request->rating;
            $film->deskripsi = $request->deskripsi;
            $film->tahun_rilis = $request->tahun_rilis;
            $film->genre_id = $request->genre_id;
            $film->sutradara = $request->sutradara;
            $film->slug = Str::slug($request->judul_film) . '-' . Str::random(10);

            if ($request->hasFile('poster')) {
                $file = $request->file('poster');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/posters'), $filename);
                $film->poster = 'uploads/posters/' . $filename;
            }

            $film->save();

            return response()->json([
                'status' => true,
                'message' => 'Film data saved successfully.',
                'data' => $film
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    } // <--- Ini penutup yang sebelumnya terlewat

    public function update(Request $request, $id)
    {
        try {
            $film = Film::find($id);
            if (!$film) {
                return response()->json([
                    'status' => false,
                    'message' => 'Film data not found'
                ], 404);
            }

            $request->validate([
                'judul_film' => 'required|string',
                'durasi' => 'required|integer',
                'rating' => 'required|numeric|between:0,10',
                'deskripsi' => 'required|string',
                'tahun_rilis' => 'required|date_format:Y',
                'poster' => 'nullable|image|max:2048',
                'genre_id' => 'required|exists:genres,id',
                'sutradara' => 'required|string'
            ]);

            $film->judul_film = $request->judul_film;
            $film->durasi = $request->durasi;
            $film->rating = $request->rating;
            $film->deskripsi = $request->deskripsi;
            $film->tahun_rilis = $request->tahun_rilis;
            $film->genre_id = $request->genre_id;
            $film->sutradara = $request->sutradara;
            $film->slug = Str::slug($request->judul_film) . '-' . Str::random(10);

            if ($request->hasFile('poster')) {
                // Delete old poster if exists
                if ($film->poster && file_exists(public_path($film->poster))) {
                    unlink(public_path($film->poster));
                }

                $file = $request->file('poster');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/posters'), $filename);
                $film->poster = 'uploads/posters/' . $filename;
            }

            $film->save();

            return response()->json([
                'status' => true,
                'message' => 'Film data updated successfully.',
                'data' => $film
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $film = Film::find($id);
            if (!$film) {
                return response()->json([
                    'status' => false,
                    'message' => 'Film data not found'
                ], 404);
            }

            // Delete poster if exists
            if ($film->poster && file_exists(public_path($film->poster))) {
                unlink(public_path($film->poster));
            }

            $film->delete();

            return response()->json([
                'status' => true,
                'message' => 'Film data deleted successfully.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
