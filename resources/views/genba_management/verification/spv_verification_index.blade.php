@extends('../layouts/app')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.11.3/viewer.min.css" />

<!-- JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.11.3/viewer.min.js"></script>
@section('subhead')
    <style>
        #modalImageContent img {
            cursor: pointer;
            transition: transform 0.2s;
        }

        #modalImageContent img:hover {
            transform: scale(1.05);
        }
    </style>
    <title>{{ $head_title }}</title>
    <script type="text/javascript">
        $(document).ready(function () {
            const urlParams = new URLSearchParams(window.location.search);
            var ref_doc = urlParams.get('ref_doc');
            if (ref_doc == '' || ref_doc == null) {
                $("#kt_activity_home_tab").addClass('show active');
                window.history.pushState('', '', '<?php echo env('BASE_URL'); ?>/spv_verification');
            } else {
                $('#temp_id').val(ref_doc);
                document_preview(ref_doc, 0);
            }
            $('#genba_category').select2({
                ajax: {
                    type: 'POST',
                    url: "{{ route('genba.get_genba_category') }}",
                    dataType: 'json',
                    delay: 250, // delay for search
                    data: function (params) {
                        var query = {
                            search: params.term,
                            _token: $("[name=_token]").val(),
                            page: params.page || 1
                        };
                        return query;
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: $.map(data.items, function (item) {
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
                placeholder: 'Select option',
            });

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
            <div id="kt_activity_head" class="card-body p-0 tab-pane fade show active" role="tabpanel"
                aria-labelledby="kt_activity_head_tab" hidden>
                <div class="post d-flex flex-column-fluid mb-0" id="kt_post">
                    <div id="kt_content_container" class="container-xxl">
                        <div class="card">
                            <div class="card-body">
                                <form class="row">
                                    <input type="hidden" name="status_id" id="status_id">
                                    <div class="col-lg-6">
                                        <input type="date" class="form-control " id="date" name="date" placeholder="Date" />
                                    </div>
                                    <div class="col-lg-6">
                                        <select name="genba_category" id="genba_category" class="form-select"
                                            data-kt-select2="true" data-placeholder="Select option" data-allow-clear="false"
                                            data-hide-search="true"></select>
                                    </div>
                                    <div class="d-flex justify-content-end align-items-center my-3 mx-3">
                                        <button type="button" id="submit-filter" class="btn btn-primary btn-sm"
                                            onclick=""><i class="fa fa-search"></i>
                                            Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                <div class="tab-content">
                    <div id="kt_activity_home" class="card-body p-0 tab-pane fade show active" role="tabpanel"
                        aria-labelledby="kt_activity_home_tab">
                        <div class="d-flex flex-column-fluid mt-lg-5 mt-sm-5">
                            <div id="kt_content_container" class="container-xxl">
                                <div class="card col-xxl-12 card-sticky">
                                    <div class="card-header border-0 pt-6">
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
                                                    class="form-control form-control-solid w-250px ps-15  text-sm form-control-sm"
                                                    placeholder="Search" />
                                            </div>
                                        </div>
                                        <div class="card-toolbar">

                                            <div class="d-flex justify-content-end">
                                                {{-- <button type="reset"
                                                    class="btn btn-light btn-active-light-primary me-2"
                                                    data-kt-menu-dismiss="true"
                                                    data-kt-goodreceive-table-filter="reset">Reset</button> --}}
                                                <button type="submit" id="submit-filterss" hidden
                                                    class="btn btn-primary btn-sm tex-sm" data-kt-menu-dismiss="true"
                                                    data-kt-goodreceive-table-filter="filter">Apply</button>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="card-body pt-0">
                                        <table class="table align-middle table-row-dashed table-striped fs-7 gy-3"
                                            id="kt_doc_table">
                                            <thead>
                                                <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                                    <th class="min-w-20px pe-2">No</th>
                                                    <th class="min-w-100px">DocDate</th>
                                                    <th class="min-w-100px">Process</th>
                                                    <th class="min-w-100px">Area Checked</th>
                                                    <th class="min-w-100px">Station</th>
                                                    <th class="min-w-100px">Auditor</th>
                                                    <th class="min-w-100px">Category</th>
                                                    <th class=" min-w-70px">View</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                                    <th class="min-w-20px pe-2">No</th>
                                                    <th class="min-w-100px">DocDate</th>
                                                    <th class="min-w-100px">Process</th>
                                                    <th class="min-w-100px">Area Checked</th>
                                                    <th class="min-w-100px">Station</th>
                                                    <th class="min-w-100px">Auditor</th>
                                                    <th class="min-w-100px">Category</th>
                                                    <th class=" min-w-70px">View</th>
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
                        <div class="d-flex flex-column-fluid mt-lg-5 mt-sm-5">
                            <div id="kt_content_container" class="container-xxl">
                                <div class="tab-content">
                                    <div id="kt_form_header" class="tab-pane fade" role="tabpanel"
                                        aria-labelledby="kt_form_header_tab">
                                        <div id="div_form"></div>
                                    </div>
                                    <div id="kt_form_detail" class="tab-pane fade" role="tabpanel"
                                        aria-labelledby="kt_form_detail_tab">
                                        <div id="div_form_detail"></div>
                                    </div>
                                    <div id="kt_form_attachment" class="tab-pane fade" role="tabpanel"
                                        aria-labelledby="kt_form_attachment_tab">
                                        <div id="attachment_list"></div>
                                    </div>
                                    <div id="kt_form_preview" class="tab-pane fade" role="tabpanel"
                                        aria-labelledby="kt_form_preview_tab" style="text-align: center">
                                        <div class="lds-roller mt-20 mb-10" id="lds-roller-preview">
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                        </div>
                                        <div id="file_view" class="p-5"></div>
                                    </div>
                                    <div id="kt_tag_label" class="tab-pane fade" role="tabpanel"
                                        aria-labelledby="kt_tag_label_tab" style="text-align: center">
                                        <div class="lds-roller mt-20 mb-10" id="lds-roller-tag-label">
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                        </div>
                                        <div id="tag_label_view" class="p-5"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <input type="hidden" name="temp_id" id="temp_id">
    </div>

    <script>
        $(document).ready(function () {
            var frontTable = $('#kt_doc_table').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                language: {
                    'processing': '<div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>'
                },
                info: !1,
                order: [],
                columnDefs: [{
                    orderable: !1,
                    targets: 0
                }],
                ajax: {
                    url: "{{ route('genba.verification_list') }}",
                    type: 'POST',
                    data: function (d) {
                        d._token = $("[name=_token]").val(),
                            d.genba_date = $("#genba_date").val(),
                            d.front_table_search = $("#front_table_search").val();
                    },
                    cache: false,
                },
                columns: [{
                    data: 'no',
                    name: 'no',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'date',
                    name: 'date'
                }, {
                    data: 'process',
                    name: 'process'
                }, {
                    data: 'area_checked',
                    name: 'area_checked'
                }, {
                    data: 'station',
                    name: 'station'
                },
                {
                    data: 'auditor',
                    name: 'auditor'
                },
                {
                    data: 'category',
                    name: 'category'
                },
                {
                    data: 'action',
                    name: 'action'
                },
                ],
            });

            $('#front_table_search').on('keyup', function () {
                frontTable.search(this.value).draw();
            });

            $("#submit-filter").click(function () {
                frontTable.ajax.reload();
            });

        });

        $("#genba_date").change(function () {
            document.getElementById('submit-filter').click();

        });

        function docSearch(id, element) {
            $("#status_id").val(id);
            $('#status_id').val(id).trigger('change');
            document.getElementById('submit-filter').click();
            document.querySelectorAll('.card-front').forEach(function (el) {
                el.classList.remove('bg-light-success');
            });
            element.classList.add('bg-light-success');
        }

        function resetFrontCard() {
            document.querySelectorAll('.card-front').forEach(function (el) {
                el.classList.remove('bg-light-success');
            });
        }

        function refreshTable() {
            $('#kt_doc_table').DataTable().ajax.reload();
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
            document.getElementById('kt_activity_preview_tab').click();
            var token = $("[name=_token]").val();
            var trc_unix_id = '0';
            var data = {
                _token: token,
                trc_unix_id: trc_unix_id
            };
            $.ajax({
                type: "POST",
                url: "{{ route('genba.activity') }}",
                data: data,
                success: function (data) {
                    svg.style.display = 'inline-block';
                    spinner.style.display = 'none';
                    spinner.style.display = 'none';
                    button.disabled = false;
                    buttonText.textContent = 'Create';

                    $("#div_form").html(data);
                    $("#kt_form_header").addClass('show active');
                    $("#kt_form_header_tab").addClass('active');

                    $("#kt_form_detail").removeClass('show active');
                    $("#kt_form_detail_tab").removeClass('active');

                    $("#kt_form_attachment").removeClass('show active');
                    $("#kt_form_attachment_tab").removeClass('active');

                    $("#kt_form_preview").removeClass('show active');
                    $("#kt_form_preview_tab").removeClass('active');

                    $("#kt_tag_label").removeClass('show active');
                    $("#kt_tag_label_tab").removeClass('active');


                    setTimeout(function () {
                        $("#form").css("display", "");
                        $("#form_loader").css("display", "none");
                        $("#lds-roller-form").css("display", "none");
                    }, 500)
                }
            });
        }

        function document_preview(id, no) {
            var button = document.getElementById('btn_form_view_doc_' + no);
            var svg = document.getElementById('svg_form_view_doc_' + no);
            var spinner = document.getElementById('spinner_form_view_doc_' + no);
            if (no != 0) {
                svg.style.display = 'none';
                spinner.style.display = 'inline-block';
                button.disabled = true;
            }
            $("#temp_id").val(id);
            $("#lds-roller-form").css("display", "");
            $("#form").css("display", "none");
            $("#form_loader").css("display", "");
            document.getElementById('kt_activity_preview_tab').click();
            var token = $("[name=_token]").val();
            var trc_unix_id = $("#temp_id").val();
            var data = {
                _token: token,
                trc_unix_id: id,
            };
            $.ajax({
                type: "POST",
                url: "{{ route('genba.verification_activity') }}",
                data: data,
                success: function (data) {
                    if (no > 0) {
                        svg.style.display = 'inline-block';
                        spinner.style.display = 'none';
                        button.disabled = false;
                    }
                    $("#div_form").html(data);
                    $("#kt_form_header").addClass('show active');
                    $("#kt_form_header_tab").addClass('active');

                    $("#kt_form_detail").removeClass('show active');
                    $("#kt_form_detail_tab").removeClass('active');
                    $("#kt_activity_head").removeClass('show active');
                    $("#kt_activity_head_tab").removeClass('active');

                    $("#kt_form_attachment").removeClass('show active');
                    $("#kt_form_attachment_tab").removeClass('active');

                    $("#kt_form_preview").removeClass('show active');
                    $("#kt_form_preview_tab").removeClass('active');

                    $("#kt_tag_label").removeClass('show active');
                    $("#kt_tag_label_tab").removeClass('active');


                    setTimeout(function () {
                        $("#form").css("display", "");
                        $("#form_loader").css("display", "none");
                        $("#lds-roller-form").css("display", "none");
                        window.history.pushState('', '',
                            '<?php echo env('BASE_URL'); ?>/spv_verification?ref_doc=' +
                            id + '_' + status_id);
                        activity_table();
                    }, 500)
                }
            });
        }

        function delete_document(id, no) {
            var button = document.getElementById('btn_form_delete_doc_' + no);
            var svg = document.getElementById('svg_form_delete_doc_' + no);
            var spinner = document.getElementById('spinner_form_delete_doc_' + no);
            svg.style.display = 'none';
            spinner.style.display = 'inline-block';
            button.disabled = true;
            Swal.fire({
                icon: 'warning',
                title: 'Delete Data ?',
                showCancelButton: true,
                confirmButtonText: 'Confirm',
            }).then(function (isConfirm) {
                if (isConfirm.value === true) {
                    execute_delete_item(id, no);

                } else {
                    svg.style.display = 'inline-block';
                    spinner.style.display = 'none';
                    button.disabled = false;
                }
            })

        }

        function execute_delete_item(id, no) {
            var button = document.getElementById('btn_form_delete_doc_' + no);
            var svg = document.getElementById('svg_form_delete_doc_' + no);
            var spinner = document.getElementById('spinner_form_delete_doc_' + no);
            var token = $("[name=_token]").val();
            var data = {
                _token: token,
                trc_unix_id: id
            };
            $.ajax({
                type: "POST",
                url: "{{ route('genba.delete_genba') }}",
                data: data,
                success: function (response) {
                    if (response.code != 200) {
                        Toast.fire({
                            position: 'top-end',
                            title: "Data berhasil dihapus!",
                            icon: "success"
                        })
                        svg.style.display = 'inline-block';
                        spinner.style.display = 'none';
                        refreshTable();
                    } else {
                        svg.style.display = 'inline-block';
                        spinner.style.display = 'none';
                        Toast.fire({
                            position: 'top-end',
                            title: 'Error',
                            icon: "error"
                        })
                    }
                }
            });

        }
    </script>
    <script>
        $(document).ready(function () {

            const urlParams = new URLSearchParams(window.location.search);
            var ref_form = urlParams.get('ref_form');
            var ref_tab = urlParams.get('ref_tab');
            var ref_doc = urlParams.get('ref_doc');
            var revise = urlParams.get('revise');
            if (ref_doc == null) {
                $("#kt_activity_home_tab").addClass('show active');
            } else {
                $('#temp_id').val(ref_doc);
                // document_preview(ref_doc);
            }
        })

        function getDetail(id) {
            $("#pack_line").val(id);
            document.getElementById('kt_form_detail_tab').click();
        };

        let streamMap = {}; // Objek untuk menyimpan stream kamera
        // Akses Kamera


        function next_form() {
            var button = document.getElementById('btn_next_form');
            var svg = document.getElementById('svg_next_form');
            var spinner = document.getElementById('spinner_next_form');
            var buttonText = document.getElementById('btn_text_next_form');
            svg.style.display = 'none';
            spinner.style.display = 'inline-block';
            button.disabled = true;

            var date = $("#date").val();
            var area_checked = $("#area_checked").val();
            var auditor = $("#auditor").val();
            var note = $("#note").val();
            var status = $("#status").val();
            var trc_unix_id = $("#trc_unix_id").val();
            $("#div-form-activity").html("");
            var token = $("[name=_token]").val();
            var data = {
                _token: token,
                date: date,
                area_checked: area_checked,
                auditor: auditor,
                note: note,
                status: status,
                trc_unix_id: trc_unix_id
            };
            $.ajax({
                type: "post",
                url: "{{ route('genba.add_genba') }}",
                data: data,
                cache: false,
                success: function (data) {
                    $("#kt_form_header").removeClass('show active');
                    $("#kt_form_header_tab").removeClass('active');

                    $("#kt_form_detail").addClass('show active');
                    $("#kt_form_detail_tab").addClass('active');

                    $("#kt_form_attachment").removeClass('show active');
                    $("#kt_form_attachment_tab").removeClass('active');

                    $("#kt_form_preview").removeClass('show active');
                    $("#kt_form_preview_tab").removeClass('active');

                    $("#kt_tag_label").removeClass('show active');
                    $("#kt_tag_label_tab").removeClass('active');
                    svg.style.display = 'inline-block';
                    spinner.style.display = 'none';
                    button.disabled = false;
                    $("#div_form_detail").html(data);
                    setTimeout(function () {
                        $("#form-detail").css("display", "");
                        $("#form_loader").css("display", "none");
                        $("#form_detail_loader").css("display", "none");
                        $("#lds-roller-form-detail").css("display", "none");
                        svg.style.display = 'inline-block';
                        spinner.style.display = 'none';
                        buttonText.textContent = 'Add Line';
                        button.disabled = false;
                    }, 500)
                },
                error: function (jqXHR, textStatus) {
                    svg.style.display = 'inline-block';
                    spinner.style.display = 'none';
                    buttonText.textContent = 'Create';
                    button.disabled = false;
                    Swal.fire({
                        text: "Please reload and try again! ",
                        icon: "error",
                        buttonsStyling: !1,
                        confirmButtonText: "Close",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    })
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
            setTimeout(function () {
                svg.style.display = 'inline-block';
                spinner.style.display = 'none';
                buttonText.textContent = 'Back';
                button.disabled = false;
                document.getElementById('kt_activity_home_tab').click();
            }, 300)
            $("#kt_activity_head").addClass('show active');
            $("#kt_activity_head_tab").addClass('active');
            window.history.pushState('', '', '<?php echo env('BASE_URL'); ?>/spv_verification');
        }

        function emptyStr(str) {
            return !str || !/[^\s]+/.test(str);
        };
        $(document).on('click', '.thumbnail-img', function () {
            var imageData = $(this).data('images');
            var images = imageData ? imageData.split(',') : [];

            var modalGallery = $('#modalImageContent');
            modalGallery.empty();

            // ambil BASE_URL dari .env
            var baseUrl = "<?= rtrim(env('BASE_URL'), '/'); ?>"; // contoh: https://nux.summitadyawinsa.co.id

            images.forEach(function (image) {
                if (!image || image.trim() === '') return;

                // pastikan path foto benar
                var cleanedImage = image.trim();

                // kalau datanya cuma nama file (belum ada "photos/"), tambahkan
                if (!cleanedImage.startsWith('photos/')) {
                    cleanedImage = 'photos/' + cleanedImage;
                }

                // gabungkan jadi path akhir
                var imageUrl = `${baseUrl}/storage/app/public/${cleanedImage}`;

                var imgTag = `
                    <div class="col-4 mb-2">
                        <img src="${imageUrl}" class="img-fluid rounded" alt="Image">
                    </div>`;
                modalGallery.append(imgTag);
            });

            $('#imageModal').modal('show');

            $('#imageModal').on('shown.bs.modal', function () {
                if (window.modalViewer) {
                    window.modalViewer.destroy();
                }

                const imageContainer = document.getElementById('modalImageContent');
                window.modalViewer = new Viewer(imageContainer, {
                    toolbar: true,
                    navbar: false,
                    title: false,
                    tooltip: true,
                    movable: true,
                    zoomable: true,
                    scalable: false,
                    transition: true,
                    fullscreen: true,
                });
            });


            $('#imageModal').on('hidden.bs.modal', function () {
                if (window.modalViewer) {
                    window.modalViewer.destroy();
                    window.modalViewer = null;
                }
            });
        });


        function activity_table() {
            $('#kt_activity_table tbody').empty();
            $('#kt_activity_table').DataTable().destroy();
            var frontTable = $('#kt_activity_table').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                paging: true,
                deferLoading: 57,
                language: {
                    'processing': '<div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>'
                },
                pageLength: 10,
                info: !1,
                columnDefs: [{
                    orderable: 0,
                    targets: 0
                }],
                ajax: {
                    url: "{{ route('genba.verification_activity_list') }}",
                    type: 'POST',
                    data: function (d) {
                        d._token = $("[name=_token]").val(),
                            d.status_id = $("#status_id2").val(),
                            d.trc_unix_id = $("#temp_id").val();
                    },
                    cache: false,
                },
                columns: [{
                    data: 'no',
                    name: 'no',
                    searchable: false,
                    className: 'text-center bold'
                },
                {
                    data: 'asign_to_dept',
                    name: 'asign_to_dept'
                },
                {
                    data: 'findings',
                    name: 'findings'
                },
                {
                    data: 'result',
                    name: 'result',
                    className: 'text-center',
                    render: function (data, type, row) {
                        // Cek jika data 'result' adalah 3, jika iya tampilkan simbol silang
                        if (data == 3) {
                            return '<span class="icon icon-primary"><i class="fas fa-window-close fa-2x text-danger"></i></span>';
                            // return '<span class="svg-icon svg-icon-danger svg-icon-2x"><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo1/dist/../src/media/svg/icons/Navigation/Close.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"><defs/><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">    <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">        <rect x="0" y="7" width="16" height="2" rx="1"/>        <rect opacity="0.3" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000) " x="0" y="7" width="16" height="2" rx="1"/>    </g></g></svg><!--end::Svg Icon--></span>';
                            // return '<span class="svg-icon svg-icon-primary svg-icon-3x "><svg xmlns="http://www.w3.org/2000/svg"xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"height="24px" viewBox="0 0 24 24" version="1.1"><defs/><g stroke="none" stroke-width="1" fill="none"fill-rule="evenodd"><g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)"    fill="#000000"><rect x="0" y="7" width="16" height="2"rx="1"></rect><rect opacity="0.3"transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000) "x="0" y="7" width="16" height="2" rx="1"></rect></g></g></svg></span>';
                        } else if (data == 2) {
                            return '<span class="svg-icon svg-icon-primary svg-icon-3x"><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo1/dist/../src/media/svg/icons/Design/Triangle.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"><defs/><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">    <rect x="0" y="0" width="24" height="24"/>    <path d="M3.95428417,19 L20.0457158,19 C20.3218582,19 20.5457158,18.7761424 20.5457158,18.5 C20.5457158,18.3982978 20.5147019,18.2990138 20.4568119,18.215395 L12.4110961,6.59380547 C12.2539131,6.36676337 11.9424371,6.31013137 11.715395,6.46731437 C11.6659703,6.50153145 11.623121,6.54438079 11.5889039,6.59380547 L3.54318807,18.215395 C3.38600507,18.4424371 3.44263707,18.7539131 3.66967918,18.9110961 C3.75329796,18.968986 3.85258194,19 3.95428417,19 Z" fill="#000000"/></g></svg><!--end::Svg Icon--></span>';
                            // return '<span class="svg-icon svg-icon-primary svg-icon-3x "><svg xmlns="http://www.w3.org/2000/svg"xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"viewBox="0 0 24 24" version="1.1"><defs/><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24" /><pathd="M3.95428417,19 L20.0457158,19 C20.3218582,19 20.5457158,18.7761424 20.5457158,18.5 C20.5457158,18.3982978 20.5147019,18.2990138 20.4568119,18.215395 L12.4110961,6.59380547 C12.2539131,6.36676337 11.9424371,6.31013137 11.715395,6.46731437 C11.6659703,6.50153145 11.623121,6.54438079 11.5889039,6.59380547 L3.54318807,18.215395 C3.38600507,18.4424371 3.44263707,18.7539131 3.66967918,18.9110961 C3.75329796,18.968986 3.85258194,19 3.95428417,19 Z"fill="#000000" /></g></svg></span>';
                        } else if (data == 1) {
                            // return '<span class=""><i class="fa fa-circle fa-fw"></i></span>';
                            return '<span class="svg-icon svg-icon-primary svg-icon-3x "><svg xmlns="http://www.w3.org/2000/svg"xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"viewBox="0 0 24 24" version="1.1"><defs/><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24" /><circle fill="#000000" cx="12" cy="12" r="8" /></g></svg></span>';
                        }
                        return data; // Jika bukan 3, tampilkan nilai asli
                    }
                },
                {
                    data: 'photo',
                    name: 'photo',
                    render: function (data, type, row) {
                        if (data != null || data != '') {
                            var html = '<div class="thumbnail-gallery">';
                            html +=
                                '<button class="btn btn-light-primary btn-sm thumbnail-img" data-images="' +
                                data + '"> <i class="fa fa-camera" ></i> </button></div>';
                            return html;
                        } else {
                            return "";
                        }
                    }
                }
                ],
            });


            frontTable.on('draw', function () {
                var info = frontTable.page.info();
                var start = info.start;
                var rowIndex = start;

                frontTable.rows({
                    page: 'current'
                }).every(function () {
                    var row = this.node();
                    var group = $(row).prev().hasClass('group') ? $(row).prev() :
                        null;
                    if (group) {
                        rowIndex = start;
                    }
                    $('td:eq(0)', row).html(++rowIndex);
                });
            });
            frontTable.ajax.reload();
        }
        listUserAssign();

        function document_verify(id, no, genba_id, scope_id) {
            var button = document.getElementById('btn_verify_doc_' + id);
            var svg = document.getElementById('svg_verify_doc_' + id);
            var spinner = document.getElementById('spinner_verify_doc_' + id);
            $("#scope_id").val(scope_id);
            $("#genba_id").val(genba_id);
            $("#check_item_id").val(id);

            // svg.style.display = 'inline-block';
            // spinner.style.display = 'none';
            // button.disabled = false;
            var data = {
                _token: $("[name=_token]").val(),
                check_item_id: id,
                genba_id: genba_id,
                scope_id: scope_id
            };
            $('#kt_modal_show').modal('show');
            $.ajax({
                type: "POST",
                url: "{{ route('genba.getVerifiedform') }}",
                data: data,
                dataType: "json",
                success: function (data) {
                    $('#asign_to_dept').change(data.asign_to_dept);
                    $('#asign_to').change(data.asign_to);
                    setTimeout(function () {
                        svg.style.display = 'inline-block';
                        spinner.style.display = 'none';
                        buttonText.textContent = 'Back';
                        button.disabled = false;
                    }, 300)
                }
            });
        }

        function listUserAssign() {
            var query = {
                _token: $("[name=_token]").val(),
            };
            $.ajax({
                type: "POST",
                url: "{{ route('genba.get_user_data') }}",
                data: query,
                dataType: "json",
                success: function (data) {
                    if (data && data.length > 0) {
                        $('#asign_to').empty();
                        data.forEach(function (item) {
                            var option = $('<option></option>')
                                .attr('value', item
                                    .username)
                                .text(item
                                    .full_name);

                            $('#asign_to').append(option);
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.log("Error loading data: " + error);
                }
            });
        }

        function save_verification() {
            var asign_to = $("#asign_to").val();
            var asign_to_name = $("#asign_to option:selected").text();

            var asign_to_dept = $("#asign_to_dept").val();
            var priority = $("#priority").val();
            var genba_id = $("#genba_id").val();
            var check_item_id = $("#check_item_id").val();
            var scope_id = $("#scope_id").val();

            var data = {
                _token: $("[name=_token]").val(),
                scope_id: scope_id,
                check_item_id: check_item_id,
                genba_id: genba_id,
                priority: priority,
                asign_to_dept: asign_to_dept,
                asign_to: asign_to,
                asign_to_name: asign_to_name

            };
            $.ajax({
                type: "POST",
                url: "{{ route('genba.save_verified') }}",
                data: data,
                dataType: "json",
                success: function (data) {
                    if (data.code == 200) {
                        $("#kt_modal_show").modal('hide');
                    } else {

                    }
                }
            });

        }
    </script>
@endsection