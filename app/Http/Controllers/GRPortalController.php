<?php

namespace App\Http\Controllers;

use App\Models\GRPortal;
use App\Models\AppModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt ;
use Illuminate\Support\Facades\DB;
use PDF;  

class GRPortalController extends Controller
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
        return view('gr_portal/gr_portal_index', $data); 
    }
 
    public function front_table(Request $request){   
        // $flow_id = ($request->flow_id === null ? 28 : $request->flow_id) ;
        // $position = ($request->position == 0 ? 1 : $request->position) ;  
        $vendor_id = Auth::user()->partner_id ; 
        // dd($vendor_id); 
        // SELECT a.Company, a.VendorNum, b.VendorID, b.Name, a.PackSlip AS C050_DocNum, a.ReceiptDate AS C050_DocDate, a.Received
        // FROM  Erp.RcvHead a INNER JOIN
        // Erp.Vendor b ON a.VendorNum = b.VendorNum
        // WHERE (a.Received = 1)

        $search = $request->front_table_search ;   
        date_default_timezone_set('Asia/Jakarta'); 
        $yearX = substr(date('Y'),2,2).date('m') ;
        $year = ($request->range_date === null ? $yearX : $request->range_date) ;   
        $columns = array(  
          0 =>'a.LegalNumber', 
          1 =>'a.LegalNumber',
          2 =>'a.ReceiptDate',
          3 =>'a.PackSlip'
        );  
         
        $totalData = GRPortal::get_transaction_list($year, $search, $vendor_id)->count(); 
        $totalFiltered = $totalData;  
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = ($request->input('order.0.column')==0 ? $columns[1] : $columns[$request->input('order.0.column')]);
        $dir = ($request->input('order.0.column')==0 ? 'desc' : $request->input('order.0.dir')) ; 
        // dd($order);
        if(empty($search))
        {            
        $posts = GRPortal::get_transaction_list($year, $search, $vendor_id)
        ->offset($start)
        ->limit($limit)
        ->orderBy($order,$dir)
        ->get();
        } else {
        
        $posts =  GRPortal::get_transaction_list($year, $search, $vendor_id) 
        ->offset($start)
        ->limit($limit) 
        ->orderBy($order,$dir)
        ->get();  
        $totalFiltered = GRPortal::get_transaction_list($year, $search, $vendor_id)->count();
        } 
        $data = array();
        if(!empty($posts))
        { 
        $no = $start ;
        // dd($posts);
        foreach ($posts as $post)
        { 
        $no++; 
        $trc_id = Crypt::encryptString($post->VendorNum.'_'.$post->PackSlip) ;   
        $sys_id = "'".str_replace("=","-", $trc_id).'_'.$no."'" ;     
    
        $button = '
        <span class="svg-icon svg-icon-primary svg-icon-2x" style="cursor: pointer;" id="'.$trc_id.'"  onclick="document_preview('.$sys_id.') ;">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="black"></path><path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="black"></path><path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="black"></path></svg>
        </span>' ; 
        // dd($post->C050_DocDate);
        $nestedData['no'] = $no ; 
        $nestedData['docnum'] = $post->LegalNumber ;   
        $nestedData['shipnum'] = $post->PackSlip ;	
        $nestedData['docdate'] = AppModel::local_date_formate_name(substr($post->ReceiptDate,0,10)) ;    
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

    public function export_front_table(Request $request){   
        // $flow_id = ($request->flow_id === null ? 28 : $request->flow_id) ;
        // $position = ($request->position == 0 ? 1 : $request->position) ;  
        $vendor_id = Auth::user()->partner_id ; 
        $search = $request->front_table_search ;   
        date_default_timezone_set('Asia/Jakarta'); 
        $yearX = substr(date('Y'),2,2).date('m') ;
        $year = ($request->range_date === null ? $yearX : $request->range_date) ;    
        $data['full_name'] = Auth::user()->full_name ;
        $data['list'] = GRPortal::get_transaction_list($year, $search, $vendor_id)->get(); 
        $data['num'] = GRPortal::get_transaction_list($year, $search, $vendor_id)->count(); 
        // $data['ref_form'] = GRPortal::find_trc_name(1200, $flow_id) ; 
        return view('gr_portal.gr_export', $data); 
    }

    public function show(Request $request) {         
        $str_req = explode("_",$request->trc_unix_id); 
        $str = explode("_",Crypt::decryptString(str_replace("-","=", $str_req[0]))); 
        // $trc_type_id = $str[0] ; 
        // $month_id = $str[1] ; 
        // $trc_id = $str[2] ; 
        // $rev_id = $str[3] ;           
        // $flow_id = $request->flow_id ;
        $sys_id = "'".$request->trc_unix_id."'" ;    
        $vendor_num = $str[0] ;
        $pack_slip = $str[1] ;
        $data_detail = GRPortal::data_detail($vendor_num, $pack_slip);  
        foreach ($data_detail as $db) {
            $sys_id = Crypt::encryptString($db->VendorNum.'_'.$db->PackSlip.'_'.$db->PackLine.'_0') ;
            $sys_id = str_replace("=", "-", $sys_id).'_1' ;
            $data['trc_unix_id'] =  $sys_id ;   
            $data['ref_form'] = '' ;
            // $data['ref_form'] = GRPortal::find_trc_name($db->C010_TrcTypeID, $flow_id) ;
            $data['ref_tab'] = $request->ref_tab ;  
        }  
        return view('gr_portal.gr_preview', $data);  
    }

    function print_view(Request $request){    
        $data['trc_unix_id'] = $request->trc_unix_id ;     
        $data['ref_form'] = $request->ref_form ;  
        return view('gr_portal.gr_direct_print', $data);
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
        PDF::Cell($w_top[1], 5, 'Material Receipt PO '.$DocType, '', 0, 'R', 0);
    
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
        PDF::Cell(35, 5, 'SJ Num', 'L', 0, 'L', 1);
        PDF::Cell(5, 5, ':', '', 0, 'C', 1);
        PDF::Cell(90, 5, $SJNum, 'R', 0, 'L', 1); 
        PDF::Cell(20, 5, 'PO Num', 'L', 0, 'L', 1); 
        PDF::Cell(5, 5, ':', '', 0, 'C', 1);
        PDF::Cell(36, 5, $PONum, 'R', 0, 'L', 1);
    
        PDF::Ln();
        PDF::SetFont('dejavusans', '', 8);
        PDF::Cell(35, 5, 'SJ Date', 'L', 0, 'L', 1);
        PDF::Cell(5, 5, ':', '', 0, 'C', 1);
        PDF::Cell(90, 5, $SJDate, 'R', 0, 'L', 1); 
        PDF::Cell(20, 5, 'DN Num', 'L', 0, 'L', 1); 
        PDF::Cell(5, 5, ':', '', 0, 'C', 1);
        PDF::Cell(36, 5, $DNNum, 'R', 0, 'L', 1);
    
        PDF::Ln();
        PDF::SetFont('dejavusans', '', 8);
        PDF::Cell(35, 5, '', 'BL', 0, 'L', 1);
        PDF::Cell(5, 5, '', 'B', 0, 'C', 1);
        PDF::Cell(90, 5, '', 'BR', 0, 'L', 1); 
        PDF::Cell(20, 5, 'DN Date', 'BL', 0, 'L', 1); 
        PDF::Cell(5, 5, ':', 'B', 0, 'C', 1);
        PDF::Cell(36, 5, $DNDate, 'BR', 0, 'L', 1);
      
        
        $imgdata = base64_decode(substr(str_replace("-","+", $Logo),22)) ; 
        PDF::Image('@'.$imgdata, 155, 5, 50, 13, '', '', '', false, 150, '', false, false, 1, false, false, true); 
    
        if ($doc_status==1) {    
            PDF::Image('dist/img/draft.png', 10, 40, 350, 230, 'PNG', '', '', false, 150, '', false, false, 1, false, false, true);
        } 
         
       PDF::Ln(8);
       PDF::SetFont('dejavusans', '', 8);
       PDF::SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
       PDF::Cell($w_head[0], 6, $header[0], 'TB', 0, 'L', 1);
       PDF::Cell($w_head[1], 6, $header[1], 'TB', 0, 'L', 1); 
       PDF::Cell($w_head[2], 6, $header[2], 'TB', 0, 'R', 1);
       PDF::Cell($w_head[3], 6, $header[3], 'TB', 0, 'L', 1); 
       PDF::Cell($w_head[4], 6, $header[4], 'TB', 0, 'R', 1);
       PDF::Ln(7);
    
     PDF::SetFillColor(224, 235, 255);
     PDF::SetTextColor(0);
     PDF::SetFont('');
    }
    
    public function draw_print_main_header($header, $DocNum, $DocDate, $PartnerName, $Address, $UserName, $PICName, $DocType, $SubTotal, $Discount, $Ppn, $Total, $TOP, $Status, $Rev, $Currency, $Description, $Vendor, $sum, $CompanyName, $Logo, $DocRegNum, $City, $Province, $PostalCode, $State, $Phone, $Fax, $ProjectName, $PartnerCode, $doc_status, $vendor_num, $pack_slip, $print_option, $AddressHtml, $PartnerAddressHtml, $SJNum, $SJDate, $PONum, $DNNum, $DNDate, $AddressCompany, $url){
     
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
        PDF::Cell($w_top[1], 5, 'Material Receipt PO '.$DocType, '', 0, 'R', 0);
    
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
        PDF::Cell(35, 5, 'SJ Num', 'L', 0, 'L', 1);
        PDF::Cell(5, 5, ':', '', 0, 'C', 1);
        PDF::Cell(90, 5, $SJNum, 'R', 0, 'L', 1); 
        PDF::Cell(20, 5, 'PO Num', 'L', 0, 'L', 1); 
        PDF::Cell(5, 5, ':', '', 0, 'C', 1);
        PDF::Cell(36, 5, $PONum, 'R', 0, 'L', 1);
    
        PDF::Ln();
        PDF::SetFont('dejavusans', '', 8);
        PDF::Cell(35, 5, 'SJ Date', 'L', 0, 'L', 1);
        PDF::Cell(5, 5, ':', '', 0, 'C', 1);
        PDF::Cell(90, 5, $SJDate, 'R', 0, 'L', 1); 
        PDF::Cell(20, 5, 'DN Num', 'L', 0, 'L', 1); 
        PDF::Cell(5, 5, ':', '', 0, 'C', 1);
        PDF::Cell(36, 5, $DNNum, 'R', 0, 'L', 1);
    
        PDF::Ln();
        PDF::SetFont('dejavusans', '', 8);
        PDF::Cell(35, 5, '', 'BL', 0, 'L', 1);
        PDF::Cell(5, 5, '', 'B', 0, 'C', 1);
        PDF::Cell(90, 5, '', 'BR', 0, 'L', 1); 
        PDF::Cell(20, 5, 'DN Date', 'BL', 0, 'L', 1); 
        PDF::Cell(5, 5, ':', 'B', 0, 'C', 1);
        PDF::Cell(36, 5, $DNDate, 'BR', 0, 'L', 1);
      
        
        $imgdata = base64_decode(substr(str_replace("-","+", $Logo),22)) ; 
        PDF::Image('@'.$imgdata, 155, 5, 50, 13, '', '', '', false, 150, '', false, false, 1, false, false, true); 
    
    
         
       PDF::Ln(8);
       PDF::SetFont('dejavusans', '', 8);
       PDF::SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
       PDF::Cell($w_head[0], 6, $header[0], 'TB', 0, 'L', 1);
       PDF::Cell($w_head[1], 6, $header[1], 'TB', 0, 'L', 1); 
       PDF::Cell($w_head[2], 6, $header[2], 'TB', 0, 'R', 1);
       PDF::Cell($w_head[3], 6, $header[3], 'TB', 0, 'L', 1); 
       PDF::Cell($w_head[4], 6, $header[4], 'TB', 0, 'R', 1);
       PDF::Ln(7);
          

        $DB = GRPortal::detail_list_data($vendor_num,$pack_slip);
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

        // dd($vendor_num, $pack_slip, $Status, $print_option, $startX, $limitX);
        $all_page = GRPortal::load_data_print_page($vendor_num, $pack_slip, $Status, $print_option, $startX, $limitX); 
        // dd($all_page);
         foreach ($all_page as $row) {
             $num_pages = PDF::getNumPages();
             PDF::startTransaction();
             PDF::SetFont('dejavusans', '', 7.5);
             PDF::SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
        
             
            PDF::Cell($w_detail[0], 5, $row[0], '', 0, 'L', 0);
            PDF::Cell($w_detail[1], 5, ($row[1] == $row[11] ? $row[11] : substr($row[1],0,50)), '', 0, 'L', 0);
            PDF::Cell($w_detail[2], 5, $row[3], '', 0, 'R', 0);
            PDF::Cell($w_detail[3], 5, $row[4], '', 0, 'L', 0);
            PDF::Cell($w_detail[4], 5, $row[12], '', 0, 'R', 0); 
            PDF::Ln(4);
     
            PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 2, 'color' => array(128, 128, 128)));
            PDF::Cell($w_detail[0], 5, '', 'B', 0, 'L', 0);
            PDF::Cell($w_detail[1], 5, ($row[1] == $row[11] ? '' : $row[11]), 'B', 0, 'L', 0);
            PDF::Cell($w_detail[2], 5, (($row[4] == $row[9] && $row[8] == $row[3]) ? '' : $row[8]), 'B', 0, 'R', 0);
            PDF::Cell($w_detail[3], 5, (($row[4] == $row[9] && $row[8] == $row[3]) ? '' : $row[9]), 'B', 0, 'L', 0);
            PDF::Cell($w_detail[4], 5, '', 'B', 0, 'L', 0); 
            PDF::Ln(6); 
         } 
     
         if ($num<=5) {
            PDF::SetFont('dejavusans', '', 8);
            PDF::SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
            PDF::Cell($w_detail[0] + $w_detail[1], 6, 'Total Qty :', 'TB', 0, 'C');
            PDF::Cell($w_detail[2], 6, $sum, 'TB', 0, 'R');
            PDF::Cell($w_detail[3] + $w_detail[4], 6, '', 'TB', 0, 'R');
    
            PDF::Ln(); 
            PDF::SetFont('dejavusans', '', 8, '', 'false');
            PDF::Cell(191, 7, 'Description :', '', 0, 'L');
            PDF::Ln();
            PDF::writeHTML($Description, true, false, true, false, ''); 
            PDF::Ln(); 
    
    
             
    
            PDF::SetLineStyle(array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
            PDF::Cell(191, 3, '', 'T', 0, 'L');
    
            PDF::SetY(-58); 
    
            $num_row = 225 ; 
            PDF::Ln(10); 
              
            $style = array(
                'border' => true,
                'vpadding' => 2,
                'hpadding' => 2,
                'fgcolor' => array(0,0,0),
                'bgcolor' => false, //array(255,255,255)
                'module_width' => 1, // width of a single module in points
                'module_height' => 1 // height of a single module in points
            );
       
            PDF::write2DBarcode($url, 'QRCODE,H', 158, 200, 45, 45, $style, 'N'); 
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
                $start = 15 * ($page - 1) ;
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
            $jml_data = $jml_data - 6 ; 
        }
     
        $all_page = GRPortal::load_data_print_page($vendor_num,$pack_slip,$Status,$print_option,$start,$limit); 
    
    if ($page!=1 && $jml_data > 0) {
        foreach ($all_page as $row) {
            $num_pages = PDF::getNumPages();
                PDF::startTransaction();
                PDF::SetFont('dejavusans', '', 7.5);
                PDF::SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
        
                PDF::Cell($w_detail[0], 5, $row[0], '', 0, 'L', 0);
                PDF::Cell($w_detail[1], 5, ($row[1] == $row[11] ? $row[11] : substr($row[1],0,50)), '', 0, 'L', 0);
                PDF::Cell($w_detail[2], 5, $row[3], '', 0, 'R', 0);
                PDF::Cell($w_detail[3], 5, $row[4], '', 0, 'L', 0);
                PDF::Cell($w_detail[4], 5, $row[12], '', 0, 'R', 0); 
                PDF::Ln(4);
    
                PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 2, 'color' => array(128, 128, 128)));
                PDF::Cell($w_detail[0], 5, '', 'B', 0, 'L', 0);
                PDF::Cell($w_detail[1], 5, ($row[1] == $row[11] ? '' : $row[11]), 'B', 0, 'L', 0);
                PDF::Cell($w_detail[2], 5, (($row[4] == $row[9] && $row[8] == $row[3]) ? '' : $row[8]), 'B', 0, 'R', 0);
                PDF::Cell($w_detail[3], 5, (($row[4] == $row[9] && $row[8] == $row[3]) ? '' : $row[9]), 'B', 0, 'L', 0);
                PDF::Cell($w_detail[4], 5, '', 'B', 0, 'L', 0); 
                PDF::Ln(6); 
        }
      
        PDF::SetY(-15);
        PDF::SetFont('courier', 'I', 9);
        PDF::SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
        PDF::Cell(100, 6, $DocRegNum, 'T', 0, 'L');
        PDF::Cell(81, 6, '', 'T', 0, 'L');
        PDF::Cell(10, 6, PDF::getAliasNumPage().'/'.PDF::getAliasNbPages(), 'T', 0, 'L');
    
        if ($jml_data<=0) { 
            PDF::Cell($w_detail[0] + $w_detail[1], 6, 'Total Qty :', 'TB', 0, 'C');
            PDF::Cell($w_detail[2], 6, $sum, 'TB', 0, 'R');
            PDF::Cell($w_detail[3] + $w_detail[4], 6, '', 'TB', 0, 'R');
    
            PDF::Ln(); 
            PDF::SetFont('dejavusans', '', 8, '', 'false');
            PDF::Cell(191, 7, 'Description :', '', 0, 'L');
            PDF::Ln();
            PDF::writeHTML($Description, true, false, true, false, ''); 
            PDF::Ln(); 
     
    
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
            PDF::Cell($w_detail[2], 5, $row[3], '', 0, 'R', 0);
            PDF::Cell($w_detail[3], 5, $row[4], '', 0, 'L', 0);
            PDF::Cell($w_detail[4], 5, $row[12], '', 0, 'R', 0); 
            PDF::Ln(4);
    
            PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 2, 'color' => array(128, 128, 128)));
            PDF::Cell($w_detail[0], 5, '', 'B', 0, 'L', 0);
            PDF::Cell($w_detail[1], 5, ($row[1] == $row[11] ? '' : $row[11]), 'B', 0, 'L', 0);
            PDF::Cell($w_detail[2], 5, (($row[4] == $row[9] && $row[8] == $row[3]) ? '' : $row[8]), 'B', 0, 'R', 0);
            PDF::Cell($w_detail[3], 5, (($row[4] == $row[9] && $row[8] == $row[3]) ? '' : $row[9]), 'B', 0, 'L', 0);
            PDF::Cell($w_detail[4], 5, '', 'B', 0, 'L', 0); 
            PDF::Ln(6); 
        }
    
            PDF::SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
            
            PDF::Cell($w_detail[0] + $w_detail[1], 6, 'Total Qty :', 'TB', 0, 'C');
            PDF::Cell($w_detail[2], 6, $sum, 'TB', 0, 'R');
            PDF::Cell($w_detail[3] + $w_detail[4], 6, '', 'TB', 0, 'R');
    
            PDF::Ln(); 
            PDF::SetFont('dejavusans', '', 8, '', 'false');
            PDF::Cell(191, 7, 'Description :', '', 0, 'L');
            PDF::Ln();
            PDF::writeHTML($Description, true, false, true, false, ''); 
            PDF::Ln(); 
    
     
            PDF::SetLineStyle(array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
            PDF::Cell(191, 3, '', 'T', 0, 'L');
    
            PDF::SetY(-58);  
            
            PDF::Ln(10); 
              
           $style = array(
               'border' => false,
               'vpadding' => 'auto',
               'hpadding' => 'auto',
               'fgcolor' => array(0,0,0),
               'bgcolor' => false, //array(255,255,255)
               'module_width' => 1, // width of a single module in points
               'module_height' => 1 // height of a single module in points
           ); 

           PDF::write2DBarcode($url, 'QRCODE,H', 158, 200, 45, 45, $style, 'N');
           PDF::Text(20, 25, 'T');
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
        $doc_status = 3 ;
        $vendor_num = $str[0] ;
        $pack_slip = $str[1] ;
        $print_option = 0 ;  

        $data = DB::connection('sqlsrv4')->table('Erp.RcvHead AS a')
        ->join('Erp.Vendor AS b', 'a.VendorNum', 'b.VendorNum')
        ->where('a.VendorNum', '=', $vendor_num)
        ->where('a.PackSlip', '=', "$pack_slip") 
        ->select('a.*', 'b.Name AS PartnerName', 'b.VendorID AS PartnerCode', 'b.Address1', 'b.Address2', 'b.City', 'b.State', 'b.Country')
        ->get();

     
    
     if($data->count() > 0){
     foreach($data as $db){ 
        $DocNum = $db->LegalNumber;
        $DocDate = AppModel::local_date_formate_name(substr($db->ReceiptDate,0,10));
        $PartnerName = $db->PartnerName ;
        $PartnerCode = $db->PartnerCode ; 
        $PartnerAddress = $db->Address1.' '.$db->Address2.' '.$db->City.' '.$db->State.' '.$db->Country ; 
        $ProjectName = '' ; 
        $PICName = '' ;
        $Telp = '' ;
        $Fax = '' ; 
        $DocType = '' ;
        $SubTotal = '' ;
        $Discount = '' ;
        $Ppn = '' ;
        $Total = '' ;
        $TOP = '' ;
        $Status = '';
        $Rev = '' ; 
        $Checked = '' ;
        $Approval = '' ;
        $Legalized = '' ; 
        $Currency = '' ;
        $judul = $db->LegalNumber ;     
        $Description = $db->ReceiptComment ; 
        $UserName = $db->EntryPerson ; 
        $SJNum = $db->PackSlip ;
        $SJDate = AppModel::local_date_formate_name(substr($db->ReceiptDate,0,10));
        $DNNum = '' ;
        $DNDate = '';
        $PONum = '' ;
        $Address = '' ;
        $Vendor = $db->PartnerName ;
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
        $SJNum = '' ;
        $SJDate = '' ;
        $DNNum = '' ;
        $DNDate = '' ;
        $PONum = '' ;
        $UserName = '' ;
        $Currency = '' ;
        $Vendor = '' ;
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
    
     $DocRegNum= '' ;
     $sum = number_format(GRPortal::sum_detail_doc($vendor_num, $pack_slip),0);
     
    
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
 
        $header = array('No.', 'Product Name', 'Qty', 'UoM', 'Price');   
        
        $port = env('WEB_DOMAIN') ;  
        $url = $port.'/'."approval_gr?ref_doc=".$request->trc_unix_id."&ref_tab=1&ref_form=".$request->ref_form ; 

 

     $this->draw_print_main_header($header, $DocNum, $DocDate, $PartnerName, $Address, $UserName, $PICName, $DocType, $SubTotal, $Discount, $Ppn, $Total, $TOP, $Status, $Rev, $Currency, $Description, $Vendor, $sum, $CompanyName, $Logo, $DocRegNum, $City, $Province, $PostalCode, $State, $Phone, $Fax, $ProjectName, $PartnerCode, $doc_status, $vendor_num, $pack_slip, $print_option, $AddressHtml, $PartnerAddressHtml, $SJNum, $SJDate, $PONum, $DNNum, $DNDate, $AddressCompany, $url); 
     
     PDF::Output($judul.'.pdf');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }
 

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\GRPortal  $gRPortal
     * @return \Illuminate\Http\Response
     */
    public function edit(GRPortal $gRPortal)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\GRPortal  $gRPortal
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, GRPortal $gRPortal)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\GRPortal  $gRPortal
     * @return \Illuminate\Http\Response
     */
    public function destroy(GRPortal $gRPortal)
    {
        //
    }
}
