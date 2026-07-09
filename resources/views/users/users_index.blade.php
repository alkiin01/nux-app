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
                                            placeholder="Search username, name, email" />
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
                                    <button type="button" class="btn btn-light-primary btn-sm me-3" id="btn_add_users"
                                        onclick="add_user()">
                                        <span id="svg_add_users" class="svg-icon svg-icon-2">
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
                                        <span id="spinner_add_users"
                                            class="spinner-border spinner-border-sm align-middle ms-2"
                                            style="display: none;"></span>
                                        <span id="btn_text_add_users">Create</span>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <table class="table align-middle table-row-dashed table-striped gy-2 fs-7"
                                    id="user_table">
                                    <thead>
                                        <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                            <th class="min-w-20px pe-2">No</th>
                                            <th class="min-w-20px">Username</th>
                                            <th class="min-w-100px">Name</th>
                                            <th class="min-w-100px">Email</th>
                                            <th class="min-w-100px">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                    <tfoot>
                                        <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                            <th class="min-w-20px pe-2">No</th>
                                            <th class="min-w-20px">Username</th>
                                            <th class="min-w-100px">Name</th>
                                            <th class="min-w-100px">Email</th>
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
        <div id="div_add_user">
        </div>
        <div class="div_show_user"></div>
    </div>

    <input type="text" hidden id="temp_id">
    <input type="text" hidden id="l_order_line">
    <input type="text" hidden id="l_order_rel">
    <input type="text" hidden id="pack_line">
    <script>
        $(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            const ref_id = urlParams.get('ref_id');
            if (!ref_id) {
                $("#div_data_table").show();
                $("#div_add_user").hide();
                window.history.replaceState({}, '', '<?php echo env('BASE_URL'); ?>/data_users');
                $('#user_table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('data_users.front_table') }}",
                        type: 'GET',
                        data: function(d) {
                            d._token = $('meta[name="csrf-token"]').attr('content');
                            d.search_custom = $('#front_table_search').val();
                        }
                    },
                    columns: [{
                            data: null,
                            name: 'no',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row, meta) {
                                return meta.row + 1 + meta.settings._iDisplayStart;
                            }
                        },
                        {
                            data: 'username',
                            name: 'username'
                        },
                        {
                            data: 'full_name',
                            name: 'full_name'
                        },
                        {
                            data: 'email',
                            name: 'email'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            className: 'text-center',
                            render: function(data, type, row, meta) {
                                return data;
                            }
                        }
                    ]
                });
                return;
            }else if(ref_id === 'new'){
                add_user();
            }
             else {
                edit_user(ref_id);
            }
        });
        $('#front_table_search').on('keyup', function() {
            $('#user_table').DataTable().ajax.reload();
        });

        function edit_user(encryptedId) {
            const token = $("[name='_token']").val();
            $.ajax({
                url: '{{ route('data_users.show') }}',
                type: 'POST',
                data: {
                    _token: token,
                    id: encryptedId
                },
                success: function(response) {
                    if (response.success == false) {
                        Swal.fire('Oops!', response.message, 'error');
                    } else {
                        window.history.pushState('', '',
                            `{{ url('data_users') }}?ref_id=${encryptedId}`);
                        document.getElementById('div_data_table').style.display = 'none';
                        document.getElementById('div_add_user').style.display = 'none';
                        $('.div_show_user').html(response);
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Gagal mengambil data user.', 'error');
                }
            });
        }
        function add_user() {
            const button = document.getElementById('btn_add_users');
            const svg = document.getElementById('svg_add_users');
            const spinner = document.getElementById('spinner_add_users');
            const btn_text = document.getElementById('btn_text_add_users');

            svg.style.display = 'none';
            spinner.style.display = 'inline-block';
            btn_text.textContent = 'Please wait...';
            button.disabled = true;
            setTimeout(function() {
                const token = $("[name='_token']").val();
                $.ajax({
                    url: '{{ route('data_users.create_form') }}',
                    type: 'POST',
                    data: {
                        _token: token
                    },
                    success: function(response) {
                        if (response.success == false) {
                            Swal.fire('Oops!', response.message, 'error');
                        } else {
                            document.getElementById('div_data_table').style.display = 'none';
                            document.getElementById('div_add_user').style.display = 'block';
                            window.history.pushState('', '', `{{ url('data_users') }}?ref_id=new`);
                            $('#div_add_user').html(response);
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal mengambil data user.', 'error');
                    }
                });
            }, 200);
        }

        function deleteUser(encryptedId) {
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Data ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('data_users.delete_user') }}',
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            user_id: encryptedId
                        },
                        success: function(response) {
                            Swal.fire(
                                'Dihapus!',
                                'Data pengguna telah dihapus.',
                                'success'
                            );
                            $('#user_table').DataTable().ajax.reload();
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                            Swal.fire(
                                'Gagal!',
                                'Terjadi kesalahan saat menghapus data.',
                                'error'
                            );
                        }
                    });
                }
            });
        }
    </script>
@endsection
