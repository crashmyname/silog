<?php

namespace App\Controllers;

use App\Models\Route;
use Bpjs\Framework\Helpers\BaseController;
use Bpjs\Framework\Core\Request;
use Bpjs\Framework\Helpers\View;

class RouteController extends BaseController
{
    // Controller logic here
    public function list(Request $request) {
        $rows = Route::query()->where('is_active', '=', 1)->orderBy('kode', 'ASC')->get();
        return $this->json(['data' => array_map(fn($r) => [
            'id' => (int)$r->id, 'kode' => $r->kode, 'asal' => $r->asal, 'tujuan' => $r->tujuan,
            'jarak' => $r->jarak, 'estimasi' => $r->estimasi, 'tarif' => $r->tarif
        ], $rows)], 200);
    }

    public function store(Request $request) {
        if (!$request->validate(['kode' => 'required|max:30', 'asal' => 'required', 'tujuan' => 'required']))
            return $this->json(['errors' => $request->errors()], 422);
        
        $r = Route::create([
            'kode' => strtoupper($request->kode), 'asal' => $request->asal, 'tujuan' => $request->tujuan,
            'jarak' => $request->jarak, 'estimasi' => $request->estimasi, 'tarif' => $request->tarif, 'is_active' => 1
        ]);
        return $this->json(['message' => 'Rute ditambahkan.', 'data' => ['id' => (int)$r->id]], 201);
    }

    public function update(Request $request, $id) {
        $r = Route::find((int)$id);
        if (!$r) return $this->json(['message' => 'Rute tidak ditemukan.'], 404);
        $r->update([
            'kode' => strtoupper($request->kode), 'asal' => $request->asal, 'tujuan' => $request->tujuan,
            'jarak' => $request->jarak, 'estimasi' => $request->estimasi, 'tarif' => $request->tarif
        ]);
        return $this->json(['message' => 'Rute diperbarui.'], 200);
    }

    public function destroy($id) {
        $r = Route::find((int)$id);
        if (!$r) return $this->json(['message' => 'Rute tidak ditemukan.'], 404);
        $r->delete();
        return $this->json(['message' => 'Rute dihapus.'], 200);
    }
}
