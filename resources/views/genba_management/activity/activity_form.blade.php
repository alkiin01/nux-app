<style>
    .card-header.sticky-top {
        position: sticky;
        top: 100;
        /* Menjaga header di atas container saat di-scroll */
        z-index: 10;
        /* Menjaga header berada di atas konten */
        background-color: #F1FAFF;
        /* Warna latar belakang header */
        /* Warna teks */
    }
</style>
<div class="col-xxl-12">
    <div id="form_loader" style="text-align: center;">
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
        <div class="card " id="">
            <div class="card-header">
                <div class="card-title">Process Audit</div>
                <div class="card-toolbar">
                    <button class="btn btn-success btn-sm" id="btn_back_home" onclick="backHome()">
                        <span id="svg_back_home" class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <defs />
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <path
                                        d="M3.95709826,8.41510662 L11.47855,3.81866389 C11.7986624,3.62303967 12.2013376,3.62303967 12.52145,3.81866389 L20.0429,8.41510557 C20.6374094,8.77841684 21,9.42493654 21,10.1216692 L21,19.0000642 C21,20.1046337 20.1045695,21.0000642 19,21.0000642 L4.99998155,21.0000673 C3.89541205,21.0000673 2.99998155,20.1046368 2.99998155,19.0000673 L2.99999828,10.1216672 C2.99999935,9.42493561 3.36258984,8.77841732 3.95709826,8.41510662 Z M10,13 C9.44771525,13 9,13.4477153 9,14 L9,17 C9,17.5522847 9.44771525,18 10,18 L14,18 C14.5522847,18 15,17.5522847 15,17 L15,14 C15,13.4477153 14.5522847,13 14,13 L10,13 Z"
                                        fill="#000000" />
                                </g>
                            </svg>
                        </span>
                        <span id="spinner_back_home" class="spinner-border spinner-border-sm svg-icon svg-icon-2"
                            style="display: none;"></span>
                        <span id="btn_text_back_home">Back</span>
                    </button>
                </div>
            </div>
        </div>
        <input type="hidden" name="activity_id" value="{{ $id_activity }}" id="activity_id">
        <?php $no = 0; ?>

        @foreach ($scopes as $scope => $items)
            <div class="card card-custom card-sticky  mb-3 col-md-12 gutter-b mt-5" id="kt_card_<?php echo $no; ?>">
                <div class="card-header">
                    <div class="card-title">{{ $scope }}</div>
                    <div class="card-toolbar">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="popover"
                            data-bs-trigger="click" title="Information" data-bs-html="true"
                            data-bs-content="<span><i class='fa fa-circle text-primary'></i>  Jika telah sesuai dengan persyaratan/ poin cek</span><br><i class='fa fa-exclamation-triangle text-primary'></i> Jika persyaratan/item check sudah dilakukan namun tidak maksimal / tidak konsisten / masih perlu dilakukan improvement<br><i class='fa fa-times text-primary'></i> Jika tidak sesuai dengan persyaratan / poin cek">
                            <i class="fa fa-info-circle"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @foreach ($items as $item)
                        <div class="row mb-5">
                            <label class="col-lg-3 col-form-label ">{{ $item['check_item'] }}<br>
                                <span class="text-muted font-weight-bold font-size-sm pb-4">
                                    {{ $item['check_item_eng'] }}
                                </span>
                            </label>

                            
                            <div class="col-lg-3 justify-content-center align-content-center">
                                <input type="hidden" name="scope_id_{{ $item['check_item_id'] }}"
                                    id= "scope_id_{{ $item['check_item_id'] }}" value="{{ $item['scope_id'] }}">
                                <div class="radio-inline justify-content-center align-content-center p-8">
                                    <input type="radio"
                                        class="form-check-input  form-check-sm"id="radio_{{ $item['check_item_id'] }}_1"
                                        name="answers[{{ $item['check_item_id'] }}]" data-gtm-form-interact-field-id="1"
                                        @if ($item['result'] == 1) checked="checked" @endif value="1">
                                    <label class="svg-icon svg-icon-primary svg-icon-3x form-check-label"
                                        for="radio_{{ $item['check_item_id'] }}_1">
                                        <i class="fa fa-circle text-success fa-2x"></i></label>
                                    <input type="radio" class="form-check-input form-check-sm"
                                        id="radio_{{ $item['check_item_id'] }}_2"
                                        name="answers[{{ $item['check_item_id'] }}]"
                                        data-gtm-form-interact-field-id="2"
                                        @if ($item['result'] == 2) checked="checked" @endif value="2">
                                    <label class="svg-icon svg-icon-primary svg-icon-3x form-check-label"
                                        for="radio_{{ $item['check_item_id'] }}_2">
                                        <i
                                            class="fa fa-exclamation-triangle fa-2x text-warning"></i><!--end::Svg Icon--></label>
                                    <input type="radio" class="form-check-input  form-check-sm"
                                        id="radio_{{ $item['check_item_id'] }}_3"
                                        name="answers[{{ $item['check_item_id'] }}]"
                                        data-gtm-form-interact-field-id="3"
                                        @if ($item['result'] == 3) checked="checked" @endif value="3">
                                    <label class="svg-icon svg-icon-primary svg-icon-3x form-check-label"
                                        for="radio_{{ $item['check_item_id'] }}_3">
                                        <i class="fa fa-times fa-2x text-danger"></i>
                                        <!--end::Svg Icon--></label>

                                </div>
                            </div>
                            <div class="col-lg-2 justify-content-center align-content-center">
                                <button type="button" id="openCameraBtn_{{ $item['check_item_id'] }}"
                                    @if ($item['result'] > 1) style="" @else style=" display:  none; " @endif
                                    class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#modalId_{{ $item['check_item_id'] }}"
                                    onclick="toggleCameraSection({{ $item['check_item_id'] }} , {{ $item['scope_id'] }},{{ $id_activity }})">
                                    <i class="fas fa-camera"></i>Photos
                                </button>
                                {{-- <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#ModalAsign{{ $item['check_item_id'] }}" onclick="">
                                        <i class="fas fa-pencil-alt"></i> Asign To
                                    </button> --}}
                            </div>
                        </div>
                        </button>
                        <!-- if you want to close by clicking outside the modal, delete the last endpoint:data-bs-backdrop and data-bs-keyboard -->
                        <div class="modal fade " id="modalId_{{ $item['check_item_id'] }}" tabindex="-1"
                            data-bs-backdrop="static" data-bs-keyboard="false" role="dialog"
                            aria-labelledby="modalTitleId" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered modal-sm"
                                role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalTitleId">
                                            Findings
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"
                                            onclick="close_camera('{{ $item['check_item_id'] }}')"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row my-3">
                                            <!-- Kamera -->
                                            <div class="camera-section mt-3" style="display: none;"
                                                id="cameraSection_{{ $item['check_item_id'] }}">
                                                <video id="video_{{ $item['check_item_id'] }}" width="100%"
                                                    height="200" autoplay style="display: none;"></video>
                                                <canvas id="canvas_{{ $item['check_item_id'] }}"
                                                    style="display: none;"></canvas>
                                                <img id="photo_{{ $item['check_item_id'] }}" src=""
                                                    alt="Hasil Foto" class="img-fluid mt-2" style="display: none;">
                                                <input type="hidden" name="photo_data[]"
                                                    id="photoData_{{ $item['check_item_id'] }}">
                                                <input type="hidden" name="photo_name[]"
                                                    id="photoname_{{ $item['check_item_id'] }}">
                                                <div class="mt-3">
                                                    <h5>Captured Files:</h5>
                                                    <div id="fileNamesContainer_{{ $item['check_item_id'] }}">
                                                    </div> <!-- Tempat menampilkan nama file -->
                                                </div>
                                            </div>
                                            <!-- Upload Gambar -->
                                            <div class="mt-3">
                                                <input type="file" id="uploadImage_{{ $item['check_item_id'] }}"
                                                    class="form-control" accept="image/*"
                                                    onchange="uploadImage({{ $item['check_item_id'] }})" multiple>
                                            </div>
                                        </div>

                                        <button type="button" id="openCameraBtn_{{ $item['check_item_id'] }}"
                                            class="btn btn-primary"
                                            onclick="open_camera({{ $item['check_item_id'] }})">Open
                                            Camera</button>
                                        <button type="button" id="closeCameraBtn_{{ $item['check_item_id'] }}"
                                            class="btn btn-danger"
                                            onclick="close_camera({{ $item['check_item_id'] }})"
                                            style="display: none;">Tutup Kamera
                                        </button>
                                        <button type="button" id="captureBtn_{{ $item['check_item_id'] }}"
                                            class="btn btn-primary mt-2"
                                            onclick="capture({{ $item['check_item_id'] }})"
                                            style="display: none;">Ambil Foto</button>
                                        <div class="mt-3">
                                            <div id="photo_show_{{ $item['check_item_id'] }}">
                                            </div>
                                            <label for="findings" class="form-label required">Comments</label>
                                            <textarea class="form-control" name="findings" id="findings_{{ $item['check_item_id'] }}" cols="20"
                                                rows="10"></textarea>
                                        </div>
                                        <div class="mt-3">
                                            
                                            <div class="col-lg-6">
                                                <label for="asign_to" class="form-label required">Detailed Area</label>
                                                <p>{{ $process }}</p>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <label for="asign_to_dept" class="form-label required">Asign To
                                                    </label>
                                                    <select type="text"
                                                        name="asign_to_dept_{{ $item['check_item_id'] }}"
                                                        id="asign_to_dept_{{ $item['check_item_id'] }}"
                                                        class="form-control" placeholder="" aria-describedby="helpId"
                                                        data-kt-select2="true" data-placeholder="Select option"
                                                        data-allow-clear="false" data-hide-search="false"
                                                        data-dropdown-parent="#modalId_{{ $item['check_item_id'] }}" ></select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                                            onclick="close_camera('{{ $item['check_item_id'] }}')" id="close_modal">
                                            Close
                                        </button>
                                        <button type="button" class="btn btn-primary"
                                            onclick="save_captured({{ $item['check_item_id'] }})">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <script>
                            $('#asign_to_' + {{ $item['check_item_id'] }}).select2({
                                ajax: {
                                    type: 'POST',
                                    url: "{{ route('genba.get_user_data') }}",
                                    dataType: 'json',
                                    delay: 250, // delay for search
                                    data: function(params) {
                                        var query = {
                                            search: params.term,
                                            _token: $("[name=_token]").val(),
                                            page: params.page || 1
                                        };
                                        return query;
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
                                    cache: false
                                },
                                placeholder: 'Select option',
                            });

                            $('#asign_to_dept_' + {{ $item['check_item_id'] }}).select2({
                                ajax: {
                                    type: 'POST',
                                    url: "{{ route('genba.get_section') }}",
                                    dataType: 'json',
                                    delay: 250, // delay for search
                                    data: function(params) {
                                        var query = {
                                            search: params.term,
                                            _token: $("[name=_token]").val(),
                                            page: params.page || 1
                                        };
                                        return query;
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
                                    cache: false
                                },
                                placeholder: 'Select option',
                            });
                        </script>
                        <?php $no++; ?>
                    @endforeach
                </div>
            </div>
        @endforeach
        <input type="hidden" name="no" id="no" value="{{ $no }}">
        <div class="card " id="">
            <div class="card-header">
                <div class="card-toolbar">
                    <button class="btn btn-success btn-sm" id="btn_submit_form" onclick="submit_form()">
                        <span id="svg_submit_form" class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <defs />
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24" />
                                    <path
                                        d="M6.26193932,17.6476484 C5.90425297,18.0684559 5.27315905,18.1196257 4.85235158,17.7619393 C4.43154411,17.404253 4.38037434,16.773159 4.73806068,16.3523516 L13.2380607,6.35235158 C13.6013618,5.92493855 14.2451015,5.87991302 14.6643638,6.25259068 L19.1643638,10.2525907 C19.5771466,10.6195087 19.6143273,11.2515811 19.2474093,11.6643638 C18.8804913,12.0771466 18.2484189,12.1143273 17.8356362,11.7474093 L14.0997854,8.42665306 L6.26193932,17.6476484 Z"
                                        fill="#000000" fill-rule="nonzero"
                                        transform="translate(11.999995, 12.000002) rotate(-180.000000) translate(-11.999995, -12.000002) " />
                                </g>
                            </svg>
                        </span>
                        <span id="spinner_submit_form" class="spinner-border spinner-border-sm svg-icon svg-icon-2"
                            style="display: none;"></span>
                        <span id="btn_text_submit_form">Submit</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script>
         var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
        var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl, {
                trigger: 'focus',
            })
        })
        $('input[type="radio"]').change(function(e) {

            let itemId = $(this).attr('name').match(/\d+/)[0];
            let selectedValue = $(this).val();
            var scope_id = $("#scope_id_" + itemId).val(); // Ambil nilai yang baru

            var token = $("[name=_token]").val();
            var activity_id = $("input[name='activity_id']").val();
            var data = {
                _token: token,
                activity_id: activity_id,
                scope_id: scope_id,
                check_item_id: itemId,
                answer: selectedValue
            };
            $.ajax({
                type: "post",
                url: "{{ route('genba.post_form_spv') }}",
                data: data,
                dataType: "json",
                success: function(data) {

                }
            });
            if (selectedValue != 1) {
                $("#openCameraBtn_" + itemId).css('display', '');
            } else {
                $("#openCameraBtn_" + itemId).css('display', 'none');

            }
        });


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
