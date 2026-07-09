<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class IssueMaterial extends Model
{
    use HasFactory;

    protected $table = 'issue_material_headers';

    protected $fillable = [
        'doc_num',
        'job_num',
        'job_part_num',
        'doc_date',
        'total_required_qty',
        'total_issued_qty',
        'issue_percent',
        'status',
        'api_sync_status',
        'created_by',
        'updated_by',
    ];

    public static function epicorJobs($search = null, $category = null, $startDate = null, $endDate = null, $shift = null)
    {
        $query = DB::connection('sqlsrv4')
            ->table('Erp.JobHead as j')
            ->leftJoin('Erp.JobMtl as m', 'j.JobNum', '=', 'm.JobNum')
            ->select(
                'j.JobNum',
                'j.PartNum',
                DB::raw('CONVERT(date, j.ReqDueDate) as JobDate'),
                DB::raw('SUM(ISNULL(m.RequiredQty, 0)) as TotalRequiredQty')
            )
            ->where('j.JobNum', '!=', '')
            ->where('j.JobReleased', '=', 1)
            ->groupBy('j.JobNum', 'j.PartNum', DB::raw('CONVERT(date, j.ReqDueDate)'));

        if (!empty($category)) {
            $prefix = self::resolveJobCategoryPrefix($category);
            if (!empty($prefix)) {
                $query->where('j.JobNum', 'like', $prefix . '%');
            }
        }

        if (!empty($startDate)) {
            $query->whereDate('j.ReqDueDate', '>=', $startDate);
        }

        if (!empty($endDate)) {
            $query->whereDate('j.ReqDueDate', '<=', $endDate);
        }

        self::applyShiftFilter($query, $shift);

        if (!empty($search)) {
            $query->where(function ($sub) use ($search) {
                $sub->where('j.JobNum', 'like', "%{$search}%")
                    ->orWhere('j.PartNum', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    public static function epicorJobCategoryCounts($startDate = null, $endDate = null, $category = null, $shift = null)
    {
        $query = DB::connection('sqlsrv4')
            ->table('Erp.JobHead as j')
            ->where('j.JobNum', '!=', '')
            ->where('j.JobReleased', '=', 1);

        if (!empty($category)) {
            $prefix = self::resolveJobCategoryPrefix($category);
            if (!empty($prefix)) {
                $query->where('j.JobNum', 'like', $prefix . '%');
            }
        }

        if (!empty($startDate)) {
            $query->whereDate('j.ReqDueDate', '>=', $startDate);
        }

        if (!empty($endDate)) {
            $query->whereDate('j.ReqDueDate', '<=', $endDate);
        }

        self::applyShiftFilter($query, $shift);

        $rows = $query
            ->selectRaw("CASE
                WHEN j.JobNum LIKE 'ASY%' THEN 'assembly'
                WHEN j.JobNum LIKE 'STP%' THEN 'stamping'
                WHEN j.JobNum LIKE 'RPC%' THEN 'repacking'
                WHEN j.JobNum LIKE 'SBC%' THEN 'subcon'
                ELSE 'other'
            END as Category")
            ->selectRaw('COUNT(1) as Total')
            ->groupByRaw("CASE
                WHEN j.JobNum LIKE 'ASY%' THEN 'assembly'
                WHEN j.JobNum LIKE 'STP%' THEN 'stamping'
                WHEN j.JobNum LIKE 'RPC%' THEN 'repacking'
                WHEN j.JobNum LIKE 'SBC%' THEN 'subcon'
                ELSE 'other'
            END")
            ->get();

        $counts = [
            'assembly' => 0,
            'stamping' => 0,
            'repacking' => 0,
            'subcon' => 0,
        ];

        foreach ($rows as $row) {
            if (array_key_exists($row->Category, $counts)) {
                $counts[$row->Category] = (int) $row->Total;
            }
        }

        return $counts;
    }

    public static function resolveJobCategoryPrefix($category)
    {
        $map = [
            'assembly' => 'ASY',
            'stamping' => 'STP',
            'repacking' => 'RPC',
            'subcon' => 'SBC',
        ];

        $key = strtolower((string) $category);

        return $map[$key] ?? null;
    }

    private static function applyShiftFilter($query, $shift)
    {
        if (empty($shift)) {
            return;
        }

        $normalizedShift = strtoupper(trim((string) $shift));

        if ($normalizedShift === 'SHIFT_1' || $normalizedShift === 'SHIFT 1') {
            $query->where('j.JobCode', '=', 'SHIFT 1');

            return;
        }

        if ($normalizedShift === 'SHIFT_2' || $normalizedShift === 'SHIFT 2') {
            $query->where('j.JobCode', '=', 'SHIFT 2');
        }
    }

    public static function epicorJobMaterials($jobNum, $search = null)
    {
        $query = DB::connection('sqlsrv4')
            ->table('Erp.JobMtl as jm')
            ->leftJoin('Erp.Part as p', 'jm.PartNum', '=', 'p.PartNum')
            ->select(
                'jm.JobNum',
                'jm.MtlSeq',
                'jm.PartNum',
                DB::raw('ISNULL(p.PartDescription, jm.PartNum) as PartName'),
                    DB::raw("ISNULL(p.IUM, '') as UOM"),
                DB::raw('ISNULL(jm.RequiredQty, 0) as RequiredQty')
            )
            ->where('jm.JobNum', $jobNum)
            ->orderBy('jm.MtlSeq', 'asc');

        if (!empty($search)) {
            $query->where(function ($sub) use ($search) {
                $sub->where('jm.PartNum', 'like', "%{$search}%")
                    ->orWhere('p.PartDescription', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    public static function localIssuedByJob($jobNum)
    {
        return (float) DB::table('issue_material_headers as h')
            ->join('issue_material_details as d', 'h.id', '=', 'd.header_id')
            ->where('h.job_num', $jobNum)
            ->sum('d.qty_issue');
    }

    public static function epicorIssuedByJob($jobNum)
    {
        try {
            $row = DB::connection('sqlsrv4')
                ->table('Erp.JobMtl as jm')
                ->selectRaw('SUM(ISNULL(jm.IssuedQty, 0)) as TotalIssuedQty')
                ->where('jm.JobNum', $jobNum)
                ->first();

            return (float) ($row->TotalIssuedQty ?? 0);
        } catch (\Throwable $th) {
            return 0;
        }
    }

    public static function issuedByJob($jobNum)
    {
        $epicorIssued = self::epicorIssuedByJob($jobNum);
        if ($epicorIssued > 0) {
            return $epicorIssued;
        }

        return self::localIssuedByJob($jobNum);
    }

    /**
     * Batch-load issued quantities for multiple jobs in 2 queries instead of N×2.
     * Returns array keyed by JobNum => issuedQty (float).
     */
    public static function batchIssuedByJobs(array $jobNums): array
    {
        if (empty($jobNums)) {
            return [];
        }

        try {
            $epicorMap = DB::connection('sqlsrv4')
                ->table('Erp.JobMtl')
                ->selectRaw('JobNum, SUM(ISNULL(IssuedQty, 0)) as TotalIssuedQty')
                ->whereIn('JobNum', $jobNums)
                ->groupBy('JobNum')
                ->pluck('TotalIssuedQty', 'JobNum')
                ->toArray();
        } catch (\Throwable $th) {
            $epicorMap = [];
        }

        $needLocal = array_values(array_filter($jobNums, function ($jn) use ($epicorMap) {
            return (float) ($epicorMap[$jn] ?? 0) <= 0;
        }));

        $localMap = [];
        if (!empty($needLocal)) {
            $localMap = DB::table('issue_material_headers as h')
                ->join('issue_material_details as d', 'h.id', '=', 'd.header_id')
                ->whereIn('h.job_num', $needLocal)
                ->select('h.job_num', DB::raw('SUM(d.qty_issue) as TotalIssuedQty'))
                ->groupBy('h.job_num')
                ->pluck('TotalIssuedQty', 'job_num')
                ->toArray();
        }

        $result = [];
        foreach ($jobNums as $jobNum) {
            $epicorIssued = (float) ($epicorMap[$jobNum] ?? 0);
            $result[$jobNum] = $epicorIssued > 0 ? $epicorIssued : (float) ($localMap[$jobNum] ?? 0);
        }

        return $result;
    }

    /**
     * Lightweight COUNT query for frontTable pagination — no JOIN or GROUP BY overhead.
     */
    public static function epicorJobsCount($search = null, $category = null, $startDate = null, $endDate = null, $shift = null): int
    {
        $query = DB::connection('sqlsrv4')
            ->table('Erp.JobHead as j')
            ->where('j.JobNum', '!=', '')
            ->where('j.JobReleased', '=', 1);

        if (!empty($category)) {
            $prefix = self::resolveJobCategoryPrefix($category);
            if (!empty($prefix)) {
                $query->where('j.JobNum', 'like', $prefix . '%');
            }
        }

        if (!empty($startDate)) {
            $query->whereDate('j.ReqDueDate', '>=', $startDate);
        }

        if (!empty($endDate)) {
            $query->whereDate('j.ReqDueDate', '<=', $endDate);
        }

        self::applyShiftFilter($query, $shift);

        if (!empty($search)) {
            $query->where(function ($sub) use ($search) {
                $sub->where('j.JobNum', 'like', "%{$search}%")
                    ->orWhere('j.PartNum', 'like', "%{$search}%");
            });
        }

        return (int) $query->count();
    }

    public static function epicorIssuedMaterials($jobNum)
    {
        return DB::connection('sqlsrv4')
            ->table('Erp.JobMtl as jm')
            ->leftJoin('Erp.Part as p', 'jm.PartNum', '=', 'p.PartNum')
            ->select(
                'jm.MtlSeq',
                'jm.PartNum',
                DB::raw('ISNULL(p.PartDescription, jm.PartNum) as PartName'),
                DB::raw("ISNULL(p.IUM, '') as UOM"),
                DB::raw('ISNULL(jm.RequiredQty, 0) as RequiredQty'),
                DB::raw('ISNULL(jm.IssuedQty, 0) as IssuedQty')
            )
            ->where('jm.JobNum', $jobNum)
            ->whereRaw('ISNULL(jm.IssuedQty, 0) > 0')
            ->orderBy('jm.MtlSeq', 'asc')
            ->get();
    }

    public static function calculateStatus($percent)
    {
        if ($percent <= 0) {
            return 'NOT_ISSUED';
        }

        if ($percent >= 100) {
            return 'FULLY_ISSUED';
        }

        return 'PARTIAL_ISSUED';
    }
}
