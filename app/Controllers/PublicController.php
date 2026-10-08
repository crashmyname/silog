<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Documents;
use Bpjs\Framework\Helpers\BaseController;
use Bpjs\Framework\Core\Request;
use Bpjs\Framework\Helpers\View;

class PublicController extends BaseController
{
    // Controller logic here
    /* ============================================================
       KATEGORI — GET /api/categories
       ============================================================ */
    public function categories()
    {
        $list = Category::query()
            ->where('is_active', '=', 1)
            ->orderBy('name', 'ASC')
            ->get();

        $data = array_map(function ($c) {
            return [
                'id'    => (int) $c->id,
                'name'  => $c->name,
                'color' => $c->color,
                'icon'  => $c->icon,
            ];
        }, $list);

        return $this->json([
            'data'  => $data,
            'total' => count($data),
        ],200);
    }

    /* ============================================================
       DOKUMEN — GET /api/documents?q=&category_id=&page=
       ============================================================ */
    public function documents(Request $request)
    {
        $q     = trim((string) ($request->q ?? ''));
        $catId = $request->category_id ? (int) $request->category_id : null;
        $page  = max(1, (int) ($request->page ?? 1));

        $query = Documents::query()
            ->with('category')
            ->where('status', '=', 'active');

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'LIKE', "%{$q}%")
                    ->orWhere('original_name', 'LIKE', "%{$q}%")
                    ->orWhere('description', 'LIKE', "%{$q}%");
            });
        }

        if ($catId) {
            $query->where('category_id', '=', $catId);
        }

        $result = $query->orderBy('id', 'DESC')->paginate(12);

        /* Normalisasi output supaya JS mudah pakai */
        $result['data'] = array_map(function ($d) {
            return [
                'id'             => (int) $d['id'],
                'title'          => $d['title'],
                'description'    => $d['description'],
                'category_id'    => (int) $d['category_id'],
                'category_name'  => $d['category']['name']  ?? null,
                'category_color' => $d['category']['color'] ?? '#64748b',
                'original_name'  => $d['original_name'],
                'file_size'      => (int) $d['file_size'],
                'file_size_human'=> $d['file_size_human'] ?? null,
                'created_at'     => $d['created_at'],
                'download_url'   => base_url() . '/documents/' . $d['id'] . '/download',
            ];
        }, $result['data']);

        return $this->json($result,200);
    }

    /* ============================================================
       DOWNLOAD — GET /documents/{id}/download (publik)
       Redirect ke URL bertoken
       ============================================================ */
    public function download($id)
    {
        $doc = Documents::query()
            ->where('id', '=', (int) $id)
            ->where('status', '=', 'active')
            ->first();

        if (!$doc) {
            http_response_code(404);
            exit('Dokumen tidak ditemukan.');
        }

        $full = storage_path($doc->file_path);
        if (!is_file($full)) {
            http_response_code(404);
            exit('File tidak tersedia di server.');
        }

        $url = storage_secure($doc->file_path, 3600);
        header('Location: ' . $url);
        exit;
    }

    /* ============================================================
       STATISTIK — GET /api/stats (untuk hero home)
       ============================================================ */
    public function stats()
    {
        $totalDocs = Documents::query()
            ->where('status', '=', 'active')
            ->count();

        $totalCats = Category::query()
            ->where('is_active', '=', 1)
            ->count();

        return $this->json([
            'documents'  => (int) $totalDocs,
            'categories' => (int) $totalCats,
        ],200);
    }
}
