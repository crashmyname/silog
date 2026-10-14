<?php

namespace App\Controllers;

use App\Models\Item;
use Bpjs\Framework\Helpers\BaseController;
use Bpjs\Framework\Core\Request;
use Bpjs\Framework\Helpers\View;

class ItemController extends BaseController
{
    // Controller logic here
    public function index()   { return $this->view('admin/items/index'); }

    public function list(Request $request)
    {
        $q = trim((string) ($request->q ?? ''));
        $query = Item::query()->where('is_active', '=', 1);
        if ($q !== '') {
            $query->where(function ($s) use ($q) {
                $s->where('ics_code', 'LIKE', "%{$q}%")
                  ->orWhere('ics_name', 'LIKE', "%{$q}%");
            });
        }
        $rows = $query->orderBy('ics_code', 'ASC')->get();
        return $this->json([
            'data' => array_map(fn($r) => [
                'id'       => (int) $r->id,
                'ics_code' => $r->ics_code,
                'ics_name' => $r->ics_name,
                'type'     => $r->type,
                'unit'     => $r->unit,
            ], $rows),
        ],200);
    }

    public function store(Request $request)
    {
        if (!$request->validate([
            // 'ics_code' => 'required|max:30',
            'ics_name' => 'required|max:120',
        ])) return $this->json(['errors' => $request->errors()], 422);

        if (Item::query()->where('ics_code', '=', $request->ics_code)->first()) {
            return $this->json(['errors' => ['ics_code' => 'Kode ICC sudah ada.']], 422);
        }

        $i = Item::create([
            'ics_code' => strtoupper(trim($request->ics_code)),
            'ics_name' => trim($request->ics_name),
            'type'     => $request->type ?? null,
            'unit'     => $request->unit ?? 'PCS',
            'is_active'=> 1,
        ]);

        return $this->json(['message' => 'Item ditambahkan.', 'data' => ['id' => (int)$i->id]], 201);
    }

    public function update(Request $request, $id)
    {
        $i = Item::find((int) $id);
        if (!$i) return $this->json(['message' => 'Item tidak ditemukan.'], 404);

        $i->update([
            'ics_code' => strtoupper(trim($request->ics_code)),
            'ics_name' => trim($request->ics_name),
            'type'     => $request->type ?? null,
            'unit'     => $request->unit ?? 'PCS',
        ]);

        return $this->json(['message' => 'Item diperbarui.'], 200);
    }

    public function destroy($id)
    {
        $i = Item::find((int) $id);
        if (!$i) return $this->json(['message' => 'Item tidak ditemukan.'], 404);
        $i->delete();
        return $this->json(['message' => 'Item dihapus.'], 200);
    }
}
