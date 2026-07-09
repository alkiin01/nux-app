<?php

namespace App\Http\Controllers;

use App\Models\AppModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class SjApprovalController extends Controller
{
    public function index()
    {
        $myId = Auth::user()->id;
        $uri = explode('/', url()->current());
        $segmentNum = env('SEGMENT_NUM');
        $menu = (count($uri) <= $segmentNum)
            ? $this->menu($myId, 'home')
            : $this->menu($myId, $uri[$segmentNum]);

        $data['head_title'] = $menu['head_title'];
        $data['menu_level_1'] = $menu['menu_level_1'];
        $data['menu_level_2'] = $menu['menu_level_2'];
        $data['menu_level_3'] = $menu['menu_level_3'];
        $data['menu_level_4'] = $menu['menu_level_4'];
        $data['my_name'] = Auth::user()->full_name;

        return view('sj_approval.sj_approval_index', $data);
    }

    public function get_count_document(Request $request)
    {
        $myId = Auth::user()->id;

        $data['total_check'] = DB::table('t100_sj_general as h')
            ->where('h.is_deleted', 0)
            ->where('h.checked_by', $myId)
            ->where('h.status_checker', 'PENDING')
            ->count();

        $data['total_approve'] = DB::table('t100_sj_general as h')
            ->where('h.is_deleted', 0)
            ->where('h.approved_by', $myId)
            ->where('h.status_checker', 'APPROVED')
            ->where('h.status_approver', 'PENDING')
            ->count();

        $data['total_all'] = DB::table('t100_sj_general as h')
            ->where('h.is_deleted', 0)
            ->where(function ($q) use ($myId) {
                $q->where('h.created_by', $myId)
                  ->orWhere('h.checked_by', $myId)
                  ->orWhere('h.approved_by', $myId);
            })
            ->count();

        return response()->json($data);
    }

    public function front_table(Request $request)
    {
        $myId = Auth::user()->id;
        $search = $request->front_table_search;
        $statusId = (int) $request->status_id;

        $columns = [
            0 => 'h.sj_number',
            1 => 'h.sj_date',
            2 => 'h.recipient_name',
            3 => 'h.category',
            4 => 'h.status_checker',
            5 => 'h.status_approver',
        ];

        $base = DB::table('t100_sj_general as h')
            ->leftJoin('users as u', 'u.id', '=', 'h.created_by')
            ->leftJoin('users as c', 'c.id', '=', 'h.checked_by')
            ->leftJoin('users as a', 'a.id', '=', 'h.approved_by')
            ->where('h.is_deleted', 0)
            ->select('h.*', 'u.full_name as creator_name', 'c.full_name as checker_name', 'a.full_name as approver_name');

        if ($statusId === 1) {
            $base->where('h.checked_by', $myId)->where('h.status_checker', 'PENDING');
        } elseif ($statusId === 2) {
            $base->where('h.approved_by', $myId)->where('h.status_checker', 'APPROVED')->where('h.status_approver', 'PENDING');
        } elseif ($statusId === 3) {
            $base->where(function ($q) use ($myId) {
                $q->where('h.created_by', $myId)
                  ->orWhere('h.checked_by', $myId)
                  ->orWhere('h.approved_by', $myId);
            });
        } else {
            $base->where(function ($q) use ($myId) {
                $q->where('h.created_by', $myId)
                  ->orWhere('h.checked_by', $myId)
                  ->orWhere('h.approved_by', $myId);
            });
        }

        if (!empty($search)) {
            $base->where(function ($q) use ($search) {
                $q->where('h.sj_number', 'like', '%' . $search . '%')
                  ->orWhere('h.recipient_name', 'like', '%' . $search . '%')
                  ->orWhere('h.category', 'like', '%' . $search . '%');
            });
        }

        $total = (clone $base)->count();
        $filtered = $total;

        $limit = $request->input('length');
        $start = $request->input('start');
        $colIdx = $request->input('order.0.column', 0);
        $order = $columns[$colIdx] ?? 'h.sj_date';
        $dir = $request->input('order.0.dir', 'desc');

        $posts = (clone $base)->offset($start)->limit($limit)->orderBy($order, $dir)->get();

        $data = [];
        $no = $start;

        foreach ($posts as $post) {
            $no++;
            $encId = str_replace('=', '-', Crypt::encryptString($post->id));
            $hasRealSjNumber = !empty($post->sj_number) && !$this->is_temp_sj_number($post->sj_number);
            $canCheck = ($post->checked_by == $myId && $post->status_checker == 'PENDING' && $hasRealSjNumber);
            $canApprove = ($post->approved_by == $myId && $post->status_checker == 'APPROVED' && $post->status_approver == 'PENDING');

            $mode = 'view';
            if ($canCheck) {
                $mode = 'check';
            } elseif ($canApprove) {
                $mode = 'approve';
            }

            $action = '<div class="d-flex gap-1 flex-wrap justify-content-end">';
            $action .= "<button class=\"btn btn-sm btn-light-info\" onclick=\"openPreview('{$encId}', '{$mode}')\" title=\"Preview\"><i class=\"fa fa-eye fs-6\"></i></button>";
            $action .= '<a href="' . url('sj_general/print/' . $encId) . '" target="_blank" class="btn btn-sm btn-light-primary" title="Print"><i class="fa fa-print fs-6"></i></a>';

            $action .= '</div>';

            $data[] = [
                'no' => $no,
                'sj_number' => ($hasRealSjNumber ? $post->sj_number : '<span class="text-muted">DRAFT</span>'),
                'sj_date' => $post->sj_date ? AppModel::local_date_formate($post->sj_date) : '-',
                'recipient_name' => $post->recipient_name ?: '-',
                'category' => $this->category_badge($post->category),
                'status_checker' => $this->status_badge($post->status_checker),
                'status_approver' => $this->status_badge($post->status_approver),
                'action' => $action,
            ];
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => intval($total),
            'recordsFiltered' => intval($filtered),
            'data' => $data,
        ]);
    }

    public function do_check(Request $request)
    {
        $encId = $request->enc_id;
        $id = Crypt::decryptString(str_replace('-', '=', $encId));
        $status = $request->action_status;
        $note = $request->note;
        $now = now('Asia/Jakarta')->format('Y-m-d H:i:s');
        $myId = Auth::user()->id;

        try {
            $sj = DB::table('t100_sj_general')->where('id', $id)->where('checked_by', $myId)->first();
            if (!$sj) {
                return response()->json(['process_status' => 403, 'msg_process' => 'Akses ditolak.']);
            }

            DB::table('t100_sj_general')->where('id', $id)->update([
                'status_checker' => $status,
                'checker_note' => $note,
                'checked_at' => $now,
                'updated_at' => $now,
            ]);

            return response()->json(['process_status' => 200, 'msg_process' => $status === 'APPROVED' ? 'Dokumen berhasil di-check.' : 'Dokumen telah ditolak.']);
        } catch (\Exception $e) {
            return response()->json(['process_status' => 500, 'msg_process' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    public function do_approve(Request $request)
    {
        $encId = $request->enc_id;
        $id = Crypt::decryptString(str_replace('-', '=', $encId));
        $status = $request->action_status;
        $note = $request->note;
        $now = now('Asia/Jakarta')->format('Y-m-d H:i:s');
        $myId = Auth::user()->id;

        try {
            $sj = DB::table('t100_sj_general')->where('id', $id)->where('approved_by', $myId)->first();
            if (!$sj) {
                return response()->json(['process_status' => 403, 'msg_process' => 'Akses ditolak.']);
            }

            DB::table('t100_sj_general')->where('id', $id)->update([
                'status_approver' => $status,
                'approver_note' => $note,
                'approved_at' => $now,
                'updated_at' => $now,
            ]);

            return response()->json(['process_status' => 200, 'msg_process' => $status === 'APPROVED' ? 'Dokumen berhasil disetujui.' : 'Dokumen telah ditolak.']);
        } catch (\Exception $e) {
            return response()->json(['process_status' => 500, 'msg_process' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    private function category_badge($category)
    {
        $colors = [
            'A' => 'danger',
            'B' => 'success',
            'C' => 'warning',
            'D' => 'info',
        ];

        $c = $colors[$category] ?? 'secondary';
        return '<span class="badge badge-light-' . $c . ' fs-8 fw-bold">' . $category . '</span>';
    }

    private function status_badge($status)
    {
        $map = [
            'DRAFT' => 'secondary',
            'PENDING' => 'warning',
            'APPROVED' => 'success',
            'REJECTED' => 'danger',
        ];

        $c = $map[$status] ?? 'secondary';
        return '<span class="badge badge-light-' . $c . ' fs-8 fw-bold">' . ($status ?: 'DRAFT') . '</span>';
    }

    private function is_temp_sj_number($sjNumber)
    {
        return is_string($sjNumber) && str_starts_with($sjNumber, 'TMP/SJG/');
    }
}
