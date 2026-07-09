<input type="hidden" name="_token" value="{{ csrf_token() }}">
@php
    $requestStatus = $RequestStatus ?? 'DRAFT';
    $canEditApproved = !empty($canUpdateQtySubmit) && in_array($requestStatus, ['APPROVED', 'AUTO_APPROVED']);
    $isEditable = in_array($requestStatus, ['DRAFT', 'REJECTED']);
    $statusClass = [
        'DRAFT' => 'badge-light-primary',
        'PENDING_APPROVAL' => 'badge-light-warning',
        'APPROVED' => 'badge-light-success',
        'AUTO_APPROVED' => 'badge-light-success',
        'COMPLETED' => 'badge-light-info',
        'REJECTED' => 'badge-light-danger',
        'CANCELLED' => 'badge-light-dark',
    ][$requestStatus] ?? 'badge-light-secondary';
@endphp

<script type="text/javascript">
    var detailTableInstance = null;

    function clear_detail_table_state() {
        if ($.fn.DataTable.isDataTable('#kt_form_table')) {
            $('#kt_form_table').DataTable().clear().destroy();
        }

        detailTableInstance = null;
        $('#kt_form_table tbody').empty();
    }

    $(document).ready(function() {
        var DocNum = $("#InptDocNum").val();
        if (DocNum == "") {
            clear_detail_table_state();
            var token = $("[name=_token]").val();
            var DocDate = $("#InptDocDate").val();
            var string = "&_token=" + token + "&DocDate=" + DocDate;
            $.ajax({
                type: 'POST',
                url: "{{ route('issue_miscellaneous.get_new_docnum') }}",
                data: string,
                cache: false,
                dataType: 'json',
                success: function(data) {
                    $("#InptDocNum").val(data.DocNum);
                    clear_detail_table_state();
                    apply_form_state();
                    detail_table();
                },
                error: function(jqXHR, textStatus) {
                    Toast.fire({
                        position: 'top-end',
                        title: " Please reload and tr again! ",
                        icon: "error"
                    })
                }
            })
        } else {
            detail_table();
        }
    })
</script>
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
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <div class="d-flex align-items-center position-relative my-1">
                        <button type="button" class="btn btn-primary btn-sm me-2" id="btn_submit_form"
                            onclick="submit_form()">
                            <span id="svg_submit_form" class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px"
                                    viewBox="0 0 24 24">
                                    <g stroke="none" fill="none">
                                        <polygon points="0 0 24 0 24 24 0 24" />
                                        <path
                                            d="M17,4 L6,4 C4.79111111,4 4,4.7 4,6 L4,18 C4,19.3 4.79111111,20 6,20 L18,20 C19.2,20 20,19.3 20,18 L20,7.20710678 C20,7.07449854 19.9473216,6.94732158 19.8535534,6.85355339 L17,4 Z M17,11 L7,11 L7,4 L17,4 L17,11 Z"
                                            fill="#000000" />
                                        <rect fill="#000000" opacity="0.3" x="12" y="4" width="3" height="5"
                                            rx="0.5" />
                                    </g>
                                </svg>
                            </span>
                            <span id="spinner_submit_form" class="spinner-border spinner-border-sm align-middle ms-2"
                                style="display: none;"></span>
                            <span id="btn_text_submit_form">Save</span>
                        </button>
                        <button type="button" class="btn btn-success btn-sm" id="btn_submit_document"
                            onclick="submit_document()">
                            <span id="svg_submit_document" class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M15.43 8.56949L10.744 15.1395C10.6422 15.282 10.5804 15.4492 10.5651 15.6236C10.5498 15.7981 10.5815 15.9734 10.657 16.1315L13.194 21.4425C13.2737 21.6097 13.3991 21.751 13.5557 21.8499C13.7123 21.9488 13.8938 22.0014 14.079 22.0015H14.117C14.3087 21.9941 14.4941 21.9307 14.6502 21.8191C14.8062 21.7075 14.9261 21.5526 14.995 21.3735L21.933 3.33649C22.0011 3.15918 22.0164 2.96594 21.977 2.78013C21.9376 2.59432 21.8452 2.4239 21.711 2.28949L15.43 8.56949Z" fill="black"/>
                                    <path opacity="0.3" d="M20.664 2.06648L2.62602 9.00148C2.44768 9.07085 2.29348 9.19082 2.1824 9.34663C2.07131 9.50244 2.00818 9.68731 2.00074 9.87853C1.99331 10.0697 2.04189 10.259 2.14054 10.4229C2.23919 10.5869 2.38359 10.7185 2.55601 10.8015L7.86601 13.3365C8.02383 13.4126 8.19925 13.4448 8.37382 13.4297C8.54839 13.4145 8.71565 13.3526 8.85801 13.2505L15.43 8.56548L21.711 2.28448C21.5762 2.15096 21.4055 2.05932 21.2198 2.02064C21.034 1.98196 20.8409 1.99788 20.664 2.06648Z" fill="black"/>
                                </svg>
                            </span>
                            <span id="spinner_submit_document" class="spinner-border spinner-border-sm align-middle ms-2"
                                style="display: none;"></span>
                            <span id="btn_text_submit_document">Submit Approval</span>
                        </button>
                        <button type="button" class="btn btn-info btn-sm ms-2" id="btn_update_qty_submit"
                            onclick="open_qty_submit_modal()" style="display: none;">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M8 10C8 8.89543 8.89543 8 10 8H20C21.1046 8 22 8.89543 22 10V20C22 21.1046 21.1046 22 20 22H10C8.89543 22 8 21.1046 8 20V10Z" fill="black"/>
                                    <path opacity="0.3" d="M2 4C2 2.89543 2.89543 2 4 2H14C15.1046 2 16 2.89543 16 4V14C16 15.1046 15.1046 16 14 16H4C2.89543 16 2 15.1046 2 14V4Z" fill="black"/>
                                </svg>
                            </span>
                            <span id="btn_text_update_qty_submit">Update Qty Submit</span>
                        </button>
                        <span class="badge {{ $statusClass }} ms-3" id="request_status_badge">{{ $requestStatus }}</span>
                    </div>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-secondary btn-sm me-2" id="btn_back" onclick="back_to_list()">
                        <span class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M9.60001 11H21C21.6 11 22 11.4 22 12C22 12.6 21.6 13 21 13H9.60001V15.6C9.60001 16.1523 8.9277 16.4308 8.53432 16.0474L4.53432 12.2C4.20722 11.8856 4.20723 11.3702 4.53435 11.0558L8.53435 7.20576C8.92773 6.82229 9.60001 7.10075 9.60001 7.653V11Z" fill="black"/>
                            </svg>
                        </span>
                        Back
                    </button>
                    <button class="btn btn-danger btn-sm" id="btn_cancel_doc" onclick="cancel_document()">
                        <span id="svg_cancel_doc" class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path opacity="0.3" d="M6 19.7C5.7 19.7 5.5 19.6 5.3 19.4C4.9 19 4.9 18.4 5.3 18L18 5.3C18.4 4.9 19 4.9 19.4 5.3C19.8 5.7 19.8 6.29999 19.4 6.69999L6.7 19.4C6.5 19.6 6.3 19.7 6 19.7Z" fill="black"/>
                                <path d="M18.8 19.7C18.5 19.7 18.3 19.6 18.1 19.4L5.40001 6.69999C5.00001 6.29999 5.00001 5.7 5.40001 5.3C5.80001 4.9 6.40001 4.9 6.80001 5.3L19.5 18C19.9 18.4 19.9 19 19.5 19.4C19.3 19.6 19 19.7 18.8 19.7Z" fill="black"/>
                            </svg>
                        </span>
                        <span id="spinner_cancel_doc" class="spinner-border spinner-border-sm svg-icon svg-icon-2"
                            style="display: none;"></span>
                        <span id="btn_text_cancel_doc">Cancel</span>
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if ($requestStatus === 'REJECTED' && !empty($RejectedReason ?? ''))
                    <div class="alert alert-danger d-flex align-items-start mb-5">
                        <span class="svg-icon svg-icon-2hx svg-icon-danger me-3 mt-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="black" />
                                <rect x="11" y="7" width="2" height="7" rx="1" fill="black" />
                                <rect x="11" y="16" width="2" height="2" rx="1" fill="black" />
                            </svg>
                        </span>
                        <div>
                            <div class="fw-bolder mb-1">Rejected Reason</div>
                            <div>{{ $RejectedReason }}</div>
                        </div>
                    </div>
                @endif

                <div class="row mb-5">
                    <div class="col-md-6">
                        <form id="form-segment-1">
                            <div class="form-group mb-3">
                                <label>Transaction Type <span class="text-danger">*</span></label>
                                <select class="form-select form-select-solid" data-kt-select2="true"
                                    data-placeholder="Select option" data-allow-clear="false" id="TransactionType"
                                    name="TransactionType" data-hide-search="false">
                                    <option value=""></option>
                                    <option value="ST" {{ ($TransactionType ?? '') === 'ST' ? 'selected' : '' }}>Stationary (ST)</option>
                                    <option value="TR" {{ ($TransactionType ?? '') === 'TR' ? 'selected' : '' }}>Tool Room (TR)</option>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label>Reason Code <span class="text-danger">*</span></label>
                                <select class="form-select form-select-solid" data-kt-select2="true"
                                    data-placeholder="Select reason code" data-allow-clear="false" id="ReasonCode"
                                    name="ReasonCode" data-hide-search="false">
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label>Approved By <span class="text-danger">*</span></label>
                                <select class="form-select form-select-solid" data-kt-select2="true"
                                    data-placeholder="Select approver" data-allow-clear="false" id="ApprovedBy"
                                    name="ApprovedBy" data-hide-search="false">
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label>Category <span class="text-danger">*</span></label>
                                <select class="form-select form-select-solid" data-kt-select2="true"
                                    data-placeholder="Select option" data-allow-clear="false" id="Category"
                                    name="Category" data-hide-search="false">
                                <option value=""></option>

                                <option value="INV3" {{ ($Category ?? '') === 'INV3' ? 'selected' : '' }}>Store Room (SR)</option>
                                <option value="INV6" {{ ($Category ?? '') === 'INV6' ? 'selected' : '' }}>General Affairs (GA) </option>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label>Bin</label>
                                <select class="form-select form-select-solid" data-kt-select2="true"
                                    data-placeholder="Select option" data-allow-clear="false" id="ToBinID"
                                    name="ToBinID" data-hide-search="false">
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label>Lot</label>
                                <input type="text" class="form-control bg-light-primary" id="LotNum"
                                    name="LotNum" value="A" readonly />
                            </div>
                        </form>
                    </div>

                    <div class="col-md-6">
                        <form id="form-segment-2">
                            <div class="form-group mb-3">
                                <label>Part Num <span class="text-danger">*</span></label>
                                <select class="form-select form-select-solid" data-kt-select2="true"
                                    data-placeholder="Select option" data-allow-clear="false" id="InptPartNum"
                                    name="InptPartNum" data-hide-search="false">
                                </select>

                            </div>
                            <div class="form-group mb-3">
                                <label>Qty</label>
                                <input type="text" class="form-control bg-light-primary" id="InptQty"
                                    name="InptQty" />
                            </div>
                            <div class="form-group mb-3">
                                <label>UOM</label>
                                <input type="text" class="form-control bg-light-primary" id="InptUOM"
                                    name="InptUOM" />
                            </div>
                            <div class="form-group mb-3">
                                <label>Stock</label>
                                <input type="text" class="form-control bg-light-primary" id="InptOnhandQty"
                                    name="InptOnhandQty" readonly />
                            </div>
                            <div class="form-group mb-3">
                                <label>Reference</label>
                                <input type="text" class="form-control" id="InptReference"
                                    name="InptReference" placeholder="Enter reference" />
                            </div>
                        </form>
                    </div>
                </div>
                <hr>
                <div class="row mb-5">
                    <div class="col-md-6">
                        <form id="form-segment-3">
                            <div class="form-group mb-3">
                                <label>DocNum <span class="text-danger">*</span></label>
                                <input type="text" class="form-control bg-light-primary" id="InptDocNum"
                                    name="InptDocNum" value="{{ $DocNum ?? '' }}" readonly />
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <form id="form-segment-4">
                            <div class="form-group mb-5">
                                <label>Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="InptDocDate" name="InptDocDate"
                                    value="{{ $DocDate ?? date('Y-m-d') }}" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-5" id="detail_table_card">
            <div class="card-header border-1 pt-6 pb-6 mb-5">
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
                        <input type="text" data-kt-goodreceive-table-filter="search" id="detail_table_search"
                            class="form-control form-control-solid w-250px ps-15 text-sm form-control-sm"
                            placeholder="Search PartNum" />
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table class="table align-middle table-row-dashed table-striped gy-2 fs-7" id="kt_form_table">
                    <thead>
                        <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                            <th class="min-w-20px pe-2">No</th>
                            <th class="min-w-20px pe-2">Delete</th>
                            <th class="min-w-20px">PartNum</th>
                            <th class="min-w-120px">PartName</th>
                            <th class="min-w-80px">Qty Request</th>
                            <th class="min-w-80px">Qty Submit</th>
                            <th class="min-w-20px">From WH</th>
                            <th class="min-w-100px">Bin</th>
                            <th class="min-w-100px">Reference</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                            <th class="min-w-20px pe-2">No</th>
                            <th class="min-w-20px pe-2">Delete</th>
                            <th class="min-w-20px">PartNum</th>
                            <th class="min-w-120px">PartName</th>
                            <th class="min-w-80px">Qty Request</th>
                            <th class="min-w-80px">Qty Submit</th>
                            <th class="min-w-20px">From WH</th>
                            <th class="min-w-100px">Bin</th>
                            <th class="min-w-100px">Reference</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<input type="text" name="CategoryFromTag" id="CategoryFromTag" value="" hidden />
<input type="text" name="InptPartName" id="InptPartName" value="" hidden />
<input type="text" name="InptReference" id="InptReference" value="" hidden />
<input type="text" name="RequestStatus" id="RequestStatus" value="{{ $requestStatus }}" hidden />
<input type="text" name="CanUpdateQtySubmit" id="CanUpdateQtySubmit" value="{{ !empty($canUpdateQtySubmit) ? '1' : '0' }}" hidden />

<!-- Submit Modal -->
<div class="modal fade" tabindex="-1" id="modal_submit_document">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Qty Submit</h5>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                    aria-label="Close">
                    <span class="svg-icon svg-icon-2x">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                transform="rotate(-45 6 17.3137)" fill="black" />
                            <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                transform="rotate(45 7.41422 6)" fill="black" />
                        </svg>
                    </span>
                </div>
            </div>
            <div class="modal-body">
                <div class="alert alert-info d-flex align-items-center p-3 mb-5">
                    <span class="svg-icon svg-icon-2hx svg-icon-info me-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="black"/>
                            <rect x="11" y="14" width="7" height="2" rx="1" transform="rotate(-90 11 14)" fill="black"/>
                            <rect x="11" y="17" width="2" height="2" rx="1" transform="rotate(-90 11 17)" fill="black"/>
                        </svg>
                    </span>
                    <div class="d-flex flex-column">
                        <span class="fs-7">Isi Qty Submit bertahap. Nilai wajib lebih dari 0 dan tidak boleh melebihi Qty Request.</span>
                    </div>
                </div>
                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4" id="submit_modal_table">
                    <thead>
                        <tr class="fw-bolder text-muted">
                            <th class="min-w-50px">No</th>
                            <th class="min-w-150px">PartNum</th>
                            <th class="min-w-200px">Part Name</th>
                            <th class="min-w-100px text-end">Qty Request</th>
                            <th class="min-w-100px text-end">Qty Submit</th>
                        </tr>
                    </thead>
                    <tbody id="submit_modal_tbody">
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary btn-sm" id="btn_execute_submit" onclick="execute_update_qty_submit()">
                    <span class="svg-icon svg-icon-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="black"/>
                            <path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM16 12C16 11.4 15.6 11 15 11H13V9C13 8.4 12.6 8 12 8C11.4 8 11 8.4 11 9V11H9C8.4 11 8 11.4 8 12C8 12.6 8.4 13 9 13H11V15C11 15.6 11.4 16 12 16C12.6 16 13 15.6 13 15V13H15C15.6 13 16 12.6 16 12Z" fill="black"/>
                        </svg>
                    </span>
                    <span id="spinner_execute_submit" class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;"></span>
                    <span id="btn_text_execute_submit">Save Qty Submit</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function parse_numeric_value(value) {
        if (value === null || value === undefined || value === '') {
            return NaN;
        }

        if (typeof value === 'number') {
            return value;
        }

        let normalizedValue = value.toString().trim();

        if (normalizedValue.includes(',') && normalizedValue.includes('.')) {
            if (normalizedValue.lastIndexOf(',') > normalizedValue.lastIndexOf('.')) {
                normalizedValue = normalizedValue.replace(/\./g, '').replace(',', '.');
            } else {
                normalizedValue = normalizedValue.replace(/,/g, '');
            }
        } else if (normalizedValue.includes(',')) {
            normalizedValue = normalizedValue.replace(',', '.');
        }

        return parseFloat(normalizedValue);
    }

    function format_decimal_value(value) {
        const numericValue = parse_numeric_value(value);

        if (!isFinite(numericValue)) {
            return '0.00';
        }

        return numericValue.toFixed(2);
    }

    function is_form_editable() {
        return $('#RequestStatus').val() === 'DRAFT' || $('#RequestStatus').val() === 'REJECTED';
    }

    function is_submitter_approved_edit() {
        const status = $('#RequestStatus').val();
        const canUpdateQtySubmit = $('#CanUpdateQtySubmit').val() === '1';

        return canUpdateQtySubmit && (status === 'APPROVED' || status === 'AUTO_APPROVED');
    }

    function can_edit_header() {
        return is_form_editable() || is_submitter_approved_edit();
    }

    function can_edit_detail() {
        return is_form_editable() || is_submitter_approved_edit();
    }

    function apply_form_state() {
        const status = $('#RequestStatus').val();
        const editable = is_form_editable();
        const approvedSubmitterEdit = is_submitter_approved_edit();
        const headerEditable = editable || approvedSubmitterEdit;
        const hasDocNum = $('#InptDocNum').val() !== '';
        const canUpdateQtySubmit = $('#CanUpdateQtySubmit').val() === '1';
        const canOpenQtySubmit = canUpdateQtySubmit && hasDocNum && (status === 'APPROVED' || status === 'AUTO_APPROVED');

        $('#TransactionType').prop('disabled', !headerEditable);
        $('#ReasonCode').prop('disabled', !headerEditable);
        $('#ApprovedBy').prop('disabled', !headerEditable);
        $('#Category').prop('disabled', !headerEditable);
        $('#ToBinID').prop('disabled', !headerEditable);
        $('#InptPartNum').prop('disabled', !editable);
        $('#InptQty').prop('readonly', !editable);
        $('#InptDocDate').prop('readonly', !headerEditable);
        $('#btn_submit_form').prop('disabled', !headerEditable);
        $('#btn_submit_document').toggle(editable && hasDocNum);
        $('#btn_cancel_doc').toggle(editable && hasDocNum);
        $('#btn_update_qty_submit').toggle(canOpenQtySubmit);

        if (approvedSubmitterEdit) {
            $('#btn_text_submit_form').text('Update Header');
        } else if (!editable) {
            $('#btn_text_submit_form').text('Locked');
        } else {
            $('#btn_text_submit_form').text('Save');
        }

        $('#request_status_badge').text(status);
    }

    $(document).ready(function() {
        const initialReasonCode = @json($ReasonCode ?? '');
        const initialApprovedBy = @json($ApprovedBy ?? '');

        $('#TransactionType').select2({
            placeholder: 'Select option',
            allowClear: false,
        });

        $('#ReasonCode').select2({
            placeholder: 'Select reason code',
            allowClear: false,
        });

        $('#ApprovedBy').select2({
            placeholder: 'Select approver',
            allowClear: false,
            ajax: {
                type: 'POST',
                url: "{{ route('issue_miscellaneous.approval_users') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        _token: $("[name=_token]").val(),
                        search: params.term || ''
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data.items || [], function(item) {
                            return {
                                id: item.id,
                                text: item.text
                            };
                        })
                    };
                },
                cache: true
            }
        });

        if (initialApprovedBy) {
            $.ajax({
                type: 'POST',
                url: "{{ route('issue_miscellaneous.approval_users') }}",
                dataType: 'json',
                data: {
                    _token: $("[name=_token]").val(),
                    search: initialApprovedBy
                },
                success: function(data) {
                    let selectedItem = null;
                    $.each(data.items || [], function(_, item) {
                        if (item.id === initialApprovedBy) {
                            selectedItem = item;
                            return false;
                        }
                    });

                    if (selectedItem) {
                        const option = new Option(selectedItem.text, selectedItem.id, true, true);
                        $('#ApprovedBy').append(option).trigger('change');
                    }
                }
            });
        }

        $('#TransactionType').on('change', function() {
            load_reason_codes($(this).val(), '');
        });

        if ($('#TransactionType').val() !== '') {
            load_reason_codes($('#TransactionType').val(), initialReasonCode);
        }

        // Inisialisasi select2 Category di dalam ready agar aman saat inject via AJAX
        $('#Category').select2({
            placeholder: 'Select option',
            allowClear: false,
        });

        // Inisialisasi select2 ToBinID dan isi default option
        $('#ToBinID').select2({
            placeholder: 'Select option',
            allowClear: false,
        });
        $("#ToBinID").append(`<option value="GENERAL">GENERAL BIN</option>`);
        $("#ToBinID").val("GENERAL").trigger("change");

        // Inisialisasi select2 kosong untuk PartNum (sebelum kategori dipilih)
        $('#InptPartNum').select2({
            placeholder: 'Pilih Category terlebih dahulu',
            allowClear: false,
        });

        $("#Category").on('change', function() {
            const value = $(this).val();
            if (value != "") {
                GetPart(value);
            }
        });

        if ($('#Category').val() !== '') {
            GetPart($('#Category').val());
        }

        apply_form_state();
    });

    $('#InptQty').on('blur', function() {
        const rawValue = ($(this).val() || '').toString().trim();

        if (rawValue === '') {
            return;
        }

        const numericValue = parse_numeric_value(rawValue);
        if (isFinite(numericValue)) {
            $(this).val(format_decimal_value(numericValue));
        }
    });

    $(document).on('blur', '.qty-submit-input', function() {
        const rawValue = ($(this).val() || '').toString().trim();

        if (rawValue === '') {
            return;
        }

        const numericValue = parse_numeric_value(rawValue);
        if (isFinite(numericValue)) {
            $(this).val(format_decimal_value(numericValue));
        }
    });

    function load_reason_codes(transactionType, selectedReasonCode) {
        $('#ReasonCode').empty().trigger('change');

        if (!transactionType) {
            return;
        }

        $.ajax({
            type: 'POST',
            url: "{{ route('issue_miscellaneous.reason_codes') }}",
            dataType: 'json',
            data: {
                _token: $("[name=_token]").val(),
                transaction_type: transactionType,
            },
            success: function(data) {
                $('#ReasonCode').append('<option value=""></option>');
                $.each(data.items || [], function(_, item) {
                    $('#ReasonCode').append(new Option(item.text, item.id, false, false));
                });

                if (selectedReasonCode) {
                    $('#ReasonCode').val(selectedReasonCode).trigger('change');
                }
            },
            error: function() {
                Toast.fire({
                    position: 'top-end',
                    title: 'Gagal memuat Reason Code',
                    icon: 'error'
                });
            }
        });
    }

    $("#InptPartNum").on('change', function(){
        const value = $(this).val()
        const text = $("#InptPartNum option:selected").text();
        $("#InptPartName").val(text);
        $("#InptUOM").val("");
        $("#InptOnhandQty").val("");
        
        if ($("#Category").val() != "" && (value != "" && value != null)) {
            GetUOM(value)
        } else {
            Toast.fire({
                position: 'top-end',
                title: "Silakan Pilih Category",
                icon: "error"
            });
        }
    });
    function GetPart(value) {
        $('#InptPartNum').empty();
        $('#InptPartNum').select2({
            ajax: {
                type: 'POST',
                url: "{{ route('issue_miscellaneous.showPart') }}",
                dataType: 'json',
                delay: 250, // delay for search
                data: function(params) {
                    var query = {
                        search: params.term,
                        Category: value,
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
                cache: true
            },
            placeholder: 'Select option',
        });
        $("#InptPartNum").trigger('change.select2');

    }

    function GetUOM(value){
        var token = $("[name=_token]").val();
        var data = {
            _token : token,
            partnum : value,
            category : $("#Category").val()
        };
        $.ajax({
            type: "POST",
            url: "{{ route('issue_miscellaneous.ShowUOM')  }}",
            data: data,
            dataType: "json",
            success: function (data) {
                if(data.code == 200){
                    $("#InptUOM").val(data.UOM);
                    $("#InptOnhandQty").val(format_decimal_value(data.OnhandQty ?? 0));
                }else{
                    Toast.fire({
                    position: 'top-end',
                    title: data['msg_error'] || 'Error loading UOM',
                    icon: "error"
                })
                }
            }
        });
    }


    function submit_form() {
        if (!can_edit_header()) {
            Toast.fire({ position: 'top-end', title: 'Document is locked in its current status', icon: 'error' });
            return;
        }

        var transactionType = $('#TransactionType').val();
        var reasonCode = $('#ReasonCode').val();
        var category = $('#Category').val();
        var approvedBy = $('#ApprovedBy').val();
        var partNum = $('#InptPartNum').val();
        var qty = parse_numeric_value($('#InptQty').val());
        var onhand = parse_numeric_value($('#InptOnhandQty').val());

        if (!transactionType) {
            Toast.fire({ position: 'top-end', title: 'Silakan pilih Transaction Type', icon: 'error' });
            return;
        }
        if (!reasonCode) {
            Toast.fire({ position: 'top-end', title: 'Silakan pilih Reason Code', icon: 'error' });
            return;
        }
        if (!category) {
            Toast.fire({ position: 'top-end', title: 'Silakan pilih Category', icon: 'error' });
            return;
        }
        if (category === 'INV3' && !approvedBy) {
            Toast.fire({ position: 'top-end', title: 'Silakan pilih Approved By', icon: 'error' });
            return;
        }

        if (!is_submitter_approved_edit()) {
            if (!partNum) {
                Toast.fire({ position: 'top-end', title: 'Silakan pilih Part Num', icon: 'error' });
                return;
            }
            if (!isFinite(qty) || qty <= 0) {
                Toast.fire({ position: 'top-end', title: 'Qty wajib diisi (angka)', icon: 'error' });
                return;
            }
            if (isFinite(onhand) && qty > onhand) {
                Toast.fire({ position: 'top-end', title: 'Qty melebihi stock', icon: 'error' });
                return;
            }
        }
        if (!$('#CategoryFromTag').val()) {
            $('#CategoryFromTag').val(category);
        }

        submit_form_job();
    }

    function submit_form_job() {
        var token = $("[name=_token]").val();
        var button = document.getElementById('btn_submit_form');
        var svg = document.getElementById('svg_submit_form');
        var spinner = document.getElementById('spinner_submit_form');
        var buttonText = document.getElementById('btn_text_submit_form');
        var isApprovedHeaderEdit = is_submitter_approved_edit();
        var CategoryFromTag = $('#CategoryFromTag').val() || $('#Category').val();
        var submitUrl = isApprovedHeaderEdit
            ? "{{ route('issue_miscellaneous.update_header_submitter') }}"
            : "{{ route('issue_miscellaneous.store_item') }}";
        var defaultButtonText = isApprovedHeaderEdit ? 'Update Header' : 'Save';

        function reset_submit_form_button() {
            svg.style.display = 'inline-block';
            spinner.style.display = 'none';
            buttonText.textContent = defaultButtonText;
            button.disabled = false;
        }

        function get_ajax_error_message(jqXHR, fallbackMessage) {
            if (jqXHR && jqXHR.responseJSON) {
                if (jqXHR.responseJSON.status) {
                    return jqXHR.responseJSON.status;
                }

                if (jqXHR.responseJSON.message) {
                    return jqXHR.responseJSON.message;
                }
            }

            if (jqXHR && jqXHR.responseText) {
                try {
                    var parsed = JSON.parse(jqXHR.responseText);
                    if (parsed.status) {
                        return parsed.status;
                    }
                    if (parsed.message) {
                        return parsed.message;
                    }
                } catch (error) {
                }
            }

            return fallbackMessage;
        }

        svg.style.display = 'none';
        spinner.style.display = 'inline-block';
        buttonText.textContent = 'Please Wait...';
        button.disabled = true;
        var formData = '';
        for (var i = 1; i < 5; i++) {
            formData += $('#form-segment-' + i).serialize() + '&';
        }

        formData = formData.slice(0, -1);
        var string = "&_token=" + token + "&CategoryFromTag=" + CategoryFromTag + '&' + formData;

        $.ajax({
            type: 'POST',
            url: submitUrl,
            data: string,
            cache: false,
            dataType: 'json',
            success: function(data) {
                reset_submit_form_button();

                if (data.code == 200 || data.status === 'success') {
                    if (!isApprovedHeaderEdit) {
                        $("#InptPartNum").val(null).trigger('change');
                        $("#InptPartName").val("");
                        $("#InptQty").val("");
                        $("#InptUOM").val("");
                        $("#InptOnhandQty").val("");
                        $("#InptReference").val("");
                        $('#RequestStatus').val('DRAFT');
                    }
                    apply_form_state();
                    Toast.fire({
                        position: 'top-end',
                        title: (data.message || data.status || "Data berhasil tersimpan!"),
                        icon: "success"
                    });
                    refresh_detail_table();
                } else {
                    Toast.fire({
                        position: 'top-end',
                        title: data.message || data.status || 'Gagal menyimpan detail',
                        icon: "error"
                    });
                }
            },
            error: function(jqXHR, textStatus) {
                reset_submit_form_button();
                Toast.fire({
                    position: 'top-end',
                    title: get_ajax_error_message(jqXHR, 'Gagal menyimpan detail'),
                    icon: "error"
                });
            }
        })
    }

    function delete_item(trc_id, no, DocRef) {
        if (!can_edit_detail()) {
            Toast.fire({ position: 'top-end', title: 'Document is locked in its current status', icon: 'error' });
            return;
        }

        var button = document.getElementById('btn_delete_item_' + no);
        var svg = document.getElementById('svg_delete_item_' + no);
        var spinner = document.getElementById('spinner_delete_item_' + no);
        svg.style.display = 'none';
        spinner.style.display = 'inline-block';
        button.disabled = true;
        Swal.fire({
            icon: 'warning',
            title: 'Delete Data ?',
            text: "Hapus Label : " + DocRef,
            showCancelButton: true,
            confirmButtonText: 'Confirm',
        }).then(function(isConfirm) {
            if (isConfirm.value === true) {
                execute_delete_item(trc_id, no);
            } else {
                svg.style.display = 'inline-block';
                spinner.style.display = 'none';
                button.disabled = false;
            }
        })
    }

    function execute_delete_item(trc_id, no) {
        var token = $("[name=_token]").val();
        var button = document.getElementById('btn_delete_item_' + no);
        var svg = document.getElementById('svg_delete_item_' + no);
        var spinner = document.getElementById('spinner_delete_item_' + no);
        svg.style.display = 'none';
        spinner.style.display = 'inline-block';
        button.disabled = true;
        var string = "&_token=" + token + '&trc_id=' + trc_id;
        $.ajax({
            type: 'POST',
            url: "{{ route('issue_miscellaneous.delete_item') }}",
            data: string,
            cache: false,
            dataType: 'json',
            success: function(data) {
                svg.style.display = 'inline-block';
                spinner.style.display = 'none';
                button.disabled = false;
                if (data.code == 200) {
                    Toast.fire({
                        position: 'top-end',
                        title: "Data berhasil dihapus!",
                        icon: "success"
                    })
                    refresh_detail_table();
                } else {
                    Toast.fire({
                        position: 'top-end',
                        title: data.status,
                        icon: "error"
                    })
                }
            },
            error: function(jqXHR, textStatus) {
                svg.style.display = 'inline-block';
                spinner.style.display = 'none';
                button.disabled = false;
                Toast.fire({
                    position: 'top-end',
                    title: " Please check all field! ",
                    icon: "error"
                })
            }
        })
    }


    $("#detail_table_search").keyup(function(event) {
        if (event.keyCode == 13) {
            refresh_detail_table();
        }
    });

    function refresh_detail_table() {
        if ($.fn.DataTable.isDataTable('#kt_form_table') && detailTableInstance) {
            detailTableInstance.ajax.reload(null, false);
            return;
        }

        detail_table();
    }

    function detail_table() {
        if ($.fn.DataTable.isDataTable('#kt_form_table') && detailTableInstance) {
            detailTableInstance.ajax.reload(null, false);
            return detailTableInstance;
        }

        detailTableInstance = $("#kt_form_table").DataTable({
            processing: true,
            serverSide: true,
            responsive: false,
            language: {
                'processing': '<div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>'
            },
            info: false,
            order: [],
            columnDefs: [{
                orderable: false,
                targets: 0
            }],
            ajax: {
                url: "{{ route('issue_miscellaneous.detail_table') }}",
                type: 'POST',
                data: function(d) {
                    d._token = $("[name=_token]").val();
                    d.DocNum = $("#InptDocNum").val();
                    d.search = $("#detail_table_search").val();
                },
                cache: false,
                dataType: 'json'
            },
            columns: [{
                    data: 'no',
                    className: 'text-center'
                },
                {
                    data: 'action',
                    className: 'text-center',
                    orderable: false
                },
                {
                    data: 'PartNum'
                },
                {
                    data: 'PartName'
                },
                {
                    data: 'Qty',
                    className: 'text-right',
                    render: function(data) {
                        return format_decimal_value(data);
                    }
                },
                {
                    data: 'QtySubmit',
                    className: 'text-right',
                    render: function(data) {
                        return format_decimal_value(data);
                    }
                },
                {
                    data: 'FromWarehouseDesc'
                },
                {
                    data: 'ToWarehouseDesc'
                },
                {
                    data: 'Reference'
                }
            ]
        });

        return detailTableInstance;
    }

    function submit_document() {
        if (!is_form_editable()) {
            Toast.fire({ position: 'top-end', title: 'Only draft or rejected documents can be submitted', icon: 'error' });
            return;
        }

        const docNum = $("#InptDocNum").val();
        if (!docNum || docNum === '') {
            Toast.fire({
                position: 'top-end',
                title: "No document to submit!",
                icon: "error"
            });
            return;
        }

        Swal.fire({
            icon: 'question',
            title: 'Submit Document?',
            text: 'Dokumen akan dikirim untuk approval. Qty Submit diisi setelah approved oleh user berwenang.',
            showCancelButton: true,
            confirmButtonText: 'Ya, Submit',
            cancelButtonText: 'Batal'
        }).then(function(result) {
            if (result.value === true) {
                execute_submit_document();
            }
        });
    }

    function execute_submit_document() {
        const button = document.getElementById('btn_submit_document');
        const spinner = document.getElementById('spinner_submit_document');
        const buttonText = document.getElementById('btn_text_submit_document');

        button.disabled = true;
        spinner.style.display = 'inline-block';
        buttonText.textContent = 'Submitting...';

        $.ajax({
            url: "{{ route('issue_miscellaneous.submit_document') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                DocRef: $("#InptDocNum").val()
            },
            success: function(response) {
                spinner.style.display = 'none';
                buttonText.textContent = 'Submit Approval';
                button.disabled = false;

                if (response.status === 'success') {
                    Toast.fire({
                        position: 'top-end',
                        title: response.message || "Document submitted successfully!",
                        icon: "success"
                    });

                    setTimeout(function() {
                        window.location.href = "{{ route('issue_miscellaneous.index') }}";
                    }, 1000);
                } else {
                    Toast.fire({
                        position: 'top-end',
                        title: response.message || "Failed to submit document!",
                        icon: "error"
                    });
                }
            },
            error: function() {
                spinner.style.display = 'none';
                buttonText.textContent = 'Submit Approval';
                button.disabled = false;
                Toast.fire({
                    position: 'top-end',
                    title: "Error submitting document!",
                    icon: "error"
                });
            }
        });
    }

    function open_qty_submit_modal() {
        const canUpdateQtySubmit = $('#CanUpdateQtySubmit').val() === '1';
        const status = $('#RequestStatus').val();
        const isAllowedStatus = status === 'APPROVED' || status === 'AUTO_APPROVED';

        if (!canUpdateQtySubmit) {
            Toast.fire({ position: 'top-end', title: 'Anda tidak memiliki akses untuk update Qty Submit', icon: 'error' });
            return;
        }

        if (!isAllowedStatus) {
            Toast.fire({ position: 'top-end', title: 'Qty Submit hanya bisa diisi setelah approved', icon: 'error' });
            return;
        }

        const docNum = ($("#InptDocNum").val() || '').toString().trim();
        if (!docNum || docNum === '') {
            Toast.fire({
                position: 'top-end',
                title: "No document found!",
                icon: "error"
            });
            return;
        }

        // Fetch detail data for qty submit update
        $.ajax({
            url: "{{ route('issue_miscellaneous.detail_table') }}",
            type: "POST",
            dataType: 'json',
            data: {
                _token: "{{ csrf_token() }}",
                DocNum: docNum,
                length: 100,
                start: 0,
                search: ""
            },
            success: function(response) {
                let payload = response;
                if (typeof payload === 'string') {
                    try {
                        payload = JSON.parse(payload);
                    } catch (e) {
                        payload = {};
                    }
                }

                let items = Array.isArray(payload.data) ? payload.data : [];

                if (items.length === 0) {
                    Toast.fire({
                        position: 'top-end',
                        title: "No items found!",
                        icon: "error"
                    });
                    return;
                }

                // Populate modal table
                let tbody = $('#submit_modal_tbody');
                tbody.empty();
                
                items.forEach((item, index) => {
                    const qtyMoveDisplay = format_decimal_value(item.QtyMove);
                    const parsedQtySubmit = parse_numeric_value(item.QtySubmit);
                    const qtySubmitValue = !isFinite(parsedQtySubmit) || parsedQtySubmit <= 0
                        ? format_decimal_value(item.QtyMove)
                        : format_decimal_value(parsedQtySubmit);
                    let row = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${item.PartNum}</td>
                            <td>${item.PartName || ''}</td>
                            <td class="text-end">${qtyMoveDisplay}</td>
                            <td class="text-end">
                                <input type="number" class="form-control form-control-sm text-end qty-submit-input" 
                                    data-linekey="${item.id}" 
                                    data-max="${item.QtyMove}" 
                                    value="${qtySubmitValue}" 
                                    min="0" 
                                    max="${item.QtyMove}" 
                                    step="0.01" />
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });

                // Show modal
                $('#modal_submit_document').modal('show');
            },
            error: function() {
                Toast.fire({
                    position: 'top-end',
                    title: "Failed to load items!",
                    icon: "error"
                });
            }
        });
    }

    function execute_update_qty_submit() {
        const button = document.getElementById('btn_execute_submit');
        const spinner = document.getElementById('spinner_execute_submit');
        const buttonText = document.getElementById('btn_text_execute_submit');

        // Collect filled QtySubmit values only for gradual update
        let submitData = [];
        let isValid = true;
        let errorMsg = '';

        $('.qty-submit-input').each(function() {
            const lineKey = $(this).data('linekey');
            const rawValue = ($(this).val() || '').toString().trim();
            if (rawValue === '') {
                return;
            }

            const qtySubmit = parseFloat(rawValue);
            const maxQty = parseFloat($(this).data('max'));

            if (!isFinite(qtySubmit) || qtySubmit <= 0) {
                isValid = false;
                errorMsg = 'Qty Submit harus lebih dari 0!';
                return false;
            }

            if (qtySubmit > maxQty) {
                isValid = false;
                errorMsg = 'Qty Submit tidak boleh melebihi Qty Request!';
                return false;
            }

            submitData.push({
                lineKey: lineKey,
                qtySubmit: qtySubmit
            });
        });

        if (submitData.length === 0) {
            Toast.fire({
                position: 'top-end',
                title: 'Isi minimal satu Qty Submit untuk di-update',
                icon: 'error'
            });
            return;
        }

        if (!isValid) {
            Toast.fire({
                position: 'top-end',
                title: errorMsg,
                icon: "error"
            });
            return;
        }

        // Show loading state
        button.disabled = true;
        spinner.style.display = 'inline-block';
        buttonText.textContent = 'Saving...';

        $.ajax({
            url: "{{ route('issue_miscellaneous.update_qty_submit') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                DocRef: $("#InptDocNum").val(),
                submitData: submitData
            },
            success: function(response) {
                spinner.style.display = 'none';
                buttonText.textContent = 'Save Qty Submit';
                button.disabled = false;

                if (response.status === 'success') {
                    $('#modal_submit_document').modal('hide');
                    Toast.fire({
                        position: 'top-end',
                        title: response.message || "Qty Submit updated successfully!",
                        icon: "success"
                    });

                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                } else {
                    Toast.fire({
                        position: 'top-end',
                        title: response.message || "Failed to update Qty Submit!",
                        icon: "error"
                    });
                }
            },
            error: function() {
                spinner.style.display = 'none';
                buttonText.textContent = 'Save Qty Submit';
                button.disabled = false;
                Toast.fire({
                    position: 'top-end',
                    title: "Error updating Qty Submit!",
                    icon: "error"
                });
            }
        });
    }

    function cancel_document() {
        if (!is_form_editable()) {
            Toast.fire({ position: 'top-end', title: 'Only draft or rejected documents can be cancelled', icon: 'error' });
            return;
        }

        var DocNum = $("#InptDocNum").val();
        if (!DocNum) {
            window.location.href = "{{ url('/issue_miscellaneous') }}";
            return;
        }

        Swal.fire({
            icon: 'warning',
            title: 'Cancel Document?',
            html: "Dokumen <b>" + DocNum + "</b> dan semua detailnya akan dihapus!<br>Yakin ingin membatalkan?",
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            confirmButtonColor: '#d33',
            cancelButtonText: 'Tidak',
        }).then(function(result) {
            if (result.value === true) {
                execute_cancel_document(DocNum);
            }
        });
    }

    function execute_cancel_document(DocNum) {
        var token = $("[name=_token]").val();
        var button = document.getElementById('btn_cancel_doc');
        var svg = document.getElementById('svg_cancel_doc');
        var spinner = document.getElementById('spinner_cancel_doc');
        var buttonText = document.getElementById('btn_text_cancel_doc');
        
        svg.style.display = 'none';
        spinner.style.display = 'inline-block';
        buttonText.textContent = 'Please Wait...';
        button.disabled = true;
        
        var string = "&_token=" + token + '&DocNum=' + DocNum;
        
        $.ajax({
            type: 'POST',
            url: "{{ route('issue_miscellaneous.cancel_document') }}",
            data: string,
            cache: false,
            dataType: 'json',
            success: function(data) {
                if (data.code == 200) {
                    Toast.fire({
                        position: 'top-end',
                        title: data.status,
                        icon: "success"
                    });
                    setTimeout(function() {
                        window.location.href = "{{ url('/issue_miscellaneous') }}";
                    }, 1000);
                } else {
                    svg.style.display = 'inline-block';
                    spinner.style.display = 'none';
                    buttonText.textContent = 'Cancel';
                    button.disabled = false;
                    Toast.fire({
                        position: 'top-end',
                        title: data.status,
                        icon: "error"
                    });
                }
            },
            error: function(jqXHR, textStatus) {
                svg.style.display = 'inline-block';
                spinner.style.display = 'none';
                buttonText.textContent = 'Cancel';
                button.disabled = false;
                Toast.fire({
                    position: 'top-end',
                    title: "Error deleting document!",
                    icon: "error"
                });
            }
        });
    }

    function back_to_list() {
        window.history.pushState({}, '', window.location.pathname);
        document.getElementById('kt_activity_home_tab').click();
        if (typeof refresh_front_table === 'function') {
            refresh_front_table();
        }
    }
</script>
