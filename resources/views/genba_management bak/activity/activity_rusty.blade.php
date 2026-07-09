<style>
    .card-header.sticky-top {
        position: sticky;
        top: 100px;
        z-index: 10;
        background-color: #F1FAFF;
    }
</style>

<div class="col-xxl-12">
    <div id="form_loader" style="text-align: center; display: none;">
        <div class="lds-roller mt-10 mb-10" id="lds-roller-form">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>

    <div id="form_label">
        <div class="card mb-5">
            <div class="card-header">
                <div class="card-title">Process Audit</div>
                <div class="card-toolbar">
                    <button class="btn btn-success btn-sm" id="btn_back_home" onclick="backHome()">
                        <span id="svg_back_home" class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <path d="M3.95709826,8.41510662 L11.47855,3.81866389 C11.7986624,3.62303967 12.2013376,3.62303967 12.52145,3.81866389 L20.0429,8.41510557 C20.6374094,8.77841684 21,9.42493654 21,10.1216692 L21,19.0000642 C21,20.1046337 20.1045695,21.0000642 19,21.0000642 L4.99998155,21.0000673 C3.89541205,21.0000673 2.99998155,20.1046368 2.99998155,19.0000673 L2.99999828,10.1216672 C2.99999935,9.42493561 3.36258984,8.77841732 3.95709826,8.41510662 Z M10,13 C9.44771525,13 9,13.4477153 9,14 L9,17 C9,17.5522847 9.44771525,18 10,18 L14,18 C14.5522847,18 15,17.5522847 15,17 L15,14 C15,13.4477153 14.5522847,13 14,13 L10,13 Z" fill="#000000" />
                                </g>
                            </svg>
                        </span>
                        <span id="spinner_back_home" class="spinner-border spinner-border-sm" style="display: none;"></span>
                        <span id="btn_text_back_home">Back</span>
                    </button>
                </div>
            </div>
        </div>

        @foreach ($scopes as $scope_name => $items)
        @php
        $scope_id = $items[0]['scope_id'];
        $loop_index = $loop->index;
        @endphp
        <div class="row g-5 g-xl-8">
            <div class="col-12 mb-5">
                <div class="card card-custom" id="kt_card_{{ $loop_index }}">
                    <div class="card-header">
                        <div class="card-title">{{ $scope_name }}</div>
                        <div class="card-toolbar">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="popover" data-bs-trigger="click" title="Information" data-bs-html="true" data-bs-content="<span><i class='fa fa-circle text-primary'></i> Jika telah sesuai.</span><br><i class='fa fa-exclamation-triangle text-primary'></i> Jika perlu improvement.</span><br><i class='fa fa-times text-primary'></i> Jika tidak sesuai.</span>">
                                <i class="fa fa-info-circle"></i>
                            </button>
                        </div>
                    </div>

                    <form id="form_genba_upload_{{ $loop_index }}" class="form_genba_upload" enctype="multipart/form-data" method="post">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <input type="hidden" name="scope_id" value="{{ $scope_id }}">
                        <input type="hidden" name="activity_id" id="id_activity" value="{{ $id_activity }}">

                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Photo Findings</label>
                                <div>
                                    <button type="button" class="btn btn-sm btn-primary me-2 btn-upload-file" data-index="{{ $loop_index }}">
                                        <i class="fa fa-upload"></i> Upload File
                                    </button>
                                    <button type="button" class="btn btn-sm btn-warning btn-take-photo" data-index="{{ $loop_index }}">
                                        <i class="fa fa-camera"></i> Ambil Foto
                                    </button>
                                </div>
                                <input type="file" id="file_input_upload_{{ $loop_index }}" accept="image/*" multiple style="display: none;">
                                <input type="file" id="file_input_camera_{{ $loop_index }}" accept="image/*" capture="environment" style="display: none;">
                                <div class="mt-3 border rounded p-2 d-flex flex-wrap gap-2" id="preview_container_{{ $loop_index }}" style="min-height: 100px;">
                                    <small class="text-muted align-self-center">Pratinjau foto akan muncul di sini...</small>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="findings_{{ $loop_index }}" class="form-label">Comments</label>
                                <textarea class="form-control" name="findings" id="findings_{{ $loop_index }}" cols="20" rows="5" required></textarea>
                            </div>
                            <div class="row mt-10">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="assign_to_{{ $loop_index }}" class="form-label">Assign To</label>
                                        <select class="form-select assign_to" id="assign_to_{{ $loop_index }}" name="assign_to" required>
                                            <option value="">Assign To</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="assign_to_dept_{{ $loop_index }}" class="form-label">Assign To Dept</label>
                                        <select class="form-select assign_to_dept" id="assign_to_dept_{{ $loop_index }}" name="assign_to_dept" required>
                                            <option value="">Pilih Dept</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="detail_area_rusty_{{ $loop_index }}" class="form-label">Detail Area</label>
                                        <input type="text" class="form-control" name="detail_area_rusty" id="detail_area_rusty_{{ $loop_index }}" placeholder="Detail area temuan">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <button type="submit" class="btn btn-primary btn-sm btn_submit_form">
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <polygon points="0 0 24 0 24 24 0 24" />
                                            <path d="M6.26193932,17.6476484 C5.90425297,18.0684559 5.27315905,18.1196257 4.85235158,17.7619393 C4.43154411,17.404253 4.38037434,16.773159 4.73806068,16.3523516 L13.2380607,6.35235158 C13.6013618,5.92493855 14.2451015,5.87991302 14.6643638,6.25259068 L19.1643638,10.2525907 C19.5771466,10.6195087 19.6143273,11.2515811 19.2474093,11.6643638 C18.8804913,12.0771466 18.2484189,12.1143273 17.8356362,11.7474093 L14.0997854,8.42665306 L6.26193932,17.6476484 Z" fill="#000000" fill-rule="nonzero" transform="translate(11.999995, 12.000002) rotate(-180.000000) translate(-11.999995, -12.000002) " />
                                        </g>
                                    </svg>
                                </span>
                                <span class="spinner-border spinner-border-sm" style="display: none;"></span>
                                <span class="btn_text_submit_form">Save</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="card col-xxl-12 card-sticky">
            <div class="card-header border-0 pb-0">
                <div class="card-title">
                    <div class="d-flex align-items-center position-relative my-1">
                        <span class="svg-icon svg-icon-1 position-absolute ms-6">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                            </svg>
                        </span>
                        <input type="text" data-kt-goodreceive-table-filter="search" id="front_table_search" class="form-control form-control-solid w-[50%] ps-15 text-sm form-control-sm" placeholder="Search" />

                    </div>
                </div>
                <div class="card-toolbar">
                    <button type="submit" class="btn btn-success btn-sm btn_finish_form ms-3" onclick="finishGenba()">
                        <span class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24" />
                                    <path d="M6.26193932,17.6476484 C5.90425297,18.0684559 5.27315905,18.1196257 4.85235158,17.7619393 C4.43154411,17.404253 4.38037434,16.773159 4.73806068,16.3523516 L13.2380607,6.35235158 C13.6013618,5.92493855 14.2451015,5.87991302 14.6643638,6.25259068 L19.1643638,10.2525907 C19.5771466,10.6195087 19.6143273,11.2515811 19.2474093,11.6643638 C18.8804913,12.0771466 18.2484189,12.1143273 17.8356362,11.7474093 L14.0997854,8.42665306 L6.26193932,17.6476484 Z"
                                        fill="#000000" fill-rule="nonzero"
                                        transform="translate(11.999995, 12.000002) rotate(-180.000000) translate(-11.999995, -12.000002)" />
                                </g>
                            </svg>
                        </span>
                        <span class="spinner-border spinner-border-sm" style="display: none;"></span>
                        <span class="btn_text_finish_form">Finish</span>
                    </button>
                </div>
            </div>
            <div class="card-body pt-0">
                <table class="table align-middle table-row-dashed table-striped fs-7 gy-3" id="kt_rusty_table">
                    <thead>
                        <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                            <th class="min-w-20px pe-2">No</th>
                            <th class="min-w-100px">Findings Pict.</th>
                            <th class="min-w-100px">Findings</th>
                            <th class="min-w-100px">Area Checked</th>
                            <th class="min-w-100px">PIC</th>
                            <th class="min-w-100px">Due Date</th>
                            <th class="min-w-100px">Status</th>
                            <th class="min-w-100px">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        @endforeach
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>

<script>
    var fileStore = {};

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl, {
            trigger: 'focus'
        });
    });

    function toggleSubmitSpinner(button, show) {
        const svgIcon = $(button).find('.svg-icon');
        const spinner = $(button).find('.spinner-border');
        const btnText = $(button).find('.btn_text_submit_form');
        const btnFinish = $(button).find('.btn_text_finish_form');

        if (show) {
            $(button).prop('disabled', true);
            svgIcon.hide();
            spinner.show();
            btnText.text('Processing...');
            btnFinish.text('Processing...');
        } else {
            $(button).prop('disabled', false);
            svgIcon.show();
            spinner.hide();
            btnText.text('Save');
            btnFinish.text('Finish');
        }
    }

    function handleFiles(files, index) {
        if (!fileStore[index]) {
            fileStore[index] = [];
        }
        const previewContainer = $('#preview_container_' + index);
        previewContainer.find('small').remove();

        for (const file of files) {
            if (fileStore[index].some(f => f.name === file.name && f.size === file.size)) continue;

            fileStore[index].push(file);

            const reader = new FileReader();
            reader.onload = function(e) {
                const previewHtml = `
                    <div class="position-relative">
                        <img src="${e.target.result}" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 p-0 remove-preview-btn"
                                style="width: 20px; height: 20px; line-height: 1;"
                                data-index="${index}" data-file-name="${file.name}" data-file-size="${file.size}">
                            &times;
                        </button>
                    </div>`;
                previewContainer.append(previewHtml);
            };
            reader.readAsDataURL(file);
        }
    }

    $(document).ready(function() {
        $('.assign_to_dept').select2({
            ajax: {
                type: 'POST',
                url: "{{ route('genba.get_section') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: $.map(data.items, function(item) {
                            return {
                                id: item.id,
                                text: item.name
                            };
                        }),
                        pagination: {
                            more: data.pagination.more
                        }
                    };
                },
                cache: true
            },
            placeholder: 'Pilih Departemen',
            allowClear: true
        });

        $('.assign_to').select2({
            ajax: {
                type: 'POST',
                url: "{{ route('genba.get_user_data') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: $.map(data.items, function(item) {
                            return {
                                id: item.id,
                                text: item.name
                            };
                        }),
                        pagination: {
                            more: data.pagination.more
                        }
                    };
                },
                cache: true
            },
            placeholder: 'Pilih PIC',
            allowClear: true
        });

        if ($.fn.DataTable.isDataTable('#kt_rusty_table')) {
            $('#kt_rusty_table').DataTable().clear().destroy();
        }


        var table = $('#kt_rusty_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('genba.get_data_rusty') }}",
                type: "POST",
                data: function(d) {
                    d.id_activity = "{{ $id_activity ?? '' }}",
                        d._token = $('meta[name="csrf-token"]').attr('content')
                }
            },
            columns: [{
                data: 'no'
            }, {
                data: 'Path',

                render: function(data, type, row) {
                    if (!data || data.trim() === '') {
                        return '-';
                    }

                    const images = data.split(',');

                    if (!Array.isArray(images) || images.length === 0) {
                        return '-';
                    }

                    const galleryId = `gallery-${row.id || row.no}`;
                    let htmlOutput = '';

                    const firstImageUrl = `{{ asset('storage/app/public') }}/${images[0]}`;
                    htmlOutput += `
                        <a href="${firstImageUrl}" 
                           data-fancybox="${galleryId}" 
                           data-caption="Gambar 1 dari ${images.length} - ${row.findings || ''}" 
                           class="btn btn-sm btn-primary">
                           <i class="fa fa-images me-2"></i>Lihat (${images.length})
                        </a>
                    `;

                    for (let i = 1; i < images.length; i++) {
                        const imageUrl = `{{ asset('storage/app/public') }}/${images[i]}`;
                        htmlOutput += `
                            <a href="${imageUrl}" 
                               data-fancybox="${galleryId}" 
                               data-caption="Gambar ${i + 1} dari ${images.length} - ${row.findings || ''}" 
                               style="display:none;">
                            </a>
                        `;
                    }

                    return htmlOutput;
                }
            }, {
                data: 'findings'
            }, {
                data: 'asign_to_dept'
            }, {
                data: 'asign_to_name'
            }, {
                data: 'due_date'
            }, {
                data: 'verification_result'
            }, {
                data: 'action'
            }],
            drawCallback: function() {
                Fancybox.bind('[data-fancybox]', {});
            }
        });

        $(document).on('click', '.btn-upload-file', function() {
            const index = $(this).data('index');
            $('#file_input_upload_' + index).click();
        });

        $(document).on('click', '.btn-take-photo', function() {
            const index = $(this).data('index');
            $('#file_input_camera_' + index).click();
        });

        $(document).on('change', 'input[type="file"][id^="file_input_"]', function() {
            const index = this.id.split('_').pop();
            if (this.files.length > 0) {
                handleFiles(this.files, index);
                $(this).val('');
            }
        });

        $(document).on('click', '.remove-preview-btn', function() {
            const index = $(this).data('index');
            const fileName = $(this).data('file-name');
            const fileSize = $(this).data('file-size');

            fileStore[index] = fileStore[index].filter(file => !(file.name === fileName && file.size === fileSize));
            $(this).parent().remove();

            if ($('#preview_container_' + index).children().length === 0) {
                $('#preview_container_' + index).html('<small class="text-muted align-self-center">Pratinjau foto akan muncul di sini...</small>');
            }
        });



        $('.form_genba_upload').on('submit', function(e) {
            e.preventDefault();

            var currentForm = this;
            var submitButton = $(currentForm).find('.btn_submit_form');
            const index = currentForm.id.split('_').pop();

            // if ((!fileStore[index] || fileStore[index].length === 0) || !$(currentForm).find('select[name="assign_to"]').val() || !$(currentForm).find('select[name="assign_to_dept"]').val()) {
            //     Swal.fire({
            //         text: "Mohon lengkapi semua field yang wajib diisi (Photo Findings, Assign To, Assign To Dept)!",
            //         icon: "error",
            //         buttonsStyling: false,
            //         confirmButtonText: "Tutup",
            //         customClass: {
            //             confirmButton: "btn btn-primary"
            //         }
            //     });
            //     return;
            // }

            toggleSubmitSpinner(submitButton, true);
            var formData = new FormData(currentForm);

            if (fileStore[index] && fileStore[index].length > 0) {
                fileStore[index].forEach(function(file) {
                    formData.append('photos[]', file);
                });
            }


            $.ajax({
                type: "POST",
                url: "{{ route('genba.upload_photo') }}",
                data: formData,
                dataType: "json",
                contentType: false,
                processData: false,
                success: function(data) {
                    toggleSubmitSpinner(submitButton, false);
                    if (data.code == 200) {
                        Toast.fire({
                            position: 'top-end',
                            title: "Data berhasil disimpan!",
                            icon: "success"
                        });

                        currentForm.reset();
                        $(currentForm).find('.assign_to, .assign_to_dept').val(null).trigger('change');
                        $('#preview_container_' + index).html('<small class="text-muted align-self-center">Pratinjau foto akan muncul di sini...</small>');
                        fileStore[index] = [];

                        $('#kt_rusty_table').DataTable().ajax.reload();

                    } else {
                        Toast.fire({
                            position: 'top-end',
                            title: data.message || 'Error: Pastikan data benar.',
                            icon: "error"
                        });
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    toggleSubmitSpinner(submitButton, false);
                    Toast.fire({
                        position: 'top-end',
                        title: 'Terjadi kesalahan saat mengirim data.',
                        icon: "error"
                    });
                    console.error("AJAX Error:", textStatus, errorThrown);
                }
            });
        });
    });


    function refresh_data_rusty(){
        $('#kt_rusty_table').DataTable().ajax.reload();
    }
    function delete_document_rusty(id) {
        var button = document.getElementById('btn_form_delete_rusty_' + id);
        var svg = document.getElementById('svg_form_delete_rusty_' + id);
        var spinner = document.getElementById('spinner_form_delete_rusty_' + id);

        svg.style.display = 'none';
        spinner.style.display = 'inline-block';
        button.disabled = true;

        Swal.fire({
            icon: 'warning',
            title: 'Delete Data Rusty?',
            showCancelButton: true,
            confirmButtonText: 'Confirm',
            cancelButtonText: 'Cancel'
        }).then(function(result) {
            if (result.isConfirmed) {
                execute_delete_item_rusty(id);
            } else {
                svg.style.display = 'inline-block';
                spinner.style.display = 'none';
                button.disabled = false;
            }
        });
    }

    function execute_delete_item_rusty(id) {
        var button = document.getElementById('btn_form_delete_rusty_' + id);
        var svg = document.getElementById('svg_form_delete_rusty_' + id);
        var spinner = document.getElementById('spinner_form_delete_rusty_' + id);

        var token = $("[name=_token]").val();

        $.ajax({
            type: "POST",
            url: "{{ route('genba.delete_rusty') }}",
            data: {
                _token: token,
                check_item_id: id
            },
            success: function(response) {
                if (response.code == 200) {
                    Toast.fire({
                        position: 'top-end',
                        title: "Rusty berhasil dihapus!",
                        icon: "success"
                    });
                    refresh_data_rusty();
                } else {
                    Toast.fire({
                        position: 'top-end',
                        title: 'Error',
                        icon: "error"
                    });
                }
            },
            error: function() {
                Toast.fire({
                    position: 'top-end',
                    title: 'Server error!',
                    icon: "error"
                });
            },
            complete: function() {
                svg.style.display = 'inline-block';
                spinner.style.display = 'none';
                button.disabled = false;
            }
        });
    }



    function finishGenba() {
        var submitButton = $(".btn_finish_form");
        toggleSubmitSpinner(submitButton, true);

        // $(currentForm).find('.btn_finish_form');
        var id_activity = $('#id_activity').val()
        var data = {
            _token: $('meta[name="csrf-token"]').attr('content'),
            id_activity: id_activity
        };
        $.ajax({
            type: "POST",
            url: "{{ route('genba.finish_activity') }}",
            data: data,
            dataType: "json",
            success: function(data) {
                toggleSubmitSpinner(submitButton, false);
                if (data.code == 200) {
                    Toast.fire({
                        position: 'top-end',
                        title: "Data berhasil disimpan!",
                        icon: "success"
                    });

                } else {
                    toggleSubmitSpinner(submitButton, false);
                    Toast.fire({
                        position: 'top-end',
                        title: data.message || 'Error: Pastikan data benar.',
                        icon: "error"
                    });
                }
            }
        });
    }
    var KTLayoutStickyCard = function() {
        /* ... kode asli ... */
    }();
</script>