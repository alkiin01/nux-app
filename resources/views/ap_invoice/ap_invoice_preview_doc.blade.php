<script>
    $(document).ready(function() {
        var doc = '<?php echo $doc; ?>';
        window.history.pushState('', '', '<?php echo env('BASE_URL'); ?>/ap_invoice?ref_doc=' + doc);
        setTimeout(() => {
            $("#form_loader").css("display", "none");
        }, 1000);
        $("#kt_detail_table").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('ap_invoice/preview_doc/detail') }}",
                type: 'post',
                data: function(d) {
                    d._token = '{{ csrf_token() }}';
                    d.id = doc;
                    d.search = $("#detail_table_search").val();
                }
            },
            columns: [{
                data: 'no',
                orderable: false,
                searchable: false,
                className: 'text-center'
            }, {
                data: 'PackSlip'
            }, {
                data: 'Qty'
            }, {
                data: 'PriceGR'
            }, {
                data: 'PricePO'
            }, {
                data: 'AmountGR'
            }, {
                data: 'AmountPO'
            }, {
                data: 'UOM',
            }, {
                data: 'View',
                orderable: false,
                searchable: false,
                className: 'text-center'
            }],
            createdRow: function(row, data, dataIndex) {
                let PO = parseFloat(String(data.PricePO).replace(/[Rp\s.]/g, '').replace(',',
                    '.')) || 0;
                let GR = parseFloat(String(data.PriceGR).replace(/[Rp\s.]/g, '').replace(',',
                    '.')) || 0;
                let PricePO = parseFloat(String(data.PricePO).replace(/[Rp\s.]/g, '').replace(',',
                        '.')) ||
                    0;
                let PriceGR = parseFloat(String(data.PriceGR).replace(/[Rp\s.]/g, '').replace(',',
                        '.')) ||
                    0;
                if (PO !== GR || PriceGR !== PricePO) {
                    let cells = $(row).find("td");
                    // $(row).addClass('bg-warning');
                    $(cells[3]).css("color", "red");
                    $(cells[4]).css("color", "red");
                    $(cells[5]).css("color", "red");
                    $(cells[6]).css("color", "red");
                }
            }
        })
        $("#detail_table_search").on('keyup change', function() {
            $("#kt_detail_table").DataTable().ajax.reload()
        })
        checkStatus()
    })

    function checkStatus() {
        $.ajax({
            url: "{{ url('ap_invoice/check_status') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: "{{ $data->id }}",
                group: "{{ $data->GroupID }}",
                invNum: "{{ $data->InvoiceNum }}"
            },
            success: function(response) {
                if (response == 'PO0' || response == 'AP0') {
                    $("#cancel_approved").css('display', 'none')
                    //  $("#approved").css('display', 'none')
                } else if (response == 'PO1' || response == 'AP1') {
                    show_new_approval()
                    $("#approved").css('display', 'none')
                } else if (response == 'NO PO') {
                    $("#cancel_approved").css('display', 'none')
                    $("#approved").css('display', 'none')
                } else if (response == 'AP1') {
                    show_new_approval()
                    $("#recalculateBtn").css('display', 'block')
                } else {
                    $("#cancel_approved").css('display', 'none')
                    $("#approved").css('display', 'none')
                    $("#update_btn").css('display', 'none')
                }
            },
            error: function(xhr) {
                console.log(xhr)
            }
        })
    }
</script>
<div class="card-body">
    <div id="form_loader" style="text-align: center;">
        <div class="lds-roller mt-5" id="lds-roller-form">
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
</div>

<div style="background-color: #f5f8fa;"><br></div>
<div class="card mt-2">
    <div class="card-header py-6 mb-6">
        <ul class="nav nav-tabs nav-line-tabs nav-stretch fs-6 border-0">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#preview_tab">Preview</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#attachment_tab">Attachment</a>
            </li>
        </ul>
        <div class="card-toolbar">
            <div class="d-flex justify-content-end" data-kt-goodreceive-table-toolbar="base">
                <a href="{{ url('ap_invoice') }}" type="button"
                    class="btn me-2 btn-xs btn-outline btn-outline-dashed btn-outline-success btn-active-light-success text-muted">
                    <span class="svg-icon svg-icon-muted svg-icon-2x">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none">
                            <path d="M14.6 4L6.6 12L14.6 20H10.6L3.3 12.7C2.9 12.3 2.9 11.7 3.3 11.3L10.6 4H14.6Z"
                                fill="black" />
                            <path opacity="0.3"
                                d="M21.6 4L13.6 12L21.6 20H17.6L10.3 12.7C9.9 12.3 9.9 11.7 10.3 11.3L17.6 4H21.6Z"
                                fill="black" />
                        </svg>
                    </span>
                    Back
                </a>
            </div>
        </div>
    </div>

    <div class="card-body pt-0">
        <div class="tab-content">
            <div class="tab-pane fade show active" id="preview_tab" role="tabpanel">
                <div class="row" id="form">
                    <div class="col-md-6 mb-5">
                        <form>
                            <div class="form-group mb-5">
                                <label>Group ID</label>
                                <input type="text" class="form-control bg-light-primary" id="group_id"
                                    value="{{ $data->GroupID }}" readonly />
                            </div>
                            <div class="form-group mb-5">
                                <label>PO Number</label>
                                <input type="number" class="form-control bg-light-primary" id="PONum"
                                    name="PONum" value="{{ $data->PONum }}" readonly />
                            </div>
                            <div class="form-group mb-5">
                                <label>Fiscal Period</label>
                                <input type="text" class="form-control bg-light-primary" id="PackSlip"
                                    name="PackSlip" value="{{ $data->FiscalPeriod }}" readonly />
                            </div>
                            <div class="form-group mb-5">
                                <label>Terms</label>
                                <select class="form-control bg-light-primary" id="terms">
                                    <option value="{{ $data->TermsCode }}">{{ $data->TermsCode }}</option>
                                </select>
                            </div>
                            <div class="form-group mb-5">
                                <label>Applied Date</label>
                                <input type="date" class="form-control bg-light-primary" id="applied_date"
                                    name="PackSlip" value="{{ $data->AppliedDate }}" readonly />
                            </div>
                            <div class="form-group mb-5">
                                <label>Due Date</label>
                                <input type="date" class="form-control bg-light-primary" id="due_date"
                                    name="PackSlip" value="{{ $data->DueDate }}" readonly />
                            </div>
                            <div class="form-group mb-5">
                                <label>DPP GR</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light fw-bold">Rp.</span>
                                    <input type="text" class="form-control bg-light-primary" id="dpp_gr"
                                        name="PackSlip" value="{{ number_format($data->DPPGR, 0, ',', '.') }}"
                                        readonly />
                                </div>
                            </div>
                            <div class="form-group mb-5">
                                <label>Tax GR</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light fw-bold">Rp.</span>
                                    <input type="text" class="form-control bg-light-primary" id="tax_gr"
                                        name="PackSlip" value="{{ number_format($data->TaxGR, 0, ',', '.') }}"
                                        readonly />
                                </div>
                            </div>
                            <div class="form-group mb-5">
                                <label>Amount GR</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light fw-bold">Rp.</span>
                                    <input type="text" class="form-control bg-light-primary" id="total_gr"
                                        name="PackSlip" value="{{ number_format($data->TotalGR, 0, ',', '.') }}"
                                        readonly />
                                </div>
                            </div>
                            <div class="form-group mb-5">
                                <label>Amount Confirm</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light fw-bold">Rp.</span>
                                    <input type="text" class="form-control bg-light-primary" id="total_po"
                                        name="PackSlip" value="{{ number_format($data->ConfirmNote, 0, ',', '.') }}"
                                        readonly />
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Form kanan -->
                    <div class="col-md-6 mb-5">
                        <form>
                            <div class="form-group mb-5">
                                <label>Invoice Number</label>
                                <input type="text" class="form-control bg-light-primary" id="InvNumber"
                                    value="{{ $data->InvoiceNum }}" readonly />
                            </div>
                            <div class="form-group mb-5">
                                <label>Fiscal Year</label>
                                <input type="text" class="form-control bg-light-primary" id="EntryDate"
                                    value="{{ $data->FiscalYear }}" readonly />
                            </div>
                            <div class="form-group mb-5">
                                <label>Vendor</label>
                                <input type="text" class="form-control bg-light-primary" id="ArrivedDate"
                                    name="ArrivedDate" value="{{ $vendor ?? $data->VendorNum }}" readonly />
                            </div>
                            <div class="form-group mb-5">
                                <label>Supplier Inv Date</label>
                                <input type="date" class="form-control bg-light-primary" id="supplier_inv_date"
                                    name="PackSlip" value="{{ $data->SupplierInvoiceDate }}" readonly />
                            </div>
                            <div class="form-group mb-5">
                                <label>Invoice Date</label>
                                <input type="date" class="form-control bg-light-primary" id="invoice_date"
                                    name="PackSlip" value="{{ $data->InvoiceDate }}" readonly />
                            </div>
                            <div class="form-group mb-5">
                                <label>Variance</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light fw-bold">Rp.</span>
                                    <input type="text" class="form-control bg-light-primary" id="variance"
                                        name="PackSlip"
                                        value="{{ number_format((float) $data->variance, 2, '.', '') }}" readonly />
                                </div>
                            </div>
                            <div class="form-group mb-5">
                                <label>DPP PO</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light fw-bold">Rp.</span>
                                    <input type="text" class="form-control bg-light-primary" id="dpp_po"
                                        name="PackSlip" value="{{ number_format($data->DPPPO, 0, ',', '.') }}"
                                        readonly />
                                </div>
                            </div>
                            <div class="form-group mb-5">
                                <label>Tax PO</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light fw-bold">Rp.</span>
                                    <input type="text" class="form-control bg-light-primary" id="tax_po"
                                        name="PackSlip" value="{{ number_format($data->TaxPO, 0, ',', '.') }}"
                                        readonly />
                                </div>
                            </div>
                            <div class="form-group mb-5">
                                <label>Amount PO</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light fw-bold">Rp.</span>
                                    <input type="text" class="form-control bg-light-primary" id="total_po"
                                        name="PackSlip" value="{{ number_format($data->TotalPO, 0, ',', '.') }}"
                                        readonly />
                                </div>
                            </div>
                        </form>
                    </div>

                    <hr style="color: gray">

                    <!-- Tombol Aksi -->
                    <div class="col-md-12" style="display:flex; justify-content:space-between">
                        <div>
                            <button type="button" class="btn btn-success btn-sm mr-2 mt-2" id="update_btn"
                                onclick="update()">
                                <span id="update_text">Update</span>
                                <span id="spinner_update_btn"
                                    class="spinner-border spinner-border-sm align-middle ms-2"
                                    style="display: none;"></span>
                            </button>

                            <button type="button" class="btn btn-primary btn-sm mr-2 mt-2" id="submit_btn"
                                onclick="submit()" style="display:none">
                                <span id="submit_text">Submit</span>
                                <span id="spinner_submit_btn"
                                    class="spinner-border spinner-border-sm align-middle ms-2"
                                    style="display: none;"></span>
                            </button>
                        </div>

                        <div>
                            <button type="button" class="btn btn-light-danger btn-sm mr-2 mt-2" id="cancel_approved"
                                onclick="cancel()">
                                <span id="btn_text_delete_gr">Cancel Approved</span>
                                <span id="spinner_delete_gr"
                                    class="spinner-border spinner-border-sm align-middle ms-2"
                                    style="display: none;"></span>
                            </button>

                            <button type="button" class="btn btn-primary btn-sm mr-2 mt-2" id="approved"
                                onclick="approved()">
                                <span id="btn_text_update_gr">Approved</span>
                                <span id="spinner_update_gr"
                                    class="spinner-border spinner-border-sm align-middle ms-2"
                                    style="display: none;"></span>
                            </button>
                            @if ($data->Approved_AP == 1 && $data->Status == 2)
                                {{-- @if ($data->Approved_AP == 0 && $data->Status == 0) --}}
                                <button type="button" class="btn btn-primary btn-sm mr-2 mt-2" id="recalculateBtn">
                                    <span id="btn_text_recalculate">Recalculate</span>
                                    <span id="spin_btn_recalculate"
                                        class="spinner-border spinner-border-sm align-middle ms-2"
                                        style="display: none;"></span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Attachment -->
            <div class="tab-pane fade" id="attachment_tab" role="tabpanel">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                            <a onclick="showPdfModal('{{ $data->InvoiceAttachment }}')"
                                class="text-gray-800 text-hover-primary d-flex flex-column">
                                <div class="symbol symbol-60px mb-5">
                                    <img src="<?= env('APP_ASSETS') ?>assets/media/svg/files/doc.svg"
                                        alt="" />
                                </div>
                                <div class="fs-5 fw-bolder mb-2">
                                </div>
                            </a>
                            <div class="fs-7 fw-bold text-gray-400">{{ $data->InvoiceAttachment }}</div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                            <a onclick="showPdfModal('{{ $data->FakturAttachment }}')"
                                class="text-gray-800 text-hover-primary d-flex flex-column">
                                <div class="symbol symbol-60px mb-5">
                                    <img src="<?= env('APP_ASSETS') ?>assets/media/svg/files/doc.svg"
                                        alt="" />
                                </div>
                                <div class="fs-5 fw-bolder mb-2">
                                </div>
                            </a>
                            <div class="fs-7 fw-bold text-gray-400">{{ $data->FakturAttachment }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
</div>

<div class="card mt-5">
    <div class="card-header border-1 mb-5">
        <div class="card-title">
            <div class="d-flex align-items-center position-relative my-1">
                <span class="svg-icon svg-icon-1 position-absolute ms-6">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none">
                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1"
                            transform="rotate(45 17.0365 15.1223)" fill="black" />
                        <path
                            d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                            fill="black" />
                    </svg>
                </span>
                <input type="text" data-kt-goodreceive-table-filter="search" id="detail_table_search"
                    class="form-control form-control-solid w-250px ps-15 text-sm form-control-sm"
                    placeholder="PackSlip" />
            </div>
        </div>
        <div>
            <button type="button" class="btn btn-primary btn-sm mr-2 mt-2" id="showModalItemAll">
                <span id="btn_text_item_gr">All Item</span>
                <span id="spinner_item_gr" class="spinner-border spinner-border-sm align-middle ms-2"
                    style="display: none;"></span>
            </button>
        </div>
    </div>

    <div class="card-body pt-0">
        <table class="table align-middle table-row-dashed table-striped gy-2 fs-7" id="kt_detail_table">
            <thead>
                <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                    <th class="min-w-20px pe-2">No</th>
                    <th class="min-w-50px">PackSlip</th>
                    <th class="min-w-50px">Total Qty</th>
                    <th class="min-w-50px">GR Unit Price</th>
                    <th class="min-w-20px">PO Unit Price</th>
                    <th class="min-w-20px">GR Total</th>
                    <th class="min-w-20px">PO Total</th>
                    <th class="min-w-50px">UOM</th>
                    <th class="min-w-50px">View</th>
                </tr>
            </thead>
            <tfoot>
                <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                    <th class="min-w-20px pe-2">No</th>
                    <th class="min-w-50px">PackSlip</th>
                    <th class="min-w-50px">Total Qty</th>
                    <th class="min-w-50px">GR Unit Price</th>
                    <th class="min-w-20px">PO Unit Price</th>
                    <th class="min-w-20px">GR Total</th>
                    <th class="min-w-20px">PO Total</th>
                    <th class="min-w-50px">UOM</th>
                    <th class="min-w-50px">View</th>
                </tr>
            </tfoot>
            <tbody></tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="modalDtl" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="labelModal">Item Detail</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table align-middle table-row-dashed table-striped gy-2 fs-7" id="detailItemTb">
                    <thead>
                        <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                            <th class="min-w-20px pe-2">No</th>
                            <th class="min-w-20px">PackSlip</th>
                            <th class="min-w-20px">Part Num</th>
                            <th class="min-w-60px">Description</th>
                            <th class="min-w-50px">Qty</th>
                            <th class="min-w-60px">PO Unit Price</th>
                            <th class="min-w-50px">GR Unit Price</th>
                            <th class="min-w-50px">GR Total</th>
                            <th class="min-w-50px">PO Total</th>
                            <th class="min-w-50px">UOM</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                            <th class="min-w-20px pe-2">No</th>
                            <th class="min-w-20px">PackSlip</th>
                            <th class="min-w-20px">Part Num</th>
                            <th class="min-w-60px">Description</th>
                            <th class="min-w-50px">Qty</th>
                            <th class="min-w-60px">PO Unit Price</th>
                            <th class="min-w-50px">GR Unit Price</th>
                            <th class="min-w-50px">GR Total</th>
                            <th class="min-w-50px">PO Total</th>
                            <th class="min-w-50px">UOM</th>
                        </tr>
                    </tfoot>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                {{-- <button type="button" class="btn btn-primary" id='saveDtlitem'>Save</button> --}}
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalDocument" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="labelModal">Document</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="height: 90vh; overflow-y: auto;">
                <div class="mb-2 w-100">
                    <iframe id="pdfFrame" src="" frameborder="0" width="100%" height="100%"
                        style="border: none; border-radius: 8px; min-height: 80vh;">
                    </iframe>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                {{-- <button type="button" class="btn btn-primary" id='saveDtlitem'>Save</button> --}}
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalItemAll" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <input type="text" data-kt-goodreceive-table-filter="search" id="search_item"
                    class="form-control form-control-solid w-250px ps-15 text-sm form-control-sm"
                    placeholder="PONum/PackSlip/PartNum" />
                <button type="button" class="btn btn-primary btn-sm" onclick="exportExcel()">Export</button>
            </div>
            <div class="modal-body">
                <table class="table align-middle table-row-dashed table-striped gy-2 fs-7" id="all_item_tbl">
                    <thead>
                        <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                            <th class="min-w-20px pe-2">No</th>
                            <th class="min-w-40px">Part</th>
                            <th class="min-w-60px">Description</th>
                            <th class="min-w-40px">Total Qty</th>
                            <th class="min-w-40px">GR Price</th>
                            <th class="min-w-40px">PO Price</th>
                            <th class="min-w-40px">GR Amount</th>
                            <th class="min-w-40px">PO Amount</th>
                            <th class="min-w-30px">UOM</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                            <th class="min-w-20px pe-2">No</th>
                            <th class="min-w-40px">Part</th>
                            <th class="min-w-60px">Description</th>
                            <th class="min-w-40px">Total Qty</th>
                            <th class="min-w-40px">GR Price</th>
                            <th class="min-w-40px">PO Price</th>
                            <th class="min-w-40px">GR Amount</th>
                            <th class="min-w-40px">PO Amount</th>
                            <th class="min-w-30px">UOM</th>
                        </tr>
                        <tr class="fw-bold text-gray-800">
                            <th colspan="7" class="text-end">TOTAL :</th>
                            <th id="total_po_amount">Rp 0</th>
                            <th id="total_gr_amount">Rp 0</th>
                            <th></th>
                        </tr>
                    </tfoot>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                {{-- <button type="button" class="btn btn-primary" id='saveDtlitem'>Save</button> --}}
            </div>
        </div>
    </div>
</div>
<script>
    function show_new_approval() {
        $.ajax({
            url: "{{ url('ap_invoice/show_new_approval') }}",
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                inv: $("#InvNumber").val(),
                grp: $("#group_id").val()
            },
            success: function(response) {
                const data = response.data
                $("#dpp_po").val(parseFloat(data.DPPPO.toLocaleString('id-ID')))
                $("#tax_po").val(parseFloat(data.TaxPO.toLocaleString('id-ID')))
                $("#total_po").val(parseFloat(data.TotalPO.toLocaleString('id-ID')))
            },
            error: function(xhr) {
                Toast.fire({
                    position: 'top-end',
                    title: xhr.responseJSON.message,
                    icon: "error"
                });
            }
        })
    }

    function backHome() {
        $("#kt_content").removeClass("d-none");
        $("#file_view").addClass("d-none")
        window.history.pushState('', '', '/ap_invoice');
        primaryTable.ajax.reload()
        statusHeader()
    }

    function approved() {
        $("#svg_update_gr").hide()
        $("#spinner_update_gr").show()
        $.ajax({
            url: "{{ url('ap_invoice/approved') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: "{{ $data->id }}",
                group: "{{ $data->GroupID }}",
                invNum: "{{ $data->InvoiceNum }}"
            },
            success: function(response) {
                if (response.status == 200) {
                    Toast.fire({
                        position: 'top-end',
                        title: "Approved Successfully",
                        icon: "success"
                    });
                    $("#approved").css('display', 'none')
                    $("#cancel_approved").css('display', 'block')
                    checkStatus()
                } else {
                    Toast.fire({
                        position: 'top-end',
                        title: response.messsage,
                        icon: "error"
                    });
                }
                $("#spinner_update_gr").hide()
                $("#svg_update_gr").show()
            },
            error: function(xhr) {
                console.log(xhr)
                Toast.fire({
                    position: 'top-end',
                    title: xhr.responseJSON.message,
                    icon: "error"
                });
                $("#spinner_update_gr").hide()
                $("#svg_update_gr").show()
            }
        })
    }

    function cancel() {
        $("#svg_delete_gr").hide()
        $("#spinner_delete_gr").show()
        $.ajax({
            url: "{{ url('ap_invoice/cancel_approval') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: "{{ $data->id }}",
                group: "{{ $data->GroupID }}",
                invNum: "{{ $data->InvoiceNum }}"
            },
            success: function(response) {
                if (response.status == 200) {
                    Toast.fire({
                        position: 'top-end',
                        title: "Cancel Approved Successfully",
                        icon: "success"
                    });
                    $("#cancel_approved").css('display', 'none')
                    $("#approved").css('display', 'block')
                } else {
                    Toast.fire({
                        position: 'top-end',
                        title: response.message,
                        icon: "error"
                    });
                }
                $("#spinner_delete_gr").hide()
                $("#svg_delete_gr").show()
            },
            error: function(xhr) {
                console.log(xhr)
                Toast.fire({
                    position: 'top-end',
                    title: xhr.responseJSON.message,
                    icon: "error"
                });
                $("#spinner_delete_gr").hide()
                $("#svg_delete_gr").show()
            }
        })
    }

    function update() {
        $.ajax({
            url: "{{ url('ap_invoice/check_status') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: "{{ $data->id }}",
                group: "{{ $data->GroupID }}",
                invNum: "{{ $data->InvoiceNum }}"
            },
            success: function(res) {
                if (res == 'AP1') {
                    cancel()
                }
                $("#invoice_date").removeClass('bg-light-primary').prop('readonly', false)
                $("#due_date").removeClass('bg-light-primary').prop('readonly', false)
                $("#supplier_inv_date").removeClass('bg-light-primary').prop('readonly', false)
                $("#applied_date").removeClass('bg-light-primary').prop('readonly', false)
                $("#terms").removeClass('bg-light-primary').prop('readonly', false)
                $.ajax({
                    url: "{{ url('ap_invoice/terms') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        const $terms = $("#terms");
                        const selected = $terms.val();
                        $terms.empty();
                        response.forEach(function(item) {
                            const selectedAttr = (item.TermsCode === selected) ?
                                'selected' : '';
                            $terms.append(
                                `<option value="${item.TermsCode}" ${selectedAttr}>${item.TermsCode} - ${item.Description}</option>`
                            );
                        });
                    }
                })
                $("#update_btn").css('display', 'none')
                $("#submit_btn").css('display', 'block')
            },
            error: function(xhr) {
                console.log(xhr)
            }
        })
    }
    $("#terms, #invoice_date").on('change', function() {
        $.ajax({
            url: "{{ url('ap_invoice/change_terms') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                terms: $("#terms").val(),
                invDate: $("#invoice_date").val()
            },
            success: function(response) {
                if (response.status == 200) {
                    $("#due_date").val(response.DueDate)
                } else {
                    Toast.fire({
                        position: 'top-end',
                        title: 'Server error',
                        icon: "error"
                    });
                }
            }
        })
    })

    function submit() {
        const terms = $("#terms").val();
        const supplier_inv_date = $("#supplier_inv_date").val();
        const applied_date = $("#applied_date").val();
        const invoice_date = $("#invoice_date").val();
        const due_date = $("#due_date").val();
        const fields = {
            "Terms": $("#terms").val(),
            "Supplier Invoice Date": $("#supplier_inv_date").val(),
            "Applied Date": $("#applied_date").val(),
            "Invoice Date": $("#invoice_date").val(),
            "Due Date": $("#due_date").val(),
        };
        let emptyFields = [];
        for (let label in fields) {
            if (!fields[label] || fields[label].trim() === "") {
                emptyFields.push(label);
            }
        }
        if (emptyFields.length > 0) {
            Toast.fire({
                position: 'top-end',
                icon: "error",
                title: "Kolom kosong:\n" + emptyFields.join(", ")
            });
            return;
        }

        $.ajax({
            url: "{{ url('ap_invoice/submit_change') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                terms: terms,
                supplier_inv_date: supplier_inv_date,
                applied_date: applied_date,
                invoice_date: invoice_date,
                due_date: due_date,
                id: "{{ $data->id }}",
                group: "{{ $data->GroupID }}",
                invNum: "{{ $data->InvoiceNum }}"
            },
            success: function(response) {
                if (response.status === 200) {
                    Toast.fire({
                        position: 'top-end',
                        title: 'Perubahan berhasil disimpan!',
                        icon: "success"
                    });
                    $("#invoice_date").addClass('bg-light-primary').prop('readonly', true)
                    $("#supplier_inv_date").addClass('bg-light-primary').prop('readonly', true)
                    $("#applied_date").addClass('bg-light-primary').prop('readonly', true)
                    $("#terms").addClass('bg-light-primary').prop('readonly', true)
                    $("#update_btn").css('display', 'block')
                    $("#submit_btn").css('display', 'none')
                } else {
                    Toast.fire({
                        position: 'top-end',
                        title: response.message || 'Gagal menyimpan perubahan',
                        icon: "error"
                    });
                }
            },
            error: function(xhr) {
                console.error(xhr);
                Toast.fire({
                    position: 'top-end',
                    title: 'Terjadi kesalahan pada server',
                    icon: "error"
                });
            }
        });
    }

    function document_preview(linkedBtn) {
        if ($.fn.DataTable.isDataTable('#detailItemTb')) {
            $('#detailItemTb').DataTable().clear().destroy();
        }
        $("#modalDtl").modal('show')
        $("#detailItemTb").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('ap_invoice/detail_packslip') }}",
                type: "POST",
                data: function(d) {
                    d._token = "{{ csrf_token() }}"
                    d.linkedBtn = linkedBtn
                }
            },
            columns: [{
                data: 'no',
                orderable: false,
                searchable: false,
                className: 'text-center'
            }, {
                data: 'PackSlip'
            }, {
                data: 'PartNum'
            }, {
                data: 'PartDesc'
            }, {
                data: 'Qty'
            }, {
                data: 'PricePO'
            }, {
                data: 'PriceGR'
            }, {
                data: 'AmountPO'
            }, {
                data: 'AmountGR'
            }, {
                data: 'UOM'
            }],
            createdRow: function(row, data, dataIndex) {
                let PO = parseFloat(String(data.PricePO).replace(/[Rp\s.]/g, '').replace(',', '.')) || 0;
                let GR = parseFloat(String(data.PriceGR).replace(/[Rp\s.]/g, '').replace(',', '.')) || 0;
                let PricePO = parseFloat(String(data.PricePO).replace(/[Rp\s.]/g, '').replace(',', '.')) ||
                    0;
                let PriceGR = parseFloat(String(data.PriceGR).replace(/[Rp\s.]/g, '').replace(',', '.')) ||
                    0;
                if (PO !== GR || PriceGR !== PricePO) {
                    let cells = $(row).find("td");
                    // $(row).addClass('bg-warning');
                    $(cells[5]).css("color", "red");
                    $(cells[6]).css("color", "red");
                    $(cells[7]).css("color", "red");
                    $(cells[8]).css("color", "red");
                }
            }
        })
    }

    function showPdfModal(file) {
        const folder = "{{ $data->InvoiceNum }}";
        const baseUrl = `https://vendor.summitadyawinsa.co.id/invoice/document/${folder}/`;
        // const baseUrl = `http://127.0.0.1:8000/public/${folder}/`
        const pdfUrl = baseUrl + file;

        // console.log(pdfUrl);

        document.getElementById("pdfFrame").src = pdfUrl;
        $("#modalDocument").modal('show');
    }
    $("#showModalItemAll").on('click', function() {
        if ($.fn.DataTable.isDataTable($("#all_item_tbl"))) {
            $("#all_item_tbl").DataTable().clear().destroy()
        }
        $("#modalItemAll").modal('show')
        $("#all_item_tbl").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('ap_invoice/all_item_tbl') }}",
                type: "POST",
                data: function(d) {
                    d._token = "{{ csrf_token() }}",
                        d.PONum = $("#PONum").val(),
                        d.InvNumber = $("#InvNumber").val(),
                        d.Group = $("#group_id").val(),
                        d.search = $("#search_item").val()
                }
            },
            columns: [{
                data: 'no',
                className: 'text-center'
            }, {
                data: 'PartNum'
            }, {
                data: 'PartDesc'
            }, {
                data: 'Qty'
            }, {
                data: 'PricePO'
            }, {
                data: 'PriceGR'
            }, {
                data: 'AmountPO'
            }, {
                data: 'AmountGR'
            }, {
                data: 'UOM'
            }],
            footerCallback: function(row, data, start, end, display) {
                let api = this.api();
                let json = api.ajax.json();
                if (json && json.summary) {
                    let totalPO = parseFloat(json.summary.TotalPO) || 0;
                    let totalGR = parseFloat(json.summary.TotalGR) || 0;
                    $("#total_po_amount").html("Rp " + totalPO.toLocaleString("id-ID"));
                    $("#total_gr_amount").html("Rp " + totalGR.toLocaleString("id-ID"));
                }
            },
            createdRow: function(row, data, dataIndex) {
                let PO = parseFloat(String(data.PricePO).replace(/[Rp\s.]/g, '').replace(',',
                    '.')) || 0;
                let GR = parseFloat(String(data.PriceGR).replace(/[Rp\s.]/g, '').replace(',',
                    '.')) || 0;
                let PricePO = parseFloat(String(data.PricePO).replace(/[Rp\s.]/g, '').replace(',',
                        '.')) ||
                    0;
                let PriceGR = parseFloat(String(data.PriceGR).replace(/[Rp\s.]/g, '').replace(',',
                        '.')) ||
                    0;
                if (PO !== GR || PriceGR !== PricePO) {
                    let cells = $(row).find("td");
                    $(cells[4]).css("color", "red");
                    $(cells[5]).css("color", "red");
                    $(cells[6]).css("color", "red");
                    $(cells[7]).css("color", "red");
                    $(cells[8]).css("color", "red");
                }
            }
        })
    })
    $("#search_item").on('change', function() {
        $("#all_item_tbl").DataTable().ajax.reload()
    })

    function exportExcel() {
        const PONum = $("#PONum").val()
        const InvNum = $("#InvNumber").val()
        const GroupID = $("#group_id").val()
        const url = "{{ url('ap_invoice/export') }}" +
            "?PONum=" + encodeURIComponent(PONum) +
            "&Group=" + encodeURIComponent(GroupID) +
            "&InvNum=" + encodeURIComponent(InvNum);
        window.location.href = url;
    }
    $("#recalculateBtn").on('click', function() {
        const inv = $("#InvNumber").val()
        // $("#btn_text_recalculate").hide()
        $("#spin_btn_recalculate").show()
        if (!inv) {
            Toast.fire({
                position: 'top-end',
                title: 'Invoice Required!',
                icon: "error"
            });
            return
        }
        $.ajax({
            url: "{{ url('ap_invoice/recalculate') }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                inv: inv
            },
            success: function(response) {
                if (response.status == 'success') {
                    Toast.fire({
                        position: 'top-end',
                        title: "Recalculate Successfully",
                        icon: "success"
                    });
                    checkStatus()
                    show_new_approval()
                }
                // $("#btn_text_recalculate").show()
                $("#spin_btn_recalculate").hide()
            },
            error: function(xhr) {
                console.log(xhr.message.responseText);
            }
        })
    })
</script>
