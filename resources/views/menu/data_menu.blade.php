@extends('layouts.app')


@section('subhead')
    <title>{{ $head_title }}</title>
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
            <div id="div_data_table" class="card-body p-0 tab-pane fade show active" role="tabpanel"
                aria-labelledby="kt_activity_home_tab">
                <div class="post d-flex flex-column-fluid mb-5" id="kt_post">
                    <div id="kt_content_container" class="container-xxl">

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
                                        <input type="text" data-kt-goodreceive-table-filter="search"
                                            id="front_table_search"
                                            class="form-control form-control-solid w-250px ps-15 text-sm form-control-sm"
                                            placeholder="Search..." />
                                    </div>
                                </div>
                                <div class="card-toolbar">

                                    <div class="d-flex justify-content-end align-items-center d-none"
                                        data-kt-goodreceive-table-toolbar="selected">
                                        <div class="fw-bolder me-5">
                                            <span class="me-2"
                                                data-kt-goodreceive-table-select="selected_count"></span>Selected
                                        </div>
                                        <button type="button" class="btn btn-danger"
                                            data-kt-goodreceive-table-select="delete_selected">Delete Selected</button>
                                    </div>
                                    <button type="button" class="btn btn-light-primary btn-sm me-3" id="btn_add_menu">
                                        <span id="svg_add_menu" class="svg-icon svg-icon-2">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                                viewBox="0 0 24 24" version="1.1">
                                                <title>Stockholm-icons / Navigation / Plus</title>
                                                <desc>Created with Sketch.</desc>
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
                                        <span id="spinner_add_menu"
                                            class="spinner-border spinner-border-sm align-middle ms-2"
                                            style="display: none;"></span>
                                        <span id="btn_text_add_menu">Create</span>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <table class="table align-middle table-row-dashed table-striped gy-2 fs-7"
                                    id="menu_table">
                                    <thead>
                                        <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                            <th class="min-w-20px pe-2">No</th>
                                            <th class="min-w-20px">Group ID</th>
                                            <th class="min-w-20px">Sub Group ID</th>
                                            <th class="min-w-70px">Name</th>
                                            <th class="min-w-70px">URL</th>
                                            <th class="min-w-100px">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                    <tfoot>
                                        <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                            <th class="min-w-20px pe-2">No</th>
                                            <th class="min-w-20px">Group ID</th>
                                            <th class="min-w-20px">Sub Group ID</th>
                                            <th class="min-w-70px">Name</th>
                                            <th class="min-w-70px">URL</th>
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
    <div id="div_add_menu">
    </div>
    <script>
        $(document).ready(function() {
            const params = new URLSearchParams(window.location.search);
            const ref_doc = params.get('ref_doc');
            if (!ref_doc) {
                font_table();
            } else if (ref_doc == 'create') {
                $.ajax({
                    url: '{{ route('data_menu.create_form') }}',
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                    },
                    success: function(response) {
                        $("#svg_add_menu").show();
                        $("#spinner_add_menu").hide();
                        $("#btn_text_add_menu").text("Create");
                        $("#kt_content").addClass('d-none');
                        $('#div_add_menu').removeClass('d-none');
                        window.history.pushState({}, '', '?ref_doc=create');
                        $('#div_add_menu').html(response);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        $("#svg_add_menu").show();
                        $("#spinner_add_menu").hide();
                        $("#btn_text_add_menu").text("Create");
                        Swal.fire(
                            'Gagal!',
                            'Terjadi kesalahan saat memuat form.',
                            'error'
                        );
                    }
                });
            } else {
                edit_menu(ref_doc)
            }
        });

        function font_table() {
            $('#menu_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('data_menu.front_table') }}',
                    type: 'POST',
                    data: function(d) {
                        d._token = $('meta[name="csrf-token"]').attr('content');
                        d.search_custom = $('#front_table_search').val();
                    },
                },
                columns: [{
                        data: 'no',
                        name: 'no',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'group_id',
                        name: 'group_id'
                    },
                    {
                        data: 'sub_group_id',
                        name: 'sub_group_id'
                    },
                    {
                        data: 'menu_name',
                        name: 'menu_name'
                    },
                    {
                        data: 'menu',
                        name: 'menu'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                order: [
                    [0, 'asc']
                ]
            });
        }
        $('#front_table_search').on('keyup', function() {
            $('#menu_table').DataTable().ajax.reload();
        });
        $("#btn_add_menu").on('click', function() {
            $("#svg_add_menu").hide();
            $("#spinner_add_menu").show();
            $("#btn_text_add_menu").text("Loading...");
            $.ajax({
                url: '{{ route('data_menu.create_form') }}',
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                },
                success: function(response) {
                    $("#svg_add_menu").show();
                    $("#spinner_add_menu").hide();
                    $("#btn_text_add_menu").text("Create");
                    $("#kt_content").addClass('d-none');
                    $('#div_add_menu').removeClass('d-none');
                    window.history.pushState({}, '', '?ref_doc=create');
                    $('#div_add_menu').html(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    $("#svg_add_menu").show();
                    $("#spinner_add_menu").hide();
                    $("#btn_text_add_menu").text("Create");
                    Swal.fire(
                        'Gagal!',
                        'Terjadi kesalahan saat memuat form.',
                        'error'
                    );
                }
            });
        });

        function edit_menu(id) {
            $.ajax({
                url: "{{ route('data_menu.edit_form') }}",
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id
                },
                success: function(res) {
                    $("#kt_content").addClass('d-none');
                    $('#div_add_menu').removeClass('d-none');
                    window.history.pushState({}, '', `?ref_doc=${id}`);
                    $('#div_add_menu').html(res);
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    Swal.fire(
                        'Gagal!',
                        'Terjadi kesalahan saat memuat form.',
                        'error'
                    );
                }
            })
        }
        function confirmDelete(id){
            Swal.fire({
                title: "Anda yakin?",
                text: "Data akan di hapus secara permanen!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, hapus!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('data_menu/delete_data_menu') }}",
                        type: 'post',
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: id
                        },
                        success: function(response) {
                            const status = response.status
                            const msg = response.message
                            Toast.fire({
                                position: 'top-end',
                                title: msg,
                                icon: status
                            });
                            $("#menu_table").DataTable().ajax.reload(null, false)
                        },
                        error: function(xhr) {
                            console.log(xhr)
                            Toast.fire({
                                position: 'top-end',
                                title: 'Something went wrong',
                                icon: 'error'
                            });
                        }
                    })
                }
            });
        }
    </script>
@endsection
