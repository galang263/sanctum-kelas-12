<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Exception;

class GenreController extends Controller
{
    public function index()
    {
       try {
        $genre = Genre::latest()->get();
        return response()->json([
            'status' => true,
            'message' => 'data genre berhasil diambil',
            'data' => $genre
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
        try{
            $validated = $request->validate([
                'nama_genre' => 'required|unique:genres,nama_genre',
            ]);
            $genre = new Genre();
            $genre->nama_genre = $request->nama_genre;
            $genre->slug = Str::slug($request->nama_genre). str::random(10);
            $genre->save();

            return response()->json([
                'status' => true,
                'message' => 'data genre berhasil ditambahkan',
                'data' => $genre
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try{

            $genre = Genre::find($id);
            if (!$genre) {
                return response()->json([
                    'status' => false,
                    'message' => 'data genre tidak ditemukan'
                ], 404);
            }

            $validated = $request->validate([
                'nama_genre' => 'required|unique:genres,nama_genre,'.$id,
            ]);
            $genre = Genre::find($id);
            $genre->nama_genre = $request->nama_genre;
            $genre->slug = Str::slug($request->nama_genre). str::random(10);
            $genre->save();

            return response()->json([
                'status' => true,
                'message' => 'data genre berhasil diubah',
                'data' => $genre
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id){
        try{
            $genre = Genre::find($id);
            if (!$genre) {
                return response()->json([
                    'status' => false,
                    'message' => 'data genre tidak ditemukan'
                ], 404);
            }
            $genre->delete();
            return response()->json([
                'status' => true,
                'message' => 'data genre berhasil dihapus'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
