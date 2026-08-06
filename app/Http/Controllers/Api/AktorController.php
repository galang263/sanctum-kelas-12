<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Aktor;
use Illuminate\Http\Request;
use Exception;

class AktorController extends Controller
{
    public function index()
    {
        try {
            $aktor = Aktor::all();
            return response()->json([
                'status' => true,
                'message' => 'data aktor berhasil diambil',
                'data' => $aktor
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
                'nama_aktor' => 'required|string',
                'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
                'tanggal_lahir' => 'required|date',
                'umur' => 'required|integer',
                'foto' => 'nullable|image|max:2048'
            ]);

            $aktor = new Aktor();
            $aktor->nama_aktor = $request->nama_aktor;
            $aktor->jenis_kelamin = $request->jenis_kelamin;
            $aktor->tanggal_lahir = $request->tanggal_lahir;
            $aktor->umur = $request->umur;

            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/aktors'), $filename);
                $aktor->foto = 'uploads/aktors/' . $filename;
            }

            $aktor->save();

            return response()->json([
                'status' => true,
                'message' => 'Actors data saved successfully.',
                'data' => $aktor
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
            $aktor = Aktor::find($id);
            if (!$aktor) {
                return response()->json([
                    'status' => false,
                    'message' => 'data tidak ditemukan'
                ], 404);
            }
            $validated = $request->validate([
                'nama_aktor' => 'required|unique:aktors,nama_aktor,'.$id,
            ]);
            $aktor->nama_aktor = $request->nama_aktor;
            $aktor->save();

            return response()->json([
                'status' => true,
                'message' => 'data aktor berhasil diubah',
                'data' => $aktor
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
        try{
            $aktor = Aktor::find($id);
            if (!$aktor) {
                return response()->json([
                    'status' => false,
                    'message' => 'data tidak ditemukan'
                ], 404);
            }
            $aktor->delete();
            return response()->json([
                'status' => true,
                'message' => 'data aktor berhasil dihapus',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
