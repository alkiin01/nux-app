<?php

namespace App\Http\Controllers;

use App\Models\PRApproval;
use App\Models\AppModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
use App\Jobs\SendApprovalEmailJob;
use PDF;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PRApprovalController extends Controller
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
        $approval_url = env('APP_URL')."/pr_approval?ref_doc=".$approval_url ;   
        $document_id = "PR Num ".$document_id ;   
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
        // List Section
        $data['section_list'] = PRApproval::get_section_list();
        $data['head_title'] = $menu['head_title'];
        $data['menu_level_1'] = $menu['menu_level_1'];
        $data['menu_level_2'] = $menu['menu_level_2'];
        $data['menu_level_3'] = $menu['menu_level_3'];
        $data['menu_level_4'] = $menu['menu_level_4'];
        $data['doc_access_list'] = DB::table('t100_user_doc_access')->where('user_id', $my_id)->get();
        return view('pr_approval/pr_approval_index', $data);
    }

    public function get_attachment_list(Request $request)
    {
        $str_req = explode("_", $request->trc_unix_id);
        $str = explode("_", Crypt::decryptString(str_replace("-", "=", $str_req[0])));
        $pr_num = $str[0];
        $data['list'] = PRApproval::get_attachment_list($pr_num);
        $data['count'] = PRApproval::get_attachment_list($pr_num)->count();
        return view('pr_approval/attachment_list', $data);
    }

    public function get_comment_list(Request $request)
    {
        $str_req = explode("_", $request->trc_unix_id);
        $str = explode("_", Crypt::decryptString(str_replace("-", "=", $str_req[0])));
        $pr_num = $str[0];
        $data['list'] = PRApproval::get_comment_list($pr_num);
        $data['count'] = PRApproval::get_comment_list($pr_num)->count();
        return view('pr_approval/comment_list', $data);
    }

    public function sent_comment(Request $request)
    {
        $str_req = explode("_", $request->trc_unix_id);
        $str = explode("_", Crypt::decryptString(str_replace("-", "=", $str_req[0])));
        $pr_num = $str[0];
        $comment = $request->comment;
        $my_username = Auth::user()->username;
        $my_fullname = Auth::user()->full_name;
        $insert_db = PRApproval::sent_comment($pr_num, $my_username, $my_fullname, $comment);
        if ($insert_db) {
            $data['status'] = 1;
            $data['desc'] = 'Success sent comment';
        } else {
            $data['status'] = 0;
            $data['desc'] = 'Fail sent comment';
        }
        echo json_encode($data);
    }

    public function get_count_document(Request $request)
    {
        $section_id = $request->section_id;
        $data['total_check'] = PRApproval::get_count_document_check($section_id);
        $data['total_approve'] = PRApproval::get_count_document_approve($section_id);
        $data['total_legal'] = PRApproval::get_count_document_legal($section_id);
        $data['total_document'] = PRApproval::get_count_document($section_id);
        echo json_encode($data);
    }

    public function front_table(Request $request)
    {
        $status_id = $request->status_id;
        $section_id = $request->section_id;
        $search = $request->front_table_search;
        // dd($status_id);
        $columns = array(
            0 => 'pr_num',
            1 => 'pr_num',
            2 => 'docdate',
            3 => 'amount',
            4 => 'status_checker',
            5 => 'status_approver',
            6 => 'status_legalizer',
        );

        $baseQuery = PRApproval::get_transaction_list($search, $status_id, $section_id);
        $totalData = (clone $baseQuery)->count();
        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = ($request->input('order.0.column') == 0 ? $columns[1] : $columns[$request->input('order.0.column')]);
        $dir = ($request->input('order.0.column') == 0 ? 'desc' : $request->input('order.0.dir'));
        $posts = (clone $baseQuery)
            ->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        $my_username = Auth::user()->username;
        $data = array();
        if (!empty($posts)) {
            $no = $start;

            foreach ($posts as $post) {
                $no++;
                $trc_id = Crypt::encryptString($post->pr_num);
                $sys_id = "'" . str_replace("=", "-", $trc_id) . '_' . $no . "'";

                $button = '
        <span class="svg-icon svg-icon-primary svg-icon-2x" style="cursor: pointer;" id="' . $trc_id . '"  onclick="document_preview(' . $sys_id . ') ;">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="black"></path><path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="black"></path><path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="black"></path></svg>
        </span>';

                // if ($post->user_id2 == Auth::user()->username && ($post->status_checker == 'P')) {
                //     $docnum = '<span class="bg-light-warning">'.($post->docnum != '' ? $post->docnum."-" : '' ).$post->pr_num.'</span>' ;
                // } else if ($post->user_id3 == Auth::user()->username && $post->status_approver == 'P'  && $post->status_checker == 'A') {
                //     $docnum = '<span class="bg-light-warning">'.($post->docnum != '' ? $post->docnum."-" : '' ).$post->pr_num.'</span>' ;
                // } else if ($post->user_id4 == Auth::user()->username && $post->status_legalizer == 'P'  && $post->status_approver == 'A') {
                //     $docnum = '<span class="bg-light-warning">'.($post->docnum != '' ? $post->docnum."-" : '' ).$post->pr_num.'</span>' ;
                // } else {
                //     $docnum = '<span class="">'.($post->docnum != '' ? $post->docnum."-" : '' ).$post->pr_num.'</span>' ;
                // }

                if ($post->user_id2 == Auth::user()->username && ($post->status_checker == 'P')) {
                    $docnum = '<span class="bg-light-warning">' . $post->pr_num . '</span>';
                } else if ($post->user_id3 == Auth::user()->username && $post->status_approver == 'P'  && $post->status_checker == 'A') {
                    $docnum = '<span class="bg-light-warning">' . $post->pr_num . '</span>';
                } else if ($post->user_id4 == Auth::user()->username && $post->status_legalizer == 'P'  && $post->status_approver == 'A') {
                    $docnum = '<span class="bg-light-warning">' . $post->pr_num . '</span>';
                } else {
                    $docnum = '<span class="">' . $post->pr_num . '</span>';
                }

                if ($post->user_id2 == '') {
                    $status_checker = '';
                } else if ($post->status_checker == '' || $post->status_checker == 'P') {
                    $status_checker = '<span class="btn btn-sm btn-primary text-xs">waiting</span>';
                } else if ($post->status_checker == 'R') {
                    $status_checker = '<span class="btn btn-sm btn-danger text-xs">Reject</span>';
                } else {
                    $status_checker = '<span class="btn btn-sm btn-success text-xs">Completed</span>';
                }
                if ($post->status_approver == '' || $post->status_approver == 'P') {
                    $status_approver = '<span class="btn btn-sm btn-primary text-xs">waiting</span>';
                } else if ($post->status_approver == 'R') {
                    $status_approver = '<span class="btn btn-sm btn-danger text-xs">Reject</span>';
                } else {
                    $status_approver = '<span class="btn btn-sm btn-success text-xs">Completed</span>';
                }
                if ($post->status_legalizer == '' || $post->status_legalizer == 'P') {
                    $status_legalizer = '<span class="btn btn-sm btn-primary text-xs">waiting</span>';
                } else if ($post->status_legalizer == 'R') {
                    $status_legalizer = '<span class="btn btn-sm btn-danger text-xs">Reject</span>';
                } else {
                    $status_legalizer = '<span class="btn btn-sm btn-success text-xs">Completed</span>';
                }

                $nestedData['no'] = $no;
                $nestedData['prnum'] = $docnum;
                $nestedData['docnum'] = $post->docnum;
                $nestedData['docdate'] = AppModel::local_date_formate_name(substr($post->docdate, 0, 10));
                $nestedData['remark'] = '';
                $nestedData['amount'] = number_format($post->amount, 2);
                $nestedData['check'] = $status_checker;
                $nestedData['approve'] = $status_approver;
                $nestedData['legal'] = $status_legalizer;
                $nestedData['action'] = $button;
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

    public function export_front_table(Request $request)
    {
        // $str = explode("_", $request->flow_id) ;
        // $flow_id = $str[0] ;
        // $trc_type_id = $str[1] ;
        // $position = ($request->position == 0 ? 1 : $request->position) ;  
        $vendor_id = Auth::user()->partner_id;
        $search = $request->front_table_search;
        date_default_timezone_set('Asia/Jakarta');
        $yearX = substr(date('Y'), 2, 2) . date('m');
        $year = ($request->range_date === null ? $yearX : $request->range_date);
        $data['full_name'] = Auth::user()->full_name;
        $data['list'] = PRApproval::get_transaction_list($year, $search, $vendor_id)->get();
        $data['num'] = PRApproval::get_transaction_list($year, $search, $vendor_id)->count();
        $data['ref_form'] = '';
        return view('pr_approval.po_export', $data);
    }

    public function show(Request $request)
    {
        $str_req = explode("_", $request->trc_unix_id);
        $str = explode("_", Crypt::decryptString(str_replace("-", "=", $str_req[0])));
        $sys_id = "'" . $request->trc_unix_id . "'";
        $PONum = $str[0];
        $data_detail = PRApproval::data_detail($PONum);
        foreach ($data_detail as $db) {
            $sys_id = Crypt::encryptString($db->PONum . '_0');
            $sys_id = str_replace("=", "-", $sys_id) . '_1';
            $data['trc_unix_id'] =  $sys_id;
            $data['ref_form'] = '';
            $data['ref_tab'] = 1;
        }
        return view('pr_approval.po_preview', $data);
    }

    function print_view(Request $request)
    {
        $data['trc_unix_id'] = $request->trc_unix_id;
        $data['ref_form'] = $request->ref_form;
        return view('pr_approval.pr_direct_print', $data);
    }

    function get_button_approve(Request $request)
    {
        $data['trc_unix_id'] = $request->trc_unix_id;
        $sys_id = "'" . $request->trc_unix_id . "'";

        $str_req = explode("_", $request->trc_unix_id);
        $str = explode("_", Crypt::decryptString(str_replace("-", "=", $str_req[0])));
        $pr_num = $str[0];
        $my_username = Auth::user()->username;
        $db_section_id = PRApproval::data_detail($pr_num);
        $section_id = '';
        foreach ($db_section_id as $row) {
            $section_id = $row->ReqCategory_c;
        }
        $check_buyer = PRApproval::check_access_req_action($pr_num, $my_username, $section_id);
        if ($check_buyer > 0) {
            $data['button_approve'] = '<button class="btn btn-primary btn-sm text-sm" style="width: 100px;" onclick="getApprovalForm(' . $sys_id . ')">Approve</button>';
        }
        echo json_encode($data);
    }

    public function show_attachment(Request $request)
    { 
        $client = new Client();
        $data = [] ;
        $username = Auth::user()->username;
        $password = Crypt::decryptString(Auth::user()->epicor_password);
        $seqNum = $request->seqNum;
        $host_api = self::get_host_api();
        try {
            $response = $client->request('POST', $host_api . 'Attachment', [
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
        return $data;
    }

    public function submit_approval(Request $request)
    {

        $client = new Client();
        $data = [];
        $host_api = self::get_host_api();
        $username = Auth::user()->username;
        $my_name = Auth::user()->full_name;
        $password = Crypt::decryptString(Auth::user()->epicor_password);
        $str_req = explode("_", $request->trc_unix_id);
        $str = explode("_", Crypt::decryptString(str_replace("-", "=", $str_req[0])));
        $pr_num = $str[0];
        $approve_msg =  $request->approve_msg;
        $approve_sts =  $request->approve_sts;
        $db_pr = PRApproval::data_detail($pr_num);
        $section_id = '';
        foreach ($db_pr as $row) {
            $section_id = $row->ReqCategory_c;
        }
        $db_sequence_approval = PRApproval::get_sequence_approval($pr_num, $section_id);
        $req_action_id = '';
        $next_req_action_id = '';
        $next_user_id = '';
        
        foreach ($db_sequence_approval as $row) { 
            $user_id1 = $row->user_id1 ;
            if ($row->status_checker == 'P') {
                if ($approve_sts == 'A') {
                    $req_action_id = $row->req_action_id2;
                    $next_req_action_id = $row->req_action_id3;
                    $next_user_id = $row->user_id3;
                } else {
                    $req_action_id = $row->req_action_id2 ;
                    $next_req_action_id = " " ;
                    $next_user_id = $row->user_id1 ;
                }
            } else if ($row->status_approver == 'P') {
                if ($approve_sts == 'A') {
                    $req_action_id = $row->req_action_id3;
                    $next_req_action_id = $row->req_action_id4;
                    $next_user_id = $row->user_id4;
                } else {
                    $req_action_id = $row->req_action_id3 ;
                    $next_req_action_id = " " ;
                    $next_user_id = $row->user_id1 ;
                }
            } else if ($row->status_legalizer == 'P') {
                if ($approve_sts == 'A') {
                    $req_action_id = $row->req_action_id4;
                    $next_req_action_id = 'PUR';
                    $next_user_id = '';
                } else {
                    $req_action_id = $row->req_action_id4 ;
                    $next_req_action_id = " " ;
                    $next_user_id = $row->user_id1 ;
                }
            } 
        } 
        
        if ($req_action_id != '') {
            $req_action = PRApproval::get_req_action_name($req_action_id);
            // dd($pr_num, $approve_sts, $req_action_id, $req_action, $username, $my_name, $next_req_action_id, $next_user_id, $approve_msg);
            if ($req_action != '') {
                try {
                    $response = $client->request('POST', $host_api . 'PR/ApprovePR', [
                        'json' => [
                            "reqNum" => $pr_num,
                            "replyOption" => "$approve_sts",
                            "reqActionID" => "$req_action_id",
                            "reqActionIDReqActionDesc" => "$req_action",
                            "currDispatcherID" => "$username",
                            "currDispatcherName" => "$my_name",
                            "nextActionID" => "$next_req_action_id",
                            "nextDispatcherID" => "$next_user_id",
                            "approveMsg"  => "$approve_msg",
                            "nik" => "$username",
                            "password" => "$password",
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
            } else {
                $data['code'] = 500;
                $data['desc'] = "Requisition Description Not Found";
            }
        } else {
            $data['code'] = 500;
            $data['desc'] = "Requisition Description Not Found";
        }

        if ($data['code'] == 200) {
            if ($next_req_action_id != 'PUR') { 
                $template = "You have a document (PR Num : **".$pr_num."**) that requires your approval." ;
                $next_user = explode("~", PRApproval::get_email_by_username($next_user_id));
                $email_next_user = $next_user[0] ;
                $name_next_user = $next_user[1] ; 
                $cc = '' ;  
                self::sendApprovalNotification($email_next_user, $name_next_user, $template, $request->trc_unix_id, $cc, $approve_msg);
            } else { 
                if($approve_sts == 'A') {
                    $template = "You have a document (PR Num : **".$pr_num."**) that requires your review." ;
                    $get_list_email_buyer = PRApproval::get_list_email_buyer();
                    $cc = [];  
                    if ($get_list_email_buyer->count() > 0) 
                    { 
                        foreach ($get_list_email_buyer as $email) {
                            $cc[] = $email->EMailAddress ;  
                        } 
                        $next_user = explode("~", PRApproval::get_email_by_username($user_id1));
                        $email_created = $next_user[0] ; 
                        $cc[] = $email_created ;  
                    }  
                    $email_next_user = 'system@summitadyawinsa.co.id' ;
                    $name_next_user = 'Epicor Notification' ; 

                    self::sendApprovalNotification($email_next_user, $name_next_user, $template, $request->trc_unix_id, $cc, $approve_msg);
                } else {
                    $template = "You have a document (PR Num : **".$pr_num."**) that requires your review." ;
                    $next_user = explode("~", PRApproval::get_email_by_username($user_id1));
                    $email_next_user = $next_user[0] ;
                    $name_next_user = $next_user[1] ; 
                    $cc = '' ; 
                    self::sendApprovalNotification($email_next_user, $name_next_user, $template, $request->trc_unix_id, $cc, $approve_msg);
                } 
            }
        }
        return $data;
    }

    public function draw_print_header($header, $DocNum, $Address, $DocType, $Rev, $CompanyName, $Logo, $City, $Province, $PostalCode, $State, $Phone, $Fax, $ProjectName, $PartnerCode, $doc_status, $DeptDesc)
    {

        $w_top = array(130, 61);
        $w_head = array(35, 5, 55, 32, 95, 191);
        $w_detail = array(15, 76, 25, 15, 35, 25);
        PDF::SetFont('courier', 'B', 9);
        PDF::Cell($w_top[0], 5, $CompanyName, '', 0, 'L', 0);
        PDF::Cell($w_top[1], 5, '', '', 0, 'L', 0);
        PDF::SetFont('courier', '', 9, '', false);
        PDF::Ln();
        PDF::Cell($w_top[0], 5, $Address, '', 0, 'L', 0);
        PDF::Cell($w_top[1], 5, '', '', 0, 'C', 0);

        PDF::Ln();
        PDF::Cell($w_top[0], 5, $City . ', ' . $Province . ' ' . $PostalCode, '', 0, 'L', 0);
        PDF::Cell($w_top[1], 5, '', '', 0, 'L', 0);

        PDF::Ln();
        PDF::Cell($w_top[0], 5, $State, '', 0, 'L', 0);
        PDF::Cell($w_top[1], 5, '', '', 0, 'L', 0);


        // $imgdata = base64_decode(substr(str_replace("-", "+", $Logo), 22));
        // PDF::Image('@' . $imgdata, 155, 12, 50, 13, '', '', '', false, 150, '', false, false, 1, false, false, true);

        if ($doc_status == 1) {
            $port = env('APP_URL');
            $url =  $port . 'dist/img/draft.png';
            $img = file_get_contents($url);
            $draft_image = base64_encode($img);
            $draft_image = base64_decode($draft_image);
            // PDF::Image('@' . $draft_image, 10, 40, 350, 230, '', '', '', false, 150, '', false, false, 1, false, false, true);
        }
        PDF::Ln();
        PDF::Cell($w_top[0], 5, 'Phone: ' . $Phone . ' Fax: ' . $Fax, '', 0, 'L', 0);
        PDF::SetFont('courier', 'B', 9);
        PDF::Cell($w_top[1], 5, 'PR ' . $DeptDesc, '', 0, 'R', 0);

        PDF::Ln();
        PDF::SetFont('courier', '', 9);
        PDF::Cell($w_head[0], 5, ' Document No.', 'LT', 0, 'L', 0);
        PDF::Cell($w_head[1], 5, ':', 'T', 0, 'L', 0);
        PDF::SetFont('courier', 'B', 9);
        PDF::Cell($w_head[2], 5, $DocNum . ' ' . $Rev, 'T', 0, 'L', 0);

        PDF::SetFont('courier', '', 9);
        PDF::Cell($w_head[3], 5, '', 'T', 0, 'C', 0);
        PDF::Cell($w_head[3], 5, '', 'T', 0, 'C', 0);
        PDF::Cell($w_head[3], 5, '', 'RT', 0, 'C', 0);

        PDF::Ln();
        PDF::SetFont('courier', '', 9);
        PDF::Cell($w_head[0], 5, ' Vendor', 'L', 0, 'L', 0);
        PDF::Cell($w_head[1], 5, ':', '', 0, 'L', 0);
        PDF::Cell($w_head[2], 5, $PartnerCode, '', 0, 'L', 0);

        PDF::Cell($w_head[3], 5, '', '', 0, 'C', 0);
        PDF::Cell($w_head[3], 5, '', '', 0, 'C', 0);
        PDF::Cell($w_head[3], 5, '', 'R', 0, 'C', 0);
        PDF::Ln();

        PDF::SetFont('courier', '', 9);
        PDF::Cell($w_head[0], 5, ' Project Name', 'L', 0, 'L', 0);
        PDF::Cell($w_head[1], 5, ':', '', 0, 'L', 0);
        PDF::Cell($w_head[2], 5, $ProjectName, '', 0, 'L', 0);

        PDF::Cell($w_head[3], 5, '', '', 0, 'C', 0);
        PDF::Cell($w_head[3], 5, '', '', 0, 'C', 0);
        PDF::Cell($w_head[3], 5, '', 'R', 0, 'C', 0);
        PDF::Ln();

        PDF::Cell($w_head[4], 5, '', 'LB', 0, 'L', 0);
        PDF::Cell($w_head[3], 5, '', 'B', 0, 'C', 0);
        PDF::Cell($w_head[3], 5, '', 'B', 0, 'C', 0);
        PDF::Cell($w_head[3], 5, '', 'RB', 0, 'C', 0);

        PDF::Ln();
        PDF::SetFont('courier', '', 2);
        PDF::Cell($w_head[5], 2, '', 'TB', 0, 'L', 0);

        PDF::Ln();
        PDF::SetFont('courier', '', 9);
        PDF::Cell($w_detail[0], 6, $header[0], 'TB', 0, 'L', 0);
        PDF::Cell($w_detail[1], 6, $header[1], 'TB', 0, 'L', 0);
        PDF::Cell($w_detail[2], 6, $header[2], 'TB', 0, 'R', 0);
        PDF::Cell($w_detail[3], 6, $header[3], 'TB', 0, 'L', 0);
        PDF::Cell($w_detail[5], 6, $header[6], 'TB', 0, 'R', 0);
        PDF::Cell($w_detail[4], 6, $header[4], 'TB', 0, 'R', 0);

        PDF::Ln();
        PDF::SetFillColor(224, 235, 255);
        PDF::SetTextColor(0);
        PDF::SetFont('');
    }

    public function draw_print_main_header($header, $DocNum, $DocDate, $PartnerName, $Address, $UserName, $PICName, $DocType, $SubTotal, $Discount, $Ppn, $Total, $TOP, $Status, $Rev, $Approval, $Legalized, $Currency, $Description, $Vendor, $Sign_1, $Sign_2, $Sign_3, $Sign_4, $IsCompleted, $LastUpdate1, $LastUpdate2, $LastUpdate3, $LastUpdate4, $sum, $CompanyName, $Logo, $DocRegNum, $City, $Province, $PostalCode, $State, $Phone, $Fax, $ProjectName, $PartnerCode, $Checked, $IDUser2, $doc_status, $pr_num, $print_option, $DeptDesc)
    {

        $w_top = array(130, 61);
        $w_head = array(35, 5, 55, 32, 95, 191);
        $w_detail = array(15, 76, 25, 15, 35, 25);

        PDF::SetFont('courier', 'B', 9);

        PDF::Cell($w_top[0], 5, $CompanyName, '', 0, 'L', 0);
        PDF::Cell($w_top[1], 5, '', '', 0, 'L', 0);
        PDF::SetFont('courier', '', 9, '', false);
        PDF::Ln();
        PDF::Cell($w_top[0], 5, $Address, '', 0, 'L', 0);
        PDF::Cell($w_top[1], 5, '', '', 0, 'C', 0);

        PDF::Ln();
        PDF::Cell($w_top[0], 5, $City . ', ' . $Province . ' ' . $PostalCode, '', 0, 'L', 0);
        PDF::Cell($w_top[1], 5, '', '', 0, 'L', 0);

        PDF::Ln();
        PDF::Cell($w_top[0], 5, $State, '', 0, 'L', 0);
        PDF::Cell($w_top[1], 5, '', '', 0, 'L', 0);
        // $imgdata = base64_decode(substr(str_replace("-", "+", $Logo), 22));
        // PDF::Image('@' . $imgdata, 155, 12, 50, 13, '', '', '', false, 150, '', false, false, 1, false, false, true);

        if ($doc_status == 1) {
            $port = env('APP_URL');
            $url =  $port . 'dist/img/draft.png';
            $img = file_get_contents($url);
            $draft_image = base64_encode($img);
            $draft_image = base64_decode($draft_image);
            // PDF::Image('@' . $draft_image, 10, 40, 350, 230, '', '', '', false, 150, '', false, false, 1, false, false, true);
        }

        PDF::Ln();
        PDF::Cell($w_top[0], 5, 'Phone: ' . $Phone . ' Fax: ' . $Fax, '', 0, 'L', 0);
        PDF::SetFont('courier', 'B', 9);
        PDF::Cell($w_top[1], 5, 'PR ' . $DeptDesc, '', 0, 'R', 0);

        PDF::Ln();
        PDF::SetFont('courier', '', 9);
        PDF::Cell($w_head[0], 5, ' Document No.', 'LT', 0, 'L', 0);
        PDF::Cell($w_head[1], 5, ':', 'T', 0, 'L', 0);
        PDF::SetFont('courier', 'B', 9);
        PDF::Cell($w_head[2], 5, $DocNum . ' ' . $pr_num, 'T', 0, 'L', 0);

        PDF::SetFont('courier', '', 9);
        PDF::Cell($w_head[3], 5, '', 'T', 0, 'C', 0);
        PDF::Cell($w_head[3], 5, '', 'T', 0, 'C', 0);
        PDF::Cell($w_head[3], 5, '', 'RT', 0, 'C', 0);

        PDF::Ln();
        PDF::SetFont('courier', '', 9);
        PDF::Cell($w_head[0], 5, ' Vendor', 'L', 0, 'L', 0);
        PDF::Cell($w_head[1], 5, ':', '', 0, 'L', 0);
        PDF::Cell($w_head[2], 5, $PartnerCode, '', 0, 'L', 0);

        PDF::Cell($w_head[3], 5, '', '', 0, 'C', 0);
        PDF::Cell($w_head[3], 5, '', '', 0, 'C', 0);
        PDF::Cell($w_head[3], 5, '', 'R', 0, 'C', 0);
        PDF::Ln();

        PDF::SetFont('courier', '', 9);
        PDF::Cell($w_head[0], 5, ' Project Name', 'L', 0, 'L', 0);
        PDF::Cell($w_head[1], 5, ':', '', 0, 'L', 0);
        PDF::Cell($w_head[2], 5, $ProjectName, '', 0, 'L', 0);

        PDF::Cell($w_head[3], 5, '', '', 0, 'C', 0);
        PDF::Cell($w_head[3], 5, '', '', 0, 'C', 0);
        PDF::Cell($w_head[3], 5, '', 'R', 0, 'C', 0);
        PDF::Ln();

        PDF::Cell($w_head[4], 5, '', 'LB', 0, 'L', 0);
        PDF::Cell($w_head[3], 5, '', 'B', 0, 'C', 0);
        PDF::Cell($w_head[3], 5, '', 'B', 0, 'C', 0);
        PDF::Cell($w_head[3], 5, '', 'RB', 0, 'C', 0);

        PDF::Ln();
        PDF::SetFont('courier', '', 2);
        PDF::Cell($w_head[5], 2, '', 'TB', 0, 'L', 0);

        PDF::Ln();
        PDF::SetFont('courier', '', 9);
        PDF::Cell($w_detail[0], 6, $header[0], 'TB', 0, 'L', 0);
        PDF::Cell($w_detail[1], 6, $header[1], 'TB', 0, 'L', 0);
        PDF::Cell($w_detail[2], 6, $header[2], 'TB', 0, 'R', 0);
        PDF::Cell($w_detail[3], 6, $header[3], 'TB', 0, 'L', 0);
        PDF::Cell($w_detail[5], 6, $header[6], 'TB', 0, 'R', 0);
        PDF::Cell($w_detail[4], 6, $header[4], 'TB', 0, 'R', 0);
        PDF::Ln();



        $DB = PRApproval::detail_list_data($pr_num);
        $num = $DB->count();

        $startX = 0;
        if ($num >= 19) {
            $jml_dataX = $num - 19;
            if ($jml_dataX == 0) {
                $limitX = 18;
            } else {
                $limitX = 19;
            }
        } elseif ($num > 13 && $num < 19) {
            $jml_bX = $num;
            $jml_dataX = $num - 13;
            if ($jml_dataX < 0) {
                $limitX = $jml_dataX;
            } else {
                $limitX = $jml_bX - 1;
            }
        } else {
            $jml_dataX = $num - 15;
            $limitX = $num;
        }
        $all_page = PRApproval::load_data_print_page($pr_num, $Status, $print_option, $startX, $limitX);
        $sum_price = PRApproval::summary_ammount($pr_num);



        foreach ($all_page as $row) {
            $num_pages = PDF::getNumPages();
            PDF::startTransaction();
            PDF::SetFont('courier', '', 7.5);
            PDF::SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
            // $sum_price_total = ($row[8] * $row[9]);
            $sum_price_total = ($row[8] * $row[12]);
            PDF::Cell($w_detail[0], 5, $row[0], '', 0, 'L', 0);
            PDF::Cell($w_detail[1], 5, $row[11], '', 0, 'L', 0);
            PDF::Cell($w_detail[2], 5, $row[2], '', 0, 'R', 0);
            PDF::Cell($w_detail[3], 5, $row[3], '', 0, 'L', 0);
            PDF::Cell($w_detail[5], 5, number_format($sum_price_total, 0), '', 0, 'R', 0);
            PDF::Cell($w_detail[4], 5, $row[4], '', 0, 'R', 0);

            PDF::Ln(5);

            PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 2, 'color' => array(128, 128, 128)));
            PDF::Cell($w_detail[0], 5, '', 'B', 0, 'L', 0);
            PDF::Cell($w_detail[1], 5, $row[10], 'B', 0, 'L', 0);
            if ($row[6] != $row[3]) {
                PDF::Cell($w_detail[2], 5, $row[5], 'B', 0, 'R', 0);
                PDF::Cell($w_detail[3], 5, $row[6], 'B', 0, 'L', 0);
            } else {
                PDF::Cell($w_detail[2], 5, '', 'B', 0, 'R', 0);
                PDF::Cell($w_detail[3], 5, '', 'B', 0, 'L', 0);
            }

            PDF::Cell($w_detail[5], 5, '', 'B', 0, 'L', 0);
            PDF::Cell($w_detail[4], 5, '', 'B', 0, 'R', 0);
            PDF::Ln();
            //  $sum_price = $sum_price + $sum_price_total ;
        }

        if ($num <= 13) {
            PDF::Cell(91, 7, 'Total :', 'TB', 0, 'C');
            PDF::Cell(25, 7, $sum, 'TB', 0, 'R');
            PDF::Cell(15, 7, '', 'TB', 0, 'R');
            PDF::Cell(25, 7, number_format($sum_price, 0), 'TB', 0, 'R');
            PDF::Cell(35, 7, '', 'TB', 0, 'R');
            PDF::Ln();
            PDF::SetFont('courier', '', 8, '', 'false');
            PDF::Cell(191, 7, 'Description :', '', 0, 'L');
            PDF::Ln();
            PDF::writeHTML($Description, true, false, true, false, '');
            $num_row = 95 + ($limitX  * 10);

            PDF::Ln(10);
            //    if($IsCompleted==1){ PDF::Image('assets/images/sign/company_stamp.png', 140, $num_row, 40, 17, 'PNG', '', '', false, 150, '', false, false, 1, false, false, true); }
            PDF::Cell(5, 6, '', '', 0, 'C');
            PDF::Cell(38, 4, 'Prepared By', '', 0, 'C');
            PDF::Cell(10, 4, '', '', 0, 'C');
            if (!is_null($IDUser2)) {
                PDF::Cell(38, 4, 'Checked By', '', 0, 'C');
            } else {
                PDF::Cell(38, 4, '', '', 0, 'C');
            }
            PDF::Cell(10, 4, '', '', 0, 'C');
            PDF::Cell(38, 4, 'Approved By', '', 0, 'C');
            PDF::Cell(10, 4, '', '', 0, 'C');
            PDF::Cell(38, 4, 'Legalized by', '', 0, 'C');


            PDF::Ln();
            PDF::SetFont('courier', 'I', 8);

            PDF::Cell(5, 6, '', '', 0, 'C');
            PDF::Cell(38, 4, $UserName, '', 0, 'C');
            PDF::Cell(10, 4, '', '', 0, 'C');
            if (!is_null($IDUser2)) {
                PDF::Cell(38, 4, $Checked, '', 0, 'C');
            } else {
                PDF::Cell(38, 4, '', '', 0, 'C');
            }
            PDF::Cell(10, 4, '', '', 0, 'C');
            PDF::Cell(38, 4, $Approval, '', 0, 'C');
            PDF::Cell(10, 4, '', '', 0, 'C');
            PDF::Cell(38, 4, $Legalized, '', 0, 'C');
            PDF::Cell(10, 4, '', '', 0, 'C');
            PDF::Ln();

            PDF::SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
            //    if(!empty($Sign_1)){  
            //    $imgdata_sign_1 = base64_decode(substr(str_replace("-","+", $Sign_1),22)) ;  
            //    PDF::Image('@'.$imgdata_sign_1, 17, $num_row, 40, 17, '', '', '', false, 150, '', false, false, 1, false, false, true); 
            //    } 
            PDF::Cell(5, 6, '', '', 0, 'C');
            PDF::Cell(38, 6, $LastUpdate1, 'T', 0, 'C');
            PDF::Cell(10, 6, '', '', 0, 'C');

            if (!is_null($IDUser2)) {
                //    if(!empty($Sign_2)){ 
                //    $imgdata_sign_2 = base64_decode(substr(str_replace("-","+", $Sign_2),22)) ;
                //    PDF::Image('@'.$imgdata_sign_2, 65, $num_row, 40, 17, '', '', '', false, 150, '', false, false, 1, false, false, true); 
                //    }
                PDF::Cell(38, 6, $LastUpdate2, 'T', 0, 'C');
            } else {
                PDF::Cell(38, 6, '', '', 0, 'C');
            }
 
            PDF::Cell(10, 6, '', '', 0, 'C');
            //   if(!empty($Sign_3)){ 
            //   $imgdata_sign_3 = base64_decode(substr(str_replace("-","+", $Sign_3),22)) ;
            //   PDF::Image('@'.$imgdata_sign_3, 113, $num_row, 40, 17, '', '', '', false, 150, '', false, false, 1, false, false, true);  
            //   }

            PDF::Cell(38, 6, $LastUpdate3, 'T', 0, 'C');

            PDF::Cell(10, 6, '', '', 0, 'C');
            //    if(!empty($Sign_4)){ 
            //       $imgdata_sign_4 = base64_decode(substr(str_replace("-","+", $Sign_4),22)) ;
            //       PDF::Image('@'.$imgdata_sign_4, 161, $num_row, 40, 17, '', '', '', false, 150, '', false, false, 1, false, false, true);   
            //    }
            PDF::Cell(38, 6, $LastUpdate4, 'T', 0, 'C');
            PDF::Ln();

            PDF::SetY(-15);
            PDF::SetFont('courier', 'I', 8);
            PDF::SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
            PDF::Cell(100, 6, $DocRegNum, 'T', 0, 'L');
            PDF::Cell(81, 6, '', 'T', 0, 'L');
            PDF::Cell(10, 6, PDF::getAliasNumPage() . '/' . PDF::getAliasNbPages(), 'T', 0, 'L');
        } else {
            PDF::SetY(-15);
            PDF::SetFont('courier', 'I', 8);
            PDF::SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
            PDF::Cell(100, 6, $DocRegNum, 'T', 0, 'L');
            PDF::Cell(81, 6, '', 'T', 0, 'L');
            PDF::Cell(10, 6, PDF::getAliasNumPage() . '/' . PDF::getAliasNbPages(), 'T', 0, 'L');

            PDF::AddPage();
            $this->draw_print_header($header, $DocNum, $Address, $DocType, $Rev, $CompanyName, $Logo, $City, $Province, $PostalCode, $State, $Phone, $Fax, $ProjectName, $PartnerCode, $doc_status, $pr_num, $DeptDesc);
        }


        $jml_data = $num;
        $page = 1;
        $limit = 1;
        $start = 0;

        while ($jml_data >= 0) {

            if ($jml_data >= 19) {
                $jml_data = $jml_data - 19;
                if ($jml_data == 0) {
                    $limit = 18;
                    // $start = $limit * ($page - 1) ;
                    $start = 19 * ($page - 1);
                    $jml_data = $jml_data + 1;
                } else {
                    $limit = 19;
                    $start = $limit * ($page - 1);
                }
            } else if ($jml_data > 13 && $jml_data < 19) {
                $jml_b = $jml_data;
                $jml_data = $jml_data - 14;
                if ($jml_data < 0) {
                    $start = $limit * ($page - 1);
                    $limit = $jml_data;
                } else {
                    $start = $limit * ($page - 1);
                    $limit = $jml_b - 1;
                    $jml_data = 1;
                }
            } else {
                $start = $start + $limit;
                $limit = $jml_data;
                $jml_data = $jml_data - 14;
            }
            $all_page = PRApproval::load_data_print_page($pr_num, $Status, $print_option, $start, $limit);

            if ($page != 1 && $jml_data > 0) {
                // $sum_price = 0 ;
                foreach ($all_page as $row) {
                    $num_pages = PDF::getNumPages();
                    PDF::startTransaction();
                    PDF::SetFont('courier', '', 7.5);
                    PDF::SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));

                    $sum_price_total = ($row[8] * $row[12]);
                    PDF::Cell($w_detail[0], 5, $row[0], '', 0, 'L', 0);
                    PDF::Cell($w_detail[1], 5, $row[11], '', 0, 'L', 0);
                    PDF::Cell($w_detail[2], 5, $row[2], '', 0, 'R', 0);
                    PDF::Cell($w_detail[3], 5, $row[6], '', 0, 'L', 0);
                    PDF::Cell($w_detail[5], 5, number_format($sum_price_total, 0), '', 0, 'R', 0);
                    PDF::Cell($w_detail[4], 5, $row[4], '', 0, 'R', 0);

                    PDF::Ln(5);

                    PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 2, 'color' => array(128, 128, 128)));
                    PDF::Cell($w_detail[0], 5, '', 'B', 0, 'L', 0);
                    PDF::Cell($w_detail[1], 5, $row[10], 'B', 0, 'L', 0);
                    if ($row[6] != $row[3]) {
                        PDF::Cell($w_detail[2], 5, $row[5], 'B', 0, 'R', 0);
                        PDF::Cell($w_detail[3], 5, $row[3], 'B', 0, 'L', 0);
                    } else {
                        PDF::Cell($w_detail[2], 5, '', 'B', 0, 'R', 0);
                        PDF::Cell($w_detail[3], 5, '', 'B', 0, 'L', 0);
                    }

                    PDF::Cell($w_detail[5], 5, '', 'B', 0, 'L', 0);
                    PDF::Cell($w_detail[4], 5, '', 'B', 0, 'R', 0);
                    PDF::Ln();
                    //  $sum_price = $sum_price + $sum_price_total ;
                }

                PDF::SetY(-15);
                PDF::SetFont('courier', 'I', 8);
                PDF::SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
                PDF::Cell(100, 6, $DocRegNum, 'T', 0, 'L');
                PDF::Cell(81, 6, '', 'T', 0, 'L');
                PDF::Cell(10, 6, PDF::getAliasNumPage() . '/' . PDF::getAliasNbPages(), 'T', 0, 'L');

                if ($jml_data <= 0) {
                    PDF::Cell(91, 7, 'Total :', 'TB', 0, 'C');
                    PDF::Cell(25, 7, $sum, 'TB', 0, 'R');
                    PDF::Cell(15, 7, '', 'TB', 0, 'R');
                    PDF::Cell(25, 7, number_format($sum_price, 0), 'TB', 0, 'R');
                    PDF::Cell(35, 7, '', 'TB', 0, 'R');
                    PDF::Ln();
                    PDF::SetFont('courier', '', 8, '', 'false');
                    PDF::Cell(191, 7, 'Description :', '', 0, 'L');
                    PDF::Ln();
                    PDF::writeHTML($Description, true, false, true, false, '');
                } else {
                    PDF::AddPage();
                    $this->draw_print_header($header, $DocNum, $Address, $DocType, $Rev, $CompanyName, $Logo, $City, $Province, $PostalCode, $State, $Phone, $Fax, $ProjectName, $PartnerCode, $doc_status, $pr_num, $DeptDesc);
                }
            }

            if ($page != 1 && $jml_data <= 0) {
                foreach ($all_page as $row) {
                    $num_pages = PDF::getNumPages();
                    PDF::startTransaction();
                    PDF::SetFont('courier', '', 7.5);
                    PDF::SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));

                    // $sum_price_total = ($row[8] * $row[9]);
                    $sum_price_total = ($row[8] * $row[12]);
                    PDF::Cell($w_detail[0], 5, $row[0], '', 0, 'L', 0);
                    PDF::Cell($w_detail[1], 5, $row[11], '', 0, 'L', 0);
                    PDF::Cell($w_detail[2], 5, $row[2], '', 0, 'R', 0);
                    PDF::Cell($w_detail[3], 5, $row[6], '', 0, 'L', 0);
                    PDF::Cell($w_detail[5], 5, number_format($sum_price_total, 0), '', 0, 'R', 0);
                    PDF::Cell($w_detail[4], 5, $row[4], '', 0, 'R', 0);

                    PDF::Ln(5);

                    PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 2, 'color' => array(128, 128, 128)));
                    PDF::Cell($w_detail[0], 5, '', 'B', 0, 'L', 0);
                    PDF::Cell($w_detail[1], 5, $row[10], 'B', 0, 'L', 0);
                    if ($row[6] != $row[3]) {
                        PDF::Cell($w_detail[2], 5, $row[5], 'B', 0, 'R', 0);
                        PDF::Cell($w_detail[3], 5, $row[3], 'B', 0, 'L', 0);
                    } else {
                        PDF::Cell($w_detail[2], 5, '', 'B', 0, 'R', 0);
                        PDF::Cell($w_detail[3], 5, '', 'B', 0, 'L', 0);
                    }

                    PDF::Cell($w_detail[5], 5, '', 'B', 0, 'L', 0);
                    PDF::Cell($w_detail[4], 5, '', 'B', 0, 'R', 0);
                    PDF::Ln();
                    //  $sum_price = $sum_price + $sum_price_total ;
                }

                PDF::Cell(91, 7, 'Total Qty :', 'TB', 0, 'C');
                PDF::Cell(25, 7, $sum, 'TB', 0, 'R');
                PDF::Cell(15, 7, '', 'TB', 0, 'R');
                PDF::Cell(25, 7, number_format($sum_price, 0), 'TB', 0, 'R');
                PDF::Cell(35, 7, '', 'TB', 0, 'R');
                PDF::Ln();
                PDF::SetFont('courier', '', 8, '', 'false');
                PDF::Cell(191, 7, 'Description :', '', 0, 'L');
                PDF::Ln();
                PDF::writeHTML($Description, true, false, true, false, '');

                $num_row = 95 + ($limit * 10);

                PDF::Ln(10);
                //    if($IsCompleted==1){ PDF::Image('assets/images/sign/company_stamp.png', 140, $num_row, 40, 17, 'PNG', '', '', false, 150, '', false, false, 1, false, false, true); }
                PDF::Cell(5, 6, '', '', 0, 'C');
                PDF::Cell(38, 4, 'Prepared By', '', 0, 'C');
                PDF::Cell(10, 4, '', '', 0, 'C');
                if (!is_null($IDUser2)) {
                    PDF::Cell(38, 4, 'Checked By', '', 0, 'C');
                } else {
                    PDF::Cell(38, 4, '', '', 0, 'C');
                }
                PDF::Cell(10, 4, '', '', 0, 'C');
                PDF::Cell(38, 4, 'Approved By', '', 0, 'C');
                PDF::Cell(10, 4, '', '', 0, 'C');
                PDF::Cell(38, 4, 'Legalized by', '', 0, 'C');


                PDF::Ln();
                PDF::SetFont('courier', 'I', 8);

                PDF::Cell(5, 6, '', '', 0, 'C');
                PDF::Cell(38, 4, $UserName, '', 0, 'C');
                PDF::Cell(10, 4, '', '', 0, 'C');
                if (!is_null($IDUser2)) {
                    PDF::Cell(38, 4, $Checked, '', 0, 'C');
                } else {
                    PDF::Cell(38, 4, '', '', 0, 'C');
                }
                PDF::Cell(10, 4, '', '', 0, 'C');
                PDF::Cell(38, 4, $Approval, '', 0, 'C');
                PDF::Cell(10, 4, '', '', 0, 'C');
                PDF::Cell(38, 4, $Legalized, '', 0, 'C');
                PDF::Cell(10, 4, '', '', 0, 'C');
                PDF::Ln();

                PDF::SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
                //    if(!empty($Sign_1)){  
                //    $imgdata_sign_1 = base64_decode(substr(str_replace("-","+", $Sign_1),22)) ;  
                //    PDF::Image('@'.$imgdata_sign_1, 17, $num_row, 40, 17, '', '', '', false, 150, '', false, false, 1, false, false, true); 
                //    } 
                PDF::Cell(5, 6, '', '', 0, 'C');
                PDF::Cell(38, 6, $LastUpdate1, 'T', 0, 'C');
                PDF::Cell(10, 6, '', '', 0, 'C');

                if (!is_null($IDUser2)) {
                    //    if(!empty($Sign_2)){ 
                    //    $imgdata_sign_2 = base64_decode(substr(str_replace("-","+", $Sign_2),22)) ;
                    //    PDF::Image('@'.$imgdata_sign_2, 65, $num_row, 40, 17, '', '', '', false, 150, '', false, false, 1, false, false, true); 
                    //    }
                    PDF::Cell(38, 6, $LastUpdate2, 'T', 0, 'C');
                } else {
                    PDF::Cell(38, 6, '', '', 0, 'C');
                }


                PDF::Cell(10, 6, '', '', 0, 'C');
                //   if(!empty($Sign_3)){ 
                //   $imgdata_sign_3 = base64_decode(substr(str_replace("-","+", $Sign_3),22)) ;
                //   PDF::Image('@'.$imgdata_sign_3, 113, $num_row, 40, 17, '', '', '', false, 150, '', false, false, 1, false, false, true);  
                //   }

                PDF::Cell(38, 6, $LastUpdate3, 'T', 0, 'C');

                PDF::Cell(10, 6, '', '', 0, 'C');
                //    if(!empty($Sign_4)){ 
                //       $imgdata_sign_4 = base64_decode(substr(str_replace("-","+", $Sign_4),22)) ;
                //       PDF::Image('@'.$imgdata_sign_4, 161, $num_row, 40, 17, '', '', '', false, 150, '', false, false, 1, false, false, true);   
                //    }
                PDF::Cell(38, 6, $LastUpdate4, 'T', 0, 'C');
                PDF::Ln();

                PDF::SetY(-15);
                PDF::SetFont('courier', 'I', 8);
                PDF::SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
                PDF::Cell(100, 6, $DocRegNum, 'T', 0, 'L');
                PDF::Cell(81, 6, '', 'T', 0, 'L');
                PDF::Cell(10, 6, PDF::getAliasNumPage() . '/' . PDF::getAliasNbPages(), 'T', 0, 'L');
            }

            $page++;
        }
    }

    public function file_print(Request $request)
    {
        $str_req = explode("_", $request->trc_unix_id);

        $str = explode("_", Crypt::decryptString(str_replace("-", "=", $str_req[0])));

        $pr_num = $str[0];
        $doc_status = 3;
        $print_option = 0;
        // dd($pr_num);


        $data = DB::connection('sqlsrv4')->table('Erp.ReqHead AS a')
        ->join('Erp.ReqHead_UD AS b', 'a.SysRowID', '=', 'b.ForeignSysRowID')
        ->leftJoin('Ice.UD01 AS c', function($join) { 
            $join->on('b.ReqCategory_c', '=', 'c.Key1') 
            ->on(DB::raw("'Section'"), '=', 'c.Key5');
        })
        ->select(
            'b.DocNum_C', 
            'b.ProjectCode_c', 
            'b.ReqCategory_c', 
            DB::raw('NULL AS PartnerName'),   
            'a.*', 
            DB::raw('NULL AS PartnerCode'), 
            DB::raw('NULL AS Address'), 
            'c.Character02 AS DeptDesc' 
        )
        ->where('a.ReqNum', '=', $pr_num)  
        ->get();

        if ($data->count() > 0) {
            foreach ($data as $db) {
                $DocNum = $db->DocNum_C;
                $DocDate = AppModel::local_date_formate_name(substr($db->CreatedOn, 0, 10));
                $PartnerName = $db->PartnerName;
                $PartnerCode = $db->PartnerCode;
                $Address = $db->Address;
                $ProjectName = '';
                $PICName = '';
                $Telp = '';
                $Fax = '';
                $DocType = $db->ReqCategory_c;
                $DeptDesc = $db->DeptDesc;
                $SubTotal = '';
                $Discount = '';
                $Ppn = '';
                $Total = '';;
                $TOP = '';
                $Status = 3;
                $Rev = '';
                $Checked = '';
                $Approval = '';
                $Legalized = '';
                $Cat1 = 'Project : ' . $db->ProjectCode_c;
                $Currency = '';
                $judul = $pr_num ;
                $Description = $db->CommentText;
                $PRDocNum = '';
                $UserName = $db->CreatedBy;
            }
        } else {
            $DocNum = '';
            $DocDate = '';
            $PartnerName = '';
            $PartnerCode = '';
            $ProjectName = '';
            $Address = '';
            $PICName = '';
            $Telp = '';
            $Fax = '';
            $UserGrpFlowFrom = '';
            $SubTotal = '';
            $Discount = '';
            $Ppn = '';
            $Total = '';
            $TOP = '';
            $Rev = '';
                $DeptDesc = '';
                $DocType = '';
            $judul = '';
            $Approval = '';
            $Legalized = '';
            $Status = 0;
            $Address = '';
            $Description = '';
            $PRDocNum = '';
            $UserName = '';
            $Currency = '';
        }
        $Description = '
        <table style="border-collapse:collapse; " width="100%"> 
        <tr><td style=" width: 100%;">' . $Description . '</td></tr>  
        </table>';

        $CompanyProfile = explode("^", AppModel::find_company_profile('all'));

        $CompanyCode = $CompanyProfile[0];
        $CompanyName = $CompanyProfile[1];
        $Address = $CompanyProfile[2];
        $City = $CompanyProfile[3];
        $Province = $CompanyProfile[4];
        $State = $CompanyProfile[5];
        $PostalCode = $CompanyProfile[6];
        $Phone = $CompanyProfile[7];
        $Fax = $CompanyProfile[8];
        $Logo = $CompanyProfile[11];

        $DocRegNum = AppModel::find_doc_reg_num(1000);
        $sum = number_format(PRApproval::summary_qty($pr_num));




        $db_sign = DB::connection('sqlsrv5')->table("f_pr_approval_status('$DocType')")
            ->where('pr_num', '=', "$pr_num")
            ->get();

        $Checked = 'Sect. Head';
        $IDUser2 = NULL;

        if ($db_sign->count() > 0) {
            foreach ($db_sign as $db) {
                $Sign2 = '';
                $IDUser2 = $db->user_id2;
                $Checked = $db->user2;
                $Approval = $db->user3;
                $Legalized = $db->user4;
                $Vendor = 'Please Re-email';
                $UserName    = $db->user1;
                $pending_sign = '';
                $reject_sign = '';
                $Sign_1 = '';
                $Created = $db->user1;
                $Sign_2 = '';
                $Checked = $db->user2;
                $Sign_3 = '';
                $Approval = $db->user3;
                $Sign_4 = '';
                $Legalized = $db->user4;


                $IsCompleted = 1;
                $LastUpdate1 = ($db->last_update1 == '' ? 'PENDING' : $db->last_update1);
                $LastUpdate2 = ($db->status_checker == 'P' ? 'PENDING' : ($db->status_checker == 'R' ? 'REJECTED' : $db->last_update3));
                $LastUpdate3 = ($db->status_approver == 'P' ? 'PENDING' : ($db->status_approver == 'R' ? 'REJECTED' : $db->last_update4));
                $LastUpdate4 = ($db->status_legalizer == 'P' ? 'PENDING' : ($db->status_legalizer == 'R' ? 'REJECTED' : $db->last_update5));
            }
        } else {
            $Sign_1 = '';
            $Sign_2 = '';
            $Sign_3 = '';
            $Sign_4 = '';
            $IDUser2 = NULL;
            $UserName    = $UserName;
            $Checked = 'Sect. Head';
            $Approval = $Approval;
            $Legalized = $Legalized;
            $Vendor = 'Please Re-email';
            $IsCompleted = 0;
            $LastUpdate1 = 'PENDING';
            $LastUpdate2 = 'PENDING';
            $LastUpdate3 = 'PENDING';
            $LastUpdate4 = 'PENDING';
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
        PDF::SetFillColor(255, 255, 255);

        $header = array('Line', 'Item Name', 'Qty', 'UoM', 'Date Req.', 'Desc', 'Price');
        $this->draw_print_main_header($header, $DocNum, $DocDate, $PartnerName, $Address, $UserName, $PICName, $DocType, $SubTotal, $Discount, $Ppn, $Total, $TOP, $Status, $Rev, $Approval, $Legalized, $Currency, $Description, $Vendor, $Sign_1, $Sign_2, $Sign_3, $Sign_4, $IsCompleted, $LastUpdate1, $LastUpdate2, $LastUpdate3, $LastUpdate4, $sum, $CompanyName, $Logo, $DocRegNum, $City, $Province, $PostalCode, $State, $Phone, $Fax, $ProjectName, $PartnerCode, $Checked, $IDUser2, $doc_status, $pr_num, $print_option, $DeptDesc);

        PDF::Output($judul . '.pdf', 'I');
    }

    #region export pr excel

   public function exportExcel(Request $r)
{
    try {
        $status_id  = (string) $r->query('status_id', '4');
        $section_id = (string) $r->query('section_id', '0');
        $search     = trim((string) $r->query('front_table_search', ''));

        
        if (strtoupper(trim($section_id)) !== 'TMF') {
            return response()->json(['message' => 'Export hanya tersedia untuk Section TMF.'], 422);
        }

        
        $q = PRApproval::get_transaction_list($search, $status_id, $section_id);

        
        $q->where(function ($w) {
            $w->where('status_checker', '<>', 'A')
              ->orWhereNull('status_checker')
              ->orWhere('status_checker', '=', '')
              ->orWhere('status_approver', '<>', 'A')
              ->orWhereNull('status_approver')
              ->orWhere('status_approver', '=', '')
              ->orWhere('status_legalizer', '<>', 'A')
              ->orWhereNull('status_legalizer')
              ->orWhere('status_legalizer', '=', '');
        });

        
        $q->where(function ($w) {
            $w->where(function ($x) {
                $x->whereNull('status_checker')
                  ->orWhere('status_checker', '<>', 'R')
                  ->orWhere('status_checker', '');
            })
            ->where(function ($x) {
                $x->whereNull('status_approver')
                  ->orWhere('status_approver', '<>', 'R')
                  ->orWhere('status_approver', '');
            })
            ->where(function ($x) {
                $x->whereNull('status_legalizer')
                  ->orWhere('status_legalizer', '<>', 'R')
                  ->orWhere('status_legalizer', '');
            });
        });

        $rows = $q->orderBy('docdate', 'desc')
                  ->orderBy('pr_num', 'asc')
                  ->get();

        $ss = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $ss->getActiveSheet();
        $sheet->setTitle('PR Export');

        
        $sheet->setCellValue('A1', 'PR Number');
        $sheet->setCellValue('B1', 'Doc Date');
        $sheet->setCellValue('C1', 'Amount');
        $sheet->setCellValue('D1', 'Pak Budi');
        $sheet->setCellValue('E1', 'Pak Gunawan');
        $sheet->setCellValue('F1', 'Pak Sirawut');
        $sheet->setCellValue('G1', 'Pak Pornthep');

        
        $mapStatus = function ($v) {
            if ($v === null || $v === '' || strtoupper($v) === 'P') return 'waiting';
            if (strtoupper($v) === 'R') return 'Reject';
            if (strtoupper($v) === 'A') return 'Completed';
            return (string)$v;
        };

        $ridx = 2;
        foreach ($rows as $row) {
            
            $sheet->setCellValueExplicit(
                'A'.$ridx,
                (string)($row->pr_num ?? ''),
                \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
            );

            
            $sheet->setCellValue('B'.$ridx, (string)($row->docdate ?? ''));
            $sheet->setCellValue('C'.$ridx, (float)($row->amount ?? 0));

            $amount = (float)($row->amount ?? 0);

            
            $checkerStat  = $mapStatus($row->status_checker ?? null);
            $approverStat = $mapStatus($row->status_approver ?? null);
            $legalStat    = $mapStatus($row->status_legalizer ?? null);

            
            $budi = $gunawan = $sirawut = $pornthep = '';

            if ($amount > 3000000) {
                
                
                $gunawan  = (($row->user_id2 ?? '') === '') ? '' : $checkerStat; 
                $sirawut  = $approverStat;
                $pornthep = $legalStat;
            } else {
                
                
                $budi     = (($row->user_id2 ?? '') === '') ? '' : $checkerStat; 
                $gunawan  = $approverStat;
                $sirawut  = $legalStat;
                
            }

            
            $sheet->setCellValue('D'.$ridx, $budi);
            $sheet->setCellValue('E'.$ridx, $gunawan);
            $sheet->setCellValue('F'.$ridx, $sirawut);
            $sheet->setCellValue('G'.$ridx, $pornthep);

            $ridx++;
        }

        
        if ($ridx > 2) {
            $sheet->getStyle('C2:C'.($ridx - 1))
                  ->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        }

        
        $lastRow = $sheet->getHighestRow();
        $lastCol = $sheet->getHighestColumn();

        $sheet->getStyle("A1:{$lastCol}1")->getFont()->setBold(true);
        $sheet->getStyle("A1:{$lastCol}{$lastRow}")
              ->getBorders()->getAllBorders()
              ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'PR_Export_'.\Carbon\Carbon::now()->format('Ymd_His').'.xlsx';
        $safeName = rawurlencode($filename);
        $writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($ss);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control'       => 'max-age=0, no-cache, no-store, must-revalidate',
            'Pragma'              => 'no-cache',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"; filename*=UTF-8''{$safeName}",
        ]);
    } catch (\Throwable $e) {
        \Log::error('Export PR Excel gagal: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
        return response()->json(['message' => 'Export error: '.$e->getMessage()], 500);
    }
}


    #endregion export pr excel
}
