@extends('../layouts/app')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.11.3/viewer.min.css" />

<!-- JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.11.3/viewer.min.js"></script>

@section('subhead')
    <title>{{ $head_title }}</title>
    <script type="text/javascript">
        $(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            var ref_doc = urlParams.get('ref_doc');
            if (ref_doc == '' || ref_doc == null) {
                $("#kt_activity_home_tab").addClass('show active');
                window.history.pushState('', '', '<?php echo env('BASE_URL'); ?>/genba_mng_management');
            } else {
                $('#temp_id').val(ref_doc);
                document_preview(ref_doc);
            }
        })
    </script>
@endsection
<style>
    #modalImageContent img {
        cursor: pointer;
        transition: transform 0.2s;
    }

    #modalImageContent img:hover {
        transform: scale(1.05);
    }
</style>
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
                                <!-- <div class="card-toolbar">
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
                                </div> -->
                            </div>
                            <div class="card-body pt-0">
                                <table class="table align-middle table-row-dashed table-striped fs-7 gy-3"
                                    id="kt_doc_table">
                                    <thead>
                                        <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                            <th class="min-w-20px pe-2">No</th>
                                            <th class="min-w-100px">DocNum</th>
                                            <th class="min-w-100px">Findings Pict.</th>
                                            <th class="min-w-100px">DocDate</th>
                                            <th class="min-w-100px">Area Checked</th>
                                            <th class="min-w-100px">Findings</th>
                                            <th class="min-w-100px">Auditor</th>
                                            <th class="min-w-100px">Status</th>
                                            <th class=" min-w-70px">Action</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                            <th class="min-w-20px pe-2">No</th>
                                            <th class="min-w-100px">DocNum</th>
                                            <th class="min-w-100px">Findings Pict.</th>
                                            <th class="min-w-100px">DocDate</th>
                                            <th class="min-w-100px">Area Checked</th>
                                            <th class="min-w-100px">Findings</th>
                                            <th class="min-w-100px">Auditor</th>
                                            <th class="min-w-100px">Status</th>
                                            <th class=" min-w-70px">Action</th>
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
                <div class="d-flex flex-column-fluid">
                    <div id="kt_content_container" class="container-xxl">
                        <div class="card">
                            <div class="card-header card-header-stretch">
                                <div class="card-title d-flex align-items-center">
                                    {{-- <button class="btn btn-primary btn-sm text-sm" style="width: 100px;" onclick="getApprovalForm()">Approve</button> --}}
                                    <div id="button_approve"></div>
                                    <button class="btn btn-success btn-sm text-sm ms-2" style="width: 100px;"
                                        onclick="backHome()">Back</button>
                                </div>
                                <div class="card-body">

                                </div>
                                {{-- <div class="card-toolbar m-0">
                                    <ul class="nav nav-tabs nav-line-tabs nav-stretch fs-6 border-0 fw-bolder"
                                        role="tablist">
                                        <li class="nav-item" role="presentation" onclick="getPreview()">
                                            <a id="kt_activity_file_tab"
                                                class="nav-link tab_preview justify-content-center text-active-gray-800 active"
                                                data-bs-toggle="tab" role="tab" href="#kt_activity_file">Preview</a>
                                        </li>
                                        <li class="nav-item" role="presentation" onclick="getAttachmentList()">
                                            <a id="kt_activity_attachment_tab"
                                                class="nav-link tab_preview justify-content-center text-active-gray-800"
                                                data-bs-toggle="tab" role="tab"
                                                href="#kt_activity_attachment">Attachment</a>
                                        </li>
                                        <li class="nav-item" role="presentation" onclick="getCommentList()">
                                            <a id="kt_activity_comment_tab"
                                                class="nav-link tab_preview justify-content-center text-active-gray-800"
                                                data-bs-toggle="tab" role="tab"
                                                href="#kt_activity_comment">Comment</a>
                                        </li>
                                    </ul>
                                </div> --}}
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="tab-content">
                                    <div id="kt_activity_file" class="card-body tab_preview p-0 tab-pane fade show active"
                                        role="tabpanel" aria-labelledby="kt_activity_file_tab">
                                        <div class="lds-roller mt-20" id="lds-roller">
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>`
                                            <div></div>
                                        </div>
                                        <div id="file_views"></div>

                                        <div id="photo_view"></div>

                                    </div>
                                    <div id="kt_activity_attachment" class="card-body tab_preview p-0 tab-pane fade show"
                                        role="tabpanel" aria-labelledby="kt_activity_attachment_tab"
                                        style="text-align: center">
                                        <div class="lds-roller mt-20" id="lds-roller-attachment">
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                        </div>
                                        <div id="attachment_list"></div>
                                    </div>
                                    <div id="kt_activity_comment" class="card-body tab_preview p-0 tab-pane fade show"
                                        role="tabpanel" aria-labelledby="kt_activity_comment_tab"
                                        style="text-align: center">
                                        <div class="lds-roller mt-20" id="lds-roller-comment">
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                        </div>
                                        <div class="card-body" id="kt_drawer_chat_messenger_body">
                                            <div class="scroll-y me-n5 pe-5" data-kt-element="messages"
                                                data-kt-scroll="true" data-kt-scroll-activate="true"
                                                data-kt-scroll-height="300px"
                                                data-kt-scroll-dependencies="#kt_drawer_chat_messenger_header, #kt_drawer_chat_messenger_footer"
                                                data-kt-scroll-wrappers="#kt_drawer_chat_messenger_body"
                                                data-kt-scroll-offset="0px">
                                                <div id="comment_list"></div>
                                            </div>
                                        </div>

                                        <div class="card-footer pt-4" id="kt_drawer_chat_messenger_footer">
                                            <textarea id="input_comment" class="form-control form-control-flush mb-3" rows="1" data-kt-element="input"
                                                placeholder="Type a message"></textarea>
                                            <div class="d-flex flex-stack">
                                                <div class="d-flex align-items-center me-2">
                                                    <button class="btn btn-sm btn-icon btn-active-light-primary me-1"
                                                        type="button" data-bs-toggle="tooltip" title="Coming soon">
                                                        <i class="bi bi-paperclip fs-3"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-icon btn-active-light-primary me-1"
                                                        type="button" data-bs-toggle="tooltip" title="Coming soon">
                                                        <i class="bi bi-upload fs-3"></i>
                                                    </button>
                                                </div>
                                                <button class="btn btn-primary" type="button" data-kt-element="send"
                                                    onclick="sentComment()">Send</button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="modal fade" id="imageModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="true"
        role="dialog" aria-labelledby="imageModaltitle" aria-hidden="true">
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
                    {{-- <img id="modalImage" src="" alt="Image Preview" class="img-fluid" /> --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>




    <input type="hidden" name="temp_id" id="temp_id" value="">
    <!-- Optional: Place to the bottom of scripts -->


    <script>
        $(document).ready(function() {
            var table = $('#kt_doc_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('genba.front_mng_table') }}",
                    type: "POST",
                    data: function(d) {
                        d._token = $("[name=_token]").val();
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
                    },{
                        data: 'DocNum',
                        name: 'DocNum',
                        orderable: false,
                        searchable: false
                    }, {
                        data: 'path',
                        name: 'path',
                        render: function(data, type, row) {

                            if (!emptyStr(data)) {
                                var images = data.split(',');
                                var html = '<div class="thumbnail-gallery">';
                                html +=
                                    '<button class="btn btn-light-primary btn-sm thumbnail-img" data-images="' +
                                    data +
                                    '" style="text-align: center; width: 40px; height: 35px;"> <i class="fa fa-camera" ></i> </button></div>';
                                return html;
                            } else {
                                return "";
                            }
                        }
                    },
                    {
                        data: 'date',
                        name: 'date'
                    }, {
                        data: 'area_checked',
                        name: 'area_checked'
                    },
                    {
                        data: 'findings',
                        name: 'findings'
                    },
                    {
                        data: 'auditor',
                        name: 'auditor',
                    }, {
                        data: 'status',
                        name: 'status',
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

            $('#front_table_search').on('keyup', function() {
                table.search(this.value).draw();
            });
        });

        function emptyStr(str) {
            return !str || !/[^\s]+/.test(str);
        };
        $(document).on('click', '.thumbnail-img', function() {
            var imageData = $(this).data('images');
            console.log(imageData);
            var images = imageData.split(',');

            var modalGallery = $('#modalImageContent');
            modalGallery.empty();

            images.forEach(function(image) {
                var imageUrl = '<?= env('BASE_URL');?>/storage/app/public/' + image.trim();
                var imgTag = `<div class="col-4 mb-2">
                        <img src="${imageUrl}" class="img-fluid rounded" alt="Image">
                      </div>`;
                modalGallery.append(imgTag);
            });
            $('#imageModal').modal('show');
            $('#imageModal').on('shown.bs.modal', function() {
                // if (window.modalViewer) {
                //     window.modalViewer.destroy(); // Jika sudah ada instance sebelumnya
                // }
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

            // Hapus viewer saat modal ditutup
            $('#imageModal').on('hidden.bs.modal', function() {
                if (window.modalViewer) {
                    window.modalViewer.destroy();
                    window.modalViewer = null;
                }
            });
        });

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
                url: "{{ route('genba.mng_activity') }}",
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
            $("#lds-roller").css("display", "");
            $("#file_views").html("");
            document.getElementById('kt_activity_preview_tab').click();
            var token = $("[name=_token]").val();
            var data = {
                _token: token,
                trc_unix_id: id
            };
            $.ajax({
                type: 'POST',
                url: "{{ route('genba.mng_activity') }}",
                data: data,
                cache: false,
                success: function(data) {
                    $(".tab_preview").removeClass('active');
                    $("#kt_activity_file").addClass('show active');
                    $("#kt_activity_file_tab").addClass('active');
                    $("#temp_id").val(id);
                    $("#file_views").html(data);
                    setTimeout(function() {
                        $("#lds-roller").css("display", "none");
                        window.history.pushState('', '',
                            '<?php echo env('BASE_URL'); ?>/genba_mng_management?ref_doc=' +
                            id);
                    }, 500)
                }
            }).done(function() {
                get_photo(); // Panggil di sini setelah sukses
            });
        }

        function get_photo() {
            var temp_id = $("#temp_id").val();
            var token = $("[name=_token]").val();
            var data = {
                _token: token,
                trc_unix_id: temp_id,
            };
            $.ajax({
                type: 'POST',
                url: "{{ route('genba.get_photo_findings') }}",
                data: data,
                cache: false,
                success: function(data) {

                    $("#photo_view").html(data);
                    $("#photo_view").css("display", "");

                }
            })
        }

        function showAttachment(data, no) {
            window.modalViewer = new Viewer(document.getElementById('findingsPhoto'), {
                toolbar: true,
                navbar: false,
                title: false,
                tooltip: true,
                movable: true,
                zoomable: true,
                scalable: false,
                transition: true,
            });
        }

        function backHome() {
            // document.getElementById('submit-filter').click();
            document.getElementById('kt_activity_home_tab').click();
            $("#temp_id").val('');
            window.history.pushState('', '', '<?php echo env('BASE_URL'); ?>/genba_mng_management');
        }

        function getPreview() {

        }

        function saveAction() {
            var currentPhotos = JSON.parse(document.getElementById("photoData").value || "[]");
            var action_plan = $("#findings").val();
            var temp_id = $("#temp_id").val();
            var token = $("[name=_token]").val();
            var data = {
                _token: token,
                trc_unix_id: temp_id,
                action_plan: action_plan,
                dataphoto: currentPhotos
            };
            $.ajax({
                type: "POST",
                url: "{{ route('genba.save_action_plan') }}",
                data: data,
                success: function(data) {
                    if (data.code == 200) {
                        Toast.fire({
                            position: 'top-end',
                            title: "Data berhasil disimpan!",
                            icon: "success"
                        })
                        $("#modalAction").modal('hide');
                        refreshTable();
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
        // function document_preview(id, no) {
        //     var button = document.getElementById('btn_form_view_doc_' + no);
        //     var svg = document.getElementById('svg_form_view_doc_' + no);
        //     var spinner = document.getElementById('spinner_form_view_doc_' + no);
        //     $("#temp_id").val(id);
        //     svg.style.display = 'none';
        //     spinner.style.display = 'inline-block';

        //     // button.disabled = true;
        //     // $("#lds-roller-form").css("display", "");
        //     // $("#form").css("display", "none");
        //     // $("#form_loader").css("display", "");
        //     // document.getElementById('kt_activity_preview_tab').click();
        //     var token = $("[name=_token]").val();
        //     var trc_unix_id = $("#temp_id").val();
        //     var data = {
        //         _token: token,
        //         trc_unix_id: id
        //     };
        //     let section = document.getElementById('cameraSection');
        //     section.style.display = 'block';
        //     // open_camera(id);
        //     // $("#modalAction").modal('show');
        //     $.ajax({
        //         type: "POST",
        //         url: "{{ route('genba.mng_activity') }}",
        //         data: data,
        //         success: function(data) {
        //             if (no > 0) {
        //                 svg.style.display = 'inline-block';
        //                 spinner.style.display = 'none';
        //                 button.disabled = false;

        //             }
        //             $("#div_form").html(data);
        //             $("#kt_form_header").addClass('show active');
        //             $("#kt_form_header_tab").addClass('active');

        //             $("#kt_form_detail").removeClass('show active');
        //             $("#kt_form_detail_tab").removeClass('active');

        //             $("#kt_form_attachment").removeClass('show active');
        //             $("#kt_form_attachment_tab").removeClass('active');

        //             $("#kt_form_preview").removeClass('show active');
        //             $("#kt_form_preview_tab").removeClass('active');

        //             $("#kt_tag_label").removeClass('show active');
        //             $("#kt_tag_label_tab").removeClass('active');


        //             setTimeout(function() {
        //                 $("#form").css("display", "");
        //                 $("#form_loader").css("display", "none");
        //                 $("#lds-roller-form").css("display", "none");
        //             }, 500)
        //         }
        //     });
        // }

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
                url: "{{ route('genba.delete_mng_genba') }}",
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
            let section = document.getElementById('cameraSection');
            section.style.display = 'block';
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
            let video = document.getElementById("video");
            let canvas = document.getElementById("canvas");
            let photoData = document.getElementById("photoData");
            let photosContainer = document.querySelector('.camera-section')
            let fileNamesContainer = document.getElementById('fileNamesContainer'); // Kontainer untuk nama file

            let context = canvas.getContext("2d");

            // Mengatur ukuran canvas sama dengan ukuran video
            canvas.width = 450;
            canvas.height = 475;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            let imageData = canvas.toDataURL("image/png");

            // Menyimpan hanya nama file foto yang diambil
            let fileName = 'photo_' + new Date().getTime() + '.png'; // Menyusun nama file
            let fileNameDisplay = document.createElement("div");
            fileNameDisplay.textContent = fileName; // Menampilkan nama file di dalam div

            // Menambahkan nama file ke dalam container di dalam modal
            fileNamesContainer.appendChild(fileNameDisplay);

            // Menambahkan nama file ke dalam array (agar bisa dikirimkan ke server)
            let currentPhotos = JSON.parse(photoData.value || "[]");
            currentPhotos.push(fileName);
            photoData.value = JSON.stringify(currentPhotos);

        }

        function close_camera(id) {
            let video = document.getElementById("video");
            let captureBtn = document.getElementById("captureBtn");
            let openCameraBtn = document.getElementById("openCameraBtn");
            let closeCameraBtn = document.getElementById("closeCameraBtn");
            let photosContainer = document.querySelector('.camera-section')
            let section = document.getElementById('cameraSection');

            if (streamMap[id]) {
                let tracks = streamMap[id].getTracks();
                tracks.forEach(track => track.stop()); // Matikan semua track
            }
            section.style.display = 'block';
            video.style.display = "none";
            captureBtn.style.display = "none";
            openCameraBtn.style.display = "";
            closeCameraBtn.style.display = "none";
            let currentPhotos = JSON.parse(document.getElementById("photoData").value || '[]');
            // if (currentPhotos.length > 0) {
            //     save_captured(currentPhotos, id);
            // }
        }

        function toggleCameraSection(id) {
            let section = document.getElementById('cameraSection');
            section.style.display = 'block';
            open_camera(id);
        }

        function save_captured(currentPhotos, id) {
            var activity_id = $("input[name='activity_id']").val();
            var scope_id = $("#scope_id").val();
            var token = $("[name=_token]").val();
            var findings = $("#findings").val();
            var data = {
                _token: token,
                activity_id: activity_id,
                scope_id: scope_id,
                check_item_id: id,
                photos: currentPhotos,
                findings: findings
            };
            $.ajax({
                type: "post",
                url: "{{ route('genba.post_photo_mng') }}",
                data: data,
                success: function(data) {
                    Toast.fire({
                        position: 'top-end',
                        title: "Foto berhasil disimpan!",
                        icon: "success"
                    });
                    $("#close_modal").click();
                    $("#modalId").modal('hide');
                },
                error: function(xhr, status, error) {
                    // Menangani error jika terjadi
                    console.error("Gagal menyimpan foto:", error);
                    alert("Terjadi kesalahan saat menyimpan foto.");
                }
            });
        }

        function uploadImage() {
            let inputFile = document.getElementById("uploadImage");
            let photoData = document.getElementById("photoData");

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
                url: "{{ route('genba.add_mng_genba') }}",
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

        function submitForm() {
            var trc_unix_id = $("#trc_unix_id").val();
            var token = $("[name=_token]").val();

            var data = {
                _token: token,
                status: status,
                trc_unix_id: trc_unix_id
            };
            $.ajax({
                type: "POST",
                url: "{{ route('genba.submit_form_mng') }}",
                data: data,
                success: function(data) {
                    Toast.fire({
                        position: 'top-end',
                        title: "Data berhasil dihapus!",
                        icon: "success"
                    })
                    svg.style.display = 'inline-block';
                    spinner.style.display = 'none';
                    backHome();
                }
            });
        }


        var KTLayoutStickyCard = function() {
            // Private properties
            var _element;
            var _object;

            // Private functions
            var _init = function() {
                var offset = 300;

                if (typeof KTLayoutHeader !== 'undefined') {
                    offset = KTLayoutHeader.getHeight();
                }

                _object = new KTCard(_element, {
                    sticky: {
                        offset: offset,
                        zIndex: 90,
                        position: {
                            top: function() {
                                var pos = 0;
                                var body = KTUtil.getBody();

                                if (KTUtil.isBreakpointUp('lg')) {
                                    if (typeof KTLayoutHeader !== 'undefined' && KTLayoutHeader
                                        .isFixed()) {
                                        pos = pos + KTLayoutHeader.getHeight();
                                    }

                                    if (typeof KTLayoutSubheader !== 'undefined' && KTLayoutSubheader
                                        .isFixed()) {
                                        pos = pos + KTLayoutSubheader.getHeight();
                                    }
                                } else {
                                    if (typeof KTLayoutHeader !== 'undefined' && KTLayoutHeader
                                        .isFixedForMobile()) {
                                        pos = pos + KTLayoutHeader.getHeightForMobile();
                                    }
                                }

                                pos = pos - 1; // remove header border width

                                return pos;
                            },
                            left: function(card) {
                                return KTUtil.offset(_element).left;
                            },
                            right: function(card) {
                                var body = KTUtil.getBody();

                                var cardWidth = parseInt(KTUtil.css(_element, 'width'));
                                var bodyWidth = parseInt(KTUtil.css(body, 'width'));
                                var cardOffsetLeft = KTUtil.offset(_element).left;

                                return bodyWidth - cardWidth - cardOffsetLeft;
                            }
                        }
                    }
                });

                _object.initSticky();

                KTUtil.addResizeHandler(function() {
                    _object.updateSticky();
                });
            }

            // Public methods
            return {
                init: function(id) {
                    _element = KTUtil.getById(id);

                    if (!_element) {
                        return;
                    }

                    // Initialize
                    _init();
                },

                update: function() {
                    if (_object) {
                        _object.updateSticky();
                    }
                }
            };
        }();

        // Webpack support
        if (typeof module !== 'undefined') {
            module.exports = KTLayoutStickyCard;
        }
    </script>
@endsection
