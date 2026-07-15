<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MasterPackShipment extends Model
{
    use HasFactory;

    // ── Front-table query ────────────────────────────────────────────────────

    public static function get_transaction_list($search, $status_id)
    {
        $result = DB::table('MasterPackShipments AS m')
            ->leftJoin('TruckingNumber AS t', 'm.TruckingID', '=', 't.id')
            ->whereNull('m.DeletedAt');

        if (!empty($search)) {
            $result = $result->where(function ($query) use ($search) {
                $query->where('m.PackingListNum', 'LIKE', "%$search%")
                      ->orWhere('m.CustomerName',  'LIKE', "%$search%")
                      ->orWhere('m.CustID',        'LIKE', "%$search%") 
                      ->orWhere('m.ShipViaCode',   'LIKE', "%$search%")
                      ->orWhere('t.Nopol',         'LIKE', "%$search%")
                      ->orWhere('t.Driver',        'LIKE', "%$search%");
            });
        }
        if($status_id == 0) {
            $result = $result->whereNull('m.PackingListNum')->whereNull('m.ShippedAt');
        }
        if ($status_id == 1) {
            $result = $result->whereNull('m.PackingListNum');
        } elseif ($status_id == 2) {
            $result = $result->whereNotNull('m.PackingListNum')->whereNull('m.ShippedAt');
        } elseif ($status_id == 3) {
            $result = $result->whereNotNull('m.ShippedAt');
        }

        return $result->select('m.id', 'm.PackingListNum', 'm.CustID', 'm.CustomerName', 'm.ShipViaCode', 'm.ManualNopol', 'm.ManualDriver', 'm.CreatedAt', 'm.CreatedBy', 'm.ShippedAt', 'm.ShippedBy', 't.Nopol', 't.Driver');
    }

    // ── Header CRUD ──────────────────────────────────────────────────────────

    public static function get_header($id)
    {
        return DB::table('MasterPackShipments AS m')
            ->leftJoin('TruckingNumber AS t', 'm.TruckingID', '=', 't.id')
            -> leftjoin('Users AS u', 'm.CreatedBy', '=', 'u.username')
            ->where('m.id', $id)
            ->whereNull('m.DeletedAt')
            ->select('m.*', 't.Nopol', 't.Jenis', 't.Driver', 't.NoTlp', 't.DriverCadangan', 't.NoTlpCadangan', 'u.full_name AS CreatedByName')
            ->first();
    }

    public static function store_header($data)
    {
        return DB::table('MasterPackShipments')->insertGetId($data);
    }

    public static function update_header($id, $data)
    {
        return DB::table('MasterPackShipments')->where('id', $id)->update($data);
    }

    public static function soft_delete_header($id)
    {
        return DB::table('MasterPackShipments')
            ->where('id', $id)
            ->update([
                'DeletedAt' => now(),
                'UpdatedBy' => Auth::user()->username,
                'UpdatedAt' => now(),
            ]);
    }

    // ── Auto-generate Packing List number ────────────────────────────────────
    // Format: SAI-PCH-{YY}{MM}{NNNN}  e.g. SAI-PCH-26060001

    public static function generate_packing_list_num()
    {
        $prefix = 'SAI-PCH-' . date('ym'); // e.g. SAI-PCH-2606
        $last   = DB::table('MasterPackShipments')
            ->whereNotNull('PackingListNum')
            ->where('PackingListNum', 'like', $prefix . '%')
            ->orderByDesc('PackingListNum')
            ->value('PackingListNum');

        $next = $last ? (intval(substr($last, -4)) + 1) : 1;

        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    // ──Detail CRUD ──────────────────────────────────────────────────────────

    public static function get_details($master_id)
    {
        return DB::table('DtlPackShipment')
            ->where('MasterPackID', $master_id)
            ->orderBy('id', 'asc')
            ->get();
    }

    public static function get_detail_list($master_id, $search)
    {
        $result = DB::table('DtlPackShipment')
            ->where('MasterPackID', $master_id);

        if (!empty($search)) {
            $result = $result->where(function ($q) use ($search) {
                $q->where('PackNum',      'LIKE', "%$search%")
                  ->orWhere('LegalNumber', 'LIKE', "%$search%")
                  ->orWhere('PONum',       'LIKE', "%$search%");
            });
        }

        return $result->select('id', 'MasterPackID', 'PackNum', 'LegalNumber', 'PONum');
    }

    public static function store_detail($data)
    {
        return DB::table('DtlPackShipment')->insertGetId($data);
    }

    public static function delete_detail($detail_id)
    {
        return DB::table('DtlPackShipment')->where('id', $detail_id)->delete();
    }

    // ── Duplicate-check helpers ──────────────────────────────────────────────

    /** PackNum sudah ada di dokumen ini? */
    public static function check_pack_num_in_doc($pack_num, $master_id)
    {
        return DB::table('DtlPackShipment')
            ->where('PackNum', $pack_num)
            ->where('MasterPackID', $master_id)
            ->count();
    }

    /** PackNum sudah dipakai di dokumen lain? */
    public static function check_pack_num_globally($pack_num, $exclude_master_id = null)
    {
        $query = DB::table('DtlPackShipment')->where('PackNum', $pack_num);

        if ($exclude_master_id) {
            $query->where('MasterPackID', '!=', $exclude_master_id);
        }

        return $query->count();
    }

    // ── Summary counters ─────────────────────────────────────────────────────

    public static function count_total()
    {
        return DB::table('MasterPackShipments')->whereNull('DeletedAt')->count();
    }

    public static function count_draft()
    {
        return DB::table('MasterPackShipments')
            ->whereNull('DeletedAt')
            ->whereNull('PackingListNum')
            ->count();
    }

    public static function count_submitted()
    {
        return DB::table('MasterPackShipments')
            ->whereNull('DeletedAt')
            ->whereNotNull('PackingListNum')
            ->whereNull('ShippedAt')
            ->count();
    }

    public static function count_shipped()
    {
        return DB::table('MasterPackShipments')
            ->whereNull('DeletedAt')
            ->whereNotNull('ShippedAt')
            ->count();
    }
}
