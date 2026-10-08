<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Documents;
use Bpjs\Framework\Helpers\BaseController;
use Bpjs\Framework\Core\Request;
use Bpjs\Framework\Helpers\Char;
use Bpjs\Framework\Helpers\View;

class DocumentController extends BaseController
{
    // Controller logic here
    private const MAX_SIZE   = 10 * 1024 * 1024;
    private const STORAGE_DIR = 'documents'; 

    public function index()
    {
        return $this->view('admin/documents');
    }

    public function list(Request $request)
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
                    ->orWhere('original_name', 'LIKE', "%{$q}%");
            });
        }

        if ($catId) {
            $query->where('category_id', '=', $catId);
        }

        $result = $query->orderBy('id', 'DESC')->paginate(12);

        // Normalisasi field supaya JS mudah pakai
        $result['data'] = array_map(function ($d) {
            return [
                'id'             => $d['id'],
                'title'          => $d['title'],
                'description'    => $d['description'],
                'category_id'    => $d['category_id'],
                'category_name'  => $d['category']['name']  ?? null,
                'category_color' => $d['category']['color'] ?? '#64748b',
                'original_name'  => $d['original_name'],
                'file_size'      => (int) $d['file_size'],
                'file_size_human'=> $d['file_size_human'] ?? null,
                'mime_type'      => $d['mime_type'],
                'created_at'     => $d['created_at'],
            ];
        }, $result['data']);

        return $this->json($result,200);
    }

    public function store(Request $request)
    {
        /* ---------- 1. Validasi input teks ---------- */
        if (!$request->validate([
            'category_id' => 'required|integer',
            'title'       => 'required|max:120',
            'description' => 'max:1000',
        ])) {
            return $this->json(['errors' => $request->errors()], 422);
        }

        $categoryId  = (int) $request->category_id;
        $title       = $request->title;
        $description = $request->description ?? null;

        if (!Category::find($categoryId)) {
            return $this->json(['errors' => ['category_id' => 'Kategori tidak ditemukan.']], 422);
        }

        /* ---------- 2. File wajib ada ---------- */
        if (!$request->hasFile('file')) {
            return $this->json(['errors' => ['file' => 'File PDF wajib diupload.']], 422);
        }

        $fileInfo = $request->file('file'); // ['name','extension','mime_type','size','tmp_name',...]

        /* ---------- 3. Validasi file ---------- */
        if (strtolower($fileInfo['extension']) !== 'pdf') {
            return $this->json(['errors' => ['file' => 'File harus berformat PDF.']], 422);
        }

        if ((int) $fileInfo['size'] > self::MAX_SIZE) {
            return $this->json([
                'errors' => ['file' => 'Ukuran maksimal ' . (self::MAX_SIZE / 1048576) . ' MB.']
            ], 422);
        }

        // MIME asli dari server
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $fileInfo['tmp_name']);
        finfo_close($finfo);

        if ($mime !== 'application/pdf') {
            return $this->json(['errors' => ['file' => 'File bukan PDF yang valid.']], 422);
        }

        // Magic bytes: 5 byte pertama harus "%PDF-"
        $fp   = fopen($fileInfo['tmp_name'], 'rb');
        $head = fread($fp, 5);
        fclose($fp);

        if ($head !== '%PDF-') {
            return $this->json(['errors' => ['file' => 'File bukan PDF yang valid.']], 422);
        }

        /* ---------- 4. Simpan file ke storage ---------- */
        $uuid         = Char::uuid();
        $storedName   = $uuid . '.pdf';
        $subDir       = self::STORAGE_DIR . '/' . date('Y') . '/' . date('m');
        $targetDir    = storage_path($subDir);
        $relativePath = $subDir . '/' . $storedName;

        if (!store($fileInfo, $targetDir, $storedName)) {
            return $this->json(['errors' => ['file' => 'Gagal menyimpan file.']], 500);
        }

        $fullPath = storage_path($relativePath);
        $hash     = hash_file('sha256', $fullPath);

        /* ---------- 5. Cek duplikat by hash ---------- */
        $existing = Documents::query()->where('file_hash', '=', $hash)->first();

        if ($existing) {
            @unlink($fullPath);
            return $this->json([
                'errors' => ['file' => 'Dokumen ini sudah pernah diupload: "' . $existing->title . '".']
            ], 422);
        }

        /* ---------- 6. Insert ke database ---------- */
        $doc = Documents::create([
            'category_id'   => $categoryId,
            'title'         => $title,
            'description'   => $description,
            'original_name' => $fileInfo['name'],
            'stored_name'   => $storedName,
            'file_path'     => $relativePath,
            'file_size'     => (int) $fileInfo['size'],
            'mime_type'     => $mime,
            'file_ext'      => 'pdf',
            'file_hash'     => $hash,
            'status'        => 'active',
            'upload_by'   => auth()->user()->id,
        ]);

        return $this->json([
            'message'  => 'Dokumen berhasil diupload.',
            'data'     => $doc->toCleanArray(),
            'redirect' => base_url() . '/admin/documents/' . $doc->id,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $doc = Documents::find((int) $id);
        if (!$doc) {
            return $this->json(['message' => 'Dokumen tidak ditemukan.'], 404);
        }

        if (!$request->validate([
            'category_id' => 'required|integer',
            'title'       => 'required|max:120',
            'description' => 'max:1000',
        ])) {
            return $this->json(['errors' => $request->errors()], 422);
        }

        $categoryId = (int) $request->category_id;
        if (!Category::find($categoryId)) {
            return $this->json(['errors' => ['category_id' => 'Kategori tidak valid.']], 422);
        }

        $doc->update([
            'category_id' => $categoryId,
            'title'       => $request->title,
            'description' => $request->description ?? null,
        ]);

        return $this->json([
            'message'  => 'Dokumen diperbarui.',
            'data'     => $doc->toCleanArray(),
            'redirect' => base_url() . '/admin/documents/' . $doc->id,
        ], 200);
    }

    public function destroy($id)
    {
        $doc = Documents::find((int) $id);
        if (!$doc) {
            return $this->json(['message' => 'Dokumen tidak ditemukan.'], 404);
        }

        $full = storage_path($doc->file_path);
        if (is_file($full)) {
            @unlink($full);
        }

        $doc->delete();

        return $this->json(['message' => 'Dokumen dihapus.'], 200);
    }

    public function download($id)
    {
        $doc = Documents::find((int) $id);
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
}
