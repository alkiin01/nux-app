<style>
    .dtrg-group td {
        font-weight: bolder;
        font-size: 12px;
        padding: 5px;
    }

    .select2 .select2-search .select2-dropdown .select2-search__field .select2-search--dropdown {
        z-index: 9999 !important;
    }

    .modal .modal-dialog .modal-body .modal-footer {
        z-index: 1050 !important;
    }
</style>
<div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
        <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
            data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
            class="page-title d-flex align-items-center flex-wrap me-3 mb-1 mb-lg-0">
            <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">Summary Genba
                <span class="h-20px border-gray-200 border-start ms-3 mx-2"></span>
                <small class="text-muted fs-7 fw-bold my-1 ms-1">#{{ auth()->user()->full_name }}</small>
            </h1>
        </div>
    </div>
</div>
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="tab-content">
        <div class="row g-5 g-xl-12">
            <div class="card col-md-12 card-stretch gutter-b">
                <div class="card-header">
                    <div class="card-title">Detail Summary Genba</div>
                    <div class="card-toolbar">
                        <button class="btn btn-success btn-sm" id="btn_back_home" onclick="backHome()">
                            <span id="svg_back_home" class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <defs />
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <path
                                            d="M3.95709826,8.41510662 L11.47855,3.81866389 C11.7986624,3.62303967 12.2013376,3.62303967 12.52145,3.81866389 L20.0429,8.41510557 C20.6374094,8.77841684 21,9.42493654 21,10.1216692 L21,19.0000642 C21,20.1046337 20.1045695,21.0000642 19,21.0000642 L4.99998155,21.0000673 C3.89541205,21.0000673 2.99998155,20.1046368 2.99998155,19.0000673 L2.99999828,10.1216672 C2.99999935,9.42493561 3.36258984,8.77841732 3.95709826,8.41510662 Z M10,13 C9.44771525,13 9,13.4477153 9,14 L9,17 C9,17.5522847 9.44771525,18 10,18 L14,18 C14.5522847,18 15,17.5522847 15,17 L15,14 C15,13.4477153 14.5522847,13 14,13 L10,13 Z"
                                            fill="#000000" />
                                    </g>
                                </svg>
                            </span>
                            <span id="spinner_back_home" class="spinner-border spinner-border-sm svg-icon svg-icon-2"
                                style="display: none;"></span>
                            <span id="btn_text_back_home">Back</span>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form action="" class="form" id="header_form">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <input type="hidden" value="{{ $trc_unix_id }}" id="trc_unix_id">
                                    <div class="col-lg-6 mb-5">
                                        <label>Date</label>
                                        <input type="date" class="form-control" name="date" id="date"
                                            value="{{ $date ? date('Y-m-d', strtotime($date)) : date('Y-m-d') }}"
                                            required disabled />
                                    </div>

                                    <div class="col-lg-6 mb-5">
                                        <label>Genba Category</label>
                                        <input type="text" class="form-control" name="genba_category"
                                            id="genba_category" value="{{ $category }}" readonly />
                                    </div>
                                </div>
                            </div>
                        </div>

                </div>
                </form>

            </div>
            <div class="row g-5 g-xl-12 mt-10">
                <div class="col-xl-3 col-lg-6 col-sm-6">
                    <a href="#" onclick="docSearch(1, this);" class="card bgi-no-repeat card-xl-stretch mb-xl-8 card-front" style="background-position: right top; background-size: 30% auto; background-image: url(<?= env('APP_ASSETS') ?>assets/media/svg/shapes/abstract-4.svg)">
                        <div class="card-body">
                            <div class="text-gray-900 fw-bolder fs-4 mb-2 mt-2" id="total_action"></div>
                            <div class="fw-bold text-gray-900">Open</div>
                        </div>
                    </a>
                </div>
                <div class="col-xl-3 col-lg-6 col-sm-6">
                    <a href="#" onclick="docSearch(2, this);" class="card bgi-no-repeat card-xl-stretch mb-xl-8 card-front" style="background-position: right top; background-size: 30% auto; background-image: url(<?= env('APP_ASSETS') ?>assets/media/svg/shapes/abstract-2.svg)">
                        <div class="card-body">
                            <div class="text-gray-900 fw-bolder fs-4 mb-2 mt-2" id="total_evidence"></div>
                            <div class="fw-bold text-gray-900">Open Overdue</div>
                        </div>
                    </a>
                </div>
                <div class="col-xl-3 col-lg-6 col-sm-6">
                    <a href="#" onclick="docSearch(3, this);" class="card bgi-no-repeat card-xl-stretch mb-xl-8 card-front" style="background-position: right top; background-size: 30% auto; background-image: url(<?= env('APP_ASSETS') ?>assets/media/svg/shapes/abstract-1.svg)">
                        <div class="card-body">
                            <div class="text-gray-900 fw-bolder fs-4 mb-2 mt-2" id="total_verified"></div>
                            <div class="fw-bold text-gray-900">Close</div>
                        </div>
                    </a>
                </div>
                <div class="col-xl-3 col-lg-6 col-sm-6">
                    <a href="#" onclick="docSearch(4, this);" class="card bgi-no-repeat card-xl-stretch mb-xl-8 card-front card-front-1" style="background-position: right top; background-size: 30% auto; background-image: url(<?= env('APP_ASSETS') ?>assets/media/svg/shapes/abstract-3.svg)">
                        <div class="card-body">
                            <div class="text-gray-900 fw-bolder fs-4 mb-2 mt-2" id="total_findings"></div>
                            <div class="fw-bold text-gray-900">All Findings</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <div class="row g-5 g-xl-12 mt-5">
            <div class="card col-md-12 card-stretch gutter-b">
                <div class="card-header">
                    <div class="card-title">Activity Table</div>
                    <div class="card-toolbar">

                    </div>
                </div>
                <div class="card-body">
                    <table class="table align-middle dataTable table-row-dashed table-striped fs-7 gy-3"
                        id="kt_detailed_table">
                        <thead>
                            <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                <th class="min-w-20px pe-2">No</th>
                                <th class="min-w-30px">Pict.</th>
                                <th class="min-w-100px">Problem</th>
                                <th class="min-w-50px">Detail Area</th>
                                <th class="min-w-100px">Auditor</th>
                                <th class="min-w-40px">DIC</th>
                                <th class="min-w-50px">Due Date</th>
                                <th class="min-w-50px">Complete Date</th>
                                <th class="min-w-100px">Action Plan</th>
                                <th class="min-w-100px">Evidence</th>
                                <th class="min-w-100px">Verified</th>
                                <th class="min-w-100px">Pict. After</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                <th class="min-w-20px pe-2">No</th>
                                <th class="min-w-30px">Pict.</th>
                                <th class="min-w-100px">Problem</th>
                                <th class="min-w-50px">Detail Area</th>
                                <th class="min-w-100px">Auditor</th>
                                <th class="min-w-40px">DIC</th>
                                <th class="min-w-50px">Due Date</th>
                                <th class="min-w-50px">Complete Date</th>
                                <th class="min-w-100px">Action Plan</th>
                                <th class="min-w-100px">Evidence</th>
                                <th class="min-w-100px">Verified</th>
                                <th class="min-w-100px">Pict. After</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div id="kt_activity_preview" class="card-body p-0 tab-pane fade show" role="tabpanel"
            aria-labelledby="kt_activity_preview_tab">
            <div class="d-flex flex-column-fluid mt-lg-5 mt-sm-5">
                <div id="kt_content_container" class="container-xxl">
                    <div id="div-form-activity">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal trigger button -->


<!-- if you want to close by clicking outside the modal, delete the last endpoint:data-bs-backdrop and data-bs-keyboard -->
<div class="modal fade" id="imageModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="true"
    role="dialog" aria-labelledby="imageModaltitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content ">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModaltitle">
                    Findings
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div id="modalImageContent" class="row" style="cursor: zoom-in;"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="kt_modal_show" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="true"
    role="dialog" aria-labelledby="kt_modal_showtittle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="kt_modal_showtittle">
                    Asign Findings
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form">
                    <div class="row">
                        <div class="col-lg-6">
                            <input type="hidden" name="genba_id" id="genba_id">
                            <input type="hidden" name="scope_id" id="scope_id">
                            <input type="hidden" name="check_item_id" id="check_item_id">
                            <label for="asign_to" class="form-label">Asign To</label>
                            <select class="form-control" data-kt-select2="true" data-placeholder="Select option"
                                data-allow-clear="false" data-hide-search="false" name="asign_to" id="asign_to"
                                data-dropdown-parent="#kt_modal_show">
                            </select>
                        </div>
                        <div class="col-lg-6">
                            <label for="asign_to_dept" class="form-label">Department </label>
                            <select type="text" name="asign_to_dept" id="asign_to_dept" class="form-control"
                                placeholder="" aria-describedby="helpId" data-kt-select2="true"
                                data-placeholder="Select option" data-allow-clear="false" data-hide-search="false"
                                data-dropdown-parent="#kt_modal_show"></select>
                        </div>
                        <div class="col-lg-6">
                            <label for="priority" class="form-label">Priority </label>
                            <select type="text" name="priority" id="priority" class="form-control"
                                placeholder="" aria-describedby="helpId" data-kt-select2="true"
                                data-placeholder="Select option" data-allow-clear="false" data-hide-search="false"
                                data-dropdown-parent="#kt_modal_show">
                                <option value="1">Low</option>
                                <option value="2">Middle</option>
                                <option value="3">High</option>
                                <option value="4">Urgent</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Close
                </button>
                <button type="button" class="btn btn-primary" onclick="save_verification()">Save</button>
            </div>
        </div>
    </div>
</div>
<script>
    $('#area_checked').select2({
        ajax: {
            type: 'POST',
            url: "{{ route('genba.get_genba_area') }}",
            dataType: 'json',
            delay: 250, 
            data: function(params) {
                var query = {
                    search: params.term,
                    _token: $("[name=_token]").val(),
                    page: params.page || 1
                };
                return query;
            },
            processResults: function(data, params) {
                params.page = params.page || 1;
                return {
                    results: $.map(data.items, function(item) {
                        return {
                            id: item.name,
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

    $('#asign_to').select2({
        ajax: {
            type: 'POST',
            url: "{{ route('genba.get_user_data') }}",
            dataType: 'json',
            delay: 250, 
            data: function(params) {
                var query = {
                    search: params.term,
                    _token: $("[name=_token]").val(),
                    page: params.page || 1
                };
                return query;
            },
            processResults: function(data, params) {
                params.page = params.page || 1;
                return {
                    results: $.map(data.items, function(item) {
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
            cache: false
        },
        placeholder: 'Select option',
    });
    $('#asign_to_dept').select2({
        ajax: {
            type: 'POST',
            url: "{{ route('genba.get_section') }}",
            dataType: 'json',
            delay: 250, 
            data: function(params) {
                var query = {
                    search: params.term,
                    _token: $("[name=_token]").val(),
                    page: params.page || 1
                };
                return query;
            },
            processResults: function(data, params) {
                params.page = params.page || 1;
                return {
                    results: $.map(data.items, function(item) {
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
            cache: false
        },
        placeholder: 'Select option',
    });

    var token = $("[name=_token]").val();
    var trc_unix_id = $("#temp_id").val();

    $.ajax({
        type: 'POST',
        url: "{{ route('genba.get_waitting_findings') }}",
        data: {
            _token: token,
            trc_unix_id: trc_unix_id
        },
        cache: false,
        dataType: 'json',
        success: function(data) {
            $("#total_action").text(data.total_action + " Finding");
            $("#total_evidence").text(data.total_evidence + " Finding");
            $("#total_verified").text(data.total_verified + " Finding");
            $("#total_findings").text(data.total_findings + " Finding");
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", status, error);
            console.error("Response:", xhr.responseText);
        }
    });
</script>

<script>
    const myModal = new bootstrap.Modal(
        document.getElementById("imageModal"),
        options,
    );
</script>