@extends('../layouts/app')

@section('subhead')
    <title>Issue Miscellaneous - Approval</title>
@endsection
<script src="<?= env('APP_ASSETS') ?>assets/js/jquery/jquery.min.js"></script>

@section('subcontent')
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">Issue Miscellaneous Approval
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
                <div class="post d-flex flex-column-fluid" id="kt_post">
                    <div id="kt_content_container" class="container-xxl">
                        <div class="row g-6 mb-6">
                            <div class="col-md-6 col-lg-3">
                                <div class="card card-flush">
                                    <div class="card-body">
                                        <div class="fs-2hx fw-bold" id="count_pending">0</div>
                                        <div class="fs-7 fw-bold text-muted">Need to Approve</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="card card-flush">
                                    <div class="card-body">
                                        <div class="fs-2hx fw-bold" id="count_approved">0</div>
                                        <div class="fs-7 fw-bold text-muted">Approved</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="card card-flush">
                                    <div class="card-body">
                                        <div class="fs-2hx fw-bold" id="count_completed">0</div>
                                        <div class="fs-7 fw-bold text-muted">Completed</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="card card-flush">
                                    <div class="card-body">
                                        <div class="fs-2hx fw-bold" id="count_rejected">0</div>
                                        <div class="fs-7 fw-bold text-muted">Rejected</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header border-1 pt-6">
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
                                        <input type="text" data-kt-approval-table-filter="search" id="approval_table_search"
                                            class="form-control form-control-solid w-250px ps-15 text-sm form-control-sm"
                                            placeholder="Search DocNum" />
                                    </div>
                                </div>
                            </div>
                            <div class="card-body py-4">
                                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_approval_table">
                                    <thead>
                                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                            <th class="min-w-125px">DocNum</th>
                                            <th class="min-w-125px">Doc Date</th>
                                            <th class="min-w-100px">Category</th>
                                            <th class="min-w-100px">Submitted By</th>
                                            <th class="min-w-125px">Submitted At</th>
                                            <th class="min-w-80px">Total Lines</th>
                                            <th class="text-end min-w-100px">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600 fw-bold">
                                    </tbody>
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
                        <div id="div-form-approval-issue-miscellaneous"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Use global Toast from main.blade.php - don't redefine it
        function approval_table(params) {
            console.log('approval_table() called');
            
            if ($.fn.DataTable.isDataTable('#kt_approval_table')) {
                $('#kt_approval_table').DataTable().destroy();
            }

            if ($('#kt_approval_table').length === 0) {
                console.error('Element #kt_approval_table not found!');
                return;
            }

            var t = $("#kt_approval_table").DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('issue_miscellaneous.approval_table') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data": {
                        _token: "{{ csrf_token() }}"
                    }
                },
                "columns": [{
                        "data": "DocNum",
                        "className": "text-start"
                    },
                    {
                        "data": "DocDate",
                        "className": "text-start"
                    },
                    {
                        "data": "Category",
                        "className": "text-start"
                    },
                    {
                        "data": "SubmittedBy",
                        "className": "text-start"
                    },
                    {
                        "data": "SubmittedAt",
                        "className": "text-start"
                    },
                    {
                        "data": "TotalLine",
                        "className": "text-center"
                    },
                    {
                        "data": "action",
                        "className": "text-end"
                    }
                ],
                "order": [
                    [4, 'desc']
                ],
                "columnDefs": [{
                    "targets": [6],
                    "orderable": false,
                }, ],
            });

            $('#approval_table_search').off('keyup').on('keyup', function() {
                t.search($(this).val()).draw();
            });

            console.log('approval_table initialized');
            load_status_counts();
        }

        function load_status_counts() {
            $.ajax({
                url: "{{ route('issue_miscellaneous.get_approval_status_counts') }}",
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(data) {
                    $('#count_pending').text(data.pending_approval || 0);
                    $('#count_approved').text(data.approved || 0);
                    $('#count_completed').text(data.completed || 0);
                    $('#count_rejected').text(data.rejected || 0);
                },
                error: function() {
                    console.error('Failed to load approval status counts');
                }
            });
        }

        function review_document(refUrl) {
            $("#div-form-approval-issue-miscellaneous").html("");

            const newUrl = window.location.pathname + '?ref_url=' + encodeURIComponent(refUrl);
            window.history.pushState({ ref_url: refUrl }, '', newUrl);

            $.ajax({
                type: 'POST',
                url: "{{ route('issue_miscellaneous.approval_form') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    ref_url: refUrl,
                    action: 'decrypt'
                },
                cache: false,
                dataType: 'json',
                success: function(decryptResponse) {
                    if (decryptResponse.status !== 'success') {
                        if (typeof Toast !== 'undefined' && Toast.fire) {
                            Toast.fire({
                                position: 'top-end',
                                title: decryptResponse.message || "Failed to process document!",
                                icon: "error"
                            });
                        }

                        return;
                    }

                    const docNum = decryptResponse.docnum;

                    $.ajax({
                        type: 'POST',
                        url: "{{ route('issue_miscellaneous.approval_form') }}",
                        data: {
                            _token: "{{ csrf_token() }}",
                            DocNum: docNum
                        },
                        cache: false,
                        success: function(data) {
                            $("#div-form-approval-issue-miscellaneous").html(data);

                            // Force switch to preview tab even when hidden nav is used.
                            const previewTabEl = document.getElementById('kt_activity_preview_tab');
                            if (previewTabEl && window.bootstrap && bootstrap.Tab) {
                                bootstrap.Tab.getOrCreateInstance(previewTabEl).show();
                            } else if (previewTabEl) {
                                previewTabEl.click();
                            }

                            // Initialize partial form logic explicitly after HTML injection.
                            if (typeof init_approval_form === 'function') {
                                init_approval_form(docNum);
                            }
                        },
                        error: function() {
                            if (typeof Toast !== 'undefined' && Toast.fire) {
                                Toast.fire({
                                    position: 'top-end',
                                    title: "Please reload and try again!",
                                    icon: "error"
                                });
                            } else {
                                alert("Please reload and try again!");
                            }
                        }
                    });
                },
                error: function() {
                    if (typeof Toast !== 'undefined' && Toast.fire) {
                        Toast.fire({
                            position: 'top-end',
                            title: "Failed to process document!",
                            icon: "error"
                        });
                    } else {
                        alert("Failed to process document!");
                    }
                }
            });
        }

        // Handle URL parameter on page load/refresh - PRIORITY
        function initializeApprovalPage() {
            console.log('initializeApprovalPage() called');
            const urlParams = new URLSearchParams(window.location.search);
            const refUrl = urlParams.get('ref_url');
            
            console.log('Page loaded - refUrl:', refUrl);
            
            if (refUrl) {
                console.log('ref_url found, attempting to decrypt and load form');
                // Decrypt and load form directly on page refresh
                $.ajax({
                    type: 'POST',
                    url: "{{ route('issue_miscellaneous.approval_form') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        ref_url: refUrl,
                        action: 'decrypt'
                    },
                    cache: false,
                    dataType: 'json',
                    success: function(decryptResponse) {
                        console.log('Decrypt success:', decryptResponse);
                        if (decryptResponse.status === 'success') {
                            // Load form directly without table
                            const docNum = decryptResponse.docnum;
                            $("#div-form-approval-issue-miscellaneous").html("");

                            $.ajax({
                                type: 'POST',
                                url: "{{ route('issue_miscellaneous.approval_form') }}",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    DocNum: docNum
                                },
                                cache: false,
                                success: function(data) {
                                    $("#div-form-approval-issue-miscellaneous").html(data);

                                    // Switch to preview tab
                                    const previewTabEl = document.getElementById('kt_activity_preview_tab');
                                    if (previewTabEl && window.bootstrap && bootstrap.Tab) {
                                        bootstrap.Tab.getOrCreateInstance(previewTabEl).show();
                                    } else if (previewTabEl) {
                                        previewTabEl.click();
                                    }

                                    // Initialize form
                                    if (typeof init_approval_form === 'function') {
                                        init_approval_form(docNum);
                                    }
                                },
                                error: function(err) {
                                    console.error('Form load error:', err);
                                    if (typeof Toast !== 'undefined' && Toast.fire) {
                                        Toast.fire({
                                            position: 'top-end',
                                            title: "Failed to load form!",
                                            icon: "error"
                                        });
                                    } else {
                                        alert("Failed to load form!");
                                    }
                                    // If form load fails, show table
                                    approval_table();
                                }
                            });
                        } else {
                            console.error('Decrypt failed:', decryptResponse.message);
                            approval_table();
                        }
                    },
                    error: function(err) {
                        console.error('Decrypt AJAX error:', err);
                        approval_table();
                    }
                });
            } else {
                console.log('No ref_url, loading table');
                // No ref_url, show table normally
                approval_table();
            }
        }

        $(document).ready(function() {
            console.log('$(document).ready() fired');
            initializeApprovalPage();
        });

        // Failsafe: also call on window.onload
        window.addEventListener('load', function() {
            console.log('window.onload fired');
            // Check if table already initialized, if not, initialize it
            if (!$.fn.DataTable.isDataTable('#kt_approval_table')) {
                console.log('DataTable not initialized, initializing now');
                initializeApprovalPage();
            }
        });

        function back_approval_home() {
            document.getElementById('kt_activity_home_tab').click();
            approval_table();
        }
    </script>
@endsection
