<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrderEntry;
use App\Models\AppModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
use PDF;

class PurchaseOrderEntryController extends Controller
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
        return view('purchase_order_entry/purchase_order_entry_index', $data);
    }

    public function get_vendor_list(Request $request)
    {
        $search = $request->search;
        $page = $request->post('page', 1); // default to page 1
        $pageSize = 10; // Number of items per page

        $query = PurchaseOrderEntry::get_vendor_list();
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

    public function get_partnum_list(Request $request)
    {
        $search = $request->search;
        $ponum = $request->ponum;
        $page = $request->post('page', 1); // default to page 1
        $pageSize = 10; // Number of items per page

        $query = PurchaseOrderEntry::get_poline_list($ponum);
        if ($search) {
            $query->where('PartNum', 'like', '%' . $search . '%');
            $query->orWhere('LineDesc', 'like', '%' . $search . '%');
        }
        $VendorList = $query->paginate($pageSize, ['*'], 'page', $page);

        return response()->json([
            'items' => $VendorList->map(function ($VendorList) {
                return [
                    'id' => $VendorList->POLine,
                    'name' => $VendorList->PartNum
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
            0 => 'a.PONum',
            1 => 'a.PONum',
            2 => 'a.DocNum_c',
            3 => 'a.OrderDate',
            4 => 'b.Name'
        );
        $totalData = PurchaseOrderEntry::get_transaction_list($search, $vendor_id, $status_id)->count();
        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[($request->input('order.0.column') == null ? 0 : $request->input('order.0.column'))];
        $dir = ($request->input('order.0.dir') == null ? 'ASC' : $request->input('order.0.dir'));
        if (empty($search)) {
            $posts = PurchaseOrderEntry::get_transaction_list($search, $vendor_id, $status_id)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $posts = PurchaseOrderEntry::get_transaction_list($search, $vendor_id, $status_id)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = PurchaseOrderEntry::get_transaction_list($search, $vendor_id, $status_id)->count();
        }

        $data = array();
        if (!empty($posts)) {
            $no = $start;
            foreach ($posts as $post) {
                $no++;
                $trc_id = Crypt::encryptString($post->PONum);
                $sys_id = "'" . str_replace("=", "-", $trc_id) . "'";
                $button = '
            <span class="svg-icon svg-icon-primary svg-icon-2x" style="cursor: pointer;" id="svg_document_preview_' . $post->PONum . '" onclick="document_preview(' . $sys_id . ', ' . $post->PONum . ') ;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="black"></path>
                    <path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="black"></path>
                    <path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="black"></path>
                </svg>
            </span>
            <span id="spinner_document_preview_' . $post->PONum . '" class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;"></span>';
                $my_username = Auth::user()->username;
                $nestedData['no'] = $no;
                $nestedData['PONum'] = $post->DocNum_c . '<br/>' . $post->PONum . ' ' . ($post->Approve == 1 ? 'Done' : 'Draft');
                $nestedData['DocNum'] = $post->DocNum_c;
                $nestedData['Vendor'] = $post->Name;
                $nestedData['OrderDate'] = AppModel::local_date_formate_name(substr($post->OrderDate, 0, 10));
                $nestedData['DueDate'] = AppModel::local_date_formate_name(substr($post->DueDate, 0, 10));
                $nestedData['Approved'] = ($post->ApprovalStatus == 'A' ? 'Approved' : 'Pending');
                $nestedData['action'] = $button;
                $data[] = $nestedData;
            }
        }
        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );
        echo json_encode($json_data);
    }

    public function get_count_document(Request $request)
    {
        $vendor_id = $request->SelectVendorID;
        $data['total_draft'] = PurchaseOrderEntry::get_count_document_draft($vendor_id);
        $data['total_document'] = PurchaseOrderEntry::get_count_document($vendor_id);
        $data['total_waiting'] = PurchaseOrderEntry::get_count_waiting($vendor_id);
        $data['total_approved'] = PurchaseOrderEntry::get_count_approved($vendor_id);
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
        return view('purchase_order_entry/purchase_order_entry_header', $data);
    }

    public function get_preview_doc(Request $request)
    {
        $str_req = explode("_", $request->trc_unix_id);
        $str = explode("~", Crypt::decryptString(str_replace("-", "=", $str_req[0])));
        $PONum = $str[0];
        $data_detail = PurchaseOrderEntry::data_header($PONum);
        // dd($data_detail); 
        if ($data_detail) {
            $data['code'] = 200;
            $data['status'] = 'Ok';
            if ($data_detail->count() > 0) {
                foreach ($data_detail as $db) {
                    $sys_id = Crypt::encryptString($db->PONum);
                    $sys_id = str_replace("=", "-", $sys_id);
                    $data['trc_unix_id'] = $sys_id;
                    $data['PONum'] = $db->PONum;
                    $data['VendorNum'] = $db->VendorNum;
                    $data['VendorName'] = $db->Name;
                    $data['DocNum_c'] = $db->DocNum_c;
                    $data['OrderDate'] = $db->OrderDate;
                    $data['DueDate'] = $db->DueDate;
                    $data['CommentText'] = $db->CommentText;
                    $data['ref_tab'] = 1;
                }
            } else {
                $data['trc_unix_id'] = '';
                $data['PONum'] = '';
                $data['VendorNum'] = '';
                $data['VendorName'] = '';
                $data['DocNum_c'] = '';
                $data['OrderDate'] = '';
                $data['DueDate'] = '';
                $data['CommentText'] = '';
                $data['ref_tab'] = 0;
            }
        } else {
            $data['code'] = 500;
            $data['status'] = "Please reload and try again!";
        }
        return view('purchase_order_entry/purchase_order_entry_header', $data);
    }

    public function get_header_attr(Request $request)
    {
        $str_req = explode("_", $request->trc_unix_id);
        $str = explode("~", Crypt::decryptString(str_replace("-", "=", $str_req[0])));
        $PONum = $str[0];
        $data_detail = PurchaseOrderEntry::data_header($PONum);
        if ($data_detail) {
            $data['code'] = 200;
            $data['status'] = 'Ok';
            if ($data_detail->count() > 0) {
                foreach ($data_detail as $db) {
                    $sys_id = Crypt::encryptString($db->PONum);
                    $sys_id = str_replace("=", "-", $sys_id);
                    $data['trc_unix_id'] = $sys_id;
                    $data['PONum'] = $db->PONum;
                    $data['VendorNum'] = $db->VendorNum;
                    $data['VendorName'] = $db->Name;
                    $data['DocNum_c'] = $db->DocNum_c;
                    $data['OrderDate'] = $db->OrderDate;
                    $data['DueDate'] = $db->DueDate;
                    $data['CommentText'] = $db->CommentText;
                    $data['ref_tab'] = 1;
                }
            } else {
                $data['trc_unix_id'] = '';
                $data['PONum'] = '';
                $data['VendorNum'] = '';
                $data['VendorName'] = '';
                $data['DocNum_c'] = '';
                $data['OrderDate'] = '';
                $data['DueDate'] = '';
                $data['CommentText'] = '';
                $data['ref_tab'] = 0;
            }
        } else {
            $data['code'] = 500;
            $data['status'] = "Please reload and try again!";
        }
        return json_encode($data);
    }


    public function detail_table(Request $request)
    {
        if ($request->temp_id != '') {
            $str = explode("~", Crypt::decryptString(str_replace("-", "=", $request->temp_id)));
            $PONum = $str[0];
        } else {
            $PONum = 0;
        }
        // dd($PONum);
        $search = $request->detail_table_search;
        $columns = array(
            0 => 'POLine',
            1 => 'POLine',
            2 => 'PartNum',
            3 => 'OurQty',
            4 => 'OurQty',
        );

        $totalData = PurchaseOrderEntry::get_detail_transaction_list($search, $PONum)->count();
        // dd($totalData);
        $totalFiltered = $totalData;
        $limit = $request->length;
        $start = $request->start;
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        if (empty($search)) {
            $posts = PurchaseOrderEntry::get_detail_transaction_list($search, $PONum)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $posts = PurchaseOrderEntry::get_detail_transaction_list($search, $PONum)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = PurchaseOrderEntry::get_detail_transaction_list($search, $PONum)->count();
        }
        $data = array();

        if (!empty($posts)) {
            $no = $start;
            foreach ($posts as $post) {
                $no++;
                $trc_id = Crypt::encryptString($post->POLine);
                $sys_id = "'" . str_replace("=", "-", $trc_id) . "'";
                $button =
                    '<span class="svg-icon svg-icon-primary svg-icon-2x" style="cursor: pointer;" id="svg_document_detail_preview_' . $no . '"  onclick="getDetail(' . $sys_id . ') ;">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="black"></path><path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="black"></path><path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="black"></path></svg>
        </span> 
        <span id="spinner_document_detail_preview_' . $no . '" class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;"></span>';


                $nestedData['no'] = $post->POLine;
                $nestedData['PartNum'] = $post->PartNum . '<br/>' . $post->LineDesc;
                $nestedData['Qty'] = 'Our : ' . number_format($post->OrderQty, 0) . ' ' . $post->IUM . '<br/>' . 'Supp : ' . number_format($post->XOrderQty, 0) . ' ' . $post->PUM;
                $nestedData['action'] = $button;
                $nestedData['Status'] = 0;

                $data[] = $nestedData;
            }
        }
        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );
        echo json_encode($json_data);
    }


    public function detail_tag_table(Request $request)
    {
        if ($request->temp_id != '') {
            $str = explode("~", string: Crypt::decryptString(str_replace("-", "=", $request->temp_id)));
            $PONum = $str[0];
        } else {
            $PONum = 0;
        }

        $poline = $request->poline;
        $columns = array(
            0 => 'POLine',
            1 => 'POLine',
            2 => 'PartNum',
            3 => 'OurQty',
        );

        $totalData = PurchaseOrderEntry::get_detail_tag_list($PONum, $poline, )->count();
        $totalFiltered = $totalData;
        $limit = $request->length;
        $start = $request->start;
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        if (empty($search)) {
            $posts = PurchaseOrderEntry::get_detail_tag_list($PONum, $poline)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $posts = PurchaseOrderEntry::get_detail_tag_list($PONum, $poline)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = PurchaseOrderEntry::get_detail_tag_list($PONum, $poline)->count();
        }
        $data = array();

        if (!empty($posts)) {
            $no = $start;
            foreach ($posts as $post) {
                $no++;
                $trc_id = Crypt::encryptString($post->POLine);
                $sys_id = "'" . str_replace("=", "-", $trc_id) . "'";
                $button =
                    '<button type="button" title="Generate Label" class="btn btn-light-primary btn-sm" id="btn_generate_tag_label_' . $no . '" onclick="form_generate_tag_label(' . $sys_id . ',' . $no . ')" style="text-align: center; width: 40px; height: 35px;">
                <span id="svg_generate_tag_label_' . $no . '" class="svg-icon svg-icon-2" style="margin-left: -7px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="black"></path>
                    <path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="black"></path><path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="black"></path>
                    </svg>
                </span>
                <span id="spinner_generate_tag_label_' . $no . '" class="spinner-border spinner-border-sm svg-icon svg-icon-1x" style="display: none;"></span>  
            </button>';

                $nestedData['no'] = $post->POLine;
                $nestedData['PartNum'] = $post->PartNum . '<br/>' . $post->PartDesc;
                $nestedData['Qty'] = $post->TransQty;
                $nestedData['action'] = $button;
                $nestedData['Status'] = 0;

                $data[] = $nestedData;
            }
        }
        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );
        echo json_encode($json_data);
    }
    public function generate_tag_label(Request $request)
    {

        $str_req = explode("_", $request->temp_id);
        $str_id = explode("~", Crypt::decryptString(str_replace("-", "=", $str_req[0])));
        $PONum = $str_id[0];
        $StandardPackQty = $request->StandardPackQty;
        $POLine = $request->POLine;
        $EntryPerson = $request->EntryPerson;
        $quality_name = $request->quality_operator_name;
        $OurQty = $request->OurQty;
        $PartNum = $request->PartNum;
        $ModelName = $request->ModelName;
        $PartDescription = str_replace(",", "__", $request->PartDescription);
        $PORel = 1;
        $delete = DB::table('t500_POHead_Tag as a')
            ->leftJoin('t510_PODetail_Tag as b', function ($join) {
                $join->on('a.PONum', 'b.PONum');
            })
            ->where('b.PONum', $PONum)
            ->where('b.POLine', $POLine);

        if ($delete->count() > 0) {
            $exe_delete = $delete->delete();
            if ($exe_delete) {
                $generate = PurchaseOrderEntry::generate_tag_label($PONum, $POLine, $PORel, $StandardPackQty, $EntryPerson, $quality_name, $OurQty, $PartNum, $PartDescription, $ModelName);
                if ($generate) {
                    $data['process_status'] = 200;
                    $data['msg_process'] = 'Data updated successfully';
                } else {
                    $data['process_status'] = 500;
                    $data['msg_process'] = 'Data updated fail !';
                }
            } else {
                $data['process_status'] = 500;
                $data['msg_process'] = 'Login sebagai admin data!';
            }
        } else {
            $generate = PurchaseOrderEntry::generate_tag_label($PONum, $POLine, $PORel, $StandardPackQty, $EntryPerson, $quality_name, $OurQty, $PartNum, $PartDescription, $ModelName);
            if ($generate) {
                $data['process_status'] = 200;
                $data['msg_process'] = 'Data updated successfully';
            } else {
                $data['process_status'] = 500;
                $data['msg_process'] = 'Data updated fail !';
            }
        }
        return json_encode($data);
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

        $totalData = PurchaseOrderEntry::get_detail_po_list($PONum, $PartNum)->count();
        $totalFiltered = $totalData;
        $limit = $request->length;
        $start = $request->start;
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        if (empty($search)) {
            $posts = PurchaseOrderEntry::get_detail_po_list($PONum, $PartNum)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $posts = PurchaseOrderEntry::get_detail_po_list($PONum, $PartNum)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = PurchaseOrderEntry::get_detail_po_list($PONum, $PartNum)->count();
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
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );
        echo json_encode($json_data);
    }

    public function get_preview_doc_detail(Request $request)
    {
        if ($request->trc_unix_id != '') {
            $str = explode("~", Crypt::decryptString(str_replace("-", "=", $request->trc_unix_id)));
            $PONum = $str[0];
        } else {
            $PONum = 0;
        }
        $POLine = ($request->detail_id == "" ? 0 : Crypt::decryptString(str_replace("-", "=", $request->detail_id)));
        $data['POLine'] = $POLine;

        $data_detail = PurchaseOrderEntry::data_detail($PONum, $POLine);
        // dd($data_detail);
        if ($data_detail) {
            $data['code'] = 200;
            $data['status'] = 'Ok';
            if ($data_detail->count() > 0) {
                foreach ($data_detail as $db) {
                    $data['PONum'] = $db->PONUM;
                    $data['POLine'] = $db->POLine;
                    $data['PartNum'] = $db->PartNum;
                    $data['LineDesc'] = $db->LineDesc;
                    $data['OrderQty'] = (int) $db->OrderQty;
                    $data['XOrderQty'] = (int) $db->XOrderQty;
                    $data['IUM'] = $db->IUM;
                    $data['PUM'] = $db->PUM;
                    $data['ModelName'] = $db->RevisionNum;
                }
            } else {
                $data['PONum'] = 0;
                $data['POLine'] = '';
                $data['PartNum'] = '';
                $data['PartNum'] = '';
                $data['LineDesc'] = '';
                $data['OrderQty'] = '';
                $data['XOrderQty'] = '';
                $data['IUM'] = '';
                $data['PUM'] = '';
                $data['ModelName'] = '';
            }
        } else {
            $data['code'] = 500;
            $data['status'] = "Please reload and try again!";
        }
        return view('purchase_order_entry/purchase_order_entry_detail', $data);
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
        $data['list'] = PurchaseOrderEntry::get_attachment_list($PackSlip, $VendorNum);
        $data['count'] = PurchaseOrderEntry::get_attachment_list($PackSlip, $VendorNum)->count();
        return view('purchase_order_entry/attachment_list', $data);
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
                    $data['ref_doc'] = str_replace("=", "-", $trc_id);
                }
            }

        } catch (RequestException $e) {
            \Log::error('API request failed', [
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
                    $data['ref_doc'] = str_replace("=", "-", $trc_id);
                }
            }

        } catch (RequestException $e) {
            \Log::error('API request failed', [
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
        $ourRemQty = PurchaseOrderEntry::get_qty_balance_po_line($poNum, $poLine, $poRelNum, $inputOurQty, $packSlip, $packLine, $vendorNum);
        $totalQtyCompleted = PurchaseOrderEntry::get_job_qty_completed($inputOurQty, $packSlip, $packLine, $jobNum, $vendorNum);
        $totalQtyBefore = PurchaseOrderEntry::get_total_qty_before($packSlip, $packLine, $vendorNum);
        $thisTranQty = $inputOurQty - $totalQtyBefore;
        $rowMod = ($packLine == 0 ? "A" : "U");
        if ($request->rowMod == "D") {
            $rowMod = "D";
        }

        # Check Job
        # Check Material Onhand
        # Check Part Onhand
        $part_onhand_status = ['code' => 1, 'status' => 'Ok !'];
        $mtl_onhand_status = ['code' => 1, 'status' => 'Ok !'];
        $job_status = ['code' => 1, 'status' => 'Ok !'];

        if ($jobNum != '' && $rowMod == "D") {
            $part_onhand_status = PurchaseOrderEntry::check_part_onhand_status($jobNum, $thisTranQty, $LotNum);
            $job_status = PurchaseOrderEntry::check_job_status($jobNum);
        } else if ($jobNum != '' && $thisTranQty < 0) {
            $part_onhand_status = PurchaseOrderEntry::check_part_onhand_status($jobNum, $thisTranQty, $LotNum);
            $job_status = PurchaseOrderEntry::check_job_status($jobNum);
        } else if ($jobNum != '' && $thisTranQty > 0) {
            $mtl_onhand_status = PurchaseOrderEntry::check_material_onhand_status($jobNum, $assemblySeq, $jobSeq, $thisTranQty, $LotNum);
            $job_status = PurchaseOrderEntry::check_job_status($jobNum);
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
                    $warehouseCode = $request->warehouseCode;
                    $binNum = $request->binNum;
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
                    $ToWarehouseID = $request->warehouseCode;
                    $PartNum = $request->PartNum;
                    $ToBinID = $request->binNum;
                    $username = Auth::user()->username;
                    $password = Crypt::decryptString(Auth::user()->epicor_password);
                    $client = new Client();
                    $data = [];
                    $host_api = self::get_host_api();
                    try {
                        $response = $client->request('POST', $host_api . 'Receipt/UpdateDetail', [
                            'json' => [
                                'nik' => "$username",
                                'password' => "$password",
                                'poNum' => $poNum,
                                'poLine' => $poLine,
                                "packSlip" => $packSlip,
                                'packLine' => $packLine,
                                "vendorNum" => $vendorNum,
                                'qtyOption' => "$qtyOption",
                                "inputOurQty" => $inputOurQty,
                                "ium" => "$ium",
                                "vendorQty" => $vendorQty,
                                "pum" => "$pum",
                                "issuedComplete" => $issuedComplete,
                                "convOverride" => $convOverride,
                                "warehouseCode" => "$warehouseCode",
                                "binNum" => "$binNum",
                                "tranReference" => "$tranReference",
                                "rowMod" => $rowMod,
                                "receivedComplete" => $receivedComplete,
                                "received" => $received,
                                "receiptDate" => "$receiptDate",
                                "lotNum" => "A",
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
                            if ($jobNum !== '' && $data['transaction_code'] == 200) {
                                $db_job_mtl = PurchaseOrderEntry::get_list_job_mtl($jobNum, $assemblySeq, $jobSeq);
                                $isReturn = false;
                                if ($rowMod != "D") {
                                    if ($data['transaction_code'] == 200) {
                                        $data['packLine'] = $responseBody['data']['rcvDtl']['packLine'];
                                        if ($jobNum !== '') {
                                            if ($db_job_mtl->count() > 0) {
                                                $total_success = 0;
                                                foreach ($db_job_mtl as $row) {
                                                    if ($row->WarehouseCode != '' && $row->BinNum != '') {
                                                        $qty_issue = $row->QtyPer * ($inputOurQty - $totalQtyBefore);
                                                        $post_issue_material = self::submit_issue_material($assemblySeq, $jobNum, $row->MtlSeq, $row->BinNum, $row->BinName, $row->WarehouseCode, $row->WarehouseName, $LotNum, $qty_issue, $isReturn);
                                                        if ($post_issue_material['transaction_code'] == 200) {
                                                            $total_success = $total_success + 1;
                                                        }
                                                    }
                                                }
                                                $data['total_success'] = $total_success;
                                                if ($total_success == $db_job_mtl->count()) {
                                                    $update_issue_material_status = PurchaseOrderEntry::update_issue_material_status($packSlip, $vendorNum, $data['packLine']);
                                                    $post_job_receipt = self::submit_job_receipt($jobNum, $inputOurQty, $totalQtyBefore, $LotNum, $ToWarehouseID, $PartNum, $ToBinID, $packSlip, $vendorNum, $data['packLine'], $rowMod);
                                                    if ($post_job_receipt['code'] == 200) {
                                                        $update_job_receipt_status = PurchaseOrderEntry::update_job_receipt_status($packSlip, $vendorNum, $data['packLine']);
                                                    } else {

                                                    }
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    $data['packLine'] = 0;
                                    if ($jobNum !== '') {
                                        $post_job_receipt = self::delete_receipt_job($jobNum, $totalQtyBefore, $LotNum, $ToWarehouseID, $PartNum, $ToBinID);
                                    }
                                    if ($jobNum !== '') {
                                        if ($db_job_mtl->count() > 0) {
                                            foreach ($db_job_mtl as $row) {
                                                if ($row->WarehouseCode != '' && $row->BinNum != '') {
                                                    $qty_issue = $row->QtyPer * $totalQtyBefore;
                                                    $isReturn = true;
                                                    $post_issue_material = self::submit_issue_material($assemblySeq, $jobNum, $row->MtlSeq, $row->BinNum, $row->BinName, $row->WarehouseCode, $row->WarehouseName, $LotNum, $qty_issue, $isReturn);
                                                }
                                            }
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
        return $data;
    }

    public function submit_issue_material($assemblySeq, $jobNum, $jobMtlSeq, $binNum, $binNumDescription, $warehouseCode, $WarehouseDescription, $lotNum, $qty, $isReturn)
    {
        if ($qty < 0) {
            $isReturn = true;
            $qty = abs($qty);
        }
        if ($qty <> 0) {
            $username = Auth::user()->username;
            $password = Crypt::decryptString(Auth::user()->epicor_password);
            $client = new Client();
            $data = [];
            $host_api = self::get_host_api();
            try {
                $response = $client->request('POST', $host_api . 'IssueMtl/MtlMovement', [
                    'json' => [
                        "assemblySeq" => $assemblySeq,
                        "jobNum" => "$jobNum",
                        "jobMtlSeq" => $jobMtlSeq,
                        "binNum" => "$binNum",
                        "binNumDescription" => "$binNumDescription",
                        "warehouseCode" => "$warehouseCode",
                        "WarehouseDescription" => "$WarehouseDescription",
                        "lotNum" => "$lotNum",
                        "qty" => "$qty",
                        "rowMod" => "A",
                        "isReturn" => $isReturn,
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
                if ($data['code'] == 200) {
                    $data['transaction_code'] = $responseBody['data']['epi_code'];
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
        $username = Auth::user()->username;
        $password = Crypt::decryptString(Auth::user()->epicor_password);
        $client = new Client();
        $data = [];
        $host_api = self::get_host_api();
        $status_rollback_job_receipt = false;
        $db_detail_receipt_gr = PurchaseOrderEntry::get_detail_receipt_gr($packSlip, $vendorNum);
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

        $db_detail_gr = PurchaseOrderEntry::get_detail_issue_gr($packSlip, $vendorNum);
        if ($db_detail_gr->count() > 0) {
            $total_back = 0;
            $nomor = 0;
            foreach ($db_detail_gr->get() as $row) {
                ${'db_job_mtl_' . $nomor} = PurchaseOrderEntry::get_list_job_mtl($row->JobNum, $row->AssemblySeq, $row->JobSeq);
                if (${'db_job_mtl_' . $nomor}->count() > 0) {
                    $nomor_dtl = 0;
                    foreach (${'db_job_mtl_' . $nomor} as $dtl) {
                        if ($row->WareHouseCode != '' && $row->BinNum != '') {
                            $qty_issue = $dtl->QtyPer * $row->OurQty;
                            $isReturn = true;
                            ${'post_issue_material_' . $nomor_dtl} = self::submit_issue_material($row->AssemblySeq, $row->JobNum, $dtl->MtlSeq, $dtl->BinNum, $dtl->BinName, $dtl->WarehouseCode, $dtl->WarehouseName, $row->LotNum, $qty_issue, $isReturn);

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
            }

        } catch (RequestException $e) {
            $data['code'] = 500;
            $data['desc'] = $e->getMessage();
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
            \Log::error('API request failed', [
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

                    $whseDefault = PurchaseOrderEntry::get_part_whse_receipt($data['partNum']);
                    $binDefault = PurchaseOrderEntry::get_part_bin_receipt($data['partNum']);

                    $data['whseDefault'] = ($whseDefault == '0' ? $responseBody['data']['rcvDtls']['wareHouseCode'] : $whseDefault);
                    $data['binDefault'] = ($binDefault == '0' ? $responseBody['data']['rcvDtls']['binNum'] : $binDefault);
                }
            }
        } catch (RequestException $e) {
            \Log::error('API request failed', [
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
        $data['list'] = PurchaseOrderEntry::get_transaction_list($year, $search, $vendor_id)->get();
        $data['num'] = PurchaseOrderEntry::get_transaction_list($year, $search, $vendor_id)->count();
        $data['ref_form'] = '';
        return view('purchase_order_entry.po_export', $data);
    }

    function print_view(Request $request)
    {
        $data['trc_unix_id'] = $request->trc_unix_id;
        $data['ref_form'] = $request->ref_form;
        if ($request->trc_unix_id != '') {
            $str = explode("~", Crypt::decryptString(str_replace("-", "=", $request->trc_unix_id)));
            $PONum = $str[0];
        } else {
            $PONum = 0;
        }
        $search = '';
        $data_detail = PurchaseOrderEntry::get_detail_transaction_list($search, $PONum)->count();
        if ($data_detail > 0) {
            return view('purchase_order_entry.po_direct_print', $data);
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
        $data_detail = PurchaseOrderEntry::get_detail_transaction_list($search, $PackSlip, $VendorNum)->count();
        if ($data_detail > 0) {
            return view('purchase_order_entry.tag_label_direct_print', $data);
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

    public function tag_label_print(Request $request)
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
        $form_data = PurchaseOrderEntry::get_rcv_dtl($PackSlip, $VendorNum);
        $w_top = array(95, 35, 60, 2, 30, 55);
        $style = array(
            'border' => false,
            'vpadding' => 2,
            'hpadding' => 2,
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255)
            'module_width' => 1, // width of a single module in points
            'module_height' => 1 // height of a single module in points
        );

        foreach ($form_data as $db) {
            $item_no = (strlen($db->PartNum) > 35 ? substr($db->PartNum, 0, 35) . ' ...' : $db->PartNum);
            $item_name = (strlen($db->PartDescription) > 35 ? substr(str_replace(",", "", $db->PartDescription), 0, 35) : $db->PartDescription);
            $item_code = "";
            $category_id = $db->ClassID;
            $category_name = strtoupper((strlen($db->ClassID) > 13 ? $db->ClassID : $db->ClassID));
            $qty = number_format($db->OurQty, 0);
            $myName = Auth::user()->username;
            date_default_timezone_set('Asia/Jakarta');
            $getDate = date('d-m-Y H:i');


            $docnum = $db->LegalNumber;
            $ext_docnum = $db->PackSlip;
            $docdate = AppModel::local_date_formate_name(($db->ArrivedDate == '' ? $db->ArrivedDate : $db->ArrivedDate));


            $partner_name = (strlen($db->VendorName) > 35 ? substr($db->VendorName, 0, 35) . ' ...' : ($db->VendorName == '' ? '-' : $db->VendorName));

            $partner_code = ($db->VendorCode_c == '' ? '-' : $db->VendorCode_c);
            $product_type = '';
            $address_id = '';
            $pcs_per_unit = 1;
            $unit_weight = 1;
            $customer_code = $db->ProdCode;

            $status_part = substr($docdate, 3, 8);
            $qr = $db->PartNum . '~' . $db->OurQty . '~' . $db->WareHouseCode . '~' . $db->WhseName . '~' . $db->LotNum . '~' . $db->PackLine;

            PDF::SetTitle($docnum);
            PDF::SetAuthor('Aji');
            PDF::setPrintHeader(false);
            PDF::SetTopMargin(5);
            PDF::SetMargins(5, 5, 7, 7);
            PDF::SetAutoPageBreak(TRUE, 0);
            PDF::setImageScale(PDF_IMAGE_SCALE_RATIO);
            PDF::setPrintFooter(false);
            PDF::AddPage('P', 'A6');
            PDF::SetFillColor(255, 255, 255);
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
            PDF::Cell($w_top[4], 5, $db->EntryPerson, 'TBR', 0, 'C', 0);
            PDF::Cell($w_top[4], 5, $partner_code, 'TBR', 0, 'C', 0);
            PDF::write2DBarcode($qr, 'QRCODE,H', 7.5, 12, 30, 30, $style, 'N');
            PDF::Ln(1);

            PDF::Cell($w_top[1], 5, '', 'L', 0, 'L', 0);
            PDF::Cell($w_top[2], 5, '', 'R', 0, 'L', 0);
            PDF::Ln();

            PDF::SetFont('dejavusans', '', 8);
            PDF::Cell(25, 7, ' Legal Number', 'L', 0, 'L', 0);
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
            PDF::Cell(68, 7, $partner_name, 'R', 0, 'L', 0);
            PDF::Ln();

            PDF::Cell(25, 7, ' PACKSLIP', 'L', 0, 'L', 0);
            PDF::Cell($w_top[3], 7, ':', '', 0, 'L', 0);
            PDF::Cell(68, 7, $ext_docnum, 'R', 0, 'L', 0);
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
            PDF::SetFont('dejavusans', 'B', 12);
            PDF::Cell(35, 10, $status_part, 'R', 0, 'C', 0);
            PDF::Ln();

            PDF::SetFont('dejavusans', '', 8);
            PDF::Cell(30, 7, '(' . $db->IUM . ')', 'BLR', 0, 'C', 0);
            PDF::Cell(30, 7, '', 'BR', 0, 'C', 0);
            PDF::Cell(35, 7, '', 'BR', 0, 'C', 0);
            PDF::Ln();

            PDF::SetFont('dejavusans', '', 8);
            PDF::Cell(45, 5, $myName, '', 0, 'L', 0);
            PDF::Cell(50, 5, $getDate, '', 0, 'R', 0);

        }
        PDF::Output($docnum . '.pdf');
    }


}
