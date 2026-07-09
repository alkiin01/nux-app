<?php

namespace App\Http\Controllers;

use App\Models\DelConfirm;
use App\Models\AppModel;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt ;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB ;
use PDF;

class DelConfirmController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $my_id = Auth::user()->id ; 
        $uri = explode("/", url()->current()); 
        if (count($uri) < 4) {
            $menu = $this->menu($my_id, 'home') ;   
        } else {
            $menu = $this->menu($my_id, $uri[3]) ;  
        } 
        $data['head_title'] = $menu['head_title'];
        $data['menu_level_1'] = $menu['menu_level_1'] ;
        $data['menu_level_2'] = $menu['menu_level_2'] ;
        $data['menu_level_3'] = $menu['menu_level_3'] ;
        $data['menu_level_4'] = $menu['menu_level_4'] ;  
        $data['doc_access_list'] = DB::table('t100_user_doc_access')->where('user_id', $my_id)->get() ;
        return view('del_confirm/delcon_index', $data) ; 
    }

    public function open_doc(Request $request)
    { 
        $my_id = Auth::user()->id ; 
        $uri = explode("/", url()->current()); 
        if (count($uri) < 4) {
            $menu = $this->menu($my_id, 'home') ;   
        } else {
            $menu = $this->menu($my_id, $uri[3]) ;  
        } 
        $data['head_title'] = $menu['head_title'];
        $data['menu_level_1'] = $menu['menu_level_1'] ;
        $data['menu_level_2'] = $menu['menu_level_2'] ;
        $data['menu_level_3'] = $menu['menu_level_3'] ;
        $data['menu_level_4'] = $menu['menu_level_4'] ; 

        $str_di = explode("_", Crypt::decryptString(str_replace("-","=", $request->ref_doc))) ;  
        $data_head = DelConfirm::get_head_properties($str_di); 
        foreach ($data_head as $item) {
            $status_doc = $item->C013_DraftReadyApprCancel ; 
        }
        
        if ($data_head->count() > 0) {
            foreach ($data_head as $item) {
                $data['docnum'] = $item->C050_DocNum ;
                $data['docdate'] = AppModel::local_date_formate(substr($item->C050_DocDate,0,10)) ;
                $data['shipnum'] = $item->C050_ExtDocNum ;
                $data['shipdate'] = AppModel::local_date_formate(substr($item->C050_ExtDocDate,0,10)) ;
                $data['remark'] = $item->C059_Remark ;
                $data['ponum'] = $item->C051_PONum ;
                $flow_id = $item->C017_UserGrpFlowID_From ;
                $status_doc = $item->C013_DraftReadyApprCancel ;
            }
        } else {
            $data['docnum'] = '' ;
            $data['docdate'] = '' ;
            $data['shipnum'] = '' ;
            $data['shipdate'] = '' ;
            $data['remark'] = '' ;
            $data['ponum'] = '' ;
            $flow_id = 0 ;
            $status_doc = 0 ;
        }
        $data['ref_doc'] = $request->ref_doc ;
        $data['ref_form'] = $request->ref_form ;
        $data['ref_doc_po'] = str_replace("=","-", Crypt::encryptString(DelConfirm::get_id_po($str_di))) ;
        $data['flow_id_form'] = $str_di[0].'_'.$flow_id.'_'.DelConfirm::get_id_po($str_di)[0] ;

        if ($status_doc == 1) {
            return view('del_confirm/di_form', $data) ; 
        } else if ($status_doc == 3) {
            return view('del_confirm/preview', $data) ; 
        } else {
            return view('errors.404');
        }
    }
 

    public function get_head_properties(Request $request)
    {   
        $str_po = explode("_", Crypt::decryptString(str_replace("-","=", $request->ref_doc_po))) ;  
        $str_di = explode("_", Crypt::decryptString(str_replace("-","=", $request->ref_doc))) ;  
        $data_head = DelConfirm::get_head_properties($str_di); 
        $data = array();
        if ($data_head->count() > 0) {
            foreach ($data_head as $item) { 
                $data['docnum'] = $item->C050_DocNum ;
                $data['docdate'] = AppModel::local_date_formate(substr($item->C050_DocDate,0,10)) ;
                $data['shipnum'] = $item->C050_ExtDocNum ;
                $data['shipdate'] = AppModel::local_date_formate(substr($item->C050_ExtDocDate,0,10)) ;
                $data['remark'] = $item->C059_Remark ;
                $data['ponum'] = $item->C051_PONum ;
            }
        } else { 
            $data['docnum'] = '' ;
            $data['docdate'] = '' ;
            $data['shipnum'] = '' ;
            $data['shipdate'] = '' ;
            $data['remark'] = '' ;
            $data['ponum'] = '' ;
        }   
        echo json_encode($data);  
    }

    public function store_head(Request $request)
    {    
        $str_di = explode("_", Crypt::decryptString(str_replace("-","=", $request->ref_doc))) ;   
        $detail['C050_ExtDocNum'] = $request->input_shipnum ;
        $detail['C050_ExtDocDate'] = AppModel::post_date_formate($request->input_shipdate) ;
        $detail['C059_Remark'] = $request->input_remark ; 

        $detail_node['C050_ExtDocNum'] = $request->input_shipnum ;
        $detail_node['C050_ExtDocDate'] = AppModel::post_date_formate($request->input_shipdate) ; 

        $index['C010_TrcTypeID'] = $str_di[0] ;
        $index['C011_Month'] = $str_di[1] ;
        $index['C000_SysID'] = $str_di[2] ;
        $index['C050_Rev'] = $str_di[3] ;

        $check_shipnum = DelConfirm::check_shipnum ($str_di, $request->input_shipnum) ;
        if($check_shipnum == 0){
            $update = DelConfirm::update_head($detail, $detail_node, $index);  
            if ($update) {
                $data['process'] = 1 ;
                $data['msg'] = 'Success update' ;
            } else {
                $data['process'] = 0 ;
                $data['msg'] = 'Fail update' ;
            } 
        } else {
            $data['process'] = 0 ;
            $data['msg'] = 'Ship number already used!' ;
        }  
        echo json_encode($data);  
    }

    public function store_detail(Request $request)
    {    
        $str_di = explode("_", Crypt::decryptString(str_replace("-","=", $request->id))) ;    
        $trc_type_id_po = $str_di[0] ;
        $month_id_po = $str_di[1] ;
        $trc_id_po = $str_di[2] ;
        $rev_id_po = $str_di[3] ;
        $line_id_po = $str_di[8] ;

        $trc_type_id = $str_di[4] ;
        $month_id = $str_di[5] ;
        $trc_id = $str_di[6] ;
        $rev_id = $str_di[7] ;

        if ($str_di[9]=="") {
            $line_id = DelConfirm::get_detail_line_id($trc_type_id,$month_id,$trc_id,$rev_id); 
        } else {
            $line_id = $str_di[9] ; 
        }
        

        $balance_po = DelConfirm::get_detail_balance_po($trc_type_id_po,$month_id_po,$trc_id_po,$rev_id_po,$line_id_po);
        $qty_di_be = DelConfirm::get_detail_qty_di($trc_type_id,$month_id,$trc_id,$rev_id,$line_id);
        $balance = ($balance_po + $qty_di_be) - $request->qty ;
        if ($balance<0) {
            $data['process'] = 0 ;
            $data['msg'] = 'Delivery qty exceeds the limit!' ;
        } else {

            

            $detail['C110_Qty'] = $request->qty ;
            $detail['C110_Qty2'] = $request->qty ;
            $detail['C111_QtyBal'] = $request->qty ;
            $detail['C111_QtyBal2'] = $request->qty ;
            $detail_po['C111_QtyBal2'] = ($balance_po + $qty_di_be) - $request->qty ;
            $update_po = DelConfirm::update_balance_po($trc_type_id_po,$month_id_po,$trc_id_po,$rev_id_po,$line_id_po,$detail_po);
            if ($update_po) {

                DelConfirm::update_di($trc_type_id_po, $month_id_po, $trc_id_po, $rev_id_po, $line_id_po, $trc_type_id, $month_id, $trc_id, $rev_id, $line_id, $detail);  
                $index = [  
                    'C010_TrcTypeID' =>  $trc_type_id,   
                    'C011_Month' =>  $month_id,       
                    'C012_TrcID' =>  $trc_id, 
                    'C000_LineSrc' =>  $line_id,
                ] ;   
                DelConfirm::delete_label($index); 
                DelConfirm::update_total_pallet_by_detail($trc_type_id,$month_id,$trc_id,$line_id_po); 
                $data['process'] = 1 ;
                $data['msg'] = 'Success update ' ; 
            
            } else {
                $data['process'] = 0 ;
                $data['msg'] = 'Fail update' ;
            }
        }
         
        echo json_encode($data);  
    }

    public function front_table(Request $request){  
        date_default_timezone_set('Asia/Jakarta');

        $flow_id = ($request->flow_id === null ? 28 : $request->flow_id) ;
        $status = ($request->position == 0 ? 1 : $request->position) ;  
        $vendor_id = Auth::user()->partner_id ;  
        $search = $request->front_table_search ;   
         
        $yearX = substr(date('Y'),2,2).date('m') ;
        $year = ($request->range_date === null ? $yearX : $request->range_date) ;   
        $columns = array(  
          0 =>'a.C000_SysID', 
          1 =>'a.C050_DocNum',
          2 =>'a.C050_DocDate',
          3 =>'b.C051_PONum',
          4 =>'a.C050_ExtDocNum', 
          5 =>'a.C013_DraftReadyApprCancel',    
        );   
        $totalData = DelConfirm::get_transaction_list($year, $flow_id, $status, $vendor_id,  $search)->count(); 
        $totalFiltered = $totalData;  
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = ($request->input('order.0.column')==0 ? $columns[1] : $columns[$request->input('order.0.column')]);
        $dir = ($request->input('order.0.column')==0 ? 'desc' : $request->input('order.0.dir')) ; 
        if(empty($search))
        {            
        $posts = DelConfirm::get_transaction_list($year, $flow_id, $status, $vendor_id,  $search)
        ->offset($start)
        ->limit($limit)
        ->orderBy($order,$dir)
        ->get();
        } else {
        
        $posts =  DelConfirm::get_transaction_list($year, $flow_id, $status, $vendor_id,  $search) 
        ->offset($start)
        ->limit($limit) 
        ->orderBy($order,$dir)
        ->get();  
        $totalFiltered = DelConfirm::get_transaction_list($year, $flow_id, $status, $vendor_id,  $search)->count();
        } 
        $data = array();
        if(!empty($posts))
        { 
        $no = $start ;
        foreach ($posts as $post)
        { 
        $no++; 
        $trc_id = str_replace("=","-", Crypt::encryptString($post->C010_TrcTypeID.'_'.$post->C011_Month.'_'.$post->C000_SysID.'_'.$post->C050_Rev)) ;  
        $sys_id = str_replace("=","-", Crypt::encryptString($post->C010_TrcTypeID.'_'.$post->C011_Month.'_'.$post->C000_SysID.'_'.$post->C050_Rev.'_'.$post->C013_DraftReadyApprCancel.'_0')) ;  
        // $sys_id = "'".str_replace("=","-", $sys_id).'_'.$no."'" ;  
        $ref_form = DelConfirm::find_trc_name($post->C010_TrcTypeID, $flow_id) ;    
    
         if ($post->C013_DraftReadyApprCancel == 3) {
            $button = '
            <a href="del_confirm/open_doc?ref_doc='.$sys_id.'&ref_form='.$ref_form.'" title="Confirm">
            <span class="svg-icon svg-icon-primary svg-icon-2x" style="cursor: pointer;" id="'.$trc_id.'">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="black"/>
                <path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="black"/>
                <path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="black"/>
                </svg>
            </span></a>' ; 
         } else {
            $button = '
            <a href="del_confirm/open_doc?ref_doc='.$sys_id.'&ref_form='.$ref_form.'" title="Draft">
            <span class="svg-icon svg-icon-primary svg-icon-2x" style="cursor: pointer;" id="'.$trc_id.'">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="black"/>
                <path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM12 16.8C11 16.8 10.2 16.4 9.5 15.8C8.8 15.1 8.5 14.3 8.5 13.3C8.5 12.8 8.59999 12.3 8.79999 11.9L10 13.1V10.1C10 9.50001 9.6 9.10001 9 9.10001H6L7.29999 10.4C6.79999 11.3 6.5 12.2 6.5 13.3C6.5 14.8 7.10001 16.2 8.10001 17.2C9.10001 18.2 10.5 18.8 12 18.8C12.6 18.8 13 18.3 13 17.8C13 17.2 12.6 16.8 12 16.8ZM16.7 16.2C17.2 15.3 17.5 14.4 17.5 13.3C17.5 11.8 16.9 10.4 15.9 9.39999C14.9 8.39999 13.5 7.79999 12 7.79999C11.4 7.79999 11 8.19999 11 8.79999C11 9.39999 11.4 9.79999 12 9.79999C12.9 9.79999 13.8 10.2 14.5 10.8C15.2 11.5 15.5 12.3 15.5 13.3C15.5 13.8 15.4 14.3 15.2 14.7L14 13.5V16.5C14 17.1 14.4 17.5 15 17.5H18L16.7 16.2Z" fill="black"/>
                <path opacity="0.3" d="M12 16.8C11 16.8 10.2 16.4 9.5 15.8C8.8 15.1 8.5 14.3 8.5 13.3C8.5 12.8 8.59999 12.3 8.79999 11.9L7.29999 10.4C6.79999 11.3 6.5 12.2 6.5 13.3C6.5 14.8 7.10001 16.2 8.10001 17.2C9.10001 18.2 10.5 18.8 12 18.8C12.6 18.8 13 18.3 13 17.8C13 17.2 12.6 16.8 12 16.8Z" fill="black"/>
                <path opacity="0.3" d="M15.5 13.3C15.5 13.8 15.4 14.3 15.2 14.7L16.7 16.2C17.2 15.3 17.5 14.4 17.5 13.3C17.5 11.8 16.9 10.4 15.9 9.39999C14.9 8.39999 13.5 7.79999 12 7.79999C11.4 7.79999 11 8.19999 11 8.79999C11 9.39999 11.4 9.79999 12 9.79999C12.9 9.79999 13.8 10.2 14.5 10.8C15.1 11.5 15.5 12.4 15.5 13.3Z" fill="black"/>
                </svg>
            </span></a>' ;
         }
        
     

        if ($post->C013_DraftReadyApprCancel==0) {
                $doc_status =  '<span class="badge badge-danger">Cancel</span>' ;
            } elseif ($post->C013_DraftReadyApprCancel==1) {
                $doc_status =  '<span class="badge badge-info">Draft</span>' ; 
            } else {
                $doc_status = '<span class="badge badge-primary">Confirm</span>' ;
            }
     
        $nestedData['no'] = $no ; 
        $nestedData['docnum'] = $post->C050_DocNum ;  
        $nestedData['ponum'] = $post->C051_PONum ;  
        $nestedData['shipnum'] = $post->C050_ExtDocNum ;	
        $nestedData['docdate'] = AppModel::local_date_formate_name(substr($post->C050_DocDate,0,10)) ;    
        $nestedData['status'] = $doc_status ;  
        $nestedData['action'] = $button ;  
        $data[] = $nestedData; 
        }
        } 
        $json_data = array(
        "draw"            => intval($request->input('draw')),  
        "recordsTotal"    => intval($totalData),  
        "recordsFiltered" => intval($totalFiltered), 
        "data"            => $data   
        ); 
        echo json_encode($json_data); 
    } 

    public function item_po_list(Request $request)
      {   
        $search = $request->search_item ;    
        $search_po_form = ($request->search_po_form == '' ? '0_0_0_0' : $request->search_po_form) ;    
        $str_po = explode("_", $search_po_form) ;
        $trc_type_id = $str_po[0] ; 
        
        if ($trc_type_id == 1100 || $trc_type_id == 1130) {
            $columns = array(  
                0 =>'C000_SysID',  
                1 =>'ItemNum_Req',
                2 =>'ItemName_Req',
                3 =>'C110_Qty', 
                4 =>'C111_QtyBal2',    
            );  
        } else {
            $columns = array(  
                0 =>'C000_SysID',  
                1 =>'ItemNum',
                2 =>'ItemName',
                3 =>'C110_Qty', 
                4 =>'C111_QtyBal2',    
            ); 
        }
         
        $totalData = DelConfirm::get_item_po_list($search_po_form, $search)->count(); 
        $totalFiltered = $totalData;  
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = ($request->input('order.0.column')==0 ? $columns[1] : $columns[$request->input('order.0.column')]);
        $dir = ($request->input('order.0.column')==0 ? 'desc' : $request->input('order.0.dir')) ; 
        if(empty($search))
        {            
        $posts = DelConfirm::get_item_po_list($search_po_form, $search)
        ->offset($start)
        ->limit($limit)
        ->orderBy($order,$dir)
        ->get();
        } else {
        
        $posts =  DelConfirm::get_item_po_list($search_po_form, $search) 
        ->offset($start)
        ->limit($limit) 
        ->orderBy($order,$dir)
        ->get();  
        $totalFiltered = DelConfirm::get_item_po_list($search_po_form, $search)->count();
        } 
        $data = array();
        if(!empty($posts))
        { 
        $no = $start ;
        foreach ($posts as $post)
        { 
        $no++;  
        if ($trc_type_id == 1100 || $trc_type_id == 1130) {
            $nestedData['no'] = $no ; 
            $nestedData['delivery_date'] = AppModel::local_date_formate_name(substr($post->C063_dtDelivery,0,10)) ;  
            $nestedData['item_no'] = $post->ItemNum_Req ;  
            $nestedData['item_name'] = $post->ItemName_Req ;	
            $nestedData['qty'] = number_format($post->C110_Qty,0) ;    
            $nestedData['balance'] = number_format($post->C111_QtyBal2,0) ;   
        } else {
            $nestedData['no'] = $no ; 
            $nestedData['delivery_date'] = AppModel::local_date_formate_name(substr($post->C063_dtDelivery,0,10)) ;  
            $nestedData['item_no'] = $post->ItemNum ;  
            $nestedData['item_name'] = $post->ItemName ;	
            $nestedData['qty'] = number_format($post->C110_Qty,0) ;    
            $nestedData['balance'] = number_format($post->C111_QtyBal2,0) ;  
        }
        
        $data[] = $nestedData; 
        }
        } 
        $json_data = array(
        "draw"            => intval($request->input('draw')),  
        "recordsTotal"    => intval($totalData),  
        "recordsFiltered" => intval($totalFiltered), 
        "data"            => $data   
        ); 
        echo json_encode($json_data); 
      }

      public function listing_po(Request $request)
      {  
        $vendor_id = Auth::user()->partner_id ;    
        $searchTerm = $request->searchTerm;      
        $str = explode("_", $request->flow_id);    
        $flow_id = $str[1] ; 
        $trc_type_id = $str[2] ; 
        $range_date = $request->range_date;      
        $page = $request->page ;
        $resultCount = 25; 
        $offset = ($page - 1) * $resultCount;
        $db = array();    
        $db = DelConfirm::get_listing_po($trc_type_id, $vendor_id, $offset, $resultCount, $flow_id, $range_date, $searchTerm) ;  
        $count = DelConfirm::get_count_listing_po($trc_type_id, $vendor_id, $flow_id, $range_date, $searchTerm) ; 
        $endCount = $offset + $resultCount ; 
        $morePages = $count > $endCount ;  
        $results = array(
            "results" => $db,
            "pagination" => array(
            "more" => $morePages
        )
        );     
        echo json_encode($results);
      }

      public function detail_order(Request $request)
      {   
        $str_po = explode("_", Crypt::decryptString(str_replace("-","=", $request->ref_doc_po))) ; 
        $str_di = explode("_", Crypt::decryptString(str_replace("-","=", $request->ref_doc))) ;
        $trc_type_id_po = $str_po[0] ;
        $month_id_po = $str_po[1] ;
        $trc_id_po = $str_po[2] ;
        $rev_id_po = $str_po[3] ;

        $trc_type_id = $str_di[0] ;
        $month_id = $str_di[1] ;
        $trc_id = $str_di[2] ;
        $rev_id = $str_di[3] ;

        $search = $request->front_table_search ;   
        if ($trc_type_id_po == 1100 || $trc_type_id_po == 1130) {
            $columns = array(  
                0 =>'a.C000_SysID',  
                1 =>'a.ItemNum_Req',
                2 =>'a.ItemName_Req',
                3 =>'a.C110_Qty', 
                4 =>'a.C111_QtyBal2',    
                5 =>'c.C110_Qty',    
                6 =>'c.TotalPallet',    
            );  
        } else {
            $columns = array(  
                0 =>'a.C000_SysID',  
                1 =>'a.ItemNum',
                2 =>'a.ItemName',
                3 =>'a.C110_Qty', 
                4 =>'a.C111_QtyBal2',    
                5 =>'c.C110_Qty',    
                6 =>'c.TotalPallet',    
            );  
        }
         
        $totalData = DelConfirm::get_detail_order_list($str_po, $str_di, $search)->count(); 
        $totalFiltered = $totalData;  
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = ($request->input('order.0.column')==0 ? $columns[1] : $columns[$request->input('order.0.column')]);
        $dir = ($request->input('order.0.column')==0 ? 'desc' : $request->input('order.0.dir')) ; 
        if(empty($search))
        {            
        $posts = DelConfirm::get_detail_order_list($str_po, $str_di, $search)
        ->offset($start)
        ->limit($limit)
        ->orderBy($order,$dir)
        ->get();
        } else {
        
        $posts =  DelConfirm::get_detail_order_list($str_po, $str_di, $search) 
        ->offset($start)
        ->limit($limit) 
        ->orderBy($order,$dir)
        ->get();  
        $totalFiltered = DelConfirm::get_detail_order_list($str_po, $str_di, $search)->count();
        } 
        $data = array();
        if(!empty($posts))
        { 
        $no = $start ;
        foreach ($posts as $post)
        { 
        $no++;  
        $line_id =  str_replace("=","-", Crypt::encryptString($trc_type_id_po.'_'.$month_id_po.'_'.$trc_id_po.'_'.$rev_id_po.'_'.$trc_type_id.'_'.$month_id.'_'.$trc_id.'_'.$rev_id.'_'.$post->C000_SysID.'_'.$post->C000_SysID_DI.'_'.$post->C100_ItemIntID)) ;  
        $sysid = "'".$line_id."'" ;

        if ($trc_type_id_po == 1100 || $trc_type_id_po == 1130) {
            $nestedData['no'] = $no ;    
            $nestedData['itemno'] = '<span class="text-sm detailItemS detailItemS-'.$line_id.'">'.$post->ItemNum_Req.'</span>' ;  
            $nestedData['itemname'] = '<span class="text-sm detailItemS detailItemS-'.$line_id.'">'.$post->ItemName_Req.'</span>' ;	   
        } else {
            $nestedData['no'] = $no ;  
            $nestedData['itemno'] = '<span class="text-sm detailItemS detailItemS-'.$line_id.'">'.$post->ItemNum.'</span>' ;  
            $nestedData['itemname'] = '<span class="text-sm detailItemS detailItemS-'.$line_id.'">'.$post->ItemName.'</span>' ;	  
        }
            $nestedData['qty'] = '<input class="form-control detailItemS detailItemS-'.$line_id.' text-sm" style="height: 20px; border: none; width: 80px; padding: 0px 0px; color: #7e8299; font-size: 12.35px;  background: transparent; vertical-align: top; font-family: Poppins;"  type="text" readonly
            value="'.number_format($post->QtyPO,0).'"/>' ;    
            $nestedData['balance'] = '<input type="hidden" id="bal_'.$line_id.'" readonly value="'.number_format($post->BalPO,0).'"/><input type="hidden" id="balbe_'.$line_id.'" readonly value="'.number_format($post->BalPO+$post->QtyDI,0).'"/><input class="form-control number_format_coma detailItemS detailItemS-'.$line_id.' text-sm" id="result_'.$line_id.'" style="height: 20px; border: none; width: 80px; padding: 0px 0px; color: #7e8299; font-size: 12.35px;  background: transparent; vertical-align: top; font-family: Poppins;"  type="text" readonly
            value="'.number_format($post->BalPO,0).'"/>' ;   
            if ($post->BalPO > 0 || $post->QtyDI > 0) {
                $nestedData['delivery'] = '<input onkeyup="calculateSource(event, '.$sysid.')" class="form-control number_format_coma detailItemS detailItemS-'.$line_id.'  
                qty_'.$line_id.' text-sm" id="'.$line_id.'" style="height: 20px; border: none; width: 80px; padding: 0px 0px; color: #7e8299; font-size: 12.35px; 
                background: transparent; vertical-align: top; font-family: Poppins;"  type="text" value="'.number_format($post->QtyDI,0).'"/>' ;  
            } else {
                $nestedData['delivery'] = ($post->QtyDI==0?'':number_format($post->QtyDI,0)) ; 
            } 
                $nestedData['pack'] = '<input onclick="packForm(event, '.$sysid.')" class="form-control cursor-pointer number_format_coma detailItemS detailItemS-'.$line_id.'  
                pallet_'.$line_id.' text-sm" id="pallet_'.$line_id.'" style="height: 20px; border: none; width: 40px; padding: 0px 0px; color: #7e8299; font-size: 12.35px; 
                background: transparent; vertical-align: top; font-family: Poppins;"  type="text" value="'.number_format($post->TotalPallet,0).'" readonly/>' ; 
            
            
        $data[] = $nestedData; 
        }
        } 
        $json_data = array(
        "draw"            => intval($request->input('draw')),  
        "recordsTotal"    => intval($totalData),  
        "recordsFiltered" => intval($totalFiltered), 
        "data"            => $data   
        ); 
        echo json_encode($json_data); 
      } 

      public function checking_all_rule(Request $request){
        $str_po = explode("_", Crypt::decryptString(str_replace("-","=", $request->ref_doc_po))) ; 
        $str_di = explode("_", Crypt::decryptString(str_replace("-","=", $request->ref_doc))) ;
        $trc_type_id_po = $str_po[0] ;
        $month_id_po = $str_po[1] ;
        $trc_id_po = $str_po[2] ;
        $rev_id_po = $str_po[3] ;

        $trc_type_id = $str_di[0] ;
        $month_id = $str_di[1] ;
        $trc_id = $str_di[2] ;
        $rev_id = $str_di[3] ;
        
        $check_1 = DelConfirm::check_head_di($trc_type_id, $month_id, $trc_id, $rev_id);  
        $check_2 = DelConfirm::check_detail_di($trc_type_id, $month_id, $trc_id, $rev_id); 
        
        if($check_1==0) {
            $data['process'] = 0 ; 
            $data['msg'] = 'Check Shipdate or Shipnum !' ;
        } else if($check_2==0) {
            $data['process'] = 0 ; 
            $data['msg'] = 'Select at least one item to process!' ;
        } else {
                $data['process'] = 1 ; 
                $data['msg'] = 'Success' ; 
        }  
            echo json_encode($data); 
    }

    public function checking_revise(Request $request){
        $str_po = explode("_", Crypt::decryptString(str_replace("-","=", $request->ref_doc_po))) ; 
        $str_di = explode("_", Crypt::decryptString(str_replace("-","=", $request->ref_doc))) ;
        $trc_type_id_po = $str_po[0] ;
        $month_id_po = $str_po[1] ;
        $trc_id_po = $str_po[2] ;
        $rev_id_po = $str_po[3] ;

        $trc_type_id = $str_di[0] ;
        $month_id = $str_di[1] ;
        $trc_id = $str_di[2] ;
        $rev_id = $str_di[3] ;
        
        $check_1 = DelConfirm::check_as_source_po($trc_type_id_po, $month_id_po, $trc_id_po, $rev_id_po);   
        $check_2 = DelConfirm::check_connecting_di($trc_type_id, $month_id, $trc_id, $rev_id);   
        
        if($check_1==11) {
            $data['process'] = 0 ; 
            $data['msg'] = 'PO is being used in another DI !' ;
        } else if($check_2==0) {
            $data['process'] = 0 ; 
            $data['msg'] = 'This document has been confirm by SAI!' ;
        } else {
            $data['process'] = 1 ; 
            $data['msg'] = 'Success' ; 
        }  
        echo json_encode($data); 
    }

    public function proceed_to_draft(Request $request){
        date_default_timezone_set('Asia/Jakarta');
        $str = explode("_", $request->flow_id) ;    
        $trc_type_id = $str[0] ;
        $flow_id = $str[1] ;
        $trc_type_id_b = $str[2] ; 

        $str_po = explode("_", $request->search_po_form) ;    
        $trc_type_id_po = $str_po[0] ;
        $month_id_po = $str_po[1] ;
        $trc_id_po = $str_po[2] ;
        $rev_id_po = $str_po[3] ;

        $CreateDateSQL = date('Y-m-d') ;
        $CreateDate = date('d-m-Y') ; 
        $CreateTime = date ("H:i:s") ;
        $current_year = date('Y') ; 
        $estimation_date = strtotime('2 day',strtotime($CreateDate));
        $estimation_date = date('d/m/Y', $estimation_date);
        $user_id = Auth::user()->reg_id ;
        $first_name = Auth::user()->first_name ; 

        $month_id = substr(date('Y'),2,2).''.date('m') ;   
        $trc_id = DelConfirm::find_trc_id_proc_head($trc_type_id, $month_id) ; 

        $trc_unix_id = Crypt::encryptString($trc_type_id.'_'.$month_id.'_'.$trc_id.'_0_1') ;    
        $trc_unix_id = str_replace("=","-", $trc_unix_id) ;  
        
        $db_trc_type = DelConfirm::transaction_type_properties($trc_type_id, $flow_id) ;  
        if($db_trc_type->count()>0){
            foreach($db_trc_type AS $t){
                $c010 = $t->c10 ; 
                $trc_code = $t->trc_code ;  
                $doc_num = $t->doc_num ;  
                $next_doc_num = DelConfirm::find_doc_num_proc($current_year, $c010) ; 
                $data['doc_num'] = 'SAI/'.$doc_num.'/'.substr(date('Y'),2,2).''.sprintf('%05s',$next_doc_num) ; 
                $act_mgr_id = $t->act_mgr_id ; 
                $type_src_id = $t->type_src_id ;  
            }  
        } else {
            $c010 = 0 ;      
            $trc_code = '' ; 
            $act_mgr_id = NULL ; 
            $type_src_id = NULL ; 
            $next_doc_num = 0 ;
            $data['doc_num'] = NULL ;   
        } 

        $detail_history['C010_TrcTypeID'] = $trc_type_id ;
        $detail_history['C011_Month'] = $month_id ;
        $detail_history['C012_JDN_ID'] = $trc_id ;
        $detail_history['C050_Rev'] = 0 ;
        $detail_history['C000_SysID'] = 1 ;
        $detail_history['C014_ActMgrID'] = $act_mgr_id ;
        $detail_history['C045_DTime'] = $CreateDateSQL.' '.$CreateTime; 
        $detail_history['C045_UserID'] = $user_id ;
        $detail_history['C045_UserName'] = $first_name ;
        $detail_history['C046_StartSession'] = 'CreateNew' ;  

        $detail['C010_TrcTypeID'] = $trc_type_id ;
        $detail['C011_Month'] = $month_id ;
        $detail['C000_SysID'] = $trc_id ;
        $detail['C050_Rev'] = 0 ;
        $detail['C013_TrcTypeSrcID'] = $type_src_id ;
        $detail['C013_DraftReadyApprCancel'] = 1 ;
        $detail['C014_ActMgrID'] = $act_mgr_id ; 
        $detail['C017_UserGrpFlowID_From'] = $flow_id ; 
        $detail['C017_UserGrpFlowID_To'] = $flow_id ; 
        $detail['C017_UserGrpFlowTo'] = $trc_code ;
        $detail['C017_UserGrpFlowFrom'] = $trc_code ; 
        $detail['C018_FlowTypeID'] = 3 ;
        $detail['C045_UserID'] = $user_id ;
        $detail['C045_UserName'] = $first_name ;
        $detail['C045_DTime'] = $CreateDateSQL.' '.$CreateTime;
        $detail['C090_EstDlvDate'] = AppModel::post_date_formate($estimation_date) ; 
        $detail['C050_DocNum'] = $data['doc_num'] ;
        $detail['C050_DocDate'] = $CreateDateSQL ;
        $detail['C085_isPPN'] = 1 ;
        $detail['C070_CurrencyID'] = 1 ;
        $detail['C071_Rate'] = 1 ; 

        $detail_node['C010_TrcTypeID'] = $trc_type_id ;
        $detail_node['C011_Month'] = $month_id ;
        $detail_node['C000_SysID'] = $trc_id ;
        $detail_node['C050_Rev'] = 0 ;
        $detail_node['C013_DraftReadyApprCancel'] = 1 ;
        $detail_node['C013_TrcTypeSrcID'] = $type_src_id ;
        $detail_node['C014_ActMgrID'] = $act_mgr_id ; 
        $detail_node['C017_UserGrpFlowID_From'] = $flow_id ;
        $detail_node['C017_UserGrpFlowID_To'] = $flow_id ;
        $detail_node['C017_UserGrpFlowFrom'] = $trc_code ;
        $detail_node['C017_UserGrpFlowTo'] = $trc_code ;
        $detail_node['C018_FlowTypeID'] = 3 ;
        $detail_node['C020_NodeHistoryID_Active'] = 1 ;
        $detail_node['C045_UserID'] = $user_id ;
        $detail_node['C045_UserName'] = $first_name ;
        $detail_node['C045_DTime'] = $CreateDateSQL.' '.$CreateTime;
        $detail_node['C045_UserNameUpdate'] = $first_name ;
        $detail_node['C045_DTimeLasUpdate'] = $CreateDateSQL.' '.$CreateTime ;
        $detail_node['C050_DocNum'] = $data['doc_num'] ;
        $detail_node['C050_DocDate'] = $CreateDateSQL ;  
        $detail_node['PositionID'] = 0 ;

        $detail_counter['TableName'] = 'T500_Proc' ;
        $detail_counter['Opsi_1'] = $trc_type_id ;
        $detail_counter['YearMonth'] = $month_id ;
        $detail_counter['CurrentSysID'] = $trc_id ;
        $detail_counter['Opsi_2'] = 0 ;  

        $detail_counter_2['C020'] = date('Y') ;
        $detail_counter_2['C010'] = $c010 ;
        $detail_counter_2['C030'] = $next_doc_num ;  

        $insert_proc = DelConfirm::create_proc($detail);
        $insert_node = DelConfirm::create_node($detail_node);
        $insert_node_history = DelConfirm::create_node_history($detail_history);
        $create_connection = DelConfirm::create_connection($trc_type_id_po, $month_id_po, $trc_id_po, $rev_id_po, $trc_type_id, $month_id, $trc_id, 0);
        $copy_to_node = DelConfirm::transfer_document_node($trc_type_id_po, $month_id_po, $trc_id_po, $rev_id_po, $trc_type_id, $month_id, $trc_id, 0) ;
        $copy_to_proc = DelConfirm::transfer_document_proc($trc_type_id_po, $month_id_po, $trc_id_po, $rev_id_po, $trc_type_id, $month_id, $trc_id, 0) ;
  
        $check_doc_counter = DelConfirm::check_doc_counter($c010); 
        if($check_doc_counter>0){
            DelConfirm::update_t100_docnum1 ($c010,$detail_counter_2); 
        } else {
            DelConfirm::insert_t100_docnum1 ($detail_counter_2);  
        }

        $check_doc_counter_2 = DelConfirm::check_doc_counter_2 ($trc_type_id, $month_id);  
        if($check_doc_counter_2>0){
            DelConfirm::update_t_000_Counter($trc_type_id, $month_id, $detail_counter); 
        } else {
            DelConfirm::insert_t_000_Counter($detail_counter); 
        }  

        if($copy_to_proc) {
            $data['ref_doc'] = $trc_unix_id ; 
            $data['ref_form'] = $trc_code ; 
            $data['process'] = 1 ; 
            $data['msg'] = 'Success create' ;
        } else {
            $data['ref_doc'] = '' ; 
            $data['ref_form'] = '' ; 
            $data['trc_unix_id'] = '' ;  
            $data['process'] = 0 ; 
            $data['msg'] = 'Fail' ; 
        }  
        echo json_encode($data); 
    }

    public function document_confirm(Request $request){
        $str_po = explode("_", Crypt::decryptString(str_replace("-","=", $request->ref_doc_po))) ; 
        $str_di = explode("_", Crypt::decryptString(str_replace("-","=", $request->ref_doc))) ;
        
        $trc_type_id_po = $str_po[0] ;
        $month_id_po = $str_po[1] ;
        $trc_id_po = $str_po[2] ;
        $rev_id_po = $str_po[3] ;

        $trc_type_id = $str_di[0] ;
        $month_id = $str_di[1] ;
        $trc_id = $str_di[2] ;
        $rev_id = $str_di[3] ;   
    
        $status_open_close = DelConfirm::get_status_open_close($trc_type_id_po,$month_id_po,$trc_id_po,$rev_id_po);
        $doc_data = DelConfirm::get_head_properties($str_di); 
        foreach($doc_data as $row) { 
            $flow_id = $row->C017_UserGrpFlowID_To ; 
            $data['ref_form'] = $row->C017_UserGrpFlowFrom ; 
        }  
        date_default_timezone_set('Asia/Jakarta');
        $my_username =  Auth::user()->username ;   
        $time_current = date('Y-m-d').' '.date("H:i:s");   
        $detail['C013_DraftReadyApprCancel'] = 3 ;
        $detail['C045_UserNameUpdate'] = $my_username;
        $detail['C045_DTimeLasUpdate'] = $time_current ; 
        $detail_node['C013_DraftReadyApprCancel'] = 3 ;  
        $detail_as_source['C012_TrcOpenClose1'] = $status_open_close ;  
        $index['C010_TrcTypeID'] = $trc_type_id ;
        $index['C011_Month'] = $month_id ;
        $index['C000_SysID'] = $trc_id ;
        $index['C050_Rev'] = $rev_id ; 
        $index_po['C010_TrcTypeID'] = $trc_type_id_po ;
        $index_po['C011_Month'] = $month_id_po ;
        $index_po['C000_SysID'] = $trc_id_po ;
        $index_po['C050_Rev'] = $rev_id_po ; 
        
        $update_head = DelConfirm::update_head($detail, $detail_node, $index);   
        if($update_head) {
            DelConfirm::update_as_source($detail_as_source, $index_po); 
            DelConfirm::generate_as_source($trc_type_id, $month_id, $trc_id, $rev_id, $flow_id); 
            $sys_id = str_replace("=","-", Crypt::encryptString($trc_type_id.'_'.$month_id.'_'.$trc_id.'_'.$rev_id.'_3')).'_1' ;   
            $data['trc_unix_id'] = $sys_id ;  
            $data['process'] = 1 ; 
            $data['msg'] = 'Success Confirm' ;
        } else {
            $data['process'] = 0 ; 
            $data['msg'] = 'Fail, Please reload and try again!' ; 
        }  
        echo json_encode($data); 
    }

    public function cancel_confirm(Request $request){
        $str_po = explode("_", Crypt::decryptString(str_replace("-","=", $request->ref_doc_po))) ; 
        $str_di = explode("_", Crypt::decryptString(str_replace("-","=", $request->ref_doc))) ;
        
        $trc_type_id_po = $str_po[0] ;
        $month_id_po = $str_po[1] ;
        $trc_id_po = $str_po[2] ;
        $rev_id_po = $str_po[3] ;

        $trc_type_id = $str_di[0] ;
        $month_id = $str_di[1] ;
        $trc_id = $str_di[2] ;
        $rev_id = $str_di[3] ;   
    
        $doc_data = DelConfirm::get_head_properties($str_di); 
        foreach($doc_data as $row) { 
            $flow_id = $row->C017_UserGrpFlowID_To ; 
            $data['ref_form'] = $row->C017_UserGrpFlowFrom ; 
        }  
        date_default_timezone_set('Asia/Jakarta');
        $my_username =  Auth::user()->username ;   
        $time_current = date('Y-m-d').' '.date("H:i:s");   
        $detail['C013_DraftReadyApprCancel'] = 1 ;
        $detail['C045_UserNameUpdate'] = $my_username;
        $detail['C045_DTimeLasUpdate'] = $time_current ; 
        $detail_node['C013_DraftReadyApprCancel'] = 1 ;  
        $detail_as_source['C012_TrcOpenClose1'] = 11 ;  
        $index['C010_TrcTypeID'] = $trc_type_id ;
        $index['C011_Month'] = $month_id ;
        $index['C000_SysID'] = $trc_id ;
        $index['C050_Rev'] = $rev_id ; 

        $index_po['C010_TrcTypeID'] = $trc_type_id_po ;
        $index_po['C011_Month'] = $month_id_po ;
        $index_po['C000_SysID'] = $trc_id_po ;
        $index_po['C050_Rev'] = $rev_id_po ; 
        

        $update_head = DelConfirm::update_head($detail, $detail_node, $index);   
        if($update_head) {
        DelConfirm::update_as_source($detail_as_source, $index_po);   
        DelConfirm::delete_as_source($trc_type_id, $month_id, $trc_id, $rev_id);  
            $data['process'] = 1 ; 
            $data['msg'] = 'Success Cancel' ;
        } else {
            $data['process'] = 0 ; 
            $data['msg'] = 'Fail, Please reload and try again!' ; 
        }  
        echo json_encode($data); 
    }

    public function return_to_draft(Request $request){
        $str_po = explode("_", Crypt::decryptString(str_replace("-","=", $request->ref_doc_po))) ; 
        $str_di = explode("_", Crypt::decryptString(str_replace("-","=", $request->ref_doc))) ;
        
        $trc_type_id_po = $str_po[0] ;
        $month_id_po = $str_po[1] ;
        $trc_id_po = $str_po[2] ;
        $rev_id_po = $str_po[3] ;

        $trc_type_id = $str_di[0] ;
        $month_id = $str_di[1] ;
        $trc_id = $str_di[2] ;
        $rev_id = $str_di[3] ;   
        $rev_next_id = $str_di[3] + 1 ;   
    
        $doc_data = DelConfirm::get_head_properties($str_di); 
        foreach($doc_data as $row) { 
            $flow_id = $row->C017_UserGrpFlowID_To ; 
            $data['ref_form'] = $row->C017_UserGrpFlowFrom ; 
        }  
        date_default_timezone_set('Asia/Jakarta');
        $my_username =  Auth::user()->username ;   
        $time_current = date('Y-m-d').' '.date("H:i:s");   
        $detail['C013_DraftReadyApprCancel'] = 1 ;
        $detail['C045_UserNameUpdate'] = $my_username;
        $detail['C045_DTimeLasUpdate'] = $time_current ; 
        $detail['C050_Rev'] = $rev_next_id ; 
        $detail_node['C050_Rev'] = $rev_next_id ; 
        $detail_node['C013_DraftReadyApprCancel'] = 1 ;

        $index['C010_TrcTypeID'] = $trc_type_id ;
        $index['C011_Month'] = $month_id ;
        $index['C000_SysID'] = $trc_id ;
        $index['C050_Rev'] = $rev_id ; 

        $detail_as_source['C012_TrcOpenClose1'] = 11 ;  
        $index_po['C010_TrcTypeID'] = $trc_type_id_po ;
        $index_po['C011_Month'] = $month_id_po ;
        $index_po['C000_SysID'] = $trc_id_po ;
        $index_po['C050_Rev'] = $rev_id_po ; 
        
        $index_connection['C034_TrcTypeID_To'] = $trc_type_id ;
        $index_connection['C035_Month_To'] = $month_id ;
        $index_connection['C036_TrcID_To'] = $trc_id ;
        $index_connection['C050_Rev_To'] = $rev_id ; 
        $detail_connection['C050_Rev_To'] = $rev_next_id ;  
        $update_connection = DelConfirm::update_connection($detail_connection, $index_connection);   

        $index_detail['C010_TrcTypeID'] = $trc_type_id ;
        $index_detail['C011_Month'] = $month_id ;
        $index_detail['C012_TrcID'] = $trc_id ;
        $index_detail['C050_Rev'] = $rev_id ; 
        $detail_update['C050_Rev'] = $rev_next_id ; 
        $update_detail_rev = DelConfirm::update_detail_rev($detail_update, $index_detail);  
        
        $update_head = DelConfirm::update_head($detail, $detail_node, $index);   
        if($update_head) {
        DelConfirm::update_as_source($detail_as_source, $index_po);   
        DelConfirm::delete_as_source($trc_type_id, $month_id, $trc_id, $rev_id); 
            $str_po = explode("_", Crypt::decryptString(str_replace("-","=", $request->ref_doc_po))) ; 
            $str_di = explode("_", Crypt::decryptString(str_replace("-","=", $request->ref_doc))) ; 
            $data['ref_doc'] = str_replace("=","-", Crypt::encryptString($trc_type_id.'_'.$month_id.'_'.$trc_id.'_'.$rev_next_id.'_1')) ; 
            $data['ref_doc_po'] = $request->ref_doc_po ;
            $data['process'] = 1 ; 
            $data['msg'] = 'Success Cancel' ;
        } else {
            $data['process'] = 0 ; 
            $data['msg'] = 'Fail, Please reload and try again!' ; 
        }  
        echo json_encode($data); 
    }

    public function document_delete(Request $request){
        $str_po = explode("_", Crypt::decryptString(str_replace("-","=", $request->ref_doc_po))) ; 
        $str_di = explode("_", Crypt::decryptString(str_replace("-","=", $request->ref_doc))) ;
        
        $trc_type_id_po = $str_po[0] ;
        $month_id_po = $str_po[1] ;
        $trc_id_po = $str_po[2] ;
        $rev_id_po = $str_po[3] ;

        $trc_type_id = $str_di[0] ;
        $month_id = $str_di[1] ;
        $trc_id = $str_di[2] ;
        $rev_id = $str_di[3] ;  

        date_default_timezone_set('Asia/Jakarta');
        $my_username =  Auth::user()->username ;   
        $time_current = date('Y-m-d').' '.date("H:i:s");   
        $detail['C013_DraftReadyApprCancel'] = 0 ;
        $detail['C045_UserNameUpdate'] = $my_username;
        $detail['C045_DTimeLasUpdate'] = $time_current ; 
        $detail_node['C013_DraftReadyApprCancel'] = 0 ;  
        $index['C010_TrcTypeID'] = $trc_type_id ;
        $index['C011_Month'] = $month_id ;
        $index['C000_SysID'] = $trc_id ;
        $index['C050_Rev'] = $rev_id ; 

        $detail_as_source['C012_TrcOpenClose1'] = 1 ;  
        $index_po['C010_TrcTypeID'] = $trc_type_id_po ;
        $index_po['C011_Month'] = $month_id_po ;
        $index_po['C000_SysID'] = $trc_id_po ;
        $index_po['C050_Rev'] = $rev_id_po ; 
        
        $rollback_detail = DelConfirm::rollback_detail($trc_type_id_po, $month_id_po, $trc_id_po, $rev_id_po, $trc_type_id, $month_id, $trc_id, $rev_id);    
        if ($rollback_detail) {
            $update_head = DelConfirm::update_head($detail, $detail_node, $index);   
            if($update_head) {
            DelConfirm::update_as_source($detail_as_source, $index_po);   
            DelConfirm::delete_connection($trc_type_id_po, $month_id_po, $trc_id_po, $rev_id_po, $trc_type_id, $month_id, $trc_id, $rev_id);   
            $index_label = [  
                'C010_TrcTypeID' =>  $trc_type_id,   
                'C011_Month' =>  $month_id,       
                'C012_TrcID' =>  $trc_id, 
            ] ;
            DelConfirm::delete_label($index_label); 
                $data['process'] = 1 ; 
                $data['msg'] = 'Success Delete' ;
            } else {
                $data['process'] = 0 ; 
                $data['msg'] = 'Fail, Please reload and try again!' ; 
            }  
        } else {
            $data['process'] = 0 ; 
            $data['msg'] = 'Fail, Please reload and try again!' ; 
        }
        echo json_encode($data); 
    }

    public function add_tag_label(Request $request){  
        $str_di = explode("_", Crypt::decryptString(str_replace("-","=", $request->ref_doc_pack))) ;  
        $trc_type_id = $str_di[4] ;
        $month_id = $str_di[5] ;
        $trc_id = $str_di[6] ;
        $rev_id = $str_di[7] ;   
        $line_search_id = $str_di[9] ;   
        $item_id = $str_di[10] ;   
        $qty = $request->qty ;   
        $serial_number = $request->serial_number ; 
        $my_name = Auth::user()->name ;
        // dd($str_di);
        $line_id = DelConfirm::get_label_line_id($trc_type_id, $month_id, $trc_id);
        $total_order = DelConfirm::get_total_order($str_di);
        $total_pack = DelConfirm::get_total_label($str_di);
        $result_total_pack = $total_order - ($total_pack + $qty) ;

        $str =  explode("_",  $trc_type_id.'_'.$month_id.'_'.$trc_id.'_'.$rev_id) ;
        $doc_data = DelConfirm::get_head_properties($str); 
        foreach($doc_data as $row) { 
            $flow_id = $row->C017_UserGrpFlowID_To ;  
            $docnum = $row->C050_DocNum ;  
            $docdate = $row->C050_DocDate ;  
        }   

        $detail = [
            'C010_TrcTypeID' =>  $trc_type_id,
            'C011_Month' =>  $month_id,
            'C012_TrcID' =>  $trc_id, 
            'C000_SysID' =>  $line_id,
            'C000_LineSrc' =>  $line_search_id,
            'C100_ItemIntID' =>  $item_id, 
            'C050_DocNum' =>  $docnum,
            'C050_DocDate' =>  $docdate, 
            'C110_Qty' =>  $qty,   
            'C111_QtyBal' =>  $qty,      
            'created_by' =>  $my_name,
            'serial_number' =>  $serial_number
        ] ; 

        if ($result_total_pack >= 0) {
            $insert_tag = DelConfirm::insert_label($detail);   
            if($insert_tag) {   
                DelConfirm::update_total_pallet($str_di);    
                $data['process'] = 1 ; 
                $data['msg'] = 'Success ' ;
            } else {
                $data['process'] = 0 ; 
                $data['msg'] = 'Fail, Please reload and try again!' ; 
            } 
        } else {
            $data['process'] = 0 ; 
            $data['msg'] = 'Fail, Max Qty = '.$total_order.' !' ; 
        }
        echo json_encode($data);  
    }

    public function get_ref_doc_id(Request $request){   
        $str_di = explode("_", Crypt::decryptString(str_replace("-","=", $request->ref_doc))) ;   
        $get_id = DelConfirm::get_ref_doc($str_di);  
        if ($get_id->count() > 0) { 
            foreach ($get_id as $post) {
                $data['id'] =  str_replace("=","-", Crypt::encryptString($post->trc_type_id_po.'_'.$post->month_id_po.'_'.$post->trc_id_po.'_'.$post->rev_id_po.'_'.$post->trc_type_id.'_'.$post->month_id.'_'.$post->trc_id.'_'.$post->rev_id.'_'.$post->line_id_po.'_'.$post->line_id_di.'_'.$post->item_id)) ;  
            } 
            $data['process'] = 1 ; 
            $data['msg'] = 'Success ' ; 
        } else {
            $data['process'] = 0 ; 
            $data['msg'] = 'Fail, please reload and try again !' ; 
        }
        echo json_encode($data);  
    }

    public function tag_lable_table(Request $request) 
    {
        $str_di = explode("_", Crypt::decryptString(str_replace("-","=", $request->ref_doc))) ;  
        $trc_type_id = $str_di[4] ;
        $month_id = $str_di[5] ;
        $trc_id = $str_di[6] ;  
        $line_id = $str_di[9] ;    
          
        $searchTerm = $request->searchTerm;
        $columns = array(  
            0 =>'a.C000_SysID', 
            1 =>'a.serial_number', 
            2 =>'a.C110_Qty', 
            3 =>'a.C000_SysID' 
        );   

        $totalData = DelConfirm::get_detail_tag_label($trc_type_id,$month_id,$trc_id,$line_id)->count(); 
        $totalFiltered = $totalData; 
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = ($request->input('order.0.column')==0 ? $columns[1] : $columns[$request->input('order.0.column')]);
        $dir = ($request->input('order.0.column')==0 ? 'desc' : $request->input('order.0.dir')) ;           
        $posts = DelConfirm::get_detail_tag_label($trc_type_id,$month_id,$trc_id,$line_id)
        ->offset($start)
        ->limit($limit)
        ->orderBy($order,$dir)
        ->get(); 
        $data = array();
        if(!empty($posts))
        { 
        $no = $start ;
        foreach ($posts as $post)
        { 
            $no++;
            $user_id = Auth::user()->id ;  
            $trc_id = str_replace("=","-", Crypt::encryptString($post->C010_TrcTypeID.'_'.$post->C011_Month.'_'.$post->C012_TrcID.'_'.$post->C000_SysID.'_'.$post->C000_LineSrc)) ;   
            $sys_id = "'".$trc_id."'" ;     
            $qty = "'".number_format($post->C110_Qty,0)."'" ;     

            $button_delete = '<button type="button" title="Delete" onclick="confirm_delete_tag_label('.$sys_id.', '.$qty.') ;" class="btn btn-sm btn-danger" style="width: 45px;"><i class="fa fa-trash"></i></button>' ; 
            $button_save = '<button type="button" title="Save" onclick="updateTagLabel('.$sys_id.')" class="btn btn-sm btn-success" style="width: 45px;"><i class="fa fa-save"></i></button>' ; 

            $nestedData['no'] =  '<button type="button" style="width: 50px; border: none; background: transparent; height: 25px;"><input readonly class="form-control text-sm text-center" style="color: #7e8299; font-size: 12.35px; background: transparent; vertical-align: top; font-family: Poppins; width: 50px;"  type="text" value="'.$no.'"/></button>' ;   

            $nestedData['qty'] = '<button type="button" style="border: none; background: transparent; height: 25px;"><input type="number" class="form-control cursor-auto number_format_coma text-sm text-end" id="qty_pack_'.$trc_id.'" 
            style="color: #7e8299; font-size: 12.35px; background: transparent; vertical-align: top; font-family: Poppins;"  type="text" value="'.number_format($post->C110_Qty,0).'"/></button>' ;  

            $nestedData['sn'] = '<button type="button" style="width: 150px; border: none; background: transparent; height: 25px;"><input class="form-control cursor-auto text-sm text-start" id="sn_'.$trc_id.'" style="width: 250px; color: #7e8299; font-size: 12.35px; background: transparent; font-family: Poppins;"  type="text" value="'.$post->serial_number.'"/></button>' ;   

            $nestedData['action'] = $button_save.' '.$button_delete ;  
            $data[] = $nestedData; 
        }
        } 
        $json_data = array(
        "draw"            => intval($request->input('draw')),  
        "recordsTotal"    => intval($totalData),  
        "recordsFiltered" => intval($totalFiltered), 
        "data"            => $data   
        ); 
        echo json_encode($json_data); 
    }

    public function store_tag_label(Request $request){  
        $str_di = explode("_", Crypt::decryptString(str_replace("-","=", $request->id))) ;  
        $trc_type_id = $str_di[0] ;
        $month_id = $str_di[1] ;
        $trc_id = $str_di[2] ; 
        $line_id = $str_di[3] ;    
        $line_search_id = $str_di[4] ;    
        $qty = $request->qty ;   
        $serial_number = $request->sn ; 
 
        $total_order = DelConfirm::get_total_order_by_detail($trc_type_id, $month_id, $trc_id, $line_search_id);
        $total_pack = DelConfirm::get_total_label_by_detail($trc_type_id, $month_id, $trc_id, $line_id);
        $qty_before = DelConfirm::get_qty_before_label($trc_type_id, $month_id, $trc_id, $line_id, $line_search_id);
        
        $result_total_pack = $total_order - (($total_pack - $qty_before) + $qty) ; 
        // dd($total_order.' '.$total_pack.' '.$qty_before.' '.$qty);
        $index = [  
            'C010_TrcTypeID' =>  $trc_type_id,   
            'C011_Month' =>  $month_id,       
            'C012_TrcID' =>  $trc_id,
            'C000_SysID' =>  $line_id,
            'C000_LineSrc' =>  $line_search_id,
        ] ; 
        $detail = [  
            'C110_Qty' =>  $qty,   
            'C111_QtyBal' =>  $qty,       
            'serial_number' =>  $serial_number
        ] ; 

        if ($result_total_pack >= 0) {
            $update_label = DelConfirm::update_label($detail, $index);   
            if($update_label) {        
                $data['process'] = 1 ; 
                $data['msg'] = 'Success ' ;
            } else {
                $data['process'] = 0 ; 
                $data['msg'] = 'Fail, Please reload and try again!' ; 
            } 
        } else {
            $data['process'] = 0 ; 
            $data['msg'] = 'Fail, Max Qty = '.$total_order.' !' ; 
        }
        echo json_encode($data);  
    }

    public function destroy_tag_label(Request $request){  
        $str_di = explode("_", Crypt::decryptString(str_replace("-","=", $request->id))) ;  
        $trc_type_id = $str_di[0] ;
        $month_id = $str_di[1] ;
        $trc_id = $str_di[2] ; 
        $line_id = $str_di[3] ;    
        $line_search_id = $str_di[4] ;     
        $index = [  
            'C010_TrcTypeID' =>  $trc_type_id,   
            'C011_Month' =>  $month_id,       
            'C012_TrcID' =>  $trc_id,
            'C000_SysID' =>  $line_id,
            'C000_LineSrc' =>  $line_search_id,
        ] ;  
 
        $update_label = DelConfirm::delete_label($index);   
        if($update_label) {        
            DelConfirm::update_total_pallet_by_detail($trc_type_id,$month_id,$trc_id,$line_search_id);  
            $data['process'] = 1 ; 
            $data['msg'] = 'Success ' ;
        } else {
            $data['process'] = 0 ; 
            $data['msg'] = 'Fail, Please reload and try again!' ; 
        }  
        echo json_encode($data);  
    }

    public function clear_tag_label(Request $request){  
        $str_di = explode("_", Crypt::decryptString(str_replace("-","=", $request->ref_doc_pack))) ;  
        $trc_type_id = $str_di[4] ;
        $month_id = $str_di[5] ;
        $trc_id = $str_di[6] ;
        $rev_id = $str_di[7] ;   
        $line_search_id = $str_di[9] ;  
        $index = [  
            'C010_TrcTypeID' =>  $trc_type_id,   
            'C011_Month' =>  $month_id,       
            'C012_TrcID' =>  $trc_id, 
            'C000_LineSrc' =>  $line_search_id,
        ] ;     
        $update_label = DelConfirm::delete_label($index);   
        if($update_label) {        
            DelConfirm::update_total_pallet_by_detail($trc_type_id,$month_id,$trc_id,$line_search_id);  
            $data['process'] = 1 ; 
            $data['msg'] = 'Success ' ;
        } else {
            $data['process'] = 0 ; 
            $data['msg'] = 'Fail, Please reload and try again!' ; 
        }  
        echo json_encode($data);  
    }

    public function generate_tag_label(Request $request) {  
        $str_di = explode("_", Crypt::decryptString(str_replace("-","=", $request->ref_doc_pack))) ;  
        $trc_type_id = $str_di[4] ;
        $month_id = $str_di[5] ;
        $trc_id = $str_di[6] ;
        $rev_id = $str_di[7] ;   
        $line_search_id = $str_di[9] ;   
        $item_id = $str_di[10] ;   
        $qty = $request->qty ;   
        $serial_number = $request->serial_number ; 
        $my_name = Auth::user()->name ;
        $line_id = DelConfirm::get_label_line_id($trc_type_id, $month_id, $trc_id);
        $total_order = DelConfirm::get_total_order($str_di);
        $total_pack = DelConfirm::get_total_label($str_di);
        // dd($trc_type_id,$month_id,$trc_id,$rev_id,$line_id,$line_search_id,$qty,$serial_number);
        $update_label = DelConfirm::generate_tag_label($trc_type_id,$month_id,$trc_id,$rev_id,$line_id,$line_search_id,$qty,$serial_number);   
        if($update_label>0) {        
            DelConfirm::update_total_pallet_by_detail($trc_type_id,$month_id,$trc_id,$line_search_id);  
            $data['process'] = 1 ; 
            $data['msg'] = 'Success ' ;
        } else {
            $data['process'] = 0 ; 
            $data['msg'] = 'Fail, Please reload and try again!' ; 
        }  
        echo json_encode($data);  
    }


    function print_view(Request $request){    
        $data['trc_unix_id'] = $request->trc_unix_id ;     
        return view('del_confirm.direct_file_print', $data);
    } 

    
    public function draw_print_header($header, $DocNum, $Address, $DocType, $Rev, $CompanyName, $Logo, $City, $Province, $PostalCode, $State, $Phone, $Fax, $ProjectName, $PartnerCode, $doc_status, $AddressHtml,$PartnerAddressHtml, $DocDate, $PartnerName, $PICName, $SJNum, $SJDate, $PONum, $DNNum, $DNDate, $AddressCompany){
        $w_top = array(130, 61); 
        $w_head = array(10, 101, 30, 25, 25);
        $w_detail = array(10, 101, 30, 25, 25);
        $imgdata = base64_decode($Logo);  
        
        
        PDF::SetFillColor(255,255,255);
        PDF::SetTextColor(0); 
        PDF::SetFont('dejavusans', 'B', 9);
        
        PDF::Cell($w_top[0], 5, $CompanyName, '', 0, 'L', 0);
        PDF::Cell($w_top[1], 5, '', '', 0, 'L', 0);
        PDF::SetFont('dejavusans', '', 9, '', false); 
        PDF::Ln();
        PDF::Cell($w_top[0], 5, $AddressCompany, '', 0, 'L', 0);
        PDF::Cell($w_top[1], 5, '', '', 0, 'C', 0);
         
        PDF::Ln();
        PDF::Cell($w_top[0], 5, $City.', '.$Province.' '.$PostalCode, '', 0, 'L', 0);
        PDF::Cell($w_top[1], 5, '', '', 0, 'L', 0);
         
        PDF::Ln();
        PDF::Cell($w_top[0], 5, 'Indonesia', '', 0, 'L', 0);
        PDF::Cell($w_top[1], 5, '', '', 0, 'L', 0);
       
        PDF::Ln();
        PDF::Cell($w_top[0], 5, 'Phone: '.$Phone.' Fax: '.$Fax, '', 0, 'L', 0); 
        PDF::Cell($w_top[1], 5, 'Delivery Instruction '.$DocType, '', 0, 'R', 0);
    
        PDF::Ln();
        PDF::SetFont('dejavusans', '', 8);
        PDF::Cell(35, 5, 'Vendor Name', 'TL', 0, 'L', 1);
        PDF::Cell(5, 5, ':', 'T', 0, 'C', 1);
        PDF::Cell(90, 5, $PartnerName, 'TR', 0, 'L', 1); 
        PDF::Cell(20, 5, 'Doc. Num.', 'TL', 0, 'L', 1); 
        PDF::Cell(5, 5, ':', 'T', 0, 'C', 1);
        PDF::Cell(36, 5, $DocNum, 'TR', 0, 'L', 1);
    
        PDF::Ln();
        PDF::SetFont('dejavusans', '', 8);
        PDF::Cell(35, 5, 'Project Name', 'L', 0, 'L', 1);
        PDF::Cell(5, 5, ':', '', 0, 'C', 1);
        PDF::Cell(90, 5, $ProjectName, 'R', 0, 'L', 1); 
        PDF::Cell(20, 5, 'Doc. Date', 'L', 0, 'L', 1); 
        PDF::Cell(5, 5, ':', '', 0, 'C', 1);
        PDF::Cell(36, 5, $DocDate, 'R', 0, 'L', 1);
    
        PDF::Ln();
        PDF::SetFont('dejavusans', '', 8);
        PDF::Cell(35, 5, '', 'L', 0, 'L', 1);
        PDF::Cell(5, 5, '', '', 0, 'C', 1);
        PDF::Cell(90, 5, '', 'R', 0, 'L', 1); 
        PDF::Cell(20, 5, 'Rev', 'L', 0, 'L', 1); 
        PDF::Cell(5, 5, ':', '', 0, 'C', 1);
        PDF::Cell(36, 5, $Rev, 'R', 0, 'L', 1);
        
        PDF::Ln();
        PDF::SetFont('dejavusans', '', 8);
        PDF::Cell(35, 5, 'Ship Number', 'L', 0, 'L', 1);
        PDF::Cell(5, 5, ':', '', 0, 'C', 1);
        PDF::Cell(90, 5, $SJNum, 'R', 0, 'L', 1); 
        PDF::Cell(20, 5, 'PO Num', 'L', 0, 'L', 1); 
        PDF::Cell(5, 5, ':', '', 0, 'C', 1);
        PDF::Cell(36, 5, $PONum, 'R', 0, 'L', 1);
    
        PDF::Ln();
        PDF::SetFont('dejavusans', '', 8);
        PDF::Cell(35, 5, 'Delivery Date', 'L', 0, 'L', 1);
        PDF::Cell(5, 5, ':', '', 0, 'C', 1);
        PDF::Cell(90, 5, $SJDate, 'R', 0, 'L', 1); 
        PDF::Cell(20, 5, '', 'L', 0, 'L', 1); 
        PDF::Cell(5, 5, '', '', 0, 'C', 1);
        PDF::Cell(36, 5, '', 'R', 0, 'L', 1);
    
        PDF::Ln();
        PDF::SetFont('dejavusans', '', 8);
        PDF::Cell(35, 5, '', 'BL', 0, 'L', 1);
        PDF::Cell(5, 5, '', 'B', 0, 'C', 1);
        PDF::Cell(90, 5, '', 'BR', 0, 'L', 1); 
        PDF::Cell(20, 5, '', 'BL', 0, 'L', 1); 
        PDF::Cell(5, 5, '', 'B', 0, 'C', 1);
        PDF::Cell(36, 5, '', 'BR', 0, 'L', 1);
      
        
        $imgdata = base64_decode(substr(str_replace("-","+", $Logo),22)) ; 
        PDF::Image('@'.$imgdata, 155, 5, 50, 13, '', '', '', false, 150, '', false, false, 1, false, false, true); 
    
        if ($doc_status==1) {    
            PDF::Image('public/dist/img/draft.png', 10, 40, 350, 230, 'PNG', '', '', false, 150, '', false, false, 1, false, false, true);
        } 
         
       PDF::Ln(8);
       PDF::SetFont('dejavusans', '', 8);
       PDF::SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
       PDF::Cell($w_head[0], 6, $header[0], 'TB', 0, 'L', 1);
       PDF::Cell($w_head[1], 6, $header[1], 'TB', 0, 'L', 1); 
       PDF::Cell($w_head[4], 6, '', 'TB', 0, 'R', 1);
       PDF::Cell($w_head[2], 6, $header[2], 'TB', 0, 'R', 1);
       PDF::Cell($w_head[3], 6, $header[3], 'TB', 0, 'R', 1); 
       
       PDF::Ln(7);
    
     PDF::SetFillColor(224, 235, 255);
     PDF::SetTextColor(0);
     PDF::SetFont('');
    }
    
    public function draw_print_main_header($header, $DocNum, $DocDate, $PartnerName, $Address, $UserName, $PICName, $DocType, $SubTotal, $Discount, $Ppn, $Total, $TOP, $Status, $Rev, $Approval, $Legalized, $Currency, $Description, $Vendor, $Sign_1, $Sign_2, $Sign_3, $Sign_4, $IsCompleted, $LastUpdate1, $LastUpdate2, $LastUpdate3, $LastUpdate4, $sum, $CompanyName, $Logo, $DocRegNum, $City, $Province, $PostalCode, $State, $Phone, $Fax, $ProjectName, $PartnerCode, $Checked, $IDUser2, $doc_status, $trc_type_id, $month_id, $trc_id,$rev_id,$print_option,$AddressHtml,$PartnerAddressHtml, $SJNum, $SJDate, $PONum, $DNNum, $DNDate, $AddressCompany, $flow_id, $url){
     
        $w_top = array(130, 61); 
        $w_head = array(10, 101, 30, 25, 25);
        $w_detail = array(10, 101, 30, 25, 25);
        $imgdata = base64_decode($Logo);  
        
        
        PDF::SetFillColor(255,255,255);
        PDF::SetTextColor(0); 
        PDF::SetFont('dejavusans', 'B', 9);
        
        PDF::Cell($w_top[0], 5, $CompanyName, '', 0, 'L', 0);
        PDF::Cell($w_top[1], 5, '', '', 0, 'L', 0);
        PDF::SetFont('dejavusans', '', 9, '', false); 
        PDF::Ln();
        PDF::Cell($w_top[0], 5, $AddressCompany, '', 0, 'L', 0);
        PDF::Cell($w_top[1], 5, '', '', 0, 'C', 0);
         
        PDF::Ln();
        PDF::Cell($w_top[0], 5, $City.', '.$Province.' '.$PostalCode, '', 0, 'L', 0);
        PDF::Cell($w_top[1], 5, '', '', 0, 'L', 0);
         
        PDF::Ln();
        PDF::Cell($w_top[0], 5, 'Indonesia', '', 0, 'L', 0);
        PDF::Cell($w_top[1], 5, '', '', 0, 'L', 0);
       
        PDF::Ln();
        PDF::Cell($w_top[0], 5, 'Phone: '.$Phone.' Fax: '.$Fax, '', 0, 'L', 0); 
        PDF::Cell($w_top[1], 5, 'Delivery Instruction '.$DocType, '', 0, 'R', 0);
    
        PDF::Ln();
        PDF::SetFont('dejavusans', '', 8);
        PDF::Cell(35, 5, 'Vendor Name', 'TL', 0, 'L', 1);
        PDF::Cell(5, 5, ':', 'T', 0, 'C', 1);
        PDF::Cell(90, 5, $PartnerName, 'TR', 0, 'L', 1); 
        PDF::Cell(20, 5, 'Doc. Num.', 'TL', 0, 'L', 1); 
        PDF::Cell(5, 5, ':', 'T', 0, 'C', 1);
        PDF::Cell(36, 5, $DocNum, 'TR', 0, 'L', 1);
    
        PDF::Ln();
        PDF::SetFont('dejavusans', '', 8);
        PDF::Cell(35, 5, 'Project Name', 'L', 0, 'L', 1);
        PDF::Cell(5, 5, ':', '', 0, 'C', 1);
        PDF::Cell(90, 5, $ProjectName, 'R', 0, 'L', 1); 
        PDF::Cell(20, 5, 'Doc. Date', 'L', 0, 'L', 1); 
        PDF::Cell(5, 5, ':', '', 0, 'C', 1);
        PDF::Cell(36, 5, $DocDate, 'R', 0, 'L', 1);
    
        PDF::Ln();
        PDF::SetFont('dejavusans', '', 8);
        PDF::Cell(35, 5, '', 'L', 0, 'L', 1);
        PDF::Cell(5, 5, '', '', 0, 'C', 1);
        PDF::Cell(90, 5, '', 'R', 0, 'L', 1); 
        PDF::Cell(20, 5, 'Rev', 'L', 0, 'L', 1); 
        PDF::Cell(5, 5, ':', '', 0, 'C', 1);
        PDF::Cell(36, 5, $Rev, 'R', 0, 'L', 1);
        
        PDF::Ln();
        PDF::SetFont('dejavusans', '', 8);
        PDF::Cell(35, 5, 'Ship Number', 'L', 0, 'L', 1);
        PDF::Cell(5, 5, ':', '', 0, 'C', 1);
        PDF::Cell(90, 5, $SJNum, 'R', 0, 'L', 1); 
        PDF::Cell(20, 5, 'PO Num', 'L', 0, 'L', 1); 
        PDF::Cell(5, 5, ':', '', 0, 'C', 1);
        PDF::Cell(36, 5, $PONum, 'R', 0, 'L', 1);
    
        PDF::Ln();
        PDF::SetFont('dejavusans', '', 8);
        PDF::Cell(35, 5, 'Delivery Date', 'L', 0, 'L', 1);
        PDF::Cell(5, 5, ':', '', 0, 'C', 1);
        PDF::Cell(90, 5, $SJDate, 'R', 0, 'L', 1); 
        PDF::Cell(20, 5, '', 'L', 0, 'L', 1); 
        PDF::Cell(5, 5, '', '', 0, 'C', 1);
        PDF::Cell(36, 5, '', 'R', 0, 'L', 1);
    
        PDF::Ln();
        PDF::SetFont('dejavusans', '', 8);
        PDF::Cell(35, 5, '', 'BL', 0, 'L', 1);
        PDF::Cell(5, 5, '', 'B', 0, 'C', 1);
        PDF::Cell(90, 5, '', 'BR', 0, 'L', 1); 
        PDF::Cell(20, 5, '', 'BL', 0, 'L', 1); 
        PDF::Cell(5, 5, '', 'B', 0, 'C', 1);
        PDF::Cell(36, 5, '', 'BR', 0, 'L', 1);
      
        
        $imgdata = base64_decode(substr(str_replace("-","+", $Logo),22)) ; 
        PDF::Image('@'.$imgdata, 155, 5, 50, 13, '', '', '', false, 150, '', false, false, 1, false, false, true); 
    
        if ($doc_status==1) {    
            PDF::Image('public/dist/img/draft.png', 10, 40, 350, 230, 'PNG', '', '', false, 150, '', false, false, 1, false, false, true);
        } 
         
       PDF::Ln(8);
       PDF::SetFont('dejavusans', '', 8);
       PDF::SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
       PDF::Cell($w_head[0], 6, $header[0], 'TB', 0, 'L', 1);
       PDF::Cell($w_head[1], 6, $header[1], 'TB', 0, 'L', 1); 
       PDF::Cell($w_head[4], 6, '', 'TB', 0, 'R', 1);
       PDF::Cell($w_head[2], 6, $header[2], 'TB', 0, 'R', 1);
       PDF::Cell($w_head[3], 6, $header[3], 'TB', 0, 'R', 1); 
       
       PDF::Ln(7);
         
        $DB = DelConfirm::detail_list_data($trc_type_id,$month_id,$trc_id,$rev_id);
        $num = $DB->count() ;
      
        $startX = 0 ;
        if ($num >= 15) {
            $jml_dataX = $num - 15 ;
            if ($jml_dataX==0) {
                $limitX = 14 ;
            } else {
                $limitX = 15 ;
            }
        } elseif ($num > 5 && $num<15) {
            $jml_bX = $num ;
            $jml_dataX = $num - 5 ;
            if ($jml_dataX<0) {
                $limitX = $jml_dataX ;
            } else {
                $limitX = $jml_bX - 1 ;
            }
        } else {
            $jml_dataX = $num - 6 ;
            $limitX = $num ;
        }
     
        if ($flow_id==30) { 
            $all_page = DelConfirm::load_data_print_page($trc_type_id, $month_id, $trc_id, $rev_id, $Status, $print_option, $startX, $limitX); 
        } else if ($flow_id==28) { 
            $all_page = DelConfirm::load_data_print_page_sbc($trc_type_id, $month_id, $trc_id, $rev_id, $Status, $print_option, $startX, $limitX); 
        } else { 
            $all_page = DelConfirm::load_data_print_page_others($trc_type_id, $month_id, $trc_id, $rev_id, $Status, $print_option, $startX, $limitX); 
        }
           
         foreach ($all_page as $row) { 
            $num_pages = PDF::getNumPages();
            PDF::startTransaction();
            PDF::SetFont('dejavusans', '', 7.5);
            PDF::SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
         
            PDF::Cell($w_detail[0], 5, $row[0], '', 0, 'L', 0);
            PDF::Cell($w_detail[1], 5, ($row[1] == $row[11] ? $row[11] : substr($row[1],0,50)), '', 0, 'L', 0);
            PDF::Cell($w_detail[4], 5, '', '', 0, 'R', 0);  
            PDF::Cell($w_detail[2], 5, $row[3], '', 0, 'R', 0);
            PDF::Cell($w_detail[3], 5, $row[4], '', 0, 'R', 0);
            
    
            PDF::Ln(4);
     
            PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 2, 'color' => array(128, 128, 128)));
            PDF::Cell($w_detail[0], 5, '', 'B', 0, 'L', 0);
            PDF::Cell($w_detail[1], 5, ($row[1] == $row[11] ? '' : $row[11]), 'B', 0, 'L', 0);
            PDF::Cell($w_detail[4], 5, '', 'B', 0, 'L', 0);
            PDF::Cell($w_detail[2], 5, ($row[4] == $row[9] ? '' : $row[8]), 'B', 0, 'R', 0);
            PDF::Cell($w_detail[3], 5, ($row[4] == $row[9] ? '' : $row[9]), 'B', 0, 'R', 0);  
            PDF::Ln(6); 
    
         } 
     
         if ($num<=5) {
            PDF::SetFont('dejavusans', '', 8);
            PDF::SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
            PDF::Cell($w_detail[0] + $w_detail[1], 6, '', 'TB', 0, 'C');
            PDF::Cell($w_detail[2], 6, '', 'TB', 0, 'R');
            PDF::Cell($w_detail[3], 6, '', 'TB', 0, 'R');
            PDF::Cell($w_detail[4], 6, '', 'TB', 0, 'R');
             
            PDF::Ln(); 
            PDF::SetFont('dejavusans', '', 8, '', 'false');
            PDF::Cell(191, 7, 'Description :', '', 0, 'L');
            PDF::Ln();
            PDF::writeHTML($Description, true, false, true, false, ''); 
            PDF::Ln(); 
    
    
            $db_jurnal = DelConfirm::load_data_print_jurnal($trc_type_id,$month_id,$trc_id,$rev_id); 
            $num_jurnal = DelConfirm::rows_data_print_jurnal($trc_type_id,$month_id,$trc_id,$rev_id)->count() ;  
     
            if($num_jurnal>0){
                PDF::Cell(20, 5, 'Account', '', 0, 'L', 0);
                PDF::Cell(91, 5, 'Description', '', 0, 'L', 0); 
                PDF::Cell(20, 5, 'Currency', '', 0, 'L', 0);
                PDF::Cell(30, 5, 'Debet', '', 0, 'R', 0);
                PDF::Cell(30, 5, 'Credit', '', 0, 'R', 0); 
                PDF::Ln(2);
                PDF::SetLineStyle(array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
                PDF::Cell(191, 3, '', 'B', 0, 'L');
                PDF::Ln();
                }
    
            foreach($db_jurnal as $row_jurnal) {  
                PDF::SetFont('dejavusans', '', 8);
                PDF::SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
                PDF::Cell(20, 5, $row_jurnal[1], '', 0, 'L', 0);
                PDF::Cell(91, 5, $row_jurnal[2], '', 0, 'L', 0); 
                PDF::Cell(20, 5, $row_jurnal[3], '', 0, 'L', 0);
                PDF::Cell(30, 5, $row_jurnal[4], '', 0, 'R', 0);
                PDF::Cell(30, 5, $row_jurnal[5], '', 0, 'R', 0);
                PDF::Ln();
            }
    
                PDF::SetLineStyle(array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
                PDF::Cell(191, 3, '', 'T', 0, 'L');
    
            PDF::SetY(-58); 
    
            $num_row = 225 ; 
            PDF::Ln(10); 
            if($IsCompleted==1){ PDF::Image('dist/img/sign/company_stamp.png', 90, $num_row, 40, 17, 'PNG', '', '', false, 150, '', false, false, 1, false, false, true); }
            PDF::Cell(5, 6, '', '', 0, 'C');
            PDF::Cell(38, 4, '', '', 0, 'C');
            PDF::Cell(10, 4, '', '', 0, 'C'); 
            PDF::Cell(38, 4, '', '', 0, 'C'); 
            PDF::Cell(10, 4, '', '', 0, 'C');
            PDF::Cell(38, 4, '', '', 0, 'C');
            PDF::Cell(10, 4, '', '', 0, 'C');
            PDF::Cell(38, 4, '', '', 0, 'C');
           
            
            PDF::Ln(20);   
            PDF::SetFont('courier', 'I', 8);
            
            PDF::Cell(5, 6, '', '', 0, 'C');
            PDF::Cell(38, 4, $LastUpdate1, '', 0, 'C');
            PDF::Cell(10, 4, '', '', 0, 'C'); 
            PDF::Cell(38, 4, $LastUpdate3, '', 0, 'C'); 
            PDF::Cell(10, 4, '', '', 0, 'C');
            PDF::Cell(38, 4, $LastUpdate4, '', 0, 'C');
            PDF::Cell(10, 4, '', '', 0, 'C');
            PDF::Cell(38, 4, '', '', 0, 'C');
            PDF::Cell(10, 4, '', '', 0, 'C'); 
            PDF::Ln(); 
            
            PDF::SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
            if(!empty($Sign_1)){  
            $imgdata_sign_1 = base64_decode(substr(str_replace("-","+", $Sign_1),22)) ;  
            PDF::Image('@'.$imgdata_sign_1, 17, $num_row, 40, 17, '', '', '', false, 150, '', false, false, 1, false, false, true); 
            } 
            
            PDF::SetFont('dejavusans', 'I', 8);
            PDF::Cell(5, 6, '', '', 0, 'C');
            PDF::Cell(38, 6, '', '', 0, 'C');
            PDF::Cell(10, 6, '', '', 0, 'C'); 
             
            PDF::Cell(38, 6, '', '', 0, 'C');  
            
            
            PDF::Cell(10, 6, '', '', 0, 'C'); 
            PDF::Cell(38, 6, '', '', 0, 'C');   
            PDF::Cell(10, 6, '', '', 0, 'C'); 
             
    
            $style = array(
                'border' => true,
                'vpadding' => 2,
                'hpadding' => 2,
                'fgcolor' => array(0,0,0),
                'bgcolor' => false, //array(255,255,255)
                'module_width' => 1, // width of a single module in points
                'module_height' => 1 // height of a single module in points
            );
      
            // PDF::write2DBarcode($url, 'QRCODE,H', 163, 190, 40, 40, $style, 'N');
            // PDF::Text(20, 25, '');
            // PDF::Cell(38, 6, '', '', 0, 'C'); 
            // PDF::Ln();  

            PDF::write2DBarcode($url, 'QRCODE,H', 163, 190, 40, 40, $style, 'N');
            PDF::Text(20, 25, '');
            PDF::Cell(38, 6, '', '', 0, 'C'); 
            PDF::Ln();  
    
            PDF::SetY(-15);
            PDF::SetFont('courier', 'I', 9);
            PDF::SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
            PDF::Cell(100, 6, $DocRegNum, 'T', 0, 'L');
            PDF::Cell(81, 6, '', 'T', 0, 'L');
            PDF::Cell(10, 6, PDF::getAliasNumPage().'/'.PDF::getAliasNbPages(), 'T', 0, 'L'); 
    
        }else{
    
            PDF::SetY(-15);
            PDF::SetFont('courier', 'I', 9);
            PDF::SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
            PDF::Cell(100, 6, $DocRegNum, 'T', 0, 'L');
            PDF::Cell(81, 6, '', 'T', 0, 'L');
            PDF::Cell(10, 6, PDF::getAliasNumPage().'/'.PDF::getAliasNbPages(), 'T', 0, 'L'); 
    
            PDF::AddPage();
            $this->draw_print_header($header, $DocNum, $Address, $DocType, $Rev, $CompanyName, $Logo, $City, $Province, $PostalCode, $State, $Phone, $Fax, $ProjectName, $PartnerCode, $doc_status,$AddressHtml,$PartnerAddressHtml, $DocDate, $PartnerName, $PICName, $SJNum, $SJDate, $PONum, $DNNum, $DNDate, $AddressCompany);   
        }  
         
    
     $jml_data = $num  ;  
     $page = 1 ;
     $limit = 1 ;
     $start = 0 ; 
    
    while($jml_data >= 0) { 
     
    if($jml_data >= 15){
        $jml_data = $jml_data - 15 ;
            if($jml_data==0){
                $limit = 14 ;
                $start = $limit * ($page - 1) ;
                $jml_data = $jml_data + 1 ;
            }else{
                $limit = 15 ;
                $start = $limit * ($page - 1) ;
            }
        
    }else if($jml_data > 5 && $jml_data<15){ 
            $jml_b = $jml_data ;
            $jml_data = $jml_data - 6 ; 
            if($jml_data<0){ 
            $start = $limit * ($page - 1) ;
            $limit = $jml_data ;
            }else{ 
            $start = $limit * ($page - 1) ;
            $limit = $jml_b - 1 ;
            $jml_data = 1 ;
            }
        
        }else{ 
            $start = $start + $limit ;
            $limit = $jml_data ; 
            $jml_data = $jml_data - 7 ; 
        }
     
    
        if($flow_id==30){ $all_page = DelConfirm::load_data_print_page($trc_type_id,$month_id,$trc_id,$rev_id,$Status,$print_option,$start,$limit); } 
        else if($flow_id==28){ $all_page = DelConfirm::load_data_print_page_sbc($trc_type_id,$month_id,$trc_id,$rev_id,$Status,$print_option,$start,$limit); } 
        else { $all_page = DelConfirm::load_data_print_page_others($trc_type_id,$month_id,$trc_id,$rev_id,$Status,$print_option,$start,$limit); }  
    
    if ($page!=1 && $jml_data > 0) {
        foreach ($all_page as $row) { 
                $num_pages = PDF::getNumPages();
                PDF::startTransaction();
                PDF::SetFont('dejavusans', '', 7.5);
                PDF::SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0))); 
                PDF::Cell($w_detail[0], 5, $row[0], '', 0, 'L', 0);
                PDF::Cell($w_detail[1], 5, ($row[1] == $row[11] ? $row[11] : substr($row[1],0,50)), '', 0, 'L', 0);
                PDF::Cell($w_detail[4], 5, '', '', 0, 'R', 0); 
                PDF::Cell($w_detail[2], 5, $row[3], '', 0, 'R', 0);
                PDF::Cell($w_detail[3], 5, $row[4], '', 0, 'R', 0); 
                PDF::Ln(4); 
                PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 2, 'color' => array(128, 128, 128)));
                PDF::Cell($w_detail[0], 5, '', 'B', 0, 'L', 0);
                PDF::Cell($w_detail[1], 5, ($row[1] == $row[11] ? '' : $row[11]), 'B', 0, 'L', 0);
                PDF::Cell($w_detail[4], 5, '', 'B', 0, 'L', 0);
                PDF::Cell($w_detail[2], 5, ($row[4] == $row[9] ? '' : $row[8]), 'B', 0, 'R', 0);
                PDF::Cell($w_detail[3], 5, ($row[4] == $row[9] ? '' : $row[9]), 'B', 0, 'R', 0); 
                PDF::Ln(6);  
        }
      
        PDF::SetY(-15);
        PDF::SetFont('courier', 'I', 9);
        PDF::SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
        PDF::Cell(100, 6, $DocRegNum, 'T', 0, 'L');
        PDF::Cell(81, 6, '', 'T', 0, 'L');
        PDF::Cell(10, 6, PDF::getAliasNumPage().'/'.PDF::getAliasNbPages(), 'T', 0, 'L');
    
        if ($jml_data<=0) { 
            PDF::Cell($w_detail[0] + $w_detail[1], 6, '', 'TB', 0, 'C');
            PDF::Cell($w_detail[2], 6, '', 'TB', 0, 'R');
            PDF::Cell($w_detail[3], 6, '', 'TB', 0, 'R');
            PDF::Cell($w_detail[4], 6, '', 'TB', 0, 'R');
    
            PDF::Ln(); 
            PDF::SetFont('dejavusans', '', 8, '', 'false');
            PDF::Cell(191, 7, 'Description :', '', 0, 'L');
            PDF::Ln();
            PDF::writeHTML($Description, true, false, true, false, ''); 
            PDF::Ln(); 
    
    
            $db_jurnal = DelConfirm::load_data_print_jurnal($trc_type_id,$month_id,$trc_id,$rev_id); 
            $num_jurnal = DelConfirm::rows_data_print_jurnal($trc_type_id,$month_id,$trc_id,$rev_id)->count() ;   
     
            if($num_jurnal>0){
                PDF::Cell(20, 5, 'Account', '', 0, 'L', 0);
                PDF::Cell(91, 5, 'Description', '', 0, 'L', 0); 
                PDF::Cell(20, 5, 'Currency', '', 0, 'L', 0);
                PDF::Cell(30, 5, 'Debet', '', 0, 'R', 0);
                PDF::Cell(30, 5, 'Credit', '', 0, 'R', 0); 
                PDF::Ln(2);
                PDF::SetLineStyle(array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
                PDF::Cell(191, 3, '', 'B', 0, 'L');
                PDF::Ln();
                }
    
            foreach($db_jurnal as $row_jurnal) {  
                PDF::SetFont('dejavusans', '', 8);
                PDF::SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
                PDF::Cell(20, 5, $row_jurnal[1], '', 0, 'L', 0);
                PDF::Cell(91, 5, $row_jurnal[2], '', 0, 'L', 0); 
                PDF::Cell(20, 5, $row_jurnal[3], '', 0, 'L', 0);
                PDF::Cell(30, 5, $row_jurnal[4], '', 0, 'R', 0);
                PDF::Cell(30, 5, $row_jurnal[5], '', 0, 'R', 0);
                PDF::Ln();
            }
    
                PDF::SetLineStyle(array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
                PDF::Cell(191, 3, '', 'T', 0, 'L');
                
        }else{
            PDF::AddPage();
            $this->draw_print_header($header, $DocNum, $Address, $DocType, $Rev, $CompanyName, $Logo, $City, $Province, $PostalCode, $State, $Phone, $Fax, $ProjectName, $PartnerCode, $doc_status,$AddressHtml,$PartnerAddressHtml, $DocDate, $PartnerName, $PICName, $SJNum, $SJDate, $PONum, $DNNum, $DNDate, $AddressCompany);   
        }  
     }
     
     if($page!=1 && $jml_data<=0){
    
        foreach ($all_page as $row) {
            $num_pages = PDF::getNumPages();
            PDF::startTransaction();
            PDF::SetFont('dejavusans', '', 7.5);
            PDF::SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
        
            PDF::Cell($w_detail[0], 5, $row[0], '', 0, 'L', 0);
            PDF::Cell($w_detail[1], 5, ($row[1] == $row[11] ? $row[11] : substr($row[1],0,50)), '', 0, 'L', 0);
            PDF::Cell($w_detail[4], 5, '', '', 0, 'R', 0);
            PDF::Cell($w_detail[2], 5, $row[3], '', 0, 'R', 0);
            PDF::Cell($w_detail[3], 5, $row[4], '', 0, 'R', 0);
             
            PDF::Ln(4);
    
            PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 2, 'color' => array(128, 128, 128)));
            PDF::Cell($w_detail[0], 5, '', 'B', 0, 'L', 0);
            PDF::Cell($w_detail[1], 5, ($row[1] == $row[11] ? '' : $row[11]), 'B', 0, 'L', 0);
            PDF::Cell($w_detail[4], 5, '', 'B', 0, 'L', 0); 
            PDF::Cell($w_detail[2], 5, ($row[4] == $row[9] ? '' : $row[8]), 'B', 0, 'R', 0);
            PDF::Cell($w_detail[3], 5, ($row[4] == $row[9] ? '' : $row[9]), 'B', 0, 'R', 0); 
            PDF::Ln(6);  
        }
    
            PDF::SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
            
            PDF::Cell($w_detail[0] + $w_detail[1], 6, '', 'TB', 0, 'C');
            PDF::Cell($w_detail[2], 6, '', 'TB', 0, 'R');
            PDF::Cell($w_detail[3], 6, '', 'TB', 0, 'R');
            PDF::Cell($w_detail[4], 6, '', 'TB', 0, 'R');
            
    
            PDF::Ln(); 
            PDF::SetFont('dejavusans', '', 8, '', 'false');
            PDF::Cell(191, 7, 'Description :', '', 0, 'L');
            PDF::Ln();
            PDF::writeHTML($Description, true, false, true, false, ''); 
            PDF::Ln(); 
    
    
            $db_jurnal = DelConfirm::load_data_print_jurnal($trc_type_id,$month_id,$trc_id,$rev_id); 
            $num_jurnal = DelConfirm::rows_data_print_jurnal($trc_type_id,$month_id,$trc_id,$rev_id)->count() ;   
     
            if($num_jurnal>0){
                PDF::Cell(20, 5, 'Account', '', 0, 'L', 0);
                PDF::Cell(91, 5, 'Description', '', 0, 'L', 0); 
                PDF::Cell(20, 5, 'Currency', '', 0, 'L', 0);
                PDF::Cell(30, 5, 'Debet', '', 0, 'R', 0);
                PDF::Cell(30, 5, 'Credit', '', 0, 'R', 0); 
                PDF::Ln(2);
                PDF::SetLineStyle(array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
                PDF::Cell(191, 3, '', 'B', 0, 'L');
                PDF::Ln();
                }
    
            foreach($db_jurnal as $row_jurnal) {  
                PDF::SetFont('dejavusans', '', 8);
                PDF::SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
                PDF::Cell(20, 5, $row_jurnal[1], '', 0, 'L', 0);
                PDF::Cell(91, 5, $row_jurnal[2], '', 0, 'L', 0); 
                PDF::Cell(20, 5, $row_jurnal[3], '', 0, 'L', 0);
                PDF::Cell(30, 5, $row_jurnal[4], '', 0, 'R', 0);
                PDF::Cell(30, 5, $row_jurnal[5], '', 0, 'R', 0);
                PDF::Ln();
            }
    
                PDF::SetLineStyle(array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
                PDF::Cell(191, 3, '', 'T', 0, 'L');
    
            PDF::SetY(-58); 
            $num_row = 225 ;
            
            PDF::Ln(10); 
            if($IsCompleted==1){ PDF::Image('dist/img/sign/company_stamp.png', 90, $num_row, 40, 17, 'PNG', '', '', false, 150, '', false, false, 1, false, false, true); }
            PDF::Cell(5, 6, '', '', 0, 'C');
            PDF::Cell(38, 4, '', '', 0, 'C');
            PDF::Cell(10, 4, '', '', 0, 'C'); 
            PDF::Cell(38, 4, '', '', 0, 'C'); 
            PDF::Cell(10, 4, '', '', 0, 'C');
            PDF::Cell(38, 4, '', '', 0, 'C');
            PDF::Cell(10, 4, '', '', 0, 'C');
            PDF::Cell(38, 4, '', '', 0, 'C');
           
            
            PDF::Ln(20);   
            PDF::SetFont('courier', 'I', 8);
            
            PDF::Cell(5, 6, '', '', 0, 'C');
            PDF::Cell(38, 4, '', '', 0, 'C');
            PDF::Cell(10, 4, '', '', 0, 'C'); 
            PDF::Cell(38, 4, '', '', 0, 'C'); 
            PDF::Cell(10, 4, '', '', 0, 'C');
            PDF::Cell(38, 4, '', '', 0, 'C');
            PDF::Cell(10, 4, '', '', 0, 'C');
            PDF::Cell(38, 4, '', '', 0, 'C');
            PDF::Cell(10, 4, '', '', 0, 'C'); 
            PDF::Ln(); 
             
            
            PDF::SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));  
            PDF::SetFont('dejavusans', 'I', 8);
            PDF::Cell(5, 6, '', '', 0, 'C');
            PDF::Cell(38, 6, '', '', 0, 'C');
            PDF::Cell(10, 6, '', '', 0, 'C');  
            PDF::Cell(38, 6, '', '', 0, 'C');  
            
            
           PDF::Cell(10, 6, '', '', 0, 'C'); 
           PDF::Cell(38, 6, '', '', 0, 'C');   
           PDF::Cell(10, 6, '', '', 0, 'C'); 
            
    
           $style = array(
               'border' => false,
               'vpadding' => 'auto',
               'hpadding' => 'auto',
               'fgcolor' => array(0,0,0),
               'bgcolor' => false, //array(255,255,255)
               'module_width' => 1, // width of a single module in points
               'module_height' => 1 // height of a single module in points
           );
    
        //    PDF::write2DBarcode($url, 'QRCODE,H', 142, 190, 40, 40, $style, 'N');
        //    PDF::Text(20, 25, 'T');
        //    PDF::Cell(38, 6, '', '', 0, 'C'); 
        //    PDF::Ln(); 

           PDF::write2DBarcode($url, 'QRCODE,H', 163, 190, 40, 40, $style, 'N');
            PDF::Text(20, 25, '');
            PDF::Cell(38, 6, '', '', 0, 'C'); 
            PDF::Ln();  
    
            PDF::SetY(-15);
            PDF::SetFont('courier', 'I', 9);
            PDF::SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
            PDF::Cell(100, 6, $DocRegNum, 'T', 0, 'L');
            PDF::Cell(81, 6, '', 'T', 0, 'L');
            PDF::Cell(10, 6, PDF::getAliasNumPage().'/'.PDF::getAliasNbPages(), 'T', 0, 'L');
      
     }
     
    $page++; 
    } 
     
      
     }
       
    
    public function file_print(Request $request){
        $str_req = explode("_",$request->trc_unix_id); 
        $str = explode("_",Crypt::decryptString(str_replace("-","=", $str_req[0]))); 
        $trc_type_id = $str[0];
        $month_id = $str[1];
        $trc_id= $str[2];
        $rev_id = $str[3];
        $doc_status = $str[4];
        $print_option = 0 ;
     
     $data = DB::connection('sqlsrv2')->table('Q_G1_Proc')
     ->where('C010_TrcTypeID', '=', $trc_type_id)
     ->where('C011_Month', '=', $month_id)
     ->where('C000_SysID', '=', $trc_id)
     ->where('C050_Rev', '=', $rev_id)
     ->select('Q_G1_Proc.*')
     ->get();
     if($data->count() > 0){
     foreach($data as $db){ 
     $DocNum = $db->C050_DocNum;
     $DocDate = AppModel::local_date_formate_name(substr($db->C050_DocDate,0,10));
     $PartnerName = $db->PartnerName ;
     $PartnerCode = $db->PartnerCode ; 
     $PartnerAddress = $db->Address ; 
     $ProjectName = $db->Cat1 ; 
     $PICName = $db->C061_PICName ;
     $Telp = $db->Telp ;
     $Fax = $db->Fax ; 
     $DocType = $db->C017_UserGrpFlowTo ;
     $SubTotal = number_format(round($db->C073_Amount)) ;
     $Discount = number_format(round($db->C074_AmountDiscount)) ;
     $Ppn = number_format(round($db->C075_AmountPpn)) ;
     $Total = number_format(round($db->C076_AmountFinal)) ;
     $TOP = number_format(round($db->C052_TermOfPayment)) ;
     $Status = $db->C013_DraftReadyApprCancel;
     $Rev = ($rev_id==0 ? '-' : $rev_id) ; 
     $Checked = $db->Checked ;
     $Approval = $db->Approval ;
     $Legalized = $db->Legalized ;
     $Cat1 = 'Project : '.$db->Cat1 ;
     $Currency = $db->Currency ;
     $judul = $db->C050_DocNum ;     
     $Description = $db->C059_Remark ;
     $PRDocNum = $db->C051_PONum ;
     $UserName = $db->C045_UserName ; 
     $SJNum = $db->C050_ExtDocNum ;
     $SJDate = AppModel::local_date_formate_name(substr($db->C050_ExtDocDate,0,10));
     $DNNum = $db->C050_ExtDocNum1 ;
     $DNDate = AppModel::local_date_formate_name(substr($db->C050_ExtDocDate1,0,10));
     $PONum = $db->C051_PONum ;
     $Address = '' ;
     $flow_id = $db->C017_UserGrpFlowID_From ;
     }
     }else{ 
     $DocNum = '';
     $DocDate = '';
     $PartnerName= ''; 
     $PartnerCode= ''; 
     $ProjectName = ''; 
     $PartnerAddress= '' ;  
     $PICName = '' ;
     $Telp= '' ;
     $Fax= '' ;
     $UserGrpFlowFrom= '' ;
     $SubTotal= '' ;
     $Discount= '' ;
     $Ppn= '' ;
     $Total= '' ;
     $TOP= '' ;
     $Rev= '' ;
     $DocType= '' ;
     $judul= '' ;
     $Approval = '' ;
     $Legalized = '' ; 
     $Status = 0 ; 
     $Address = '' ; 
     $Description = '' ;
     $PRDocNum = '' ;
     $SJNum = '' ;
     $SJDate = '' ;
     $DNNum = '' ;
     $DNDate = '' ;
     $PONum = '' ;
     $UserName = '' ;
     $Currency = '' ;
     $flow_id = '' ;
     }
     
     $Description = '
     <table style="border-collapse:collapse; " width="100%"> 
     <tr><td style=" width: 100%;">'.$Description.'</td></tr>  
     </table>' ;
     
     $CompanyProfile = explode("^", AppModel::find_company_profile('all')) ;
     $CompanyCode= $CompanyProfile[0] ;
     $CompanyName= $CompanyProfile[1] ;
     $AddressCompany= $CompanyProfile[2] ;
     $City= $CompanyProfile[3] ;
     $Province= $CompanyProfile[4] ;
     $State= $CompanyProfile[5] ; 
     $PostalCode= $CompanyProfile[6] ;
     $Phone= $CompanyProfile[7] ;
     $Fax= $CompanyProfile[8] ;
     $Logo= $CompanyProfile[11] ;  
    
     $AddressHtml= '
     <table style="border-collapse:collapse; padding: 0px 5px;" width="100%"> 
     <tr>
     <td style=" width: 48.5%; border-left-width: 1px; border-right-width: 1px;">'.$PartnerAddress.'</td> 
     <td style=" width: 49%; border-left-width: 1px; border-right-width: 1px;">'.$AddressCompany.'<br>'.$City.' - '.$Province.', '.$PostalCode.'<br>'.$State.'</td>
     </tr> 
     </table>   
     ' ;
    
     $DocRegNum= AppModel::find_doc_reg_num($trc_type_id) ;
     $sum = number_format(DelConfirm::sum_detail_doc($trc_type_id,$month_id,$trc_id,$rev_id),0);
     
     $db_sign =  DB::connection('sqlsrv3')->table('t200_documents_approve')
     ->where('trc_type_id', '=', $trc_type_id)
     ->where('month_id', '=', $month_id)
     ->where('trc_id', '=', $trc_id)
     ->where('rev_id', '=', $rev_id)
     ->select('*')
     ->get();  
     if($db_sign->count() > 0){
     foreach($db_sign as $db){  
     $Sign2 = '' ; 
     $Checked = $Checked ; 
     $Approval = $Approval ;
     $Legalized = $Legalized ; 
     $Vendor = 'Please Re-email' ; 
     $UserName	= $db->user1 ; 
     
     $IDUser2 = $db->user_id2 ;
     $IsCompleted = (empty($db->approve4) ? 0 : $db->approve4);  
     $LastUpdate1 = AppModel::local_date_formate_name(substr($db->update1,0,10)).' '.substr($db->update1,11,5) ;
     $LastUpdate2 = ($db->approve2==0 ? '' : AppModel::local_date_formate_name(substr($db->update2,0,10)).' '.substr($db->update2,11,5)) ;
     $LastUpdate3 = ($db->approve3==0 ? '' : AppModel::local_date_formate_name(substr($db->update3,0,10)).' '.substr($db->update3,11,5)) ;  
     $LastUpdate4 = ($db->approve4==0 ? '' : AppModel::local_date_formate_name(substr($db->update4,0,10)).' '.substr($db->update4,11,5)) ;  
     } }else{ 
     $Sign_1 = '' ;
     $Sign_2 = '' ;
     $Sign_3 = '' ; 
     $Sign_4 = '' ;
     $IDUser2 = NULL ;
     $UserName	= $UserName ;
     $Checked = 'Sect. Head' ; 
     $Approval = $Approval ;
     $Legalized = $Legalized ; 
     $Vendor = 'Please Re-email' ; 
     $IsCompleted = 0 ; 
     $LastUpdate1 = '' ;
     $LastUpdate2 = '' ;
     $LastUpdate3 = '' ; 
     $LastUpdate4 = '' ;
     }
      
    PDF::SetTitle($judul);
    PDF::SetAuthor('Aji');
    PDF::setPrintHeader(false);
    PDF::SetTopMargin(5); 
    PDF::SetMargins(13, 7, 7, 7);
    PDF::SetAutoPageBreak(TRUE, 0); 
    PDF::setImageScale(PDF_IMAGE_SCALE_RATIO);
    PDF::setPrintFooter(false);
    PDF::AddPage('P', 'CATENV_N9_1/2');
    PDF::SetFillColor(255,255,255);
    
    
     
     $PartnerAddressHtml= '
     <table style="border-collapse:collapse; padding: 0px 25px;" width="100%"> 
    
     <tr>
     <td style=" width: 48.5%; border-left-width: 1px; border-right-width: 1px;"></td> 
     <td style=" width: 49%; border-left-width: 1px; border-right-width: 1px;">Ship To :</td>
     </tr>
     
     <tr>
     <td style=" width: 48.5%; border-left-width: 1px; border-right-width: 1px;">Telp. : '.$Telp.'</td> 
     <td style=" width: 49%; border-left-width: 1px; border-right-width: 1px;"></td>
     </tr>
     
     <tr>
     <td style=" width: 48.5%; border-left-width: 1px; border-right-width: 1px;">Fax : '.$Fax.'</td> 
     <td style=" width: 49%; border-left-width: 1px; border-right-width: 1px;">'.$AddressCompany.'</td>
     </tr> 
     
     <tr>
     <td style=" width: 48.5%; border-left-width: 1px; border-right-width: 1px;"></td> 
     <td style=" width: 49%; border-left-width: 1px; border-right-width: 1px;">'.$City.' - '.$Province.', '.$PostalCode.'</td>
     </tr> 
     
     <tr>
     <td style=" width: 48.5%; border-left-width: 1px; border-right-width: 1px;">Project : '.$ProjectName.'</td> 
     <td style=" width: 49%; border-left-width: 1px; border-right-width: 1px;">'.$State.'</td>
     </tr>
     
     </table> 
     ' ;
     
        $header = array('No.', 'Product Name', 'Qty', 'UoM', 'Bal');   
        
        $port = env('WEB_DOMAIN') ;  

        $simple_string = $trc_type_id.'_'.$month_id.'_'.$trc_id.'_'.$rev_id ;
        $ciphering = "AES-128-CTR";
        $iv_length = openssl_cipher_iv_length($ciphering);
        $options = 0;
        $encryption_iv = '1234567891011121';
        $encryption_key = "summitadyawinsa";
        $encryption = openssl_encrypt($simple_string, $ciphering, $encryption_key, $options, $encryption_iv);

        // $url = $request->trc_unix_id ; 
        // $url = $encryption ;
        $url = str_replace("=", '^', $encryption) ;
     $this->draw_print_main_header($header, $DocNum, $DocDate, $PartnerName, $Address, $UserName, $PICName, $DocType, $SubTotal, $Discount, $Ppn, $Total, $TOP, $Status, $Rev, $Approval, $Legalized, $Currency, $Description, $Vendor, $Sign_1, $Sign_2, $Sign_3, $Sign_4, $IsCompleted, $LastUpdate1, $LastUpdate2, $LastUpdate3, $LastUpdate4, $sum, $CompanyName, $Logo, $DocRegNum, $City, $Province, $PostalCode, $State, $Phone, $Fax, $ProjectName, $PartnerCode, $Checked, $IDUser2, $doc_status, $trc_type_id, $month_id, $trc_id,$rev_id,$print_option,$AddressHtml,$PartnerAddressHtml, $SJNum, $SJDate, $PONum, $DNNum, $DNDate, $AddressCompany, $flow_id, $url); 
     PDF::Output($judul.'.pdf');
    }

    function print_label_view(Request $request){    
        $data['trc_unix_id'] = $request->trc_unix_id ;     
        return view('del_confirm.direct_label_print', $data);
    }
 
    function file_label_print(Request $request){    
        $str = explode("_",$request->trc_unix_id) ;  
        $str = explode("_",Crypt::decryptString(str_replace("-","=",$str[0]))) ; 
        $trc_type_id = $str[0];
        $month_id = $str[1];
        $trc_id= $str[2];
        $rev_id = $str[3];   
    
        $db = DelConfirm::get_head_properties($str);
        foreach ($db AS $row) {
            $flow_id = $row->C017_UserGrpFlowID_To ;
            $flow_id = $row->C017_UserGrpFlowID_To ;
        } 
        $str_prop = explode("_", DelConfirm::find_as_source_properties($trc_type_id, $flow_id)) ;
        if ($str_prop[7] == 1100 || $str_prop[7] == 1130) {
            $form_data = DB::connection('sqlsrv2')->table('v_tag_label_ims')     
            ->where('trc_type_id', '=', $trc_type_id)
            ->where('month_id', '=', $month_id)
            ->where('trc_id', '=', $trc_id)  
            ->where('C050_Rev', '=', $rev_id)  
            ->get() ;
        } else {
            $form_data = DB::connection('sqlsrv2')->table('v_tag_label_others_ims')     
            ->where('trc_type_id', '=', $trc_type_id)
            ->where('month_id', '=', $month_id)
            ->where('trc_id', '=', $trc_id)  
            ->where('C050_Rev', '=', $rev_id)  
            ->get() ;
        } 
         
      
        $w_top = array(95, 35, 60, 2, 30, 55);  
        $style = array(
            'border' => false,
            'vpadding' => 2,
            'hpadding' => 2,
            'fgcolor' => array(0,0,0),
            'bgcolor' => false, //array(255,255,255)
            'module_width' => 1, // width of a single module in points
            'module_height' => 1 // height of a single module in points
        );
           
        foreach ( $form_data as $db ) 
        { 
            $item_no = (strlen($db->item_no) > 35 ? substr($db->item_no,0,35).' ...' : $db->item_no) ;
            $item_name = (strlen($db->item_name) > 35 ? substr(str_replace(",","", $db->item_name),0,35) : $db->item_name) ; 
            $category_id = $db->category_id ;
            $category_name = strtoupper((strlen($db->category_name) > 13 ? $db->category_code : $db->category_name)) ; 
            $qty = number_format($db->qty_2,0) ;
            $myName = Auth::user()->username ;
            date_default_timezone_set('Asia/Jakarta') ;
            $getDate = date('d-m-Y H:i') ; 
     
            $docnum =  $db->trc_type_id.'-'.$db->month_id.'-'.$db->trc_id.'-'.$db->rev_id.'-'.$db->line_id ;   
            $docnum2 =  $db->docnum ;   
            $ext_docnum =  $db->ext_docnum ;  
            $docdate = AppModel::local_date_formate_name(($db->ext_docdate == '' ? $db->docdate : $db->ext_docdate)) ;   
            $partner_code = ($db->partner_code == '' ? '-' : $db->partner_code) ;
            $product_type = ($db->length <= 0 ? 0 : 1) ;
            $address_id = $db->address_id ; 
            $pcs_per_unit = number_format(($db->pcs_per_unit == null ? 0 : $db->pcs_per_unit),1);
            $unit_weight = number_format(($db->unit_weight == null ? 0 : $db->unit_weight),3);  
            $customer_code = $db->customer_code ;
            
            $spec = $db->spec ;
    
            if ( $db->status_part_id == 1 ) {
                $status_part = 'OK' ;
            } else if ( $db->status_part_id == 2 ) {
                $status_part = 'NG' ;
            } else if ( $db->status_part_id == 3 ) {
                $status_part = 'Repair' ;
            } else {
                $status_part = 'OK' ;
            }
    
    
            if ( $category_id == 1 )
            {
                if ( $db->length == 0 ) {
                    $size_l = 'XC' ;
                    $unit = 'Kg' ;
                    $unit2 = 'Kg' ;
                } else if ( $db->length < 0 ) {
                    $size_l = '' ;
                    $unit = 'Btg' ;
                    $unit2 = 'Pcs' ;
                } else {
                    $size_l = 'X'.number_format($db->length,0) ;
                    $unit = 'Sht' ;
                    $unit2 = 'Pcs' ;
                }
                
                $size = number_format($db->thickness,2).'X'.number_format($db->width,1).''.$size_l ;
                
                
                if($address_id==0){$address = '';} else
                if($address_id==1){$address = 'A'.$db->detail_address_id;} else
                if($address_id==2){$address = 'B'.$db->detail_address_id;} else
                if($address_id==3){$address = 'C'.$db->detail_address_id;} else
                if($address_id==4){$address = 'D'.$db->detail_address_id;} else
                if($address_id==5){$address = 'E'.$db->detail_address_id;} else
                if($address_id==6){$address = 'F'.$db->detail_address_id;} else
                if($address_id==7){$address = 'G'.$db->detail_address_id;} else
                if($address_id==8){$address = 'H'.$db->detail_address_id;} else
                if($address_id==9){$address = 'I'.$db->detail_address_id;} else
                if($address_id==10){$address = 'J'.$db->detail_address_id;} else
                if($address_id==11){$address = 'K'.$db->detail_address_id;} else
                if($address_id==12){$address = 'L'.$db->detail_address_id;} else
                if($address_id==13){$address = 'M'.$db->detail_address_id;} else
                if($address_id==14){$address = 'N'.$db->detail_address_id;} else
                if($address_id==15){$address = 'O'.$db->detail_address_id;} else
                if($address_id==16){$address = 'P'.$db->detail_address_id;} else
                { $address = ''; }
    
                
                if($product_type==0){
                    $uom = $unit_weight ;
                    $unit = 'Kg' ;
                    $total_pcs = ($uom == 0 ? 0 : number_format(floor($db->qty_2 / $db->unit_weight),0)) ; 
                } else {
                    $uom = $pcs_per_unit ; 
                    
                    $total_pcs = ($uom == 0 ? 0 : number_format(floor($db->qty_2 * $uom),0)) ; 
                }
                
     
            PDF::SetTitle($docnum);
            PDF::SetAuthor('Aji');
            PDF::setPrintHeader(false);
            PDF::SetTopMargin(5); 
            PDF::SetMargins(5, 5, 7, 7);
            PDF::SetAutoPageBreak(TRUE, 0); 
            PDF::setImageScale(PDF_IMAGE_SCALE_RATIO);
            PDF::setPrintFooter(false);
            PDF::AddPage('P', 'A6');
            PDF::SetFillColor(255,255,255); 
            PDF::SetTextColor(0); 
            PDF::SetFont('dejavusans', '', 8);
    
            PDF::Cell($w_top[0], 5, 'FO-34-1', 'B', 0, 'L', 0); 
            PDF::Ln();
    
            PDF::SetFont('dejavusans', 'B', 9);
            PDF::Cell($w_top[1], 5, '', 'RL', 0, 'L', 0);
            PDF::Cell($w_top[2], 5, '', 'R', 0, 'L', 0);  
            PDF::Ln();
    
            PDF::Cell($w_top[1], 5, '', 'RL', 0, 'L', 0); 
            PDF::Cell($w_top[2], 5, '', 'R', 0, 'L', 0);  
            PDF::Ln();
    
            PDF::Cell($w_top[1], 5, '', 'RL', 0, 'L', 0);
            PDF::Cell($w_top[2], 5, 'IN - INVENTORY - TAG LABEL', 'R', 0, 'C', 0); 
            PDF::Ln();
    
            PDF::SetFont('dejavusans', 'B', 18);
            PDF::Cell($w_top[1], 5, '', 'RL', 0, 'L', 0); 
            PDF::Cell($w_top[2], 5, $category_name, 'R', 0, 'C', 0); 
            PDF::Ln();
    
            PDF::SetFont('dejavusans', 'B', 9); 
            PDF::Cell($w_top[1], 6, '', 'RL', 0, 'L', 0);
            PDF::Cell($w_top[2], 6, '', 'R', 0, 'C', 0); 
            PDF::Ln();
    
    
            PDF::Cell($w_top[1], 5, '', 'BRL', 0, 'L', 0);
            PDF::Cell($w_top[4], 5, $db->username, 'TBR', 0, 'C', 0); 
            PDF::Cell($w_top[4], 5, $customer_code, 'TBR', 0, 'C', 0); 
            PDF::write2DBarcode($docnum, 'QRCODE,H', 7.5, 12, 30, 30, $style, 'N');
            PDF::Ln(1);
    
    
            PDF::Cell($w_top[1], 5, '', 'L', 0, 'L', 0);
            PDF::Cell($w_top[2], 5, '', 'R', 0, 'L', 0); 
            PDF::Ln();
    
            PDF::SetFont('dejavusans', '', 8);
    
            PDF::Cell(25, 7, ' ID', 'L', 0, 'L', 0);
            PDF::Cell($w_top[3], 7, ':', '', 0, 'L', 0);
            PDF::Cell(68, 7, $docnum, 'R', 0, 'L', 0); 
            PDF::Ln();
    
            PDF::Cell(25, 7, ' DATE', 'L', 0, 'L', 0);
            PDF::Cell($w_top[3], 7, ':', '', 0, 'L', 0);
            PDF::Cell(68, 7, $docdate, 'R', 0, 'L', 0); 
            PDF::Ln();
    
            PDF::Cell(25, 7, ' ITEM NO', 'L', 0, 'L', 0);
            PDF::Cell($w_top[3], 7, ':', '', 0, 'L', 0);
            PDF::Cell(68, 7, $item_no, 'R', 0, 'L', 0); 
            PDF::Ln();
    
            PDF::Cell(25, 7, ' ITEM NAME', 'L', 0, 'L', 0);
            PDF::Cell($w_top[3], 7, ':', '', 0, 'L', 0);
            PDF::Cell(68, 7, $item_name, 'R', 0, 'L', 0); 
            PDF::Ln();
    
            PDF::Cell(25, 7, ' SPEC', 'L', 0, 'L', 0);
            PDF::Cell($w_top[3], 7, ':', '', 0, 'L', 0);
            PDF::Cell(68, 7, $spec, 'R', 0, 'L', 0); 
            PDF::Ln();
    
            PDF::Cell(25, 7, ' SIZE', 'L', 0, 'L', 0);
            PDF::Cell($w_top[3], 7, ':', '', 0, 'L', 0);
            PDF::Cell(68, 7, $size, 'R', 0, 'L', 0); 
            PDF::Ln();
    
            PDF::Cell(25, 7, ' SUPPLIER', 'L', 0, 'L', 0);
            PDF::Cell($w_top[3], 7, ':', '', 0, 'L', 0);
            PDF::Cell(68, 7, $partner_code. ' / '.$ext_docnum, 'R', 0, 'L', 0); 
            PDF::Ln();
    
            PDF::Cell(25, 7, ' COIL No.', 'L', 0, 'L', 0);
            PDF::Cell($w_top[3], 7, ':', '', 0, 'L', 0);
            PDF::Cell(68, 7, $db->serial_number, 'R', 0, 'L', 0); 
            PDF::Ln();
    
            PDF::Cell($w_top[1], 5, '', 'BL', 0, 'L', 0);
            PDF::Cell($w_top[2], 5, '', 'BR', 0, 'L', 0); 
            PDF::Ln();
    
            PDF::SetFont('dejavusans', 'B', 9);
            PDF::Cell(20, 8, 'QTY', 'BLR', 0, 'C', 0);
            PDF::Cell(20, 8, 'Conv', 'BLR', 0, 'C', 0);
            PDF::Cell(20, 8, 'Total', 'BR', 0, 'C', 0);
            PDF::Cell(35, 8, '', 'R', 0, 'L', 0); 
            PDF::Ln();
    
            PDF::Cell(20, 5, '', 'LR', 0, 'C', 0);
            PDF::Cell(20, 5, '', 'LR', 0, 'C', 0);
            PDF::Cell(20, 5, '', 'R', 0, 'C', 0);
            PDF::Cell(35, 5, '', 'R', 0, 'C', 0); 
            PDF::Ln();
    
            PDF::SetFont('dejavusans', 'B', 11);
            PDF::Cell(20, 10, $qty, 'LR', 0, 'C', 0);
            PDF::Cell(20, 10, $uom, 'LR', 0, 'C', 0);
            PDF::Cell(20, 10, $total_pcs, 'R', 0, 'C', 0);
            PDF::Cell(35, 10, substr($docdate,3,8), 'R', 0, 'C', 0); 
            PDF::Ln();
    
            PDF::SetFont('dejavusans', '', 8);
            PDF::Cell(20, 7, $unit, 'BLR', 0, 'C', 0);
            PDF::Cell(20, 7, $unit2, 'BLR', 0, 'C', 0);
            PDF::Cell(20, 7, 'Pcs', 'BR', 0, 'C', 0);
            PDF::Cell(35, 7, '', 'BR', 0, 'C', 0); 
            PDF::Ln();
    
            
            PDF::Cell(45, 5, $myName, '', 0, 'L', 0);
            PDF::Cell(50, 5, $getDate, '', 0, 'R', 0); 
    
            } else {
    
            PDF::SetTitle($docnum);
            PDF::SetAuthor('Aji');
            PDF::setPrintHeader(false);
            PDF::SetTopMargin(5); 
            PDF::SetMargins(5, 5, 7, 7);
            PDF::SetAutoPageBreak(TRUE, 0); 
            PDF::setImageScale(PDF_IMAGE_SCALE_RATIO);
            PDF::setPrintFooter(false);
            PDF::AddPage('P', 'A6');
            PDF::SetFillColor(255,255,255); 
            PDF::SetTextColor(0); 
            PDF::SetFont('dejavusans', '', 8);
    
            PDF::Cell($w_top[0], 5, 'FO-34-1', 'B', 0, 'L', 0); 
            PDF::Ln();
    
            PDF::SetFont('dejavusans', 'B', 9);
            PDF::Cell($w_top[1], 5, '', 'RL', 0, 'L', 0);
            PDF::Cell($w_top[2], 5, '', 'R', 0, 'L', 0);  
            PDF::Ln();
    
            PDF::Cell($w_top[1], 5, '', 'RL', 0, 'L', 0); 
            PDF::Cell($w_top[2], 5, '', 'R', 0, 'L', 0);  
            PDF::Ln();
    
            PDF::Cell($w_top[1], 5, '', 'RL', 0, 'L', 0);
            PDF::Cell($w_top[2], 5, 'IN - INVENTORY - TAG LABEL', 'R', 0, 'C', 0); 
            PDF::Ln();
    
            PDF::SetFont('dejavusans', 'B', 16);
            PDF::Cell($w_top[1], 5, '', 'RL', 0, 'L', 0); 
            PDF::Cell($w_top[2], 5, $category_name, 'R', 0, 'C', 0); 
            PDF::Ln();
     
            PDF::SetFont('dejavusans', 'B', 9);
            PDF::Cell($w_top[1], 6, '', 'RL', 0, 'L', 0);
            PDF::Cell($w_top[2], 6, '', 'R', 0, 'C', 0); 
            PDF::Ln();
     
            PDF::Cell($w_top[1], 5, '', 'BRL', 0, 'L', 0);
            PDF::Cell($w_top[4], 5, $db->username, 'TBR', 0, 'C', 0); 
            PDF::Cell($w_top[4], 5, $partner_code, 'TBR', 0, 'C', 0); 
            PDF::write2DBarcode($docnum, 'QRCODE,H', 7.5, 12, 30, 30, $style, 'N');
            PDF::Ln(1);
    
            PDF::Cell($w_top[1], 5, '', 'L', 0, 'L', 0);
            PDF::Cell($w_top[2], 5, '', 'R', 0, 'L', 0); 
            PDF::Ln();
    
            PDF::SetFont('dejavusans', '', 8); 
            PDF::Cell(25, 7, ' ID', 'L', 0, 'L', 0);
            PDF::Cell($w_top[3], 7, ':', '', 0, 'L', 0);
            PDF::Cell(68, 7, $docnum, 'R', 0, 'L', 0); 
            PDF::Ln();
    
            PDF::Cell(25, 7, ' DATE', 'L', 0, 'L', 0);
            PDF::Cell($w_top[3], 7, ':', '', 0, 'L', 0);
            PDF::Cell(68, 7, $docdate, 'R', 0, 'L', 0); 
            PDF::Ln();
    
            PDF::Cell(25, 7, ' ITEM NO', 'L', 0, 'L', 0);
            PDF::Cell($w_top[3], 7, ':', '', 0, 'L', 0);
            PDF::Cell(68, 7, $item_no, 'R', 0, 'L', 0); 
            PDF::Ln();
    
            PDF::Cell(25, 7, ' ITEM NAME', 'L', 0, 'L', 0);
            PDF::Cell($w_top[3], 7, ':', '', 0, 'L', 0);
            PDF::Cell(68, 7, $item_name, 'R', 0, 'L', 0); 
            PDF::Ln();
    
            PDF::Cell(25, 7, ' SUPPLIER', 'L', 0, 'L', 0);
            PDF::Cell($w_top[3], 7, ':', '', 0, 'L', 0);
            PDF::Cell(68, 7, $partner_code.' / '.$ext_docnum, 'R', 0, 'L', 0); 
            PDF::Ln();
    
            PDF::Cell(25, 7, '', 'L', 0, 'L', 0);
            PDF::Cell($w_top[3], 7, '', '', 0, 'L', 0);
            PDF::Cell(68, 7, '', 'R', 0, 'L', 0); 
            PDF::Ln();
    
            PDF::Cell($w_top[1], 5, '', 'BL', 0, 'L', 0);
            PDF::Cell($w_top[2], 5, '', 'BR', 0, 'L', 0); 
            PDF::Ln();
    
            PDF::SetFont('dejavusans', 'B', 9);
            PDF::Cell(30, 8, 'QTY', 'BLR', 0, 'C', 0); 
            PDF::Cell(30, 8, 'Inspection', 'BR', 0, 'C', 0);
            PDF::Cell(35, 8, '', 'R', 0, 'L', 0); 
            PDF::Ln();
    
            PDF::Cell(30, 5, '', 'LR', 0, 'C', 0); 
            PDF::Cell(30, 5, '', 'R', 0, 'C', 0);
            PDF::Cell(35, 5, '', 'R', 0, 'C', 0); 
            PDF::Ln();
    
            PDF::SetFont('dejavusans', 'B', 11);
            PDF::Cell(30, 10, $qty, 'LR', 0, 'C', 0); 
            PDF::Cell(30, 10, '', 'R', 0, 'C', 0);
            PDF::SetFont('dejavusans', 'B', 22);
            PDF::Cell(35, 10, $status_part, 'R', 0, 'C', 0); 
            PDF::Ln();
    
            PDF::SetFont('dejavusans', '', 8);
            PDF::Cell(30, 7, '', 'BLR', 0, 'C', 0); 
            PDF::Cell(30, 7, '', 'BR', 0, 'C', 0);
            PDF::Cell(35, 7, '', 'BR', 0, 'C', 0); 
            PDF::Ln();
      
            PDF::SetFont('dejavusans', '', 8);
            PDF::Cell(45, 5, $myName, '', 0, 'L', 0);
            PDF::Cell(50, 5, $getDate, '', 0, 'R', 0); 
                
            }
    
    }
      
        PDF::Output('label.pdf');
        
    }
    
}
