<?php

namespace App\Controllers;

use App\Models\Vehicle;
use Bpjs\Framework\Helpers\BaseController;
use Bpjs\Framework\Core\Request;
use Bpjs\Framework\Helpers\View;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

    /* ============================================================
       IMPORT EXCEL — POST /admin/vehicles/import
       ============================================================ */
    public function import(Request $request)
    {
        if (!$request->hasFile('file')) {
            return $this->json(['errors' => ['file' => 'File wajib diupload.']], 422);
        }

        $file = $request->file('file');
        $ext  = strtolower($file['extension'] ?? '');

        if (!in_array($ext, ['xlsx', 'xls', 'csv'], true)) {
            return $this->json([
                'errors' => ['file' => 'Format harus .xlsx, .xls, atau .csv']
            ], 422);
        }

        if ((int) $file['size'] > 10 * 1024 * 1024) {
            return $this->json(['errors' => ['file' => 'Ukuran maksimal 10 MB.']], 422);
        }

        try {
            $spreadsheet = IOFactory::load($file['tmp_name']);
        } catch (\Exception $e) {
            return $this->json([
                'errors' => ['file' => 'Gagal membaca file: ' . $e->getMessage()]
            ], 422);
        }

        $sheet = $spreadsheet->getActiveSheet();
        $rows  = $sheet->toArray(null, true, false, false);

        if (empty($rows)) {
            return $this->json(['errors' => ['file' => 'File kosong.']], 422);
        }

        $header = array_map(
            fn($h) => strtolower(trim((string) $h)),
            $rows[0] ?? []
        );

        $col = [
            'plate_number' => array_search('plate_number', $header),
            'tonase'       => array_search('tonase',       $header),
            'jenis'        => array_search('jenis',        $header),
            'vendor'       => array_search('vendor',       $header),
        ];

        if ($col['plate_number'] === false) {
            return $this->json([
                'errors' => ['file' => 'Header wajib memuat kolom: plate_number. Opsional: tonase, jenis, vendor.']
            ], 422);
        }

        $inserted = 0;
        $updated  = 0;
        $skipped  = 0;
        $errors   = [];

        for ($i = 1; $i < count($rows); $i++) {
            $rowNum = $i + 1;
            $row    = $rows[$i];

            $plate  = strtoupper(trim((string) ($row[$col['plate_number']] ?? '')));
            $tonase = $col['tonase'] !== false ? trim((string) ($row[$col['tonase']] ?? '')) : null;
            $jenis  = $col['jenis']  !== false ? trim((string) ($row[$col['jenis']]  ?? '')) : null;
            $vendor = $col['vendor'] !== false ? trim((string) ($row[$col['vendor']] ?? '')) : null;

            if ($plate === '' && $tonase === '' && $jenis === '' && $vendor === '') continue;

            if ($plate === '') {
                $errors[] = "Baris {$rowNum}: plate_number kosong.";
                $skipped++;
                continue;
            }

            $existing = Vehicle::query()->where('plate_number', '=', $plate)->first();

            try {
                if ($existing) {
                    $existing->update([
                        'tonase' => $tonase ?: $existing->tonase,
                        'jenis'  => $jenis  ?: $existing->jenis,
                        'vendor' => $vendor ?: $existing->vendor,
                    ]);
                    $updated++;
                } else {
                    Vehicle::create([
                        'plate_number' => $plate,
                        'tonase'       => $tonase ?: null,
                        'jenis'        => $jenis  ?: null,
                        'vendor'       => $vendor ?: null,
                        'is_active'    => 1,
                    ]);
                    $inserted++;
                }
            } catch (\Exception $e) {
                $errors[] = "Baris {$rowNum}: " . $e->getMessage();
                $skipped++;
            }
        }

        return $this->json([
            'message' => "Import selesai: {$inserted} baru, {$updated} diperbarui, {$skipped} dilewati.",
            'summary' => [
                'inserted' => $inserted,
                'updated'  => $updated,
                'skipped'  => $skipped,
                'errors'   => $errors,
            ],
        ], 200);
    }

    /* ============================================================
       DOWNLOAD TEMPLATE — GET /admin/vehicles/import-template
       ============================================================ */
    public function importTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Kendaraan');

        $headers = ['plate_number', 'tonase', 'jenis', 'vendor'];
        $sheet->fromArray($headers, null, 'A1');

        $samples = [
            ['B 9453 PXX', '16 Ton', 'Rental', 'PT Trans Jaya'],
            ['B 9123 XYZ', '10 Ton', 'Truk',   'PT Trans Jaya'],
            ['B 9123 KYT', '10 Ton', 'Rental', 'PT Logistik Nusantara'],
        ];
        $sheet->fromArray($samples, null, 'A2');

        $sheet->getStyle('A1:D1')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A1:D1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('2563EB');
        $sheet->getStyle('A1:D1')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $lastRow = count($samples) + 1;
        $sheet->getStyle("A1:D{$lastRow}")->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->getColor()->setRGB('CBD5E1');

        foreach (range('A', 'D') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }
        $sheet->freezePane('A2');

        $filename = 'template_kendaraan.xlsx';
        $this->streamXlsx($spreadsheet, $filename);
    }

    private function streamXlsx(Spreadsheet $spreadsheet, string $filename)
    {
        while (ob_get_level()) ob_end_clean();

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Pragma: public');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
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
