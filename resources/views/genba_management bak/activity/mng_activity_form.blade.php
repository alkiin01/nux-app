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
        @foreach ($scopes as $scope => $items)
            <?php $no = 1; ?>
            <div class="card card-custom card-sticky  mb-3 col-md-12 gutter-b mt-5" id="kt_card_<?php echo $no; ?>">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">{{ $scope }}</div>
                    </div>
                    <div class="card-body">
                        @foreach ($items as $item)
                            <div class="row mb-5">
                                <label class="col-lg-4 col-form-label ">{{ $item['check_item'] }}<br>
                                    <span class="text-muted font-weight-bold font-size-sm pb-4">
                                        {{ $item['check_item_eng'] }}
                                </label>
                                </span>
                                <div class="col-lg-8 ">
                                    <input type="hidden" name="scope_id_{{ $item['check_item_id'] }}"
                                        id= "scope_id_{{ $item['check_item_id'] }}" value="{{ $item['scope_id'] }}">
                                    <div class="radio-inline">
                                        <input type="radio"
                                            class="form-check-input form-check-sm"id="radio_{{ $item['check_item_id'] }}"
                                            name="answers[{{ $item['check_item_id'] }}]"
                                            data-gtm-form-interact-field-id="1"
                                            @if ($item['result'] == 1) checked="checked" @endif value="1">
                                        <span class="svg-icon svg-icon-primary svg-icon-3x form-check-label">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                                viewBox="0 0 24 24" version="1.1">
                                                <defs />
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <rect x="0" y="0" width="24" height="24" />
                                                    <circle fill="#000000" cx="12" cy="12" r="8" />
                                                </g>
                                            </svg><!--end::Svg Icon--></span>
                                        <input type="radio"
                                            class="form-check-input form-check-sm"id="radio_{{ $item['check_item_id'] }}"
                                            name="answers[{{ $item['check_item_id'] }}]"
                                            data-gtm-form-interact-field-id="2"
                                            @if ($item['result'] == 2) checked="checked" @endif value="2">
                                        <span class="svg-icon svg-icon-primary svg-icon-3x form-check-label"><svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                                viewBox="0 0 24 24" version="1.1">
                                                <defs />
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <rect x="0" y="0" width="24" height="24" />
                                                    <path
                                                        d="M3.95428417,19 L20.0457158,19 C20.3218582,19 20.5457158,18.7761424 20.5457158,18.5 C20.5457158,18.3982978 20.5147019,18.2990138 20.4568119,18.215395 L12.4110961,6.59380547 C12.2539131,6.36676337 11.9424371,6.31013137 11.715395,6.46731437 C11.6659703,6.50153145 11.623121,6.54438079 11.5889039,6.59380547 L3.54318807,18.215395 C3.38600507,18.4424371 3.44263707,18.7539131 3.66967918,18.9110961 C3.75329796,18.968986 3.85258194,19 3.95428417,19 Z"
                                                        fill="#000000" />
                                                </g>
                                            </svg><!--end::Svg Icon--></span>
                                        <input type="radio" class="form-check-input form-check-sm"
                                            id="radio_{{ $item['check_item_id'] }}"
                                            name="answers[{{ $item['check_item_id'] }}]"
                                            data-gtm-form-interact-field-id="3"
                                            @if ($item['result'] == 3) checked="checked" @endif value="3">
                                        <span class="svg-icon svg-icon-primary svg-icon-3x form-check-label"><svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                height="24px" viewBox="0 0 24 24" version="1.1">
                                                <defs></defs>
                                                <g stroke="none" stroke-width="1" fill="none"
                                                    fill-rule="evenodd">
                                                    <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)"
                                                        fill="#000000">
                                                        <rect x="0" y="7" width="16" height="2"
                                                            rx="1">
                                                        </rect>
                                                        <rect opacity="0.3"
                                                            transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000) "
                                                            x="0" y="7" width="16" height="2" rx="1">
                                                        </rect>
                                                    </g>
                                                </g>
                                            </svg><!--end::Svg Icon--></span>

                                    </div>
                                    <!-- Modal trigger button -->
                                    <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal"
                                        data-bs-target="#modalId_{{ $item['check_item_id'] }}"
                                        onclick="toggleCameraSection({{ $item['check_item_id'] }})">
                                        <i class="fas fa-camera"></i>Photos
                                    </button>

                                </div>
                            </div>

                            </button>

                            <!-- Modal Body -->
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
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <!-- Kamera -->
                                                <div class="camera-section mt-3" style="display: none;"
                                                    id="cameraSection_{{ $item['check_item_id'] }}">
                                                    <video id="video_{{ $item['check_item_id'] }}" width="100%"
                                                        height="200" autoplay style="display: none;"></video>
                                                    <canvas id="canvas_{{ $item['check_item_id'] }}"
                                                        style="display: none;"></canvas>
                                                    <img id="photo_{{ $item['check_item_id'] }}" src=""
                                                        alt="Hasil Foto" class="img-fluid mt-2"
                                                        style="display: none;">
                                                    <input type="hidden" name="photo_data[]"
                                                        id="photoData_{{ $item['check_item_id'] }}">

                                                    <div class="mt-3">
                                                        <h5>Captured Files:</h5>
                                                        <div id="fileNamesContainer_{{ $item['check_item_id'] }}">
                                                        </div> <!-- Tempat menampilkan nama file -->
                                                    </div>
                                                </div>

                                                <!-- Upload Gambar -->
                                                <div class="mt-3">
                                                    <input type="file"
                                                        id="uploadImage_{{ $item['check_item_id'] }}"
                                                        class="form-control" accept="image/*"
                                                        onchange="uploadImage({{ $item['check_item_id'] }})" multiple>
                                                </div>
                                                <div class="mt-3">
                                                    <label for="findings">Comments</label>
                                                    <textarea class="form-control" name="findings" id="findings_{{ $item['check_item_id'] }}" cols="20"
                                                        rows="10"></textarea>
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
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                                                onclick="close_camera('{{ $item['check_item_id'] }}')">
                                                Close
                                            </button>
                                            <button type="button" class="btn btn-primary"
                                                onclick="save_captured(JSON.parse(document.getElementById('photoData_{{ $item['check_item_id'] }}').value), {{ $item['check_item_id'] }})">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script></script>

                            <!-- Optional: Place to the bottom of scripts -->


                            <?php $no++; ?>
                        @endforeach
                    </div>
                </div>
        @endforeach
    </div>
    <div class="card mt-5" id="">
        <div class="card-header">
            <div class="card-toolbar">
                <button class="btn btn-primary btn-sm" id="btn_back_home" onclick="submitForm()">
                    <span id="svg_back_home" class="svg-icon svg-icon-2">
                        <i class="fa fa-save"></i>
                    </span>
                    <span id="spinner_back_home" class="spinner-border spinner-border-sm svg-icon svg-icon-2"
                        style="display: none;"></span>
                    <span id="btn_text_back_home">Save</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
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
            url: "{{ route('genba.post_form_mng') }}",
            data: data,
            dataType: "json",
            success: function(data) {

            }
        });

        $("#openCameraBtn_" + itemId).prop("disabled", false);
    });
    // This card is lazy initialized using data-card="true" attribute. You can access to the card object as shown below and override its behavior
    "use strict";

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
