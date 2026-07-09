@extends('../layouts/app')

@section('subhead')
    <title>{{ $head_title }}</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endsection

<script src="<?= env('APP_ASSETS') ?>assets/js/jquery/jquery.min.js"></script>

@section('subcontent')
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <div class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">Surat Jalan General
                    <span class="h-20px border-gray-200 border-start ms-3 mx-2"></span>
                    <small class="text-muted fs-7 fw-bold my-1 ms-1">Daftar Dokumen</small>
                </h1>
            </div>

        </div>
    </div>

    <div class="content     post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-xxl">

            {{-- Tabs --}}
            <ul class="nav nav-tabs nav-line-tabs mb-5 fs-7">
                <li class="nav-item">
                    <a class="nav-link active" id="tab_all" data-bs-toggle="tab" href="#"
                        onclick="loadTable('all')">Semua</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab_mine" data-bs-toggle="tab" href="#"
                        onclick="loadTable('mine')">Permintaan Saya</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab_wait_check" data-bs-toggle="tab" href="#"
                        onclick="loadTable('waiting_check')">Menunggu Cek</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab_wait_approve" data-bs-toggle="tab" href="#"
                        onclick="loadTable('waiting_approve')">Menunggu Approve</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab_approved" data-bs-toggle="tab" href="#"
                        onclick="loadTable('approved')">Approved</a>
                </li>
            </ul>

            <div class="card shadow-sm">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <div class="d-flex align-items-center position-relative my-1">
                            <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                <i class="fa fa-search fs-6 text-gray-400"></i>
                            </span>
                            <input type="text" id="search_input" class="form-control form-control-solid w-250px ps-14"
                                placeholder="Cari nomor / penerima..." onkeyup="reloadTable()" />
                        </div>
                        
                    </div>
                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                            <a href="{{ url('sj_general/create') }}" class="btn btn-sm fw-bolder btn-primary">
                                <i class="fa fa-plus fs-6"></i> Buat Baru
                            </a>
                        </div>
                </div>
                <div class="card-body pt-0">
                    <table class="table table-bordered table-hover align-middle fs-7" id="sj_table" style="width:100%">
                        <thead class="table-light fw-bold">
                            <tr>
                                <th class="text-center" style="width:45px">No</th>
                                <th>No. SJ</th>
                                <th>Tanggal</th>
                                <th class="text-center" style="width:80px">Kategori</th>
                                <th>Penerima</th>
                                <th>Dibuat Oleh</th>
                                <th class="text-center" style="width:110px">Status Cek</th>
                                <th class="text-center" style="width:110px">Status Approve</th>
                                <th class="text-center" style="width:170px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Review action (check/approve) --}}
    <div class="modal fade" id="modal_action" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal_action_title">Konfirmasi Tindakan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="action_enc_id">
                    <input type="hidden" id="action_type">
                    <input type="hidden" id="action_status">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Catatan (opsional)</label>
                        <textarea class="form-control" id="action_note" rows="3" placeholder="Tuliskan catatan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btn_confirm_action" onclick="confirmAction()">
                        <span class="spinner-border spinner-border-sm d-none" id="spinner_action"></span> Konfirmasi
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Delete confirm --}}
    <div class="modal fade" id="modal_delete" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hapus Dokumen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="fa fa-exclamation-triangle text-danger fs-1 mb-3 d-block"></i>
                    <p>Yakin ingin menghapus dokumen ini?</p>
                    <input type="hidden" id="delete_enc_id">
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete()">Hapus</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('submenu')
    {!! $menu_level_1 !!}
@endsection
@section('submenu2')
    {!! $menu_level_2 !!}
@endsection
@section('submenu3')
    {!! $menu_level_3 !!}
@endsection
@section('submenu4')
    {!! $menu_level_4 !!}
@endsection

<script>
    var current_status = 'all';
    var sj_table;

    $(document).ready(function() {
        init_table();
    });

    function init_table() {
        sj_table = $('#sj_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('sj_general.table') }}',
                type: 'POST',
                data: function(d) {
                    d._token = '{{ csrf_token() }}';
                    d.front_table_search = $('#search_input').val();
                    d.status_filter = current_status;
                },
            },
            columns: [{
                    data: 'no',
                    orderable: false,
                    className: 'text-center'
                },
                {
                    data: 'sj_number'
                },
                {
                    data: 'sj_date',
                    className: 'text-center'
                },
                {
                    data: 'category',
                    className: 'text-center',
                    orderable: false
                },
                {
                    data: 'recipient_name'
                },
                {
                    data: 'creator_name'
                },
                {
                    data: 'status_checker',
                    className: 'text-center',
                    orderable: false
                },
                {
                    data: 'status_approver',
                    className: 'text-center',
                    orderable: false
                },
                {
                    data: 'action',
                    orderable: false,
                    className: 'text-center'
                },
            ],
            order: [
                [1, 'desc']
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
            },
            pageLength: 15,
        });
    }

    function loadTable(status) {
        current_status = status;
        sj_table.ajax.reload();
    }

    function reloadTable() {
        sj_table.ajax.reload();
    }

    function editSj(enc_id) {
        window.location.href = '{{ url('sj_general/edit') }}?id=' + enc_id;
    }

    function openDetail(enc_id) {
        window.location.href = '{{ url('sj_general/edit') }}?id=' + enc_id;
    }

    function deleteSj(enc_id) {
        $('#delete_enc_id').val(enc_id);
        $('#modal_delete').modal('show');
    }

    function confirmDelete() {
        $.ajax({
            url: '{{ route('sj_general.destroy') }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                enc_id: $('#delete_enc_id').val()
            },
            success: function(res) {
                $('#modal_delete').modal('hide');
                if (res.process_status == 200) {
                    Swal.fire('Berhasil', res.msg_process, 'success').then(() => sj_table.ajax.reload());
                } else {
                    Swal.fire('Gagal', res.msg_process, 'error');
                }
            }
        });
    }

    function submitReview(enc_id) {
        Swal.fire({
            title: 'Kirim untuk Approval?',
            text: 'Dokumen akan diberi nomor SJ dan status checker/approver menjadi PENDING.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Submit',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route('sj_general.submit_review') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        enc_id: enc_id
                    },
                    success: function(res) {
                        if (res.process_status == 200) {
                            Swal.fire('Berhasil', res.msg_process, 'success').then(() => sj_table
                                .ajax.reload());
                        } else {
                            Swal.fire('Gagal', res.msg_process, 'error');
                        }
                    }
                });
            }
        });
    }

    function doCheck(enc_id, status) {
        $('#action_enc_id').val(enc_id);
        $('#action_type').val('check');
        $('#action_status').val(status);
        $('#action_note').val('');
        $('#modal_action_title').text(status === 'APPROVED' ? 'Setujui Pemeriksaan' : 'Tolak Pemeriksaan');
        $('#btn_confirm_action').removeClass('btn-success btn-danger').addClass(status === 'APPROVED' ? 'btn-success' :
            'btn-danger');
        $('#modal_action').modal('show');
    }

    function doApprove(enc_id, status) {
        $('#action_enc_id').val(enc_id);
        $('#action_type').val('approve');
        $('#action_status').val(status);
        $('#action_note').val('');
        $('#modal_action_title').text(status === 'APPROVED' ? 'Setujui Dokumen' : 'Tolak Dokumen');
        $('#btn_confirm_action').removeClass('btn-success btn-danger').addClass(status === 'APPROVED' ? 'btn-success' :
            'btn-danger');
        $('#modal_action').modal('show');
    }

    function confirmAction() {
        var type = $('#action_type').val();
        var route = type === 'check' ? '{{ route('sj_general.do_check') }}' : '{{ route('sj_general.do_approve') }}';
        $('#spinner_action').removeClass('d-none');
        $.ajax({
            url: route,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                enc_id: $('#action_enc_id').val(),
                action_status: $('#action_status').val(),
                note: $('#action_note').val(),
            },
            success: function(res) {
                $('#spinner_action').addClass('d-none');
                $('#modal_action').modal('hide');
                if (res.process_status == 200) {
                    Swal.fire('Berhasil', res.msg_process, 'success').then(() => sj_table.ajax.reload());
                } else {
                    Swal.fire('Gagal', res.msg_process, 'error');
                }
            },
            error: function() {
                $('#spinner_action').addClass('d-none');
                Swal.fire('Error', 'Terjadi kesalahan server.', 'error');
            }
        });
    }
</script>
