@extends('../layouts/app')


@section('subhead')
    <title>{{ $head_title }}</title>
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

<script src="/assets/js/jquery/jquery.min.js"></script>

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
            <div id="kt_activity_home" class="card-body p-0 tab-pane fade show" role="tabpanel"
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
                                        <input type="text" data-kt-goodreceive-table-filter="search"
                                            id="front_table_search"
                                            class="form-control form-control-solid w-250px ps-15 text-sm form-control-sm"
                                            placeholder="Search JO/PartNum" />
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
                                            <th class="min-w-20px">Team Name</th>
                                            <th class="min-w-20px">Role</th>
                                            <th class="min-w-100px">Action</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                            <th class="min-w-20px pe-2">No</th>
                                            <th class="min-w-20px">Team Name</th>
                                            <th class="min-w-20px">Role</th>
                                            <th class="min-w-100px">Action</th>
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
                        <div id="div-form-teams"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="text" hidden id="temp_id">

    <script>
        $(document).ready(function() {
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
                columnDefs: [{
                    orderable: false,
                    targets: 0
                }],
                ajax: {
                    url: "{{ route('team.get_team_data') }}",
                    type: 'POST',
                    data: function(d) {
                        d._token = $("[name=_token]").val();
                    },
                    cache: false,
                    dataType: 'json',
                    error: function(xhr, error, thrown) {
                        console.log("DataTables Error:", xhr.responseText);
                    }
                },
                columns: [{
                        data: 'no',
                        className: 'text-center'
                    },
                    {
                        data: 'name',
                    },
                    {
                        data: 'role'
                    }, {
                        data: 'action',
                        className: 'text-center',
                        orderable: false
                    },
                ],

            });


            setTimeout(function() {
                frontTable.ajax.reload();
            }, 500)

            return true;
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


            $("#div-form-teams").html("");
            var token = $("[name=_token]").val();
            var data = {
                _token: token
            };
            $.ajax({
                type: "post",
                url: "{{ route('team.add_document') }}",
                data: data,
                cache: false,
                success: function(data) {
                    svg.style.display = 'inline-block';
                    spinner.style.display = 'none';
                    buttonText.textContent = 'Create';
                    button.disabled = false;
                    document.getElementById('kt_activity_preview_tab').click();
                    $("#div-form-teams").html(data);
                    $("#lds-roller-form").css("display", "");
                    $("#form_label").css("display", "none");
                    $("#form_loader").css("display", "");
                    $("#team_name").val("");
                    $("#team_role").val("");
                    $("#id_teams").val("0");
                    setTimeout(function() {
                        $("#form_label").css("display", "");
                        $("#form_loader").css("display", "none");
                        $("#lds-roller-form").css("display", "none");
                        $("#createNew").val(1);
                    }, 500)
                },
                error: function(jqXHR, textStatus) {
                    svg.style.display = 'inline-block';
                    spinner.style.display = 'none';
                    buttonText.textContent = 'Create';
                    button.disabled = false;
                    Toast.fire({
                        position: 'top-end',
                        title: " Please reload and try again! ",
                        icon: "error"
                    })
                }
            });
        }



        function refresh_front_table() {
            if ($.fn.DataTable.isDataTable('#kt_doc_table')) {
                $('#kt_doc_table').DataTable().destroy();
            }
            front_table();
        }

        function delete_document(teamName, teamRole, no) {
            var button = document.getElementById('btn_form_delete_doc_' + no);
            var svg = document.getElementById('svg_form_delete_doc_' + no);
            var spinner = document.getElementById('spinner_form_delete_doc_' + no);

            svg.style.display = 'none';
            spinner.style.display = 'inline-block';
            buttonText.textContent = 'Please Wait...';
            button.disabled = true;

            $("#div-form-teams").html("");
            var token = $("[name=_token]").val();
            var string = "&_token=" + token + "&trc_unix_id=" + no;
            $.ajax({
                type: "POST",
                url: "{{ route('team.delete_document') }}",
                data: string,
                success: function(data) {
                    svg.style.display = 'inline-block';
                    spinner.style.display = 'none';
                    buttonText.textContent = 'Submit';
                    button.disabled = false;
                    setTimeout(function() {
                        refresh_front_table()
                    }, 500)

                }
            });
        }

        function open_document(teamName, teamRole, no) {
            var button = document.getElementById('btn_form_view_doc_' + no);
            var svg = document.getElementById('svg_form_view_doc_' + no);
            var spinner = document.getElementById('spinner_form_view_doc_' + no);

            svg.style.display = 'none';
            spinner.style.display = 'inline-block';
            button.disabled = true;

            $("#div-form-teams").html("");
            var token = $("[name=_token]").val();
            var string = "&_token=" + token + "&trc_unix_id=" + no;
            $.ajax({
                type: 'POST',
                url: "{{ route('team.add_document') }}",
                data: string,
                cache: false,
                success: function(data) {
                    svg.style.display = 'inline-block';
                    spinner.style.display = 'none';
                    button.disabled = false;
                    document.getElementById('kt_activity_preview_tab').click();
                    $("#div-form-teams").html(data);
                    $("#lds-roller-form").css("display", "");
                    $("#form_label").css("display", "none");
                    $("#form_loader").css("display", "");
                    setTimeout(function() {
                        $("#form_label").css("display", "");
                        $("#form_loader").css("display", "none");
                        $("#lds-roller-form").css("display", "none");
                        $("#createNew").val(1);
                        $("#team_name").val(teamName);
                        $("#team_role").val(teamRole);
                        $("#id_teams").val(no);
                    }, 500)
                },
                error: function(jqXHR, textStatus) {
                    svg.style.display = 'inline-block';
                    spinner.style.display = 'none';
                    buttonText.textContent = 'Create';
                    button.disabled = false;
                    Toast.fire({
                        position: 'top-end',
                        title: " Please reload and try again! ",
                        icon: "error"
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
            $("#isCopart").val(0);
            $("#createNew").val(0);
            // refresh_front_table();
            setTimeout(function() {
                svg.style.display = 'inline-block';
                spinner.style.display = 'none';
                buttonText.textContent = 'Back';
                button.disabled = false;
                document.getElementById('kt_activity_home_tab').click();
            }, 300)
        }
    </script>
@endsection
