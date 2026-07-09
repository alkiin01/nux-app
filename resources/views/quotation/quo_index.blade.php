@extends('../layouts/app')


@section('subhead')
    <title>{{ $head_title }}</title>
    <script>
        $(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            const ref_doc = urlParams.get('ref_doc');
            if (!ref_doc) {
                $("#kt_activity_home_tab").addClass('show active');
                window.history.replaceState({}, '', '<?php echo env('BASE_URL'); ?>/quotation');
                return;
            } else {
                $("#kt_content").addClass('d-none');
                $("#preview_quotation").removeClass('d-none');
                document_preview(ref_doc);
                return;
            }
            $('#temp_id').val(ref_doc);
            document_preview(ref_doc, 0);
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

    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="tab-content">
            <div class="post d-flex flex-column-fluid" id="kt_post">
                <div id="kt_content_container" class="container-xxl">
                    <div class="row g-5 g-xl-8 mb-2">
                        <div class="col-xl-3 col-lg-6 col-sm-6">
                            <a href="#" onclick="docSearch(0, this);"
                                class="card bgi-no-repeat card-xl-stretch mb-xl-8 card-front"
                                style="background-position: right top; background-size: 30% auto; background-image: url(<?= env('APP_ASSETS') ?>assets/media/svg/shapes/abstract-1.svg)">
                                <div class="card-body">
                                    <div class="text-gray-900 fw-bolder fs-4 mb-2 mt-2" id="total_w_supplier"></div>
                                    <div class="fw-bold text-gray-900">Waiting Approved Supplier</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-sm-6">
                            <a href="#" onclick="docSearch(1, this);"
                                class="card bgi-no-repeat card-xl-stretch mb-xl-8 card-front"
                                style="background-position: right top; background-size: 30% auto; background-image: url(<?= env('APP_ASSETS') ?>assets/media/svg/shapes/abstract-4.svg)">
                                <div class="card-body">
                                    <div class="text-gray-900 fw-bolder fs-4 mb-2 mt-2" id="total_w_sai"></div>
                                    <div class="fw-bold text-gray-900">Waiting Approved SAI</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-sm-6">
                            <a href="#" onclick="docSearch(2, this);"
                                class="card bgi-no-repeat card-xl-stretch mb-xl-8 card-front"
                                style="background-position: right top; background-size: 30% auto; background-image: url(<?= env('APP_ASSETS') ?>assets/media/svg/shapes/abstract-2.svg)">
                                <div class="card-body">
                                    <div class="text-gray-900 fw-bolder fs-4 mb-2 mt-2" id="total_w_legalize"></div>
                                    <div class="fw-bold text-gray-900">Waiting Legalize</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-sm-6">
                            <a href="#" onclick="docSearch(5, this);"
                                class="card bgi-no-repeat card-xl-stretch mb-xl-8 card-front card-front-1"
                                style="background-position: right top; background-size: 30% auto; background-image: url(<?= env('APP_ASSETS') ?>assets/media/svg/shapes/abstract-3.svg)">
                                <div class="card-body">
                                    <div class="text-gray-900 fw-bolder fs-4 mb-2 mt-2" id="total_doc"></div>
                                    <div class="fw-bold text-gray-900">All Documents</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
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
                                        <input type="text" data-kt-goodreceive-table-filter="search" id="primary_search"
                                            class="form-control form-control-solid w-250px ps-15 text-sm form-control-sm"
                                            placeholder="Search Supplier" />
                                    </div>
                                </div>
                                <div class="card-toolbar">
                                    <div class="d-flex justify-content-end" data-kt-goodreceive-table-toolbar="base">
                                        <button type="button" id="export_excel"
                                            class="btn btn-light-success btn-sm me-3">
                                            <span class="svg-icon svg-icon-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                    class="size-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                                                </svg>
                                            </span>
                                            Export
                                        </button>
                                        <button type="button" class="btn btn-light-primary btn-sm me-3"
                                            data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                            <span class="svg-icon svg-icon-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none">
                                                    <path
                                                        d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z"
                                                        fill="black" />
                                                </svg>
                                            </span>
                                            Filter</button>
                                        <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px"
                                            data-kt-menu="true" id="kt-toolbar-filter">
                                            <div class="px-7 py-5">
                                                <div class="fs-4 text-dark fw-bolder">Filter Options</div>
                                            </div>
                                            <div class="separator border-gray-200"></div>
                                            <div class="px-7 py-5">
                                                <div class="mb-5">
                                                    <label class="form-label fs-5 fw-bold mb-3">Status :</label>
                                                    <select class="form-select form-select-solid fw-bolder"
                                                        data-kt-select2="true" data-placeholder="Select option"
                                                        data-allow-clear="false" id="status_filter"
                                                        data-hide-search="true">
                                                        <option selected disabled>Selected Option</option>
                                                        <option value="0">Waiting approved supplier</option>
                                                        <option value="1">Waiting approved SAI</option>
                                                        <option value="2">Waiting legalize</option>
                                                    </select>
                                                </div>
                                                <div class="mb-5">
                                                    <label class="form-label fs-5 fw-bold mb-3">Effective Date :</label>
                                                    <input type="date" class="form-control form-control-solid"
                                                        id="effective_filter">
                                                </div>
                                                <div class="mb-5">
                                                    <label class="form-label fs-5 fw-bold mb-3">Expired Date :</label>
                                                    <input type="date" class="form-control form-control-solid"
                                                        id="expired_filter">
                                                </div>
                                                <div class="d-flex justify-content-end">
                                                    <button type="submit" id="submit-filter"
                                                        class="btn btn-primary btn-sm" data-kt-menu-dismiss="true"
                                                        data-kt-goodreceive-table-filter="filter">Filter</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <table class="table align-middle table-row-dashed table-striped gy-2 fs-7"
                                    id="primary_table">
                                    <thead>
                                        <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                            <th class="min-w-20px pe-2">No</th>
                                            <th class="min-w-50px">Supplier</th>
                                            <th class="min-w-20px">Customer</th>
                                            <th class="min-w-20px">Total Item</th>
                                            <th class="min-w-50px">Effective</th>
                                            <th class="min-w-50px">Expired</th>
                                            <th class="min-w-20px">Approval Supplier</th>
                                            <th class="min-w-20px">Approval SAI</th>
                                            <th class="min-w-20px">Legalize</th>
                                            <th class="min-w-10px">View</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                            <th class="min-w-20px pe-2">No</th>
                                            <th class="min-w-50px">Supplier</th>
                                            <th class="min-w-50px">Customer</th>
                                            <th class="min-w-20px">Total Item</th>
                                            <th class="min-w-50px">Effective</th>
                                            <th class="min-w-50px">Expired</th>
                                            <th class="min-w-20px">Approval Supplier</th>
                                            <th class="min-w-20px">Approval SAI</th>
                                            <th class="min-w-20px">Legalize</th>
                                            <th class="min-w-10px">View</th>
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
    <div class="content d-flex flex-column flex-column-fluid d-none" id="preview_quotation">
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
                                            <a class="nav-link active" data-bs-toggle="tab"
                                                href="#summary_tab">Quotation
                                                Requests</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#document_tab">Document</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" id="list_quotation_btn"
                                                href="#list_quotation_tab">Pending Quotation</a>
                                        </li>
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
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="summary_tab" role="tabpanel">
                                        <div class="">
                                            <div class="d-flex justify-content-between">
                                                <div class="d-flex align-items-center position-relative my-1">
                                                    <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" viewBox="0 0 24 24" fill="none">
                                                            <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546"
                                                                height="2" rx="1"
                                                                transform="rotate(45 17.0365 15.1223)" fill="black" />
                                                            <path
                                                                d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                                                fill="black" />
                                                        </svg>
                                                    </span>
                                                    <input type="text" data-kt-goodreceive-table-filter="search"
                                                        id="summary_search"
                                                        class="form-control form-control-solid w-250px ps-15 text-sm form-control-sm"
                                                        placeholder="Search Part" />
                                                </div>
                                                <div class="d-flex">
                                                    <div class="text-end">
                                                        <button class="btn btn-primary btn-sm d-none"
                                                            id="approvedBtn">Approved</button>
                                                        <button class="btn btn-danger btn-sm d-none"
                                                            id="canceledBtn">Canceled</button>
                                                            <button class="btn btn-success btn-sm d-none" id="postBtn">
                                                                <span class="svg-icon svg-icon-1">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                        viewBox="0 0 24 24" stroke-width="1.5"
                                                                        stroke="currentColor" class="size-6" width="24"
                                                                        height="24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            d="M7.5 7.5h-.75A2.25 2.25 0 0 0 4.5 9.75v7.5a2.25 2.25 0 0 0 2.25 2.25h7.5a2.25 2.25 0 0 0 2.25-2.25v-7.5a2.25 2.25 0 0 0-2.25-2.25h-.75m0-3-3-3m0 0-3 3m3-3v11.25m6-2.25h.75a2.25 2.25 0 0 1 2.25 2.25v7.5a2.25 2.25 0 0 1-2.25 2.25h-7.5a2.25 2.25 0 0 1-2.25-2.25v-.75" />
                                                                    </svg>
                                                                </span>
                                                                Post
                                                            </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <table class="table align-middle table-row-dashed table-striped gy-2 fs-7"
                                                id="summary_table">
                                                <thead>
                                                    <tr
                                                        class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                                        <th class="min-w-20px pe-2">No</th>
                                                        <th class="min-w-50px">Part Number</th>
                                                        <th class="min-w-20px">Part Name</th>
                                                        <th class="min-w-50px">Raw Material</th>
                                                        <th class="min-w-50px">Material Spec</th>
                                                        <th class="min-w-20px">Previous Price</th>
                                                        <th class="min-w-20px">New Update Price</th>
                                                        <th class="min-w-20px">GAP</th>
                                                        <th class="min-w-20px">%</th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr
                                                        class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                                        <th class="min-w-20px pe-2">No</th>
                                                        <th class="min-w-50px">Part Number</th>
                                                        <th class="min-w-20px">Part Name</th>
                                                        <th class="min-w-50px">Raw Material</th>
                                                        <th class="min-w-50px">Material Spec</th>
                                                        <th class="min-w-20px">Previous Price</th>
                                                        <th class="min-w-20px">New Update Price</th>
                                                        <th class="min-w-20px">GAP</th>
                                                        <th class="min-w-20px">%</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="document_tab" role="tabpanel">
                                        <div id="iframe_document"></div>
                                    </div>
                                    <div class="tab-pane fade" id="list_quotation_tab" role="tabpanel">
                                        <div class="d-flex justify-content-between">
                                            <div class="d-flex align-items-center position-relative my-1">
                                                <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none">
                                                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546"
                                                            height="2" rx="1"
                                                            transform="rotate(45 17.0365 15.1223)" fill="black" />
                                                        <path
                                                            d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                                            fill="black" />
                                                    </svg>
                                                </span>
                                                <input type="text" data-kt-goodreceive-table-filter="search"
                                                    id="list_quotation_search"
                                                    class="form-control form-control-solid w-250px ps-15 text-sm form-control-sm"
                                                    placeholder="Search Part" />
                                            </div>
                                            <div>
                                                <button class="btn btn-light-primary btn-sm" id="print_pending_quo">
                                                    <span class="svg-icon svg-icon-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                            class="size-6">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                                                        </svg>
                                                        Export
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                        <div>
                                            <table class="table align-middle table-row-dashed table-striped gy-2 fs-7"
                                                id="list_quotation_table">
                                                <thead>
                                                    <tr
                                                        class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                                        <th class="min-w-20px pe-2">No</th>
                                                        <th class="min-w-50px">Part Number</th>
                                                        <th class="min-w-20px">Part Name</th>
                                                        <th class="min-w-50px">Raw Material</th>
                                                        <th class="min-w-50px">Material Spec</th>
                                                        <th class="min-w-20px">Material Price</th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr
                                                        class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                                        <th class="min-w-20px pe-2">No</th>
                                                        <th class="min-w-50px">Part Number</th>
                                                        <th class="min-w-20px">Part Name</th>
                                                        <th class="min-w-50px">Raw Material</th>
                                                        <th class="min-w-50px">Material Spec</th>
                                                        <th class="min-w-20px">Material Price</th>
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
    </div>
    {{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        let primary_table;
        $(document).ready(function() {
            total_header()
            primary_table = $("#primary_table").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('quotation/show_data') }}",
                    type: "POST",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}"
                        d.search = $("#primary_search").val()
                        d.filter = $("#status_filter").val(),
                            d.effective = $("#effective_filter").val(),
                            d.expired = $("#expired_filter").val()
                    }
                },
                columns: [{
                    data: 'No'
                }, {
                    data: 'SupplierName'
                }, {
                    data: 'Customer'
                },{
                    data: 'Count'
                }, {
                    data: 'Effective'
                }, {
                    data: 'Expired'
                }, {
                    data: 'Approved1'
                }, {
                    data: 'Approved2'
                },{
                    data: 'Legalize'
                }, {
                    data: 'View'
                }]
            })
        })
        $("#primary_search").on('change', function() {
            primary_table.ajax.reload(null, false)
        })
        $("#submit-filter").on('click', function() {
            primary_table.ajax.reload(null, false)
        })

        function total_header() {
            $.ajax({
                url: "{{ url('quotation/total_header') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    $("#total_w_sai").html(response.w_sai + " Document");
                    $("#total_w_legalize").html(response.w_legalize + " Document");
                    $("#total_w_supplier").html(response.w_supplier + " Document");
                    $("#total_doc").html(response.total + " Document");
                    const $select = $('#status_filter');
                }
            })
        }

        function formatRupiah(value) {
            if (value === null || value === undefined || value === '') {
                return '';
            }
            const num = parseFloat(value);
            if (isNaN(num)) return '';
            return 'Rp ' + num.toLocaleString('id-ID');
        }

        function summary_table(id) {
            if ($.fn.DataTable.isDataTable('#summary_table')) {
                $('#summary_table').DataTable().clear().destroy();
            }
            $("#summary_table").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('quotation/summary_table') }}",
                    type: 'post',
                    data: function(d) {
                        d._token = "{{ csrf_token() }}",
                            d.ref_doc = id,
                            d.search = $("#summary_search").val()
                    }
                },
                columns: [{
                    data: 'No'
                }, {
                    data: 'PartFG'
                }, {
                    data: 'PartFGDesc'
                }, {
                    data: 'PartMtl'
                }, {
                    data: 'PartMtlDesc'
                }, {
                    data: 'PassTotalSalesPrice'
                }, {
                    data: 'CurrentTotalSalesPrice'
                }, {
                    data: 'GAP'
                }, {
                    data: 'Percentage'
                }]
            })
        }
        $("#summary_search").on('input', function() {
            $("#summary_table").DataTable().ajax.reload()
        })

        function document_preview(id) {
            window.history.replaceState({}, '', `<?php echo env('BASE_URL'); ?>/quotation?ref_doc=${id}`);
            $("#kt_content").addClass('d-none')
            $("#preview_quotation").removeClass('d-none')
            const tab = new bootstrap.Tab(document.querySelector('a[href="#summary_tab"]'));
            tab.show();
            $.ajax({
                url: "{{ url('quotation/find_data') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id
                },
                success: function(response) {
                    const data = response.data
                    if (response.status == 'success') {
                        document_tab(data)
                        if (response.status_approval == 'Legalize' && response.role_status == 3) {
                            $("#approvedBtn").addClass('d-none')
                            $("#canceledBtn").removeClass('d-none')
                        } else if (response.status_approval == 'Approved' && response.role_status == 3) {
                            $("#approvedBtn").removeClass('d-none')
                            $("#approvedBtn").text('Legalize')
                            $("#canceledBtn").addClass('d-none')
                        } else if (response.status_approval == 'Legalize' && response.role_status == 2) {
                            $("#canceledBtn").addClass('d-none')
                            $("#approvedBtn").addClass('d-none')
                        } else if (response.status_approval == 'Approved' && response.role_status == 2) {
                            $("#canceledBtn").removeClass('d-none')
                            $("#approvedBtn").addClass('d-none')
                        } else if (response.status_approval == 'Pending' && response.role_status == 2) {
                            $("#canceledBtn").addClass('d-none')
                            $("#approvedBtn").removeClass('d-none')
                        } else if (response.status_approval == 'Pending' && response.role_status == 3) {
                            $("#canceledBtn").addClass('d-none')
                            $("#approvedBtn").addClass('d-none')
                        }else{
                            $("#canceledBtn").addClass('d-none')
                            $("#approvedBtn").addClass('d-none')
                        }
                        if (response.status_approval == 'Legalize' && response.auth_session == '230715-001') {
                            $("#postBtn").removeClass('d-none')
                        } else {
                            $("#postBtn").addClass('d-none')
                        }
                        summary_table(id)
                    } else {
                        Toast.fire({
                            position: 'top-end',
                            title: response.message,
                            icon: "error"
                        });
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseJSON.message)
                    Toast.fire({
                        position: 'top-end',
                        title: 'Unknown error',
                        icon: "error"
                    });
                }
            })
            proccess_table(id)
        }
        $("#backBtn").on('click', function() {
            window.history.replaceState({}, '', '<?php echo env('BASE_URL'); ?>/quotation');
            $("#kt_content").removeClass('d-none')
            $("#preview_quotation").addClass('d-none')
            primary_table.ajax.reload(null, false)
        })

        function proccess_table(id) {
            if ($.fn.DataTable.isDataTable('#proccess_tab_table')) {
                $('#proccess_tab_table').DataTable().clear().destroy();
            }
            $("#proccess_tab_table").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('quotation/process_show') }}",
                    type: "POST",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}"
                        d.id = id
                    }
                },
                columns: [{
                    data: 'No'
                }, {
                    data: 'NameProccess'
                }, {
                    data: 'Machine'
                }, {
                    data: 'Stroke'
                }, {
                    data: 'Rate'
                }, {
                    data: 'Estimate'
                }]
            })
            sub_total_table(id)
        }

        function sub_total_table(id) {
            if ($.fn.DataTable.isDataTable('#sub_total_table')) {
                $('#sub_total_table').DataTable().clear().destroy();
            }
            $("#sub_total_table").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('quotation/sub_total_show') }}",
                    type: "POST",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}"
                        d.id = id
                    }
                },
                columns: [{
                    data: 'No'
                }, {
                    data: 'Item'
                }, {
                    data: 'Percentage'
                }, {
                    data: 'Estimate'
                }]
            })
        }
        $("#approvedBtn").on('click', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const ref_doc = urlParams.get('ref_doc');
            $.ajax({
                url: "{{ url('quotation/approved') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    ref_doc: ref_doc
                },
                success: function(response) {
                    if (response.status == 'success') {
                        Toast.fire({
                            position: 'top-end',
                            title: response.message,
                            icon: "success"
                        });
                        $("#approvedBtn").addClass('d-none')
                        $("#canceledBtn").removeClass('d-none')
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
                        title: 'Unknown Error',
                        icon: "error"
                    });
                }
            })
        })
        $("#canceledBtn").on('click', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const ref_doc = urlParams.get('ref_doc');
            $.ajax({
                url: "{{ url('quotation/canceled') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    ref_doc: ref_doc
                },
                success: function(response) {
                    if (response.status == 'success') {
                        Toast.fire({
                            position: 'top-end',
                            title: response.message,
                            icon: "success"
                        });
                        $("#approvedBtn").removeClass('d-none')
                        $("#canceledBtn").addClass('d-none')
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
                        title: 'Unknown Error',
                        icon: "error"
                    });
                }
            })
        })

        function document_tab(data) {
            const container = $("#iframe_document")
            // src="https://vendor.summitadyawinsa.co.id/view/${id}"
            container.html(
                `<iframe 
            src="https://vendor.summitadyawinsa.co.id/quotation/view/${data}"
            frameborder="0"
            style="
                width: 100%;
                height: 100vh;
                border: none;
            ">
        </iframe>`)
        }

        function docSearch(number) {
            $("#status_filter")
                .val(number)
                .trigger('change');
            primary_table.ajax.reload(null, false)
        }
        $("#export_excel").on("click", function() {
            const params = $.param({
                search: $("#primary_search").val(),
                filter: $("#status_filter").val(),
                effective: $("#effective_filter").val(),
                expired: $("#expired_filter").val()
            });
            window.location.href = "{{ url('quotation/print_excel') }}?" + params;
        });
        let table_quotation;
        function doc_print(ref_doc) {
            const url = "{{ url('quotation/print_out') }}" + "?ref_doc=" + encodeURIComponent(ref_doc);
            window.open(url, '_blank');
        }
        function list_quotation_table() {
            const urlParams = new URLSearchParams(window.location.search);
            const ref_doc = urlParams.get('ref_doc');
            table_quotation = $("#list_quotation_table").DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                ajax: {
                    url: "{{ url('quotation/list_quotation') }}",
                    type: "POST",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}"
                        d.ref_doc = ref_doc
                        d.search = $("#list_quotation_search").val()
                    }
                },
                columns: [{
                        data: 'No'
                    },
                    {
                        data: 'PartFG'
                    },
                    {
                        data: 'PartFGDesc'
                    },
                    {
                        data: 'PartMtl'
                    },
                    {
                        data: 'PartMtlDesc'
                    },
                    {
                        data: 'MtlWPrice'
                    }
                ]
            });
        }
        $("#list_quotation_btn").on('click', function() {
            list_quotation_table()
        });
        $("#list_quotation_search").on('input', function() {
            table_quotation.ajax.reload();
        });
        $("#print_pending_quo").on('click', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const ref_doc = urlParams.get('ref_doc');
            const url = "{{ url('quotation/print_pending_quo') }}" + "?ref_doc=" + encodeURIComponent(ref_doc);
            window.open(url);
        });
        $("#postBtn").on('click', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const ref_doc = urlParams.get('ref_doc');
        $("#loadingOverlay").removeClass('d-none');
        $("#postBtn").prop('disabled', true);
        $.ajax({
            url: "{{ url('quotation/post_quo') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                ref_doc: ref_doc
            },
            success: function(response) {
                $("#loadingOverlay").addClass('d-none');
                $("#postBtn").prop('disabled', false);
                if (response.status == 'success') {
                Toast.fire({
                    position: 'top-end',
                    title: response.message,
                    icon: "success"
                });

                $("#postBtn").addClass('d-none')

            } else {
                Toast.fire({
                    position: 'top-end',
                    title: response.message,
                    icon: "error"
                });
            }
    },
    error: function(xhr) {
        $("#loadingOverlay").addClass('d-none');
        $("#postBtn").prop('disabled', false);
        console.log(xhr)
        Toast.fire({
            position: 'top-end',
            title: 'Unknown Error',
            icon: "error"
        });
        }
    })
})
    </script>
@endsection
