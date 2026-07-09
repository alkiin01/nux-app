@extends('../layouts/app')

@section('subhead')
    <title>{{ $head_title }}</title>
@endsection

<script src="<?= env('APP_ASSETS') ?>assets/js/jquery/jquery.min.js"></script>

@section('subcontent')
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <div class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">Surat Jalan Approval
                    <span class="h-20px border-gray-200 border-start ms-3 mx-2"></span>
                    <small class="text-muted fs-7 fw-bold my-1 ms-1">#{{ $my_name }}</small>
                </h1>
            </div>
        </div>
    </div>

    <div hidden>
        <div class="card-toolbar m-0">
            <ul class="nav nav-tabs nav-line-tabs nav-stretch fs-6 border-0 fw-bolder" role="tablist">
                <li class="nav-item" role="presentation">
                    <a id="kt_activity_home_tab" class="nav-link justify-content-center text-active-gray-800 active" data-bs-toggle="tab" role="tab" href="#kt_activity_home">Home</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a id="kt_activity_preview_tab" class="nav-link justify-content-center text-active-gray-800" data-bs-toggle="tab" role="tab" href="#kt_activity_preview">Preview</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="content d-flex flex-column-fluid" id="kt_content">
        <div class="tab-content w-100">
            <div id="kt_activity_home" class="tab-pane fade show active" role="tabpanel" aria-labelledby="kt_activity_home_tab">
                <div class="container-xxl">
                    <div class="row g-5 g-xl-8 mb-2">
                        <div class="col-xl-4 col-lg-4 col-sm-6">
                            <a href="#" onclick="docSearch(1, this); return false;" class="card bgi-no-repeat card-xl-stretch mb-xl-8 card-front" style="background-position: right top; background-size: 30% auto; background-image: url(<?= env('APP_ASSETS') ?>assets/media/svg/shapes/abstract-4.svg)">
                                <div class="card-body">
                                    <div class="text-gray-900 fw-bolder fs-4 mb-2 mt-2" id="total_check">0</div>
                                    <div class="fw-bold text-gray-900">Waiting Check</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-sm-6">
                            <a href="#" onclick="docSearch(2, this); return false;" class="card bgi-no-repeat card-xl-stretch mb-xl-8 card-front" style="background-position: right top; background-size: 30% auto; background-image: url(<?= env('APP_ASSETS') ?>assets/media/svg/shapes/abstract-2.svg)">
                                <div class="card-body">
                                    <div class="text-gray-900 fw-bolder fs-4 mb-2 mt-2" id="total_approve">0</div>
                                    <div class="fw-bold text-gray-900">Waiting Approve</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-sm-6">
                            <a href="#" onclick="docSearch(3, this); return false;" class="card bgi-no-repeat card-xl-stretch mb-xl-8 card-front card-front-1" style="background-position: right top; background-size: 30% auto; background-image: url(<?= env('APP_ASSETS') ?>assets/media/svg/shapes/abstract-3.svg)">
                                <div class="card-body">
                                    <div class="text-gray-900 fw-bolder fs-4 mb-2 mt-2" id="total_all">0</div>
                                    <div class="fw-bold text-gray-900">All Status</div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-header border-0 pt-6">
                            <div class="card-title">
                                <div class="d-flex align-items-center position-relative my-1">
                                    <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                            <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                                        </svg>
                                    </span>
                                    <input type="text" id="front_table_search" class="form-control form-control-solid w-250px ps-15 text-sm form-control-sm" placeholder="Search SJ Number / Receiver" />
                                </div>
                            </div>
                            <div class="card-toolbar">
                                <div class="d-flex justify-content-end" data-kt-goodreceive-table-toolbar="base">
                                    <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true" id="kt-toolbar-filter">
                                        <div class="px-7 py-5">
                                            <div class="fs-4 text-dark fw-bolder">Filter Options</div>
                                        </div>
                                        <div class="separator border-gray-200"></div>
                                        <div class="px-7 py-5">
                                            <div class="mb-5">
                                                <label class="form-label fs-5 fw-bold mb-3">Status:</label>
                                                <select class="form-select form-select-solid fw-bolder form-select-sm text-sm" id="status_id" data-hide-search="true">
                                                    <option value="1">Waiting Check</option>
                                                    <option value="2">Waiting Approve</option>
                                                    <option value="3" selected>All Status</option>
                                                </select>
                                            </div>
                                            <div class="d-flex justify-content-end">
                                                <button type="submit" id="submit-filter" class="btn btn-primary btn-sm" data-kt-menu-dismiss="true">Apply</button>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-light-primary btn-sm me-3 text-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                        <span class="svg-icon svg-icon-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black" />
                                            </svg>
                                        </span>
                                        Filter
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="card-body pt-0">
                            <table class="table align-middle table-row-dashed table-striped fs-7 gy-3" id="kt_doc_table">
                                <thead>
                                    <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                        <th class="min-w-20px pe-2">No</th>
                                        <th class="min-w-120px">SJ Num</th>
                                        <th class="min-w-100px">DocDate</th>
                                        <th class="min-w-150px">Receiver</th>
                                        <th class="min-w-100px">Category</th>
                                        <th class="min-w-100px">Check</th>
                                        <th class="min-w-100px">Approve</th>
                                        <th class="text-end min-w-70px">Action</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                        <th class="min-w-20px pe-2">No</th>
                                        <th class="min-w-120px">SJ Num</th>
                                        <th class="min-w-100px">DocDate</th>
                                        <th class="min-w-150px">Receiver</th>
                                        <th class="min-w-100px">Category</th>
                                        <th class="min-w-100px">Check</th>
                                        <th class="min-w-100px">Approve</th>
                                        <th class="text-end min-w-70px">Action</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div id="kt_activity_preview" class="tab-pane fade show" role="tabpanel" aria-labelledby="kt_activity_preview_tab">
                <div class="container-xxl">
                    <div class="card shadow-sm">
                        <div class="card-header card-header-stretch">
                            <div class="card-title d-flex align-items-center gap-2" id="preview_action_buttons"></div>
                            <div class="card-toolbar m-0">
                                <button class="btn btn-light-primary btn-sm" onclick="backHome()">Back</button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <iframe id="preview_iframe" src="" style="width:100%; min-height:900px; border:0;"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var docTable = null;
        var currentEncId = '';
        var currentMode = 'view';
        var printBaseUrl = '{{ url('sj_general/print') }}/';

        $(document).ready(function() {
            loadCounts();
            initTable();

            $('#submit-filter').on('click', function() {
                if (docTable) {
                    docTable.ajax.reload();
                }
            });

            $('#front_table_search').on('keyup', function() {
                if (docTable) {
                    docTable.ajax.reload();
                }
            });
        });

        function loadCounts() {
            $.ajax({
                url: '{{ route('sj_approval.get_count_document') }}',
                type: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(res) {
                    $('#total_check').text(res.total_check || 0);
                    $('#total_approve').text(res.total_approve || 0);
                    $('#total_all').text(res.total_all || 0);
                }
            });
        }

        function docSearch(statusId, el) {
            $('#status_id').val(statusId);
            if (docTable) {
                docTable.ajax.reload();
            }
            $('.card-front').removeClass('border border-primary');
            $(el).addClass('border border-primary');
        }

        function initTable() {
            docTable = $('#kt_doc_table').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                lengthChange: false,
                pageLength: 10,
                order: [[2, 'desc']],
                ajax: {
                    url: '{{ route('sj_approval.front_table') }}',
                    type: 'POST',
                    data: function(d) {
                        d._token = '{{ csrf_token() }}';
                        d.status_id = $('#status_id').val();
                        d.front_table_search = $('#front_table_search').val();
                    }
                },
                columns: [
                    { data: 'no', className: 'text-center' },
                    { data: 'sj_number' },
                    { data: 'sj_date' },
                    { data: 'recipient_name' },
                    { data: 'category', className: 'text-center' },
                    { data: 'status_checker', className: 'text-center' },
                    { data: 'status_approver', className: 'text-center' },
                    { data: 'action', orderable: false, className: 'text-end' },
                ],
                drawCallback: function() {
                    loadCounts();
                }
            });
        }

        function openPreview(encId, mode) {
            currentEncId = encId;
            currentMode = mode || 'view';
            $('#preview_iframe').attr('src', printBaseUrl + encId);
            renderPreviewActions();

            if (window.bootstrap && document.getElementById('kt_activity_preview_tab')) {
                var previewTab = new bootstrap.Tab(document.getElementById('kt_activity_preview_tab'));
                previewTab.show();
            } else {
                $('#kt_activity_home').removeClass('show active');
                $('#kt_activity_preview').addClass('show active');
            }
        }

        function renderPreviewActions() {
            var html = '';
            html += '<button class="btn btn-sm btn-light-info" onclick="window.open(printBaseUrl + currentEncId, \"_blank\")"><i class="fa fa-print fs-6"></i> Print</button>';

            if (currentMode === 'check') {
                html += '<button class="btn btn-sm btn-success" onclick="doCheck(currentEncId, \"APPROVED\")"><i class="fa fa-check fs-6"></i> Check Approve</button>';
                html += '<button class="btn btn-sm btn-danger" onclick="doCheck(currentEncId, \"REJECTED\")"><i class="fa fa-times fs-6"></i> Check Reject</button>';
            } else if (currentMode === 'approve') {
                html += '<button class="btn btn-sm btn-success" onclick="doApprove(currentEncId, \"APPROVED\")"><i class="fa fa-thumbs-up fs-6"></i> Approve</button>';
                html += '<button class="btn btn-sm btn-danger" onclick="doApprove(currentEncId, \"REJECTED\")"><i class="fa fa-thumbs-down fs-6"></i> Reject</button>';
            }

            $('#preview_action_buttons').html(html);
        }

        function backHome() {
            if (window.bootstrap && document.getElementById('kt_activity_home_tab')) {
                var homeTab = new bootstrap.Tab(document.getElementById('kt_activity_home_tab'));
                homeTab.show();
            } else {
                $('#kt_activity_preview').removeClass('show active');
                $('#kt_activity_home').addClass('show active');
            }

            $('#preview_iframe').attr('src', '');
            currentEncId = '';
            currentMode = 'view';
            $('#preview_action_buttons').html('');
        }

        function doCheck(encId, status) {
            Swal.fire({
                title: status === 'APPROVED' ? 'Setujui check?' : 'Tolak check?',
                text: 'Status akan diperbarui.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, lanjutkan',
            }).then(function(result) {
                if (!result.isConfirmed) return;

                $.ajax({
                    url: '{{ route('sj_approval.do_check') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        enc_id: encId,
                        action_status: status,
                        note: ''
                    },
                    success: function(res) {
                        if (res.process_status == 200) {
                            Swal.fire('Berhasil', res.msg_process, 'success');
                            docTable.ajax.reload(null, false);
                            loadCounts();
                            backHome();
                        } else {
                            Swal.fire('Gagal', res.msg_process, 'error');
                        }
                    }
                });
            });
        }

        function doApprove(encId, status) {
            Swal.fire({
                title: status === 'APPROVED' ? 'Setujui dokumen?' : 'Tolak dokumen?',
                text: 'Status akan diperbarui.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, lanjutkan',
            }).then(function(result) {
                if (!result.isConfirmed) return;

                $.ajax({
                    url: '{{ route('sj_approval.do_approve') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        enc_id: encId,
                        action_status: status,
                        note: ''
                    },
                    success: function(res) {
                        if (res.process_status == 200) {
                            Swal.fire('Berhasil', res.msg_process, 'success');
                            docTable.ajax.reload(null, false);
                            loadCounts();
                            backHome();
                        } else {
                            Swal.fire('Gagal', res.msg_process, 'error');
                        }
                    }
                });
            });
        }
    </script>
@endsection