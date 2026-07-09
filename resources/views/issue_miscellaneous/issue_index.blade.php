@extends('../layouts/app')


@section('subhead')
    <title>{{ $head_title }}</title>
    <script type="text/javascript">
        $(document).ready(function() {
            front_table();

            const urlParams = new URLSearchParams(window.location.search);
            var ref_form = urlParams.get('ref_form');
            var ref_tab = urlParams.get('ref_tab');
            var ref_doc = urlParams.get('ref_doc');
            var revise = urlParams.get('revise');
            if (ref_doc == null) {
                $("#kt_activity_home_tab").addClass('show active');
            } else {
                document_preview(ref_doc);
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
                </h1> ˚
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
                <div class="post d-flex flex-column-fluid mb-0" id="kt_post">
                    <div id="kt_content_container" class="container-xxl">
                        <div class="card">
                            <div class="card-body">

                                <div class="row" id="form">
                                    <div class="col-md-6 mb-5 mt-5">
                                        <form>
                                            <div>
                                                <div class="form-group mb-3">
                                                    <label class="mb-2 text-sm">Date <span
                                                            class="text-danger">*</span></label>
                                                    <input type="date" class="form-control bg-light-primary"
                                                        id="SearchDocDate" name="SearchDocDate"
                                                        value="" />
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="col-md-6  mt-5">
                                        <form>
                                            <div>
                                                <div class="form-group mb-3">
                                                    <label class="mb-2 text-sm">Category <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select form-select-solid" data-kt-select2="true"
                                                        data-placeholder="Select option" data-allow-clear="false"
                                                        id="SearchCategory" name="SearchCategory"
                                                        data-hide-search="false">
                                                    <option value=""></option>
                                                    <option value="INV3">Store Room (SR)</option>
                                                    <option value="INV6">General Affairs (GA) </option>
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
                                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546"
                                                    height="2" rx="1" transform="rotate(45 17.0365 15.1223)"
                                                    fill="black" />
                                                <path
                                                    d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                                    fill="black" />
                                            </svg>
                                        </span>
                                        <input type="text" data-kt-goodreceive-table-filter="search"
                                            id="front_table_search"
                                            class="form-control form-control-solid w-250px ps-15 text-sm form-control-sm"
                                            placeholder="Search DocNum / Reason Code / Request By" />
                                    </div>
                                </div>

                                <div class="card-toolbar">
                                    <button type="button" class="btn btn-primary btn-sm me-3" id="btn_add_document"
                                        onclick="add_document()">
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
                                    </button>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <table class="table align-middle table-row-dashed table-striped gy-2 fs-7"
                                    id="kt_doc_table">
                                    <thead>
                                        <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                            <th class="min-w-20px pe-2">No</th>
                                            <th class="min-w-20px">Open</th>
                                            <th class="min-w-100px">DocNum</th>
                                            <th class="min-w-20px">DocDate</th>
                                            <th class="min-w-100px">Reason Code</th>
                                            <th class="min-w-100px">Request By</th>
                                            <th class="min-w-100px">Approved</th>
                                            <th class="min-w-100px">Submitted</th>
                                            <th class="min-w-140px">Status</th>
                                            <th class="min-w-20px">Total</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                            <th class="min-w-20px pe-2">No</th>
                                            <th class="min-w-20px">Open</th>
                                            <th class="min-w-100px">DocNum</th>
                                            <th class="min-w-20px">DocDate</th>
                                            <th class="min-w-100px">Reason Code</th>
                                            <th class="min-w-100px">Request By</th>
                                            <th class="min-w-100px">Approved</th>
                                            <th class="min-w-100px">Submitted</th>
                                            <th class="min-w-140px">Status</th>
                                            <th class="min-w-20px">Total</th>
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
                        <div id="div-form-issue-miscellaneuos"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- index --}}
    <script>
        function document_preview(refDoc) {
            open_document(refDoc, 'ref');
        }

        function front_table() {
            var frontTable = $("#kt_doc_table").DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                language: {
                    'processing': '<div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>'
                },
                info: false,
                order: [],
                columnDefs: [{
                    orderable: false,
                    targets: 0
                }],
                ajax: {
                    url: "{{ route('issue_miscellaneous.front_table') }}",
                    type: 'POST',
                    data: function(d) {
                        d._token = $("[name=_token]").val();
                        d.Category = $("#SearchCategory").val();
                        d.DocDate = $("#SearchDocDate").val();
                        d.front_table_search = $("#front_table_search").val();
                    },
                    cache: false,
                    dataType: 'json'
                },
                columns: [{
                        data: 'no',
                        className: 'text-center'
                    },
                    {
                        data: 'action',
                        className: 'text-center',
                        orderable: false
                    },
                    {
                        data: 'DocNum'
                    },
                    {
                        data: 'DocDate'
                    },
                    {
                        data: 'ReasonCode'
                    },
                    {
                        data: 'CreatedBy'
                    },
                    {
                        data: 'Approved'
                    },
                    {
                        data: 'Submitted'
                    },
                    {
                        data: 'RequestStatus'
                    },
                    {
                        data: 'TotalLine'
                    }
                ],
                initComplete: function(settings, json) {
                    var button = document.getElementById('btn_submit_search');
                    var svg = document.getElementById('svg_submit_search');
                    var spinner = document.getElementById('spinner_submit_search');
                    var buttonText = document.getElementById('btn_text_submit_search');
                    setTimeout(function() {
                        svg.style.display = 'inline-block';
                        spinner.style.display = 'none';
                        buttonText.textContent = 'Submit';
                        button.disabled = false;
                    }, 500)
                }
            });

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
        $("#front_table_search").keyup(function(event) {
            if (event.keyCode == 13) {
                refresh_front_table();
            }
        });

        function refresh_front_table() {
            if ($.fn.DataTable.isDataTable('#kt_doc_table')) {
                $('#kt_doc_table').DataTable().destroy();
            }
            front_table();
        }
    </script>
    {{-- end index --}}

    {{-- form --}}
    <script>
        function load_document_form(docNum, refDoc, button, svg, spinner) {
            var token = $("[name=_token]").val();

            if ($.fn.DataTable.isDataTable('#kt_form_table')) {
                $('#kt_form_table').DataTable().clear().destroy();
            }

            $("#div-form-issue-miscellaneuos").empty();

            $.ajax({
                type: 'POST',
                url: "{{ route('issue_miscellaneous.add_document') }}",
                data: {
                    _token: token,
                    DocNum: docNum
                },
                cache: false,
                success: function(data) {
                    if (button && svg && spinner) {
                        svg.style.display = 'inline-block';
                        spinner.style.display = 'none';
                        button.disabled = false;
                    }

                    if (refDoc) {
                        window.history.pushState({ ref_doc: refDoc }, '', window.location.pathname + '?ref_doc=' + encodeURIComponent(refDoc));
                    }

                    document.getElementById('kt_activity_preview_tab').click();
                    $("#div-form-issue-miscellaneuos").html(data);
                    $("#lds-roller-form").css("display", "");
                    $("#form_label").css("display", "none");
                    $("#form_loader").css("display", "");
                    setTimeout(function() {
                        $("#form_label").css("display", "");
                        $("#form_loader").css("display", "none");
                        $("#lds-roller-form").css("display", "none");
                    }, 500);
                },
                error: function() {
                    if (button && svg && spinner) {
                        svg.style.display = 'inline-block';
                        spinner.style.display = 'none';
                        button.disabled = false;
                    }

                    Toast.fire({
                        position: 'top-end',
                        title: " Please reload and try again! ",
                        icon: "error"
                    });
                }
            });
        }

        function document_preview(refDoc) {
            var token = $("[name=_token]").val();

            $.ajax({
                type: 'POST',
                url: "{{ route('issue_miscellaneous.add_document') }}",
                data: {
                    _token: token,
                    ref_doc: refDoc,
                    action: 'decrypt'
                },
                cache: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status !== 'success' || !response.DocNum) {
                        Toast.fire({
                            position: 'top-end',
                            title: response.message || 'Failed to process document',
                            icon: 'error'
                        });
                        return;
                    }

                    load_document_form(response.DocNum, refDoc);
                },
                error: function() {
                    Toast.fire({
                        position: 'top-end',
                        title: 'Failed to process document',
                        icon: 'error'
                    });
                }
            });
        }

        function open_document(refDoc, no) {
            var button = document.getElementById('btn_form_view_doc_'+no);
            var svg = document.getElementById('svg_form_view_doc_'+no);
            var spinner = document.getElementById('spinner_form_view_doc_'+no); 

            if (button && svg && spinner) {
                svg.style.display = 'none';
                spinner.style.display = 'inline-block'; 
                button.disabled = true;
            }

            $.ajax({
                type: 'POST',
                url: "{{ route('issue_miscellaneous.add_document') }}",
                data: {
                    _token: $("[name=_token]").val(),
                    ref_doc: refDoc,
                    action: 'decrypt'
                },
                cache: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status !== 'success' || !response.DocNum) {
                        if (button && svg && spinner) {
                            svg.style.display = 'inline-block';
                            spinner.style.display = 'none';
                            button.disabled = false;
                        }

                        Toast.fire({
                            position: 'top-end',
                            title: response.message || 'Failed to process document',
                            icon: 'error'
                        });
                        return;
                    }

                    load_document_form(response.DocNum, refDoc, button, svg, spinner);
                },
                error: function() {
                    if (button && svg && spinner) {
                        svg.style.display = 'inline-block';
                        spinner.style.display = 'none';
                        button.disabled = false;
                    }

                    Toast.fire({
                        position: 'top-end',
                        title: 'Failed to process document',
                        icon: 'error'
                    });
                }
            });
        } 

        function add_document() {
            var button = document.getElementById('btn_add_document');
            var svg = document.getElementById('svg_add_document');
            var spinner = document.getElementById('spinner_add_document');
            var buttonText = document.getElementById('btn_text_add_document'); 
            svg.style.display = 'none';
            spinner.style.display = 'inline-block';
            buttonText.textContent = 'Please Wait...'; 
            button.disabled = true;    
            if ($.fn.DataTable.isDataTable('#kt_form_table')) {
                $('#kt_form_table').DataTable().clear().destroy();
            }
            $("#div-form-issue-miscellaneuos").empty();
            var token = $("[name=_token]").val(); 

            $("#laborHedSeq").val(""); 
            $("#laborDtlSeq").val("");  
            $("#isCopart").val(0); 

            var string = "&_token="+token ;
            $.ajax({
                type	: 'POST',
                url	: "{{ route('issue_miscellaneous.add_document') }}",
                data	: string,
                cache	: false, 
                success : function(data){   
                    svg.style.display = 'inline-block';
                    spinner.style.display = 'none';
                    buttonText.textContent = 'Create'; 
                    button.disabled = false; 
                    window.history.pushState({}, '', window.location.pathname);
                    document.getElementById('kt_activity_preview_tab').click() ; 
                    $("#div-form-issue-miscellaneuos").html(data);  
                    $("#lds-roller-form").css("display", "");  
                    $("#form_label").css("display", "none");   
                    $("#form_loader").css("display", ""); 
                    setTimeout (function() {
                        $("#form_label").css("display", "");   
                        $("#form_loader").css("display", "none");   
                        $("#lds-roller-form").css("display", "none");   
                    },500) 
                },
                error: function( jqXHR, textStatus ) {
                    svg.style.display = 'inline-block';
                    spinner.style.display = 'none';
                    buttonText.textContent = 'Create'; 
                    button.disabled = false;  
                    Toast.fire({
                        position: 'top-end',
                        title: " Please reload and try again! ",
                        icon:"error"
                    }) 
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
            $("#laborHedSeq").val(""); 
            $("#laborDtlSeq").val("");  
            $("#isCopart").val(0); 
            $("#createNew").val(0) ;
            refresh_front_table(); 
            setTimeout(function(){
                svg.style.display = 'inline-block';
                spinner.style.display = 'none';
                buttonText.textContent = 'Back'; 
                button.disabled = false; 
                window.history.pushState({}, '', window.location.pathname);
                document.getElementById('kt_activity_home_tab').click(); 
            }, 300) 
        }
    </script>
    {{-- end form --}}
@endsection
