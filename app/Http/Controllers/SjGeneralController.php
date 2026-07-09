<?php

namespace App\Http\Controllers;

use App\Models\SjGeneral;
use App\Models\AppModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use PDF;

class SjGeneralController extends Controller
{
    /* =========================================================
     * INDEX - list page
     * ========================================================= */
    public function index()
    {
        $my_id = Auth::user()->id;
        $uri = explode("/", url()->current());
        $SEGMENT_NUM = env('SEGMENT_NUM');
        $menu = (count($uri) <= $SEGMENT_NUM)
            ? $this->menu($my_id, 'home')
            : $this->menu($my_id, $uri[$SEGMENT_NUM]);

        $data['head_title']   = $menu['head_title'];
        $data['menu_level_1'] = $menu['menu_level_1'];
        $data['menu_level_2'] = $menu['menu_level_2'];
        $data['menu_level_3'] = $menu['menu_level_3'];
        $data['menu_level_4'] = $menu['menu_level_4'];

        return view('sj_general.index', $data);
    }

    /* =========================================================
     * FRONT TABLE - AJAX DataTables
     * ========================================================= */
    public function front_table(Request $request)
    {
        $my_id  = Auth::user()->id;
        $search = $request->front_table_search;
        $status = $request->status_filter;

        $columns = [
            0 => 'h.id',
            1 => 'h.sj_number',
            2 => 'h.sj_date',
            3 => 'h.category',
            4 => 'h.recipient_name',
            5 => 'h.status_checker',
            6 => 'h.status_approver',
        ];

        $base     = SjGeneral::get_list($search, $status, $my_id);
        $total    = (clone $base)->count();
        $filtered = $total;

        $limit  = $request->input('length');
        $start  = $request->input('start');
        $colIdx = $request->input('order.0.column', 0);
        $order  = $columns[$colIdx] ?? $columns[1];
        $dir    = $request->input('order.0.dir', 'desc');

        $posts  = (clone $base)->offset($start)->limit($limit)->orderBy($order, $dir)->get();

        if (!empty($search)) {
            $filtered = (clone SjGeneral::get_list($search, $status, $my_id))->count();
        }

        $data = [];
        $no   = $start;

        foreach ($posts as $post) {
            $no++;
            $enc_id  = str_replace('=', '-', Crypt::encryptString($post->id));
            $category_badge = $this->category_badge($post->category);
            $hasRealSjNumber = !empty($post->sj_number) && !$this->is_temp_sj_number($post->sj_number);

            $checker_badge  = $this->status_badge($post->status_checker);
            $approver_badge = $this->status_badge($post->status_approver);

            $action = '<div class="d-flex gap-1">';
            $action .= '<button class="btn btn-sm btn-light-primary" onclick="openDetail(\'' . $enc_id . '\')" title="Detail"><i class="fa fa-eye fs-6"></i></button>';

            // Edit - only if checker PENDING and creator
            if ($post->created_by == $my_id && $post->status_checker == 'DRAFT') {
                $action .= '<button class="btn btn-sm btn-light-warning" onclick="editSj(\'' . $enc_id . '\')" title="Edit"><i class="fa fa-edit fs-6"></i></button>';
                $action .= '<button class="btn btn-sm btn-light-danger" onclick="deleteSj(\'' . $enc_id . '\')" title="Hapus"><i class="fa fa-trash fs-6"></i></button>';
            }

            // Send draft for review
            if ($post->created_by == $my_id && $post->status_checker == 'DRAFT') {
                $action .= '<button class="btn btn-sm btn-light-success" onclick="submitReview(\'' . $enc_id . '\')" title="Kirim ke Pemeriksa"><i class="fa fa-paper-plane fs-6"></i></button>';
            }

            // Check action
            if ($post->checked_by == $my_id && $post->status_checker == 'PENDING' && $hasRealSjNumber) {
                $action .= '<button class="btn btn-sm btn-success" onclick="doCheck(\'' . $enc_id . '\', \'APPROVED\')" title="Setujui Cek"><i class="fa fa-check fs-6"></i></button>';
                $action .= '<button class="btn btn-sm btn-danger" onclick="doCheck(\'' . $enc_id . '\', \'REJECTED\')" title="Tolak Cek"><i class="fa fa-times fs-6"></i></button>';
            }

            // Approve action
            if ($post->approved_by == $my_id && $post->status_checker == 'APPROVED' && $post->status_approver == 'PENDING') {
                $action .= '<button class="btn btn-sm btn-success" onclick="doApprove(\'' . $enc_id . '\', \'APPROVED\')" title="Setujui"><i class="fa fa-thumbs-up fs-6"></i></button>';
                $action .= '<button class="btn btn-sm btn-danger" onclick="doApprove(\'' . $enc_id . '\', \'REJECTED\')" title="Tolak"><i class="fa fa-thumbs-down fs-6"></i></button>';
            }

            // Print
            if ($hasRealSjNumber) {
                $action .= '<a href="' . url('sj_general/print/' . $enc_id) . '" target="_blank" class="btn btn-sm btn-light-info" title="Print PDF"><i class="fa fa-print fs-6"></i></a>';
            }

            $action .= '</div>';

            $data[] = [
                'no'             => $no,
                'sj_number'      => $hasRealSjNumber ? $post->sj_number : '<span class="text-muted">DRAFT</span>',
                'sj_date'        => $post->sj_date ? AppModel::local_date_formate($post->sj_date) : '-',
                'category'       => $category_badge,
                'recipient_name' => $post->recipient_name,
                'creator_name'   => $post->creator_name,
                'status_checker' => $checker_badge,
                'status_approver'=> $approver_badge,
                'action'         => $action,
            ];
        }

        echo json_encode([
            'draw'            => intval($request->input('draw')),
            'recordsTotal'    => intval($total),
            'recordsFiltered' => intval($filtered),
            'data'            => $data,
        ]);
    }

    /* =========================================================
     * CREATE FORM
     * ========================================================= */
    public function create()
    {
        $my_id = Auth::user()->id;
        $defaultDepartmentId = $this->resolveDepartmentId($this->getLoggedInDepartmentId());
        $uri = explode("/", url()->current());
        $SEGMENT_NUM = env('SEGMENT_NUM');
        $menu = (count($uri) <= $SEGMENT_NUM)
            ? $this->menu($my_id, 'home')
            : $this->menu($my_id, $uri[$SEGMENT_NUM]);

        $data['head_title']   = $menu['head_title'];
        $data['menu_level_1'] = $menu['menu_level_1'];
        $data['menu_level_2'] = $menu['menu_level_2'];
        $data['menu_level_3'] = $menu['menu_level_3'];
        $data['menu_level_4'] = $menu['menu_level_4'];
        $data['sj']           = null;
        $data['details']      = collect();
        $data['mode']         = 'create';
        $data['departments']  = SjGeneral::get_departments();
        $data['default_department_id'] = $defaultDepartmentId;

        return view('sj_general.form', $data);
    }

    /* =========================================================
     * EDIT FORM
     * ========================================================= */
    public function edit(Request $request)
    {
        $enc_id = $request->query('id');
        $id     = Crypt::decryptString(str_replace('-', '=', $enc_id));

        $my_id = Auth::user()->id;
        $uri = explode("/", url()->current());
        $SEGMENT_NUM = env('SEGMENT_NUM');
        $menu = (count($uri) <= $SEGMENT_NUM)
            ? $this->menu($my_id, 'home')
            : $this->menu($my_id, $uri[$SEGMENT_NUM]);

        $sj = DB::table('t100_sj_general')->where('id', $id)->first();
        if (!$sj) abort(404);

        $data['head_title']   = $menu['head_title'];
        $data['menu_level_1'] = $menu['menu_level_1'];
        $data['menu_level_2'] = $menu['menu_level_2'];
        $data['menu_level_3'] = $menu['menu_level_3'];
        $data['menu_level_4'] = $menu['menu_level_4'];
        $data['sj']           = $sj;
        $data['details']      = DB::table('t100_sj_general_detail')->where('sj_general_id', $id)->get();
        $data['mode']         = 'edit';
        $data['enc_id']       = $enc_id;
        $data['departments']  = SjGeneral::get_departments();
        $data['default_department_id'] = $sj->department_id ?: $this->resolveDepartmentId($this->getLoggedInDepartmentId());

        return view('sj_general.form', $data);
    }

    /* =========================================================
     * STORE (create)
     * ========================================================= */
    public function store(Request $request)
    {
        $my_id   = Auth::user()->id;
        $now     = Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');
        $today   = Carbon::now('Asia/Jakarta')->format('Y-m-d');
        $tmpSjNumber = 'TMP/SJG/' . Carbon::now('Asia/Jakarta')->format('YmdHis') . '/' . strtoupper(Str::random(6));
        $departmentId = $request->department_id ?: $this->resolveDepartmentId($this->getLoggedInDepartmentId());
        $departmentName = $request->department_name ?: $this->getDepartmentNameById($departmentId);

        $category = SjGeneral::get_category($request->return_status, $request->value_aspect);

        if ((int) $request->checked_by === (int) $request->approved_by) {
            return response()->json([
                'process_status' => 422,
                'msg_process' => 'Pemeriksa dan penyetuju harus user yang berbeda.',
            ]);
        }

        $assignmentValidation = $this->validateApproverAssignment(
            $request->checked_by,
            $request->approved_by,
            $category,
            $departmentId
        );

        if ($assignmentValidation !== null) {
            return response()->json([
                'process_status' => 422,
                'msg_process' => $assignmentValidation,
            ]);
        }

        try {
            $sj_id = DB::table('t100_sj_general')->insertGetId([
            'sj_number'       => $tmpSjNumber,
                'sj_date'          => $request->sj_date ?: $today,
                'po_num'           => $request->po_num,
                'ship_via_code'    => $request->ship_via_code,
                'return_status'    => $request->return_status,
                'value_aspect'     => $request->value_aspect,
                'category'         => $category,
                'remark'           => $request->remark,
                'recipient_name'   => $request->recipient_name,
                'recipient_address'=> $request->recipient_address,
                'driver_name'      => $request->driver_name,
                'plate_num'        => $request->plate_num,
                'department_id'    => $departmentId,
                'department_name'  => $departmentName,
                'checked_by'       => $request->checked_by,
                'approved_by'      => $request->approved_by,
                'status_checker'   => 'DRAFT',
                'status_approver'  => 'DRAFT',
                'created_by'       => $my_id,
                'is_deleted'       => 0,
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);

            $enc_id = str_replace('=', '-', Crypt::encryptString((string) $sj_id));

            return response()->json([
                'process_status' => 200,
                'msg_process' => 'Header berhasil disimpan.',
                'id' => $sj_id,
                'enc_id' => $enc_id,
            ]);
        } catch (\Exception $e) {
            \Log::error('SjGeneral store error: ' . $e->getMessage());
            return response()->json(['process_status' => 500, 'msg_process' => 'TERJADI ERROR KETIKA MENYIMPAN HEADER'], 500);
        }
    }

    /* =========================================================
     * UPDATE (edit)
     * ========================================================= */
    public function update(Request $request)
    {
        $enc_id  = $request->enc_id;
        $id      = Crypt::decryptString(str_replace('-', '=', $enc_id));
        $now     = Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');
        $my_id   = Auth::user()->id;
        $departmentId = $request->department_id ?: $this->resolveDepartmentId($this->getLoggedInDepartmentId());
        $departmentName = $request->department_name ?: $this->getDepartmentNameById($departmentId);
        $category = SjGeneral::get_category($request->return_status, $request->value_aspect);

        if ((int) $request->checked_by === (int) $request->approved_by) {
            return response()->json([
                'process_status' => 422,
                'msg_process' => 'Pemeriksa dan penyetuju harus user yang berbeda.',
            ]);
        }

        $assignmentValidation = $this->validateApproverAssignment(
            $request->checked_by,
            $request->approved_by,
            $category,
            $departmentId
        );

        if ($assignmentValidation !== null) {
            return response()->json([
                'process_status' => 422,
                'msg_process' => $assignmentValidation,
            ]);
        }

        try {
            $sj = DB::table('t100_sj_general')->where('id', $id)->first();
            if (!$sj) {
                return response()->json(['process_status' => 404, 'msg_process' => 'Data tidak ditemukan.']);
            }

            if ((int) $sj->created_by !== (int) $my_id) {
                return response()->json(['process_status' => 403, 'msg_process' => 'Hanya pembuat dokumen yang dapat mengubah header.']);
            }

            if ($sj->status_checker !== 'DRAFT') {
                return response()->json(['process_status' => 422, 'msg_process' => 'Header hanya bisa diubah saat status masih DRAFT.']);
            }

            DB::table('t100_sj_general')->where('id', $id)->update([
                'sj_date'          => $request->sj_date,
                'po_num'           => $request->po_num,
                'ship_via_code'    => $request->ship_via_code,
                'return_status'    => $request->return_status,
                'value_aspect'     => $request->value_aspect,
                'category'         => $category,
                'remark'           => $request->remark,
                'recipient_name'   => $request->recipient_name,
                'recipient_address'=> $request->recipient_address,
                'driver_name'      => $request->driver_name,
                'plate_num'        => $request->plate_num,
                'department_id'    => $departmentId,
                'department_name'  => $departmentName,
                'checked_by'       => $request->checked_by,
                'approved_by'      => $request->approved_by,
                'updated_at'       => $now,
            ]);

            return response()->json(['process_status' => 200, 'msg_process' => 'Header berhasil diupdate.']);
        } catch (\Exception $e) {
            return response()->json(['process_status' => 500, 'msg_process' => 'Gagal update: ' . $e->getMessage()], 500);
        }
    }

    /* =========================================================
     * SAVE DETAIL ONLY (create/update details)
     * ========================================================= */
    public function save_detail(Request $request)
    {
        $enc_id = $request->enc_id;
        $id     = Crypt::decryptString(str_replace('-', '=', $enc_id));
        $my_id  = Auth::user()->id;

        try {
            $sj = DB::table('t100_sj_general')->where('id', $id)->first();
            if (!$sj) {
                return response()->json(['process_status' => 404, 'msg_process' => 'Data tidak ditemukan.']);
            }

            if ((int) $sj->created_by !== (int) $my_id) {
                return response()->json(['process_status' => 403, 'msg_process' => 'Hanya pembuat dokumen yang dapat menyimpan detail.']);
            }

            if ($sj->status_checker !== 'DRAFT') {
                return response()->json(['process_status' => 422, 'msg_process' => 'Detail hanya bisa diubah saat status masih DRAFT.']);
            }

            if ($request->mode === 'delete') {
                $detail_id = (int) $request->detail_id;
                if ($detail_id <= 0) {
                    return response()->json(['process_status' => 422, 'msg_process' => 'ID detail tidak valid.']);
                }

                $deleted = DB::table('t100_sj_general_detail')
                    ->where('id', $detail_id)
                    ->where('sj_general_id', $id)
                    ->delete();

                if (!$deleted) {
                    return response()->json(['process_status' => 404, 'msg_process' => 'Detail tidak ditemukan atau sudah terhapus.']);
                }

                return response()->json(['process_status' => 200, 'msg_process' => 'Detail berhasil dihapus.']);
            }

            $partNums = is_array($request->part_num) ? $request->part_num : [$request->part_num];
            $partNames = is_array($request->part_name) ? $request->part_name : [$request->part_name];
            $qtys = is_array($request->qty) ? $request->qty : [$request->qty];
            $uoms = is_array($request->uom) ? $request->uom : [$request->uom];
            $remarks = is_array($request->item_remark) ? $request->item_remark : [$request->item_remark];
            $detailIds = is_array($request->detail_id) ? $request->detail_id : [$request->detail_id];

            if (count($partNums) === 0 || trim((string) ($partNums[0] ?? '')) === '') {
                return response()->json(['process_status' => 422, 'msg_process' => 'Part Number wajib diisi.']);
            }

            $now = Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');
            $savedItems = [];

            foreach ($partNums as $i => $partNumRaw) {
                $partNum = trim((string) $partNumRaw);
                $partName = trim((string) ($partNames[$i] ?? ''));
                $qty = (float) ($qtys[$i] ?? 0);
                $uom = trim((string) ($uoms[$i] ?? ''));
                $remark = trim((string) ($remarks[$i] ?? ''));
                $detailId = (int) ($detailIds[$i] ?? 0);

                if ($partNum === '') {
                    return response()->json(['process_status' => 422, 'msg_process' => 'Part Number wajib diisi.']);
                }

                if ($partName === '') {
                    return response()->json(['process_status' => 422, 'msg_process' => 'Part Name wajib diisi.']);
                }

                if ($qty <= 0) {
                    return response()->json(['process_status' => 422, 'msg_process' => 'Qty harus lebih dari 0.']);
                }

                if ($detailId > 0) {
                    $updated = DB::table('t100_sj_general_detail')
                        ->where('id', $detailId)
                        ->where('sj_general_id', $id)
                        ->update([
                            'part_num'   => $partNum,
                            'part_name'  => $partName,
                            'qty'        => $qty,
                            'uom'        => $uom,
                            'remark'     => $remark,
                            'updated_at' => $now,
                        ]);

                    if ($updated === 0) {
                        $exists = DB::table('t100_sj_general_detail')
                            ->where('id', $detailId)
                            ->where('sj_general_id', $id)
                            ->exists();

                        if (!$exists) {
                            return response()->json(['process_status' => 404, 'msg_process' => 'Detail yang diubah tidak ditemukan.']);
                        }
                    }
                } else {
                    $detailId = DB::table('t100_sj_general_detail')->insertGetId([
                        'sj_general_id' => $id,
                        'part_num'      => $partNum,
                        'part_name'     => $partName,
                        'qty'           => $qty,
                        'uom'           => $uom,
                        'remark'        => $remark,
                        'created_at'    => $now,
                        'updated_at'    => $now,
                    ]);
                }

                $savedItems[] = [
                    'detail_id'   => $detailId,
                    'part_num'    => $partNum,
                    'part_name'   => $partName,
                    'qty'         => $qty,
                    'uom'         => $uom,
                    'item_remark' => $remark,
                ];
            }

            return response()->json([
                'process_status' => 200,
                'msg_process' => 'Detail berhasil disimpan.',
                'item' => $savedItems[0] ?? null,
                'items' => $savedItems,
            ]);
        } catch (\Exception $e) {
            return response()->json(['process_status' => 500, 'msg_process' => 'Gagal simpan detail: ' . $e->getMessage()], 500);
        }
    }

    /* =========================================================
     * SUBMIT FOR REVIEW (generate SJ number)
     * ========================================================= */
    public function submit_review(Request $request)
    {
        $enc_id  = $request->enc_id;
        $id      = Crypt::decryptString(str_replace('-', '=', $enc_id));
        $now     = Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');

        try {
            $sj = DB::table('t100_sj_general')->where('id', $id)->first();
            if (!$sj) return response()->json(['process_status' => 404, 'msg_process' => 'Data tidak ditemukan.']);

            if ($sj->status_checker !== 'DRAFT') {
                return response()->json(['process_status' => 422, 'msg_process' => 'Dokumen sudah pernah dikirim untuk approval.']);
            }

            $detailCount = DB::table('t100_sj_general_detail')->where('sj_general_id', $id)->count();
            if ($detailCount <= 0) {
                return response()->json(['process_status' => 422, 'msg_process' => 'Tambahkan detail part/barang terlebih dahulu sebelum dikirim.']);
            }

            if (!$sj->checked_by || !$sj->approved_by) {
                return response()->json(['process_status' => 422, 'msg_process' => 'Pemeriksa dan penyetuju wajib dipilih sebelum kirim approval.']);
            }

            $departmentCode = null;
            if (!empty($sj->department_id)) {
                $departmentCode = DB::table('Department')
                    ->where('id', $sj->department_id)
                    ->value('DepartementCode');
            }

            $sj_number = (!$sj->sj_number || $this->is_temp_sj_number($sj->sj_number))
                ? SjGeneral::generate_sj_number($departmentCode)
                : $sj->sj_number;
            DB::table('t100_sj_general')->where('id', $id)->update([
                'sj_number'        => $sj_number,
                'status_checker'   => 'PENDING',
                'status_approver'  => 'PENDING',
                'updated_at'       => $now,
            ]);
            return response()->json(['process_status' => 200, 'msg_process' => 'Dokumen berhasil dikirim ke pemeriksa/penyetuju. Nomor SJ: ' . $sj_number]);
        } catch (\Exception $e) {
            return response()->json(['process_status' => 500, 'msg_process' => 'Gagal submit: ' . $e->getMessage()], 500);
        }
    }

    /* =========================================================
     * CHECKER ACTION (approve/reject)
     * ========================================================= */
    public function do_check(Request $request)
    {
        $enc_id = $request->enc_id;
        $id     = Crypt::decryptString(str_replace('-', '=', $enc_id));
        $status = $request->action_status; // APPROVED / REJECTED
        $note   = $request->note;
        $now    = Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');
        $my_id  = Auth::user()->id;

        try {
            $sj = DB::table('t100_sj_general')->where('id', $id)->where('checked_by', $my_id)->first();
            if (!$sj) return response()->json(['process_status' => 403, 'msg_process' => 'Akses ditolak.']);

            DB::table('t100_sj_general')->where('id', $id)->update([
                'status_checker' => $status,
                'checker_note'   => $note,
                'checked_at'     => $now,
                'updated_at'     => $now,
            ]);
            $msg = $status === 'APPROVED' ? 'Dokumen berhasil disetujui.' : 'Dokumen telah ditolak.';
            return response()->json(['process_status' => 200, 'msg_process' => $msg]);
        } catch (\Exception $e) {
            return response()->json(['process_status' => 500, 'msg_process' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    /* =========================================================
     * APPROVER ACTION
     * ========================================================= */
    public function do_approve(Request $request)
    {
        $enc_id = $request->enc_id;
        $id     = Crypt::decryptString(str_replace('-', '=', $enc_id));
        $status = $request->action_status;
        $note   = $request->note;
        $now    = Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');
        $my_id  = Auth::user()->id;

        try {
            $sj = DB::table('t100_sj_general')->where('id', $id)->where('approved_by', $my_id)->first();
            if (!$sj) return response()->json(['process_status' => 403, 'msg_process' => 'Akses ditolak.']);

            DB::table('t100_sj_general')->where('id', $id)->update([
                'status_approver' => $status,
                'approver_note'   => $note,
                'approved_at'     => $now,
                'updated_at'      => $now,
            ]);
            $msg = $status === 'APPROVED' ? 'Dokumen berhasil disetujui.' : 'Dokumen telah ditolak.';
            return response()->json(['process_status' => 200, 'msg_process' => $msg]);
        } catch (\Exception $e) {
            return response()->json(['process_status' => 500, 'msg_process' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    /* =========================================================
     * DELETE
     * ========================================================= */
    public function destroy(Request $request)
    {
        $enc_id = $request->enc_id;
        $id     = Crypt::decryptString(str_replace('-', '=', $enc_id));
        $now    = Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');

        try {
            DB::table('t100_sj_general')->where('id', $id)->update(['is_deleted' => 1, 'updated_at' => $now]);
            return response()->json(['process_status' => 200, 'msg_process' => 'Data berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['process_status' => 500, 'msg_process' => 'Gagal hapus: ' . $e->getMessage()], 500);
        }
    }

    /* =========================================================
     * GET USERS BY MIN LEVEL (AJAX Select2)
     * ========================================================= */
    public function get_users_by_level(Request $request)
    {
        $min_level     = $request->min_level;
        $search        = $request->search;
        $department_id = $request->department_id ?: $this->resolveDepartmentId($this->getLoggedInDepartmentId());
        $page          = $request->post('page', 1);
        $pageSize      = 10;

        $query = SjGeneral::get_users_by_min_level($min_level, $search, $department_id);
        $users = $query->paginate($pageSize, ['*'], 'page', $page);

        return response()->json([
            'items' => $users->map(fn($u) => [
                'id'   => $u->id,
                'name' => $u->full_name . ' (' . $u->role_name . ')',
            ]),
            'pagination' => ['more' => $users->hasMorePages()],
        ]);
    }

    private function getLoggedInDepartmentId()
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }

        return $user->DepartmentID
            ?? $user->department_id
            ?? null;
    }

    private function resolveDepartmentId($departmentId)
    {
        if (empty($departmentId)) {
            return null;
        }

        return DB::table('Department')
            ->where('id', $departmentId)
            ->whereNull('DeletedAt')
            ->value('id');
    }

    private function getDepartmentNameById($departmentId)
    {
        if (empty($departmentId)) {
            return null;
        }

        return DB::table('Department')
            ->where('id', $departmentId)
            ->value('DepartmentName');
    }

    private function validateApproverAssignment($checkedBy, $approvedBy, $category, $departmentId)
    {
        if (empty($checkedBy) || empty($approvedBy)) {
            return 'Pemeriksa dan penyetuju wajib dipilih.';
        }

        if (empty($category)) {
            return 'Kategori approval tidak valid. Periksa Return Status dan Value Aspect.';
        }

        $levels = SjGeneral::get_approval_levels($category);
        $checkerLevel = $levels['checker'] ?? null;
        $approverLevel = $levels['approver'] ?? null;

        if (empty($checkerLevel) || empty($approverLevel)) {
            return 'Konfigurasi level approval tidak ditemukan untuk kategori ini.';
        }

        $checkerExists = $this->isEligibleApprover($checkedBy, $checkerLevel, $departmentId);
        if (!$checkerExists) {
            return 'User pemeriksa tidak memenuhi minimal level untuk kategori ini atau tidak sesuai departemen.';
        }

        $approverExists = $this->isEligibleApprover($approvedBy, $approverLevel, $departmentId);
        if (!$approverExists) {
            return 'User penyetuju tidak memenuhi minimal level untuk kategori ini atau tidak sesuai departemen.';
        }

        return null;
    }

    private function isEligibleApprover($userId, $minLevel, $departmentId)
    {
        return SjGeneral::get_users_by_min_level($minLevel, null, $departmentId)
            ->where('u.id', $userId)
            ->exists();
    }

    /* =========================================================
     * GET DEPARTMENTS (AJAX Select2)
     * ========================================================= */
    public function get_departments(Request $request)
    {
        $departments = SjGeneral::get_departments();
        return response()->json($departments);
    }

    /* =========================================================
     * GET PARTS (AJAX Select2 from Epicor)
     * ========================================================= */
    public function get_parts(Request $request)
    {
        $search   = $request->search;
        $page     = $request->post('page', 1);
        $pageSize = 10;

        $query = DB::connection('sqlsrv4')->table('Erp.Part as a')
            ->select('a.PartNum', 'a.PartDescription');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('a.PartNum', 'like', '%' . $search . '%')
                  ->orWhere('a.PartDescription', 'like', '%' . $search . '%');
            });
        }

        $parts = $query->paginate($pageSize, ['*'], 'page', $page);

        return response()->json([
            'items' => $parts->map(fn($p) => [
                'id'   => $p->PartNum,
                'name' => $p->PartNum . ' - ' . $p->PartDescription,
                'desc' => $p->PartDescription,
            ]),
            'pagination' => ['more' => $parts->hasMorePages()],
        ]);
    }

    /* =========================================================
     * GET OPEN PO (AJAX Select2)
     * ========================================================= */
    public function get_open_po(Request $request)
    {
        $search   = $request->search;
        $page     = $request->post('page', 1);
        $pageSize = 10;

        $query = DB::connection('sqlsrv4')
            ->table('Erp.POHeader as a')
            ->leftJoin('Erp.Vendor as b', 'b.VendorNum', '=', 'a.VendorNum')
            ->selectRaw("a.PONum, a.VendorNum, b.Name, a.OpenOrder, CONCAT(ISNULL(b.Address1, ''), ' ', ISNULL(b.Address2, ''), ' ', ISNULL(b.Address3, '')) as Alamat")
            ->where('a.ApprovalStatus', 'A')
            ->where('a.OpenOrder', 1)
            ->orderBy('a.PONum', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('a.PONum', 'like', '%' . $search . '%')
                    ->orWhere('b.Name', 'like', '%' . $search . '%')
                    ->orWhere('a.VendorNum', 'like', '%' . $search . '%');
            });
        }

        $rows = $query->paginate($pageSize, ['*'], 'page', $page);

        return response()->json([
            'items' => $rows->map(fn($r) => [
                'id' => $r->PONum,
                'text' => $r->PONum . ' - ' . ($r->Name ?? '-'),
                'ponum' => $r->PONum,
                'vendor_num' => $r->VendorNum,
                'recipient_name' => $r->Name,
                'recipient_address' => trim($r->Alamat ?? ''),
            ]),
            'pagination' => ['more' => $rows->hasMorePages()],
        ]);
    }

    /* =========================================================
     * GET SHIP VIA LIST (AJAX Select2)
     * ========================================================= */
    public function get_ship_via_list(Request $request)
    {
        $search = $request->search;

        $query = DB::connection('sqlsrv4')
            ->table('Erp.ShipVia')
            ->select('ShipViaCode', 'Description')
            ->orderBy('ShipViaCode');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('ShipViaCode', 'like', '%' . $search . '%')
                    ->orWhere('Description', 'like', '%' . $search . '%');
            });
        }

        $rows = $query->limit(100)->get();

        return response()->json([
            'items' => $rows->map(fn($r) => [
                'id' => $r->ShipViaCode,
                'text' => $r->ShipViaCode . ' - ' . $r->Description,
                'code' => $r->ShipViaCode,
                'desc' => $r->Description,
            ]),
        ]);
    }

    /* =========================================================
     * CALCULATE CATEGORY (AJAX)
     * ========================================================= */
    public function calculate_category(Request $request)
    {
        $category = SjGeneral::get_category($request->return_status, $request->value_aspect);
        $levels   = $category ? SjGeneral::get_approval_levels($category) : null;

        return response()->json([
            'category' => $category,
            'levels'   => $levels,
        ]);
    }

    /* =========================================================
     * PRINT PDF
     * ========================================================= */
    public function print_pdf(Request $request)
    {
        $enc_id = $request->query('id') ?? explode('/', $request->path())[2];
        // handle route param
        if ($request->route('id')) {
            $enc_id = $request->route('id');
        }
        $id  = Crypt::decryptString(str_replace('-', '=', $enc_id));
        $sj  = DB::table('t100_sj_general as h')
            ->leftJoin('users as u', 'u.id', '=', 'h.created_by')
            ->leftJoin('users as c', 'c.id', '=', 'h.checked_by')
            ->leftJoin('users as a', 'a.id', '=', 'h.approved_by')
            ->where('h.id', $id)
            ->select('h.*', 'u.full_name as creator_name', 'c.full_name as checker_name', 'a.full_name as approver_name')
            ->first();

        if (!$sj) abort(404);

        $details = DB::table('t100_sj_general_detail')->where('sj_general_id', $id)->get();
        $company = AppModel::find_company_profile('all');
        $company_arr = explode('^', $company);

        $filename = 'SuratJalanGeneral_' . ($sj->sj_number ?? 'DRAFT') . '.pdf';

        PDF::SetAuthor('NUX System');
        PDF::SetCreator('NUX Portal - SAI');
        PDF::SetTitle($filename);
        PDF::SetMargins(6.35, 6.35, 6.35);
        PDF::SetAutoPageBreak(true, 12.7);
        PDF::setPrintHeader(false);
        PDF::setPrintFooter(false);
        PDF::AddPage('P', 'A4');
        PDF::SetFont('dejavusans', '', 8);

        $dateText = $sj->sj_date ? date('j/n/Y', strtotime($sj->sj_date)) : '-';
        $docNumber = $this->is_temp_sj_number($sj->sj_number ?? '') ? 'DRAFT' : ($sj->sj_number ?? 'DRAFT');
        $companyName = trim((string) ($company_arr[1] ?? 'PT. SUMMIT ADYAWINSA INDONESIA'));
        $companyAddress = trim((string) ($company_arr[2] ?? ''));

        // RDL page setup: Left/Right/Top 0.25in, Bottom 0.5in
        $x = 6.35;
        $y = 6.35;
        $w = 210 - (2 * 6.35);

        PDF::SetLineStyle([
            'width' => 0.18,
            'cap' => 'butt',
            'join' => 'miter',
            'dash' => 0,
            'color' => [211, 211, 211],
        ]);
        PDF::SetTextColor(0, 0, 0);

        PDF::SetFont('dejavusans', '', 10);
        PDF::SetXY($x, $y);
        PDF::MultiCell(120, 4.7, $companyName . "\n" . $companyAddress, 0, 'L', false, 1);

        $logoPath = public_path('assets/media/logos/sai-logo.png');
        if (file_exists($logoPath)) {
            PDF::Image($logoPath, $x + $w - 52, $y + 0.5, 52, 13.5, 'PNG');
        }

        PDF::SetFont('dejavusans', '', 10);
        PDF::SetXY($x + $w - 30, $y + 7.8);
        // PDF::Cell(30, 5, 'Page: ' . PDF::getAliasNumPage() . ' of ' . PDF::getAliasNbPages(), 0, 1, 'R');

        PDF::SetFont('dejavusans', 'B', 11);
        PDF::SetXY($x, $y + 25.1);
        // PDF::Cell(55, 7, 'Packing Slip: ' . $docNumber, 0, 0, 'L');
        PDF::Rect($x + 62.9, $y + 23.9, 81.53, 8.13);
        PDF::SetXY($x + 62.9, $y + 25.0);
        PDF::Cell(81.53, 6, 'Packing Slip ' . $docNumber, 0, 0, 'C');

        $boxY = $y + 35.0;
        $boxH = 49.0;
        $leftW = 96.2;
        $rightW = $w - $leftW;

        PDF::Rect($x, $boxY, $w, $boxH);
        PDF::Rect($x, $boxY, $leftW, $boxH);
        PDF::Rect($x + $leftW, $boxY, $rightW, $boxH);

        PDF::SetFont('dejavusans', 'B', 10);
        PDF::SetXY($x + 2, $boxY + 2);
        PDF::Cell(20, 4, 'Ship To :', 0, 1, 'L');

        PDF::SetFont('dejavusans', '', 10);
        PDF::SetXY($x + 22, $boxY + 2);
        PDF::MultiCell($leftW - 24, 4.5, trim((string) ($sj->recipient_name ?? '-')) . "\n" . trim((string) ($sj->recipient_address ?? '-')), 0, 'L', false, 1);

        $rx = $x + $leftW;
        $colW = $rightW / 2;
        $rY = $boxY;

        PDF::SetFont('dejavusans', '', 9.3);
        // Row 1: Reg No (left) + Date (right)
        PDF::Rect($rx, $rY, $colW, 8);
        PDF::Rect($rx + $colW, $rY, $colW, 8);
        PDF::SetXY($rx + 1.5, $rY + 2);
        PDF::Cell($colW - 3, 4, 'Reg No. : ' . $docNumber, 0, 0, 'L');
        PDF::SetXY($rx + $colW + 1.5, $rY + 2);
        PDF::Cell($colW - 3, 4, 'Date : ' . $dateText, 0, 0, 'L');
        $rY += 8;

        // Row 2: Cust PO full width
        PDF::Rect($rx, $rY, $rightW, 8);
        PDF::SetXY($rx + 1.5, $rY + 2);
        PDF::Cell($rightW - 3, 4, 'Cust. PO Reff : ' . ($sj->po_num ?: '0'), 0, 0, 'L');
        $rY += 8;

        // Row 3: SO Reff (left) + Cycle (right)
        PDF::Rect($rx, $rY, $colW, 8);
        PDF::Rect($rx + $colW, $rY, $colW, 8);
        PDF::SetXY($rx + 1.5, $rY + 2);
        PDF::Cell($colW - 3, 4, 'SO. Reff : 0', 0, 0, 'L');
        PDF::SetXY($rx + $colW + 1.5, $rY + 2);
        PDF::Cell($colW - 3, 4, 'Cycle : -', 0, 0, 'L');
        $rY += 8;

        PDF::Rect($rx, $rY, $rightW, 6);
        PDF::SetFont('dejavusans', 'B', 9);
        PDF::SetXY($rx, $rY + 1.2);
        PDF::Cell($rightW, 4, 'Transportation Data Filled by Security', 0, 0, 'C');
        $rY += 6;

        PDF::Rect($rx, $rY, $colW, 8);
        PDF::Rect($rx + $colW, $rY, $colW, 8);
        PDF::SetFont('dejavusans', '', 9.2);
        PDF::SetXY($rx + 1.5, $rY + 2);
        PDF::Cell($colW - 3, 4, 'Car Num : ' . trim((string) ($sj->plate_num ?: '-')), 0, 0, 'L');
        PDF::SetXY($rx + $colW + 1.5, $rY + 2);
        PDF::Cell($colW - 3, 4, 'Checked by :', 0, 0, 'L');
        $rY += 8;

        PDF::Rect($rx, $rY, $colW, 10);
        PDF::Rect($rx + $colW, $rY, $colW, 10);
        PDF::SetXY($rx + 1.5, $rY + 1.5);
        PDF::Cell($colW - 3, 4, 'Time in :', 0, 1, 'L');
        PDF::SetX($rx + 1.5);
        PDF::Cell($colW - 3, 4, 'Time out :', 0, 0, 'L');
        PDF::SetXY($rx + $colW + 1.5, $rY + 3.3);
        PDF::Cell($colW - 3, 4, 'Name :', 0, 0, 'L');

        $itemY = $boxY + $boxH + 7.2;
        $itemH = 20.5;
        $headH = 7.2;
        $lineW = 10.8;
        $partW = 110.5;
        $revW = 35.7;
        $qtyW = 20.6;
        $uomW = $w - $lineW - $partW - $revW - $qtyW ;

        // PDF::Rect($x, $itemY, $w, $itemH);
        // PDF::Line($x, $itemY + $headH, $x + $w, $itemY + $headH);
        // PDF::Line($x + $lineW, $itemY, $x + $lineW, $itemY + $itemH);
        // PDF::Line($x + $lineW + $partW, $itemY, $x + $lineW + $partW, $itemY + $itemH);
        // PDF::Line($x + $lineW + $partW + $revW, $itemY, $x + $lineW + $partW + $revW, $itemY + $itemH);
        // PDF::Line($x + $lineW + $partW + $revW + $qtyW, $itemY, $x + $lineW + $partW + $revW + $qtyW, $itemY + $itemH);

        PDF::SetFont('dejavusans', 'B', 9.5);
        PDF::SetXY($x, $itemY + 1.8);
        PDF::Cell($lineW, 4, 'Line', 0, 0, 'C');
        PDF::Cell($partW, 4, 'Part Number', 0, 0, 'L');
        PDF::Cell($revW, 4, 'Model', 0, 0, 'C');
        PDF::Cell($qtyW, 4, 'Quantity', 0, 0, 'R');
        PDF::Cell($uomW, 4, 'UoM', 0, 0, 'C');

        $rowY = $itemY + $headH + 1.5;
        $rowStep = 8.8;
        $lineNo = 1;
        PDF::SetFont('dejavusans', '', 10);
        foreach ($details as $d) {
            if ($rowY + $rowStep > $itemY + $itemH - 2) {
                break;
            }

            $partText = trim((string) ($d->part_num ?? '-'));
            if (!empty($d->part_name)) {
                $partText .= "\n" . trim((string) $d->part_name);
            }

            PDF::SetXY($x + 1, $rowY);
            PDF::Cell($lineW - 2, 1, (string) $lineNo++, 0, 0, 'C');
            PDF::SetXY($x + $lineW + 1, $rowY - 0.5);
            PDF::MultiCell($partW - 2, 1, $partText, 0, 'L', false, 1);
            PDF::SetXY($x + $lineW + $partW + 1, $rowY);
            PDF::Cell($revW - 2, 1, '', 0, 0, 'C');
            PDF::SetXY($x + $lineW + $partW + $revW + 1, $rowY);
            PDF::Cell($qtyW - 2, 1, number_format((float) $d->qty, 2), 0, 0, 'R');
            PDF::SetXY($x + $lineW + $partW + $revW + $qtyW + 1, $rowY);
            PDF::Cell($uomW - 2, 1, trim((string) ($d->uom ?? '')), 0, 0, 'C');

            $rowY += $rowStep;
        }

        $legY = $itemY + $itemH + 7.2;
        $legH = 40.5;
        $leftLegW = 99.58;
        $rightLegW = $w - $leftLegW;

        PDF::Rect($x, $legY, $w, $legH);
        PDF::Line($x + $leftLegW, $legY, $x + $leftLegW, $legY + $legH);
        PDF::Line($x, $legY + 7, $x + $w, $legY + 7);

        PDF::SetFont('dejavusans', '', 8.2);
        PDF::SetXY($x, $legY + 1.8);
        PDF::Cell($leftLegW, 4, 'Transfered Legalization (Supplier Area)', 0, 0, 'C');
        PDF::Cell($rightLegW, 4, 'Transfered Legalization (Buyer Area)', 0, 0, 'C');

        $supY = $legY + 7;
        $col1 = 31.60;
        $col2 = 33.33;
        $col3 = $leftLegW - $col1 - $col2;
        PDF::Line($x + $col1, $supY, $x + $col1, $legY + $legH);
        PDF::Line($x + $col1 + $col2, $supY, $x + $col1 + $col2, $legY + $legH);
        PDF::Line($x, $supY + 7, $x + $leftLegW, $supY + 7);
        PDF::Line($x, $supY + 28, $x + $leftLegW, $supY + 28);

        PDF::SetFont('dejavusans', '', 8.5);
        PDF::SetXY($x, $supY + 1.5);
        PDF::Cell($col1, 4, 'Dibuat', 0, 0, 'C');
        PDF::Cell($col2, 4, 'Diperiksa', 0, 0, 'C');
        PDF::Cell($col3, 4, 'Disetujui', 0, 0, 'C');

        PDF::SetFont('dejavusans', '', 7.5);
        PDF::SetXY($x, $supY + 20);
        PDF::Cell($col1, 20, trim((string) ($sj->creator_name ?? '-')), 0, 0, 'C');
        PDF::Cell($col2, 20, trim((string) ($sj->checker_name ?? '-')), 0, 0, 'C');
        PDF::Cell($col3, 20, trim((string) ($sj->approver_name ?? '-')), 0, 0, 'C');

        $bx = $x + $leftLegW;
        PDF::Line($bx, $supY + 7, $bx + $rightLegW, $supY + 7);
        PDF::Line($bx, $supY + 14, $bx + $rightLegW, $supY + 14);
        PDF::Line($bx + ($rightLegW / 2), $supY, $bx + ($rightLegW / 2), $legY + $legH);

        PDF::SetFont('dejavusans', '', 8);
        PDF::SetXY($bx + 1.5, $supY + 1.5);
        PDF::Cell(($rightLegW / 2) - 3, 4, 'Time in :', 0, 0, 'L');
        PDF::SetXY($bx + ($rightLegW / 2) + 1.5, $supY + 1.5);
        PDF::Cell(($rightLegW / 2) - 3, 4, 'Time Out :', 0, 0, 'L');
        PDF::SetXY($bx + 1.5, $supY + 8.5);
        PDF::Cell(($rightLegW / 2) - 3, 4, 'Recieved By :', 0, 0, 'L');
        PDF::SetXY($bx + ($rightLegW / 2) + 1.5, $supY + 8.5);
        PDF::Cell(($rightLegW / 2) - 3, 4, 'Signature & Comp. Stamp', 0, 0, 'L');
        PDF::SetXY($bx + 1.5, $supY + 19.5);
        PDF::MultiCell(($rightLegW / 2) - 3, 4, 'Name\nOccupation,', 0, 'L', false, 1);

         PDF::SetY($bx + 160.5);
        PDF::SetFont('courier', 'I', 9);
        PDF::SetLineStyle([
            'width' => 0.18,
            'cap' => 'butt',
            'join' => 'miter',
            'dash' => 0,
            'color' => [211, 211, 211],
        ]);
        PDF::Cell(100, 6, 'FO-35-01', 'T', 0, 'L');
        PDF::Cell(81, 6, '', 'T', 0, 'L');
        PDF::Cell(10, 6, PDF::getAliasNumPage() . '/' . PDF::getAliasNbPages(), 'T', 0, 'L');

        PDF::Output($filename, 'I');
    }

    /* =========================================================
     * PRIVATE HELPERS
     * ========================================================= */
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

    private function is_temp_sj_number($sj_number)
    {
        return is_string($sj_number) && str_starts_with($sj_number, 'TMP/SJG/');
    }

    private function status_badge($status)
    {
        $map = [
            'DRAFT'    => 'secondary',
            'PENDING'  => 'warning',
            'APPROVED' => 'success',
            'REJECTED' => 'danger',
        ];
        $c = $map[$status] ?? 'secondary';
        return '<span class="badge badge-light-' . $c . '">' . $status . '</span>';
    }
}
