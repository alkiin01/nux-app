<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB ;

class DelConfirm extends Model
{
    use HasFactory;


    public static function get_transaction_list($range_date, $flow_id, $status, $vendor_id,  $search) 
    {      
        $year = '20'.substr($range_date,0,2) ;
        $month = substr($range_date,2,2) ;
        $start_date = $year.'-'.$month.'-01'; 
        $last_date = date('Y-m-t', strtotime($start_date)) ;  

        $result = DB::connection('sqlsrv2')->table("T500_Node as a")
        ->join("T500_Proc as b", function($join){
            $join->on("a.C010_TrcTypeID", "b.C010_TrcTypeID");
            $join->on("a.C011_Month", "b.C011_Month");
            $join->on("a.C000_SysID", "b.C000_SysID");
            $join->on("a.C050_Rev", "b.C050_Rev");
        }) 
        ->where('a.C010_TrcTypeID', 200) 
        ->whereBetween('a.C050_DocDate', [$start_date, $last_date])
        ->where('a.C017_UserGrpFlowID_From', $flow_id)
        ->where('a.C013_DraftReadyApprCancel', $status) ;  
        
        if (!empty($search) ) { 
            $result = $result->where(function($query) use ($search){
            $query->where('b.C050_DocNum','LIKE',"%$search%")
            ->orWhere('b.C050_ExtDocNum','LIKE',"%$search%")
            ->orWhere('b.C051_PONum','LIKE',"%$search%") ;
            }) ;
        }  

        if (!empty($search) ) { 
            $result = $result->where('a.C060_PartnerID', $vendor_id) ;
        } 

        $result = $result->select('b.*') ;
        return $result ;
    }

    public static function get_item_po_list($search_po_form, $search) 
    {     
        $str_po = explode("_", $search_po_form) ;
        $trc_type_id = $str_po[0] ;
        $month_id = $str_po[1] ;
        $trc_id = $str_po[2] ;
        $rev_id = $str_po[3] ;
 
        if($trc_type_id == 1100) {
            $db = DB::connection('sqlsrv2')->table('Q_G2_Proc') 
            ->where('C010_TrcTypeID', $trc_type_id)
            ->where('C011_Month', $month_id)
            ->where('C012_TrcID', $trc_id)  
            ->where('C050_Rev', $rev_id)  ;  
            if ($search != '') {
                $db = $db->where(function($join) use ($search) {
                    $join->where('ItemNum', 'LIKE',  '%' . $search . '%') ;
                    $join->orWhere('ItemName', 'LIKE',  '%' . $search . '%') ; 
                }) ;
            } ; 
        return $db ;
        } else if ($trc_type_id == 1130) {
            $db = DB::connection('sqlsrv2')->table('Q_G2_Proc_Subcon') 
            ->where('C010_TrcTypeID', $trc_type_id)
            ->where('C011_Month', $month_id)
            ->where('C012_TrcID', $trc_id)  
            ->where('C050_Rev', $rev_id)  ;  
            if ($search != '') {
                $db = $db->where(function($join) use ($search) {
                    $join->where('ItemNum_Req', 'LIKE',  '%' . $search . '%') ;
                    $join->orWhere('ItemName_Req', 'LIKE',  '%' . $search . '%') ; 
                }) ;
            } ; 
            return $db ;
        } else { 
            $db = DB::connection('sqlsrv2')->table('Q_G2_Proc_Other') 
            ->where('C010_TrcTypeID', $trc_type_id)
            ->where('C011_Month', $month_id)
            ->where('C012_TrcID', $trc_id)  
            ->where('C050_Rev', $rev_id)  ;  
            if ($search != '') {
                $db = $db->where(function($join) use ($search) {
                    $join->where('ItemNum', 'LIKE',  '%' . $search . '%') ;
                    $join->orWhere('ItemName', 'LIKE',  '%' . $search . '%') ; 
                }) ;
            } ; 
            return $db ;
        }
    }

    public static function get_detail_order_list($str_po, $str_di, $search)
    {      
        $trc_type_id_po = $str_po[0] ;
        $month_id_po = $str_po[1] ;
        $trc_id_po = $str_po[2] ;
        $rev_id_po = $str_po[3] ;

        $trc_type_id = $str_di[0] ;
        $month_id = $str_di[1] ;
        $trc_id = $str_di[2] ;
        $rev_id = $str_di[3] ;
 
        if($trc_type_id_po == 1100) {
            $db = DB::connection('sqlsrv2')->table('Q_G2_Proc_Subcon as a') 
            ->join('T500_Node as d', function($join){
                $join->on('a.C010_TrcTypeID', 'd.C010_TrcTypeID');
                $join->on('a.C011_Month', 'd.C011_Month');
                $join->on('a.C012_TrcID', 'd.C000_SysID');
                $join->on('a.C050_Rev', 'd.C050_Rev');
            })
            ->leftJoin('T500_500_2 as b', function($join) use($trc_type_id,$month_id,$trc_id,$rev_id) {
                $join->on('a.C010_TrcTypeID', 'b.C010_TrcTypeID');
                $join->on('a.C011_Month', 'b.C011_Month');
                $join->on('a.C012_TrcID', 'b.C000_SysID');
                $join->on('a.C050_Rev', 'b.C050_Rev');
                $join->on(DB::raw($trc_type_id), 'b.C034_TrcTypeID_To');
                $join->on(DB::raw($month_id), 'b.C035_Month_To');
                $join->on(DB::raw($trc_id), 'b.C036_TrcID_To');
                $join->on(DB::raw($rev_id), 'b.C050_Rev_To');
            })
            ->leftJoin('T510_Proc as c', function($join) use($trc_type_id,$month_id,$trc_id,$rev_id) {
                $join->on('b.C034_TrcTypeID_To', 'c.C010_TrcTypeID');
                $join->on('b.C035_Month_To', 'c.C011_Month');
                $join->on('b.C036_TrcID_To', 'c.C012_TrcID');
                $join->on('b.C050_Rev_To', 'c.C050_Rev');
                $join->on('c.C000_LineSrc', 'a.C000_SysID');
            }) 
            ->where('a.C010_TrcTypeID', $trc_type_id_po)
            ->where('a.C011_Month', $month_id_po)
            ->where('a.C012_TrcID', $trc_id_po)  
            ->where('a.C050_Rev', $rev_id_po)  ;   
            if ($search != '') {
                $db = $db->where(function($join) use ($search) {
                    $join->where('a.ItemNum', 'LIKE',  '%' . $search . '%') ;
                    $join->orWhere('a.ItemName', 'LIKE',  '%' . $search . '%') ; 
                }) ;
            } ; 
        } else if ($trc_type_id_po == 1130) {
            $db = DB::connection('sqlsrv2')->table('Q_G2_Proc_Subcon as a') 
            ->join('T500_Node as d', function($join){
                $join->on('a.C010_TrcTypeID', 'd.C010_TrcTypeID');
                $join->on('a.C011_Month', 'd.C011_Month');
                $join->on('a.C012_TrcID', 'd.C000_SysID');
                $join->on('a.C050_Rev', 'd.C050_Rev');
            })
            ->leftJoin('T500_500_2 as b', function($join) use($trc_type_id,$month_id,$trc_id,$rev_id) {
                $join->on('a.C010_TrcTypeID', 'b.C010_TrcTypeID');
                $join->on('a.C011_Month', 'b.C011_Month');
                $join->on('a.C012_TrcID', 'b.C000_SysID');
                $join->on('a.C050_Rev', 'b.C050_Rev');
                $join->on(DB::raw($trc_type_id), 'b.C034_TrcTypeID_To');
                $join->on(DB::raw($month_id), 'b.C035_Month_To');
                $join->on(DB::raw($trc_id), 'b.C036_TrcID_To');
                $join->on(DB::raw($rev_id), 'b.C050_Rev_To');
            })
            ->leftJoin('T510_Proc as c', function($join) use($trc_type_id,$month_id,$trc_id,$rev_id) {
                $join->on('b.C034_TrcTypeID_To', 'c.C010_TrcTypeID');
                $join->on('b.C035_Month_To', 'c.C011_Month');
                $join->on('b.C036_TrcID_To', 'c.C012_TrcID');
                $join->on('b.C050_Rev_To', 'c.C050_Rev');
                $join->on('c.C000_LineSrc', 'a.C000_SysID');
            }) 
            ->where('a.C010_TrcTypeID', $trc_type_id_po)
            ->where('a.C011_Month', $month_id_po)
            ->where('a.C012_TrcID', $trc_id_po)  
            ->where('a.C050_Rev', $rev_id_po)  ;   
            if ($search != '') {
                $db = $db->where(function($join) use ($search) {
                    $join->where('a.ItemNum_Req', 'LIKE',  '%' . $search . '%') ;
                    $join->orWhere('a.ItemName_Req', 'LIKE',  '%' . $search . '%') ; 
                }) ;
            } ;  
        } else { 
            $db = DB::connection('sqlsrv2')->table('Q_G2_Proc_Subcon as a') 
            ->join('T500_Node as d', function($join){
                $join->on('a.C010_TrcTypeID', 'd.C010_TrcTypeID');
                $join->on('a.C011_Month', 'd.C011_Month');
                $join->on('a.C012_TrcID', 'd.C000_SysID');
                $join->on('a.C050_Rev', 'd.C050_Rev');
            })
            ->leftJoin('T500_500_2 as b', function($join) use($trc_type_id,$month_id,$trc_id,$rev_id) {
                $join->on('a.C010_TrcTypeID', 'b.C010_TrcTypeID');
                $join->on('a.C011_Month', 'b.C011_Month');
                $join->on('a.C012_TrcID', 'b.C000_SysID');
                $join->on('a.C050_Rev', 'b.C050_Rev');
                $join->on(DB::raw($trc_type_id), 'b.C034_TrcTypeID_To');
                $join->on(DB::raw($month_id), 'b.C035_Month_To');
                $join->on(DB::raw($trc_id), 'b.C036_TrcID_To');
                $join->on(DB::raw($rev_id), 'b.C050_Rev_To');
            })
            ->leftJoin('T510_Proc as c', function($join) use($trc_type_id,$month_id,$trc_id,$rev_id) {
                $join->on('b.C034_TrcTypeID_To', 'c.C010_TrcTypeID');
                $join->on('b.C035_Month_To', 'c.C011_Month');
                $join->on('b.C036_TrcID_To', 'c.C012_TrcID');
                $join->on('b.C050_Rev_To', 'c.C050_Rev');
                $join->on('c.C000_LineSrc', 'a.C000_SysID');
            }) 
            ->where('a.C010_TrcTypeID', $trc_type_id_po)
            ->where('a.C011_Month', $month_id_po)
            ->where('a.C012_TrcID', $trc_id_po)  
            ->where('a.C050_Rev', $rev_id_po)  ;   
            if ($search != '') {
                $db = $db->where(function($join) use ($search) {
                    $join->where('a.ItemNum', 'LIKE',  '%' . $search . '%') ;
                    $join->orWhere('a.ItemName', 'LIKE',  '%' . $search . '%') ; 
                }) ;
            } ;  
        }
        return $db->select('a.*','a.C111_QtyBal AS BalPOtoGR', 'a.C111_QtyBal2 AS BalPO','a.C110_Qty AS QtyPO','c.C111_QtyBal AS BalDI','c.C110_Qty AS QtyDI','c.C000_SysID AS C000_SysID_DI', 'c.TotalPallet') ;
    }

    public static function get_ref_doc($str_di)
    {      
        $trc_type_id_po = $str_di[0] ;
        $month_id_po = $str_di[1] ;
        $trc_id_po = $str_di[2] ;
        $rev_id_po = $str_di[3] ;

        $trc_type_id = $str_di[4] ;
        $month_id = $str_di[5] ;
        $trc_id = $str_di[6] ;
        $rev_id = $str_di[7] ;
        $line_id = $str_di[9] ;
        $line_id_po = $str_di[8] ;

        $db = DB::connection('sqlsrv2')->table('T510_Proc as a') 
        ->join('T500_Node as d', function($join){
            $join->on('a.C010_TrcTypeID', 'd.C010_TrcTypeID');
            $join->on('a.C011_Month', 'd.C011_Month');
            $join->on('a.C012_TrcID', 'd.C000_SysID');
            $join->on('a.C050_Rev', 'd.C050_Rev');
        })
        ->leftJoin('T500_500_2 as b', function($join) use($trc_type_id,$month_id,$trc_id,$rev_id) {
            $join->on('a.C010_TrcTypeID', 'b.C010_TrcTypeID');
            $join->on('a.C011_Month', 'b.C011_Month');
            $join->on('a.C012_TrcID', 'b.C000_SysID');
            $join->on('a.C050_Rev', 'b.C050_Rev');
            $join->on(DB::raw($trc_type_id), 'b.C034_TrcTypeID_To');
            $join->on(DB::raw($month_id), 'b.C035_Month_To');
            $join->on(DB::raw($trc_id), 'b.C036_TrcID_To');
            $join->on(DB::raw($rev_id), 'b.C050_Rev_To');
        })
        ->leftJoin('T510_Proc as c', function($join) use($line_id_po) {
            $join->on('b.C034_TrcTypeID_To', 'c.C010_TrcTypeID');
            $join->on('b.C035_Month_To', 'c.C011_Month');
            $join->on('b.C036_TrcID_To', 'c.C012_TrcID');
            $join->on('b.C050_Rev_To', 'c.C050_Rev');
            $join->on(DB::raw($line_id_po), 'c.C000_LineSrc');
        }) 
        ->where('a.C010_TrcTypeID', $trc_type_id_po)
        ->where('a.C011_Month', $month_id_po)
        ->where('a.C012_TrcID', $trc_id_po)  
        ->where('a.C050_Rev', $rev_id_po) ;    

        return $db->select(DB::raw("$trc_type_id_po as trc_type_id_po"), DB::raw("$month_id_po as month_id_po"), DB::raw("$trc_id_po as trc_id_po"), DB::raw("$rev_id_po as rev_id_po"), DB::raw("$trc_type_id as trc_type_id"), DB::raw("$month_id as month_id"), DB::raw("$trc_id as trc_id"), DB::raw("$rev_id as rev_id"), 'a.C000_SysID as line_id_po', 'c.C000_SysID as line_id_di', 'a.C100_ItemIntID as item_id')->get() ;
    }


    public static function get_id_po($str_di) 
    {     
        $trc_type_id = $str_di[0] ;
        $month_id = $str_di[1] ;
        $trc_id = $str_di[2] ;
        $rev_id = $str_di[3] ;
        $db = DB::connection('sqlsrv2')->table('T500_500_2 as a') 
        ->where('a.C034_TrcTypeID_To', $trc_type_id)
        ->where('a.C035_Month_To', $month_id)
        ->where('a.C036_TrcID_To', $trc_id)
        ->where('a.C050_Rev_To', $rev_id)
        ->select("a.*")->get() ;  
        
        if ($db->count() > 0) {
            foreach($db as $item){
                $result = $item->C010_TrcTypeID.'_'.$item->C011_Month.'_'.$item->C000_SysID.'_'.$item->C050_Rev ;
            }
        } else {
            $result = '0_0_0_0' ;
        } 
        return $result ;
    } 
    

    public static function get_listing_po($trc_type_id, $vendor_id, $offset, $resultCount, $flow_id, $range_date, $searchTerm) 
    {     
        $year = '20'.substr($range_date,0,2) ;
        $month = substr($range_date,2,2) ;
        $start_date = $year.'-'.$month.'-01'; 
        $last_date = date('Y-m-t', strtotime($start_date)) ;  

        $db = DB::connection('sqlsrv2')->table('T500_Node as a')
        ->join('T500_AsSource as b', function($join){
            $join->on("a.C010_TrcTypeID", "b.C010_TrcTypeID");
            $join->on("a.C011_Month", "b.C011_Month");
            $join->on("a.C000_SysID", "b.C000_SysID");
            $join->on("a.C050_Rev", "b.C050_Rev");
        })
        ->whereBetween('a.C050_DocDate', [$start_date, $last_date])
        ->where('a.C017_UserGrpFlowID_From', $flow_id)
        ->where('a.C050_DocNum', 'LIKE', '%' . "PO" . '%')
        ->where('b.C012_TrcOpenClose1', 1) ;
        if ($searchTerm != '') {
            $db = $db->where('a.C050_DocNum', 'LIKE',  '%' . $searchTerm . '%') ;
        }
        if ($vendor_id != '') {
            $db = $db->where('a.C060_PartnerID', $vendor_id)   ;
        } 
        $result = $db->orderBy('a.C011_Month', 'DESC')->orderBy('a.C000_SysID', 'DESC')->offset($offset)->limit($resultCount) 
        ->get([DB::raw("CONCAT (a.C010_TrcTypeID,'_',a.C011_Month,'_',a.C000_SysID,'_',a.C050_Rev) AS id"), DB::raw('a.C050_DocNum AS text')]) ;  
        return $result ;
    } 

    public static function get_count_listing_po($trc_type_id, $vendor_id, $flow_id, $range_date, $searchTerm) 
    {     
        $year = '20'.substr($range_date,0,2) ;
        $month = substr($range_date,2,2) ;
        $start_date = $year.'-'.$month.'-01'; 
        $last_date = date('Y-m-t', strtotime($start_date)) ;  

        $db = DB::connection('sqlsrv2')->table('T500_Node as a')
        ->join('T500_AsSource as b', function($join){
            $join->on("a.C010_TrcTypeID", "b.C010_TrcTypeID");
            $join->on("a.C011_Month", "b.C011_Month");
            $join->on("a.C000_SysID", "b.C000_SysID");
            $join->on("a.C050_Rev", "b.C050_Rev");
        })
        ->whereBetween('a.C050_DocDate', [$start_date, $last_date])
        ->where('b.C017_UserGrpFlowID', $flow_id)
        ->where('a.C050_DocNum', 'LIKE', '%' . "PO" . '%')
        ->where('a.C010_TrcTypeID', $trc_type_id)
        ->where('b.C012_TrcOpenClose1', 1) ;
        if ($searchTerm != '') {
            $db = $db->where('a.C050_DocNum', 'LIKE',  '%' . $searchTerm . '%') ;
        }
        if ($vendor_id != '') {
            $db = $db->where('a.C060_PartnerID', $vendor_id)   ;
        } 
        $result = $db->count() ;  
        return $result ;
    }

    public static function find_trc_name($trc_type_id, $flow_id){  
        $db = DB::connection('sqlsrv3')->table('t100_transaction_type') 
        ->where('trc_type_id', '=', $trc_type_id) 
        ->where('flow_id', '=', $flow_id) 
        ->select('trc_code')
        ->get() ;
        $r = $db->count();
        if($r>0){
        foreach($db as $h){
        $result = $h->trc_code  ; }
        }else{
        $result = '' ; }
        return $result ;
    }

    public static function find_trc_id_proc_head($trc_type_id,$mont_id)
    {  
        $db = DB::connection('sqlsrv2')->table('T_000_Counter') 
        ->where('TableName', '=', 'T500_Proc')
        ->where('YearMonth', '=', $mont_id)
        ->where('Opsi_1', '=', $trc_type_id)
        ->select('CurrentSysID')
        ->get() ;
        $r = $db->count();
        if($r>0){
        foreach($db as $h){
        $result = $h->CurrentSysID + 1 ; }
        }else{
        $result = 1 ; }
        return $result ;
    } 

    public static function head_detail_properties ($trc_type_id, $flow_id, $trc_type_id_b) 
    {     
        return DB::connection('sqlsrv3')->table('t100_transaction_type') 
            ->where('status_id', '=', 3)
            ->where('category_id', '=', 5)
            ->where('trc_type_id', '=', $trc_type_id)
            ->where('flow_id', '=', $flow_id)
            ->where('trc_type_id_b', '=', $trc_type_id_b)
            ->select('t100_transaction_type.*')
        ->get() ;
    }

    public static function insert_t500_proc ($detail) 
    {     
        return DB::connection('sqlsrv2')->table('T500_Proc')->insert($detail); 
    }

    public static function insert_t500_node ($detail_node) 
    {     
        return DB::connection('sqlsrv2')->table('T500_Node')->insert($detail_node);  
    }

    public static function check_doc_counter ($c010) 
    {     
        return DB::connection('sqlsrv2')->table('T_100_DocNum1')->where('C010','=', $c010)->where('C020', '=', date('Y'))->count(); 
    }

    public static function update_t100_docnum1 ($c010,$detail_counter_2)
    {     
        return DB::connection('sqlsrv2')->table('T_100_DocNum1')->where('C020','=',date('Y'))->where('C010','=',$c010)->update($detail_counter_2) ; 
    }

    public static function insert_t100_docnum1 ($detail_counter_2)
    {     
        return DB::connection('sqlsrv2')->table('T_100_DocNum1')->insert($detail_counter_2);
    }

    public static function insert_t500_500_2 ($detail_connecting)
    {     
        return DB::connection('sqlsrv2')->table('T500_500_2')->insert($detail_connecting);
    }

    public static function check_doc_counter_2 ($trc_type_id, $month_id)
    {     
        return DB::connection('sqlsrv2')->table('T_000_Counter')
        ->where('TableName','=','T500_Proc')
        ->where('Opsi_1','=', $trc_type_id)
        ->where('YearMonth','=',$month_id)
        ->count();
    }

    public static function check_shipnum ($str_di, $shipnum)
    {     
        $trc_type_id = $str_di[0] ;
        $month_id = $str_di[1] ;
        $trc_id = $str_di[2] ;
        $rev_id = $str_di[3] ;
        return DB::connection('sqlsrv2')->table('T500_Node') 
        ->where('C010_TrcTypeID', $trc_type_id)
        ->where('C011_Month', '<>', $month_id)
        ->where('C000_SysID', '<>', $trc_id)
        ->where('C050_Rev', '<>', $rev_id)
        ->where('C050_ExtDocNum', "$shipnum")
        ->get()->count();
    }

    public static function update_head ($detail, $detail_node, $index)
    {     
        DB::connection('sqlsrv2')->table('T500_Node')->where($index)->update($detail_node) ;
        return DB::connection('sqlsrv2')->table('T500_Proc')->where($index)->update($detail) ; 
    }

    public static function update_as_source ($detail, $index)
    {     
        return DB::connection('sqlsrv2')->table('T500_AsSource')->where($index)->update($detail) ; 
    }

    public static function update_t_000_Counter($trc_type_id, $month_id, $detail_counter)
    {     
        return DB::connection('sqlsrv2')->table('T_000_Counter')
        ->where('TableName','=','T500_Proc')
        ->where('Opsi_1','=',$trc_type_id)
        ->where('YearMonth','=',$month_id)
        ->update($detail_counter);
    }

    public static function insert_t_000_Counter($detail_counter)
    {     
        return DB::connection('sqlsrv2')->table('T_000_Counter')->insert($detail_counter);
    }


    public static function find_doc_num_proc($current_year, $c010){  
        $db = DB::connection('sqlsrv2')->table('T_100_DocNum1')  
        ->where('C020', '=', $current_year)
        ->where('C010', '=', $c010)
        ->select('C030 AS last')
        ->get() ;
        $r = $db->count(); 
        if($db->count() > 0 ){
        foreach($db as $t){
        $nextNoTransaksi = $t->last + 1 ;
        $result = $nextNoTransaksi; }
        }else{ 
        $result = 1 ; 
        }
        return $result ; 
    } 

    public static function get_head_properties($str_po) 
    {     
        $trc_type_id = $str_po[0] ;
        $month_id = $str_po[1] ;
        $trc_id = $str_po[2] ;
        $rev_id = $str_po[3] ;
        return DB::connection('sqlsrv2')->table('Q_G1_Proc as a')  
        ->where('a.C010_TrcTypeID', $trc_type_id)
        ->where('a.C011_Month', $month_id)
        ->where('a.C000_SysID', $trc_id)  
        ->where('a.C050_Rev', $rev_id)
        ->select('a.*')
        ->get() ;
    }

    public static function get_total_order($str_di) 
    {     
        $trc_type_id = $str_di[4] ;
        $month_id = $str_di[5] ;
        $trc_id = $str_di[6] ;
        $rev_id = $str_di[7] ;
        $line_id = $str_di[9] ;
        $db = DB::connection('sqlsrv2')->table('T510_Proc as a')  
        ->where('a.C010_TrcTypeID', $trc_type_id)
        ->where('a.C011_Month', $month_id)
        ->where('a.C012_TrcID', $trc_id)  
        ->where('a.C050_Rev', $rev_id)
        ->where('a.C000_SysID', $line_id)
        ->select('C110_Qty')
        ->get() ; 
        if($db->count() > 0 ){
            foreach($db as $t){
                $result = $t->C110_Qty ; 
            }
        } else { 
            $result = 0 ; 
        }
        return $result ; 
    }

    public static function get_total_label($str_di) 
    {     
        $trc_type_id = $str_di[4] ;
        $month_id = $str_di[5] ;
        $trc_id = $str_di[6] ; 
        $line_id = $str_di[9] ;
        $db = DB::connection('sqlsrv2')->table('T511_Proc as a')  
        ->where('a.C010_TrcTypeID', $trc_type_id)
        ->where('a.C011_Month', $month_id)
        ->where('a.C012_TrcID', $trc_id)   
        ->where('a.C000_LineSrc', $line_id)
        ->select(DB::raw("SUM(C110_Qty) AS qty"))
        ->get() ; 
        if($db->count() > 0 ){
            foreach($db as $t){
                $result = $t->qty ; 
            }
        } else { 
            $result = 0 ; 
        }
        return $result ; 
    }

    public static function get_total_order_by_detail($trc_type_id, $month_id, $trc_id, $line_id)
    {      
        $db = DB::connection('sqlsrv2')->table('T510_Proc as a') 
        ->join('T500_Node as b', function($join) {
            $join->on('a.C010_TrcTypeID', 'b.C010_TrcTypeID');
            $join->on('a.C011_Month', 'b.C011_Month');
            $join->on('a.C012_TrcID', 'b.C000_SysID');
            $join->on('a.C050_Rev', 'b.C050_Rev');
        }) 
        ->where('a.C010_TrcTypeID', $trc_type_id)
        ->where('a.C011_Month', $month_id)
        ->where('a.C012_TrcID', $trc_id)   
        ->where('a.C000_SysID', $line_id)
        ->select('C110_Qty')
        ->get() ; 
        if($db->count() > 0 ){
            foreach($db as $t){
                $result = $t->C110_Qty ; 
            }
        } else { 
            $result = 0 ; 
        }
        return $result ; 
    }

    public static function get_total_label_by_detail($trc_type_id, $month_id, $trc_id, $line_id)
    {      
        $db = DB::connection('sqlsrv2')->table('T511_Proc as a')  
        ->where('a.C010_TrcTypeID', $trc_type_id)
        ->where('a.C011_Month', $month_id)
        ->where('a.C012_TrcID', $trc_id)   
        ->where('a.C000_LineSrc', $line_id)
        ->select(DB::raw("SUM(C110_Qty) AS qty"))
        ->get() ; 
        if($db->count() > 0 ){
            foreach($db as $t){
                $result = $t->qty ; 
            }
        } else { 
            $result = 0 ; 
        }
        return $result ; 
    }

    public static function get_qty_before_label($trc_type_id, $month_id, $trc_id, $line_id, $line_search_id)
    {      
        $db = DB::connection('sqlsrv2')->table('T511_Proc as a')  
        ->where('a.C010_TrcTypeID', $trc_type_id)
        ->where('a.C011_Month', $month_id)
        ->where('a.C012_TrcID', $trc_id)   
        ->where('a.C000_SysID', $line_id)
        ->where('a.C000_LineSrc', $line_search_id)
        ->select(DB::raw("SUM(C110_Qty) AS qty"))
        ->get() ; 
        if($db->count() > 0 ){
            foreach($db as $t){
                $result = $t->qty ; 
            }
        } else { 
            $result = 0 ; 
        }
        return $result ; 
    }

    public static function post_header($trc_type_id_to, $month_id_to, $trc_id_to, $rev_id_to, $type_src_id, $act_mgr_id, $user_id, $trc_type_id, $month_id, $trc_id, $rev_id) 
    {      
        $detail_po =  DB::connection('sqlsrv2')->table('Q_G1_Proc as a')  
        ->where('a.C010_TrcTypeID', $trc_type_id)
        ->where('a.C011_Month', $month_id)
        ->where('a.C000_SysID', $trc_id)  
        ->where('a.C050_Rev', $rev_id)
        ->select('a.*')
        ->get() ; 
        $data = [] ;
        foreach ($detail_po as $key => $val) {  
            $key = $val ;
        }

        $post_proc = 
        dd($data);
    }

    public static function get_detail_balance_po($trc_type_id, $month_id, $trc_id, $rev_id, $line_id) 
    {      
        $db = DB::connection('sqlsrv2')->table('T510_Proc as a') 
        ->where('a.C010_TrcTypeID', $trc_type_id)
        ->where('a.C011_Month', $month_id)
        ->where('a.C012_TrcID', $trc_id)
        ->where('a.C050_Rev', $rev_id)
        ->where('a.C000_SysID', $line_id)
        ->select("a.C111_QtyBal2")->get() ;   
        if ($db->count() > 0) {
            foreach($db as $item){
                $result = $item->C111_QtyBal2 ;
            }
        } else {
            $result = 0 ;
        } 
        return $result ;
    } 

    public static function get_detail_qty_di($trc_type_id, $month_id, $trc_id, $rev_id, $line_id) 
    {      
        $db = DB::connection('sqlsrv2')->table('T510_Proc as a') 
        ->where('a.C010_TrcTypeID', $trc_type_id)
        ->where('a.C011_Month', $month_id)
        ->where('a.C012_TrcID', $trc_id)
        ->where('a.C050_Rev', $rev_id)
        ->where('a.C000_SysID', $line_id)
        ->select("a.C110_Qty")->get() ;   
        if ($db->count() > 0) {
            foreach($db as $item){
                $result = $item->C110_Qty ;
            }
        } else {
            $result = 0 ;
        } 
        return $result ;
    } 

    public static function get_detail_line_id($trc_type_id, $month_id, $trc_id, $rev_id) 
    {      
        $db = DB::connection('sqlsrv2')->table('T510_Proc as a') 
        ->where('a.C010_TrcTypeID', $trc_type_id)
        ->where('a.C011_Month', $month_id)
        ->where('a.C012_TrcID', $trc_id)
        ->where('a.C050_Rev', $rev_id) 
        ->select(DB::raw("MAX(a.C000_SysID) as line_id"))->get() ;   
        if ($db->count() > 0) {
            foreach($db as $item){
                $result = ($item->line_id + 1) ;
            }
        } else {
            $result = 1 ;
        } 
        return $result ;
    } 

    public static function get_label_line_id($trc_type_id, $month_id, $trc_id) 
    {      
        $db = DB::connection('sqlsrv2')->table('T511_Proc as a') 
        ->where('a.C010_TrcTypeID', $trc_type_id)
        ->where('a.C011_Month', $month_id)
        ->where('a.C012_TrcID', $trc_id) 
        ->select(DB::raw("MAX(a.C000_SysID) as line_id"))->get() ;   
        if ($db->count() > 0) {
            foreach($db as $item){
                $result = ($item->line_id + 1) ;
            }
        } else {
            $result = 1 ;
        } 
        return $result ;
    }

    public static function update_balance_po($trc_type_id, $month_id, $trc_id, $rev_id, $line_id, $detail_po) 
    {      
        return DB::connection('sqlsrv2')->table('T510_Proc') 
        ->where('C010_TrcTypeID', $trc_type_id)
        ->where('C011_Month', $month_id)
        ->where('C012_TrcID', $trc_id)
        ->where('C050_Rev', $rev_id) 
        ->where('C000_SysID', $line_id) 
        ->update($detail_po) ;    
    } 

    public static function update_di($trc_type_id_po, $month_id_po, $trc_id_po, $rev_id_po, $line_id_po, $trc_type_id, $month_id, $trc_id, $rev_id, $line_id, $detail) 
    {
        
        $qty = $detail['C110_Qty'] ;  
        if ($qty > 0) {
            $check_di = DB::connection('sqlsrv2')->table('T510_Proc')
            ->where('C010_TrcTypeID', $trc_type_id)
            ->where('C011_Month', $month_id)
            ->where('C012_TrcID', $trc_id)
            ->where('C050_Rev', $rev_id) 
            ->where('C000_SysID', $line_id) 
            ->get()->count();

            if ($check_di>0) {
                DB::connection('sqlsrv2')->table('T510_Proc') 
                ->where('C010_TrcTypeID', $trc_type_id)
                ->where('C011_Month', $month_id)
                ->where('C012_TrcID', $trc_id)
                ->where('C050_Rev', $rev_id) 
                ->where('C000_SysID', $line_id) 
                ->update($detail) ;    
            } else {
                $db_detail_po = DB::connection('sqlsrv2')->table('T510_Proc') 
                ->where('C010_TrcTypeID', $trc_type_id_po)
                ->where('C011_Month', $month_id_po)
                ->where('C012_TrcID', $trc_id_po)
                ->where('C050_Rev', $rev_id_po) 
                ->where('C000_SysID', $line_id_po) 
                ->get() ; 

            
                foreach ($db_detail_po as $row) {
                    $detail_po['C010_TrcTypeID'] = $trc_type_id ;
                    $detail_po['C011_Month'] = $month_id ;
                    $detail_po['C012_TrcID'] = $trc_id ;
                    $detail_po['C050_Rev'] = $rev_id ;
                    $detail_po['C000_SysID'] = $line_id ;
                    $detail_po['C000_LineSrc'] = $row->C000_SysID ; 
                    $detail_po['C100_ItemIntID'] = $row->C100_ItemIntID ;
                    $detail_po['C100_ItemExtID'] = $row->C100_ItemExtID ;
                    $detail_po['C102_PriceInt'] = $row->C102_PriceInt ;
                    $detail_po['C110_Qty'] = $qty ;
                    $detail_po['C110_Qty2'] = $qty * $row->C150_UnitConvertion ;
                    $detail_po['C111_QtyBal'] = $qty ;
                    $detail_po['C111_QtyBal2'] = $qty ;
                    $detail_po['C125_AmountInt'] = $qty * $row->C150_UnitConvertion * $row->C102_PriceInt ;
                    $detail_po['C130_MLedgerID'] = $row->C130_MLedgerID ;
                    $detail_po['C135_Rate'] = $row->C135_Rate ;
                    $detail_po['C140_KategoryID'] = $row->C140_KategoryID ;
                    $detail_po['C150_UnitConvertion'] = $row->C150_UnitConvertion ;
                    $detail_po['C160_PRType'] = $row->C160_PRType ;
                    $detail_po['PostCategoryID'] = $row->PostCategoryID ;
                    $detail_po['C128_NetAmount'] = $qty * $row->C150_UnitConvertion * $row->C102_PriceInt ;
                    DB::connection('sqlsrv2')->table('T510_Proc') 
                    ->where('C010_TrcTypeID', $trc_type_id)
                    ->where('C011_Month', $month_id)
                    ->where('C012_TrcID', $trc_id)
                    ->where('C050_Rev', $rev_id) 
                    ->where('C000_SysID', $line_id) 
                    ->insert($detail_po) ; 
                }     
            }  
        } else {
            $check_di = DB::connection('sqlsrv2')->table('T510_Proc')
            ->where('C010_TrcTypeID', $trc_type_id)
            ->where('C011_Month', $month_id)
            ->where('C012_TrcID', $trc_id)
            ->where('C050_Rev', $rev_id) 
            ->where('C000_SysID', $line_id) 
            ->delete(); 
        }
    } 

    public static function check_head_di($trc_type_id,$month_id,$trc_id,$rev_id)
    {  
        $db = DB::connection('sqlsrv2')->table('T500_Proc') 
        ->where('C010_TrcTypeID', '=', $trc_type_id)
        ->where('C011_Month', '=', $month_id)
        ->where('C000_SysID', '=', $trc_id)
        ->where('C050_Rev', '=', $rev_id)  
        ->where(function($query){
        $query->whereNull('C050_ExtDocDate') 
            ->orwhereNull('C050_ExtDocNum')  ;
        })->get() ;    
        $r = $db->count(); 
        $result = 0 ; 
        if($r>0){ 
        $result = 0 ;
        }else{
        $result = 1 ; 
        }
        
        return $result ;  
    }

    public static function check_detail_di($trc_type_id,$month_id,$trc_id,$rev_id)
    {  
        $db = DB::connection('sqlsrv2')->table('T510_Proc') 
        ->where('C010_TrcTypeID', '=', $trc_type_id)
        ->where('C011_Month', '=', $month_id)
        ->where('C012_TrcID', '=', $trc_id)
        ->where('C050_Rev', '=', $rev_id) 
        ->where(function($query){
            $query->where('C110_Qty', '>', 0)  ;
        }) 
        ->get() ;   
        $r = $db->count(); 
        if($r>0){ 
            $result = 1 ;
        }else{
            $result = 0 ; 
        }
        return $result ;  
    }

    public static function find_as_source_properties($trc_type_id, $flow_id){  
        $db = DB::connection('sqlsrv3')->table('t100_transaction_type') 
        ->where('trc_type_id', '=', $trc_type_id) 
        ->where('flow_id', '=', $flow_id) 
        ->where('trc_name', 'LIKE', '%DI%') 
        ->select('act_mgr_id', 'flow_id', 'type_src_id', 'type_src_id_next_1', 'act_mgr_dest_id_1', 'type_src_id_next_2', 'act_mgr_dest_id_2', 'trc_type_id_b')
        ->limit(1)
        ->get() ;   
        $r = $db->count(); 
        if($r>0){
        foreach ($db as $h) {
            $result = $h->act_mgr_id."_".$h->flow_id."_".$h->type_src_id."_".$h->type_src_id_next_1."_".$h->act_mgr_dest_id_1."_".$h->type_src_id_next_2."_".$h->act_mgr_dest_id_2."_".$h->trc_type_id_b  ;
        }
        }else{
            $result = "0_0_0_0_0_0_0_0" ; 
        } 
        return $result ;  
    }

    public static function generate_as_source($trc_type_id,$month_id,$trc_id,$rev_id, $flow_id) {  
        DB::connection('sqlsrv2')->table('T500_AsSource') 
        ->where('C010_TrcTypeID', '=', $trc_type_id)
        ->where('C011_Month', '=', $month_id)
        ->where('C000_SysID', '=', $trc_id)
        ->where('C050_Rev', '=', $rev_id) 
        ->delete();
        $prop = explode("_", self::find_as_source_properties($trc_type_id, $flow_id)) ; 
        return DB::connection('sqlsrv2')->update("
        INSERT INTO T500_AsSource (C010_TrcTypeID, C011_Month, C000_SysID, C050_Rev, C012_TrcOpenClose, C012_Responded, C013_TrcTypeSrcID, C014_ActMgrID, C016_ContentFlowID, 
        C017_UserGrpFlowID, C018_FlowTypeID, C019_ActMgrDestID, C045_UserID, C045_DTime, C050_DocNum, C050_DocDate, C060_PartnerID) 
        SELECT C010_TrcTypeID, C011_Month, C000_SysID, C050_Rev, 1, 1, $prop[3], C014_ActMgrID, 4, C017_UserGrpFlowID_To, 3, $prop[4], C045_UserID, C045_DTime, 
        C050_DocNum, C050_DocDate, C060_PartnerID 
        FROM T500_Node  
        WHERE C010_TrcTypeID=$trc_type_id AND C011_Month=$month_id AND C000_SysID=$trc_id AND C050_Rev=$rev_id  
        ") ;    
    }

    public static function delete_as_source($trc_type_id,$month_id,$trc_id,$rev_id) {  
        DB::connection('sqlsrv2')->table('T500_AsSource') 
        ->where('C010_TrcTypeID', '=', $trc_type_id)
        ->where('C011_Month', '=', $month_id)
        ->where('C000_SysID', '=', $trc_id)
        ->where('C050_Rev', '=', $rev_id) 
        ->delete(); 
    }

    public static function insert_label($detail) {  
        return DB::connection('sqlsrv2')->table('T511_Proc')->insert($detail) ; 
    }

    public static function update_label($detail, $index) {  
        return DB::connection('sqlsrv2')->table('T511_Proc')->where($index)->update($detail) ; 
    }

    public static function delete_label($index) {  
        return DB::connection('sqlsrv2')->table('T511_Proc')->where($index)->delete() ; 
    }
    
    public static function create_proc($detail) {  
        return DB::connection('sqlsrv2')->table('T500_Proc')->insert($detail) ; 
    }

    public static function create_node($detail) {  
        return DB::connection('sqlsrv2')->table('T500_Node')->insert($detail) ; 
    }

    public static function create_node_history($detail) {  
        return DB::connection('sqlsrv2')->table('T500_NodeHistory')->insert($detail) ; 
    } 

    public static function get_detail_tag_label($trc_type_id,$month_id,$trc_id,$line_id) 
    {    
        $db = DB::connection('sqlsrv2')->table('T511_Proc as a')   
        ->where('a.C010_TrcTypeID', $trc_type_id)
        ->where('a.C011_Month', $month_id) 
        ->where('a.C012_TrcID', $trc_id)  
        ->where('a.C000_LineSrc', $line_id)  
        ->select('a.*') ;      
        return $db ;
    }

    public static function update_total_pallet($str_di) 
    {     
        $trc_type_id = $str_di[4] ;
        $month_id = $str_di[5] ;
        $trc_id = $str_di[6] ;
        $rev_id = $str_di[7] ;
        $line_id = $str_di[9] ;
        $total_rows = DB::connection('sqlsrv2')->table('T511_Proc as a')   
        ->where('a.C010_TrcTypeID', $trc_type_id)
        ->where('a.C011_Month', $month_id) 
        ->where('a.C012_TrcID', $trc_id)  
        ->where('a.C000_LineSrc', $line_id)  
        ->select('a.*')->get()->count() ; 

        $result = DB::connection('sqlsrv2')->table('T510_Proc')  
        ->where('C010_TrcTypeID', $trc_type_id)
        ->where('C011_Month', $month_id)
        ->where('C012_TrcID', $trc_id)  
        ->where('C050_Rev', $rev_id)
        ->where('C000_SysID', $line_id)
        ->update([
            'TotalPallet' => $total_rows
        ]) ;  
        return $result ; 
    }

    public static function update_total_pallet_by_detail($trc_type_id,$month_id,$trc_id,$line_id) 
    {      
        $total_rows = DB::connection('sqlsrv2')->table('T511_Proc as a')   
        ->where('a.C010_TrcTypeID', $trc_type_id)
        ->where('a.C011_Month', $month_id) 
        ->where('a.C012_TrcID', $trc_id)   
        ->where('a.C000_LineSrc', $line_id)  
        ->select('a.*')->get()->count() ; 

        $result = DB::connection('sqlsrv2')->table('T510_Proc as a')  
        ->join('T500_Node as b', function($join) {
            $join->on('a.C010_TrcTypeID', 'b.C010_TrcTypeID');
            $join->on('a.C011_Month', 'b.C011_Month');
            $join->on('a.C012_TrcID', 'b.C000_SysID');
            $join->on('a.C050_Rev', 'b.C050_Rev');
        }) 
        ->where('a.C010_TrcTypeID', $trc_type_id)
        ->where('a.C011_Month', $month_id)
        ->where('a.C012_TrcID', $trc_id)   
        ->where('a.C000_SysID', $line_id)
        ->update([
            'a.TotalPallet' => $total_rows
        ]) ;  
        return $result ; 
    }

    public static function generate_tag_label($trc_type_id,$month_id,$trc_id,$rev_id,$line_id,$line_search_id,$qty_pack,$serial_number) {  
        $my_name = auth()->user()->username ;
        $clear = DB::connection('sqlsrv2')->table('T511_Proc')
        ->where('C010_TrcTypeID', '=', $trc_type_id)
        ->where('C011_Month', '=', $month_id) 
        ->where('C012_TrcID', '=', $trc_id)  
        ->where('C000_LineSrc', '=', $line_search_id)  
        ->delete();

        $check_db = DB::connection('sqlsrv2')->table('T500_Proc as a')  
        ->join('T510_Proc as b', function($join) {
            $join->on('a.C010_TrcTypeID', 'b.C010_TrcTypeID');
            $join->on('a.C011_Month', 'b.C011_Month') ;
            $join->on('a.C000_SysID', 'b.C012_TrcID') ; 
            $join->on('a.C050_Rev', 'b.C050_Rev') ; 
        })
        ->join('T500_Node as c', function($join) { 
            $join->on('b.C010_TrcTypeID', 'c.C010_TrcTypeID') ; 
            $join->on('b.C011_Month', 'c.C011_Month') ; 
            $join->on('b.C012_TrcID', 'c.C000_SysID') ; 
            $join->on('b.C050_Rev', 'c.C050_Rev') ; 
        }) 
        ->where('b.C010_TrcTypeID', $trc_type_id) 
        ->where('b.C011_Month', $month_id) 
        ->where('b.C012_TrcID', $trc_id) 
        ->where('b.C050_Rev', $rev_id) 
        ->where('b.C000_SysID', $line_search_id) 
        ->select('b.*', 'a.C050_DocNum', 'a.C050_DocDate')
        ->get() ;      
         
        $check_balance = DB::connection('sqlsrv2')->table('T511_Proc as a')  
        ->join('T510_Proc as b', function($join) {
            $join->on('a.C010_TrcTypeID', 'b.C010_TrcTypeID');
            $join->on('a.C011_Month', 'b.C011_Month') ;
            $join->on('a.C012_TrcID', 'b.C012_TrcID') ; 
            $join->on('a.C050_Rev', 'b.C050_Rev') ; 
            $join->on('a.C000_SysID', 'b.C000_SysID') ; 
        }) 
        ->join('T500_Node as c', function($join) { 
            $join->on('b.C010_TrcTypeID', 'c.C010_TrcTypeID') ; 
            $join->on('b.C011_Month', 'c.C011_Month') ; 
            $join->on('b.C012_TrcID', 'c.C000_SysID') ; 
            $join->on('b.C050_Rev', 'c.C050_Rev') ; 
        }) 
        ->where('a.C010_TrcTypeID', $trc_type_id)
        ->where('a.C011_Month', $month_id) 
        ->where('a.C012_TrcID', $trc_id)  
        ->where('a.C050_Rev', $rev_id)  
        ->where('a.C000_SysID', $line_search_id)  
        ->select('a.C010_TrcTypeID', 'a.C011_Month', 'a.C012_TrcID', 'a.C050_Rev', 'a.C000_SysID', DB::raw('SUM(a.C110_Qty) AS qty_all')) 
        ->groupBy('a.C010_TrcTypeID', 'a.C011_Month', 'a.C012_TrcID', 'a.C050_Rev', 'a.C000_SysID', 'b.C110_Qty') 
        ->get() ; 

        $qty_all = 0 ;
        foreach ($check_balance AS $td) {
            $qty_all = $td->qty_all ;
        }
 
        foreach ($check_db as $a) {   
            $docnum = $a->C050_DocNum ;
            $docdate = $a->C050_DocDate ; 
            $item_id = $a->C100_ItemIntID ; 
            $qty_1 = $a->C110_Qty ;    
        }

        $qty_production = $qty_1 ;
        $standard_pallet = $qty_pack ; 
        if (floor(($qty_production - $qty_all)/$standard_pallet) > 0) {
            $total_pallet = floor(($qty_production - $qty_all)/$standard_pallet) + $line_id + 1 ; 
        } else {
            $total_pallet = 0 ;
        }
        
        $sisa_qty = ($qty_production - $qty_all) % $standard_pallet ;  
        $qty_package = $qty_production ;
        for ($i=($line_id + 2); $i<=$total_pallet; $i++) {  
            ${'index_data'.$i} = [
                'C010_TrcTypeID' =>  $trc_type_id,
                'C011_Month' =>   $month_id,
                'C012_TrcID' =>  $month_id, 
                'C000_SysID' => $line_id,
                'C000_LineSrc' => $line_search_id
            ] ;  

            ${'data_tag'.$i} = [
                'C010_TrcTypeID' =>  $trc_type_id,
                'C011_Month' =>  $month_id,
                'C012_TrcID' =>  $trc_id, 
                'C000_SysID' =>  $i,
                'C000_LineSrc' =>  $line_search_id,
                'C100_ItemIntID' =>  $item_id, 
                'C050_DocNum' =>  $docnum,
                'C050_DocDate' =>  $docdate, 
                'C110_Qty' =>  ($qty_package-$standard_pallet >= 0 ? $standard_pallet : $qty_package),   
                'C111_QtyBal' =>  ($qty_package-$standard_pallet >= 0 ? $standard_pallet : $qty_package),      
                'created_by' =>  $my_name,  
                'serial_number' =>  $serial_number  
            ] ; 

            ${'check_tag_id'.$i} = DB::connection('sqlsrv2')->table('T511_Proc')  
            ->where(${'index_data'.$i})   
            ->count() ;  
            if (${'check_tag_id'.$i} == 0) {
                ${'insert_tag'.$i} = DB::connection('sqlsrv2')->table('T511_Proc')   
                ->insert(${'data_tag'.$i}) ;
            }  
            $line_id++;
            $qty_package = $qty_package - $standard_pallet ;
        }  

        $line_id = $line_id + 2 ;
        if ($sisa_qty>0) { 
            ${'index_data'} = [
                'C010_TrcTypeID' =>  $trc_type_id,
                'C011_Month' =>   $month_id,
                'C012_TrcID' =>  $month_id, 
                'C000_SysID' => $line_id,
                'C000_LineSrc' => $line_search_id
            ] ;  
            ${'data_tag'} = [
                'C010_TrcTypeID' =>  $trc_type_id,
                'C011_Month' =>  $month_id,
                'C012_TrcID' =>  $trc_id, 
                'C000_SysID' =>  $i,
                'C000_LineSrc' =>  $line_search_id,
                'C100_ItemIntID' =>  $item_id, 
                'C050_DocNum' =>  $docnum,
                'C050_DocDate' =>  $docdate, 
                'C110_Qty' =>  ($qty_package-$standard_pallet >= 0 ? $standard_pallet : $qty_package),   
                'C111_QtyBal' => ($qty_package-$standard_pallet >= 0 ? $standard_pallet : $qty_package),   
                'created_by' =>  $my_name 
            ] ; 

            ${'check_tag_id'} =  DB::connection('sqlsrv2')->table('T511_Proc')  
            ->where(${'index_data'})   
            ->count() ;

            if (${'check_tag_id'} == 0) {
                ${'insert_tag'} =  DB::connection('sqlsrv2')->table('T511_Proc')   
                ->insert(${'data_tag'}) ;
            } 
        }

        $result = DB::connection('sqlsrv2')->table('T511_Proc')
        ->where('C010_TrcTypeID', '=', $trc_type_id)
        ->where('C011_Month', '=', $month_id) 
        ->where('C012_TrcID', '=', $trc_id)  
        ->where('C000_LineSrc', '=', $line_search_id)  
        ->get()->count() ; 

        return $result ;
    }


    public static function get_status_open_close($trc_type_id,$month_id,$trc_id,$rev_id){  
        $total_rows = DB::connection('sqlsrv2')->table('T510_Proc as a')   
        ->where('a.C010_TrcTypeID', $trc_type_id)
        ->where('a.C011_Month', $month_id) 
        ->where('a.C012_TrcID', $trc_id)   
        ->where('a.C050_Rev', $rev_id)  
        ->where('a.C111_QtyBal2', '>', 0)  
        ->get()->count() ;  
        if($total_rows>0){
            $result = 1 ; 
        }else{
            $result = 2 ; 
        } 
        return $result ;  
    }

    public static function detail_list_data($trc_type_id,$month_id,$trc_id,$rev_id){ 
        if($trc_type_id==1200){ 
        $t = DB::connection('sqlsrv2')->table('Q_G2_Proc')
           ->where('C010_TrcTypeID', '=', $trc_type_id)
           ->where('C011_Month', '=', $month_id)
           ->where('C012_TrcID', '=', $trc_id)
           ->where('C050_Rev', '=', $rev_id)
           ->select('Q_G2_Proc.*', DB::raw("NULL as PostCategoryID"),  'QtyOutToReq AS Convertion', DB::raw("NULL AS MLedgerID"))
           ->orderBy('C000_SysID','ASC')
           ->get() ;
        } else if($trc_type_id==1205){
        $t = DB::connection('sqlsrv2')->table('Q_G2_Proc_Subcon')
           ->where('C010_TrcTypeID', '=', $trc_type_id)
           ->where('C011_Month', '=', $month_id)
           ->where('C012_TrcID', '=', $trc_id)
           ->where('C050_Rev', '=', $rev_id)
           ->select('Q_G2_Proc_Subcon.*', DB::raw("NULL as PostCategoryID"),  'C150_UnitConvertion AS Convertion', DB::raw("NULL AS MLedgerID"))
           ->orderBy('C000_SysID','ASC')
           ->get() ;
        } else {
        $t = DB::connection('sqlsrv2')->table('Q_G2_Proc_Other')
           ->where('C010_TrcTypeID', '=', $trc_type_id)
           ->where('C011_Month', '=', $month_id)
           ->where('C012_TrcID', '=', $trc_id)
           ->where('C050_Rev', '=', $rev_id)
           ->select('Q_G2_Proc_Other.*',  'C150_UnitConvertion AS Convertion', DB::raw("NULL AS MLedgerID"))
           ->orderBy('C000_SysID','ASC')
           ->get() ;  
       } 
        return $t ; 
    }

    public static function load_data_print_page($trc_type_id,$month_id,$trc_id,$rev_id,$Status,$print_option,$offset,$limit){ 
        $query = DB::connection('sqlsrv2')->table('Q_G2_Proc')
          ->leftJoin('T500_500_2', function ($join) {
              $join->on('Q_G2_Proc.C010_TrcTypeID', '=', 'T500_500_2.C034_TrcTypeID_To');
              $join->on('Q_G2_Proc.C011_Month', '=', 'T500_500_2.C035_Month_To');
              $join->on('Q_G2_Proc.C012_TrcID', '=', 'T500_500_2.C036_TrcID_To');
              $join->on('Q_G2_Proc.C050_Rev', '=', 'T500_500_2.C050_Rev_To');
          })
          ->leftJoin('T510_Proc', function ($join) {
              $join->on('T500_500_2.C010_TrcTypeID', '=', 'T510_Proc.C010_TrcTypeID');
              $join->on('T500_500_2.C011_Month', '=', 'T510_Proc.C011_Month');
              $join->on('T500_500_2.C000_SysID', '=', 'T510_Proc.C012_TrcID');
              $join->on('T500_500_2.C050_Rev', '=', 'T510_Proc.C050_Rev');
              $join->on('Q_G2_Proc.C000_SysID', '=', 'T510_Proc.C000_LineSrc');
          })
          ->where('Q_G2_Proc.C010_TrcTypeID', '=', $trc_type_id)
          ->where('Q_G2_Proc.C011_Month', '=', $month_id)
          ->where('Q_G2_Proc.C012_TrcID', '=', $trc_id)
          ->where('Q_G2_Proc.C050_Rev', '=', $rev_id)
          ->select('Q_G2_Proc.*', 'T510_Proc.C111_QtyBal2 AS qty_bk_order', DB::raw("NULL as PostCategoryID"))
          ->offset($offset)
          ->limit($limit)
          ->orderBy('Q_G2_Proc.C000_SysID','ASC')
          ->get() ;
          
       $data = array();
       $no = $offset + 1 ; 
        
       foreach($query as $l) { 
       $dtDelivery = AppModel::local_date_formate_name(substr($l->C063_dtDelivery,0,10)) ;
       $ItemName = $l->ItemName_Out ;
       $ItemNum_Req = $l->ItemNum_Req ;
       if($Status==1){$StatusX = '- Draft';}else{$StatusX = '';} 
       if($print_option!=0){
       $Product = $ItemNum_Req.' - '.$ItemName ;
       }else{
       $Product = $ItemName ;   
       }  
        
       
       $line_x = $no.'^'.$ItemNum_Req.'^'.$dtDelivery.'^'.number_format(round($l->C110_Qty2)).'^'.$l->Unit_Req.'^'.number_format(round($l->C102_PriceInt,2),2).'^'.number_format(round($l->C127_AmountDiscount,2),2).'^'.number_format(round($l->C125_AmountInt,2),2).'^'.number_format(round($l->C110_Qty)).'^'.$l->Unit_Out.'^'.$l->PostCategoryID.'^'.$ItemName.'^'.number_format(round($l->qty_bk_order,0)) ;
      
       $no++;
       $data[] = explode('^', chop($line_x));
       }
       return $data;
      }

      public static function load_data_print_page_sbc($trc_type_id,$month_id,$trc_id,$rev_id,$Status,$print_option,$offset,$limit){   
        $query = DB::connection('sqlsrv2')->table('Q_G2_Proc_Subcon')
        ->leftJoin('T500_500_2', function ($join) {
           $join->on('Q_G2_Proc_Subcon.C010_TrcTypeID', '=', 'T500_500_2.C034_TrcTypeID_To');
           $join->on('Q_G2_Proc_Subcon.C011_Month', '=', 'T500_500_2.C035_Month_To');
           $join->on('Q_G2_Proc_Subcon.C012_TrcID', '=', 'T500_500_2.C036_TrcID_To');
           $join->on('Q_G2_Proc_Subcon.C050_Rev', '=', 'T500_500_2.C050_Rev_To');
       })
       ->leftJoin('T510_Proc', function ($join) {
           $join->on('T500_500_2.C010_TrcTypeID', '=', 'T510_Proc.C010_TrcTypeID');
           $join->on('T500_500_2.C011_Month', '=', 'T510_Proc.C011_Month');
           $join->on('T500_500_2.C000_SysID', '=', 'T510_Proc.C012_TrcID');
           $join->on('T500_500_2.C050_Rev', '=', 'T510_Proc.C050_Rev');
           $join->on('Q_G2_Proc_Subcon.C000_SysID', '=', 'T510_Proc.C000_LineSrc');
       })
           ->where('Q_G2_Proc_Subcon.C010_TrcTypeID', '=', $trc_type_id)
           ->where('Q_G2_Proc_Subcon.C011_Month', '=', $month_id)
           ->where('Q_G2_Proc_Subcon.C012_TrcID', '=', $trc_id)
           ->where('Q_G2_Proc_Subcon.C050_Rev', '=', $rev_id)
           ->select('Q_G2_Proc_Subcon.*', 'T510_Proc.C111_QtyBal2 AS qty_bk_order', DB::raw("NULL as PostCategoryID"))
           ->offset($offset)
           ->limit($limit)
           ->orderBy('Q_G2_Proc_Subcon.C000_SysID', 'ASC')
           ->get() ;
        $data = array();
        $no = $offset + 1 ;  
        foreach($query as $l) { 
        $dtDelivery = AppModel::local_date_formate_name(substr($l->C063_dtDelivery,0,10)) ;
        $ItemName = $l->ItemName_Out ;
        $ItemNum_Req = $l->ItemNum_Req ;
        if($Status==1){$StatusX = '- Draft';}else{$StatusX = '';} 
        if($print_option!=0){
        $Product = $ItemNum_Req.' - '.$ItemName ;
        }else{
        $Product = $ItemNum_Req.' - '.$ItemName ;
        }  
        
        $line_x = $no.'^'.$ItemNum_Req.'^'.$dtDelivery.'^'.number_format(round($l->C110_Qty2)).'^'.$l->Unit_Req.'^'.number_format(round($l->C102_PriceInt,2),2).'^'.number_format(round($l->C127_AmountDiscount,2),2).'^'.number_format(round($l->C125_AmountInt,2),2).'^'.number_format(round($l->C110_Qty)).'^'.$l->Unit_Out.'^'.$l->PostCategoryID.'^'.$ItemName.'^'.number_format(round($l->C111_QtyBal,0)) ;
        $no++;
        $data[] = explode('^', chop($line_x));
        }
        return $data;
       }

       public static function load_data_print_page_others($trc_type_id,$month_id,$trc_id,$rev_id,$Status,$print_option,$offset,$limit){  
            $query = DB::connection('sqlsrv2')->table('Q_G2_Proc_Other') 
                ->where('Q_G2_Proc_Other.C010_TrcTypeID', '=', $trc_type_id)
                ->where('Q_G2_Proc_Other.C011_Month', '=', $month_id)
                ->where('Q_G2_Proc_Other.C012_TrcID', '=', $trc_id)
                ->where('Q_G2_Proc_Other.C050_Rev', '=', $rev_id)
            ->select('Q_G2_Proc_Other.*', 'Q_G2_Proc_Other.ItemName AS ItemName_Out', 'Q_G2_Proc_Other.ItemNum AS ItemNum_Out', 'Q_G2_Proc_Other.C100_ItemIntID AS C100_ItemExtID', 'Q_G2_Proc_Other.C150_UnitConvertion AS QtyOutToReq', 'Q_G2_Proc_Other.Unit AS Unit_Out', 'Q_G2_Proc_Other.BuyUnit AS Unit_Req')
            ->offset($offset)
            ->limit($limit)
            ->orderBy('Q_G2_Proc_Other.C000_SysID','ASC')
            ->get() ;   
        
            $data = array();
            $no = $offset + 1 ;  
            foreach($query as $l) { 
            $dtDelivery = AppModel::local_date_formate_name(substr($l->C063_dtDelivery,0,10)) ;
            $ItemName = $l->ItemName_Out ;
            $ItemNum_Req = $l->ItemNum_Out ;
            if($Status==1){$StatusX = '- Draft';}else{$StatusX = '';} 
            if($print_option!=0){
            $Product = $ItemNum_Req.' - '.$ItemName ;
            }else{
            $Product = $ItemNum_Req.' - '.$ItemName ;   
            }  
            
            $line_x = $no.'^'.$ItemNum_Req.'^'.$dtDelivery.'^'.number_format(round($l->C110_Qty2)).'^'.$l->Unit_Req.'^'.number_format(round($l->C102_PriceInt,2),2).'^'.number_format(round($l->C127_AmountDiscount,2),2).'^'.number_format(round($l->C125_AmountInt,2),2).'^'.number_format(round($l->C110_Qty)).'^'.$l->Unit_Out.'^'.$l->PostCategoryID.'^'.$ItemName.'^'.number_format(round($l->C111_QtyBal,0)) ;
            
            $no++;
            $data[] = explode('^', chop($line_x));  
            }
            return $data;
       }

       public static function load_data_print_jurnal($trc_type_id,$month_id,$trc_id,$rev_id)
            { 
                $query = DB::connection('sqlsrv2')->table('Q610_Jurnal')  
                ->groupBy('C010_TrcTypeID', 'C011_Month', 'C000_SysID', 'C050_Rev', 'Code', 'Account', 'MinPlus', 'Currency') 
                ->having('C010_TrcTypeID', '=', $trc_type_id)
                ->having('C011_Month', '=', $month_id)
                ->having('C000_SysID', '=', $trc_id) 
                ->having('C050_Rev', '=', $rev_id)
                ->select( 'C010_TrcTypeID', 'C011_Month', 'C000_SysID', 'C050_Rev', 'Code', 'Account', 'MinPlus', DB::raw('SUM(Debet_IDR) AS Debet_IDR'), DB::raw('SUM(Credit_IDR) AS Credit_IDR'), 'Currency')
                ->orderBy('MinPlus','DESC') ;  
                $data = array();
                $no = 1 ;  
                foreach($query->get() as $l) {  
                $line_x = $no.'^'.$l->Code.'^'.$l->Account.'^'.$l->Currency.'^'.($l->Debet_IDR==0 ? '' : number_format($l->Debet_IDR,2)).'^'.($l->Credit_IDR==0 ? '' : number_format($l->Credit_IDR,2)) ;
                $no++;
                $data[] = explode('^', chop($line_x));
                }
            return $data;
            }

        public static function sum_detail_doc($trc_type_id,$month_id,$trc_id,$rev_id) { 
                $t = DB::connection('sqlsrv2')->table('T510_Proc')
                ->where('C010_TrcTypeID', '=', $trc_type_id)
                ->where('C011_Month', '=', $month_id)
                ->where('C012_TrcID', '=', $trc_id)
                ->where('C050_Rev', '=', $rev_id)
                ->select(DB::raw("SUM(C110_Qty2) as result_sum"))
                ->get() ;    
            if($t->count() > 0){
            foreach($t as $h){
            $result = $h->result_sum ; }
            }else{ $result = 0 ; }
            return $result ; 
        }

        public static function rows_data_print_jurnal($trc_type_id,$month_id,$trc_id,$rev_id){ 
            $query = DB::connection('sqlsrv2')->table('Q610_Jurnal')  
            ->groupBy('C010_TrcTypeID', 'C011_Month', 'C000_SysID', 'C050_Rev', 'Code', 'Account', 'MinPlus') 
            ->having('C010_TrcTypeID', '=', $trc_type_id)
            ->having('C011_Month', '=', $month_id)
            ->having('C000_SysID', '=', $trc_id) 
            ->having('C050_Rev', '=', $rev_id)
            ->select( 'C010_TrcTypeID', 'C011_Month', 'C000_SysID', 'C050_Rev', 'Code', 'Account', 'MinPlus', DB::raw('SUM(Debet_IDR) AS Debet_IDR'), DB::raw('SUM(Credit_IDR) AS Credit_IDR'))
            ->orderBy('MinPlus','DESC')
            ->get(); ; 
           return $query;
        }

    public static function check_as_source_po($trc_type_id,$month_id,$trc_id,$rev_id) {  
        $t = DB::connection('sqlsrv2')->table('T500_AsSource')
        ->where('C010_TrcTypeID', '=', $trc_type_id)
        ->where('C011_Month', '=', $month_id)
        ->where('C000_SysID', '=', $trc_id)
        ->where('C050_Rev', '=', $rev_id) 
        ->get() ;    

        if($t->count() > 0){
            foreach($t as $h) {
                $result = $h->C012_TrcOpenClose1 ; 
            }
        } else { 
            $result = 11 ; 
        }
        return $result ; 
    }

    public static function check_connecting_di($trc_type_id,$month_id,$trc_id,$rev_id) {  
        $doc_num = $trc_type_id.'_'.$month_id.'_'.$trc_id.'_'.$rev_id ;
        $t = DB::connection('sqlsrv2')->table('T500_Node')
        ->where('C050_ExtDocNum1', "$doc_num") 
        ->get()->count() ;    

        if($t > 0){
            $result = 0 ;
        } else { 
            $result = 1 ; 
        }
        return $result ; 
    }

    public static function transaction_type_properties($trc_type_id, $flow_id) {   
        return DB::connection('sqlsrv3')->table('t100_transaction_type') 
        ->where('trc_type_id', '=', $trc_type_id) 
        ->where('flow_id', '=', $flow_id) 
        ->where('trc_name', 'LIKE', '%DI%')  
        ->select('t100_transaction_type.*')
        ->get();     
    }

    

    public static function create_connection($trc_type_id_po, $month_id_po, $trc_id_po, $rev_id_po, $trc_type_id, $month_id, $trc_id, $rev_id) {  
        $db = DB::connection('sqlsrv2')->table('T500_Node')
        ->where('C010_TrcTypeID', '=', $trc_type_id)
        ->where('C011_Month', '=', $month_id)
        ->where('C000_SysID', '=', $trc_id)
        ->where('C050_Rev', '=', $rev_id)
        ->get(); 
        foreach ($db as $t){
            $d['C010_TrcTypeID'] = $trc_type_id_po ;
            $d['C011_Month'] = $month_id_po ;
            $d['C000_SysID'] = $trc_id_po ;
            $d['C050_Rev'] = $rev_id_po ;
            $d['C013_TrcTypeSrcID'] = $t->C013_TrcTypeSrcID ;
            $d['C018_FlowTypeID'] = 3 ;
            $d['C034_TrcTypeID_To'] = $trc_type_id ;
            $d['C035_Month_To'] = $month_id ;
            $d['C036_TrcID_To'] = $trc_id ;
            $d['C050_Rev_To'] = $rev_id ;
            $d['C016_ContentFlowID'] = 4 ;
        } 
        return DB::connection('sqlsrv2')->table('T500_500_2')->insert($d); 
    }

    public static function delete_connection($trc_type_id_po, $month_id_po, $trc_id_po, $rev_id_po, $trc_type_id, $month_id, $trc_id, $rev_id) {   
        $d['C010_TrcTypeID'] = $trc_type_id_po ;
        $d['C011_Month'] = $month_id_po ;
        $d['C000_SysID'] = $trc_id_po ;
        $d['C050_Rev'] = $rev_id_po ; 
        $d['C034_TrcTypeID_To'] = $trc_type_id ;
        $d['C035_Month_To'] = $month_id ;
        $d['C036_TrcID_To'] = $trc_id ;
        $d['C050_Rev_To'] = $rev_id ; 
        return DB::connection('sqlsrv2')->table('T500_500_2')->where($d)->delete(); 
    }

    public static function transfer_document_node($trc_type_id,$month_id,$trc_id,$rev_id,$trc_type_id_to,$month_id_to,$trc_id_to,$rev_id_to){ 
        $db = DB::connection('sqlsrv2')->table('T500_Node')
        ->where('C010_TrcTypeID', '=', $trc_type_id)
        ->where('C011_Month', '=', $month_id)
        ->where('C000_SysID', '=', $trc_id)
        ->where('C050_Rev', '=', $rev_id)
        ->get(); 
        foreach ($db as $t){ 
            $d['C060_PartnerID'] = $t->C060_PartnerID ; 
        } 
        return DB::connection('sqlsrv2')->table('T500_Node')
        ->where('C010_TrcTypeID', '=', $trc_type_id_to)
        ->where('C011_Month', '=', $month_id_to)
        ->where('C000_SysID', '=', $trc_id_to)
        ->where('C050_Rev', '=', $rev_id_to)
        ->update($d); 
    }
    
    public static function transfer_document_proc($trc_type_id,$month_id,$trc_id,$rev_id,$trc_type_id_to,$month_id_to,$trc_id_to,$rev_id_to){ 
        $db = DB::connection('sqlsrv2')->table('T500_Proc')
        ->leftJoin('T010_Partner', function ($join) {
        $join->on('T010_Partner.SysID', '=', 'T500_Proc.C060_PartnerID'); 
        })
        ->leftJoin('T059_Currency', function ($join) {
        $join->on('T010_Partner.CurrencyID', '=', 'T059_Currency.SysID'); 
        })
        ->where('C010_TrcTypeID', '=', $trc_type_id)
        ->where('C011_Month', '=', $month_id)
        ->where('C000_SysID', '=', $trc_id)
        ->where('C050_Rev', '=', $rev_id)
        ->select('T500_Proc.C060_PartnerID', 'T010_Partner.PICName', 'T500_Proc.C062_ActFixedAssetID', 'T500_Proc.C113_PRType', 'T500_Proc.MLedgerID', 'T500_Proc.SubLedger1ID', 'T500_Proc.SubLedger2ID', 'T500_Proc.PostCategoryID', 'T500_Proc.Remark_PR', 'T500_Proc.YearIdx', 'T500_Proc.DeptID', 'T500_Proc.C080_PRCategoryID', 'T500_Proc.C085_isPPN AS IsPPN', 'T500_Proc.C090_EstDlvDate', 'T500_Proc.C017_UserGrpFlowID_To', 'T500_Proc.C017_UserGrpFlowTo', 'T010_Partner.TermOfPayment', 'T010_Partner.CurrencyID', 'T059_Currency.Rate', 'T500_Proc.C050_DocNum', 'T500_Proc.C059_Remark')
        ->get(); 
      
        foreach ($db as $t){ 
            $d['C060_PartnerID'] = $t->C060_PartnerID ;
            $d['C061_PICName'] = $t->PICName ;
            $d['C062_ActFixedAssetID'] =  $t->C062_ActFixedAssetID ;
            $d['C113_PRType'] = $t->C113_PRType ;
            $d['MLedgerID'] = $t->MLedgerID ;
            $d['SubLedger1ID'] = $t->SubLedger1ID ;
            $d['SubLedger2ID'] = $t->SubLedger2ID ;
            $d['PostCategoryID'] = $t->PostCategoryID ;
            $d['Remark_PR'] = $t->Remark_PR ;
            $d['YearIdx'] = $t->YearIdx ;
            $d['DeptID'] = $t->DeptID ;
            $d['C080_PRCategoryID'] = $t->C080_PRCategoryID ;
            $d['C085_isPPN'] = (is_null($t->IsPPN) ? 1 : $t->IsPPN) ;
            $d['C090_EstDlvDate'] = $t->C090_EstDlvDate ;
            $d['C017_UserGrpFlowID_From'] = $t->C017_UserGrpFlowID_To ;
            $d['C017_UserGrpFlowFrom'] = $t->C017_UserGrpFlowTo ;
            $d['C052_TermOfPayment'] = $t->TermOfPayment ;
            $d['C070_CurrencyID'] = (is_null($t->CurrencyID) ? 1 : $t->CurrencyID) ;
            $d['C071_Rate'] = (is_null($t->Rate) ? 1 : $t->Rate) ;
            $d['C051_PONum'] = $t->C050_DocNum ;
        }  
    
        return DB::connection('sqlsrv2')->table('T500_Proc')
        ->where('C010_TrcTypeID', '=', $trc_type_id_to)
        ->where('C011_Month', '=', $month_id_to)
        ->where('C000_SysID', '=', $trc_id_to)
        ->where('C050_Rev', '=', $rev_id_to)
        ->update($d); 
    }

    public static function rollback_detail($trc_type_id_po, $month_id_po, $trc_id_po, $rev_id_po, $trc_type_id, $month_id, $trc_id, $rev_id) { 
        $db = DB::connection('sqlsrv2')->table('T510_Proc as a') 
        ->join('T500_Node as d', function($join){
            $join->on('a.C010_TrcTypeID', 'd.C010_TrcTypeID');
            $join->on('a.C011_Month', 'd.C011_Month');
            $join->on('a.C012_TrcID', 'd.C000_SysID');
            $join->on('a.C050_Rev', 'd.C050_Rev');
        })
        ->leftJoin('T500_500_2 as b', function($join) use($trc_type_id,$month_id,$trc_id,$rev_id) {
            $join->on('a.C010_TrcTypeID', 'b.C010_TrcTypeID');
            $join->on('a.C011_Month', 'b.C011_Month');
            $join->on('a.C012_TrcID', 'b.C000_SysID');
            $join->on('a.C050_Rev', 'b.C050_Rev');
            $join->on(DB::raw($trc_type_id), 'b.C034_TrcTypeID_To');
            $join->on(DB::raw($month_id), 'b.C035_Month_To');
            $join->on(DB::raw($trc_id), 'b.C036_TrcID_To');
            $join->on(DB::raw($rev_id), 'b.C050_Rev_To');
        })
        ->leftJoin('T510_Proc as c', function($join) {
            $join->on('b.C034_TrcTypeID_To', 'c.C010_TrcTypeID');
            $join->on('b.C035_Month_To', 'c.C011_Month');
            $join->on('b.C036_TrcID_To', 'c.C012_TrcID');
            $join->on('b.C050_Rev_To', 'c.C050_Rev');
            $join->on('a.C000_SysID', 'c.C000_LineSrc');
        }) 
        ->where('a.C010_TrcTypeID', $trc_type_id_po)
        ->where('a.C011_Month', $month_id_po)
        ->where('a.C012_TrcID', $trc_id_po)  
        ->where('a.C050_Rev', $rev_id_po)
        ->select('a.C010_TrcTypeID', 'a.C011_Month', 'a.C012_TrcID', 'a.C050_Rev', 'a.C000_SysID', 'a.C111_QtyBal2 as bal_po', 'c.C110_Qty as qty_di')
        ->get() ;  
      
        $i = 0 ;
        $result = true ;
        foreach ($db as $t){ 
            ${'index'.$i}['C010_TrcTypeID'] = $t->C010_TrcTypeID ;
            ${'index'.$i}['C011_Month'] = $t->C011_Month ;
            ${'index'.$i}['C012_TrcID'] = $t->C012_TrcID ;
            ${'index'.$i}['C050_Rev'] = $t->C050_Rev ;
            ${'index'.$i}['C000_SysID'] = $t->C000_SysID ; 
            ${'detail'.$i}['C111_QtyBal2'] = $t->bal_po +  $t->qty_di ;  
            ${'post'.$i} = DB::connection('sqlsrv2')->table('T510_Proc')->where(${'index'.$i})->update(${'detail'.$i}); 
            $result = ${'post'.$i} ;
            $i++;
        }  
    
        return $result ; 
    }

    public static function update_connection($detail_connection, $index_connection) { 
        return DB::connection('sqlsrv2')->table('T500_500_2')->where($index_connection)->update($detail_connection); 
    }

    public static function update_detail_rev($detail, $index) { 
        return DB::connection('sqlsrv2')->table('T510_Proc')->where($index)->update($detail); 
    }


}
