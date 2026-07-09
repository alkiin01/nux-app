@extends('../layouts/app')


@section('subhead')
    <title>{{ $head_title }}</title>
    <script type="text/javascript">
        $(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            var ref_doc = urlParams.get('ref_doc');
            if (ref_doc == '' || ref_doc == null) {
                $("#kt_activity_home_tab").addClass('show active');
                window.history.pushState('', '', '<?php echo env('BASE_URL'); ?>/ap_invoice');
            } else {
                $('#temp_id').val(ref_doc);
                document_preview(ref_doc, 0);
            }
        })
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
                            <a href="#" onclick="docSearch(1, this);"
                                class="card bgi-no-repeat card-xl-stretch mb-xl-8 card-front"
                                style="background-position: right top; background-size: 30% auto; background-image: url(<?= env('APP_ASSETS') ?>assets/media/svg/shapes/abstract-4.svg)">
                                <div class="card-body">
                                    <div class="text-gray-900 fw-bolder fs-4 mb-2 mt-2" id="total_po"></div>
                                    <div class="fw-bold text-gray-900">Approve Purchase</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-sm-6">
                            <a href="#" onclick="docSearch(2, this);"
                                class="card bgi-no-repeat card-xl-stretch mb-xl-8 card-front"
                                style="background-position: right top; background-size: 30% auto; background-image: url(<?= env('APP_ASSETS') ?>assets/media/svg/shapes/abstract-2.svg)">
                                <div class="card-body">
                                    <div class="text-gray-900 fw-bolder fs-4 mb-2 mt-2" id="total_ap"></div>
                                    <div class="fw-bold text-gray-900">Approve AP</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-sm-6">
                            <a href="#" onclick="docSearch(0, this);"
                                class="card bgi-no-repeat card-xl-stretch mb-xl-8 card-front"
                                style="background-position: right top; background-size: 30% auto; background-image: url(<?= env('APP_ASSETS') ?>assets/media/svg/shapes/abstract-1.svg)">
                                <div class="card-body">
                                    <div class="text-gray-900 fw-bolder fs-4 mb-2 mt-2" id="total_pending"></div>
                                    <div class="fw-bold text-gray-900">Pending</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-sm-6">
                            <a href="#" onclick="docSearch(3, this);"
                                class="card bgi-no-repeat card-xl-stretch mb-xl-8 card-front card-front-1"
                                style="background-position: right top; background-size: 30% auto; background-image: url(<?= env('APP_ASSETS') ?>assets/media/svg/shapes/abstract-3.svg)">
                                <div class="card-body">
                                    <div class="text-gray-900 fw-bolder fs-4 mb-2 mt-2" id="total_reject"></div>
                                    <div class="fw-bold text-gray-900">Reject</div>
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
                                        <input type="text" data-kt-goodreceive-table-filter="search" id="search_table"
                                            class="form-control form-control-solid w-250px ps-15 text-sm form-control-sm"
                                            placeholder="Search Vendor/Grp/Inv/PO" />
                                    </div>
                                </div>
                                <div class="card-toolbar">
                                    <div class="d-flex justify-content-end" data-kt-goodreceive-table-toolbar="base">
                                        <button type="button" class="btn btn-light-primary btn-sm me-3"
                                            data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                            <!--begin::Svg Icon | path: icons/duotune/general/gen031.svg-->
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
                                                <div class="mb-3">
                                                    <label class="form-label fs-5 fw-bold mb-3">Status:</label>
                                                    <select class="form-select form-select-solid fw-bolder"
                                                        data-kt-select2="true" data-placeholder="Select option"
                                                        data-allow-clear="false" id="status_filter"
                                                        data-hide-search="true">
                                                        <option selected disabled>Selected Option</option>
                                                        <option value='0'>Pending</option>
                                                        <option value="1">Approved Purchase</option>
                                                        <option value="2">Approved AP</option>
                                                        <option value="3">Reject</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fs-5 fw-bold mb-3">Start:</label>
                                                    <input type="date" id="start_date"
                                                        class="form-control form-control-solid">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fs-5 fw-bold mb-3">Finish:</label>
                                                    <input type="date" id="finish_date"
                                                        class="form-control form-control-solid">
                                                </div>
                                                <div class="d-flex justify-content-end">
                                                    <button type="submit" id="submit-filter" class="btn btn-primary"
                                                        data-kt-menu-dismiss="true"
                                                        data-kt-goodreceive-table-filter="filter"
                                                        onclick="filterApply()">Apply</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- <button type="button" class="btn btn-light-primary btn-sm me-3"
                                        id="btn_add_document" onclick="add_document()">
                                        <span id="svg_add_document" class="svg-icon svg-icon-2">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                                viewBox="0 0 24 24" version="1.1">
                                                <defs />
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <rect fill="#000000" x="4" y="11" width="16" height="2"
                                                        rx="1" />
                                                    <rect fill="#000000" opacity="0.3"
                                                        transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) "
                                                        x="4" y="11" width="16" height="2" rx="1" />
                                                </g>
                                            </svg>
                                        </span>
                                        <span id="spinner_add_document"
                                            class="spinner-border spinner-border-sm align-middle ms-2"
                                            style="display: none;"></span>
                                        <span id="btn_text_add_document">Create</span>
                                    </button> --}}
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <table class="table align-middle table-row-dashed table-striped gy-2 fs-7"
                                    id="primary_table">
                                    <thead>
                                        <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                            <th class="min-w-20px pe-2">No</th>
                                            <th class="min-w-20px">Vendor</th>
                                            <th class="min-w-20px">Group</th>
                                            <th class="min-w-50px">Invoice Num</th>
                                            <th class="min-w-20px">PO Num</th>
                                            <th class="min-w-80px">Approval Purchase</th>
                                            <th class="min-w-80px">Approval AP</th>
                                            <th class="min-w-50px">Invoice Date</th>
                                            <th class="min-w-20px">View</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                            <th class="min-w-20px pe-2">No</th>
                                            <th class="min-w-20px">Vendor</th>
                                            <th class="min-w-20px">Group</th>
                                            <th class="min-w-50px">Invoice Num</th>
                                            <th class="min-w-20px">PO Num</th>
                                            <th class="min-w-80px">Approval Purchase</th>
                                            <th class="min-w-80px">Approval AP</th>
                                            <th class="min-w-50px">Invoice Date</th>
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
    <div id="document_preview" class="p-5 d-none">
        <div style="background-color: #f5f8fa;"><br></div>
        <div class="card mt-2">
            <div class="card-header py-6 mb-6">
                <ul class="nav nav-tabs nav-line-tabs nav-stretch fs-6 border-0">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#preview_tab">Preview</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#attachment_tab">Attachment</a>
                    </li>
                </ul>
                <div class="card-toolbar">
                    <div class="d-flex justify-content-end" data-kt-goodreceive-table-toolbar="base">
                        <a href="{{ url('ap_invoice') }}" type="button"
                            class="btn me-2 btn-xs btn-outline btn-outline-dashed btn-outline-success btn-active-light-success text-muted">
                            <span class="svg-icon svg-icon-muted svg-icon-2x">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none">
                                    <path d="M14.6 4L6.6 12L14.6 20H10.6L3.3 12.7C2.9 12.3 2.9 11.7 3.3 11.3L10.6 4H14.6Z"
                                        fill="black" />
                                    <path opacity="0.3"
                                        d="M21.6 4L13.6 12L21.6 20H17.6L10.3 12.7C9.9 12.3 9.9 11.7 10.3 11.3L17.6 4H21.6Z"
                                        fill="black" />
                                </svg>
                            </span>
                            Back
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body pt-0">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="preview_tab" role="tabpanel">
                        <div class="row" id="form">
                            <div class="col-md-6 mb-5">
                                <form>
                                    <div class="form-group mb-5">
                                        <label>Faktur Number</label>
                                        <input type="text" class="form-control bg-light-primary" id="fakturNumber"
                                            value="" readonly />
                                    </div>
                                    <div class="form-group mb-5">
                                        <label>Group ID</label>
                                        <input type="text" class="form-control bg-light-primary" id="group_id"
                                            value="" readonly />
                                    </div>
                                    <div class="form-group mb-5">
                                        <label>Fiscal Period</label>
                                        <input type="text" class="form-control bg-light-primary" id="FiscalPeriod"
                                            name="FiscalPeriod" value="" readonly />
                                    </div>
                                    <div class="form-group mb-5">
                                        <label>Terms</label>
                                        <select class="form-control bg-light-primary" id="terms">
                                            <option value=""></option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-5">
                                        <label>Applied Date</label>
                                        <input type="date" class="form-control bg-light-primary" id="applied_date"
                                            name="PackSlip" value="" readonly />
                                    </div>
                                    <div class="form-group mb-5">
                                        <label>Due Date</label>
                                        <input type="date" class="form-control bg-light-primary" id="due_date"
                                            name="PackSlip" value="" readonly />
                                    </div>
                                    <div class="form-group mb-5">
                                        <label>DPP GR</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light fw-bold">Rp.</span>
                                            <input type="text" class="form-control bg-light-primary" id="dpp_gr"
                                                name="PackSlip" value="" readonly />
                                        </div>
                                    </div>
                                    <div class="form-group mb-5">
                                        <label>Tax GR</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light fw-bold">Rp.</span>
                                            <input type="text" class="form-control bg-light-primary" id="tax_gr"
                                                name="PackSlip" value="" readonly />
                                        </div>
                                    </div>
                                    <div class="form-group mb-5">
                                        <label>Amount GR</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light fw-bold">Rp.</span>
                                            <input type="text" class="form-control bg-light-primary" id="total_gr"
                                                name="PackSlip" value="" readonly />
                                        </div>
                                    </div>
                                    <div class="form-group mb-5">
                                        <label>Amount Confirm</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light fw-bold">Rp.</span>
                                            <input type="text" class="form-control bg-light-primary"
                                                id="total_confirm" name="PackSlip" value="" readonly />
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Form kanan -->
                            <div class="col-md-6 mb-5">
                                <form>
                                    <div class="form-group mb-5">
                                        <label>Invoice Number</label>
                                        <input type="text" class="form-control bg-light-primary" id="InvNumber"
                                            value="" readonly />
                                    </div>
                                    <div class="form-group mb-5">
                                        <label>PO Number</label>
                                        <input type="number" class="form-control bg-light-primary" id="PONum"
                                            name="PONum" value="" readonly />
                                    </div>
                                    <div class="form-group mb-5">
                                        <label>Fiscal Year</label>
                                        <input type="text" class="form-control bg-light-primary" id="EntryDate"
                                            value="" readonly />
                                    </div>
                                    <div class="form-group mb-5">
                                        <label>Vendor</label>
                                        <input type="text" class="form-control bg-light-primary" id="Vendor"
                                            name="Vendor" value="" readonly />
                                    </div>
                                    <div class="form-group mb-5">
                                        <label>Supplier Inv Date</label>
                                        <input type="date" class="form-control bg-light-primary"
                                            id="supplier_inv_date" name="PackSlip" value="" readonly />
                                    </div>
                                    <div class="form-group mb-5">
                                        <label>Invoice Date</label>
                                        <input type="date" class="form-control bg-light-primary" id="invoice_date"
                                            name="PackSlip" value="" readonly />
                                    </div>
                                    <div class="form-group mb-5">
                                        <label>Variance</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light fw-bold">Rp.</span>
                                            <input type="text" class="form-control bg-light-primary" id="variance"
                                                name="PackSlip" value="" readonly />
                                        </div>
                                    </div>
                                    <div class="form-group mb-5">
                                        <label>DPP PO</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light fw-bold">Rp.</span>
                                            <input type="text" class="form-control bg-light-primary" id="dpp_po"
                                                name="PackSlip" value="" readonly />
                                        </div>
                                    </div>
                                    <div class="form-group mb-5">
                                        <label>Tax PO</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light fw-bold">Rp.</span>
                                            <input type="text" class="form-control bg-light-primary" id="tax_po"
                                                name="PackSlip" value="" readonly />
                                        </div>
                                    </div>
                                    <div class="form-group mb-5">
                                        <label>Amount PO</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light fw-bold">Rp.</span>
                                            <input type="text" class="form-control bg-light-primary" id="amount_po"
                                                name="PackSlip" value="" readonly />
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="form-group mb-5 d-none" id="div_remark">
                                <label>Remark</label>
                                <textarea class="form-control bg-light-primary" id="remark" readonly></textarea>
                            </div>
                            <hr style="color: gray">

                            <!-- Tombol Aksi -->
                            <div class="col-md-12" style="display:flex; justify-content:space-between">
                                <div>
                                    <button type="button" class="btn btn-success btn-sm mr-2 mt-2" id="update_btn"
                                        onclick="update()">
                                        <span id="update_text">Update</span>
                                        <span id="spinner_update_btn"
                                            class="spinner-border spinner-border-sm align-middle ms-2"
                                            style="display: none;"></span>
                                    </button>

                                    <button type="button" class="btn btn-primary btn-sm mr-2 mt-2" id="submit_btn"
                                        onclick="submit()" style="display:none">
                                        <span id="submit_text">Submit</span>
                                        <span id="spinner_submit_btn"
                                            class="spinner-border spinner-border-sm align-middle ms-2"
                                            style="display: none;"></span>
                                    </button>
                                </div>

                                <div>
                                    <button type="button" class="btn btn-warning btn-sm mr-2 mt-2" id="reject_btn"
                                        onclick="reject()">
                                        <span id="btn_text_reject_gr">Reject</span>
                                        <span id="spinner_reject_gr"
                                            class="spinner-border spinner-border-sm align-middle ms-2"
                                            style="display: none;"></span>
                                    </button>
                                    <button type="button" class="btn btn-light-danger btn-sm mr-2 mt-2"
                                        id="cancel_approved" onclick="cancel()">
                                        <span id="btn_text_delete_gr">Cancel Approved</span>
                                        <span id="spinner_delete_gr"
                                            class="spinner-border spinner-border-sm align-middle ms-2"
                                            style="display: none;"></span>
                                    </button>

                                    <button type="button" class="btn btn-primary btn-sm mr-2 mt-2" id="approved"
                                        onclick="approved()">
                                        <span id="btn_text_update_gr">Approved</span>
                                        <span id="spinner_update_gr"
                                            class="spinner-border spinner-border-sm align-middle ms-2"
                                            style="display: none;"></span>
                                    </button>
                                    {{-- @if ($data->Approved_AP == 1 && $data->Status == 2) --}}
                                    {{-- @if ($data->Approved_AP == 0 && $data->Status == 0) --}}
                                    {{-- <button type="button" class="btn btn-primary btn-sm mr-2 mt-2"
                                            id="recalculateBtn">
                                            <span id="btn_text_recalculate">Recalculate</span>
                                            <span id="spin_btn_recalculate"
                                                class="spinner-border spinner-border-sm align-middle ms-2"
                                                style="display: none;"></span>
                                        </button> --}}
                                    {{-- @endif --}}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Attachment -->
                    <div class="tab-pane fade" id="attachment_tab" role="tabpanel">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                                    <a onclick="showPdfModal('invoice')"
                                        class="text-gray-800 text-hover-primary d-flex flex-column">
                                        <div class="symbol symbol-60px mb-5">
                                            <img src="<?= env('APP_ASSETS') ?>assets/media/svg/files/doc.svg"
                                                alt="" />
                                        </div>
                                        <div class="fs-5 fw-bolder mb-2">
                                            Invoice Document
                                        </div>
                                    </a>
                                    <div class="fs-7 fw-bold text-gray-400"></div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                                    <a onclick="showPdfModal('facture')"
                                        class="text-gray-800 text-hover-primary d-flex flex-column">
                                        <div class="symbol symbol-60px mb-5">
                                            <img src="<?= env('APP_ASSETS') ?>assets/media/svg/files/doc.svg"
                                                alt="" />
                                        </div>
                                        <div class="fs-5 fw-bolder mb-2">
                                            Facture Document
                                        </div>
                                    </a>
                                    <div class="fs-7 fw-bold text-gray-400"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="card mt-5">
            <div class="card-header border-1 mb-5">
                <div class="card-title">
                    <div class="d-flex align-items-center position-relative my-1">
                        <span class="svg-icon svg-icon-1 position-absolute ms-6">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2"
                                    rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                <path
                                    d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                    fill="black" />
                            </svg>
                        </span>
                        <input type="text" data-kt-goodreceive-table-filter="search" id="detail_table_search"
                            class="form-control form-control-solid w-250px ps-15 text-sm form-control-sm"
                            placeholder="PackSlip" />
                    </div>
                </div>
                <div>
                    <button type="button" class="btn btn-primary btn-sm mr-2 mt-2" id="showModalItemAll">
                        <span id="btn_text_item_gr">All Item</span>
                        <span id="spinner_item_gr" class="spinner-border spinner-border-sm align-middle ms-2"
                            style="display: none;"></span>
                    </button>
                </div>
            </div>

            <div class="card-body pt-0">
                <table class="table align-middle table-row-dashed table-striped gy-2 fs-7" id="kt_detail_table">
                    <thead>
                        <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                            <th class="min-w-20px pe-2">No</th>
                            <th class="min-w-50px">PackSlip</th>
                            <th class="min-w-50px">Total Qty</th>
                            <th class="min-w-50px">GR Unit Price</th>
                            <th class="min-w-20px">PO Unit Price</th>
                            <th class="min-w-20px">GR Total</th>
                            <th class="min-w-20px">PO Total</th>
                            <th class="min-w-50px">UOM</th>
                            <th class="min-w-50px">View</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                            <th class="min-w-20px pe-2">No</th>
                            <th class="min-w-50px">PackSlip</th>
                            <th class="min-w-50px">Total Qty</th>
                            <th class="min-w-50px">GR Unit Price</th>
                            <th class="min-w-20px">PO Unit Price</th>
                            <th class="min-w-20px">GR Total</th>
                            <th class="min-w-20px">PO Total</th>
                            <th class="min-w-50px">UOM</th>
                            <th class="min-w-50px">View</th>
                        </tr>
                    </tfoot>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <div class="modal fade" id="modalDtl" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="labelModal">Item Detail</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table align-middle table-row-dashed table-striped gy-2 fs-7" id="detailItemTb">
                            <thead>
                                <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                    <th class="min-w-20px pe-2">No</th>
                                    <th class="min-w-20px">PackSlip</th>
                                    <th class="min-w-50px">Part Num</th>
                                    <th class="min-w-60px">Description</th>
                                    <th class="min-w-50px">Qty</th>
                                    <th class="min-w-50px">PO Unit Price</th>
                                    <th class="min-w-50px">GR Unit Price</th>
                                    <th class="min-w-50px">GR Total</th>
                                    <th class="min-w-50px">PO Total</th>
                                    <th class="min-w-20px">UOM</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                    <th class="min-w-20px pe-2">No</th>
                                    <th class="min-w-20px">PackSlip</th>
                                    <th class="min-w-20px">Part Num</th>
                                    <th class="min-w-60px">Description</th>
                                    <th class="min-w-50px">Qty</th>
                                    <th class="min-w-60px">PO Unit Price</th>
                                    <th class="min-w-50px">GR Unit Price</th>
                                    <th class="min-w-50px">GR Total</th>
                                    <th class="min-w-50px">PO Total</th>
                                    <th class="min-w-50px">UOM</th>
                                </tr>
                            </tfoot>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        {{-- <button type="button" class="btn btn-primary" id='saveDtlitem'>Save</button> --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalDocument" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable modal-fullscreen-sm-down">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="labelModal">Document</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0" style="height: 90vh; overflow-y: auto;">
                        <div class="mb-2 w-100">
                            <iframe id="pdfFrame" src="" frameborder="0" width="100%" height="100%"
                                style="border: none; border-radius: 8px; min-height: 80vh;">
                            </iframe>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        {{-- <button type="button" class="btn btn-primary" id='saveDtlitem'>Save</button> --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalItemAll" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <input type="text" data-kt-goodreceive-table-filter="search" id="search_item"
                            class="form-control form-control-solid w-250px ps-15 text-sm form-control-sm"
                            placeholder="PONum/PackSlip/PartNum" />
                        <button type="button" class="btn btn-primary btn-sm" onclick="exportExcel()">Export</button>
                    </div>
                    <div class="modal-body">
                        <table class="table align-middle table-row-dashed table-striped gy-2 fs-7" id="all_item_tbl">
                            <thead>
                                <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                    <th class="min-w-20px pe-2">No</th>
                                    <th class="min-w-40px">Part</th>
                                    <th class="min-w-60px">Description</th>
                                    <th class="min-w-40px">Total Qty</th>
                                    <th class="min-w-40px">GR Unit Price</th>
                                    <th class="min-w-40px">PO Unit Price</th>
                                    <th class="min-w-40px">GR Amount</th>
                                    <th class="min-w-40px">PO Amount</th>
                                    <th class="min-w-30px">UOM</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                    <th class="min-w-20px pe-2">No</th>
                                    <th class="min-w-40px">Part</th>
                                    <th class="min-w-60px">Description</th>
                                    <th class="min-w-40px">Total Qty</th>
                                    <th class="min-w-40px">GR Price</th>
                                    <th class="min-w-40px">PO Price</th>
                                    <th class="min-w-40px">GR Total</th>
                                    <th class="min-w-40px">PO Total</th>
                                    <th class="min-w-30px">UOM</th>
                                </tr>
                                <tr class="fw-bold text-gray-800">
                                    <th colspan="6" class="text-end">TOTAL :</th>
                                    <th id="total_po_amount">Rp 0</th>
                                    <th id="total_gr_amount">Rp 0</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        {{-- <button type="button" class="btn btn-primary" id='saveDtlitem'>Save</button> --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalReject" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="labelModal">Remark Reject</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="mb-2 w-100 p-2">
                            <textarea class="form-control" id="remark_reject" rows="5" placeholder="Enter your remark here..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id='submitReject'>Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="text" id="InvoiceAttachment"hidden>
    <input type="text" id="FakturAttachment" hidden>
    <script>
        let primaryTable
        $(document).ready(function() {
            primaryTable = $("#primary_table").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('ap_invoice/table_primary') }}",
                    type: "POST",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}"
                        d.search = $("#search_table").val()
                        d.status_filter = $("#status_filter").val(),
                            d.start_date = $("#start_date").val(),
                            d.finish_date = $("#finish_date").val()
                    }
                },
                columns: [{
                        data: 'no',
                        className: 'text-center',
                        orderable: false
                    },
                    {
                        data: "Vendor"
                    },
                    {
                        data: 'GroupID'
                    },
                    {
                        data: 'InvoiceNum'
                    },
                    {
                        data: 'PONum'
                    },
                    {
                        data: 'status_PO',
                        className: 'text-center'
                    },
                    {
                        data: 'status_AP',
                        className: 'text-center'
                    },
                    {
                        data: 'InvoiceDate'
                    },
                    {
                        data: 'View',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ]
            })
            $('#search_table').on('keyup', function() {
                primaryTable.ajax.reload()
            })
            statusHeader()
        })

        function statusHeader() {
            $.ajax({
                url: "{{ url('ap_invoice/header') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $("#total_po").text(response.approved_po)
                    $("#total_ap").text(response.approved_ap)
                    $("#total_pending").text(response.pending)
                    $("#total_reject").text(response.reject)
                },
                error: function(error) {
                    console.log(error)
                }
            })
        }

        function previewInvoice(id) {
            $.ajax({
                url: "{{ url('ap_invoice/preview_doc') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    doc: id
                },
                success: function(response) {
                    $("#kt_content").addClass("d-none");
                    $("#document_preview").removeClass("d-none").html(response);
                }
            })
        }

        function filterApply() {
            primaryTable.ajax.reload()
        }

        function docSearch(number) {
            $("#status_filter").val(number);
            primaryTable.ajax.reload()
        }

        function document_preview(ref_doc) {
            $("#document_preview").removeClass("d-none");
            $("#kt_content").addClass("d-none");
            window.history.pushState('', '', '<?php echo env('BASE_URL'); ?>/ap_invoice?ref_doc=' + ref_doc);
            preview_doc(ref_doc)
            table_preview_doc(ref_doc)
            checkStatus(ref_doc)
        }
        function formatRupiah(num) {
            return Number(num).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function preview_doc(ref_doc) {
            $.ajax({
                url: "{{ url('ap_invoice/preview_doc') }}",
                type: 'post',
                data: {
                    _token: '{{ csrf_token() }}',
                    doc: ref_doc
                },
                success: function(response) {
                    const data = response.data
                    $("#fakturNumber").val(data.FakturNum)
                    $("#group_id").val(data.GroupID)
                    $("#InvNumber").val(data.InvoiceNum)
                    $("#PONum").val(data.PONum)
                    $("#EntryDate").val(data.FiscalYear)
                    $("#FiscalPeriod").val(
                        String(data.FiscalPeriod).padStart(2, '0')
                    );
                    $("#Vendor").val(response.vendor)
                    $("#supplier_inv_date").val(data.SupplierInvoiceDate)
                    $("#invoice_date").val(data.InvoiceDate)
                    $("#due_date").val(data.DueDate)
                    if ($("#terms option[value='" + data.TermsCode + "']").length === 0) {
                        $("#terms").append(
                            new Option(data.TermsCode, data.TermsCode, true, true)
                        );
                    }

                    $("#terms").val(data.TermsCode).trigger('change');

                    $("#applied_date").val(data.AppliedDate)
                    $("#dpp_gr").val(formatRupiah(data.DPPGR))
                    $("#dpp_po").val(formatRupiah(data.DPPPO))
                    $("#tax_gr").val(formatRupiah(data.TaxGR))
                    $("#tax_po").val(formatRupiah(data.TaxPO))
                    $("#total_gr").val(formatRupiah(data.TotalGR))
                    $("#amount_po").val(formatRupiah(data.TotalPO))
                    // $("#variance").val(parseFloat(data.Variance).toLocaleString('en-US'))
                    $("#total_confirm").val(formatRupiah(data.ConfirmNote))
                    $("#variance").val(formatRupiah(data.variance))
                    $("#InvoiceAttachment").val(data.InvoiceAttachment)
                    $("#FakturAttachment").val(data.FakturAttachment)
                    if (data.Remark) {
                        $("#div_remark").removeClass('d-none')
                        $("#remark").val(data.Remark)
                    }
                },
                error: function(xhr) {
                    console.log(xhr)
                }
            })
        }

        function table_preview_doc(ref_doc) {
            if ($.fn.DataTable.isDataTable('#kt_detail_table')) {
                $('#kt_detail_table').DataTable().destroy();
            }
            $("#kt_detail_table").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('ap_invoice/preview_doc/detail') }}",
                    type: 'post',
                    data: function(d) {
                        d._token = '{{ csrf_token() }}';
                        d.id = ref_doc;
                        d.search = $("#detail_table_search").val();
                    }
                },
                columns: [{
                    data: 'no',
                    orderable: false,
                    searchable: false,
                    className: 'text-center'
                }, {
                    data: 'PackSlip'
                }, {
                    data: 'Qty'
                }, {
                    data: 'PriceGR'
                }, {
                    data: 'PricePO'
                }, {
                    data: 'AmountGR'
                }, {
                    data: 'AmountPO'
                }, {
                    data: 'UOM',
                }, {
                    data: 'View',
                    orderable: false,
                    searchable: false,
                    className: 'text-center'
                }],
                createdRow: function(row, data, dataIndex) {
                    let PO = parseFloat(String(data.PricePO).replace(/[Rp\s.]/g, '').replace(',',
                        '.')) || 0;
                    let GR = parseFloat(String(data.PriceGR).replace(/[Rp\s.]/g, '').replace(',',
                        '.')) || 0;
                    let PricePO = parseFloat(String(data.PricePO).replace(/[Rp\s.]/g, '').replace(',',
                            '.')) ||
                        0;
                    let PriceGR = parseFloat(String(data.PriceGR).replace(/[Rp\s.]/g, '').replace(',',
                            '.')) ||
                        0;
                    if (PO !== GR || PriceGR !== PricePO) {
                        let cells = $(row).find("td");
                        // $(row).addClass('bg-warning');
                        $(cells[3]).css("color", "red");
                        $(cells[4]).css("color", "red");
                        $(cells[5]).css("color", "red");
                        $(cells[6]).css("color", "red");
                    }
                }
            })
            $("#detail_table_search").on('keyup change', function() {
                $("#kt_detail_table").DataTable().ajax.reload()
            })
        }

        function checkStatus(ref_doc) {
            $.ajax({
                url: "{{ url('ap_invoice/check_status') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: ref_doc,
                },
                success: function(response) {
                    if (response == 'PO0' || response == 'AP0') {
                        $("#cancel_approved").css('display', 'none')
                        //  $("#approved").css('display', 'none')
                    } else if (response == 'PO1' || response == 'AP1') {
                        preview_doc(ref_doc)
                        $("#approved").css('display', 'none')
                    } else if (response == 'NO PO') {
                        $("#cancel_approved").css('display', 'none')
                        $("#approved").css('display', 'none')
                        $("#reject_btn").css('display', 'none')
                    } else if (response == 'AP1') {
                        preview_doc(ref_doc)
                        $("#recalculateBtn").css('display', 'block')
                    } else {
                        $("#reject_btn").css('display', 'none')
                        $("#cancel_approved").css('display', 'none')
                        $("#approved").css('display', 'none')
                        $("#update_btn").css('display', 'none')
                        $("#reject_btn").css('display', 'block')
                    }
                },
                error: function(xhr) {
                    console.log(xhr)
                }
            })
        }

        function backHome() {
            $("#kt_content").removeClass("d-none");
            $("#document_preview").addClass("d-none")
            window.history.pushState('', '', '/ap_invoice');
            primaryTable.ajax.reload()
            statusHeader()
        }

        function approved() {
            const urlParams = new URLSearchParams(window.location.search);
            const ref_doc = urlParams.get('ref_doc');
            $("#svg_update_gr").hide()
            $("#spinner_update_gr").show()
            $.ajax({
                url: "{{ url('ap_invoice/approved') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: ref_doc,
                },
                success: function(response) {
                    if (response.status == 200) {
                        Toast.fire({
                            position: 'top-end',
                            title: "Approved Successfully",
                            icon: "success"
                        });
                        $("#approved").css('display', 'none')
                        $("#cancel_approved").css('display', 'block')
                        checkStatus()
                    } else {
                        Toast.fire({
                            position: 'top-end',
                            title: response.messsage,
                            icon: "error"
                        });
                    }
                    $("#spinner_update_gr").hide()
                    $("#svg_update_gr").show()
                },
                error: function(xhr) {
                    console.log(xhr)
                    Toast.fire({
                        position: 'top-end',
                        title: xhr.responseJSON.message,
                        icon: "error"
                    });
                    $("#spinner_update_gr").hide()
                    $("#svg_update_gr").show()
                }
            })
        }

        function cancel() {
            const urlParams = new URLSearchParams(window.location.search);
            const ref_doc = urlParams.get('ref_doc');
            $("#svg_delete_gr").hide()
            $("#spinner_delete_gr").show()
            $.ajax({
                url: "{{ url('ap_invoice/cancel_approval') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: ref_doc,
                },
                success: function(response) {
                    if (response.status == 200) {
                        Toast.fire({
                            position: 'top-end',
                            title: "Cancel Approved Successfully",
                            icon: "success"
                        });
                        $("#cancel_approved").css('display', 'none')
                        $("#approved").css('display', 'block')
                    } else {
                        Toast.fire({
                            position: 'top-end',
                            title: response.message,
                            icon: "error"
                        });
                    }
                    $("#spinner_delete_gr").hide()
                    $("#svg_delete_gr").show()
                },
                error: function(xhr) {
                    console.log(xhr)
                    Toast.fire({
                        position: 'top-end',
                        title: xhr.responseJSON.message,
                        icon: "error"
                    });
                    $("#spinner_delete_gr").hide()
                    $("#svg_delete_gr").show()
                }
            })
        }

        function reject() {
            const urlParams = new URLSearchParams(window.location.search);
            const ref_doc = urlParams.get('ref_doc');
            $.ajax({
                url: "{{ url('ap_invoice/reject') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: ref_doc
                },
                success: function(response) {
                    if (response.status == 200) {
                        $("#modalReject").modal('show')
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
                        title: xhr.responseJSON.message,
                        icon: "error"
                    });
                }
            })
        }

        function update() {
            const urlParams = new URLSearchParams(window.location.search);
            const ref_doc = urlParams.get('ref_doc');
            $.ajax({
                url: "{{ url('ap_invoice/check_status') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: ref_doc,
                },
                success: function(res) {
                    if (res == 'AP1') {
                        cancel()
                    }
                    $("#invoice_date").removeClass('bg-light-primary').prop('readonly', false)
                    $("#due_date").removeClass('bg-light-primary').prop('readonly', false)
                    $("#supplier_inv_date").removeClass('bg-light-primary').prop('readonly', false)
                    $("#applied_date").removeClass('bg-light-primary').prop('readonly', false)
                    $("#terms").removeClass('bg-light-primary').prop('readonly', false)
                    $.ajax({
                        url: "{{ url('ap_invoice/terms') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            const $terms = $("#terms");
                            const selected = $terms.val();
                            $terms.empty();
                            response.forEach(function(item) {
                                const selectedAttr = (item.TermsCode === selected) ?
                                    'selected' : '';
                                $terms.append(
                                    `<option value="${item.TermsCode}" ${selectedAttr}>${item.TermsCode} - ${item.Description}</option>`
                                );
                            });
                        }
                    })
                    $("#update_btn").css('display', 'none')
                    $("#submit_btn").css('display', 'block')
                },
                error: function(xhr) {
                    console.log(xhr)
                }
            })
        }
        $("#terms, #invoice_date").on('change', function() {
            $.ajax({
                url: "{{ url('ap_invoice/change_terms') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    terms: $("#terms").val(),
                    invDate: $("#invoice_date").val()
                },
                success: function(response) {
                    if (response.status == 200) {
                        $("#due_date").val(response.DueDate)
                    } else {
                        Toast.fire({
                            position: 'top-end',
                            title: 'Server error',
                            icon: "error"
                        });
                    }
                }
            })
        })

        function submit() {
            const urlParams = new URLSearchParams(window.location.search);
            const ref_doc = urlParams.get('ref_doc');
            const terms = $("#terms").val();
            const supplier_inv_date = $("#supplier_inv_date").val();
            const applied_date = $("#applied_date").val();
            const invoice_date = $("#invoice_date").val();
            const due_date = $("#due_date").val();
            const fields = {
                "Terms": $("#terms").val(),
                "Supplier Invoice Date": $("#supplier_inv_date").val(),
                "Applied Date": $("#applied_date").val(),
                "Invoice Date": $("#invoice_date").val(),
                "Due Date": $("#due_date").val(),
            };
            let emptyFields = [];
            for (let label in fields) {
                if (!fields[label] || fields[label].trim() === "") {
                    emptyFields.push(label);
                }
            }
            if (emptyFields.length > 0) {
                Toast.fire({
                    position: 'top-end',
                    icon: "error",
                    title: "Kolom kosong:\n" + emptyFields.join(", ")
                });
                return;
            }

            $.ajax({
                url: "{{ url('ap_invoice/submit_change') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    terms: terms,
                    supplier_inv_date: supplier_inv_date,
                    applied_date: applied_date,
                    invoice_date: invoice_date,
                    due_date: due_date,
                    id: ref_doc,
                },
                success: function(response) {
                    if (response.status === 200) {
                        Toast.fire({
                            position: 'top-end',
                            title: 'Perubahan berhasil disimpan!',
                            icon: "success"
                        });
                        $("#invoice_date").addClass('bg-light-primary').prop('readonly', true)
                        $("#due_date").addClass('bg-light-primary').prop('readonly', true)
                        $("#supplier_inv_date").addClass('bg-light-primary').prop('readonly', true)
                        $("#applied_date").addClass('bg-light-primary').prop('readonly', true)
                        $("#terms").addClass('bg-light-primary').prop('readonly', true)
                        $("#update_btn").css('display', 'block')
                        $("#submit_btn").css('display', 'none')
                    } else {
                        Toast.fire({
                            position: 'top-end',
                            title: response.message || 'Gagal menyimpan perubahan',
                            icon: "error"
                        });
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseJSON.message);
                    Toast.fire({
                        position: 'top-end',
                        title: 'Terjadi kesalahan pada server',
                        icon: "error"
                    });
                }
            });
        }

        function detail_preview(linkedBtn) {
            if ($.fn.DataTable.isDataTable('#detailItemTb')) {
                $('#detailItemTb').DataTable().clear().destroy();
            }
            $("#modalDtl").modal('show')
            $("#detailItemTb").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('ap_invoice/detail_packslip') }}",
                    type: "POST",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}"
                        d.linkedBtn = linkedBtn
                    }
                },
                columns: [{
                    data: 'no',
                    orderable: false,
                    searchable: false,
                    className: 'text-center'
                }, {
                    data: 'PackSlip'
                }, {
                    data: 'PartNum'
                }, {
                    data: 'PartDesc'
                }, {
                    data: 'Qty'
                }, {
                    data: 'PricePO'
                }, {
                    data: 'PriceGR'
                }, {
                    data: 'AmountPO'
                }, {
                    data: 'AmountGR'
                }, {
                    data: 'UOM'
                }],
                createdRow: function(row, data, dataIndex) {
                    let PO = parseFloat(String(data.PricePO).replace(/[Rp\s.]/g, '').replace(',', '.')) || 0;
                    let GR = parseFloat(String(data.PriceGR).replace(/[Rp\s.]/g, '').replace(',', '.')) || 0;
                    let PricePO = parseFloat(String(data.PricePO).replace(/[Rp\s.]/g, '').replace(',', '.')) ||
                        0;
                    let PriceGR = parseFloat(String(data.PriceGR).replace(/[Rp\s.]/g, '').replace(',', '.')) ||
                        0;
                    if (PO !== GR || PriceGR !== PricePO) {
                        let cells = $(row).find("td");
                        // $(row).addClass('bg-warning');
                        $(cells[5]).css("color", "red");
                        $(cells[6]).css("color", "red");
                        $(cells[7]).css("color", "red");
                        $(cells[8]).css("color", "red");
                    }
                }
            })
        }

        function showPdfModal(type) {
            const urlParams = new URLSearchParams(window.location.search);
            const ref_doc = urlParams.get('ref_doc');
            $.ajax({
                url: "{{ url('ap_invoice/show_pdf') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: ref_doc,
                    type: type
                },
                success: function(response) {
                    const file = response.file;
                    const folder = response.folder;
                    const baseUrl = `https://vendor.summitadyawinsa.co.id/invoice/document/${folder}/`;
                    // const baseUrl = `http://127.0.0.1:8000/vendor/${folder}/`
                    const pdfUrl = baseUrl + file;
                    document.getElementById("pdfFrame").src = pdfUrl;
                    $("#modalDocument").modal('show');
                },
                error: function(xhr) {
                    console.log(xhr)
                }
            })
        }
        $("#showModalItemAll").on('click', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const ref_doc = urlParams.get('ref_doc');
            if ($.fn.DataTable.isDataTable($("#all_item_tbl"))) {
                $("#all_item_tbl").DataTable().clear().destroy()
            }
            $("#modalItemAll").modal('show')
            $("#all_item_tbl").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('ap_invoice/all_item_tbl') }}",
                    type: "POST",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}",
                            d.id = ref_doc,
                            d.search = $("#search_item").val()
                    }
                },
                columns: [{
                    data: 'no',
                    className: 'text-center'
                }, {
                    data: 'PartNum'
                }, {
                    data: 'PartDesc'
                }, {
                    data: 'Qty'
                }, {
                    data: 'PricePO'
                }, {
                    data: 'PriceGR'
                }, {
                    data: 'AmountPO'
                }, {
                    data: 'AmountGR'
                }, {
                    data: 'UOM'
                }],
                footerCallback: function(row, data, start, end, display) {
                    let api = this.api();
                    let json = api.ajax.json();
                    if (json && json.summary) {
                        let totalPO = parseFloat(json.summary.TotalPO) || 0;
                        let totalGR = parseFloat(json.summary.TotalGR) || 0;
                        $("#total_po_amount").html("Rp " + totalPO.toLocaleString("id-ID"));
                        $("#total_gr_amount").html("Rp " + totalGR.toLocaleString("id-ID"));
                    }
                },
                createdRow: function(row, data, dataIndex) {
                    let PO = parseFloat(String(data.PricePO).replace(/[Rp\s.]/g, '').replace(',',
                        '.')) || 0;
                    let GR = parseFloat(String(data.PriceGR).replace(/[Rp\s.]/g, '').replace(',',
                        '.')) || 0;
                    let PricePO = parseFloat(String(data.PricePO).replace(/[Rp\s.]/g, '').replace(',',
                            '.')) ||
                        0;
                    let PriceGR = parseFloat(String(data.PriceGR).replace(/[Rp\s.]/g, '').replace(',',
                            '.')) ||
                        0;
                    if (PO !== GR || PriceGR !== PricePO) {
                        let cells = $(row).find("td");
                        $(cells[4]).css("color", "red");
                        $(cells[5]).css("color", "red");
                        $(cells[6]).css("color", "red");
                        $(cells[7]).css("color", "red");
                    }
                }
            })
        })
        $("#search_item").on('change', function() {
            $("#all_item_tbl").DataTable().ajax.reload()
        })

        function exportExcel() {
            const urlParams = new URLSearchParams(window.location.search);
            const ref_doc = urlParams.get('ref_doc');
            const url = "{{ url('ap_invoice/export') }}" +
                "?ref_doc=" + encodeURIComponent(ref_doc);
            window.location.href = url;
        }
        $("#recalculateBtn").on('click', function() {
            const inv = $("#InvNumber").val()
            // $("#btn_text_recalculate").hide()
            $("#spin_btn_recalculate").show()
            if (!inv) {
                Toast.fire({
                    position: 'top-end',
                    title: 'Invoice Required!',
                    icon: "error"
                });
                return
            }
            $.ajax({
                url: "{{ url('ap_invoice/recalculate') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    inv: inv
                },
                success: function(response) {
                    if (response.status == 'success') {
                        Toast.fire({
                            position: 'top-end',
                            title: "Recalculate Successfully",
                            icon: "success"
                        });
                        checkStatus()
                        show_new_approval()
                    }
                    // $("#btn_text_recalculate").show()
                    $("#spin_btn_recalculate").hide()
                },
                error: function(xhr) {
                    console.log(xhr.message.responseText);
                }
            })
        })
        $("#submitReject").on('click', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const ref_doc = urlParams.get('ref_doc');
            const remark_reject = $("#remark_reject").val();
            if (!remark_reject) {
                Toast.fire({
                    position: 'top-end',
                    title: 'Remark is required!',
                    icon: "error"
                });
                return;
            }
            if (remark_reject.length >= 255) {
                Toast.fire({
                    position: 'top-end',
                    title: 'Remark maximal 255 characters!',
                    icon: "error"
                });
                return;
            }
            $.ajax({
                url: "{{ url('ap_invoice/submit_reject') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: ref_doc,
                    remark_reject: remark_reject
                },
                success: function(response) {
                    if (response.status == 200) {
                        Toast.fire({
                            position: 'top-end',
                            title: "Reject Successfully",
                            icon: "success"
                        });
                        $("#modalReject").modal('hide')
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
                        title: xhr.responseJSON.message,
                        icon: "error"
                    });
                }
            })
        })
    </script>
@endsection
