<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class EntertainReport extends Model
{
    /* ===================== Helpers ===================== */

   
    protected static function normalizeFlatStrings(array $arr): array
    {
        $out = [];
        $seen = [];
        foreach ($arr as $v) {
            if (is_array($v)) {
                $v = Arr::get($v, 'SAIMember', '');
            }
            $v = trim((string) $v);
            if ($v === '') continue;
            $k = mb_strtolower($v);
            if (!isset($seen[$k])) {
                $seen[$k] = true;
                $out[] = $v;
            }
        }
        return $out; 
    }


    
   protected static function buildInternalRowsFirstOnly(array $saiNames, ?string $firstCc = null): array
{
    $rows = [];
    foreach ($saiNames as $i => $name) {
        $name = trim((string) $name);
        if ($name === '') continue;

        $rows[] = [
            'SAIMember'  => $name,
            'CostCenter' => $i === 0 ? (string)($firstCc ?? '') : '',
        ];
    }
    return $rows;
}


    /* ===================== Header list & util ===================== */

    public static function headers(string $search = null)
{
    $q = DB::table('EntertainReportHeader as h')
        ->select([
            'h.SysID', 'h.Date', 'h.Customer', 'h.Category', 'h.NumCA',
            'h.TotalAmount', 'h.Description',
        ])
        ->selectSub(function ($s) {
            $s->from('EntertainReportInternalMember as im')
              ->selectRaw("MAX(NULLIF(im.CostCenter,''))")
              ->whereColumn('im.HeaderID', 'h.SysID');
        }, 'CostCenter');

    if ($search !== null && $search !== '') {
        $q->where('h.Customer', 'like', "%{$search}%");
    }

    return $q->orderBy('h.Date', 'desc');
}

    public static function headerById(int $id)
{
    return DB::table('EntertainReportHeader as h')
        ->select([
            'h.SysID', 'h.Date', 'h.Customer', 'h.Category', 'h.NumCA',
            'h.TotalAmount', 'h.Description',
        ])
        ->selectSub(function ($s) {
            $s->from('EntertainReportInternalMember as im')
              ->selectRaw("MAX(NULLIF(im.CostCenter,''))")
              ->whereColumn('im.HeaderID', 'h.SysID');
        }, 'CostCenter')
        ->where('h.SysID', $id)
        ->first();
}

    /* ===================== Header create/update/delete ===================== */

    public static function createHeader(array $data): int
    {
        $customer = trim((string) ($data['Customer'] ?? ''));
        $category = trim((string) ($data['Category'] ?? ''));
        $numCA    = trim((string) ($data['NumCA'] ?? ''));

        if ($customer === '' || $numCA === '') {
            throw new \InvalidArgumentException('Customer dan Num CA wajib diisi.');
        }

        return DB::transaction(function () use ($data, $customer, $category, $numCA) {
            $headerId = DB::table('EntertainReportHeader')->insertGetId([
                'Date'        => $data['Date'],
                'Customer'    => $customer,
                'Category'    => $category,
                'NumCA'       => $numCA,
                'TotalAmount' => 0,
                'Description' => $data['Description'] ?? null,
            ]);

            // External
            $externals = self::normalizeFlatStrings((array) ($data['ExternalMembers'] ?? []));
            if (!empty($externals)) {
                $rows = array_map(fn($name) => ['HeaderID' => $headerId, 'CustomerMember' => $name], $externals);
                DB::table('EntertainReportExternalMember')->insert($rows);
            }

            // Internal 
            $internals = self::normalizeFlatStrings((array) ($data['InternalMembers'] ?? []));
if (!empty($internals)) {
    $firstCc = Arr::get($data, 'CostCenter.0'); // ambil CC baris 0 kalau dikirim dari form
    // fallback opsional jika firstCc kosong: ambil dari master MySQL
    if (!$firstCc && isset($internals[0])) {
        $firstCc = self::getCostCenterByName($internals[0]);
    }

    $rows = self::buildInternalRowsFirstOnly($internals, $firstCc);
    $rows = array_map(fn($r) => [
        'HeaderID'   => $headerId,
        'SAIMember'  => $r['SAIMember'],
        'CostCenter' => $r['CostCenter'],
    ], $rows);
    DB::table('EntertainReportInternalMember')->insert($rows);
}

            return $headerId;
        });
    }

    public static function updateHeader(int $id, array $data): bool
    {
        $customer = trim((string) ($data['Customer'] ?? ''));
        $numCA    = trim((string) ($data['NumCA'] ?? ''));
        $category = trim((string) ($data['Category'] ?? ''));

        if ($customer === '' || $numCA === '') {
            throw new \InvalidArgumentException('Customer dan Num CA wajib diisi.');
        }

        return (bool) DB::transaction(function () use ($id, $data, $customer, $category, $numCA) {
            DB::table('EntertainReportHeader')
                ->where('SysID', $id)
                ->update([
                    'Date'        => $data['Date'],
                    'Customer'    => $customer,
                    'Category'    => $category,
                    'NumCA'       => $numCA,
                    'Description' => $data['Description'] ?? null,
                ]);

            // External
            if (array_key_exists('ExternalMembers', $data)) {
                DB::table('EntertainReportExternalMember')->where('HeaderID', $id)->delete();
                $externals = self::normalizeFlatStrings((array) ($data['ExternalMembers'] ?? []));
                if (!empty($externals)) {
                    $rows = array_map(fn($name) => ['HeaderID' => $id, 'CustomerMember' => $name], $externals);
                    DB::table('EntertainReportExternalMember')->insert($rows);
                }
            }

            // Internal 
           if (array_key_exists('InternalMembers', $data)) {
    DB::table('EntertainReportInternalMember')->where('HeaderID', $id)->delete();

    $internals = self::normalizeFlatStrings((array) ($data['InternalMembers'] ?? []));
    if (!empty($internals)) {
        $firstCc = Arr::get($data, 'CostCenter.0');
        if (!$firstCc && isset($internals[0])) {
            $firstCc = self::getCostCenterByName($internals[0]);
        }

        $rows = self::buildInternalRowsFirstOnly($internals, $firstCc);
        $rows = array_map(fn($r) => [
            'HeaderID'   => $id,
            'SAIMember'  => $r['SAIMember'],
            'CostCenter' => $r['CostCenter'],
        ], $rows);
        DB::table('EntertainReportInternalMember')->insert($rows);
    }
}

            self::recalcAndGetTotal($id);
            return true;
        });
    }

    public static function deleteHeader(int $id): bool
    {
        return (bool) DB::table('EntertainReportHeader')->where('SysID', $id)->delete();
    }

    /* ===================== ITEMS ===================== */

    public static function items(int $headerId)
    {
        return DB::table('EntertainReportItemDtl')->where('HeaderID', $headerId);
    }

    public static function itemById(int $itemId)
    {
        return DB::table('EntertainReportItemDtl')->where('SysID', $itemId)->first();
    }

    public static function createItems(int $headerId, array $rows): int
    {
        $inserted = 0;
        DB::transaction(function () use ($headerId, $rows, &$inserted) {
            foreach ($rows as $r) {
                $item = trim((string) Arr::get($r, 'Item'));
                $shop = trim((string) Arr::get($r, 'RestaurantShop'));
                $amt  = (float) Arr::get($r, 'Amount', 0);
                if ($item === '' || $amt < 0) continue;

                DB::table('EntertainReportItemDtl')->insert([
                    'HeaderID'       => $headerId,
                    'Item'           => $item,
                    'RestaurantShop' => $shop ?: null,
                    'Amount'         => $amt,
                ]);
                $inserted++;
            }
            self::recalcAndGetTotal($headerId);
        });
        return $inserted;
    }

    public static function updateItem(int $itemId, array $itemData): bool
    {
        return (bool) DB::transaction(function () use ($itemId, $itemData) {
            $item = DB::table('EntertainReportItemDtl')->where('SysID', $itemId)->first();
            if (!$item) return false;

            DB::table('EntertainReportItemDtl')
                ->where('SysID', $itemId)
                ->update([
                    'Item'           => trim((string) Arr::get($itemData, 'Item')),
                    'RestaurantShop' => trim((string) Arr::get($itemData, 'RestaurantShop')),
                    'Amount'         => (float) Arr::get($itemData, 'Amount', 0),
                ]);

            self::recalcAndGetTotal((int) $item->HeaderID);
            return true;
        });
    }

    public static function deleteItem(int $itemId): bool
    {
        $headerId = DB::table('EntertainReportItemDtl')->where('SysID', $itemId)->value('HeaderID');
        $deleted  = (bool) DB::table('EntertainReportItemDtl')->where('SysID', $itemId)->delete();
        if ($deleted && $headerId) {
            self::recalcAndGetTotal((int) $headerId);
        }
        return $deleted;
    }

    public static function itemDetails(int $headerId)
    {
        return DB::table('EntertainReportItemDtl AS d')
            ->select('d.SysID', 'd.HeaderID', 'd.Item', 'd.RestaurantShop', 'd.Amount')
            ->where('d.HeaderID', $headerId);
    }

    /* ===================== MEMBERS ===================== */

   
   public static function createMembers(int $headerId, array $custMembers, array $saiMembers, ?string $firstCc = null): int
{
    $inserted = 0;

    DB::transaction(function () use ($headerId, $custMembers, $saiMembers, $firstCc, &$inserted) {
        // === External (Customer Members)
        $ext = self::normalizeFlatStrings($custMembers);
        if ($ext) {
            $rows = array_map(fn($c) => [
                'HeaderID'       => $headerId,
                'CustomerMember' => $c,
            ], $ext);

            DB::table('EntertainReportExternalMember')
              ->upsert($rows, ['HeaderID','CustomerMember'], []); // idempotent
            $inserted += count($rows);
        }

        // === Internal (SAI) — hanya baris pertama yang punya CC
        $ints = self::normalizeFlatStrings($saiMembers);
        if ($ints) {
            $rows = self::buildInternalRowsFirstOnly($ints, $firstCc);
            $rows = array_map(fn($r) => [
                'HeaderID'   => $headerId,
                'SAIMember'  => $r['SAIMember'],
                'CostCenter' => $r['CostCenter'],
            ], $rows);

            DB::table('EntertainReportInternalMember')
              ->upsert($rows, ['HeaderID','SAIMember'], ['CostCenter']);
            $inserted += count($rows);
        }
    });

    return $inserted;
}


    
    public static function headerMembers(int $headerId)
    {
        $ext = DB::table('EntertainReportExternalMember')
            ->where('HeaderID', $headerId)
            ->pluck('CustomerMember')
            ->values();

        $ints = DB::table('EntertainReportInternalMember')
            ->where('HeaderID', $headerId)
            ->orderBy('SysID')
            ->get(['SAIMember', 'CostCenter']);

        return [
            'CustomerMember' => $ext,
            'SAIMember'      => $ints->pluck('SAIMember')->values(),
            'CostCenter'     => $ints->pluck('CostCenter')->values(), 
        ];
    }

    public static function externalMembersByHeader(int $headerId)
    {
        return DB::table('EntertainReportExternalMember')
            ->where('HeaderID', $headerId)
            ->orderBy('SysID');
    }

    public static function internalMembersByHeader(int $headerId)
    {
        return DB::table('EntertainReportInternalMember')
            ->select('SysID', 'HeaderID', 'SAIMember', 'CostCenter') 
            ->where('HeaderID', $headerId)
            ->orderBy('SysID');
    }

    /* ===================== TOTAL ===================== */

    public static function sumItemsTotal(int $headerId): float
    {
        return (float) DB::table('EntertainReportItemDtl')->where('HeaderID', $headerId)->sum('Amount');
    }

    public static function updateHeaderTotal(int $headerId, float $total = null): float
    {
        if ($total === null) {
            $total = self::sumItemsTotal($headerId);
        }
        DB::table('EntertainReportHeader')->where('SysID', $headerId)->update(['TotalAmount' => $total]);
        return $total;
    }

    public static function recalcAndGetTotal(int $headerId): float
    {
        return self::updateHeaderTotal($headerId, null);
    }

    /* ===================== Lain-lain ===================== */

    public static function getCustomer()
    {
        return DB::connection('sqlsrv4')->table('Erp.Customer')->get();
    }

    public static function getHeaderCategories()
{
    
    return collect([
        'Regular',
        'Golf',
        'Membership',
        'Bench Mark',
        'Consumable',
        
    ]);
}
// === Sumber data SAI (MySQL) ===
protected static function mysqlEmployeeQuery()
{
    // tabel 'employees' dengan kolom 'employee_name' & 'cost_center'
    return DB::connection('mysql')->table('tb_employees');
}

/**
 * Ambil list nama internal (SAI) dengan pagination sederhana (untuk Select2 ajax).
 * Return: ['data' => Collection<string>, 'hasMore' => bool]
 */
public static function internalMemberNames(?string $q, int $page = 1, int $perPage = 20): array
{
    $qb = self::mysqlEmployeeQuery()
        ->selectRaw('DISTINCT TRIM(employee_name) AS name')
        ->whereRaw("TRIM(employee_name) <> ''");

    if ($q !== null && $q !== '') {
        // hindari wildcard injection
        $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $q) . '%';
        $qb->where('employee_name', 'like', $like);
    }

    $offset = max(0, ($page - 1) * $perPage);
    $rows   = $qb->orderBy('name')->offset($offset)->limit($perPage + 1)->get();

    $hasMore = $rows->count() > $perPage;
    $data    = $rows->take($perPage)->pluck('name')->values();

    return ['data' => $data, 'hasMore' => $hasMore];
}

/** Ambil Cost Center berdasarkan nama internal (exact, trim). */
public static function getCostCenterByName(?string $name): ?string
{
    $name = trim((string) $name);
    if ($name === '') return null;

    return self::mysqlEmployeeQuery()
        ->whereRaw('TRIM(employee_name) = ?', [$name])
        ->value('cc_code');
}

}
