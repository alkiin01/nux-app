<input type="hidden" name="_token" value="{{ csrf_token() }}">
<input type="hidden" id="header_id" value="{{ $header->id }}">
<input type="hidden" id="job_num" value="{{ $header->job_num }}">

<div class="col-xxl-12">
    <div class="card mb-5">
        <div class="card-header">
            <div class="card-title">
                <h3 class="fw-bolder m-0">Issue Material Form</h3>
            </div>
            <div class="card-toolbar">
                <button type="button" class="btn btn-light-primary btn-sm me-2" id="btn_sync_api" onclick="sync_internal_api()">
                    <span id="svg_sync_api" class="svg-icon svg-icon-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path opacity="0.3" d="M12 4C7.6 4 4 7.6 4 12H2L5 15L8 12H6C6 8.7 8.7 6 12 6C14.6 6 16.8 7.7 17.6 10H19.7C18.8 6.6 15.7 4 12 4Z" fill="black"/>
                            <path d="M19 9L16 12H18C18 15.3 15.3 18 12 18C9.4 18 7.2 16.3 6.4 14H4.3C5.2 17.4 8.3 20 12 20C16.4 20 20 16.4 20 12H22L19 9Z" fill="black"/>
                        </svg>
                    </span>
                    <span id="spinner_sync_api" class="spinner-border spinner-border-sm align-middle ms-2" style="display:none;"></span>
                    <span id="btn_text_sync_api">Sync Internal API</span>
                </button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="back_to_list()">Back</button>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">Doc Num</label>
                    <input type="text" class="form-control bg-light-primary" value="{{ $header->doc_num }}" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Job Num</label>
                    <input type="text" class="form-control bg-light-primary" value="{{ $header->job_num }}" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Part Job</label>
                    <input type="text" class="form-control bg-light-primary" value="{{ $header->job_part_num }}" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Progress</label>
                    <input type="text" class="form-control bg-light-primary" value="{{ number_format((float) $header->issue_percent, 2) }}% ({{ $header->status }})" readonly>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Material <span class="text-danger">*</span></label>
                    <select class="form-select form-select-solid" id="material_select" data-placeholder="Select material"></select>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">Mtl Seq</label>
                    <input type="text" class="form-control bg-light-primary" id="mtl_seq" readonly>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Part Num</label>
                    <input type="text" class="form-control bg-light-primary" id="part_num" readonly>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">UOM</label>
                    <input type="text" class="form-control bg-light-primary" id="uom" readonly>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Part Name</label>
                    <input type="text" class="form-control bg-light-primary" id="part_name" readonly>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">Qty Required</label>
                    <input type="text" class="form-control bg-light-primary" id="qty_required" readonly>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">Qty Issue <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="qty_issue" min="0.0001" step="0.0001" placeholder="0.0000">
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">Lot Number</label>
                    <input type="text" class="form-control" id="lot_num" maxlength="100" placeholder="e.g. LOT-001">
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">Bin Number</label>
                    <input type="text" class="form-control" id="bin_num" maxlength="50" placeholder="e.g. BIN-A1">
                </div>
            </div>
            <button type="button" class="btn btn-primary btn-sm" id="btn_add_item" onclick="store_item()">
                <span id="svg_add_item" class="svg-icon svg-icon-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect fill="black" x="4" y="11" width="16" height="2" rx="1"></rect>
                        <rect fill="black" opacity="0.3" transform="translate(12, 12) rotate(-270) translate(-12, -12)" x="4" y="11" width="16" height="2" rx="1"></rect>
                    </svg>
                </span>
                <span id="spinner_add_item" class="spinner-border spinner-border-sm align-middle ms-2" style="display:none;"></span>
                <span id="btn_text_add_item">Add Item</span>
            </button>
        </div>
    </div>

    <div class="card" id="detail_table_card">
        <div class="card-header border-1 pt-6 pb-6 mb-5">
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    <span class="svg-icon svg-icon-1 position-absolute ms-6">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                            <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                        </svg>
                    </span>
                    <input type="text" id="detail_table_search" class="form-control form-control-solid w-250px ps-15 text-sm form-control-sm" placeholder="Search PartNum / PartName" />
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table align-middle table-row-dashed table-striped gy-2 fs-7" id="kt_form_table">
                <thead>
                    <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                        <th>No</th>
                        <th>Delete</th>
                        <th>Mtl Seq</th>
                        <th>PartNum</th>
                        <th>PartName</th>
                        <th>UOM</th>
                        <th class="text-end">Qty Required</th>
                        <th class="text-end">Qty Issue</th>
                        <th>Lot Number</th>
                        <th>Bin Number</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                        <th>No</th>
                        <th>Delete</th>
                        <th>Mtl Seq</th>
                        <th>PartNum</th>
                        <th>PartName</th>
                        <th>UOM</th>
                        <th class="text-end">Qty Required</th>
                        <th class="text-end">Qty Issue</th>
                        <th>Lot Number</th>
                        <th>Bin Number</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
    let detailTableInstance = null;

    function format_decimal_value(value, precision = 4) {
        const numberValue = parseFloat(value);
        if (!isFinite(numberValue)) {
            return (0).toFixed(precision);
        }

        return numberValue.toFixed(precision);
    }

    function reset_material_form() {
        $('#material_select').val(null).trigger('change');
        $('#mtl_seq').val('');
        $('#part_num').val('');
        $('#part_name').val('');
        $('#uom').val('');
        $('#qty_required').val('');
        $('#qty_issue').val('');
        $('#lot_num').val('');
        $('#bin_num').val('');
    }

    function setup_material_select() {
        $('#material_select').select2({
            placeholder: 'Select material',
            allowClear: true,
            ajax: {
                type: 'POST',
                url: "{{ route('inventory_rm_out.material_options') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        _token: $('[name=_token]').val(),
                        job_num: $('#job_num').val(),
                        header_id: $('#header_id').val(),
                        search: params.term || '',
                        page: params.page || 1
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: $.map(data.items || [], function(item) {
                            return {
                                id: item.id,
                                text: item.text,
                                part_num: item.part_num,
                                part_name: item.part_name,
                                uom: item.uom,
                                qty_required: item.qty_required
                            };
                        }),
                        pagination: {
                            more: data.pagination && data.pagination.more
                        }
                    };
                },
                cache: true
            }
        });

        $('#material_select').on('select2:select', function(e) {
            const data = e.params.data;
            $('#mtl_seq').val(data.id || '');
            $('#part_num').val(data.part_num || '');
            $('#part_name').val(data.part_name || '');
            $('#uom').val(data.uom || '');
            $('#qty_required').val(format_decimal_value(data.qty_required, 4));
        });
    }

    function store_item() {
        const payload = {
            _token: $('[name=_token]').val(),
            header_id: $('#header_id').val(),
            mtl_seq: $('#mtl_seq').val(),
            part_num: $('#part_num').val(),
            part_name: $('#part_name').val(),
            uom: $('#uom').val(),
            qty_required: $('#qty_required').val(),
            qty_issue: $('#qty_issue').val(),
            lot_num: $('#lot_num').val(),
            bin_num: $('#bin_num').val()
        };

        if (!payload.mtl_seq) {
            Toast.fire({ position: 'top-end', title: 'Pilih material terlebih dahulu', icon: 'error' });
            return;
        }

        if (!payload.qty_issue || parseFloat(payload.qty_issue) <= 0) {
            Toast.fire({ position: 'top-end', title: 'Qty Issue wajib lebih dari 0', icon: 'error' });
            return;
        }

        $('#svg_add_item').hide();
        $('#spinner_add_item').show();
        $('#btn_add_item').prop('disabled', true);
        $('#btn_text_add_item').text('Please Wait...');

        $.ajax({
            type: 'POST',
            url: "{{ route('inventory_rm_out.store_item') }}",
            data: payload,
            dataType: 'json',
            success: function(response) {
                $('#svg_add_item').show();
                $('#spinner_add_item').hide();
                $('#btn_add_item').prop('disabled', false);
                $('#btn_text_add_item').text('Add Item');

                if (response.code === 200 || response.status === 'success') {
                    Toast.fire({
                        position: 'top-end',
                        title: response.message || 'Data berhasil tersimpan',
                        icon: 'success'
                    });
                    reset_material_form();
                    refresh_detail_table();
                } else {
                    Toast.fire({
                        position: 'top-end',
                        title: response.message || response.status || 'Gagal simpan data',
                        icon: 'error'
                    });
                }
            },
            error: function(jqXHR) {
                $('#svg_add_item').show();
                $('#spinner_add_item').hide();
                $('#btn_add_item').prop('disabled', false);
                $('#btn_text_add_item').text('Add Item');

                const message = (jqXHR.responseJSON && (jqXHR.responseJSON.message || jqXHR.responseJSON.status))
                    ? (jqXHR.responseJSON.message || jqXHR.responseJSON.status)
                    : 'Gagal simpan data';

                Toast.fire({ position: 'top-end', title: message, icon: 'error' });
            }
        });
    }

    function delete_item(detailId, no) {
        const button = document.getElementById('btn_delete_item_' + no);
        const svg = document.getElementById('svg_delete_item_' + no);
        const spinner = document.getElementById('spinner_delete_item_' + no);

        svg.style.display = 'none';
        spinner.style.display = 'inline-block';
        button.disabled = true;

        Swal.fire({
            icon: 'warning',
            title: 'Delete item?',
            showCancelButton: true,
            confirmButtonText: 'Confirm'
        }).then(function(result) {
            if (result.value === true) {
                execute_delete_item(detailId, no);
            } else {
                svg.style.display = 'inline-block';
                spinner.style.display = 'none';
                button.disabled = false;
            }
        });
    }

    function execute_delete_item(detailId, no) {
        $.ajax({
            type: 'POST',
            url: "{{ route('inventory_rm_out.delete_item') }}",
            dataType: 'json',
            data: {
                _token: $('[name=_token]').val(),
                detail_id: detailId
            },
            success: function(response) {
                const button = document.getElementById('btn_delete_item_' + no);
                const svg = document.getElementById('svg_delete_item_' + no);
                const spinner = document.getElementById('spinner_delete_item_' + no);
                svg.style.display = 'inline-block';
                spinner.style.display = 'none';
                button.disabled = false;

                if (response.code === 200 || response.status === 'success') {
                    Toast.fire({ position: 'top-end', title: response.message || 'Data berhasil dihapus', icon: 'success' });
                    refresh_detail_table();
                } else {
                    Toast.fire({ position: 'top-end', title: response.message || response.status || 'Gagal hapus data', icon: 'error' });
                }
            },
            error: function() {
                const button = document.getElementById('btn_delete_item_' + no);
                const svg = document.getElementById('svg_delete_item_' + no);
                const spinner = document.getElementById('spinner_delete_item_' + no);
                svg.style.display = 'inline-block';
                spinner.style.display = 'none';
                button.disabled = false;

                Toast.fire({ position: 'top-end', title: 'Gagal hapus data', icon: 'error' });
            }
        });
    }

    function detail_table() {
        if ($.fn.DataTable.isDataTable('#kt_form_table') && detailTableInstance) {
            detailTableInstance.ajax.reload(null, false);
            return;
        }

        detailTableInstance = $('#kt_form_table').DataTable({
            processing: true,
            serverSide: true,
            responsive: false,
            language: {
                processing: '<div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>'
            },
            order: [],
            columnDefs: [{
                orderable: false,
                targets: [0, 1]
            }],
            ajax: {
                url: "{{ route('inventory_rm_out.detail_table') }}",
                type: 'POST',
                data: function(d) {
                    d._token = $('[name=_token]').val();
                    d.header_id = $('#header_id').val();
                    d.search = $('#detail_table_search').val();
                },
                cache: false,
                dataType: 'json'
            },
            columns: [
                { data: 'no', className: 'text-center' },
                { data: 'action', className: 'text-center', orderable: false },
                { data: 'mtl_seq' },
                { data: 'part_num' },
                { data: 'part_name' },
                { data: 'uom' },
                { data: 'qty_required', className: 'text-end' },
                { data: 'qty_issue', className: 'text-end' },
                { data: 'lot_num' },
                { data: 'bin_num' }
            ]
        });
    }

    function refresh_detail_table() {
        if ($.fn.DataTable.isDataTable('#kt_form_table') && detailTableInstance) {
            detailTableInstance.ajax.reload(null, false);
            return;
        }

        detail_table();
    }

    function sync_internal_api() {
        $('#svg_sync_api').hide();
        $('#spinner_sync_api').show();
        $('#btn_sync_api').prop('disabled', true);
        $('#btn_text_sync_api').text('Please Wait...');

        $.ajax({
            type: 'POST',
            url: "{{ route('inventory_rm_out.sync_internal_api') }}",
            dataType: 'json',
            data: {
                _token: $('[name=_token]').val(),
                header_id: $('#header_id').val()
            },
            success: function(response) {
                $('#svg_sync_api').show();
                $('#spinner_sync_api').hide();
                $('#btn_sync_api').prop('disabled', false);
                $('#btn_text_sync_api').text('Sync Internal API');

                if (response.code === 200 || response.status === 'success') {
                    Toast.fire({ position: 'top-end', title: response.message || 'Sync API berhasil dijalankan', icon: 'success' });
                } else {
                    Toast.fire({ position: 'top-end', title: response.message || response.status || 'Sync API gagal', icon: 'error' });
                }
            },
            error: function() {
                $('#svg_sync_api').show();
                $('#spinner_sync_api').hide();
                $('#btn_sync_api').prop('disabled', false);
                $('#btn_text_sync_api').text('Sync Internal API');
                Toast.fire({ position: 'top-end', title: 'Sync API gagal', icon: 'error' });
            }
        });
    }

    function back_to_list() {
        if (typeof back_to_issue_material_list === 'function') {
            back_to_issue_material_list();
            return;
        }

        window.location.href = "{{ route('inventory_rm_out.index') }}";
    }

    $('#detail_table_search').on('keyup', function(event) {
        if (event.keyCode === 13) {
            refresh_detail_table();
        }
    });

    $(document).ready(function() {
        setup_material_select();
        detail_table();
    });
</script>
