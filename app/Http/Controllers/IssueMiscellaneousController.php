<?php

namespace App\Http\Controllers;

use App\Models\AppModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Models\IssueMiscellaneous;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
use PDF;
use Illuminate\Http\Request;

class IssueMiscellaneousController extends Controller
{
    private const APPROVAL_USER_IDS = [
        '130918-001','150421-002','040814-100','010217-014','260117-001','040814-024','070817-040','160320-001',
        '250714-012','041119-001','070817-033','110817-006','040814-385','040814-396','190219-007','200918-005',
        '061217-002','111121-001','080817-002','040817-001','050421-004','210821-002','240818-001','260421-002',
        '210821-001','270820-001','070817-023','040814-487','290321-010','141220-001','120421-002','260918-001',
        '210521-003','040814-433','080920-001','040915-002','250920-001','200321-001','190916-009','051020-019',
        '070817-042','040814-382','290621-001','070817-003','051020-040','040814-444','070316-002','040816-012',
        '220917-005','040814-390','080814-004','080814-003','190916-007','100316-002','230121-002','040814-403',
        '210721-008','290216-002','010721-015','210618-003','170220-001','040814-395','080819-002','240821-002',
        '060320-002'
    ];

    private function canUpdateQtySubmitter()
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        $candidates = [
            (string) ($user->username ?? ''),
            (string) ($user->userid ?? ''),
            (string) ($user->nik ?? ''),
            (string) ($user->id ?? ''),
        ];

        foreach (['161022-037', '270723-001','060320-002','040814-435'] as $authorizedCode) {
            if (in_array((string) $authorizedCode, $candidates, true)) {
                return true;
            }
        }

        return false;
    }

    private function approvalUserCandidates(): array
    {
        $user = Auth::user();
        if (!$user) {
            return [];
        }

        $candidates = [
            trim((string) ($user->username ?? '')),
            trim((string) ($user->userid ?? '')),
            trim((string) ($user->nik ?? '')),
            trim((string) ($user->id ?? '')),
        ];

        return array_values(array_unique(array_filter($candidates, function ($value) {
            return $value !== '';
        })));
    }

    public function index()
    {
        $my_id = Auth::user()->id;
        $segment_number = env('SEGMENT_NUM');
        $uri = explode("/", url()->current());
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
        return view('issue_miscellaneous/issue_index', $data);
    }

    public function front_table(Request $request)
    {
        $DocDate = $request->DocDate;
        $category = $request->Category ?? $request->category;
        $section_id = $request->section_id;
        $search = $request->front_table_search;
        // dd($status_id);
        $columns = array(
            0 => 'DocNum',
            1 => 'DocNum',
            2 => 'DocNum',
            3 => 'DocDate',
            4 => 'ReasonCode',
            5 => 'CreatedBy',
            6 => 'Approved',
            7 => 'Submitted',
            8 => 'RequestStatus',
            9 => 'TotalLine'
        );

        $totalData = IssueMiscellaneous::get_transaction_list(null, $category, $DocDate)->count();
        $totalFiltered = IssueMiscellaneous::get_transaction_list($search, $category, $DocDate)->count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = ($request->input('order.0.column') == 0 ? $columns[1] : $columns[$request->input('order.0.column')]);
        $dir = ($request->input('order.0.column') == 0 ? 'desc' : $request->input('order.0.dir'));
        $posts = IssueMiscellaneous::get_transaction_list($search, $category, $DocDate)
            ->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();
        $data = array();
        if (!empty($posts)) {
            $no = $start;
            foreach ($posts as $post) {
                $no++;
                $encryptedDocNum = str_replace('=', '-', Crypt::encryptString($post->DocNum));
                $refDoc = "'" . $encryptedDocNum . "'";
                $statusClass = 'badge-light-secondary';
                if ($post->RequestStatus === 'DRAFT') {
                    $statusClass = 'badge-light-primary';
                } elseif ($post->RequestStatus === 'PENDING_APPROVAL') {
                    $statusClass = 'badge-light-warning';
                } elseif (in_array($post->RequestStatus, ['APPROVED', 'AUTO_APPROVED'], true)) {
                    $statusClass = 'badge-light-success';
                } elseif ($post->RequestStatus === 'REJECTED') {
                    $statusClass = 'badge-light-danger';
                } elseif ($post->RequestStatus === 'CANCELLED') {
                    $statusClass = 'badge-light-dark';
                } elseif ($post->RequestStatus === 'COMPLETED') {
                    $statusClass = 'badge-light-info';
                }
                $statusBadge = '<span class="badge ' . $statusClass . '">' . ($post->RequestStatus ?? 'DRAFT') . '</span>';
                $button = '<button type="button" title="Generate Label" class="btn btn-light-primary btn-sm" id="btn_form_view_doc_' . $no . '" onclick="open_document(' . $refDoc . ',' . $no . ')" style="text-align: center; width: 40px; height: 35px;">
                            <span id="svg_form_view_doc_' . $no . '" class="svg-icon svg-icon-2" style="margin-left: -7px;">
                              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="black"></path>
                                <path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="black"></path><path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="black"></path>
                              </svg>
                            </span>
                            <span id="spinner_form_view_doc_' . $no . '" class="spinner-border spinner-border-sm svg-icon svg-icon-1x" style="display: none;"></span>  
                        </button>';

                $nestedData['no'] = $no;
                $nestedData['DocNum'] =  $post->DocNum;
                $nestedData['DocDate'] = $post->DocDate;
                $nestedData['ReasonCode'] = $post->ReasonCode;
                $nestedData['CreatedAt'] = $post->CreatedAt;
                $nestedData['CreatedBy'] = $post->CreatedBy;
                $nestedData['Approved'] = $post->Approved ? 'Approved' : 'Pending';
                $nestedData['Submitted'] = $post->Submitted ? 'Submitted' : 'Pending';
                $nestedData['RequestStatus'] = $statusBadge;
                $nestedData['UpdatedBy'] = $post->UpdatedBy;
                $nestedData['LastUpdated'] = $post->LastUpdated;
                $nestedData['TotalLine'] = $post->TotalLine;
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
        return response()->json($json_data);
    }
    public function add_document(Request $request)
    {
        $action = $request->action ?? null;

        if ($action === 'encrypt') {
            $docNum = $request->DocNum ?? '';
            if (!$docNum) {
                return response()->json(['status' => 'error', 'message' => 'DocNum not provided'], 400);
            }

            try {
                $encrypted = str_replace('=', '-', Crypt::encryptString($docNum));
                return response()->json(['status' => 'success', 'ref_doc' => $encrypted]);
            } catch (\Exception $e) {
                return response()->json(['status' => 'error', 'message' => 'Encryption failed'], 500);
            }
        }

        if ($action === 'decrypt') {
            $refDoc = $request->ref_doc ?? '';
            if (!$refDoc) {
                return response()->json(['status' => 'error', 'message' => 'ref_doc not provided'], 400);
            }

            try {
                $decrypted = Crypt::decryptString(str_replace('-', '=', $refDoc));
                return response()->json(['status' => 'success', 'DocNum' => $decrypted]);
            } catch (\Exception $e) {
                return response()->json(['status' => 'error', 'message' => 'Invalid or expired link'], 400);
            }
        }

        $data['DocNum'] = $request->DocNum ?? '';

        if (empty($data['DocNum']) && $request->filled('ref_doc')) {
            try {
                $data['DocNum'] = Crypt::decryptString(str_replace('-', '=', $request->ref_doc));
            } catch (\Exception $e) {
                $data['DocNum'] = '';
            }
        }

        $data['RequestStatus'] = 'DRAFT';
        $data['canUpdateQtySubmit'] = $this->canUpdateQtySubmitter();

        if (!empty($data['DocNum'])) {
            $parts = explode('~', $data['DocNum']);
            if (count($parts) === 3) {
                $header = DB::table('IssueMiscellaneous')
                    ->where('TranTypeID', $parts[0])
                    ->where('MonthID', $parts[1])
                    ->where('TranSeqID', $parts[2])
                    ->where('IsDelete', 0)
                    ->first();

                if ($header) {
                    $data['RequestStatus'] = $header->RequestStatus ?: 'DRAFT';
                    $data['DocDate'] = $header->DocDate;
                    $data['Category'] = $header->Category;
                    $data['TransactionType'] = $header->TransactionType ?? '';
                    $data['ReasonCode'] = $header->ReasonCode ?? '';
                    $data['ApprovedBy'] = $header->ApprovedBy ?? '';
                    $data['RejectedReason'] = $header->RejectedReason;
                }
            }
        }

        return view('issue_miscellaneous/form', $data);
    }

    public function reason_codes(Request $request)
    {
        $transactionType = strtoupper($request->transaction_type ?? '');
        if (!in_array($transactionType, ['ST', 'TR'], true)) {
            return response()->json(['items' => []]);
        }

        $prefix = $transactionType . '%';

        $reasons = DB::connection('sqlsrv4')->table('Erp.Reason')
            ->select('ReasonCode', 'Description')
            ->where('ReasonType', 'M')
            ->whereIn('ReasonCode',['ST-031','ST-001','ST-021','ST-019','ST-024','ST-009','ST-012','ST-023','ST-022','ST-028','ST-017','ST-001','ST-004','ST-006','ST-017','TR-032','TR-002','TR-022','TR-020','TR-025','TR-010','TR-013','TR-023','TR-029','TR-001','TR-005','TR-007'])
            ->where('ReasonCode', 'like', $prefix)
            ->orderBy('ReasonCode')
            ->get();

        $items = $reasons->map(function ($row) {
            return [
                'id' => $row->ReasonCode,
                'text' => $row->ReasonCode . ' - ' . $row->Description,
            ];
        })->values();

        return response()->json(['items' => $items]);
    }

    public function approval_users(Request $request)
    {
        $search = trim((string) ($request->search ?? ''));

        $query = DB::connection('sqlsrv4')
            ->table('Ice.SysUserFile')
            ->select('UserID', 'Name')
            ->whereIn('UserID', self::APPROVAL_USER_IDS)
            ->orderBy('UserID');

        if ($search !== '') {
            $query->where(function ($sub) use ($search) {
                $sub->where('UserID', 'like', '%' . $search . '%')
                    ->orWhere('Name', 'like', '%' . $search . '%');
            });
        }

        $rows = $query->get();

        $items = $rows->map(function ($row) {
            $name = trim((string) ($row->Name ?? ''));

            return [
                'id' => $row->UserID,
                'text' => $row->UserID . ($name !== '' ? ' - ' . $name : ''),
            ];
        })->values();

        return response()->json(['items' => $items]);
    }

    public function get_new_docnum(Request $request)
    {
        $data['DocNum'] = IssueMiscellaneous::get_new_docnum($request->DocDate);
        echo json_encode($data);
    }
    public function showPart(Request $request)
    {
        $Category = $request->Category;
        $search = $request->search;
        $page = $request->post('page', 1);
        $pageSize = 10;
        $query = IssueMiscellaneous::showPart($Category);
        if ($search) {
            $query->where(function ($sub) use ($search) {
                $sub->Where('a.PartDescription', 'LIKE', '%' . $search . '%')
                ->orWhere('a.PartNum', 'LIKE', '%' . $search . '%');
            });

        }
        $part = $query->paginate($pageSize, ['*'], 'page', $page);
        $items = [];
        foreach ($part->items() as $partItem) {
            $items[] = [
                'id' => $partItem->PartNum,
                'name' => $partItem->PartDescription
            ];
        }
        return response()->json([
            'items' => $items,
            'pagination' => [
                'more' => $part->hasMorePages(),
            ]
        ]);
    }

    public function ShowUOM(Request $request)
    {
        $part = $request->partnum;
        $category = $request->category ?? $request->Category ?? null;
        $query = IssueMiscellaneous::ShowUOM($part);
        $result = $query->first();
        $stock = IssueMiscellaneous::ShowPartStock($part, $category)->first();
        $consignment = IssueMiscellaneous::ShowConsignmentStatus($part, $category)->first();
        if (!$result) {
            $data["UOM"] = "";
            $data["OnhandQty"] = 0;
            $data["Consignment"] = 0;
            $data["code"] = 400;
            $data["msg_error"] = "UOM Tidak Ditemukan";
        } else {
            $data["UOM"] = $result->IUM;
            $data["OnhandQty"] = $stock->OnhandQty ?? 0;
            $data["Consignment"] = $consignment->Consignment_c;
            $data["code"] = 200;
            $data["msg_error"] = "OK";
        }
        echo json_encode($data);
    }

    public function store_item(Request $request)
    {
        $data = $request->all();
        $result = IssueMiscellaneous::store_item($data);
        return response()->json($result);
    }

    public function detail_table(Request $request)
    {
        $DocNum = $request->DocNum;
        $search = $request->search;
        $docParts = explode('~', $DocNum);
        $requestStatus = 'DRAFT';

        if (count($docParts) === 3) {
            $header = DB::table('IssueMiscellaneous')
                ->where('TranTypeID', $docParts[0])
                ->where('MonthID', $docParts[1])
                ->where('TranSeqID', $docParts[2])
                ->where('IsDelete', 0)
                ->first();

            if ($header) {
                $requestStatus = $header->RequestStatus ?: 'DRAFT';
            }
        }

        $isEditable = in_array($requestStatus, ['DRAFT', 'REJECTED'], true)
            || ($this->canUpdateQtySubmitter() && in_array($requestStatus, ['APPROVED', 'AUTO_APPROVED'], true));

        $totalData = IssueMiscellaneous::get_detail_list($DocNum)->count();
        $totalFiltered = IssueMiscellaneous::get_detail_list($DocNum, $search)->count();
        $limit = $request->input('length');
        $start = $request->input('start');

        // Default ordering
        $order = 'TranSeqID';
        $dir = 'desc';

        if ($request->input('order.0.column')) {
            $columns = array(
                0 => 'TranSeqID',
                1 => 'TranSeqID',
                2 => 'PartNum',
                3 => 'PartName',
                4 => 'QtyMove',
                5 => 'FromWarehouseDesc',
                6 => 'FromBinID',
                7 => 'FromBinID',
                8 => 'Reference'
            );
            $order = $columns[$request->input('order.0.column')] ?? 'TranSeqID';
            $dir = $request->input('order.0.dir');
        }

        $posts = IssueMiscellaneous::get_detail_list($DocNum, $search)
            ->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        $data = array();
        if (!empty($posts)) {
            $no = $start;
            foreach ($posts as $post) {
                $no++;
                // Construct composite key for deletion
                $id = $post->TranTypeID . '~' . $post->MonthID . '~' . $post->TranSeqID . '~' . $post->LineID;
                $DocRef = $post->PartNum;

                $button = $isEditable
                    ? '<button type="button" class="btn btn-icon btn-danger btn-sm" id="btn_delete_item_' . $no . '" onclick="delete_item(\'' . $id . '\',' . $no . ',\'' . $DocRef . '\')">
                                <span id="svg_delete_item_' . $no . '" class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="black"></path>
                                        <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="black"></path>
                                        <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="black"></path>
                                    </svg>
                                </span>
                                <span id="spinner_delete_item_' . $no . '" class="spinner-border spinner-border-sm" style="display: none;"></span>
                           </button>'
                    : '<span class="text-muted">-</span>';

                $nestedData['no'] = $no;
                $nestedData['action'] = $button;
                $nestedData['id'] = $id;
                $nestedData['PartNum'] = $post->PartNum;
                $nestedData['PartName'] = $post->PartName;
                $nestedData['Qty'] = $post->QtyMove;
                $nestedData['QtyMove'] = $post->QtyMove;
                $nestedData['QtySubmit'] = $post->QtySubmit ?? 0;
                $nestedData['FromWarehouseDesc'] = $post->FromWarehouseDesc;
                $nestedData['ToWarehouseDesc'] = $post->FromBinID;
                $nestedData['Reference'] = $post->Reference ?? '';
                $data[] = $nestedData;
            }
        }

        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        );
        return response()->json($json_data);
    }

    public function delete_item(Request $request)
    {
        $id = $request->trc_id;
        $result = IssueMiscellaneous::delete_item($id);
        return response()->json($result);
    }

    public function submit_document(Request $request)
    {
        $DocNum = $request->DocRef;

        $result = IssueMiscellaneous::submit_document($DocNum);
        return response()->json($result);
    }

    public function update_qty_submit(Request $request)
    {
        $DocNum = $request->DocRef;
        $submitData = $request->submitData;

        $result = IssueMiscellaneous::update_qty_submit($DocNum, $submitData);
        return response()->json($result);
    }

    public function update_header_submitter(Request $request)
    {
        $result = IssueMiscellaneous::update_header_submitter($request->all());
        return response()->json($result);
    }

    public function approval_index()
    {
        $my_id = Auth::user()->id;
        $segment_number = env('SEGMENT_NUM');
        $uri = explode("/", url()->current());
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
        return view('issue_miscellaneous/approval_index', $data);
    }

    public function approval_table(Request $request)
    {
        $approvedByCandidates = $this->approvalUserCandidates();

        $columns = array(
            0 => 'DocNum',
            1 => 'DocDate',
            2 => 'Category',
            3 => 'SubmittedBy',
            4 => 'SubmittedAt',
            5 => 'TotalLine'
        );

        $totalData = IssueMiscellaneous::get_approval_list_count($approvedByCandidates);

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $posts = IssueMiscellaneous::get_approval_list($approvedByCandidates, $start, $limit, $order, $dir);
        } else {
            $search = $request->input('search.value');
            $posts =  IssueMiscellaneous::get_approval_list_search($approvedByCandidates, $search, $start, $limit, $order, $dir);
            $totalFiltered = IssueMiscellaneous::get_approval_list_search_count($approvedByCandidates, $search);
        }

        $data = array();
        if (!empty($posts)) {
            $no = $start;
            foreach ($posts as $post) {
                $no++;
                $encryptedDocNum = str_replace('=', '-', Crypt::encryptString($post->DocNum));
                $nestedData['DocNum'] = $post->DocNum;
                $nestedData['DocDate'] = $post->DocDate;
                $nestedData['Category'] = $post->Category;
                $nestedData['SubmittedBy'] = $post->SubmittedBy;
                $nestedData['SubmittedAt'] = $post->SubmittedAt;
                $nestedData['TotalLine'] = $post->TotalLine;
                $nestedData['action'] = '<button class="btn btn-sm btn-primary" onclick="review_document(\'' . $encryptedDocNum . '\')">Review</button>';
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

    public function approval_form(Request $request)
    {
        $action = $request->action ?? null;

        // Handle encryption for URL parameter
        if ($action === 'encrypt') {
            $docNum = $request->DocNum ?? '';
            if (!$docNum) {
                return response()->json(['status' => 'error', 'message' => 'DocNum not provided'], 400);
            }
            try {
                $encrypted = Crypt::encryptString($docNum);
                return response()->json(['status' => 'success', 'encrypted_docnum' => $encrypted]);
            } catch (\Exception $e) {
                return response()->json(['status' => 'error', 'message' => 'Encryption failed'], 500);
            }
        }

        // Handle decryption from URL parameter
        if ($action === 'decrypt') {
            $refUrl = $request->ref_url ?? '';
            if (!$refUrl) {
                return response()->json(['status' => 'error', 'message' => 'ref_url not provided'], 400);
            }
            try {
                $decrypted = Crypt::decryptString($refUrl);
                return response()->json(['status' => 'success', 'docnum' => $decrypted]);
            } catch (\Exception $e) {
                return response()->json(['status' => 'error', 'message' => 'Invalid or expired link'], 400);
            }
        }

        // Normal HTML rendering for form partial
        $data['DocNum'] = $request->DocNum ?? '';
        return view('issue_miscellaneous.approval_form', $data);
    }

    public function approval_detail(Request $request)
    {
        $DocNum = $request->DocNum;
        $result = IssueMiscellaneous::get_approval_detail($DocNum);
        return response()->json($result);
    }

    public function approve_document(Request $request)
    {
        $DocNum = $request->DocNum;
        $result = IssueMiscellaneous::approve_document($DocNum);
        return response()->json($result);
    }

    public function reject_document(Request $request)
    {
        $DocNum = $request->DocNum;
        $result = IssueMiscellaneous::reject_document($DocNum, $request->RejectedReason);
        return response()->json($result);
    }

    public function cancel_document(Request $request)
    {
        $DocNum = $request->DocNum;
        $result = IssueMiscellaneous::delete_document($DocNum);
        return response()->json($result);
    }

    public function get_approval_status_counts(Request $request)
    {
        try {
            $pendingApproval = DB::table('IssueMiscellaneous')
                ->where('Category', 'INV3')
                ->where('IsDelete', 0)
                ->where('RequestStatus', 'PENDING_APPROVAL')
                ->count();

            $approved = DB::table('IssueMiscellaneous')
                ->where('Category', 'INV3')
                ->where('IsDelete', 0)
                ->whereIn('RequestStatus', ['APPROVED', 'AUTO_APPROVED'])
                ->count();

            $completed = DB::table('IssueMiscellaneous')
                ->where('Category', 'INV3')
                ->where('IsDelete', 0)
                ->where('RequestStatus', 'COMPLETED')
                ->count();

            $rejected = DB::table('IssueMiscellaneous')
                ->where('Category', 'INV3')
                ->where('IsDelete', 0)
                ->where('RequestStatus', 'REJECTED')
                ->count();

            return response()->json([
                'pending_approval' => $pendingApproval,
                'approved' => $approved,
                'completed' => $completed,
                'rejected' => $rejected,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'pending_approval' => 0,
                'approved' => 0,
                'completed' => 0,
                'rejected' => 0,
            ], 500);
        }
    }
}
