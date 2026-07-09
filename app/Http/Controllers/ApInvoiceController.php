<?php

namespace App\Http\Controllers;

use App\Models\ApInvoice;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;

class ApInvoiceController extends Controller
{
    protected $ApInvoice;
    public function __construct(ApInvoice $ApInvoice)
    {
        $this->ApInvoice = $ApInvoice;
    }
    public function index(Request $request)
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
        return view('ap_invoice.ap_invoice_index', $data);
    }
    public function tablePrimary(Request $request)
    {
        $limit = $request->length;
        $offset = $request->start;
        $search = $request->search;
        $status_filter = $request->status_filter;
        $start_date = $request->start_date;
        $finish_date = $request->finish_date;
        $data = $this->ApInvoice->tablePrimary($search, $status_filter, $start_date, $finish_date, $limit, $offset);
        $data = collect($data)->map(function ($row, $index) use ($offset) {
            $statusAP = '<button class="btn btn-warning btn-sm">Pending</button>';
            $statusPO = '<button class="btn btn-warning btn-sm">Pending</button>';
            if (($row->Approved_AP == 0 && $row->Status == 1) || ($row->Approved_AP == 0 && $row->Status == 0)) {
                $statusAP = '<button class="btn btn-warning btn-sm">Pending</button>';
            } else if ($row->Approved_AP == 1 && $row->Status == 2) {
                $statusAP = '<button class="btn btn-success btn-sm">Approved</button>';
            } else if ($row->Approved_AP == 0 && $row->Status == 4) {
                $statusAP = '<button class="btn btn-danger btn-sm">Reject</button>';
            }
            if ($row->Approved_PO == 0 && $row->Status == 0) {
                $statusPO = '<button class="btn btn-warning btn-sm">Pending</button>';
            } else if (($row->Approved_PO == 1 && $row->Status == 1) || ($row->Approved_PO == 1 && $row->Status == 2)) {
                $statusPO = '<button class="btn btn-success btn-sm">Approved</button>';
            } else if ($row->Approved_PO == 0 && $row->Status == 3) {
                $statusPO = '<button class="btn btn-danger btn-sm">Reject</button>';
            }
            return [
                'no' => $offset + $index + 1,
                'Vendor' => $row->VendorName,
                'GroupID' => $row->GroupID,
                'InvoiceNum' => $row->InvoiceNum,
                'PONum' => $row->PONum,
                'status_PO' => $statusPO,
                'status_AP' => $statusAP,
                'InvoiceDate' => $row->InvoiceDate,
                'View' => '<button onclick="document_preview(\'' . Crypt::encryptString($row->id) . '\')" class="btn btn-primary btn-sm btn-icon"><span class="svg-icon svg-icon-primary svg-icon-2x" style="cursor: pointer;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="black"/>
                <path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM12 16.8C11 16.8 10.2 16.4 9.5 15.8C8.8 15.1 8.5 14.3 8.5 13.3C8.5 12.8 8.59999 12.3 8.79999 11.9L10 13.1V10.1C10 9.50001 9.6 9.10001 9 9.10001H6L7.29999 10.4C6.79999 11.3 6.5 12.2 6.5 13.3C6.5 14.8 7.10001 16.2 8.10001 17.2C9.10001 18.2 10.5 18.8 12 18.8C12.6 18.8 13 18.3 13 17.8C13 17.2 12.6 16.8 12 16.8ZM16.7 16.2C17.2 15.3 17.5 14.4 17.5 13.3C17.5 11.8 16.9 10.4 15.9 9.39999C14.9 8.39999 13.5 7.79999 12 7.79999C11.4 7.79999 11 8.19999 11 8.79999C11 9.39999 11.4 9.79999 12 9.79999C12.9 9.79999 13.8 10.2 14.5 10.8C15.2 11.5 15.5 12.3 15.5 13.3C15.5 13.8 15.4 14.3 15.2 14.7L14 13.5V16.5C14 17.1 14.4 17.5 15 17.5H18L16.7 16.2Z" fill="black"/>
                <path opacity="0.3" d="M12 16.8C11 16.8 10.2 16.4 9.5 15.8C8.8 15.1 8.5 14.3 8.5 13.3C8.5 12.8 8.59999 12.3 8.79999 11.9L7.29999 10.4C6.79999 11.3 6.5 12.2 6.5 13.3C6.5 14.8 7.10001 16.2 8.10001 17.2C9.10001 18.2 10.5 18.8 12 18.8C12.6 18.8 13 18.3 13 17.8C13 17.2 12.6 16.8 12 16.8Z" fill="black"/>
                <path opacity="0.3" d="M15.5 13.3C15.5 13.8 15.4 14.3 15.2 14.7L16.7 16.2C17.2 15.3 17.5 14.4 17.5 13.3C17.5 11.8 16.9 10.4 15.9 9.39999C14.9 8.39999 13.5 7.79999 12 7.79999C11.4 7.79999 11 8.19999 11 8.79999C11 9.39999 11.4 9.79999 12 9.79999C12.9 9.79999 13.8 10.2 14.5 10.8C15.1 11.5 15.5 12.4 15.5 13.3Z" fill="black"/>
                </svg>
            </span></button>'
            ];
        });
        $count = $this->ApInvoice->countApInvoice($search, $status_filter, $start_date, $finish_date);
        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => intval($count),
            'recordsFiltered' => intval($count),
            'data' => $data
        ]);
    }
    public function header(Request $request)
    {
        return response()->json($this->ApInvoice->Header());
    }
    public function preview_doc(Request $request)
    {
        $id = Crypt::decryptString($request->doc);
        $data = $this->ApInvoice->getById($id);
        $vendorID = $data->VendorID;
        $vendorObj = $this->ApInvoice->VendorName($vendorID);
        $vendorName = $vendorObj->Name ?? $vendorID;
        return response()->json([
            'data' => $data,
            'doc' => $request->doc,
            'vendor' => $vendorName
        ]);
    }
    public function previewDetail(Request $request)
    {
        $limit = $request->length;
        $offset = $request->start;
        $search = $request->search;
        $id = Crypt::decryptString($request->id);
        $data = $this->ApInvoice->previewDetail($id, $search, $limit, $offset);
        $data = collect($data)->map(function ($row, $index) use ($offset) {
            $linkedBtn = $row->PONum . '~' . $row->InvoiceNum . '~' . $row->PackSlip;
            return [
                'no' => $offset + $index + 1,
                'PackSlip' => $row->PackSlip,
                'Qty' => number_format($row->Qty, 0, ',', '.'),
                'PricePO' => 'Rp ' . number_format($row->PricePO, 0, '.', ','),
                'PriceGR' => 'Rp ' . number_format($row->PriceGR, 0, '.', ','),
                'AmountPO' => 'Rp ' . number_format($row->AmountPO, 0, '.', ','),
                'AmountGR' => 'Rp ' . number_format($row->AmountGR, 0, '.', ','),
                'UOM' => $row->UOM,
                'View' => '
                <span class="svg-icon svg-icon-primary svg-icon-2x" style="cursor: pointer;" id="svg_detail_preview_' . $linkedBtn . '"   onclick="detail_preview(\'' . $linkedBtn . '\')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="black"></path>
                        <path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="black"></path>
                        <path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="black"></path>
                    </svg>
                </span>
                <span id="spinner_detail_preview_' . $linkedBtn . '" class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;"></span>'
            ];
        });
        $count = $this->ApInvoice->CountPreviewDtl($id, $search);
        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => intval($count),
            'recordsFiltered' => intval($count),
            'data' => $data
        ]);
    }
    public function approved(Request $request)
    {
        $id = Crypt::decryptString($request->id);
        $val = $this->ApInvoice->approvedVal($id);
        if ($val == false) {
            return response()->json([
                'status' => 404,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        try {
            $result = $this->ApInvoice->approved($id);
            if (is_array($result) && isset($result['api'])) {
                $sendEpicor = $this->ApInvoice->SendEpicor($id);
                if ($sendEpicor['status'] === 'error') {
                    return response()->json([
                        'status' => 400,
                        'message' => $sendEpicor['message'],
                        'step' => $sendEpicor['step'],
                        'detail' => $sendEpicor['response']
                    ], 400);
                }
            }

            return response()->json([
                'status' => 200,
                'message' => 'Approved successfully'
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => $th->getMessage()
            ]);
        }
    }
    public function cancel_approval(Request $request)
    {
        $id = Crypt::decryptString($request->id);
        $val = $this->ApInvoice->approvedVal($id);
        if ($val == false) {
            return response()->json([
                'status' => 404,
                'message' => 'Data tidak ditemukan'
            ]);
        }
        try {
            $result = $this->ApInvoice->cancelApproval($id);
            if (is_array($result) && isset($result['error'])) {
                return response()->json([
                    'status' => 500,
                    'message' => $result['error']
                ]);
            }
            if (is_array($result) && isset($result['api'])) {
                $cancelEpicor = $this->ApInvoice->deleteAP($id);
                if ($cancelEpicor['status'] === 'error') {
                    return response()->json([
                        'status' => 400,
                        'message' => $cancelEpicor['message'],
                        'step' => $cancelEpicor['step'],
                        'detail' => $cancelEpicor['response']
                    ], 400);
                }
            }
            return response()->json([
                'status' => 200
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => $th->getMessage()
            ]);
        }
    }
    public function check_status(Request $request)
    {
        $id = Crypt::decryptString($request->id);
        $data = $this->ApInvoice->CheckStatus($id);
        return response()->json($data);
    }
    public function terms(Request $request)
    {
        return response()->json($this->ApInvoice->terms());
    }
    public function submit_change(Request $request)
    {
        $id = Crypt::decryptString($request->id);
        $group = $request->group;
        $invNum = $request->invNum;
        $val = $this->ApInvoice->approvedVal($id);
        if ($val == false) {
            return response()->json([
                'status' => 404,
                'message' => 'Data tidak ditemukan'
            ]);
        }
        $data = [
            'InvoiceDate' => $request->invoice_date,
            'AppliedDate' => $request->applied_date,
            'SupplierInvoiceDate' => $request->supplier_inv_date,
            'TermsCode' => $request->terms,
            'DueDate' => $request->due_date,
            'Updated_at' => now('Asia/Jakarta')->format('Y-m-d'),
            'Updated_by' => Auth::user()->id
        ];
        // dd($data);
        try {
            $this->ApInvoice->submit_change($id, $data);
            return response()->json([
                'status' => 200
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => $th->getMessage()
            ]);
        }
    }
    public function detailPackSlip(Request $request)
    {
        $linkedBtn = $request->linkedBtn;
        [$PONum, $InvoiceNum, $PackSlip] = explode('~', $linkedBtn);
        $offset = $request->start;
        $limit = $request->length;
        $data = $this->ApInvoice->RcvHead($PONum, $InvoiceNum, $PackSlip, $limit, $offset);
        $data = collect($data)->map(function ($row, $index) use ($offset) {
            return [
                'no' => $offset + $index + 1,
                'PackSlip' => $row->PackSlip,
                'PartNum' => $row->PartNum,
                'PartDesc' => $row->PartDesc,
                'Qty' => number_format($row->Qty, 0, ',', '.'),
                'PricePO' => 'Rp ' . number_format($row->PricePO, 0, '.', ','),
                'PriceGR' => 'Rp ' . number_format($row->PriceGR, 0, '.', ','),
                'AmountPO' => 'Rp ' . number_format($row->AmountPO, 0, '.', ','),
                'AmountGR' => 'Rp ' . number_format($row->AmountGR, 0, '.', ','),
                'UOM' => $row->UOM
            ];
        });
        $count = $this->ApInvoice->CountVendoeShpDtl($PONum, $InvoiceNum, $PackSlip);
        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => intval($count),
            'recordsFiltered' => intval($count),
            'data' => $data
        ]);
    }
    public function change_terms(Request $request)
    {
        $invoiceDate = $request->invDate;
        $terms = $request->terms;
        $data = $this->ApInvoice->checkTerms($terms);
        $days = intval($data->NumberOfDays);
        $dueDate = \Carbon\Carbon::parse($invoiceDate)->addDays($days)->format('Y-m-d');
        return response()->json([
            'status' => 200,
            'DueDate' => $dueDate
        ]);
    }
    public function all_item_tbl(Request $request)
    {
        $id = Crypt::decryptString($request->id);
        $offset = $request->start;
        $limit = $request->length;
        $search = $request->input('search');
        $data = $this->ApInvoice->allItemTbl($id, $search, $offset, $limit);
        $data = collect($data)->map(function ($row, $index) use ($offset) {
            return [
                'no' => $offset + $index + 1,
                'PartNum' => $row->PartNum,
                'PartDesc' => $row->PartDesc,
                'Qty' => number_format($row->Qty, 0, ',', '.'),
                'PricePO' => 'Rp ' . number_format($row->PricePO, 0, '.', ','),
                'PriceGR' => 'Rp ' . number_format($row->PriceGR, 0, '.', ','),
                'AmountPO' => 'Rp ' . number_format($row->AmountPO, 0, '.', ','),
                'AmountGR' => 'Rp ' . number_format($row->AmountGR, 0, '.', ','),
                'UOM' => $row->UOM
            ];
        });
        $count = $this->ApInvoice->allItemTblCount($id, $search);
        $summary = $this->ApInvoice->SummaryReceipt($id);
        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => intval($count),
            'recordsFiltered' => intval($count),
            'data' => $data,
            'summary' => $summary
        ]);
    }
    public function export(Request $request)
    {
        $ref_doc = Crypt::decryptString($request->ref_doc);
        while (ob_get_level() > 0)
            ob_end_clean();
        if (class_exists(\Debugbar::class))
            \Debugbar::disable();
        ini_set('zlib.output_compression', '0');
        $content = Excel::raw(
            new \App\Exports\APInvoiceExport($ref_doc),
            ExcelFormat::XLSX
        );

        return response($content, 200, [
            "Content-Type" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            "Content-Disposition" => "inline; filename=invoice.xlsx",
            "Cache-Control" => "max-age=0, no-cache, must-revalidate, proxy-revalidate",
            "Content-Length" => strlen($content),
        ]);
    }
    public function Recalculate(Request $request)
    {
        $inv = $request->inv;
        try {
            $recal = $this->ApInvoice->Recalculate($inv);
            if ($recal == true) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Recalculate Berhasil'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }
    // public function show_new_approval(Request $request)
    // {
    //     $id = Crypt::decryptString($request->id);
    //     $data = $this->ApInvoice->show_new_approval($id);
    //     return response()->json([
    //         'data' => $data
    //     ]);
    // }
    public function show_pdf(Request $request)
    {
        $id = Crypt::decryptString($request->id);
        $type = $request->type;
        $data = $this->ApInvoice->show_pdf($id, $type);
        return response()->json([
            'file' => $data['file'],
            'folder' => $data['folder']
        ]);
    }
    public function reject(Request $request)
    {
        $id = Crypt::decryptString($request->id);
        try {
            $this->ApInvoice->reject($id);
            return response()->json([
                'status' => 200,
                'message' => 'Rejected successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => $th->getMessage()
            ]);
        }
    }
    public function submit_reject(Request $request)
    {
        $id = Crypt::decryptString($request->id);
        $remark = $request->remark_reject;
        try {
            $this->ApInvoice->submit_reject($id, $remark);
            return response()->json([
                'status' => 200,
                'message' => 'Rejected successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => $th->getMessage()
            ]);
        }
    }
}
