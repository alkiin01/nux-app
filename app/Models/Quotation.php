<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Quotation extends Model
{
    use HasFactory;
    public function master_price_list_show_data($search, $limit, $offset)
    {
        $query = DB::table('PriceListHeader');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('SupplierName', 'LIKE', "%{$search}%")
                    ->orWhere('SupplierNum', 'LIKE', "%{$search}%");
            });
        }
        return $query->offset($offset)->limit($limit)->orderByDesc('HeaderID')->get();
    }
    public function master_price_list_count_data($search)
    {
        $query = DB::table('PriceListHeader');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('SupplierName', 'LIKE', "%{$search}%")
                    ->orWhere('SupplierNum', 'LIKE', "%{$search}%");
            });
        }
        return $query->count();
    }
    public function master_price_list_store_data($data)
    {
        return DB::table('PriceListHeader')->insertGetId($data);
    }
    public function checkById($id)
    {
        return DB::table('quo_master_price')
            ->where('id', $id)
            ->first();
    }
    public function master_price_list_update($id, $data)
    {
        return DB::table('quo_master_price')
            ->where('id', $id)->update($data);
    }
    public function quotation_show_data($search, $filter, $effective, $expired, $limit, $offset)
    {
        $query = DB::connection('vendor-app-epicor')
            ->table('QuotationHeader')
            ->select('SupplierNum', 'SupplierName', 'Status', 'Customer', 'EffectiveDate', 'ExpiredDate', DB::raw('COUNT(*) AS count'))
            ->where('Status', '>=', 0)
            ->groupBy('SupplierNum', 'SupplierName', 'Status', 'Customer', 'EffectiveDate', 'ExpiredDate');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('Customer', 'LIKE', "%{$search}%")
                    ->orWhere('SupplierName', 'LIKE', "%{$search}%");
            });
        }
        if ($filter !== null && $filter !== '') {
            $query->where('Status', (int) $filter);
        }
        if (!empty($effective)) {
            $query->whereDate('EffectiveDate', '>=', $effective);
        }
        if (!empty($expired)) {
            $query->whereDate('ExpiredDate', '<=', $expired);
        }
        return $query->offset($offset)->limit($limit)->get();
    }
    public function quotation_count_data($search, $filter, $effective, $expired)
    {
        $query = DB::connection('vendor-app-epicor')
            ->table('QuotationHeader')
            ->select('SupplierNum', 'SupplierName', 'Status', 'Customer', 'EffectiveDate', 'ExpiredDate', DB::raw('SUM(CurrentTotalSalesPrice) AS Amount'))
            ->where('Status', '>=', 0)
            ->groupBy('SupplierNum', 'SupplierName', 'Status', 'Customer', 'EffectiveDate', 'ExpiredDate');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('Customer', 'LIKE', "%{$search}%")
                    ->orWhere('SupplierName', 'LIKE', "%{$search}%");
            });
        }
        if ($filter !== null && $filter !== '') {
            $query->where('Status', (int) $filter);
        }
        if (!empty($effective)) {
            $query->whereDate('EffectiveDate', '>=', $effective);
        }
        if (!empty($expired)) {
            $query->whereDate('ExpiredDate', '<=', $expired);
        }
        return $query->get()->count();
    }
    public function delete_quotation_data($id)
    {
        DB::table('PriceListHeader')
            ->where('HeaderID', $id)
            ->delete();
        DB::table('PriceListMtl')
            ->where('HeaderID', $id)
            ->delete();
        DB::table('PriceListPurchase')
            ->where('HeaderID', $id)
            ->delete();
        DB::table('PriceListProcess')
            ->where('HeaderID', $id)
            ->delete();
        DB::table('PriceListOtherCost')
            ->where('HeaderID', $id)
            ->delete();
        return true;
    }
    public function quoById($SupplierNum, $Status, $Customer, $Effective, $Expired)
    {
        return DB::connection('vendor-app-epicor')
            ->table('QuotationHeader')
            ->where('SupplierNum', $SupplierNum)
            ->where('Customer', $Customer)
            ->where('EffectiveDate', $Effective)
            ->where('ExpiredDate', $Expired)
            ->get();
    }
    public function proccess_show($id, $limit, $offset)
    {
        $query = DB::connection('vendor-app-epicor')
            ->table('QuotationProcess')
            ->where('QuotationID', $id);
        return $query->offset($offset)->limit($limit)->get();
    }
    public function proccess_count($id)
    {
        $query = DB::connection('vendor-app-epicor')
            ->table('QuotationProcess')
            ->where('QuotationID', $id);
        return $query->count();
    }
    public function getQuoById($SupplierNum, $Status, $Customer, $Effective, $Expired)
    {
        return DB::connection('vendor-app-epicor')
            ->table('QuotationHeader')
            ->where('SupplierNum', $SupplierNum)
            ->where('Status', $Status)
            ->where('Customer', $Customer)
            ->where('EffectiveDate', $Effective)
            ->where('ExpiredDate', $Expired)
            ->get();
    }
    public function updateQuo($SupplierNum, $Status, $Customer, $Effective, $Expired)
    {
        $data_role = DB::table('QuoRoleApproval')
            ->where('UserID', Auth::user()->id)
            ->first();
        if ($data_role && $data_role->Type >= 2) {
            $data_quo = DB::connection('vendor-app-epicor')
                ->table('QuotationHeader')
                ->where('SupplierNum', $SupplierNum)
                ->where('Status', $Status)
                ->where('Customer', $Customer)
                ->where('EffectiveDate', $Effective)
                ->where('ExpiredDate', $Expired)
                ->get();
            DB::connection('vendor-app-epicor')
                ->table('QuotationHeader')
                ->where('SupplierNum', $SupplierNum)
                ->where('Status', $Status)
                ->where('Customer', $Customer)
                ->where('EffectiveDate', $Effective)
                ->where('ExpiredDate', $Expired)
                ->update([
                    'Status' => $data_role->Type
                ]);
            if ($data_role->Type == 2) {
                $name_value = [
                    'Approval_2' => Auth::user()->full_name,
                    'Create_2' => now('Asia/Jakarta')
                ];
            } else {
                $name_value = [
                    'Approval_3' => Auth::user()->full_name,
                    'Create_3' => now('Asia/Jakarta')
                ];
            }
            foreach ($data_quo as $row) {
                DB::connection('vendor-app-epicor')
                    ->table('QuotationHistoryApproval')
                    ->where('QuotationHeaderID', $row->HeaderID)
                    ->update($name_value);
            }
            return true;
        } else {
            return false;
        }
    }
    public function cancelQuo($SupplierNum, $Status, $Customer, $Effective, $Expired)
    {
        $data_role = DB::table('QuoRoleApproval')
            ->where('UserID', Auth::user()->id)
            ->first();
        if ($data_role && $data_role->Type >= 2) {
            $data_quo = DB::connection('vendor-app-epicor')
                ->table('QuotationHeader')
                ->where('SupplierNum', $SupplierNum)
                ->where('Status', $Status)
                ->where('Customer', $Customer)
                ->where('EffectiveDate', $Effective)
                ->where('ExpiredDate', $Expired)
                ->get();
            DB::connection('vendor-app-epicor')
                ->table('QuotationHeader')
                ->where('SupplierNum', $SupplierNum)
                ->where('Status', $Status)
                ->where('Customer', $Customer)
                ->where('EffectiveDate', $Effective)
                ->where('ExpiredDate', $Expired)
                ->update([
                    'Status' => $data_role->Type - 1
                ]);
            if ($data_role->Type == 2) {
                $name_value = [
                    'Approval_2' => null,
                    'Create_2' => null
                ];
            } else {
                $name_value = [
                    'Approval_3' => null,
                    'Create_3' => null
                ];
            }
            foreach ($data_quo as $row) {
                DB::connection('vendor-app-epicor')
                    ->table('QuotationHistoryApproval')
                    ->where('QuotationHeaderID', $row->HeaderID)
                    ->update($name_value);
            }
            return true;
        } else {
            return false;
        }
    }
    public function get_supplier($search, $page = 1)
    {
        return DB::connection('sqlsrv4')
            ->table('Erp.Vendor')
            ->select('VendorNum', 'Name')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('VendorNum', 'LIKE', "%{$search}%")
                        ->orWhere('Name', 'LIKE', "%{$search}%");
                });
            })
            ->orderBy('VendorNum')
            ->paginate(50, ['*'], 'page', $page);
    }
    public function get_customer($search, $page = 1)
    {
        return DB::connection('sqlsrv4')
            ->table('Erp.Part')
            ->select('ProdCode')
            ->whereNotNull('ProdCode')
            ->where('ProdCode', '<>', '')
            ->when($search, function ($q) use ($search) {
                $q->where('ProdCode', 'LIKE', "%{$search}%");
            })
            ->groupBy('ProdCode')
            ->paginate(50, ['*'], 'page', $page);
    }
    public function get_period($search, $page = 1)
    {
        return DB::table('PriceListPeriod')
            ->when($search, function ($q) use ($search) {
                $q->where('EffectiveDate', 'LIKE', "%{$search}%")
                    ->orWhere('ExpiredDate', 'LIKE', "%{$search}%");
            })
            ->paginate(50, ['*'], 'page', $page);

    }
    public function check_period($effective, $expired)
    {
        return DB::table('PriceListPeriod')
            ->where('EffectiveDate', $effective)
            ->where('ExpiredDate', $expired)
            ->first();
    }
    public function add_period($effective, $expired)
    {
        return DB::table('PriceListPeriod')
            ->insertGetId([
                'EffectiveDate' => $effective,
                'ExpiredDate' => $expired
            ]);
    }

    public function get_part_mtl($search, $type)
    {
        if ($type == 'FG') {
            // $type = ;
            $query = DB::connection('sqlsrv4')
                ->table('Erp.Part as a')
                ->leftJoin('Erp.VendPart as b', function ($join) {
                    $join->on('a.PartNum', '=', 'b.PartNum')
                        ->whereRaw('b.EffectiveDate = (
                    SELECT MAX(EffectiveDate)
                    FROM Erp.VendPart
                    WHERE PartNum = a.PartNum
                )');
                })
                ->select(
                    'a.PartNum as PartNum',
                    'a.PartDescription as Description',
                    'b.PUM as UOM',
                    'b.BaseUnitPrice'
                )
                ->whereIn('a.ClassID', ['SFG1', 'FG', 'SFG2']);
        } else {
            $query = DB::connection('sqlsrv4')
                ->table('Erp.Part as a')
                ->leftJoin('Erp.VendPart as b', function ($join) {
                    $join->on('a.PartNum', '=', 'b.PartNum')
                        ->whereRaw('b.EffectiveDate = (
                    SELECT MAX(EffectiveDate)
                    FROM Erp.VendPart
                    WHERE PartNum = a.PartNum
                )');
                })
                ->select(
                    'a.PartNum as PartNum',
                    'a.PartDescription as Description',
                    'b.PUM as UOM',
                    'b.BaseUnitPrice'
                )
                ->where('a.ClassID', $type);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('a.PartNum', 'like', "%{$search}%")
                    ->orWhere('a.PartDescription', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('b.EffectiveDate', 'desc')
            ->paginate(50);
    }
    public function get_purchase_part($search)
    {
        $query = DB::connection('sqlsrv4')
            ->table('Erp.Part')
            ->select('PartNum', 'PartDescription')
            ->where('ClassID', 'SFG2');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('PartNum', 'like', "%{$search}%")
                    ->orWhere('PartDescription', 'like', "%{$search}%");
            });
        }

        return $query->paginate(50);
    }
    public function getMaterialByHeaderID($id)
    {
        return DB::table('PriceListHeader')
            ->where('HeaderID', $id)
            ->first();
    }
    public function updateMaterial($id, $data)
    {
        return DB::table('PriceListMtl')
            ->where('MtlID', $id)
            ->update($data);
    }
    public function get_period_by_id($PeriodID)
    {
        return DB::table('PriceListPeriod')
            ->where('id', $PeriodID)
            ->first();
    }
    public function insertMaterial($data)
    {
        return DB::table('PriceListMtl')
            ->insertGetId($data);
    }
    public function show_process($headerID, $MtlID, $limit, $offset)
    {
        return DB::table('PriceListProcess')
            ->where('HeaderID', $headerID)
            ->where('MtlID', $MtlID)
            ->offset($offset)
            ->limit($limit)
            ->get();
    }
    public function count_show_process($headerID, $MtlID)
    {
        return DB::table('PriceListProcess')
            ->where('HeaderID', $headerID)
            ->where('MtlID', $MtlID)
            ->count();
    }
    public function getProcessByIdAndName($headerID, $MtlID, $name_process)
    {
        return DB::table('PriceListProcess')
            ->where('HeaderID', $headerID)
            ->where('MtlID', $MtlID)
            ->where('NameProcess', $name_process)
            ->first();
    }
    public function insertProcess($data)
    {
        return DB::table('PriceListProcess')
            ->insert($data);
    }
    public function getProcessID($processID)
    {
        return DB::table('PriceListProcess')
            ->where('ProcessID', $processID)
            ->first();
    }
    public function updateProcess($process_id, $data)
    {
        return DB::table('PriceListProcess')
            ->where('ProcessID', $process_id)
            ->update($data);
    }
    public function deleteProcess($id)
    {
        return DB::table('PriceListProcess')
            ->where('ProcessID', $id)
            ->delete();
    }
    public function PriceListPreview($headerID, $MtlID)
    {
        return DB::table('PriceListHeader as a')
            ->leftJoin('PriceListMtl as b', 'a.HeaderID', '=', 'b.HeaderID')
            ->leftJoin('PriceListPeriod as c', 'a.PeriodID', '=', 'c.id')
            ->select(
                'a.SupplierName',
                'a.Customer',
                'a.PeriodID',
                'c.EffectiveDate',
                'c.ExpiredDate',
                'b.*'
            )
            ->where('a.HeaderID', $headerID)
            ->where('b.MtlID', $MtlID)
            ->first();
    }
    public function check_role($id)
    {
        return DB::table('QuoRoleApproval')
            ->where('UserID', $id)
            ->value('Type');
    }
    public function check_document($id)
    {
        return DB::table('PriceListHeader')
            ->where('HeaderID', $id)
            ->first();
    }
    public function update_master_header($headerID, $period)
    {
        return DB::table('PriceListHeader')
            ->where('HeaderID', $headerID)
            ->update(['PeriodID' => $period]);
    }
    public function update_material_period($headerID, $materialID, $period)
    {
        $data_period = DB::table('PriceListPeriod')
            ->where('id', $period)
            ->first();
        return DB::table('PriceListMtl')
            ->where('HeaderID', $headerID)
            ->where('MtlID', $materialID)
            ->update([
                'EffectiveDate' => $data_period->EffectiveDate,
                'ExpiredDate' => $data_period->ExpiredDate
            ]);
    }
    public function delete_document($id)
    {
        DB::table('PriceListHeader')
            ->where('HeaderID', $id)
            ->delete();
        DB::table('PriceListPart')
            ->where('HeaderID', $id)
            ->delete();
        DB::table('PriceListProcess')
            ->where('HeaderID', $id)
            ->delete();
        DB::table('PriceListSubTotal')
            ->where('HeaderID', $id)
            ->delete();
        return true;
    }
    public function data_header($cutSupplier, $customer)
    {
        return DB::table('PriceListHeader')
            ->where('SupplierNum', $cutSupplier)
            ->where('Customer', $customer)
            ->first();
    }
    public function sum_manufacturing_cost($headerID, $MtlID)
    {
        return DB::table('PriceListProcess')
            ->where('HeaderID', $headerID)
            ->where('MtlID', $MtlID)
            ->sum('Estimate');
    }
    public function show_purchase($headerID, $MtlID, $limit, $offset)
    {
        return DB::table('PriceListPurchase')
            ->where('HeaderID', $headerID)
            ->where('MtlID', $MtlID)
            ->offset($offset)
            ->limit($limit)
            ->get();
    }
    public function count_show_purchase($headerID, $MtlID)
    {
        return DB::table('PriceListPurchase')
            ->where('HeaderID', $headerID)
            ->where('MtlID', $MtlID)
            ->count();
    }
    public function data_purchase($headerID, $MtlID, $purchase_part)
    {
        return DB::table('PriceListPurchase')
            ->where('HeaderID', $headerID)
            ->where('MtlID', $MtlID)
            ->where('PurchasePart', $purchase_part)
            ->first();
    }
    public function find_purchase($id)
    {
        return DB::table('PriceListPurchase')
            ->where('PurchaseID', $id)
            ->first();
    }
    public function store_purchase($data)
    {
        return DB::table('PriceListPurchase')
            ->insert($data);
    }
    public function update_purchase($purchase_id, $data)
    {
        return DB::table('PriceListPurchase')
            ->where('PurchaseID', $purchase_id)
            ->update($data);
    }
    public function delete_purchase($id)
    {
        return DB::table('PriceListPurchase')
            ->where('PurchaseID', $id)
            ->delete();
    }
    public function show_all_part($headerID, $search, $limit, $offset)
    {
        $query = DB::table('PriceListMtl')
            ->where('HeaderID', $headerID);
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('PartMtl', 'like', "%{$search}%")
                    ->orWhere('PartFG', 'like', "%{$search}%")
                    ->orWhere('PartMtlDesc', 'like', "%{$search}%")
                    ->orWhere('PartFGDesc', 'like', "%{$search}%");
            });
        }
        return $query->offset($offset)->limit($limit)->orderByDesc('MtlID')->get();
    }
    public function count_all_part($headerID, $search)
    {
        $query = DB::table('PriceListMtl')
            ->where('HeaderID', $headerID);
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('PartMtl', 'like', "%{$search}%")
                    ->orWhere('PartFG', 'like', "%{$search}%")
                    ->orWhere('PartMtlDesc', 'like', "%{$search}%")
                    ->orWhere('PartFGDesc', 'like', "%{$search}%");
            });
        }
        return $query->count();
    }
    public function delete_part($headerID, $MtlID)
    {
        DB::table('PriceListMtl')
            ->where('MtlID', $MtlID)
            ->where('HeaderID', $headerID)
            ->delete();
        DB::table('PriceListPurchase')
            ->where('MtlID', $MtlID)
            ->where('HeaderID', $headerID)
            ->delete();
        DB::table('PriceListProcess')
            ->where('MtlID', $MtlID)
            ->where('HeaderID', $headerID)
            ->delete();
        return true;
    }
    public function get_manufactur_cost($mtl)
    {
        return DB::table('PriceListProcess')
            ->where('MtlID', $mtl)
            ->sum('Estimate');
    }
    public function get_material_cost($mtl)
    {
        return DB::table('PriceListMtl')
            ->where('MtlID', $mtl)
            ->sum('MtlCEstimate');
    }
    public function get_sub_total($mtl){
        $mtlCost = DB::table('PriceListMtl')
        ->where('MtlID', $mtl)
        ->sum('MtlCEstimate');
        $Purchase = DB::table('PriceListPurchase')
        ->where('MtlID', $mtl)
        ->sum('Estimate');
        $manufactur =  DB::table('PriceListProcess')
        ->where('MtlID', $mtl)
        ->sum('Estimate');
        $results = round($mtlCost + $Purchase + $manufactur);
        return $results;
    }
    public function get_part_qty($mtl)
    {
        return DB::table('PriceListMtl')
            ->where('MtlID', $mtl)
            ->value('PartWQty');
    }
    public function getOtherCostByMtl($mtl, $name)
    {
        return DB::table('PriceListOtherCost')
            ->where('MtlID', $mtl)
            ->where('NameItem', $name)
            ->first();
    }
    public function storeOtherCost($data)
    {
        return DB::table('PriceListOtherCost')
            ->insert($data);
    }
    public function show_other_cost($headerID, $MtlID)
    {
        return DB::table('PriceListOtherCost')
            ->where('HeaderID', $headerID)
            ->where('MtlID', $MtlID)
            ->get();
    }
    public function find_other_cost($OtherCostID)
    {
        return DB::table('PriceListOtherCost')
            ->where('OtherCostID', $OtherCostID)
            ->first();
    }
    public function update_other_cost($id, $data)
    {
        return DB::table('PriceListOtherCost')
            ->where('OtherCostID', $id)
            ->update($data);
    }
    public function delete_other_cost($id)
    {
        return DB::table('PriceListOtherCost')
            ->where('OtherCostID', $id)
            ->delete();
    }
    public function show_all_summary($Supplier, $Status, $Customer, $Effective, $Expired, $search, $limit, $offset)
    {
        $query = DB::connection('vendor-app-epicor')
            ->table('QuotationHeader')
            ->where('SupplierNum', $Supplier)
            ->where('Status', $Status)
            ->where('Customer', $Customer)
            ->where('EffectiveDate', $Effective)
            ->where('ExpiredDate', $Expired);
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('PartMtl', 'like', "%{$search}%")
                    ->orWhere('PartMtlDesc', 'like', "%{$search}%")
                    ->orWhere('PartFG', 'like', "%{$search}%")
                    ->orWhere('PartFGDesc', 'like', "%{$search}%");
            });
        }
        return $query->offset($offset)->limit($limit)->get();
    }
    public function count_all_summary($Supplier, $Status, $Customer, $Effective, $Expired, $search)
    {
        $query = DB::connection('vendor-app-epicor')
            ->table('QuotationHeader')
            ->where('SupplierNum', $Supplier)
            ->where('Status', $Status)
            ->where('Customer', $Customer)
            ->where('EffectiveDate', $Effective)
            ->where('ExpiredDate', $Expired);
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('PartMtl', 'like', "%{$search}%")
                    ->orWhere('PartMtlDesc', 'like', "%{$search}%")
                    ->orWhere('PartFG', 'like', "%{$search}%")
                    ->orWhere('PartFGDesc', 'like', "%{$search}%");
            });
        }
        return $query->count();
    }
    public function total_header()
    {
        return DB::connection('vendor-app-epicor')
            ->table('QuotationHeader')
            ->select('SupplierNum', 'SupplierName', 'Status', 'Customer', 'EffectiveDate', 'ExpiredDate', DB::raw('SUM(CurrentTotalSalesPrice) AS Amount'))
            ->groupBy('SupplierNum', 'SupplierName', 'Status', 'Customer', 'EffectiveDate', 'ExpiredDate');
    }
    public function confirm_master($head, $data)
    {
        return DB::table('PriceListMtl')
            ->where('HeaderID', $head)
            ->update($data);
    }
    public function header_view($HeaderID)
    {
        return DB::table('PriceListHeader')
            ->where('HeaderID', $HeaderID)
            ->first();
    }
    public function material_view($MtlID)
    {
        return DB::table('PriceListMtl')
            ->where('MtlID', $MtlID)
            ->first();
    }
    public function purchase_view($MtlID)
    {
        return DB::table('PriceListPurchase')
            ->where('MtlID', $MtlID)
            ->get();
    }
    public function process_view($MtlID)
    {
        return DB::table('PriceListProcess')
            ->where('MtlID', $MtlID)
            ->get();
    }
    public function other_cost_view($MtlID)
    {
        return DB::table('PriceListOtherCost')
            ->where('MtlID', $MtlID)
            ->get();
    }
    public function QuoHeader($supplier, $status, $customer, $effective, $expired)
    {
        return DB::connection('vendor-app-epicor')
            ->table('QuotationHeader as a')
            ->leftJoin('users as b', 'a.CreatedBy', '=', 'b.id')
            ->select('a.*', 'b.full_name', 'b.Logo','b.signature')
            ->where('a.SupplierNum', $supplier)
            ->where('a.Status', $status)
            ->where('a.Customer', $customer)
            ->where('a.EffectiveDate', $effective)
            ->where('a.ExpiredDate', $expired)
            ->orderBy('a.CreatedAt','desc')
            ->first();
    }
    public function QuoTabel($supplier, $status, $customer, $effective, $expired)
    {
        return DB::connection('vendor-app-epicor')
            ->table('QuotationHeader')
            ->where('SupplierNum', $supplier)
            ->where('Status', $status)
            ->where('Customer', $customer)
            ->where('EffectiveDate', $effective)
            ->where('ExpiredDate', $expired)
            ->get();
    }
    public function get_name_supplier($supplier_num)
    {
        return DB::connection('sqlsrv4')
            ->table('Erp.Vendor')
            ->where('VendorNum', $supplier_num)
            ->select('Name')
            ->first();
    }
    public function get_approval($header_id)
    {
        return DB::connection('vendor-app-epicor')
        ->table('QuotationHistoryApproval as a')
        ->leftJoin('users as b', 'a.Approval_1', '=', 'b.full_name')
        ->select('a.*', 'b.signature')
            ->where('QuotationHeaderID', $header_id)
            ->first();
    }
    public function download_preview($headerID)
    {
        return DB::table('PriceListMtl')
        ->where('HeaderID', $headerID)
        ->select(
            'PartFG',
            'PartFGDesc',
            'PartMtl',
            'PartMtlDesc',
            'MtlWPrice',
            DB::raw('TopEndCoil / 1000.0 as TopEndCoil'),
            'EffectiveDate',
            'ExpiredDate'
        )
        ->orderByDesc('MtlID')
        ->get();
    }
    public function list_quotation($supplierNum,$customer,$effectiveDate,$expiredDate,$search,$limit,$offset)
{
    $query = DB::table('PriceListHeader as a')
        ->leftJoin('PriceListMtl as b','a.HeaderID','=','b.HeaderID')
        ->select(
            'b.PartFG',
            'b.PartFGDesc',
            'b.PartMtl',
            'b.PartMtlDesc',
            'b.MtlWPrice'
        )
        ->where('a.SupplierNum',$supplierNum)
        ->where('a.Customer',$customer)
        ->where('b.EffectiveDate',$effectiveDate)
        ->where('b.ExpiredDate',$expiredDate)
        ->whereNotExists(function ($query) use ($supplierNum,$customer,$effectiveDate,$expiredDate) {

            $query->select(DB::raw(1))
                ->from(DB::raw('[vendor-app-epicor].[dbo].[QuotationHeader] as q'))
                ->whereColumn('q.PartFG','b.PartFG')
                ->whereColumn('q.PartMtl','b.PartMtl')
                ->where('q.SupplierNum',$supplierNum)
                ->where('q.Customer',$customer)
                ->where('q.EffectiveDate',$effectiveDate)
                ->where('q.ExpiredDate',$expiredDate);
        });

    if (!empty($search)) {
        $query->where(function($q) use ($search) {
            $q->where('b.PartFG','like',"%$search%")
              ->orWhere('b.PartFGDesc','like',"%$search%")
              ->orWhere('b.PartMtl','like',"%$search%")
              ->orWhere('b.PartMtlDesc','like',"%$search%");
        });
    }

    return $query->offset($offset)
        ->limit($limit)
        ->get();
}
public function count_list_quotation($supplierNum,$customer,$effectiveDate,$expiredDate,$search){
    $query = DB::table('PriceListHeader as a')
        ->leftJoin('PriceListMtl as b','a.HeaderID','=','b.HeaderID')
        ->select(
            'b.PartFG',
            'b.PartFGDesc',
            'b.PartMtl',
            'b.PartMtlDesc',
            'b.MtlWPrice'
        )
        ->where('a.SupplierNum',$supplierNum)
        ->where('a.Customer',$customer)
        ->where('b.EffectiveDate',$effectiveDate)
        ->where('b.ExpiredDate',$expiredDate)
        ->whereNotExists(function ($query) use ($supplierNum,$customer,$effectiveDate,$expiredDate) {

            $query->select(DB::raw(1))
                ->from(DB::raw('[vendor-app-epicor].[dbo].[QuotationHeader] as q'))
                ->whereColumn('q.PartFG','b.PartFG')
                ->whereColumn('q.PartMtl','b.PartMtl')
                ->where('q.SupplierNum',$supplierNum)
                ->where('q.Customer',$customer)
                ->where('q.EffectiveDate',$effectiveDate)
                ->where('q.ExpiredDate',$expiredDate);
        });

    if (!empty($search)) {
        $query->where(function($q) use ($search) {
            $q->where('b.PartFG','like',"%$search%")
              ->orWhere('b.PartFGDesc','like',"%$search%")
              ->orWhere('b.PartMtl','like',"%$search%")
              ->orWhere('b.PartMtlDesc','like',"%$search%");
        });
    }

    return $query->count();
}
public function print_pending_quo($supplierNum, $customer, $effectiveDate, $expiredDate){
    $query = DB::table('PriceListHeader as a')
        ->leftJoin('PriceListMtl as b', 'a.HeaderID', '=', 'b.HeaderID')
        ->select(
            'b.PartFG',
            'b.PartFGDesc',
            'b.PartMtl',
            'b.PartMtlDesc',
            'b.MtlWPrice'
        )
        ->where('a.SupplierNum', $supplierNum)
        ->where('a.Customer', $customer)
        ->where('b.EffectiveDate', $effectiveDate)
        ->where('b.ExpiredDate', $expiredDate)
        ->whereNotExists(function ($query) use ($supplierNum, $customer, $effectiveDate, $expiredDate) {

            $query->select(DB::raw(1))
                ->from(DB::raw('[vendor-app-epicor].[dbo].[QuotationHeader] as q'))
                ->whereColumn('q.PartFG', 'b.PartFG')
                ->whereColumn('q.PartMtl', 'b.PartMtl')
                ->where('q.SupplierNum', $supplierNum)
                ->where('q.Customer', $customer)
                ->where('q.EffectiveDate', $effectiveDate)
                ->where('q.ExpiredDate', $expiredDate);
        });
    return $query->get();
}
public function get_quo_data($Supplier, $Status, $Customer, $Effective, $Expired)
    {
        return DB::connection('vendor-app-epicor')
            ->table('QuotationHeader')
            ->where('SupplierNum', $Supplier)
            ->where('Status', $Status)
            ->where('Customer', $Customer)
            ->where('EffectiveDate', $Effective)
            ->where('ExpiredDate', $Expired)
            ->cursor();
    }
    public function update_quo_head($Supplier, $Customer, $Effective, $Expired)
    {
        return DB::connection('vendor-app-epicor')
            ->table('QuotationHeader')
            ->where('SupplierNum', $Supplier)
            ->where('Status', 3)
            ->where('Customer', $Customer)
            ->where('EffectiveDate', $Effective)
            ->where('ExpiredDate', $Expired)
            ->update([
                'Status' => 4
            ]);
    }
}
