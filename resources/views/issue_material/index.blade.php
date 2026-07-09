@extends('../layouts/app')

@section('subhead')
    <title>{{ $head_title }}</title>
@endsection

<script src="<?= env('APP_ASSETS') ?>assets/js/jquery/jquery.min.js"></script>

@section('subcontent')
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <div class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
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
                <div class="d-flex flex-column-fluid mt-lg-5 mt-sm-5">
                    <div id="kt_content_container" class="container-xxl">
                        <div class="row g-5 mb-5">
                            <div class="col-xl-3 col-md-6">
                                <div class="card card-flush h-100 cursor-pointer summary-filter-card" data-category="assembly">
                                    <div class="card-body">
                                        <div class="text-gray-500 fw-bold fs-7">Assembly (ASY)</div>
                                        <div class="text-dark fw-bolder fs-2 mt-2" id="count_assembly">0</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card card-flush h-100 cursor-pointer summary-filter-card" data-category="stamping">
                                    <div class="card-body">
                                        <div class="text-gray-500 fw-bold fs-7">Stamping (STP)</div>
                                        <div class="text-dark fw-bolder fs-2 mt-2" id="count_stamping">0</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card card-flush h-100 cursor-pointer summary-filter-card" data-category="repacking">
                                    <div class="card-body">
                                        <div class="text-gray-500 fw-bold fs-7">Repacking (RPC)</div>
                                        <div class="text-dark fw-bolder fs-2 mt-2" id="count_repacking">0</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card card-flush h-100 cursor-pointer summary-filter-card" data-category="subcon">
                                    <div class="card-body">
                                        <div class="text-gray-500 fw-bold fs-7">SubCon (SBC)</div>
                                        <div class="text-dark fw-bolder fs-2 mt-2" id="count_subcon">0</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card col-xxl-12 card-sticky">
                            <div class="card-header border-1 pt-6 pb-6 mb-5">
                                <div class="card-title">
                                    <div class="row g-3 w-100">
                                        <div class="col-xl-2 col-md-6">
                                            <label class="form-label mb-2 fs-8">Category Job</label>
                                            <select id="job_category" class="form-select form-select-solid form-select-sm">
                                                <option value="">All Category</option>
                                                <option value="assembly">Assembly (ASY)</option>
                                                <option value="stamping">Stamping (STP)</option>
                                                <option value="repacking">Repacking (RPC)</option>
                                                <option value="subcon">SubCon (SBC)</option>
                                            </select>
                                        </div>
                                        <div class="col-xl-2 col-md-6">
                                            <label class="form-label mb-2 fs-8">Shift</label>
                                            <select id="shift" class="form-select form-select-solid form-select-sm">
                                                <option value="">All Shift</option>
                                                <option value="SHIFT 1">SHIFT 1</option>
                                                <option value="SHIFT 2">SHIFT 2</option>
                                            </select>
                                        </div>
                                        <div class="col-xl-2 col-md-6">
                                            <label class="form-label mb-2 fs-8">Start Date</label>
                                            <input type="date" id="start_date" class="form-control form-control-solid form-control-sm" value="{{ now()->format('Y-m-d') }}">
                                        </div>
                                        <div class="col-xl-2 col-md-6">
                                            <label class="form-label mb-2 fs-8">End Date</label>
                                            <input type="date" id="end_date" class="form-control form-control-solid form-control-sm" value="{{ now()->format('Y-m-d') }}">
                                        </div>
                                        <div class="col-xl-3 col-md-6">
                                            <label class="form-label mb-2 fs-8">Search</label>
                                            <div class="d-flex align-items-center position-relative my-1">
                                                <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                                        <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                                                    </svg>
                                                </span>
                                                <input type="text" id="front_table_search" class="form-control form-control-solid ps-15 text-sm form-control-sm" placeholder="Search JobNum / PartNum" />
                                            </div>
                                        </div>
                                        <div class="col-xl-1 col-md-12 d-flex align-items-end">
                                            <button type="button" class="btn btn-primary btn-sm w-100" id="btn_apply_filter">Apply</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <table class="table align-middle table-row-dashed table-striped gy-2 fs-7" id="kt_doc_table">
                                    <thead>
                                        <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                            <th>No</th>
                                            <th>Open</th>
                                            <th>JobNum</th>
                                            <th>PartNum</th>
                                            <th>Qty Required</th>
                                            <th>Qty Issued</th>
                                            <th>Progress</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                            <th>No</th>
                                            <th>Open</th>
                                            <th>JobNum</th>
                                            <th>PartNum</th>
                                            <th>Qty Required</th>
                                            <th>Qty Issued</th>
                                            <th>Progress</th>
                                            <th>Status</th>
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
                        <div id="div-form-issue-material"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let frontTableInstance = null;

        $(document).ready(function() {
            front_table();
            load_job_category_counts();
            update_summary_card_state();

            $('#btn_apply_filter').on('click', function() {
                apply_filters();
            });

            $('#job_category, #shift, #start_date, #end_date').on('change', function() {
                apply_filters();
            });

            $(document).on('click', '.summary-filter-card', function() {
                var selectedCategory = $(this).data('category');
                var currentCategory = $('#job_category').val();

                if (currentCategory === selectedCategory) {
                    $('#job_category').val('');
                } else {
                    $('#job_category').val(selectedCategory);
                }

                apply_filters();
            });
        });

        $(document).on('click', '#kt_doc_table .btn-open-issue-material', function () {
            var jobNum = $(this).data('job-num');
            var no = $(this).data('no');

            $("#svg_form_view_doc_" + no).hide();
            $("#spinner_form_view_doc_" + no).show();
            $("#btn_form_view_doc_" + no).prop('disabled', true);

            load_document_form(jobNum, no);
        });

        function front_table() {
            frontTableInstance = $("#kt_doc_table").DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                language: {
                    processing: '<div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>'
                },
                order: [],
                columnDefs: [{
                    orderable: false,
                    targets: [0, 1]
                }],
                ajax: {
                    url: "{{ route('inventory_rm_out.front_table') }}",
                    type: 'POST',
                    data: function(d) {
                        d._token = $("[name=_token]").val();
                        d.front_table_search = $("#front_table_search").val();
                        d.job_category = $("#job_category").val();
                        d.shift = $("#shift").val();
                        d.start_date = $("#start_date").val();
                        d.end_date = $("#end_date").val();
                    },
                    cache: false,
                    dataType: 'json'
                },
                columns: [{ data: 'no', className: 'text-center' },
                    { data: 'action', className: 'text-center', orderable: false },
                    { data: 'job_num' },
                    { data: 'part_num' },
                    { data: 'required_qty', className: 'text-end' },
                    { data: 'issued_qty', className: 'text-end' },
                    { data: 'progress', className: 'text-end' },
                    { data: 'status', className: 'text-center' }
                ]
            });

            $("#front_table_search").keyup(function(event) {
                if (event.keyCode === 13) {
                    apply_filters();
                }
            });
        }

        function load_job_category_counts() {
            $.ajax({
                type: 'POST',
                url: "{{ route('inventory_rm_out.get_job_category_counts') }}",
                data: {
                    _token: $("[name=_token]").val(),
                    job_category: $("#job_category").val(),
                    shift: $("#shift").val(),
                    start_date: $("#start_date").val(),
                    end_date: $("#end_date").val()
                },
                cache: false,
                dataType: 'json',
                success: function(response) {
                    if (!response || response.status !== 'success' || !response.data) {
                        return;
                    }

                    $("#count_assembly").text(response.data.assembly || 0);
                    $("#count_stamping").text(response.data.stamping || 0);
                    $("#count_repacking").text(response.data.repacking || 0);
                    $("#count_subcon").text(response.data.subcon || 0);
                }
            });
        }

        function apply_filters() {
            update_summary_card_state();
            refresh_front_table();
            load_job_category_counts();
        }

        function update_summary_card_state() {
            var selectedCategory = $('#job_category').val();

            $('.summary-filter-card').removeClass('border border-primary bg-light-primary');

            if (selectedCategory) {
                $('.summary-filter-card[data-category="' + selectedCategory + '"]').addClass('border border-primary bg-light-primary');
            }
        }

        function refresh_front_table() {
            if ($.fn.DataTable.isDataTable('#kt_doc_table') && frontTableInstance) {
                frontTableInstance.ajax.reload(null, false);
            }
        }

        function load_document_form(jobNum, no) {
            const token = $("[name=_token]").val();

            if ($.fn.DataTable.isDataTable('#kt_form_table')) {
                $('#kt_form_table').DataTable().clear().destroy();
            }

            $("#div-form-issue-material").empty();

            $.ajax({
                type: 'POST',
                url: "{{ route('inventory_rm_out.load_form') }}",
                data: {
                    _token: token,
                    job_num: jobNum
                },
                cache: false,
                success: function(data) {
                    if (no !== undefined) {
                        $("#svg_form_view_doc_" + no).show();
                        $("#spinner_form_view_doc_" + no).hide();
                        $("#btn_form_view_doc_" + no).prop('disabled', false);
                    }

                    document.getElementById('kt_activity_preview_tab').click();
                    $("#div-form-issue-material").html(data);
                },
                error: function() {
                    if (no !== undefined) {
                        $("#svg_form_view_doc_" + no).show();
                        $("#spinner_form_view_doc_" + no).hide();
                        $("#btn_form_view_doc_" + no).prop('disabled', false);
                    }

                    Toast.fire({
                        position: 'top-end',
                        title: 'Please reload and try again!',
                        icon: 'error'
                    });
                }
            });
        }

        function open_document(jobNum, no) {
            $("#svg_form_view_doc_" + no).hide();
            $("#spinner_form_view_doc_" + no).show();
            $("#btn_form_view_doc_" + no).prop('disabled', true);

            load_document_form(jobNum, no);
        }

        function back_to_issue_material_list() {
            document.getElementById('kt_activity_home_tab').click();
            refresh_front_table();
        }
    </script>
@endsection
