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

    /* ============================================================
   DELIVERY BOARD DATA (PUBLIK) — GET /delivery/board-data
   ============================================================ */
    public function deliveryBoard(Request $request)
    {
        $date = $request->date ?? date('Y-m-d');

        /* Semua customer aktif */
        $customers = \App\Models\Customer::query()
            ->where('is_active', '=', 1)
            ->orderBy('code_customer', 'ASC')
            ->get();

        $result = [];

        foreach ($customers as $c) {
            $cycles = \App\Models\CustomerCycle::query()
                ->where('customer_id', '=', $c->id)
                ->whereNull('deleted_at')
                ->orderBy('cycle_number', 'ASC')
                ->get();

            if (!count($cycles)) continue;

            $cycleData = [];
            foreach ($cycles as $cc) {
                $delivery = \App\Models\ActualDelivery::query()
                    ->where('delivery_date', '=', $date)
                    ->where('customer_id', '=', $c->id)
                    ->where('customer_cycle_id', '=', $cc->id)
                    ->first();

                $items = [];
                if ($delivery) {
                    $dItems = \App\Models\ActualDeliveryItem::query()
                        ->where('delivery_id', '=', $delivery->id)
                        ->get();

                    foreach ($dItems as $it) {
                        $master = \App\Models\Item::find($it->item_id);
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

        /* Semua item ICS aktif */
        $allItems = \App\Models\Item::query()
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

    public function systemMenus()
    {
        $rows = \App\Models\SystemMenu::query()
            ->where('is_active', '=', 1)
            ->orderBy('sort_order', 'ASC')
            ->get();

        return $this->json([
            'data' => array_map(fn($r) => [
                'id'    => (int)$r->id,
                'title' => $r->title,
                'desc'  => $r->description,
                'icon'  => $r->icon,
                'color' => $r->color,
                'url'   => $r->url,
            ], $rows)
        ], 200);
    }
}
