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
