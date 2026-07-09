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
                            Data Menu
                        </div>
                        <div class="card-toolbar">
                            <div class="d-flex justify-content-end" data-kt-goodreceive-table-toolbar="base">
                            </div>
                            <button type="button" class="btn btn-light-success btn-sm me-3" id="btn_back_menu">
                                <span class="svg-icon svg-icon-2" id="btn_icon_menu">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 9.75 14.25 12m0 0 2.25 2.25M14.25 12l2.25-2.25M14.25 12 12 14.25m-2.58 4.92-6.374-6.375a1.125 1.125 0 0 1 0-1.59L9.42 4.83c.21-.211.497-.33.795-.33H19.5a2.25 2.25 0 0 1 2.25 2.25v10.5a2.25 2.25 0 0 1-2.25 2.25h-9.284c-.298 0-.585-.119-.795-.33Z" />
                                    </svg>
                                </span>
                                <span class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;"
                                    id="spinner_btn_back"></span>
                                <span id="btn_txt_back_menu">Back</span>
                            </button>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="row">
                            <div class="col-lg-6">
                                <label for="role_code" class="form-label">Sequence</label>
                                <select class="form-select" id="sequence_id" name="sequence_id">
                                    <option value="">Select Sequence</option>
                                    <option value="1">1
                                    </option>
                                    <option value="2">2
                                    </option>
                                    <option value="3">3
                                    </option>
                                    <option value="4">4
                                    </option>
                                    <option value="5">5
                                    </option>
                                    <option value="6">6
                                    </option>
                                </select>
                                <small class="text-danger error-text" id="error-sequence_id"></small>
                            </div>
                            <div class="col-lg-6">
                                <label for="role_name" class="form-label">Level</label>
                                <select class="form-select" id="level_id" name="level_id">
                                    <option value="">Select Level</option>
                                    <option value="1">1
                                    </option>
                                    <option value="2">2
                                    </option>
                                    <option value="3">3
                                    </option>
                                    <option value="4">4
                                    </option>
                                </select>
                                <small class="text-danger error-text" id="error-level_id"></small>
                            </div>
                            <div class="col-lg-6">
                                <label for="role_name" class="form-label">Group</label>
                                <select class="form-select" id="group_id" name="group_id">
                                </select>
                                <small class="text-danger error-text" id="error-group_id"></small>
                            </div>
                            <div class="col-lg-6">
                                <label for="role_name" class="form-label">Sub Group</label>
                                <select class="form-select" id="sub_group_id" name="sub_group_id">
                                </select>
                                <small class="text-danger error-text" id="error-sub_group_id"></small>
                            </div>
                            <div class="col-lg-6">
                                <label for="role_name" class="form-label">Menu name</label>
                                <input type="text" class="form-control" id="menu_name" name="menu_name">
                                <small class="text-danger error-text" id="error-menu_name"></small>
                            </div>
                            <div class="col-lg-6">
                                <label for="role_name" class="form-label">URL</label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon3">nux.summitadyawinsa.co.id/</span>
                                    <input type="text" class="form-control" id="menu_url" name="menu_url">
                                </div>
                                <small class="text-danger error-text" id="error-menu_url"></small>
                            </div>
                            <div class="col-lg-12">
                                <label for="role_name" class="form-label">Icon</label>
                                <input type="text" class="form-control" id="icon" name="icon">
                                <small class="text-danger error-text" id="error-icon"></small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-light-primary btn-sm" id="btn_save">
                                <span id="svg_save_icon" class="svg-icon svg-icon-2">
                                    <i class="fa fa-save"></i>
                                </span>
                                <span id="spinner_save"
                                    class="spinner-border spinner-border-sm svg-icon svg-icon-2 d-none"></span>
                                <span id="btn_text_save">Save</span>
                            </button>
                        </div>
                    </div> <!-- .card-body -->
                </div> <!-- .card -->
            </div> <!-- .container -->
        </div> <!-- .d-flex -->
    </div>
</div>
<script>
    $("#btn_back_menu").on('click', function() {
        $("#btn_icon_menu").hide();
        $("#spinner_btn_back").show();
        $("#btn_txt_back_menu").text("Loading...");
        window.history.pushState({}, '', '/data_menu');
        setTimeout(function() {
            $('#div_add_menu').addClass('d-none');
            $("#kt_content").removeClass('d-none');
            $('#menu_table').DataTable().ajax.reload();
        }, 200);
    });
    $('#group_id').select2({
        placeholder: 'Pilih Group',
        minimumInputLength: 0,
        allowClear: true,
        ajax: {
            url: "{{ route('data_menu.get_groups') }}",
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
    $('#sub_group_id').select2({
        placeholder: 'Pilih Sub Group',
        minimumInputLength: 0,
        allowClear: true,
        ajax: {
            url: "{{ route('data_menu.get_sub_groups') }}",
            method: "POST",
            dataType: 'json',
            delay: 300,
            data: function(params) {
                return {
                    _token: "{{ csrf_token() }}",
                    group_id: $("#group_id").val(),
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
    $('#menu_url').on('input', function() {
        let value = $(this).val();
        value = value.toLowerCase()
            .replace(/\s+/g, '_')
            .replace(/[^a-z0-9_]/g, '');
        $(this).val(value);
    });
    $("#btn_save").on('click', function() {
        $("#svg_save_icon").addClass('d-none')
        $("#spinner_save").removeClass('d-none')
        $('#btn_text_save').text('Loading...')
        $.ajax({
            url: "{{ route('data_menu.save_menu') }}",
            type: 'post',
            data: {
                _token: '{{ csrf_token() }}',
                sequence_id: $("#sequence_id").val(),
                level_id: $("#level_id").val(),
                group_id: $("#group_id").val(),
                sub_group_id: $("#sub_group_id").val(),
                menu_name: $("#menu_name").val(),
                menu_url: $("#menu_url").val(),
                icon: $("#icon").val()
            },
            success: function(res) {
                if (res.status == true) {
                    Swal.fire({
                        title: 'Success!',
                        text: res.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: res.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
                $("#svg_save_icon").removeClass('d-none')
                $("#spinner_save").addClass('d-none')
                $('#btn_text_save').text('Save')
            },
            error: function(xhr) {
                console.log(xhr.responseJSON.message)
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
                $("#svg_save_icon").removeClass('d-none')
                $("#spinner_save").addClass('d-none')
                $('#btn_text_save').text('Save')
            }
        })
    })
</script>
