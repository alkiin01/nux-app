<?php

namespace App\Http\Controllers;

use App\Exports\QuotationPendingExport;
use App\Exports\QuotationPreviewExport;
use App\Imports\QuoMasterPriceImport;
use App\Imports\QuotationImport;
use App\Models\Quotation;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\If_;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\QuotationExport;
use TCPDF;

class QuotationController extends Controller
{
    protected $quotation;
    public function __construct(Quotation $quotation)
    {
        $this->quotation = $quotation;
    }
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
        return view('quotation/quo_index', $data);
    }
    public function master_price_list()
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
        return view('quotation/master_price', $data);
    }
    public function master_price_list_show_data(Request $request)
    {
        $search = $request->search;
        $limit = $request->length;
        $offset = $request->start;
        $data = $this->quotation->master_price_list_show_data($search, $limit, $offset);
        $data = collect($data)->map(function ($row, $index) use ($offset) {
            $id = Crypt::encryptString($row->HeaderID);
            return [
                'No' => $offset + $index + 1,
                'SupplierName' => $row->SupplierName,
                'Customer' => $row->Customer,
                'View' => '
                <button class="btn btn-primary btn-sm btn-icon" style="cursor: pointer;" id="svg_part_preview_' . $id . '"   onclick="part_preview(\'' . $id . '\')">
                <span class="svg-icon svg-icon-primary svg-icon-2x" style="cursor: pointer;" id="svg_part_preview_' . $id . '"   >
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="black"></path>
                        <path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="black"></path>
                        <path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="black"></path>
                    </svg>
                </span>
                <span id="spinner_part_preview_' . $id . '" class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;"></span>
                </button>
                <button class="btn btn-danger btn-sm btn-icon" style="cursor: pointer;" id="svg_part_delete_' . $id . '"   onclick="part_delete(\'' . $id . '\')">
                <span class="svg-icon svg-icon-danger svg-icon-2x" style="cursor: pointer;" id="svg_part_delete_' . $id . '"   >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                    </svg>
                </span>
                <span id="spinner_part_delete_' . $id . '" class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;"></span>
                </button>
                '
            ];
        });
        $count = $this->quotation->master_price_list_count_data($search);
        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => intval($count),
            'recordsFiltered' => intval($count),
            'data' => $data
        ]);
    }
    public function master_price_list_store_data(Request $request)
    {
        $Supplier = $request->supplier;
        $customer = $request->customer;
        $period = $request->period;
        $cutSupplier = explode('~', $Supplier);
        try {
            $check = $this->quotation->data_header($cutSupplier[0], $customer);
            if ($check) {
                $header = $check->HeaderID;
            } else {
                $header = $this->quotation->master_price_list_store_data([
                    'SupplierNum' => $cutSupplier[0],
                    'SupplierName' => $cutSupplier[1],
                    'Customer' => $customer,
                    'CreatedAt' => now('Asia/Jakarta')->format('Y-m-d H:i'),
                    'CreatedBy' => Auth::user()->id,
                    'UpdatedAt' => now('Asia/Jakarta')->format('Y-m-d H:i'),
                    'UpdatedBy' => Auth::user()->id,
                    'PeriodID' => $period
                ]);
                $data_period = $this->quotation->get_period_by_id($period);
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Data successfully created',
                'effective_date' => isset($data_period) ? Carbon::parse($data_period->EffectiveDate)->format('Y-m-d') : null,
                'expired_date' => isset($data_period) ? Carbon::parse($data_period->ExpiredDate)->format('Y-m-d') : null,
                'HeaderID' => Crypt::encryptString($header)
            ]);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => $th->getMessage()]);
        }
    }
    public function master_price_list_find_data(Request $request)
    {
        $id = Crypt::decryptString($request->id);
        try {
            $check = $this->quotation->checkById($id);
            if (!$check) {
                return response()->json(['status' => 'error', 'message' => 'Data Not Found']);
            } else {
                return response()->json(['status' => 'success', 'message' => 'Get Data Success', 'data' => $check]);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => $th->getMessage()]);
        }
    }
    public function master_price_list_update(Request $request)
    {
        $id = Crypt::decryptString($request->id);
        try {
            $check = $this->quotation->checkById($id);
            if (!$check) {
                return response()->json(['status' => 'error', 'message' => 'Data not found']);
            } else {
                $this->quotation->master_price_list_update($id, [
                    'PartNumber' => $request->part_number,
                    'Description' => $request->description,
                    'Price' => (float) $request->price,
                    'UOM' => $request->uom
                ]);
                return response()->json(['status' => 'success', 'message' => 'Update data successfully']);
            }

        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => $th->getMessage()]);
        }
    }
    public function quotation_show_data(Request $request)
    {
        $search = $request->search;
        $limit = $request->length;
        $offset = $request->start;
        $filter = $request->filter;
        $effective = $request->effective;
        $expired = $request->expired;
        $data = $this->quotation->quotation_show_data($search, $filter, $effective, $expired, $limit, $offset);
        $data = collect($data)->map(function ($row, $index) use ($offset) {
            $id = Crypt::encryptString($row->SupplierNum . '~' . $row->Status . '~' . $row->Customer . '~' . $row->EffectiveDate . '~' . $row->ExpiredDate);
            if ($row->Status == 1) {
                $Approved1 = '<button class="btn btn-success btn-sm">Completed</button>';
                $Approved2 = '<button class="btn btn-warning btn-sm">Waiting</button>';
                $Legalize = '<button class="btn btn-warning btn-sm">Waiting</button>';
            } else if ($row->Status == 2) {
                $Approved1 = '<button class="btn btn-success btn-sm">Completed</button>';
                $Approved2 = '<button class="btn btn-success btn-sm">Completed</button>';
                $Legalize = '<button class="btn btn-warning btn-sm">Waiting</button>';
            } else if ($row->Status == 3) {
                $Legalize = '<button class="btn btn-success btn-sm">Completed</button>';
                $Approved1 = '<button class="btn btn-success btn-sm">Completed</button>';
                $Approved2 = '<button class="btn btn-success btn-sm">Completed</button>';
            } else if ($row->Status == 4) {
                $Legalize = '<button class="btn btn-primary btn-sm">Posted</button>';
                $Approved1 = '<button class="btn btn-primary btn-sm">Posted</button>';
                $Approved2 = '<button class="btn btn-primary btn-sm">Posted</button>';
            } else {
                $Approved1 = '<button class="btn btn-warning btn-sm">Waiting</button>';
                $Approved2 = '<button class="btn btn-warning btn-sm">Waiting</button>';
                $Legalize = '<button class="btn btn-warning btn-sm">Waiting</button>';
            }
            $btn_print = '';
            $btn_print = '<button class="btn btn-primary btn-sm btn-icon" onclick="doc_print(\'' . $id . '\')">
                <span class="svg-icon svg-icon-primary svg-icon-2x" width="24" height="24" style="cursor: pointer;" id="svg_document_preview_' . $id . '">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                    </svg>
                </span>
                <span id="spinner_document_preview_' . $id . '" class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;">
                </span>
                </button>';
            $btn_view = '<button class="btn btn-primary btn-sm btn-icon" onclick="document_preview(\'' . $id . '\')">
                <span class="svg-icon svg-icon-primary svg-icon-2x" style="cursor: pointer;" id="svg_document_preview_' . $id . '">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="black"></path>
                        <path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="black"></path>
                        <path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="black"></path>
                    </svg>
                </span>
                <span id="spinner_document_preview_' . $id . '" class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;">
                </span>
                </button>';
            return [
                'No' => $offset + $index + 1,
                'SupplierName' => $row->SupplierName,
                'Customer' => $row->Customer,
                'Effective' => Carbon::parse($row->EffectiveDate)->format('d-M-Y'),
                'Expired' => Carbon::parse($row->ExpiredDate)->format('d-M-Y'),
                'Approved1' => $Approved1,
                'Approved2' => $Approved2,
                'Legalize' => $Legalize,
                'Count' => $row->count,
                'View' => $btn_print . $btn_view
            ];
        });
        $count = $this->quotation->quotation_count_data($search, $filter, $effective, $expired);
        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => intval($count),
            'recordsFiltered' => intval($count),
            'data' => $data
        ]);
    }
    public function quotation_find_data(Request $request)
    {
        $id = Crypt::decryptString($request->id);
        $ids = explode('~', $id);
        // dd($ids);
        $SupplierNum = $ids[0];
        $Status = $ids[1];
        $Customer = $ids[2];
        $Effective = $ids[3];
        $Expired = $ids[4];
        try {
            $check = $this->quotation->quoById($SupplierNum, $Status, $Customer, $Effective, $Expired);
            if ($check->isEmpty()) {
                return response()->json(['status' => 'error', 'message' => 'Data not found']);
            } else {
                $role = DB::table('QuoRoleApproval')
                    ->where('UserID', Auth::user()->id)
                    ->value('Type');
                switch ((int) $Status) {
                    case 1:
                        $status = 'Pending';
                        break;

                    case 2:
                        $status = 'Approved';
                        break;

                    case 3:
                        $status = 'Legalize';
                        break;
                    case 4:
                        $status = 'Post';
                        break;
                    default:
                        $status = 'Unknown';
                }
                return response()->json([
                    'status' => 'success',
                    'message' => 'Data success',
                    'data' => $SupplierNum . '~' . $Status . '~' . $Customer . '~' . $Effective . '~' . $Expired,
                    'status_approval' => $status,
                    'role_status' => (int) $role,
                    'auth_session' => Auth::user()->username
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => $th->getMessage()]);
        }
    }
    public function quotation_process_show(Request $request)
    {
        $id = Crypt::decryptString($request->id);
        $limit = $request->length;
        $offset = $request->start;
        $data = $this->quotation->proccess_show($id, $limit, $offset);
        $data = collect($data)->map(function ($row, $index) use ($offset) {
            return [
                'No' => $offset + $index + 1,
                'NameProccess' => $row->NameProcess,
                'Machine' => $row->Machine,
                'Stroke' => $row->Stroke,
                'Rate' => $row->Rate,
                'Estimate' => $row->Estimate
            ];
        });
        $count = $this->quotation->proccess_count($id);
        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => intval($count),
            'recordsFiltered' => intval($count),
            'data' => $data
        ]);
    }
    public function approved(Request $request)
    {
        $ref = Crypt::decryptString($request->ref_doc);
        $refs = explode('~', $ref);
        $SupplierNum = $refs[0];
        $Status = $refs[1];
        $Customer = $refs[2];
        $Effective = $refs[3];
        $Expired = $refs[4];
        try {
            $check = $this->quotation->getQuoById($SupplierNum, $Status, $Customer, $Effective, $Expired);
            if ($check->isEmpty()) {
                return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
            } else {
                $this->quotation->updateQuo($SupplierNum, $Status, $Customer, $Effective, $Expired);
                return response()->json(['status' => 'success', 'message' => 'Data berhasil di approved']);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => $th->getMessage()]);
        }
    }
    public function import(Request $request)
    {
        $request->validate([
            'file_import' => 'required|file|mimes:xlsx,xls|max:10240'
        ]);

        try {

            DB::beginTransaction();

            $file = $request->file('file_import');

            Excel::import(new QuoMasterPriceImport, $file);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Import berhasil'
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'status' => 500,
                'message' => 'Import gagal: ' . $e->getMessage()
            ], 500);
        }
    }
    public function canceled(Request $request)
    {
        $ref_doc = Crypt::decryptString($request->ref_doc);
        $ref = explode('~', $ref_doc);
        $SupplierNum = $ref[0];
        $Status = $ref[1];
        $Customer = $ref[2];
        $Effective = $ref[3];
        $Expired = $ref[4];
        try {
            $check = $this->quotation->getQuoById($SupplierNum, $Status, $Customer, $Effective, $Expired);
            if ($check->isEmpty()) {
                return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
            } else {
                $this->quotation->cancelQuo($SupplierNum, $Status, $Customer, $Effective, $Expired);
                return response()->json(['status' => 'success', 'message' => 'Data berhasil di canceled']);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => $th->getMessage()]);
        }
    }
    public function get_supplier(Request $request)
    {
        $page = $request->page ?? 1;
        $search = $request->search ?? '';
        $data = $this->quotation->get_supplier($search, $page);
        return response()->json([
            'results' => collect($data->items())->map(function ($item) {
                return [
                    'id' => $item->VendorNum . '~' . $item->Name,
                    'text' => $item->VendorNum . ' - ' . $item->Name,
                ];
            }),
            'pagination' => [
                'more' => $data->hasMorePages()
            ]
        ]);
    }
    public function get_customer(Request $request)
    {
        $page = $request->page ?? 1;
        $search = $request->search ?? '';
        $data = $this->quotation->get_customer($search, $page);
        return response()->json([
            'results' => collect($data->items())->map(function ($item) {
                return [
                    'id' => $item->ProdCode,
                    'text' => $item->ProdCode
                ];
            }),
            'pagination' => [
                'more' => $data->hasMorePages()
            ]
        ]);
    }
    public function get_period(Request $request)
    {
        $page = $request->page ?? 1;
        $search = $request->search ?? '';

        $data = $this->quotation->get_period($search, $page);

        $results = [
            [
                'id' => 'AddItem',
                'text' => '➕ Add New Period'
            ]
        ];

        foreach ($data->items() as $item) {
            $results[] = [
                'id' => $item->id,
                'text' =>
                    \Carbon\Carbon::parse($item->EffectiveDate)->format('d M Y') .
                    ' - ' .
                    \Carbon\Carbon::parse($item->ExpiredDate)->format('d M Y')
            ];
        }

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => $data->hasMorePages()
            ]
        ]);
    }
    public function add_period(Request $request)
    {
        $effective = $request->EffectiveDate;
        $expired = $request->ExpiredDate;
        try {
            $check = $this->quotation->check_period($effective, $expired);
            if ($check) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Period already exists'
                ]);
            } else {
                $this->quotation->add_period($effective, $expired);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Period added successfully'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }
    public function update_master_header(Request $request){
        $ref_doc = Crypt::decryptString($request->ref_doc);
        $refs = explode('~', $ref_doc);
        $headerID=$refs[0];
        $materialID=$refs[1];
        $period = $request->period;
        try {
            $check = $this->quotation->check_document($headerID);
            if (!$check) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data not found'
                ]);
            } else {
                $this->quotation->update_master_header($headerID,$period);
                $this->quotation->update_material_period($headerID,$materialID,$period);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Data updated successfully'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }
    public function get_part_mtl(Request $request)
    {
        $data = $this->quotation->get_part_mtl($request->search, $request->type);
        return response()->json([
            'results' => $data->map(function ($item) {
                return [
                    'id' => $item->PartNum,
                    'text' => $item->PartNum . ' - ' . $item->Description,
                    'Description' => $item->Description,
                    'UOM' => $item->UOM,
                    'Price' => $item->BaseUnitPrice
                ];
            }),
            'pagination' => [
                'more' => $data->hasMorePages()
            ]
        ]);
    }
    public function get_purchase_part(Request $request)
    {
        $data = $this->quotation->get_purchase_part($request->search);
        return response()->json([
            'results' => $data->map(function ($item) {
                return [
                    'id' => $item->PartNum,
                    'text' => $item->PartNum . '-' . $item->PartDescription,
                    'Desc' => $item->PartDescription
                ];
            }),
            'pagination' => [
                'more' => $data->hasMorePages()
            ]
        ]);
    }
    public function master_price_list_store_material(Request $request)
    {
        $id = Crypt::decryptString($request->header_id);
        $header_data = $this->quotation->getMaterialByHeaderID($id);
        $data = [
            'HeaderID' => $id,
            'PartMtl' => $request->part_mtl,
            'PartMtlDesc' => $request->material_cost_spec,
            'PartFG' => $request->part_fg,
            'PartFGDesc' => $request->part_fg_desc,
            'MtlWQty' => $request->material_weight_qty,
            'PartWQty' => $request->part_weight_qty,
            'ScrapQty' => $request->scrap_qty,
            'MtlWPrice' => $request->material_weight_price,
            'MtlSPrice' => $request->material_sheet_price,
            'ScrapPrice' => $request->scrap_price,
            'MtlCEstimate' => $request->material_cost_estimate,
            'MtlWEstimate' => $request->material_weight_estimate,
            'ScrapEstimate' => abs((float) $request->scrap_estimate),
            'UOM' => $request->uom,
            'DepreciationQty' => $request->dep_qty,
            'DepreciationPrice' => $request->dep_price,
            'CreatedAt' => now('Asia/Jakarta'),
            'CreatedBy' => Auth::user()->id,
            'VolQty' => $request->volume_qty,
            'Note' => $request->note,
            'TopEndCoil' => $request->top_end_coil
        ];
        try {
            if ($header_data) {
                if (!$header_data->PeriodID) {
                    $data['EffectiveDate'] = $request->effective_date;
                    $data['ExpiredDate'] = $request->expired_date;
                } else {
                    $period = $this->quotation->get_period_by_id($header_data->PeriodID);
                    $data['EffectiveDate'] = $period->EffectiveDate;
                    $data['ExpiredDate'] = $period->ExpiredDate;
                }
                $MtlID = $this->quotation->insertMaterial($data);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Data berhasil dibuat',
                    'ref_doc' => Crypt::encryptString($header_data->HeaderID . '~' . $MtlID)
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data header tidak ditemukan'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }
    public function master_price_list_update_material(Request $request)
    {
        $ref = Crypt::decryptString($request->ref_doc);
        $refs = explode('~', $ref);
        $MtlID = $refs[1];
        // $data = $this->quotation->
        $data = [
            'PartMtl' => $request->part_mtl,
            'PartMtlDesc' => $request->material_cost_spec,
            'PartFG' => $request->part_fg,
            'PartFGDesc' => $request->part_fg_desc,
            'MtlWQty' => $request->material_weight_qty,
            'PartWQty' => $request->part_weight_qty,
            'ScrapQty' => $request->scrap_qty,
            'MtlWPrice' => $request->material_weight_price,
            'MtlSPrice' => $request->material_sheet_price,
            'ScrapPrice' => $request->scrap_price,
            'MtlCEstimate' => $request->material_cost_estimate,
            'MtlWEstimate' => $request->material_weight_estimate,
            'ScrapEstimate' => abs((float) $request->scrap_estimate),
            'UOM' => $request->uom,
            'DepreciationQty' => $request->dep_qty,
            'DepreciationPrice' => $request->dep_price,
            'VolQty' => $request->volume_qty,
            'Note' => $request->note,
            // 'EffectiveDate' => $request->effective_date,
            // 'ExpiredDate' => $request->expired_date,
            'TopEndCoil' => $request->top_end_coil
        ];
        // dd($data);
        try {
            $this->quotation->updateMaterial($MtlID, $data);
            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil di ubah'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }
    public function master_price_list_show_process(Request $request)
    {
        $refs = Crypt::decryptString($request->ref_doc);
        $refs = explode('~', $refs);
        $headerID = $refs[0];
        $MtlID = $refs[1];
        $offset = (int) $request->start;
        $limit = (int) $request->length;
        $rows = $this->quotation->show_process($headerID, $MtlID, $limit, $offset);
        $data = collect($rows)->map(function ($row) {
            return [
                'NameProcess' => $row->NameProcess,
                'Machine' => $row->Machine,
                'Stroke' => (float) $row->Stroke,
                'Rate' => 'Rp ' . rtrim(rtrim(number_format((float) $row->Rate, 2, '.', ','), '0'), '.'),
                'Estimate' => 'Rp ' . rtrim(rtrim(number_format((float) $row->Estimate, 2, '.', ','), '0'), '.'),
                'View' => '
                <div class="d-flex gap-2 justify-content-center">
                    <button class="btn btn-primary btn-sm btn-edit" data-id="' . $row->ProcessID . '" onclick="editProcess(' . $row->ProcessID . ')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07
                                a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1
                                1.13-1.897l8.932-8.931Z"/>
                        </svg>
                    </button>

                    <button class="btn btn-danger btn-sm btn-delete" data-id="' . $row->ProcessID . '" 
                    onclick="hapusProcess(' . $row->ProcessID . ')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21
                                c.342.052.682.107 1.022.166m-1.022-.165
                                L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077
                                H8.084a2.25 2.25 0 0 1-2.244-2.077
                                L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397"/>
                        </svg>
                    </button>
                </div>
            '
            ];
        });
        $count = $this->quotation->count_show_process($headerID, $MtlID);
        return response()->json([
            'draw' => (int) $request->draw,
            'recordsTotal' => (int) $count,
            'recordsFiltered' => (int) $count,
            'data' => $data->values()
        ]);
    }
    public function master_list_store_process(Request $request)
    {
        $refs = Crypt::decryptString($request->ref_doc);
        $refs = explode('~', $refs);
        $headerID = $refs[0];
        $MtlID = $refs[1];
        $data = [
            'NameProcess' => $request->name_process,
            'Machine' => $request->machine,
            'Stroke' => str_replace(',', '.', $request->stroke),
            'Rate' => str_replace(',', '.', $request->rate),
            'Estimate' => str_replace(',', '.', $request->estimate)
        ];
        try {
            if (!$request->process_id) {
                $check_avail = $this->quotation->getProcessByIdAndName($headerID, $MtlID, $request->name_process);
                if ($check_avail) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Nama proses sudah digunakan'
                    ]);
                }
                $data['HeaderID'] = (int) $headerID;
                $data['MtlID'] = (int) $MtlID;
                $data['CreatedAt'] = now('Asia/Jakarta');
                $data['CreatedBy'] = Auth::user()->id;
                $this->quotation->insertProcess($data);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Process berhasil di buat'
                ]);
            } else {
                $check_data = $this->quotation->getProcessID($request->process_id);
                if (!$check_data) {
                    return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
                } else {
                    $data['UpdatedAt'] = now('Asia/Jakarta');
                    $data['UpdatedBy'] = Auth::user()->id;
                    $this->quotation->updateProcess($request->process_id, $data);
                    return response()->json(['status' => 'success', 'message' => 'Data berhasil diubah']);
                }
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => $th->getMessage()]);
        }
    }
    public function master_price_list_find_process(Request $request)
    {
        try {
            $check_data = $this->quotation->getProcessID($request->processID);
            if (!$check_data) {
                return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
            } else {
                return response()->json(['status' => 'success', 'data' => $check_data]);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => $th->getMessage()]);
        }
    }
    public function master_price_list_delete_process(Request $request)
    {
        try {
            $check_data = $this->quotation->getProcessID($request->id);
            if (!$check_data) {
                return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
            } else {
                $this->quotation->deleteProcess($request->id);
                return response()->json(['status' => 'success', 'message' => 'Data berhasil di hapus']);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => $th->getMessage()]);
        }
    }
    public function master_price_list_show_other_cost(Request $request)
    {
        $refs = Crypt::decryptString($request->ref_doc);
        $refs = explode('~', $refs);
        $headerID = $refs[0];
        $MtlID = $refs[1];
        $data = $this->quotation->show_other_cost($headerID, $MtlID);
        $rows = collect($data)->map(function ($row, $index) {
            $qty = str_replace('_', ' ', $row->AdditionType);
            $percent = '-';
            if ($row->Percentage > 0) {
                $percent = rtrim(rtrim(number_format((float) $row->Percentage, 2, '.', ','), '0'), '.') . '%';
            }
            return [
                'No' => $index + 1,
                'NameItem' => $row->NameItem,
                'Percentage' => $percent,
                'Quantity' => $qty,
                'Estimate' => 'Rp ' . rtrim(rtrim(number_format((float) $row->Estimate, 2, '.', ','), '0'), '.'),
                'View' => '<div class="d-flex gap-2 justify-content-center">
                    <button class="btn btn-primary btn-sm btn-edit" data-id="' . $row->OtherCostID . '" onclick="otherEdit(' . $row->OtherCostID . ')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07
                                a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1
                                1.13-1.897l8.932-8.931Z"/>
                        </svg>
                    </button>

                    <button class="btn btn-danger btn-sm btn-delete" data-id="' . $row->OtherCostID . '" 
                    onclick="otherHapus(' . $row->OtherCostID . ')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21
                                c.342.052.682.107 1.022.166m-1.022-.165
                                L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077
                                H8.084a2.25 2.25 0 0 1-2.244-2.077
                                L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397"/>
                        </svg>
                    </button>
                </div>'
            ];
        });
        return response()->json([
            'draw' => (int) $request->draw,
            'recordsTotal' => $rows->count(),
            'recordsFiltered' => $rows->count(),
            'data' => $rows
        ]);
    }
    public function master_price_list_preview(Request $request)
    {
        $refs = Crypt::decryptString($request->ref_doc);
        $refs = explode('~', $refs);
        $headerID = $refs[0];
        $MtlID = $refs[1];
        try {
            $data = $this->quotation->PriceListPreview($headerID, $MtlID);
            if (!$data) {
                return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
            } else {
                if ($data && $data->EffectiveDate && $data->ExpiredDate) {
                    $data->period = \Carbon\Carbon::parse($data->EffectiveDate)->format('d M Y') .
                        ' - ' .
                        \Carbon\Carbon::parse($data->ExpiredDate)->format('d M Y');
                }
                return response()->json(['status' => 'success', 'data' => $data]);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => $th->getMessage()]);
        }
    }
    public function master_price_list_delete_document(Request $request)
    {
        try {
            $id = Crypt::decryptString($request->id);
            $check = $this->quotation->check_document($id);
            if ($check) {
                $this->quotation->delete_document($id);
                return response()->json(['status' => 'success', 'message' => 'Data berhasil di hapus']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => $th->getMessage()]);
        }
    }
    public function master_price_list_show_purchase(Request $request)
    {
        $refs = Crypt::decryptString($request->ref_doc);
        $refs = explode('~', $refs);
        // dd($refs);
        $headerID = $refs[0];
        $MtlID = $refs[1];
        $offset = (int) $request->start;
        $limit = (int) $request->length;
        $rows = $this->quotation->show_purchase($headerID, $MtlID, $limit, $offset);
        $data = collect($rows)->map(function ($row, $index) use ($offset) {
            return [
                'No' => $offset + $index + 1,
                'PurchasePart' => $row->PurchasePart,
                'SpecPurchasePart' => $row->SpecPurchasePart,
                'Qty' => number_format((float) $row->Qty, 0, ',', '.'),
                'Price' => 'Rp ' . number_format((float) $row->Price, 0, '.', ','),
                'Estimate' => 'Rp ' . number_format((float) $row->Estimate, 0, '.', ','),
                'View' => '
                <div class="d-flex gap-2 justify-content-center">
                    <button class="btn btn-primary btn-sm btn-edit" data-id="' . $row->PurchaseID . '" onclick="editPurchase(' . $row->PurchaseID . ')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07
                                a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1
                                1.13-1.897l8.932-8.931Z"/>
                        </svg>
                    </button>

                    <button class="btn btn-danger btn-sm btn-delete" data-id="' . $row->PurchaseID . '" 
                    onclick="hapusPurchase(' . $row->PurchaseID . ')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21
                                c.342.052.682.107 1.022.166m-1.022-.165
                                L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077
                                H8.084a2.25 2.25 0 0 1-2.244-2.077
                                L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397"/>
                        </svg>
                    </button>
                </div>
            '
            ];
        });
        $count = $this->quotation->count_show_purchase($headerID, $MtlID);
        return response()->json([
            'draw' => (int) $request->draw,
            'recordsTotal' => (int) $count,
            'recordsFiltered' => (int) $count,
            'data' => $data->values()
        ]);
    }
    public function master_price_list_purchase_store(Request $request)
    {
        $ref_doc = Crypt::decryptString($request->ref_doc);
        $refs = explode('~', $ref_doc);
        $headerID = $refs[0];
        $MtlID = $refs[1];
        try {
            $data = [
                'PurchasePart' => $request->purchase_part,
                'SpecPurchasePart' => $request->spec_purchase_part,
                'Qty' => $request->qty,
                'Price' => str_replace(',', '', $request->price),
                'Estimate' => str_replace(',', '', $request->estimate)
            ];
            if (!$request->purchase_id) {
                $check = $this->quotation->data_purchase($headerID, $MtlID, $request->purchase_part);
                if ($check) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Nama item sudah digunakan'
                    ]);
                }
                $ref_doc = Crypt::decryptString($request->ref_doc);
                $refs = explode('~', $ref_doc);
                $data['HeaderID'] = $refs[0];
                $data['MtlID'] = $refs[1];
                // $data['CreatedAt'] = now('Asia/Jakarta');
                // $data['CreatedBy'] = Auth::user()->id;
                $this->quotation->store_purchase($data);
                $message = 'Data berhasil di submit';
            } else {
                // $data['UpdatedAt'] = now('Asia/Jakarta');
                // $data['UpdatedBy'] = Auth::user()->id;
                $this->quotation->update_purchase($request->purchase_id, $data);
                $message = 'Data berhasil di ubah';
            }
            return response()->json([
                'status' => 'success',
                'message' => $message
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }
    public function master_price_list_find_purchase(Request $request)
    {
        $id = $request->id;
        try {
            $data = $this->quotation->find_purchase($id);
            if ($data) {
                return response()->json([
                    'status' => 'success',
                    'data' => $data
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan'
                ]);
            }

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }
    public function master_price_list_delete_purchase(Request $request)
    {
        $id = $request->id;
        try {
            $data = $this->quotation->find_purchase($id);
            if ($data) {
                $this->quotation->delete_purchase($id);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Data berhasil di hapus'
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }
    public function master_price_list_show_data_part(Request $request)
    {
        $headerID = Crypt::decryptString($request->id);
        $offset = (int) $request->start;
        $limit = (int) $request->length;
        $search = $request->search;
        $rows = $this->quotation->show_all_part($headerID, $search, $limit, $offset);
        $data = collect($rows)->map(function ($row, $index) use ($offset) {
            $ref_doc = Crypt::encryptString($row->HeaderID . '~' . $row->MtlID);
            $status = '<button class="btn btn-warning btn-sm">Draft</button>';
            if ($row->Status == 1) {
                $status = '<button class="btn btn-success btn-sm">Confirm</button>';
            }
            return [
                'No' => $offset + $index + 1,
                'PartMtl' => $row->PartMtl,
                'PartMtlDesc' => $row->PartMtlDesc,
                'PartFG' => $row->PartFG,
                'PartFGDesc' => $row->PartFGDesc,
                'Status' => $status,
                'View' => '
                <div class="d-flex gap-2 justify-content-center">
                <button class="btn btn-primary btn-sm btn-icon" style="cursor: pointer;" id="svg_document_view_' . $ref_doc . '"   onclick="document_view(\'' . $ref_doc . '\')">
                <span class="svg-icon svg-icon-primary svg-icon-2x" style="cursor: pointer;" id="svg_document_view_' . $ref_doc . '"   >
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                </span>
                <span id="spinner_document_view_' . $ref_doc . '" class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;"></span>
                </button>
                    <button class="btn btn-primary btn-sm btn-icon" style="cursor: pointer;" id="svg_document_preview_' . $ref_doc . '"   onclick="document_preview(\'' . $ref_doc . '\')">
                <span class="svg-icon svg-icon-primary svg-icon-2x" style="cursor: pointer;" id="svg_document_preview_' . $ref_doc . '"   >
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="black"></path>
                        <path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="black"></path>
                        <path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="black"></path>
                    </svg>
                </span>
                <span id="spinner_document_preview_' . $ref_doc . '" class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;"></span>
                </button>

                    <button class="btn btn-danger btn-sm btn-icon" data-id="' . $ref_doc . '" 
                    onclick="hapusDoc(\'' . $ref_doc . '\')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21
                                c.342.052.682.107 1.022.166m-1.022-.165
                                L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077
                                H8.084a2.25 2.25 0 0 1-2.244-2.077
                                L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397"/>
                        </svg>
                    </button>
                </div>
            '
            ];
        });
        $count = $this->quotation->count_all_part($headerID, $search);
        return response()->json([
            'draw' => (int) $request->draw,
            'recordsTotal' => (int) $count,
            'recordsFiltered' => (int) $count,
            'data' => $data->values()
        ]);
    }
    public function master_price_list_delete_part(Request $request)
    {
        $ref_doc = Crypt::decryptString($request->ref_doc);
        $refs = explode('~', $ref_doc);
        try {
            $this->quotation->delete_part($refs[0], $refs[1]);
            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil di hapus'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }
    public function back_mtl(Request $request)
    {
        $ref_doc = Crypt::decryptString($request->ref_doc);
        $refs = explode('~', $ref_doc);
        return response()->json(Crypt::encryptString($refs[0]));
    }
    public function name_item_other_cost(Request $request)
    {
        $ref_doc = Crypt::decryptString($request->ref_doc);
        $refs = explode('~', $ref_doc);
        $mtl = $refs[1];
        $item = $request->item;
        $results = '';
        if ($item == 'x_material_cost') {
            $results = $this->quotation->get_material_cost($mtl);
        } else if ($item == 'x_manufactur_cost') {
            $results = $this->quotation->get_manufactur_cost($mtl);
        } else if ($item == 'blank_cost') {
            $results = $this->quotation->get_part_qty($mtl);
        }else if ($item == 'x_sub_total'){
            $results = $this->quotation->get_sub_total($mtl);
        }
         else {
            $results = 0;
        }
        return response()->json($results);
    }
    public function store_other_cost(Request $request)
    {
        $ref_doc = Crypt::decryptString($request->ref_doc);
        $refs = explode('~', $ref_doc);
        $head = $refs[0];
        $mtl = $refs[1];
        $name = $request->name;
        $estimate = $request->estimate;
        $percen = $request->percen;
        $data = $this->quotation->getOtherCostByMtl($mtl, $name);
        if ($data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Item sudah digunakan'
            ]);
        } else {
            $this->quotation->storeOtherCost([
                'HeaderID' => $head,
                'MtlID' => $mtl,
                'NameItem' => $name,
                'Percentage' => $percen,
                'Estimate' => (float) $estimate,
                'AdditionType' => $request->addition_type_other_cost,
                'CreatedAt' => now('Asia/Jakarta'),
                'CreatedBy' => Auth::user()->id
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil di kirim'
            ]);
        }

    }
    public function find_other_cost(Request $request)
    {
        $OtherCostID = $request->OtherCostID;
        $data = $this->quotation->find_other_cost($OtherCostID);
        if ($data) {
            if ($data == 'Administration Charge') {
                $results = $this->quotation->get_material_cost($data->MtlID);
            } else {
                $results = $this->quotation->get_manufactur_cost($data->MtlID);
            }
            return response()->json([
                'status' => 'success',
                'data' => $data,
                'results' => $results
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ]);
        }
    }
    public function update_other_cost(Request $request)
    {
        $id = $request->id;
        $percen = (float) $request->percen;
        $estimate = (float) $request->estimate;
        $additionType = (string) $request->addition_type_other_cost;
        $data = $this->quotation->find_other_cost($id);
        if ($data) {
            $this->quotation->update_other_cost($id, [
                'Percentage' => $percen,
                'Estimate' => $estimate,
                'AdditionType' => $additionType,
                'UpdatedAt' => now('Asia/Jakarta'),
                'UpdatedBy' => Auth::user()->id
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil diubah'
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ]);
        }
    }
    public function delete_other_cost(Request $request)
    {
        $id = $request->id;
        $data = $this->quotation->find_other_cost($id);
        if ($data) {
            $this->quotation->delete_other_cost($id);
            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil dihapus'
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ]);
        }
    }
    public function summary_table(Request $request)
    {
        $id = Crypt::decryptString($request->ref_doc);
        $id = explode('~', $id);
        $Supplier = $id[0];
        $Status = $id[1];
        $Customer = $id[2];
        $Effective = $id[3];
        $Expired = $id[4];
        $offset = (int) $request->start;
        $limit = (int) $request->length;
        $search = $request->search;
        $rows = $this->quotation->show_all_summary($Supplier, $Status, $Customer, $Effective, $Expired, $search, $limit, $offset);
        $data = collect($rows)->map(function ($row, $index) use ($offset) {
            $current = (float) $row->CurrentTotalSalesPrice;
            $pass = (float) $row->PassTotalSalesPrice;
            $gap = $current - $pass;
            if ($gap > 0) {
                $gapRp = '<span class="text-danger">Rp +' . number_format($gap, 2, ',', '.') . '</span>';
            } elseif ($gap < 0) {
                $gapRp = '<span class="text-success">Rp ' . number_format($gap, 2, ',', '.') . '</span>';
            } else {
                $gapRp = '<span class="text-primary">Rp 0</span>';
            }
            if ($pass == 0) {
                $percen = 0;
            } else {
                $percen = (($current - $pass) / $pass) * 100;
            }

            if ($percen > 0) {
                $percentage = '<span class="text-danger">+' . number_format($percen, 2) . '%</span>';
            } elseif ($percen < 0) {
                $percentage = '<span class="text-success">' . number_format($percen, 2) . '%</span>';
            } else {
                $percentage = '<span class="text-primary">0.00%</span>';
            }


            return [
                'No' => $offset + $index + 1,
                'PartMtl' => $row->PartMtl,
                'PartMtlDesc' => $row->PartMtlDesc,
                'PartFG' => $row->PartFG,
                'PartFGDesc' => $row->PartFGDesc,
                'CurrentTotalSalesPrice' => 'Rp ' . number_format((float) $row->CurrentTotalSalesPrice, 2, '.', ','),
                'PassTotalSalesPrice' => 'Rp ' . number_format((float) $row->PassTotalSalesPrice, 2, '.', ','),
                'GAP' => $gapRp,
                'Percentage' => $percentage
            ];
        });
        $count = $this->quotation->count_all_summary($Supplier, $Status, $Customer, $Effective, $Expired, $search);
        return response()->json([
            'draw' => (int) $request->draw,
            'recordsTotal' => (int) $count,
            'recordsFiltered' => (int) $count,
            'data' => $data->values()
        ]);
    }
    public function master_price_list_quotation_delete_data(Request $request)
    {
        $id = Crypt::decryptString($request->id);
        try {
            $this->quotation->delete_quotation_data($id);
            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil di hapus'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);

        }
    }
    public function total_header(Request $request)
    {
        $w_supplier = $this->quotation->total_header()->where('Status', 0)->get()->count();
        $w_sai = $this->quotation->total_header()->where('Status', 1)->get()->count();
        $w_legalize = $this->quotation->total_header()->where('Status', 2)->get()->count();
        return response()->json([
            'w_supplier' => $w_supplier,
            'w_sai' => $w_sai,
            'w_legalize' => $w_legalize,
            'total' => $this->quotation->total_header()->where('Status', '>=', 0)->get()->count(),
            'filter' => [
                'w_supplier' => 0,
                'w_sai' => 1,
                'w_legalize' => 2
            ]
        ]);
    }
    public function print_excel(Request $request)
    {
        $search = $request->search;
        $filter = $request->filter;
        $effective = $request->effective;
        $expired = $request->expired;

        return Excel::download(
            new QuotationExport($search, $filter, $effective, $expired),
            'Quotation_' . date('Ymd_His') . '.xlsx'
        );
    }
    public function print_out(Request $request)
    {
        $ref_doc = Crypt::decryptString($request->ref_doc);
        $refs = explode('~', $ref_doc);
        $supplier = $refs[0];
        $status = $refs[1];
        $customer = $refs[2];
        $effective = $refs[3];
        $expired = $refs[4];
        $header = $this->quotation->QuoHeader($supplier, $status, $customer, $effective, $expired);
        $table = $this->quotation->QuoTabel($supplier, $status, $customer, $effective, $expired);
        $get_supplier = $this->quotation->get_name_supplier($header->SupplierNum);
        $approval = $this->quotation->get_approval($header->HeaderID);
        $html = view('quotation/quo_preview', [
            'head' => $header,
            'table' => $table,
            'supplier' => $get_supplier,
            'approval' => $approval
        ])->render();
        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('NUX');
        $pdf->SetAuthor('Quotation System');
        $pdf->SetTitle('Quotation Document');
        $pdf->SetFont('dejavusans', '', 9);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(5, 5, 5);
        $pdf->SetAutoPageBreak(true, 5);
        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');

        return response($pdf->Output('quo_' . $supplier . '.pdf', 'I'))
            ->header('Content-Type', 'application/pdf');
    }
    public function confirm_master(Request $request)
    {
        $ref_doc = Crypt::decryptString($request->ref_doc);
        $refs = explode('~', $ref_doc);
        $head = $refs[0];
        $mtl = $refs[1];
        $this->quotation->confirm_master($head, [
            'Status' => 1
        ]);
        return response()->json([
            'status' => 'success',
            'message' => 'Confirm success'
        ]);
    }
    public function cancel_master(Request $request)
    {
        $ref_doc = Crypt::decryptString($request->ref_doc);
        $refs = explode('~', $ref_doc);
        $head = $refs[0];
        $mtl = $refs[1];
        $this->quotation->confirm_master($head, [
            'Status' => 0
        ]);
        return response()->json([
            'status' => 'success',
            'message' => 'Confirm success'
        ]);
    }
    public function document_view(Request $request)
    {
        $ref_doc = Crypt::decryptString($request->ref_doc);
        $refs = explode('~', $ref_doc);
        $header = $this->quotation->header_view($refs[0]);
        $material = $this->quotation->material_view($refs[1]);
        $purchase = $this->quotation->purchase_view($refs[1]);
        $process = $this->quotation->process_view($refs[1]);
        $other_cost = $this->quotation->other_cost_view($refs[1]);
        $html = view('quotation.doc_view', [
            'header' => $header,
            'material' => $material,
            'purchase' => $purchase,
            'process' => $process,
            'other' => $other_cost
        ])->render();
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('NUX');
        $pdf->SetAuthor('Quotation System');
        $pdf->SetTitle('Quotation Document');
        $pdf->SetFont('dejavusans', '', 9);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 15);

        $pdf->AddPage();

        $pdf->writeHTML($html, true, false, true, false, '');

        $pdfContent = $pdf->Output('quotation.pdf', 'S');

        return response()->json([
            'status' => 200,
            'pdf' => base64_encode($pdfContent)
        ]);
    }
    public function download_preview(Request $request)
    {
        $ref_preview = Crypt::decryptString($request->ref_preview);
        $refs = explode('~', $ref_preview);
        $data = $this->quotation->download_preview($refs[0]);
        return Excel::download(
            new QuotationPreviewExport($data),
            'Quotation_Preview_' . date('Ym') . '.xlsx'
        );
    }
    public function import_update(Request $request)
    {
        $ref_doc = Crypt::decryptString($request->ref_preview);
        $refs = explode('~', $ref_doc);
        $headerID = $refs[0];
        try {
            Excel::import(new QuotationImport($headerID), $request->file('file'));
            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil di import'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }
    public function list_quotation(Request $request)
    {
        $ref_doc = Crypt::decryptString($request->ref_doc);
        $refs = explode('~',$ref_doc);
        $supplierNum=$refs[0];
        $customer=$refs[2];
        $effectiveDate=$refs[3];
        $expiredDate=$refs[4];
        $offset = (int) $request->start;
        $limit = (int) $request->length;
        $search = $request->search;
        $rows = $this->quotation->list_quotation($supplierNum,$customer,$effectiveDate,$expiredDate,$search, $limit, $offset);
        $data = collect($rows)->map(function ($row, $index) use ($offset) {
            return [
                'No' => $offset + $index + 1,
                'PartFG' => $row->PartFG,
                'PartFGDesc' => $row->PartFGDesc,
                'PartMtl'=>$row->PartMtl,
                'PartMtlDesc'=>$row->PartMtlDesc,
                'MtlWPrice' => number_format($row->MtlWPrice,2,'.',','),
            ];
        });
        $count = $this->quotation->count_list_quotation($supplierNum,$customer,$effectiveDate,$expiredDate,$search);
        return response()->json([
            'draw' => (int) $request->draw,
            'recordsTotal' => (int) $count,
            'recordsFiltered' => (int) $count,
            'data' => $data->values()
        ]);
    }
    public function print_pending_quo(Request $request)
    {
        $ref_doc = Crypt::decryptString($request->ref_doc);
        $refs = explode('~', $ref_doc);
        $supplierNum = $refs[0];
        $customer = $refs[2];
        $effectiveDate = $refs[3];
        $expiredDate = $refs[4];
        $data = $this->quotation->print_pending_quo($supplierNum, $customer, $effectiveDate, $expiredDate);
        return Excel::download(
            new QuotationPendingExport($data),
            'Quotation_Pending_' . date('Ym') . '.xlsx'
        );
    }
    public function post_quo(Request $request)
    {
        $ref_doc = Crypt::decryptString($request->ref_doc);
        $refs = explode('~', $ref_doc);
        $SupplierNum = $refs[0];
        $Status = $refs[1];
        $Customer = $refs[2];
        $Effective = $refs[3];
        $Expired = $refs[4];
        try {
            if ($Status < 3) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid Status'
                ]);
            }
            $data = $this->quotation->get_quo_data($SupplierNum, $Status, $Customer, $Effective, $Expired);
            if (!$data) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data not found'
                ]);
            }
            $client = new Client([
                'timeout' => 30
            ]);
            $host_api = self::get_host_api();
            foreach ($data as $item) {
                $response = $client->request('POST', $host_api . 'SupplierPriceLst/UpdateHeader', [
                    'json' => [
                        "vendorNum" => $SupplierNum,
                        "partNum" => $item->PartFG,
                        "effectiveDate" => $item->EffectiveDate,
                        "expirationDate" => $item->ExpiredDate,
                        "baseUnitPrice" => $item->CurrentTotalSalesPrice,
                        "pum" => "PCS",
                        "username" => Auth::user()->username,
                        "password" => Crypt::decryptString(Auth::user()->epicor_password)
                    ],
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                    'verify' => false,
                ]);
                $body = json_decode($response->getBody()->getContents(), true);
                if ($body['code'] != 200) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'API Error',
                        'response' => $body
                    ], 500);
                }
                if ($body['data']['epi_code'] != 200) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Epicor Error: ' . $body['data']['epi_status'],
                        'response' => $body
                    ], 500);
                }
            }
            $this->quotation->update_quo_head($SupplierNum, $Customer, $Effective, $Expired);
            return response()->json([
                'status' => 'success',
                'message' => 'All request successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }
}
