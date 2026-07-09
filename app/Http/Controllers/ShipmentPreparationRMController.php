<?php

namespace App\Http\Controllers;

use App\Models\ShipmentPreparation;
use App\Models\ErrorLogs;
use App\Models\AppModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt ;
use Illuminate\Support\Facades\DB; 
use GuzzleHttp\Exception\RequestException; 
use GuzzleHttp\Client;  
use PDF;  
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Trim;

class ShipmentPreparationRMController extends Controller
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
        if (count($uri) < 5) {
            $menu = $this->menu($my_id, 'home') ;   
        } else {
            $menu = $this->menu($my_id, $uri[4]) ;  
        }   
        $data['head_title'] = $menu['head_title'];
        $data['menu_level_1'] = $menu['menu_level_1'] ;
        $data['menu_level_2'] = $menu['menu_level_2'] ;
        $data['menu_level_3'] = $menu['menu_level_3'] ;
        $data['menu_level_4'] = $menu['menu_level_4'] ;  
        return view('shipment_preparation_rm/shipment_preparation_rm_index', $data); 
    }

    public function get_attachment_list(Request $request){     
        $str_req = explode("_",$request->trc_unix_id); 
        $str = explode("_",Crypt::decryptString(str_replace("-","=", $str_req[0])));   
        $po_num = $str[0] ;  
        $data['list'] = ShipmentPreparation::get_attachment_list($po_num);  
        $data['count'] = ShipmentPreparation::get_attachment_list($po_num)->count();  
        return view('shipment_preparation_rm/attachment_list', $data) ;
    }

    public function get_comment_list(Request $request){     
        $str_req = explode("_",$request->trc_unix_id); 
        $str = explode("_",Crypt::decryptString(str_replace("-","=", $str_req[0])));   
        $po_num = $str[0] ;  
        $data['list'] = ShipmentPreparation::get_comment_list($po_num);  
        $data['count'] = ShipmentPreparation::get_comment_list($po_num)->count();  
        return view('shipment_preparation_rm/comment_list', $data) ;
    }

     

    public function get_count_document(Request $request){      
        $data['total_check'] = ShipmentPreparation::get_count_document_check();  
        $data['total_approve'] = ShipmentPreparation::get_count_document_approve();  
        $data['total_document'] = ShipmentPreparation::get_count_document();  
        echo json_encode($data); 
    }

    public function get_preview_doc(Request $request){  
        $str_req = explode("_",$request->trc_unix_id); 
        $str = explode("_",Crypt::decryptString(str_replace("-","=", $str_req[0])));  
        $sys_id = "'".$request->trc_unix_id."'" ;    
        $PackNum = $str[0] ;     
        $data_detail = ShipmentPreparation::data_detail($PackNum);  
        if($data_detail->count() > 0)  {
            foreach ($data_detail as $db) {
                $sys_id = Crypt::encryptString($db->PackNum.'_0') ;
                $sys_id = str_replace("=", "-", $sys_id).'_1' ;
                $data['trc_unix_id'] =  $sys_id ;   
                $data['PackNum'] = $db->PackNum ;
                $data['OrderNum'] = $db->OrderNum_c ;
                $data['LegalNumber'] = $db->LegalNumber ; 
                $data['CustNum'] = $db->CustNum ; 

                $data['ref_tab'] = 1 ;  
            }  
        } else {
            $data['PackNum'] = '' ;
            $data['LegalNumber'] = '' ;
            $data['CustNum'] = 0 ;
            $data['OrderNum'] = '' ;
            $data['trc_unix_id'] = '' ;
            $data['ref_tab'] = 0 ;  
        }
        
        echo json_encode($data); 
    }

    public function front_table(Request $request){     
        $status_id = $request->status_id ; 
        $section_id = $request->section_id ; 
        $search = $request->front_table_search ; 
        // dd($status_id);
        $columns = array(  
          0 =>'PackNum', 
          1 =>'PackNum', 
          2 =>'ShipDate', 
          3 =>'ReadyToPrint_c' 
        );  
        
        $totalData = ShipmentPreparation::get_transaction_list($search, $status_id)->count();    
        $totalFiltered = $totalData;  
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = ($request->input('order.0.column')==0 ? $columns[1] : $columns[$request->input('order.0.column')]);
        $dir = ($request->input('order.0.column')==0 ? 'desc' : $request->input('order.0.dir')) ; 
        if(empty($search))
        {            
            $posts = ShipmentPreparation::get_transaction_list($search, $status_id)
            ->offset($start)
            ->limit($limit)
            ->orderBy($order,$dir)
            ->get();
        } else { 
            $posts =  ShipmentPreparation::get_transaction_list($search, $status_id) 
            ->offset($start)
            ->limit($limit) 
            ->orderBy($order,$dir)
            ->get();  
            $totalFiltered = ShipmentPreparation::get_transaction_list($search, $status_id)->count();
        } 
        $data = array();
        if(!empty($posts))
        { 
        $no = $start ;  
        foreach ($posts as $post)
        { 
        $no++; 
        $trc_id = Crypt::encryptString($post->PackNum) ;   
        $sys_id = "'".str_replace("=","-", $trc_id).'_'.$no."'" ;    
        $button = '
        <span class="svg-icon svg-icon-primary svg-icon-2x" style="cursor: pointer;" id="'.$trc_id.'"  onclick="document_preview('.$sys_id.') ;">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="black"></path><path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="black"></path><path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="black"></path></svg>
        </span>
        <span class="svg-icon svg-icon-danger svg-icon-2x" style="cursor: pointer;" id="'.$trc_id.'"  onclick="delete_document_confirm('.$sys_id.', '.$post->PackNum.') ;">
             <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">  
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24"/>
                        <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"/>
                        <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"/>
                    </g>
                </svg>
        </span>' ; 

        $my_username = Auth::user()->username ;     
        $nestedData['no'] = $no ;  
        $nestedData['PackNum'] = $post->PackNum ;     
        $nestedData['ShipDate'] = AppModel::local_date_formate_name(substr($post->ShipDate,0,10)) ;    
        $nestedData['ReadyToPrint_c'] = ($post->ReadyToPrint_c == 1 ? 'Done' : 'Draft') ;   
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

    public function detail_table(Request $request){   
        if ($request->trc_unix_id != '') {
            $str_req = explode("_",$request->trc_unix_id); 
            $str = explode("_",Crypt::decryptString(str_replace("-","=", $str_req[0])));   
            $PackNum = $str[0] ;   
        } else {
            $PackNum = 0 ;  
        } 
        $search = $request->detail_table_search ;  
        $columns = array(  
          0 =>'PackLine', 
          1 =>'PackLine', 
          2 =>'PartNum', 
          3 =>'OurInventoryShipQty', 
          4 =>'LotNum'
        );  
            
            $totalData = ShipmentPreparation::get_detail_transaction_list($search, $PackNum)->count();    
            // dd($totalData);
            $totalFiltered = $totalData;  
            $limit = $request->input('length');
            $start = $request->input('start');
            $order = ($request->input('order.0.column')==0 ? $columns[1] : $columns[$request->input('order.0.column')]);
            $dir = ($request->input('order.0.column')==0 ? 'desc' : $request->input('order.0.dir')) ; 
        if(empty($search))
        {            
            $posts = ShipmentPreparation::get_detail_transaction_list($search, $PackNum)
            ->offset($start)
            ->limit($limit)
            ->orderBy($order,$dir)
            ->get();
        } else { 
            $posts =  ShipmentPreparation::get_detail_transaction_list($search, $PackNum) 
            ->offset($start)
            ->limit($limit)             
            ->orderBy($order,$dir)
            ->get();  
            $totalFiltered = ShipmentPreparation::get_detail_transaction_list($search, $PackNum)->count();
        } 
        $data = array();
        if(!empty($posts))
        { 
            $no = $start ;  
            foreach ($posts as $post)
        { 
            $no++; 
            $trc_id = Crypt::encryptString($post->PackNum."_".$post->PackLine) ;   
            $sys_id = "'".str_replace("=","-", $trc_id).'_'.$no."'" ;    
            $button = '
            <span class="svg-icon svg-icon-primary svg-icon-2x" style="cursor: pointer;" id="'.$trc_id.'"  onclick="refresh_detail_release_table('.$post->PackLine.') ;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="black"></path><path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="black"></path><path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="black"></path></svg>
            </span>' ; 

            $nestedData['no'] = $no ;  
            $nestedData['PartNum'] = $post->PartNum ;      
            $nestedData['Qty'] = number_format($post->OurInventoryShipQty, 0) ;      
            $nestedData['LotNum'] = $post->LotNum ;      
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

    public function detail_release_table(Request $request){   
        if ($request->trc_unix_id != '') {
            $str_req = explode("_",$request->trc_unix_id); 
            $str = explode("_",Crypt::decryptString(str_replace("-","=", $str_req[0])));   
            $PackNum = $str[0] ;   
        } else {
            $PackNum = 0 ;   
        } 

        $PackLine = $request->pack_line ;   
        $search = $request->detail_table_search ;  
        $columns = array(  
          0 =>'SysID', 
          1 =>'PackLine', 
          2 =>'PartNum', 
          3 =>'Qty', 
          4 =>'LotNum'
        );  
        
        $totalData = ShipmentPreparation::get_detail_release_transaction_list($search, $PackNum, $PackLine)->count();    
        // dd($totalData);
        $totalFiltered = $totalData;  
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = ($request->input('order.0.column')==0 ? $columns[1] : $columns[$request->input('order.0.column')]);
        $dir = ($request->input('order.0.column')==0 ? 'desc' : $request->input('order.0.dir')) ; 
        if(empty($search))
        {            
            $posts = ShipmentPreparation::get_detail_release_transaction_list($search, $PackNum, $PackLine)
            ->offset($start)
            ->limit($limit)
            ->orderBy($order,$dir)
            ->get();
        } else { 
            $posts =  ShipmentPreparation::get_detail_release_transaction_list($search, $PackNum, $PackLine) 
            ->offset($start)
            ->limit($limit) 
            ->orderBy($order,$dir)
            ->get();  
            $totalFiltered = ShipmentPreparation::get_detail_release_transaction_list($search, $PackNum, $PackLine)->count();
        } 
        $data = array();
        if(!empty($posts))
        { 
        $no = $start ;  
        foreach ($posts as $post)
        { 
        $no++; 
        $trc_id = Crypt::encryptString($post->PackNum."_".$post->PackLine) ;   
        $sys_id = "'".str_replace("=","-", $trc_id).'_'.$no."'" ;    
        $button = '
        <span class="svg-icon svg-icon-danger svg-icon-2x" style="cursor: pointer;" id="'.$trc_id.'"  onclick="delete_lot_confirm('.$post->PackNum.', '.$post->PackLine.', '.$post->SysID.') ;">
             <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">  
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" he ight="24"/>
                        <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"/>
                        <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"/>
                    </g>
                </svg>
        </span>' ; 
        $parts = "'".$post->PartNum."'
        " ;
        // $button .= '
        // <span class="svg-icon svg-icon-PRIMARY svg-icon-2x" style="cursor: pointer;" id="'.$trc_id.'"  onclick="delivery_confirm('.$post->PackNum.', '.$post->PackLine.', '.$post->SysID.','.$parts.') ;">
        //      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="black"></path><path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="black"></path><path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="black"></path></svg>
        // </span>
        // ';
        $nestedData['no'] = $no ;  
        $nestedData['PartNum'] = $post->PartNum ;      
        $nestedData['Qty'] = $post->Qty ;      
        $nestedData['LotNum'] = $post->LotNum ;      
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
        $vendor_id = Auth::user()->partner_id ; 
        $search = $request->front_table_search ;   
        date_default_timezone_set('Asia/Jakarta'); 
        $yearX = substr(date('Y'),2,2).date('m') ;
        $year = ($request->range_date === null ? $yearX : $request->range_date) ;    
        $data['full_name'] = Auth::user()->full_name ;
        $data['list'] = ShipmentPreparation::get_transaction_list($year, $search, $vendor_id)->get(); 
        $data['num'] = ShipmentPreparation::get_transaction_list($year, $search, $vendor_id)->count(); 
        $data['ref_form'] = '' ; 
        return view('shipment_preparation_rm.po_export', $data); 
    }

    public function show(Request $request) {         
        $str_req = explode("_",$request->trc_unix_id); 
        $str = explode("_",Crypt::decryptString(str_replace("-","=", $str_req[0])));  
        $sys_id = "'".$request->trc_unix_id."'" ;    
        $PONum = $str[0] ;  
        $data_detail = ShipmentPreparation::data_detail($PONum);    
        foreach ($data_detail as $db) {
            $sys_id = Crypt::encryptString($db->PONum.'_0') ;
            $sys_id = str_replace("=", "-", $sys_id).'_1' ;
            $data['trc_unix_id'] =  $sys_id ;   
            $data['ref_form'] = '' ;
            $data['ref_tab'] = 1 ;  
        }  
        return view('shipment_preparation_rm.po_preview', $data);  
    }

    function print_view(Request $request){    
        $data['trc_unix_id'] = $request->trc_unix_id ;     
        $data['ref_form'] = $request->ref_form ; 
        return view('shipment_preparation_rm.po_direct_print', $data);
    }  
 
    public function check_before_delete(Request $request)
    {  
        $packNum = $request->packNum ;
        $packLine = $request->packLine ;
        $sysID = $request->sysID ;
        $qty_pack_line = ShipmentPreparation::get_qty_before($packNum, $packLine) ;
        $qty_pack_line_lot = ShipmentPreparation::get_qty_lot($sysID) ;
        if ($qty_pack_line <= $qty_pack_line_lot) {
            $data = self::delete_lot($packNum, $packLine);  
        } else {
            $ds_prop = ShipmentPreparation::get_prop_lot($sysID);
            if ($ds_prop->count() > 0) {
                foreach ($ds_prop AS $row) {
                    $orderNum = $row->OrderNum ;
                    $orderLine = $row->OrderLine ;
                    $orderRel = $row->OrderRelNum ;
                    $partNum = $row->PartNum ;
                    $lotNum = $row->LotNum ;
                    $lineDesc = str_replace(",", "__", $row->LineDesc) ;
                    $displayInvQty = $row->Qty ;
                    $poNum = $row->PoNum ; 
                } 
                $data = self::update_detail($packNum, $orderNum, $orderLine, $orderRel, $partNum, $lotNum, str_replace("__", ",",  $lineDesc), ($qty_pack_line - $displayInvQty), $poNum, $packLine);
            } else {
                $data['code'] = 500 ;
                $data['desc'] = 'Please try again!' ;
            }
        }
        if ($data['code'] == 200) {
            $delete_record = ShipmentPreparation::delete_record_lot($sysID);
        }
        return $data ;
    }

    public function ready_to_print(Request $request)
    { 
        $str_req = explode("_",$request->trc_unix_id); 
        $str = explode("_",Crypt::decryptString(str_replace("-","=", $str_req[0])));   
        $packNum = $str[0] ;  
        $username = Auth::user()->username ;
        $password = Crypt::decryptString(Auth::user()->epicor_password) ;
        $client = new Client();
        $data = [];
        $host_api = self::get_host_api() ;
        try { 
            $response = $client->request('POST', $host_api.'Shipment/ReadyToPrint', [
                'json' => ['nik' => "$username", 'password' => "$password", 'packNum' => $packNum, 'readyToPrint_c' => true],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'verify' => false, // gunakan ini jika Anda ingin melewati verifikasi SSL
            ]);
 
            $responseBody = json_decode($response->getBody()->getContents(), true); 
            $data['code'] = $responseBody['code'];
            $data['desc'] = $responseBody['desc'];   

        } catch (RequestException $e) {
            // Log error details
            \Log::error('API request failed', [
                'message' => $e->getMessage(),
                'request' => $e->getRequest(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null,
            ]);

            $data['code'] = 500;
            $data['desc'] = $e->getMessage();   
        }
        return $data ;
    }

    public function un_ready_to_print(Request $request)
    { 
        $str_req = explode("_",$request->trc_unix_id); 
        $str = explode("_",Crypt::decryptString(str_replace("-","=", $str_req[0])));   
        $packNum = $str[0] ;  
        $username = Auth::user()->username ;
        $password = Crypt::decryptString(Auth::user()->epicor_password) ;
        $client = new Client();
        $data = [];
        $host_api = self::get_host_api() ;
        try { 
            $response = $client->request('POST', $host_api.'Shipment/ReadyToPrint', [
                'json' => ['nik' => "$username", 'password' => "$password", 'packNum' => $packNum, 'readyToPrint_c' => false],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'verify' => false, // gunakan ini jika Anda ingin melewati verifikasi SSL
            ]);
 
            $responseBody = json_decode($response->getBody()->getContents(), true); 
            $data['code'] = $responseBody['code'];
            $data['desc'] = $responseBody['desc'];   

        } catch (RequestException $e) {
            // Log error details
            \Log::error('API request failed', [
                'message' => $e->getMessage(),
                'request' => $e->getRequest(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null,
            ]);

            $data['code'] = 500;
            $data['desc'] = $e->getMessage();   
        }
        return $data ;
    }

    public function delete_lot($packNum, $packLine)
    { 
        $username = Auth::user()->username ;
        $password = Crypt::decryptString(Auth::user()->epicor_password) ;
        $client = new Client();
        $data = [];
        $host_api = self::get_host_api() ;
        try { 
            $response = $client->request('DELETE', $host_api.'Shipment/DeleteLine', [
                'json' => ['nik' => "$username", 'password' => "$password", 'packNum' => "$packNum", 'packLine' => "$packLine"],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'verify' => false, // gunakan ini jika Anda ingin melewati verifikasi SSL
            ]);
 
            $responseBody = json_decode($response->getBody()->getContents(), true); 
            $data['code'] = $responseBody['code'];
            $data['desc'] = $responseBody['desc'];   

        } catch (RequestException $e) {
            // Log error details
            \Log::error('API request failed', [
                'message' => $e->getMessage(),
                'request' => $e->getRequest(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null,
            ]);

            $data['code'] = 500;
            $data['desc'] = $e->getMessage();   
        }
        return $data ;
    }

    public function delete_document(Request $request)
    { 
        $username = Auth::user()->username ; 
        $password = Crypt::decryptString(Auth::user()->epicor_password) ;
        if ($request->trc_unix_id != '') {
            $str_req = explode("_",$request->trc_unix_id); 
            $str = explode("_",Crypt::decryptString(str_replace("-","=", $str_req[0])));   
            $PackNum = $str[0] ;   
        } else {
            $PackNum = 0 ;  
        } 
        $client = new Client();
        $data = [];
        $host_api = self::get_host_api() ;
        try { 
            $response = $client->request('DELETE', $host_api.'Shipment/DeleteHead', [
                'json' => ['nik' => "$username", 'password' => "$password", 'packNum' => $PackNum],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'verify' => false, // gunakan ini jika Anda ingin melewati verifikasi SSL
            ]);
 
            $responseBody = json_decode($response->getBody()->getContents(), true); 
            $data['code'] = $responseBody['code'];
            $data['desc'] = $responseBody['desc'];    

        } catch (RequestException $e) {
            // Log error details
            \Log::error('API request failed', [
                'message' => $e->getMessage(),
                'request' => $e->getRequest(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null,
            ]);

            $data['code'] = 500;
            $data['desc'] = $e->getMessage();   
        }
        if ($data['code'] == 200) {
            $clear_record_lot = ShipmentPreparation::destroy_record_lot($PackNum);
        }
        return $data ;
    }

    public function add_document(Request $request)
    { 
        $username = Auth::user()->username ;
        $password = Crypt::decryptString(Auth::user()->epicor_password) ;
        $client = new Client();
        $data = [];
        $host_api = self::get_host_api() ;
        try { 
            $response = $client->request('POST', $host_api.'Shipment/GetNew', [
                'json' => ['nik' => "$username", 'password' => "$password"],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'verify' => false, // gunakan ini jika Anda ingin melewati verifikasi SSL
            ]);
 
            $responseBody = json_decode($response->getBody()->getContents(), true); 
            $data['code'] = $responseBody['code'];
            $data['desc'] = $responseBody['desc']; 
            $sys_id = Crypt::encryptString($responseBody['packNum'].'_0') ;
            $sys_id = str_replace("=", "-", $sys_id).'_1' ;
            $data['trc_unix_id'] =  $sys_id ;   

        } catch (RequestException $e) {
            // Log error details
            \Log::error('API request failed', [
                'message' => $e->getMessage(),
                'request' => $e->getRequest(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null,
            ]);

            $data['code'] = 500;
            $data['desc'] = $e->getMessage();  
            $data['trc_unix_id'] = '' ;   
        }
        return $data ;
    }

    public function set_order_number(Request $request)
    {
        $username = Auth::user()->username ;
        $password = Crypt::decryptString(Auth::user()->epicor_password) ;
        $str_req = explode("_",$request->trc_unix_id); 
        $str = explode("_",Crypt::decryptString(str_replace("-","=", $str_req[0])));     
        $packNum = $str[0] ;   
        $orderNum = $request->input('OrderNum') ; 
        $data = [];
        $host_api = self::get_host_api() ;
        $check_so = ShipmentPreparation::check_so_number($orderNum, $packNum)  ;
        if ($check_so > 0) { 
            $client = new Client(); 
            try {
                $response = $client->request('POST', $host_api.'Shipment/SetOrderNum', [
                    'json' => [
                        'packNum' => $packNum, 
                        'orderNum' => $orderNum, 
                        'nik' => "$username",
                        'password' => "$password"
                    ],
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                    'verify' => false, 
                ]);

                $custNum  = ShipmentPreparation::get_cust_num($orderNum) ;
                $responseBody = json_decode($response->getBody()->getContents(), true); 
                $data['code'] = $responseBody['code'];
                $data['desc'] = $responseBody['desc'];  
                $data['orderNum'] = $request->input('OrderNum') ;  
                $data['custNum'] = $custNum ;


            } catch (RequestException $e) {
                // Log error details
                \Log::error('API request failed', [
                    'message' => $e->getMessage(),
                    'request' => $e->getRequest(),
                    'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null,
                ]);

                $data['code'] = 500;
                $data['desc'] = $e->getMessage();   
                $data['orderNum'] = 0 ;  

            } 
        } else {
            $data['code'] = 500;
            $data['desc'] = ' SO Tidak sesuai!';   
            $data['orderNum'] = 0 ;  
        }
        return $data ;
    }

    public function submit_label(Request $request)
    {    
        $int_label = explode("~",$request->int_label) ;
        $ext_label = $request->ext_label ;
        $OrderNum = $request->OrderNum ;  
        $CustNum = $request->CustNum ;
        $PartNum = $int_label[0] ;
        if ($int_label[6] != "1") {
            $LotNum = $int_label[4] ;
        } else {
            $LotNum = $int_label[5] ;
        }
        if($CustNum == 3){
        $db_order = ShipmentPreparation::get_order_line_MMKI($OrderNum, $PartNum,$ext_label) ;  
        }else{
        $db_order = ShipmentPreparation::get_order_line($OrderNum, $PartNum) ;  
        }
        if ($db_order->count() > 1) {
            $data['process_status'] = 1 ; 
            $data['ready_to_post'] = 0 ; 
            $data['msg_process'] = 'Update berhasil !' ; 
        } else {  
            if ($db_order->count() == 1) {
                foreach ($db_order AS $row) {
                    $data['order_line'] = $row->OrderLine ;
                    $data['order_rel'] = $row->OrderRelNum ;
                    $data['part_num'] = $row->PartNum ;
                    $data['po_num'] = $row->PONum ;
                    $data['lot_num'] = $LotNum ;
                    $data['warehouseCode'] = $row->WarehouseCode ;
                    $data['WarehouseDesc'] = $row->WarehouseDesc ;
                    $data['part_name'] = str_replace(",", "__", $row->LineDesc) ;
                } 
                $data['code'] = 200 ;
                $data['process_status'] = 1 ; 
                $data['ready_to_post'] = 1 ; 
                $data['msg_process'] = 'Update berhasil !' ;  
            } else {
                    $data['lot_num'] = '' ; 
                    $data['order_line'] = '' ; 
                $data['order_rel'] = '' ; 
                $data['part_num'] = '' ; 
                $data['part_name'] = '' ; 
                $data['po_num'] = '' ; 
                $data['process_status'] = 0 ;  
                $data['ready_to_post'] = 0 ;
                
                    $data['code'] = 500 ;
                    $data['msg_process'] = 'Item tidak ditemuakan pada SO !' ;
               
            }  
        } 
        return json_encode($data);
    }

    public function submit_label_by_slip_no(Request $request)
    {    
        $int_label = explode("~",$request->int_label) ;
        $ext_label = $request->ext_label ;
        $OrderNum = $request->OrderNum ;  
        $str_req = explode("_",$request->trc_unix_id); 
        $str = explode("_",Crypt::decryptString(str_replace("-","=", $str_req[0])));     
        $packNum = $str[0] ;  
        $PartNum = $int_label[0] ;
        $CustNum=   $request->CustNum ;
        // dd(substr($int_label[5],0,3));
        if ($int_label[7] != "-") {
            $LotNum = $int_label[5] ;
        
        } else {
            $LotNum = $int_label[4] ;
        }
        // dd($CustNum);
        if($CustNum == 3){
            $db_order = ShipmentPreparation::get_order_line_MMKI($OrderNum, $PartNum,$ext_label) ;  
            if(strpos($PartNum,"_") === false){
                $saiexp = str_replace(".", "", str_replace(" ", "", $PartNum));
            }else if(strpos($PartNum,".") === false){
                $saiexp = str_replace("_", "", str_replace(" ", "", $PartNum));
            }
            if (strpos($PartNum, "-") !== false) {
                $saiexp = explode('-', $PartNum)[0];
            }
            if ($saiexp != $ext_label){
                $error_from = "SHP";
                $error_type = "LABEL";
                $description = "Label part customer ".$ext_label." tidak sesuai dengan label part SAI ". $PartNum ;
                
                $error_update = ErrorLogs::logError($error_from, $error_type, $description,0,$OrderNum,$PartNum);
                if($CustNum == 3) {
                    $data['code'] = 405 ;
                    $data['msg_process'] = 'Part Number tidak cocok' ;
                    $data['process_status'] = 0 ;
                } 
                return json_encode($data);
            }
        }else{
        $db_order = ShipmentPreparation::get_order_line_by_slip_no($OrderNum, $PartNum, $ext_label) ; 
        }
        
        if ($db_order->count() == 1 ) { 
                foreach ($db_order AS $row) {
                    $data['order_line'] = $row->OrderLine ;
                    $data['order_rel'] = $row->OrderRelNum ;
                    $data['part_num'] = $row->PartNum ;
                    $data['po_num'] = $row->PONum ;
                    $data['lot_num'] = $LotNum ;
                    $data['part_name'] = str_replace(",", "__", $row->LineDesc) ;
                } 
                $data['process_status'] = 1 ; 
                $data['ready_to_post'] = 1 ;
                $data['msg_process'] = 'Update berhasil !' ;  
            } else if ($db_order->count() > 1) { 
                $data['lot_num'] = '' ; 
                $data['order_line'] = '' ; 
                $data['order_rel'] = '' ; 
                $data['part_num'] = '' ; 
                $data['part_name'] = '' ; 
                $data['po_num'] = '' ; 
                $data['process_status'] = 0 ;  
                $data['ready_to_post'] = 0 ;
                $data['msg_process'] = 'DemandReference pada SO ditemukan double !' ; 
            } else {
                $data['lot_num'] = '' ; 
                $data['order_line'] = '' ; 
                $data['order_rel'] = '' ; 
                $data['part_num'] = '' ; 
                $data['part_name'] = '' ; 
                $data['po_num'] = '' ; 
                $data['process_status'] = 0 ;  
                $data['ready_to_post'] = 0 ;
                $data['msg_process'] = 'Item tidak ditemuakan pada SO !' ;
            }   
        return json_encode($data);
    }
   
    public function post_detail(Request $request)
    { 
        $str_req = explode("_",$request->trc_unix_id);
        $str = explode("_",Crypt::decryptString(str_replace("-","=", $str_req[0])));
        $packNum = $str[0] ;
        $orderNum = $request->input('orderNum') ;
        $orderLine = $request->input('orderLine') ;
        $orderRel = $request->input('orderRel') ;
        $partNum = $request->input('partNum') ;
        $ext_label = $request->input('ext_label') ;
        $lotNum = $request->lot_num ;
        $lineDesc = $request->input('lineDesc') ;
        $displayInvQty = $request->input('displayInvQty') ;
        $poNum = $request->input('poNum') ;
        $warehouseCode = $request->input('warehouseCode') ;
        $warehouseDesc = $request->input('warehouseDesc') ;
        
        $pack_line = ShipmentPreparation::get_pack_line($packNum, $orderNum, $orderLine, $orderRel, $lotNum);
        if ($pack_line > 0) {
          $qty_before = ShipmentPreparation::get_qty_before($packNum, $pack_line);
          $data = self::update_detail($packNum, $orderNum, $orderLine, $orderRel, $partNum, $lotNum, $lineDesc, ($displayInvQty + $qty_before), $poNum, $pack_line) ;
          if ($data['code'] == 200) {
            $insert_record = ShipmentPreparation::insert_record_pallet_with_pack_line($packNum, $orderNum, $orderLine, $orderRel, $partNum, $lotNum, $lineDesc, $displayInvQty, $poNum, $pack_line, $ext_label);
          }
        } else {
          $data = self::add_detail($packNum, $orderNum, $orderLine, $orderRel, $partNum, $lotNum, $lineDesc, $displayInvQty, $poNum,$warehouseCode,$warehouseDesc) ;
          if ($data['code'] == 200) {
            $insert_record = ShipmentPreparation::insert_record_pallet($packNum, $orderNum, $orderLine, $orderRel, $partNum, $lotNum, $lineDesc, $displayInvQty, $poNum, $ext_label);
          }
        }   
        return $data ;
    }

    public function update_detail($packNum, $orderNum, $orderLine, $orderRel, $partNum, $lotNum, $lineDesc, $displayInvQty, $poNum, $pack_line)
    {
        $username = Auth::user()->username ;
        $password = Crypt::decryptString(Auth::user()->epicor_password) ;  
        $lineDesc = str_replace("__", ",", $lineDesc) ;  
        $client = new Client(); 
        $data = [] ;
        $host_api = self::get_host_api();
        // dd([
        //     'packNum' => $packNum, 
        //     'packLine' => $pack_line, 
        //     'orderNum' => $orderNum, 
        //     'orderLine' => $orderLine, 
        //     'orderRel' => $orderRel, 
        //     'partNum' => $partNum, 
        //     'lineDesc' => $lineDesc, 
        //     'lotNum' => "$lotNum", 
        //     'displayInvQty' => number_format($displayInvQty,0), 
        //     'poNum' => $poNum,  
        //     'nik' => "$username",
        //     'password' => "$password"
        // ]);
        try {
            $response = $client->request('POST', $host_api.'Shipment/UpdatePackLine', [
                'json' => [
                    'packNum' => $packNum, 
                    'packLine' => $pack_line, 
                    'orderNum' => $orderNum, 
                    'orderLine' => $orderLine, 
                    'orderRel' => $orderRel, 
                    'partNum' => $partNum, 
                    'lineDesc' => $lineDesc, 
                    'lotNum' => "$lotNum", 
                    'displayInvQty' => $displayInvQty, 
                    // 'displayInvQty' => number_format($displayInvQty,0), 
                    'poNum' => $poNum,  
                    'nik' => "$username",
                    'password' => "$password"
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'verify' => false, 
            ]);
    
            $responseBody = json_decode($response->getBody()->getContents(), true); 
            $data['code'] = $responseBody['code'];
            $data['desc'] = $responseBody['desc'];     
        } catch (RequestException $e) {  
            \Log::error('API request failed', [
                'message' => $e->getMessage(),
                'request' => $e->getRequest(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null,
            ]);
            $data['code'] = 500;
            $data['desc'] = $e->getMessage();  
        }
        return $data ; 
    }

    public function add_detail($packNum, $orderNum, $orderLine, $orderRel, $partNum, $lotNum, $lineDesc, $displayInvQty, $poNum,$warehouseCode,$warehouseDesc)
    {
        $username = Auth::user()->username ;
        $password = Crypt::decryptString(Auth::user()->epicor_password) ;  
        $lineDesc = str_replace("__", ",", $lineDesc) ;  
        $client = new Client(); 
        $data = [] ;
        $host_api = self::get_host_api();
        try {
            $response = $client->request('POST', $host_api.'Shipment/AddPackLine', [
                'json' => [
                   'packNum' => $packNum, 
                    'orderNum' => $orderNum, 
                    'orderLine' => $orderLine, 
                    'orderRel' => $orderRel, 
                    'partNum' => $partNum, 
                    'lineDesc' => $lineDesc, 
                    'lotNum' => $lotNum, 
                    'warehouseCode' => "$warehouseCode", 
                    'warehouseDesc' => "$warehouseDesc", 
                    // 'displayInvQty' => number_format($displayInvQty,0), 
                    'displayInvQty' => $displayInvQty, 
                    'poNum' => $poNum,  
                    'nik' => "$username",
                    'password' => "$password"
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'verify' => false, 
            ]);
 
            $responseBody = json_decode($response->getBody()->getContents(), true); 
            $data['code'] = $responseBody['code'];
            $data['desc'] = $responseBody['desc'];    

        } catch (RequestException $e) {
            // Log error details
            \Log::error('API request failed', [
                'message' => $e->getMessage(),
                'request' => $e->getRequest(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null,
            ]);
            $data['code'] = 500;
            $data['desc'] = $e->getMessage();  
        }
        return $data ; 
    }
      
}
