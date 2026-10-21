<?php

namespace App\Controllers;

use App\Models\SystemMenu;
use Bpjs\Framework\Helpers\BaseController;
use Bpjs\Framework\Core\Request;
use Bpjs\Framework\Helpers\View;

class SystemMenuController extends BaseController
{
    // Controller logic here
    public function list(Request $request) {
        $rows = SystemMenu::query()->orderBy('sort_order', 'ASC')->get();
        return $this->json(['data' => array_map(fn($r) => [
            'id' => (int)$r->id, 'title' => $r->title, 'desc' => $r->description,
            'icon' => $r->icon, 'color' => $r->color, 'url' => $r->url
        ], $rows)], 200);
    }

    public function store(Request $request) {
        if (!$request->validate(['title' => 'required|max:80', 'description' => 'required'])) {
            return $this->json(['errors' => $request->errors()], 422);
        }
        $m = SystemMenu::create([
            'title' => $request->title, 'description' => $request->description,
            'icon' => $request->icon ?? 'fa-circle', 'color' => $request->color ?? '#3b82f6',
            'url' => $request->url ?? '#', 'sort_order' => $request->sort_order ?? 0, 'is_active' => 1
        ]);
        return $this->json(['message' => 'Menu ditambahkan.', 'data' => ['id' => (int)$m->id]], 201);
    }

    public function update(Request $request, $id) {
        $m = SystemMenu::find((int)$id);
        if (!$m) return $this->json(['message' => 'Menu tidak ditemukan.'], 404);
        $m->update([
            'title' => $request->title, 'description' => $request->description,
            'icon' => $request->icon ?? $m->icon, 'color' => $request->color ?? $m->color,
            'url' => $request->url ?? $m->url, 'sort_order' => $request->sort_order ?? $m->sort_order
        ]);
        return $this->json(['message' => 'Menu diperbarui.'], 200);
    }

    public function destroy($id) {
        $m = SystemMenu::find((int)$id);
        if (!$m) return $this->json(['message' => 'Menu tidak ditemukan.'], 404);
        $m->delete();
        return $this->json(['message' => 'Menu dihapus.'], 200);
    }
}
