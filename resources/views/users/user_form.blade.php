<div class="tab-content">
    <div id="kt_activity_home" class="card-body p-0 tab-pane fade show active" role="tabpanel"
        aria-labelledby="kt_activity_home_tab">
        <div class="post d-flex flex-column-fluid mb-5" id="kt_post">
            <div class="container-xxl">
                <!-- Tambahkan konten di sini -->
            </div>
        </div>

        <div class="d-flex flex-column-fluid mt-lg-5 mt-sm-5">
            <div class="container-xxl">
                <div class="card col-xxl-12 card-sticky">
                    <div class="card-header border-1 pt-6 pb-6 mb-5">
                        <div class="card-title">
                            Data User
                        </div>
                        <div class="card-toolbar">
                            <div class="d-flex justify-content-end" data-kt-goodreceive-table-toolbar="base">
                            </div>
                            <button type="button" class="btn btn-light-success btn-sm me-3" id="btn_back_user"
                                onclick="back_user()">
                                <span class="svg-icon svg-icon-2">
                                    <!-- SVG -->
                                </span>
                                <span class="spinner-border spinner-border-sm align-middle ms-2"
                                    style="display: none;"></span>
                                <span id="btn_txt_back_user">Back</span>
                            </button>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <form id="form_edit_user" onsubmit="handleSubmit(event)" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-lg-6">
                                    <label for="role_code" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username">
                                    <small class="text-danger error-text" id="error-username"></small>
                                </div>
                                <div class="col-lg-6">
                                    <label for="role_name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name">
                                    <small class="text-danger error-text" id="error-full_name"></small>
                                </div>
                                <div class="col-lg-6">
                                    <label for="role_name" class="form-label">Call Name</label>
                                    <input type="text" class="form-control" id="call_name" name="call_name">
                                    <small class="text-danger error-text" id="error-call_name"></small>
                                </div>
                                <div class="col-lg-6">
                                    <label for="role_name" class="form-label">Email</label>
                                    <input type="text" class="form-control" id="email" name="email">
                                    <small class="text-danger error-text" id="error-email"></small>
                                </div>
                                <div class="col-lg-6">
                                    <label for="role_name" class="form-label">Gender</label>
                                    <select class="form-select" id="gender" name="gender">
                                        <option value="">Select Gender</option>
                                        <option value="1">Male
                                        </option>
                                        <option value="2">Female
                                        </option>
                                    </select>
                                </div>
                                <div class="col-lg-6">
                                    <label for="role_name" class="form-label">Phone Number</label>
                                    <input type="text" class="form-control" id="phone_number" name="phone_number">
                                </div>
                            </div>

                            <!-- Avatar Section -->
                            <div class="row mt-5">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="avatar_input" class="form-label">Avatar</label>
                                        <div class="input-group">
                                            <input type="file" class="form-control" id="avatar_input"
                                                name="avatar" accept="image/*" onchange="previewAvatar(this)">
                                            <span class="input-group-text text-muted small">.jpg, .png, .gif (Max
                                                5MB)</span>
                                        </div>
                                        <small class="text-danger error-text" id="error-avatar"></small>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card border border-2 border-primary p-3">
                                                <p class="text-muted small fw-bold mb-2">New Avatar Preview</p>
                                                <img id="avatar_preview" src="" alt="Avatar Preview"
                                                    class="rounded img-fluid d-none"
                                                    style="height: 150px; object-fit: cover; width: 100%;">
                                                <div id="avatar_preview_placeholder"
                                                    class="d-flex align-items-center justify-content-center rounded"
                                                    style="height: 150px; background-color: #f5f5f5;">
                                                    <span class="text-muted">Preview</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Signature Section -->
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="signature_input" class="form-label">Signature</label>
                                        <div class="input-group">
                                            <input type="file" class="form-control" id="signature_input"
                                                name="signature" accept="image/*" onchange="previewSignature(this)">
                                            <span class="input-group-text text-muted small">.jpg, .png, .gif (Max
                                                5MB)</span>
                                        </div>
                                        <small class="text-danger error-text" id="error-signature"></small>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card border border-2 border-primary p-3">
                                                <p class="text-muted small fw-bold mb-2">New Signature Preview</p>
                                                <img id="signature_preview" src="" alt="Signature Preview"
                                                    class="rounded img-fluid d-none"
                                                    style="height: 150px; object-fit: cover; width: 100%;">
                                                <div id="signature_preview_placeholder"
                                                    class="d-flex align-items-center justify-content-center rounded"
                                                    style="height: 150px; background-color: #f5f5f5;">
                                                    <span class="text-muted">Preview</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-light-primary btn-sm" id="btn_save_role">
                                    <span id="svg_save_icon" class="svg-icon svg-icon-2">
                                        <i class="fa fa-save"></i>
                                    </span>
                                    <span id="spinner_save"
                                        class="spinner-border spinner-border-sm svg-icon svg-icon-2"
                                        style="display: none;"></span>
                                    <span id="btn_text_save">Save</span>
                                </button>
                            </div>
                        </form>
                    </div> <!-- .card-body -->
                </div> <!-- .card -->
            </div> <!-- .container -->
        </div> <!-- .d-flex -->
    </div>
</div>

<div class="tab-content">
    <div id="kt_activity_home" class="card-body p-0 tab-pane fade show active" role="tabpanel"
        aria-labelledby="kt_activity_home_tab">
        <div class="post d-flex flex-column-fluid mb-5" id="kt_post">
            <div class="container-xxl">
                <!-- Tambahkan konten di sini -->
            </div>
        </div>

        <div class="d-flex flex-column-fluid mt-lg-5 mt-sm-5">
            <div class="container-xxl">
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
                                    id="menu_table_search"
                                    class="form-control form-control-solid w-250px ps-15 text-sm form-control-sm"
                                    placeholder="Search..." />
                            </div>
                        </div>
                        <div class="card-toolbar">
                            <div class="d-flex justify-content-end" data-kt-goodreceive-table-toolbar="base">
                            </div>
                            <button type="button" class="btn btn-light-primary btn-sm me-3 d-none" data-bs-toggle="modal"
                                data-bs-target="#staticBackdrop" id="btn_add_menu">
                                <span id="svg_add_users" class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                        width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
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
                                <span class="spinner-border spinner-border-sm align-middle ms-2"
                                    style="display: none;"></span>
                                <span id="btn_txt_add_menu">Add Menu</span>
                            </button>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <table class="table align-middle table-row-dashed table-striped gy-2 fs-7"
                            id="user_menu_table">
                            <thead>
                                <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                    <th class="min-w-20px pe-2">No</th>
                                    <th class="min-w-100px">Name</th>
                                    <th class="min-w-100px">URL</th>
                                    <th class="min-w-100px">Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>
                                <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                    <th class="min-w-20px pe-2">No</th>
                                    <th class="min-w-100px">Name</th>
                                    <th class="min-w-100px">Menu</th>
                                    <th class="min-w-100px">Action</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div> <!-- .card-body -->
                </div> <!-- .card -->
            </div> <!-- .container -->
        </div> <!-- .d-flex -->
    </div>
</div>
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="card-title fs-5" id="staticBackdropLabel">Add Menu</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="role_name" class="form-label">Menu</label>
                    <select class="form-select" id="menu_id" name="menu_id">
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary btn-sm me-3"
                    id="btn_submit_add_menu">Submit</button>
            </div>
        </div>
    </div>
</div>
<script>
    // Avatar Preview Function
    function previewAvatar(input) {
        const preview = document.getElementById('avatar_preview');
        const placeholder = document.getElementById('avatar_preview_placeholder');

        if (input.files && input.files[0]) {
            const file = input.files[0];

            // Validate file size (5MB)
            const maxSize = 5 * 1024 * 1024;
            if (file.size > maxSize) {
                alert('File size exceeds 5MB. Please select a smaller file.');
                input.value = '';
                preview.classList.add('d-none');
                placeholder.classList.remove('d-none');
                return;
            }

            // Validate file type
            if (!file.type.match('image.*')) {
                alert('Please select a valid image file.');
                input.value = '';
                preview.classList.add('d-none');
                placeholder.classList.remove('d-none');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
                placeholder.classList.add('d-none');
            };
            reader.readAsDataURL(file);
        }
    }

    // Signature Preview Function
    function previewSignature(input) {
        const preview = document.getElementById('signature_preview');
        const placeholder = document.getElementById('signature_preview_placeholder');

        if (input.files && input.files[0]) {
            const file = input.files[0];

            // Validate file size (5MB)
            const maxSize = 5 * 1024 * 1024;
            if (file.size > maxSize) {
                alert('File size exceeds 5MB. Please select a smaller file.');
                input.value = '';
                preview.classList.add('d-none');
                placeholder.classList.remove('d-none');
                return;
            }

            // Validate file type
            if (!file.type.match('image.*')) {
                alert('Please select a valid image file.');
                input.value = '';
                preview.classList.add('d-none');
                placeholder.classList.remove('d-none');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
                placeholder.classList.add('d-none');
            };
            reader.readAsDataURL(file);
        }
    }
    $('#menu_id').select2({
        placeholder: 'Pilih Menu',
        minimumInputLength: 0,
        allowClear: true,
        dropdownParent: $('#staticBackdrop'),
        ajax: {
            url: "{{ route('data_users.get_menu') }}",
            method: "POST",
            dataType: 'json',
            delay: 300,
            data: function(params) {
                return {
                    _token: "{{ csrf_token() }}",
                    search: params.term || '',
                    page: params.page || 1
                };
            },
            processResults: function(data, params) {
                params.page = params.page || 1;

                return {
                    results: data.results,
                    pagination: {
                        more: data.pagination.more
                    }
                };
            },
            cache: true
        }
    });
    // Handle Form Submission
    function handleSubmit(e) {
        e.preventDefault();
        const btn = document.getElementById('btn_save_role');
        const svg = document.getElementById('svg_save_icon');
        const spinner = document.getElementById('spinner_save');
        const btnText = document.getElementById('btn_text_save');

        svg.style.display = 'none';
        spinner.style.display = 'inline-block';
        btnText.textContent = 'Saving...';
        btn.disabled = true;

        const token = $("[name=_token]").val();
        const formData = new FormData(document.getElementById('form_edit_user'));
        $.ajax({
            type: 'POST',
            url: '{{ route('data_users.submit_store') }}',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': token
            },
            success: function(response) {

                svg.style.display = 'inline-block';
                spinner.style.display = 'none';
                btnText.textContent = 'Save';
                btn.disabled = false;

                if (response.success == true) {
                    $('.error-text').text('');
                    $('.form-control').removeClass('is-invalid');
                    $("#btn_add_menu").removeClass('d-none');
                    window.history.pushState('', '', `{{ url('data_users') }}?ref_id=${response.encrypted_id}`);
                    $("#btn_save_role").addClass('d-none');
                    user_menu_table();
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    })
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: response.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr) {
                svg.style.display = 'inline-block';
                spinner.style.display = 'none';
                btnText.textContent = 'Save';
                btn.disabled = false;
                if (xhr.status === 422) {

                    let errors = xhr.responseJSON.errors;

                    $.each(errors, function(key, value) {

                        $('#error-' + key).text(value[0]);

                        $('[name="' + key + '"]').addClass('is-invalid');

                    });

                } else {

                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred while saving data.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });

                }
            }
        });
    }
    $("#btn_submit_add_menu").on('click', function() {
        const menuId = $('#menu_id').val();
        const userId = window.location.search.split('ref_id=')[1];
        if (!menuId) {
            Swal.fire({
                title: 'Error!',
                text: 'Please select a menu.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return;
        }
        $.ajax({
            type: 'POST',
            url: '{{ route('data_users.submit_add_menu') }}',
            data: {
                _token: "{{ csrf_token() }}",
                user_id: userId,
                menu_id: menuId
            },
            success: function(response) {
                if (response.success == true) {
                    $('#staticBackdrop').modal('hide');
                    $('#menu_id').val(null).trigger('change');
                    $('#user_menu_table').DataTable().ajax.reload();
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: response.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    title: 'Error!',
                    text: 'An error occurred while adding the menu.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });

    function user_menu_table() {
        const userId = window.location.search.split('ref_id=')[1];
        if ($.fn.DataTable.isDataTable('#user_menu_table')) {
            $('#user_menu_table').DataTable().destroy();
        }
        $('#user_menu_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('data_users.user_menu_table') }}",
                type: 'POST',
                data: function(d) {
                    d._token = $('meta[name="csrf-token"]').attr('content');
                    d.user_id = userId;
                    d.search_custom = $('#menu_table_search').val();
                }
            },
            pageLength: 10,
            columns: [{
                    data: 'id',
                    name: 'id',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row, meta) {
                        return meta.row + 1 + meta.settings._iDisplayStart;
                    }
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'url',
                    name: 'url'
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
    }
    // $(document).ready(function() {
    //     user_menu_table();
    //     $('#menu_table_search').on('keyup', function() {
    //         $('#user_menu_table').DataTable().ajax.reload();
    //     });
    // });
    function deleteMenu(encryptedId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('data_users.delete_menu') }}',
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: encryptedId
                    },
                    success: function(response) {
                        if (response.success == true) {
                            $('#user_menu_table').DataTable().ajax.reload();
                            Swal.fire(
                                'Deleted!',
                                response.message,
                                'success'
                            );
                        } else {
                            Swal.fire(
                                'Error!',
                                response.message,
                                'error'
                            );
                        }
                    },
                    error: function() {
                        Swal.fire(
                            'Error!',
                            'An error occurred while deleting the menu.',
                            'error'
                        );
                    }
                });
            }
        });
    }
    // Back to users list
    function back_user() {
        window.location.href = '{{ url('data_users') }}';
    }
</script>
