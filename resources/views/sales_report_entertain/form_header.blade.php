      <div class="col-xxl-12">
          <div class="card mt-5">
              <div class="card-header">
                  <div class="card-title">
                      {{ isset($header) && $header->SysID ? 'Edit Entertain Report Header' : 'Add Entertain Report Header' }}
                  </div>
                  <div class="card-toolbar">
                      <button class="btn btn-success btn-sm" id="btn_back_home" onclick="backHome()">
                          <span id="svg_back_home" class="svg-icon svg-icon-2">
                              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                  <g fill="none">
                                      <rect width="24" height="24" />
                                      <path d="M3.957 8.415L11.479 3.819a1 1 0 0 1 1.042 0l7.522 4.596A2 2 0 0 1 21 10.122V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-8.878a2 2 0 0 1 .957-1.707ZM10 13a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1h-4Z" fill="#000" />
                                  </g>
                              </svg>
                          </span>
                          <span id="spinner_back_home" class="spinner-border spinner-border-sm svg-icon svg-icon-2" style="display:none;"></span>
                          <span id="btn_text_back_home">Back</span>
                      </button>
                  </div>
              </div>

              <div class="card-body">
                  <form id="entertain-header-form">
                      <div class="row">
                          <div class="col-md-6">

                              <div class="form-group mb-3">
                                  <label>Date <span class="text-danger">*</span></label>
                                  <input type="date" class="form-control" id="Date" name="Date" required
                                      value="{{ $header->Date ?? now()->format('Y-m-d') }}">
                              </div>


                              <div class="form-group mb-3">
                                  <label>Customer <span class="text-danger">*</span></label>
                                  @php $current = old('Customer', $header->Customer ?? ''); @endphp
                                  <select class="form-select" id="Customer" name="Customer"
                                      data-control="select2" data-placeholder="Pilih Customer" required>
                                      <option value="">-- Select Option --</option>
                                      @foreach($customers as $c)
                                      @php $name = $c->Name; @endphp
                                      <option value="{{ $name }}" @selected($current===$name)>{{ $name }}</option>
                                      @endforeach
                                      <option value="Others">Others</option>
                                  </select>                      
                                </div>

                              <div class="form-group mb-3">
                                  <label>Num CA  <span class="text-danger">*</span></label>
                                  <input type="text" class="form-control" id="NumCA" name="NumCA" required

                                      value="{{ $header->NumCA ?? '' }}">
                              </div>
                          </div>

                          <div class="col-md-6">

                              <div class="form-group mb-3">
                                  <label>Total Amount <small class="text-muted">(auto)</small></label>
                                  <input type="text" id="TotalAmount" class="form-control"
                                      value="{{ isset($header) ? number_format($header->TotalAmount ?? 0, 2, '.', '') : '0.00' }}"
                                      readonly>
                                  <input type="hidden" id="TotalAmountRaw" name="TotalAmount" value="{{ $header->TotalAmount ?? 0 }}">
                              </div>

                              <div class="form-group mb-3">
                                  <label>Category <span class="text-danger">*</span></label>
                                  @php $cat = strtolower(old('Category', $header->Category ?? '')); @endphp
                                  <select class="form-select" id="Category" name="Category" required>
                                      <option value="" disabled @selected($cat==='' )>-- Select Option --</option>
                                      <option value="Golf" @selected($cat==='golf' )>Golf</option>
                                      <option value="Regular" @selected($cat==='regular' )>Regular</option>
                                      <option value="Membership" @selected($cat==='MemShip' )>Membership</option>
                                      <option value="Bench Mark" @selected($cat==='BenchMark' )>Bench Mark</option>
                                      <option value="Consumable" @selected($cat==='Consumable' )>Consumable</option>
                                  </select>
                              </div>


                              <div class="form-group mb-3">
                                  <label>Description</label>
                                  <textarea class="form-control" id="Description" name="Description">{{ $header->Description ?? '' }}</textarea>
                              </div>
                          </div>
                      </div>

                      <input type="hidden" id="ID_Report" name="ID_Report" value="{{ $header->SysID ?? '' }}">

                      <div class="text-start">
                          <button type="button" class="btn btn-primary btn-sm" id="btn_save_header" onclick="submitEntertainHeader()">
                              <span id="svg_save_header" class="svg-icon svg-icon-2">
                                  <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                                      <g stroke="none" fill="none">
                                          <polygon points="0 0 24 0 24 24 0 24" />
                                          <path d="M17,4 L6,4 C4.79111111,4 4,4.7 4,6 L4,18 C4,19.3 4.79111111,20 6,20 L18,20 C19.2,20 20,19.3 20,18 L20,7.20710678 C20,7.07449854 19.9473216,6.94732158 19.8535534,6.85355339 L17,4 Z M17,11 L7,11 L7,4 L17,4 L17,11 Z" fill="#000000" />
                                          <rect fill="#000000" opacity="0.3" x="12" y="4" width="3" height="5" rx="0.5" />
                                      </g>
                                  </svg>
                              </span>
                              <span id="spinner_save_header" class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;"></span>
                              <span id="btn_text_save_header">Save</span>
                          </button>
                      </div>
                  </form>
              </div>
          </div>


          <div class="card mt-5">
              <div class="card-header d-flex align-items-center border-1 pt-6 pb-6 mb-5">
                  <div class="card-toolbar ms-auto">
                      <form action="{{ route('entertain.import') }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-center gap-3 position-relative my-1">
                          @csrf
                          <input type="file" name="file" required class="form-control form-control-sm" style="max-width:250px;">

                          <button type="submit" id="btn_import_excel" class="btn btn-light-success btn-sm p-0" title="Import Excel" style="width:40px;height:35px;display:flex;align-items:center;justify-content:center;">
                              <span id="svg_import_excel" class="svg-icon svg-icon-2 p-0 m-0">
                                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-arrow-up" viewBox="0 0 16 16">
                                      <path d="M8 6.5a.5.5 0 0 0-.5.5v3.793L6.354 9.146a.5.5 0 1 0-.708.708l2 2a.5.5 0 0 0 .708 0l2-2a.5.5 0 1 0-.708-.708L8.5 10.793V7a.5.5 0 0 0-.5-.5z" />
                                      <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zm-3-.5a.5.5 0 0 1-.5-.5V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4h-2.5z" />
                                  </svg>
                              </span>
                              <span id="spinner_import_excel" class="spinner-border spinner-border-sm align-middle" style="display:none;"></span>
                          </button>

                          <button type="button" class="btn btn-primary btn-sm me-3" id="btn_create_detail" onclick="create_detail()" @if(empty($header->SysID)) disabled @endif>
                              <span id="svg_create_detail" class="svg-icon svg-icon-2">
                                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                      <g fill="none">
                                          <rect fill="#000" x="4" y="11" width="16" height="2" rx="1" />
                                          <rect fill="#000" opacity=".3" transform="rotate(-90 12 12)" x="4" y="11" width="16" height="2" rx="1" />
                                      </g>
                                  </svg>
                              </span>
                              <span id="spinner_create_detail" class="spinner-border spinner-border-sm align-middle ms-2" style="display:none;"></span>
                              <span id="btn_text_create_detail">Create</span>
                          </button>

                          <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>
                      </form>
                  </div>
              </div>

              <div class="card-body pt-0">
                  <table class="table align-middle table-row-dashed table-striped gy-2 fs-7" id="kt_doc_table">
                      <thead>
                          <tr class="text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                              <th class="min-w-20px pe-2">No</th>
                              <th class="min-w-20px">Item</th>
                              <th class="min-w-100px">Restaurant/Shop</th>
                              <th class="min-w-100px">Amount</th>
                              <th class="min-w-20px">Customer Member</th>
                              <th class="min-w-20px">Sai Member</th>
                              <th class="min-w-20px">Action</th>
                          </tr>
                      </thead>
                      <tbody></tbody>
                      <tfoot>
                          <tr class="text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                              <th class="min-w-20px pe-2">No</th>
                              <th class="min-w-20px">Item</th>
                              <th class="min-w-100px">Restaurant/Shop</th>
                              <th class="min-w-100px">Amount</th>
                              <th class="min-w-20px">Customer Member</th>
                              <th class="min-w-20px">Sai Member</th>
                              <th class="min-w-20px">Action</th>
                          </tr>
                      </tfoot>
                  </table>
              </div>
          </div>
      </div>

      <script>
          $(function() {
              $('#form_loader,#lds-roller-form').hide();
          });

          function submitEntertainHeader() {
              const $btn = $('#btn_save_header');
              if ($btn.prop('disabled')) return;

              const cust = ($('#Customer').val() || '').trim();
           
              const nc = ($('#NumCA').val() || '').trim();
              if (!cust) return Swal.fire('Peringatan', 'Customer is required', 'warning');
            
              if (!nc) return Swal.fire('Peringatan', 'Num CA is required', 'warning');

              const id = $('#ID_Report').val();
              const form = $('#entertain-header-form');

              const url = id ?
                  "{{ route('sales_report_entertain.update_header', ['SysID'=>'__ID__']) }}".replace('__ID__', id) :
                  "{{ route('sales_report_entertain.store') }}";

              const data = form.serialize() + (id ? '&_method=PUT' : '');
              const method = 'POST';


              $btn.prop('disabled', true);
              $('#spinner_save_header').show();
              $('#svg_save_header').hide();
              $('#btn_text_save_header').text('Saving...');

              $.ajax({
                  url,
                  type: method,
                  data,
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  success: function(res) {
                      if (res && res.success) {
                          if (res.data && res.data.SysID) {
                              $('#ID_Report').val(res.data.SysID);
                              $('#btn_create_detail').prop('disabled', false);
                          }
                          Swal.fire('Success', id ? 'Header updated.' : 'Header saved.', 'success');
                      } else {
                          Swal.fire('Failed', (res && res.message) || 'Failed to save header data.', 'error');
                      }
                  },
                  error: function(xhr) {
                      let msg = 'An error occurred while saving the header.';
                      if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                          msg = Object.values(xhr.responseJSON.errors).map(a => a.join(' ')).join('\n');
                      }
                      Swal.fire('Error', msg, 'error');
                  },
                  complete: function() {

                      $btn.prop('disabled', false);
                      $('#spinner_save_header').hide();
                      $('#svg_save_header').show();
                      $('#btn_text_save_header').text('Save');
                  }
              });
          }

          $(function() {
              if ($('#ID_Report').val()) $('#btn_create_detail').prop('disabled', false);
          });
      </script>

      <script>
          $(document).ready(function() {
              loadDetailTable();
          });

          let detailTable;
          const aggMembers = {
              cust: '',
              sai: ''
          };

          function loadDetailTable() {
              const reportId = $('#ID_Report').val();
              if (!reportId) return;

              $.get('{{ route("sales_report_entertain.get_details") }}', {
                      ID_Report: reportId
                  })
                  .done(function(resp) {
                      if (!resp || !resp.success) {
                          Swal.fire('Failed', (resp && resp.message) || 'Failed to load details', 'error');
                          return;
                      }

                      const data = resp.data || {};
                      const items = Array.isArray(data.items) ? data.items : [];
                      const members = data.members || {};


                      aggMembers.cust = (members.CustomerMember || []).filter(Boolean).join(', ');
                      aggMembers.sai = (members.SAIMember || []).filter(Boolean).join(', ');

                      if ($.fn.DataTable.isDataTable('#kt_doc_table')) {

                          detailTable.clear().rows.add(items).draw();
                          return;
                      }

                      detailTable = $('#kt_doc_table').DataTable({
                          data: items,
                          columns: [{
                                  data: null,
                                  className: 'text-center',
                                  render: (d, t, r, m) => m.row + 1
                              },
                              {
                                  data: 'Item',
                                  className: 'text-center',
                                  orderable: false
                              },
                              {
                                  data: 'RestaurantShop',
                                  className: 'text-center',
                                  orderable: false,
                                  render: d => d || '-'
                              },
                              {
                                  data: 'Amount',
                                  className: 'text-center',
                                  orderable: false,
                                  render: d => 'Rp. ' + Number(d).toLocaleString('id-ID', {
                                      minimumFractionDigits: 2,
                                      maximumFractionDigits: 2
                                  })
                              },


                              {
                                  data: null,
                                  className: 'text-center col-cust',
                                  orderable: false,
                                  searchable: false,
                                  defaultContent: ''
                              },
                              {
                                  data: null,
                                  className: 'text-center col-sai',
                                  orderable: false,
                                  searchable: false,
                                  defaultContent: ''
                              },

                              {
                                  data: 'SysID',
                                  className: 'text-center',
                                  orderable: false,
                                  searchable: false,
                                  render: (id) => `
                                    <button type="button" class="btn btn-light-warning btn-sm" title="Edit Detail" onclick="editDetail(${id},this)" style="align-items:center; width:40px;height:35px;"> 
                                        <span class="svg-icon svg-icon-2" style="margin-left:-7px;"> 
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                             <g fill="none"><rect width="24" height="24"/> 
                                             <path d="M5,21 L5,19 L19,19 L19,21 L5,21 Z M19.707,6.293 L17.707,4.293 C17.317,3.902 16.683,3.902 16.293,4.293 L7,13.586 L7,17 L10.414,17 L19.707,7.707 C20.098,7.317 20.098,6.683 19.707,6.293 Z" fill="#000"/>
                                              </g>
                                               </svg>
                                                </span> 
                                                </button>
                                    
                                    <button type="button" title="Delete Report Detail" class="btn btn-light-danger btn-sm" onclick="deleteDetail(${id})" style="text-align:center; width:40px; height:35px;"> <span class="svg-icon svg-icon-2" style="margin-left:-7px;"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"> <g fill="none"><rect width="24" height="24"/> <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8,14 L16,14 L16,10 L8,10 Z" fill="#000"/> <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000" opacity="0.3"/> </g> </svg> </span> <span class="spinner-border spinner-border-sm svg-icon svg-icon-1x" style="display:none;"></span> </button>`
                              }
                          ],
                          order: [],
                          destroy: true,


                          rowCallback: function(row, rowData, displayIndex) {
                              const isFirstRowInPage = (displayIndex === 0);
                              $(row).find('td.col-cust').text(isFirstRowInPage ? (aggMembers.cust || '—') : '—');
                              $(row).find('td.col-sai').text(isFirstRowInPage ? (aggMembers.sai || '—') : '—');
                          }
                      });
                  })
                  .fail(() => Swal.fire('Error', 'An error occurred while loading details', 'error'));
          }


          $(function() {
              const css = `<style>
    #kt_doc_table td.col-cust, #kt_doc_table td.col-sai { white-space: normal; }
  </style>`;
              document.head.insertAdjacentHTML('beforeend', css);
          });

          function create_detail() {
              const reportId = $('#ID_Report').val();
              if (!reportId) return Swal.fire('Report id not available yet!', 'Save header first', 'warning');
              $('#kt_content_container').html(`
            <div id="form_loader" style="text-align:center;">
                <div class="lds-roller mt-10 mb-10" id="lds-roller-form">
                    <div></div><div></div><div></div><div></div>
                    <div></div><div></div><div></div><div></div>
                </div>
            </div>`);
              $.get('{{ url("sales-report-entertain/form-detail") }}', {
                          SysID: reportId
                      },
                      html => $('#kt_content_container').html(html))
                  .fail(xhr => Swal.fire('Failed to load detail form', xhr.responseText, 'error'));
          }

          function parseIDRCurrency(s) {
              if (!s) return 0;
              const clean = String(s).replace(/[^\d,.\-]/g, '').replace(/\./g, '').replace(',', '.');
              const n = parseFloat(clean);
              return isNaN(n) ? 0 : n;
          }

          function editDetail(detailSysID, btn) {
              const reportId = $('#ID_Report').val();
              if (!reportId) return Swal.fire('Report id not available yet!', 'Save header first', 'warning');


              if (detailSysID && detailSysID.nodeType === 1) {
                  const _btn = detailSysID;
                  detailSysID = btn;
                  btn = _btn;
              }
              detailSysID = parseInt(detailSysID, 10);
              if (!detailSysID) return Swal.fire('Error', 'Invalid detail id', 'error');


              let custPrefill = '',
                  saiPrefill = '';
              let itemPrefill = '',
                  shopPrefill = '',
                  amountPrefill = 0;
              if (btn) {
                  const $tr = $(btn).closest('tr');


                  custPrefill = ($tr.find('td.col-cust').text() || '').trim();
                  saiPrefill = ($tr.find('td.col-sai').text() || '').trim();
                  if (custPrefill === '—') custPrefill = '';
                  if (saiPrefill === '—') saiPrefill = '';

                  itemPrefill = ($tr.children().eq(1).text() || '').trim();
                  shopPrefill = ($tr.children().eq(2).text() || '').trim();
                  amountPrefill = parseIDRCurrency(($tr.children().eq(3).text() || '').trim());
              }


              $('#kt_content_container').html(`
    <div id="form_loader" style="text-align:center;">
      <div class="lds-roller mt-10 mb-10" id="lds-roller-form">
        <div></div><div></div><div></div><div></div>
        <div></div><div></div><div></div><div></div>
      </div>
    </div>`);


              $.get('{{ url("sales-report-entertain/form-detail") }}', {
                      SysID: reportId,
                      detailSysID
                  })
                  .done(function(html) {
                      $('#kt_content_container').html(html);


                      $('#form_mode').val('edit');
                      $('input[name="DetailSysID"]').val(detailSysID);

                      $('.add-item,.remove-item,.add-shop,.remove-shop,.add-amount,.remove-amount').hide();


                      const $item = $('input[name="Item[]"]').first();
                      const $shop = $('input[name="RestaurantShop[]"]').first();
                      const $amt = $('input[name="Amount[]"]').first();

                      if (!$item.val()) $item.val(itemPrefill);
                      if (!$shop.val()) $shop.val(shopPrefill);
                      if (!$amt.val()) $amt.val(Number(amountPrefill || 0).toFixed(2));


                      const $cust = $('input[name="CustomerMember"], input[name="CustomerMember[]"]');
                      const $sai = $('select[name="SAIMember"], select[name="SAIMember[]"]');

                      if ($cust.length) $cust.val(custPrefill);

                      if ($sai.length) {
                          const raw = saiPrefill;
                          const arr = raw && raw.includes(',') ? raw.split(',').map(s => s.trim()).filter(Boolean) : raw;

                          if (Array.isArray(arr)) {

                              arr.forEach(v => {
                                  if (v && !$sai.find(`option[value="${v}"]`).length) {
                                      $sai.append(new Option(v, v, true, true));
                                  }
                              });
                              $sai.val(arr).trigger('change');
                          } else {
                              if (raw && !$sai.find(`option[value="${raw}"]`).length) {
                                  $sai.append(new Option(raw, raw, true, true));
                              }
                              $sai.val(raw).trigger('change');
                          }
                      }
                  })
                  .fail(xhr => Swal.fire('Failed to load detail form', xhr.responseText, 'error'));
          }

          function deleteDetail(detailSysID) {
              Swal.fire({
                  title: 'Confirm Delete',
                  text: 'Are you sure you want to delete this detail?',
                  icon: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#d33',
                  cancelButtonColor: '#6c757d',
                  confirmButtonText: 'Yes, Delete',
                  cancelButtonText: 'Cancel'
              }).then(res => {
                  if (!res.isConfirmed) return;
                  $.ajax({
                      url: '{{ url("sales-report-entertain/delete-detail") }}',
                      type: 'DELETE',
                      data: {
                          _token: '{{ csrf_token() }}',
                          SysID: detailSysID
                      },
                      success: res => {
                          if (res.success) {
                              Swal.fire('Success!', res.message, 'success');
                              loadDetailTable();
                              if (typeof res.total !== 'undefined') setHeaderTotal(res.total);
                          } else {
                              Swal.fire('Failed', res.message, 'error');
                          }
                      },
                      error: xhr => Swal.fire('Error', xhr.responseText, 'error')
                  });
              });
          }

          document.getElementById('btn_import_excel').addEventListener('click', function() {
              const fileInput = document.querySelector('input[type="file"][name="file"]');
              if (fileInput && fileInput.files.length > 0) {
                  document.getElementById('svg_import_excel').style.display = 'none';
                  document.getElementById('spinner_import_excel').style.display = 'inline-block';
              }
          });

          function setHeaderTotal(val) {
              const n = Number(val || 0);
              $('#TotalAmount').val(n.toLocaleString('id-ID', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
              }));
              $('#TotalAmountRaw').val(n);
          }
      </script>