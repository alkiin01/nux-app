<?php

namespace App\Http\Controllers;

use App\Models\ReceiptEntry;
use App\Models\AppModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use PDF;

class ReceiptEntryController extends Controller
{
    /**  
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $my_id = Auth::user()->id;
        $uri = explode("/", url()->current());
        $segment_number = env('SEGMENT_NUM');
        if (count($uri) <= $segment_number) {
            $menu = $this->menu($my_id, 'home');
        } else {
            $menu = $this->menu($my_id, $uri[$segment_number]);
        }
        $data['head_title'] = $menu['head_title'];
        $data['menu_level_1'] = $menu['menu_level_1'];
        $data['menu_level_2'] = $menu['menu_level_2'];
        $data['menu_level_3'] = $menu['menu_level_3'];
        $data['menu_level_4'] = $menu['menu_level_4'];
        return view('receipt_entry/receipt_entry_index', $data);
    }

    public function get_vendor_list(Request $request)
    {
        $search = $request->search;
        $page = $request->post('page', 1); // default to page 1
        $pageSize = 10; // Number of items per page

        $query = ReceiptEntry::get_vendor_list();
        if ($search) {
            $query->where('Name', 'like', '%' . $search . '%');
        }
        $VendorList = $query->paginate($pageSize, ['*'], 'page', $page);

        return response()->json([
            'items' => $VendorList->map(function ($VendorList) {
                return [
                    'id' => $VendorList->VendorNum,
                    'name' => $VendorList->Name
                ];
            }),
            'pagination' => [
                'more' => $VendorList->hasMorePages(),
            ]
        ]);
    }

    public function front_table(Request $request)
    {
        $status_id = $request->status_id;
        $vendor_id = $request->SelectVendorID;
        $search = $request->front_table_search;
        $columns = array(
            0 => 'a.LegalNumber',
            1 => 'a.LegalNumber',
            2 => 'a.LegalNumber',
            3 => 'a.EntryDate',
            4 => 'a.PackSlip'
        );

        $totalData = ReceiptEntry::get_transaction_list($search, $vendor_id, $status_id)->count();
        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[($request->input('order.0.column') == null ? 0 : $request->input('order.0.column'))];
        $dir = ($request->input('order.0.dir') == null ? 'DESC' : $request->input('order.0.dir'));
        if (empty($search)) {
            $posts = ReceiptEntry::get_transaction_list($search, $vendor_id, $status_id)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $posts =  ReceiptEntry::get_transaction_list($search, $vendor_id, $status_id)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = ReceiptEntry::get_transaction_list($search, $vendor_id, $status_id)->count();
        }

        $data = array();
        if (!empty($posts)) {
            $no = $start;
            foreach ($posts as $post) {
                $no++;
                $trc_id = Crypt::encryptString($post->PackSlip . '~' . $post->VendorNum);
                $sys_id = "'" . str_replace("=", "-", $trc_id) . "'";
                $button = '
            <span class="svg-icon svg-icon-primary svg-icon-2x" style="cursor: pointer;" id="svg_document_preview_' . $no . '"   onclick="document_preview(' . $sys_id . ', ' . $no . ') ;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="black"></path>
                    <path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="black"></path>
                    <path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="black"></path>
                </svg>
            </span>
            <span id="spinner_document_preview_' . $no . '" class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;"></span>';

                $my_username = Auth::user()->username;
                $nestedData['no'] = $no;
                $nestedData['LegalNumber'] = $post->LegalNumber . '<br/>' . ($post->Received == 1 ? 'Done' : 'Draft');
                $nestedData['PackSlip'] = $post->PackSlip;
                $nestedData['Vendor'] = $post->PackSlip . '<br/>' . $post->Name;
                $nestedData['EntryDate'] = AppModel::local_date_formate_name(substr($post->EntryDate, 0, 10)) . '<br/>' . ($post->EntryDate == $post->ReceiptDate ? '' : AppModel::local_date_formate_name(substr($post->ReceiptDate, 0, 10)));
                $nestedData['ReceiptDate'] = AppModel::local_date_formate_name(substr($post->ReceiptDate, 0, 10));
                $nestedData['Received'] = ($post->Received == 1 ? 'Done' : 'Draft');
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

    public function get_count_document(Request $request)
    {
        $vendor_id = $request->SelectVendorID;
        $data['total_draft'] = ReceiptEntry::get_count_document_draft($vendor_id);
        $data['total_document'] = ReceiptEntry::get_count_document($vendor_id);
        echo json_encode($data);
    }

    public function add_document(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $data['code'] = 200;
        $data['desc'] = '';
        $data['PackSlip'] = '';
        $data['VendorNum'] = '';
        $data['VendorName'] = '';
        $data['PONum'] = '';
        $data['LegalNumber'] = '';
        $data['trc_unix_id'] = '';
        $data['ReceiptComment'] = '';
        $data['ref_tab'] = 0;
        $data['EntryDate'] = date('Y-m-d');
        $data['ArrivedDate'] = date('Y-m-d');
        return view('receipt_entry/receipt_entry_header', $data);
    }

    public function get_preview_doc(Request $request)
    {
        $str_req = explode("_", $request->trc_unix_id);
        $str = explode("~", Crypt::decryptString(str_replace("-", "=", $str_req[0])));
        $PackSlip = $str[0];
        $VendorNum = $str[1];
        $data_detail = ReceiptEntry::data_header($PackSlip, $VendorNum);
        if ($data_detail) {
            $data['code'] =  200;
            $data['status'] =  'Ok';
            if ($data_detail->count() > 0) {
                foreach ($data_detail as $db) {
                    $sys_id = Crypt::encryptString($db->PackSlip . '~' . $db->VendorNum);
                    $sys_id = str_replace("=", "-", $sys_id);
                    $data['trc_unix_id'] =  $sys_id;
                    $data['PackSlip'] = $db->PackSlip;
                    $data['VendorNum'] = $db->VendorNum;
                    $data['VendorName'] = $db->Name;
                    $data['PONum'] = $db->PONum;
                    $data['LegalNumber'] = $db->LegalNumber;
                    $data['EntryDate'] = $db->EntryDate;
                    $data['ArrivedDate'] = $db->ArrivedDate;
                    $data['ReceiptComment'] = $db->ReceiptComment;
                    $data['ref_tab'] = 1;
                }
            } else {
                $data['PackSlip'] = '';
                $data['VendorNum'] = '';
                $data['VendorName'] = '';
                $data['PONum'] = '';
                $data['LegalNumber'] = '';
                $data['EntryDate'] = '';
                $data['ArrivedDate'] = '';
                $data['trc_unix_id'] = '';
                $data['ReceiptComment'] = '';
                $data['ref_tab'] = 0;
            }
        } else {
            $data['code'] =  500;
            $data['status'] =  "Please reload and try again!";
        }
        return view('receipt_entry/receipt_entry_header', $data);
    }

    public function get_header_attr(Request $request)
    {
        $str_req = explode("_", $request->trc_unix_id);
        $str = explode("~", Crypt::decryptString(str_replace("-", "=", $str_req[0])));
        $PackSlip = $str[0];
        $VendorNum = $str[1];
        $data_detail = ReceiptEntry::data_header($PackSlip, $VendorNum);
        if ($data_detail) {
            $data['code'] =  200;
            $data['status'] =  'Ok';
            if ($data_detail->count() > 0) {
                foreach ($data_detail as $db) {
                    $sys_id = Crypt::encryptString($db->PackSlip . '~' . $db->VendorNum);
                    $sys_id = str_replace("=", "-", $sys_id);
                    $data['trc_unix_id'] =  $sys_id;
                    $data['PackSlip'] = $db->PackSlip;
                    $data['VendorNum'] = $db->VendorNum;
                    $data['VendorName'] = $db->Name;
                    $data['PONum'] = $db->PONum;
                    $data['LegalNumber'] = $db->LegalNumber;
                    $data['EntryDate'] = $db->EntryDate;
                    $data['ArrivedDate'] = $db->ArrivedDate;
                    $data['ReceiptComment'] = $db->ReceiptComment;
                    $data['ref_tab'] = 1;
                }
            } else {
                $data['PackSlip'] = '';
                $data['VendorNum'] = '';
                $data['VendorName'] = '';
                $data['PONum'] = '';
                $data['LegalNumber'] = '';
                $data['EntryDate'] = '';
                $data['ArrivedDate'] = '';
                $data['trc_unix_id'] = '';
                $data['ReceiptComment'] = '';
                $data['ref_tab'] = 0;
            }
        } else {
            $data['code'] =  500;
            $data['status'] =  "Please reload and try again!";
        }
        return json_encode($data);
    }


    public function detail_table(Request $request)
    {
        if ($request->temp_id != '') {
            $str = explode("~", Crypt::decryptString(str_replace("-", "=", $request->temp_id)));
            $PackSlip = $str[0];
            $VendorNum = $str[1];
        } else {
            $PackSlip = 0;
            $VendorNum = 0;
        }

        $search = $request->detail_table_search;
        $columns = array(
            0 => 'PackLine',
            1 => 'PackLine',
            2 => 'PartNum',
            3 => 'OurQty',
            4 => 'PONum',
            //   5 =>'JobReceiptStatus_c', 
            //   6 =>'IssueMaterialStatus_c' 
        );

        $totalData = ReceiptEntry::get_detail_transaction_list($search, $PackSlip, $VendorNum)->count();
        $totalFiltered = $totalData;
        $limit = $request->length;
        $start = $request->start;
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        if (empty($search)) {
            $posts = ReceiptEntry::get_detail_transaction_list($search, $PackSlip, $VendorNum)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $posts =  ReceiptEntry::get_detail_transaction_list($search, $PackSlip, $VendorNum)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = ReceiptEntry::get_detail_transaction_list($search, $PackSlip, $VendorNum)->count();
        }
        $data = array();

        if (!empty($posts)) {
            $no = $start;
            foreach ($posts as $post) {
                $no++;
                $trc_id = Crypt::encryptString($post->PackLine);
                $sys_id = "'" . str_replace("=", "-", $trc_id) . "'";
                $button =
                    '<span class="svg-icon svg-icon-primary svg-icon-2x" style="cursor: pointer;" id="svg_document_detail_preview_' . $no . '"  onclick="getDetail(' . $sys_id . ') ;">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="black"></path><path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="black"></path><path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="black"></path></svg>
        </span> 
        <span id="spinner_document_detail_preview_' . $no . '" class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;"></span>';

                $button_job_status = ($post->JobReceiptStatus_c == 1 ?
                    '<span class="svg-icon svg-icon-success svg-icon-2x" style="cursor: pointer;" id="' . $trc_id . '" title="Job Receipt Status">
             <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" id="svg_delete_line_' . $post->PackLine . '">  
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <polygon points="0 0 24 0 24 24 0 24"/>
                        <path d="M9.26193932,16.6476484 C8.90425297,17.0684559 8.27315905,17.1196257 7.85235158,16.7619393 C7.43154411,16.404253 7.38037434,15.773159 7.73806068,15.3523516 L16.2380607,5.35235158 C16.6013618,4.92493855 17.2451015,4.87991302 17.6643638,5.25259068 L22.1643638,9.25259068 C22.5771466,9.6195087 22.6143273,10.2515811 22.2474093,10.6643638 C21.8804913,11.0771466 21.2484189,11.1143273 20.8356362,10.7474093 L17.0997854,7.42665306 L9.26193932,16.6476484 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(14.999995, 11.000002) rotate(-180.000000) translate(-14.999995, -11.000002) "/>
                        <path d="M4.26193932,17.6476484 C3.90425297,18.0684559 3.27315905,18.1196257 2.85235158,17.7619393 C2.43154411,17.404253 2.38037434,16.773159 2.73806068,16.3523516 L11.2380607,6.35235158 C11.6013618,5.92493855 12.2451015,5.87991302 12.6643638,6.25259068 L17.1643638,10.2525907 C17.5771466,10.6195087 17.6143273,11.2515811 17.2474093,11.6643638 C16.8804913,12.0771466 16.2484189,12.1143273 15.8356362,11.7474093 L12.0997854,8.42665306 L4.26193932,17.6476484 Z" fill="#000000" fill-rule="nonzero" transform="translate(9.999995, 12.000002) rotate(-180.000000) translate(-9.999995, -12.000002) "/>
                    </g>
                </svg>
        </span>'
                    :
                    '<span class="svg-icon svg-icon-danger svg-icon-2x" style="cursor: pointer;" id="' . $trc_id . '" title="Job Receipt Status">
             <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" id="svg_delete_line_' . $post->PackLine . '">  
                    <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
                        <rect x="0" y="7" width="16" height="2" rx="1"/>
                        <rect opacity="0.3" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000) " x="0" y="7" width="16" height="2" rx="1"/>
                    </g>
                </svg>
        </span>');

                $button_issue_status = ($post->IssueMaterialStatus_c == 1 ?
                    '<span class="svg-icon svg-icon-success svg-icon-2x" style="cursor: pointer;" id="' . $trc_id . '" title="Issue Material Status">
             <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" id="svg_delete_line_' . $post->PackLine . '">  
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <polygon points="0 0 24 0 24 24 0 24"/>
                        <path d="M9.26193932,16.6476484 C8.90425297,17.0684559 8.27315905,17.1196257 7.85235158,16.7619393 C7.43154411,16.404253 7.38037434,15.773159 7.73806068,15.3523516 L16.2380607,5.35235158 C16.6013618,4.92493855 17.2451015,4.87991302 17.6643638,5.25259068 L22.1643638,9.25259068 C22.5771466,9.6195087 22.6143273,10.2515811 22.2474093,10.6643638 C21.8804913,11.0771466 21.2484189,11.1143273 20.8356362,10.7474093 L17.0997854,7.42665306 L9.26193932,16.6476484 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(14.999995, 11.000002) rotate(-180.000000) translate(-14.999995, -11.000002) "/>
                        <path d="M4.26193932,17.6476484 C3.90425297,18.0684559 3.27315905,18.1196257 2.85235158,17.7619393 C2.43154411,17.404253 2.38037434,16.773159 2.73806068,16.3523516 L11.2380607,6.35235158 C11.6013618,5.92493855 12.2451015,5.87991302 12.6643638,6.25259068 L17.1643638,10.2525907 C17.5771466,10.6195087 17.6143273,11.2515811 17.2474093,11.6643638 C16.8804913,12.0771466 16.2484189,12.1143273 15.8356362,11.7474093 L12.0997854,8.42665306 L4.26193932,17.6476484 Z" fill="#000000" fill-rule="nonzero" transform="translate(9.999995, 12.000002) rotate(-180.000000) translate(-9.999995, -12.000002) "/>
                    </g>
                </svg>
        </span>'
                    :
                    '<span class="svg-icon svg-icon-danger svg-icon-2x" style="cursor: pointer;" id="' . $trc_id . '" title="Issue Material Status">
             <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" id="svg_delete_line_' . $post->PackLine . '">  
                    <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
                        <rect x="0" y="7" width="16" height="2" rx="1"/>
                        <rect opacity="0.3" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000) " x="0" y="7" width="16" height="2" rx="1"/>
                    </g>
                </svg>
        </span>');

                $button_received_status = ($post->Received == 1 ?
                    '<span class="svg-icon svg-icon-success svg-icon-2x" style="cursor: pointer;" id="' . $trc_id . '" title="Received Status">
             <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" id="svg_delete_line_' . $post->PackLine . '">  
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <polygon points="0 0 24 0 24 24 0 24"/>
                        <path d="M9.26193932,16.6476484 C8.90425297,17.0684559 8.27315905,17.1196257 7.85235158,16.7619393 C7.43154411,16.404253 7.38037434,15.773159 7.73806068,15.3523516 L16.2380607,5.35235158 C16.6013618,4.92493855 17.2451015,4.87991302 17.6643638,5.25259068 L22.1643638,9.25259068 C22.5771466,9.6195087 22.6143273,10.2515811 22.2474093,10.6643638 C21.8804913,11.0771466 21.2484189,11.1143273 20.8356362,10.7474093 L17.0997854,7.42665306 L9.26193932,16.6476484 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(14.999995, 11.000002) rotate(-180.000000) translate(-14.999995, -11.000002) "/>
                        <path d="M4.26193932,17.6476484 C3.90425297,18.0684559 3.27315905,18.1196257 2.85235158,17.7619393 C2.43154411,17.404253 2.38037434,16.773159 2.73806068,16.3523516 L11.2380607,6.35235158 C11.6013618,5.92493855 12.2451015,5.87991302 12.6643638,6.25259068 L17.1643638,10.2525907 C17.5771466,10.6195087 17.6143273,11.2515811 17.2474093,11.6643638 C16.8804913,12.0771466 16.2484189,12.1143273 15.8356362,11.7474093 L12.0997854,8.42665306 L4.26193932,17.6476484 Z" fill="#000000" fill-rule="nonzero" transform="translate(9.999995, 12.000002) rotate(-180.000000) translate(-9.999995, -12.000002) "/>
                    </g>
                </svg>
        </span>'
                    :
                    '<span class="svg-icon svg-icon-danger svg-icon-2x" style="cursor: pointer;" id="' . $trc_id . '" title="Received Status">
             <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" id="svg_delete_line_' . $post->PackLine . '">  
                    <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
                        <rect x="0" y="7" width="16" height="2" rx="1"/>
                        <rect opacity="0.3" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000) " x="0" y="7" width="16" height="2" rx="1"/>
                    </g>
                </svg>
        </span>');

                $nestedData['no'] = $post->PackLine;
                $nestedData['PartNum'] = $post->PartNum . '<br/>' . $post->PartDescription;
                $nestedData['Qty'] = 'Our : ' . number_format($post->OurQty, 0) . ' ' . $post->IUM . '<br/>' . 'Supp : ' . number_format($post->VendorQty, 0) . ' ' . $post->PUM;
                $nestedData['PONum'] = $post->PONum . '/' . $post->POLine . '/' . $post->PORelNum;
                $nestedData['action'] = $button;
                $nestedData['button_job_status'] = $button_job_status;
                $nestedData['button_issue_status'] = $button_issue_status;
                $nestedData['Status'] = $button_received_status . ' ' . ($post->JobNum !== '' ? $button_issue_status . ' ' . $button_job_status : '');
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

    public function detail_po_list_table(Request $request)
    {
        $PONum = ($request->ponum == null ? 0 : $request->ponum);
        $PartNum = ($request->partnum == null ? 0 : $request->partnum);
        $columns = array(
            0 => 'POLine',
            1 => 'POLine',
            2 => 'PartNum',
            3 => 'OrderQty',
            4 => 'PONUM'
        );
        $totalData = ReceiptEntry::get_detail_po_list($PONum, $PartNum)->count();
        $totalFiltered = $totalData;
        $limit = $request->length;
        $start = $request->start;
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        if (empty($search)) {
            $posts = ReceiptEntry::get_detail_po_list($PONum, $PartNum)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $posts =  ReceiptEntry::get_detail_po_list($PONum, $PartNum)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = ReceiptEntry::get_detail_po_list($PONum, $PartNum)->count();
        }
        $data = array();

        if (!empty($posts)) {
            $no = $start;
            foreach ($posts as $post) {
                $no++;
                $button =
                    '<span class="svg-icon svg-icon-primary svg-icon-2x" style="cursor: pointer;" onclick="selectPOList(' . $post->PONUM . ', ' . $post->POLine . ') ;">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="black"></path><path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="black"></path><path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="black"></path></svg>
        </span>';
                $nestedData['no'] = $post->POLine;
                $nestedData['PartNum'] = $post->PartNum . '<br/>' . $post->LineDesc;
                $nestedData['Qty'] = 'Our : ' . number_format($post->OrderQty, 0) . ' ' . $post->IUM . '<br/>' . 'Supp : ' . number_format($post->XOrderQty, 0) . ' ' . $post->PUM;
                $nestedData['PONum'] = $post->PONUM . '/' . $post->POLine;
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

    public function get_preview_doc_detail(Request $request)
    {
        if ($request->trc_unix_id != '') {
            $str = explode("~", Crypt::decryptString(str_replace("-", "=", $request->trc_unix_id)));
            $PackSlip = $str[0];
            $VendorNum = $str[1];
        } else {
            $PackSlip = 0;
            $VendorNum = 0;
        }
        $PackLine = ($request->detail_id == "" ? 0 : Crypt::decryptString(str_replace("-", "=", $request->detail_id)));
        $data['PackLine'] = $PackLine;
        $search = '';

        $data_detail = ReceiptEntry::data_detail($PackSlip, $PackLine, $VendorNum);
        $po_header = ReceiptEntry::get_po_header($PackSlip, $VendorNum);
        $lot_date = date('dmy');
        if ($data_detail) {
            $data['code'] =  200;
            $data['status'] =  'Ok';
            if ($data_detail->count() > 0) {
                foreach ($data_detail as $db) {
                    $data['PONum'] = $db->PONum;
                    $data['POLine'] = $db->POLine;
                    $data['PORelNum'] = $db->PORelNum;
                    $data['PartNum'] = $db->PartNum;
                    $data['PartDescription'] = $db->PartDescription;
                    $data['OurQty'] = (int) $db->OurQty;
                    $data['VendorQty'] = (int) $db->VendorQty;
                    $data['IUM'] = $db->IUM;
                    $data['PUM'] = $db->PUM;
                    $data['WareHouseCode'] = $db->WareHouseCode;
                    $data['BinNum'] = $db->BinNum;
                    $data['LotNum'] = $db->LotNum;
                    $data['JobNum'] = $db->JobNum;
                    $data['AssemblySeq'] = $db->AssemblySeq;
                    $data['JobSeq'] = $db->JobSeq;
                    $data['checkedOur'] = ($db->QtyOption == 'Our' ? 'checked' : '');
                    $data['checkedSupplier'] = ($db->QtyOption == 'Supplier' ? 'checked' : '');
                    $data['checkedOverride'] = ($db->ConvOverride == true ? 'checked' : '');
                    $data['LineButton'] = 'Update GR';
                }
            } else {
                $data['PONum'] = $po_header;
                $data['POLine'] = '';
                $data['PORelNum'] = '';
                $data['PartNum'] = '';
                $data['PartDescription'] = '';
                $data['OurQty'] = '';
                $data['VendorQty'] = '';
                $data['IUM'] = 'PCS';
                $data['PUM'] = 'PCS';
                $data['WareHouseCode'] = '';
                $data['BinNum'] = '';
                // $data['LotNum'] = 'A';
                $data['LotNum'] = $lot_date;
                $data['JobNum'] = '';
                $data['AssemblySeq'] = '';
                $data['JobSeq'] = '';
                $data['checkedOur'] = 'checked';
                $data['checkedSupplier'] = '';
                $data['LineButton'] = 'Update GR';
                $data['checkedOverride'] = '';
            }
        } else {
            $data['code'] =  500;
            $data['status'] =  "Please reload and try again!";
        }

        $data['JobRequiredQty'] = ReceiptEntry::get_job_info($data['JobNum']);
        $data['UomList'] = ReceiptEntry::uom_list($data['IUM']);
        $data['PumList'] = ReceiptEntry::uom_list($data['PUM']);
        $PackLineList = ReceiptEntry::get_detail_transaction_list($search, $PackSlip, $VendorNum)
            ->select('PackSlip', 'VendorNum', 'PackLine', 'PartNum', DB::raw("CASE WHEN PackLine = $PackLine THEN 'selected' ELSE '' END AS selected"))
            ->get();
        if ($PackLineList) {
            $data['PackLineList'] = $PackLineList;
        } else {
            $data['PackLineList'] = null;
        }
        $data['WHList'] = ReceiptEntry::wh_list($data['WareHouseCode']);
        return view('receipt_entry/receipt_entry_detail', $data);
    }

    public function show_attachment(Request $request)
    {
        $client = new Client();
        $data = [];
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
            Log::error('API request failed', [
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

    public function get_attachment_list(Request $request)
    {
        if ($request->trc_unix_id) {
            $str = explode("~", Crypt::decryptString(str_replace("-", "=", $request->trc_unix_id)));
            $PackSlip = $str[0];
            $VendorNum = $str[1];
        } else {
            $PackSlip = 0;
            $VendorNum = 0;
        }
        $data['list'] = ReceiptEntry::get_attachment_list($PackSlip, $VendorNum);
        $data['count'] = ReceiptEntry::get_attachment_list($PackSlip, $VendorNum)->count();
        return view('receipt_entry/attachment_list', $data);
    }

    public function get_new_gr(Request $request)
    {
        $poNum = $request->poNum;
        $packSlip = $request->packSlip;
        $arrivalDate = $request->ArrivedDate;
        $receiptComment = $request->ReceiptComment;
        $username = Auth::user()->username;
        $password = Crypt::decryptString(Auth::user()->epicor_password);
        $client = new Client();
        $data = [];
        $host_api = self::get_host_api();
        try {
            $response = $client->request('POST', $host_api . 'Receipt/GetNew', [
                'json' => [
                    'nik' => "$username",
                    'password' => "$password",
                    'poNum' => $poNum,
                    'receiptComment' => $receiptComment,
                    'arrivalDate' => $arrivalDate,
                    'packSlip' => "$packSlip"
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'verify' => false,
            ]);
            $responseBody = json_decode($response->getBody()->getContents(), true);
            $data['code'] = $responseBody['code'];
            $data['status'] = $responseBody['status'];
            if ($data['code'] == 200) {
                $data['transaction_code'] = $responseBody['data']['epi_code'];
                $data['transaction_status'] = $responseBody['data']['epi_status'];
                if ($data['transaction_code'] == 200) {
                    $data['data'] = $responseBody['data']['rcvHead'];
                    $data['vendorNum'] = $responseBody['data']['rcvHead']['vendorNum'];
                    $data['packSlip'] = $responseBody['data']['rcvHead']['packSlip'];
                    $data['legalNumber'] = $responseBody['data']['rcvHead']['legalNumber'];
                    $data['purPointName'] = $responseBody['data']['rcvHead']['purPointName'];
                    $trc_id = Crypt::encryptString($data['packSlip'] . '~' . $data['vendorNum']);
                    $data['ref_doc']  = str_replace("=", "-", $trc_id);
                }
            }
        } catch (RequestException $e) {
            Log::error('API request failed', [
                'message' => $e->getMessage(),
                'request' => $e->getRequest(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null,
            ]);
            $data['code'] = 500;
            $data['desc'] = $e->getMessage();
        }
        return $data;
    }

    public function update_gr(Request $request)
    {
        if ($request->temp_id != '') {
            $str = explode("~", Crypt::decryptString(str_replace("-", "=", $request->temp_id)));
            $packSlip = $str[0];
            $vendorNum = $str[1];
        } else {
            $packSlip = 0;
            $vendorNum = 0;
        }
        $poNum = $request->poNum;
        $packSlip = $request->packSlip;
        $arrivalDate = $request->ArrivedDate;
        $receiptComment = $request->ReceiptComment;
        $username = Auth::user()->username;
        $password = Crypt::decryptString(Auth::user()->epicor_password);
        $client = new Client();
        $data = [];
        $host_api = self::get_host_api();
        try {
            $response = $client->request('POST', $host_api . 'Receipt/UpdateHeader', [
                'json' => [
                    'nik' => "$username",
                    'password' => "$password",
                    'poNum' => $poNum,
                    'receiptComment' => $receiptComment,
                    'arrivalDate' => $arrivalDate,
                    'packSlip' => "$packSlip",
                    'rowMod' => "U",
                    "vendorNum" => $vendorNum,
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'verify' => false,
            ]);
            $responseBody = json_decode($response->getBody()->getContents(), true);
            $data['code'] = $responseBody['code'];
            $data['status'] = $responseBody['status'];
            if ($data['code'] == 200) {
                $data['transaction_code'] = $responseBody['data']['epi_code'];
                $data['transaction_status'] = $responseBody['data']['epi_status'];
                if ($data['transaction_code'] == 200) {
                    $data['data'] = $responseBody['data']['rcvHead'];
                    $data['vendorNum'] = $responseBody['data']['rcvHead']['vendorNum'];
                    $data['packSlip'] = $responseBody['data']['rcvHead']['packSlip'];
                    $data['legalNumber'] = $responseBody['data']['rcvHead']['legalNumber'];
                    $data['purPointName'] = $responseBody['data']['rcvHead']['purPointName'];
                    $trc_id = Crypt::encryptString($data['packSlip'] . '~' . $data['vendorNum']);
                    $data['ref_doc']  = str_replace("=", "-", $trc_id);
                }
            }
        } catch (RequestException $e) {
            Log::error('API request failed', [
                'message' => $e->getMessage(),
                'request' => $e->getRequest(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null,
            ]);
            $data['code'] = 500;
            $data['desc'] = $e->getMessage();
        }
        return $data;
    }

    public function get_qty_info(Request $request)
    {
        if ($request->temp_id != '') {
            $str = explode("~", Crypt::decryptString(str_replace("-", "=", $request->temp_id)));
            $packSlip = $str[0];
            $vendorNum = $str[1];
        } else {
            $packSlip = 0;
            $vendorNum = 0;
        }

        $poNum = $request->poNum;
        $username = Auth::user()->username;
        $password = Crypt::decryptString(Auth::user()->epicor_password);
        $poNum = $request->poNum;
        $poLine = $request->poLine;
        $packLine = $request->packLine;
        $qtyOption = $request->qtyOption;
        $inputOurQty = $request->inputOurQty;
        $ium = $request->ium;
        $vendorQty = $request->vendorQty;
        $pum = $request->pum;
        $convOverride = ($request->convOverride == 0 ? false : true);
        $rowMod = ($packLine == 0 ? "A" : "U");

        $client = new Client();
        $data = [];
        $host_api = self::get_host_api();
        try {
            $response = $client->request('GET', $host_api . 'Receipt/GetQtyInfo', [
                'json' => [
                    'nik' => "$username",
                    'password' => "$password",
                    'poNum' => "$poNum",
                    'poLine' => "$poLine",
                    'packSlip' => "$packSlip",
                    'packLine' => $packLine,
                    'qtyOption' => "$qtyOption",
                    'inputOurQty' => $inputOurQty,
                    'ium' => "$ium",
                    'vendorQty' => $vendorQty,
                    'pum' => "$pum",
                    'convOverride' => $convOverride,
                    'vendorNum' => $vendorNum,
                    'rowMod' => "$rowMod",
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'verify' => false,
            ]);
            $responseBody = json_decode($response->getBody()->getContents(), true);
            $data['code'] = $responseBody['code'];
            $data['status'] = $responseBody['status'];
            if ($data['code'] == 200) {
                $data['transaction_code'] = $responseBody['data']['epi_code'];
                $data['transaction_status'] = $responseBody['data']['epi_status'];
                if ($data['transaction_code'] == 200) {
                    $data['thisTranQty'] = $responseBody['data']['rcvDtls']['thisTranQty'];
                }
            }
        } catch (RequestException $e) {
            $data['code'] = 500;
            $data['transaction_code'] = 500;
            $data['desc'] = $e->getMessage();
            $data['transaction_status'] = $e->getMessage();
        }
        return $data;
    }

    public function update_line_gr(Request $request)
    {
        if ($request->temp_id != '') {
            $str = explode("~", Crypt::decryptString(str_replace("-", "=", $request->temp_id)));
            $packSlip = $str[0];
            $vendorNum = $str[1];
        } else {
            $packSlip = 0;
            $vendorNum = 0;
        }

        $lotTag = $request->lotTag;
        $poNum = $request->poNum;
        $poLine = $request->poLine;
        $poRelNum = 1;
        $packLine = $request->packLine;
        $qtyOption = $request->qtyOption;
        $inputOurQty = $request->inputOurQty;
        $jobNum = $request->JobNum;
        $assemblySeq = $request->AssemblySeq;
        $jobSeq = $request->JobSeq;
        $LotNum = $request->lotNum;
        $partNum = $request->PartNum;
        $partName = $request->PartName;
        $seqnum = $request->seqnum;
        $PartTb = DB::connection('sqlsrv4')
            ->table('Part')
            ->where('PartNum', $partNum)
            ->select('BinReceipt_c', 'WhseReceipt_c', 'PartNum')
            ->first();
        $ourRemQty = ReceiptEntry::get_qty_balance_po_line($poNum, $poLine, $poRelNum, $inputOurQty, $packSlip, $packLine, $vendorNum);
        $totalQtyCompleted = ReceiptEntry::get_job_qty_completed($inputOurQty, $packSlip, $packLine, $jobNum, $vendorNum);
        $totalQtyBefore = ReceiptEntry::get_total_qty_before($packSlip, $packLine, $vendorNum);
        if ($packLine > 0) {
            $statusIssueJob = explode("~", ReceiptEntry::get_status_issue_job($packSlip, $packLine, $vendorNum));
        } else {
            $statusIssueJob = explode("~", "1~1");
        }
        $thisTranQty = $inputOurQty - $totalQtyBefore;
        $rowMod = ($packLine == 0 ? "A" : "U");
        if ($request->rowMod == "D") {
            $rowMod = "D";
        }
        $part_onhand_status = ['code' => 1, 'status' => 'Ok !'];
        $mtl_onhand_status = ['code' => 1, 'status' => 'Ok !'];
        $job_status = ['code' => 1, 'status' => 'Ok !'];

        if ($jobNum != '' && $rowMod == "D") {
            $part_onhand_status = ReceiptEntry::check_part_onhand_status($jobNum, $thisTranQty, $LotNum);
            $job_status = ReceiptEntry::check_job_status($jobNum);
        } else if ($jobNum != '' && $thisTranQty < 0) {
            $part_onhand_status = ReceiptEntry::check_part_onhand_status($jobNum, $thisTranQty, $LotNum);
            $job_status = ReceiptEntry::check_job_status($jobNum);
        } else if ($jobNum != '' && $thisTranQty > 0) {
            $mtl_onhand_status = ReceiptEntry::check_material_onhand_status($jobNum, $assemblySeq, $jobSeq, $thisTranQty, $LotNum);
            $job_status = ReceiptEntry::check_job_status($jobNum);
        }

        if ($jobNum != '' && $job_status['code'] == 0) {
            $data['code'] = 200;
            $data['transaction_code'] = 500;
            $data['transaction_status'] = $job_status['status'];
        } else 
        if ($inputOurQty == 0 && $rowMod != "D") {
            $data['code'] = 200;
            $data['transaction_code'] = 500;
            $data['transaction_status'] = "Qty tidak boleh sama dengan 0";
        } else
        if ($jobNum != '' && $thisTranQty < 0 && $part_onhand_status['code'] == 0) {
            $data['code'] = 200;
            $data['transaction_code'] = 500;
            $data['transaction_status'] = $part_onhand_status['status'];
        } else if ($jobNum != '' && $thisTranQty > 0 && $mtl_onhand_status['code'] == 0) {
            $data['code'] = 200;
            $data['transaction_code'] = 500;
            $data['transaction_status'] = $mtl_onhand_status['status'];
        } else {
            $ium = $request->ium;
            $vendorQty = $request->vendorQty;
            $pum = $request->pum;
            $convOverride = ($request->convOverride == 'false' ? false : true);
            $warehouseCode = $PartTb->WhseReceipt_c;
            $binNum = $PartTb->BinReceipt_c;
            $tranReference = '';
            if ($ourRemQty <= 0) {
                $receivedComplete = true;
            } else {
                $receivedComplete = false;
            }

            if ($jobNum != '') {
                if ($totalQtyCompleted <= 0) {
                    $issuedComplete = true;
                } else {
                    $issuedComplete = false;
                }
            } else {
                $issuedComplete = false;
            }

            $received = true;
            $receiptDate = $request->receiptDate;
            $ToWarehouseID = $PartTb->WhseReceipt_c;
            $PartNum = $request->PartNum;
            $ToBinID = $PartTb->BinReceipt_c;
            $username = Auth::user()->username;
            $password = Crypt::decryptString(Auth::user()->epicor_password);
            $client = new Client();
            $data = [];
            $host_api = self::get_host_api();
            if ($rowMod != "D" && $seqnum != '') {
                $checkTag = ReceiptEntry::get_tag_list($packSlip, $packLine, $poNum, $poLine, $partNum, $warehouseCode, $binNum, $lotTag, $seqnum);
                if ($checkTag->count() > 0) {

                    $data['code'] = 500;
                    $data['status'] = 'Label sudah digunakan !';
                    $data['transaction_code'] = 500;
                    $data['transaction_status'] = 'Label sudah digunakan !';
                    return $data;
                }
            }
            try {
                $qtyOpt = $qtyOption[0];
                $response = $client->request('POST', $host_api . 'Receipt/UpdateDetail', [
                    'json' => [
                        'nik' => "$username",
                        'password' => "$password",
                        'poNum' => $poNum,
                        'poLine' => $poLine,
                        "packSlip" => $packSlip,
                        'packLine' => $packLine,
                        "vendorNum" => $vendorNum,
                        'qtyOption' => "$qtyOpt",
                        "inputOurQty" => $inputOurQty,
                        "ium" => "$ium",
                        "vendorQty" => $vendorQty,
                        "pum" => "$pum",
                        "issuedComplete" => $issuedComplete,
                        "convOverride" => $convOverride,
                        "warehouseCode" => "$warehouseCode",
                        "binNum" => "$binNum",
                        "tranReference" => ($tranReference === 'undefined' ? "" : $tranReference),
                        "rowMod" => $rowMod,
                        "receivedComplete" => ($receivedComplete  === 'undefined' ? true : $receivedComplete),
                        "received" => ($received === 'undefined' ? true : $received),
                        "receiptDate" => "$receiptDate",
                        "lotNum" => $LotNum,
                    ],
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                    'verify' => false,
                ]);
                $responseBody = json_decode($response->getBody()->getContents(), true);
                $data['code'] = $responseBody['code'];
                $data['status'] = $responseBody['status'];
                if ($rowMod != "D" && $seqnum != '') {
                        $packLine = $responseBody['data']['rcvDtl']['packLine'];
                        $saveTag = ReceiptEntry::save_tag($packSlip, $packLine, $poNum, $poLine, $seqnum, $inputOurQty, $vendorNum, $lotTag, $partNum, $partName, $warehouseCode, $binNum);
                    } else {
                        $deleteTag = ReceiptEntry::delete_tag($packSlip, $packLine, $poNum, $poLine);
                    }
                if ($data['code'] == 200) {

                    $data['transaction_code'] = $responseBody['data']['epi_code'];
                    $data['transaction_status'] = $responseBody['data']['epi_status'];
                    if (!empty($jobNum) && $data['transaction_code'] == 200) {
                        $db_job_mtl = ReceiptEntry::get_list_job_mtl($jobNum, $assemblySeq, $jobSeq);
                        // dd($db_job_mtl);
                        $isReturn = false;
                        if ($rowMod != "D") {
                            if ($data['transaction_code'] == 200) {
                                $data['packLine'] = $responseBody['data']['rcvDtl']['packLine'];
                                if ($jobNum !== '') {
                                        // if ($db_job_mtl->count() > 0) {
                                        //     $total_success = 0;
                                        //     foreach ($db_job_mtl as $row) {
                                        //         if ($row->WarehouseCode != '' && $row->BinNum != '') {
                                        //             $PartBin = DB::connection('sqlsrv4')
                                        //             ->table('Erp.PartBin as pb')
                                        //             ->where('pb.PartNum', $row->PartNum)
                                        //             ->where('pb.BinNum', $row->BinNum)
                                        //             ->where('pb.OnhandQty', '>', 0)
                                        //             ->where('pb.WarehouseCode',$row->WarehouseCode)
                                        //             ->select('pb.LotNum', 'pb.OnhandQty','pb.WarehouseCode','pb.BinNum')
                                        //             ->orderByRaw("
                                        //                 CASE 
                                        //                     WHEN pb.LotNum = 'A' THEN 0
                                        //                     WHEN ISNUMERIC(pb.LotNum) = 1 THEN 1
                                        //                     ELSE 2   
                                        //                 END
                                        //             ")
                                        //             ->orderByRaw("
                                        //                 CASE 
                                        //                     WHEN ISNUMERIC(pb.LotNum) = 1 
                                        //                     THEN CAST(pb.LotNum AS INT) 
                                        //                 END ASC
                                        //             ")
                                        //             ->get();
                                        //             $issued=0;
                                        //             $qty_issue = $row->QtyPer * ($inputOurQty - $totalQtyBefore);
                                        //             $OnHandQty = $PartBin->sum('pb.OnhandQty');
                                        //             if ($PartBin->count() > 0 && $OnHandQty > $qty_issue) {
                                        //                 foreach ($PartBin as $item) {
                                        //                     // $lotNum = $item->LotNum;
                                        //                     // $qty_issue = $row->QtyPer * ($inputOurQty - $totalQtyBefore);
                                        //                     $MaterialQty = $item->OnhandQty;
                                        //                     if ($thisTranQty > 0) {
                                        //                         $mtl_onhand_status = ReceiptEntry::check_material_onhand_status($jobNum, $assemblySeq, $jobSeq, $thisTranQty, $item->LotNum);
                                        //                         $job_status = ReceiptEntry::check_job_status($jobNum);
                                        //                     }
                                        //                     if($mtl_onhand_status['code'] == 0){
                                        //                         $results[] = [
                                        //                             'code' => 500,
                                        //                             'step' => 'IssueMaterial',
                                        //                             'transaction_code' => 500,
                                        //                             'status' => 'error',
                                        //                             'message' => 'Beberapa Komponen minus stok'
                                        //                         ];
                                        //                     }else{
                                        //                         if ($item->OnhandQty < $qty_issue || $issued < $qty_issue) {
                                        //                             $isReturn = false;
                                        //                             $post_issue_material = self::submit_issue_material(
                                        //                                 $assemblySeq,
                                        //                                 $jobNum,
                                        //                                 $row->MtlSeq,
                                        //                                 $item->BinNum,
                                        //                                 $row->BinName,
                                        //                                 $item->WarehouseCode,
                                        //                                 $row->WarehouseName,
                                        //                                 $item->LotNum,
                                        //                                 $item->OnhandQty,
                                        //                                 $isReturn,
                                        //                                 $row->PartNum
                                        //                             );
                                        //                             $issued = $item->OnhandQty;
                                        //                         } else {
                                        //                             $isReturn = false;
                                        //                             $post_issue_material = self::submit_issue_material(
                                        //                                 $assemblySeq,
                                        //                                 $jobNum,
                                        //                                 $row->MtlSeq,
                                        //                                 $item->BinNum,
                                        //                                 $row->BinName,
                                        //                                 $item->WarehouseCode,
                                        //                                 $row->WarehouseName,
                                        //                                 $item->LotNum,
                                        //                                 $qty_issue,
                                        //                                 $isReturn,
                                        //                                 $row->PartNum
                                        //                             );
                                        //                             $issued = $qty_issue;
                                        //                         }
                                        //                     }
                                        //                     // Log::info($post_issue_material);
                                        //                 }
                                        //             }else {
                                        //                 $results[] = [
                                        //                     'code' => 500,
                                        //                     'step' => 'IssueMaterial',
                                        //                     'transaction_code' => 500,
                                        //                     'status' => 'error',
                                        //                     'message' => 'Data Part bin tidak lengkap atau Qty Issue lebih dari On Hand Qty'
                                        //                 ];
                                        //             }
                                        //             if (($post_issue_material['transaction_code'] ?? 0) == 200) {
                                        //                 $total_success++;
                                        //             } else {
                                        //                 $results[] = [
                                        //                     'code' => 200,
                                        //                     'step' => 'IssueMaterial',
                                        //                     'transaction_code' => 500,
                                        //                     'status' => 'error',
                                        //                     'message' => 'Gagal submit issue material'
                                        //                 ];
                                        //             }
                                                    
                                        //         } else {
                                        //             $results[] = [
                                        //                 'code' => 200,
                                        //                 'step' => 'IssueMaterial',
                                        //                 'transaction_code' => 200,
                                        //                 'status' => 'success',
                                        //                 'message' => 'Tidak ada material yang di issue'
                                        //             ];
                                        //         }
                                        //     }
                                        // }
                                        // $data['total_success'] = $total_success;
                                        // if ($total_success == $db_job_mtl->count()) {
                                        //     $update_issue_material_status = ReceiptEntry::update_issue_material_status($packSlip, $vendorNum, $data['packLine']);
                                        // }
                                        $post_job_receipt = self::submit_job_receipt($jobNum, $inputOurQty, $totalQtyBefore, $LotNum, $ToWarehouseID, $PartNum, $ToBinID, $packSlip, $vendorNum, $data['packLine'], $rowMod);
                                        if ($post_job_receipt['code'] == 200) {
                                            $update_job_receipt_status = ReceiptEntry::update_job_receipt_status($packSlip, $vendorNum, $data['packLine']);
                                        }
                                }
                            }
                        } else {
                            $data['packLine'] = 0;
                            if ($jobNum !== '') {
                                if ($statusIssueJob[1] == 1) {
                                    $post_job_receipt = self::delete_receipt_job($jobNum, $totalQtyBefore, $LotNum, $ToWarehouseID, $PartNum, $ToBinID);
                                }
                            }
                            if ($jobNum !== '') {
                                if ($db_job_mtl->count() > 0) {
                                    $no = 0;
                                    foreach ($db_job_mtl as $row) {
                                        if ($row->WarehouseCode != '' && $row->BinNum != '') {
                                            $qty_issue = $row->QtyPer * $totalQtyBefore;
                                            $isReturn = true;
                                            $post_issue_material = self::submit_issue_material($assemblySeq, $jobNum, $row->MtlSeq, $row->BinNum, $row->BinName, $row->WarehouseCode, $row->WarehouseName, $LotNum, $qty_issue, $isReturn,$PartNum);
                                        }
                                        $no++;
                                    }
                                }
                            }
                        }
                    }

                    if ($rowMod != "D") {
                        $dbPartPs = ReceiptEntry::get_data_part_ps($PartNum);
                        if ($dbPartPs->count() > 0) {
                            foreach ($dbPartPs as $row) {
                                $mtlPartPS = $row->mtlpart_ps;
                                $qtyper_ps = $row->qtyper_ps;
                                $warehouse = $row->warehouse_ps;
                                $binNumPs = $row->binnum_ps;
                                $iumps = $row->ium_ps;
                                $isReturn = false;
                                $PartBin = DB::connection('sqlsrv4')
                                ->table('Erp.PartBin as pb')
                                ->where('pb.PartNum', $row->mtlpart_ps)
                                ->where('pb.BinNum', $row->binnum_ps)
                                ->where('pb.OnhandQty', '>', 0)
                                ->where('pb.WarehouseCode',$row->warehouse_ps)
                                ->select('pb.LotNum', 'pb.OnhandQty','pb.WarehouseCode')
                                ->orderByRaw("
                                    CASE 
                                        WHEN pb.LotNum = 'A' THEN 0
                                        WHEN ISNUMERIC(pb.LotNum) = 1 THEN 1
                                        ELSE 2 
                                    END
                                ")
                                ->orderByRaw("
                                    CASE 
                                        WHEN ISNUMERIC(pb.LotNum) = 1 
                                        THEN CAST(pb.LotNum AS INT) 
                                    END ASC
                                ")
                                ->get();
                                foreach($PartBin as $p){

                                    if ($totalQtyBefore > 0) {
                                        $tranQty = $totalQtyBefore * $qtyper_ps;
                                        if($p->OnhandQty < $tranQty){
                                        $isReturn = true;
                                            $posted_issue = self::submit_issue_miscellaneous($mtlPartPS, $qtyper_ps, $warehouse, $binNumPs, $p->LotNum, $tranQty, $isReturn);
                                            if (($posted_issue['code'] ?? 500) == 200) {
                                                $tranQty = $inputOurQty * $qtyper_ps;
                                                $isReturn = false;
                                                self::submit_issue_miscellaneous($mtlPartPS, $qtyper_ps, $warehouse, $binNumPs, $p->LotNum, $tranQty, $isReturn);
                                            }
                                        }

                                    } else {
                                        $tranQty = $inputOurQty * $qtyper_ps;
                                        if($p->OnhandQty < $tranQty){

                                        self::submit_issue_miscellaneous($mtlPartPS, $qtyper_ps, $warehouse, $binNumPs, $p->LotNum, $tranQty, $isReturn);
                                        }
                                    }
                                }
                               
                            }
                        }
                    } else {
                        $dbPartPs = ReceiptEntry::get_data_part_ps($PartNum);
                        
                        if ($dbPartPs->count() > 0) {
                            foreach ($dbPartPs as $row) {
                                $PartBin = DB::connection('sqlsrv4')
                                ->table('Erp.PartBin as pb')
                                ->where('pb.PartNum', $row->mtlpart_ps)
                                ->where('pb.BinNum', $row->binnum_ps)
                                ->where('pb.OnhandQty', '>', 0)
                                ->where('pb.WarehouseCode',$row->WarehouseCode)
                                ->select('pb.LotNum', 'pb.OnHandQty','pb.WarehouseCode')
                                ->orderByRaw("
                                    CASE 
                                        WHEN pb.LotNum = 'A' THEN 0
                                        WHEN ISNUMERIC(pb.LotNum) = 1 THEN 1
                                        ELSE 2 
                                    END
                                ")
                                ->orderByRaw("
                                    CASE 
                                        WHEN ISNUMERIC(pb.LotNum) = 1 
                                        THEN CAST(pb.LotNum AS INT) 
                                    END ASC
                                ")
                                ->get();
                                $mtlPartPS = $row->mtlpart_ps;
                                $qtyper_ps = $row->qtyper_ps;
                                $warehouse = $row->warehouse_ps;
                                $binNumPs = $row->binnum_ps;
                                $isReturn = true;
                                $tranQty = $totalQtyBefore * $qtyper_ps;
                                foreach($PartBin as $p){
                                    self::submit_issue_miscellaneous($mtlPartPS, $row->ium_ps, $warehouse, $binNumPs, $p->LotNum, $tranQty, $isReturn);
                                }
                            }
                        }
                    }
                }
            } catch (RequestException $e) {
                $data['code'] = 500;
                $data['status'] = $e->getMessage();
                $data['transaction_code'] = 500;
                $data['transaction_status'] = $e->getMessage();
            }
        }
        $data['temp_id'] = $packSlip . ' ' . $vendorNum;
        return $data;
    }
    public function submit_issue_miscellaneous($mtlPartPS, $iumps, $warehouse, $binNum, $lotNum, $qty, $isReturn)
{
    if ($qty < 0) {
        $isReturn = true;
        $qty = abs($qty);
    }

    $data = ['code' => 500, 'status' => 'Unknown error', 'transaction_code' => 500];

    if ($qty != 0) {
        $username = Auth::user()->username;
        $password = Crypt::decryptString(Auth::user()->epicor_password);
        $client = new Client();
        $host_api = self::get_host_api();

        try {
            $response = $client->request('POST', $host_api . 'Receipt/IssueMtlPs', [
                'json' => [
                    "mtlPartPS" => $mtlPartPS,
                    "warehouse" => $warehouse,
                    "ium" => $iumps,
                    "binNum" => $binNum,
                    "lotNum" => $lotNum,
                    "tranQty" => $qty,
                    "isReturn" => $isReturn,
                    'nik' => $username,
                    'password' => $password
                ],
                'headers' => ['Content-Type' => 'application/json'],
                'verify' => false,
            ]);

            $responseBody = json_decode($response->getBody()->getContents(), true);

            if (is_array($responseBody)) {
                $data['code'] = $responseBody['code'] ?? 500;
                $data['status'] = $responseBody['status'] ?? 'Unknown status';
                $data['transaction_code'] = $responseBody['data']['epi_code'] ?? 500;
            } else {
                $data['status'] = 'Response not valid JSON';
            }

        } catch (RequestException $e) {
            $data['status'] = $e->getMessage();
        }
    }

    return $data;
}

    // public function submit_issue_miscellaneous($mtlPartPS, $iumps, $warehouse, $binNum, $lotNum, $qty, $isReturn)
    // {
    //     if ($qty < 0) {
    //         $isReturn = true;
    //         $qty = abs($qty);
    //     }
    //     if ($qty <> 0) {
    //         $username = Auth::user()->username;
    //         $password = Crypt::decryptString(Auth::user()->epicor_password);
    //         $client = new Client();
    //         $data = [];
    //         $host_api = self::get_host_api();

    //         try {
    //             $response = $client->request('POST', $host_api . 'Receipt/IssueMtlPs', [
    //                 'json' => [
    //                     "mtlPartPS" => "$mtlPartPS",
    //                     "warehouse" => "$warehouse",
    //                     "ium" => "$iumps",
    //                     "binNum" => "$binNum",
    //                     "lotNum" => "$lotNum",
    //                     "tranQty" => "$qty",
    //                     "isReturn" => $isReturn,
    //                     'nik' => "$username",
    //                     'password' => "$password"
    //                 ],
    //                 'headers' => [
    //                     'Content-Type' => 'application/json',
    //                 ],
    //                 'verify' => false,
    //             ]);

    //             $responseBody = json_decode($response->getBody()->getContents(), true);
    //             $data['code'] = $responseBody['code'];
    //             $data['status'] = $responseBody['status'];
    //             if ($data['code'] == 200) {
    //                 $data['transaction_code'] = $responseBody['data']['epi_code'];
    //             } else {
    //                 $data['transaction_code'] = 500;
    //             }
    //         } catch (RequestException $e) {
    //             $data['code'] = 500;
    //             $data['transaction_code'] = 500;
    //             $data['status'] = $e->getMessage();
    //         }
    //     return $data;
    // }
    // }
    public function submit_issue_material(
        $assemblySeq, $jobNum, $jobMtlSeq,
        $binNum, $binNumDescription,
        $warehouseCode, $WarehouseDescription,
        $lotNum, $qty, $isReturn, $PartNum
    ) {
        if ($qty < 0) {
            $isReturn = true;
            $qty = abs($qty);
        }
    
        if ($qty <> 0) {
            $username = Auth::user()->username;
            $password = Crypt::decryptString(Auth::user()->epicor_password);
            $client   = new Client();
            $data     = [];
            $host_api = self::get_host_api();
            $lotNum_final = $lotNum;
            try {
                $response = $client->request('POST', $host_api . 'IssueMtl/MtlMovement', [
                    'json' => [
                        "assemblySeq"           => $assemblySeq,
                        "jobNum"                => "$jobNum",
                        "jobMtlSeq"             => $jobMtlSeq,
                        "binNum"                => "$binNum",
                        "binNumDescription"     => "$binNumDescription",
                        "warehouseCode"         => "$warehouseCode",
                        "WarehouseDescription"  => "$WarehouseDescription",
                        "lotNum"                => $lotNum_final,
                        "qty"                   => "$qty",
                        "rowMod"                => "A",
                        "isReturn"              => $isReturn,
                        'nik'                   => "$username",
                        'password'              => "$password"
                    ],
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                    'verify' => false,
                ]);
    
                $responseBody = json_decode($response->getBody()->getContents(), true);
                $data['code'] = $responseBody['code'];
                $data['status'] = $responseBody['status'];
    
                if ($data['code'] == 200) {
                    $data['transaction_code'] = $responseBody['data']['epi_code'];
                    $data['epi_status'] = $responseBody['data']['epi_status'] ?? null;
                } else {
                    $data['transaction_code'] = 500;
                }
            } catch (RequestException $e) {
                $data['code'] = 500;
                $data['transaction_code'] = 500;
                $data['status'] = $e->getMessage();
            }
        } else {
            $data['transaction_code'] = 200;
        }
    
        return $data;
    }
    
    public function get_po_info(Request $request)
    {
        $poNum = $request->poNum;
        $packSlip = $request->packSlip;
        $username = Auth::user()->username;
        $password = Crypt::decryptString(Auth::user()->epicor_password);
        $client = new Client();
        $data = [];
        $host_api = self::get_host_api();
        try {
            $response = $client->request('GET', $host_api . 'Receipt/GetPOInfo', [
                'json' => [
                    'nik' => "$username",
                    'password' => "$password",
                    'poNum' => $poNum,
                    'packSlip' => "$packSlip"
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'verify' => false,
            ]);
            $responseBody = json_decode($response->getBody()->getContents(), true);
            $data['code'] = $responseBody['code'];
            $data['status'] = $responseBody['status'];
            if ($data['code'] == 200) {
                $data['transaction_code'] = $responseBody['data']['epi_code'];
                $data['transaction_status'] = $responseBody['data']['epi_status'];
                if ($data['transaction_code'] == 200) {
                    $data['data'] = $responseBody['data']['rcvHead'];
                    $data['vendorNum'] = $responseBody['data']['rcvHead']['vendorNum'];
                    $data['purPointName'] = $responseBody['data']['rcvHead']['purPointName'];
                }
            }
        } catch (RequestException $e) {
            Log::error('API request failed', [
                'message' => $e->getMessage(),
                'request' => $e->getRequest(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null,
            ]);
            $data['code'] = 500;
            $data['desc'] = $e->getMessage();
        }
        return $data;
    }



    public function get_po_line_info(Request $request)
    {
        if ($request->temp_id != '') {
            $str = explode("~", Crypt::decryptString(str_replace("-", "=", $request->temp_id)));
            $packSlip = $str[0];
            $vendorNum = $str[1];
        } else {
            $packSlip = 0;
            $vendorNum = 0;
        }
        $poLine = $request->poLine;
        $poNum = $request->poNum;
        $username = Auth::user()->username;
        $password = Crypt::decryptString(Auth::user()->epicor_password);
        $client = new Client();
        $data = [];
        $host_api = self::get_host_api();
        try {
            $response = $client->request('GET', $host_api . 'Receipt/RcvDtlPo', [
                'json' => [
                    'nik' => "$username",
                    'password' => "$password",
                    'poNum' => $poNum,
                    'packSlip' => "$packSlip",
                    'poLine' => $poLine,
                    'vendorNum' => "$vendorNum"
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'verify' => false,
            ]);
            $responseBody = json_decode($response->getBody()->getContents(), true);
            $data['code'] = $responseBody['code'];
            $data['status'] = $responseBody['status'];
            if ($data['code'] == 200) {
                $data['transaction_code'] = $responseBody['data']['epi_code'];
                $data['transaction_status'] = $responseBody['data']['epi_status'];
                if ($data['transaction_code'] == 200) {
                    $data['wareHouseCode'] = $responseBody['data']['rcvDtls']['wareHouseCode'];
                    $data['wareHouseCodeDescription'] = $responseBody['data']['rcvDtls']['wareHouseCodeDescription'];
                    $data['binNum'] = $responseBody['data']['rcvDtls']['binNum'];
                    $data['partNum'] = $responseBody['data']['rcvDtls']['partNum'];
                    $data['partDescription'] = $responseBody['data']['rcvDtls']['partDescription'];
                    $data['ourQty'] = $responseBody['data']['rcvDtls']['ourQty'];
                    $data['vendorQty'] = $responseBody['data']['rcvDtls']['vendorQty'];
                    $data['ium'] = $responseBody['data']['rcvDtls']['ium'];
                    $data['pum'] = $responseBody['data']['rcvDtls']['pum'];
                    $data['jobNum'] = $responseBody['data']['rcvDtls']['jobNum'];
                    $data['jobSeq'] = $responseBody['data']['rcvDtls']['jobSeq'];
                    $data['assemblySeq'] = $responseBody['data']['rcvDtls']['assemblySeq'];
                    $data['jobRequiredQty'] = $responseBody['data']['rcvDtls']['jobRequiredQty'];
                    $WhseResult = explode('~', ReceiptEntry::get_part_whse_receipt($data['partNum']));
                    $whseDefault = $WhseResult[0];
                    $binDefault = $WhseResult[1];
                    $data['whseDefault'] = $whseDefault;
                    $data['binDefault'] = $binDefault;
                }
            }
        } catch (RequestException $e) {
            Log::error('API request failed', [
                'message' => $e->getMessage(),
                'request' => $e->getRequest(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null,
            ]);
            $data['code'] = 500;
            $data['desc'] = $e->getMessage();
        }
        return $data;
    }

    public function upload_attachment(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
        ]);

        if ($request->temp_id != '') {
            $str = explode("~", Crypt::decryptString(str_replace("-", "=", $request->temp_id)));
            $packSlip = $str[0];
            $vendorNum = $str[1];
        } else {
            $packSlip = 0;
            $vendorNum = 0;
        }
        $username = Auth::user()->username;
        $password = Crypt::decryptString(Auth::user()->epicor_password);
        $file = $request->file('file');
        $timestamp = now()->format('Ymd_His');
        $fileExtension = $file->getClientOriginalExtension();
        $fileName = $timestamp . '.' . $fileExtension;
        $description = $request->input('description');
        $fileContent = base64_encode(file_get_contents($file->getRealPath()));

        $client = new Client();
        $data = [];
        $host_api = self::get_host_api();

        try {
            $response = $client->request('POST', $host_api . 'Receipt/AttachFile', [
                'json' => [
                    'data' => $fileContent,
                    'docTypeID' => '', // Isi jika diperlukan
                    'fileName' => $fileName,
                    'parentTable' => 'RcvHead',
                    'DrawDesc' => "$description",
                    'packSlip' => "$packSlip",
                    'vendorNum' => $vendorNum,
                    'nik' => "$username",
                    'password' => "$password"
                ],
                'verify' => false,
            ]);

            $responseBody = json_decode($response->getBody()->getContents(), true);
            $data['code'] = $responseBody['code'];
            $data['status'] = $responseBody['status'];
            if ($data['code'] == 200) {
                $data['transaction_code'] = $responseBody['data']['epi_code'];
                $data['transaction_status'] = $responseBody['data']['epi_status'];
                if ($data['transaction_code'] == 200) {
                    $data['data'] = $responseBody['data']['rcvHeadAttch'];
                }
            }
        } catch (\Exception $e) {
            $data['code'] = 500;
            $data['desc'] = $e->getMessage();
        }
        return $data;
    }

    public function delete_attachment(Request $request)
    {
        if ($request->temp_id != '') {
            $str = explode("~", Crypt::decryptString(str_replace("-", "=", $request->temp_id)));
            $packSlip = $str[0];
            $vendorNum = $str[1];
        } else {
            $packSlip = 0;
            $vendorNum = 0;
        }
        $poNum = $request->poNum;
        $drawSeq = $request->DrawSeq;
        $rowMod = $request->rowMod;
        $username = Auth::user()->username;
        $password = Crypt::decryptString(Auth::user()->epicor_password);

        $client = new Client();
        $data = [];
        $host_api = self::get_host_api();
        try {
            $response = $client->request('POST', $host_api . 'Receipt/DettachFile', [
                'json' => [
                    "poNum" => $poNum,
                    "packSlip" => "$packSlip",
                    "vendorNum" => $vendorNum,
                    "DrawSeq" => $drawSeq,
                    "rowMod" => "$rowMod",
                    'nik' => "$username",
                    'password' => "$password"
                ],
                'verify' => false,
            ]);

            $responseBody = json_decode($response->getBody()->getContents(), true);
            $data['code'] = $responseBody['code'];
            $data['status'] = $responseBody['status'];
            if ($data['code'] == 200) {
                $data['transaction_code'] = $responseBody['data']['epi_code'];
                $data['transaction_status'] = $responseBody['data']['epi_status'];
                if ($data['transaction_code'] == 200) {
                    $data['data'] = $responseBody['data']['rcvHeadAttch'];
                }
            }
        } catch (\Exception $e) {
            $data['code'] = 500;
            $data['desc'] = $e->getMessage();
        }
        return $data;
    }

    public function submit_job_receipt($jobnum, $total_qty, $before_qty, $LotNum, $ToWarehouseID, $PartNum, $ToBinID, $rowMod)
    {
        $data['code'] = 500;
        $data['status'] = 'Failed !';
        $delete_first = true;
        if ($before_qty > 0) {
            $delete_first = self::delete_receipt_job($jobnum, $before_qty, $LotNum, $ToWarehouseID, $PartNum, $ToBinID);
        }
        if ($delete_first && $rowMod <> 'D') {
            $username = Auth::user()->username;
            $password = Crypt::decryptString(Auth::user()->epicor_password);
            $client = new Client();
            $data = [];
            $host_api = self::get_host_api();
            try {
                $response = $client->request('POST', $host_api . 'JobRec/BypassUpdate', [
                    'json' => [
                        'jobNum' => "$jobnum",
                        'qty' => $total_qty,
                        'lotNum' => "$LotNum",
                        'wareHouseCode' => "$ToWarehouseID",
                        'partNum' => "$PartNum",
                        'binNum' => "$ToBinID",
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
                $data['status'] = $responseBody['status'];
            } catch (RequestException $e) {
                $data['code'] = 500;
                $data['status'] = $e->getMessage();
            }
        }
        return $data;
    }

    public function delete_receipt_job($jobnum, $Qty, $LotNum, $ToWarehouseID, $PartNum, $ToBinID)
    {
        $Qty = (int) ($Qty * -1);
        $username = Auth::user()->username;
        $password = Crypt::decryptString(Auth::user()->epicor_password);
        $client = new Client();
        $data = [];
        $host_api = self::get_host_api();
        try {
            $response = $client->request('POST', $host_api . 'JobRec/BypassUpdate', [
                'json' => [
                    'jobNum' => "$jobnum",
                    'qty' => $Qty,
                    'lotNum' => "$LotNum",
                    'wareHouseCode' => "$ToWarehouseID",
                    'partNum' => "$PartNum",
                    'binNum' => "$ToBinID",
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
            $data['status'] = $responseBody['status'];
        } catch (RequestException $e) {
            $data['code'] = 500;
            $data['status'] = $e->getMessage();
        }
        // if ($data['code'] == 200) {
        //     $delete_history = InventoryMoveIn::delete_inventory_dtl($TranTypeID, $MonthID, $TranSeqID, $LineID) ; 
        // }  
        return $data;
    }

    //

    public function export_front_table(Request $request)
    {
        $vendor_id = Auth::user()->partner_id;
        $search = $request->front_table_search;
        date_default_timezone_set('Asia/Jakarta');
        $yearX = substr(date('Y'), 2, 2) . date('m');
        $year = ($request->range_date === null ? $yearX : $request->range_date);
        $data['full_name'] = Auth::user()->full_name;
        $data['list'] = ReceiptEntry::get_transaction_list($year, $search, $vendor_id)->get();
        $data['num'] = ReceiptEntry::get_transaction_list($year, $search, $vendor_id)->count();
        $data['ref_form'] = '';
        return view('receipt_entry.po_export', $data);
    }
    function print_view(Request $request)
    {
        $data['trc_unix_id'] = $request->trc_unix_id;
        $data['ref_form'] = $request->ref_form;
        if ($request->trc_unix_id != '') {
            $str = explode("~", Crypt::decryptString(str_replace("-", "=", $request->trc_unix_id)));
            $PackSlip = $str[0];
            $VendorNum = $str[1];
        } else {
            $PackSlip = 0;
            $VendorNum = 0;
        }
        $search = '';
        $data_detail = ReceiptEntry::get_detail_transaction_list($search, $PackSlip, $VendorNum)->count();
        if ($data_detail > 0) {
            return view('receipt_entry.gr_direct_print', [
                'PackSlip'=>$PackSlip,
                'VendorNum'=>$VendorNum
            ]);
        } else {
            $html = '
                <div class="col-md-12 col-lg-12 col-xl-12"> 
                    <div class="card h-100 flex-center bg-light-primary border-primary border border-dashed p-8"> 
                        <img onclick="getForm()" src="' . env('APP_ASSETS') . 'assets/media/svg/files/upload.svg" class="alt="" /> <br/>
                            <a class="text-hover-primary fs-5 fw-bolder mb-2">No Data Entry</a> 
                        <div class="fs-7 fw-bold text-gray-400">Please entry detail of receipt !</div> 
                    </div> 
                </div>';
            return response($html, 200)->header('Content-Type', 'text/html');
        }
    }
    function print_tag_label_view(Request $request)
    {
        $data['trc_unix_id'] = $request->trc_unix_id;
        $data['ref_form'] = $request->ref_form;
        if ($request->trc_unix_id != '') {
            $str = explode("~", Crypt::decryptString(str_replace("-", "=", $request->trc_unix_id)));
            $PackSlip = $str[0];
            $VendorNum = $str[1];
        } else {
            $PackSlip = 0;
            $VendorNum = 0;
        }
        $search = '';
        $data_detail = ReceiptEntry::get_detail_transaction_list($search, $PackSlip, $VendorNum)->count();
        if ($data_detail > 0) {
            return view('receipt_entry.tag_label_direct_print', [
                'PackSlip'=>$PackSlip,
                'VendorNum'=>$VendorNum
            ]);
        } else {
            $html = '
                    <div class="col-md-12 col-lg-12 col-xl-12"> 
                        <div class="card h-100 flex-center bg-light-primary border-primary border border-dashed p-8"> 
                            <img onclick="getForm()" src="' . env('APP_ASSETS') . 'assets/media/svg/files/upload.svg" class="alt="" /> <br/>
                                <a class="text-hover-primary fs-5 fw-bolder mb-2">No Data Entry</a> 
                            <div class="fs-7 fw-bold text-gray-400">Please entry detail of receipt !</div> 
                        </div> 
                    </div>';
            return response($html, 200)->header('Content-Type', 'text/html');
        }
    }
    public function scan_document(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $data['code'] = 200;
        $data['desc'] = '';
        $data['PackSlip'] = '';
        $data['VendorNum'] = '';
        $data['VendorName'] = '';
        $data['PONum'] = '';
        $data['LegalNumber'] = '';
        $data['trc_unix_id'] = '';
        $data['ReceiptComment'] = '';
        $data['ref_tab'] = 0;
        $data['EntryDate'] = date('Y-m-d');
        $data['ArrivedDate'] = date('Y-m-d');
        return view('receipt_entry/receipt_entry_header_scan', $data);
    }
    public function get_po_scan(Request $request)
{
    $PONum = $request->PONum;
    $ShipNum = $request->ShipNum;
    $id = $request->id;
    $data = DB::connection('vendor-app-epicor')
        ->table('VendorShpHead as a')
        ->join('VendorShpDtl as b', 'a.id', '=', 'b.VendorShpHeadID')
        ->select(
            'a.DocNum as LegalNumber',
            'a.PONum as PoNumber',
            'b.ItemNum as PartNum',
            'b.ShipQty as Qty',
            'a.ShipNum as PackingSlip',
            'b.POLine as PO',
            'b.Status as status',
            'b.VendorShpHeadID as id',
            'a.VendorNum'
        )
        ->where('a.PONum', $PONum)
        ->where('a.ShipNum', $ShipNum)
        ->where('a.id', $id)
        ->get();
    $dataEpicor = DB::connection('sqlsrv4')
        ->table('Erp.RcvDtl')
        ->where('PackSlip', $ShipNum)
        ->where('PONum', $PONum)
        ->get();

    $apiData = [];
    $username = Auth::user()->username;
    $password = Crypt::decryptString(Auth::user()->epicor_password);
    $client = new Client();
    $host_api = self::get_host_api();

    try {
        $response = $client->request('GET', $host_api . 'Receipt/GetPOInfo', [
            'json' => [
                'nik' => $username,
                'password' => $password,
                'poNum' => $PONum
            ],
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'verify' => false,
        ]);

        $responseBody = json_decode($response->getBody()->getContents(), true);
        $apiData['code'] = $responseBody['code'];
        $apiData['status'] = $responseBody['status'];

        if ($apiData['code'] == 200) {
            $apiData['transaction_code'] = $responseBody['data']['epi_code'];
            $apiData['transaction_status'] = $responseBody['data']['epi_status'];

            if ($apiData['transaction_code'] == 200) {
                $apiData['data'] = $responseBody['data']['rcvHead'];
                $apiData['vendorNum'] = $responseBody['data']['rcvHead']['vendorNum'] ?? '';
                $apiData['purPointName'] = $responseBody['data']['rcvHead']['purPointName'] ?? '';
            }
        }
    } catch (RequestException $e) {
        Log::error('API request failed', [
            'message' => $e->getMessage(),
            'request' => $e->getRequest(),
            'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null,
        ]);
        $apiData['code'] = 500;
        $apiData['desc'] = $e->getMessage();
    }
    $data = collect($data)->map(function ($item) {
        return array_map(function ($val) {
            return is_string($val) ? mb_convert_encoding($val, 'UTF-8', 'UTF-8') : $val;
        }, (array)$item);
    });

    $dataEpicor = collect($dataEpicor)->map(function ($item) {
        return array_map(function ($val) {
            return is_string($val) ? mb_convert_encoding($val, 'UTF-8', 'UTF-8') : $val;
        }, (array)$item);
    });

    return response()->json([
        'status' => 'success',
        'data' => $data,
        'apiData' => $apiData,
        'epicor' => $dataEpicor
    ], 200, [], JSON_UNESCAPED_UNICODE);
}
    public function rcvDtl(Request $request)
    {
        $PONum = $request->PONum;
        $PackSlip = $request->PackSlip;
        $PoLine = $request->PoLine;
        $vendorNum = $request->vendorNum;
        $username = Auth::user()->username;
        $password = Crypt::decryptString(Auth::user()->epicor_password);
        $client = new Client();
        $host_api = self::get_host_api();
        $response = $client->request('GET', $host_api . 'Receipt/RcvDtlPo', [
            'json' => [
                'nik' => "$username",
                'password' => "$password",
                'poNum' => $PONum,
                'packSlip' => "$PackSlip",
                'poLine' => $PoLine,
                'vendorNum' => "$vendorNum"
            ],
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'verify' => false,
        ]);
        $response = json_decode($response->getBody()->getContents(), true);
        return $response;
    }
    public function update_detail(Request $request)
    {
        $username = Auth::user()->username;
        $password = Crypt::decryptString(Auth::user()->epicor_password);
        $poNum = $request->poNum;
        $poLine = $request->poLine;
        $packSlip = $request->packSlip;
        $packLine = $request->packLine;
        $vendorNum = $request->vendorNum;
        $qtyOption = $request->qtyOption;
        $inputOurQty = $request->inputOurQty;
        $ium = $request->ium;
        $vendorQty = $request->vendorQty;
        $pum = $request->pum;
        $issuedComplete = $request->issuedComplete;
        $convOverride = $request->convOverride;
        $warehouseCode = $request->warehouseCode;
        $binNum = $request->binNum;
        $tranReference = $request->tranReference;
        $rowMod = $request->rowMod;
        $receivedComplete = $request->receivedComplete;
        $received = $request->received;
        $receiptDate = $request->receiptDate;
        $LotNum = $request->lotNum;
        $client = new Client();
        $host_api = self::get_host_api();
        $response = $client->request('POST', $host_api . 'Receipt/UpdateDetail', [
            'json' => [
                "poNum" => $poNum,
                "poLine" => $poLine,
                "packSlip" => $packSlip,
                "packLine" => $packLine,
                "qtyOption" => "Our",
                "inputOurQty" => $inputOurQty,
                "ium" => $ium,
                "vendorQty" => $vendorQty,
                "pum" => $pum,
                "convOverride" => false,
                "warehouseCode" => $warehouseCode,
                "binNum" => $binNum,
                "tranReference" => "",
                "issuedComplete" => false,
                "lotNum" =>  $LotNum,
                "vendorNum" => $vendorNum,
                "rowMod" => "A", // A / U / D
                "receivedComplete" => false, //Complete checkbox
                "received" => true, // Recevied Checkbox
                "receiptDate" => $receiptDate,
                'nik' => "$username",
                'password' => "$password"
            ],
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'verify' => false,
        ]);
        $response = json_decode($response->getBody()->getContents(), true);
        return $response;
    }
    public function new_insert_gr(Request $request)
    {
        $PONum = $request->PONum;
        $PackSlip = $request->PackSlip;
        $ArrivedDate = $request->ArrivedDate;
        $ReceiptComment = $request->ReceiptComment;
        $LegalNumber = $request->LegalNumber;
        $data_vend = DB::connection('vendor-app-epicor')
            ->table('VendorShpHead')
            ->where('ShipNum',$PackSlip)
            ->where('PONum',$PONum)
            ->first();
        $vendorNum = $request->vendorNum ?? $data_vend->VendorNum;
        $VendorName = $request->VendorName;
        // $defaultLotDate = date('dmy');
        // $ArrivedDate = Carbon::parse($data_vend->EstDlvDate)->format('Y-m-d');
        $id = $request->id;
        $username = Auth::user()->username;
        $password = Crypt::decryptString(Auth::user()->epicor_password);
        $client = new Client();
        $host_api = self::get_host_api();
        $results = [];
        try {
            $data_sj = DB::connection('vendor-app-epicor')
            ->table('VendorShpHead')
            ->where('id', $id)
            ->where('ShipNum',$PackSlip)
            ->where('PONum',$PONum)
            ->where('Status',3)
            ->first();
            if ($data_sj) {
                return response()->json([
                    'status' => 'Data sudah pernah di GR',
                    'code' => 500,
                    'message' =>'Data sudah pernah di GR'
                ]);
            }
            try {
                $getNew = $client->post($host_api . 'Receipt/GetNew', [
                    'json' => [
                        'poNum' => $PONum,
                        'packSlip' => $PackSlip,
                        'nik' => $username,
                        'receiptComment' => $ReceiptComment,
                        'arrivalDate' => $ArrivedDate,
                        'password' => $password
                    ],
                    'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
                    'verify' => false,
                ]);

                $responseGetNew = json_decode($getNew->getBody()->getContents(), true);
                \Log::info($responseGetNew);
                if (!isset($responseGetNew['code']) || $responseGetNew['code'] != 200) {
                    $results[] = [
                        'step' => 'GetNew',
                        'status' => 'error',
                        'code' => $responseGetNew['code'] ?? 500,
                        'message' => $responseGetNew['status'] ?? 'Gagal membuat GetNew'
                    ];
                } else {
                    $results[] = [
                        'step' => 'GetNew',
                        'status' => 'success',
                        'code' => $responseGetNew['code'] ?? 200,
                        'message' => $responseGetNew['status'] ?? 'Berhasil membuat GetNew'
                    ];
                }
            } catch (\Throwable $e) {
                $results[] = [
                    'step' => 'GetNew Exception',
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
            }
            try {
                $updateHeader = $client->post($host_api . 'Receipt/UpdateHeader', [
                    'json' => [
                        'nik' => $username,
                        'password' => $password,
                        'poNum' => $PONum,
                        'receiptComment' => $ReceiptComment,
                        'arrivalDate' => $ArrivedDate,
                        'packSlip' => $PackSlip,
                        'vendorNum' => $vendorNum,
                        'rowMod' => 'U'
                    ],
                    'headers' => ['Content-Type' => 'application/json'],
                    'verify' => false,
                ]);

                $resUpdateHdr = json_decode($updateHeader->getBody()->getContents(), true);
                \Log::info($resUpdateHdr);
                if (!isset($resUpdateHdr['code']) || $resUpdateHdr['code'] != 200) {
                    $results[] = [
                        'step' => 'UpdateHeader',
                        'status' => 'error',
                        'code' => $resUpdateHdr['code'] ?? 500,
                        'message' => $resUpdateHdr['status'] ?? 'Gagal update header'
                    ];
                } else {
                    $results[] = [
                        'step' => 'UpdateHeader',
                        'status' => 'success',
                        'code' => $resUpdateHdr['code'] ?? 200,
                        'message' => $resUpdateHdr['status'] ?? 'Berhasil update header'
                    ];
                }
            } catch (\Throwable $e) {
                $results[] = [
                    'step' => 'UpdateHeader Exception',
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
            }
            $vendorShp = DB::connection('vendor-app-epicor')
                ->table('VendorShpHead as a')
                ->join('VendorShpDtl as b', 'b.VendorShpHeadID', '=', 'a.id')
                ->select('b.POLine', 'b.ShipQty','b.ItemNum','a.EstDlvDate as lot')
                ->where('a.id', $id)
                // ->distinct()
                ->get();
            $itemNums = $vendorShp->pluck('ItemNum')->toArray();
            $PartTb = DB::connection('sqlsrv4')
                ->table('Part')
                ->whereIn('PartNum', $itemNums)
                ->select('BinReceipt_c', 'WhseReceipt_c', 'PartNum')
                ->get();
            $missingParts = $PartTb->filter(function ($part) {
                return empty($part->WhseReceipt_c) || empty($part->BinReceipt_c);
            });
            if ($missingParts->isNotEmpty()) {
                return response()->json([
                    'code' => 4212,
                    'transaction_status' => 'Wh code dan Bin kosong di Item : ',
                    'status' => $missingParts->pluck('PartNum')->values()
                ]);
            }
            foreach ($vendorShp as $item) {
                $poLine = $item->POLine;
                $QtyDtl = $item->ShipQty;
                $defaultLotDate = Carbon::parse($item->lot)->format('dmy');
                try {
                    try {
                        $rcvDtl = $client->request('GET', $host_api . 'Receipt/RcvDtlPo', [
                            'json' => [
                                'nik' => "$username",
                                'password' => "$password",
                                'poNum' => $PONum,
                                'packSlip' => "$PackSlip",
                                'poLine' => $poLine,
                                'vendorNum' => "$vendorNum"
                            ],
                            'headers' => [
                                'Content-Type' => 'application/json',
                            ],
                            'verify' => false,
                        ]);

                        $rcvResponse = json_decode($rcvDtl->getBody()->getContents(), true);
                        \Log::info($rcvResponse);
                        if ($rcvResponse['status'] == 'Ok' && $rcvResponse['data']['epi_code'] == 200) {
                            $results[] = [
                                'poLine' => $poLine,
                                'step' => 'RcvDtlPo',
                                'status' => 'success',
                                'code' => $rcvResponse['data']['epi_code'],
                                'message' => $rcvResponse['data']['epi_status']
                            ];
                        } else if($rcvResponse['status'] == 'Ok' && $rcvResponse['data']['epi_code'] !== 200) {
                            $results[] = [
                                'poLine' => $poLine,
                                'step' => 'RcvDtlPo',
                                'status' => 'error',
                                'code' => $rcvResponse['data']['epi_code'],
                                'message' => $rcvResponse['data']['epi_status'] ?? 'Gagal update RCVDTLPO'
                            ];
                        }else{
                            $results[] = [
                                'poLine' => $poLine,
                                'step' => 'RcvDtlPo',
                                'status' => 'error',
                                'code' => $rcvResponse['code']?? 500,
                                'message' => $rcvResponse['status'] ?? 'Gagal update RCVDTLPO'
                            ];
                        }
                    } catch (\Throwable $e) {
                        $results[] = [
                            'poLine' => $poLine,
                            'step' => 'RcvDtlPo Exception',
                            'status' => 'error',
                            'message' => $e->getMessage()
                        ];
                        continue;
                    }

                    $rcvDetails = $rcvResponse['data']['rcvDtls'] ?? [];
                    if (isset($rcvDetails['poRelNum'])) {
                        $rcvDetails = [$rcvDetails];
                    }
                    $seqIndex = 1;
                    foreach ($rcvDetails as $itemDtl) {
                        $seqnum = (($poLine * 1000) + $seqIndex);
                        $seqIndex++;
                        $PoRelNum = $itemDtl['poRelNum'] ?? null;
                        $inputOurQty = $QtyDtl;
                        $packLine = $itemDtl['packLine'] ?? $seqIndex++;
                        $jobNum = $itemDtl['jobNum'] ?? '';
                        $jobSeq = $itemDtl['jobSeq'] ?? null;
                        $assemblySeq = $itemDtl['assemblySeq'] ?? null;
                        $ium = $itemDtl['ium'] ?? '';
                        $vendorQty = $itemDtl['vendorQty'] ?? $QtyDtl;
                        $pum = $itemDtl['pum'] ?? '';
                        $convOverride = $itemDtl['convOverride'] ?? null;
                        $wareHouseCode = $itemDtl['wareHouseCode'] ?? null;
                        $tranReference = $itemDtl['tranReference'] ?? '';
                        $qtyOption = $itemDtl['qtyOption'] ?? null;
                        $PartNum = $itemDtl['partNum'] ?? null;
                        $receiptDate = $itemDtl['receiptDate'] ?? date('Y-m-d');
                        // $receiptDate = $ArrivedDate;
                        $ToBinID = $itemDtl['binNum'] ?? null;
                        $rowMod = ($packLine == 0 ? "A" : "U");
                        $partName = $itemDtl['partDescription'] ?? null;
                        $PartTb = DB::connection('sqlsrv4')
                            ->table('Part')
                            ->where('PartNum', $PartNum)
                            ->select('BinReceipt_c','WhseReceipt_c')
                            ->first();
                        $Whname = $PartTb->WhseReceipt_c;
                        $binNum = $PartTb->BinReceipt_c;
                        if (!$Whname || !$binNum) {
                            return response()->json([
                                'poLine' => $poLine,
                                'Part' => $PartNum,
                                'step' => 'Part',
                                'status' => 'error',
                                'message' => 'Warehouse Code atau BinNum Kosong '.$PartNum
                            ]);
                            }
                       
                        $ourRemQty = ReceiptEntry::get_qty_balance_po_line(
                            $PONum,
                            $poLine,
                            $PoRelNum,
                            $inputOurQty,
                            $PackSlip,
                            $packLine,
                            $vendorNum
                        );
                        $totalQtyCompleted = ReceiptEntry::get_job_qty_completed(
                            $inputOurQty,
                            $PackSlip,
                            $packLine,
                            $jobNum,
                            $vendorNum
                        );
                        $totalQtyBefore = ReceiptEntry::get_total_qty_before($PackSlip, $packLine, $vendorNum);
                        $statusIssueJob = ($packLine > 0)
                            ? explode("~", ReceiptEntry::get_status_issue_job($PackSlip, $packLine, $vendorNum))
                            : explode("~", "1~1");
                        $thisTranQty = $inputOurQty - $totalQtyBefore;
                        $part_onhand_status = ['code' => 1, 'status' => 'Ok !'];
                        $mtl_onhand_status = ['code' => 1, 'status' => 'Ok !'];
                        $job_status = ['code' => 1, 'status' => 'Ok !'];

                        if ($jobNum != '' && $rowMod == "D") {
                            $part_onhand_status = ReceiptEntry::check_part_onhand_status($jobNum, $thisTranQty, $defaultLotDate);
                            $job_status = ReceiptEntry::check_job_status($jobNum);
                        } elseif ($jobNum != '' && $thisTranQty < 0) {
                            $part_onhand_status = ReceiptEntry::check_part_onhand_status($jobNum, $thisTranQty, $defaultLotDate);
                            $job_status = ReceiptEntry::check_job_status($jobNum);
                        }
                        if ($jobNum != '' && ($job_status['code'] ?? 1) == 0) {
                            $data = [
                                'code' => 200,
                                'transaction_code' => 500,
                                'transaction_status' => $job_status['status']
                            ];
                            $results[] = [
                                'poLine' => $poLine,
                                'PoRelNum' => $PoRelNum,
                                'step' => 'Validation',
                                'status' => 'error',
                                'message' => $job_status['status']
                            ];
                            continue;
                        } elseif ($inputOurQty == 0 && $rowMod != "D") {
                            $results[] = [
                                'poLine' => $poLine,
                                'PoRelNum' => $PoRelNum,
                                'step' => 'Validation',
                                'status' => 'error',
                                'message' => 'Qty tidak boleh sama dengan 0'
                            ];
                            continue;
                        } elseif ($jobNum != '' && $thisTranQty < 0 && ($part_onhand_status['code'] ?? 1) == 0) {
                            $results[] = [
                                'poLine' => $poLine,
                                'PoRelNum' => $PoRelNum,
                                'step' => 'Validation',
                                'status' => 'error',
                                'message' => $part_onhand_status['status']
                            ];
                            continue;
                        } elseif ($jobNum != '' && $thisTranQty > 0 && ($mtl_onhand_status['code'] ?? 1) == 0) {
                            $results[] = [
                                'poLine' => $poLine,
                                'PoRelNum' => $PoRelNum,
                                'step' => 'Validation',
                                'status' => 'error',
                                'message' => $mtl_onhand_status['status']
                            ];
                            continue;
                        }
                        $receivedComplete = ($ourRemQty <= 0);
                        $issuedComplete = ($jobNum != '' && $totalQtyCompleted <= 0) ? true : false;
                        $received = true;
                        $resUpdateDtl = null;
                        try {
                            $payload = [
                                'nik' => (string) $username,
                                'password' => (string) $password,
                                'poNum' => $PONum,
                                'poLine' => $poLine,
                                'packSlip' => $PackSlip,
                                'packLine' => $packLine,
                                'vendorNum' => $vendorNum,
                                'qtyOption' => $qtyOption,
                                'inputOurQty' => $QtyDtl,
                                'ium' => (string) $ium,
                                'vendorQty' => $QtyDtl,
                                'pum' => (string) $pum,
                                'issuedComplete' => $issuedComplete,
                                'convOverride' => $convOverride,
                                'warehouseCode' =>$Whname,
                                'binNum' => $binNum,
                                'tranReference' => ($tranReference === 'undefined' ? "" : $tranReference),
                                'rowMod' => $rowMod,
                                'receivedComplete' => $receivedComplete,
                                'received' => $received,
                                'receiptDate' => $receiptDate,
                                'lotNum' => $defaultLotDate
                            ];
                            $updateDetail = $client->request('POST', $host_api . 'Receipt/UpdateDetail', [
                                'json' => $payload,
                                'headers' => ['Content-Type' => 'application/json'],
                                'verify' => false,
                            ]);

                            $resUpdateDtl = json_decode($updateDetail->getBody()->getContents(), true);
                            // dd($resUpdateDtl);
                            \Log::info($resUpdateDtl);
                            $dataCode = $resUpdateDtl['code'] ?? 500;
                            $dataStatus = $resUpdateDtl['status'] ?? null;

                            if ($dataCode == 200) {
                                $packLineResp = $resUpdateDtl['data']['rcvDtl']['packLine'] ?? $packLine;
                                $results[] = [
                                    'poLine' => $poLine,
                                    'PoRelNum' => $PoRelNum,
                                    'step' => 'UpdateDetail',
                                    'status' => 'success',
                                    'epi_code' => $resUpdateDtl['data']['epi_code'] ?? null,
                                    'epi_status' => $resUpdateDtl['data']['epi_status'] ?? null
                                ];
                                $transaction_code = $resUpdateDtl['data']['epi_code'] ?? null;
                                if (!empty($jobNum) && $transaction_code == 200) {
                                    $db_job_mtl = ReceiptEntry::get_list_job_mtl($jobNum, $assemblySeq, $jobSeq);
                                    // dd($db_job_mtl);
                                    // $total_success = 0;
                                    // if ($db_job_mtl->count() > 0) {
                                    //     foreach ($db_job_mtl as $row) {
                                    //         if (!empty($row->WarehouseCode) && !empty($row->BinNum)) {
                                    //             $PartBin = DB::connection('sqlsrv4')
                                    //                 ->table('Erp.PartBin as pb')
                                    //                 ->where('pb.PartNum', $row->PartNum)
                                    //                 ->where('pb.BinNum', $row->BinNum)
                                    //                 ->where('pb.OnhandQty', '>', 0)
                                    //                 ->where('pb.WarehouseCode',$row->WarehouseCode)
                                    //                 ->select('pb.LotNum', 'pb.OnhandQty','pb.WarehouseCode')
                                    //                 ->orderByRaw("
                                    //                     CASE 
                                    //                         WHEN pb.LotNum = 'A' THEN 0
                                    //                         WHEN ISNUMERIC(pb.LotNum) = 1 THEN 1
                                    //                         ELSE 2 
                                    //                     END
                                    //                 ")
                                    //                 ->orderByRaw("
                                    //                     CASE 
                                    //                         WHEN ISNUMERIC(pb.LotNum) = 1 
                                    //                         THEN CAST(pb.LotNum AS INT) 
                                    //                     END ASC
                                    //                 ")
                                    //                 ->get();
                                    //             $issued = 0;
                                    //             $qty_issue = $row->QtyPer * ($inputOurQty - $totalQtyBefore);
                                    //             $onHandQty = $PartBin->sum('OnhandQty');
                                    //             if ($PartBin->count() > 0 && $onHandQty > $qty_issue) {
                                    //                 foreach ($PartBin as $item) {
                                    //                     // $lotNum = $item->LotNum;
                                    //                     // $qty_issue = $row->QtyPer * ($inputOurQty - $totalQtyBefore);
                                    //                     // $MaterialQty = $item->OnHandQty;
                                    //                     if ($thisTranQty > 0) {
                                    //                         $mtl_onhand_status = ReceiptEntry::check_material_onhand_status($jobNum, $assemblySeq, $jobSeq, $thisTranQty, $item->LotNum);
                                    //                         $job_status = ReceiptEntry::check_job_status($jobNum);
                                    //                     }
                                    //                     if ($mtl_onhand_status['code'] ==0) {
                                    //                         $results[] = [
                                    //                             'code' => 500,
                                    //                             'step' => 'IssueMaterial',
                                    //                             'transaction_code' => 500,
                                    //                             'status' => 'error',
                                    //                             'message' => 'Beberapa komponen minus stock'
                                    //                         ];
                                    //                     } else {
                                    //                         if ($item->OnhandQty < $qty_issue || $issued < $qty_issue) {
                                    //                             $isReturn = false;
                                    //                             $post_issue_material = self::submit_issue_material(
                                    //                                 $assemblySeq,
                                    //                                 $jobNum,
                                    //                                 $row->MtlSeq,
                                    //                                 $row->BinNum,
                                    //                                 $row->BinName,
                                    //                                 $row->WarehouseCode,
                                    //                                 $row->WarehouseName,
                                    //                                 $item->LotNum,
                                    //                                 $item->OnhandQty,
                                    //                                 $isReturn,
                                    //                                 $row->PartNum
                                    //                             );
                                    //                             $issued = $item->OnhandQty;
                                    //                         } else {
                                    //                             $isReturn = false;
                                    //                             $post_issue_material = self::submit_issue_material(
                                    //                                 $assemblySeq,
                                    //                                 $jobNum,
                                    //                                 $row->MtlSeq,
                                    //                                 $row->BinNum,
                                    //                                 $row->BinName,
                                    //                                 $row->WarehouseCode,
                                    //                                 $row->WarehouseName,
                                    //                                 $item->LotNum,
                                    //                                 $qty_issue,
                                    //                                 $isReturn,
                                    //                                 $row->PartNum
                                    //                             );
                                    //                             $issued = $qty_issue;
                                    //                         }
                                    //                     }
                                                        
                                    //                     // Log::info($post_issue_material);
                                    //                 }
                                    //             }else {
                                    //                 $results[] = [
                                    //                     'code' => 500,
                                    //                     'step' => 'IssueMaterial',
                                    //                     'transaction_code' => 500,
                                    //                     'status' => 'error',
                                    //                     'message' => 'Data Part bin tidak lengkap atau Qty Issue lebih dari On Hand Qty'
                                    //                 ];
                                    //             }
                                    //             if (($post_issue_material['transaction_code'] ?? 0) == 200) {
                                    //                 $total_success++;
                                    //             } else {
                                    //                 $results[] = [
                                    //                     'code' => 200,
                                    //                     'step' => 'IssueMaterial',
                                    //                     'transaction_code' => 500,
                                    //                     'status' => 'error',
                                    //                     'message' => 'Gagal submit issue material'
                                    //                 ];
                                    //             }
                                    //         } else {
                                    //             $results[] = [
                                    //                 'code' => 200,
                                    //                 'step' => 'IssueMaterial',
                                    //                 'transaction_code' => 200,
                                    //                 'status' => 'success',
                                    //                 'message' => 'Tidak ada material yang di issue'
                                    //             ];
                                    //         }
                                    //     }
                                    // }
                                    // if ($db_job_mtl->count() > 0 && $total_success == $db_job_mtl->count()) {
                                    //     ReceiptEntry::update_issue_material_status($PackSlip, $vendorNum, $packLineResp ?? $packLine);
                                    // }
                                    $totalQty = (int) $inputOurQty;
                                    $post_job_receipt = self::submit_job_receipt($jobNum, $totalQty, $totalQtyBefore, $defaultLotDate, $Whname, $PartNum, $binNum, $rowMod);
                                    if (($post_job_receipt['code'] ?? 500) == 200) {
                                        ReceiptEntry::update_job_receipt_status($PackSlip, $vendorNum, $packLineResp ?? $packLine);
                                    } else {
                                        $results[] = [
                                            'code' => 200,
                                            'step' => 'JobReceipt',
                                            'transaction_code' => 500,
                                            'status' => 'error',
                                            'message' => 'Gagal update job receipt'
                                        ];
                                    }
                                }else{
                                    $results[] = [
                                        'code' => 200,
                                        'step' => 'Job Number',
                                        'transaction_code' => 200,
                                        'status' => 'error',
                                        'message' => 'Berhasil update GR namun JO tidak ada'
                                    ];
                                }
                                if ($rowMod != "D") {
                                    $dbPartPs = ReceiptEntry::get_data_part_ps($PartNum);
                                    if ($dbPartPs->count() > 0) {
                                        foreach ($dbPartPs as $row) {
                                            $mtlPartPS = $row->mtlpart_ps;
                                            $qtyper_ps = $row->qtyper_ps;
                                            $warehouse = $row->warehouse_ps;
                                            $binNumPs = $row->binnum_ps;
                                            $iumps = $row->ium_ps;
                                            $isReturn = false;
                                            $PartBin = DB::connection('sqlsrv4')
                                            ->table('Erp.PartBin as pb')
                                            ->where('pb.PartNum', $row->mtlpart_ps)
                                            ->where('pb.BinNum', $row->binnum_ps)
                                            ->where('pb.OnhandQty', '>', 0)
                                            ->where('pb.WarehouseCode',$row->warehouse_ps)
                                            ->select('pb.LotNum', 'pb.OnhandQty','pb.WarehouseCode')
                                            ->orderByRaw("
                                                CASE 
                                                    WHEN pb.LotNum = 'A' THEN 0
                                                    WHEN ISNUMERIC(pb.LotNum) = 1 THEN 1
                                                    ELSE 2 
                                                END
                                            ")
                                            ->orderByRaw("
                                                CASE 
                                                    WHEN ISNUMERIC(pb.LotNum) = 1 
                                                    THEN CAST(pb.LotNum AS INT) 
                                                END ASC
                                            ")
                                            ->get();
                                            foreach($PartBin as $p){
                                                if ($totalQtyBefore > 0) {
                                                    $tranQty = $totalQtyBefore * $qtyper_ps;
                                                    if($p->OnhandQty < $tranQty){
                                                    $isReturn = true;
                                                        $posted_issue = self::submit_issue_miscellaneous($mtlPartPS, $qtyper_ps, $warehouse, $binNumPs, $p->LotNum, $tranQty, $isReturn);
                                                        if (($posted_issue['code'] ?? 500) == 200) {
                                                            $tranQty = $inputOurQty * $qtyper_ps;
                                                            $isReturn = false;
                                                            self::submit_issue_miscellaneous($mtlPartPS, $qtyper_ps, $warehouse, $binNumPs, $p->LotNum, $tranQty, $isReturn);
                                                        }
                                                    }
            
                                                } else {
                                                    $tranQty = $inputOurQty * $qtyper_ps;
                                                    if($p->OnhandQty < $tranQty){
            
                                                    self::submit_issue_miscellaneous($mtlPartPS, $qtyper_ps, $warehouse, $binNumPs, $p->LotNum, $tranQty, $isReturn);
                                                    }
                                                }
                                           
                                        }
                                    }
                                        } else {
                                            $dbPartPs = ReceiptEntry::get_data_part_ps($PartNum);
                                            
                                            if ($dbPartPs->count() > 0) {
                                                foreach ($dbPartPs as $row) {
                                                    $PartBin = DB::connection('sqlsrv4')
                                                    ->table('Erp.PartBin as pb')
                                                    ->where('pb.PartNum', $row->mtlpart_ps)
                                                    ->where('pb.BinNum', $row->binnum_ps)
                                                    ->where('pb.OnhandQty', '>', 0)
                                                    ->where('pb.WarehouseCode',$row->WarehouseCode)
                                                    ->select('pb.LotNum', 'pb.OnhandQty','pb.WarehouseCode')
                                                    ->orderByRaw("
                                                        CASE 
                                                            WHEN pb.LotNum = 'A' THEN 0
                                                            WHEN ISNUMERIC(pb.LotNum) = 1 THEN 1
                                                            ELSE 2 
                                                        END
                                                    ")
                                                    ->orderByRaw("
                                                        CASE 
                                                            WHEN ISNUMERIC(pb.LotNum) = 1 
                                                            THEN CAST(pb.LotNum AS INT) 
                                                        END ASC
                                                    ")
                                                    ->get();
                                                    $mtlPartPS = $row->mtlpart_ps;
                                                    $qtyper_ps = $row->qtyper_ps;
                                                    $warehouse = $row->warehouse_ps;
                                                    $binNumPs = $row->binnum_ps;
                                                    $isReturn = true;
                                                    $tranQty = $totalQtyBefore * $qtyper_ps;
                                                    foreach($PartBin as $p){
                                                        self::submit_issue_miscellaneous($mtlPartPS, $row->ium_ps, $warehouse, $binNumPs, $p->LotNum, $tranQty, $isReturn);
                                                    }
                                                }
                                            }
                                }
                            }
                    } else {
                        $results[] = [
                            'poLine' => $poLine,
                            'PoRelNum' => $PoRelNum,
                            'step' => 'UpdateDetail',
                            'status' => 'error',
                            'epi_code' => $resUpdateDtl['code'] ?? 500,
                            'epi_status' => $resUpdateDtl['status'] ?? ($resUpdateDtl['message'] ?? 'UpdateDetail gagal')
                        ];
                    }

                        } catch (\Throwable $e) {
                            \Log::error('UpdateDetail exception', ['poLine' => $poLine, 'exception' => $e->getMessage()]);
                            $resUpdateDtl = [
                                'code' => 500,
                                'status' => $e->getMessage()
                            ];
                            $results[] = [
                                'poLine' => $poLine,
                                'step' => 'UpdateDetail Exception',
                                'status' => 'error',
                                'message' => $e->getMessage(),
                                'res' => $resUpdateDtl
                            ];
                        }
                    }

                } catch (\Throwable $e) {
                    \Log::error('processing poLine failed', ['poLine' => $poLine, 'exception' => $e->getMessage()]);
                    $results[] = [
                        'poLine' => $poLine,
                        'step' => 'Processing Exception',
                        'status' => 'error',
                        'message' => $e->getMessage()
                    ];
                }
            }
            $updateShp = ReceiptEntry::vendor_shp_update($id, 3, 'DELIVERED');
                if ($updateShp == true) {
                    $results[] = [
                        'step' => 'UpdateShp',
                        'status' => 'success',
                        'code' =>  200,
                        'message' => 'Berhasil update Shp'
                    ];
                } else {
                    $results[] = [
                        'step' => 'UpdateShp',
                        'status' => 'error',
                        'code' => 500,
                        'message' =>'Gagal Update Shp'
                    ];
                }
            $all_success = collect($results)->every(function ($r) {
                return isset($r['status']) && $r['status'] === 'success';
            });

            // if ($all_success) {
                
            // }

            $trc_id = Crypt::encryptString($PackSlip . '~' . $vendorNum);
            $ref_doc = str_replace('=', '-', $trc_id);

            return response()->json([
                'code' => 200,
                'message' => 'Processed all lines',
                'ref_doc' => $ref_doc,
                'results' => $results,
                'legalNumber' => $LegalNumber,
                'vendor' => $VendorName,
                'vendorNum' => $vendorNum
            ]);

        } catch (\Throwable $th) {
            \Log::error('new_insert_gr unexpected exception', ['exception' => $th->getMessage()]);
            return response()->json(['code' => 500, 'message' => 'Unexpected error: ' . $th->getMessage()]);
        }
    }
    public function insert_gr(Request $request)
    {
        $PONum = $request->PONum;
        $PackSlip = $request->PackSlip;
        $ArrivedDate = $request->ArrivedDate;
        $ReceiptComment = $request->ReceiptComment;

        $username = Auth::user()->username;
        $password = Crypt::decryptString(Auth::user()->epicor_password);
        $client = new Client();
        $data = [];
        $host_api = self::get_host_api();

        try {
            $response = $client->post($host_api . 'Receipt/GetNew', [
                'json' => [
                    'poNum' => $PONum,
                    'packSlip' => $PackSlip,
                    'nik' => $username,
                    'receiptComment' => $ReceiptComment,
                    'arrivalDate' => $ArrivedDate,
                    'password' => $password
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'verify' => false,
            ]);

            $responseBody = json_decode($response->getBody()->getContents(), true);

            $data['code'] = $responseBody['code'];
            $data['status'] = $responseBody['status'];

            if ($data['code'] == 200) {
                $data['transaction_code'] = $responseBody['data']['epi_code'];
                $data['transaction_status'] = $responseBody['data']['epi_status'];

                if ($data['transaction_code'] == 200) {
                    $rcvHead = $responseBody['data']['rcvHead'];
                    $data['data'] = $rcvHead;
                    $data['vendorNum'] = $rcvHead['vendorNum'];
                    $data['packSlip'] = $rcvHead['packSlip'];
                    $data['legalNumber'] = $rcvHead['legalNumber'];
                    $data['purPointName'] = $rcvHead['purPointName'];

                    // Buat ref_doc seperti get_new_gr
                    $trc_id = Crypt::encryptString($data['packSlip'] . '~' . $data['vendorNum']);
                    $data['ref_doc'] = str_replace('=', '-', $trc_id);
                }
            }
        } catch (RequestException $e) {
            Log::error('API request failed (insert_gr)', [
                'message' => $e->getMessage(),
                'request' => $e->getRequest(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null,
            ]);

            $data['code'] = 500;
            $data['desc'] = $e->getMessage();
        }

        return $data;
    }
    public function delete_gr(Request $request)
    {
        if ($request->temp_id != '') {
            $str = explode("~", Crypt::decryptString(str_replace("-", "=", $request->temp_id)));
            $packSlip = $str[0];
            $vendorNum = $str[1];
        } else {
            $packSlip = 0;
            $vendorNum = 0;
        }
        $poNum = $request->poNum;
        $packSlip = $request->packSlip;
        $arrivalDate = $request->ArrivedDate;
        $receiptComment = $request->ReceiptComment;
        $status = $request->status;
        $username = Auth::user()->username;
        $password = Crypt::decryptString(Auth::user()->epicor_password);
        $client = new Client();
        $data = [];
        $host_api = self::get_host_api();
        $status_rollback_job_receipt = false;
        $db_detail_receipt_gr = ReceiptEntry::get_detail_receipt_gr($packSlip, $vendorNum);
        if ($db_detail_receipt_gr->count() > 0) {
            $total_back = 0;
            $nomor = 0;
            foreach ($db_detail_receipt_gr->get() as $row) {
                ${'delete_first_' . $nomor} = self::delete_receipt_job($row->JobNum, $row->OurQty, $row->LotNum, $row->WareHouseCode, $row->PartNum, $row->BinNum);
                if (${'delete_first_' . $nomor}) {
                    $total_back = $total_back + 1;
                }
                $nomor++;
            }
            if ($db_detail_receipt_gr->count() == $total_back) {
                $status_rollback_job_receipt = true;
            }
        }

        $db_detail_gr = ReceiptEntry::get_detail_issue_gr($packSlip, $vendorNum);
        if ($db_detail_gr->count() > 0) {
            $total_back = 0;
            $nomor = 0;
            foreach ($db_detail_gr->get() as $row) {
                ${'db_job_mtl_' . $nomor} = ReceiptEntry::get_list_job_mtl($row->JobNum, $row->AssemblySeq, $row->JobSeq);
                if (${'db_job_mtl_' . $nomor}->count() > 0) {
                    $nomor_dtl = 0;
                    foreach (${'db_job_mtl_' . $nomor} as $dtl) {
                        if ($row->WareHouseCode != '' && $row->BinNum != '') {
                            $qty_issue = $dtl->QtyPer * $row->OurQty;
                            $isReturn = true;
                            ${'post_issue_material_' . $nomor_dtl} = self::submit_issue_material($row->AssemblySeq, $row->JobNum, $dtl->MtlSeq, $dtl->BinNum, $dtl->BinName, $dtl->WarehouseCode, $dtl->WarehouseName, $row->LotNum, $qty_issue, $isReturn, $row->PartNum);
                        }
                        $nomor_dtl++;
                    }
                }
                $nomor++;
            }
        }

        try {
            $response = $client->request('POST', $host_api . 'Receipt/UpdateHeader', [
                'json' => [
                    'nik' => "$username",
                    'password' => "$password",
                    'poNum' => $poNum,
                    'receiptComment' => $receiptComment,
                    'arrivalDate' => $arrivalDate,
                    'packSlip' => "$packSlip",
                    'rowMod' => "D",
                    "vendorNum" => $vendorNum,
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'verify' => false,
            ]);
            $responseBody = json_decode($response->getBody()->getContents(), true);
            $data['code'] = $responseBody['code'];
            $data['status'] = $responseBody['status'];
            if ($data['code'] == 200) {
                $data['transaction_code'] = $responseBody['data']['epi_code'];
                $data['transaction_status'] = $responseBody['data']['epi_status'];
                $vendorShp = DB::connection('vendor-app-epicor')
                    ->table('VendorShpHead')
                    ->where('ShipNum', $packSlip)
                    ->where('PONum', $poNum)
                    ->orderByDesc('id')
                    ->first();
                if ($vendorShp) {
                    if ($status == 'confirm') {
                        $status1 = 2;
                        $status2 = 'CONFIRM';
                    } else {
                        $status1 = 4;
                        $status2 = 'REJECT';
                    }
                    ReceiptEntry::vendor_shp_update($vendorShp->id, $status1, $status2);
                }
                // \Log::info('Hapus Berhasil');
            }
        } catch (RequestException $e) {
            $data['code'] = 500;
            $data['desc'] = $e->getMessage();
        }
        return $data;
    }
    public function updateDeliveryStatus(Request $request)
    {
        $PackSlip = $request->PackSlip;
        if (empty($PackSlip)) {
            return response()->json([
                'status' => 'error',
                'message' => 'PackSlip is required.'
            ], 400);
        } else {
            $vendorHead = DB::connection('vendor-app-epicor')->table('VendorShpHead')
                ->where('ShipNum', $PackSlip)
                ->first();
            if ($vendorHead) {
                DB::connection('vendor-app-epicor')->table('VendorShpHead')
                    ->where('id', $vendorHead->id)
                    ->update(['Status' => 3]);
                DB::connection('vendor-app-epicor')->table('VendorShpDtl')
                    ->where('VendorShpHeadID', $vendorHead->id)
                    ->update(['Status' => 'DELIVERED']);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Delivery status updated successfully.'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'PackSlip not found.'
                ], 404);
            }
        }

    }
}
