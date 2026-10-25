<?php

namespace App\Controllers;

use App\Models\Item;
use Bpjs\Framework\Helpers\BaseController;
use Bpjs\Framework\Core\Request;
use Bpjs\Framework\Helpers\View;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
                'part_number' => $r->part_number,
                'type'     => $r->type,
                'unit'     => $r->unit,
            ], $rows),
        ],200);
    }

    public function store(Request $request)
    {
        // SESUDAH
        if (!$request->validate([
            // 'ics_code'    => 'required|max:30',
            'part_number' => 'required|max:60',
            'ics_name'    => 'required|max:120',
        ])) return $this->json(['errors' => $request->errors()], 422);

        if (Item::query()->where('ics_code', '=', $request->ics_code)->first()) {
            return $this->json(['errors' => ['ics_code' => 'Kode ICC sudah ada.']], 422);
        }

        $i = Item::create([
            'ics_code' => strtoupper(trim($request->ics_code)),
            'ics_name' => trim($request->ics_name),
            'part_number' => $request->part_number,
            'type'     => $request->type ?? null,
            'unit'     => $request->unit ?? 'PCS',
            'is_active'=> 1,
        ]);

        return $this->json(['message' => 'Item ditambahkan.', 'data' => ['id' => (int)$i->id]], 201);
    }

     /* ============================================================
       IMPORT EXCEL — POST /admin/items/import
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

        /* Baca Excel */
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

        /* Parse header */
        $header = array_map(
            fn($h) => strtolower(trim((string) $h)),
            $rows[0] ?? []
        );

        $col = [
            'ics_code' => array_search('ics_code', $header),
            'ics_name' => array_search('ics_name', $header),
            'part_number' => array_search('part_number', $header),
            'type'     => array_search('type',     $header),
            'unit'     => array_search('unit',     $header),
        ];

        if ($col['ics_code'] === false || $col['ics_name'] === false || $col['part_number'] === false) {
            return $this->json([
                'errors' => ['file' => 'Header wajib memuat kolom: ics_code, ics_name, part_number. Opsional: type, unit.']
            ], 422);
        }

        /* Proses baris data */
        $inserted = 0;
        $updated  = 0;
        $skipped  = 0;
        $errors   = [];

        for ($i = 1; $i < count($rows); $i++) {
            $rowNum = $i + 1;
            $row    = $rows[$i];

            $icsCode = trim((string) ($row[$col['ics_code']] ?? ''));
            $icsName = trim((string) ($row[$col['ics_name']] ?? ''));
            $partNumber = trim((string) ($row[$col['part_number']] ?? ''));
            $type    = $col['type'] !== false ? trim((string) ($row[$col['type']] ?? '')) : null;
            $unit    = $col['unit'] !== false ? trim((string) ($row[$col['unit']] ?? '')) : 'PCS';

            /* Skip baris kosong total */
            if ($icsCode === '' && $icsName === '' && $type === '') continue;

            if ($icsCode === '') {
                $errors[] = "Baris {$rowNum}: ics_code kosong.";
                $skipped++;
                continue;
            }
            if ($icsName === '') {
                $errors[] = "Baris {$rowNum}: ics_name kosong.";
                $skipped++;
                continue;
            }
            if ($partNumber === '') {
                $errors[] = "Baris {$rowNum}: part_number kosong.";
                $skipped++;
                continue;
            }

            $icsCode = strtoupper($icsCode);
            $existing = Item::query()->where('ics_code', '=', $icsCode)->first();

            try {
                if ($existing) {
                    $existing->update([
                        'ics_name' => $icsName,
                        'type'     => $type ?: $existing->type,
                        'unit'     => $unit ?: $existing->unit,
                    ]);
                    $updated++;
                } else {
                    Item::create([
                        'ics_code'  => $icsCode,
                        'ics_name'  => $icsName,
                        'part_number' => $partNumber,
                        'type'      => $type ?: null,
                        'unit'      => $unit ?: 'PCS',
                        'is_active' => 1,
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
       DOWNLOAD TEMPLATE — GET /admin/items/import-template
       ============================================================ */
    public function importTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Items');

        /* Header */
        $headers = ['ics_code', 'part_number', 'ics_name', 'type', 'unit'];
        $sheet->fromArray($headers, null, 'A1');

        $samples = [
            ['HL-3C2BA', 'PN-001', 'Holder Assy',   'Part', 'PCS'],
            ['HC-3C56',  'PN-002', 'Cover Bracket', 'Part', 'PCS'],
            ['TL-3C33A', 'PN-003', 'Tool Set',      'Part', 'PCS'],
        ];
        $sheet->fromArray($samples, null, 'A2');

        $sheet->getStyle('A1:E1')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A1:E1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('2563EB');
        $sheet->getStyle('A1:E1')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $lastRow = count($samples) + 1;
        $sheet->getStyle("A1:E{$lastRow}")->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->getColor()->setRGB('CBD5E1');

        foreach (range('A', 'E') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        /* Freeze header */
        $sheet->freezePane('A2');

        /* Stream */
        $filename = 'template_items.xlsx';
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
        $i = Item::find((int) $id);
        if (!$i) return $this->json(['message' => 'Item tidak ditemukan.'], 404);

        $i->update([
            'ics_code' => strtoupper(trim($request->ics_code)),
            'ics_name' => trim($request->ics_name),
            'part_number' => $request->part_number,
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
