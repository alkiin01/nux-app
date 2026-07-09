<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; 
use Illuminate\Support\Facades\DB ;
  
class ProductionSchedule extends Model
{
    use HasFactory;
  
    public static function get_resource_group($category_id) 
    {       
        $result = DB::connection('sqlsrv4')->table('Erp.ResourceGroup AS a') 
            ->where('a.ResourceType', '=', 'MACHINE') 
            ->where('a.JCDept', 'LIKE', '%'.$category_id.'%')  ;  
        return $result ;
    } 

    public static function get_resource($line) 
    {       
        $result = DB::connection('sqlsrv4')->table('Erp.Resource AS a')  
            ->where('a.ResourceGrpID', "$line")  ;  
        return $result ;
    } 

    public static function get_resource_form($category_id) 
    {       
        $result = DB::connection('sqlsrv4')->table('Erp.ResourceGroup AS a') 
            ->join('Erp.Resource AS b', 'a.ResourceGrpID', 'b.ResourceGrpID')
            ->where('a.ResourceType', '=', 'MACHINE') 
            ->where('a.JCDept', 'LIKE', '%'.$category_id.'%')
            ->select('b.ResourceID', 'b.Description')  ;  
        return $result ;
    }  

    public static function get_warehouse_id() 
    {       
        $result = DB::connection('sqlsrv4')->table('Erp.Warehse')   
            ->select('WarehouseCode', 'Description')  ;  
        return $result ;
    }  

    public static function get_detail_list($date,$line,$shift,$machine) 
    {     
        $result = DB::connection('sqlsrv5')->table("f_production_schedule('$date','$line','$shift')") ;
        if (!empty($machine)) {
            $result = $result->where('home_line_detail_id', "$machine");
        }
        return $result ;    
    }

    public static function data_detail($jo_num, $process_detail_id, $item_no) 
    {        
        $results = DB::connection('sqlsrv5')->table(DB::raw("dbo.f_production_schedule_by_jo('$jo_num', '$process_detail_id', '$item_no')"))->get();
        return $results; 
    }

    public static function get_detail_tag_label_table($job_num,$process_detail_id,$part_num) 
    {    
        $db = DB::table('t510_production_tag as a')     
        ->where('a.job_num', "$job_num")
        ->where('a.process_detail_id', $process_detail_id)   
        ->where('a.item_no', "$part_num")  ;     
        return $db ;
    }
    
    public static function get_detail_tag_label($job_num,$process_detail_id,$part_num) 
    {    
        $db = DB::table('t510_production_tag as a')    
        ->leftJoin('t510_InventoryMove as b', function($join){
            $join->on('a.job_num', 'b.DocNumReference') ;
            $join->on('a.process_detail_id', 'b.DocNumReferenceLine') ;
            $join->on('a.line_search_id', 'b.DocNumReferenceLineRel') ;
            $join->on('a.item_no', 'b.PartNum') ;

        })
        ->where('a.job_num', "$job_num")
        ->where('a.process_detail_id', $process_detail_id)   
        ->where('a.item_no', "$part_num")    
        ->select('a.job_num', 'a.process_detail_id', 'a.line_search_id', 'a.item_no', 'a.item_name', 'a.cust_name', 'a.home_line_detail_id', 'a.qty_1', 'a.qty_2', 'a.log_date', 'a.production_date', 
        'a.operator_name', 'a.quality_name', 'a.remark_d', 'a.is_delete', 'a.doc_date', 'a.created_by', 
        'a.model_name', 'a.FromWarehouseID', 'a.FromWarehouseDesc', 'a.FromBinID', 'a.ToWarehouseID', 'a.ToWarehouseDesc', 'a.ToBinID', DB::raw('COUNT(b.DocNumReference) AS TotalScan'), 'a.EngineeringAlert') 
        ->having(DB::raw('COUNT(b.DocNumReference)'), 0)
        ->groupBy('a.job_num', 'a.process_detail_id', 'a.line_search_id', 'a.item_no', 'a.item_name', 'a.cust_name', 'a.home_line_detail_id', 'a.qty_1', 'a.qty_2', 'a.log_date', 'a.production_date', 
        'a.operator_name', 'a.quality_name', 'a.remark_d', 'a.is_delete', 'a.doc_date', 'a.created_by', 
        'a.model_name', 'a.FromWarehouseID', 'a.FromWarehouseDesc', 'a.FromBinID', 'a.ToWarehouseID', 'a.ToWarehouseDesc', 'a.ToBinID', 'a.EngineeringAlert') ;     
        return $db ;
    }

    public static function get_detail_table($job_num,$process_detail_id,$part_num) 
    {    
        $db = DB::table('t510_production_tag as a')    
        ->leftJoin('t510_InventoryMove as b', function($join){
            $join->on('a.job_num', 'b.DocNumReference') ;
            $join->on('a.process_detail_id', 'b.DocNumReferenceLine') ;
            $join->on('a.line_search_id', 'b.DocNumReferenceLineRel') ;
            $join->on('a.item_no', 'b.PartNum') ;

        })
        ->where('a.job_num', "$job_num")
        ->where('a.process_detail_id', $process_detail_id)   
        ->where('a.item_no', "$part_num")    
        ->select('a.job_num', 'a.process_detail_id', 'a.line_search_id', 'a.item_no', 'a.item_name', 'a.cust_name', 'a.home_line_detail_id', 'a.qty_1', 'a.qty_2', 'a.log_date', 'a.production_date', 
        'a.operator_name', 'a.quality_name', 'a.remark_d', 'a.is_delete', 'a.doc_date', 'a.created_by', 
        'a.model_name', 'a.FromWarehouseID', 'a.FromWarehouseDesc', 'a.FromBinID', 'a.ToWarehouseID', 'a.ToWarehouseDesc', 'a.ToBinID', DB::raw('COUNT(b.DocNumReference) AS TotalScan'), 'a.EngineeringAlert')  
        ->groupBy('a.job_num', 'a.process_detail_id', 'a.line_search_id', 'a.item_no', 'a.item_name', 'a.cust_name', 'a.home_line_detail_id', 'a.qty_1', 'a.qty_2', 'a.log_date', 'a.production_date', 
        'a.operator_name', 'a.quality_name', 'a.remark_d', 'a.is_delete', 'a.doc_date', 'a.created_by', 
        'a.model_name', 'a.FromWarehouseID', 'a.FromWarehouseDesc', 'a.FromBinID', 'a.ToWarehouseID', 'a.ToWarehouseDesc', 'a.ToBinID', 'a.EngineeringAlert') ;     
        return $db ;
    }

    public static function check_total_qty($job_num,$process_detail_id,$part_num)
    {    
        $qty_all = 0 ;
        $dbTag = DB::table('t510_production_tag')
        ->where('job_num', '=', $job_num)
        ->where('process_detail_id', '=', $process_detail_id)  
        ->where('item_no', '=', "$part_num")  
        ->where('is_delete', 0)
        ->select('job_num', 'process_detail_id', DB::raw('SUM(qty_1) AS total_qty_already_generate')) 
        ->groupBy('job_num', 'process_detail_id')
        ->get() ;
        
        if ($dbTag->count() > 0) {
            foreach ($dbTag AS $row) {
                $qty_all = $row->total_qty_already_generate ;
            } 
        }    
        return $qty_all ;
    }
    

    public static function check_total_qty_before($job_num,$process_detail_id,$part_num,$detail_trc_unix_id)
    {    
        $qty_all = 0 ;
        $dbTag = DB::table('t510_production_tag')
        ->where('job_num', '=', $job_num)
        ->where('process_detail_id', '=', $process_detail_id)  
        ->where('item_no', '=', "$part_num")  
        ->where('is_delete', 0)
        ->where('line_search_id', $detail_trc_unix_id)
        ->select('job_num', 'process_detail_id', DB::raw('SUM(qty_1) AS total_qty_already_generate')) 
        ->groupBy('job_num', 'process_detail_id')
        ->get() ;
        
        if ($dbTag->count() > 0) {
            foreach ($dbTag AS $row) {
                $qty_all = $row->total_qty_already_generate ;
            } 
        }    
        return $qty_all ;
    }
    
    public static function get_enggineering_alert($part_num) 
    {       
        $result = DB::connection('sqlsrv4')->table('Erp.Part AS a')  
        ->where('a.PartNum', "$part_num")
        ->get()  ;  
        if ($result->count() > 0) {
            foreach ($result AS $row) {
                $enggineering_alert = $row->EngineeringAlert ;
            }
        } else {
            $enggineering_alert = '' ;
        }
        return $enggineering_alert ;
    } 
    public static function get_warehouse_to($job_num, $part_num) 
    {       
        $result = DB::connection('sqlsrv4')->table('Erp.JobHead AS a')
        ->join('Erp.JobProd AS b', function ($join) {
            $join->on('a.JobNum', '=', 'b.JobNum')
                 ->on('a.PartNum', '=', 'b.PartNum');
        })
        ->join('Erp.Warehse AS c', 'b.WarehouseCode', '=', 'c.WarehouseCode')
        ->select('a.JobNum', 'a.PartNum', 'b.WarehouseCode', 'c.Name AS WarehouseName')
        ->where('a.JobNum', "$job_num")  
        ->groupBy('a.JobNum', 'a.PartNum', 'b.WarehouseCode', 'c.Name')
        ->limit(1)
        ->get(); 
        if ($result->count() > 0) {
            foreach ($result AS $row) {
                $wh = $row->WarehouseCode."~".$row->WarehouseName ;
            }
        } else {
            $wh = '~' ;
        }
        return $wh ;
    }

    public static function get_line_max($job_num, $process_detail_id, $part_num) {
        $db_line = DB::table('t510_production_tag')
        ->where('job_num', '=', "$job_num")
        ->where('process_detail_id', '=', $process_detail_id)   
        ->where('item_no', '=', "$part_num")  
        ->where('is_delete', 0)
        ->select('line_search_id') 
        ->orderBy('line_search_id', 'DESC')
        ->limit(1)
        ->get() ;

        if ($db_line->count() > 0) {
            foreach ($db_line AS $row) {
                $result = $row->line_search_id ;
            }
        } else {
            $result = 0 ;
        }

        return $result ;
    }

    public static function generate_tag_label($job_num,$process_detail_id,$qty_plan,$qty_pack,$home_line_detail_id,$operator_name,$quality_name,$model_name,$part_num,$part_name,$cust_name,$production_date, $warehouse_code, $warehouse_desc) {  
        $my_name = auth()->user()->username ; 
        $part_name = str_replace("__", ",",  $part_name) ;
        $engineering_alert = self::get_enggineering_alert($part_num) ;  
        $warehouse_to = explode("~", self::get_warehouse_to($job_num, $part_num)) ;  
        // $warehouse_code = $warehouse_to[0] ;
        // $warehouse_desc = $warehouse_to[1] ;
        
        $db_line = DB::table('t510_production_tag')
        ->where('job_num', '=', "$job_num")
        ->where('process_detail_id', '=', $process_detail_id)   
        ->where('item_no', '=', "$part_num")  
        ->where('is_delete', 0)
        ->select('line_search_id') 
        ->orderBy('line_search_id', 'DESC')
        ->limit(1)
        ->get() ; 
        

        if ($db_line->count() > 0) {
            foreach ($db_line AS $row) {
                $line_search_id = $row->line_search_id + 1 ;
              } 
        } else {
            $line_search_id = 1 ;
        }

        $qty_all = 0 ;
        $dbTag = DB::table('t510_production_tag')
        ->where('job_num', '=', $job_num)
        ->where('process_detail_id', '=', $process_detail_id)  
        ->where('item_no', '=', "$part_num")  
        ->where('is_delete', 0)
        ->select('job_num', 'process_detail_id', 'item_no', DB::raw('SUM(qty_1) AS total_qty_already_generate')) 
        ->groupBy('job_num', 'process_detail_id', 'item_no')
        ->get() ;
         
        if ($dbTag->count() > 0) {
            foreach ($dbTag AS $row) {
               $qty_all = $row->total_qty_already_generate ;
            } 
        } 

        $qty_production = $qty_plan ;
        $standard_pallet = $qty_pack ;  
        if (floor(($qty_production - $qty_all)/$standard_pallet) > 0) { 
            $total_pallet = floor(($qty_production - $qty_all)/$standard_pallet)  ; 
        } else {
            $total_pallet = 0 ;
        }
        $sisa_qty = ($qty_production - $qty_all) % $standard_pallet ;  
        $total_pallet = $total_pallet +  $line_search_id ;  
        for ($i=$line_search_id ; $i<$total_pallet; $i++) {  
            ${'index_data'.$i} = [
                'job_num' =>  "$job_num",
                'process_detail_id' =>   $process_detail_id, 
                'item_no' => "$part_num", 
                'line_search_id' => $line_search_id
            ] ;  

            ${'data_tag'.$i} = [
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
                'EngineeringAlert' =>  "$engineering_alert", 
                'ToWarehouseID' =>  "$warehouse_code", 
                'ToWarehouseDesc' =>  "$warehouse_desc", 
                'ToBinID' =>  "GENERAL", 
                'created_by' =>  $my_name  
            ] ;  
            
            ${'check_tag_id'.$i} = DB::table('t510_production_tag')  
            ->where(${'index_data'.$i})   
            ->count() ; 
  
                if (${'check_tag_id'.$i} == 0) {
                    ${'insert_tag'.$i} = DB::table('t510_production_tag')   
                    ->insert(${'data_tag'.$i}) ;
                }  
            $line_search_id++;

        } 

        $line_search_id = $line_search_id + 2 ;
        if ($sisa_qty>0) { 
            ${'index_data'} = [
                'job_num' =>  "$job_num",
                'process_detail_id' =>   $process_detail_id,
                'item_no' => "$part_num",  
                'line_search_id' => $line_search_id
            ] ;  
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
                'EngineeringAlert' =>  "$engineering_alert", 
                'ToWarehouseID' =>  "$warehouse_code", 
                'ToWarehouseDesc' =>  "$warehouse_desc", 
                'ToBinID' =>  "GENERAL", 
                'created_by' =>  "$my_name"  
            ] ; 

            ${'check_tag_id'} = DB::table('t510_production_tag')  
            ->where(${'index_data'})   
            ->count() ;
 
                if (${'check_tag_id'} == 0) {
                    ${'insert_tag'} = DB::table('t510_production_tag')   
                    ->insert(${'data_tag'}) ;
                } 
        }
        $result = $i ;
        return $result ;
    }

    public static function add_tag_label($job_num,$process_detail_id,$qty_plan,$qty_pack,$home_line_detail_id,$operator_name,$quality_name,$model_name,$part_num,$part_name,$cust_name,$production_date) {  
        $my_name = auth()->user()->username ; 
        $part_name = str_replace("__", ",",  $part_name) ;
        $engineering_alert = self::get_enggineering_alert($part_num) ;  
        $warehouse_to = explode("~", self::get_warehouse_to($job_num, $part_num)) ;  
        $warehouse_code = $warehouse_to[0] ;
        $warehouse_desc = $warehouse_to[1] ;
        
        $db_line = DB::table('t510_production_tag')
        ->where('job_num', '=', "$job_num")
        ->where('process_detail_id', '=', $process_detail_id)   
        ->where('item_no', '=', "$part_num")  
        ->where('is_delete', 0)
        ->select('line_search_id') 
        ->orderBy('line_search_id', 'DESC')
        ->limit(1)
        ->get() ; 

        if ($db_line->count() > 0) {
            foreach ($db_line AS $row) {
                $line_search_id = $row->line_search_id + 1 ;
              } 
        } else {
            $line_search_id = 1 ;
        }
         
        $standard_pallet = $qty_pack ;  
        $data_tag = [
            'job_num' =>  "$job_num",
            'process_detail_id' =>  $process_detail_id, 
            'line_search_id' =>  $line_search_id,
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
            'EngineeringAlert' =>  "$engineering_alert", 
            'ToWarehouseID' =>  "$warehouse_code", 
            'ToWarehouseDesc' =>  "$warehouse_desc", 
            'ToBinID' =>  "GENERAL", 
            'created_by' =>  $my_name  
        ] ; 

        $result = DB::table('t510_production_tag')   
        ->insert($data_tag) ; 
        return $result ;
    }

    public static function update_tag_label($job_num,$process_detail_id,$qty_plan,$qty_pack,$home_line_detail_id,$operator_name,$quality_name,$model_name,$part_num,$part_name,$cust_name,$production_date,$line_search_id) {  
        $my_name = auth()->user()->username ; 
        
        $part_name = str_replace("__", ",",  $part_name) ;  
        $engineering_alert = self::get_enggineering_alert($part_num) ;  
        $warehouse_to = explode("~", self::get_warehouse_to($job_num, $part_num)) ;  
        $warehouse_code = $warehouse_to[0] ;
        $warehouse_desc = $warehouse_to[1] ;

        $where_data_tag = [
            'job_num' =>  $job_num,
            'process_detail_id' =>  $process_detail_id, 
            'line_search_id' =>  $line_search_id,
            'item_no' => "$part_num",  
        ] ; 
        $data_tag = [
            'job_num' =>  $job_num,
            'process_detail_id' =>  $process_detail_id, 
            'line_search_id' =>  $line_search_id,
            'item_no' => "$part_num", 
            'item_name' =>  str_replace('~', ',', $part_name),   
            'cust_name' =>  $cust_name,  
            'doc_date' =>  "$production_date",   
            'home_line_detail_id' =>  "$home_line_detail_id",
            'qty_1' =>  $qty_plan,
            'qty_2' =>  $qty_pack,
            'operator_name' =>  $operator_name,
            'quality_name' =>  $quality_name,
            'production_date' =>  "$production_date", 
            'model_name' =>  "$model_name", 
            'EngineeringAlert' =>  "$engineering_alert", 
            // 'ToWarehouseID' =>  "$warehouse_code", 
            // 'ToWarehouseDesc' =>  "$warehouse_desc", 
            'ToBinID' =>  "GENERAL", 
            'created_by' =>  "$my_name"  
        ] ; 
        $update = DB::table('t510_production_tag')   
                ->where($where_data_tag)
                ->update($data_tag) ;
        return $update ;
    }
 
    public static function check_line($job_num,$process_detail_id,$part_num,$line_search_id) {   
        $result = DB::table('t510_production_tag')
        ->where('job_num', '=', "$job_num")
        ->where('process_detail_id', '=', $process_detail_id)  
        ->where('line_search_id', $line_search_id)
        ->where('item_no', '=', "$part_num")  
        ->where('is_delete', 0)->count() ;  
        return $result ;
    }

    public static function get_current_line_warehouse($job_num,$process_detail_id,$part_num,$line_search_id){
        $result = DB::table('t510_production_tag')
        ->where('job_num', '=', "$job_num")
        ->where('process_detail_id', '=', $process_detail_id)  
        ->where('line_search_id', $line_search_id)
        ->where('item_no', '=', "$part_num")  
        ->where('is_delete', 0)
        ->get();
        if ($result->count() > 0) {
            foreach ($result AS $row) {
                $wh = $row->ToWarehouseID."~".$row->ToWarehouseName ;
            }
        } else {
            $wh = '~' ;
        }
        return $wh ;
    }
    public static function get_detail_tag_label_id($job_num,$process_detail_id,$line_search_id,$part_num) 
    {     
        $db = DB::table('t510_production_tag as a')    
        ->leftJoin('t510_InventoryMove as b', function($join){
            $join->on('a.job_num', 'b.DocNumReference') ;
            $join->on('a.process_detail_id', 'b.DocNumReferenceLine') ;
            $join->on('a.line_search_id', 'b.DocNumReferenceLineRel') ;
            $join->on('a.item_no', 'b.PartNum') ;

        })
        ->where('a.job_num', "$job_num")
        ->where('a.process_detail_id', $process_detail_id)  
        ->where('a.line_search_id', $line_search_id)
        ->where('a.item_no', "$part_num")  
        ->having(DB::raw('COUNT(b.DocNumReference)'), 0)
        ->select('a.job_num', 'a.process_detail_id', 'a.line_search_id', 'a.item_no', 'a.item_name', 'a.cust_name', 'a.home_line_detail_id', 'a.qty_1', 'a.qty_2', 'a.log_date', 'a.production_date', 
        'a.operator_name', 'a.quality_name', 'a.remark_d', 'a.is_delete', 'a.doc_date', 'a.created_by', 
        'a.model_name', 'a.FromWarehouseID', 'a.FromWarehouseDesc', 'a.FromBinID', 'a.ToWarehouseID', 'a.ToWarehouseDesc', 'a.ToBinID', DB::raw('COUNT(b.DocNumReference) AS TotalScan'), 'a.EngineeringAlert') 
        ->groupBy('a.job_num', 'a.process_detail_id', 'a.line_search_id', 'a.item_no', 'a.item_name', 'a.cust_name', 'a.home_line_detail_id', 'a.qty_1', 'a.qty_2', 'a.log_date', 'a.production_date', 
        'a.operator_name', 'a.quality_name', 'a.remark_d', 'a.is_delete', 'a.doc_date', 'a.created_by', 
        'a.model_name', 'a.FromWarehouseID', 'a.FromWarehouseDesc', 'a.FromBinID', 'a.ToWarehouseID', 'a.ToWarehouseDesc', 'a.ToBinID', 'a.EngineeringAlert') ;  
        return $db ;
    }

    public static function get_total_detail_tag_label($job_num,$process_detail_id,$part_num) 
    {      
        $db = DB::table('t510_production_tag as a')    
        ->where('a.job_num', "$job_num")
        ->where('a.process_detail_id', $process_detail_id)   
        ->where('a.item_no', "$part_num") ;
        return $db ;
    }

}
