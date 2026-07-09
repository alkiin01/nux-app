@extends('../layouts/app')

@section('subhead')
<title>{{ $head_title }}</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<script type="text/javascript">
    $(document).ready(function() {
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
    })
</script>
@endsection

<script src="<?php echo env('APP_ASSETS') ?>assets/js/jquery/jquery.min.js"></script>

@section('subcontent')
<div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
        <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
            <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">{{ $head_title }}
                <span class="h-20px border-gray-200 border-start ms-3 mx-2"></span>
                <small class="text-muted fs-7 fw-bold my-1 ms-1">#{{ auth()->user()->full_name }}</small>
            </h1>
        </div>
    </div>
</div>

<div class="d-flex flex-column-fluid mt-lg-5 mt-sm-5">
    <div id="kt_content_container" class="container-xxl">
        <div class="card col-xxl-15 card-sticky">
            <div class="card-header border-1 pt-6 pb-6 mb-5">
                <div class="card-title">
                    <div class="d-flex align-items-center position-relative my-1">
                        <span class="svg-icon svg-icon-1 position-absolute ms-6">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                            </svg>
                        </span>
                        <input type="text" data-kt-goodreceive-table-filter="search" id="front_table_search" class="form-control form-control-solid w-250px ps-15 text-sm form-control-sm" placeholder="Search  Customer" />
                    </div>
                    <button
                        type="button"
                        id="btn_summary"
                        class="btn btn-primary btn-sm d-inline-flex align-items-center gap-2 shadow-sm px-3 ms-3"
                        onclick="summary()"
                        title="Summary Report">
                        <i class="bi bi-clipboard-data"></i>
                        <span>Summary</span>
                        <span id="sum_spinner" class="spinner-border spinner-border-sm ms-1 d-none" role="status" aria-hidden="true"></span>
                    </button>
                </div>

                <div class="card-toolbar d-flex align-items-center">
                    <form action="{{ route('entertain.import') }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-center gap-3 position-relative my-1">
                        @csrf
                        <input type="file" name="file" required class="form-control form-control-sm" style="max-width: 250px;">

                        <button type="submit" id="btn_import_excel" class="btn btn-light-success btn-sm p-0" title="Import Report" style=" width: 40px; height: 35px; align-items: center; display: flex; justify-content: center;">
                            <span id="svg_import_excel" class="svg-icon svg-icon-2 p-0 m-0" style="display: inline-block; align-items: center; justify-content: center;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-arrow-up p-0 m-0" viewBox="0 0 16 16" style="display: flex; align-items: center; justify-content: center;">
                                    <path d="M8 6.5a.5.5 0 0 0-.5.5v3.793L6.354 9.146a.5.5 0 1 0-.708.708l2 2a.5.5 0 0 0 .708 0l2-2a.5.5 0 1 0-.708-.708L8.5 10.793V7a.5.5 0 0 0-.5-.5z" />
                                    <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zm-3-.5a.5.5 0 0 1-.5-.5V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4h-2.5z" />
                                </svg>
                            </span>
                            <span id="spinner_import_excel" class="spinner-border spinner-border-sm align-middle" style="display: none;"></span>
                        </button>
                        <button type="button" class="btn btn-primary btn-sm me-3" id="btn_add_document" onclick="add_report()">
                            <span id="svg_add_document" class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <defs />
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1" />
                                        <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) " x="4" y="11" width="16" height="2" rx="1" />
                                    </g>
                                </svg>
                            </span>
                            <span id="spinner_add_document" class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;"></span>
                            <span id="btn_text_add_document">Create</span>
                        </button>
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>

                    </form>
                </div>
            </div>
            <div class="card-body pt-0">
                <table class="table align-middle table-row-dashed table-striped gy-2 fs-7" id="kt_doc_table">
                    <thead>
                        <tr class="text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                            <th class="min-w-20px pe-2">No</th>
                            <th class="min-w-20px">Date</th>
                            <th class="min-w-100px">Customer</th>
                            <th class="min-w-100px">Category</th>
                            <th class="min-w-100px">Num CA</th>
                            <th class="min-w-100px">Cost Center</th>
                            <th class="min-w-100px">Total Amount</th>
                            <th class="min-w-20px">Description</th>
                            <th class="min-w-20px">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                    </tbody>

                    <tfoot>
                        <tr class="text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                            <th class="min-w-20px pe-2">No</th>
                            <th class="min-w-20px">Date</th>
                            <th class="min-w-100px">Customer</th>
                            <th class="min-w-100px">Category</th>
                            <th class="min-w-100px">Num CA</th>
                            <th class="min-w-100px">Cost Center</th>
                            <th class="min-w-100px">Total Amount</th>
                            <th class="min-w-20px">Description</th>
                            <th class="min-w-20px">Action</th>
                        </tr>
                    </tfoot>
                </table>

            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        loadEntertainReportTable();
    });
    $(document).on('keyup', '#front_table_search', function() {
        const keyword = $(this).val();
        loadEntertainReportTable(keyword);
    });

    var entertainTable;

    function loadEntertainReportTable(keyword = '') {
        $.ajax({
            url: "{{ route('sales_report_entertain.get_reports') }}",
            type: 'GET',
            data: {
                keyword
            },
            success: function(data) {
                if ($.fn.DataTable.isDataTable('#kt_doc_table')) {
                    entertainTable.clear().rows.add(data).draw();
                } else {
                    entertainTable = $('#kt_doc_table').DataTable({
                        data: data,
                        columns: [{
                                data: null,
                                render: (data, type, row, meta) => meta.row + 1,
                                className: 'text-center'
                            },
                            {
                                data: 'Date',
                                orderable: false,
                                className: 'text-start text-nowrap'
                            },
                            {
                                data: 'Customer',
                                orderable: false,
                                className: 'text-start',
                            },
                            {
                                data: 'Category',
                                orderable: false,
                                className: 'text-start',
                            },

                            {
                                data: 'NumCA',
                                orderable: false,
                                className: 'text-start',

                            },
                            {
                                data: 'CostCenter',
                                orderable: false,
                                className: 'text-start',
                                render: d => d || '-'
                            },
                            {
                                data: 'TotalAmount',
                                orderable: false,
                                render: d => 'Rp ' + (Number(d).toLocaleString('id-ID', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                })),
                                className: 'text-start'
                            },
                            {
                                data: 'Description',
                                orderable: false,
                                className: 'text-center',
                                render: d => d || '-'
                            },
                            {
                                data: 'SysID',
                                className: 'text-center text-nowrap',
                                render: function(data, type, row) {
                                    return `
                                    <div style="display:inline-flex;align-items:center;gap:6px;white-space:nowrap">
       <button type="button" title="Export Report"
        class="btn btn-light-success btn-sm p-0"
        style="width:25;height:25;display:inline-flex;align-items:center;justify-content:center;line-height:1;color:var(--bs-success)"
        onmouseenter="this.style.color='#fff'"
        onmouseleave="this.style.color='var(--bs-success)'"
        onclick="exportExcel(${data}, event)">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" aria-hidden="true">
          <path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12V7l-4-4Z" fill="currentColor" opacity="0.3"/>
          <path d="M10.5 10.5L8 14l2.5 3.5h1.6L9.8 14l2.3-3.5h-1.6Z" fill="currentColor"/>
          <path d="M14 3v4h4" fill="currentColor"/>
        </svg>
      </button>
                                    <button type="button" title="Edit Report"
        class="btn btn-light-warning btn-sm p-0"
        style="width:25;height:25;display:inline-flex;align-items:center;justify-content:center;line-height:1;color:var(--bs-warning)"
        onmouseenter="this.style.color='#fff'"
        onmouseleave="this.style.color='var(--bs-warning)'"
        onclick="editReport_header(${data})">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" aria-hidden="true">
          <g fill="none"><rect width="24" height="24"/>
          <path d="M5,21 L5,19 L19,19 L19,21 L5,21 Z M19.707,6.293 L17.707,4.293 C17.317,3.902 16.683,3.902 16.293,4.293 L7,13.586 L7,17 L10.414,17 L19.707,7.707 C20.098,7.317 20.098,6.683 19.707,6.293 Z" fill="currentColor"/></g>
        </svg>
      </button>

      
      <button type="button" title="Delete Report"
        class="btn btn-light-danger btn-sm p-0"
        style="width:25;height:25;display:inline-flex;align-items:center;justify-content:center;line-height:1;color:var(--bs-danger)"
        onmouseenter="this.style.color='#fff'"
        onmouseleave="this.style.color='var(--bs-danger)'"
        onclick="deleteReport(${data})">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" aria-hidden="true">
          <g fill="none"><rect width="24" height="24"/>
          <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="currentColor"/>
          <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="currentColor" opacity="0.3"/></g>
        </svg>
      </button>
                                    </div>
                                    `;
                                },
                                orderable: false,
                                searchable: false,
                                className: 'text-center'
                            }
                        ],
                        destroy: true
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load the table.'
                });
            }
        });
    }

    function add_report() {
        var button = document.getElementById('btn_add_document');
        var svg = document.getElementById('svg_add_document');
        var spinner = document.getElementById('spinner_add_document');
        var buttonText = document.getElementById('btn_text_add_document');

        svg.style.display = 'none';
        spinner.style.display = 'inline-block';
        buttonText.textContent = 'Please Wait...';
        button.disabled = true;

        $.ajax({
            url: "{{ route('sales_report_entertain.add_report') }}",
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                svg.style.display = 'inline-block';
                spinner.style.display = 'none';
                buttonText.textContent = 'Create';
                button.disabled = false;

                $("#kt_content_container").html(data);

                loadDetailTable();

                setTimeout(function() {
                    $('#form_loader, #lds-roller-form').hide();
                }, 100);

                $('html, body').animate({
                    scrollTop: $("#kt_content_container").offset().top
                }, 400);
            },
            error: function() {
                svg.style.display = 'inline-block';
                spinner.style.display = 'none';
                buttonText.textContent = 'Create';
                button.disabled = false;

                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: 'Failed to load the form header.'
                });
            }
        });
    }


    function summary() {
        const $btn = $('#btn_summary');
        const oldHtml = $btn.html();

        $btn.prop('disabled', true)
            .html('<span class="spinner-border spinner-border-sm align-middle"></span> Loading...');

        $.ajax({
            url: "{{ route('sales_report_entertain.summary') }}",
            type: 'GET',
            dataType: 'html',
            cache: false,
            success: function(html) {
                 if (typeof window.disposeSummaryPage === 'function') window.disposeSummaryPage();  
                const $parsed = $($.parseHTML(html, document, true));
                const $src = $parsed.filter('#kt_content_container').length ?
                    $parsed.filter('#kt_content_container') :
                    $parsed.find('#kt_content_container');

                const innerHTML = $src.length ? $src.html() : html;
                
                
                const $target = $('#kt_content_container');
                $target.html(innerHTML);


                $src.find('script').each(function() {
                    const src = this.getAttribute('src');
                    if (src) {
                        $.ajax({
                            url: src,
                            dataType: 'script',
                            cache: true
                        });
                    } else {
                        $.globalEval(this.text || this.textContent || this.innerHTML || '');
                    }
                });


                if (typeof window.initSummaryPage === 'function') {
                    window.initSummaryPage();
                }

                $('html, body').animate({
                    scrollTop: $target.offset().top
                }, 300);
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: 'Failed to load the summary page.'
                });
            },
            complete: function() {
                $btn.prop('disabled', false).html(oldHtml);
            }
        });
    }






    function submitEntertainHeader() {
        var form = $('#entertain-header-form');
        var formData = form.serialize();
        var reportId = $('#ID_Report').val();

        var url = '';
        var type = '';

        if (reportId) {
            url = "{{ route('sales_report_entertain.update_header', ':SysID') }}".replace(':SysID', reportId);
            type = 'PUT';
        } else {
            url = "{{ route('sales_report_entertain.store') }}";
            type = 'POST';
        }

        $.ajax({
            url: url,
            type: type,
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: reportId ? 'Success' : 'Success',
                        text: reportId ? 'Header data updated successfully!' : 'Header data saved successfully!'
                    });
                    $('#ID_Report').val(response.data.SysID);
                    if (response.data && response.data.SysID) {
                        $('#btn_create_detail').prop('disabled', false);
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: 'Failed to save the data!'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while saving the data!'
                });
            }
        });
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

        $.ajax({
            url: "{{ route('sales_report_entertain.index') }}",
            type: 'GET',
            success: function(data) {

                $('#kt_content_container').html($(data).find('#kt_content_container').html());
                loadEntertainReportTable();

                svg.style.display = 'inline-block';
                spinner.style.display = 'none';
                buttonText.textContent = 'Back';
                button.disabled = false;

                $('html, body').animate({
                    scrollTop: 0
                }, 'fast');
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: 'Failed to load the main page.'
                });

                svg.style.display = 'inline-block';
                spinner.style.display = 'none';
                buttonText.textContent = 'Back';
                button.disabled = false;
            }
        });
    }

    function editReport_header(SysID) {
        $('#kt_content_container').html(
            '<div id="form_loader" style="text-align:center;">' +
            '<div class="lds-roller mt-10 mb-10" id="lds-roller-form">' +
            '<div></div><div></div><div></div><div></div>' +
            '<div></div><div></div><div></div><div></div>' +
            '</div></div>'
        );

        $.ajax({
            url: "{{ route('sales_report_entertain.edit_report_header') }}",
            type: 'GET',
            data: {
                SysID: SysID
            },
            dataType: 'html',
            success: function(data) {
                $("#kt_content_container").html(data);

                loadDetailTable();

                setTimeout(function() {
                    var loader = document.getElementById('form_loader');
                    var roller = document.getElementById('lds-roller-form');
                    if (loader) loader.style.display = 'none';
                    if (roller) roller.style.display = 'none';
                }, 100);
            },
            error: function(xhr) {
                let msg = 'Failed to load the edit form.';
                if (xhr.responseText) msg += '\n' + xhr.responseText;

                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: msg
                });

                loadEntertainReportTable();
            }
        });
    }

    function deleteReport(SysID) {
        Swal.fire({
            title: 'Are you sure to delete this data?',
            text: "Data that has been deleted cannot be restored!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('sales_report_entertain.delete_report') }}",
                    type: 'POST',
                    data: {
                        SysID: SysID,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Data successfully deleted!'
                            });
                            loadEntertainReportTable();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed',
                                text: 'Failed to delete data!'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while deleting the data!'
                        });
                    }
                });
            }
        });
    }

    function exportExcel(SysID, ev) {
        const btn = ev?.currentTarget || null;
        const oldHtml = btn ? btn.innerHTML : '';
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        }

        Swal.fire({
            title: 'Are you sure to download this file?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, download',
            cancelButtonText: 'Cancel',
        }).then((res) => {
            if (!res.isConfirmed) {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = oldHtml;
                }
                return;
            }

            const url = "{{ route('sales_report_entertain.export', ':SysID') }}".replace(':SysID', SysID);

            fetch(url, {
                    credentials: 'same-origin'
                })
                .then(r => {
                    if (!r.ok) throw new Error('HTTP ' + r.status);
                    return r.blob();
                })
                .then(blob => {
                    const a = document.createElement('a');
                    a.href = URL.createObjectURL(blob);
                    a.download = `Entertain_Report_${SysID}.xlsx`;
                    document.body.appendChild(a);
                    a.click();
                    URL.revokeObjectURL(a.href);
                    a.remove();
                })
                .catch(() => Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: 'Can`t download the file.'
                }))
                .finally(() => {
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = oldHtml;
                    }
                });
        });
    }
</script>

<script>
    document.getElementById('btn_import_excel').addEventListener('click', function() {
        let fileInput = document.querySelector('input[type="file"][name="file"]');
        if (fileInput && fileInput.files.length > 0) {
            document.getElementById('svg_import_excel').style.display = 'none';
            document.getElementById('spinner_import_excel').style.display = 'inline-block';
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Import Failed',
        text: '{{ session('
        error ') }}'
    });
</script>
@endif

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Import Success',
        text: '{{ session('
        success ') }}',
        timer: 3000,
        showConfirmButton: false
    });
</script>
@endif

@endsection