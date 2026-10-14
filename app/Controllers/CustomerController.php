<?php

namespace App\Controllers;

use App\Models\Customer;
use App\Models\CustomerCycle;
use Bpjs\Framework\Helpers\BaseController;
use Bpjs\Framework\Core\Request;
use Bpjs\Framework\Helpers\View;

class CustomerController extends BaseController
{
    // Controller logic here
    private const MAX_CYCLE = 15;

    /* ============================================================
       HALAMAN UTAMA — GET /admin/customers
       ============================================================ */
    public function index()
    {
        return $this->view('admin/customers/index');
    }

    /* ============================================================
       LIST JSON — GET /admin/customers/list
       ============================================================ */
    public function list(Request $request)
    {
        $q = trim((string) ($request->q ?? ''));

        $query = Customer::query()->where('is_active', '=', 1);

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'LIKE', "%{$q}%")
                    ->orWhere('code_customer', 'LIKE', "%{$q}%");
            });
        }

        $rows = $query->orderBy('code_customer', 'ASC')->get();

        $data = [];
        foreach ($rows as $c) {
            $cycles = CustomerCycle::query()
                ->where('customer_id', '=', $c->id)
                ->orderBy('cycle_number', 'ASC')
                ->get();

            $data[] = [
                'id'            => (int) $c->id,
                'code_customer'          => $c->code_customer,
                'name'          => $c->name,
                'address'       => $c->address,
                'contact_person'=> $c->contact_person,
                'phone'         => $c->phone,
                'cycle_count'   => count($cycles),
                'cycles'        => array_map(fn($cc) => [
                    'id'              => (int) $cc->id,
                    'cycle_number'    => (int) $cc->cycle_number,
                    'etd_from_ise'    => $cc->etd_from_ise,
                    'eta_at_customer' => $cc->eta_at_customer,
                    'is_active'       => (bool) $cc->is_active,
                ], $cycles),
            ];
        }

        return $this->json(['data' => $data, 'total' => count($data)],200);
    }

    /* ============================================================
       DETAIL — GET /admin/customers/{id}
       ============================================================ */
    public function show($id)
    {
        $c = Customer::find((int) $id);
        if (!$c) return $this->json(['message' => 'Customer tidak ditemukan.'], 404);

        $cycles = CustomerCycle::query()
            ->where('customer_id', '=', $c->id)
            ->orderBy('cycle_number', 'ASC')
            ->get();

        return $this->json([
            'data' => [
                'id'             => (int) $c->id,
                'code_customer'           => $c->code_customer,
                'name'           => $c->name,
                'address'        => $c->address,
                'contact_person' => $c->contact_person,
                'phone'          => $c->phone,
                'cycles'         => array_map(fn($cc) => [
                    'id'              => (int) $cc->id,
                    'cycle_number'    => (int) $cc->cycle_number,
                    'etd_from_ise'    => $cc->etd_from_ise,
                    'eta_at_customer' => $cc->eta_at_customer,
                    'is_active'       => (bool) $cc->is_active,
                ], $cycles),
            ],
        ],200);
    }

    /* ============================================================
       STORE — POST /admin/customers
       ============================================================ */
    public function store(Request $request)
    {
        if (!$request->validate([
            'code_customer' => 'required|max:30',
            'name' => 'required|max:120',
        ])) {
            return $this->json(['errors' => $request->errors()], 422);
        }

        if (Customer::query()->where('code_customer', '=', $request->code_customer)->first()) {
            return $this->json(['errors' => ['code_customer' => 'Kode customer sudah dipakai.']], 422);
        }

        $customer = Customer::create([
            'code_customer'           => strtoupper(trim($request->code_customer)),
            'name'           => trim($request->name),
            'address'        => $request->address ?? null,
            'contact_person' => $request->contact_person ?? null,
            'phone'          => $request->phone ?? null,
            'is_active'      => 1,
        ]);

        /* Simpan cycle sekaligus (opsional) */
        $this->saveCycles($customer->id, $request->cycles ?? []);

        return $this->json([
            'message' => 'Customer ditambahkan.',
            'data'    => ['id' => (int) $customer->id],
        ], 201);
    }

    /* ============================================================
       UPDATE — PUT /admin/{id}/customers
       ============================================================ */
    public function update(Request $request, $id)
    {
        $c = Customer::find((int) $id);
        if (!$c) return $this->json(['message' => 'Customer tidak ditemukan.'], 404);

        if (!$request->validate([
            'code_customer' => 'required|max:30',
            'name' => 'required|max:120',
        ])) {
            return $this->json(['errors' => $request->errors()], 422);
        }

        $exists = Customer::query()
            ->where('code_customer', '=', $request->code_customer)
            ->where('id', '!=', $c->id)
            ->first();
        if ($exists) {
            return $this->json(['errors' => ['code_customer' => 'Kode customer sudah dipakai.']], 422);
        }

        $c->update([
            'code_customer'           => strtoupper(trim($request->code_customer)),
            'name'           => trim($request->name),
            'address'        => $request->address ?? null,
            'contact_person' => $request->contact_person ?? null,
            'phone'          => $request->phone ?? null,
        ]);

        /* Sync cycles */
        if ($request->cycles !== null) {
            $this->syncCycles($c->id, $request->cycles);
        }

        return $this->json(['message' => 'Customer diperbarui.'], 200);
    }

    /* ============================================================
       DELETE — DELETE /admin/{id}/customers
       ============================================================ */
    public function destroy($id)
    {
        $c = Customer::findOrFail((int) $id);
        if (!$c) return $this->json(['message' => 'Customer tidak ditemukan.'], 404);
        if($c){
            CustomerCycle::deleteWhere(['customer_id'=>$c->id]);
        }
        $c->delete();  // soft delete
        return $this->json(['message' => 'Customer dihapus.'], 200);
    }

    /* ============================================================
       Helper: simpan cycle baru
       ============================================================ */
    private function saveCycles(int $customerId, $cycles)
    {
        if (!is_array($cycles)) return;

        $data = json_decode(json_encode($cycles), true);
        if (!is_array($data)) return;

        foreach ($data as $row) {
            $n = (int) ($row['cycle_number'] ?? 0);
            if ($n < 1 || $n > self::MAX_CYCLE) continue;

            CustomerCycle::create([
                'customer_id'     => $customerId,
                'cycle_number'    => $n,
                'etd_from_ise'    => $this->normalizeTime($row['etd_from_ise']    ?? null),
                'eta_at_customer' => $this->normalizeTime($row['eta_at_customer'] ?? null),
                'is_active'       => 1,
            ]);
        }
    }

    /* ============================================================
       Helper: sync cycle (update/insert/delete)
       ============================================================ */
    private function syncCycles(int $customerId, $cycles)
    {
        /* Normalisasi */
        if (is_string($cycles)) {
            $cycles = json_decode($cycles, true) ?: [];
        }
        $rows = json_decode(json_encode($cycles), true);
        if (!is_array($rows)) $rows = [];

        /* ============================================================
        1. Kumpulkan ID yang dikirim dari frontend
        ============================================================ */
        $keptIds = [];
        foreach ($rows as $row) {
            if (!empty($row['id'])) $keptIds[] = (int) $row['id'];
        }

        /* ============================================================
        2. Hapus HANYA yang tidak dikirim
        ============================================================ */
        $existing = CustomerCycle::query()
            ->where('customer_id', '=', $customerId)
            ->get();

        foreach ($existing as $row) {
            if (!in_array((int) $row->id, $keptIds, true)) {
                $model = CustomerCycle::find((int) $row->id);
                if ($model) $model->delete();
            }
        }

        /* ============================================================
        3. Update atau insert
        ============================================================ */
        foreach ($rows as $row) {
            $n = (int) ($row['cycle_number'] ?? 0);
            if ($n < 1 || $n > self::MAX_CYCLE) continue;

            $payload = [
                'cycle_number'    => $n,
                'etd_from_ise'    => $this->normalizeTime($row['etd_from_ise']    ?? null),
                'eta_at_customer' => $this->normalizeTime($row['eta_at_customer'] ?? null),
                'is_active'       => 1,
            ];

            if (!empty($row['id'])) {
                $model = CustomerCycle::find((int) $row['id']);
                if ($model && (int) $model->customer_id === $customerId) {
                    $model->update($payload);
                }
            } else {
                $payload['customer_id'] = $customerId;
                CustomerCycle::create($payload);
            }
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
}
