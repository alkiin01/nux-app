<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB ;
 
class POPortal extends Model
{
    use HasFactory;


    public static function get_transaction_list($range_date, $search, $vendor_id) 
    {     
        $year = '20'.substr($range_date,0,2) ;
        $month = substr($range_date,2,2) ;
        $start_date = $year.'-'.$month.'-01'; 
        $last_date = date('Y-m-t', strtotime($start_date)) ;  

        $result = DB::connection('sqlsrv4')->table('Erp.POHeader AS a')
                ->join('Erp.POHeader_UD AS b', 'a.SysRowID', '=', 'b.ForeignSysRowID')
                ->join('Erp.Vendor AS c', 'a.VendorNum', '=', 'c.VendorNum')
                ->select('b.DocNum_C', 'a.*')
                ->whereBetween('a.OrderDate', [$start_date, $last_date]) 
                ->where('a.Confirmed', 1) ;
 

        if (!empty($search) ) { 
            $result = $result->where('b.DocNum_C', 'LIKE', "%$search%") ;
        }   

        if (!empty($vendor_id) ) { 
            $result = $result->where('a.VendorNum', $vendor_id) ;
        } 

        return $result ;
    }

    public static function sum_detail_doc($po_num){ 
        $t = DB::connection('sqlsrv4')
        ->table('Erp.PODetail AS a')
        ->where('a.PONum', '=', $po_num)
        ->select(DB::raw("SUM(a.DocExtCost + a.DocTotalTax) as result_sum"))
        ->get();    
     if($t->count() > 0){
     foreach($t as $h){
     $result = $h->result_sum ; }
     }else{ $result = 0 ; }
     return $result ; }

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


    public static function data_detail($po_num) 
    {     
        // $result = DB::connection('sqlsrv2')->table('T500_Node') 
        // ->join('T500_Proc', function($join){
        // $join->on('T500_Node.C010_TrcTypeID', '=', 'T500_Proc.C010_TrcTypeID');
        // $join->on('T500_Node.C011_Month', '=', 'T500_Proc.C011_Month'); 
        // $join->on('T500_Node.C000_SysID', '=', 'T500_Proc.C000_SysID'); 
        // $join->on('T500_Node.C050_Rev', '=', 'T500_Proc.C050_Rev'); 
        // })
        // ->leftJoin('T010_Partner', 'T500_Node.C060_PartnerID', '=', 'T010_Partner.SysID') 
        // ->leftJoin('T100_FixedAsset', 'T500_Proc.C062_ActFixedAssetID', '=', 'T100_FixedAsset.SysID') 
        // ->leftJoin('T064_PRCategory', 'T500_Proc.C080_PRCategoryID', '=', 'T064_PRCategory.SysID') 
        // ->select('T500_Node.*', 'T010_Partner.PartnerName', 'T500_Proc.C062_ActFixedAssetID', 'T500_Proc.C090_EstDlvDate', 'T500_Proc.C059_Remark', 'T100_FixedAsset.Descr as ActFixedAsset', 'T500_Proc.C080_PRCategoryID', 'T064_PRCategory.Sub1 as Cat1', 'T064_PRCategory.Sub2 as Cat2') 
        // ->where('T500_Node.C010_TrcTypeID', '=', $trc_type_id)
        // ->where('T500_Node.C011_Month', '=', $month_id)
        // ->where('T500_Node.C000_SysID', '=', $trc_id)
        // ->where('T500_Node.C050_Rev', '=', $rev_id) 
        // ->get() ; 

        $result = DB::connection('sqlsrv4')->table('Erp.POHeader AS a')
        ->join('Erp.POHeader_UD AS b', 'a.SysRowID', '=', 'b.ForeignSysRowID')
        ->join('Erp.Vendor AS c', 'a.VendorNum', '=', 'c.VendorNum')
        ->select('b.DocNum_C', 'a.*') 
        ->where('a.PONum', $po_num)
        ->where('a.Confirmed', 1)->get() ; 

        return $result ;
    }

    
    public static function detail_list_data($po_num){ 
        $t = DB::connection('sqlsrv4')->table('Erp.PODetail AS a')
        ->where('a.PONum', '=', $po_num) 
        ->select('a.*', DB::raw("NULL as PostCategoryID"),  DB::raw("NULL  AS Convertion"), DB::raw("NULL AS MLedgerID"))
        ->orderBy('a.POLine','ASC')
        ->get() ; 
        return $t ; 
    }
   
    public static function load_all_data_print_page($trc_type_id,$month_id,$trc_id,$rev_id,$Status,$print_option){ 
       $query = DB::connection('sqlsrv2')->table('Q_G2_Proc')
         ->where('C010_TrcTypeID', '=', $trc_type_id)
         ->where('C011_Month', '=', $month_id)
         ->where('C012_TrcID', '=', $trc_id)
         ->where('C050_Rev', '=', $rev_id)
         ->select('Q_G2_Proc.*', DB::raw("NULL as PostCategoryID")) 
         ->orderBy('C000_SysID','ASC')
         ->get() ;
         
      $data = array();
      $no =  1 ; 
       
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
      
      $line_x = $no.'^'.$Product.'^'.$dtDelivery.'^'.number_format(round($l->C110_Qty2)).'^'.$l->Unit_Req.'^'.number_format(round($l->C102_PriceInt,2),2).'^'.number_format(round($l->C127_AmountDiscount,2),2).'^'.number_format(round($l->C125_AmountInt,2),2).'^'.number_format(round($l->C110_Qty)).'^'.$l->Unit_Out.'^'.$l->PostCategoryID ;
      $no++;
      $data[] = explode('^', chop($line_x));
      }
      return $data;
     }
    
     public static function load_data_print_page($po_num,$Status,$print_option,$offset,$limit){ 
        $query = DB::connection('sqlsrv4')->table('Erp.PODetail AS a')
          ->where('a.PONum', '=', $po_num) 
          ->select('a.*', DB::raw("NULL as PostCategoryID"))
          ->offset($offset)
          ->limit($limit)
          ->orderBy('a.POLine','ASC')
          ->get() ;
          
       $data = array();
       $no = $offset + 1 ; 
        
       foreach($query as $l) { 
       $dtDelivery = AppModel::local_date_formate_name(substr($l->DueDate,0,10)) ;
       $ItemName = $l->LineDesc ;
       $ItemNum_Req = $l->PartNum ;
       if($Status==1){$StatusX = '- Draft';}else{$StatusX = '';} 
       if($print_option!=0){
       $Product = $ItemNum_Req.' - '.$ItemName ;
       }else{
       $Product = $ItemName ;   
       }  
        
       
       $line_x = $no.'^'.$ItemNum_Req.'^'.$dtDelivery.'^'.number_format(round($l->OrderQty)).'^'.$l->PUM.'^'.
       number_format(round($l->UnitCost,2),2).'^'.number_format(round($l->DocExtCost,2),2).'^'.
       number_format(round($l->DocExtCost,2),2).'^'.number_format(round($l->XOrderQty)).'^'.$l->IUM.'^'.$l->PostCategoryID.'^'.$ItemName ;
      
       $no++;
       $data[] = explode('^', chop($line_x));
       }
       return $data;
      }
      
      public static function load_all_data_print_page_sbc($trc_type_id,$month_id,$trc_id,$rev_id,$Status,$print_option){   
          $query = DB::connection('sqlsrv2')->table('Q_G2_Proc_Subcon')
             ->where('C010_TrcTypeID', '=', $trc_type_id)
             ->where('C011_Month', '=', $month_id)
             ->where('C012_TrcID', '=', $trc_id)
             ->where('C050_Rev', '=', $rev_id)
             ->select('Q_G2_Proc_Subcon.*', DB::raw("NULL as PostCategoryID")) 
             ->orderBy('C000_SysID', 'ASC')
             ->get() ;
          $data = array();
          $no = 1 ;  
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
          
          $line_x = $no.'^'.$Product.'^'.$dtDelivery.'^'.number_format(round($l->C110_Qty2)).'^'.$l->Unit_Req.'^'.number_format(round($l->C102_PriceInt,2),2).'^'.number_format(round($l->C127_AmountDiscount)).'^'.number_format(round($l->C125_AmountInt,2),2).'^'.number_format(round($l->C110_Qty)).'^'.$l->Unit_Out.'^'.$l->PostCategoryID ;
          $no++;
          $data[] = explode('^', chop($line_x));
          }
          return $data;
         }
      
      public static function load_data_print_page_sbc($trc_type_id,$month_id,$trc_id,$rev_id,$Status,$print_option,$offset,$limit){   
       $query = DB::connection('sqlsrv2')->table('Q_G2_Proc_Subcon')
          ->where('C010_TrcTypeID', '=', $trc_type_id)
          ->where('C011_Month', '=', $month_id)
          ->where('C012_TrcID', '=', $trc_id)
          ->where('C050_Rev', '=', $rev_id)
          ->select('Q_G2_Proc_Subcon.*', DB::raw("NULL as PostCategoryID"))
          ->offset($offset)
          ->limit($limit)
          ->orderBy('C000_SysID', 'ASC')
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
       
       $line_x = $no.'^'.$ItemNum_Req.'^'.$dtDelivery.'^'.number_format(round($l->C110_Qty2)).'^'.$l->Unit_Req.'^'.number_format(round($l->C102_PriceInt,2),2).'^'.number_format(round($l->C127_AmountDiscount,2),2).'^'.number_format(round($l->C125_AmountInt,2),2).'^'.number_format(round($l->C110_Qty)).'^'.$l->Unit_Out.'^'.$l->PostCategoryID.'^'.$ItemName ;
       $no++;
       $data[] = explode('^', chop($line_x));
       }
       return $data;
      }
      
      public static function load_all_data_print_page_others($trc_type_id,$month_id,$trc_id,$rev_id,$Status,$print_option){  
       $query = DB::connection('sqlsrv2')->table('Q_G2_Proc_Other')
          ->where('C010_TrcTypeID', '=', $trc_type_id)
          ->where('C011_Month', '=', $month_id)
          ->where('C012_TrcID', '=', $trc_id)
          ->where('C050_Rev', '=', $rev_id)
          ->select('Q_G2_Proc_Other.*', 'ItemName AS ItemName_Out', 'ItemNum AS ItemNum_Out', 'C100_ItemIntID AS C100_ItemExtID', 'C150_UnitConvertion AS QtyOutToReq', 'Unit AS Unit_Out', 'BuyUnit AS Unit_Req')
          ->orderBy('C000_SysID','ASC')
          ->get() ;  
       $data = array();
       $no = 1 ;  
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
       
       $line_x = $no.'^'.$Product.'^'.$dtDelivery.'^'.number_format(round($l->C110_Qty2)).'^'.$l->Unit_Req.'^'.number_format($l->C102_PriceInt,2).'^'.number_format(round($l->C127_AmountDiscount,2),2).'^'.number_format($l->C125_AmountInt,2).'^'.number_format(round($l->C110_Qty)).'^'.$l->Unit_Out.'^'.$l->PostCategoryID ;
       $no++;
       $data[] = explode('^', chop($line_x));
       }
       return $data;
      }
      
      public static function load_data_print_page_others($trc_type_id,$month_id,$trc_id,$rev_id,$Status,$print_option,$offset,$limit){  
          $query = DB::connection('sqlsrv2')->table('Q_G2_Proc_Other')
             ->where('C010_TrcTypeID', '=', $trc_type_id)
             ->where('C011_Month', '=', $month_id)
             ->where('C012_TrcID', '=', $trc_id)
             ->where('C050_Rev', '=', $rev_id)
             ->select('Q_G2_Proc_Other.*', 'ItemName AS ItemName_Out', 'ItemNum AS ItemNum_Out', 'C100_ItemIntID AS C100_ItemExtID', 'C150_UnitConvertion AS QtyOutToReq', 'Unit AS Unit_Out', 'BuyUnit AS Unit_Req')
             ->offset($offset)
             ->limit($limit)
             ->orderBy('C000_SysID','ASC')
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
          
          $line_x = $no.'^'.$ItemNum_Req.'^'.$dtDelivery.'^'.number_format(round($l->C110_Qty2)).'^'.$l->Unit_Req.'^'.number_format(round($l->C102_PriceInt,2),2).'^'.number_format(round($l->C127_AmountDiscount,2),2).'^'.number_format(round($l->C125_AmountInt,2),2).'^'.number_format(round($l->C110_Qty)).'^'.$l->Unit_Out.'^'.$l->PostCategoryID.'^'.$ItemName ;
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


}
