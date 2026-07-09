<?php

namespace App\Http\Controllers;

use App\Models\IssueMaterial;
use App\Models\IssueMaterialDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class IssueMaterialController extends Controller
{
    public function index()
    {
        $data = $this->menuData();

        return view('issue_material/index', $data);
    }

    public function form(Request $request)
    {
        $jobNum = $request->job_num;

        if (empty($jobNum)) {
            return redirect()->route('inventory_rm_out.index');
        }

        $data = $this->buildFormData($jobNum);

        return view('issue_material/form', $data);
    }

    public function form_load(Request $request)
    {
        $jobNum = $request->job_num;

        if (empty($jobNum)) {
            return response('job_num is required', 422);
        }
        $jobNum = Crypt::decryptString($jobNum);
        $data = $this->buildFormData($jobNum);

        return view('issue_material/form', $data);
    }

    private function buildFormData($jobNum)
    {

        $header = IssueMaterial::where('job_num', $jobNum)
            ->orderBy('id', 'desc')
            ->first();

        $epicorJob = IssueMaterial::epicorJobs($jobNum)
            ->where('j.JobNum', $jobNum)
            ->first();

        $requiredQty = (float) ($epicorJob->TotalRequiredQty ?? 0);
        $issuedQty = IssueMaterial::issuedByJob($jobNum);
        $percent = $requiredQty > 0 ? round(($issuedQty / $requiredQty) * 100, 2) : 0;

        if (!$header) {
            $header = IssueMaterial::create([
                'doc_num' => $this->generateDocNum(),
                'job_num' => $jobNum,
                'job_part_num' => $epicorJob->PartNum ?? null,
                'doc_date' => now()->format('Y-m-d'),
                'total_required_qty' => $requiredQty,
                'total_issued_qty' => $issuedQty,
                'issue_percent' => $percent,
                'status' => IssueMaterial::calculateStatus($percent),
                'created_by' => Auth::user()->username ?? Auth::user()->name,
                'updated_by' => Auth::user()->username ?? Auth::user()->name,
            ]);
        } else {
            $header->update([
                'total_required_qty' => $requiredQty,
                'total_issued_qty' => $issuedQty,
                'issue_percent' => $percent,
                'status' => IssueMaterial::calculateStatus($percent),
                'updated_by' => Auth::user()->username ?? Auth::user()->name,
            ]);
        }

        $this->syncDetailsFromEpicor($header, $jobNum);

        $data = $this->menuData();
        $data['head_title'] = 'Issue Material Form';
        $data['header'] = $header;

        return $data;
    }

    public function frontTable(Request $request)
    {
        $search = $request->front_table_search;
        $jobCategory = $request->job_category;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $shift = $request->shift;
        $columns = [
            0 => 'JobNum',
            1 => 'JobNum',
            2 => 'JobNum',
            3 => 'PartNum',
            4 => 'TotalRequiredQty',
        ];

        // Use lightweight COUNT query (no JOIN/GROUP BY) instead of ->get()->count()
        $totalData = IssueMaterial::epicorJobsCount(null, $jobCategory, $startDate, $endDate, $shift);
        $totalFiltered = IssueMaterial::epicorJobsCount($search, $jobCategory, $startDate, $endDate, $shift);

        $limit = (int) $request->input('length', 10);
        $start = (int) $request->input('start', 0);
        $orderCol = (int) $request->input('order.0.column', 0);
        $order = $columns[$orderCol] ?? 'JobNum';
        $dir = $request->input('order.0.dir', 'desc');

        $posts = IssueMaterial::epicorJobs($search, $jobCategory, $startDate, $endDate, $shift)
            ->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        // Batch-load all issued quantities in 2 queries instead of N×2
        $jobNums = $posts->pluck('JobNum')->all();
        $issuedMap = IssueMaterial::batchIssuedByJobs($jobNums);

        $data = [];
        $no = $start;

        foreach ($posts as $post) {
            $no++;
            $requiredQty = (float) ($post->TotalRequiredQty ?? 0);
            $issuedQty = $issuedMap[$post->JobNum] ?? 0.0;
            $percent = $requiredQty > 0 ? round(($issuedQty / $requiredQty) * 100, 2) : 0;
            $status = IssueMaterial::calculateStatus($percent);

            $statusClass = 'badge-light-primary';
            if ($status === 'PARTIAL_ISSUED') {
                $statusClass = 'badge-light-warning';
            } elseif ($status === 'FULLY_ISSUED') {
                $statusClass = 'badge-light-success';
            }
            $encryptedJob = Crypt::encryptString($post->JobNum);
            $button = '<button type="button" class="btn btn-light-primary btn-sm btn-open-issue-material" id="btn_form_view_doc_' . $no . '" data-job-num="' .  e($encryptedJob) . '" data-no="' . $no . '">'
                . '<span id="svg_form_view_doc_' . $no . '">Open</span>'
                . '<span id="spinner_form_view_doc_' . $no . '" class="spinner-border spinner-border-sm" style="display:none;"></span>'
                . '</button>';

            $nestedData['no'] = $no;
            $nestedData['action'] = $button;
            $nestedData['job_num'] = $post->JobNum;
            $nestedData['part_num'] = $post->PartNum;
            $nestedData['required_qty'] = number_format($requiredQty, 2);
            $nestedData['issued_qty'] = number_format($issuedQty, 2);
            $nestedData['progress'] = number_format($percent, 2) . '%';
            $nestedData['status'] = '<span class="badge ' . $statusClass . '">' . $status . '</span>';
            $data[] = $nestedData;
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => intval($totalFiltered),
            'data' => $data,
        ]);
    }

    public function getJobCategoryCounts(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $jobCategory = $request->job_category;
        $shift = $request->shift;

        $counts = IssueMaterial::epicorJobCategoryCounts($startDate, $endDate, $jobCategory, $shift);

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'data' => $counts,
        ]);
    }

    public function materialOptions(Request $request)
    {
        $jobNum = $request->job_num;
        $headerId = $request->header_id;
        $search = $request->search;
        $page = (int) $request->post('page', 1);
        $pageSize = 20;

        if (empty($jobNum)) {
            return response()->json(['items' => [], 'pagination' => ['more' => false]]);
        }

        $query = IssueMaterial::epicorJobMaterials($jobNum, $search);

        if (!empty($headerId)) {
            $existing = IssueMaterialDetail::where('header_id', $headerId)
                ->pluck('mtl_seq')
                ->filter()
                ->toArray();

            if (!empty($existing)) {
                $query->whereNotIn('jm.MtlSeq', $existing);
            }
        }

        $materials = $query->paginate($pageSize, ['*'], 'page', $page);

        $items = [];
        foreach ($materials->items() as $row) {
            $items[] = [
                'id' => $row->MtlSeq,
                'part_num' => $row->PartNum,
                'part_name' => $row->PartName,
                'uom' => $row->UOM,
                'qty_required' => (float) $row->RequiredQty,
                'text' => $row->MtlSeq . ' - ' . $row->PartNum . ' - ' . $row->PartName,
            ];
        }

        return response()->json([
            'items' => $items,
            'pagination' => [
                'more' => $materials->hasMorePages(),
            ],
        ]);
    }

    public function storeItem(Request $request)
    {
        $request->validate([
            'header_id' => 'required|integer',
            'mtl_seq' => 'required|integer',
            'part_num' => 'required|string|max:100',
            'part_name' => 'nullable|string|max:255',
            'uom' => 'nullable|string|max:20',
            'qty_required' => 'required|numeric|min:0',
            'qty_issue' => 'required|numeric|min:0.0001',
            'lot_num' => 'nullable|string|max:100',
            'bin_num'=>'nullable|max:100'
        ]);

        $header = IssueMaterial::findOrFail($request->header_id);
        $username = Auth::user()->username ?? Auth::user()->name;

        DB::transaction(function () use ($request, $header, $username) {
            $detail = IssueMaterialDetail::where('header_id', $header->id)
                ->where('mtl_seq', $request->mtl_seq)
                ->first();

            if ($detail) {
                $detail->update([
                    'qty_issue' => (float) $detail->qty_issue + (float) $request->qty_issue,
                    'lot_num' => $request->lot_num ?? $detail->lot_num,
                    'updated_by' => $username,
                ]);
            } else {
                IssueMaterialDetail::create([
                    'header_id' => $header->id,
                    'mtl_seq' => $request->mtl_seq,
                    'part_num' => $request->part_num,
                    'part_name' => $request->part_name,
                    'uom' => $request->uom,
                    'qty_required' => (float) $request->qty_required,
                    'qty_issue' => (float) $request->qty_issue,
                    'bin_num'=>$request->bin_num,
                    'lot_num' => $request->lot_num ?? null,
                    'created_by' => $username,
                    'updated_by' => $username,
                ]);
            }

            $this->refreshHeaderSummary($header);
        });

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'message' => 'Data berhasil tersimpan',
            // Keep legacy key format used by some existing scripts.
            'Status' => 'Success',
        ]);
    }

    public function detailTable(Request $request)
    {
        $headerId = $request->header_id;
        $search = $request->search;

        $query = IssueMaterialDetail::where('header_id', $headerId);

        if (!empty($search)) {
            $query->where(function ($sub) use ($search) {
                $sub->where('part_num', 'like', '%' . $search . '%')
                    ->orWhere('part_name', 'like', '%' . $search . '%');
            });
        }

        $totalData = IssueMaterialDetail::where('header_id', $headerId)->count();
        $totalFiltered = $query->count();

        $limit = (int) $request->input('length', 10);
        $start = (int) $request->input('start', 0);

        $posts = $query
            ->offset($start)
            ->limit($limit)
            ->orderBy('id', 'asc')
            ->get();

        $data = [];
        $no = $start;

        foreach ($posts as $post) {
            $no++;
            $button = '<button type="button" class="btn btn-icon btn-danger btn-sm" onclick="delete_item(' . $post->id . ')">
             <span id="svg_add_item" class="svg-icon svg-icon-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                    </span>
            </button>';
            $data[] = [
                'no' => $no,
                'action' => $button,
                'mtl_seq' => $post->mtl_seq,
                'part_num' => $post->part_num,
                'part_name' => $post->part_name,
                'uom' => $post->uom,
                'lot_num' => $post->lot_num,
                'qty_required' => number_format((float) $post->qty_required, 4),
                'qty_issue' => number_format((float) $post->qty_issue, 4),
                'bin_num'=>$post->bin_num
            ];
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => intval($totalFiltered),
            'data' => $data,
        ]);
    }

    public function deleteItem(Request $request)
    {
        $request->validate([
            'detail_id' => 'required|integer',
        ]);

        $detail = IssueMaterialDetail::findOrFail($request->detail_id);
        $header = IssueMaterial::findOrFail($detail->header_id);

        $detail->delete();
        $this->refreshHeaderSummary($header);

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'message' => 'Data berhasil dihapus',
            // Keep legacy key format used by some existing scripts.
            'Status' => 'Deleted',
        ]);
    }

    public function syncInternalApi(Request $request)
    {
        $request->validate([
            'header_id' => 'required|integer',
        ]);

        $header = IssueMaterial::findOrFail($request->header_id);

        // Placeholder for internal API integration.
        // User will complete API endpoint, payload, and auth handling.
        $header->update([
            'api_sync_status' => 'PENDING_INTEGRATION',
            'updated_by' => Auth::user()->username ?? Auth::user()->name,
        ]);

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'message' => 'Placeholder executed. Please implement internal API call in syncInternalApi().',
            // Keep legacy key format used by some existing scripts.
            'Status' => 'Placeholder executed. Please implement internal API call in syncInternalApi().',
        ]);
    }

    // Keep snake_case action schema consistent with other controllers.
    public function front_table(Request $request)
    {
        return $this->frontTable($request);
    }

    public function material_options(Request $request)
    {
        return $this->materialOptions($request);
    }

    public function store_item(Request $request)
    {
        return $this->storeItem($request);
    }

    public function detail_table(Request $request)
    {
        return $this->detailTable($request);
    }

    public function delete_item(Request $request)
    {
        return $this->deleteItem($request);
    }

    public function sync_internal_api(Request $request)
    {
        return $this->syncInternalApi($request);
    }

    public function get_job_category_counts(Request $request)
    {
        return $this->getJobCategoryCounts($request);
    }

    private function refreshHeaderSummary(IssueMaterial $header)
    {
        $requiredQty = (float) IssueMaterial::epicorJobMaterials($header->job_num)
            ->sum('RequiredQty');

        $issuedQty = IssueMaterial::issuedByJob($header->job_num);

        $percent = $requiredQty > 0 ? round(($issuedQty / $requiredQty) * 100, 2) : 0;

        $header->update([
            'total_required_qty' => $requiredQty,
            'total_issued_qty' => $issuedQty,
            'issue_percent' => $percent,
            'status' => IssueMaterial::calculateStatus($percent),
            'updated_by' => Auth::user()->username ?? Auth::user()->name,
        ]);
    }

    private function syncDetailsFromEpicor(IssueMaterial $header, $jobNum)
    {
        try {
            $epicorIssued = IssueMaterial::epicorIssuedMaterials($jobNum);
        } catch (\Throwable $th) {
            return;
        }

        if ($epicorIssued->isEmpty()) {
            return;
        }

        $username = Auth::user()->username ?? Auth::user()->name;

        DB::transaction(function () use ($header, $epicorIssued, $username) {
            foreach ($epicorIssued as $row) {
                $detail = IssueMaterialDetail::where('header_id', $header->id)
                    ->where('mtl_seq', (int) $row->MtlSeq)
                    ->first();

                if ($detail) {
                    $detail->update([
                        'part_num' => $row->PartNum,
                        'part_name' => $row->PartName,
                        'uom' => $row->UOM,
                        'qty_required' => (float) $row->RequiredQty,
                        'qty_issue' => (float) $row->IssuedQty,
                        'updated_by' => $username,
                    ]);
                } else {
                    IssueMaterialDetail::create([
                        'header_id' => $header->id,
                        'mtl_seq' => (int) $row->MtlSeq,
                        'part_num' => $row->PartNum,
                        'part_name' => $row->PartName,
                        'uom' => $row->UOM,
                        'qty_required' => (float) $row->RequiredQty,
                        'qty_issue' => (float) $row->IssuedQty,
                        'created_by' => $username,
                        'updated_by' => $username,
                    ]);
                }
            }
        });

        $requiredQty = (float) IssueMaterial::epicorJobMaterials($jobNum)
            ->sum('RequiredQty');
        $issuedQty = IssueMaterial::issuedByJob($jobNum);
        $percent = $requiredQty > 0 ? round(($issuedQty / $requiredQty) * 100, 2) : 0;

        $header->update([
            'total_required_qty' => $requiredQty,
            'total_issued_qty' => $issuedQty,
            'issue_percent' => $percent,
            'status' => IssueMaterial::calculateStatus($percent),
            'updated_by' => $username,
        ]);
    }

    private function generateDocNum()
    {
        $monthKey = now()->format('ym');
        $prefix = 'IM-' . $monthKey . '-';

        $last = IssueMaterial::where('doc_num', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$last) {
            return $prefix . '0001';
        }

        $lastSeq = (int) substr($last->doc_num, -4);
        $nextSeq = str_pad((string) ($lastSeq + 1), 4, '0', STR_PAD_LEFT);

        return $prefix . $nextSeq;
    }
    private function menuData()
    {
        $myId = Auth::user()->id;
        $segmentNumber = env('SEGMENT_NUM');
        $uri = explode('/', url()->current());
        if (count($uri) <= $segmentNumber) {
            $menu = $this->menu($myId, 'home');
        } else {
            $menu = $this->menu($myId, $uri[$segmentNumber]);
        }
        return [
            'head_title' => $menu['head_title'],
            'menu_level_1' => $menu['menu_level_1'],
            'menu_level_2' => $menu['menu_level_2'],
            'menu_level_3' => $menu['menu_level_3'],
            'menu_level_4' => $menu['menu_level_4'],
        ];
    }
    public function check_label(Request $request)
    {
        try {
            $label = explode('~', $request->label);
            // dd($label);
            if (count($label) == 11) {
                $partNum = $label[0];
                // $qtyPerPage = $label[1];
                // $whFG = $label[2];
                $binNum = $label[3];
                $lot = $label[4];
                // $shipNum = $label[5];
                // $unknown = $label[6];
                // $whCode = $label[7];
                // $whDesc = $label[8];
                // $shpHeadId = $label[9];
                // $sheet = $label[10];
            } else if (count($label) == 9) {
                $partNum = $label[0];
                // $qtyPerPage = $label[1];
                // $whCode = $label[2];
                $binNum = $label[3];
                $lot = $label[4];
                // $transCode = $label[5];
                // $page = $label[6];
                // $whDesc = $label[7];
                // $sheet = $label[8];
            }else if(count($label) == 8){
                $partNum = $label[0];
                // $qtyPerPage = $label[1];
                // $whCode = $label[2];
                $binNum = $label[3];
                $lot='';
                // $jobNum = $label[4];
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data Invalid'
                ], 422);
            }
            $data = DB::connection('sqlsrv4')
                ->table('Erp.JobMtl as jm')
                ->leftJoin('Erp.Part as p', 'jm.PartNum', '=', 'p.PartNum')
                ->select(
                    'jm.JobNum',
                    'jm.MtlSeq',
                    'jm.PartNum',
                    'jm.IssuedQty',
                    DB::raw('ISNULL(p.PartDescription, jm.PartNum) as PartName'),
                    DB::raw("ISNULL(p.IUM, '') as UOM"),
                    DB::raw('ISNULL(jm.RequiredQty, 0) as RequiredQty')
                )
                ->where('jm.JobNum', $request->job_num)
                ->where('jm.PartNum', $partNum)
                ->orderBy('jm.MtlSeq', 'asc')
                ->first();
            if(!$data){
                return response()->json([
                    'status'=>false,
                    'message'=>'Material Not Found'
                ],404);
            }
            return response()->json([
                'status' => true,
                'data' => $data,
                'lot_num'=>$lot,
                'bin_num'=>$binNum,
                'message' => 'Show data successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
