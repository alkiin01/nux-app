@extends('../layouts/app')

@section('subhead')
    <title>{{ $head_title }}</title>
    <script type="text/javascript">
        $(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            var ref_doc = urlParams.get('ref_doc');
            if (ref_doc == '' || ref_doc == null) {
                $("#kt_activity_home_tab").addClass('show active');
                window.history.pushState('', '', '<?php echo env('BASE_URL'); ?>/genba_management');
            } else {
                $('#temp_id').val(ref_doc);
                document_preview(ref_doc);
            }
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
            <div id="kt_activity_home" class="card-body p-0 tab-pane fade show active" role="tabpanel"
                aria-labelledby="kt_activity_home_tab">
                <input type="hidden" name="status_id" id="status_id" value="">
                <div class="post d-flex flex-column-fluid" id="kt_post">
                    <div id="kt_content_container" class="container-xxl">
                        <div class="row g-5 g-xl-8 mb-2">
                            <div class="col-xl-6 col-lg-6 col-sm-6">
                                <a href="#" onclick="docSearch(4, this);"
                                    class="card bgi-no-repeat card-xl-stretch mb-xl-8 card-front"
                                    style="background-position: right top; background-size: 30% auto; background-image: url(<?= env('APP_ASSETS') ?>assets/media/svg/shapes/abstract-4.svg)">
                                    <div class="card-body">
                                        <div class="text-gray-900 fw-bolder fs-4 mb-2 mt-2" id="total_check"></div>
                                        <div class="fw-bold text-gray-900">Draft</div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-sm-6">
                                <a href="#" onclick="docSearch(3, this);"
                                    class="card bgi-no-repeat card-xl-stretch mb-xl-8 card-front"
                                    style="background-position: right top; background-size: 30% auto; background-image: url(<?= env('APP_ASSETS') ?>assets/media/svg/shapes/abstract-2.svg)">
                                    <div class="card-body">
                                        <div class="text-gray-900 fw-bolder fs-4 mb-2 mt-2" id="total_approve"></div>
                                        <div class="fw-bold text-gray-900">Done</div>
                                    </div>
                                </a>
                            </div>
                            <button class="" id="submit-filter" name="submit-filter" hidden></button>

                        </div>
                    </div>
                </div>
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
                                            class="form-control form-control-solid w-250px ps-15  text-sm form-control-sm"
                                            placeholder="Search" />
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
                                <table class="table align-middle table-row-dashed table-striped fs-7 gy-3"
                                    id="kt_doc_table">
                                    <thead>
                                        <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                            <th class="min-w-20px pe-2">No</th>
                                            <th class="min-w-100px">DocDate</th>
                                            <th class="min-w-100px">Process</th>
                                            <th class="min-w-100px">Station / Mech. Num</th>
                                            <th class="min-w-100px">Line Checked</th>
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
                                            <th class="min-w-100px">Station / Mech. Num</th>
                                            <th class="min-w-100px">Line Checked</th>
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
    <script>
        $(document).ready(function() {
            var table = $('#kt_doc_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('genba.table_front') }}",
                    type: "POST",
                    data: function(d) {
                        d._token = $("[name=_token]").val(),
                            d.status_id = $("#status_id").val();
                    },
                    cache: false,
                    dataType: 'json',
                    error: function(xhr, error, thrown) {
                        console.log("DataTables Error:", xhr.responseText);
                    }
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
                        data: 'station',
                        name: 'station'
                    }, {
                        data: 'area_checked',
                        name: 'area_checked'
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
                "order": [
                    [1, "desc"]
                ],
                "drawCallback": function(settings) {
                    $(".view_doc").on('click', function() {
                        var doc_id = $(this).data('doc_id');
                        document_preview(doc_id);
                    });
                }
            });



            $("#front_table_search").keyup(function(event) {
                if (event.keyCode == 13) {
                    table.ajax.reload();
                }
            });

            $("#submit-filter").click(function() {
                table.ajax.reload();
            });
        });

        function refreshTable() {
            $('#kt_doc_table').DataTable().ajax.reload();
        }

        function docSearch(id, element) {
            $("#status_id").val(id);
            $('#status_id').val(id).trigger('change');
            document.getElementById('submit-filter').click();
            document.querySelectorAll('.card-front').forEach(function(el) {
                el.classList.remove('bg-light-success');
            });
            element.classList.add('bg-light-success');
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
                success: function(data) {
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


                    setTimeout(function() {
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
            $("#temp_id").val(id);
            svg.style.display = 'none';
            spinner.style.display = 'inline-block';

            button.disabled = true;
            $("#lds-roller-form").css("display", "");
            $("#form").css("display", "none");
            $("#form_loader").css("display", "");
            document.getElementById('kt_activity_preview_tab').click();
            var token = $("[name=_token]").val();
            var trc_unix_id = $("#temp_id").val();
            var data = {
                _token: token,
                trc_unix_id: id
            };
            $.ajax({
                type: "POST",
                url: "{{ route('genba.activity') }}",
                data: data,
                success: function(data) {
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

                    $("#kt_form_attachment").removeClass('show active');
                    $("#kt_form_attachment_tab").removeClass('active');

                    $("#kt_form_preview").removeClass('show active');
                    $("#kt_form_preview_tab").removeClass('active');

                    $("#kt_tag_label").removeClass('show active');
                    $("#kt_tag_label_tab").removeClass('active');

                    window.history.pushState('', '',
                        '<?php echo env('BASE_URL'); ?>/genba_management?ref_doc=' +
                        id);
                    setTimeout(function() {
                        $("#form").css("display", "");
                        $("#form_loader").css("display", "none");
                        $("#lds-roller-form").css("display", "none");

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
            }).then(function(isConfirm) {
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
                success: function(response) {
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
                // document_preview(ref_doc);
            }
        })

        function getDetail(id) {
            $("#pack_line").val(id);
            document.getElementById('kt_form_detail_tab').click();
        };

        let streamMap = {}; // Objek untuk menyimpan stream kamera
        // Akses Kamera
        function open_camera(id) {
            let video = document.getElementById("video_" + id);
            let captureBtn = document.getElementById("captureBtn_" + id);
            let openCameraBtn = document.getElementById("openCameraBtn_" + id);
            let closeCameraBtn = document.getElementById("closeCameraBtn_" + id);
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
                .then(function(mediaStream) {
                    streamMap[id] = mediaStream; // ✅ Simpan stream di objek agar bisa dihentikan nanti
                    video.srcObject = mediaStream;
                    video.style.display = "";
                    captureBtn.style.display = "";
                    openCameraBtn.style.display = "none";
                    closeCameraBtn.style.display = "";
                })
                .catch(function(err) {
                    console.error("Error membuka kamera:", err);
                    alert("Gagal mengakses kamera: " + err.message);
                });
        }

        function capture(id) {
    try {
        // 1. Definisi Element
        let video = document.getElementById("video_" + id);
        let canvas = document.getElementById("canvas_" + id);
        let photoData = document.getElementById("photoData_" + id);
        let photoname = document.getElementById("photoname_" + id);
        let fileNamesContainer = document.getElementById('fileNamesContainer_' + id);
        let captureBtn = document.getElementById("captureBtn_" + id);

        // 2. Proses Capture Gambar
        let context = canvas.getContext("2d");
        canvas.width = 450;
        canvas.height = 475;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        let imageData = canvas.toDataURL("image/png");
        let fileName = 'photo_' + new Date().getTime() + '.png';

        // 3. Update Data JSON (Hidden Input)
        let currentPhotos = JSON.parse(photoData.value || "[]");
        let currentphotoname = JSON.parse(photoname.value || "[]");
        
        currentphotoname.push(fileName);
        currentPhotos.push(imageData);
        
        photoData.value = JSON.stringify(currentPhotos);
        photoname.value = JSON.stringify(currentphotoname);

        // 4. Tampilkan Nama File di List
        let fileNameDisplay = document.createElement("div");
        fileNameDisplay.innerHTML = '<i class="fa fa-check-circle text-success me-2"></i> ' + fileName;
        fileNameDisplay.className = "mb-1 text-dark fw-bold";
        fileNamesContainer.appendChild(fileNameDisplay);

        // -----------------------------------------------------
        // 5. LOGIKA TOMBOL BERUBAH (Ambil -> Tersimpan -> Tambah)
        // -----------------------------------------------------
        
        // A. Efek saat diklik (Hijau & Tersimpan)
        captureBtn.classList.remove('btn-primary');
        captureBtn.classList.add('btn-success');
        captureBtn.innerHTML = '<i class="fa fa-check"></i> Tersimpan!';
        captureBtn.disabled = true; // Disable sebentar

        // B. Kembalikan tombol setelah 1 detik menjadi "Tambah Foto"
        setTimeout(() => {
            captureBtn.classList.remove('btn-success');
            captureBtn.classList.add('btn-primary');
            
            // Text berubah menjadi "Tambah Foto"
            captureBtn.innerHTML = '<i class="fas fa-camera"></i> Tambah Foto';
            
            captureBtn.disabled = false;
        }, 1000);

        // -----------------------------------------------------
        // 6. LOGIKA NOTIFIKASI TOAST (SweetAlert2)
        // -----------------------------------------------------
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Foto berhasil ditambahkan!',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
        } else {
            console.warn("SweetAlert2 belum di-load, toast tidak muncul.");
        }

    } catch (error) {
        console.error("Gagal mengambil foto:", error);
        alert("Terjadi kesalahan sistem. Silakan coba lagi.");
    }
}
        function emptyStr(str) {
            return !str || !/[^\s]+/.test(str);
        };
        function save_captured(id) {
            var currentPhotos = JSON.parse(document.getElementById("photoData_" + id).value || "[]");
            let photoData = document.getElementById("photoname_" + id);
            let photoName = JSON.parse(photoData.value || "[]");
            let dt = document.getElementById("photoData_" + id);

            let files = JSON.parse(dt.value || "[]");
            // Cek apakah ada foto yang ingin disimpan
            // if (files.length === 0) {
            //     alert("Tidak ada foto untuk disimpan.");
            //     return;
            // }
            var activity_id = $("input[name='activity_id']").val();
            var scope_id = $("#scope_id_" + id).val();
            var asign_to = $("#asign_to_" + id).val();
            var asign_to_name = $("#asign_to_" + id).text();
            var asign_to_dept = $("#asign_to_dept_" + id).val();
            var asign_to_dept_name = $("#asign_to_dept_" + id).text();
            var token = $("[name=_token]").val();
            var findings = $("#findings_" + id).val();
            var detail_area = $("#detail_area_" + id).val();
            if( emptyStr(asign_to_dept) || emptyStr(findings)){
                Toast.fire({
                        position: 'top-end',
                        title: "Please fill in all fields mandatory !",
                        icon: "information"
                    });
                    return false;
            }
            var data = {
                _token: token,
                activity_id: activity_id,
                scope_id: scope_id,
                check_item_id: id,
                asign_to: asign_to,
                asign_to_name: asign_to_name,
                asign_to_dept: asign_to_dept,
                asign_to_dept_name: asign_to_dept_name,
                photos: photoName,
                dataphoto: currentPhotos,
                findings: findings,
                detail_area: detail_area
            };
            $.ajax({
                type: "post",
                url: "{{ route('genba.post_photo_spv') }}",
                data: data,
                success: function(data) {
                    Toast.fire({
                        position: 'top-end',
                        title: "Foto berhasil disimpan!",
                        icon: "success"
                    });
                    
                    // $("#modalId_" + id).modal('hide');
                },
                error: function(xhr, status, error) {
                    // Menangani error jika terjadi
                    console.error("Gagal menyimpan foto:", error);
                    alert("Terjadi kesalahan saat menyimpan foto.");
                }
            });
            $("#close_modal").click();
        }

        function close_camera(id) {
            let video = document.getElementById("video_" + id);
            let section = document.getElementById('cameraSection');
            let captureBtn = document.getElementById("captureBtn_" + id);
            let openCameraBtn = document.getElementById("openCameraBtn_" + id);
            let closeCameraBtn = document.getElementById("closeCameraBtn_" + id);
            let photosContainer = document.querySelector('.camera-section')

            if (streamMap[id]) {
                let tracks = streamMap[id].getTracks();
                tracks.forEach(track => track.stop()); // Matikan semua track
            }

            video.style.display = "none";
            captureBtn.style.display = "none";
            openCameraBtn.style.display = "";
            closeCameraBtn.style.display = "none";
            section.style.display = 'none';
            let currentPhotos = JSON.parse(document.getElementById("photoData_" + id).value || '[]');
            let namePhotos = JSON.parse(document.getElementById("photoname_" + id).value || '[]');

            if (currentPhotos.length > 0) {
                save_captured(id);
            }
        }

        function toggleCameraSection(id, scope, id_activity) {
            let section = document.getElementById('cameraSection_' + id);
            section.style.display = 'block';
            // open_camera(id);
            get_data_photo(id, scope, id_activity);
        }

        function get_data_photo(id, scope, id_activity) {
            $('#photo_show_' + id).empty();
            $.ajax({
                type: "POST",
                url: "{{ route('genba.get_data_photo') }}",
                data: {
                    _token: $("[name=_token]").val(),
                    scope_id: scope,
                    activity_id: id_activity,
                    check_item_id: id
                },
                dataType: 'json',
                success: function(data) {
                    if (data.asign_to_dept != null) {
                        var asign_to_dept = new Option(data.asign_to_dept, data.asign_to_dept_name, true, true);
                    } else if (data.asign_to != null) {
                        var asign_to = new Option(data.asign_to, data.asign_to_name, true, true);
                    } else {
                        var asign_to = new Option('', '', true, true);
                        var asign_to_dept = new Option('', '', true, true);
                    }
                    $("#findings_" + id).val(data.findings);
                    $("#detail_area_" + id).val(data.area_detail);
                    $("#asign_to_" + id).append(asign_to).trigger('change');
                    $("#asign_to_dept_" + id).append(asign_to_dept).trigger('change');
                    if (data.photo && data.photo.length > 0) {
                        data.photo.forEach(function(photoPath) {
                            var photoHtml = ` <a href="#" class="text-gray-800 text-hover-primary d-flex flex-column">
                            <div class="symbol symbol-60px mb-5">
                                <img src="<?= env('BASE_URL');?>/storage/app/public/${photoPath}" alt="" />
                            </div>
                            <div class="fs-5 fw-bolder mb-2">
                                ${photoPath} 
                            </div>
                        </a>
                        <div class="fs-7 fw-bold text-gray-400">
                        </div>
                    `;
                            $('#photo_show_' + id).append(photoHtml);
                        });
                    } else {
                        // Jika tidak ada foto, tampilkan pesan
                        $('#photo_show_' + id).html('<p>No photos available.</p>');
                    }
                }
            });

        }


        function uploadImage(id) {
            let inputFile = document.getElementById("uploadImage_" + id);
            let photoData = document.getElementById("photoData_" + id);

            let currentPhotos = JSON.parse(photoData.value || "[]");
            let files = inputFile.files;

            // Proses setiap file yang diunggah
            for (let i = 0; i < files.length; i++) {
                let file = files[i];
                let reader = new FileReader();

                reader.onloadend = function() {
                    // Menyimpan file dalam format base64
                    currentPhotos.push(reader.result);
                    photoData.value = JSON.stringify(currentPhotos); // Menyimpan array base64 ke dalam hidden input
                }

                reader.readAsDataURL(file);
            }
        }

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
            var genba_category = $("#genba_category").val();
            var trc_unix_id = $("#trc_unix_id").val();
            $("#div-form-activity").html("");
            var token = $("[name=_token]").val();
            var station = $("#station").val();
            var process = $("#process").val();
            if (date == "" || area_checked == "" || auditor == "" || genba_category == "" || process == "") {
                svg.style.display = 'inline-block';
                spinner.style.display = 'none';
                buttonText.textContent = 'Next';
                button.disabled = false;
                Swal.fire({
                    text: "Please fill in all fields!",
                    icon: "error",
                    buttonsStyling: !1,
                    confirmButtonText: "Close",
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                })
                return false;
            }

            var data = {
                _token: token,
                date: date,
                area_checked: area_checked,
                auditor: auditor,
                station: station,
                process: process,
                genba_category: genba_category,
                trc_unix_id: trc_unix_id
            };
            $.ajax({
                type: "post",
                url: "{{ route('genba.add_genba') }}",
                data: data,
                cache: false,
                success: function(data) {
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
                    setTimeout(function() {
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
                error: function(jqXHR, textStatus) {
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
            window.history.pushState('', '', '<?php echo env('BASE_URL'); ?>/genba_management');
            // refresh_front_table();
            setTimeout(function() {
                svg.style.display = 'inline-block';
                spinner.style.display = 'none';
                buttonText.textContent = 'Back';
                button.disabled = false;
                document.getElementById('kt_activity_home_tab').click();
            }, 300)
            refreshTable()
        }

        function submit_form() {
            var genba_id = $("#activity_id").val();
            var token = $("[name=_token]").val();
            var no = $("#no").val();
            for (let i = 1; i <= no; i++) {

                var answers = $("[name='answers[" + i + "]']:checked").val()
                console.log(i + ": " + answers);

                // console.log(i + ": " + answers);
                // if (answers == "" || answers == null || answers == undefined) {
                //     Swal.fire({
                //         text: "Please fill in all fields!",
                //         icon: "error",
                //         buttonsStyling: !1,
                //         confirmButtonText: "Close",
                //         customClass: {
                //             confirmButton: "btn btn-primary"
                //         }    
                //     })
                //     return false;
                // }
            }
            var data = {
                _token: token,
                genba_id: genba_id
            };
            $.ajax({
                type: "POST",
                url: "{{ route('genba.submit_form_genba') }}",
                data: data,
                dataType: "json",
                success: function(data) {
                    if (data.code == 200) {
                        Toast.fire({
                            position: 'top-end',
                            title: "Data berhasil disimpan!",
                            icon: "success"
                        })
                        setTimeout(function() {
                            backHome();
                        }, 1000)
                    } else {
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
@endsection
