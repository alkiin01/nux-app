<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryMoveIn extends Model
{
    use HasFactory;

    public static function get_line_detail($TranTypeID, $MonthID, $TranSeqID, $LineID)
    {
        $result = DB::table('t510_InventoryMove AS a') 
        ->where('a.TranTypeID', $TranTypeID)
        ->where('a.MonthID', $MonthID)
        ->where('a.TranSeqID', $TranSeqID)
        ->where('a.LineID', $LineID)
        ->get() ; 
        return $result ;
    }

    public static function push_jo_number_to_labor_dtl($laborHedSeq, $laborDtlSeq, $JoNum, $OprSeq)
    { 
        return DB::connection('sqlsrv4')->table('Erp.LaborDtl')
            ->where('LaborHedSeq', $laborHedSeq)
            ->where('laborDtlSeq', $laborDtlSeq) 
            ->update([
                "JobNum" => "$JoNum",
                "OprSeq" => $OprSeq
            ]); ;
    }

    public static function get_labor_hed_seq($InptActualClockinDate, $InptShiftID, $InptEmployeeID)
    {
        $LaborHedSeq = '' ;
        $result = DB::connection('sqlsrv4')->table('Erp.LaborHed')
            ->where('PayRollDate', "$InptActualClockinDate")
            ->where('Shift', $InptShiftID)
            ->where('EmployeeNum', "$InptEmployeeID")
            ->get();
        if($result->count() > 0) {
            foreach ($result AS $row) {
                $LaborHedSeq = $row->LaborHedSeq ;
            }
        }
        return $LaborHedSeq;
    }

    public static function get_resource_group($category_id)
    {
        $result = DB::connection('sqlsrv4')->table('Erp.ResourceGroup AS a')
            ->where('a.ResourceType', '=', 'MACHINE')
            ->where('a.JCDept', 'LIKE', '%' . $category_id . '%');
        return $result;
    }

    public static function get_resource($line)
    {
        $result = DB::connection('sqlsrv4')->table('Erp.Resource AS a')
            ->where('a.ResourceGrpID', "$line");
        return $result;
    }

    public static function get_employee_list()
    {
        $result = DB::connection('sqlsrv4')->table('Erp.EmpBasic AS a')
            ->where('a.EmpStatus', '=', 'A')
            ->select('a.EmpID', 'a.Name');
        return $result;
    }

    public static function get_job_list($date)
    {
        date_default_timezone_set('Asia/Jakarta');
        $start_date = date('Y-m-d', strtotime('-30 days'));
        $end_date = date('Y-m-d', strtotime('+15 day'));
        $result = DB::connection('sqlsrv5')->table("f_job_list('$start_date', '$end_date')");
        return $result;
    }

    public static function get_shift_list()
    {
        $result = DB::connection('sqlsrv4')->table('Erp.JCShift AS a');
        return $result;
    }

    public static function check_document_status($laborHedSeq,  $laborDtlSeq)
    {
        $result = DB::connection('sqlsrv4')->table('Erp.LaborDtl AS a')
        ->where('a.laborHedSeq', $laborHedSeq)
        ->where('a.laborDtlSeq', $laborDtlSeq)
        ->get() ;
        $status = 0 ;
        if ($result->count() > 0) {
            foreach ($result AS $row) {
                $status = ($row->TimeStatus == 'A' ? 1 : 0) ;
            }
        }
        return $status ;
    }

    public static function get_descr_reason_code($ReasonCode)
    {
        $result = DB::connection('sqlsrv4')->table('Erp.Reason AS a')
        ->where('a.ReasonCode', "$ReasonCode") 
        ->get() ;
        $Description = '' ;
        if ($result->count() > 0) {
            foreach ($result AS $row) {
                $Description = $row->Description ;
            }
        }
        return $Description ;
    }

    public static function get_reason_code_scrap_list()
    {
        $result = DB::connection('sqlsrv4')->table('Erp.Reason AS a')->where('ReasonType', 'S');
        return $result;
    }

    public static function get_indirect_code_list()
    {
        $result = DB::connection('sqlsrv4')->table('Erp.Indirect AS a');
        return $result;
    }

    public static function get_part_num_list($jobNum, $opSeq, $laborHedSeq,  $laborDtlSeq)
    {
        $result = DB::connection('sqlsrv5')->table("f_list_co_part('$jobNum', $opSeq, $laborHedSeq,  $laborDtlSeq)");
        return $result;
    }

    public static function get_job_properties($jobNum, $OprSeq)
    {
        $result = DB::connection('sqlsrv5')->table("f_job_detail('$jobNum', $OprSeq)");
        return $result;
    }

    public static function get_detail_list($CreatedBy, $ToWarehouseID)
    { 
        // dd($CreatedBy);
        $result = DB::table("ViewInventoryMoveHead")  ;  
        return $result ;
    }

    public static function get_wh_list()
    { 
        $result = DB::connection('sqlsrv4')->table("Erp.Warehse")->get() ;  
        return $result ;
    }

    public static function get_new_docnum($DocDate)
    { 
        date_default_timezone_set('Asia/Jakarta');
        $DateTime = date('Y-m-d H:i:s'); 
        $Month = date('m');
        $Year = substr(date('Y'),2,2);
        $MonthID = $Year.$Month ; 
        $Username = Auth::user()->username ;

        $result = DB::table("t500_InventoryMove")->where('TranTypeID', 100)->where('MonthID', $MonthID)->orderBy('TranSeqID', 'DESC')->limit(1)->get() ;  
        if ($result->count() > 0) {
            foreach ($result AS $row) {
                $TranSeqID = ($row->TranSeqID + 1) ;
                $DocNum = "100~".$MonthID."~".$TranSeqID ;
            }
        } else {
            $TranSeqID = 1 ;
            $DocNum = "100~".$MonthID."~".$TranSeqID ;
        } 

        DB::table("t500_InventoryMove")
        ->insert([
            "TranTypeID" => 100,
            "MonthID" => $MonthID,
            "TranSeqID" => $TranSeqID,
            "DocDate" => "$DocDate",
            "CreatedBy" => "$Username",
            "LastUpdated" => "$DateTime",
            "UpdatedBy" => "$Username" 
        ]);
        return $DocNum ;
    }
 
    public static function data_detail($JONum)
    {
        return DB::connection('sqlsrv5')->table("f_production_schedule_by_jo('$JONum')")->get();
    } 

    public static function get_line_id($TranTypeID, $MonthID, $TranSeqID)
    {  
        $result = DB::table("t510_InventoryMove")
        ->where('TranTypeID', $TranTypeID)
        ->where('MonthID', $MonthID)
        ->where('TranSeqID', $TranSeqID)
        ->orderBy('LineID', 'DESC')->limit(1)->get() ;  
        if ($result->count() > 0) {
            foreach ($result AS $row) {
                $LineID = ($row->LineID + 1) ; 
            }
        } else {
            $LineID = 1 ; 
        }  
        return $LineID ;
    }

    public static function get_part_name($PartNum)
    {  
        $result = DB::connection('sqlsrv4')->table("Erp.Part")
        ->where('PartNum', "$PartNum")->get() ;   
        return $result ;
    }

    public static function get_detail_table($TranTypeID, $MonthID, $TranSeqID)
    { 
        $db = DB::table('t510_InventoryMove as a')
            ->where('a.TranTypeID', $TranTypeID) 
            ->where('a.MonthID', $MonthID) 
            ->where('a.TranSeqID', $TranSeqID) ;
        return $db;
    }

    public static function check_barcode($mit, $line, $LineRel, $PartNum)
    { 
        $db = DB::table('t510_InventoryMove as a')
            ->where('a.DocNumReference', "$mit") 
            ->where('a.DocNumReferenceLine', $line) 
            ->where('a.DocNumReferenceLineRel', $LineRel)
            ->where('a.PartNum', "$PartNum")
            ->count() ;
        return $db;
    }

    public static function get_mit_properties($mit) 
    {  
        $result = DB::connection('sqlsrv4')->table("Ice.UD101")
        ->where('Key1', "$mit")->get() ;   
        return $result ;
    }

    public static function push_inventory_dtl($mit, $PartNum, $Qty, $line, $LotNum, $ToBinID, $TranTypeID, $MonthID, $TranSeqID, $LineRel)
    {
        $LineID = self::get_line_id($TranTypeID, $MonthID, $TranSeqID);
        $db_part_profile = self::get_part_name($PartNum) ; 
        if ($db_part_profile->count() > 0) {
            foreach ($db_part_profile AS $row) {
                $PartName = $row->PartDescription ; 
                $Cust = $row->ProdCode ; 
            }
        } else {
            $PartName = '' ; 
            $Cust = '' ; 
        } 
        $db_mit_properties = self::get_mit_properties($mit) ; 
        if ($db_mit_properties->count() > 0) {
            foreach ($db_mit_properties AS $row) {
                $FromWarehouseID = $row->ShortChar03 ; 
                $FromWarehouseDesc = $row->Character03 ; 
                $FromBinID = $row->ShortChar04 ; 
                $ToWarehouseID = $row->ShortChar01 ; 
                $ToWarehouseDesc = $row->Character01 ; 
                $ToBinID = $row->ShortChar02 ; 
            }
        } else {
            $PartName = '' ; 
            $Cust = '' ; 
        }   
        $db = DB::table('t510_InventoryMove')
        ->insert([
            "TranTypeID" => $TranTypeID,
            "MonthID" => $MonthID,
            "TranSeqID" => $TranSeqID,
            "LineID" => $LineID,
            "PartNum" => "$PartNum",
            "PartName" => "$PartName", 
            "CutomerID" => "$Cust",
            "QtyMove" => $Qty,
            "QtyBalance" => $Qty,
            "FromWarehouseID" => "$FromWarehouseID",
            "FromWarehouseDesc" => "$FromWarehouseDesc",
            "FromBinID" => "$FromBinID",
            "ToWarehouseID" => "$ToWarehouseID", 
            "ToWarehouseDesc" => "$ToWarehouseDesc",
            "ToBinID" => "$ToBinID",
            "DocNumReference" => "$mit",
            "DocNumReferenceLine" => $line,
            "DocNumReferenceLineRel" => $LineRel,
            "LotNum" => "$LotNum",
            "IsDelete" => 0 
        ]) ; 
        return $db;
    } 

    public static function delete_inventory_dtl($TranTypeID, $MonthID, $TranSeqID, $LineID)
    { 
        $db = DB::table('t510_InventoryMove')
        ->where([
            "TranTypeID" => $TranTypeID,
            "MonthID" => $MonthID,
            "TranSeqID" => $TranSeqID,
            "LineID" => $LineID
        ])
        ->delete() ; 
        return $db;
    } 

    public static function push_inventory_dtl_from_job($jobnum, $PartNum, $Qty, $opSeq, $LotNum, $ToBinID, $TranTypeID, $MonthID, $TranSeqID, $LineRel, $FromWarehouseID, $FromWarehouseDesc, $FromBinID, $ToWarehouseID, $ToWarehouseDesc)
    {
        $LineID = self::get_line_id($TranTypeID, $MonthID, $TranSeqID);
        $db_part_profile = self::get_part_name($PartNum) ; 
        if ($db_part_profile->count() > 0) {
            foreach ($db_part_profile AS $row) {
                $PartName = $row->PartDescription ; 
                $Cust = $row->ProdCode ; 
            }
        } else {
            $PartName = '' ; 
            $Cust = '' ; 
        }  
        $db = DB::table('t510_InventoryMove')
        ->insert([
            "TranTypeID" => $TranTypeID,
            "MonthID" => $MonthID,
            "TranSeqID" => $TranSeqID,
            "LineID" => $LineID,
            "PartNum" => "$PartNum",
            "PartName" => "$PartName", 
            "CutomerID" => "$Cust",
            "QtyMove" => $Qty,
            "QtyBalance" => $Qty,
            "FromWarehouseID" => "$FromWarehouseID",
            "FromWarehouseDesc" => "$FromWarehouseDesc",
            "FromBinID" => "$FromBinID",
            "ToWarehouseID" => "$ToWarehouseID", 
            "ToWarehouseDesc" => "$ToWarehouseDesc",
            "ToBinID" => "$ToBinID",
            "DocNumReference" => "$jobnum",
            "DocNumReferenceLine" => $opSeq,
            "DocNumReferenceLineRel" => $LineRel,
            "LotNum" => "$LotNum",
            "IsDelete" => 0 
        ]) ; 
        return $db;
    }

    public static function get_name_warehouse($code)
    {  
        $result = DB::connection('sqlsrv4')->table("Warehse")
        ->where('WarehouseCode', "$code")->get() ;  
        if ($result->count() > 0) {
            foreach ($result AS $row) {
                $Description = $row->Description ; 
            }
        } else {
            $Description = '' ; 
        }  
        return $Description ;
    }
    public static function showBin($WarehouseCode){
        return DB::connection('sqlsrv4')->table("Erp.WhseBin")
        ->where('Warehousecode',$WarehouseCode)
        ->select('BinNum','Description')
        ->get();
    }
}
