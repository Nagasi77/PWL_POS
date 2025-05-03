<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LevelModel;
use Illuminate\Support\Facades\Log;
class LevelController extends Controller
{
    public function index()
    {
        return LevelModel::all();
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validated = $request->validate([
                'level_kode' => 'required|string|max:10|unique:m_level,level_kode',
                'level_nama' => 'required|string|max:100',
            ]);

            $level = LevelModel::create($validated);

            Log::info('Level baru berhasil dibuat.', ['data' => $level]);

            return response()->json([
                'message' => 'Level berhasil dibuat.',
                'data' => $level
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Jika validasi gagal
            return response()->json([
                'error' => 'Validasi gagal',
                'details' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            // Error lainnya (DB error, logic error, dll)
            Log::error('Gagal membuat level.', ['error' => $e->getMessage()]);
            return response()->json([
                'error' => 'Terjadi kesalahan saat membuat level.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show(LevelModel $level)
    {
        return LevelModel::find($level);
    }

    public function update(Request $request, LevelModel $level): \Illuminate\Http\JsonResponse
    {
        $level->update($request->all());
        return LevelModel::find($level);
    }
    public function destroy(LevelModel $user): \Illuminate\Http\JsonResponse
    {
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus',
        ]);
    }
}
