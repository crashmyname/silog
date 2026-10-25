<?php

namespace App\Controllers;

use App\Models\ActualDelivery;
use App\Models\ActualDeliveryItem;
use App\Models\Customer;
use App\Models\CustomerCycle;
use App\Models\Item;
use Bpjs\Framework\Helpers\BaseController;
use Bpjs\Framework\Core\Request;
use Bpjs\Framework\Helpers\View;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ActualDeliveryController extends BaseController
{
    // Controller logic here
    /* ============================================================
       HALAMAN — GET /admin/actual-delivery
       ============================================================ */
    public function index()
    {
        return $this->view('admin/actual-delivery/index');
    }

    /* ============================================================
       LIST — GET /admin/actual-delivery/list?date=&customer_id=
       ============================================================ */
    public function list(Request $request)
    {
        $date       = $request->date ?? date('Y-m-d');
        $customerId = $request->customer_id ? (int) $request->customer_id : null;

        $query = ActualDelivery::query()
            ->where('delivery_date', '=', $date);

        if ($customerId) $query->where('customer_id', '=', $customerId);

        $rows = $query->orderBy('customer_id', 'ASC')->get();

        $data = [];
        foreach ($rows as $d) {
            $c = Customer::find($d->customer_id);
            $cc = CustomerCycle::find($d->customer_cycle_id);

            $items = ActualDeliveryItem::query()
                ->where('delivery_id', '=', $d->id)
                ->get();

            $itemList = [];
            foreach ($items as $it) {
                $master = Item::find($it->item_id);
                $itemList[] = [
                    'id'              => (int) $it->id,
                    'item_id'         => (int) $it->item_id,
                    'icb_code'        => $master?->icb_code,
                    'icb_name'        => $master?->icb_name,
                    'type'            => $master?->type,
                    'total_delivery'  => (int) $it->total_delivery,
                    'plan_delivery'   => (int) $it->plan_delivery,
                    'actual_delivery' => (int) $it->actual_delivery,
                    'notes'           => $it->notes,
                ];
            }

            $data[] = [
                'id'               => (int) $d->id,
                'delivery_date'    => $d->delivery_date,
                'customer_id'      => (int) $d->customer_id,
                'customer_code'    => $c?->code,
                'customer_name'    => $c?->name,
                'customer_cycle_id'=> (int) $d->customer_cycle_id,
                'cycle_number'     => $cc ? (int) $cc->cycle_number : null,
                'vehicle_id'       => $d->vehicle_id ? (int) $d->vehicle_id : null,
                'vehicle_note'     => $d->vehicle_note,
                'etd_plan'         => $d->etd_plan,
                'eta_plan'         => $d->eta_plan,
                'actual_departure' => $d->actual_departure,
                'actual_arrival'   => $d->actual_arrival,
                'status'           => $d->status,
                'notes'            => $d->notes,
                'items'            => $itemList,
            ];
        }

        return $this->json([
            'date' => $date,
            'data' => $data,
            'total' => count($data),
        ],200);
    }

    /* ============================================================
       STORE — POST /admin/actual-delivery
       Body: { delivery_date, customer_id, items: [...], cycles: [...] }
       Structure cycles[] : { cycle_id, vehicle_note, actual_departure, actual_arrival, status, notes, items: [...] }
       ============================================================ */
    public function store(Request $request)
    {
        if (!$request->validate([
            'delivery_date' => 'required',
            'customer_id'   => 'required|integer',
        ])) return $this->json(['errors' => $request->errors()], 422);

        $customer = Customer::find((int) $request->customer_id);
        if (!$customer) return $this->json(['errors' => ['customer_id' => 'Customer tidak valid.']], 422);

        $cycles = $request->cycles;
        $cycles = $cycles ? json_decode(json_encode($cycles), true) : [];
        if (!is_array($cycles) || empty($cycles)) {
            return $this->json(['errors' => ['cycles' => 'Minimal satu cycle harus diisi.']], 422);
        }

        $savedIds = [];

        foreach ($cycles as $row) {
            $cycleId = (int) ($row['cycle_id'] ?? 0);
            $cc = CustomerCycle::find($cycleId);
            if (!$cc || (int) $cc->customer_id !== (int) $customer->id) continue;

            /* Cari existing atau buat baru */
            $existing = ActualDelivery::query()
                ->where('delivery_date',    '=', $request->delivery_date)
                ->where('customer_id',      '=', $customer->id)
                ->where('customer_cycle_id','=', $cc->id)
                ->first();

            $payload = [
                'delivery_date'    => $request->delivery_date,
                'customer_id'      => (int) $customer->id,
                'customer_cycle_id'=> (int) $cc->id,
                'vehicle_id'       => !empty($row['vehicle_id']) ? (int) $row['vehicle_id'] : null,
                'vehicle_note'     => trim($row['vehicle_note'] ?? '') ?: null,
                'etd_plan'         => $this->normalizeTime($row['etd_plan'] ?? $cc->etd_from_ise),
                'eta_plan'         => $this->normalizeTime($row['eta_plan'] ?? $cc->eta_at_customer),
                'actual_departure' => $this->normalizeTime($row['actual_departure'] ?? null),
                'actual_arrival'   => $this->normalizeTime($row['actual_arrival']   ?? null),
                'status'           => $this->normalizeStatus($row['status'] ?? 'waiting'),
                'notes'            => trim($row['notes'] ?? '') ?: null,
                'created_by'       => $_SESSION['user_id'] ?? null,
            ];

            if ($existing) {
                $existing->update($payload);
                $deliveryId = (int) $existing->id;
            } else {
                $created = ActualDelivery::create($payload);
                $deliveryId = (int) $created->id;
            }

            /* Sync items */
            $this->syncItems($deliveryId, $row['items'] ?? []);

            $savedIds[] = $deliveryId;
        }

        return $this->json([
            'message'  => 'Actual delivery tersimpan.',
            'saved_ids'=> $savedIds,
        ], 201);
    }

    /* ============================================================
       UPDATE header — PUT /admin/{id}/actual-delivery
       ============================================================ */
    public function update(Request $request, $id)
    {
        $d = ActualDelivery::find((int) $id);
        if (!$d) return $this->json(['message' => 'Data tidak ditemukan.'], 404);

        $d->update([
            'vehicle_id'       => !empty($request->vehicle_id) ? (int) $request->vehicle_id : null,
            'vehicle_note'     => trim($request->vehicle_note ?? '') ?: null,
            'etd_plan'         => $this->normalizeTime($request->etd_plan),
            'eta_plan'         => $this->normalizeTime($request->eta_plan),
            'actual_departure' => $this->normalizeTime($request->actual_departure),
            'actual_arrival'   => $this->normalizeTime($request->actual_arrival),
            'status'           => $this->normalizeStatus($request->status ?? 'waiting'),
            'notes'            => trim($request->notes ?? '') ?: null,
        ]);

        if ($request->items !== null) {
            $this->syncItems((int) $d->id, $request->items);
        }

        return $this->json(['message' => 'Diperbarui.'], 200);
    }

    /* ============================================================
       DELETE
       ============================================================ */
    public function destroy($id)
    {
        $d = ActualDelivery::find((int) $id);
        if (!$d) return $this->json(['message' => 'Data tidak ditemukan.'], 404);
        $d->delete();
        return $this->json(['message' => 'Data dihapus.'], 200);
    }

    /* ============================================================
   BOARD — GET /admin/actual-delivery/board
   ============================================================ */
    public function board()
    {
        return $this->view('admin/actual-delivery/board');
    }

    /* ============================================================
    BOARD DATA — GET /admin/actual-delivery/board-data?date=Y-m-d
    ============================================================ */
    public function boardData(Request $request)
    {
        $date = $request->date ?? date('Y-m-d');

        /* 1. Semua customer aktif */
        $customers = Customer::query()
            ->where('is_active', '=', 1)
            ->orderBy('code_customer', 'ASC')
            ->get();

        $result = [];

        foreach ($customers as $c) {
            $cycles = CustomerCycle::query()
                ->where('customer_id', '=', $c->id)
                ->whereNull('deleted_at')
                ->orderBy('cycle_number', 'ASC')
                ->get();

            if (!count($cycles)) continue;

            $cycleData = [];

            foreach ($cycles as $cc) {
                $delivery = ActualDelivery::query()
                    ->where('delivery_date', '=', $date)
                    ->where('customer_id', '=', $c->id)
                    ->where('customer_cycle_id', '=', $cc->id)
                    ->first();

                $items = [];
                if ($delivery) {
                    $dItems = ActualDeliveryItem::query()
                        ->where('delivery_id', '=', $delivery->id)
                        ->get();

                    foreach ($dItems as $it) {
                        $master = Item::find($it->item_id);
                        if (!$master) continue;
                        $items[] = [
                            'item_id'         => (int) $it->item_id,
                            'ics_code'        => $master->ics_code,
                            'total_delivery'  => (int) $it->total_delivery,
                            'plan_delivery'   => (int) $it->plan_delivery,
                            'actual_delivery' => (int) $it->actual_delivery,
                        ];
                    }
                }

                $cycleData[] = [
                    'id'               => (int) $cc->id,
                    'cycle_number'     => (int) $cc->cycle_number,
                    'etd_plan'         => $cc->etd_from_ise,
                    'eta_plan'         => $cc->eta_at_customer,
                    'vehicle_note'     => $delivery?->vehicle_note,
                    'actual_departure' => $delivery?->actual_departure,
                    'actual_arrival'   => $delivery?->actual_arrival,
                    'status'           => $delivery?->status ?? 'waiting',
                    'notes'            => $delivery?->notes,
                    'items'            => $items,
                ];
            }

            $result[] = [
                'id'      => (int) $c->id,
                'code_customer'    => $c->code_customer,
                'name'    => $c->name,
                'cycles'  => $cycleData,
            ];
        }

        /* 2. Semua item ICS aktif — untuk baris */
        $allItems = Item::query()
            ->where('is_active', '=', 1)
            ->orderBy('ics_code', 'ASC')
            ->get();

        $itemsList = [];
        foreach ($allItems as $i) {
            $hasPlan = false;
            foreach ($result as $cust) {
                foreach ($cust['cycles'] as $cyc) {
                    foreach ($cyc['items'] as $it) {
                        if ($it['item_id'] === (int) $i->id &&
                            ($it['plan_delivery'] > 0 ||
                            $it['actual_delivery'] > 0 ||
                            $it['total_delivery'] > 0)) {
                            $hasPlan = true;
                            break 3;
                        }
                    }
                }
            }
            if (!$hasPlan) continue;

            $itemsList[] = [
                'id'       => (int) $i->id,
                'ics_code' => $i->ics_code,
                'ics_name' => $i->ics_name,
                'type'     => $i->type,
            ];
        }

        return $this->json([
            'date'      => $date,
            'customers' => $result,
            'items'     => $itemsList,
        ],200);
    }

        /* ============================================================
       PREVIEW IMPORT — POST /admin/actual-delivery/preview-import
       ============================================================ */
    public function previewImport(Request $request)
    {
        $parsedData = $this->parseExcel($request);
        
        if (isset($parsedData['error'])) {
            return $this->json(['errors' => ['file' => $parsedData['error']]], 422);
        }

        return $this->json([
            'message' => 'Preview berhasil dimuat.',
            'rows'    => $parsedData
        ], 200);
    }

    /* ============================================================
       PROSES IMPORT — POST /admin/actual-delivery/import
       ============================================================ */
        public function import(Request $request)
    {
        $parsedData = $this->parseExcel($request);
        
        if (isset($parsedData['error'])) {
            return $this->json(['errors' => ['file' => $parsedData['error']]], 422);
        }

        $inserted = 0;
        $updated  = 0;
        $skipped  = 0;
        $errors   = [];
        $groupedData = [];

        foreach ($parsedData as $row) {
            if (!$row['is_valid']) {
                $skipped++;
                continue;
            }

            $key = $row['customer_id'] . '_' . $row['date'] . '_' . $row['cycle_id'];
            if (!isset($groupedData[$key])) {
                $groupedData[$key] = [
                    'customer_id' => $row['customer_id'],
                    'date'        => $row['date'],
                    'cycle_id'    => $row['cycle_id'],
                    'plates'      => [],
                    'etd_plan'    => $row['etd_plan'],
                    'eta_plan'    => $row['eta_plan'],
                    'items'       => []
                ];
            }
            
            if ($row['no_pol'] && !in_array($row['no_pol'], $groupedData[$key]['plates'])) {
                $groupedData[$key]['plates'][] = $row['no_pol'];
            }

            // Jumlahkan QTY (PERBAIKAN: gunakan raw_qty)
            $itemFound = false;
            foreach ($groupedData[$key]['items'] as &$existingItem) {
                if ($existingItem['item_id'] === $row['item_id']) {
                    $existingItem['qty'] += $row['raw_qty']; 
                    $itemFound = true;
                    break;
                }
            }
            unset($existingItem);

            if (!$itemFound) {
                $groupedData[$key]['items'][] = [
                    'item_id' => $row['item_id'],
                    'qty'     => $row['raw_qty'] 
                ];
            }
        }

        // ==== SIMPAN KE DATABASE ====
        foreach ($groupedData as $g) {
            $existing = ActualDelivery::query()
                ->where('delivery_date', '=', $g['date'])
                ->where('customer_id', '=', $g['customer_id'])
                ->where('customer_cycle_id', '=', $g['cycle_id'])
                ->first();

            $vehicleNote = count($g['plates']) > 0 ? implode(', ', $g['plates']) : null;

            $payload = [
                'delivery_date'     => $g['date'],
                'customer_id'       => $g['customer_id'],
                'customer_cycle_id' => $g['cycle_id'],
                'vehicle_note'      => $vehicleNote,
                'etd_plan'          => $g['etd_plan'],
                'eta_plan'          => $g['eta_plan'],
                'status'            => 'WAITING',
                'created_by'        => $_SESSION['user_id'] ?? null,
                'is_active'         => 1
            ];

            if ($existing) {
                $existing->update($payload);
                $deliveryId = $existing->id;
                $updated++;
            } else {
                $created = ActualDelivery::create($payload);
                $deliveryId = $created->id;
                $inserted++;
            }

            ActualDeliveryItem::deleteWhere(['delivery_id' => $deliveryId]);
            
            foreach ($g['items'] as $it) {
                ActualDeliveryItem::create([
                    'delivery_id'     => $deliveryId,
                    'item_id'         => $it['item_id'],
                    'total_delivery'  => $it['qty'],
                    'plan_delivery'   => $it['qty'],
                    'actual_delivery' => 0, 
                    'is_active'       => 1
                ]);
            }
        }

        return $this->json([
            'message' => "Import selesai: {$inserted} data baru, {$updated} diperbarui, {$skipped} baris invalid dilewati.",
            'summary' => [
                'inserted' => $inserted,
                'updated'  => $updated,
                'skipped'  => $skipped,
            ]
        ], 200);
    }

        /* ============================================================
       HELPER: PARSING & VALIDASI EXCEL
       ============================================================ */
    private function parseExcel(Request $request)
    {
        if (!$request->hasFile('file')) {
            return ['error' => 'File wajib diupload.'];
        }

        $file = $request->file('file');
        $ext  = strtolower($file['extension'] ?? '');

        if (!in_array($ext, ['xlsx', 'xls', 'csv'], true)) {
            return ['error' => 'Format harus Excel (.xlsx/.xls)'];
        }

        try {
            $spreadsheet = IOFactory::load($file['tmp_name']);
            $sheet = $spreadsheet->getActiveSheet();
            $highestRow = $sheet->getHighestRow();
        } catch (\Exception $e) {
            return ['error' => 'Gagal membaca file: ' . $e->getMessage()];
        }

        // Cari Header
        $headerRow = 1;
        $colIndex = [];
        for ($r = 1; $r <= 5; $r++) {
            $rowData = $sheet->rangeToArray("A{$r}:Z{$r}", null, true, false)[0];
            $found = false;
            foreach ($rowData as $idx => $val) {
                $valStr = strtoupper(trim((string)$val));
                if ($valStr === 'CUSTOMER')      { $colIndex['customer'] = $idx; $found = true; }
                if ($valStr === 'DELIVERY DATE') { $colIndex['date'] = $idx; $found = true; }
                if ($valStr === 'CYCLE')         { $colIndex['cycle_time'] = $idx; $found = true; }
                if ($valStr === 'ICS CODE')      { $colIndex['ics_code'] = $idx; $found = true; }
                if ($valStr === 'QTY')           { $colIndex['qty'] = $idx; $found = true; }
                if ($valStr === 'NO POLISI')     { $colIndex['no_pol'] = $idx; $found = true; }
                if ($valStr === 'RUTE')          { $colIndex['rute'] = $idx; $found = true; }
            }
            if ($found) { $headerRow = $r; break; }
        }

        if (!isset($colIndex['customer']) || !isset($colIndex['ics_code'])) {
            return ['error' => 'Header Excel tidak dikenali. Pastikan ada kolom CUSTOMER, RUTE, ICS CODE, QTY.'];
        }

        $rowsResult = [];
        
        // Ambil semua data Master Customer & Item dari DB
        $allCustomers = Customer::query()->get();
        $allItems = Item::query()->get();

        for ($r = $headerRow + 1; $r <= $highestRow; $r++) {
            $rowData = $sheet->rangeToArray("A{$r}:Z{$r}", null, true, false)[0];

            $custNameExcel = trim((string)($rowData[$colIndex['customer']] ?? ''));
            $dateRaw       = $rowData[$colIndex['date']] ?? null;
            $icsCode       = trim((string)($rowData[$colIndex['ics_code']] ?? ''));
            $qty           = (int)($rowData[$colIndex['qty']] ?? 0);
            $noPol         = isset($colIndex['no_pol']) ? trim((string)($rowData[$colIndex['no_pol']] ?? '')) : '';
            $ruteStr       = isset($colIndex['rute']) ? trim((string)($rowData[$colIndex['rute']] ?? '')) : '';
            $cycleTimeStr  = isset($colIndex['cycle_time']) ? trim((string)($rowData[$colIndex['cycle_time']] ?? '')) : '';

            if (empty($custNameExcel) && empty($icsCode) && empty($dateRaw)) continue;

            $rowItem = [
                'row_num'     => $r,
                'raw_cust'    => $custNameExcel,
                'raw_date'    => $dateRaw,
                'raw_ics'     => $icsCode,
                'raw_qty'     => $qty,
                'raw_rute'    => $ruteStr,
                'raw_time'    => $cycleTimeStr,
                'no_pol'      => $noPol,
                'is_valid'    => false,
                'error_msg'   => ''
            ];

            if (empty($ruteStr) || empty($dateRaw) || empty($icsCode)) {
                $rowItem['error_msg'] = 'Data RUTE, Tanggal, atau ICS Code kosong.';
                $rowsResult[] = $rowItem;
                continue;
            }

            // Format Tanggal
            if (is_numeric($dateRaw)) {
                $date = Date::excelToDateTimeObject($dateRaw)->format('Y-m-d');
            } else {
                $date = date('Y-m-d', strtotime($dateRaw));
            }
            $rowItem['date'] = $date;

            // Cari Customer & Cycle berdasarkan RUTE
            // Ambil angka paling belakang (misal "AHM DELTA PLAN 6" -> 6)
            preg_match('/(\d+)\s*$/', $ruteStr, $matches);
            $cycleNum = isset($matches[1]) ? (int)$matches[1] : 0;

            $foundCustomer = null;
            $foundCycle = null;

            foreach ($allCustomers as $c) {
                $isMatch = false;
                
                // Kadang di DB namanya kolomnya `code` bukan `code_customer`
                $codeCust = $c->code_customer ?? $c->code ?? '';
                $nameCust = $c->name ?? '';

                // 1. Cek apakah kode customer ada di dalam teks RUTE
                if (!empty($codeCust) && stripos($ruteStr, $codeCust) !== false) {
                    $isMatch = true;
                }
                // 2. Cek apakah nama customer ada di dalam teks RUTE
                else if (!empty($nameCust) && stripos($ruteStr, $nameCust) !== false) {
                    $isMatch = true;
                }
                // 3. Fallback: Cek apakah nama customer di DB cocok dengan kolom CUSTOMER di Excel
                else if (!empty($custNameExcel) && !empty($nameCust) && stripos($custNameExcel, $nameCust) !== false) {
                    $isMatch = true;
                }

                if ($isMatch) {
                    // KITA HAPUS ->whereNull('deleted_at') KARENA MODEL SUDAH MENANGANI SOFT DELETE
                    $cycle = CustomerCycle::query()
                        ->where('customer_id', '=', $c->id)
                        ->where('cycle_number', '=', $cycleNum)
                        ->first();

                    if ($cycle) {
                        $foundCustomer = $c;
                        $foundCycle = $cycle;
                        break; // Keluar dari loop jika sudah ketemu pasangan Customer + Cycle
                    }
                }
            }

            // Jika gagal nemu, berikan pesan error yang lebih jelas
            if (!$foundCustomer || !$foundCycle) {
                $rowItem['error_msg'] = "Gagal mencocokkan Rute '{$ruteStr}' (Cycle: {$cycleNum}). Cek apakah Customer '{$custNameExcel}' memiliki Cycle {$cycleNum} di database.";
                $rowsResult[] = $rowItem;
                continue;
            }

            $rowItem['customer_id'] = $foundCustomer->id;
            $rowItem['cycle_id'] = $foundCycle->id;

            // Cari Item
            $foundItem = null;
            foreach ($allItems as $i) {
                if (trim($i->ics_code) === $icsCode) {
                    $foundItem = $i;
                    break;
                }
            }
            
            if (!$foundItem) {
                $rowItem['error_msg'] = "ICS Code '{$icsCode}' tidak ada di Master Item.";
                $rowsResult[] = $rowItem;
                continue;
            }
            $rowItem['item_id'] = $foundItem->id;

            // Parse Jam ETD & ETA
            $rowItem['etd_plan'] = null;
            $rowItem['eta_plan'] = null;
            if (preg_match('/(\d{2}:\d{2})\s*-\s*(\d{2}:\d{2})/', $cycleTimeStr, $timeMatches)) {
                $rowItem['etd_plan'] = $timeMatches[1] . ':00';
                $rowItem['eta_plan'] = $timeMatches[2] . ':00';
            }

            // Jika sampai sini, valid!
            $rowItem['is_valid'] = true;
            $rowsResult[] = $rowItem;
        }

        return $rowsResult;
    }

    /* ============================================================
       Helper: sync items
       ============================================================ */
    private function syncItems(int $deliveryId, $items)
    {
        $rows = json_decode(json_encode($items), true);
        if (!is_array($rows)) return;

        /* Hapus semua lalu insert ulang — lebih simpel */
        ActualDeliveryItem::deleteWhere(['delivery_id' => $deliveryId]);

        foreach ($rows as $row) {
            $itemId = (int) ($row['item_id'] ?? 0);
            if (!$itemId) continue;
            if (!Item::find($itemId)) continue;

            ActualDeliveryItem::create([
                'delivery_id'     => $deliveryId,
                'item_id'         => $itemId,
                'total_delivery'  => (int) ($row['total_delivery']  ?? 0),
                'plan_delivery'   => (int) ($row['plan_delivery']   ?? 0),
                'actual_delivery' => (int) ($row['actual_delivery'] ?? 0),
                'notes'           => trim($row['notes'] ?? '') ?: null,
            ]);
        }
    }

    private function normalizeTime(?string $v): ?string
    {
        if (!$v) return null;
        $v = trim($v);
        if ($v === '') return null;
        if (preg_match('/^\d{2}:\d{2}$/', $v)) return $v . ':00';
        if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $v)) return $v;
        return null;
    }

    private function normalizeStatus(string $v): string
    {
        $allowed = ['waiting','on_the_way','arrived','delivered','delayed','cancelled'];
        return in_array($v, $allowed, true) ? $v : 'waiting';
    }
}
