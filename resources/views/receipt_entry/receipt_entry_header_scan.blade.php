<script>
     $(document).ready(function() {
         var trc_unix_id = '<?php echo $trc_unix_id; ?>';
         window.history.pushState('', '', '<?php echo env('BASE_URL'); ?>/receipt_entry?ref_doc=' + trc_unix_id);
         $("#temp_id").val(trc_unix_id);
         var temp_id = $("#temp_id").val();
         if (temp_id != '') {
             $("#PackSlip").addClass('bg-light-primary').prop('readonly', true);
             $("#PONum").addClass('bg-light-primary').prop('readonly', true);
             $("#kt_form_header_tab").removeClass('disabled').prop('disabled', false);
             $("#kt_form_detail_tab").removeClass('disabled').prop('disabled', false);
             $("#kt_form_scan_detail_tab").removeClass('disabled').prop('disabled', false);
             $("#btn_add_detail_document").removeClass('disabled').prop('disabled', false);
             $("#kt_form_attachment_tab").removeClass('disabled').prop('disabled', false);
             $("#kt_form_preview_tab").removeClass('disabled').prop('disabled', false);
         } else {
             $("#kt_form_header_tab").addClass('disabled').prop('disabled', true);
             $("#kt_form_detail_tab").addClass('disabled').prop('disabled', true);
             $("#kt_form_scan_detail_tab").removeClass('disabled').prop('disabled', true);
             $("#btn_add_detail_document").addClass('disabled').prop('disabled', true);
             $("#kt_form_attachment_tab").addClass('disabled').prop('disabled', true);
             $("#kt_form_preview_tab").addClass('disabled').prop('disabled', true);
         }
         detail_table();
         detail_po_list_table();
     })
 </script>
 <div class="card-body">
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
     <div class="form-group mb-5">
         <label>Scan QR<span class="text-danger">*</span></label>
         <input type="text" id="ScanQRInput" class="form-control" onchange="scanQRInput()">
     </div>
     <div class="row" id="form" style="display: none;">
         <div class="col-md-6 mb-5">
             <form>
                 <div class="form-group mb-5">
                     <label for="exampleInputPassword1">Legal Number <span class="text-danger">*</span></label>
                     <input type="text" class="form-control bg-light-primary" id="LegalNumber"
                         value="{{ $LegalNumber }}" readonly />
                 </div>

                 <div class="form-group mb-5">
                     <label>PO Number <span class="text-danger">*</span></label>
                     <input type="number" class="form-control bg-light-primary" id="PONum" name="PONum"
                         value="{{ $PONum }}" readonly />
                 </div>

                 <div class="form-group">
                     <label>Packing Slip<span class="text-danger">*</span></label>
                     <input type="text" class="form-control bg-light-primary" id="PackSlip" name="PackSlip"
                         value="{{ $PackSlip }}" readonly />
                 </div>
             </form>
         </div>

         <div class="col-md-6  mb-5">
             <form>
                 <div class="form-group mb-5">
                     <label>Vendor<span class="text-danger">*</span></label>
                     <input type="text" class="form-control bg-light-primary" id="VendorName"
                         value="{{ $VendorNum }}" readonly />
                 </div>

                 <div class="form-group mb-5">
                     <label>Entry Date<span class="text-danger">*</span></label>
                     <input type="date" class="form-control bg-light-primary" id="EntryDate"
                         value="{{ $EntryDate }}" readonly />
                 </div>

                 <div class="form-group">
                     <label>Arrived Date<span class="text-danger">*</span></label>
                     <input type="date" class="form-control" id="ArrivedDate" name="ArrivedDate"
                         value="{{ $ArrivedDate }}" />
                 </div>
             </form>
         </div>

         <div class="col-md-12 mb-5">
             <form>
                 <div class="form-group">
                     <label>Comment</label>
                     <textarea class="form-control" name="ReceiptComment" id="ReceiptComment">{{ $ReceiptComment }}</textarea>
                 </div>
                 <input type="text" id="vendorNum" hidden>
                 <input type="text" id="PoLine" hidden>
             </form>
         </div>

         <hr style="color: gray">

         <div class="col-md-12" style="text-align: right;">
             <button type="button" class="btn btn-light-danger btn-sm mr-2 mt-2" id="btn_delete_gr"
                 onclick="delete_gr_confirm()">
                 <span class="svg-icon svg-icon-primary svg-icon-2" id="svg_delete_gr">
                     <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                         height="24px" viewBox="0 0 24 24" version="1.1">
                         <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                             <rect x="0" y="0" width="24" height="24" />
                             <path
                                 d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z"
                                 fill="#000000" fill-rule="nonzero" />
                             <path
                                 d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z"
                                 fill="#000000" opacity="0.3" />
                         </g>
                     </svg>
                 </span>
                 <span id="btn_text_delete_gr">Delete GR</span>
                 <span id="spinner_delete_gr" class="spinner-border spinner-border-sm align-middle ms-2"
                     style="display: none;"></span>
             </button>

             <button type="button" class="btn btn-primary btn-sm mr-2 mt-2" id="btn_update_gr" onclick="insert_gr()">
                 <span class="svg-icon svg-icon-primary svg-icon-2" id="svg_update_gr">
                     <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                         width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                         <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                             <polygon points="0 0 24 0 24 24 0 24" />
                             <path
                                 d="M17,4 L6,4 C4.79111111,4 4,4.7 4,6 L4,18 C4,19.3 4.79111111,20 6,20 L18,20 C19.2,20 20,19.3 20,18 L20,7.20710678 C20,7.07449854 19.9473216,6.94732158 19.8535534,6.85355339 L17,4 Z M17,11 L7,11 L7,4 L17,4 L17,11 Z"
                                 fill="#000000" fill-rule="nonzero" />
                             <rect fill="#000000" opacity="0.3" x="12" y="4" width="3" height="5"
                                 rx="0.5" />
                         </g>
                     </svg>
                 </span>
                 <span id="btn_text_update_gr">Update GR</span>
                 <span id="spinner_update_gr" class="spinner-border spinner-border-sm align-middle ms-2"
                     style="display: none;"></span>
             </button>
         </div>
     </div>
 </div>

 <div style="background-color: #f5f8fa;"><br></div>

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
                     placeholder="PartNum" />
             </div>
         </div>
         <div class="card-toolbar">
             <button type="button" class="btn btn-light-success btn-sm me-3" id="btn_add_detail_document"
                 onclick="document.getElementById('kt_form_detail_tab').click()">
                 <span id="svg_add_detail_document" class="svg-icon svg-icon-2">
                     <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                         width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                         <defs />
                         <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                             <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1" />
                             <rect fill="#000000" opacity="0.3"
                                 transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) "
                                 x="4" y="11" width="16" height="2" rx="1" />
                         </g>
                     </svg>
                 </span>
                 <span id="spinner_add_detail_document" class="spinner-border spinner-border-sm align-middle ms-2"
                     style="display: none;"></span>
                 <span id="btn_text_add_detail_document">Add Line</span>
             </button>
         </div>
     </div>

     <div class="card-body pt-0">
         <table class="table align-middle table-row-dashed table-striped gy-2 fs-7" id="kt_detail_table">
             <thead>
                 <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                     <th class="min-w-20px pe-2">No</th>
                     <th class="min-w-20px">Action</th>
                     <th class="min-w-100px">PartNum</th>
                     <th class="min-w-30px">Qty</th>
                     <th class="min-w-50px">PO</th>
                     <th class="min-w-100px">Status</th>
                 </tr>
             </thead>
             <tfoot>
                 <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                     <th class="min-w-20px pe-2">No</th>
                     <th class="min-w-20px">Action</th>
                     <th class="min-w-100px">PartNum</th>
                     <th class="min-w-30px">Qty</th>
                     <th class="min-w-50px">PO</th>
                     <th class="min-w-100px">Status</th>
                 </tr>
             </tfoot>
             <tbody></tbody>
         </table>
     </div>
 </div>
 <input type="text" hidden id="VendorNum" value="{{ $VendorNum }}">

 <script>
     function scanQRInput() {
         const scanInput = document.getElementById('ScanQRInput').value;
         const scanValue = scanInput.split("~");

         $.ajax({
             type: 'POST',
             url: "{{ route('receipt_entry.get_po_scan') }}",
             data: {
                 _token: "{{ csrf_token() }}",
                 id: scanValue[0],
                 ShipNum: scanValue[1],
                 PONum: scanValue[2]
             },
             success: function(response) {
                 if (response.status === 'success') {
                     const apiData = response.apiData
                    //  if (apiData.code == 200) {
                    //      $("#VendorName").val(apiData.purPointName)
                    //      $("#vendorNum").val(apiData.vendorNum);
                    //  }
                     const header = response.data[0];
                     $('#LegalNumber').val(header.LegalNumber);
                     $('#PONum').val(header.PoNumber);
                     $('#PoLine').val(header.PO);
                     $("#PackSlip").val(header.PackingSlip);
                     $("#vendorNum").val(header.vendorNum);

                     const tableBody = document.querySelector('#kt_detail_table tbody');
                     tableBody.innerHTML = '';

                     if (!response.data || response.data.length === 0) {
                         tableBody.innerHTML =
                             `<tr><td colspan="6" class="text-center">No data available</td></tr>`;
                     } else {
                         response.data.forEach((row, index) => {
                             const tr = document.createElement('tr');
                             tr.innerHTML = `
                            <td>${index + 1}</td>
                            <td>
                                <span class="svg-icon svg-icon-primary svg-icon-2x" style="cursor: pointer;">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="black"></path><path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="black"></path><path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="black"></path></svg>
        </span>
                            </td>
                            <td>${row.PartNum || '-'}</td>
                            <td>${Number(row.Qty).toFixed(0) || '-'}</td>
                            <td>${row.PO || '-'}</td>
                            <td>${row.status || '-'}</td>
                        `;
                             tableBody.appendChild(tr);
                         });
                     }
                 }
             },
             error: function(response) {
                 console.log(response);
             }
         });
     }
     $("#PackSlip").on("keypress", function(event) {
         var invalidChars = ['\'', '"', ',', '~', '.', '?'];
         if (invalidChars.includes(event.key)) {
             event.preventDefault();
         }
     });

     $("#ReceiptComment").on("keypress", function(event) {
         var invalidChars = ['\'', '"', ',', '~', '.', '?'];
         if (invalidChars.includes(event.key)) {
             event.preventDefault();
         }
     });

     function refresh_front_table() {
         if ($.fn.DataTable.isDataTable('#kt_doc_table')) {
             $('#kt_doc_table').DataTable().destroy();
         }
         front_table();
     }

     function insert_gr() {
         const PONum = $("#PONum").val();
        //  const PackSlip = $("#PackSlip").val().replace(/[\'\"\,~\.\?]/g, '');
        const PackSlip = $("#PackSlip").val()
         console.log(PackSlip);
         const ArrivedDate = $("#ArrivedDate").val();
         const ReceiptComment = $("#ReceiptComment").val().replace(/[\'\"\,~\.\?]/g, '');
         const temp_id = $("#temp_id").val();
         const vendorNum = $("#vendorNum").val();

         if (PONum <= 0 || PONum == null) {
             $("#PONum").focus();
             Toast.fire({
                 position: 'top-end',
                 title: "Silahkan PO Number di isi!",
                 icon: "error"
             });
             return false;
         }

         if (PackSlip == '' || PackSlip == null) {
             $("#PackSlip").focus();
             Toast.fire({
                 position: 'top-end',
                 title: "Silahkan PackSlip di isi!",
                 icon: "error"
             });
             return false;
         }

         var button = document.getElementById('btn_update_gr');
         var svg = document.getElementById('svg_update_gr');
         var spinner = document.getElementById('spinner_update_gr');
         var buttonText = document.getElementById('btn_text_update_gr');

         svg.style.display = 'none';
         spinner.style.display = 'inline-block';
         button.disabled = true;

         var token = $("[name=_token]").val();
         var trc_unix_id = $("#temp_id").val();
         const inputSc = $("#ScanQRInput").val();
         const id = inputSc.split("~");

         $.ajax({
             type: 'POST',
             url: "{{ route('receipt_entry.new_insert_gr') }}",
             data: {
                 _token: token,
                 temp_id: temp_id,
                 vendorNum: vendorNum,
                 PONum: PONum,
                 PackSlip: PackSlip,
                 ArrivedDate: ArrivedDate,
                 ReceiptComment: ReceiptComment,
                 LegalNumber: $("#LegalNumber").val(),
                 EntryDate: $("#EntryDate").val(),
                 id: id[0],
                 VendorName: $("#VendorName").val()
             },
             success: function(data) {
                 svg.style.display = 'inline-block';
                 spinner.style.display = 'none';
                 buttonText.textContent = 'Update GR';
                 button.disabled = false;
                //  if (data.code == 201) {
                //     Toast.fire({
                //              position: 'top-end',
                //              title: data.message,
                //              icon: "error"
                //      });
                //  }
                 if (data.code == 200) {
                     $("#LegalNumber").val(data.legalNumber);
                     $("#VendorName").val(data.vendor);
                     $("#VendorNum").val(data.vendorNum);
                     $("#temp_id").val(data.ref_doc);
                     window.history.pushState('', '', '<?php echo env('BASE_URL'); ?>/receipt_entry?ref_doc=' + data
                         .ref_doc);
                     if (data.ref_doc != '') {
                         $("#PackSlip").addClass('bg-light-primary').prop('readonly', true);
                         $("#PONum").addClass('bg-light-primary').prop('readonly', true);
                         $("#kt_form_header_tab").removeClass('disabled').prop('disabled', false);
                         $("#kt_form_detail_tab").removeClass('disabled').prop('disabled', false);
                         $("#btn_add_detail_document").removeClass('disabled').prop('disabled', false);
                         $("#kt_form_attachment_tab").removeClass('disabled').prop('disabled', false);
                         $("#kt_form_preview_tab").removeClass('disabled').prop('disabled', false);
                     }
                     if (Array.isArray(data.results)) {
                         data.results.forEach(function(item) {
                             let iconType = (item.status === 'success') ? "success" : "error";
                             let msg =
                                 `[${item.step}] ${item.message || item.epi_status || item.status}`;
                             if (item.poLine) {
                                 msg = `PO Line ${item.poLine} - ${msg}`;
                             }
                             Toast.fire({
                                 position: 'top-end',
                                 title: msg,
                                 icon: iconType
                             });
                         });
                     } else {
                         Toast.fire({
                             position: 'top-end',
                             title: data.message ?? "Data berhasil update",
                             icon: "success"
                         });
                     }
                    getHeader();
                 } else if (data.code == 4212) {
                     Toast.fire({
                         position: 'top-end',
                         icon: 'error',
                         title: data.transaction_status ?
                             `${data.transaction_status} ${Array.isArray(data.status) ? data.status.join(', ') : data.status}` :
                             (data.status),
                         timer: 60000,
                         timerProgressBar: true,
                     });
                 } else {
                     Toast.fire({
                         position: 'top-end',
                         title: data.transaction_status ?? data.status ?? "Update GR gagal",
                         icon: "error"
                     });
                 }
             },
             error: function(jqXHR, textStatus) {
                Toast.fire({
                         position: 'top-end',
                         title: data.transaction_status ?? data.status ?? "Update GR gagal",
                         icon: "error"
                     });
                 console.error(textStatus);
                 svg.style.display = 'inline-block';
                 spinner.style.display = 'none';
                 buttonText.textContent = 'Update GR';
                 button.disabled = false;
             }
         });
     }

     function delete_gr_confirm() {
         var packSlip = $("#PackSlip").val().replace(/[\'\"\,~\.\?]/g, '');
         return Swal.fire({
             text: "Yakin Hapus ? " + packSlip,
             icon: "warning",
             showCancelButton: true,
             confirmButtonText: "Ya, Hapus",
             cancelButtonText: "Batal",
             confirmButtonColor: "#3085d6",
             cancelButtonColor: "#d33",
             customClass: {
                 confirmButton: "btn btn-primary",
                 cancelButton: "btn btn-secondary"
             },
             buttonsStyling: false
         }).then((result) => {
             if (result.isConfirmed) {
                 selectedDeleteGR()
             } else {
                 console.log('Penghapusan dibatalkan');
             }
         });
     }

     function selectedDeleteGR() {
         return Swal.fire({
             text: "Pilih Metode Hapus",
             icon: "warning",
             showCancelButton: true,
             confirmButtonText: "Hapus GR",
             cancelButtonText: "Reject",
             confirmButtonColor: "#3085d6",
             cancelButtonColor: "#d33",
             customClass: {
                 confirmButton: "btn btn-primary",
                 cancelButton: "btn btn-warning"
             },
             buttonsStyling: false
         }).then((result) => {
             if (result.isConfirmed) {
                 delete_gr();
             } else if (result.dismiss === Swal.DismissReason.cancel) {
                 deleteGRSJ();
             } else {
                 console.log('Dibatalkan');
             }
         });
     }


     function deleteGRSJ() {
         var poNum = $("#PONum").val();
         var packSlip = $("#PackSlip").val().replace(/[\'\"\,~\.\?]/g, '');
         var ArrivedDate = $("#ArrivedDate").val();
         var ReceiptComment = $("#ReceiptComment").val().replace(/[\'\"\,~\.\?]/g, '');
         var temp_id = $("#temp_id").val();
         var vendorNum = $("#VendorNum").val();
         if (temp_id == '') {
             $("#PONum").focus();
             Toast.fire({
                 position: 'top-end',
                 title: "Data tidak ditemukan",
                 icon: "error"
             })
             return false;
         }

         var button = document.getElementById('btn_delete_gr');
         var svg = document.getElementById('svg_delete_gr');
         var spinner = document.getElementById('spinner_delete_gr');
         var buttonText = document.getElementById('btn_text_delete_gr');
         svg.style.display = 'none';
         spinner.style.display = 'inline-block';
         buttonText.textContent = 'Please Wait...';
         button.disabled = true;
         var token = $("[name=_token]").val();
         var trc_unix_id = $("#temp_id").val();
         var string = "&_token=" + token + "&temp_id=" + temp_id + "&vendorNum=" + vendorNum + "&poNum=" + poNum +
             "&packSlip=" + packSlip + "&ArrivedDate=" + ArrivedDate + "&ReceiptComment=" + ReceiptComment + "&status=" +
             "reject";
         $.ajax({
             type: 'POST',
             url: "{{ route('receipt_entry.delete_gr') }}",
             data: string,
             cache: false,
             dataType: 'json',
             success: function(data) {

                 svg.style.display = 'inline-block';
                 spinner.style.display = 'none';
                 buttonText.textContent = 'Delete GR';
                 button.disabled = false;

                 if (data.code == 200) {
                     if (data.transaction_code == 200) {
                         Toast.fire({
                             position: 'top-end',
                             title: "Data berhasil dihapus",
                             icon: "success"
                         })
                         backHome();
                     } else {
                         Toast.fire({
                             position: 'top-end',
                             title: data.transaction_status,
                             icon: "error"
                         })
                     }
                 } else {
                     Toast.fire({
                         position: 'top-end',
                         title: data.status,
                         icon: "error"
                     })
                 }

             },
             error: function(jqXHR, textStatus) {
                 Toast.fire({
                     position: 'bottom-end',
                     title: " Please reload and try again! ",
                     icon: "error"
                 })

                 svg.style.display = 'inline-block';
                 spinner.style.display = 'none';
                 buttonText.textContent = 'Delete GR';
                 button.disabled = false;
             }
         })
     }

     function delete_gr() {

         var poNum = $("#PONum").val();
         var packSlip = $("#PackSlip").val().replace(/[\'\"\,~\.\?]/g, '');
         var ArrivedDate = $("#ArrivedDate").val();
         var ReceiptComment = $("#ReceiptComment").val().replace(/[\'\"\,~\.\?]/g, '');
         var temp_id = $("#temp_id").val();
         var vendorNum = $("#VendorNum").val();
         if (temp_id == '') {
             $("#PONum").focus();
             Toast.fire({
                 position: 'top-end',
                 title: "Data tidak ditemukan",
                 icon: "error"
             })
             return false;
         }

         var button = document.getElementById('btn_delete_gr');
         var svg = document.getElementById('svg_delete_gr');
         var spinner = document.getElementById('spinner_delete_gr');
         var buttonText = document.getElementById('btn_text_delete_gr');
         svg.style.display = 'none';
         spinner.style.display = 'inline-block';
         buttonText.textContent = 'Please Wait...';
         button.disabled = true;
         var token = $("[name=_token]").val();
         var trc_unix_id = $("#temp_id").val();
         var string = "&_token=" + token + "&temp_id=" + temp_id + "&vendorNum=" + vendorNum + "&poNum=" + poNum +
             "&packSlip=" + packSlip + "&ArrivedDate=" + ArrivedDate + "&ReceiptComment=" + ReceiptComment + "&status=" +
             "confirm";
         $.ajax({
             type: 'POST',
             url: "{{ route('receipt_entry.delete_gr') }}",
             data: string,
             cache: false,
             dataType: 'json',
             success: function(data) {

                 svg.style.display = 'inline-block';
                 spinner.style.display = 'none';
                 buttonText.textContent = 'Delete GR';
                 button.disabled = false;

                 if (data.code == 200) {
                     if (data.transaction_code == 200) {
                         Toast.fire({
                             position: 'top-end',
                             title: "Data berhasil dihapus",
                             icon: "success"
                         })
                         backHome();
                     } else {
                         Toast.fire({
                             position: 'top-end',
                             title: data.transaction_status,
                             icon: "error"
                         })
                     }
                 } else {
                     Toast.fire({
                         position: 'top-end',
                         title: data.status,
                         icon: "error"
                     })
                 }

             },
             error: function(jqXHR, textStatus) {
                 Toast.fire({
                     position: 'bottom-end',
                     title: " Please reload and try again! ",
                     icon: "error"
                 })

                 svg.style.display = 'inline-block';
                 spinner.style.display = 'none';
                 buttonText.textContent = 'Delete GR';
                 button.disabled = false;
             }
         })
     }
 </script>
