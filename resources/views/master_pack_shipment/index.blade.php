@extends('../layouts/app')

@section('subhead')
    <title>{{ $head_title }}</title>
@endsection

<script src="<?= env('APP_ASSETS') ?>assets/js/jquery/jquery.min.js"></script>

@section('subcontent')

{{-- ═══════════════════════════════════════════════════════════════════ --}}
{{-- TOOLBAR                                                            --}}
{{-- ═══════════════════════════════════════════════════════════════════ --}}
<div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
        <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
            data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
            class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
            <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">{{ $head_title }}
                <span class="h-20px border-gray-200 border-start ms-3 mx-2"></span>
                <small class="text-muted fs-7 fw-bold my-1 ms-1">#{{ auth()->user()->full_name }}</small>
            </h1>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════ --}}
{{-- TAB NAVIGATION (hidden)                                             --}}
{{-- ═══════════════════════════════════════════════════════════════════ --}}
<div hidden>
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <a id="tab_home_btn" class="nav-link active" data-bs-toggle="tab" href="#tab_home" role="tab">Home</a>
        </li>
        <li class="nav-item">
            <a id="tab_form_btn" class="nav-link" data-bs-toggle="tab" href="#tab_form" role="tab">Form</a>
        </li>
    </ul>
</div>

{{-- ═══════════════════════════════════════════════════════════════════ --}}
{{-- CONTENT AREA                                                        --}}
{{-- ═══════════════════════════════════════════════════════════════════ --}}
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
<div class="tab-content">

    {{-- ─── HOME / LIST TAB ──────────────────────────────────────────── --}}
    <div id="tab_home" class="card-body p-0 tab-pane fade show active" role="tabpanel">
        <div class="post d-flex flex-column-fluid mb-5" id="kt_post">
            <div id="kt_content_container" class="container-xxl">

                {{-- Summary Cards --}}
                <div class="row g-2 g-xl-8 mb-5">
                    <div class="col-xl-4 col-lg-4 col-sm-4">
                        <a href="#" onclick="filterByStatus(0, this);"
                            class="card bgi-no-repeat card-xl-stretch card-front"
                            style="background-position:right top;background-size:30% auto;
                                   background-image:url(<?= env('APP_ASSETS') ?>assets/media/svg/shapes/abstract-4.svg)">
                            <div class="card-body">
                                <div class="text-gray-900 fw-bolder fs-4 mb-2 mt-2" id="card_total">-</div>
                                <div class="fw-bold text-gray-900">All Documents</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-sm-4">
                        <a href="#" onclick="filterByStatus(1, this);"
                            class="card bgi-no-repeat card-xl-stretch card-front"
                            style="background-position:right top;background-size:30% auto;
                                   background-image:url(<?= env('APP_ASSETS') ?>assets/media/svg/shapes/abstract-2.svg)">
                            <div class="card-body">
                                <div class="text-gray-900 fw-bolder fs-4 mb-2 mt-2" id="card_draft">-</div>
                                <div class="fw-bold text-gray-900">Draft</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-sm-4">
                        <a href="#" onclick="filterByStatus(2, this);"
                            class="card bgi-no-repeat card-xl-stretch card-front"
                            style="background-position:right top;background-size:30% auto;
                                   background-image:url(<?= env('APP_ASSETS') ?>assets/media/svg/shapes/abstract-1.svg)">
                            <div class="card-body">
                                <div class="text-gray-900 fw-bolder fs-4 mb-2 mt-2" id="card_submitted">-</div>
                                <div class="fw-bold text-gray-900">Submitted</div>
                            </div>
                        </a>
                    </div>
                </div>

                {{-- DataTable Card --}}
                <div class="card col-xxl-12 card-sticky">
                    <div class="card-header border-1 pt-6 pb-6">
                        <div class="card-title">
                            <div class="d-flex align-items-center position-relative my-1">
                                <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black"/>
                                        <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black"/>
                                    </svg>
                                </span>
                                <input type="text" id="front_table_search"
                                    class="form-control form-control-solid w-250px ps-15 text-sm form-control-sm"
                                    placeholder="Search Customer / ShipVia / Packing No." />
                            </div>
                        </div>
                        <div class="card-toolbar">
                            {{-- Filter --}}
                            <button type="button" class="btn btn-light-primary btn-sm me-3"
                                data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black"/>
                                    </svg>
                                </span>
                                Filter
                            </button>
                            <div class="menu menu-sub menu-sub-dropdown w-250px" data-kt-menu="true">
                                <div class="px-7 py-5">
                                    <div class="fs-5 text-dark fw-bolder">Filter Options</div>
                                </div>
                                <div class="separator border-gray-200"></div>
                                <div class="px-7 py-5">
                                    <label class="form-label fw-bold">Status:</label>
                                    <select class="form-select form-select-solid form-select-sm" id="status_filter">
                                        <option value="0">All</option>
                                        <option value="1">Draft</option>
                                        <option value="2">Submitted</option>
                                    </select>
                                    <div class="d-flex justify-content-end mt-4">
                                        <button type="button" class="btn btn-primary btn-sm"
                                            data-kt-menu-dismiss="true"
                                            onclick="$('#kt_doc_table').DataTable().ajax.reload();">
                                            Apply
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Create button --}}
                            <button type="button" class="btn btn-light-primary btn-sm" onclick="createDocument()">
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"/>
                                        <rect fill="#000000" opacity="0.3"
                                            transform="translate(12,12) rotate(-270) translate(-12,-12)"
                                            x="4" y="11" width="16" height="2" rx="1"/>
                                    </svg>
                                </span>
                                Create
                            </button>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <table class="table align-middle table-row-dashed table-striped gy-2 fs-7" id="kt_doc_table">
                            <thead>
                                <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                    <th class="min-w-20px">No</th>
                                    <th class="min-w-30px">Open</th>
                                    <th class="min-w-120px">Packing List No.</th>
                                    <th class="min-w-120px">Customer</th>
                                    <th class="min-w-80px">Ship Via</th>
                                    <th class="min-w-100px">Nopol / Truck</th>
                                    <th class="min-w-80px">Created</th>
                                    <th class="min-w-60px">Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ─── FORM / EDIT TAB ──────────────────────────────────────────── --}}
    <div id="tab_form" class="card-body p-0 tab-pane fade" role="tabpanel">
        <div class="d-flex flex-column-fluid">
            <div id="kt_content_container" class="container-xxl">

                {{-- Back button --}}
                <div class="mb-4">
                    <button type="button" class="btn btn-light btn-sm" onclick="showHomeTab()">
                        <span class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path opacity="0.5" d="M14.2657 11.4343L18.45 7.25C18.8642 6.83579 18.8642 6.16421 18.45 5.75C18.0358 5.33579 17.3642 5.33579 16.95 5.75L11.4071 11.2929C11.0166 11.6834 11.0166 12.3166 11.4071 12.7071L16.95 18.25C17.3642 18.6642 18.0358 18.6642 18.45 18.25C18.8642 17.8358 18.8642 17.1642 18.45 16.75L14.2657 12.5657C13.9533 12.2533 13.9533 11.7467 14.2657 11.4343Z" fill="black"/>
                                <path d="M8.2657 11.4343L12.45 7.25C12.8642 6.83579 12.8642 6.16421 12.45 5.75C12.0358 5.33579 11.3642 5.33579 10.95 5.75L5.40712 11.2929C5.01659 11.6834 5.01659 12.3166 5.40712 12.7071L10.95 18.25C11.3642 18.6642 12.0358 18.6642 12.45 18.25C12.8642 17.8358 12.8642 17.1642 12.45 16.75L8.2657 12.5657C7.95328 12.2533 7.95328 11.7467 8.2657 11.4343Z" fill="black"/>
                            </svg>
                        </span>
                        Kembali ke List
                    </button>
                </div>

                <div class="row g-4">

                    {{-- ── LEFT: Header Form ─────────────────────────────── --}}
                    <div class="col-xl-4 col-lg-5">
                        <div class="card card-flush mb-4">
                            <div class="card-header">
                                <div class="card-title fw-bolder fs-6">Header Packing List</div>
                                <div class="card-toolbar">
                                    <span id="badge_status_form" class="badge badge-light-warning">Draft</span>
                                </div>
                            </div>
                            <div class="card-body pt-3">
                                <input type="hidden" id="form_doc_id" value="0" />

                                {{-- Nomor Packing List (read-only, muncul setelah submit) --}}
                                <div class="mb-4" id="div_packing_list_num" style="display:none">
                                    <label class="form-label fw-bold fs-7">Packing List No.</label>
                                    <input type="text" id="form_packing_list_num"
                                        class="form-control form-control-sm form-control-solid"
                                        readonly />
                                </div>

                                {{-- Customer (read-only, auto-fill dari scan surat jalan pertama) --}}
                                <div class="mb-4">
                                    <label class="form-label fw-bold fs-7">Customer</label>
                                    <input type="text" id="form_customer_name"
                                        class="form-control form-control-sm form-control-solid"
                                        placeholder="Otomatis terisi saat scan surat jalan pertama"
                                        readonly />
                                    <input type="hidden" id="form_cust_id" />
                                </div>

                                {{-- Ship Via Code --}}
                                <div class="mb-4">
                                    <label class="form-label fw-bold fs-7">Ship Via Code</label>
                                    <select class="form-select form-select-sm form-select-solid" id="form_ship_via_code">
                                        <option value=""></option>
                                    </select>
                                </div>

                                {{-- Trucking (Nopol / Driver) --}}
                                <div class="mb-4" id="div_trucking_select">
                                    <label class="form-label fw-bold fs-7">Truck / Nopol</label>
                                    <select class="form-select form-select-sm form-select-solid" id="form_trucking_id">
                                        <option value=""></option>
                                    </select>
                                    <div id="truck_info" class="mt-2 text-muted fs-8" style="display:none">
                                        <span id="truck_info_driver"></span>
                                        <span id="truck_info_noTlp"></span>
                                        <span id="truck_info_jenis"></span>
                                    </div>
                                </div>

                                {{-- Manual Nopol / Driver (dipakai jika Ship Via bukan DPK) --}}
                                <div class="mb-3" id="div_manual_nopol" style="display:none">
                                    <label class="form-label fw-bold fs-7">Nopol / No. Kendaraan</label>
                                    <input type="text" id="form_manual_nopol"
                                        class="form-control form-control-sm form-control-solid"
                                        placeholder="Contoh: B 1234 ABC" maxlength="100" />
                                </div>
                                <div class="mb-4" id="div_manual_driver" style="display:none">
                                    <label class="form-label fw-bold fs-7">Nama Driver</label>
                                    <input type="text" id="form_manual_driver"
                                        class="form-control form-control-sm form-control-solid"
                                        placeholder="Nama driver" maxlength="200" />
                                </div>

                                <div class="mb-2 text-muted fs-8" id="form_created_info" style="display:none">
                                    <span id="form_created_by_label"></span>
                                </div>

                                <div class="d-flex gap-2 mt-5">
                                    <button type="button" class="btn btn-primary btn-sm"
                                        id="btn_save_header" onclick="saveHeader()">
                                        <span id="spinner_save_header"
                                            class="spinner-border spinner-border-sm me-2" style="display:none"></span>
                                        Simpan
                                    </button>
                                    <button type="button" class="btn btn-success btn-sm"
                                        id="btn_submit_document" onclick="submitDocument()" style="display:none">
                                        <span id="spinner_submit"
                                            class="spinner-border spinner-border-sm me-2" style="display:none"></span>
                                        Submit
                                    </button>
                                    <button type="button" class="btn btn-warning btn-sm"
                                        id="btn_unsubmit_document" onclick="unsubmitDocument()" style="display:none">
                                        Unsubmit
                                    </button>
                                    <button type="button" class="btn btn-light-danger btn-sm"
                                        id="btn_delete_document" onclick="deleteDocument()" style="display:none">
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Print PDF Card --}}
                        <div class="card card-flush" id="card_print" style="display:none">
                            <div class="card-body py-4">
                                <button type="button" class="btn btn-light-info btn-sm w-100"
                                    onclick="printPdf()">
                                    <span class="svg-icon svg-icon-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3" d="M4.25 4.25C4.25 3 5.25 2 6.5 2H17.5C18.75 2 19.75 3 19.75 4.25V17.5C19.75 18.75 18.75 19.75 17.5 19.75H6.5C5.25 19.75 4.25 18.75 4.25 17.5V4.25Z" fill="black"/>
                                            <path d="M5 22H19C19.6 22 20 21.6 20 21V20H4V21C4 21.6 4.4 22 5 22Z" fill="black"/>
                                            <path d="M4 16H20V8H4V16ZM8 11C8 10.4 8.4 10 9 10H15C15.6 10 16 10.4 16 11C16 11.6 15.6 12 15 12H9C8.4 12 8 11.6 8 11Z" fill="black"/>
                                        </svg>
                                    </span>
                                    Cetak PDF
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- ── RIGHT: QR Scan + Detail Table ────────────────── --}}
                    <div class="col-xl-8 col-lg-7">
                        <div class="card card-flush mb-4" id="card_scan_section" style="display:none">
                            <div class="card-header">
                                <div class="card-title fw-bolder fs-6">Scan QR Code Surat Jalan</div>
                            </div>
                            <div class="card-body pt-3">
                                <div class="d-flex align-items-center gap-3">
                                    <input type="number" id="qr_input"
                                        class="form-control form-control-sm form-control-solid w-200px"
                                        placeholder="PackNum..."
                                        min="1" autocomplete="off" />
                                    <button type="button" class="btn btn-success btn-sm" onclick="processScan()">
                                        <span id="spinner_scan"
                                            class="spinner-border spinner-border-sm me-2" style="display:none"></span>
                                        Scan / Tambah
                                    </button>
                                    <small class="text-muted">Atau tekan <kbd>Enter</kbd> setelah scan</small>
                                </div>
                                <div id="scan_feedback" class="mt-3" style="display:none"></div>
                            </div>
                        </div>

                        <div class="card card-flush">
                            <div class="card-header pt-5 pb-4">
                                <div class="card-title fw-bolder fs-6">
                                    Detail Surat Jalan
                                    <span class="badge badge-light-primary ms-2" id="badge_detail_count">0</span>
                                </div>
                                <div class="card-toolbar">
                                    <input type="text" id="detail_table_search"
                                        class="form-control form-control-sm form-control-solid w-150px"
                                        placeholder="Cari PackNum / SJ..." />
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <table class="table align-middle table-row-dashed table-striped gy-2 fs-7"
                                    id="kt_detail_table">
                                    <thead>
                                        <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase">
                                            <th class="min-w-20px">No</th>
                                            <th class="min-w-80px">PackNum</th>
                                            <th class="min-w-120px">No. Surat Jalan</th>
                                            <th class="min-w-100px">PONum</th>
                                            <th class="min-w-40px">Aksi</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════ --}}
{{-- SCRIPTS                                                             --}}
{{-- ═══════════════════════════════════════════════════════════════════ --}}
<script>
'use strict';

var currentDocId    = 0;
var currentDocIsSubmitted = 0;
var frontTable      = null;
var detailTable     = null;

// ── Init ──────────────────────────────────────────────────────────────────
$(document).ready(function () {
    initFrontTable();
    initShipViaSelect2();
    initTruckingSelect2();

    // URL param: open a doc directly or show home tab
    var ref_doc = new URLSearchParams(window.location.search).get('ref_doc');
    if (ref_doc) {
        openDocument(ref_doc);
    } else {
        showHomeTab();
    }

    // Search front table on input
    $('#front_table_search').on('keyup', function () {
        frontTable.ajax.reload();
    });

    // Search detail table on input
    $('#detail_table_search').on('keyup', function () {
        if (detailTable) detailTable.ajax.reload();
    });

    // QR input: trigger scan on Enter
    $('#qr_input').on('keypress', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            processScan();
        }
    });
});

// ── Load summary cards ────────────────────────────────────────────────────
function loadCountDocument() {
    $.post('{{ route("mps.count_document") }}', {
        _token: '{{ csrf_token() }}'
    }, function (res) {
        $('#card_total').text(res.total);
        $('#card_draft').text(res.draft);
        $('#card_submitted').text(res.submitted);
    }, 'json');
}

// ── Filter by card click ──────────────────────────────────────────────────
function filterByStatus(status, el) {
    $('#status_filter').val(status);
    frontTable.ajax.reload();
}

// ── Front DataTable ───────────────────────────────────────────────────────
function initFrontTable() {
    frontTable = $('#kt_doc_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("mps.front_table") }}',
            type: 'POST',
            data: function (d) {
                d._token        = '{{ csrf_token() }}';
                d.front_table_search = $('#front_table_search').val();
                d.status_id     = $('#status_filter').val();
            }
        },
        columns: [
            { data: 'no',             orderable: false },
            { data: 'action',         orderable: false },
            { data: 'PackingListNum', orderable: false },
            { data: 'CustomerName',   orderable: false },
            { data: 'ShipViaCode',    orderable: false },
            { data: 'TruckNopol',     orderable: false },
            { data: 'CreatedAt',      orderable: false },
            { data: 'status',         orderable: false },
        ],
        order: [[0, 'desc']],
        pageLength: 20,
        dom: 'tip',
        language: { processing: '<span class="spinner-border spinner-border-sm"></span>' }
    });
}

// ── Detail DataTable ──────────────────────────────────────────────────────
function initDetailTable(doc_id) {
    if (detailTable) {
        detailTable.destroy();
        $('#kt_detail_table').empty();
    }

    detailTable = $('#kt_detail_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("mps.detail_table") }}',
            type: 'POST',
            data: function (d) {
                d._token              = '{{ csrf_token() }}';
                d.doc_id              = doc_id;
                d.detail_table_search = $('#detail_table_search').val();
            }
        },
        columns: [
            { data: 'no',          orderable: false },
            { data: 'PackNum',     orderable: false },
            { data: 'LegalNumber', orderable: false },
            { data: 'PONum',       orderable: false },
            { data: 'action',      orderable: false },
        ],
        order: [[0, 'asc']],
        pageLength: 50,
        dom: 'tip',
        drawCallback: function (settings) {
            var info = settings.json;
            if (info) $('#badge_detail_count').text(info.recordsTotal);
        },
        language: { processing: '<span class="spinner-border spinner-border-sm"></span>' }
    });
}

// ── Trucking – Select2 with AJAX ─────────────────────────────────────────
function initTruckingSelect2() {
    $('#form_trucking_id').select2({
        placeholder: 'Pilih Truck / Nopol',
        allowClear: true,
        minimumInputLength: 0,
        ajax: {
            url: '{{ route("mps.trucking_list") }}',
            type: 'POST',
            dataType: 'json',
            data: function (params) {
                return { _token: '{{ csrf_token() }}', search: params.term || '' };
            },
            processResults: function (res) {
                if (res.status != 1) return { results: [] };
                return {
                    results: $.map(res.data, function (item) {
                        return { id: item.id, text: item.text, nopol: item.nopol, driver: item.driver, noTlp: item.noTlp, jenis: item.jenis };
                    })
                };
            },
            cache: true
        }
    }).on('select2:select', function (e) {
        updateTruckInfo(e.params.data);
    }).on('select2:clear', function () {
        $('#truck_info').hide();
    });
}

function updateTruckInfo(data) {
    if (!data || !data.driver) { $('#truck_info').hide(); return; }
    var parts = [];
    if (data.driver) parts.push('Driver: ' + data.driver);
    if (data.noTlp)  parts.push('No. Tlp: ' + data.noTlp);
    if (data.jenis)  parts.push('(' + data.jenis + ')');
    $('#truck_info_driver').text(parts.join('  |  '));
    $('#truck_info_noTlp').text('');
    $('#truck_info_jenis').text('');
    $('#truck_info').show();
}

// ── Ship Via Code – Select2 with AJAX ─────────────────────────────────────
function initShipViaSelect2() {
    $('#form_ship_via_code').select2({
        placeholder: 'Pilih Ship Via',
        allowClear: false,
        minimumInputLength: 0,
        ajax: {
            url: '{{ route("mps.ship_via_list") }}',
            type: 'POST',
            dataType: 'json',
            data: function (params) {
                return { _token: '{{ csrf_token() }}', search: params.term || '' };
            },
            processResults: function (res) {
                if (res.status != 1) return { results: [] };
                return {
                    results: $.map(res.data, function (item) {
                        return { id: item.code, text: item.code + ' - ' + item.desc };
                    })
                };
            },
            cache: true
        }
    }).on('select2:select', function (e) {
        onShipViaChange(e.params.data.id);
    }).on('select2:clear', function () {
        onShipViaChange('');
    });
}

// ── Toggle truck section based on Ship Via ────────────────────────────────
function onShipViaChange(code) {
    var isDPK = (code.toUpperCase() === 'DPK');
    $('#div_trucking_select').toggle(isDPK);
    $('#div_manual_nopol').toggle(!isDPK);
    $('#div_manual_driver').toggle(!isDPK);
    if (isDPK) {
        $('#form_manual_nopol').val('');
        $('#form_manual_driver').val('');
    } else {
        $('#form_trucking_id').find('option:not([value=""])').remove();
        $('#form_trucking_id').val(null).trigger('change');
        $('#truck_info').hide();
    }
}

// ── Tab helpers ───────────────────────────────────────────────────────────
function showHomeTab() {
    $('#tab_home_btn').tab('show');
    loadCountDocument();
    frontTable.ajax.reload();
}

function showFormTab() {
    $('#tab_form_btn').tab('show');
}

// ── Create new document ───────────────────────────────────────────────────
function createDocument() {
    currentDocId          = 0;
    currentDocIsSubmitted = 0;

    resetHeaderForm();
    $('#badge_status_form').text('New').removeClass('badge-light-success badge-light-warning').addClass('badge-light-info');
    $('#btn_delete_document').hide();
    $('#card_scan_section').hide();
    $('#card_print').hide();
    setFormEditable(true);

    showFormTab();
    setTimeout(function () { $('#form_ship_via_code').select2('open'); }, 300);
}

// ── Open existing document ────────────────────────────────────────────────
function openDocument(trc_unix_id) {
    $.post('{{ route("mps.get_document_data") }}', {
        _token:       '{{ csrf_token() }}',
        trc_unix_id:  trc_unix_id
    }, function (res) {
        if (res.ref_tab !== 1) {
            Swal.fire('Error', res.error || 'Dokumen tidak ditemukan', 'error');
            return;
        }

        currentDocId          = res.id;
        currentDocIsSubmitted = res.is_submitted;

        $('#form_doc_id').val(res.id);
        // Set Trucking Select2
        var $truck = $('#form_trucking_id');
        $truck.find('option:not([value=""])').remove();
        $truck.val(null).trigger('change');
        if (res.TruckingID) {
            var truckText = res.TruckingNopol + (res.TruckingDriver ? ' - ' + res.TruckingDriver : '');
            $truck.append(new Option(truckText, res.TruckingID, true, true)).trigger('change');
            updateTruckInfo({ driver: res.TruckingDriver, noTlp: res.TruckingNoTlp, jenis: res.TruckingJenis });
        } else {
            $('#truck_info').hide();
        }
        // Set Ship Via Code
        var $shipVia = $('#form_ship_via_code');
        $shipVia.val(null).trigger('change');
        if (res.ShipViaCode) {
            $shipVia.append(new Option(res.ShipViaCode, res.ShipViaCode, true, true)).trigger('change');
        }
        onShipViaChange(res.ShipViaCode || '');
        // Restore manual fields if non-DPK
        if (res.ShipViaCode && res.ShipViaCode.toUpperCase() !== 'DPK') {
            $('#form_manual_nopol').val(res.ManualNopol || '');
            $('#form_manual_driver').val(res.ManualDriver || '');
        }
        // Customer (read-only, from Epicor)
        $('#form_customer_name').val(res.CustomerName || '');
        $('#form_cust_id').val(res.CustID || '');
        // Packing List Number
        if (res.PackingListNum) {
            $('#form_packing_list_num').val(res.PackingListNum);
            $('#div_packing_list_num').show();
        } else {
            $('#div_packing_list_num').hide();
        }

        if (res.CreatedBy) {
            $('#form_created_by_label').text('Dibuat oleh: ' + res.CreatedBy + ' | ' + res.CreatedAt);
            $('#form_created_info').show();
        }

        if (res.is_submitted) {
            $('#badge_status_form').text('Submitted')
                .removeClass('badge-light-warning badge-light-info')
                .addClass('badge-light-success');
            setFormEditable(false);
            $('#btn_delete_document').hide();
            $('#btn_submit_document').hide();
            $('#btn_unsubmit_document').show();
            $('#card_scan_section').hide();
            $('#card_print').show();
        } else {
            $('#badge_status_form').text('Draft')
                .removeClass('badge-light-success badge-light-info')
                .addClass('badge-light-warning');
            setFormEditable(true);
            $('#btn_delete_document').show();
            $('#btn_submit_document').show();
            $('#btn_unsubmit_document').hide();
            $('#card_scan_section').show();
            $('#card_print').hide();
        }

        initDetailTable(res.id);
        showFormTab();

        // focus QR input after render
        setTimeout(function () {
            if (!res.is_submitted) $('#qr_input').focus();
        }, 400);
    }, 'json').fail(function () {
        Swal.fire('Error', 'Terjadi kesalahan server saat membuka dokumen', 'error');
    });
}

// ── Save header ───────────────────────────────────────────────────────────
function saveHeader() {
    var shipViaCode = $('#form_ship_via_code').val();
    var isDPK       = shipViaCode && shipViaCode.toUpperCase() === 'DPK';
    var truckingId  = isDPK ? ($('#form_trucking_id').val() || 0) : 0;
    var manualNopol = isDPK ? '' : $('#form_manual_nopol').val().trim();
    var manualDriver= isDPK ? '' : $('#form_manual_driver').val().trim();

    $('#spinner_save_header').show();
    $('#btn_save_header').prop('disabled', true);

    $.post('{{ route("mps.store_head") }}', {
        _token:        '{{ csrf_token() }}',
        doc_id:        $('#form_doc_id').val(),
        ship_via_code: shipViaCode,
        trucking_id:   truckingId,
        manual_nopol:  manualNopol,
        manual_driver: manualDriver
    }, function (res) {
        $('#spinner_save_header').hide();
        $('#btn_save_header').prop('disabled', false);

        if (res.status == 1) {
            Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1500, showConfirmButton: false });

            if (currentDocId === 0) {
                currentDocId = res.id;
                $('#form_doc_id').val(res.id);
                $('#btn_delete_document').show();
                $('#btn_submit_document').show();
                $('#card_scan_section').show();
                $('#badge_status_form').text('Draft')
                    .removeClass('badge-light-info badge-light-success')
                    .addClass('badge-light-warning');
                initDetailTable(res.id);
                setTimeout(function () { $('#qr_input').focus(); }, 400);
            }
        } else {
            Swal.fire('Gagal', res.message, 'error');
        }
    }, 'json').fail(function () {
        $('#spinner_save_header').hide();
        $('#btn_save_header').prop('disabled', false);
        Swal.fire('Error', 'Terjadi kesalahan server', 'error');
    });
}

// ── Process QR scan ───────────────────────────────────────────────────────
function processScan() {
    var val = $('#qr_input').val().trim();
    if (!val || isNaN(val) || parseInt(val) <= 0) {
        showScanFeedback('warning', 'Input tidak valid.');
        return;
    }

    if (currentDocId <= 0) {
        Swal.fire('Perhatian', 'Simpan header terlebih dahulu', 'warning');
        return;
    }

    $('#spinner_scan').show();

    $.post('{{ route("mps.scan_qr") }}', {
        _token:   '{{ csrf_token() }}',
        doc_id:   currentDocId,
        pack_num: parseInt(val)
    }, function (res) {
        $('#spinner_scan').hide();
        $('#qr_input').val('').focus();

        if (res.status == 1) {
            showScanFeedback('success', res.message);
            detailTable.ajax.reload(null, false);
            // Auto-fill customer jika belum terisi
            if (res.CustomerName && !$('#form_customer_name').val()) {
                $('#form_customer_name').val(res.CustomerName);
                $('#form_cust_id').val(res.CustID);
            }
        } else {
            showScanFeedback('danger', res.message);
        }
    }, 'json').fail(function () {
        $('#spinner_scan').hide();
        showScanFeedback('danger', 'Terjadi kesalahan server');
    });
}

function showScanFeedback(type, msg) {
    var icon = type === 'success' ? '✓' : (type === 'warning' ? '⚠' : '✗');
    $('#scan_feedback')
        .removeClass('alert-success alert-warning alert-danger')
        .addClass('alert alert-' + type)
        .html('<strong>' + icon + '</strong> ' + msg)
        .show();

    if (type === 'success') {
        setTimeout(function () { $('#scan_feedback').fadeOut(); }, 2500);
    }
}

// ── Submit document (generate PackingListNum) ─────────────────────────────
function submitDocument() {
    if (currentDocId <= 0) return;

    Swal.fire({
        title: 'Submit Packing List?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Submit',
        cancelButtonText: 'Batal'
    }).then(function (result) {
        if (!result.isConfirmed) return;

        $('#spinner_submit').show();
        $('#btn_submit_document').prop('disabled', true);

        $.post('{{ route("mps.submit") }}', {
            _token: '{{ csrf_token() }}',
            doc_id: currentDocId
        }, function (res) {
            $('#spinner_submit').hide();
            $('#btn_submit_document').prop('disabled', false);

            if (res.status == 1) {
                $('#form_packing_list_num').val(res.PackingListNum);
                $('#div_packing_list_num').show();
                $('#badge_status_form').text('Submitted')
                    .removeClass('badge-light-warning badge-light-info')
                    .addClass('badge-light-success');
                setFormEditable(false);
                $('#btn_delete_document').hide();
                $('#btn_submit_document').hide();
                $('#btn_unsubmit_document').show();
                $('#card_scan_section').hide();
                $('#card_print').show();
                currentDocIsSubmitted = 1;
                Swal.fire({ icon: 'success', title: res.PackingListNum, text: 'Packing list berhasil disubmit', timer: 2000, showConfirmButton: false });
            } else {
                Swal.fire('Gagal', res.message, 'error');
            }
        }, 'json').fail(function () {
            $('#spinner_submit').hide();
            $('#btn_submit_document').prop('disabled', false);
            Swal.fire('Error', 'Terjadi kesalahan server', 'error');
        });
    });
}

// ── Unsubmit document (kembali ke Draft) ────────────────────────────────
function unsubmitDocument() {
    Swal.fire({
        title: 'Unsubmit dokumen ini?',
        text: 'Nomor Packing List akan dihapus dan dokumen kembali ke status Draft.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Unsubmit',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#f1bc00'
    }).then(function (result) {
        if (!result.isConfirmed) return;

        $.post('{{ route("mps.unsubmit") }}', {
            _token: '{{ csrf_token() }}',
            doc_id: currentDocId
        }, function (res) {
            if (res.status == 1) {
                $('#form_packing_list_num').val('');
                $('#div_packing_list_num').hide();
                $('#badge_status_form').text('Draft')
                    .removeClass('badge-light-success badge-light-info')
                    .addClass('badge-light-warning');
                setFormEditable(true);
                $('#btn_unsubmit_document').hide();
                $('#btn_submit_document').show();
                $('#btn_delete_document').show();
                $('#card_scan_section').show();
                $('#card_print').hide();
                currentDocIsSubmitted = 0;
                detailTable.ajax.reload(null, false);
                Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1800, showConfirmButton: false });
            } else {
                Swal.fire('Gagal', res.message, 'error');
            }
        }, 'json').fail(function () {
            Swal.fire('Error', 'Terjadi kesalahan server', 'error');
        });
    });
}

// ── Delete detail row ─────────────────────────────────────────────────────
function deleteDetail(detail_id, doc_id) {
    Swal.fire({
        title: 'Hapus item ini?',
        text: 'Surat jalan ini akan dihapus dari packing list.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#d33'
    }).then(function (result) {
        if (result.isConfirmed) {
            $.post('{{ route("mps.delete_detail") }}', {
                _token:    '{{ csrf_token() }}',
                detail_id: detail_id,
                doc_id:    doc_id
            }, function (res) {
                if (res.status == 1) {
                    detailTable.ajax.reload(null, false);
                    Swal.fire({ icon: 'success', title: 'Dihapus', timer: 1200, showConfirmButton: false });
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            }, 'json').fail(function () {
                Swal.fire('Error', 'Terjadi kesalahan server', 'error');
            });
        }
    });
}

// ── Delete entire document ────────────────────────────────────────────────
function deleteDocument() {
    Swal.fire({
        title: 'Hapus dokumen ini?',
        text: 'Seluruh data header dan detail akan dihapus.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#d33'
    }).then(function (result) {
        if (result.isConfirmed) {
            $.post('{{ route("mps.delete_document") }}', {
                _token: '{{ csrf_token() }}',
                doc_id: currentDocId
            }, function (res) {
                if (res.status == 1) {
                    Swal.fire({ icon: 'success', title: 'Dihapus', timer: 1200, showConfirmButton: false });
                    setTimeout(function () { showHomeTab(); }, 1300);
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            }, 'json').fail(function () {
                Swal.fire('Error', 'Terjadi kesalahan server', 'error');
            });
        }
    });
}

// ── Print PDF ─────────────────────────────────────────────────────────────
function printPdf() {
    if (currentDocId <= 0) return;
    window.open('{{ url("master_pack_shipment/print_pdf") }}?doc_id=' + currentDocId, '_blank');
}

// ── Helpers ───────────────────────────────────────────────────────────────
function resetHeaderForm() {
    $('#form_doc_id').val(0);
    $('#form_packing_list_num').val('');
    $('#div_packing_list_num').hide();
    $('#form_customer_name').val('');
    $('#form_cust_id').val('');
    $('#form_ship_via_code').val(null).trigger('change');
    $('#form_trucking_id').find('option:not([value=""])').remove();
    $('#form_trucking_id').val(null).trigger('change');
    $('#truck_info').hide();
    $('#form_manual_nopol').val('');
    $('#form_manual_driver').val('');
    $('#div_trucking_select').show();
    $('#div_manual_nopol').hide();
    $('#div_manual_driver').hide();
    $('#form_created_info').hide();
    $('#scan_feedback').hide();
    $('#btn_submit_document').hide();
    $('#btn_unsubmit_document').hide();
}

function setFormEditable(editable) {
    $('#form_ship_via_code, #form_trucking_id').prop('disabled', !editable);
    $('#form_manual_nopol, #form_manual_driver').prop('readonly', !editable);
    $('#btn_save_header').toggle(editable);
}
</script>

@endsection
