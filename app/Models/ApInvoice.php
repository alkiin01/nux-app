<?php

namespace App\Models;

use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApInvoice extends Model
{
    use HasFactory;
    public function tablePrimary($search, $status_filter, $start_date, $finish_date, $limit, $offset)
    {
        $vendorNumsByName = [];
        if (!empty($search)) {
            $vendorNumsByName = DB::connection('sqlsrv4')
                ->table('Erp.Vendor')
                ->where('Name', 'LIKE', "%{$search}%")
                ->pluck('VendorNum')
                ->toArray();
        }
        $invoiceQuery = DB::table('APInvoice as a')
            ->select(
                'a.id',
                'a.GroupID',
                'a.InvoiceNum',
                'a.PONum',
                'a.Approved_PO',
                'a.Approved_AP',
                'a.VendorNum',
                'a.InvoiceDate',
                'a.Posted',
                'a.Status'
            );
        if (!empty($search)) {
            $invoiceQuery->where(function ($q) use ($search, $vendorNumsByName) {
                $q->where('a.GroupID', 'LIKE', "%{$search}%")
                    ->orWhere('a.InvoiceNum', 'LIKE', "%{$search}%")
                    ->orWhere('a.PONum', 'LIKE', "%{$search}%");

                if (!empty($vendorNumsByName)) {
                    $q->orWhereIn('a.VendorNum', $vendorNumsByName);
                }
            });
        }
        if (!empty($start_date)) {
            $invoiceQuery->whereDate('a.InvoiceDate', '>=', $start_date);
        }
        if (!empty($finish_date)) {
            $invoiceQuery->whereDate('a.InvoiceDate', '<=', $finish_date);
        }
        if ($status_filter !== null && $status_filter !== '') {
            if ($status_filter == 3) {
                $invoiceQuery->whereIn('a.Status', [3, 4]);
            } else {
                $invoiceQuery->where('a.Status', $status_filter);
            }

        }
        $invoice = $invoiceQuery
            ->orderBy('a.InvoiceDate', 'DESC')
            ->offset($offset)
            ->limit($limit)
            ->get();

        if ($invoice->isEmpty()) {
            return collect([]);
        }
        $vendorNums = $invoice->pluck('VendorNum')->unique()->toArray();

        $vendors = DB::connection('sqlsrv4')
            ->table('Erp.Vendor')
            ->whereIn('VendorNum', $vendorNums)
            ->get()
            ->keyBy('VendorNum');
        $result = $invoice->map(function ($row) use ($vendors) {
            $vendor = $vendors->get($row->VendorNum);
            $row->VendorName = $vendor->Name ?? null;
            $row->GroupIDVendor = $vendor->GroupID ?? null;
            $row->InvoiceNumVendor = $vendor->InvoiceNum ?? null;

            return $row;
        });

        return $result;
    }

    public function countApInvoice($search, $status_filter, $start_date, $finish_date)
    {
        $vendorNumsByName = [];

        if (!empty($search)) {
            $vendorNumsByName = DB::connection('sqlsrv4')
                ->table('Erp.Vendor')
                ->where('Name', 'LIKE', "%{$search}%")
                ->pluck('VendorNum')
                ->toArray();
        }
        $query = DB::table('APInvoice as a');

        if (!empty($search)) {
            $query->where(function ($q) use ($search, $vendorNumsByName) {
                $q->where('a.GroupID', 'LIKE', "%{$search}%")
                    ->orWhere('a.InvoiceNum', 'LIKE', "%{$search}%")
                    ->orWhere('a.PONum', 'LIKE', "%{$search}%");

                if (!empty($vendorNumsByName)) {
                    $q->orWhereIn('a.VendorNum', $vendorNumsByName);
                }
            });
        }
        if (!empty($start_date)) {
            $query->whereDate('a.InvoiceDate', '>=', $start_date);
        }
        if (!empty($finish_date)) {
            $query->whereDate('a.InvoiceDate', '<=', $finish_date);
        }
        if ($status_filter !== null && $status_filter !== '') {
            $query->where('a.Status', $status_filter);
        }

        return $query->count('a.GroupID');
    }

    public function getById($id)
    {
        return DB::table('APInvoice')->where('id', $id)->first();
    }
    public function VendorName($vendorID)
    {
        return DB::connection('sqlsrv4')->table('Erp.Vendor')->where('VendorID', $vendorID)->select('Name')->first();
    }
    public function Header()
    {
        $approved_po = DB::table('APInvoice')
            ->where('Approved_PO', 1)
            ->where('Approved_AP', 0)
            ->where('Status', 1)
            ->count();

        $approved_ap = DB::table('APInvoice')
            ->where('Approved_PO', 1)
            ->where('Approved_AP', 1)
            ->where('Status', 2)
            ->count();

        $Pending = DB::table('APInvoice')
            ->where('Approved_PO', 0)
            ->where('Approved_AP', 0)
            ->where('Status', 0)
            ->count();
        $Reject = DB::table('APInvoice')
            ->where('Approved_PO', 0)
            ->where('Approved_AP', 0)
            ->whereIn('Status', [3, 4])
            ->count();

        $total = DB::table('APInvoice')
            ->count();

        return [
            'approved_po' => $approved_po,
            'approved_ap' => $approved_ap,
            'pending' => $Pending,
            'reject' => $Reject,
            'total' => $total,
        ];
    }
    public function previewDetail($id, $search, $limit, $offset)
    {
        if (is_array($id))
            $id = reset($id);
        if (is_array($search))
            $search = reset($search);
        $limit = (int) (is_array($limit) ? reset($limit) : $limit);
        $offset = (int) (is_array($offset) ? reset($offset) : $offset);

        $query = DB::table('APInvoiceDtl')
            ->select(
                DB::raw('MAX(PONum) AS PONum'),
                DB::raw('MAX(InvoiceNum) AS InvoiceNum'),
                DB::raw('MAX(PackSlip) AS PackSlip'),
                DB::raw('SUM(Qty) AS Qty'),
                DB::raw('SUM(PricePO) AS PricePO'),
                DB::raw('SUM(PriceGR) AS PriceGR'),
                DB::raw('SUM(AmountPO) AS AmountPO'),
                DB::raw('SUM(AmountGR) AS AmountGR'),
                DB::raw('MAX(UOM) AS UOM')
            )
            ->where('APInvoiceID', $id);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('PONum', 'LIKE', "%{$search}%")
                    ->orWhere('PackSlip', 'LIKE', "%{$search}%");
            });
        }

        return $query
            ->groupBy('PackSlip')
            ->orderBy('PONum', 'DESC')
            ->offset($offset)
            ->limit($limit)
            ->get();
    }


    public function CountPreviewDtl($id, $search)
    {
        if (is_array($id))
            $id = reset($id);
        if (is_array($search))
            $search = reset($search);
        $query = DB::table('APInvoiceDtl')
            ->where('APInvoiceID', $id);
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('PONum', 'LIKE', "%{$search}%")
                    ->orWhere('PackSlip', 'LIKE', "%{$search}%");
            });
        }

        return $query->distinct('PackSlip')->count();
    }
    public function approvedVal($id)
    {
        return DB::table('APInvoice')
            ->where('id', $id)
            ->exists();
    }
    public function approved($id)
    {
        $query = DB::table('APInvoice')
            ->where('id', $id)
            ->first();
        if (Auth::user()->role_id == 7) {
            $query = DB::table('APInvoice')
                ->where('id', $id)
                ->update([
                    'Approved_PO' => 1,
                    'Status' => 1
                ]);
        } elseif (Auth::user()->role_id == 3) {
            $query = DB::table('APInvoice')
                ->where('id', $id)
                ->update([
                    'Approved_AP' => 1,
                    'Status' => 2,
                    'Posted' => 1
                ]);
            return ['api' => true];
        }
        return $query;
    }
    public function cancelApproval($id)
    {
        if (Auth::user()->role_id == 3) {
            $query = DB::table('APInvoice')
                ->where('id', $id)
                ->update([
                    'Status' => 1,
                    'Approved_AP' => 0,
                    'Posted' => 0
                ]);
            return ['api' => true];
        } elseif (Auth::user()->role_id == 7) {
            $val = DB::table('APInvoice')
                ->where('id', $id)
                ->first();
            if ($val->Approved_AP == 1) {
                return ['error' => 'Batalkan AP dahulu sebelum pembatalan PO'];
            }
            $query = DB::table('APInvoice')
                ->where('id', $id)
                ->update([
                    'Status' => 0,
                    'Approved_PO' => 0
                ]);
        }
        return $query;

    }
    public function deleteAP($id)
    {
        $baseUrl = config('services.epicor.base_url');
        $ap = DB::table('APInvoice')
            ->where('id', $id)
            ->select('VendorNum', 'InvoiceNum', 'GroupID')
            ->first();
        $deleteInv = Http::withoutVerifying()->delete($baseUrl . '/APInvoice/DeleteInvoice', [
            "invoiceNum" => $ap->InvoiceNum,
            "vendorNum" => $ap->VendorNum,
            "nik" => Auth::user()->username,
            "password" => Crypt::decryptString(Auth::user()->epicor_password)
        ]);
        $invRes = $deleteInv->json();
        $invEpiCode = $invRes['data']['epi_code'] ?? null;
        $invEpiStatus = $invRes['data']['epi_status'] ?? null;
        if ($invRes['code'] != 200) {
            return [
                'status' => 'error',
                'message' => $invRes['status'] ?? 'Epicor DeleteInvoice failed',
                'step' => 'DeleteInvoice',
                'response' => $invRes
            ];
        }
        if ($invEpiCode !== null && $invEpiCode != 200) {
            return [
                'status' => 'error',
                'message' => $invEpiStatus ?? 'Epicor Delete Invoice failed',
                'step' => 'Delete Invoice',
                'response' => $invRes
            ];
        }
        $deleteGroup = Http::withoutVerifying()->delete($baseUrl . '/APInvoice/DeleteGroup', [
            "groupID" => $ap->GroupID,
            "nik" => Auth::user()->username,
            "password" => Crypt::decryptString(Auth::user()->epicor_password)
        ]);
        $grpRes = $deleteGroup->json();
        $grpEpiCode = $grpRes['data']['epi_code'] ?? null;
        $grpEpiStatus = $grpRes['data']['epi_status'] ?? null;
        if ($grpRes['code'] != 200) {
            return [
                'status' => 'error',
                'message' => $grpRes['status'] ?? 'Epicor DeleteGroup failed',
                'step' => 'DeleteGroup',
                'response' => $grpRes
            ];
        }
        if ($grpEpiCode !== null && $grpEpiCode != 200) {
            return [
                'status' => 'error',
                'message' => $grpEpiStatus ?? 'Epicor Delete Invoice failed',
                'step' => 'Delete Invoice',
                'response' => $grpRes
            ];
        }
        return [
            'status' => 'success',
            'message' => 'Data berhasil di hapus dari epicor'
        ];
    }
    public function CheckStatus($id)
    {
        $query = DB::table('APInvoice')
            ->where('id', $id)
            ->first();
        if (Auth::user()->role_id == 7) {
            $status = 'PO' . $query->Approved_PO;
        } elseif (Auth::user()->role_id == 3) {
            if ($query->Approved_PO == 0) {
                $status = 'NO PO';
            } else {
                $status = 'AP' . $query->Approved_AP;
            }
        } else {
            $status = 'Unauthorized';
        }
        return $status;
    }
    public function terms()
    {
        return DB::connection('sqlsrv4')
            ->table('Erp.Terms')
            ->select('TermsCode', 'Description')
            ->get();
    }
    public function submit_change($id, $data)
    {
        return DB::table('APInvoice')
            ->where('id', $id)
            ->update($data);
    }
    public function SendEpicor($id)
    {
        $baseUrl = config('services.epicor.base_url');
        $data = DB::table('APInvoice')
            ->where('id', $id)
            ->first();
        // dd($data);
        $updateGroup = Http::withoutVerifying()->post($baseUrl . '/APInvoice/UpdateGroup', [
            "groupID" => $data->GroupID,
            "nik" => Auth::user()->username,
            "password" => Crypt::decryptString(Auth::user()->epicor_password)
        ]);

        $groupRes = $updateGroup->json();
        $groupEpiCode = $groupRes['data']['epi_code'] ?? null;
        $groupEpiStatus = $groupRes['data']['epi_status'] ?? null;
        if ($groupRes['code'] != 200) {
            return [
                'status' => 'error',
                'message' => $groupRes['status'] ?? 'Epicor UpdateGroup failed',
                'step' => 'UpdateGroup',
                'response' => $groupRes
            ];
        }
        if ($groupEpiCode !== null && $groupEpiCode != 200) {
            return [
                'status' => 'error',
                'message' => $groupEpiStatus ?? 'Epicor UpdateGroup failed',
                'step' => 'UpdateGroup',
                'response' => $groupRes
            ];
        }
        $updateHead = Http::withoutVerifying()->post($baseUrl . '/APInvoice/UpdateHead', [
            "groupID" => $data->GroupID,
            "invoiceNum" => $data->InvoiceNum,
            "vendorID" => $data->VendorID,
            "vendorNum" => $data->VendorNum,
            "invoiceDate" => $data->InvoiceDate,
            "supplierInvDate" => $data->SupplierInvoiceDate,
            "proposedTermsCode" => $data->TermsCode,
            "nik" => Auth::user()->username,
            "password" => Crypt::decryptString(Auth::user()->epicor_password)
        ]);

        $headRes = $updateHead->json();
        $headEpiCode = $headRes['data']['epi_code'] ?? null;
        $headEpiStatus = $headRes['data']['epi_status'] ?? null;
        if ($headRes['code'] != 200) {
            return [
                'status' => 'error',
                'message' => $headRes['status'] ?? 'Epicor UpdateHead failed',
                'step' => 'UpdateGroup',
                'response' => $headRes
            ];
        }
        if ($headEpiCode !== null && $headEpiCode != 200) {
            return [
                'status' => 'error',
                'message' => $headEpiStatus ?? 'Epicor UpdateHead failed',
                'step' => 'UpdateHead',
                'response' => $headRes
            ];
        }
        Http::withoutVerifying()->post($baseUrl . '/APInvoice/SetAPInvoiceAmount', [
            "invoiceNum" => $data->InvoiceNum,
            "nik" => Auth::user()->username,
            "password" => Crypt::decryptString(Auth::user()->epicor_password),
            "vendorNum" => (int) $data->VendorNum,
            "proposedInvoiceVendorAmt" => (float) $data->ConfirmNote,
            "eFakturNumber"=>$data->FakturNum,
            "applyDate"=> $data->AppliedDate
        ]);
        $invDtl = DB::table('APInvoiceDtl')
            ->where('APInvoiceID', $id)
            ->select('InvoiceNum', 'PONum', 'PackSlip')
            ->groupBy('InvoiceNum', 'PONum', 'PackSlip')
            ->get();
        set_time_limit(0);
        ini_set('max_execution_time', 0);
        $client = new Client([
            'verify' => false,
            'timeout' => 120,
            'connect_timeout' => 10,
        ]);
        foreach ($invDtl as $row) {
            $saveDetail = $client->request('POST', $baseUrl . '/APInvoice/SetLineInvoice', [
                'json' => [
                    "invoiceNum" => $row->InvoiceNum,
                    "packSlip" => $row->PackSlip,
                    "vendorNum" => $data->VendorNum,
                    "poNum" => $row->PONum,
                    "nik" => Auth::user()->username,
                    "password" => Crypt::decryptString(Auth::user()->epicor_password)
                ]
            ]);
            $detailRes = json_decode($saveDetail->getBody()->getContents(), true);
            $detailEpiCode = $detailRes['data']['epi_code'] ?? null;
            $detailEpiStatus = $detailRes['data']['epi_status'] ?? null;
            if ($detailRes['code'] != 200) {
                return [
                    'status' => 'error',
                    'message' => $detailRes['status'] ?? 'Epicor SaveDetail failed',
                    'step' => 'UpdateGroup',
                    'response' => $detailRes
                ];
            }
            if ($detailEpiCode !== null && $detailEpiCode != 200) {
                return [
                    'status' => 'error',
                    'message' => $detailEpiStatus . $row->PackSlip ?? 'Epicor SaveDetail failed',
                    'step' => 'SaveDetail',
                    'packSlip' => $row->PackSlip,
                    'response' => $detailRes
                ];
            }
        }
        return [
            'status' => 'success',
            'message' => 'Data berhasil dikirim ke Epicor'
        ];
    }
    public function RcvHead($PONum, $InvoiceNum, $PackSlip, $limit, $offset)
    {
        $query = DB::table('APInvoiceDtl')
            ->where('PONum', $PONum)
            ->where('InvoiceNum', $InvoiceNum)
            ->where('PackSlip', $PackSlip)
            ->offset($offset)
            ->limit($limit)
            ->get();
        return $query;

    }
    public function CountVendoeShpDtl($PONum, $InvoiceNum, $PackSlip)
    {
        return DB::table('APInvoiceDtl')
            ->where('PONum', $PONum)
            ->where('InvoiceNum', $InvoiceNum)
            ->where('PackSlip', $PackSlip)
            ->count();
    }
    public function checkTerms($terms)
    {
        return DB::connection('sqlsrv4')
            ->table('Erp.Terms')
            ->select('NumberOfDays')
            ->where('TermsCode', $terms)
            ->first();
    }
    public function allItemTbl($id, $search, $offset, $limit)
    {
        $query = DB::table('APInvoiceDtl')
            ->where('APInvoiceID', $id)
            ->select(
                DB::raw('MAX(PartNum) AS PartNum'),
                DB::raw('MAX(PartDesc) AS PartDesc'),
                DB::raw('SUM(Qty) AS Qty'),
                DB::raw('MAX(PricePO) AS PricePO'),
                DB::raw('MAX(PriceGR) AS PriceGR'),
                DB::raw('SUM(AmountPO) AS AmountPO'),
                DB::raw('SUM(AmountGR) AS AmountGR'),
                DB::raw('MAX(UOM) AS UOM')
            )
            ->groupBy('PartNum');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('PartNum', 'LIKE', "%{$search}%")
                    ->orWhere('PartDesc', 'LIKE', "%{$search}%");
            });
        }
        return $query->offset($offset)->limit($limit)->get();
    }
    public function allItemTblCount($id, $search)
    {
        $base = DB::table('APInvoiceDtl')
        ->where('APInvoiceID', $id)
        ->groupBy('PartNum')
        ->select('PartNum');

    if (!empty($search)) {
        $base->where(function ($q) use ($search) {
            $q->where('PartNum', 'LIKE', "%{$search}%")
              ->orWhere('PartDesc', 'LIKE', "%{$search}%");
        });
    }

    return DB::query()
        ->fromSub($base, 't')
        ->count();
    }
    public function SummaryReceipt($id)
    {
        return DB::table('APInvoice')
            ->where('id', $id)
            ->select('TotalGR', 'TotalPO')
            ->first();
    }
    public function Recalculate($inv)
    {
        $localDB = DB::table('APInvoice')
            ->where('InvoiceNum', $inv)
            ->first();
        if (!$localDB) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }
        $recalAP = Http::withoutVerifying()->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->post('https://192.168.1.251:8001/APIDEV/APInvoice/Recalculate', [
                    "invoiceNum" => $inv,
                    "nik" => Auth::user()->username,
                    "password" => Crypt::decryptString(Auth::user()->epicor_password)
                ]);
        if (!$recalAP->successful()) {
            throw new Exception($recalAP->body());
        }
        $response = $recalAP->json();
        if (($response['code'] ?? null) !== 200) {
            return response()->json([
                'status' => false,
                'message' => 'Unknown Error'
            ]);
        }
        $data = $response['data'];
        $invoiceAmount = (float) $data['invoiceAmount'];
        $TaxPO = 0.11 * $invoiceAmount;
        $DPP_PO = $invoiceAmount / 1.11;
        DB::table('APInvoice')
            ->where('InvoiceNum', $inv)
            ->update([
                'TotalPO' => $invoiceAmount,
                'DPPPO' => $DPP_PO,
                'TaxPO' => $TaxPO
            ]);
        return true;
    }
    // public function show_new_approval($id)
    // {
    //     return DB::table('APInvoice')
    //         ->where('id', $id)
    //         ->first();
    // }
    public function show_pdf($id, $type)
    {
        $data = DB::table('APInvoice')
            ->where('id', $id)
            ->select('InvoiceNum as folder', 'InvoiceAttachment', 'FakturAttachment')
            ->first();
            $nameFolder = preg_replace('/[^A-Za-z0-9]/', '', $data->folder);
            if ($type == 'invoice') {
                return [
                    'file' => $data->InvoiceAttachment,
                    'folder' => $nameFolder
                ];
            } else if ($type == 'facture') {
                return [
                    'file' => $data->FakturAttachment,
                    'folder' => $nameFolder
                ];
            }
    }
    public function reject($id)
    {
        $data = DB::table('APInvoice')
            ->where('id', $id)
            ->first();
        if (!$data) {
            throw new \Exception("Data tidak ditemukan");
        }
        if (Auth::user()->role_id == 7) {
            if ($data->Approved_AP == 1 || $data->Status == 2) {
                throw new \Exception("Saat ini reject hanya bisa di lakukan oleh AP");
            }
            return true;
        } else if (Auth::user()->role_id == 3) {
            return true;
        } else {
            throw new \Exception("Anda tidak memiliki akses untuk melakukan reject");
        }
    }
    public function submit_reject($id, $remark)
    {
        if (Auth::user()->role_id == 7) {
            $status = 3;
        } elseif (Auth::user()->role_id == 3) {
            $status = 4;
            self::deleteAP($id);
        }
        return DB::table('APInvoice')
            ->where('id', $id)
            ->update([
                'Approved_PO' => 0,
                'Approved_AP' => 0,
                'Status' => $status,
                'RemarkReject' => $remark
            ]);
    }
}
