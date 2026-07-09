<?php

namespace App\Http\Controllers;

use App\Models\POApproval;
use App\Models\AppModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt ;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Exception\RequestException; 
use GuzzleHttp\Client;  
use App\Jobs\SendApprovalEmailJob;
use PDF;  

class POApprovalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function sendApprovalNotification($recipient, $name, $document_id, $approval_url, $cc, $approve_msg)
    {  
        // $recipient = 'aji.sanjaya@summitadyawinsa.co.id';
        // $name = 'Herno Aji Sanjaya';
        // $document_id = '12345';
        // $approve_msg = '12345';
        // $cc = ['aji.sanjaya@summitadyawinsa.co.id'] ;
        // $approval_url = 'eyJpdiI6ImFLNk5NQ0ZQVUJsUzFJT0tmMFZxQXc9PSIsInZhbHVlIjoieklSVithYVlPYlJGNnRabjVVUlpMZz09IiwibWFjIjoiZWI2MDE0MWM3ODQ0YWE5MzFmYWMwNWM2YmI4YzE2ZTM5MDgwMzFhM2FkNWVmYTRkNzkwN2U5MWMyMDA0ZDNkMyIsInRhZyI6IiJ9_1' ;
        $approval_url = env('APP_URL')."/po_approval?ref_doc=".$approval_url ;   
        $document_id = "PO Num ".$document_id ;     
        SendApprovalEmailJob::dispatch($recipient, $name, $document_id, $approval_url, $cc, $approve_msg); 
        return response()->json(['message' => 'Email queued for sending.']);
    }

    public function index()
    {
        $my_id = Auth::user()->id ; 
        $uri = explode("/", url()->current());  
        $SEGMENT_NUM = env('SEGMENT_NUM');  
        if (count($uri) <= $SEGMENT_NUM) {
            $menu = $this->menu($my_id, 'home') ;   
        } else {
            $menu = $this->menu($my_id, $uri[$SEGMENT_NUM]) ;  
        }  
        $data['head_title'] = $menu['head_title'];
        $data['menu_level_1'] = $menu['menu_level_1'] ;
        $data['menu_level_2'] = $menu['menu_level_2'] ;
        $data['menu_level_3'] = $menu['menu_level_3'] ;
        $data['menu_level_4'] = $menu['menu_level_4'] ;  
        return view('po_approval/po_approval_index', $data); 
    }

    public function get_attachment_list(Request $request){     
        $str_req = explode("_",$request->trc_unix_id); 
        $str = explode("_",Crypt::decryptString(str_replace("-","=", $str_req[0])));   
        $po_num = $str[0] ;  
        $data['list'] = POApproval::get_attachment_list($po_num);  
        $data['count'] = POApproval::get_attachment_list($po_num)->count();  
        return view('po_approval/attachment_list', $data) ;
    }

    public function get_comment_list(Request $request){     
        $str_req = explode("_",$request->trc_unix_id); 
        $str = explode("_",Crypt::decryptString(str_replace("-","=", $str_req[0])));   
        $po_num = $str[0] ;  
        $data['list'] = POApproval::get_comment_list($po_num);  
        $data['count'] = POApproval::get_comment_list($po_num)->count();  
        return view('po_approval/comment_list', $data) ;
    }

    public function sent_comment(Request $request){     
        $str_req = explode("_",$request->trc_unix_id); 
        $str = explode("_",Crypt::decryptString(str_replace("-","=", $str_req[0])));   
        $po_num = $str[0] ;  
        $comment = $request->comment ;  
        $my_username = Auth::user()->username ;  
        $my_fullname = Auth::user()->full_name ; 
        $insert_db = POApproval::sent_comment($po_num, $my_username, $my_fullname, $comment); 
        if ($insert_db) {
            $data['status'] = 1 ;
            $data['desc'] = 'Success sent comment' ;
        } else {
            $data['status'] = 0 ;
            $data['desc'] = 'Fail sent comment' ;
        } 
        echo json_encode($data); 
    }

    public function get_count_document(Request $request){    
        $section_id = $request->section_id ;   
        // dd($section_id);
        $data['total_check'] = POApproval::get_count_document_check($section_id);  
        dd($data['total_check']);
        $data['total_approve'] = POApproval::get_count_document_approve($section_id); 
        $data['total_legal'] = POApproval::get_count_document_legal($section_id);  
        $data['total_document'] = POApproval::get_count_document($section_id);  
        echo json_encode($data); 
    }

    public function front_table(Request $request){     
        $status_id = $request->status_id ; 
        $section_id = $request->section_id ; 
        $search = $request->front_table_search ; 
        // dd($status_id);
        $columns = array(  
          0 =>'po_num', 
          1 =>'po_num', 
          2 =>'orderdate',
          2 =>'amount',
          3 =>'status_checker',
          4 =>'status_approver',    
          5 =>'status_legalizer',    
        );  
         
        $totalData = POApproval::get_transaction_list($search, $status_id, $section_id)->count();     
        $totalFiltered = $totalData;  
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = ($request->input('order.0.column')==0 ? $columns[1] : $columns[$request->input('order.0.column')]);
        $dir = ($request->input('order.0.column')==0 ? 'desc' : $request->input('order.0.dir')) ; 
        if(empty($search))
        {            
        $posts = POApproval::get_transaction_list($search, $status_id, $section_id)
        ->offset($start)
        ->limit($limit)
        ->orderBy($order,$dir)
        ->get();
        } else { 
        $posts =  POApproval::get_transaction_list($search, $status_id, $section_id) 
        ->offset($start)
        ->limit($limit) 
        ->orderBy($order,$dir)
        ->get();  
        $totalFiltered = POApproval::get_transaction_list($search, $status_id, $section_id)->count();
        } 
        $data = array();
        if(!empty($posts))
        { 
        $no = $start ; 
        
        foreach ($posts as $post)
        { 
        $no++; 
        $trc_id = Crypt::encryptString($post->po_num) ;   
        $sys_id = "'".str_replace("=","-", $trc_id).'_'.$no."'" ;   
       
        $button = '
        <span class="svg-icon svg-icon-primary svg-icon-2x" style="cursor: pointer;" id="'.$trc_id.'"  onclick="document_preview('.$sys_id.') ;">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="black"></path><path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="black"></path><path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="black"></path></svg>
        </span>' ; 

        $my_username = Auth::user()->username ;   
        $check_buyer = POApproval::check_access_buyer($post->po_num, $my_username) ; 

        if ($post->user_id2 == Auth::user()->username && ($post->status_checker == 'Pending')) {
            $docnum = '<span class="bg-light-warning">'.$post->po_num.' '.$post->buyer_id.'</span>' ;
        } else if ($post->user_id3 == Auth::user()->username && ($post->status_approver == 'Pending') && $post->status_checker == 'APPROVED') {
            $docnum = '<span class="bg-light-warning">'.$post->po_num.' '.$post->buyer_id.'</span>' ;
        } else if ($post->user_id4 == Auth::user()->username && ($post->status_legalizer == 'Pending') && $post->status_checker == 'APPROVED' && $post->status_approver == 'APPROVED') {
            $docnum = '<span class="bg-light-warning">'.$post->po_num.' '.$post->buyer_id.'</span>' ;
        } else if ($check_buyer > 0) {
            $docnum = '<span class="bg-light-warning">'.$post->po_num.' '.$post->buyer_id.'</span>' ;
        } else {
            $docnum = '<span class="">'.$post->po_num.' '.$post->buyer_id.'</span>' ;
        }

        if ($post->buyer_id != 'BOBAL') {
            if ($post->status_checker == 'Pending') {
                $status_checker = '<span class="btn btn-sm btn-primary text-xs">Waiting</span>' ;
            } else if ($post->status_checker == 'REJECTED') {
                $status_checker = '<span class="btn btn-sm btn-danger text-xs">Reject</span>' ;
            } else {  
                if ($post->status_checker == '') {
                    $status_checker = '' ;
                } else {
                    $status_checker = '<span class="btn btn-sm btn-success text-xs">Completed</span>' ;
                } 
            }
            if ($post->status_approver == 'Pending') {
                $status_approver = '<span class="btn btn-sm btn-primary text-xs">Waiting</span>' ;
            } else {
                $status_approver = '<span class="btn btn-sm btn-success text-xs">Completed</span>' ;
            }
            if ($post->status_legalizer == 'Pending') {
                $status_legalizer = '<span class="btn btn-sm btn-primary text-xs">Waiting</span>' ;
            } else {
                $status_legalizer = '<span class="btn btn-sm btn-success text-xs">Completed</span>' ;
            } 
        } else {
            $status_legalizer = '<span class="btn btn-sm btn-success text-xs">Completed</span>' ;
            $status_checker = '<span class="btn btn-sm btn-success text-xs">Completed</span>' ;
            $status_approver = '<span class="btn btn-sm btn-success text-xs">Completed</span>' ;

        }    

        $nestedData['no'] = $no ; 
        $nestedData['ponum'] = $docnum ; 
        $nestedData['docnum'] = $post->docnum ;     
        $nestedData['docdate'] = AppModel::local_date_formate_name(substr($post->orderdate,0,10)) ;   
        $nestedData['remark'] = '' ;	
        $nestedData['amount'] = number_format($post->amount,0) ;	
        $nestedData['check'] = $status_checker ;  
        $nestedData['approve'] = $status_approver ;  
        $nestedData['legal'] = $status_legalizer ;  
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
        $data['list'] = POApproval::get_transaction_list($year, $search, $vendor_id)->get(); 
        $data['num'] = POApproval::get_transaction_list($year, $search, $vendor_id)->count(); 
        $data['ref_form'] = '' ; 
        return view('po_approval.po_export', $data); 
    }

    public function show(Request $request) {         
        $str_req = explode("_",$request->trc_unix_id); 
        $str = explode("_",Crypt::decryptString(str_replace("-","=", $str_req[0])));  
        $sys_id = "'".$request->trc_unix_id."'" ;    
        $PONum = $str[0] ;  
        $data_detail = POApproval::data_detail($PONum);    
        foreach ($data_detail as $db) {
            $sys_id = Crypt::encryptString($db->PONum.'_0') ;
            $sys_id = str_replace("=", "-", $sys_id).'_1' ;
            $data['trc_unix_id'] =  $sys_id ;   
            $data['ref_form'] = '' ;
            $data['ref_tab'] = 1 ;  
        }  
        return view('po_approval.po_preview', $data);  
    }

    function print_view(Request $request){    
        $data['trc_unix_id'] = $request->trc_unix_id ;     
        $data['ref_form'] = $request->ref_form ; 
        return view('po_approval.po_direct_print', $data);
    } 

    function get_button_approve(Request $request) {    
        $data['trc_unix_id'] = $request->trc_unix_id ;  
        $sys_id = "'".$request->trc_unix_id."'" ;     

        $str_req = explode("_",$request->trc_unix_id); 
        $str = explode("_",Crypt::decryptString(str_replace("-","=", $str_req[0])));   
        $po_num = $str[0] ;    
        $my_username = Auth::user()->username ;   
        $check_buyer = POApproval::check_access_buyer($po_num, $my_username) ;
        if ($check_buyer > 0) {
            $data['button_approve'] = '<button class="btn btn-primary btn-sm text-sm" style="width: 100px;" onclick="getApprovalForm('.$sys_id.')">Approve</button>' ;
        } 
        echo json_encode($data); 
    } 

    public function show_attachment(Request $request)
    {
        
        $client = new Client(); 
        $data = [];
        $username = Auth::user()->username ;
        $password = Crypt::decryptString(Auth::user()->epicor_password) ;
        $seqNum = $request->seqNum ;
        $host_api = self::get_host_api() ;
        try {
            $response = $client->request('POST', $host_api.'Attachment', [
                'json' => [
                    'seqNum' => $seqNum,  
                    "nik" => "$username",
                    "password" => "$password"
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'verify' => false,  
            ]);
         
            $responseBody = json_decode($response->getBody()->getContents(), true);
        
            // Mengisi data dari respons
            $data['code'] = $responseBody['code'];
            $data['desc'] = $responseBody['desc'];
            $data['draw'] = $responseBody['draw'];
            $data['fileDesc'] = $responseBody['fileDesc']; 
        
        } catch (RequestException $e) { 
            \Log::error('API request failed', [
                'message' => $e->getMessage(),
                'request' => $e->getRequest(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null,
            ]);
        
            $data['code'] = 500;
            $data['desc'] = 'error'; 
            $data['draw'] = '';
            $data['fileDesc'] = ''; 
        } 
        return $data ;
    }

    public function submit_approval(Request $request)
    {
        
        $client = new Client(); 
        $data = [];
        $host_api = self::get_host_api() ;
        $username = Auth::user()->username ;
        $password = Crypt::decryptString(Auth::user()->epicor_password) ;
        $str_req = explode("_",$request->trc_unix_id); 
        $str = explode("_",Crypt::decryptString(str_replace("-","=", $str_req[0])));   
        $po_num = $str[0] ;  
        $approve_msg =  $request->approve_msg ;
        $approve_sts =  $request->approve_sts ; 
        $db_po = POApproval::data_detail($po_num);
        $apvAmt = 0 ;
        foreach ($db_po AS $row) {
            $apvAmt = $row->TotalCharges ; 
        }
        $db_po = POApproval::get_sequence_approval($po_num) ;
        $msgTo = '' ;
        $msgFrom = '' ;
        foreach ($db_po AS $row) {
            $user_id1 = $row->user_id1 ;
            if ($row->status_checker == '' || $row->status_checker == null || $row->status_checker != 'APPROVED') {
                $template = "You have a document (PO Num : **".$po_num."**) that requires your approval." ;
                $msgFrom = $row->buyer_id ; 
                $msgTo = $row->buyer_id2 ; 
                $next_user_id = $row->user_id2;
            } else if ($row->status_approver == '' || $row->status_approver == null || $row->status_approver != 'APPROVED') {
                $template = "You have a document (PO Num : **".$po_num."**) that requires your approval." ;
                $msgFrom = $row->buyer_id2 ; 
                $msgTo = $row->buyer_id3 ; 
                $next_user_id = $row->user_id2; 
            } else if ($row->status_legalizer == '' || $row->status_legalizer == null || $row->status_legalizer != 'APPROVED') {
                $template = "You have a document (PO Num : **".$po_num."**) that requires your review." ;
                $msgFrom = $row->buyer_id3 ; 
                $msgTo = $row->buyer_id4 ; 
                $next_user_id = $row->user_id1;  
            } 
        }
 
        if ($msgTo != '' && $msgFrom != '') {
            $msgToName = POApproval::get_buyer_name($msgTo);  
            // dd($po_num, "$approve_sts", $apvAmt, "$msgFrom", "$msgTo", "$approve_msg", "$msgToName");
            if ($msgToName != '') {
                try {
                    $response = $client->request('POST', $host_api.'PO/ApprovePO', [
                        'json' => [
                            'poNum' => $po_num,  
                            'approverResponse' => "$approve_sts",  
                            'apvAmt' => $apvAmt,  
                            'msgFrom' =>  "$msgFrom",  
                            'msgTo' =>  "$msgTo",  
                            'msgText' =>  "$approve_msg",  
                            'msgToName' => "$msgToName",   
                            "nik" => "$username",
                            "password" => "$password"
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
                    $data['desc'] = 'error';  
                } 
            } else {
                $data['code'] = 500;
                $data['desc'] = "Buyer Description Not Found"; 
            }
        } else {
            $data['code'] = 500;
            $data['desc'] = "Buyer Description Not Found";  
        }

        if ($data['code'] == 200) { 
            $cc = [];   
            if($approve_sts == 'APPROVED') {
                $next_user = explode("~", POApproval::get_email_by_username($next_user_id));
                $email_next_user = $next_user[0] ;
                $name_next_user = $next_user[1] ;  
            } else {
                $next_user = explode("~", POApproval::get_email_by_username($user_id1));
                $email_next_user = $next_user[0] ;
                $name_next_user = $next_user[1] ;  
            } 
            $next_user = explode("~", POApproval::get_email_by_username($user_id1));
            $email_created = $next_user[0] ; 
            if ($user_id1 != $next_user_id) {
                $cc[] = $email_created ;  
            } 
            self::sendApprovalNotification($email_next_user, $name_next_user, $template, $request->trc_unix_id, $cc, $approve_msg); 
        }
        return $data ;
    }
       
    public function draw_print_header($header, $DocNum, $Address, $DocType, $Rev, $CompanyName, $Logo, $City, $Province, $PostalCode, $State, $Phone, $Fax, $ProjectName, $PartnerCode, $doc_status, $AddressHtml,$PartnerAddressHtml, $DocDate, $PartnerName, $PICName, $po_num, $status_draft){
        $w_top = array(130, 61);
        $w_head = array(10, 81, 20, 20, 15, 20, 25);
        $w_detail = array(10, 81, 20, 20, 15, 20, 25);
        $imgdata = base64_decode($Logo);  
         
        PDF::SetFillColor(255,255,255);
        PDF::SetTextColor(0); 
        PDF::SetFont('dejavusans', 'B', 9);
        PDF::Cell(115, 5, 'PURCHASE ORDER ( '.$DocNum.' ) ' . $status_draft, '', 0, 'L', 1);
        PDF::Cell(75, 5, '', '', 0, 'L', 1); 
        PDF::Ln(); 
        PDF::SetFont('dejavusans', '', 8, '', false);
        PDF::Cell(95, 5, 'Date : '.$DocDate.' '.$Rev, '', 0, 'L', 1);
        PDF::Cell(96, 5, '', '', 0, 'C', 1); 
        PDF::Ln();
    
        PDF::Ln();  
        PDF::Image('@'.$imgdata, 155, 6, 50, 13, '', '', '', false, 150, '', false, false, 1, false, false, true); 
        PDF::SetFont('dejavusans', 'B', 8);
        PDF::Cell(95, 5, 'Order To :', 'TL', 0, 'L', 1);
        PDF::Cell(96, 5, 'Invoice To :', 'TLR', 0, 'L', 1);
    
        PDF::Ln(); 
        PDF::SetFont('dejavusans', '', 9, '', false);
        PDF::Cell(95, 5, ' '.$PartnerName, 'RL', 0, 'L', 1);
        PDF::Cell(96, 5, ' '.$CompanyName, 'RL', 0, 'L', 1); 
        PDF::Ln(5);
    
        PDF::Cell(95, 5, '', 'RL', 0, 'L', 1);
        PDF::Cell(96, 5, '', 'RL', 0, 'L', 1);
    
        PDF::Ln(5);  
        PDF::SetFont('dejavusans', '', 8, '', false);
        PDF::writeHTML($AddressHtml, true, false, true, false, '');
        PDF::Ln(-4);
    
        PDF::Cell(95, 5, '', 'RL', 0, 'L', 1);
        PDF::Cell(96, 5, '', 'RL', 0, 'L', 1);
    
        PDF::Ln();  
        PDF::Cell(95, 5, ' Up. '.$PICName, 'RL', 0, 'L', 1);
        PDF::Cell(96, 5, ' NPWP : 66.841.695.1-408.000', 'RL', 0, 'L', 1); 
        PDF::Ln(5); 
        PDF::writeHTML($PartnerAddressHtml, true, false, true, false, '');
        PDF::Ln(-4);
        PDF::Cell(95, 5, '', 'BRL', 0, 'L', 1);
        PDF::Cell(96, 5, '', 'BRL', 0, 'L', 1);
    
        $imgdata = base64_decode(substr(str_replace("-","+", $Logo),22)) ; 
        PDF::Image('@'.$imgdata, 155, 5, 50, 13, '', '', '', false, 150, '', false, false, 1, false, false, true);
        
        if ($doc_status==1) { 
            $port = env('APP_URL') ;  
            $url =  $port.'dist/img/draft.png' ;
            $img = file_get_contents($url);    
            $draft_image = base64_encode($img); 
            $draft_image = base64_decode($draft_image);
           PDF::Image('@'.$draft_image, 10, 40, 350, 230, '', '', '', false, 150, '', false, false, 1, false, false, true);  
       } 
         
       PDF::Ln(8);
       PDF::SetFont('dejavusans', '', 8);
       PDF::SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
       PDF::Cell($w_head[0], 6, $header[0], 'TB', 0, 'L', 1);
       PDF::Cell($w_head[1], 6, $header[1], 'TB', 0, 'L', 1);
       PDF::Cell($w_head[2], 6, $header[2], 'TB', 0, 'R', 1);
       PDF::Cell($w_head[3], 6, $header[3], 'TB', 0, 'R', 1);
       PDF::Cell($w_head[4], 6, $header[4], 'TB', 0, 'L', 1);
       PDF::Cell($w_head[5], 6, $header[5], 'TB', 0, 'R', 1);
       PDF::Cell($w_head[6], 6, $header[6], 'TB', 0, 'R', 1);
       PDF::Ln(7);
    
     PDF::SetFillColor(224, 235, 255);
     PDF::SetTextColor(0);
     PDF::SetFont('');
    }
    
    public function draw_print_main_header($header, $DocNum, $DocDate, $PartnerName, $Address, $UserName, $PICName, $DocType, $SubTotal, $Discount, $Ppn, $Total, $TOP, $Status, $Rev, $Approval, $Legalized, $Currency, $Description, $Vendor, $Sign_1, $Sign_2, $Sign_3, $Sign_4, $IsCompleted, $LastUpdate1, $LastUpdate2, $LastUpdate3, $LastUpdate4, $sum, $CompanyName, $Logo, $DocRegNum, $City, $Province, $PostalCode, $State, $Phone, $Fax, $ProjectName, $PartnerCode, $Checked, $IDUser2, $doc_status, $po_num, $print_option,$AddressHtml,$PartnerAddressHtml, $status_draft, $BuyerID){
     
        $w_top = array(130, 61); 
        $w_head = array(10, 81, 20, 20, 15, 20, 25);
        $w_detail = array(10, 81, 20, 20, 15, 20, 25);
        $imgdata = base64_decode($Logo);  
        
        
        PDF::SetFillColor(255,255,255);
        PDF::SetTextColor(0); 
        PDF::SetFont('dejavusans', 'B', 9);
        PDF::Cell(115, 5, 'PURCHASE ORDER ( '.$DocNum.' )' . $status_draft, '', 0, 'L', 1);
        PDF::Cell(75, 5, '', '', 0, 'L', 1); 
        PDF::Ln(); 
        PDF::SetFont('dejavusans', '', 8, '', false);
        PDF::Cell(95, 5, 'Date : '.$DocDate.' '.$Rev, '', 0, 'L', 1);
        PDF::Cell(96, 5, '', '', 0, 'C', 1); 
        PDF::Ln();
    
        PDF::Ln();  
        PDF::Image('@'.$imgdata, 155, 6, 50, 13, '', '', '', false, 150, '', false, false, 1, false, false, true); 
        PDF::SetFont('dejavusans', 'B', 8);
        PDF::Cell(95, 5, 'Order To :', 'TL', 0, 'L', 1);
        PDF::Cell(96, 5, 'Invoice To :', 'TLR', 0, 'L', 1);
    
        PDF::Ln(); 
        PDF::SetFont('dejavusans', '', 9, '', false);
        PDF::Cell(95, 5, ' '.$PartnerName, 'RL', 0, 'L', 1);
        PDF::Cell(96, 5, ' '.$CompanyName, 'RL', 0, 'L', 1); 
        PDF::Ln(5);
    
        PDF::Cell(95, 5, '', 'RL', 0, 'L', 1);
        PDF::Cell(96, 5, '', 'RL', 0, 'L', 1);
    
        PDF::Ln(5);  
        PDF::SetFont('dejavusans', '', 8, '', false);
        PDF::writeHTML($AddressHtml, true, false, true, false, '');
        PDF::Ln(-4);
    
        PDF::Cell(95, 5, '', 'RL', 0, 'L', 1);
        PDF::Cell(96, 5, '', 'RL', 0, 'L', 1);
    
        PDF::Ln();  
        PDF::Cell(95, 5, ' Up. '.$PICName, 'RL', 0, 'L', 1);
        PDF::Cell(96, 5, ' NPWP : 66.841.695.1-408.000', 'RL', 0, 'L', 1); 
        PDF::Ln(5); 
        PDF::writeHTML($PartnerAddressHtml, true, false, true, false, '');
        PDF::Ln(-4);
        PDF::Cell(95, 5, '', 'BRL', 0, 'L', 1);
        PDF::Cell(96, 5, '', 'BRL', 0, 'L', 1);
        
        $imgdata = base64_decode(substr(str_replace("-","+", $Logo),22)) ; 
        PDF::Image('@'.$imgdata, 155, 5, 50, 13, '', '', '', false, 150, '', false, false, 1, false, false, true); 
        
        if ($doc_status==1) { 
            $port = env('APP_URL') ;  
            $url =  $port.'dist/img/draft.png' ;
            $img = file_get_contents($url);    
            $draft_image = base64_encode($img); 
            $draft_image = base64_decode($draft_image);
           PDF::Image('@'.$draft_image, 10, 40, 350, 230, '', '', '', false, 150, '', false, false, 1, false, false, true);  
       } 
         
       PDF::Ln(8);
       PDF::SetFont('dejavusans', '', 8);
       PDF::SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
       PDF::Cell($w_head[0], 6, $header[0], 'TB', 0, 'L', 1);
       PDF::Cell($w_head[1], 6, $header[1], 'TB', 0, 'L', 1);
       PDF::Cell($w_head[2], 6, $header[2], 'TB', 0, 'R', 1);
       PDF::Cell($w_head[3], 6, $header[3], 'TB', 0, 'R', 1);
       PDF::Cell($w_head[4], 6, $header[4], 'TB', 0, 'L', 1);
       PDF::Cell($w_head[5], 6, $header[5], 'TB', 0, 'R', 1);
       PDF::Cell($w_head[6], 6, $header[6], 'TB', 0, 'R', 1);
       PDF::Ln(7);
         
        $DB = POApproval::detail_list_data($po_num);
        $num = $DB->count() ; 
        // var_dump($num);
    
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
            $jml_dataX = $num - 7 ;
            $limitX = $jml_dataX ;
        }
        
        $all_page = POApproval::load_data_print_page($po_num,$Status,$print_option,$startX,$limitX); 
         foreach ($all_page as $row) {
             $num_pages = PDF::getNumPages();
             PDF::startTransaction();
             PDF::SetFont('dejavusans', '', 7.5);
             PDF::SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
        
             // page 1
            PDF::Cell($w_detail[0], 5, $row[0], '', 0, 'L', 0);
            PDF::Cell($w_detail[1], 5, $row[1].($row[13] > 0 ? ' | ('.$row[12].' '.$row[13].')' : ''), '', 0, 'L', 0);
            PDF::Cell($w_detail[2], 5, $row[2], '', 0, 'R', 0);
            PDF::Cell($w_detail[3], 5, $row[3], '', 0, 'R', 0);
            PDF::Cell($w_detail[4], 5, $row[4], '', 0, 'L', 0);
            PDF::Cell($w_detail[5], 5, $row[5], '', 0, 'R', 0);
            PDF::Cell($w_detail[6], 5, $row[7], '', 0, 'R', 0);
            PDF::Ln(4);
     
            PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 2, 'color' => array(128, 128, 128)));
            PDF::Cell($w_detail[0], 5, '', 'B', 0, 'L', 0);
            PDF::Cell($w_detail[1], 5, ($row[1] == $row[11] ? '' : $row[11]), 'B', 0, 'L', 0);
            PDF::Cell($w_detail[2], 5, '', 'B', 0, 'R', 0);
            PDF::Cell($w_detail[3], 5, ($row[4] == $row[9] ? '' : $row[8]), 'B', 0, 'R', 0);
            PDF::Cell($w_detail[4], 5, ($row[4] == $row[9] ? '' : $row[9]), 'B', 0, 'L', 0);
            PDF::Cell($w_detail[5], 5, '', 'B', 0, 'R', 0);
            // PDF::Cell($w_detail[6], 5, ($row[6] == 0 ? '' : '('.$row[6].')'), 'B', 0, 'R', 0); 
            PDF::Cell($w_detail[6], 5, '', 'B', 0, 'R', 0); 
            PDF::Ln(6); 
         } 
     
         if ($num<=5) {
            PDF::SetFont('dejavusans', '', 8);
            PDF::SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
            PDF::Cell(106, 6, 'Total Qty :', 'TB', 0, 'C');
            PDF::Cell(25, 6, $sum, 'TB', 0, 'R');
            PDF::Cell(60, 6, '', 'TB', 0, 'R');
    
            PDF::SetFillColor(255,255,255);
            PDF::SetTextColor(0);
            PDF::Ln(); PDF::Ln(3);
            PDF::SetFont('dejavusans', '', 8, '', 'false');
            PDF::Cell(116, 6, 'Payment term '.$TOP.' Days After Invoice Receipt', '', 0, 'L', 1);
            PDF::Cell(25, 6, 'Subtotal', '', 0, 'L', 1);
            PDF::Cell(5, 6, ':', '', 0, 'C', 1);
            PDF::Cell(15, 6, $Currency, '', 0, 'L', 1);
            PDF::Cell(30, 6, $SubTotal, '', 0, 'R', 1);
    
            PDF::Ln();
            PDF::Cell(116, 6, '', '', 0, 'L', 1);
            PDF::Cell(25, 6, 'Discount', '', 0, 'L', 1);
            PDF::Cell(5, 6, ':', '', 0, 'C', 1);
            PDF::Cell(15, 6, $Currency, '', 0, 'L', 1);
            PDF::Cell(30, 6, $Discount, '', 0, 'R', 1);
    
            PDF::Ln();
            PDF::Cell(116, 6, '', '', 0, 'L', 1);
            PDF::Cell(25, 6, 'PPN', '', 0, 'L', 1);
            PDF::Cell(5, 6, ':', '', 0, 'C', 1);
            PDF::Cell(15, 6, $Currency, '', 0, 'L', 1);
            PDF::Cell(30, 6, $Ppn, '', 0, 'R', 1);
    
            PDF::Ln();
            PDF::Cell(116, 6, 'Note :', '', 0, 'L', 1);
            PDF::Cell(25, 6, 'Total', '', 0, 'L', 1);
            PDF::Cell(5, 6, ':', '', 0, 'C', 1);
            PDF::Cell(15, 6, $Currency, '', 0, 'L', 1);
            PDF::SetFont('dejavusans', 'B', 8);
            PDF::Cell(30, 6, $Total, '', 0, 'R', 1); 
    
            PDF::Ln(8);
            PDF::SetFont('dejavusans', '', 8, '', 'false');
            PDF::writeHTML($Description, true, false, true, false, '');   
    
            PDF::Ln(8);  
    
            PDF::SetY(-80); 
            PDF::Cell(191, 4, '* General Provision : ', '', 0, 'L');
            PDF::Ln(); 
            PDF::Cell(191, 4, '1. Payment will be due every 6 & 21 day of the month.', '', 0, 'L');
            PDF::Ln(); 
            PDF::Cell(191, 4, '2. Good wich are not meet with requirement (specification and condition) will be returned.', '', 0, 'L');
            PDF::Ln(); 
            PDF::Cell(191, 4, '3. For payment purpose, please attach this purchase order in your invoice.', '', 0, 'L');
            PDF::Ln(); 
            PDF::Cell(191, 4, '4. Field this Purchase Order number in your delivery order.', '', 0, 'L');
            PDF::Ln(); 
            PDF::Cell(191, 4, '5. Invoice will be received at 09.00 AM - 15.00 PM every Wednesday.', '', 0, 'L'); 
            PDF::Ln(); 
            PDF::Cell(191, 4, '6. If there is no feedback about this PO 1x24 hr from supplier, SAI will consider that supplier already confirmed.', '', 0, 'L');  
            PDF::Ln(10); 
            
            // batas
     
            $num_row = 225 ;   
            // if($IsCompleted==1){ PDF::Image('dist/img/sign/company_stamp.png', 90, $num_row, 40, 17, 'PNG', '', '', false, 150, '', false, false, 1, false, false, true); }
            if($BuyerID!='BOBAL') {  
                PDF::Ln(15);   
                PDF::Cell(5, 6, '', '', 0, 'C');
                PDF::Cell(38, 4, 'Prepared By ', '', 0, 'C');
                PDF::Cell(10, 4, '', '', 0, 'C');  
                if(!is_null($IDUser2)){
                    PDF::Cell(38, 4, 'Checked By', '', 0, 'C');
                }else{
                    PDF::Cell(38, 4, '', '', 0, 'C');   
                }
                PDF::Cell(10, 4, '', '', 0, 'C');
                PDF::Cell(38, 4, 'Approved By', '', 0, 'C');
                PDF::Cell(10, 4, '', '', 0, 'C');
                PDF::Cell(38, 4, 'Legalized by', '', 0, 'C');
            
                
                PDF::Ln();   
                PDF::SetFont('dejavusans', 'I', 8);
                
                PDF::Cell(5, 6, '', '', 0, 'C');
                PDF::Cell(38, 4, $UserName, '', 0, 'C');
                PDF::Cell(10, 4, '', '', 0, 'C'); 
                if(!is_null($IDUser2)){
                    PDF::Cell(38, 4, $Checked, '', 0, 'C');
                }else{
                   PDF::Cell(38, 4, '', '', 0, 'C');   
                }
                PDF::Cell(10, 4, '', '', 0, 'C');
                PDF::Cell(38, 4, $Approval, '', 0, 'C');
                PDF::Cell(10, 4, '', '', 0, 'C');
                PDF::Cell(38, 4, $Legalized, '', 0, 'C');
                PDF::Cell(10, 4, '', '', 0, 'C'); 
                PDF::Ln(); 
                
                PDF::SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
                // if(!empty($Sign_1)){  
                // $imgdata_sign_1 = base64_decode(substr(str_replace("-","+", $Sign_1),22)) ;  
                // PDF::Image('@'.$imgdata_sign_1, 17, $num_row, 40, 17, '', '', '', false, 150, '', false, false, 1, false, false, true); 
                // } 
                
                PDF::SetFont('courier', 'I', 8); 
                PDF::Cell(5, 6, '', '', 0, 'C');
                PDF::Cell(38, 6, $LastUpdate1, 'T', 0, 'C');
                PDF::Cell(10, 6, '', '', 0, 'C'); 
                
                // if(!empty($Sign_3)){ 
                // $imgdata_sign_3 = base64_decode(substr(str_replace("-","+", $Sign_3),22)) ;
                // PDF::Image('@'.$imgdata_sign_3, 65, $num_row, 40, 17, '', '', '', false, 150, '', false, false, 1, false, false, true); 
                // }
                if(!is_null($IDUser2)){ 
                    PDF::Cell(38, 6, $LastUpdate2, 'T', 0, 'C'); 
                    }else{ 
                    PDF::Cell(38, 6, '', '', 0, 'C');    
                    }
                
                
                PDF::Cell(10, 6, '', '', 0, 'C');
                //    if(!empty($Sign_4)){ 
                //     $imgdata_sign_4 = base64_decode(substr(str_replace("-","+", $Sign_4),22)) ;
                //     PDF::Image('@'.$imgdata_sign_4, 113, $num_row, 40, 17, '', '', '', false, 150, '', false, false, 1, false, false, true);  
                //    }
                PDF::Cell(38, 6, $LastUpdate3, 'T', 0, 'C');  
            
                PDF::Cell(10, 6, '', '', 0, 'C'); 
                PDF::Cell(38, 6, $LastUpdate4, 'T', 0, 'C'); 
                PDF::Ln(); 
            }
    
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
            $this->draw_print_header($header, $DocNum, $Address, $DocType, $Rev, $CompanyName, $Logo, $City, $Province, $PostalCode, $State, $Phone, $Fax, $ProjectName, $PartnerCode, $doc_status, $AddressHtml,$PartnerAddressHtml, $DocDate, $PartnerName, $PICName, $po_num, $status_draft);   
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
            
        }else if($jml_data > 5 && $jml_data < 15){ 
    
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
    $all_page = POApproval::load_data_print_page($po_num,$Status,$print_option,$start,$limit); 
    
    if ($page!=1 && $jml_data > 0) {
        foreach ($all_page as $row) {
            $num_pages = PDF::getNumPages();
                PDF::startTransaction();
                PDF::SetFont('dejavusans', '', 7.5);
                PDF::SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
        
                // page 1
                PDF::Cell($w_detail[0], 5, $row[0], '', 0, 'L', 0);
                PDF::Cell($w_detail[1], 5, $row[1].($row[13] > 0 ? ' | ('.$row[12].' '.$row[13].')' : ''), '', 0, 'L', 0);
                PDF::Cell($w_detail[2], 5, $row[2], '', 0, 'R', 0);
                PDF::Cell($w_detail[3], 5, $row[3], '', 0, 'R', 0);
                PDF::Cell($w_detail[4], 5, $row[4], '', 0, 'L', 0);
                PDF::Cell($w_detail[5], 5, $row[5], '', 0, 'R', 0);
                PDF::Cell($w_detail[6], 5, $row[7], '', 0, 'R', 0);
                PDF::Ln(4);
    
                PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 2, 'color' => array(128, 128, 128)));
                PDF::Cell($w_detail[0], 5, '', 'B', 0, 'L', 0);
                PDF::Cell($w_detail[1], 5, ($row[1] == $row[11] ? '' : $row[11]), 'B', 0, 'L', 0);
                PDF::Cell($w_detail[2], 5, '', 'B', 0, 'R', 0);
                PDF::Cell($w_detail[3], 5, ($row[4] == $row[9] ? '' : $row[8]), 'B', 0, 'R', 0);
                PDF::Cell($w_detail[4], 5, ($row[4] == $row[9] ? '' : $row[9]), 'B', 0, 'L', 0);
                PDF::Cell($w_detail[5], 5, '', 'B', 0, 'R', 0); 
            PDF::Cell($w_detail[6], 5, '', 'B', 0, 'R', 0); 
                PDF::Ln(6);
        }
      
        PDF::SetY(-15);
        PDF::SetFont('courier', 'I', 9);
        PDF::SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
        PDF::Cell(100, 6, $DocRegNum, 'T', 0, 'L');
        PDF::Cell(81, 6, '', 'T', 0, 'L');
        PDF::Cell(10, 6, PDF::getAliasNumPage().'/'.PDF::getAliasNbPages(), 'T', 0, 'L');
    
        if ($jml_data<=0) { 
            PDF::Cell(106, 6, 'Total Qty :', 'TB', 0, 'C');
            PDF::Cell(25, 6, $sum, 'TB', 0, 'R');
            PDF::Cell(60, 6, '', 'TB', 0, 'R');
            
            PDF::SetFillColor(255,255,255);
            PDF::SetTextColor(0);
            PDF::Ln(); PDF::Ln(3);
            PDF::SetFont('dejavusans', '', 8, '', 'false');
            PDF::Cell(116, 6, 'Payment term '.$TOP.' Days After Invoice Receipt', '', 0, 'L', 1);
            PDF::Cell(25, 6, 'Subtotal', '', 0, 'L', 1);
            PDF::Cell(5, 6, ':', '', 0, 'C', 1);
            PDF::Cell(15, 6, $Currency, '', 0, 'L', 1);
            PDF::Cell(30, 6, $SubTotal, '', 0, 'R', 1);
    
            PDF::Ln();
            PDF::Cell(116, 6, '', '', 0, 'L', 1);
            PDF::Cell(25, 6, 'PPN', '', 0, 'L', 1);
            PDF::Cell(5, 6, ':', '', 0, 'C', 1);
            PDF::Cell(15, 6, $Currency, '', 0, 'L', 1);
            PDF::Cell(30, 6, $Ppn, '', 0, 'R', 1);
    
            PDF::Ln();
            PDF::Cell(116, 6, 'Note :', '', 0, 'L', 1);
            PDF::Cell(25, 6, 'Total', '', 0, 'L', 1);
            PDF::Cell(5, 6, ':', '', 0, 'C', 1);
            PDF::Cell(15, 6, $Currency, '', 0, 'L', 1);
            PDF::SetFont('dejavusans', 'B', 8);
            PDF::Cell(30, 6, $Total, '', 0, 'R', 1);
    
            PDF::Ln(8);
            PDF::SetFont('dejavusans', '', 8, '', 'false');
            PDF::writeHTML($Description, true, false, true, false, '');   
    
            PDF::Ln(8);  
    
            PDF::SetY(-80); 
            PDF::Cell(191, 4, '* General Provision : ', '', 0, 'L');
            PDF::Ln(); 
            PDF::Cell(191, 4, '1. Payment will be due every 6 & 21 day of the month.', '', 0, 'L');
            PDF::Ln(); 
            PDF::Cell(191, 4, '2. Good wich are not meet with requirement (specification and condition) will be returned.', '', 0, 'L');
            PDF::Ln(); 
            PDF::Cell(191, 4, '3. For payment purpose, please attach this purchase order in your invoice.', '', 0, 'L');
            PDF::Ln(); 
            PDF::Cell(191, 4, '4. Field this Purchase Order number in your delivery order.', '', 0, 'L');
            PDF::Ln(); 
            PDF::Cell(191, 4, '5. Invoice will be received at 09.00 AM - 15.00 PM every Wednesday.', '', 0, 'L'); 
            PDF::Ln(); 
            PDF::Cell(191, 4, '6. If there is no feedback about this PO 1x24 hr from supplier, SAI will consider that supplier already confirmed.', '', 0, 'L');  
            PDF::Ln(10); 
        }else{
            PDF::AddPage();
            $this->draw_print_header($header, $DocNum, $Address, $DocType, $Rev, $CompanyName, $Logo, $City, $Province, $PostalCode, $State, $Phone, $Fax, $ProjectName, $PartnerCode, $doc_status, $AddressHtml,$PartnerAddressHtml, $DocDate, $PartnerName, $PICName, $po_num, $status_draft);   
        }  
     }
    //  dd($jml_data);
     if($page!=1 && $jml_data<=0){
    
        foreach ($all_page as $row) {
            $num_pages = PDF::getNumPages();
            PDF::startTransaction();
            PDF::SetFont('dejavusans', '', 7.5);
            PDF::SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
         
            PDF::Cell($w_detail[0], 5, $row[0], '', 0, 'L', 0);
            PDF::Cell($w_detail[1], 5, $row[1].($row[13] > 0 ? ' | ('.$row[12].' '.$row[13].')' : ''), '', 0, 'L', 0);
            PDF::Cell($w_detail[2], 5, $row[2], '', 0, 'R', 0);
            PDF::Cell($w_detail[3], 5, $row[3], '', 0, 'R', 0);
            PDF::Cell($w_detail[4], 5, $row[4], '', 0, 'L', 0);
            PDF::Cell($w_detail[5], 5, $row[5], '', 0, 'R', 0);
            PDF::Cell($w_detail[6], 5, $row[7], '', 0, 'R', 0);
            PDF::Ln(4);
    
            PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 2, 'color' => array(128, 128, 128)));
            PDF::Cell($w_detail[0], 5, '', 'B', 0, 'L', 0);
            PDF::Cell($w_detail[1], 5, ($row[1] == $row[11] ? '' : $row[11]), 'B', 0, 'L', 0);
            PDF::Cell($w_detail[2], 5, '', 'B', 0, 'R', 0);
            PDF::Cell($w_detail[3], 5, ($row[4] == $row[9] ? '' : $row[8]), 'B', 0, 'R', 0);
            PDF::Cell($w_detail[4], 5, ($row[4] == $row[9] ? '' : $row[9]), 'B', 0, 'L', 0);
            PDF::Cell($w_detail[5], 5, '', 'B', 0, 'R', 0);
            // PDF::Cell($w_detail[6], 5, ($row[6] == 0 ? '' : '('.$row[6].')'), 'B', 0, 'R', 0); 
            PDF::Cell($w_detail[6], 5, '', 'B', 0, 'R', 0); 
            PDF::Ln(6);
        }
    
            PDF::SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
            PDF::Cell(106, 6, 'Total Qty :', 'TB', 0, 'C');
            PDF::Cell(25, 6, $sum, 'TB', 0, 'R');
            PDF::Cell(60, 6, '', 'TB', 0, 'R');
    
            PDF::SetFillColor(255,255,255);
            PDF::SetTextColor(0);
            PDF::Ln(); PDF::Ln(3);
            PDF::SetFont('dejavusans', '', 8, '', 'false');
            PDF::Cell(116, 6, 'Payment term '.$TOP.' Days After Invoice Receipt', '', 0, 'L', 1);
            PDF::Cell(25, 6, 'Subtotal', '', 0, 'L', 1);
            PDF::Cell(5, 6, ':', '', 0, 'C', 1);
            PDF::Cell(15, 6, $Currency, '', 0, 'L', 1);
            PDF::Cell(30, 6, $SubTotal, '', 0, 'R', 1);
    
            PDF::Ln();
            PDF::Cell(116, 6, '', '', 0, 'L', 1);
            PDF::Cell(25, 6, 'Discount', '', 0, 'L', 1);
            PDF::Cell(5, 6, ':', '', 0, 'C', 1);
            PDF::Cell(15, 6, $Currency, '', 0, 'L', 1);
            PDF::Cell(30, 6, $Discount, '', 0, 'R', 1);
    
            PDF::Ln();
            PDF::Cell(116, 6, '', '', 0, 'L', 1);
            PDF::Cell(25, 6, 'PPN', '', 0, 'L', 1);
            PDF::Cell(5, 6, ':', '', 0, 'C', 1);
            PDF::Cell(15, 6, $Currency, '', 0, 'L', 1);
            PDF::Cell(30, 6, $Ppn, '', 0, 'R', 1);
    
            PDF::Ln(); 
            PDF::Cell(116, 6, 'Note :', '', 0, 'L', 1);
            PDF::Cell(25, 6, 'Total', '', 0, 'L', 1);
            PDF::Cell(5, 6, ':', '', 0, 'C', 1);
            PDF::Cell(15, 6, $Currency, '', 0, 'L', 1);
            PDF::SetFont('dejavusans', 'B', 8);
            PDF::Cell(30, 6, $Total, '', 0, 'R', 1);
    
            PDF::Ln(8);
            PDF::SetFont('dejavusans', '', 8, '', 'false');
            PDF::writeHTML($Description, true, false, true, false, '');   
    
            PDF::Ln(8);  
    
            PDF::SetY(-80); 
            PDF::Cell(191, 4, '* General Provision : ', '', 0, 'L');
            PDF::Ln(); 
            PDF::Cell(191, 4, '1. Payment will be due every 6 & 21 day of the month.', '', 0, 'L');
            PDF::Ln(); 
            PDF::Cell(191, 4, '2. Good wich are not meet with requirement (specification and condition) will be returned.', '', 0, 'L');
            PDF::Ln(); 
            PDF::Cell(191, 4, '3. For payment purpose, please attach this purchase order in your invoice.', '', 0, 'L');
            PDF::Ln(); 
            PDF::Cell(191, 4, '4. Field this Purchase Order number in your delivery order.', '', 0, 'L');
            PDF::Ln(); 
            PDF::Cell(191, 4, '5. Invoice will be received at 09.00 AM - 15.00 PM every Wednesday.', '', 0, 'L'); 
            PDF::Ln(); 
            PDF::Cell(191, 4, '6. If there is no feedback about this PO 1x24 hr from supplier, SAI will consider that supplier already confirmed.', '', 0, 'L'); 
            PDF::Ln(10);  
     
            $num_row = 225 ;
            
            
            // if($IsCompleted==1){ PDF::Image('dist/img/sign/company_stamp.png', 90, $num_row, 40, 17, 'PNG', '', '', false, 150, '', false, false, 1, false, false, true); }
            PDF::Ln(15);   
            if($BuyerID!='BOBAL') {  
            PDF::Cell(5, 6, '', '', 0, 'C');
            PDF::Cell(38, 4, 'Prepared By ', '', 0, 'C');
            PDF::Cell(10, 4, '', '', 0, 'C'); 
            if(!is_null($IDUser2)){
                PDF::Cell(38, 4, 'Checked By', '', 0, 'C');
            }else{
                PDF::Cell(38, 4, '', '', 0, 'C');   
            }
            PDF::Cell(10, 4, '', '', 0, 'C');
            PDF::Cell(38, 4, 'Approved By', '', 0, 'C');
            PDF::Cell(10, 4, '', '', 0, 'C');
            PDF::Cell(38, 4, 'Legalized by', '', 0, 'C');
        
            
            PDF::Ln();   
            PDF::SetFont('dejavusans', 'I', 8);
            
            PDF::Cell(5, 6, '', '', 0, 'C');
            PDF::Cell(38, 4, $UserName, '', 0, 'C');
            PDF::Cell(10, 4, '', '', 0, 'C'); 
            if(!is_null($IDUser2)){
                PDF::Cell(38, 4, $Checked, '', 0, 'C');
            }else{
               PDF::Cell(38, 4, '', '', 0, 'C');   
            }
            PDF::Cell(10, 4, '', '', 0, 'C');
            PDF::Cell(38, 4, $Approval, '', 0, 'C');
            PDF::Cell(10, 4, '', '', 0, 'C');
            PDF::Cell(38, 4, $Legalized, '', 0, 'C');
            PDF::Cell(10, 4, '', '', 0, 'C'); 
            PDF::Ln(); 
            
            PDF::SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
            // if(!empty($Sign_1)){  
            // $imgdata_sign_1 = base64_decode(substr(str_replace("-","+", $Sign_1),22)) ;  
            // PDF::Image('@'.$imgdata_sign_1, 17, $num_row, 40, 17, '', '', '', false, 150, '', false, false, 1, false, false, true); 
            // } 
            
            
            PDF::SetFont('courier', 'I', 8);
            PDF::Cell(5, 6, '', '', 0, 'C');
            PDF::Cell(38, 6, $LastUpdate1, 'T', 0, 'C');
            PDF::Cell(10, 6, '', '', 0, 'C'); 
              
            if(!is_null($IDUser2)){ 
            PDF::Cell(38, 6, $LastUpdate2, 'T', 0, 'C'); 
            }else{ 
            PDF::Cell(38, 6, '', '', 0, 'C');    
            }
            
            
           PDF::Cell(10, 6, '', '', 0, 'C');
            //    if(!empty($Sign_4)){ 
            //     $imgdata_sign_4 = base64_decode(substr(str_replace("-","+", $Sign_4),22)) ;
            //     PDF::Image('@'.$imgdata_sign_4, 113, $num_row, 40, 17, '', '', '', false, 150, '', false, false, 1, false, false, true);  
            //    }
            PDF::Cell(38, 6, $LastUpdate3, 'T', 0, 'C');  
           
            PDF::Cell(10, 6, '', '', 0, 'C'); 
            PDF::Cell(38, 6, $LastUpdate4, 'T', 0, 'C'); 
            }
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
        $po_num = $str[0]; 
        $doc_status = 3 ;
        $print_option = 0 ; 
     
    $data = DB::connection('sqlsrv4')->table('Erp.POHeader AS a')
    ->join('Erp.POHeader_UD AS b', 'a.SysRowID', '=', 'b.ForeignSysRowID')
    ->join('Erp.Vendor AS c', 'a.VendorNum', '=', 'c.VendorNum')
    ->select('b.DocNum_C', 'c.Name AS PartnerName', 'a.*', 'c.VendorID AS PartnerCode', 'c.Address1', 'c.Address2', 'c.City', 'c.State', 'c.Country')
    ->where('a.PONum', '=', $po_num)  
    ->get();
    $status_approve = POApproval::get_sequence_approval($po_num);
    $db_po = POApproval::get_sequence_approval($po_num) ;
        $msgTo = '' ;
        $msgFrom = '' ;
        $status_draft = '' ;
        $msgToName = POApproval::get_buyer_name($msgTo);  
        foreach ($db_po AS $row) { 
            if (($row->status_checker == 'Pending' || $row->status_checker != 'APPROVED')) { 
                $msgTo = ' | Pending Approve : '.POApproval::get_buyer_name($row->buyer_id2) ; 
                $status_draft = ' (DRAFT)' ;
            } else if ($row->status_approver == 'Pending' || $row->status_approver != 'APPROVED') { 
                $msgTo = ' | Pending Approve : '.POApproval::get_buyer_name($row->buyer_id3) ; 
                $status_draft = ' (DRAFT)' ;
            } else if ($row->status_legalizer == 'Pending' || $row->status_legalizer != 'APPROVED') { 
                $msgTo = ' | Pending Approve : '.POApproval::get_buyer_name($row->buyer_id4) ; 
                $status_draft = ' (DRAFT)' ;
            }  else if ($row->status_checker == 'REJECTED') { 
                $msgTo = ' | Reject By : '.$row->user2 ; 
                $status_draft = ' (REJECTED)' ;
            } else if ($row->status_approver == 'REJECTED') { 
                $msgTo = ' | Reject By : '.$row->user3 ; 
                $status_draft = ' (REJECTED)' ;
            } else if ($row->status_legalizer == 'REJECTED') { 
                $msgTo = ' | Reject By : '.$row->user4 ; 
                $status_draft = ' (REJECTED)' ;
            }
        }
    
     if($data->count() > 0){ 
        foreach($data as $db){  
            $DocNum = $db->DocNum_C.'_'.$db->PONum;
            $DocDate = AppModel::local_date_formate_name($db->OrderDate) . $msgTo ;
            $PartnerName = $db->PartnerName ;
            $PartnerCode = $db->PartnerCode ;
            $PartnerAddress = $db->Address1.' '.$db->Address2.' '.$db->City.' '.$db->State.' '.$db->Country ; 
            $ProjectName = '' ; 
            $PICName = '' ;
            $Telp = '' ;
            $Fax = '' ; 
            $DocType = '' ;
            $SubTotal = number_format(round($db->DocTotalCharges,2),2) ;
            $Discount = number_format(round(0,2),0) ;
            $Ppn = number_format(round($db->DocTotalTax,2),2) ;
            $Total = number_format(round($db->DocTotalOrder,2),2) ;
            $TOP = $db->TermsCode ;
            
            $Status = 3 ;
            $Rev = '' ;  
            $Cat1 = '' ;
            $Currency = $db->CurrencyCode ;
            $judul = $db->DocNum_C ;     
            $Description = $db->CommentText ;
            $PRDocNum = '' ;
            $UserName = $db->EntryPerson ; 
            $BuyerID = $db->BuyerID ;  
            
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
        $Status = 0 ; 
        $Address = '' ; 
        $Description = '' ;
        $PRDocNum = '' ;
        $UserName = '' ;
        $Currency = '' ;
        $BuyerID = '' ;
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
    
     $DocRegNum= AppModel::find_doc_reg_num(1100) ;
     $sum = number_format(POApproval::sum_detail_doc_qty($po_num),0);
     

     $db_sign = DB::connection('sqlsrv5')->table('f_po_approval_status()')
     ->where('po_num', '=', "$po_num")  
     ->get();  
     $Checked = 'Sect. Head' ;
  
     if($db_sign->count() > 0){ 
     foreach($db_sign as $db){  
        $IDUser2 = $db->user_id2 ;
        $Checked = $db->user2 ; 
        $Approval = $db->user3 ;
        $Legalized = $db->user4 ;
        $Vendor = 'Please Re-email' ; 
        $UserName	= $db->user1 ;
        $Sign_1 = ''; $Created = $db->user1 ;
        $Sign_2 = ''; $Checked = $db->user2 ;
        $Sign_3 = ''; $Approval = $db->user3 ;
        $Sign_4 = ''; $Legalized = $db->user4 ;
    
        
        $IsCompleted = 1 ;  
        $LastUpdate1 = ($db->last_update1 == '' ? 'PENDING' : $db->last_update1) ;
        $LastUpdate2 = ($db->last_update2 == '' ? 'PENDING' : $db->last_update2) ;
        $LastUpdate3 = ($db->last_update3 == '' ? 'PENDING' : $db->last_update3) ;  
        $LastUpdate4 = ($db->last_update4 == '' ? 'PENDING' : $db->last_update4) ;  
     } }else{ 
       
     $Sign_1 = '' ;
     $Sign_2 = '' ;
     $Sign_3 = '' ; 
     $Sign_4 = '' ;
     $IDUser2 = NULL ;
     $UserName	= $UserName ;
     $Created = $UserName ;
     $Checked = 'Sect. Head' ; 
     $Approval = '' ;
     $Legalized = '' ;
     $Vendor = 'Please Re-email' ; 
     $IsCompleted = 0 ; 
     $LastUpdate1 = 'PENDING' ;
     $LastUpdate2 = 'PENDING' ;
     $LastUpdate3 = 'PENDING' ; 
     $LastUpdate4 = 'PENDING' ;

     
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
     
     $header = array('No.', 'Product Name', 'Dlv. Date', 'Qty', 'UoM', 'Price', 'Amount');
 
     $this->draw_print_main_header($header, $DocNum, $DocDate, $PartnerName, $AddressCompany, $UserName, $PICName, $DocType, $SubTotal, $Discount, $Ppn, $Total, $TOP, $Status, $Rev, $Approval, $Legalized, $Currency, $Description, $Vendor, $Sign_1, $Sign_2, $Sign_3, $Sign_4, $IsCompleted, $LastUpdate1, $LastUpdate2, $LastUpdate3, $LastUpdate4, $sum, $CompanyName, $Logo, $DocRegNum, $City, $Province, $PostalCode, $State, $Phone, $Fax, $ProjectName, $PartnerCode, $Checked, $IDUser2, $doc_status, $po_num, $print_option,$AddressHtml,$PartnerAddressHtml, $status_draft, $BuyerID); 
     
     PDF::Output($judul.'.pdf');
    }
      
}
