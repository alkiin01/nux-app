<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TimeEntry extends Model
{
    use HasFactory;

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

    public static function get_part_num_list($jobNum, $opSeq, $laborHedSeq,  $laborDtlSeq, $part_num)
    {
        $result = DB::connection('sqlsrv5')->table("f_list_co_part('$jobNum', $opSeq, $laborHedSeq,  $laborDtlSeq, '$part_num')");
        return $result;
    }

    public static function part_num_list($jobNum)
    {
        $result = DB::connection('sqlsrv4')->table("Erp.JobPart")
        ->where('JobNum', "$jobNum");
        return $result;
    }

    public static function get_job_properties($jobNum, $OprSeq)
    {
        $result = DB::connection('sqlsrv5')->table("f_job_detail('$jobNum', $OprSeq)");
        return $result;
    }

    public static function get_detail_list($JoDate, $ResourceGroupID, $ResourceID, $ShiftID, $EmployeeID)
    { 
        $my_name = auth()->user()->username;
        $result = DB::connection('sqlsrv5')->table("f_time_entry_status('$JoDate')") ;

        if ($ShiftID > 0) {
            $result = $result->where('ShiftID', $ShiftID);
        }

        if (!empty($ResourceGroupID) && $ResourceGroupID != 'null') {
            $result = $result->where('ResourceGrpID', "$ResourceGroupID");
        }

        if (!empty($ResourceID)) {
            $result = $result->where('ResourceID', "$ResourceID");
        }

        if (!empty($EmployeeID)) {
            $result = $result->where('EmployeeNum', "$EmployeeID");
        }

        return $result;
    }

    public static function get_count_document_draft($JoDate, $ResourceGroupID, $ResourceID, $ShiftID, $EmployeeID)
    {
        $my_name = auth()->user()->username;
        $result = DB::connection('sqlsrv5')->table("f_time_entry_status('$JoDate')")->where('TimeStatus', '<>', 'A') ;

        if ($ShiftID != 'null') {
            $result = $result->where('ShiftID', $ShiftID);
        }

        if (!empty($ResourceGroupID) && $ResourceGroupID != 'null') {
            $result = $result->where('ResourceGrpID', "$ResourceGroupID");
        }

        if (!empty($ResourceID) && $ResourceID != 'null') {
            $result = $result->where('ResourceID', "$ResourceID");
        }

        if (!empty($EmployeeID) && $EmployeeID != 'null') {
            $result = $result->where('EmployeeNum', "$EmployeeID");
        }

        return $result->get()->count();
    }

    public static function get_count_document($JoDate, $ResourceGroupID, $ResourceID, $ShiftID, $EmployeeID)
    {
        $result = DB::connection('sqlsrv5')->table("f_time_entry_status('$JoDate')");

        if ($ShiftID != 'null') {
            $result = $result->where('ShiftID', $ShiftID);
        }

        if (!empty($ResourceGroupID) && $ResourceGroupID != 'null') {
            $result = $result->where('ResourceGrpID', "$ResourceGroupID");
        }

        if (!empty($ResourceID) && $ResourceID != 'null') {
            $result = $result->where('ResourceID', "$ResourceID");
        }

        if (!empty($EmployeeID) && $EmployeeID != 'null') {
            $result = $result->where('EmployeeNum', "$EmployeeID");
        }
        return $result->get()->count();
    }

    public static function data_detail($JONum)
    {
        return DB::connection('sqlsrv5')->table("f_production_schedule_by_jo('$JONum')")->get();
    } 

    

    public static function get_detail_tag_label($job_num, $process_detail_id, $part_num)
    {
        $db = DB::table('t510_production_tag as a')
            ->where('a.job_num', "$job_num")
            ->where('a.process_detail_id', $process_detail_id)
            ->where('a.item_no', "$part_num")
            ->select('a.*', DB::raw('NULL AS special_mark'), DB::raw('NULL AS part_type'));
        return $db;
    }

    public static function generate_tag_label($job_num, $process_detail_id, $qty_plan, $qty_pack, $home_line_detail_id, $operator_name, $quality_name, $model_name, $part_num, $part_name, $cust_name, $production_date)
    {
        $my_name = auth()->user()->username;
        $part_name = str_replace("__", ",",  $part_name);
        $line_search_id = DB::table('t510_production_tag')
            ->where('job_num', '=', "$job_num")
            ->where('process_detail_id', '=', $process_detail_id)
            ->where('item_no', '=', $part_num)
            ->where('is_delete', 0)
            ->select(DB::raw('MAX(line_search_id) AS line_search_id'))
            ->first()->line_search_id;

        $qty_all = 0;
        $dbTag = DB::table('t510_production_tag')
            ->where('job_num', '=', $job_num)
            ->where('process_detail_id', '=', $process_detail_id)
            ->where('item_no', '=', $part_num)
            ->where('is_delete', 0)
            ->select('job_num', 'process_detail_id', DB::raw('SUM(qty_1) AS total_qty_already_generate'))
            ->groupBy('job_num', 'process_detail_id')
            ->get();
        if ($dbTag->count() > 0) {
            foreach ($dbTag as $row) {
                $qty_all = $row->total_qty_already_generate;
            }
        }

        $line_search_id = ($line_search_id == '' ? 1 : $line_search_id);
        $qty_production = $qty_plan;
        $standard_pallet = $qty_pack;

        if (floor(($qty_production - $qty_all) / $standard_pallet) > 0) {
            $total_pallet = floor(($qty_production - $qty_all) / $standard_pallet);
        } else {
            $total_pallet = 0;
        }
        $sisa_qty = ($qty_production - $qty_all) % $standard_pallet;

        for ($i = $line_search_id; $i <= $total_pallet; $i++) {

            ${'index_data' . $i} = [
                'job_num' =>  "$job_num",
                'process_detail_id' =>   $process_detail_id,
                'item_no' => "$part_num",
                'line_search_id' => $line_search_id
            ];

            ${'data_tag' . $i} = [
                'job_num' =>  "$job_num",
                'process_detail_id' =>  $process_detail_id,
                'line_search_id' =>  $i,
                'item_no' => "$part_num",
                'item_name' =>  str_replace('~', ',', $part_name),
                'cust_name' =>  $cust_name,
                'doc_date' =>  "$production_date",
                'home_line_detail_id' =>  "$home_line_detail_id",
                'qty_1' =>  $standard_pallet,
                'qty_2' =>  $standard_pallet,
                'operator_name' =>  $operator_name,
                'quality_name' =>  $quality_name,
                'model_name' =>  "$model_name",
                'production_date' =>  "$production_date",
                'created_by' =>  $my_name

            ];

            ${'check_tag_id' . $i} = DB::table('t510_production_tag')
                ->where(${'index_data' . $i})
                ->count();

            if (${'check_tag_id' . $i} == 0) {
                ${'insert_tag' . $i} = DB::table('t510_production_tag')
                    ->insert(${'data_tag' . $i});
            }
            $line_search_id++;
        }

        $line_search_id = $line_search_id + 2;
        if ($sisa_qty > 0) {
            ${'index_data'} = [
                'job_num' =>  "$job_num",
                'process_detail_id' =>   $process_detail_id,
                'item_no' => "$part_num",
                'line_search_id' => $line_search_id
            ];
            ${'data_tag'} = [
                'job_num' =>  $job_num,
                'process_detail_id' =>  $process_detail_id,
                'line_search_id' =>  $i,
                'item_no' => "$part_num",
                'item_name' =>  str_replace('~', ',', $part_name),
                'cust_name' =>  $cust_name,
                'doc_date' =>  "$production_date",
                'home_line_detail_id' =>  "$home_line_detail_id",
                'qty_1' =>  $sisa_qty,
                'qty_2' =>  $sisa_qty,
                'operator_name' =>  $operator_name,
                'quality_name' =>  $quality_name,
                'production_date' =>  "$production_date",
                'model_name' =>  "$model_name",
                'created_by' =>  "$my_name"
            ];

            ${'check_tag_id'} = DB::table('t510_production_tag')
                ->where(${'index_data'})
                ->count();

            if (${'check_tag_id'} == 0) {
                ${'insert_tag'} = DB::table('t510_production_tag')
                    ->insert(${'data_tag'});
            }
        }
        $result = $i;
        return $result;
    }


    public static function get_detail_tag_label_id($job_num, $process_detail_id, $line_search_id, $part_num)
    {
        $db = DB::table('t510_production_tag as a')
            ->where('a.job_num', "$job_num")
            ->where('a.process_detail_id', $process_detail_id)
            ->where('a.line_search_id', $line_search_id)
            ->where('a.item_no', "$part_num")
            ->select('a.*', DB::raw('NULL AS special_mark'), DB::raw('NULL AS part_type'));
        return $db;
    }
}
