@extends('../layouts/app')


@section('subhead')
    <title>{{ $head_title }}</title>
    <script type="text/javascript">
        $(document).ready(function () {
            const urlParams = new URLSearchParams(window.location.search);
            var ref_form = urlParams.get('ref_form');
            var ref_tab = urlParams.get('ref_tab');
            var ref_doc = urlParams.get('ref_doc');
            var revise = urlParams.get('revise');
            if (ref_doc == null) {
                $("#kt_activity_home_tab").addClass('show active');
            } else {
                $('#temp_id').val(ref_doc);
                document_preview(ref_doc);
            }
            $('input[type=number]').on('wheel', function (e) {
                $(this).blur(); // Menghilangkan fokus agar tidak scroll
            });
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
                    <a id="kt_activity_preview_tab" class="nav-link justify-content-center text-active-gray-800"
                        data-bs-toggle="tab" role="tab" href="#kt_activity_preview">Preview</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="tab-content">
            <div id="kt_activity_home" class="card-body p-0 tab-pane fade show active" role="tabpanel"
                aria-labelledby="kt_activity_home_tab">
                <div class="post d-flex flex-column-fluid mb-5" id="kt_post">
                    <div id="kt_content_container" class="container-xxl">
                        <div class="card">
                            <div class="card-body">
                                <div class="row" id="form">
                                    <div class="col-md-6 mb-5">
                                        <form>
                                            <div>
                                                <div class="form-group mb-3">
                                                    <label class="mb-2 text-sm">JO Date <span
                                                            class="text-danger">*</span></label>
                                                    <input type="date" class="form-control bg-light-primary" id="JoDate"
                                                        value="<?php date_default_timezone_set('Asia/Jakarta');
    echo date('Y-m-d');  ?>" />
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label class="mb-2 text-sm">Category <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select form-select-solid" data-kt-select2="true"
                                                        data-allow-clear="false" id="LineCategory" data-hide-search="true">
                                                        <option value='STP' selected>Stamping</option>
                                                        <option value='ASSY'>Assy</option>
                                                        <option value='PPIC'>Sharing / Repacking</option>
                                                        <option value='ENG'>Engineering</option>
                                                    </select>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label class="mb-2 text-sm">Shift<span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select form-select-solid text-sm"
                                                        data-kt-select2="true" data-allow-clear="false" id="ShiftID"
                                                        data-hide-search="true">
                                                        <option value='Shift 1' selected>Shift 1</option>
                                                        <option value='Shift 2'>Shift 2</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="col-md-6">
                                        <form>
                                            <div>
                                                <div class="form-group mb-3">
                                                    <label class="mb-2 text-sm">Line <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select form-select-solid" data-kt-select2="true"
                                                        data-placeholder="Select option" data-allow-clear="false"
                                                        id="ResourceGroupID" data-hide-search="false" />
                                                    </select>
                                                </div>

                                                <div class="form-group mb-3">
                                                    <label class="mb-2 text-sm">Machine <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select form-select-solid" data-kt-select2="true"
                                                        data-placeholder="Select option" data-allow-clear="true"
                                                        id="ResourceID" data-hide-search="false" />
                                                    </select>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="pt-5 pb-5">
                                        <hr style="color: gray">
                                        <button type="button" class="btn btn-primary btn-border btn-outline-primary btn-sm"
                                            id="btn_submit_search" onclick="submit_search()">
                                            <span id="svg_submit_search" class="svg-icon svg-icon-2">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                                    viewBox="0 0 24 24" version="1.1">
                                                    <defs />
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24" />
                                                        <path
                                                            d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z"
                                                            fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                                        <path
                                                            d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z"
                                                            fill="#000000" fill-rule="nonzero" />
                                                    </g>
                                                </svg>
                                            </span>
                                            <span id="spinner_submit_search"
                                                class="spinner-border spinner-border-sm svg-icon svg-icon-2"
                                                style="display: none;"></span>
                                            <span id="btn_text_submit_search">Submit</span>
                                        </button>

                                        <button type="button" class="btn btn-primary btn-border btn-outline-primary btn-sm"
                                            id="btn_export_production_sch" onclick="export_production_sch()">
                                            <span id="svg_export_production_sch" class="svg-icon svg-icon-2">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                                    viewBox="0 0 24 24" version="1.1">
                                                    <defs />
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <polygon points="0 0 24 0 24 24 0 24" />
                                                        <path
                                                            d="M5.74714567,13.0425758 C4.09410362,11.9740356 3,10.1147886 3,8 C3,4.6862915 5.6862915,2 9,2 C11.7957591,2 14.1449096,3.91215918 14.8109738,6.5 L17.25,6.5 C19.3210678,6.5 21,8.17893219 21,10.25 C21,12.3210678 19.3210678,14 17.25,14 L8.25,14 C7.28817895,14 6.41093178,13.6378962 5.74714567,13.0425758 Z"
                                                            fill="#000000" opacity="0.3" />
                                                        <path
                                                            d="M11.1288761,15.7336977 L11.1288761,17.6901712 L9.12120481,17.6901712 C8.84506244,17.6901712 8.62120481,17.9140288 8.62120481,18.1901712 L8.62120481,19.2134699 C8.62120481,19.4896123 8.84506244,19.7134699 9.12120481,19.7134699 L11.1288761,19.7134699 L11.1288761,21.6699434 C11.1288761,21.9460858 11.3527337,22.1699434 11.6288761,22.1699434 C11.7471877,22.1699434 11.8616664,22.1279896 11.951961,22.0515402 L15.4576222,19.0834174 C15.6683723,18.9049825 15.6945689,18.5894857 15.5161341,18.3787356 C15.4982803,18.3576485 15.4787093,18.3380775 15.4576222,18.3202237 L11.951961,15.3521009 C11.7412109,15.173666 11.4257142,15.1998627 11.2472793,15.4106128 C11.1708299,15.5009075 11.1288761,15.6153861 11.1288761,15.7336977 Z"
                                                            fill="#000000" fill-rule="nonzero"
                                                            transform="translate(11.959697, 18.661508) rotate(-270.000000) translate(-11.959697, -18.661508) " />
                                                    </g>
                                                </svg>
                                            </span>
                                            <span id="spinner_export_production_sch"
                                                class="spinner-border spinner-border-sm svg-icon svg-icon-2"
                                                style="display: none;"></span>
                                            <span id="btn_text_export_production_sch">Download</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

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
                                        <input type="text" data-kt-goodreceive-table-filter="search" id="front_table_search"
                                            class="form-control form-control-solid w-250px ps-15 text-sm form-control-sm"
                                            placeholder="Search JO/PartNum" />
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <table class="table align-middle table-row-dashed table-striped gy-2 fs-7"
                                    id="kt_doc_table">
                                    <thead>
                                        <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                            <th class="min-w-20px pe-2">No</th>
                                            <th class="min-w-20px">Print</th>
                                            <th class="min-w-20px">Achiev</th>
                                            <th class="min-w-100px">JONum</th>
                                            <th class="min-w-100px">Product</th>
                                            <th class="min-w-20px">Pack</th>
                                            <th class="min-w-20px">Line</th>
                                            <th class="min-w-20px">Process</th>
                                            <th class="min-w-20px">Plan</th>
                                            <th class="min-w-20px">Act</th>
                                            <th class="min-w-20px">Receive</th>
                                            <th class="min-w-100px">Remark</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                            <th class="min-w-20px pe-2">No</th>
                                            <th class="min-w-20px">Print</th>
                                            <th class="min-w-20px">Achiev</th>
                                            <th class="min-w-100px">JONum</th>
                                            <th class="min-w-100px">Product</th>
                                            <th class="min-w-20px">Pack</th>
                                            <th class="min-w-20px">Line</th>
                                            <th class="min-w-20px">Process</th>
                                            <th class="min-w-20px">Plan</th>
                                            <th class="min-w-20px">Act</th>
                                            <th class="min-w-20px">Receive</th>
                                            <th class="min-w-100px">Remark</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="kt_activity_preview" class="card-body p-0 tab-pane fade show" role="tabpanel"
                aria-labelledby="kt_activity_preview_tab">
                <div class="d-flex flex-column-fluid">
                    <div id="kt_content_container" class="container-xxl">
                        <div class="tab-content">
                            <div id="kt_activity_file" class="card-body tab_preview p-0 tab-pane fade show active"
                                role="tabpanel" aria-labelledby="kt_activity_file_tab">
                                <div class="card">
                                    <div class="card-header card-header-stretch">
                                        <div class="card-title d-flex align-items-center">
                                            <button class="btn btn-success btn-sm" id="btn_back_home" onclick="backHome()">
                                                <span id="svg_back_home" class="svg-icon svg-icon-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                        height="24px" viewBox="0 0 24 24" version="1.1">
                                                        <defs />
                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                            <rect x="0" y="0" width="24" height="24" />
                                                            <path
                                                                d="M3.95709826,8.41510662 L11.47855,3.81866389 C11.7986624,3.62303967 12.2013376,3.62303967 12.52145,3.81866389 L20.0429,8.41510557 C20.6374094,8.77841684 21,9.42493654 21,10.1216692 L21,19.0000642 C21,20.1046337 20.1045695,21.0000642 19,21.0000642 L4.99998155,21.0000673 C3.89541205,21.0000673 2.99998155,20.1046368 2.99998155,19.0000673 L2.99999828,10.1216672 C2.99999935,9.42493561 3.36258984,8.77841732 3.95709826,8.41510662 Z M10,13 C9.44771525,13 9,13.4477153 9,14 L9,17 C9,17.5522847 9.44771525,18 10,18 L14,18 C14.5522847,18 15,17.5522847 15,17 L15,14 C15,13.4477153 14.5522847,13 14,13 L10,13 Z"
                                                                fill="#000000" />
                                                        </g>
                                                    </svg>
                                                </span>
                                                <span id="spinner_back_home"
                                                    class="spinner-border spinner-border-sm svg-icon svg-icon-2"
                                                    style="display: none;"></span>
                                                <span id="btn_text_back_home">Back</span>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <div id="form_loader" style="text-align: center;">
                                            <div class="lds-roller mt-10 mb-10" id="lds-roller-form">
                                                <div></div>
                                                <div></div>
                                                <div></div>
                                                <div></div>
                                                <div></div>
                                                <div></div>
                                                <div></div>
                                                <div></div>
                                            </div>
                                        </div>
                                        <div class="row" id="form_label">
                                            <div class="col-md-6 mb-5">
                                                <form>
                                                    <div>
                                                        <div class="form-group mb-5">
                                                            <div class="d-flex">
                                                                <div class="col-6 form-group pl-2">
                                                                    <label>JO Number <span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control bg-light-primary"
                                                                        id="jo_num" readonly />
                                                                </div>

                                                                <div class="col-6 form-group" style="padding-left: 5px;">
                                                                    <label>Actual Production <span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="date" class="form-control"
                                                                        id="production_date" />
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="form-group mb-5">
                                                            <div class="d-flex">
                                                                <div class="col-6 form-group pl-2">
                                                                    <label>Part Num <span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control bg-light-primary"
                                                                        id="item_no" readonly />
                                                                </div>

                                                                <div class="col-6 form-group" style="padding-left: 5px;">
                                                                    <label>Part Name <span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control bg-light-primary"
                                                                        id="item_name" readonly />
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="form-group mb-5">
                                                            <div class="d-flex">
                                                                <div class="col-6 form-group pl-2">
                                                                    <label>Model <span class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control bg-light-primary"
                                                                        id="model_name" readonly />
                                                                </div>

                                                                <div class="col-6 form-group" style="padding-left: 5px;">
                                                                    <label>Customer <span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control bg-light-primary"
                                                                        id="partner_code" readonly />
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="form-group mb-5">
                                                            <div class="d-flex">
                                                                <div class="col-6 form-group pl-2">
                                                                    <label>Qty Plan <span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="number"
                                                                        class="form-control bg-light-primary" id="qty_plan"
                                                                        readonly />
                                                                </div>

                                                                <div class="col-6 form-group" style="padding-left: 5px;">
                                                                    <label>Qty Receive <span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="number"
                                                                        class="form-control bg-light-primary"
                                                                        id="qty_receive" readonly />
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </form>
                                            </div>

                                            <div class="col-md-6">
                                                <form>
                                                    <div>
                                                        <div class="form-group mb-5">
                                                            <label>Home Line <span class="text-danger">*</span></label>
                                                            <select class="form-select form-select-solid"
                                                                data-kt-select2="true" data-placeholder="Select option"
                                                                data-allow-clear="false" id="home_line"
                                                                data-hide-search="false" /></select>
                                                        </div>

                                                        <div class="form-group mb-5">
                                                            <div class="d-flex">
                                                                <div class="col-6 form-group pl-2">
                                                                    <label>Operator Name <span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control"
                                                                        id="operator_name" />
                                                                </div>

                                                                <div class="col-6 form-group" style="padding-left: 5px;">
                                                                    <label>Quality Name <span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control"
                                                                        id="quality_operator_name" />
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="form-group mb-5">
                                                            <div class="d-flex">
                                                                <div class="col-6 form-group pl-2">
                                                                    <label>Actual Shift <span
                                                                            class="text-danger">*</span></label>
                                                                    <select class="form-select form-select-solid text-sm"
                                                                        data-kt-select2="true" data-allow-clear="false"
                                                                        id="shift" data-hide-search="true">
                                                                        <option value='Shift 1' selected>Shift 1</option>
                                                                        <option value='Shift 2'>Shift 2</option>
                                                                    </select>
                                                                </div>

                                                                <div class="col-6 form-group" style="padding-left: 5px;">
                                                                    <label>Customer Label <span
                                                                            class="text-danger">*</span></label>
                                                                    <select class="form-select form-select-solid text-sm"
                                                                        data-kt-select2="true" data-allow-clear="false"
                                                                        id="input_label_type" data-hide-search="true">
                                                                        <option value='1' selected>Reguler</option>
                                                                        <option value='2'>Export</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="form-group mb-5">
                                                            <div class="d-flex">
                                                                <div class="col-6 form-group pl-2">
                                                                    <label>Std Packing <span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="number" class="form-control" id="std_pack"
                                                                        name="std_pack" />
                                                                </div>

                                                                <div class="col-6 form-group" style="padding-left: 5px;">
                                                                    <label>Warehouse <span
                                                                            class="text-danger">*</span></label>
                                                                    <select class="form-select form-select-solid"
                                                                        data-kt-select2="true"
                                                                        data-placeholder="Select option"
                                                                        data-allow-clear="false" id="ToWarehouseID"
                                                                        data-hide-search="false" /></select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="pt-5 pb-5 text-end">
                                            <hr style="color: gray">
                                            <button type="button" class="btn btn-primary btn-sm mr-2 mt-2"
                                                id="btn_generate_label" onclick="btn_generate_label()">
                                                <span id="svg_generate_label" class="svg-icon svg-icon-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                        height="24px" viewBox="0 0 24 24" version="1.1">
                                                        <defs />
                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                            <rect x="0" y="0" width="24" height="24" />
                                                            <path
                                                                d="M6,9 L6,15 C6,16.6568542 7.34314575,18 9,18 L15,18 L15,18.8181818 C15,20.2324881 14.2324881,21 12.8181818,21 L5.18181818,21 C3.76751186,21 3,20.2324881 3,18.8181818 L3,11.1818182 C3,9.76751186 3.76751186,9 5.18181818,9 L6,9 Z"
                                                                fill="#000000" fill-rule="nonzero" />
                                                            <path
                                                                d="M10.1818182,4 L17.8181818,4 C19.2324881,4 20,4.76751186 20,6.18181818 L20,13.8181818 C20,15.2324881 19.2324881,16 17.8181818,16 L10.1818182,16 C8.76751186,16 8,15.2324881 8,13.8181818 L8,6.18181818 C8,4.76751186 8.76751186,4 10.1818182,4 Z"
                                                                fill="#000000" opacity="0.3" />
                                                        </g>
                                                    </svg>
                                                </span>
                                                <span id="spinner_generate_label"
                                                    class="spinner-border spinner-border-sm svg-icon svg-icon-2"
                                                    style="display: none;"></span>
                                                <span id="btn_text_generate_label">Generate</span>
                                            </button>

                                            <button type="button" class="btn btn-danger btn-sm mr-2 mt-2"
                                                id="btn_clear_label" onclick="btn_clear_label()">
                                                <span id="svg_clear_label" class="svg-icon svg-icon-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                        height="24px" viewBox="0 0 24 24" version="1.1">
                                                        <defs />
                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                            <polygon points="0 0 24 0 24 24 0 24" />
                                                            <path
                                                                d="M5.85714286,2 L13.7364114,2 C14.0910962,2 14.4343066,2.12568431 14.7051108,2.35473959 L19.4686994,6.3839416 C19.8056532,6.66894833 20,7.08787823 20,7.52920201 L20,20.0833333 C20,21.8738751 19.9795521,22 18.1428571,22 L5.85714286,22 C4.02044787,22 4,21.8738751 4,20.0833333 L4,3.91666667 C4,2.12612489 4.02044787,2 5.85714286,2 Z"
                                                                fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                                            <path
                                                                d="M10.5857864,13 L9.17157288,11.5857864 C8.78104858,11.1952621 8.78104858,10.5620972 9.17157288,10.1715729 C9.56209717,9.78104858 10.1952621,9.78104858 10.5857864,10.1715729 L12,11.5857864 L13.4142136,10.1715729 C13.8047379,9.78104858 14.4379028,9.78104858 14.8284271,10.1715729 C15.2189514,10.5620972 15.2189514,11.1952621 14.8284271,11.5857864 L13.4142136,13 L14.8284271,14.4142136 C15.2189514,14.8047379 15.2189514,15.4379028 14.8284271,15.8284271 C14.4379028,16.2189514 13.8047379,16.2189514 13.4142136,15.8284271 L12,14.4142136 L10.5857864,15.8284271 C10.1952621,16.2189514 9.56209717,16.2189514 9.17157288,15.8284271 C8.78104858,15.4379028 8.78104858,14.8047379 9.17157288,14.4142136 L10.5857864,13 Z"
                                                                fill="#000000" />
                                                        </g>
                                                    </svg>
                                                </span>
                                                <span id="spinner_clear_label"
                                                    class="spinner-border spinner-border-sm svg-icon svg-icon-2"
                                                    style="display: none;"></span>
                                                <span id="btn_text_clear_label">Clear</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mt-10">
                                    <div class="card-header pt-0">
                                        <div class="card-toolbar mb-5 mt-5">
                                            <div class="d-flex justify-content-start"
                                                data-kt-goodreceive-table-toolbar="base">
                                                <button type="button" class="btn btn-light-primary btn-sm"
                                                    id="btn_print_all" onclick="btn_print_all()">
                                                    <span id="svg_print_all" class="svg-icon svg-icon-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                            height="24px" viewBox="0 0 24 24" version="1.1">
                                                            <defs />
                                                            <g stroke="none" stroke-width="1" fill="none"
                                                                fill-rule="evenodd">
                                                                <rect x="0" y="0" width="24" height="24" />
                                                                <path
                                                                    d="M16,17 L16,21 C16,21.5522847 15.5522847,22 15,22 L9,22 C8.44771525,22 8,21.5522847 8,21 L8,17 L5,17 C3.8954305,17 3,16.1045695 3,15 L3,8 C3,6.8954305 3.8954305,6 5,6 L19,6 C20.1045695,6 21,6.8954305 21,8 L21,15 C21,16.1045695 20.1045695,17 19,17 L16,17 Z M17.5,11 C18.3284271,11 19,10.3284271 19,9.5 C19,8.67157288 18.3284271,8 17.5,8 C16.6715729,8 16,8.67157288 16,9.5 C16,10.3284271 16.6715729,11 17.5,11 Z M10,14 L10,20 L14,20 L14,14 L10,14 Z"
                                                                    fill="#000000" />
                                                                <rect fill="#000000" opacity="0.3" x="8" y="2" width="8"
                                                                    height="2" rx="1" />
                                                            </g>
                                                        </svg>
                                                    </span>
                                                    <span id="spinner_print_all"
                                                        class="spinner-border spinner-border-sm align-middle ms-2"
                                                        style="display: none;"></span>
                                                    <span id="btn_text_print_all">Print All</span>
                                                </button>
                                                &nbsp;
                                                <button type="button" class="btn btn-light-primary btn-sm"
                                                    id="btn_print_all" onclick="getLabelForm('', 0)">
                                                    <span id="svg_add_manual" class="svg-icon svg-icon-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                            height="24px" viewBox="0 0 24 24" version="1.1">
                                                            <defs />
                                                            <g stroke="none" stroke-width="1" fill="none"
                                                                fill-rule="evenodd">
                                                                <rect fill="#000000" x="4" y="11" width="16" height="2"
                                                                    rx="1" />
                                                                <rect fill="#000000" opacity="0.3"
                                                                    transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) "
                                                                    x="4" y="11" width="16" height="2" rx="1" />
                                                            </g>
                                                        </svg>
                                                    </span>
                                                    <span id="spinner_add_manual"
                                                        class="spinner-border spinner-border-sm align-middle ms-2"
                                                        style="display: none;"></span>
                                                    <span id="btn_text_add_manual">Add Manual</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <table class="table align-middle table-row-dashed table-striped gy-2 fs-7"
                                            id="kt_doc_table_2">
                                            <thead>
                                                <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                                    <th class="min-w-20px pe-2">No</th>
                                                    <th class="min-w-100px">PartNum</th>
                                                    <th class="min-w-100px">Create By</th>
                                                    <th class="min-w-100px">Qty</th>
                                                    <th class="min-w-100px">Action</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                                    <th class="min-w-20px pe-2">No</th>
                                                    <th class="min-w-100px">PartNum</th>
                                                    <th class="min-w-100px">Create By</th>
                                                    <th class="min-w-100px">Qty</th>
                                                    <th class="min-w-100px">Action</th>
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

    <div class="modal fade" id="kt_modal_form" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog mw-650px">
            <div class="modal-content">
                <div class="modal-header pb-0 border-0 justify-content-end">
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)"
                                    fill="black" />
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="modal-body scroll-y mx-5 mx-xl-18 pt-0 pb-15">
                    <div class="text-center mb-5">
                        <h1 class="mb-3">TAG LABEL FORM</h1>
                        <div class="text-muted fw-bold fs-5">Please make sure all data is correct !</div>
                    </div>


                    <div class="form-group mb-5">
                        <label>Std Packing <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="inpt_std_pack" />
                    </div>

                    <div class="d-flex flex-stack">
                        <div class="me-5 fw-bold">
                            <label class="fs-6"></label>
                            <div class="fs-7 text-muted"></div>
                        </div>
                        <label class="form-check form-switch form-check-custom form-check-solid">
                            <button class="btn btn-primary btn-sm text-sm" id="btn_create_label"
                                onclick="saveUpdateLabel()">
                                <span id="svg_create_label" class="svg-icon svg-icon-2" style="margin-left: -7px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                        width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <defs />
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <polygon points="0 0 24 0 24 24 0 24" />
                                            <path
                                                d="M17,4 L6,4 C4.79111111,4 4,4.7 4,6 L4,18 C4,19.3 4.79111111,20 6,20 L18,20 C19.2,20 20,19.3 20,18 L20,7.20710678 C20,7.07449854 19.9473216,6.94732158 19.8535534,6.85355339 L17,4 Z M17,11 L7,11 L7,4 L17,4 L17,11 Z"
                                                fill="#000000" fill-rule="nonzero" />
                                            <rect fill="#000000" opacity="0.3" x="12" y="4" width="3" height="5" rx="0.5" />
                                        </g>
                                    </svg>
                                </span>
                                <span id="btn_text_create_label">Save</span>
                                <span id="spinner_create_label" class="spinner-border spinner-border-sm align-middle ms-2"
                                    style="display: none;"></span>
                            </button>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="kt_modal_show">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tag Label Preview</h5>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                        aria-label="Close">
                        <span class="svg-icon svg-icon-2x"></span>
                    </div>
                </div>

                <div class="modal-body text-center">
                    <div class="lds-roller mt-10" id="lds-roller">
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                    <div id="file_view"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <input type="text" hidden id="temp_id">
    <input type="text" hidden id="detail_temp_id">

    <script>

        function tag_label_preview(trc_unix_id) {
            $("#lds-roller").css("display", "");
            $("#file_view").html("");
            $("#kt_modal_show").modal('show');
            var token = $("[name=_token]").val();
            var layout = $("#input_label_type").val();
            var string = "&_token=" + token + "&trc_unix_id=" + trc_unix_id + "&layout=" + layout;
            $.ajax({
                type: 'POST',
                url: "{{ route('production_schedule.tag_print_view') }}",
                data: string,
                cache: false,
                success: function (data) {
                    setTimeout(function () {
                        $("#lds-roller").css("display", "none");
                        $("#file_view").html(data);
                    }, 500)
                }
            })
        };


        function btn_print_all() {
            var id = $("#temp_id").val();
            var layout = $("#input_label_type").val();
            $("#lds-roller").css("display", "");
            $("#file_view").html("");
            $("#kt_modal_show").modal('show');
            var token = $("[name=_token]").val();
            var layout = $("#input_label_type").val();
            var string = "&_token=" + token + "&trc_unix_id=" + id + "&layout=" + layout;
            $.ajax({
                type: 'POST',
                url: "{{ route('production_schedule.tag_print_view') }}",
                data: string,
                cache: false,
                success: function (data) {
                    setTimeout(function () {
                        $("#lds-roller").css("display", "none");
                        $("#file_view").html(data);
                    }, 500)
                }
            })
        };

        function saveUpdateLabel() {
            var token = $("[name=_token]").val();
            var trc_unix_id = $("#temp_id").val();
            var qty_plan = $("#qty_plan").val();
            var original_plan = $("#qty_plan").val();
            var qty_receive = $("#qty_receive").val();
            var std_pack = $("#inpt_std_pack").val();
            var home_line = $("#home_line").val();
            var operator_name = $("#operator_name").val();
            var quality_operator_name = $("#quality_operator_name").val();
            var model_name = $("#model_name").val();
            var part_num = $("#item_no").val();
            var part_name = $("#item_name").val();
            var production_date = $("#production_date").val();
            var partner_code = $("#partner_code").val();
            var temp_id = $("#temp_id").val();
            var detail_trc_unix_id = $("#detail_temp_id").val();
            var ToWarehouseID = $("#ToWarehouseID").val();
            var ToWarehouseDesc = $("#ToWarehouseID").find(":selected").text();

            var qty_plan = qty_plan - qty_receive;

            if (qty_plan <= 0) {
                Toast.fire({
                    position: 'top-end',
                    title: 'JO Sudah direceive!',
                    icon: "error"
                })
                return false;
            }

            if (std_pack == '' || std_pack == null || std_pack <= 0) {
                Toast.fire({
                    position: 'top-end',
                    title: 'Std pack harus lebih dari pada 0 !',
                    icon: "error"
                })
                return false;
            }
            if (home_line == '' || home_line == null) {
                Toast.fire({
                    position: 'top-end',
                    title: 'Detail line harus diisi !',
                    icon: "error"
                })
                return false;
            }
            if (operator_name == '' || operator_name == null) {
                Toast.fire({
                    position: 'top-end',
                    title: 'Nama operator machine harus diisi !',
                    icon: "error"
                })
                return false;
            }
            if (quality_operator_name == '' || quality_operator_name == null) {
                Toast.fire({
                    position: 'top-end',
                    title: 'Nama petugas quality harus diisi !',
                    icon: "error"
                })
                return false;
            }
            if (ToWarehouseID == '' || ToWarehouseID == null || ToWarehouseID == 'null') {
                Toast.fire({
                    position: 'top-end',
                    title: 'Warehouse harus diisi !',
                    icon: "error"
                })
                return false;
            }

            var button = document.getElementById('btn_create_label');
            var svg = document.getElementById('svg_create_label');
            var spinner = document.getElementById('spinner_create_label');
            var buttonText = document.getElementById('btn_text_create_label');
            svg.style.display = 'none';
            spinner.style.display = 'inline-block';
            buttonText.textContent = 'Please Wait...';
            button.disabled = true;

            var string = "&_token=" + token + "&trc_unix_id=" + trc_unix_id + "&qty_plan=" + qty_plan + "&std_pack=" + std_pack + "&home_line=" + home_line + "&operator_name=" + operator_name + "&quality_operator_name=" + quality_operator_name + "&partner_code=" + partner_code + "&model_name=" + model_name + "&part_num=" + part_num + "&part_name=" + part_name + "&production_date=" + production_date + "&detail_trc_unix_id=" + detail_trc_unix_id + "&original_plan=" + original_plan + "&ToWarehouseID=" + ToWarehouseID + "&ToWarehouseDesc=" + ToWarehouseDesc;
            $.ajax({
                type: 'POST',
                url: "{{ route('production_schedule.save_tag_label') }}",
                data: string,
                cache: false,
                dataType: 'json',
                success: function (data) {
                    setTimeout(function () {
                        svg.style.display = 'inline-block';
                        spinner.style.display = 'none';
                        buttonText.textContent = 'Save';
                        button.disabled = false;
                    }, 300)

                    if (data.process_status == 500) {
                        Toast.fire({
                            position: 'top-end',
                            title: data.msg_process,
                            icon: "error"
                        })
                    } else {
                        Toast.fire({
                            position: 'top-end',
                            title: data.msg_process,
                            icon: "success"
                        })
                        refresh_detail_table();
                        $("#kt_modal_form").modal('hide');
                    }
                    console.log();
                    return false;
                },
                error: function (jqXHR, textStatus) {
                    Toast.fire({
                        position: 'top-end',
                        title: "Reload and try again !",
                        icon: "error"
                    })

                    setTimeout(function () {
                        svg.style.display = 'inline-block';
                        spinner.style.display = 'none';
                        buttonText.textContent = 'Save';
                        button.disabled = false;
                    }, 300)
                }
            })
        }

        function getLabelForm(id, qty) {
            $("#kt_modal_form").modal('show');
            $("#detail_temp_id").val(id);
            $("#inpt_std_pack").val(qty);
        }



        function print_label(id, no) {
            var layout = $("#input_label_type").val();
            if (layout == 1) {
                window.open('<?php echo env('APP_URL') ?>/print_label_production_reguler?ref_doc=' + id + '&position_id=' + no, 'myWindow', 'status = 1, height = 650, width = 1100, resizable = 0, toolbar=no, location=no, status=no&#39, menubar=no, address bar=no');
            } else {
                window.open('<?php echo env('APP_URL') ?>/print_label_production_export?ref_doc=' + id + '&position_id=' + no, 'myWindow', 'status = 1, height = 650, width = 1100, resizable = 0, toolbar=no, location=no, status=no&#39, menubar=no, address bar=no');
            }
        };

        function export_production_sch() {
            var button = document.getElementById('btn_export_production_sch');
            var svg = document.getElementById('svg_export_production_sch');
            var spinner = document.getElementById('spinner_export_production_sch');
            var buttonText = document.getElementById('btn_text_export_production_sch');
            svg.style.display = 'none';
            spinner.style.display = 'inline-block';
            buttonText.textContent = 'Please Wait...';
            button.disabled = true;

            var token = $("[name=_token]").val();
            var JoDate = $("#JoDate").val();
            var LineCategory = $("#LineCategory").val();
            var ResourceGroupID = $("#ResourceGroupID").val();
            var ResourceID = $("#ResourceID").val();
            var ShiftID = $("#ShiftID").val();
            setTimeout(() => {
                svg.style.display = 'inline-block';
                spinner.style.display = 'none';
                buttonText.textContent = 'Download';
                button.disabled = false;
                var string = "?_token=" + token + "&date_sch=" + JoDate + "&category=" + LineCategory + "&line=" + ResourceGroupID + "&ResourceID=" + ResourceID + "&shift=" + ShiftID;
                window.open("<?php echo route('export_production_sch') ?>" + string);
            }, 500);
        };

        function btn_clear_label() {
            var button = document.getElementById('btn_clear_label');
            var svg = document.getElementById('svg_clear_label');
            var spinner = document.getElementById('spinner_clear_label');
            var buttonText = document.getElementById('btn_text_clear_label');
            svg.style.display = 'none';
            spinner.style.display = 'inline-block';
            buttonText.textContent = 'Please Wait...';
            button.disabled = true;
            Swal.fire({
                icon: 'warning',
                title: 'Delete Data?',
                text: "Hapus Semua Label",
                showCancelButton: true,
                confirmButtonText: 'Confirm',
            }).then(function (isConfirm) {
                if (isConfirm.value === true) {
                    clear_tag_label();
                } else {
                    svg.style.display = 'inline-block';
                    spinner.style.display = 'none';
                    buttonText.textContent = 'Clear';
                    button.disabled = false;
                }
            })
        }

        function btn_delete_label(id, qty) {
            var button = document.getElementById('btn_delete_tag_label_' + id);
            var svg = document.getElementById('svg_delete_tag_label_' + id);
            var spinner = document.getElementById('spinner_delete_tag_label_' + id);
            svg.style.display = 'none';
            spinner.style.display = 'inline-block';
            button.disabled = true;
            Swal.fire({
                icon: 'warning',
                title: 'Delete Data ?',
                text: "Hapus Label (Qty : " + qty + ")",
                showCancelButton: true,
                confirmButtonText: 'Confirm',
            }).then(function (isConfirm) {
                if (isConfirm.value === true) {
                    delete_tag_label(id);
                } else {
                    svg.style.display = 'inline-block';
                    spinner.style.display = 'none';
                    button.disabled = false;
                }
            })
        }

        function delete_tag_label(id) {
            var button = document.getElementById('btn_delete_tag_label_' + id);
            var svg = document.getElementById('svg_delete_tag_label_' + id);
            var spinner = document.getElementById('spinner_delete_tag_label_' + id);
            var token = $("[name=_token]").val();
            var trc_unix_id = $("#temp_id").val();
            var string = "&_token=" + token + "&trc_unix_id=" + trc_unix_id + "&detail_trc_unix_id=" + id;
            $.ajax({
                type: 'POST',
                url: "{{ route('production_schedule.delete_tag_label') }}",
                data: string,
                cache: false,
                dataType: 'json',
                success: function (data) {
                    if (data.process_status == 500) {
                        Toast.fire({
                            position: 'top-end',
                            title: data.msg_process,
                            icon: "error"
                        });
                    } else {
                        Toast.fire({
                            position: 'top-end',
                            title: data.msg_process,
                            icon: "success"
                        })
                        refresh_detail_table();
                    }
                    setTimeout(function () {
                        svg.style.display = 'inline-block';
                        spinner.style.display = 'none';
                        button.disabled = false;
                    }, 300)
                    return false;
                },
                error: function (jqXHR, textStatus) {
                    Toast.fire({
                        position: 'top-end',
                        title: 'Reload and try again !',
                        icon: "error"
                    });
                    setTimeout(function () {
                        svg.style.display = 'inline-block';
                        spinner.style.display = 'none';
                        button.disabled = false;
                    }, 300)
                }
            })
        }

        function clear_tag_label() {
            var button = document.getElementById('btn_clear_label');
            var svg = document.getElementById('svg_clear_label');
            var spinner = document.getElementById('spinner_clear_label');
            var buttonText = document.getElementById('btn_text_clear_label');

            var id = $("#temp_id").val();
            var token = $("[name=_token]").val();
            var string = "&_token=" + token + "&trc_unix_id=" + id;
            $.ajax({
                type: 'POST',
                url: "{{ route('production_schedule.clear_tag_label') }}",
                data: string,
                cache: false,
                dataType: 'json',
                success: function (data) {
                    if (data.process_status == 0) {
                        Toast.fire({
                            position: 'top-end',
                            title: data.msg_process,
                            icon: "error"
                        });
                    } else {
                        Toast.fire({
                            position: 'top-end',
                            title: data.msg_process,
                            icon: "success"
                        })
                        refresh_detail_table();
                    }
                    setTimeout(function () {
                        svg.style.display = 'inline-block';
                        spinner.style.display = 'none';
                        buttonText.textContent = 'Clear';
                        button.disabled = false;
                    }, 300)
                    return false;
                },
                error: function (jqXHR, textStatus) {
                    Toast.fire({
                        position: 'top-end',
                        title: 'Reload and try again !',
                        icon: "error"
                    });
                    setTimeout(function () {
                        svg.style.display = 'inline-block';
                        spinner.style.display = 'none';
                        buttonText.textContent = 'Clear';
                        button.disabled = false;
                    }, 300)
                }
            })
        }

        function backHome() {
            var button = document.getElementById('btn_back_home');
            var svg = document.getElementById('svg_back_home');
            var spinner = document.getElementById('spinner_back_home');
            var buttonText = document.getElementById('btn_text_back_home');
            svg.style.display = 'none';
            spinner.style.display = 'inline-block';
            buttonText.textContent = 'Please Wait...';
            button.disabled = true;
            $("#temp_id").val('');
            refresh_front_table();
            setTimeout(function () {
                svg.style.display = 'inline-block';
                spinner.style.display = 'none';
                buttonText.textContent = 'Back';
                button.disabled = false;
                document.getElementById('kt_activity_home_tab').click();
            }, 300)
        }

        function btn_generate_label() {
            var token = $("[name=_token]").val();
            var trc_unix_id = $("#temp_id").val();
            var qty_plan = $("#qty_plan").val();
            var qty_receive = $("#qty_receive").val();
            var std_pack = $("#std_pack").val();
            var home_line = $("#home_line").val();
            var operator_name = $("#operator_name").val();
            var quality_operator_name = $("#quality_operator_name").val();
            var model_name = $("#model_name").val();
            var part_num = $("#item_no").val();
            var part_name = $("#item_name").val();
            var production_date = $("#production_date").val();
            var partner_code = $("#partner_code").val();
            var original_plan = $("#qty_plan").val();
            var ToWarehouseID = $("#ToWarehouseID").val();
            var ToWarehouseDesc = $("#ToWarehouseID").find(":selected").text();

            var qty_plan = qty_plan - qty_receive;

            if (qty_plan <= 0) {
                Toast.fire({
                    position: 'top-end',
                    title: 'JO Sudah direceive!',
                    icon: "error"
                })
                return false;
            }

            if (std_pack == '' || std_pack == null || std_pack <= 0) {
                Toast.fire({
                    position: 'top-end',
                    title: 'Std pack harus lebih dari pada 0 !',
                    icon: "error"
                })
                return false;
            }
            if (home_line == '' || home_line == null) {
                Toast.fire({
                    position: 'top-end',
                    title: 'Detail line harus diisi !',
                    icon: "error"
                })
                return false;
            }
            if (operator_name == '' || operator_name == null) {
                Toast.fire({
                    position: 'top-end',
                    title: 'Nama operator machine harus diisi !',
                    icon: "error"
                })
                return false;
            }
            if (quality_operator_name == '' || quality_operator_name == null) {
                Toast.fire({
                    position: 'top-end',
                    title: 'Nama petugas quality harus diisi !',
                    icon: "error"
                })
                return false;
            }
            if (ToWarehouseID == '' || ToWarehouseID == null || ToWarehouseID == 'null') {
                Toast.fire({
                    position: 'top-end',
                    title: 'Warehouse harus diisi !',
                    icon: "error"
                })
                return false;
            }

            var button = document.getElementById('btn_generate_label');
            var svg = document.getElementById('svg_generate_label');
            var spinner = document.getElementById('spinner_generate_label');
            var buttonText = document.getElementById('btn_text_generate_label');
            svg.style.display = 'none';
            spinner.style.display = 'inline-block';
            buttonText.textContent = 'Please Wait...';
            button.disabled = true;

            var string = "&_token=" + token + "&trc_unix_id=" + trc_unix_id + "&qty_plan=" + original_plan + "&std_pack=" + std_pack + "&home_line=" + home_line + "&operator_name=" + operator_name + "&quality_operator_name=" + quality_operator_name + "&partner_code=" + partner_code + "&model_name=" + model_name + "&part_num=" + part_num + "&part_name=" + part_name + "&production_date=" + production_date + "&ToWarehouseID=" + ToWarehouseID + "&ToWarehouseDesc=" + ToWarehouseDesc;
            $.ajax({
                type: 'POST',
                url: "{{ route('production_schedule.generate_tag_label') }}",
                data: string,
                cache: false,
                dataType: 'json',
                success: function (data) {
                    setTimeout(function () {
                        svg.style.display = 'inline-block';
                        spinner.style.display = 'none';
                        buttonText.textContent = 'Generate';
                        button.disabled = false;
                    }, 300)

                    if (data.process_status == 500) {
                        Toast.fire({
                            position: 'top-end',
                            title: data.msg_process,
                            icon: "error"
                        })
                    } else {
                        Toast.fire({
                            position: 'top-end',
                            title: data.msg_process,
                            icon: "success"
                        })
                        refresh_detail_table();
                    }
                    console.log();
                    return false;
                },
                error: function (jqXHR, textStatus) {
                    Toast.fire({
                        position: 'top-end',
                        title: "Reload and try again !",
                        icon: "error"
                    })

                    setTimeout(function () {
                        svg.style.display = 'inline-block';
                        spinner.style.display = 'none';
                        buttonText.textContent = 'Generate';
                        button.disabled = false;
                    }, 300)
                }
            })
        }

        $(function () {
            $('#LineCategory').change(function () {
                var newOption = new Option('Select option', '0', true, true);
                $('#ResourceGroupID').append(newOption).trigger('change');
            })

            $('#ResourceGroupID').change(function () {
                var newOption = new Option('Select option', '0', true, true);
                $('#ResourceID').append(newOption).trigger('change');
            })

            $('#ResourceID').select2({
                ajax: {
                    type: 'POST',
                    url: "{{ route('production_schedule.get_resource') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        var query = {
                            search: params.term,
                            line: $("#ResourceGroupID").val(),
                            _token: $("[name=_token]").val(),
                            page: params.page || 1
                        };
                        return query;
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: $.map(data.items, function (item) {
                                return {
                                    id: item.id,
                                    text: item.name
                                };
                            }),
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },
                    cache: true
                },
                placeholder: 'Select option',
            });

            $('#home_line').select2({
                ajax: {
                    type: 'POST',
                    url: "{{ route('production_schedule.get_resource_form') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        var query = {
                            search: params.term,
                            category_id: $("#LineCategory").val(),
                            _token: $("[name=_token]").val(),
                            page: params.page || 1
                        };
                        return query;
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: $.map(data.items, function (item) {
                                return {
                                    id: item.id,
                                    text: item.name
                                };
                            }),
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },
                    cache: true
                },
                placeholder: 'Select option',
            });

            $('#ToWarehouseID').select2({
                ajax: {
                    type: 'POST',
                    url: "{{ route('production_schedule.get_warehouse_id') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        var query = {
                            search: params.term,
                            _token: $("[name=_token]").val(),
                            page: params.page || 1
                        };
                        return query;
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: $.map(data.items, function (item) {
                                return {
                                    id: item.id,
                                    text: item.name
                                };
                            }),
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },
                    cache: true
                },
                placeholder: 'Select option',
            });

            $('#ResourceGroupID').select2({
                ajax: {
                    type: 'POST',
                    url: "{{ route('production_schedule.get_resource_group') }}",
                    dataType: 'json',
                    delay: 250, // delay for search
                    data: function (params) {
                        var query = {
                            search: params.term,
                            category_id: $("#LineCategory").val(),
                            _token: $("[name=_token]").val(),
                            page: params.page || 1
                        };
                        return query;
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: $.map(data.items, function (item) {
                                return {
                                    id: item.id,
                                    text: item.name
                                };
                            }),
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },
                    cache: true
                },
                placeholder: 'Select option',
            });
        });
    </script>

    <script>

        $(document).ready(function () {
            front_table();
        })

        function front_table() {
            var frontTable = $("#kt_doc_table").DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                deferLoading: 57,
                language: {
                    'processing': '<div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>'
                },
                info: false,
                order: [],
                columnDefs: [
                    {
                        orderable: false,
                        targets: 0
                    }
                ],
                ajax: {
                    url: "{{ route('production_schedule.front_table') }}",
                    type: 'POST',
                    data: function (d) {
                        d._token = $("[name=_token]").val();
                        d.JoDate = $("#JoDate").val();
                        d.LineCategory = $("#LineCategory").val();
                        d.ResourceGroupID = $("#ResourceGroupID").val();
                        d.ResourceID = $("#ResourceID").val();
                        d.ShiftID = $("#ShiftID").val();
                        d.front_table_search = $("#front_table_search").val();
                    },
                    cache: false,
                    dataType: 'json'
                },
                columns: [
                    { data: 'no', className: 'text-center' },
                    {
                        data: 'action',
                        className: 'text-center',
                        orderable: false
                    },
                    { data: 'ach' },
                    { data: 'doc_num' },
                    { data: 'item_no' },
                    { data: 'qty_packing' },
                    { data: 'line' },
                    { data: 'process' },
                    { data: 'plan' },
                    { data: 'act' },
                    { data: 'receive' },
                    { data: 'remark' }
                ],
                initComplete: function (settings, json) {
                    var button = document.getElementById('btn_submit_search');
                    var svg = document.getElementById('svg_submit_search');
                    var spinner = document.getElementById('spinner_submit_search');
                    var buttonText = document.getElementById('btn_text_submit_search');
                    setTimeout(function () {
                        svg.style.display = 'inline-block';
                        spinner.style.display = 'none';
                        buttonText.textContent = 'Submit';
                        button.disabled = false;
                    }, 500)
                }
            });


            setTimeout(function () {
                frontTable.ajax.reload();
            }, 500)

            return true;
        }

        function submit_search() {
            var button = document.getElementById('btn_submit_search');
            var svg = document.getElementById('svg_submit_search');
            var spinner = document.getElementById('spinner_submit_search');
            var buttonText = document.getElementById('btn_text_submit_search');
            svg.style.display = 'none';
            spinner.style.display = 'inline-block';
            buttonText.textContent = 'Please Wait...';
            button.disabled = true;
            refresh_front_table();
        };

        $("#front_table_search").keyup(function (event) {
            if (event.keyCode == 13) { refresh_front_table(); }
        });

        function refresh_front_table() {
            if ($.fn.DataTable.isDataTable('#kt_doc_table')) {
                $('#kt_doc_table').DataTable().destroy();
            }
            front_table();
        }

        function detail_table() {
            var detailTable = $("#kt_doc_table_2").DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                deferLoading: 57,
                language: {
                    'processing': '<div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>'
                },
                info: !1,
                order: [],
                columnDefs: [
                    {
                        orderable: !1,
                        targets: 0
                    }],
                ajax: {
                    url: "{{ route('production_schedule.detail_table') }}",
                    type: 'POST',
                    data: function (d) { d._token = $("[name=_token]").val(), d.trc_unix_id = $("#temp_id").val(), d.detail_table_search = $("#detail_table_search").val(); },
                    cache: false,
                    dataType: 'json'
                },
                columns: [
                    { data: 'no', className: 'text-center' },
                    { data: 'item_no' },
                    { data: 'created_by' },
                    { data: 'plan' },
                    {
                        data: 'action',
                        className: 'text-center',
                        orderable: false
                    }
                ]
            })

            setTimeout(function () {
                detailTable.ajax.reload();
            }, 500)
        }

        function refresh_detail_table() {
            if ($.fn.DataTable.isDataTable('#kt_doc_table_2')) {
                $('#kt_doc_table_2').DataTable().destroy();
            }
            detail_table();
        }

        $("#detail_table_search").keyup(function (event) {
            if (event.keyCode == 13) { refresh_detail_table(); }
        });

        function form_generate_tag_label(trc_unix_id, no) {
            var button = document.getElementById('btn_generate_tag_label_' + no);
            var svg = document.getElementById('svg_generate_tag_label_' + no);
            var spinner = document.getElementById('spinner_generate_tag_label_' + no);
            svg.style.display = 'none';
            spinner.style.display = 'inline-block';
            button.disabled = true;

            $("#lds-roller-form").css("display", "");
            $("#form_label").css("display", "none");
            $("#form_loader").css("display", "");

            var token = $("[name=_token]").val();
            var string = "&_token=" + token + "&trc_unix_id=" + trc_unix_id;
            $.ajax({
                type: 'POST',
                url: "{{ route('production_schedule.get_preview_doc') }}",
                data: string,
                cache: false,
                dataType: 'json',
                success: function (data) {
                    document.getElementById('kt_activity_preview_tab').click();
                    $(".tab_preview").removeClass('active');
                    $("#kt_activity_file").addClass('show active');
                    $("#kt_activity_file_tab").addClass('active');
                    $("#temp_id").val(trc_unix_id);
                    $("#jo_num").val(data.jo_num);
                    $("#std_pack").val(data.qty_packing);
                    $("#production_date").val(data.req_due_date);
                    var newResourceID = new Option(data.home_line_detail_id, data.home_line_detail_id, true, true);
                    $('#home_line').append(newResourceID).trigger('change');
                    var newWarehouseID = new Option(data.WarehouseDesc, data.WarehouseID, true, true);
                    $('#ToWarehouseID').append(newWarehouseID).trigger('change');
                    $("#item_no").val(data.item_no);
                    $("#item_name").val(data.item_name.replace(/__/g, ","));
                    $("#partner_code").val(data.partner_code);
                    $("#model_name").val(data.model_name);
                    $("#qty_receive").val(data.qty_receive);
                    $("#qty_plan").val(data.qty_plan);
                    $("#operator_name").val("");
                    $("#quality_operator_name").val("");
                    setTimeout(function () {
                        $("#form_label").css("display", "");
                        $("#form_loader").css("display", "none");
                        $("#lds-roller-form").css("display", "none");
                        svg.style.display = 'inline-block';
                        spinner.style.display = 'none';
                        button.disabled = false;
                    }, 500)
                    refresh_detail_table();

                }
            })
        };



    </script>


@endsection