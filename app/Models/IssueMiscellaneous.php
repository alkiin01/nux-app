<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IssueMiscellaneous extends Model
{
    private const AUTHORIZED_SUBMITTER_CODES = ['161022-037', '270723-001'];

    private static function currentMonthId()
    {
        date_default_timezone_set('Asia/Jakarta');
        return substr(date('Y'), 2, 2) . date('m');
    }
   
    use HasFactory;
    protected $fillable = [
        'TranTypeID',
        'MonthID',
        'TranSeqID',
        'DocDate',
        'Category',
        'CreatedBy',
        'CreatedAt',
        'UpdatedBy',
        'LastUpdated',
        'Approved',
        'Submitted',
        'SubmittedBy',
        'RequestStatus',
        'IsDelete',
    ];
    public static function get_transaction_list($search, $category, $DocDate)
    {
        date_default_timezone_set('Asia/Jakarta');

        $result = DB::table('IssueMiscellaneous as a')
            ->leftJoin('IssueMiscellaneousDetail as b', function ($join) {
                $join->on('a.TranTypeID', '=', 'b.TranTypeID')
                    ->on('a.MonthID', '=', 'b.MonthID')
                    ->on('a.TranSeqID', '=', 'b.TranSeqID')
                    ->where('b.IsDelete', '=', 0);
            })
            ->selectRaw("
                CONCAT(a.TranTypeID, '~', a.MonthID, '~', a.TranSeqID) AS DocNum,
                a.DocDate,
                a.Category,
                a.ReasonCode,
                a.CreatedAt,
                a.CreatedBy,
                a.Approved,
                a.Submitted,
                a.RequestStatus,
                a.UpdatedBy,
                a.LastUpdated,
                COUNT(b.LineID) AS TotalLine
            ")
            ->where('a.IsDelete', 0)
            ->groupBy(
                'a.TranTypeID',
                'a.MonthID',
                'a.TranSeqID',
                'a.DocDate',
                'a.Category',
                'a.ReasonCode',
                'a.Approved',
                'a.Submitted',
                'a.RequestStatus',
                'a.CreatedAt',
                'a.CreatedBy',
                'a.UpdatedBy',
                'a.LastUpdated'
            );

        if (!empty($category)) {
            $result = $result->where('a.Category', '=', $category);
        }

        if (!empty($DocDate)) {
            $result = $result->where('a.DocDate', '=', $DocDate);
        }

        if (!empty($search)) {
            $result = $result->where(function ($query) use ($search) {
                $query->whereRaw("CONCAT(a.TranTypeID, '~', a.MonthID, '~', a.TranSeqID) LIKE ?", ["%{$search}%"])
                    ->orWhere('a.CreatedBy', 'like', "%{$search}%")
                    ->orWhere('a.ReasonCode', 'like', "%{$search}%")
                    ->orWhere('a.RequestStatus', 'like', "%{$search}%");
            });
        }

        return $result;
    }

    public static function get_new_docnum($DocDate)
    {
        $MonthID = self::currentMonthId();

        // Use numeric max to avoid lexical ordering issues (e.g. "9" > "10" as string).
        $maxTranSeq = DB::table('IssueMiscellaneous')
            ->where('TranTypeID', 200)
            ->where('MonthID', $MonthID)
            ->selectRaw('MAX(CAST(TranSeqID AS INT)) AS max_seq')
            ->value('max_seq');

        $TranSeqID = ((int) $maxTranSeq) + 1;

        // Collision guard for unexpected legacy/invalid data.
        while (DB::table('IssueMiscellaneous')
            ->where('TranTypeID', 200)
            ->where('MonthID', $MonthID)
            ->where('TranSeqID', $TranSeqID)
            ->exists()) {
            $TranSeqID++;
        }

        return '200~' . $MonthID . '~' . $TranSeqID;
    }

    public static function showPart($Category)
    {
         $result = DB::connection('sqlsrv4')->table('Erp.Part')
            ->select('PartNum', 'PartDescription')
            ->where('inactive',0)
            ;

        if (!empty($Category)) {

           
            if($Category == 'INV3'){
                $result = $result->whereIn('ClassID',['INV3','INV4']);
            }else{
                $result = $result->where('ClassID', $Category);
            }
        }

        return $result;
    }

    public static function ShowUOM($partnum)
    {
        return DB::connection('sqlsrv4')->table('Erp.Part')
            ->select('IUM')
            ->where('PartNum', $partnum);
    }

    public static function ShowPartStock($partnum, $category = null)
    {
        $query = DB::connection('sqlsrv4')->table('Erp.PartWhse as a')
            ->leftJoin('Erp.Part as b', 'b.PartNum', '=', 'a.PartNum')
            ->select('a.OnhandQty')
            ->where('a.PartNum', $partnum)
            ->where('inactive',0);

        if ($category === 'INV3') {
            $query->where('a.WarehouseCode', '05-06-01');
        } elseif ($category === 'INV6') {
            $query->where('b.ClassID', 'INV6');
        } 

        return $query;
    }

    public static function store_item($data)
    {
        date_default_timezone_set('Asia/Jakarta');
        $DateTime = date('Y-m-d H:i:s');
        $Username = Auth::user()->username ?? Auth::user()->name;

        $DocNum = $data['InptDocNum'] ?? '';
        $parts = explode('~', $DocNum);
        if (count($parts) < 3) {
            return ['code' => 400, 'status' => 'Invalid DocNum format'];
        }

        $TranTypeID = $parts[0];
        $MonthID = $parts[1];
        $TranSeqID = $parts[2];

        $PartNum = $data['InptPartNum'] ?? '';
        $PartName = $data['InptPartName'] ?? '';
        $Qty = $data['InptQty'] ?? 0;
        $Category = $data['Category'] ?? $data['CategoryFromTag'] ?? '';
        $TransactionType = strtoupper($data['TransactionType'] ?? '');
        $ReasonCode = $data['ReasonCode'] ?? '';
        $FromBinID = $data['ToBinID'] ?? 'GENERAL';
        $LotNum = $data['LotNum'] ?? 'A';
        $DocDate = $data['InptDocDate'] ?? date('Y-m-d');
        $Reference = $data['InptReference'] ?? '';
        $ApprovedBy = trim((string) ($data['ApprovedBy'] ?? ''));

        if (!in_array($TransactionType, ['ST', 'TR'], true)) {
            return ['code' => 400, 'status' => 'Transaction Type is required'];
        }

        if (empty($ReasonCode)) {
            return ['code' => 400, 'status' => 'Reason Code is required'];
        }

        if (empty($Category)) {
            return ['code' => 400, 'status' => 'Category is required'];
        }

        if ($Category === 'INV3' && $ApprovedBy === '') {
            return ['code' => 400, 'status' => 'Approved By is required'];
        }

        if (empty($PartNum)) {
            return ['code' => 400, 'status' => 'Part Num is required'];
        }

        if (!is_numeric($Qty) || $Qty <= 0) {
            return ['code' => 400, 'status' => 'Qty must be greater than 0'];
        }

        if (empty($PartName)) {
            $partInfo = DB::connection('sqlsrv4')
                ->table('Erp.Part')
                ->where('PartNum', $PartNum)
                ->select('PartDescription')
                ->first();
            $PartName = $partInfo ? $partInfo->PartDescription : '';
        }

        $FromWarehouseDesc = $Category == 'INV3' ? 'Store Room (SR)' : ($Category == 'INV6' ? 'General Affairs (GA)' : '');

        try {
            DB::transaction(function () use (
                $TranTypeID,
                $MonthID,
                $TranSeqID,
                $Category,
                $TransactionType,
                $ReasonCode,
                $DocDate,
                $DateTime,
                $Username,
                $PartNum,
                $PartName,
                $Qty,
                $FromWarehouseDesc,
                $FromBinID,
                $LotNum,
                $Reference,
                $ApprovedBy
            ) {
                $header = DB::table('IssueMiscellaneous')
                    ->where('TranTypeID', $TranTypeID)
                    ->where('MonthID', $MonthID)
                    ->where('TranSeqID', $TranSeqID)
                    ->where('IsDelete', 0)
                    ->first();

                $deletedHeader = DB::table('IssueMiscellaneous')
                    ->where('TranTypeID', $TranTypeID)
                    ->where('MonthID', $MonthID)
                    ->where('TranSeqID', $TranSeqID)
                    ->where('IsDelete', 1)
                    ->exists();

                if ($header && in_array($header->RequestStatus, ['PENDING_APPROVAL', 'APPROVED', 'AUTO_APPROVED', 'COMPLETED', 'CANCELLED'], true)) {
                    throw new \RuntimeException('Document cannot be changed in its current status');
                }

                if (!$header && $deletedHeader) {
                    throw new \RuntimeException('Document number already belongs to a deleted document. Please refresh and generate a new document number');
                }

                if (!$header) {
                    DB::table('IssueMiscellaneous')->insert([
                        'TranTypeID' => $TranTypeID,
                        'Category' => $Category,
                        'TransactionType' => $TransactionType,
                        'ReasonCode' => $ReasonCode,
                        'MonthID' => $MonthID,
                        'TranSeqID' => $TranSeqID,
                        'DocDate' => $DocDate,
                        'CreatedBy' => $Username,
                        'CreatedAt' => $DateTime,
                        'ApprovedBy' => $ApprovedBy !== '' ? $ApprovedBy : null,
                        'UpdatedBy' => $Username,
                        'LastUpdated' => $DateTime,
                        'Approved' => 0,
                        'Submitted' => 0,
                        'IsDelete' => 0,
                        'RequestStatus' => 'DRAFT',
                    ]);
                } else {
                    DB::table('IssueMiscellaneous')
                        ->where('TranTypeID', $TranTypeID)
                        ->where('MonthID', $MonthID)
                        ->where('TranSeqID', $TranSeqID)
                        ->update([
                            'Category' => $Category,
                            'TransactionType' => $TransactionType,
                            'ReasonCode' => $ReasonCode,
                            'DocDate' => $DocDate,
                            'ApprovedBy' => $ApprovedBy !== '' ? $ApprovedBy : null,
                            'UpdatedBy' => $Username,
                            'LastUpdated' => $DateTime,
                            'RequestStatus' => 'DRAFT',
                            'RejectedBy' => null,
                            'RejectedAt' => null,
                            'RejectedReason' => null,
                        ]);
                }

                $lastLine = DB::table('IssueMiscellaneousDetail')
                    ->where('TranTypeID', $TranTypeID)
                    ->where('MonthID', $MonthID)
                    ->where('TranSeqID', $TranSeqID)
                    ->max('LineID');

                $LineID = $lastLine ? ($lastLine + 1) : 1;

                DB::table('IssueMiscellaneousDetail')->insert([
                    'TranTypeID' => $TranTypeID,
                    'MonthID' => $MonthID,
                    'TranSeqID' => $TranSeqID,
                    'LineID' => $LineID,
                    'PartNum' => $PartNum,
                    'PartName' => $PartName,
                    'QtyMove' => $Qty,
                    'QtyBalance' => 0,
                    'QtySubmit' => 0,
                    'FromWarehouseID' => $Category,
                    'FromWarehouseDesc' => $FromWarehouseDesc,
                    'FromBinID' => $FromBinID,
                    'LotNum' => $LotNum,
                    'Reference' => $Reference,
                    'IsDelete' => 0,
                    'LogDate' => $DateTime,
                ]);
            });

            return ['code' => 200, 'status' => 'Success'];
        } catch (\Exception $e) {
            return ['code' => 500, 'status' => $e->getMessage()];
        }
    }

    public static function get_detail_list($DocNum, $search = null)
    {
        $parts = explode('~', $DocNum);
        if (count($parts) < 3) {
            return DB::table('IssueMiscellaneousDetail')->whereRaw('1 = 0'); // Return empty query
        }
        $TranTypeID = $parts[0];
        $MonthID = $parts[1];
        $TranSeqID = $parts[2];

        $query = DB::table('IssueMiscellaneousDetail')
            ->where('TranTypeID', $TranTypeID)
            ->where('MonthID', $MonthID)
            ->where('TranSeqID', $TranSeqID)
            ->where('IsDelete', 0);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('PartNum', 'like', '%' . $search . '%')
                    ->orWhere('PartName', 'like', '%' . $search . '%');
            });
        }

        return $query;
    }

    public static function delete_item($id)
    {
        $parts = explode('~', $id);
        if(count($parts) == 4){
             try {
                $header = DB::table('IssueMiscellaneous')
                    ->where('TranTypeID', $parts[0])
                    ->where('MonthID', $parts[1])
                    ->where('TranSeqID', $parts[2])
                    ->where('IsDelete', 0)
                    ->first();

                $canEditApproved = in_array($header->RequestStatus, ['APPROVED', 'AUTO_APPROVED'], true) && self::isAuthorizedQtySubmitter();

                if (!$header || (!in_array($header->RequestStatus, ['DRAFT', 'REJECTED'], true) && !$canEditApproved)) {
                    return ['code' => 400, 'status' => 'Document cannot be changed in its current status'];
                }

                DB::table('IssueMiscellaneousDetail')
                    ->where('TranTypeID', $parts[0])
                    ->where('MonthID', $parts[1])
                    ->where('TranSeqID', $parts[2])
                    ->where('LineID', $parts[3])
                    ->update(['IsDelete' => 1]);
                return ['code' => 200, 'status' => 'Success'];
            } catch (\Exception $e) {
                return ['code' => 500, 'status' => $e->getMessage()];
            }
        }
        
        return ['code' => 400, 'status' => 'Invalid ID format'];
    }

    public static function submit_document($DocNum)
    {
        if (empty($DocNum)) {
            return ['status' => 'error', 'message' => 'Invalid DocNum'];
        }

        $parts = explode('~', $DocNum);
        if (count($parts) !== 3) {
            return ['status' => 'error', 'message' => 'Invalid DocNum format'];
        }

        $TranTypeID = $parts[0];
        $MonthID = $parts[1];
        $TranSeqID = $parts[2];

        try {
            $username = Auth::user()->username ?? Auth::user()->name;
            $now = now();

            $header = DB::table('IssueMiscellaneous')
                ->where('TranTypeID', $TranTypeID)
                ->where('MonthID', $MonthID)
                ->where('TranSeqID', $TranSeqID)
                ->where('IsDelete', 0)
                ->first();

            if (!$header) {
                return ['status' => 'error', 'message' => 'Document not found'];
            }

            if (!in_array($header->RequestStatus, ['DRAFT', 'REJECTED'], true)) {
                return ['status' => 'error', 'message' => 'Document cannot be submitted in its current status'];
            }

            $activeLines = DB::table('IssueMiscellaneousDetail')
                ->where('TranTypeID', $TranTypeID)
                ->where('MonthID', $MonthID)
                ->where('TranSeqID', $TranSeqID)
                ->where('IsDelete', 0)
                ->get();

            if ($activeLines->isEmpty()) {
                return ['status' => 'error', 'message' => 'No items to submit'];
            }

            DB::transaction(function () use ($TranTypeID, $MonthID, $TranSeqID, $header, $username, $now) {
                $isAutoApproved = $header->Category === 'INV6';

                DB::table('IssueMiscellaneous')
                    ->where('TranTypeID', $TranTypeID)
                    ->where('MonthID', $MonthID)
                    ->where('TranSeqID', $TranSeqID)
                    ->update([
                        'Submitted' => 1,
                        'SubmittedBy' => $username,
                        'SubmittedAt' => $now,
                        'Approved' => $isAutoApproved ? 1 : 0,
                        'ApprovedBy' => $isAutoApproved ? 'System' : ($header->ApprovedBy ?? null),
                        'ApprovedAt' => $isAutoApproved ? $now : null,
                        'RequestStatus' => $isAutoApproved ? 'AUTO_APPROVED' : 'PENDING_APPROVAL',
                        'RejectedBy' => null,
                        'RejectedAt' => null,
                        'RejectedReason' => null,
                        'LastUpdated' => $now,
                        'UpdatedBy' => $username,
                    ]);
            });

            return [
                'status' => 'success',
                'message' => $header->Category === 'INV6'
                    ? 'Document submitted and approved automatically. Qty Submit can be filled by authorized submitter.'
                    : 'Document submitted and pending approval'
            ];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public static function update_qty_submit($DocNum, $submitData)
    {
        if (empty($DocNum)) {
            return ['status' => 'error', 'message' => 'Invalid DocNum'];
        }

        $parts = explode('~', $DocNum);
        if (count($parts) !== 3) {
            return ['status' => 'error', 'message' => 'Invalid DocNum format'];
        }

        if (!self::isAuthorizedQtySubmitter()) {
            return ['status' => 'error', 'message' => 'Only authorized submitter can update Qty Submit'];
        }

        $TranTypeID = $parts[0];
        $MonthID = $parts[1];
        $TranSeqID = $parts[2];

        try {
            $header = DB::table('IssueMiscellaneous')
                ->where('TranTypeID', $TranTypeID)
                ->where('MonthID', $MonthID)
                ->where('TranSeqID', $TranSeqID)
                ->where('IsDelete', 0)
                ->first();

            if (!$header) {
                return ['status' => 'error', 'message' => 'Document not found'];
            }

            if (!in_array($header->RequestStatus, ['APPROVED', 'AUTO_APPROVED'], true)) {
                return ['status' => 'error', 'message' => 'Qty Submit can only be updated after approval'];
            }

            if (empty($submitData) || !is_array($submitData)) {
                return ['status' => 'error', 'message' => 'Submit quantity data is empty'];
            }

            $activeLines = DB::table('IssueMiscellaneousDetail')
                ->where('TranTypeID', $TranTypeID)
                ->where('MonthID', $MonthID)
                ->where('TranSeqID', $TranSeqID)
                ->where('IsDelete', 0)
                ->get();

            if ($activeLines->isEmpty()) {
                return ['status' => 'error', 'message' => 'No items found'];
            }

            $lineMap = $activeLines->keyBy(function ($line) {
                return $line->TranTypeID . '~' . $line->MonthID . '~' . $line->TranSeqID . '~' . $line->LineID;
            });

            $validUpdateCount = 0;
            $username = Auth::user()->username ?? Auth::user()->name;
            $now = now();

            DB::transaction(function () use ($submitData, $lineMap, $TranTypeID, $MonthID, $TranSeqID, &$validUpdateCount, $username, $now) {
                foreach ($submitData as $item) {
                    $lineKey = $item['lineKey'] ?? '';
                    $rawQtySubmit = $item['qtySubmit'] ?? null;

                    if (!isset($lineMap[$lineKey])) {
                        throw new \RuntimeException('Invalid line submitted');
                    }

                    if ($rawQtySubmit === null || $rawQtySubmit === '') {
                        continue;
                    }

                    $qtySubmit = (float) $rawQtySubmit;
                    $line = $lineMap[$lineKey];
                    $qtyMove = (float) $line->QtyMove;

                    if ($qtySubmit <= 0) {
                        throw new \RuntimeException('Qty Submit must be greater than 0');
                    }

                    if ($qtySubmit > $qtyMove) {
                        throw new \RuntimeException('Qty Submit cannot exceed Qty Request');
                    }

                    DB::table('IssueMiscellaneousDetail')
                        ->where('TranTypeID', $line->TranTypeID)
                        ->where('MonthID', $line->MonthID)
                        ->where('TranSeqID', $line->TranSeqID)
                        ->where('LineID', $line->LineID)
                        ->update([
                            'QtySubmit' => $qtySubmit,
                        ]);

                    $validUpdateCount++;
                }

                DB::table('IssueMiscellaneous')
                    ->where('TranTypeID', $TranTypeID)
                    ->where('MonthID', $MonthID)
                    ->where('TranSeqID', $TranSeqID)
                    ->update([
                        'UpdatedBy' => $username,
                        'LastUpdated' => $now,
                    ]);
            });

            if ($validUpdateCount === 0) {
                return ['status' => 'error', 'message' => 'No valid Qty Submit values to update'];
            }

            $remaining = DB::table('IssueMiscellaneousDetail')
                ->where('TranTypeID', $TranTypeID)
                ->where('MonthID', $MonthID)
                ->where('TranSeqID', $TranSeqID)
                ->where('IsDelete', 0)
                ->where(function ($query) {
                    $query->whereNull('QtySubmit')->orWhere('QtySubmit', '<=', 0);
                })
                ->count();

            if ($remaining === 0) {
                DB::table('IssueMiscellaneous')
                    ->where('TranTypeID', $TranTypeID)
                    ->where('MonthID', $MonthID)
                    ->where('TranSeqID', $TranSeqID)
                    ->update([
                        'RequestStatus' => 'COMPLETED',
                        'UpdatedBy' => $username,
                        'LastUpdated' => now(),
                    ]);

                return ['status' => 'success', 'message' => 'Qty Submit updated. Document status changed to COMPLETED'];
            }

            return ['status' => 'success', 'message' => 'Qty Submit updated successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public static function update_header_submitter($data)
    {
        if (!self::isAuthorizedQtySubmitter()) {
            return ['status' => 'error', 'message' => 'Only authorized submitter can update approved document'];
        }

        $docNum = $data['InptDocNum'] ?? '';
        $parts = explode('~', $docNum);
        if (count($parts) !== 3) {
            return ['status' => 'error', 'message' => 'Invalid DocNum format'];
        }

        $transactionType = strtoupper($data['TransactionType'] ?? '');
        $reasonCode = $data['ReasonCode'] ?? '';
        $category = $data['Category'] ?? $data['CategoryFromTag'] ?? '';
        $approvedBy = trim((string) ($data['ApprovedBy'] ?? ''));
        $docDate = $data['InptDocDate'] ?? null;
        $toBinId = $data['ToBinID'] ?? 'GENERAL';

        if (!in_array($transactionType, ['ST', 'TR'], true)) {
            return ['status' => 'error', 'message' => 'Transaction Type is required'];
        }

        if (empty($reasonCode)) {
            return ['status' => 'error', 'message' => 'Reason Code is required'];
        }

        if (empty($category)) {
            return ['status' => 'error', 'message' => 'Category is required'];
        }

        if ($category === 'INV3' && $approvedBy === '') {
            return ['status' => 'error', 'message' => 'Approved By is required'];
        }

        try {
            $header = DB::table('IssueMiscellaneous')
                ->where('TranTypeID', $parts[0])
                ->where('MonthID', $parts[1])
                ->where('TranSeqID', $parts[2])
                ->where('IsDelete', 0)
                ->first();

            if (!$header) {
                return ['status' => 'error', 'message' => 'Document not found'];
            }

            if (!in_array($header->RequestStatus, ['APPROVED', 'AUTO_APPROVED'], true)) {
                return ['status' => 'error', 'message' => 'Header can only be updated after approval'];
            }

            $username = Auth::user()->username ?? Auth::user()->name;
            $now = now();
            $fromWarehouseDesc = $category === 'INV3' ? 'Store Room (SR)' : ($category === 'INV6' ? 'General Affairs (GA)' : '');

            DB::transaction(function () use ($parts, $transactionType, $reasonCode, $category, $approvedBy, $docDate, $toBinId, $username, $now, $fromWarehouseDesc) {
                DB::table('IssueMiscellaneous')
                    ->where('TranTypeID', $parts[0])
                    ->where('MonthID', $parts[1])
                    ->where('TranSeqID', $parts[2])
                    ->update([
                        'TransactionType' => $transactionType,
                        'ReasonCode' => $reasonCode,
                        'Category' => $category,
                        'ApprovedBy' => $approvedBy !== '' ? $approvedBy : null,
                        'DocDate' => $docDate,
                        'UpdatedBy' => $username,
                        'LastUpdated' => $now,
                    ]);

                DB::table('IssueMiscellaneousDetail')
                    ->where('TranTypeID', $parts[0])
                    ->where('MonthID', $parts[1])
                    ->where('TranSeqID', $parts[2])
                    ->where('IsDelete', 0)
                    ->update([
                        'FromWarehouseID' => $category,
                        'FromWarehouseDesc' => $fromWarehouseDesc,
                        'FromBinID' => $toBinId,
                    ]);
            });

            return ['status' => 'success', 'message' => 'Header updated successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private static function applyApprovalUserFilter($query, array $approvedByCandidates)
    {
        if (empty($approvedByCandidates)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function ($filter) use ($approvedByCandidates) {
            $filter->whereIn('a.ApprovedBy', $approvedByCandidates);

            foreach ($approvedByCandidates as $candidate) {
                $filter->orWhereRaw('LTRIM(RTRIM(a.ApprovedBy)) = ?', [$candidate]);
            }
        });
    }

    public static function get_approval_list_count(array $approvedByCandidates)
    {
        return self::applyApprovalUserFilter(DB::table('IssueMiscellaneous as a'), $approvedByCandidates)
            ->where('a.Category', 'INV3')
            ->where('a.IsDelete', 0)
            ->where('a.RequestStatus', 'PENDING_APPROVAL')
            ->count();
    }

    public static function get_approval_list(array $approvedByCandidates, $start, $limit, $order, $dir)
    {
        $query = DB::table('IssueMiscellaneous as a')
            ->join('IssueMiscellaneousDetail as b', function ($join) {
                $join->on('a.TranTypeID', '=', 'b.TranTypeID')
                    ->on('a.MonthID', '=', 'b.MonthID')
                    ->on('a.TranSeqID', '=', 'b.TranSeqID');
            })
            ->selectRaw("
                CONCAT(a.TranTypeID, '~', a.MonthID, '~', a.TranSeqID) AS DocNum,
                a.DocDate,
                a.Category,
                a.SubmittedBy,
                a.SubmittedAt,
                COUNT(b.TranTypeID) AS TotalLine
            ")
            ->where('a.Category', 'INV3')
            ->where('a.IsDelete', 0)
            ->where('a.RequestStatus', 'PENDING_APPROVAL')
            ->where('b.IsDelete', 0);

        return self::applyApprovalUserFilter($query, $approvedByCandidates)
            ->groupBy('a.TranTypeID', 'a.MonthID', 'a.TranSeqID', 'a.DocDate', 'a.Category', 'a.SubmittedBy', 'a.SubmittedAt')
            ->orderBy($order, $dir)
            ->offset($start)
            ->limit($limit)
            ->get();
    }

    public static function get_approval_list_search(array $approvedByCandidates, $search, $start, $limit, $order, $dir)
    {
        $query = DB::table('IssueMiscellaneous as a')
            ->join('IssueMiscellaneousDetail as b', function ($join) {
                $join->on('a.TranTypeID', '=', 'b.TranTypeID')
                    ->on('a.MonthID', '=', 'b.MonthID')
                    ->on('a.TranSeqID', '=', 'b.TranSeqID');
            })
            ->selectRaw("
                CONCAT(a.TranTypeID, '~', a.MonthID, '~', a.TranSeqID) AS DocNum,
                a.DocDate,
                a.Category,
                a.SubmittedBy,
                a.SubmittedAt,
                COUNT(b.TranTypeID) AS TotalLine
            ")
            ->where('a.Category', 'INV3')
            ->where('a.IsDelete', 0)
            ->where('a.RequestStatus', 'PENDING_APPROVAL')
            ->where('b.IsDelete', 0)
            ->where(function ($query) use ($search) {
                $query->whereRaw("CONCAT(a.TranTypeID, '~', a.MonthID, '~', a.TranSeqID) LIKE ?", ["%{$search}%"])
                    ->orWhere('a.SubmittedBy', 'LIKE', "%{$search}%");
            });

        return self::applyApprovalUserFilter($query, $approvedByCandidates)
            ->groupBy('a.TranTypeID', 'a.MonthID', 'a.TranSeqID', 'a.DocDate', 'a.Category', 'a.SubmittedBy', 'a.SubmittedAt')
            ->orderBy($order, $dir)
            ->offset($start)
            ->limit($limit)
            ->get();
    }

    public static function get_approval_list_search_count(array $approvedByCandidates, $search)
    {
        $query = DB::table('IssueMiscellaneous as a')
            ->join('IssueMiscellaneousDetail as b', function ($join) {
                $join->on('a.TranTypeID', '=', 'b.TranTypeID')
                    ->on('a.MonthID', '=', 'b.MonthID')
                    ->on('a.TranSeqID', '=', 'b.TranSeqID');
            })
            ->where('a.Category', 'INV3')
            ->where('a.IsDelete', 0)
            ->where('a.RequestStatus', 'PENDING_APPROVAL')
            ->where('b.IsDelete', 0)
            ->where(function ($query) use ($search) {
                $query->whereRaw("CONCAT(a.TranTypeID, '~', a.MonthID, '~', a.TranSeqID) LIKE ?", ["%{$search}%"])
                    ->orWhere('a.SubmittedBy', 'LIKE', "%{$search}%");
            });

        return self::applyApprovalUserFilter($query, $approvedByCandidates)
            ->select(DB::raw("CONCAT(a.TranTypeID, '~', a.MonthID, '~', a.TranSeqID) AS DocNum"))
            ->groupBy('a.TranTypeID', 'a.MonthID', 'a.TranSeqID', 'a.DocDate', 'a.Category', 'a.SubmittedBy', 'a.SubmittedAt')
            ->get()
            ->count();
    }

    public static function get_approval_detail($DocNum)
    {
        if (empty($DocNum)) {
            return ['status' => 'error', 'message' => 'Invalid DocNum'];
        }

        $parts = explode('~', $DocNum);
        if (count($parts) !== 3) {
            return ['status' => 'error', 'message' => 'Invalid DocNum format'];
        }

        $TranTypeID = $parts[0];
        $MonthID = $parts[1];
        $TranSeqID = $parts[2];

        try {
            $header = DB::table('IssueMiscellaneous')
                ->where('TranTypeID', $TranTypeID)
                ->where('MonthID', $MonthID)
                ->where('TranSeqID', $TranSeqID)
                ->where('IsDelete', 0)
                ->first();

            if (!$header) {
                return ['status' => 'error', 'message' => 'Document not found'];
            }

            $details = DB::table('IssueMiscellaneousDetail')
                ->where('TranTypeID', $TranTypeID)
                ->where('MonthID', $MonthID)
                ->where('TranSeqID', $TranSeqID)
                ->where('IsDelete', 0)
                ->get();

            return [
                'status' => 'success',
                'header' => [
                    'DocNum' => $DocNum,
                    'DocDate' => $header->DocDate,
                    'Category' => $header->Category,
                    'RequestStatus' => $header->RequestStatus,
                    'SubmittedBy' => $header->SubmittedBy,
                    'SubmittedAt' => $header->SubmittedAt,
                    'CreatedBy' => $header->CreatedBy,
                    'RejectedReason' => $header->RejectedReason,
                ],
                'details' => $details
            ];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public static function approve_document($DocNum)
    {
        if (empty($DocNum)) {
            return ['status' => 'error', 'message' => 'Invalid DocNum'];
        }

        $parts = explode('~', $DocNum);
        if (count($parts) !== 3) {
            return ['status' => 'error', 'message' => 'Invalid DocNum format'];
        }

        $TranTypeID = $parts[0];
        $MonthID = $parts[1];
        $TranSeqID = $parts[2];

        try {
            $header = DB::table('IssueMiscellaneous')
                ->where('TranTypeID', $TranTypeID)
                ->where('MonthID', $MonthID)
                ->where('TranSeqID', $TranSeqID)
                ->where('IsDelete', 0)
                ->first();

            if (!$header) {
                return ['status' => 'error', 'message' => 'Document not found'];
            }

            if ($header->IsDelete == 1) {
                return ['status' => 'error', 'message' => 'Document already cancelled'];
            }

            if ($header->Category !== 'INV3') {
                return ['status' => 'error', 'message' => 'Only INV3 documents can be approved'];
            }

            if ($header->RequestStatus !== 'PENDING_APPROVAL') {
                return ['status' => 'error', 'message' => 'Document is not pending approval'];
            }

            DB::table('IssueMiscellaneous')
                ->where('TranTypeID', $TranTypeID)
                ->where('MonthID', $MonthID)
                ->where('TranSeqID', $TranSeqID)
                ->update([
                    'Approved' => 1,
                    'ApprovedBy' => Auth::user()->username ?? Auth::user()->name,
                    'ApprovedAt' => now(),
                    'RequestStatus' => 'APPROVED',
                    'LastUpdated' => now(),
                    'UpdatedBy' => Auth::user()->username ?? Auth::user()->name
                ]);

            return ['status' => 'success', 'message' => 'Document approved successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public static function reject_document($DocNum, $reason = null)
    {
        if (empty($DocNum)) {
            return ['status' => 'error', 'message' => 'Invalid DocNum'];
        }

        $parts = explode('~', $DocNum);
        if (count($parts) !== 3) {
            return ['status' => 'error', 'message' => 'Invalid DocNum format'];
        }

        $TranTypeID = $parts[0];
        $MonthID = $parts[1];
        $TranSeqID = $parts[2];

        try {
            $header = DB::table('IssueMiscellaneous')
                ->where('TranTypeID', $TranTypeID)
                ->where('MonthID', $MonthID)
                ->where('TranSeqID', $TranSeqID)
                ->first();

            if (!$header) {
                return ['status' => 'error', 'message' => 'Document not found'];
            }

            if ($header->IsDelete == 1) {
                return ['status' => 'error', 'message' => 'Document already cancelled'];
            }

            if ($header->Category !== 'INV3') {
                return ['status' => 'error', 'message' => 'Only INV3 documents can be rejected'];
            }

            if ($header->RequestStatus !== 'PENDING_APPROVAL') {
                return ['status' => 'error', 'message' => 'Document is not pending approval'];
            }

            DB::table('IssueMiscellaneous')
                ->where('TranTypeID', $TranTypeID)
                ->where('MonthID', $MonthID)
                ->where('TranSeqID', $TranSeqID)
                ->update([
                    'Submitted' => 0,
                    'Approved' => 0,
                    'SubmittedBy' => null,
                    'SubmittedAt' => null,
                    'ApprovedBy' => null,
                    'ApprovedAt' => null,
                    'RequestStatus' => 'REJECTED',
                    'RejectedBy' => Auth::user()->username ?? Auth::user()->name,
                    'RejectedAt' => now(),
                    'RejectedReason' => $reason,
                    'LastUpdated' => now(),
                    'UpdatedBy' => Auth::user()->username ?? Auth::user()->name
                ]);

            return ['status' => 'success', 'message' => 'Document rejected - returned to submitter for revision'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public static function delete_document($DocNum)
    {
        $parts = explode('~', $DocNum);
        if (count($parts) < 3) {
            return ['code' => 400, 'status' => 'Invalid DocNum format'];
        }

        $TranTypeID = $parts[0];
        $MonthID = $parts[1];
        $TranSeqID = $parts[2];

        try {
            $header = DB::table('IssueMiscellaneous')
                ->where('TranTypeID', $TranTypeID)
                ->where('MonthID', $MonthID)
                ->where('TranSeqID', $TranSeqID)
                ->first();

            if (!$header) {
                return ['code' => 404, 'status' => 'Document not found'];
            }

            if (!in_array($header->RequestStatus, ['DRAFT', 'REJECTED'], true)) {
                return ['code' => 400, 'status' => 'Only draft or rejected documents can be cancelled'];
            }

            $username = Auth::user()->username ?? Auth::user()->name;
            $now = now();

            DB::transaction(function () use ($TranTypeID, $MonthID, $TranSeqID, $username, $now) {
                DB::table('IssueMiscellaneousDetail')
                    ->where('TranTypeID', $TranTypeID)
                    ->where('MonthID', $MonthID)
                    ->where('TranSeqID', $TranSeqID)
                    ->where('IsDelete', 0)
                    ->update([
                        'IsDelete' => 1,
                    ]);

                DB::table('IssueMiscellaneous')
                    ->where('TranTypeID', $TranTypeID)
                    ->where('MonthID', $MonthID)
                    ->where('TranSeqID', $TranSeqID)
                    ->update([
                        'IsDelete' => 1,
                        'RequestStatus' => 'CANCELLED',
                        'UpdatedBy' => $username,
                        'LastUpdated' => $now,
                    ]);
            });

            return ['code' => 200, 'status' => 'Document cancelled successfully'];
        } catch (\Exception $e) {
            return ['code' => 500, 'status' => $e->getMessage()];
        }
    }

    private static function isAuthorizedQtySubmitter()
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

        foreach (self::AUTHORIZED_SUBMITTER_CODES as $authorizedCode) {
            if (in_array((string) $authorizedCode, $candidates, true)) {
                return true;
            }
        }

        return false;
    }
}
