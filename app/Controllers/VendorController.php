<?php

namespace App\Controllers;

use App\Models\Vendor;
use Bpjs\Framework\Helpers\BaseController;
use Bpjs\Framework\Core\Request;
use Bpjs\Framework\Helpers\View;

class VendorController extends BaseController
{
    // Controller logic here
    public function list(Request $request) {
        $rows = Vendor::query()->where('is_active', '=', 1)->orderBy('nama', 'ASC')->get();
        return $this->json(['data' => array_map(fn($r) => [
            'id' => (int)$r->id, 'kode' => $r->kode, 'nama' => $r->nama, 'kontak' => $r->kontak,
            'telp' => $r->telp, 'email' => $r->email, 'alamat' => $r->alamat, 'status' => $r->status
        ], $rows)], 200);
    }

    public function store(Request $request) {
        if (!$request->validate(['kode' => 'required|max:30', 'nama' => 'required|max:120']))
            return $this->json(['errors' => $request->errors()], 422);
        
        $v = Vendor::create([
            'kode' => strtoupper($request->kode), 'nama' => $request->nama,
            'kontak' => $request->kontak, 'telp' => $request->telp, 'email' => $request->email,
            'alamat' => $request->alamat, 'status' => $request->status ?? 'Aktif', 'is_active' => 1
        ]);
        return $this->json(['message' => 'Vendor ditambahkan.', 'data' => ['id' => (int)$v->id]], 201);
    }

    public function update(Request $request, $id) {
        $v = Vendor::find((int)$id);
        if (!$v) return $this->json(['message' => 'Vendor tidak ditemukan.'], 404);
        $v->update([
            'kode' => strtoupper($request->kode), 'nama' => $request->nama,
            'kontak' => $request->kontak, 'telp' => $request->telp, 'email' => $request->email,
            'alamat' => $request->alamat, 'status' => $request->status
        ]);
        return $this->json(['message' => 'Vendor diperbarui.'], 200);
    }

    public function destroy($id) {
        $v = Vendor::find((int)$id);
        if (!$v) return $this->json(['message' => 'Vendor tidak ditemukan.'], 404);
        $v->delete();
        return $this->json(['message' => 'Vendor dihapus.'], 200);
    }
}
