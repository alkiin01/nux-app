@extends('../layouts/app')

@section('subhead')
    <title>{{ $head_title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .category-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            font-size: 2rem;
            font-weight: 900;
            color: #fff;
            transition: background 0.4s, transform 0.3s;
            transform: scale(1);
        }

        .category-badge.cat-A {
            background: #dc3545;
        }

        .category-badge.cat-B {
            background: #28a745;
        }

        .category-badge.cat-C {
            background: #ffc107;
            color: #333;
        }

        .category-badge.cat-D {
            background: #17a2b8;
        }

        .category-badge.cat-none {
            background: #adb5bd;
            font-size: 1rem;
        }

        .matrix-table th,
        .matrix-table td {
            text-align: center;
            vertical-align: middle;
            font-size: 12px;
            padding: 6px 8px;
        }

        .matrix-table td.cat-A {
            background: #dc3545;
            color: #fff;
            font-weight: bold;
        }

        .matrix-table td.cat-B {
            background: #28a745;
            color: #fff;
            font-weight: bold;
        }

        .matrix-table td.cat-C {
            background: #ffc107;
            color: #333;
            font-weight: bold;
        }

        .matrix-table td.cat-D {
            background: #17a2b8;
            color: #fff;
            font-weight: bold;
        }

        .matrix-table td.active-cell {
            outline: 3px solid #333;
            outline-offset: -3px;
        }

        .select2-container--default .select2-selection--single {
            height: 42px;
            line-height: 42px;
            border: 1px solid #e4e6ef;
            border-radius: 6px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 42px;
            padding-left: 12px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 42px;
        }
    </style>
@endsection

<script src="<?= env('APP_ASSETS') ?>assets/js/jquery/jquery.min.js"></script>

@section('subcontent')
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <div class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">Surat Jalan General
                    <span class="h-20px border-gray-200 border-start ms-3 mx-2"></span>
                    <small
                        class="text-muted fs-7 fw-bold my-1 ms-1">{{ $mode === 'create' ? 'Buat Dokumen Baru' : 'Edit Dokumen' }}</small>
                </h1>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ url('sj_general') }}" class="btn btn-sm btn-light-secondary">
                    <i class="fa fa-arrow-left fs-6"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="content d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-xxl">
            @php
                $detailItemsJs = ($details ?? collect())->map(function ($d) {
                    return [
                        'detail_id' => $d->id,
                        'part_num' => $d->part_num,
                        'part_name' => $d->part_name,
                        'qty' => (float) $d->qty,
                        'uom' => $d->uom,
                        'item_remark' => $d->remark,
                    ];
                })->values();
            @endphp

            @if ($mode === 'edit' && $sj)
                <input type="hidden" id="enc_id" value="{{ $enc_id }}">
            @endif

            <div class="row g-5">
                {{-- RIGHT: Matrix & Approvers --}}
                <div class="col-xl-4">
                    {{-- Category Matrix --}}
                    <div class="card shadow-sm mb-5">
                        <div class="card-header border-0"> 
                            <h3 class="card-title fw-bold text-dark fs-6">Matriks Kategori</h3>
                        </div>
                        <div class="card-body pt-0">
                            <div class="mb-4">
                                <label class="form-label fw-bold required">Return Status</label>
                                <select class="form-select" id="return_status" onchange="updateCategory()">
                                    <option value="">-- Pilih --</option>
                                    <option value="Dikembalikan ke SAI < 1 bulan"
                                        {{ ($sj->return_status ?? '') == 'Dikembalikan ke SAI < 1 bulan' ? 'selected' : '' }}>
                                        Dikembalikan ke SAI &lt; 1 bulan</option>
                                    <option value="Dikembalikan ke SAI 1 - 3 bulan"
                                        {{ ($sj->return_status ?? '') == 'Dikembalikan ke SAI 1 - 3 bulan' ? 'selected' : '' }}>
                                        Dikembalikan ke SAI 1 - 3 bulan</option>
                                    <option value="Dikembalikan ke SAI 3 - 6 bulan"
                                        {{ ($sj->return_status ?? '') == 'Dikembalikan ke SAI 3 - 6 bulan' ? 'selected' : '' }}>
                                        Dikembalikan ke SAI 3 - 6 bulan</option>
                                    <option value="Dikembalikan ke SAI 6 bulan - 3 th"
                                        {{ ($sj->return_status ?? '') == 'Dikembalikan ke SAI 6 bulan - 3 th' ? 'selected' : '' }}>
                                        Dikembalikan ke SAI 6 bulan - 3 th</option>
                                    <option value="Dikembalikan ke SAI > 3 tahun"
                                        {{ ($sj->return_status ?? '') == 'Dikembalikan ke SAI > 3 tahun' ? 'selected' : '' }}>
                                        Dikembalikan ke SAI &gt; 3 tahun</option>
                                    <option value="Tidak dikembalikan"
                                        {{ ($sj->return_status ?? '') == 'Tidak dikembalikan' ? 'selected' : '' }}>Tidak
                                        dikembalikan</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold required">Value Aspect</label>
                                <select class="form-select" id="value_aspect" onchange="updateCategory()">
                                    <option value="">-- Pilih --</option>
                                    <option value="Bernilai ≤ Rp.500.000"
                                        {{ ($sj->value_aspect ?? '') == 'Bernilai ≤ Rp.500.000' ? 'selected' : '' }}>
                                        Bernilai ≤ Rp.500.000</option>
                                    <option value="Bernilai ≤ Rp.5.000.000"
                                        {{ ($sj->value_aspect ?? '') == 'Bernilai ≤ Rp.5.000.000' ? 'selected' : '' }}>
                                        Bernilai ≤ Rp.5.000.000</option>
                                    <option value="Bernilai ≤ Rp.50.000.000"
                                        {{ ($sj->value_aspect ?? '') == 'Bernilai ≤ Rp.50.000.000' ? 'selected' : '' }}>
                                        Bernilai ≤ Rp.50.000.000</option>
                                    <option value="Bernilai > Rp.50.000.000"
                                        {{ ($sj->value_aspect ?? '') == 'Bernilai > Rp.50.000.000' ? 'selected' : '' }}>
                                        Bernilai > Rp.50.000.000</option>
                                </select>
                            </div>

                            {{-- Category result --}}
                            <div class="d-flex align-items-center gap-4 mb-4">
                                <div id="category_display" class="category-badge cat-none">?</div>
                                <div>
                                    <div class="fw-bold fs-6">Kategori</div>
                                    <div class="text-muted fs-8" id="category_label">Pilih return status & value aspect
                                    </div>
                                </div>
                            </div>

                            {{-- Visual matrix --}}
                            <div class="table-responsive">
                                <table class="table table-bordered matrix-table mb-0">
                                    <thead class="table-dark">
                                        <tr>
                                            <th style="font-size:10px; width:120px">Return Status</th>
                                            <th>≤500rb</th>
                                            <th>≤5jt</th>
                                            <th>≤50jt</th>
                                            <th>>50jt</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr data-row="0">
                                            <td style="font-size:10px; text-align:left">&lt;1 bln</td>
                                            <td class="cat-D">D</td>
                                            <td class="cat-C">C</td>
                                            <td class="cat-B">B</td>
                                            <td class="cat-A">A</td>
                                        </tr>
                                        <tr data-row="1">
                                            <td style="font-size:10px; text-align:left">1-3 bln</td>
                                            <td class="cat-D">D</td>
                                            <td class="cat-C">C</td>
                                            <td class="cat-B">B</td>
                                            <td class="cat-A">A</td>
                                        </tr>
                                        <tr data-row="2">
                                            <td style="font-size:10px; text-align:left">3-6 bln</td>
                                            <td class="cat-D">D</td>
                                            <td class="cat-C">C</td>
                                            <td class="cat-B">B</td>
                                            <td class="cat-A">A</td>
                                        </tr>
                                        <tr data-row="3">
                                            <td style="font-size:10px; text-align:left">6bln-3th</td>
                                            <td class="cat-D">D</td>
                                            <td class="cat-C">C</td>
                                            <td class="cat-A">A</td>
                                            <td class="cat-A">A</td>
                                        </tr>
                                        <tr data-row="4">
                                            <td style="font-size:10px; text-align:left">&gt;3 th</td>
                                            <td class="cat-D">D</td>
                                            <td class="cat-C">C</td>
                                            <td class="cat-A">A</td>
                                            <td class="cat-A">A</td>
                                        </tr>
                                        <tr data-row="5">
                                            <td style="font-size:10px; text-align:left">Tdk kembali</td>
                                            <td class="cat-C">C</td>
                                            <td class="cat-B">B</td>
                                            <td class="cat-A">A</td>
                                            <td class="cat-A">A</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Approvers --}}
                    <div class="card shadow-sm mb-5">
                        <div class="card-header border-0">
                            <h3 class="card-title fw-bold text-dark fs-6">Pemeriksa & Penyetuju</h3>
                        </div>
                        <div class="card-body pt-0">
                            <div class="mb-4">
                                <label class="form-label fw-bold required">Pemeriksa (Checker)</label>
                                <div class="text-muted fs-9 mb-2" id="checker_level_hint">Pilih kategori terlebih dahulu
                                </div>
                                <select class="form-select" id="checked_by" style="width:100%">
                                    @if ($sj && $sj->checked_by)
                                        @php $chk = DB::table('users')->where('id',$sj->checked_by)->first(); @endphp
                                        <option value="{{ $sj->checked_by }}" selected>{{ $chk->full_name ?? '' }}
                                        </option>
                                    @endif
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold required">Penyetuju (Approver)</label>
                                <div class="text-muted fs-9 mb-2" id="approver_level_hint">Pilih kategori terlebih dahulu
                                </div>
                                <select class="form-select" id="approved_by" style="width:100%">
                                    @if ($sj && $sj->approved_by)
                                        @php $app = DB::table('users')->where('id',$sj->approved_by)->first(); @endphp
                                        <option value="{{ $sj->approved_by }}" selected>{{ $app->full_name ?? '' }}
                                        </option>
                                    @endif
                                </select>
                            </div>

                            {{-- Approval level table --}}
                            <div class="table-responsive">
                                <table class="table table-bordered matrix-table">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Kategori</th>
                                            <th>Dibuat</th>
                                            <th>Diperiksa</th>
                                            <th>Disetujui</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="cat-A">A</td>
                                            <td>Min. Staff</td>
                                            <td>Min. Dept Head</td>
                                            <td>Min. Direktur</td>
                                        </tr>
                                        <tr>
                                            <td class="cat-B">B</td>
                                            <td>Min. Staff</td>
                                            <td>Min. Dept Head</td>
                                            <td>Min. GM</td>
                                        </tr>
                                        <tr>
                                            <td class="cat-C">C</td>
                                            <td>Min. Staff</td>
                                            <td>Min. Section Head</td>
                                            <td>Min. Asst Dept Head</td>
                                        </tr>
                                        <tr>
                                            <td class="cat-D">D</td>
                                            <td>Min. Staff</td>
                                            <td>Min. Leader</td>
                                            <td>Min. Section Head</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>


                </div>

                {{-- LEFT: Form --}}
                <div class="col-xl-8">
                    <div class="card shadow-sm mb-5">
                        <div class="card-header border-0">
                            <h3 class="card-title fw-bold text-dark fs-6">Informasi Dokumen</h3>
                        </div>
                        <div class="card-body pt-0">
                            <div class="row g-4">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Pilih PO</label>
                                    <select class="form-select" id="po_num" style="width:100%">
                                        @if ($sj && !empty($sj->po_num))
                                            <option value="{{ $sj->po_num }}" selected>{{ $sj->po_num }}</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold required">Departemen</label>
                                    @php
                                        $selectedDepartmentId = (string) ($sj->department_id ?? ($default_department_id ?? ''));
                                    @endphp
                                    <select class="form-select" id="department_id" onchange="onDepartmentChange()">
                                        <option value="">-- Pilih Departemen --</option>
                                        @foreach ($departments as $dept)
                                            <option value="{{ $dept->id }}" data-name="{{ $dept->name }}"
                                                {{ $selectedDepartmentId === (string) $dept->id ? 'selected' : '' }}>
                                                {{ $dept->name }}
                                                @if ($dept->division && $dept->division !== $dept->name)
                                                    ({{ $dept->division }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold required">Tanggal</label>
                                    <input type="date" class="form-control" id="sj_date"
                                        value="{{ $sj ? $sj->sj_date : now('Asia/Jakarta')->format('Y-m-d') }}">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label fw-bold required">Penerima</label>
                                    <input type="text" class="form-control" id="recipient_name"
                                        placeholder="Nama penerima..." value="{{ $sj->recipient_name ?? '' }}">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Alamat Penerima</label>
                                    <input type="text" class="form-control" id="recipient_address"
                                        placeholder="Alamat..." value="{{ $sj->recipient_address ?? '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold required">Ship Via</label>
                                    <select class="form-select " id="ship_via_code" style="width:100%">
                                        @if ($sj && !empty($sj->ship_via_code))
                                            <option value="{{ $sj->ship_via_code }}" selected>{{ $sj->ship_via_code }}</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Nama Driver</label>
                                    <input type="text" class="form-control" id="driver_name"
                                        placeholder="Nama driver..." value="{{ $sj->driver_name ?? '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">No. Polisi</label>
                                    <input type="text" class="form-control" id="plate_num" placeholder="B 1234 XYZ"
                                        value="{{ $sj->plate_num ?? '' }}">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Keterangan</label>
                                    <textarea class="form-control" id="remark" rows="2" placeholder="Keterangan tambahan...">{{ $sj->remark ?? '' }}</textarea>
                                </div>
                            </div>

                            <div class="row g-4 mt-5 align-items-center justify-content-end">
                                <div class="col-md-3">
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-primary" id="btn_save_header"
                                            onclick="saveHeader()">
                                            <span class="spinner-border spinner-border-sm d-none"
                                                id="spinner_save_header"></span>
                                            <i class="fa fa-save fs-6"></i>
                                            Simpan Header
                                        </button>
                                    </div>
                                </div>
                                @if ($mode === 'edit')
                                    <div class="col-md-3">
                                        <div class="d-grid gap-2">
                                            <button type="button" class="btn btn-success" id="btn_send_approval"
                                                onclick="sendForApprovalFromForm()"
                                                {{ ($sj->status_checker ?? '') !== 'DRAFT' ? 'disabled' : '' }}>
                                                <span class="spinner-border spinner-border-sm d-none"
                                                    id="spinner_send_approval"></span>
                                                <i class="fa fa-paper-plane fs-6"></i>
                                                Kirim Approval
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            {{-- Save button --}}


                        </div>

                    </div>

                    {{-- Items Table --}}
                    <div class="card shadow-sm mb-5">
                        <div class="card-header border-0 d-flex justify-content-between align-items-center">
                            <h3 class="card-title fw-bold text-dark fs-6">Detail Part / Barang</h3>
                            <button type="button" class="btn btn-sm btn-light-primary" id="btn_add_detail"
                                onclick="openDetailModal()" {{ $mode === 'create' ? 'disabled' : '' }}>
                                <i class="fa fa-plus fs-7"></i> Tambah Detail
                            </button>
                        </div>
                        <div class="card-body pt-0">
                            @if ($mode === 'create')
                                <div class="alert alert-info py-3 px-4 mb-4">
                                    Simpan header dokumen terlebih dahulu. Setelah itu, setiap detail disimpan per item langsung dari modal.
                                </div>
                            @else
                                <div class="alert alert-light-primary py-3 px-4 mb-4">
                                    Detail disimpan per item dari modal. Perubahan berlaku langsung saat klik Simpan Detail di modal.
                                </div>
                            @endif
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle fs-8" id="detail_table">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" style="width:40px">#</th>
                                            <th style="width:180px">Part Number</th>
                                            <th>Part Name</th>
                                            <th style="width:80px">Qty</th>
                                            <th style="width:70px">UOM</th>
                                            <th style="width:150px">Keterangan</th>
                                            <th style="width:40px"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="detail_tbody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>

    <div class="modal fade" id="detail_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detail_modal_title">Tambah Detail Part / Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="detail_edit_index" value="-1">
                    <div class="alert alert-secondary py-3 px-4">
                        Part Number bisa dipilih dari daftar atau diisi manual.
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Pilih Part </label>
                            <select class="form-select" id="modal_part_select" style="width:100%"></select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">Part Number</label>
                            <input type="text" class="form-control" id="modal_part_num" placeholder="Part Number">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold required">Part Name</label>
                            <input type="text" class="form-control" id="modal_part_name"
                                placeholder="Nama part/barang">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold required">Qty</label>
                            <input type="number" class="form-control text-end" id="modal_qty" min="1"
                                step="1" value="1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">UOM</label>
                            <input type="text" class="form-control" id="modal_uom" placeholder="pcs">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Keterangan</label>
                            <input type="text" class="form-control" id="modal_item_remark" placeholder="Keterangan">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btn_save_detail_modal" onclick="saveDetailFromModal()">
                        <span class="spinner-border spinner-border-sm d-none" id="spinner_save_detail_modal"></span>
                        Simpan Detail
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('submenu')
    {!! $menu_level_1 !!}
@endsection
@section('submenu2')
    {!! $menu_level_2 !!}
@endsection
@section('submenu3')
    {!! $menu_level_3 !!}
@endsection
@section('submenu4')
    {!! $menu_level_4 !!}
@endsection

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    var current_category = '{{ $sj->category ?? '' }}';
    var current_checker_level = null;
    var current_approver_level = null;
    var current_mode = '{{ $mode }}';
    var current_status_checker = '{{ $sj->status_checker ?? '' }}';
    var detailDataTable = null;
    var deleting_detail_index = -1;
    var detail_items = @json($detailItemsJs);

    var return_map = {
        'Dikembalikan ke SAI < 1 bulan': 0,
        'Dikembalikan ke SAI 1 - 3 bulan': 1,
        'Dikembalikan ke SAI 3 - 6 bulan': 2,
        'Dikembalikan ke SAI 6 bulan - 3 th': 3,
        'Dikembalikan ke SAI > 3 tahun': 4,
        'Tidak dikembalikan': 5,
    };
    var value_map = {
        'Bernilai ≤ Rp.500.000': 0,
        'Bernilai ≤ Rp.5.000.000': 1,
        'Bernilai ≤ Rp.50.000.000': 2,
        'Bernilai > Rp.50.000.000': 3,
    };

    $(document).ready(function() {
        initCheckerSelect();
        initApproverSelect();
        initModalPartSelect();
        initPoSelect();
        initShipViaSelect();

        $(document).on('change', '#checked_by, #approved_by', function() {
            enforceDifferentCheckerApprover();
        });

        if (current_category) {
            setCategoryDisplay(current_category);
            highlightMatrix();
        }

        renderDetailTable();
    });

    function enforceDifferentCheckerApprover() {
        var checkerId = $('#checked_by').val();
        var approverId = $('#approved_by').val();

        if (!checkerId || !approverId) {
            return;
        }

        if (checkerId === approverId) {
            $('#approved_by').val(null).trigger('change');
            Swal.fire('Perhatian', 'Pemeriksa dan penyetuju harus user yang berbeda.', 'warning');
        }
    }

    function initPoSelect() {
        if ($('#po_num').hasClass('select2-hidden-accessible')) {
            $('#po_num').select2('destroy');
        }

        $('#po_num').select2({
            placeholder: 'Pilih PO (opsional)',
            allowClear: true,
            width: '100%',
            ajax: {
                url: '{{ route('sj_general.get_open_po') }}',
                type: 'POST',
                data: function(params) {
                    return {
                        _token: '{{ csrf_token() }}',
                        search: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function(data) {
                    return {
                        results: (data.items || []).map(function(i) {
                            return {
                                id: i.id,
                                text: i.text,
                                recipient_name: i.recipient_name || '',
                                recipient_address: i.recipient_address || ''
                            };
                        }),
                        pagination: {
                            more: data.pagination ? data.pagination.more : false
                        }
                    };
                },
                delay: 300,
            },
        }).on('select2:select', function(e) {
            var data = e.params.data || {};
            $('#recipient_name').val(data.recipient_name || '');
            $('#recipient_address').val(data.recipient_address || '');
        });
    }

    function initShipViaSelect() {
        if ($('#ship_via_code').hasClass('select2-hidden-accessible')) {
            $('#ship_via_code').select2('destroy');
        }

        $('#ship_via_code').select2({
            placeholder: 'Pilih Ship Via',
            allowClear: true,
            width: '100%',
            ajax: {
                url: '{{ route('sj_general.get_ship_via_list') }}',
                type: 'POST',
                data: function(params) {
                    return {
                        _token: '{{ csrf_token() }}',
                        search: params.term || ''
                    };
                },
                processResults: function(data) {
                    return {
                        results: (data.items || []).map(function(i) {
                            return {
                                id: i.code || i.id,
                                text: i.text || ((i.code || i.id) + ' - ' + (i.desc || '')),
                            };
                        })
                    };
                },
                delay: 300,
            },
        });
    }

    /* ---- Category ---- */
    function updateCategory() {
        var rs = $('#return_status').val();
        var va = $('#value_aspect').val();
        if (!rs || !va) {
            setCategoryDisplay(null);
            return;
        }
        $.ajax({
            url: '{{ route('sj_general.calculate_category') }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                return_status: rs,
                value_aspect: va
            },
            success: function(res) {
                current_category = res.category;
                setCategoryDisplay(res.category);
                highlightMatrix();
                if (res.levels) {
                    updateApproverDropdowns(res.levels);
                }
            }
        });
    }

    function setCategoryDisplay(cat) {
        var el = $('#category_display');
        el.removeClass('cat-A cat-B cat-C cat-D cat-none');
        if (!cat) {
            el.addClass('cat-none').text('?');
            $('#category_label').text('Pilih return status & value aspect');
            return;
        }
        var labels = {
            A: 'Perlu Disetujui Direktur',
            B: 'Perlu Disetujui GM',
            C: 'Perlu Disetujui Asst Dept Head',
            D: 'Perlu Disetujui Section Head'
        };
        el.addClass('cat-' + cat).text(cat);
        $('#category_label').text(labels[cat] || '');
    }

    function highlightMatrix() {
        var rs = $('#return_status').val();
        var va = $('#value_aspect').val();
        $('.matrix-table td').removeClass('active-cell');
        if (!rs || !va) return;
        var row = return_map[rs];
        var col = value_map[va];
        if (row !== undefined && col !== undefined) {
            $('.matrix-table tbody tr[data-row="' + row + '"] td').eq(col + 1).addClass('active-cell');
        }
    }

    /* ---- Approver level dropdowns ---- */
    function updateApproverDropdowns(levels) {
        current_checker_level = levels.checker;
        current_approver_level = levels.approver;

        var checkerHint = {
            'leader': 'Min. Leader',
            'section head': 'Min. Section Head',
            'dept head': 'Min. Dept Head',
            'asst dept head': 'Min. Asst Dept Head'
        };
        var approverHint = {
            'section head': 'Min. Section Head',
            'asst dept head': 'Min. Asst Dept Head',
            'GM': 'Min. GM',
            'direktur': 'Min. Direktur'
        };

        $('#checker_level_hint').html('<span class="badge badge-light-info">' + (checkerHint[levels.checker] || levels
            .checker) + '</span>');
        $('#approver_level_hint').html('<span class="badge badge-light-success">' + (approverHint[levels.approver] ||
            levels.approver) + '</span>');

        // Destroy & re-init with new level
        $('#checked_by').val(null).trigger('change');
        $('#approved_by').val(null).trigger('change');

        initCheckerSelect(levels.checker);
        initApproverSelect(levels.approver);
    }

    function initCheckerSelect(min_level) {
        if ($('#checked_by').hasClass('select2-hidden-accessible')) {
            $('#checked_by').select2('destroy');
        }
        $('#checked_by').select2({
            placeholder: 'Pilih pemeriksa...',
            allowClear: true,
            width: '100%',
            ajax: {
                url: '{{ route('sj_general.get_users_by_level') }}',
                type: 'POST',
                data: function(params) {
                    return {
                        _token: '{{ csrf_token() }}',
                        search: params.term,
                        min_level: min_level || current_checker_level || 'leader',
                        department_id: $('#department_id').val() || null,
                        page: params.page || 1
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.items.map(i => ({
                            id: i.id,
                            text: i.name
                        })),
                        pagination: {
                            more: data.pagination.more
                        }
                    };
                },
                delay: 300,
            },
        });
    }

    function initApproverSelect(min_level) {
        if ($('#approved_by').hasClass('select2-hidden-accessible')) {
            $('#approved_by').select2('destroy');
        }
        $('#approved_by').select2({
            placeholder: 'Pilih penyetuju...',
            allowClear: true,
            width: '100%',
            ajax: {
                url: '{{ route('sj_general.get_users_by_level') }}',
                type: 'POST',
                data: function(params) {
                    return {
                        _token: '{{ csrf_token() }}',
                        search: params.term,
                        min_level: min_level || current_approver_level || 'section head',
                        department_id: $('#department_id').val() || null,
                        page: params.page || 1
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.items.map(i => ({
                            id: i.id,
                            text: i.name
                        })),
                        pagination: {
                            more: data.pagination.more
                        }
                    };
                },
                delay: 300,
            },
        });
    }

    /* ---- Department change: reset checker/approver if category already set ---- */
    function onDepartmentChange() {
        if (current_category) {
            $('#checked_by').val(null).trigger('change');
            $('#approved_by').val(null).trigger('change');
            initCheckerSelect(current_checker_level);
            initApproverSelect(current_approver_level);
        }
    }

    /* ---- Detail via modal ---- */
    function initDetailDataTable() {
        if ($.fn.DataTable.isDataTable('#detail_table')) {
            detailDataTable = $('#detail_table').DataTable();
            return;
        }

        detailDataTable = $('#detail_table').DataTable({
            data: [],
            paging: false,
            searching: false,
            ordering: false,
            info: false,
            autoWidth: false,
            language: {
                emptyTable: 'Belum ada detail part/barang.'
            },
            columns: [{
                    data: null,
                    className: 'text-center',
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    data: 'part_num',
                    render: function(data) {
                        return data || '-';
                    }
                },
                {
                    data: 'part_name',
                    render: function(data) {
                        return data || '-';
                    }
                },
                {
                    data: 'qty',
                    className: 'text-end',
                    render: function(data) {
                        var n = parseFloat(data);
                        return isFinite(n) ? n : 0;
                    }
                },
                {
                    data: 'uom',
                    render: function(data) {
                        return data || '-';
                    }
                },
                {
                    data: 'item_remark',
                    render: function(data) {
                        return data || '-';
                    }
                },
                {
                    data: null,
                    className: 'text-center',
                    render: function(data, type, row, meta) {
                        var isDeleting = deleting_detail_index === meta.row;
                        var deleteButton = isDeleting ?
                            '<button type="button" class="btn btn-icon btn-sm btn-light-danger" disabled><span class="spinner-border spinner-border-sm"></span></button>' :
                            '<button type="button" class="btn btn-icon btn-sm btn-light-danger" onclick="removeDetail(' +
                            meta.row +
                            ')"><i class="fa fa-times fs-7"></i></button>';

                        return '<button type="button" class="btn btn-icon btn-sm btn-light-warning me-1" onclick="editDetail(' +
                            meta.row +
                            ')"><i class="fa fa-edit fs-7"></i></button>' +
                            deleteButton;
                    }
                }
            ]
        });
    }

    function initModalPartSelect() {
        $('#modal_part_select').select2({
            placeholder: 'Cari part number...',
            allowClear: true,
            width: '100%',
            ajax: {
                url: '{{ route('sj_general.get_parts') }}',
                type: 'POST',
                data: function(params) {
                    return {
                        _token: '{{ csrf_token() }}',
                        search: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.items.map(i => ({
                            id: i.id,
                            text: i.name,
                            desc: i.desc
                        })),
                        pagination: {
                            more: data.pagination.more
                        }
                    };
                },
                delay: 400,
            },
        }).on('select2:select', function(e) {
            $('#modal_part_num').val(e.params.data.id || '');
            $('#modal_part_name').val(e.params.data.desc || '');
        }).on('select2:clear', function() {
            $('#modal_part_num').val('');
            $('#modal_part_name').val('');
        });
    }

    function renderDetailTable() {
        if (!detailDataTable) {
            initDetailDataTable();
        }

        detailDataTable.clear();

        if (detail_items.length > 0) {
            detailDataTable.rows.add(detail_items);
        }

        detailDataTable.draw();
    }

    function openDetailModal() {
        if (current_mode === 'create') {
            Swal.fire('Perhatian', 'Simpan header terlebih dahulu sebelum menambah detail.', 'warning');
            return;
        }

        $('#detail_modal_title').text('Tambah Detail Part / Barang');
        $('#detail_edit_index').val(-1);
        $('#modal_part_select').val(null).trigger('change');
        $('#modal_part_num').val('');
        $('#modal_part_name').val('');
        $('#modal_qty').val(1);
        $('#modal_uom').val('');
        $('#modal_item_remark').val('');

        $('#detail_modal').modal('show');
    }

    function editDetail(index) {
        if (deleting_detail_index >= 0) {
            return;
        }

        var item = detail_items[index];
        if (!item) return;

        $('#detail_modal_title').text('Edit Detail Part / Barang');
        $('#detail_edit_index').val(index);
        $('#modal_part_select').val(null).trigger('change');
        $('#modal_part_num').val(item.part_num || '');
        $('#modal_part_name').val(item.part_name || '');
        $('#modal_qty').val(item.qty || 1);
        $('#modal_uom').val(item.uom || '');
        $('#modal_item_remark').val(item.item_remark || '');

        $('#detail_modal').modal('show');
    }

    function saveDetailFromModal() {
        if (current_mode === 'create') {
            Swal.fire('Perhatian', 'Simpan header terlebih dahulu sebelum menyimpan detail.', 'warning');
            return;
        }

        if (current_status_checker !== 'DRAFT') {
            Swal.fire('Perhatian', 'Detail tidak bisa diubah karena dokumen sudah dikirim approval.', 'warning');
            return;
        }

        var partNum = ($('#modal_part_num').val() || '').trim();
        var partName = ($('#modal_part_name').val() || '').trim();
        var qty = parseFloat($('#modal_qty').val());

        if (!partNum) {
            Swal.fire('Perhatian', 'Part Number wajib diisi.', 'warning');
            return;
        }

        if (!partName) {
            Swal.fire('Perhatian', 'Part Name wajib diisi.', 'warning');
            return;
        }

        if (!isFinite(qty) || qty <= 0) {
            Swal.fire('Perhatian', 'Qty harus lebih dari 0.', 'warning');
            return;
        }

        var newItem = {
            part_num: partNum,
            part_name: partName,
            qty: qty,
            uom: ($('#modal_uom').val() || '').trim(),
            item_remark: ($('#modal_item_remark').val() || '').trim(),
        };

        var editIndex = parseInt($('#detail_edit_index').val(), 10);
        var currentItem = (isFinite(editIndex) && editIndex >= 0) ? (detail_items[editIndex] || null) : null;
        var detailId = currentItem && currentItem.detail_id ? currentItem.detail_id : '';

        $('#spinner_save_detail_modal').removeClass('d-none');
        $('#btn_save_detail_modal').prop('disabled', true);

        $.ajax({
            url: '{{ route('sj_general.save_detail') }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                enc_id: '{{ $enc_id ?? '' }}',
                detail_id: detailId,
                part_num: newItem.part_num,
                part_name: newItem.part_name,
                qty: newItem.qty,
                uom: newItem.uom,
                item_remark: newItem.item_remark,
            },
            success: function(res) {
                $('#spinner_save_detail_modal').addClass('d-none');
                $('#btn_save_detail_modal').prop('disabled', false);

                if (res.process_status != 200) {
                    Swal.fire('Gagal', res.msg_process, 'error');
                    return;
                }

                var savedItem = res.item || newItem;
                if (isFinite(editIndex) && editIndex >= 0) {
                    detail_items[editIndex] = savedItem;
                } else {
                    detail_items.push(savedItem);
                }

                $('#detail_modal').modal('hide');
                renderDetailTable();
                Swal.fire('Berhasil', res.msg_process, 'success');
            },
            error: function() {
                $('#spinner_save_detail_modal').addClass('d-none');
                $('#btn_save_detail_modal').prop('disabled', false);
                Swal.fire('Error', 'Terjadi kesalahan server.', 'error');
            }
        });
    }

    function removeDetail(index) {
        if (deleting_detail_index >= 0) {
            return;
        }

        if (current_status_checker !== 'DRAFT') {
            Swal.fire('Perhatian', 'Detail tidak bisa diubah karena dokumen sudah dikirim approval.', 'warning');
            return;
        }

        Swal.fire({
            title: 'Hapus detail ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
        }).then(function(result) {
            if (!result.isConfirmed) return;

            var item = detail_items[index] || {};

            if (!item.detail_id) {
                detail_items.splice(index, 1);
                renderDetailTable();
                return;
            }

            deleting_detail_index = index;
            renderDetailTable();

            $.ajax({
                url: '{{ route('sj_general.save_detail') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    enc_id: '{{ $enc_id ?? '' }}',
                    mode: 'delete',
                    detail_id: item.detail_id,
                },
                success: function(res) {
                    deleting_detail_index = -1;
                    if (res.process_status == 200) {
                        detail_items.splice(index, 1);
                        renderDetailTable();
                        Swal.fire('Berhasil', res.msg_process, 'success');
                    } else {
                        renderDetailTable();
                        Swal.fire('Gagal', res.msg_process, 'error');
                    }
                },
                error: function() {
                    deleting_detail_index = -1; 
                    renderDetailTable();
                    Swal.fire('Error', 'Terjadi kesalahan server.', 'error');
                }
            });
        });
    }

    /* ---- Save header only ---- */
    function saveHeader() {
        var rs = $('#return_status').val();
        var va = $('#value_aspect').val();
        if (!$('#department_id').val()) {
            Swal.fire('Perhatian', 'Departemen wajib dipilih.', 'warning');
            return;
        }
        if (!rs || !va) {
            Swal.fire('Perhatian', 'Return Status dan Value Aspect wajib diisi.', 'warning');
            return;
        }
        if (!$('#recipient_name').val().trim()) {
            Swal.fire('Perhatian', 'Nama penerima wajib diisi.', 'warning');
            return;
        }
        if (!$('#checked_by').val()) {
            Swal.fire('Perhatian', 'Pemeriksa wajib dipilih.', 'warning');
            return;
        }
        if (!$('#approved_by').val()) {
            Swal.fire('Perhatian', 'Penyetuju wajib dipilih.', 'warning');
            return;
        }
        if ($('#checked_by').val() && $('#approved_by').val() && $('#checked_by').val() === $('#approved_by')
        ) {
            Swal.fire('Perhatian', 'Pemeriksa dan penyetuju harus user yang berbeda.', 'warning');
            return;
        }
        if(!$('#ship_via_code').val()) {
            Swal.fire('Perhatian', 'Ship Via wajib dipilih.', 'warning');
            return;
        }

        $('#spinner_save_header').removeClass('d-none');
        $('#btn_save_header').prop('disabled', true);

        var is_edit = '{{ $mode }}' === 'edit';
        var url = is_edit ? '{{ route('sj_general.update') }}' : '{{ route('sj_general.store') }}';
        
        var postData = {
            _token: '{{ csrf_token() }}',
            sj_date: $('#sj_date').val(),
            po_num: $('#po_num').val(),
            ship_via_code: $('#ship_via_code').val(),
            return_status: rs,
            value_aspect: va,
            remark: $('#remark').val(),
            recipient_name: $('#recipient_name').val(),
            recipient_address: $('#recipient_address').val(),
            driver_name: $('#driver_name').val(),
            plate_num: $('#plate_num').val(),
            department_id: $('#department_id').val(),
            department_name: $('#department_id option:selected').data('name') || '',
            checked_by: $('#checked_by').val(),
            approved_by: $('#approved_by').val(),
        };

        if (is_edit) {
            postData.enc_id = '{{ $enc_id ?? '' }}';
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: postData,
            success: function(res) {
                $('#spinner_save_header').addClass('d-none');
                $('#btn_save_header').prop('disabled', false);
                if (res.process_status == 200) {
                    if (is_edit) {
                        Swal.fire('Berhasil', res.msg_process, 'success');
                    } else {
                        Swal.fire('Berhasil',
                                'Header berhasil disimpan. Silakan lanjut tambah detail part/barang lalu simpan detail.',
                                'success')
                            .then(() => {
                                if (res.enc_id) {
                                    window.location.href = '{{ url('sj_general/edit') }}?id=' + res
                                        .enc_id;
                                } else {
                                    window.location.href = '{{ url('sj_general') }}';
                                }
                            });
                    }
                } else {
                    Swal.fire('Gagal', res.msg_process, 'error');
                }
            },
            error: function() {
                $('#spinner_save_header').addClass('d-none');
                $('#btn_save_header').prop('disabled', false);
                Swal.fire('Error', 'Terjadi kesalahan server.', 'error');
            }
        });
    }

    function sendForApprovalFromForm() {
        if (current_mode !== 'edit') {
            Swal.fire('Perhatian', 'Simpan header terlebih dahulu sebelum kirim approval.', 'warning');
            return;
        }

        if (current_status_checker !== 'DRAFT') {
            Swal.fire('Perhatian', 'Dokumen ini sudah tidak berstatus DRAFT.', 'warning');
            return;
        }

        Swal.fire({
            title: 'Kirim untuk Approval?',
            text: 'Status checker/approver akan berubah menjadi PENDING.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Kirim',
        }).then(function(result) {
            if (!result.isConfirmed) return;

            $('#spinner_send_approval').removeClass('d-none');
            $('#btn_send_approval').prop('disabled', true);

            $.ajax({
                url: '{{ route('sj_general.submit_review') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    enc_id: '{{ $enc_id ?? '' }}',
                },
                success: function(res) {
                    $('#spinner_send_approval').addClass('d-none');
                    if (res.process_status == 200) {
                        Swal.fire('Berhasil', res.msg_process, 'success').then(function() {
                            window.location.href = '{{ url('sj_general') }}';
                        });
                    } else {
                        $('#btn_send_approval').prop('disabled', false);
                        Swal.fire('Gagal', res.msg_process, 'error');
                    }
                },
                error: function() {
                    $('#spinner_send_approval').addClass('d-none');
                    $('#btn_send_approval').prop('disabled', false);
                    Swal.fire('Error', 'Terjadi kesalahan server.', 'error');
                }
            });
        });
    }
</script>
