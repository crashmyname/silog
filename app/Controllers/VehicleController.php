<?php

namespace App\Controllers;

use App\Models\Vehicle;
use Bpjs\Framework\Helpers\BaseController;
use Bpjs\Framework\Core\Request;
use Bpjs\Framework\Helpers\View;

class VehicleController extends BaseController
{
    // Controller logic here
    public function index()   { return $this->view('admin/vehicles/index'); }

    public function list(Request $request)
    {
        $rows = Vehicle::query()->where('is_active', '=', 1)
            ->orderBy('plate_number', 'ASC')->get();

        return $this->json([
            'data' => array_map(fn($r) => [
                'id'           => (int) $r->id,
                'plate_number' => $r->plate_number,
                'tonase'       => $r->tonase,
                'jenis'        => $r->jenis,
                'vendor'       => $r->vendor,
            ], $rows),
        ],200);
    }

    public function store(Request $request)
    {
        if (!$request->validate(['plate_number' => 'required|max:20']))
            return $this->json(['errors' => $request->errors()], 422);

        if (Vehicle::query()->where('plate_number', '=', $request->plate_number)->first())
            return $this->json(['errors' => ['plate_number' => 'No. polisi sudah ada.']], 422);

        $v = Vehicle::create([
            'plate_number' => strtoupper(trim($request->plate_number)),
            'tonase'       => $request->tonase ?? null,
            'jenis'        => $request->jenis  ?? null,
            'vendor'       => $request->vendor ?? null,
            'is_active'    => 1,
        ]);

        return $this->json(['message' => 'Kendaraan ditambahkan.', 'data' => ['id' => (int)$v->id]], 201);
    }

    public function update(Request $request, $id)
    {
        $v = Vehicle::find((int) $id);
        if (!$v) return $this->json(['message' => 'Kendaraan tidak ditemukan.'], 404);

        $v->update([
            'plate_number' => strtoupper(trim($request->plate_number)),
            'tonase'       => $request->tonase ?? null,
            'jenis'        => $request->jenis  ?? null,
            'vendor'       => $request->vendor ?? null,
        ]);
        return $this->json(['message' => 'Kendaraan diperbarui.'], 200);
    }

    public function destroy($id)
    {
        $v = Vehicle::find((int) $id);
        if (!$v) return $this->json(['message' => 'Kendaraan tidak ditemukan.'], 404);
        $v->delete();
        return $this->json(['message' => 'Kendaraan dihapus.'], 200);
    }
}
