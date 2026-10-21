<?php

namespace App\Controllers;

use App\Models\Driver;
use Bpjs\Framework\Helpers\BaseController;
use Bpjs\Framework\Core\Request;
use Bpjs\Framework\Helpers\View;

class DriverController extends BaseController
{
    // Controller logic here
    public function list(Request $request) {
        $rows = Driver::query()->where('is_active', '=', 1)->orderBy('nama', 'ASC')->get();
        return $this->json(['data' => array_map(fn($r) => [
            'id' => (int)$r->id, 'nip' => $r->nip, 'nama' => $r->nama, 'telp' => $r->telp,
            'sim' => $r->sim, 'vendor' => $r->vendor, 'status' => $r->status
        ], $rows)], 200);
    }

    public function store(Request $request) {
        if (!$request->validate(['nip' => 'required|max:30', 'nama' => 'required|max:100']))
            return $this->json(['errors' => $request->errors()], 422);
        
        $d = Driver::create([
            'nip' => strtoupper($request->nip), 'nama' => $request->nama,
            'telp' => $request->telp, 'sim' => $request->sim ?? 'B2 Umum', 'vendor' => $request->vendor,
            'status' => $request->status ?? 'Aktif', 'is_active' => 1
        ]);
        return $this->json(['message' => 'Driver ditambahkan.', 'data' => ['id' => (int)$d->id]], 201);
    }

    public function update(Request $request, $id) {
        $d = Driver::find((int)$id);
        if (!$d) return $this->json(['message' => 'Driver tidak ditemukan.'], 404);
        $d->update([
            'nip' => strtoupper($request->nip), 'nama' => $request->nama,
            'telp' => $request->telp, 'sim' => $request->sim, 'vendor' => $request->vendor, 'status' => $request->status
        ]);
        return $this->json(['message' => 'Driver diperbarui.'], 200);
    }

    public function destroy($id) {
        $d = Driver::find((int)$id);
        if (!$d) return $this->json(['message' => 'Driver tidak ditemukan.'], 404);
        $d->delete();
        return $this->json(['message' => 'Driver dihapus.'], 200);
    }
}
