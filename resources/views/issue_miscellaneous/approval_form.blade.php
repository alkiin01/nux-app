<input type="hidden" id="ReviewDocNum" value="{{ $DocNum ?? '' }}" />

<div class="card mb-5">
    <div class="card-header">
        <h3 class="card-title">Document Information</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <label class="form-label fw-bold">Document Number</label>
                <input type="text" class="form-control form-control-sm" id="DocNum" readonly />
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Document Date</label>
                <input type="text" class="form-control form-control-sm" id="DocDate" readonly />
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Category</label>
                <select class="form-select form-select-solid" data-kt-select2="true"
                    data-placeholder="Select option" data-allow-clear="false" id="Category"
                    name="Category" data-hide-search="false">
                    <option value=""></option>
                    <option value="INV3">Store Room (SR)</option>
                    <option value="INV6">General Affairs (GA)</option>
                </select>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-4">
                <label class="form-label fw-bold">Submitted By</label>
                <input type="text" class="form-control form-control-sm" id="SubmittedBy" readonly />
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Submitted At</label>
                <input type="text" class="form-control form-control-sm" id="SubmittedAt" readonly />
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Created By</label>
                <input type="text" class="form-control form-control-sm" id="CreatedBy" readonly />
            </div>
            <div class="col-md-4 mt-3">
                <label class="form-label fw-bold">Status</label>
                <input type="text" class="form-control form-control-sm" id="RequestStatus" readonly />
            </div>
        </div>
    </div>
</div>

<div class="card mb-5">
    <div class="card-header">
        <h3 class="card-title">Document Items</h3>
    </div>
    <div class="card-body">
        <table class="table align-middle table-row-dashed table-striped gy-2 fs-7" id="kt_review_table">
            <thead>
                <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                    <th class="min-w-20px">No</th>
                    <th class="min-w-150px">PartNum</th>
                    <th class="min-w-200px">Part Description</th>
                    <th class="min-w-80px text-end">Qty Request</th>
                    <th class="min-w-80px text-end">Qty Submit</th>
                    <th class="min-w-100px">From WH</th>
                    <th class="min-w-100px">Bin</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between">
            <button type="button" class="btn btn-light btn-sm" onclick="back_to_approval_list()">Back to List</button>
            <div>
                <button type="button" class="btn btn-danger btn-sm me-2" id="btn_reject" onclick="reject_document()">
                    <span class="svg-icon svg-icon-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                transform="rotate(-45 6 17.3137)" fill="black" />
                            <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                transform="rotate(45 7.41422 6)" fill="black" />
                        </svg>
                    </span>
                    <span id="spinner_reject" class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;"></span>
                    <span id="btn_text_reject">Reject</span>
                </button>
                <button type="button" class="btn btn-success btn-sm" id="btn_approve" onclick="approve_document()">
                    <span class="svg-icon svg-icon-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path
                                d="M9.89557 13.4982L7.79487 11.2651C7.26967 10.7068 6.38251 10.7068 5.85731 11.2651C5.37559 11.7772 5.37559 12.5757 5.85731 13.0878L9.74989 17.2257C10.1448 17.6455 10.8118 17.6455 11.2066 17.2257L18.1427 9.85252C18.6244 9.34044 18.6244 8.54191 18.1427 8.02984C17.6175 7.47154 16.7303 7.47154 16.2051 8.02984L11.061 13.4982C10.7451 13.834 10.2115 13.834 9.89557 13.4982Z"
                                fill="black" />
                        </svg>
                    </span>
                    <span id="spinner_approve" class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;"></span>
                    <span id="btn_text_approve">Approve</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Use global Toast from main layout - provide fallback if not available
    var approvalFormToast = window.Toast || {};
    if (!approvalFormToast.fire) {
        approvalFormToast = {
            fire: function(options) {
                console.warn('Toast not available, using alert fallback', options);
                alert(options.title || options.message || 'Action result');
            }
        };
    }

    function init_approval_form(docNumFromParent) {
        let docNum = docNumFromParent || $('#ReviewDocNum').val();

        if (!docNum) {
            approvalFormToast.fire({
                position: 'top-end',
                title: "No document specified!",
                icon: "error"
            });
            return;
        }

        load_document(docNum);
    }

    function back_to_approval_list() {
        if (typeof back_approval_home === 'function') {
            back_approval_home();
            return;
        }

        window.location.href = "{{ route('issue_miscellaneous.approval_index') }}";
    }

    function load_document(docNum) {
        $.ajax({
            url: "{{ route('issue_miscellaneous.approval_detail') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                DocNum: docNum
            },
            success: function(response) {
                if (response.status === 'success') {
                    $('#DocNum').val(response.header.DocNum);
                    $('#DocDate').val(response.header.DocDate);
                    $('#Category').val(response.header.Category).change();
                    $('#RequestStatus').val(response.header.RequestStatus || 'PENDING_APPROVAL');
                    $('#SubmittedBy').val(response.header.SubmittedBy);
                    $('#SubmittedAt').val(response.header.SubmittedAt);
                    $('#CreatedBy').val(response.header.CreatedBy);

                    // Initialize Select2 for Category and disable it
                    if (!$('#Category').hasClass('select2-hidden-accessible')) {
                        $('#Category').select2({
                            placeholder: 'Select option',
                            allowClear: false
                        });
                    }
                    $('#Category').prop('disabled', true);

                    const canReview = (response.header.RequestStatus || 'PENDING_APPROVAL') === 'PENDING_APPROVAL';
                    $('#btn_approve').prop('disabled', !canReview);
                    $('#btn_reject').prop('disabled', !canReview);

                    let tbody = $('#kt_review_table tbody');
                    tbody.empty();

                    response.details.forEach((item, index) => {
                        let row = `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${item.PartNum}</td>
                                <td>${item.PartName || ''}</td>
                                <td class="text-end">${item.QtyMove}</td>
                                <td class="text-end">${item.QtySubmit}</td>
                                <td>${item.FromWarehouseDesc}</td>
                                <td>${item.FromBinID}</td>
                            </tr>
                        `;
                        tbody.append(row);
                    });
                } else {
                    approvalFormToast.fire({
                        position: 'top-end',
                        title: response.message || "Failed to load document!",
                        icon: "error"
                    });
                }
            },
            error: function() {
                approvalFormToast.fire({
                    position: 'top-end',
                    title: "Error loading document!",
                    icon: "error"
                });
            }
        });
    }

    function approve_document() {
        if (typeof Swal === 'undefined') {
            if (confirm('Approve this document?')) {
                execute_approve();
            }
            return;
        }
        Swal.fire({
            title: 'Approve Document?',
            text: "Are you sure you want to approve this document?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Approve!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                execute_approve();
            }
        });
    }

    function execute_approve() {
        const button = document.getElementById('btn_approve');
        const spinner = document.getElementById('spinner_approve');
        const buttonText = document.getElementById('btn_text_approve');
        const docNum = $('#DocNum').val();

        // Null check for elements
        if (!button || !spinner || !buttonText) {
            console.error('Required button elements not found');
            approvalFormToast.fire({
                position: 'top-end',
                title: "Error: UI elements not found!",
                icon: "error"
            });
            return;
        }

        button.disabled = true;
        spinner.style.display = 'inline-block';
        buttonText.textContent = 'Approving...';

        $.ajax({
            url: "{{ route('issue_miscellaneous.approve_document') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                DocNum: docNum
            },
            success: function(response) {
                spinner.style.display = 'none';
                buttonText.textContent = 'Approve';
                button.disabled = false;

                if (response.status === 'success') {
                    approvalFormToast.fire({
                        position: 'top-end',
                        title: response.message || "Document approved successfully!",
                        icon: "success"
                    });

                    setTimeout(function() {
                        back_to_approval_list();
                    }, 800);
                } else {
                    approvalFormToast.fire({
                        position: 'top-end',
                        title: response.message || "Failed to approve document!",
                        icon: "error"
                    });
                }
            },
            error: function() {
                spinner.style.display = 'none';
                buttonText.textContent = 'Approve';
                button.disabled = false;
                approvalFormToast.fire({
                    position: 'top-end',
                    title: "Error approving document!",
                    icon: "error"
                });
            }
        });
    }

    function reject_document() {
        if (typeof Swal === 'undefined') {
            var rejectionReason = prompt('Reject this document?\n\n(You can enter a reason or leave empty)');
            if (rejectionReason !== null) {
                execute_reject(rejectionReason || '');
            }
            return;
        }
        Swal.fire({
            title: 'Reject Document?',
            text: "Rejected reason can be left empty.",
            icon: 'warning',
            input: 'text',
            inputPlaceholder: 'Rejected reason (optional)',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Reject!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                execute_reject(result.value || '');
            }
        });
    }

    function execute_reject(rejectedReason) {
        const button = document.getElementById('btn_reject');
        const spinner = document.getElementById('spinner_reject');
        const buttonText = document.getElementById('btn_text_reject');
        const docNum = $('#DocNum').val();

        // Null check for elements
        if (!button || !spinner || !buttonText) {
            console.error('Required button elements not found');
            approvalFormToast.fire({
                position: 'top-end',
                title: "Error: UI elements not found!",
                icon: "error"
            });
            return;
        }

        button.disabled = true;
        spinner.style.display = 'inline-block';
        buttonText.textContent = 'Rejecting...';

        $.ajax({
            url: "{{ route('issue_miscellaneous.reject_document') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                DocNum: docNum,
                RejectedReason: rejectedReason
            },
            success: function(response) {
                spinner.style.display = 'none';
                buttonText.textContent = 'Reject';
                button.disabled = false;

                if (response.status === 'success') {
                    approvalFormToast.fire({
                        position: 'top-end',
                        title: response.message || "Document rejected!",
                        icon: "success"
                    });

                    setTimeout(function() {
                        back_to_approval_list();
                    }, 800);
                } else {
                    approvalFormToast.fire({
                        position: 'top-end',
                        title: response.message || "Failed to reject document!",
                        icon: "error"
                    });
                }
            },
            error: function() {
                spinner.style.display = 'none';
                buttonText.textContent = 'Reject';
                button.disabled = false;
                approvalFormToast.fire({
                    position: 'top-end',
                    title: "Error rejecting document!",
                    icon: "error"
                });
            }
        });
    }

    // Only initialize if opened directly (not via AJAX injection)
    // When loaded via AJAX, parent calls init_approval_form(docNum) explicitly
    if (window.location.pathname.includes('approval_form')) {
        init_approval_form();
    }
</script>
