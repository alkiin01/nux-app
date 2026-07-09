@extends('../layouts/app')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.11.3/viewer.min.css" />

<!-- JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.11.3/viewer.min.js"></script>
@section('subhead')
    <title>Realization</title>
    <script type="text/javascript">
        $(document).ready(function () {
            const urlParams = new URLSearchParams(window.location.search);
            var ref_doc = urlParams.get('ref_doc');
            if (ref_doc == '' || ref_doc == null) {
                $("#kt_activity_home_tab").addClass('show active');
                window.history.pushState('', '', '<?php echo env('BASE_URL'); ?>/execution_genba');
            } else {
                $('#temp_id').val(ref_doc);
                document_preview(ref_doc, 0);
            }
        })
    </script>
@endsection

<script src="<?= env('APP_ASSETS') ?>assets/js/jquery/jquery.min.js">
</script>
@section('subcontent')
    <style>
        #modalImageContent,
        #modalevidencesContent img {
            cursor: pointer;
            transition: transform 0.2s;
        }

        #modalImageContent,
        #modalevidencesContent img:hover {
            transform: scale(1.05);
        }
    </style>
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">Summary Genba
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
            {{-- <div id="kt_activity_head" class="card-body p-0 tab-pane fade show active" role="tabpanel"
                aria-labelledby="kt_activity_head_tab">
                <div class="post d-flex flex-column-fluid mb-0" id="kt_post">
                    <div id="kt_content_container" class="container-xxl">
                        <div class="card">

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6 border-1">
                                        <input type="hidden" name="status_id" id="status_id">
                                        <a href="#" onclick="docSearch(0, this);"
                                            class="card bgi-no-repeat card-xl-stretch mb-5 card-front"
                                            style="background-position: right top; background-size: 30% auto; background-image:
                                                    url(<?= env('APP_ASSETS') ?>assets/media/svg/shapes/abstract-4.svg); border:solid 1px #e4e6ef; box-shadow: 0 4px 8px rgba(128, 128, 128, 0.5);">
                                            <div class="card-body">
                                                <div class="text-gray-900 fw-bolder fs-4 mb-2 mt-2" id="total_document">
                                                </div>
                                                <div class="fw-bold text-gray-900">Pending</div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <a href="#" onclick="docSearch(1, this);"
                                            class="card bgi-no-repeat card-xl-stretch mb-5 card-front"
                                            style="background-position: right top; background-size: 30% auto; background-image: url(<?= env('APP_ASSETS') ?>assets/media/svg/shapes/abstract-2.svg); border:solid 1px #e4e6ef; box-shadow: 0 4px 8px rgba(128, 128, 128, 0.5);">
                                            <div class="card-body">
                                                <div class="text-gray-900 fw-bolder fs-4 mb-2 mt-2" id="total_draft">
                                                </div>
                                                <div class="fw-bold text-gray-900">Executed</div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
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
                                                placeholder="Search LPA Scope" />
                                        </div>
                                    </div>
                                    <div class="card-toolbar">

                                        <div class="d-flex justify-content-end"
                                            data-kt-goodreceive-table-toolbar="base">
                                            <button type="button" class="btn btn-light-primary  btn-sm me-3"
                                                data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                <span class="svg-icon svg-icon-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none">
                                                        <path
                                                            d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z"
                                                            fill="black" />
                                                    </svg>
                                                </span>
                                                Filter</button>
                                            <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px"
                                                data-kt-menu="true" id="kt-toolbar-filter">
                                                <div class="px-7 py-5">
                                                    <div class="fs-4 text-dark fw-bolder">Filter Options</div>
                                                </div>
                                                <div class="separator border-gray-200"></div>
                                                <div class="px-7 py-5">
                                                    <div class="mb-5">
                                                        <label class="form-label fs-5 fw-bold mb-3">Tanggal Dari:</label>
                                                        <input type="date" class="form-control form-control-solid" id="date_from" placeholder="Pilih tanggal">
                                                    </div>
                                                    <div class="mb-5">
                                                        <label class="form-label fs-5 fw-bold mb-3">Tanggal Sampai:</label>
                                                        <input type="date" class="form-control form-control-solid" id="date_to" placeholder="Pilih tanggal">
                                                    </div>
                                                    <div class="mb-5">
                                                        <label class="form-label fs-5 fw-bold mb-3">Auditor:</label>
                                                        <input type="text" class="form-control form-control-solid" id="auditor_filter" placeholder="Nama Auditor">
                                                    </div>
                                                    <div class="d-flex justify-content-end">
                                                        <button type="button" class="btn btn-secondary me-3" data-kt-menu-dismiss="true" id="reset-filter">Reset</button>
                                                        <button type="submit" id="submit-filter" class="btn btn-primary"
                                                            data-kt-menu-dismiss="true"
                                                            data-kt-goodreceive-table-filter="filter">Apply</button>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="d-flex justify-content-end align-items-center d-none"
                                            data-kt-goodreceive-table-toolbar="selected">
                                            <div class="fw-bolder me-5">
                                                <span class="me-2"
                                                    data-kt-goodreceive-table-select="selected_count"></span>Selected
                                            </div>
                                            <button type="button" class="btn btn-danger"
                                                data-kt-goodreceive-table-select="delete_selected">Delete Selected</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table class="table align-middle dataTable table-row-dashed table-striped fs-7 gy-3"
                                        id="kt_activity_table">
                                        <thead>
                                            <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                                <th class="min-w-20px pe-2">No</th>
                                                <th class="min-w-100px">Action</th>
                                                <th class="min-w-100px">Date</th>
                                                <th class="min-w-100px">Process</th>
                                                <th class="min-w-100px">Area Checked</th>
                                                <th class="min-w-100px">Station</th>
                                                <th class="min-w-100px">Auditor</th>
                                                <th class="min-w-100px">Category</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                                <th class="min-w-20px pe-2">No</th>
                                                <th class="min-w-100px">Action</th>
                                                <th class="min-w-100px">Date</th>
                                                <th class="min-w-100px">Process</th>
                                                <th class="min-w-100px">Area Checked</th>
                                                <th class="min-w-100px">Station</th>
                                                <th class="min-w-100px">Auditor</th>
                                                <th class="min-w-100px">Category</th>
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
    <div class="modal fade" id="imageModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="true" role="dialog"
        aria-labelledby="imageModaltitle" aria-hidden="true">
        <div class="modal-dialog modal-lg " role="document">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModaltitle">
                        Findings
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row" id="modalImageContent">

                    </div>
                    <div class="mt-3">
                        <textarea class="form-control" name="findings" id="findings" cols="20" rows="10"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="evidencesModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="true"
        role="dialog" aria-labelledby="evidencesModaltitle" aria-hidden="true">
        <div class="modal-dialog modal-lg " role="document">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title" id="evidencesModaltitle">
                        Findings
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row" id="modalevidencesContent">
                    </div>
                    <div class="mt-3">
                        <textarea class="form-control" name="evidence_comment" id="evidence_comment" cols="20" rows="10"
                            disabled></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-verification btn_verify_form" style="display: none;"
                        onclick="doVerfied()">Verify</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade " id="modalId" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog"
        aria-labelledby="modalTitleId" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Findings
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Kamera -->
                        <input type="hidden" name="modalSysID" id="modalSysID" value="">
                        <div class="camera-section mt-3" id="cameraSection" width="100%" height="200">
                            <video id="video" width="100%" height="200" autoplay style="display: none;"></video>
                            <canvas id="canvas" style="display: none;"></canvas>
                            <img id="photo" src="" alt="Hasil Foto" class="img-fluid mt-2" style="display: none;">
                            <input type="hidden" name="photo_data[]" id="photoData">
                            <input type="hidden" name="photo_name[]" id="photoname">
                            <div class="mt-3">
                                <h5>Captured Files:</h5>
                                <div id="fileNamesContainer">
                                </div> <!-- Tempat menampilkan nama file -->
                            </div>
                        </div>

                        <!-- Upload Gambar -->
                        <div class="mt-3">
                            <div class="col-md-12">
                                <input type="file" id="uploadImage" class="form-control" accept="image/*"
                                    onchange="uploadImage()">
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="col-md-12">
                                <label for="findings">Comment</label>
                                <textarea name="findings" class="form-control" id="findings"></textarea>
                            </div>
                        </div>
                    </div>
                    <button type="button" id="openCameraBtn" class="btn btn-primary" onclick="open_camera()">Open
                        Camera</button>
                    <button type="button" id="closeCameraBtn" class="btn btn-danger" onclick="close_camera()"
                        style="display: none;">Tutup Kamera
                    </button>
                    <button type="button" id="captureBtn" class="btn btn-primary mt-2" onclick="capture()"
                        style="display: none;">Ambil Foto</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="close_camera('')">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary"
                        onclick="save_captured(JSON.parse(document.getElementById('photoData').value) )">Save</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Button trigger modal -->


    <!-- Modal -->
    <div class="modal fade" id="modalWs" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Evidence Pict</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="temp_id" value="" id="temp_id">

    <script>
        function toggleSubmitSpinner(button, show) {
            const svgIcon = $(button).find('.svg-icon');
            const spinner = $(button).find('.spinner-border');
            const btnFinish = $(button).find('.btn_verify_form');

            if (show) {
                $(button).prop('disabled', true);
                svgIcon.hide();
                spinner.show();
                btnFinish.text('Processing...');
            } else {
                $(button).prop('disabled', false);
                svgIcon.show();
                spinner.hide();
                btnFinish.text('Finish');
            }
        }
        $(document).ready(function () {
            $.fn.dataTable.ext.errMode = 'none';

            var frontTable = $('#kt_activity_table').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                language: {
                    'processing': '<div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>',
                    "zeroRecords": "Please filter Data First"
                },
                pageLength: 10,
                info: !1,
                columnDefs: [{
                    orderable: 0,
                    targets: 0
                }],
                ajax: {
                    url: "{{ route('genba.execution_activity_list') }}",
                    type: 'POST',
                    data: function (d) {
                        d._token = $("[name=_token]").val(),
                            d.trc_unix_id = $("#trc_unix_id").val(),
                            d.date_from = $("#date_from").val(),
                            d.date_to = $("#date_to").val(),
                            d.auditor = $("#auditor_filter").val();
                    },
                    "error": function (xhr, error, code) {
                        console.log("Error:", error);
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
                    data: 'action',
                    name: 'action'
                }, {
                    data: 'date',
                    name: 'date'
                },
                {
                    data: 'process',
                    name: 'process'
                }, {
                    data: 'area_checked',
                    name: 'area_checked'
                }, {
                    data: 'station',
                    name: 'station'
                }, {
                    data: 'auditor',
                    name: 'auditor'
                }, {
                    data: 'category',
                    name: 'category'
                },
                ],

            });

            $('#front_table_search').on('keyup', function () {
                frontTable.search(this.value).draw();
            });

            $("#submit-filter").click(function () {
                frontTable.ajax.reload();
            });

            $("#reset-filter").click(function () {
                $("#date_from").val('');
                $("#date_to").val('');
                $("#auditor_filter").val('');
                frontTable.ajax.reload();
            });
        });
        $("#genba_date").change(function () {
            document.getElementById('submit-filter').click();

        });

        function emptyStr(str) {
            return !str || !/[^\s]+/.test(str);
        };

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
                url: "{{ route('genba.show_findings') }}",
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
                            '<?php echo env('BASE_URL'); ?>/execution_genba?ref_doc=' +
                            id);
                        activity_table();
                    }, 500)
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
            window.history.pushState('', '', '<?php echo env('BASE_URL'); ?>/execution_genba');
        }
        let streamMap = {}; // Objek untuk menyimpan stream kamera
        // Akses Kamera
        function open_camera() {
            var id = $("#modalSysID").val();
            let video = document.getElementById("video");
            let captureBtn = document.getElementById("captureBtn");
            let openCameraBtn = document.getElementById("openCameraBtn");
            let closeCameraBtn = document.getElementById("closeCameraBtn");
            let photosContainer = document.querySelector('.camera-section')

            let constraints = {
                video: {
                    facingMode: {
                        ideal: "environment"
                    }
                }
            };
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                alert("Browser ini tidak mendukung akses kamera. Coba gunakan Chrome atau Firefox terbaru.");
                return;
            }
            navigator.mediaDevices.getUserMedia(constraints)
                .then(function (mediaStream) {
                    streamMap[id] = mediaStream;
                    video.srcObject = mediaStream;
                    video.style.display = "";
                    captureBtn.style.display = "";
                    openCameraBtn.style.display = "none";
                    closeCameraBtn.style.display = "";
                })
                .catch(function (err) {
                    console.error("Error membuka kamera:", err);
                    alert("Gagal mengakses kamera: " + err.message);
                });
        }

        function activity_table() {
            $('#kt_detailed_table tbody').empty();
            $('#kt_detailed_table').DataTable().destroy();
            var frontTable = $('#kt_detailed_table').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                language: {
                    'processing': '<div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>'
                },
                pageLength: 50,
                info: !1,
                columnDefs: [{
                    orderable: 0,
                    targets: 0
                }],
                ajax: {
                    url: "{{ route('genba.show_findings_list') }}",
                    type: 'POST',
                    data: function (d) {
                        d._token = $("[name=_token]").val(),
                            d.trc_unix_id = $("#temp_id").val()
                    },
                    "error": function (xhr, error, code) {
                        console.log("Error:", error);
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
                    data: 'Path',
                    name: 'Path',
                    render: function (data, type, row) {

                        if (!emptyStr(data)) {
                            var images = data.split(',');
                            var html = '<div class="thumbnail-gallery">';
                            html +=
                                '<button class="btn btn-light-primary btn-sm thumbnail-img" data-images="' +
                                data + '"> <i class="fa fa-camera" ></i> </button></div>';
                            return html;
                        } else {
                            return "";
                        }
                    }
                },
                {
                    data: 'findings',
                    name: 'findings'
                },
                {
                    data: 'area_detail',
                    name: 'area_detail'
                },
                {
                    data: 'auditor',
                    name: 'auditor'
                },
                {
                    data: 'asign_to_dept',
                    name: 'asign_to_dept'
                },
                {
                    data: 'due_date',
                    name: 'due_date',

                },
                {
                    data: 'complete_date',
                    name: 'complete_date',

                }, {
                    data: 'action_plan',
                    name: 'action_plan',
                    render: function (data, type, row) {
                        if (data === 'Complate') {
                            return '<span class="badge badge-success">' + data + '</span>';
                        } else {
                            return '<span class="badge badge-primary">' + data + '</span>';
                        }
                    }
                },
                {
                    data: 'evidence',
                    name: 'evidence',
                    render: function (data, type, row) {
                        if (data === 'Complate') {
                            return '<span class="badge badge-success">' + data + '</span>';
                        } else {
                            return '<span class="badge badge-primary">' + data + '</span>';
                        }
                    }
                },
                {
                    data: 'verified',
                    name: 'verified',
                    render: function (data, type, row) {
                        if (data === 'Complate') {
                            return '<span class="badge badge-success">' + data + '</span>';
                        } else {
                            return '<span class="badge badge-primary">' + data + '</span>';
                        }
                    }
                },
                {
                    data: 'execution_path',
                    name: 'execution_path'
                },

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
        $(document).on('click', '.thumbnail-img', function () {
            var imageData = $(this).data('images');
            if (emptyStr(imageData)) {
                var modalGallery = $('#modalImageContent');
                modalGallery.empty();
                var imgTag = `<div class="col-4 mb-2 text-center">
                                      <span>No Data</span>
                                  </div>`;
                modalGallery.append(imgTag);
                return;
            }
            var images = imageData.split(',');
            var modalGallery = $('#modalImageContent');
            modalGallery.empty();

            images.forEach(function (image) {
                var imageUrl = image.trim();
                var imgTag = `<div class="col-4 mb-2">
                              <img src="storage/app/public/${imageUrl}" class="img-fluid rounded" alt="Image">
                          </div>`;
                modalGallery.append(imgTag);
            });
            $('#imageModal').modal('show');
            $('#imageModal').on('shown.bs.modal', function () {
                window.modalViewer = new Viewer(document.getElementById('modalImageContent'), {
                    toolbar: true,
                    navbar: false,
                    title: false,
                    tooltip: true,
                    movable: true,
                    zoomable: true,
                    scalable: false,
                    transition: true,
                });
            });

            $('#imageModal').on('hidden.bs.modal', function () {
                if (window.modalViewer) {
                    window.modalViewer.destroy();
                    window.modalViewer = null;
                }
            });
        });


        $(document).on('click', '.evidences', function () {
            var evidencesData = $(this).data('evidences');
            var commentData = $(this).data('comment');
            var verification = $(this).data('verfication'); // perbaiki typo 'verfication' kalau bisa jadi 'verification'

            var modalGallery = $('#modalevidencesContent');
            modalGallery.empty();

            if (!evidencesData || evidencesData.trim() === '') {
                modalGallery.append(`
                <div class="col-4 mb-2 text-center">
                    <span>No Data</span>
                </div>
            `);
                $("#evidence_comment").val("");
                $('#evidencesModal').modal('show');
                return;
            }

            var evidences = evidencesData.split(',');

            evidences.forEach(function (evidence) {
                var cleanPath = evidence.trim()
                    .replace(/^\/?nux-app\/storage\/app\/public\//, '')
                    .replace(/^\/?storage\/app\/public\//, '')
                    .replace(/^'/, '')
                    .replace(/'$/, '');

                // ✅ tambahkan slash agar path benar
                var evidencesUrl = `storage/app/public/${cleanPath}`;

                modalGallery.append(`
                <div class="col-4 mb-2">
                    <img src="${evidencesUrl}" class="img-fluid rounded" alt="evidence">
                </div>
            `);
            });

            $("#evidence_comment").val(commentData);
            $('#evidencesModal').modal('show');

            // tampilkan tombol verifikasi
            if (verification === "Need verification") {
                $('#evidencesModal').find('.btn-verification').show();
            } else {
                $('#evidencesModal').find('.btn-verification').hide();
            }

            // Viewer.js aktif setelah modal muncul
            $('#evidencesModal').on('shown.bs.modal', function () {
                // console.log(window.modalViewer);
                // if (window.modalViewer) {

                //     window.modalViewer.destroy();
                // }
                window.modalViewer = new Viewer(document.getElementById('modalevidencesContent'), {
                    toolbar: true,
                    navbar: false,
                    title: false,
                    tooltip: true,
                    movable: true,
                    zoomable: true,
                    scalable: false,
                    transition: true,
                });
            });

            // Hapus viewer saat modal ditutup
            $('#evidencesModal').on('hidden.bs.modal', function () {
                if (window.modalViewer) {
                    window.modalViewer.destroy();
                    window.modalViewer = null;
                }
            });
        });


        function capture() {
            var id = $("#modalSysID").val();

            let video = document.getElementById("video");
            let canvas = document.getElementById("canvas");
            let photoData = document.getElementById("photoData");
            let photosContainer = document.querySelector('.camera-section')
            let fileNamesContainer = document.getElementById('fileNamesContainer');

            let context = canvas.getContext("2d");

            canvas.width = 450;
            canvas.height = 475;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            let imageData = canvas.toDataURL("image/png");

            let fileName = 'photo_' + new Date().getTime() + '.png';
            let fileNameDisplay = document.createElement("div");
            fileNameDisplay.textContent = fileName;

            fileNamesContainer.appendChild(fileNameDisplay);

            let currentPhotos = JSON.parse(photoData.value || "[]");
            currentPhotos.push(fileName);
            photoData.value = JSON.stringify(currentPhotos);

        }

        function close_camera() {
            var id = $("#modalSysID").val();
            let section = document.getElementById('cameraSection');
            let video = document.getElementById("video");
            let captureBtn = document.getElementById("captureBtn");
            let openCameraBtn = document.getElementById("openCameraBtn");
            let closeCameraBtn = document.getElementById("closeCameraBtn");
            let photosContainer = document.querySelector('.camera-section')

            if (streamMap[id]) {
                let tracks = streamMap[id].getTracks();
                tracks.forEach(track => track.stop());
            }

            video.style.display = "none";
            captureBtn.style.display = "none";
            openCameraBtn.style.display = "";
            closeCameraBtn.style.display = "none";
            let currentPhotos = JSON.parse(document.getElementById("photoData").value || '[]');
            section.style.display = 'block';
            // if (currentPhotos.length > 0) {
            save_captured(currentPhotos, id);
            // }
        }

        function toggleCameraSection(id) {
            let section = document.getElementById('cameraSection');
            section.style.display = 'block';
            open_camera();
        }

        function doVerfied() {
            var submitButton = $(".btn_verify_form");
            toggleSubmitSpinner(submitButton, true);

            var trc_unix_id = $("#temp_id").val();
            var _token = $("[name=_token]").val();
            $.ajax({
                type: "POST",
                url: "{{ route('genba.do_verified') }}",
                data: {
                    _token: _token,
                    trc_unix_id: trc_unix_id
                },
                dataType: "json",
                success: function (data) {
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
                    setTimeout(function () {
                        location.reload();
                    }, 1200);
                }
            });
        }

        function save_captured(currentPhotos, id) {
            var execution_comment = $("#execution_comment").val();
            var status_id = $("#status_table").val();
            var modalSysID = $("#modalSysID").val();
            var activity_id = $("input[name='activity_id']").val();
            var scope_id = $("#scope_id_" + id).val();
            var token = $("[name=_token]").val();
            var data = {
                _token: token,
                SysID: modalSysID,
                status_id: status_id,
                execution_comment: execution_comment,
                photos: currentPhotos
            };
            $.ajax({
                type: "post",
                url: "{{ route('genba.post_after_genba') }}",
                data: data,
                success: function (data) { }
            });
        }

        // function document_worksheet(id, no) {
        //     var button = document.getElementById('btn_form_view_doc_' + no);
        //     var svg = document.getElementById('svg_form_view_doc_' + no);
        //     var spinner = document.getElementById('spinner_form_view_doc_' + no);
        //     if (no != 0) {
        //         svg.style.display = 'none';
        //         spinner.style.display = 'inline-block';
        //         button.disabled = true;
        //     }
        //     $("#modalWs").modal('show');
        // }
    </script>
@endsection