@extends('../layouts/app')


@section('subhead')
    <title>{{ $head_title }}</title>
    <script>
        $(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            const ref_doc = urlParams.get('ref_doc');
            const ref_preview = urlParams.get('ref_preview');
            if (!ref_doc && !ref_preview) {
                $("#kt_activity_home_tab").addClass('show active');
                window.history.replaceState({}, '', '<?php echo env('BASE_URL'); ?>/master_quotation');
                return;
            } else if (ref_doc) {
                $("#master_price_content").addClass('d-none');
                $("#create_master_price").addClass('d-none');
                $("#preview_data").removeClass('d-none');
                document_preview(ref_doc)
                return;
            } else {
                $("#master_price_content").addClass('d-none')
                $("#preview_data").addClass('d-none')
                $("#master_part_content").removeClass('d-none')
                part_preview(ref_preview)
            }
            // $('#temp_id').val(ref_doc);
            // document_preview(ref_doc, 0);
        });
    </script>
@endsection

<script src="<?= env('APP_ASSETS') ?>assets/js/jquery/jquery.min.js"></script>

@section('subcontent')
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

    <div hidden>
        <div class="card-toolbar m-0">
            <ul class="nav nav-tabs nav-line-tabs nav-stretch fs-6 border-0 fw-bolder" role="tablist">
                <li class="nav-item" role="presentation">
                    <a id="kt_activity_home_tab" class="nav-link justify-content-center text-active-gray-800 active"
                        data-bs-toggle="tab" role="tab" href="#kt_activity_home">Home</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a id="kt_form_tab" class="nav-link justify-content-center text-active-gray-800" data-bs-toggle="tab"
                        role="tab" href="#kt_form">Preview</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="content d-flex flex-column flex-column-fluid" id="master_price_content">
        <div class="tab-content">
            <div id="kt_activity_home" class="card-body p-0 tab-pane fade show active" role="tabpanel"
                aria-labelledby="kt_activity_home_tab">
                <div class="d-flex flex-column-fluid mt-lg-5 mt-sm-5">
                    <div id="kt_content_container" class="container-xxl">
                        <div class="card col-xxl-12 card-sticky">
                            <div class="card-header border-1 pt-6 pb-6 mb-5">
                                <div class="card-title">
                                    <div class="d-flex align-items-center position-relative my-1">
                                        <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none">
                                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2"
                                                    rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                                <path
                                                    d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                                    fill="black" />
                                            </svg>
                                        </span>
                                        <input type="text" data-kt-goodreceive-table-filter="search" id="master_search"
                                            class="form-control form-control-solid w-250px ps-15 text-sm form-control-sm"
                                            placeholder="Search Supplier" />
                                    </div>
                                </div>
                                <div class="card-toolbar">
                                    <div class="d-flex justify-content-end" data-kt-goodreceive-table-toolbar="base">
                                        <button type="button" class="btn btn-light-primary btn-sm me-3"
                                            data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end"
                                            id="importMasterPrice">
                                            <span class="svg-icon svg-icon-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" class="size-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 3.75H6.912a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H15M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859M12 3v8.25m0 0-3-3m3 3 3-3" />
                                                </svg>
                                            </span>
                                            Import</button>
                                        <button type="button" class="btn btn-light-primary btn-sm me-3"
                                            data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end"
                                            id="createMasterPrice">
                                            <span class="svg-icon svg-icon-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" class="size-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                </svg>
                                            </span>
                                            Create</button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <table class="table align-middle table-row-dashed table-striped gy-2 fs-7"
                                    id="master_price_table">
                                    <thead>
                                        <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                            <th class="min-w-20px pe-2">No</th>
                                            <th class="min-w-50px">Supplier</th>
                                            <th class="min-w-50px">Customer</th>
                                            <th class="min-w-20px">View</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                            <th class="min-w-20px pe-2">No</th>
                                            <th class="min-w-50px">Supplier</th>
                                            <th class="min-w-50px">Customer</th>
                                            <th class="min-w-20px">View</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content d-flex flex-column flex-column-fluid d-none" id="master_part_content">
        <div class="tab-content">
            <div id="kt_activity_home" class="card-body p-0 tab-pane fade show active" role="tabpanel"
                aria-labelledby="kt_activity_home_tab">
                <div class="d-flex flex-column-fluid mt-lg-5 mt-sm-5">
                    <div id="kt_content_container" class="container-xxl">
                        <div class="card col-xxl-12 card-sticky">
                            <div class="card-header border-1 pt-6 pb-6 mb-5">
                                <div class="card-title">
                                    <div class="d-flex align-items-center position-relative my-1">
                                        <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none">
                                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546"
                                                    height="2" rx="1" transform="rotate(45 17.0365 15.1223)"
                                                    fill="black" />
                                                <path
                                                    d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                                    fill="black" />
                                            </svg>
                                        </span>
                                        <input type="text" data-kt-goodreceive-table-filter="search" id="part_search"
                                            class="form-control form-control-solid w-250px ps-15 text-sm form-control-sm"
                                            placeholder="Search Part/Spec" />
                                    </div>
                                </div>
                                <div class="card-toolbar">
                                    <div class="d-flex justify-content-end" data-kt-goodreceive-table-toolbar="base">
                                        <button type="button" class="btn btn-light-success btn-sm me-3"
                                            data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end"
                                            id="back_btn">
                                            <span class="svg-icon svg-icon-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                    class="size-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M12 9.75 14.25 12m0 0 2.25 2.25M14.25 12l2.25-2.25M14.25 12 12 14.25m-2.58 4.92-6.374-6.375a1.125 1.125 0 0 1 0-1.59L9.42 4.83c.21-.211.497-.33.795-.33H19.5a2.25 2.25 0 0 1 2.25 2.25v10.5a2.25 2.25 0 0 1-2.25 2.25h-9.284c-.298 0-.585-.119-.795-.33Z" />
                                                </svg>
                                            </span>
                                            Back
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-light-primary btn-sm me-3"
                                        data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end"
                                        id="download_master_price">
                                        <span class="svg-icon svg-icon-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m9 13.5 3 3m0 0 3-3m-3 3v-6m1.06-4.19-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                                            </svg>

                                        </span>
                                        Unduh
                                    </button>
                                    <!-- Button trigger modal -->
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#staticBackdrop">
                                        Import update
                                    </button>

                                    <!-- Modal -->
                                    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static"
                                        data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                                        aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Import Update
                                                    </h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">File</label>
                                                        <input name="input_import_update" type="file"
                                                            id="input_import_update" class="form-control" />
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                    <button type="button" class="btn btn-primary"
                                                        id="submit_import_update">Submit</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <table class="table align-middle table-row-dashed table-striped gy-2 fs-7"
                                    id="master_part_table">
                                    <thead>
                                        <tr class="text-center text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                            <th class="min-w-20px pe-2">No</th>
                                            <th class="min-w-50px">Part FG</th>
                                            <th class="min-w-50px">Part FG Desc</th>
                                            <th class="min-w-50px">Part Mtl</th>
                                            <th class="min-w-50px">Mtl Cost Spec</th>
                                            <th class="min-w-20px">Status</th>
                                            <th class="min-w-20px">View</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr class="text-center text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                            <th class="min-w-20px pe-2">No</th>
                                            <th class="min-w-50px">Part FG</th>
                                            <th class="min-w-50px">Part FG Desc</th>
                                            <th class="min-w-50px">Part Mtl</th>
                                            <th class="min-w-50px">Mtl Cost Spec</th>
                                            <th class="min-w-20px">Status</th>
                                            <th class="min-w-20px">View</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content d-flex flex-column flex-column-fluid d-none" id="preview_data">
        <div class="tab-content">
            <div id="kt_activity_home" class="card-body p-0 tab-pane fade show active" role="tabpanel"
                aria-labelledby="kt_activity_home_tab">
                <div class="d-flex flex-column-fluid mt-lg-5 mt-sm-5">
                    <div id="kt_content_container" class="container-xxl">
                        <div class="card col-xxl-12 card-sticky">
                            <div class="card-header border-1 pt-6 pb-6 mb-5">
                                <div class="card-title">
                                    <ul class="nav nav-tabs nav-line-tabs nav-stretch fs-6 border-0">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#header_tab"
                                                id="header_tab_href">Header</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link disabled" data-bs-toggle="tab" href="#material_tab"
                                                id="material_tab_href">Material</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link disabled" data-bs-toggle="tab"
                                                href="#purchase_part_cost_tab" id="purchase_tab_href">Purchase Part
                                                Cost</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link disabled" data-bs-toggle="tab" href="#process_tab"
                                                id="process_tab_href">Process</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link disabled" data-bs-toggle="tab" href="#sub_total_tab"
                                                id="sub_total_tab_href">Other Cost</a>
                                        </li>
                                        {{-- <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#document_tab">Document</a>
                                        </li> --}}
                                    </ul>
                                </div>
                                <div class="card-toolbar">
                                    <div class="d-flex justify-content-end" data-kt-goodreceive-table-toolbar="base">
                                        <button type="button" class="btn btn-light-success btn-sm me-3"
                                            data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end"
                                            id="backBtn">
                                            <span class="svg-icon svg-icon-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                    class="size-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M12 9.75 14.25 12m0 0 2.25 2.25M14.25 12l2.25-2.25M14.25 12 12 14.25m-2.58 4.92-6.374-6.375a1.125 1.125 0 0 1 0-1.59L9.42 4.83c.21-.211.497-.33.795-.33H19.5a2.25 2.25 0 0 1 2.25 2.25v10.5a2.25 2.25 0 0 1-2.25 2.25h-9.284c-.298 0-.585-.119-.795-.33Z" />
                                                </svg>
                                            </span>
                                            Back</button>
                                        <button type="button" class="btn btn-light-success btn-sm me-3 d-none"
                                            data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end"
                                            id="backBtnCrt">
                                            <span class="svg-icon svg-icon-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                    class="size-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M12 9.75 14.25 12m0 0 2.25 2.25M14.25 12l2.25-2.25M14.25 12 12 14.25m-2.58 4.92-6.374-6.375a1.125 1.125 0 0 1 0-1.59L9.42 4.83c.21-.211.497-.33.795-.33H19.5a2.25 2.25 0 0 1 2.25 2.25v10.5a2.25 2.25 0 0 1-2.25 2.25h-9.284c-.298 0-.585-.119-.795-.33Z" />
                                                </svg>
                                            </span>
                                            Back</button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="header_tab" role="tabpanel">
                                        <div class="row" id="form">
                                            <div class="col-md-6 mb-5">
                                                <label class="form-label">Supllier</label>
                                                <select name="supplier" id="supplier" class="form-select"></select>
                                            </div>
                                            <div class="col-md-6 mb-5">
                                                <label class="form-label">Customer</label>
                                                <select name="customer" id="customer" class="form-select"></select>
                                            </div>
                                            <div class="col-md-12 mb-5">
                                                <label class="form-label">Period</label>
                                                <select name="period" id="period" class="form-select"></select>
                                            </div>
                                            <div class="justify-content-end">
                                                <button type="button" class="btn btn-primary btn-sm mr-2 mt-2 d-flex"
                                                    id="submit_master_header">
                                                    <span id="submit_header_text">Submit</span>
                                                    <span id="submit_header_spinner"
                                                        class="spinner-border spinner-border-sm align-middle ms-2"
                                                        style="display: none;"></span>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-primary btn-sm mr-2 mt-2 d-flex d-none"
                                                    id="update_master_header">
                                                    <span id="submit_header_text">Update</span>
                                                    <span id="submit_header_spinner"
                                                        class="spinner-border spinner-border-sm align-middle ms-2"
                                                        style="display: none;"></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tab Attachment -->
                                    <div class="tab-pane fade" id="material_tab" role="tabpanel">
                                        <div class="row">
                                            <div class="col-md-6 mb-5">
                                                <form>
                                                    <div class="form-group mb-5">
                                                        <label class="form-label required">Part Finish Good</label>
                                                        <select name="part_fg" id="part_fg"
                                                            class="form-select"></select>
                                                    </div>
                                                    <div class="form-group mb-5">
                                                        <label class="form-label required">Part Material</label>
                                                        <select name="part_mtl" id="part_mtl"
                                                            class="form-select"></select>
                                                    </div>
                                                    <div class="form-group mb-5">
                                                        <label class="form-label required">Material Weight Qty</label>
                                                        <input type="text" class="form-control"
                                                            id="material_weight_qty" />
                                                    </div>
                                                    <div class="form-group mb-5">
                                                        <label class="form-label required">Part Weight Qty</label>
                                                        <input type="text" class="form-control"
                                                            id="part_weight_qty" />
                                                    </div>
                                                    <div class="form-group mb-5">
                                                        <label class="form-label">Scrap Qty</label>
                                                        <input type="text" class="form-control bg-light-primary"
                                                            id="scrap_qty" readonly />
                                                    </div>
                                                    <div class="form-group mb-5">
                                                        <label class="form-label">Material Cost Estimate</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp</span>
                                                            <input type="text" class="form-control bg-light-primary"
                                                                id="material_cost_estimate" name="material_cost_estimate"
                                                                readonly />
                                                        </div>
                                                    </div>
                                                    <div class="form-group mb-5">
                                                        <label class="form-label">Scrap Estimate</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp</span>
                                                            <input type="text" class="form-control bg-light-primary"
                                                                id="scrap_estimate" readonly />
                                                        </div>
                                                    </div>
                                                    <div class="form-group mb-5" id="vol_div">
                                                        <label class="form-label">Volume Qty</label>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control" id="volume_qty"
                                                                value="0" />
                                                        </div>
                                                    </div>
                                                    <div class="form-group mb-5 d-none" id="dep_qty">
                                                        <label class="form-label">Depreciation Qty</label>
                                                        <input type="text" class="form-control" id="depreciation_qty"
                                                            value="0" />
                                                    </div>
                                                    <div class="form-group mb-5" id="effective_date_div">
                                                        <label class="form-label">Effective Date</label>
                                                        <div class="input-group">
                                                            <input type="date" class="form-control"
                                                                id="effective_date" />
                                                        </div>
                                                    </div>

                                                </form>
                                            </div>

                                            <!-- Form kanan -->
                                            <div class="col-md-6 mb-5">
                                                <div class="form-group mb-5">
                                                    <label class="form-label required">Part FG Description</label>
                                                    <input type="text" class="form-control bg-light-primary"
                                                        id="part_fg_desc" readonly />
                                                </div>
                                                <div class="form-group mb-5">
                                                    <label class="form-label required">Material Cost Spec</label>
                                                    <input type="text" class="form-control bg-light-primary"
                                                        id="material_cost_spec" readonly />
                                                </div>
                                                <div class="form-group mb-5">
                                                    <label class="form-label required">Material Weight Price</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="text" class="form-control"
                                                            id="material_weight_price" />
                                                    </div>
                                                </div>
                                                <div class="form-group mb-5 d-none" id="div_mtl_sheet">
                                                    <label class="form-label required">Material Sheet Price</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="text" class="form-control bg-light-primary"
                                                            id="material_sheet_price" readonly />
                                                    </div>
                                                </div>
                                                <div class="form-group mb-5">
                                                    <label class="form-label required">UOM</label>
                                                    <input type="text" class="form-control" id="uom" />
                                                </div>
                                                <div class="form-group mb-5">
                                                    <label class="form-label required">Scrap Price</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="text" class="form-control" id="scrap_price" />
                                                    </div>
                                                </div>
                                                <div class="form-group mb-5">
                                                    <label class="form-label">Material Weight Estimate</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="text" class="form-control bg-light-primary"
                                                            id="material_weight_estimate" readonly />
                                                    </div>
                                                </div>
                                                <div class="form-group mb-5">
                                                    <label class="form-label required">Depreciation</label>
                                                    <select name="depreciation_select" id="depreciation_select"
                                                        class="form-select">
                                                        <option selected disabled>Pilih depresiasi</option>
                                                        <option value="ya">Ya</option>
                                                        <option value="tidak">Tidak</option>
                                                    </select>
                                                </div>
                                                <div class="form-group mb-5 d-none" id="dep_price">
                                                    <label class="form-label">Depreciation Price</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="text" class="form-control"
                                                            id="depreciation_price" />
                                                    </div>
                                                </div>
                                                <div class="form-group mb-5" id="expired_date_div">
                                                    <label class="form-label">Expired Date</label>
                                                    <div class="input-group">
                                                        <input type="date" class="form-control" id="expired_date" />
                                                    </div>
                                                </div>
                                                <div class="form-group mb-5 d-none" id="div_top_end_coil">
                                                    <label class="form-label">Top End Coil</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="top_end_coil"
                                                            readonly />
                                                    </div>
                                                </div>
                                                <div class="form-group mb-5">
                                                    <label class="form-label">Note</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="note" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group mb-5">
                                                <button class="btn btn-primary" id="material_submit_btn">Submit</button>
                                                <button class="btn btn-primary d-none"
                                                    id="material_update_btn">Update</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="purchase_part_cost_tab" role="tabpanel">
                                        <!-- Button trigger modal -->
                                        <button type="button" class="btn btn-light-primary btn-sm me-3"
                                            data-bs-toggle="modal" data-bs-target="#purchaseModal" id="createPurchase">
                                            <span class="svg-icon svg-icon-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                    class="size-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                </svg>
                                            </span>
                                            Create
                                        </button>

                                        <!-- Modal -->
                                        <div class="modal fade" id="purchaseModal" data-bs-backdrop="static"
                                            data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-xl">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="purchaseBackdropLabel">Create
                                                            Purchase</h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group mb-5">
                                                                    <label class="form-label required">Purchase
                                                                        Part</label>
                                                                    <select class="form-select"
                                                                        id="purchase_part"></select>
                                                                </div>
                                                                <div class="form-group mb-5">
                                                                    <label class="form-label required">Tipe
                                                                        Purchase</label>
                                                                    <select class="form-select" id="type_purchase">
                                                                        <option selected disabled>Pilih Tipe purchase
                                                                        </option>
                                                                        <option value="purchase">Purchase</option>
                                                                        <option value="supply">Supply</option>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group mb-5">
                                                                    <label class="form-label">Price</label>
                                                                    <div class="input-group">
                                                                        <span class="input-group-text">Rp</span>
                                                                        <input type="text" class="form-control"
                                                                            id="purchase_price" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-5">
                                                                <div class="form-group mb-5">
                                                                    <label class="form-label required">Spec Purchase
                                                                        Part</label>
                                                                    <input type="text"
                                                                        class="form-control bg-light-primary"
                                                                        id="spec_purchase_part" readonly />
                                                                </div>
                                                                <div class="form-group mb-5">
                                                                    <label class="form-label required">Qty</label>
                                                                    <input type="text" class="form-control"
                                                                        id="purchase_qty" />
                                                                </div>
                                                                <div class="form-group mb-5">
                                                                    <label class="form-label">Estimate</label>
                                                                    <div class="input-group">
                                                                        <span class="input-group-text">Rp</span>
                                                                        <input type="text"
                                                                            class="form-control bg-light-primary"
                                                                            id="purchase_estimate" readonly />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer" id="footer_modal_process">
                                                        <button type="button" class="btn btn-primary"
                                                            id="save_purchase_btn">Simpan</button>
                                                        <button type="button" class="btn btn-primary d-none"
                                                            id="update_purchase_btn">Update</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <table class="table align-middle table-row-dashed table-striped gy-2 fs-7"
                                            id="purchase_tab_table">
                                            <thead>
                                                <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                                    <th class="min-w-20px pe-2">No</th>
                                                    <th class="min-w-50px">Purchase Part</th>
                                                    <th class="min-w-50px">Spec Purchase Part</th>
                                                    <th class="min-w-30px">Qty</th>
                                                    <th class="min-w-30px">Price</th>
                                                    <th class="min-w-30px">Estimate</th>
                                                    <th class="min-w-20px">View</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                                    <th class="min-w-20px pe-2">No</th>
                                                    <th class="min-w-20px">Name Item</th>
                                                    <th class="min-w-70px">Qty</th>
                                                    <th class="min-w-50px">Price</th>
                                                    <th class="min-w-20px">Estimate</th>
                                                    <th class="min-w-20px">View</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade" id="process_tab" role="tabpanel">
                                        <!-- Button trigger modal -->
                                        <div class="d-flex justify-content-between">
                                            <button type="button" class="btn btn-light-primary btn-sm me-3"
                                                data-bs-toggle="modal" data-bs-target="#ProcessModal"
                                                id="createBtnProcess">
                                                <span class="svg-icon svg-icon-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="size-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                    </svg>
                                                </span>
                                                Create
                                            </button>
                                        </div>
                                        <!-- Modal -->
                                        <div class="modal fade" id="ProcessModal" data-bs-backdrop="static"
                                            data-bs-keyboard="false" tabindex="-1" aria-labelledby="ProcelModalLabel"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-xl">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="ProcelModalLabel">Create
                                                            Process</h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group mb-5">
                                                                    <label class="form-label required">Name Process</label>
                                                                    <input type="text" class="form-control"
                                                                        id="name_process" />
                                                                </div>
                                                                <div class="form-group mb-5">
                                                                    <label class="form-label required">Stroke</label>
                                                                    <input type="text" class="form-control"
                                                                        id="stroke" />
                                                                </div>
                                                                <div class="form-group mb-5">
                                                                    <label class="form-label">Estimate</label>
                                                                    <div class="input-group">
                                                                        <span class="input-group-text">Rp</span>
                                                                        <input type="text" class="form-control"
                                                                            id="estimate" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group mb-5">
                                                                    <label class="form-label required">Machine</label>
                                                                    <input type="text" class="form-control"
                                                                        id="machine" />
                                                                </div>
                                                                <div class="form-group mb-5">
                                                                    <label class="form-label required">Rate</label>
                                                                    <div class="input-group">
                                                                        <span class="input-group-text">Rp</span>
                                                                        <input type="text" class="form-control"
                                                                            id="rate" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer" id="footer_modal_process">
                                                        <button type="button" class="btn btn-primary"
                                                            id="save_process_btn">Simpan</button>
                                                        <button type="button" class="btn btn-primary d-none"
                                                            id="update_process_btn">Update</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <table class="table align-middle table-row-dashed table-striped gy-2 fs-7"
                                            id="process_tab_table">
                                            <thead>
                                                <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                                    {{-- <th class="min-w-20px pe-2">No</th> --}}
                                                    <th class="min-w-20px">Name Proccess</th>
                                                    <th class="min-w-70px">Machine</th>
                                                    <th class="min-w-50px">Stroke</th>
                                                    <th class="min-w-20px">Rate</th>
                                                    <th class="min-w-20px">Estimate</th>
                                                    <th class="min-w-20px">View</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                                    {{-- <th class="min-w-20px pe-2">No</th> --}}
                                                    <th class="min-w-20px">Name Proccess</th>
                                                    <th class="min-w-70px">Machine</th>
                                                    <th class="min-w-50px">Stroke</th>
                                                    <th class="min-w-20px">Rate</th>
                                                    <th class="min-w-20px">Estimate</th>
                                                    <th class="min-w-20px">View</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade" id="sub_total_tab" role="tabpanel">
                                        <div class="d-flex justify-content-between">
                                            <button type="button" class="btn btn-light-primary btn-sm me-3"
                                                data-bs-toggle="modal" data-bs-target="#otherCostModal"
                                                id="createBtnOtherCost">
                                                <span class="svg-icon svg-icon-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="size-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                    </svg>
                                                </span>
                                                Create
                                            </button>
                                            <button type="button" class="btn btn-light-primary btn-sm me-3"
                                                id="confirmBtnOtherCost">
                                                <span class="svg-icon svg-icon-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="size-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                    </svg>
                                                </span>
                                                Confirm
                                            </button>
                                            <button type="button" class="btn btn-light-danger btn-sm me-3 d-none"
                                                id="cancelConfirmOtherCost">
                                                <span class="svg-icon svg-icon-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="size-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                    </svg>
                                                </span>
                                                Unconfirm
                                            </button>
                                        </div>
                                        <!-- Modal -->
                                        <div class="modal fade" id="otherCostModal" data-bs-backdrop="static"
                                            data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-xl">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="otherCostLabel"></h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close" id="btnCloseOtherCost"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group mb-5">
                                                                    <label class="form-label required">Name Item</label>
                                                                    <input type="text" class="form-control"
                                                                        id="name_item_other_cost" />
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group mb-5">
                                                                    <label class="form-label required">Addition
                                                                        Type</label>
                                                                    <select class="form-select"
                                                                        id="addition_type_other_cost">
                                                                        <option selected disabled>Pilih Tipe Penjumlahan
                                                                        </option>
                                                                        <option value="blank_cost">Blank Cost
                                                                        </option>
                                                                        <option value="x_material_cost">x Material cost
                                                                        </option>
                                                                        <option value="x_manufactur_cost">x Manufactur cost
                                                                        </option>
                                                                        <option value="x_sub_total">x Sub Total</option>
                                                                        <option value="discount">Discount</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6" id="div_percentage">
                                                                <div class="form-group mb-5">
                                                                    <label class="form-label required">Percentage</label>
                                                                    <div class="input-group">
                                                                        <input type="text" class="form-control"
                                                                            id="percentage_other_cost" />
                                                                        <span class="input-group-text">%</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 d-none" id="div_blank_price">
                                                                <div class="form-group mb-5">
                                                                    <label class="form-label required">Blank Price</label>
                                                                    <div class="input-group">
                                                                        <span class="input-group-text">Rp</span>
                                                                        <input type="text" class="form-control"
                                                                            id="blank_price" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group mb-5">
                                                                    <label class="form-label">Estimate</label>
                                                                    <div class="input-group">
                                                                        <span class="input-group-text">Rp</span>
                                                                        <input type="text"
                                                                            class="form-control bg-light-primary"
                                                                            id="estimate_other_cost" readonly />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer" id="footer_modal_process">
                                                        <button type="button" class="btn btn-primary"
                                                            id="save_other_cost_btn">Simpan</button>
                                                        <button type="button" class="btn btn-primary d-none"
                                                            id="update_other_cost_btn">Update</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <table class="table align-middle table-row-dashed table-striped gy-2 fs-7"
                                            id="sub_total_table">
                                            <thead>
                                                <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                                    <th class="min-w-20px pe-2">No</th>
                                                    <th class="min-w-50px">Item</th>
                                                    <th class="min-w-20px">Percentage</th>
                                                    <th class="min-w-50px">Quantity</th>
                                                    <th class="min-w-20px">Estimate</th>
                                                    <th class="min-w-20px">View</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                                    <th class="min-w-20px pe-2">No</th>
                                                    <th class="min-w-50px">Item</th>
                                                    <th class="min-w-20px">Percentage</th>
                                                    <th class="min-w-50px">Quantity</th>
                                                    <th class="min-w-20px">Estimate</th>
                                                    <th class="min-w-20px">View</th>
                                                </tr>
                                            </tfoot>
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
    <div class="content d-flex flex-column flex-column-fluid d-none" id="create_master_price">
        <div class="tab-content">
            <div id="kt_activity_home" class="card-body p-0 tab-pane fade show active" role="tabpanel"
                aria-labelledby="kt_activity_home_tab">
                <div class="d-flex flex-column-fluid mt-lg-5 mt-sm-5">
                    <div id="kt_content_container" class="container-xxl">
                        <div class="card col-xxl-12 card-sticky">
                            <div class="card-header border-1 pt-6 pb-6 mb-5">
                                <div class="card-title">

                                </div>
                                <div class="card-toolbar">
                                    <div class="d-flex justify-content-end" data-kt-goodreceive-table-toolbar="base">
                                        <button type="button" class="btn btn-light-success btn-sm me-3"
                                            data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end"
                                            id="backImportBtn">
                                            <span class="svg-icon svg-icon-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                    class="size-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M12 9.75 14.25 12m0 0 2.25 2.25M14.25 12l2.25-2.25M14.25 12 12 14.25m-2.58 4.92-6.374-6.375a1.125 1.125 0 0 1 0-1.59L9.42 4.83c.21-.211.497-.33.795-.33H19.5a2.25 2.25 0 0 1 2.25 2.25v10.5a2.25 2.25 0 0 1-2.25 2.25h-9.284c-.298 0-.585-.119-.795-.33Z" />
                                                </svg>
                                            </span>
                                            Back</button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div class="row" id="form_import">
                                    <div class="col-md-12 mb-5">
                                        <div class="form-group mb-5">
                                            <label>File Import</label>
                                            <input type="file" class="form-control" id="file_import" />
                                        </div>
                                        <a href="{{ asset('public/format_quotation.xlsx') }}" download>Unduh contoh
                                            file</a>
                                    </div>
                                    <hr style="color: gray">
                                    <div class="col-md-12" style="display:flex; justify-content:space-between">
                                        <div>
                                            <button type="button" class="btn btn-primary btn-sm mr-2 mt-2 d-flex"
                                                id="submit_import">
                                                <span id="submit_import_text">Submit</span>
                                                <span id="spinner_import"
                                                    class="spinner-border spinner-border-sm align-middle ms-2"
                                                    style="display: none;"></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="text" id="header_id" hidden>
    <input type="text" id="purchase_id" hidden>
    <input type="text" id="process_id" hidden>
    <input type="text" id="name_item_val" hidden>
    <input type="text" id="other_cost_id" hidden>
    <div class="modal fade" id="docModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Document Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-3 py-0" style="height:80vh">
                    <iframe id="docFrame" style="width:100%; height:100%;" frameborder="0"></iframe>
                </div>
            </div>
        </div>
    </div>

    {{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.getElementById('createBtnOtherCost').addEventListener('click', function() {
            console.log('Button clicked');
        });
        let isXCSelected;
        let master_price_table;
        $(document).ready(function() {
            master_price_table = $("#master_price_table").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('master_quotation/show_data') }}",
                    type: "POST",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}",
                            d.search = $("#master_search").val()
                    }
                },
                columns: [{
                    data: 'No'
                }, {
                    data: 'SupplierName'
                }, {
                    data: 'Customer'
                }, {
                    data: 'View'
                }]
            })
        })
        $("#master_search").on('change', function() {
            master_price_table.ajax.reload(null, false)
        })
        $('#createMasterPrice').on('click', function() {
            $("#master_price_content").addClass('d-none')
            $("#preview_data").removeClass('d-none')
            $("#backBtn").addClass('d-none')
            $("#backBtnCrt").removeClass('d-none')
            $("#material_tab_href").addClass('disabled')
            $("#purchase_tab_href").addClass('disabled')
            $("#process_tab_href").addClass('disabled')
            $("#sub_total_tab_href").addClass('disabled')
            $('#supplier, #customer, #part_mtl, #part_fg').val(null).trigger('change');
            $('#supplier, #customer,#period').val(null).trigger('change');
            $('a[href="#header_tab"]').tab('show');
            $("#material_tab_href, #purchase_tab_href, #process_tab_href, #sub_total_tab_href")
                .addClass('disabled');
            $("#master_price_content").addClass('d-none');
            $("#preview_data").removeClass('d-none');
            $("#backBtn").addClass('d-none');
            $("#backBtnCrt").removeClass('d-none');
            $("#submit_master_header").removeClass('d-none');
            $("#update_master_header").addClass('d-none');
            $("#supplier, #customer,#part_fg,#part_mtl,#period").prop('disabled', false);
            $("#part_fg,#part_mtl").removeClass('bg-light-primary')
            $("#part_fg_desc,#material_cost_spec,#material_weight_qty,#material_weight_price,#material_sheet_price,#part_weight_qty,#scrap_qty,#scrap_price,#uom,#material_cost_estimate,#material_weight_estimate,#scrap_estimate,#depreciation_qty,#depreciation_price")
                .val(null)
            $("#depreciation_select").val(null).trigger('change')
            $("#material_submit_btn").removeClass('d-none')
            $("#material_update_btn").addClass('d-none')
            get_supplier()
            get_customer()
            get_period()
        })
        $("#backBtnCrt").on('click', function() {
            $("#master_price_content").removeClass('d-none')
            $("#create_master_price").addClass('d-none');
            $("#preview_data").addClass('d-none')
            $("#master_part_content").addClass('d-none')
            master_price_table.ajax.reload(null, false)
            window.history.replaceState({}, '', '<?php echo env('BASE_URL'); ?>/master_quotation');
        })

        function get_supplier() {
            $('#supplier').select2({
                placeholder: 'Pilih Supplier',
                minimumInputLength: 0,
                allowClear: true,
                ajax: {
                    url: "{{ url('master_quotation/get_supplier') }}",
                    method: "POST",
                    dataType: 'json',
                    delay: 300,
                    data: function(params) {
                        return {
                            _token: "{{ csrf_token() }}",
                            search: params.term || '',
                            page: params.page || 1
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;

                        return {
                            results: data.results,
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },
                    cache: true
                }
            });
        }

        function get_customer() {
            $('#customer').select2({
                placeholder: 'Pilih Customer',
                minimumInputLength: 0,
                allowClear: true,
                ajax: {
                    url: "{{ url('master_quotation/get_customer') }}",
                    method: "POST",
                    dataType: 'json',
                    delay: 300,
                    data: function(params) {
                        return {
                            _token: "{{ csrf_token() }}",
                            search: params.term || '',
                            page: params.page || 1
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;

                        return {
                            results: data.results,
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },
                    cache: true
                }
            });
        }
        function get_period() {
    $('#period').select2({
        placeholder: 'Pilih Period',
        minimumInputLength: 0,
        allowClear: true,
        ajax: {
            url: "{{ url('master_quotation/get_period') }}",
            method: "POST",
            dataType: 'json',
            delay: 300,
            data: function(params) {
                return {
                    _token: "{{ csrf_token() }}",
                    search: params.term || '',
                    page: params.page || 1
                };
            },
            processResults: function(data, params) {
                params.page = params.page || 1;

                return {
                    results: data.results,
                    pagination: {
                        more: data.pagination.more
                    }
                };
            },
            cache: true
        }
    });

    $(document).on('select2:select', '#period', async function(e) {
        const data = e.params.data;

        if (data.id === 'AddItem') {

            $('#period').val(null).trigger('change');

            // ======================
            // FORM INPUT
            // ======================
            const formResult = await Swal.fire({
                title: 'Add New Period',
                width: '700px',
                html: `
                    <div class='row'>
                        <div class='col-md-6'>
                            <input type="date" id="EffectiveDate" class="swal2-input">
                        </div>
                        <div class='col-md-6'>
                            <input type="date" id="ExpiredDate" class="swal2-input">
                        </div>
                    </div>
                `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Save',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false,
                preConfirm: () => {

                    const effective = document.getElementById('EffectiveDate').value;
                    const expired = document.getElementById('ExpiredDate').value;

                    if (!effective || !expired) {
                        Swal.showValidationMessage('Both dates are required');
                        return false;
                    }

                    return {
                        EffectiveDate: effective,
                        ExpiredDate: expired
                    };
                }
            });

            if (!formResult.isConfirmed) return;

            const formData = formResult.value;

            // ======================
            // CONFIRMATION
            // ======================
            const confirmResult = await Swal.fire({
                title: "Apa anda yakin?",
                text: "Pastikan Periode sudah sesuai",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya!",
                cancelButtonText: "Batal",
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33"
            });

            if (!confirmResult.isConfirmed) return;

            // ======================
            // AJAX SAVE
            // ======================
            $.ajax({
                url: "{{ url('master_quotation/add_period') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    EffectiveDate: formData.EffectiveDate,
                    ExpiredDate: formData.ExpiredDate
                },
                success: function(res) {
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Period added',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        // reopen select2 biar refresh
                        $('#period').select2('open');

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Server error',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });

        }
    });
}
//         function get_period() {
//             $('#period').select2({
//                 placeholder: 'Pilih Period',
//                 minimumInputLength: 0,
//                 allowClear: true,
//                 ajax: {
//                     url: "{{ url('master_quotation/get_period') }}",
//                     method: "POST",
//                     dataType: 'json',
//                     delay: 300,
//                     data: function(params) {
//                         return {
//                             _token: "{{ csrf_token() }}",
//                             search: params.term || '',
//                             page: params.page || 1
//                         };
//                     },
//                     processResults: function(data, params) {
//                         params.page = params.page || 1;

//                         return {
//                             results: data.results,
//                             pagination: {
//                                 more: data.pagination.more
//                             }
//                         };
//                     },
//                     cache: true
//                 }
//             });
//             $(document).on('select2:select', '#period', function(e) {
//                 const data = e.params.data;

//                 if (data.id === 'AddItem') {

//                     $('#period').val(null).trigger('change');

//                     Swal.fire({
//                         title: 'Add New Period',
//                         width: '700px',
//                         html: `
//                 <div class='row'>
//                 <div class='col-md-6'>
//                     <input type="date" id="EffectiveDate" class="swal2-input" placeholder="Effective Date">
//                 </div>
//                 <div class='col-md-6'>
//                     <input type="date" id="ExpiredDate" class="swal2-input" placeholder="Expired Date">
//                 </div>
//                 </div>
//             `,
//                         focusConfirm: false,
//                         showCancelButton: true,
//                         confirmButtonText: 'Save',
//                         cancelButtonText: 'Cancel',

//                         customClass: {
//                             confirmButton: 'btn btn-primary',
//                             cancelButton: 'btn btn-secondary'
//                         },

//                         buttonsStyling: false,
//                         preConfirm: () => {

//                             const effective = document.getElementById('EffectiveDate').value;
//                             const expired = document.getElementById('ExpiredDate').value;

//                             if (!effective || !expired) {
//                                 Swal.showValidationMessage('Both dates are required');
//                                 return false;
//                             }

//                             return {
//                                 EffectiveDate: effective,
//                                 ExpiredDate: expired
//                             };
//                         }
//                     }).then((result) => {

//                         if (result.isConfirmed) {
//                             Swal.fire({
//                             title: "Apa anda yakin?",
//                             text: "Pastikan Periode susah sesuai",
//                             icon: "warning",
//                             showCancelButton: true,
//                             confirmButtonColor: "#3085d6",
//                             cancelButtonColor: "#d33",
//                             confirmButtonText: "Ya!"
//                             }).then((result) => {
//                                 $.ajax({
//                                 url: "{{ url('master_quotation/add_period') }}",
//                                 type: "POST",
//                                 data: {
//                                     _token: "{{ csrf_token() }}",
//                                     EffectiveDate: result.value.EffectiveDate,
//                                     ExpiredDate: result.value.ExpiredDate
//                                 },
//                                 success: function(res) {
//                                     const status = res.status
//                                     if (status == 'success') {
//                                         Swal.fire({
//                                             icon: 'success',
//                                             title: 'Period added',
//                                             timer: 1500,
//                                             showConfirmButton: false
//                                         });
//                                         $('#period').select2('open');
//                                     } else {
//                                         Swal.fire({
//                                             icon: 'error',
//                                             title: res.message,
//                                             timer: 1500,
//                                             showConfirmButton: false
//                                         });
//                                     }
//                                 }
//                             });
// });
//                         }

//                     });

//                 }
//             });
//         }
        $("#back_btn, #backImportBtn").on('click', function() {
            window.history.replaceState({}, '', '<?php echo env('BASE_URL'); ?>/master_quotation');
            $("#master_price_content").removeClass('d-none');
            $("#create_master_price").addClass('d-none');
            $("#preview_data").addClass('d-none')
            $("#master_part_content").addClass('d-none')
            master_price_table.ajax.reload(null, false);
        });
        $("#backBtn").on('click', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const ref_doc = urlParams.get('ref_doc');
            $.ajax({
                url: "{{ url('master_quotation/back_mtl') }}",
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    ref_doc: ref_doc
                },
                success: function(response) {
                    part_preview(response)
                },
                error: function(xhr) {
                    console.log(xhr)
                    Toast.fire({
                        position: 'top-end',
                        title: 'Something went wrong',
                        icon: "error"
                    });
                }
            })
        })
        $("#submit_master_header").on('click', function() {
            $("#submit_header_spinner").css('display', 'block')
            const supplier = $("#supplier").val()
            const customer = $("#customer").val()
            const period = $("#period").val()
            if (!supplier || !customer) {
                Toast.fire({
                    position: 'top-end',
                    title: 'Supplier atau customer kolom wajib di isi',
                    icon: "error"
                });
                $("#submit_header_spinner").css('display', 'none')
                return
            }
            $.ajax({
                url: "{{ url('master_quotation/store_data') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    supplier: supplier,
                    customer: customer,
                    period: period
                },
                success: function(response) {
                    if (response.status == 'success') {
                        Toast.fire({
                            position: 'top-end',
                            title: response.message,
                            icon: "success"
                        });
                        $("#header_id").val(response.HeaderID)
                        if (period !== null) {
                            $("#effective_date_div").addClass('d-none')
                            $("#expired_date_div").addClass('d-none')
                            $("#effective_date").val(response.effective_date)
                            $("#expired_date").val(response.expired_date)
                        } else {
                            $("#effective_date_div").removeClass('d-none')
                            $("#expired_date_div").removeClass('d-none')
                        }
                        $("#material_tab_href").removeClass('disabled');
                        const tabEl = document.getElementById('material_tab_href');
                        const tab = new bootstrap.Tab(tabEl);
                        tab.show();
                        part_fg()
                        part_mtl()
                    } else {
                        Toast.fire({
                            position: 'top-end',
                            title: response.message,
                            icon: "error"
                        });
                    }
                    $("#submit_header_spinner").css('display', 'none')
                },
                error: function(xhr) {
                    console.log(xhr.responseJSON.message)
                    Toast.fire({
                        position: 'top-end',
                        title: 'Unknown error',
                        icon: "success"
                    });
                    $("#submit_header_spinner").css('display', 'none')
                }
            })
        })
        $("#material_tab_href").on('click', function() {
            part_fg()
            part_mtl()
        })

        function part_mtl() {
            $('#part_mtl').select2({
                placeholder: 'Pilih Part Material',
                minimumInputLength: 0,
                allowClear: true,
                ajax: {
                    url: "{{ url('master_quotation/get_part_mtl') }}",
                    method: "POST",
                    dataType: 'json',
                    delay: 300,
                    data: function(params) {
                        return {
                            _token: "{{ csrf_token() }}",
                            type: 'RM',
                            search: params.term || '',
                            page: params.page || 1
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;

                        return {
                            results: data.results,
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },
                    cache: true
                }
            });
            $(document).on('select2:select', '#part_mtl', function(e) {
                const data = e.params.data;
                const selectedText = data.text || '';
                const supplier = $("#supplier").val();
                isXCSelected = selectedText.includes('-R') &&
                    selectedText.includes('XC') &&
                    supplier == '24~PT TRI CENTRUM FORTUNA';

                if (isXCSelected) {
                    $("#div_top_end_coil").removeClass('d-none');
                } else {
                    $("#div_top_end_coil").addClass('d-none');
                    $("#top_end_coil").val('');
                }
                $('#material_cost_spec').val(data.Description || '');
                $('#uom').val(data.UOM || '');
                if (data.UOM == 'SHEET') {
                    $("#div_mtl_sheet").removeClass('d-none')
                    $("#material_sheet_price").val(Number(data.Price).toLocaleString('en-US'))
                    $("#material_weight_price").val('')
                } else {
                    $("#div_mtl_sheet").addClass('d-none')
                    $("#material_weight_price").val(Number(data.Price).toLocaleString('en-US'))
                    $("#material_sheet_price").val('')
                }
                const qty = parseQty($("#material_weight_qty").val());
                const price = parsePrice($("#material_weight_price").val());
                const result = qty * price;
                const excelResult = Math.round(result);
                $("#material_weight_estimate").val(
                    excelResult.toLocaleString('en-US')
                );
            });
        }

        function part_fg() {
            $('#part_fg').select2({
                placeholder: 'Pilih Part FG',
                minimumInputLength: 0,
                allowClear: true,
                ajax: {
                    url: "{{ url('master_quotation/get_part_mtl') }}",
                    method: "POST",
                    dataType: 'json',
                    delay: 300,
                    data: function(params) {
                        return {
                            _token: "{{ csrf_token() }}",
                            type: 'FG',
                            search: params.term || '',
                            page: params.page || 1
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;

                        return {
                            results: data.results,
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },
                    cache: true
                }
            });
            $(document).on('select2:select', '#part_fg', function(e) {
                const data = e.params.data;
                $('#part_fg_desc').val(data.Description || '');
                // $('#uom').val(data.UOM || '');
            });
        }
        $("#depreciation_select").on('change', function() {
            const select = $("#depreciation_select").val()
            if (select == 'ya') {
                $("#dep_qty").removeClass('d-none')
                $("#dep_price").removeClass('d-none')
            } else {
                $("#dep_qty").addClass('d-none')
                $("#dep_price").addClass('d-none')
            }
        })
        $('#stroke,#percentage,#purchase_qty,#percentage_other_cost,#estimate_other_cost,#rate,#depreciation_qty')
            .on('input', function() {
                let value = this.value;
                value = value.replace(',', '.');
                value = value.replace(/[^0-9.]/g, '');
                const parts = value.split('.');
                if (parts.length > 2) {
                    value = parts[0] + '.' + parts.slice(1).join('');
                }
                this.value = value;
            });
        $('#material_weight_price,#purchase_price,#scrap_price,#depreciation_price,#estimate')
            .on('input', function() {
                let value = this.value;
                value = value.replace(/\./g, ',');
                value = value.replace(/[^0-9,]/g, '');
                const parts = value.split(',');
                if (parts.length > 2) {
                    value = parts[0] + ',' + parts.slice(1).join('');
                }

                this.value = value;
            });
        $('#part_weight_qty,#scrap_qty,#material_weight_qty').on('input', function() {
            let value = this.value;
            value = value.replace(',', '.');
            value = value.replace(/[^0-9.]/g, '');
            let parts = value.split('.');
            if (parts.length > 2) {
                parts = [parts[0], parts.slice(1).join('')];
            }
            if (parts[1]) {
                parts[1] = parts[1].substring(0, 3);
            }

            this.value = parts.join('.');
        })

        function parseQty(val) {
            if (!val) return 0;
            return parseFloat(val.replace(',', '.')) || 0;
        }

        function parsePrice(val) {
            if (!val) return 0;

            return parseFloat(
                val.toString().replace(/[.,]/g, '')
            ) || 0;
        }
        $("#material_weight_qty,#part_weight_qty").on('input', function() {
            const material_weight_qty = parseQty($("#material_weight_qty").val()) || 0
            const part_weight_qty = parseQty($("#part_weight_qty").val()) || 0
            if (isXCSelected) {
                const top_end = $("#top_end_coil").val();
                const scrap_val = material_weight_qty + parseQty(top_end) - part_weight_qty
                $("#scrap_qty").val(scrap_val.toFixed(3))
            } else {
                const scrap_val = material_weight_qty - part_weight_qty
                $("#scrap_qty").val(scrap_val.toFixed(3))
            }
        })
        $("#material_weight_qty,#material_weight_price").on('input', function() {
            const qty = parseQty($("#material_weight_qty").val());
            const price = parsePrice($("#material_weight_price").val());
            const result = qty * price;
            const excelResult = Math.round(result);
            $("#material_weight_estimate").val(
                excelResult.toLocaleString('en-US')
            );
        });
        $("#material_weight_qty").on('input', function() {
            if (isXCSelected) {
                const qty = parseQty($(this).val()) || 0;
                const onePercent = 0.01 * qty;
                $("#top_end_coil").val(onePercent.toFixed(3));
            } else {
                $("#top_end_coil").val('');
            }
        })
        $("#material_weight_price,#scrap_price,#material_weight_qty").on('input', function() {
            const top_end_coil = parseQty($("#top_end_coil").val()) || 0;
            console.log(top_end_coil)
            if (isXCSelected && top_end_coil > 0) {
                top_end_coli_calc();
            } else {
                default_calc();
            }

        })

        function default_calc() {
            const mtl = parsePrice($("#material_weight_estimate").val())
            const scrap_qty = parseQty($("#scrap_qty").val()) || 0;
            const scrap_price = parsePrice($("#scrap_price").val()) || 0;
            const scrap = scrap_qty * scrap_price;
            const results = mtl - scrap
            $("#material_cost_estimate").val(Math.round(results).toLocaleString('en-US'));
        }

        function top_end_coli_calc() {

            const material_weight_qty = parseQty($("#material_weight_qty").val()) || 0;
            const top_end_coil = parseQty($("#top_end_coil").val()) || 0;
            const mtl_price = parsePrice($("#material_weight_price").val()) || 0;

            const scrap_qty = parseQty($("#scrap_qty").val()) || 0;
            const scrap_price = parsePrice($("#scrap_price").val()) || 0;

            const material_total = (material_weight_qty + top_end_coil) * mtl_price;
            const scrap_total = scrap_qty * scrap_price;

            const results = material_total - scrap_total;

            $("#material_cost_estimate").val(
                Math.round(results).toLocaleString('en-US')
            );
        }

        $("#scrap_qty,#scrap_price").on('input', function() {
            const qty = parseQty($("#scrap_qty").val()) || 0;
            const price = parsePrice($("#scrap_price").val()) || 0;
            const scrap_estimate = -(qty * price);
            $("#scrap_estimate").val(Math.round(scrap_estimate).toLocaleString('en-US'));
        });
        $("#material_weight_qty").on('input', function() {
            const supplier_check = $("#supplier").val()
            if (supplier_check == '24~PT TRI CENTRUM FORTUNA') {
                const qty = parseQty($(this).val()) || 0;
                const onePercent = 0.01 * qty;

                $("#top_end_coil").val(onePercent.toFixed(3));
            } else {
                $("#top_end_coil").val('')
            }
        })
        $("#material_submit_btn").on('click', function() {
            const header_id = $("#header_id").val()
            const part_fg = $("#part_fg").val();
            const part_fg_desc = $("#part_fg_desc").val()
            const part_mtl = $("#part_mtl").val()
            const material_cost_spec = $("#material_cost_spec").val()
            const material_weight_qty = parseQty($("#material_weight_qty").val())
            const material_weight_price = parsePrice($("#material_weight_price").val())
            const material_sheet_price = parsePrice($("#material_sheet_price").val())
            const part_weight_qty = parseQty($("#part_weight_qty").val())
            const scrap_qty = parseQty($("#scrap_qty").val())
            const scrap_price = parsePrice($("#scrap_price").val())
            const uom = $("#uom").val()
            const mtl_c_est = parsePrice($("#material_cost_estimate").val())
            const mtl_w_est = parsePrice($("#material_weight_estimate").val())
            const scrap_est = parsePrice($("#scrap_estimate").val())
            const dep_qty = parseQty($("#depreciation_qty").val())
            const dep_price = parsePrice($("#depreciation_price").val())
            const dep_select = $("#depreciation_select").val()
            const volume_qty = $("#volume_qty").val()
            const note = $("#note").val()
            const effective_date = $("#effective_date").val()
            const expired_date = $("#expired_date").val()
            const top_end_coil = parsePrice($("#top_end_coil").val())
            if (!part_mtl || !part_fg || !material_weight_qty || !material_weight_price || !part_weight_qty || !
                scrap_qty || !scrap_price || !volume_qty) {
                Toast.fire({
                    position: 'top-end',
                    title: 'Beberapa kolom wajib di isi',
                    icon: "error"
                });
                return
            }
            if (note.length > 50) {
                Toast.fire({
                    position: 'top-end',
                    title: 'Catatan maksimum 50 karakter',
                    icon: "error"
                });
                return
            }
            if (dep_select == 'ya' && !dep_qty && !dep_price) {
                Toast.fire({
                    position: 'top-end',
                    title: 'Beberapa kolom wajib di isi',
                    icon: "error"
                });
                return
            }
            $.ajax({
                url: "{{ url('master_quotation/store_material') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    header_id: $("#header_id").val(),
                    part_fg: part_fg,
                    part_fg_desc: part_fg_desc,
                    part_mtl: part_mtl,
                    material_cost_spec: material_cost_spec,
                    material_weight_qty: material_weight_qty,
                    material_weight_price: material_weight_price,
                    material_sheet_price: material_sheet_price,
                    part_weight_qty: part_weight_qty,
                    scrap_qty: scrap_qty,
                    scrap_price: scrap_price,
                    material_weight_estimate: mtl_w_est,
                    material_cost_estimate: mtl_c_est,
                    scrap_estimate: scrap_est,
                    uom: uom,
                    dep_qty: dep_qty,
                    dep_price: dep_price,
                    volume_qty: volume_qty,
                    note: note,
                    effective_date: effective_date,
                    expired_date: expired_date,
                    top_end_coil: top_end_coil
                },
                success: function(response) {
                    const msg = response.message
                    const status = response.status
                    if (status == 'success') {
                        Toast.fire({
                            position: 'top-end',
                            title: msg,
                            icon: "success"
                        });
                        window.history.replaceState({}, '',
                            `<?php echo env('BASE_URL'); ?>/master_quotation?ref_doc=${response.ref_doc}`);
                        $("#process_tab_href").removeClass('disabled')
                        $("#purchase_tab_href").removeClass('disabled')
                        const tabEl = document.getElementById('purchase_tab_href');
                        const tab = new bootstrap.Tab(tabEl);
                        tab.show();
                        purchase_table()
                        $("#sub_total_tab_href").removeClass('disabled')
                    } else {
                        Toast.fire({
                            position: 'top-end',
                            title: msg,
                            icon: "error"
                        });
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText)
                    Toast.fire({
                        position: 'top-end',
                        title: 'Something went wrong',
                        icon: "error"
                    });
                }
            })
        })

        function process_tab_table_func() {
            const urlParams = new URLSearchParams(window.location.search);
            const ref_doc = urlParams.get('ref_doc');
            if ($.fn.DataTable.isDataTable('#process_tab_table')) {
                $('#process_tab_table').DataTable().clear().destroy();
            }
            $("#process_tab_table").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('master_quotation/show_process') }}",
                    type: "post",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}",
                            d.ref_doc = ref_doc
                    }
                },
                columns: [{
                    data: 'NameProcess'
                }, {
                    data: 'Machine'
                }, {
                    data: 'Stroke'
                }, {
                    data: 'Rate'
                }, {
                    data: 'Estimate'
                }, {
                    data: 'View'
                }]
            })
        }
        $("#process_tab_href").on('click', function() {
            process_tab_table_func()
        })
        $("#stroke,#rate").on('input', function() {
            const stroke = parseQty($("#stroke").val())
            const rate = $("#rate").val()
            const result = stroke * rate
            if (Number.isInteger(result)) {
                $("#estimate").val(result);
            } else {
                $("#estimate").val(result.toFixed(2));
            }
        })
        $("#save_process_btn").on('click', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const ref_doc = urlParams.get('ref_doc');
            const name_process = $("#name_process").val()
            const machine = $("#machine").val()
            const stroke = $("#stroke").val()
            const rate = $("#rate").val()
            if (!name_process || !machine || !stroke || !rate) {
                Toast.fire({
                    position: 'top-end',
                    title: 'Beberapa kolom wajib di isi',
                    icon: "error"
                });
                return
            }
            $.ajax({
                url: "{{ url('master_quotation/store_process') }}",
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    ref_doc: ref_doc,
                    name_process: name_process,
                    machine: machine,
                    stroke: stroke,
                    rate: rate,
                    estimate: $("#estimate").val()
                },
                success: function(response) {
                    const status = response.status
                    const msg = response.message
                    Toast.fire({
                        position: 'top-end',
                        title: msg,
                        icon: status
                    });
                    $("#process_tab_table").DataTable().ajax.reload(null, false)
                    const modalEl = document.getElementById('ProcessModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    modal.hide();
                },
                error: function(xhr) {
                    console.log(xhr)
                    Toast.fire({
                        position: 'top-end',
                        title: 'Something went wrong',
                        icon: "error"
                    });
                }
            })
        })
        $("#createBtnProcess").on('click', function() {
            $("#ProcessModalLabel").text('Create Process')
            $("#update_process_btn").addClass('d-none')
            $("#save_process_btn").removeClass('d-none')
            $("#name_process").val('')
            $("#machine").val('')
            $("#stroke").val('')
            $("#rate").val('')
            $("#estimate").val('')
        })

        function editProcess(id) {
            $.ajax({
                url: "{{ url('master_quotation/find_process') }}",
                type: "post",
                data: {
                    _token: "{{ csrf_token() }}",
                    processID: id
                },
                success: function(response) {
                    const status = response.status
                    const data = response.data
                    if (status == 'success') {
                        $("#ProcessModal").modal('show')
                        $("#ProcessModalLabel").text('Edit Process')
                        $("#update_process_btn").removeClass('d-none')
                        $("#save_process_btn").addClass('d-none')
                        $("#name_process").val(data.NameProcess)
                        $("#machine").val(data.Machine)
                        $("#stroke").val(data.Stroke)
                        $("#rate").val(Number(data.Rate).toLocaleString('en-US'))
                        $("#estimate").val(Number(data.Estimate).toLocaleString('en-US'))
                        $("#process_id").val(id)
                    } else {
                        Toast.fire({
                            position: 'top-end',
                            title: response.message,
                            icon: "error"
                        });
                    }
                },
                error: function(xhr) {
                    console.log(xhr)
                    Toast.fire({
                        position: 'top-end',
                        title: 'Something went wrong',
                        icon: "error"
                    });
                }
            })
        }
        $("#update_process_btn").on('click', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const ref_doc = urlParams.get('ref_doc');
            const name_process = $("#name_process").val()
            const machine = $("#machine").val()
            const stroke = $("#stroke").val()
            const rate = $("#rate").val()
            if (!name_process || !machine || !stroke || !rate) {
                Toast.fire({
                    position: 'top-end',
                    title: 'Beberapa kolom wajib di isi',
                    icon: "error"
                });
                return
            }
            $.ajax({
                url: "{{ url('master_quotation/store_process') }}",
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    ref_doc: ref_doc,
                    process_id: $("#process_id").val(),
                    name_process: name_process,
                    machine: machine,
                    stroke: stroke,
                    rate: rate,
                    estimate: $("#estimate").val()
                },
                success: function(response) {
                    const status = response.status
                    const msg = response.message
                    Toast.fire({
                        position: 'top-end',
                        title: msg,
                        icon: status
                    });
                    $("#process_tab_table").DataTable().ajax.reload(null, false)
                    const modalEl = document.getElementById('ProcessModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    modal.hide();
                },
                error: function(xhr) {
                    console.log(xhr)
                    Toast.fire({
                        position: 'top-end',
                        title: 'Something went wrong',
                        icon: "error"
                    });
                }
            })
        })

        function hapusProcess(id) {
            Swal.fire({
                title: "Anda yakin?",
                text: "Data akan di hapus secara permanen!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, hapus!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('master_quotation/delete_process') }}",
                        type: 'post',
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: id
                        },
                        success: function(response) {
                            const status = response.status
                            const msg = response.message
                            Toast.fire({
                                position: 'top-end',
                                title: msg,
                                icon: status
                            });
                            $("#process_tab_table").DataTable().ajax.reload(null, false)
                        },
                        error: function(xhr) {
                            console.log(xhr)
                            Toast.fire({
                                position: 'top-end',
                                title: 'Something went wrong',
                                icon: 'error'
                            });
                        }
                    })
                }
            });
        }
        $("#sub_total_tab_href").on('click', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const ref_doc = urlParams.get('ref_doc');
            console.log(ref_doc)
            sub_total_table_func()
        })

        function sub_total_table_func() {
            const urlParams = new URLSearchParams(window.location.search);
            const ref_doc = urlParams.get('ref_doc');
            if ($.fn.DataTable.isDataTable('#sub_total_table')) {
                $('#sub_total_table').DataTable().clear().destroy();
            }
            $("#sub_total_table").DataTable({
                processing: true,
                serverSide: true,
                paging: false,
                lengthChange: false,
                info: false,
                ajax: {
                    url: "{{ url('master_quotation/show_other_cost') }}",
                    type: 'post',
                    data: function(d) {
                        d._token = '{{ csrf_token() }}',
                            d.ref_doc = ref_doc
                    }
                },
                columns: [{
                    data: 'No'
                }, {
                    data: 'NameItem'
                }, {
                    data: 'Percentage'
                }, {
                    data: 'Quantity'
                }, {
                    data: 'Estimate'
                }, {
                    data: 'View'
                }]
            })
        }

        function toDateInput(value) {
            return value ? value.split(' ')[0] : '';
        }

        function formatRupiah(value) {
            const num = Number(value);
            if (!Number.isFinite(num)) return '';
            return num.toLocaleString('en-US');
        }

        function safeFixed(value, digit = 4) {
            const num = Number(value);
            if (!Number.isFinite(num)) return '';
            return num.toFixed(digit);
        }

        function part_preview(id) {
            window.history.replaceState({}, '',
                `<?php echo env('BASE_URL'); ?>/master_quotation?ref_preview=${id}`);
            if ($.fn.DataTable.isDataTable('#master_part_table')) {
                $('#master_part_table').DataTable().clear().destroy();
            }
            $("#master_price_content").addClass('d-none')
            $("#preview_data").addClass('d-none')
            $("#master_part_content").removeClass('d-none')
            $("#master_part_table").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('master_quotation/show_data_part') }}",
                    type: 'post',
                    data: function(d) {
                        d._token = "{{ csrf_token() }}",
                            d.id = id,
                            d.search = $("#part_search").val()
                    }
                },
                columns: [{
                    data: 'No',
                    className: 'text-center'
                }, {
                    data: 'PartFG',
                    className: 'text-center'
                }, {
                    data: 'PartFGDesc',
                    className: 'text-center'
                }, {
                    data: 'PartMtl',
                    className: 'text-center'
                }, {
                    data: 'PartMtlDesc',
                    className: 'text-center'
                }, {
                    data: 'Status',
                    className: 'text-center'
                }, {
                    data: 'View'
                }]
            })
        }
        $("#part_search").on('change', function() {
            $("#master_part_table").DataTable().ajax.reload(null, false)
        })
        $("#importMasterPrice").on('click', function() {
            $("#master_price_content").addClass('d-none')
            $("#preview_data").addClass('d-none')
            $("#create_master_price").removeClass('d-none')
        })
        $("#submit_import").on('click', function() {
            const $btnText = $("#submit_import_text");
            const $spinner = $("#spinner_import");
            const inputFile = $("#file_import")[0];
            const file = inputFile.files[0];
            $btnText.text('Submit');
            $spinner.hide();
            if (!file) {
                Toast.fire({
                    position: 'top-end',
                    title: 'File wajib dipilih',
                    icon: "error"
                });
                return;
            }
            const allowedTypes = [
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ];

            if (!allowedTypes.includes(file.type)) {
                Toast.fire({
                    position: 'top-end',
                    title: 'File harus berformat Excel (.xls atau .xlsx)',
                    icon: "error"
                });
                return;
            }
            const maxSize = 5 * 1024 * 1024;
            if (file.size > maxSize) {
                Toast.fire({
                    position: 'top-end',
                    title: 'Ukuran file maksimal 5MB',
                    icon: "error"
                });
                return;
            }
            $btnText.text('Loading...');
            $spinner.show();
            const formData = new FormData();
            formData.append('file_import', file);
            formData.append('_token', "{{ csrf_token() }}");

            $.ajax({
                url: "{{ url('master_quotation/import') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.status == 200) {
                        Toast.fire({
                            position: 'top-end',
                            title: res.message || 'Import berhasil',
                            icon: "success"
                        });
                    } else {
                        Toast.fire({
                            position: 'top-end',
                            title: res.message || 'Import gagal',
                            icon: "error"
                        });
                    }
                },
                error: function() {
                    Toast.fire({
                        position: 'top-end',
                        title: 'Gagal upload file',
                        icon: "error"
                    });
                },
                complete: function() {
                    $btnText.text('Submit');
                    $spinner.hide();
                }
            });
        });

        function delete_document(id) {
            Swal.fire({
                title: "Apakaha anda yakin?",
                text: "Data ini akan di hapus permanent!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, hapus!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('master_quotation/delete_document') }}",
                        type: "post",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: id
                        },
                        success: function(response) {
                            if (response.status == 'success') {
                                Toast.fire({
                                    position: 'top-end',
                                    title: 'Data berhasil di hapus',
                                    icon: "success"
                                });
                                master_price_table.ajax.reload(null, false);
                            } else {
                                Toast.fire({
                                    position: 'top-end',
                                    title: response.message,
                                    icon: "error"
                                });
                            }
                        },
                        error: function(xhr) {
                            console.log(xhr)
                            Toast.fire({
                                position: 'top-end',
                                title: 'Something went wrong!',
                                icon: "error"
                            });
                        }
                    })
                }
            });
        }
        $("#purchase_tab_href").on('click', function() {
            purchase_table()
        })

        function purchase_table() {
            const urlParams = new URLSearchParams(window.location.search);
            const ref_doc = urlParams.get('ref_doc');
            if ($.fn.DataTable.isDataTable('#purchase_tab_table')) {
                $('#purchase_tab_table').DataTable().clear().destroy();
            }
            $("#purchase_tab_table").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('master_quotation/show_purchase') }}",
                    type: 'post',
                    data: function(d) {
                        d._token = "{{ csrf_token() }}",
                            d.ref_doc = ref_doc
                    }
                },
                columns: [{
                    data: 'No'
                }, {
                    data: 'PurchasePart'
                }, {
                    data: 'SpecPurchasePart'
                }, {
                    data: 'Qty'
                }, {
                    data: 'Price'
                }, {
                    data: 'Estimate'
                }, {
                    data: 'View'
                }]
            })
        }
        $("#purchase_qty,#purchase_price").on('input', function() {
            const qty = $("#purchase_qty").val()
            const price = $("#purchase_price").val()
            const results = parsePrice(price) * parseQty(qty)
            $("#purchase_estimate").val(results.toLocaleString('en-US'))
        })
        $("#type_purchase").on('change', function() {
            const type = $("#type_purchase").val()
            if (type == 'supply') {
                $("#purchase_qty").val(0).prop('readonly', true)
                $("#purchase_price").val(0).prop('readonly', true)
                $("#purchase_estimate").val(0)
            } else {
                $("#purchase_qty").val('').prop('readonly', false)
                $("#purchase_price").val('').prop('readonly', false)
                $("#purchase_estimate").val('')
            }
        })
        $("#save_purchase_btn").on('click', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const ref_doc = urlParams.get('ref_doc');
            const purchase_part = $("#purchase_part").val()
            const spec_purchase_part = $("#spec_purchase_part").val()
            const qty = $("#purchase_qty").val()
            const price = $("#purchase_price").val()
            const estimate = $("#purchase_estimate").val()
            if (!purchase_part || !spec_purchase_part || !qty || !price || !estimate) {
                Toast.fire({
                    position: 'top-end',
                    title: 'Semua kolom wajib di isi',
                    icon: 'error'
                });
                return
            }
            $.ajax({
                url: "{{ url('master_quotation/purchase_store') }}",
                type: 'post',
                data: {
                    _token: '{{ csrf_token() }}',
                    ref_doc: ref_doc,
                    purchase_id: null,
                    purchase_part: purchase_part,
                    spec_purchase_part: spec_purchase_part,
                    qty: parseQty(qty),
                    price: parsePrice(price),
                    estimate: parsePrice(estimate)
                },
                success: function(response) {
                    const status = response.status
                    const msg = response.message
                    Toast.fire({
                        position: 'top-end',
                        title: msg,
                        icon: status
                    });
                    $("#purchase_tab_table").DataTable().ajax.reload(null, false);
                    const modalEl = document.getElementById('purchaseModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    modal.hide();
                },
                error: function(xhr) {
                    console.log(xhr)
                    Toast.fire({
                        position: 'top-end',
                        title: 'Something went wrong',
                        icon: 'error'
                    });
                }
            })
        })
        $("#createPurchase").on('click', function() {
            $("#purchaseModal").modal('show');
            $("#purchaseBackdropLabel").text('Create Purchase')
            $("#save_purchase_btn").removeClass('d-none')
            $("#update_purchase_btn").addClass('d-none')
            $("#purchase_part").val('').prop('disabled', false)
            $("#spec_purchase_part").val('')
            $("#purchase_qty").val('')
            $("#purchase_price").val('')
            $("#purchase_estimate").val('')
            $("#purchase_id").val('')
            purchase_part()
        })

        function purchase_part() {
            $("#purchase_part").select2({
                placeholder: 'Pilih Purchase Part',
                minimumInputLength: 0,
                allowClear: true,
                dropdownParent: $("#purchaseModal"),
                ajax: {
                    url: "{{ url('master_quotation/get_purchase_part') }}",
                    method: "POST",
                    dataType: 'json',
                    delay: 300,
                    data: function(params) {
                        return {
                            _token: "{{ csrf_token() }}",
                            search: params.term || '',
                            page: params.page || 1
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;

                        return {
                            results: data.results,
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },
                    cache: true
                }
            })
            $(document).on('select2:select', '#purchase_part', function(e) {
                const data = e.params.data;
                $('#spec_purchase_part').val(data.Desc || '')
            });
        }

        function editPurchase(id) {
            $.ajax({
                url: "{{ url('master_quotation/find_purchase') }}",
                type: 'post',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id
                },
                success: function(response) {
                    if (response.status == 'success') {
                        $("#purchaseModal").modal('show');
                        $("#purchaseBackdropLabel").text('Update Purchase')
                        $("#save_purchase_btn").addClass('d-none')
                        $("#update_purchase_btn").removeClass('d-none')
                        const data = response.data
                        const option = new Option(
                            data.PurchasePart,
                            data.PurchasePart,
                            true,
                            true
                        );
                        $("#purchase_part").append(option).trigger('change').prop('disabled', true)
                        $("#spec_purchase_part").val(data.SpecPurchasePart)
                        $("#purchase_qty").val(Number(data.Qty).toFixed(0))
                        $("#purchase_price").val(Number(data.Price).toLocaleString('en-US'))
                        $("#purchase_estimate").val(Number(data.Estimate).toLocaleString('en-US'))
                        $("#purchase_id").val(id)
                    } else {
                        Toast.fire({
                            position: 'top-end',
                            title: response.message,
                            icon: 'error'
                        });
                    }
                },
                error: function(xhr) {
                    console.log(xhr)
                    Toast.fire({
                        position: 'top-end',
                        title: 'Something went wrong',
                        icon: 'error'
                    });
                }
            })
        }
        $("#update_purchase_btn").on('click', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const ref_doc = urlParams.get('ref_doc');
            const id = $("#purchase_id").val()
            const purchase_part = $("#purchase_part").val()
            const spec_purchase_part = $("#spec_purchase_part").val()
            const qty = $("#purchase_qty").val()
            const price = $("#purchase_price").val()
            const estimate = $("#purchase_estimate").val()
            if (!purchase_part || !spec_purchase_part || !qty || !price || !estimate) {
                Toast.fire({
                    position: 'top-end',
                    title: 'Semua kolom wajib di isi',
                    icon: 'error'
                });
                return
            }
            $.ajax({
                url: "{{ url('master_quotation/purchase_store') }}",
                type: 'post',
                data: {
                    _token: '{{ csrf_token() }}',
                    ref_doc: ref_doc,
                    purchase_id: id,
                    purchase_part: purchase_part,
                    spec_purchase_part: spec_purchase_part,
                    qty: parseQty(qty),
                    price: parsePrice(price),
                    estimate: parsePrice(estimate)
                },
                success: function(response) {
                    const status = response.status
                    const msg = response.message
                    Toast.fire({
                        position: 'top-end',
                        title: msg,
                        icon: status
                    });
                    $("#purchase_tab_table").DataTable().ajax.reload(null, false);
                    const modalEl = document.getElementById('purchaseModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    modal.hide();
                },
                error: function(xhr) {
                    console.log(xhr)
                    Toast.fire({
                        position: 'top-end',
                        title: 'Something went wrong',
                        icon: 'error'
                    });
                }
            })
        })

        function hapusPurchase(id) {
            Swal.fire({
                title: "Anda yakin?",
                text: "Data akan di hapus secara permanen!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, hapus!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('master_quotation/delete_purchase') }}",
                        type: 'post',
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: id
                        },
                        success: function(response) {
                            const status = response.status
                            const msg = response.message
                            Toast.fire({
                                position: 'top-end',
                                title: msg,
                                icon: status
                            });
                            $("#purchase_tab_table").DataTable().ajax.reload(null, false);
                        },
                        error: function(xhr) {
                            console.log(xhr)
                            Toast.fire({
                                position: 'top-end',
                                title: 'Something went wrong',
                                icon: 'error'
                            });
                        }
                    })
                }
            });
        }

        function document_preview(ref_doc) {
            window.history.replaceState({}, '', `<?php echo env('BASE_URL'); ?>/master_quotation?ref_doc=${ref_doc}`);
            $("#submit_master_header").addClass('d-none');
            $("#backBtn").removeClass('d-none');
            $("#backBtnCrt").addClass('d-none');

            $("#material_tab_href, #purchase_tab_href, #process_tab_href, #sub_total_tab_href")
                .removeClass('disabled');

            $('a[href="#header_tab"]').tab('show');
            $("#period").val(null).trigger('change');
            $.ajax({
                url: "{{ url('master_quotation/preview') }}",
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    ref_doc: ref_doc
                },
                success: function(response) {
                    if (response.status == 'success') {
                        $("#master_part_content").addClass('d-none')
                        $("#preview_data").removeClass('d-none')
                        $("#submit_master_header").addClass('d-none')
                        $("#update_master_header").removeClass('d-none')
                        const data = response.data
                        let supplierName = data.SupplierName;
                        let supplierNum = data.SupplierNum;
                        let option = new Option(supplierName, supplierNum, true, true);
                        $('#supplier').append(option).trigger('change');
                        $('#supplier').prop('disabled', true);
                        let optionCus = new Option(data.Customer, data.Customer, true, true);
                        $('#customer').append(optionCus).prop('disabled', true)
                        $("#material_tab_href").removeClass('disabled')
                        $("#purchase_tab_href").removeClass('disabled')
                        $("#process_tab_href").removeClass('disabled')
                        $("#sub_total_tab_href").removeClass('disabled')
                        $("#material_submit_btn").addClass('d-none')
                        $('#material_update_btn').removeClass('d-none')
                        if (data.PartMtl) {
                            let optionPartNum = new Option(
                                data.PartMtl,
                                data.PartMtl,
                                true,
                                true
                            );
                            $('#part_mtl').append(optionPartNum).prop('disabled', true).addClass(
                                'bg-light-primary')
                        }
                        if (data.PartFG) {
                            let optionFG = new Option(
                                data.PartFG,
                                data.PartFG,
                                true,
                                true
                            );
                            $('#part_fg').append(optionFG).prop('disabled', true).addClass('bg-light-primary')
                        }
                        $("#part_fg_desc").val(data.PartFGDesc)
                        $("#material_cost_spec").val(data.PartMtlDesc)
                        $("#material_weight_qty").val(parseFloat(data.MtlWQty).toFixed(3))
                        $("#part_weight_qty").val(parseFloat(data.PartWQty).toFixed(3))
                        $("#uom").val(data.UOM)
                        if (data.UOM == 'SHEET') {
                            $("#div_mtl_sheet").removeClass('d-none')
                            $("#material_sheet_price").val(Number(data.MtlSPrice).toLocaleString('en-US'))
                        }
                        $("#scrap_qty").val(parseFloat(data.ScrapQty).toFixed(3))
                        $("#material_cost_estimate").val(Number(data.MtlCEstimate).toLocaleString(
                            'en-US'))
                        $("#scrap_estimate").val(Number(data.ScrapEstimate).toLocaleString('en-US'))
                        $("#material_weight_price").val(
                            Number(data.MtlWPrice || 0).toLocaleString('en-US')
                        );
                        $("#scrap_price").val(Number(data.ScrapPrice || 0).toLocaleString('en-US'))
                        $("#material_weight_estimate").val(Number(data.MtlWEstimate || 0)
                            .toLocaleString('en-US'))
                        const DepreciationQty = Number(data.DepreciationQty).toLocaleString('id-ID', {
                            maximumFractionDigits: 0
                        })
                        const DepreciationPrice = Number(data.DepreciationPrice)
                        if (DepreciationPrice > 0) {
                            $('#depreciation_select').val('ya').trigger('change');
                            $("#dep_qty").removeClass('d-none')
                            $("#dep_price").removeClass('d-none')
                            $("#depreciation_qty").val(DepreciationQty)
                            $("#depreciation_price").val(DepreciationPrice.toLocaleString('en-US'))
                        } else {
                            $('#depreciation_select').val('tidak').trigger('change');
                        }
                        if (data.TopEndCoil > 0) {
                            $("#div_top_end_coil").removeClass('d-none')
                            $("#top_end_coil").val(data.TopEndCoil)
                        }
                        $("#volume_qty").val(Number(data.VolQty))
                        const status = data.Status
                        if (status == 1) {
                            $("#confirmBtnOtherCost").addClass('d-none')
                            $("#cancelConfirmOtherCost").removeClass('d-none')
                        } else {
                            $("#confirmBtnOtherCost").removeClass('d-none')
                            $("#cancelConfirmOtherCost").addClass('d-none')
                        }
                        if (data.PeriodID !== null && data.period !== null) {
                            $("#effective_date_div").addClass('d-none')
                            $("#expired_date_div").addClass('d-none')
                        } else {
                            $("#effective_date_div").removeClass('d-none')
                            $("#expired_date_div").removeClass('d-none')
                            $("#effective_date").val(data.EffectiveDate)
                            $("#expired_date").val(data.ExpiredDate)
                        }
                        const period = data.PeriodID
                        const periodText = data.EffectiveDate + ' - ' + data.ExpiredDate
                        $('#period').select2({
                            placeholder: 'Pilih Period',
                            minimumInputLength: 0,
                            allowClear: true,
                            ajax: {
                                url: "{{ url('master_quotation/get_period') }}",
                                method: "POST",
                                dataType: 'json',
                                delay: 300,
                                data: function(params) {
                                    return {
                                        _token: "{{ csrf_token() }}",
                                        search: params.term || '',
                                        page: params.page || 1
                                    };
                                },
                                processResults: function(data, params) {
                                    params.page = params.page || 1;

                                    return {
                                        results: data.results,
                                        pagination: {
                                            more: data.pagination.more
                                        }
                                    };
                                },
                                cache: true
                            }
                        });

                        if (period) {
                            const option = new Option(periodText, period, true, true);
                            $('#period').append(option).trigger('change');
                        }
                        $(document).on('select2:select', '#period', function(e) {
                            const data = e.params.data;

                            if (data.id === 'AddItem') {

                                $('#period').val(null).trigger('change');

                                Swal.fire({
                                    title: 'Add New Period',
                                    width: '700px',
                                    html: `
                <div class='row mb-5'>
                <div class='col-md-6'>
                    <label class='form-label'>Effective>Effective Date</label>
                    <input type="date" id="EffectiveDate" class="swal2-input" placeholder="Effective Date">
                </div>
                <div class='col-md-6'>
                    <input type="date" id="ExpiredDate" class="swal2-input" placeholder="Expired Date">
                </div>
                </div>
            `,
                                    focusConfirm: false,
                                    showCancelButton: true,
                                    confirmButtonText: 'Save',
                                    cancelButtonText: 'Cancel',

                                    customClass: {
                                        confirmButton: 'btn btn-primary',
                                        cancelButton: 'btn btn-secondary'
                                    },

                                    buttonsStyling: false,
                                    preConfirm: () => {

                                        const effective = document.getElementById(
                                            'EffectiveDate').value;
                                        const expired = document.getElementById(
                                            'ExpiredDate').value;

                                        if (!effective || !expired) {
                                            Swal.showValidationMessage(
                                                'Both dates are required');
                                            return false;
                                        }

                                        return {
                                            EffectiveDate: effective,
                                            ExpiredDate: expired
                                        };
                                    }
                                }).then((result) => {

                                    if (result.isConfirmed) {

                                        $.ajax({
                                            url: "{{ url('master_quotation/add_period') }}",
                                            type: "POST",
                                            data: {
                                                _token: "{{ csrf_token() }}",
                                                EffectiveDate: result.value
                                                    .EffectiveDate,
                                                ExpiredDate: result.value.ExpiredDate
                                            },
                                            success: function(res) {
                                                const status = res.status
                                                if (status == 'success') {
                                                    Swal.fire({
                                                        icon: 'success',
                                                        title: 'Period added',
                                                        timer: 1500,
                                                        showConfirmButton: false
                                                    });
                                                    $('#period').select2('open');
                                                } else {
                                                    Swal.fire({
                                                        icon: 'error',
                                                        title: res.message,
                                                        timer: 1500,
                                                        showConfirmButton: false
                                                    });
                                                }
                                            }
                                        });

                                    }

                                });

                            }
                        });

                    } else {
                        Toast.fire({
                            position: 'top-end',
                            title: 'Something went wrong',
                            icon: 'error'
                        });
                    }
                }
            })
        }
        $("#update_master_header").on('click', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const ref_doc = urlParams.get('ref_doc');
            const period = $("#period").val()
            if (!period) {
                Toast.fire({
                    position: 'top-end',
                    title: 'Period wajib di isi',
                    icon: "error"
                });
                return
            }
            $.ajax({
                url: "{{ url('master_quotation/update_master_header') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    ref_doc: ref_doc,
                    period: period
                },
                success: function(response) {
                    const status = response.status
                    const msg = response.message
                    if (status == 'success') {
                        Toast.fire({
                            position: 'top-end',
                            title: msg,
                            icon: "success"
                        });
                        document_preview(ref_doc)
                    } else {
                        Toast.fire({
                            position: 'top-end',
                            title: msg,
                            icon: "error"
                        });
                    }
                },
                error: function(xhr) {
                    console.log(xhr)
                    Toast.fire({
                        position: 'top-end',
                        title: 'Something went wrong',
                        icon: "error"
                    });
                }
            })
        })
        $("#material_update_btn").on('click', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const ref_doc = urlParams.get('ref_doc');
            const part_fg = $("#part_fg").val();
            const part_fg_desc = $("#part_fg_desc").val()
            const part_mtl = $("#part_mtl").val()
            const material_cost_spec = $("#material_cost_spec").val()
            const material_weight_qty = parseQty($("#material_weight_qty").val())
            const material_weight_price = parsePrice($("#material_weight_price").val())
            const material_sheet_price = parsePrice($("#material_sheet_price").val())
            const part_weight_qty = parseQty($("#part_weight_qty").val())
            const scrap_qty = parseQty($("#scrap_qty").val())
            const scrap_price = parsePrice($("#scrap_price").val())
            const uom = $("#uom").val()
            const mtl_c_est = parsePrice($("#material_cost_estimate").val())
            const mtl_w_est = parsePrice($("#material_weight_estimate").val())
            const scrap_est = parsePrice($("#scrap_estimate").val())
            const dep_qty = parseQty($("#depreciation_qty").val())
            const dep_price = parsePrice($("#depreciation_price").val())
            const dep_select = $("#depreciation_select").val()
            const volume_qty = $("#volume_qty").val()
            const note = $("#note").val()
            // const effective_date = $("#effective_date").val()
            // const expired_date = $("#expired_date").val()
            const top_end_coil = parsePrice($("#top_end_coil").val())
            if (!part_mtl || !part_fg || !material_weight_qty || !material_weight_price || !part_weight_qty || !
                scrap_qty || !scrap_price || !volume_qty) {
                Toast.fire({
                    position: 'top-end',
                    title: 'Beberapa kolom wajib di isi',
                    icon: "error"
                });
                return
            }
            if (note.length > 50) {
                Toast.fire({
                    position: 'top-end',
                    title: 'Catatan maksimum 50 karakter',
                    icon: "error"
                });
                return
            }
            if (dep_select == 'ya' && !dep_qty && !dep_price) {
                Toast.fire({
                    position: 'top-end',
                    title: 'Beberapa kolom wajib di isi',
                    icon: "error"
                });
                return
            }
            $.ajax({
                url: "{{ url('master_quotation/update_material') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    ref_doc: ref_doc,
                    part_fg: part_fg,
                    part_fg_desc: part_fg_desc,
                    part_mtl: part_mtl,
                    material_cost_spec: material_cost_spec,
                    material_weight_qty: material_weight_qty,
                    material_weight_price: material_weight_price,
                    material_sheet_price: material_sheet_price,
                    part_weight_qty: part_weight_qty,
                    scrap_qty: scrap_qty,
                    scrap_price: scrap_price,
                    material_weight_estimate: mtl_w_est,
                    material_cost_estimate: mtl_c_est,
                    scrap_estimate: scrap_est,
                    uom: uom,
                    dep_qty: dep_qty,
                    dep_price: dep_price,
                    volume_qty: volume_qty,
                    note: note,
                    // effective_date: effective_date,
                    // expired_date: expired_date,
                    top_end_coil: top_end_coil
                },
                success: function(response) {
                    const msg = response.message
                    const status = response.status
                    if (status == 'success') {
                        Toast.fire({
                            position: 'top-end',
                            title: msg,
                            icon: "success"
                        });
                        // window.history.replaceState({}, '',
                        //     `/master_quotation?ref_doc=${response.ref_doc}`);
                        $("#process_tab_href").removeClass('disabled')
                        $("#purchase_tab_href").removeClass('disabled')
                        const tabEl = document.getElementById('purchase_tab_href');
                        const tab = new bootstrap.Tab(tabEl);
                        tab.show();
                        purchase_table()
                        $("#sub_total_tab_href").removeClass('disabled')
                    } else {
                        Toast.fire({
                            position: 'top-end',
                            title: msg,
                            icon: "error"
                        });
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText)
                    Toast.fire({
                        position: 'top-end',
                        title: 'Something went wrong',
                        icon: "error"
                    });
                }
            })
        })

        function hapusDoc(ref_doc) {
            Swal.fire({
                title: "Anda yakin?",
                text: "Data akan di hapus secara permanen!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, hapus!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('master_quotation/delete_part') }}",
                        type: 'post',
                        data: {
                            _token: "{{ csrf_token() }}",
                            ref_doc: ref_doc
                        },
                        success: function(response) {
                            const status = response.status
                            const msg = response.message
                            Toast.fire({
                                position: 'top-end',
                                title: msg,
                                icon: status
                            });
                            $("#master_part_table").DataTable().ajax.reload(null, false);
                        },
                        error: function(xhr) {
                            console.log(xhr)
                            Toast.fire({
                                position: 'top-end',
                                title: 'Something went wrong',
                                icon: 'error'
                            });
                        }
                    })
                }
            });
        }
        $("#createBtnOtherCost").on('click', function() {
            $("#otherCostLabel").text('Create Other Cost')
            $("#name_item_other_cost").val('').removeClass('bg-light-primary').prop('readonly', false)
            $("#percentage_other_cost").val('')
            $("#estimate_other_cost").val('')
            $("#save_other_cost_btn").removeClass('d-none')
            $("#update_other_cost_btn").addClass('d-none')
        })
        $("#addition_type_other_cost").on('change', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const ref_doc = urlParams.get('ref_doc');
            const name_item_val = $("#addition_type_other_cost").val()
            $.ajax({
                url: "{{ url('master_quotation/name_item_other_cost') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    ref_doc: ref_doc,
                    item: name_item_val
                },
                success: function(response) {
                    if (name_item_val == 'discount') {
                        $("#div_percentage").hide()
                        $("#div_blank_price").addClass('d-none')
                        $("#estimate_other_cost").removeClass('bg-light-primary').prop('readonly',
                            false)
                    } else if (name_item_val == 'blank_cost') {
                        $("#div_percentage").hide()
                        $("#div_blank_price").removeClass('d-none')
                        $("#estimate_other_cost").removeClass('bg-light-primary').prop('readonly',
                            false)
                    } else {
                        $("#div_percentage").show()
                        $("#div_blank_price").addClass('d-none')
                        $("#estimate_other_cost").addClass('bg-light-primary').prop('readonly',
                            true)
                    }
                    $("#name_item_val").val(Number(response))
                    $("#percentage_other_cost").val('')
                },
                error: function(xhr) {
                    console.log(xhr)
                }
            })
        })
        $("#blank_price").on('input', function() {
            const x_result = $("#name_item_val").val()
            const blank = $("#blank_price").val()
            const out = parseQty(x_result) * parsePrice(blank)
            $("#estimate_other_cost").val(out.toLocaleString('en-US'))
        })
        $("#percentage_other_cost").on('input', function() {
            const x_result = $("#name_item_val").val()
            const percen = $("#percentage_other_cost").val()
            const results = (percen / 100) * x_result
            $("#estimate_other_cost").val(results.toLocaleString('en-US'))
        })
        $("#save_other_cost_btn").on('click', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const ref_doc = urlParams.get('ref_doc');
            const name = $("#name_item_other_cost").val()
            const percen = $("#percentage_other_cost").val()
            const estimate = $("#estimate_other_cost").val()
            const addition_type_other_cost = $("#addition_type_other_cost").val()
            if (!name || !estimate || !addition_type_other_cost) {
                Toast.fire({
                    position: 'top-end',
                    title: 'Semua kolom wajib di isi',
                    icon: "error"
                });
                return
            }
            console.log(estimate)
            $.ajax({
                url: "{{ url('master_quotation/store_other_cost') }}",
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    ref_doc: ref_doc,
                    name: name,
                    percen: percen,
                    estimate: estimate,
                    addition_type_other_cost: addition_type_other_cost
                },
                success: function(response) {
                    const status = response.status
                    const msg = response.message
                    Toast.fire({
                        position: 'top-end',
                        title: msg,
                        icon: status
                    });
                    $("#sub_total_table").DataTable().ajax.reload(null, false)
                    const modalEl = document.getElementById('otherCostModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    modal.hide();
                },
                error: function(xhr) {
                    console.log(xhr)
                    Toast.fire({
                        position: 'top-end',
                        title: 'Something went wrong',
                        icon: "error"
                    });
                }
            })
        })

        function otherEdit(id) {
            $.ajax({
                url: "{{ url('master_quotation/find_other_cost') }}",
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    OtherCostID: id
                },
                success: function(response) {
                    if (response.status == 'success') {
                        $("#otherCostModal").modal('show')
                        $("#save_other_cost_btn").addClass('d-none')
                        $("#update_other_cost_btn").removeClass('d-none')
                        const data = response.data
                        $("#other_cost_id").val(data.OtherCostID)
                        $("#name_item_other_cost").val(data.NameItem).prop('readonly', true).addClass(
                            'bg-light-primary')
                        $("#addition_type_other_cost").val(data.AdditionType)
                        if (data.AdditionType == 'discount') {
                            $("#otherCostLabel").text('Update Other Cost')
                            $("#div_percentage").hide()
                            $("#estimate_other_cost").val(Number(data.Estimate).toLocaleString('en-US'))
                                .removeClass('bg-light-primary').prop('readonly', false)
                        } else {
                            $("#percentage_other_cost").val(Number(data.Percentage).toFixed(0))
                            $("#estimate_other_cost").val(Number(data.Estimate).toLocaleString('en-US'))
                        }
                        $("#name_item_val").val(Number(response.results))
                    } else {
                        Toast.fire({
                            position: 'top-end',
                            title: response.message,
                            icon: "error"
                        });
                    }
                },
                error: function(xhr) {
                    console.log(xhr)
                }
            })
        }
        $("#update_other_cost_btn").on('click', function() {
            const id = $("#other_cost_id").val()
            const percen = $("#percentage_other_cost").val()
            const estimate = $("#estimate_other_cost").val().replace(/,/g, '');
            const addition_type_other_cost = $("#addition_type_other_cost").val()
            if (!id || !estimate || !addition_type_other_cost) {
                Toast.fire({
                    position: 'top-end',
                    title: 'Semua kolom wajib di isi',
                    icon: "error"
                });
                return
            }
            console.log(estimate)
            $.ajax({
                url: "{{ url('master_quotation/update_other_cost') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id,
                    percen: percen,
                    estimate: estimate,
                    addition_type_other_cost: addition_type_other_cost
                },
                success: function(response) {
                    Toast.fire({
                        position: 'top-end',
                        title: response.message,
                        icon: response.status
                    });
                    $("#sub_total_table").DataTable().ajax.reload(null, false)
                    const modalEl = document.getElementById('otherCostModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    modal.hide();
                },
                error: function(xhr) {
                    console.log(xhr)
                }
            })
        })

        function otherHapus(id) {
            Swal.fire({
                title: "Anda yakin?",
                text: "Data akan di hapus secara permanen!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, hapus!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('master_quotation/delete_other_cost') }}",
                        type: 'post',
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: id
                        },
                        success: function(response) {
                            const status = response.status
                            const msg = response.message
                            Toast.fire({
                                position: 'top-end',
                                title: msg,
                                icon: status
                            });
                            $("#sub_total_table").DataTable().ajax.reload(null, false)
                        },
                        error: function(xhr) {
                            console.log(xhr)
                            Toast.fire({
                                position: 'top-end',
                                title: 'Something went wrong',
                                icon: 'error'
                            });
                        }
                    })
                }
            });
        }

        function part_delete(id) {
            Swal.fire({
                title: "Anda yakin?",
                text: "Data akan di hapus secara permanen!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, hapus!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('master_quotation/delete_data') }}",
                        type: 'post',
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: id
                        },
                        success: function(response) {
                            const status = response.status
                            const msg = response.message
                            Toast.fire({
                                position: 'top-end',
                                title: msg,
                                icon: status
                            });
                            $("#master_price_table").DataTable().ajax.reload(null, false)
                        },
                        error: function(xhr) {
                            console.log(xhr)
                            Toast.fire({
                                position: 'top-end',
                                title: 'Something went wrong',
                                icon: 'error'
                            });
                        }
                    })
                }
            });
        }
        $("#confirmBtnOtherCost").on('click', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const ref_doc = urlParams.get('ref_doc');
            $.ajax({
                url: "{{ url('master_quotation/confirm_master') }}",
                type: 'post',
                data: {
                    _token: '{{ csrf_token() }}',
                    ref_doc: ref_doc
                },
                success: function(response) {
                    Toast.fire({
                        position: 'top-end',
                        title: 'Data berhasil di konfirmasi',
                        icon: 'success'
                    });
                    $("#confirmBtnOtherCost").addClass('d-none')
                    $("#cancelConfirmOtherCost").removeClass('d-none')
                },
                error: function(xhr) {
                    console.log(xhr)
                    Toast.fire({
                        position: 'top-end',
                        title: 'Something went wrong',
                        icon: 'error'
                    });
                }
            })
        })
        $("#cancelConfirmOtherCost").on('click', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const ref_doc = urlParams.get('ref_doc');
            $.ajax({
                url: "{{ url('master_quotation/cancel_master') }}",
                type: 'post',
                data: {
                    _token: '{{ csrf_token() }}',
                    ref_doc: ref_doc
                },
                success: function(response) {
                    Toast.fire({
                        position: 'top-end',
                        title: 'Data berhasil di unconfirm',
                        icon: 'success'
                    });
                    $("#confirmBtnOtherCost").removeClass('d-none')
                    $("#cancelConfirmOtherCost").addClass('d-none')
                },
                error: function(xhr) {
                    console.log(xhr)
                    Toast.fire({
                        position: 'top-end',
                        title: 'Something went wrong',
                        icon: 'error'
                    });
                }
            })
        })

        function document_view(ref_doc) {
            $.ajax({
                url: "{{ url('master_quotation/document_view') }}",
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    ref_doc: ref_doc
                },
                success: function(response) {
                    if (response.status === 200) {
                        let pdfData = 'data:application/pdf;base64,' + response.pdf;
                        $('#docFrame').attr('src', pdfData);
                        $('#docModal').modal('show');
                    }
                },
                error: function(xhr) {
                    console.log(xhr)
                }
            })
        }
        $("#download_master_price").on('click', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const ref_preview = urlParams.get('ref_preview');
            $("#download_master_price").on('click', function() {
                const urlParams = new URLSearchParams(window.location.search);
                const ref_preview = urlParams.get('ref_preview');
                const url = "{{ url('master_quotation/download_preview') }}?ref_preview=" + ref_preview;

                window.location.href = url;
            })
        })
        $("#submit_import_update").on('click', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const ref_preview = urlParams.get('ref_preview');
            const fileInput = document.getElementById("input_import_update");
            const file = fileInput.files[0];
            if (!file) {
                Toast.fire({
                    position: 'top-end',
                    title: 'File wajib dipilih',
                    icon: 'error'
                });
                return;
            }
            const allowedExtensions = ['xls', 'xlsx'];
            const fileExtension = file.name.split('.').pop().toLowerCase();

            if (!allowedExtensions.includes(fileExtension)) {
                Toast.fire({
                    position: 'top-end',
                    title: 'File harus format Excel (.xls atau .xlsx)',
                    icon: 'error'
                });
                fileInput.value = "";
                return;
            }
            const maxSize = 5 * 1024 * 1024;
            if (file.size > maxSize) {
                Toast.fire({
                    position: 'top-end',
                    title: 'Ukuran file maksimal 5MB',
                    icon: 'error'
                });
                fileInput.value = "";
                return;
            }
            const data = new FormData();
            data.append('file', file);
            data.append('_token', "{{ csrf_token() }}");
            data.append('ref_preview', ref_preview);
            $.ajax({
                url: "{{ url('master_quotation/import_update') }}",
                type: 'POST',
                data: data,
                processData: false,
                contentType: false,
                success: function(response) {
                    Toast.fire({
                        position: 'top-end',
                        title: response.message,
                        icon: response.status
                    });
                },
                error: function(xhr) {
                    console.log(xhr)
                    Toast.fire({
                        position: 'top-end',
                        title: 'Something went wrong',
                        icon: 'error'
                    });
                }
            })
        });
        // $("#material_tab_href").on('click',function(){
        //     // const urlParams = new URLSearchParams(window.location.search);
        //     // const ref_doc = urlParams.get('ref_doc');
        //     // document_preview(ref_doc)
        // })
    </script>
@endsection
