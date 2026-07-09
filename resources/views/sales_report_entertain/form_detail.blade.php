<div class="col-xxl-12">
  <div class="card mt-5">
    <div class="card-header">
      <div class="card-title">Add Entertain Report Detail</div>

      <div class="card-toolbar">
        <button
          type="button"
          class="btn btn-light-primary btn-sm ms-2 me-2"
          id="btn_members"
          title="Kelola Member"
          data-bs-toggle="modal"
          data-bs-target="#memberModal"
          style="display:flex;align-items:center;gap:.4rem;height:35px;">
          <span id="svg_members" class="svg-icon svg-icon-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
              <g fill="none">
                <rect width="24" height="24" />
                <circle cx="8" cy="8" r="3" fill="#000" />
                <circle cx="16" cy="9" r="2.5" fill="#000" opacity=".5" />
                <path d="M4 19a4 4 0 0 1 4-4h0a4 4 0 0 1 4 4v1H4v-1Z" fill="#000" />
                <path d="M13 19c0-2 1.8-3.5 3.5-3.5S20 17 20 19v1h-7v-1Z" fill="#000" opacity=".5" />
              </g>
            </svg>
          </span>
          <span id="spinner_members" class="spinner-border spinner-border-sm align-middle" style="display:none;"></span>
          <span>Manage Members</span>
        </button>

        <button class="btn btn-success btn-sm" id="btn_back_home" onclick="backHome()">
          <span id="svg_back_home" class="svg-icon svg-icon-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
              <g fill="none" fill-rule="evenodd">
                <rect width="24" height="24" />
                <path d="M3.957 8.415L11.479 3.819a1 1 0 0 1 1.043 0L20.043 8.415A2 2 0 0 1 21 10.122V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-8.878a2 2 0 0 1 .957-1.707ZM10 13a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1h-4Z" fill="#000" />
              </g>
            </svg>
          </span>
          <span id="spinner_back_home" class="spinner-border spinner-border-sm svg-icon svg-icon-2" style="display:none;"></span>
          <span id="btn_text_back_home">Back</span>
        </button>
      </div>
    </div>

    <div class="card-body">
      <form id="entertain-detail-form">
        @csrf
        <input type="hidden" id="form_mode" name="form_mode" value="{{ isset($detail) ? 'edit' : 'create' }}">
        <input type="hidden" name="ID_Report" id="ID_Report" value="{{ $SysID }}" />
        <input type="hidden" name="DetailSysID" value="{{ $detail->SysID ?? '' }}">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Item <span class="text-danger">*</span></label>
            <div id="item-wrap">
              <div class="input-group mb-2 item-line">
                <input type="text" class="form-control" name="Item[]" required>
                <button type="button" class="btn btn-light-primary btn-sm add-item" title="add item">+</button>
                <button type="button" class="btn btn-light-danger  btn-sm remove-item" title="delete item">−</button>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <label class="form-label">Restaurant/Shop</label>
            <div id="shop-wrap">
              <div class="input-group mb-2 shop-line">
                <input type="text" class="form-control" name="RestaurantShop[]">
                <button type="button" class="btn btn-light-primary btn-sm add-shop" title="add shop">+</button>
                <button type="button" class="btn btn-light-danger  btn-sm remove-shop" title="delete shop">−</button>
              </div>
            </div>
          </div>

          <div class="col-md-3">
            <label class="form-label">Amount</label>
            <div id="amount-wrap">
              <div class="input-group mb-2 amount-line">
                <input type="number" step="0.01" class="form-control" name="Amount[]" placeholder="0.00">
                <button type="button" class="btn btn-light-primary btn-sm add-amount" title="add amount">+</button>
                <button type="button" class="btn btn-light-danger  btn-sm remove-amount" title="delete amount">−</button>
              </div>
            </div>
          </div>
        </div>

        <div class="text-start mt-3">
          <button type="button" class="btn btn-primary btn-sm" id="btn_save_detail" onclick="submitEntertainDetail()">
            <span id="svg_save_detail" class="svg-icon svg-icon-2">
              <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                <g stroke="none" fill="none">
                  <polygon points="0 0 24 0 24 24 0 24" />
                  <path d="M17,4 L6,4 C4.79111111,4 4,4.7 4,6 L4,18 C4,19.3 4.79111111,20 6,20 L18,20 C19.2,20 20,19.3 20,18 L20,7.20710678 C20,7.07449854 19.9473216,6.94732158 19.8535534,6.85355339 L17,4 Z M17,11 L7,11 L7,4 L17,4 L17,11 Z" fill="#000000" />
                  <rect fill="#000000" opacity="0.3" x="12" y="4" width="3" height="5" rx="0.5" />
                </g>
              </svg>
            </span>
            <span id="spinner_save_detail" class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;"></span>
            <span id="btn_text_save_detail">Save</span>
          </button>
        </div>
      </form>

      <div class="card mt-5" id="just-saved-card" style="display:none;">
        <div class="card-body">
          <table class="table align-middle table-row-dashed table-striped gy-2 fs-7" id="just-saved-table">
            <thead>
              <tr class="text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                <th class="min-w-20px pe-2">No</th>
                <th class="min-w-20px">Item</th>
                <th class="min-w-100px">Restaurant/Shop</th>
                <th class="min-w-100px">Amount</th>
              </tr>
            </thead>
            <tbody id="just-saved-tbody"></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Members -->
<div class="modal fade" id="memberModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Manage Members</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
        <form id="member-form">
          @csrf
          <input type="hidden" name="ID_Report" value="{{ $SysID }}" />

          <div class="mb-4">
            <label class="form-label fw-bold">Customer Members</label>
            <div id="ext-wrap">
              <input class="form-control mb-2" name="ExternalMembers[]">
            </div>
            <button class="btn btn-sm btn-light" type="button" id="add-ext">+ Add Members</button>
          </div>

          
          <div class="mb-2">
            <label class="form-label fw-bold">Internal Members (SAI)</label>
            <div id="int-wrap">
              
              <div class="row g-2 mb-2 int-line">
                <div class="col-12 col-md-7">
                  <select class="form-select sai-select" name="InternalMembers[]"></select>
                </div>
                <div class="col-12 col-md-5">
                  <input class="form-control" name="CostCenter[]" placeholder="Cost Center" value="" readonly>
                </div>
              </div>
            </div>
            <button class="btn btn-sm btn-light" type="button" id="add-int">+ Add SAI Members</button>
            <div class="form-text">Cost Center hanya untuk baris pertama (auto dari SAI).</div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary btn-sm" id="btn_save_members" onclick="submitMembers()">
          <span id="svg_save_members" class="svg-icon svg-icon-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
              <g stroke="none" fill="none">
                <polygon points="0 0 24 0 24 24 0 24" />
                <path d="M17,4 L6,4 C4.79111111,4 4,4.7 4,6 L4,18 C4,19.3 4.79111111,20 6,20 L18,20 C19.2,20 20,19.3 20,18 L20,7.20710678 C20,7.07449854 19.9473216,6.94732158 19.8535534,6.85355339 L17,4 Z M17,11 L7,11 L7,4 L17,4 L17,11 Z" fill="#000000" />
                <rect fill="#000000" opacity="0.3" x="12" y="4" width="3" height="5" rx="0.5" />
              </g>
            </svg>
          </span>
          <span id="spinner_save_members" class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;"></span>
          <span id="btn_text_save_members">Save</span>
        </button>
      </div>
    </div>
  </div>
</div>

<script>
  // ---------- Utils ----------
  function currencyID(n) {
    return 'RP. ' + Number(n || 0).toLocaleString('id-ID', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    });
  }

  // ---------- Dynamic Detail Lines ----------
  const GROUPS = [{
      wrap: '#item-wrap',
      line: '.item-line',
      add: '.add-item',
      remove: '.remove-item'
    },
    {
      wrap: '#shop-wrap',
      line: '.shop-line',
      add: '.add-shop',
      remove: '.remove-shop'
    },
    {
      wrap: '#amount-wrap',
      line: '.amount-line',
      add: '.add-amount',
      remove: '.remove-amount'
    },
  ];

  function refreshPlusMinus(wrapSel, lineSel, addSel, removeSel) {
    const $lines = $(wrapSel).find(lineSel);
    $lines.each(function(i) {
      const isLast = i === $lines.length - 1;
      $(this).find(addSel).toggle(isLast);
      $(this).find(removeSel).toggle($lines.length > 1 && !isLast);
    });
  }

  function refreshAll() {
    GROUPS.forEach(g => refreshPlusMinus(g.wrap, g.line, g.add, g.remove));
  }

  function bindDynamicHandlers() {
    $(document).off('.detailDyn');

    GROUPS.forEach(g => {
      $(document).on('click.detailDyn', `${g.wrap} ${g.add}`, function() {
        const $line = $(this).closest(g.line);
        const $clone = $line.clone(true, true);
        $clone.find('input').val('');
        $line.after($clone);
        refreshAll();
        $clone.find('input').first().focus();
      });

      $(document).on('click.detailDyn', `${g.wrap} ${g.remove}`, function() {
        const $wrap = $(g.wrap);
        const $lines = $wrap.find(g.line);
        if ($lines.length <= 1) {
          $(this).closest(g.line).find('input').val('');
        } else {
          $(this).closest(g.line).remove();
        }
        refreshAll();
      });
    });
  }

  window.mountEntertainDetail = function() {
    if (!$('#entertain-detail-form').length) return;
    bindDynamicHandlers();
    if ($('#form_mode').val() === 'edit') {
      $('.add-item,.remove-item,.add-shop,.remove-shop,.add-amount,.remove-amount').hide();
    } else {
      refreshAll();
    }
  };

  $(function() {
    window.mountEntertainDetail();
  });

  new MutationObserver(() => {
    if (document.getElementById('entertain-detail-form')) {
      window.mountEntertainDetail();
    }
  }).observe(document.body, {
    childList: true,
    subtree: true
  });

  // ---------- Just Saved Table ----------
  let justSavedDT = null;

  function renderJustSaved(items) {
    if (!items || !items.length) return;
    if (!$.fn.DataTable.isDataTable('#just-saved-table')) {
      justSavedDT = $('#just-saved-table').DataTable({
        paging: false,
        searching: false,
        info: false,
        ordering: false,
        autoWidth: false,
        columns: [{
            data: null,
            className: 'text-center',
            render: (d, t, r, m) => m.row + 1
          },
          {
            data: 'Item',
            defaultContent: ''
          },
          {
            data: 'RestaurantShop',
            defaultContent: ''
          },
          {
            data: 'Amount',
            className: 'text-end',
            render: d => currencyID(d)
          },
        ],
        data: items
      });
    } else {
      const dt = $('#just-saved-table').DataTable();
      dt.clear().rows.add(items).draw();
    }
    $('#just-saved-card').slideDown(120);
  }

  // ---------- Submit Detail ----------
  function submitEntertainDetail() {
    const $btn = $('#btn_save_detail');
    const mode = $('#form_mode').val();
    const idHdr = $('#ID_Report').val();

    if (!idHdr) {
      Swal.fire({
        icon: 'warning',
        title: 'Warning',
        text: 'Report Id not found. Save header first'
      });
      return;
    }

    const itemsRaw = $('#item-wrap input[name="Item[]"]').map(function() {
      return ($(this).val() || '').trim();
    }).get();
    const shopsRaw = $('#shop-wrap input[name="RestaurantShop[]"]').map(function() {
      return ($(this).val() || '').trim();
    }).get();
    const amntsRaw = $('#amount-wrap input[name="Amount[]"]').map(function() {
      return $(this).val();
    }).get();

    const items = itemsRaw.filter(v => v !== '');
    const N = items.length;
    if (N === 0) {
      Swal.fire({
        icon: 'warning',
        title: 'Warning',
        text: 'Minimum content 1 item!.'
      });
      return;
    }

    const shopsAligned = new Array(N);
    for (let i = 0; i < N; i++) shopsAligned[i] = (typeof shopsRaw[i] !== 'undefined' && shopsRaw[i] !== '') ? shopsRaw[i] : '-';

    const amntsAligned = new Array(N);
    for (let i = 0; i < N; i++) {
      const v = (typeof amntsRaw[i] !== 'undefined' && amntsRaw[i] !== '') ? parseFloat(String(amntsRaw[i]).replace(',', '.')) : 0;
      amntsAligned[i] = (!isNaN(v) && v > 0) ? v : 0;
    }

    const detailSysID = $('input[name="DetailSysID"]').val();
    const isEdit = (mode === 'edit' && detailSysID);

    const url = isEdit ?
      ('{{ url("sales-report-entertain/update-detail") }}/' + detailSysID) :
      '{{ url("sales-report-entertain/store-detail") }}';

    const payload = {
      _token: $('input[name="_token"]').val(),
      ID_Report: idHdr,
      Item: items,
      RestaurantShop: shopsAligned,
      Amount: amntsAligned,
      ...(isEdit ? {
        _method: 'PUT'
      } : {})
    };

    $btn.prop('disabled', true).addClass('disabled');
    $('#spinner_save_detail').show();
    $('#svg_save_detail').hide();
    $('#btn_text_save_detail').text('Saving...');

    $.ajax({
        url,
        type: 'POST',
        data: payload,
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      })
      .done(function(resp) {
        if (resp && resp.success) {
          const itemsFromSrv = resp.data?.items || [];
          const total = resp.data?.total;

          if (typeof total !== 'undefined' && typeof window.setHeaderTotal === 'function') setHeaderTotal(total);

          if ($('#kt_doc_table').length && typeof loadDetailTable === 'function') {
            loadDetailTable();
          } else if ($.fn.DataTable.isDataTable('#kt_doc_table')) {
            detailTable.clear().rows.add(itemsFromSrv).draw();
          }

          if (Array.isArray(resp.data?.rows) && resp.data.rows.length) {
            const jsItems = resp.data.rows.map(r => ({
              Item: r.Item || '',
              RestaurantShop: r.RestaurantShop || '',
              Amount: parseFloat(r.Amount || 0)
            }));
            renderJustSaved(jsItems);
          } else {
            renderJustSaved(items.map((it, i) => ({
              Item: it,
              RestaurantShop: shopsAligned[i],
              Amount: amntsAligned[i]
            })));
          }

          $('#form_mode').val('create');
          $('input[name="DetailSysID"]').val('');
          $('#item-wrap .item-line').not(':first').remove();
          $('#shop-wrap .shop-line').not(':first').remove();
          $('#amount-wrap .amount-line').not(':first').remove();
          $('#item-wrap input[name="Item[]"]').val('');
          $('#shop-wrap input[name="RestaurantShop[]"]').val('');
          $('#amount-wrap input[name="Amount[]"]').val('');
          refreshAll();
          $('#item-wrap input[name="Item[]"]').first().focus();

          Swal.fire({
            icon: 'success',
            title: 'Success',
            text: isEdit ? 'Detail change saved.' : 'Detail saved  successfully.'
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Failed',
            text: (resp && resp.message) || 'Failed to save detail.'
          });
        }
      })
      .fail(function(xhr) {
        let msg = 'An error occurred.';
        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
          msg = Object.values(xhr.responseJSON.errors).map(a => a.join(' ')).join('\n');
        } else if (xhr.responseText) {
          msg = xhr.responseText;
        }
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: msg
        });
      })
      .always(function() {
        $btn.prop('disabled', false).removeClass('disabled');
        $('#spinner_save_detail').hide();
        $('#svg_save_detail').show();
        $('#btn_text_save_detail').text('Save');
      });
  }

  // ---------- Members ----------
  
  $(document)
    .off('click.addExt', '#add-ext')
    .on('click.addExt', '#add-ext', function() {
      $('#ext-wrap').append('<input class="form-control mb-2" name="ExternalMembers[]">');
    });

 
  function buildIntRow(index) {
    return `
    <div class="row g-2 mb-2 int-line">
      <div class="col-12 col-md-7">
        <select class="form-select sai-select" name="InternalMembers[]"></select>
      </div>
      ${ index === 0 ? `
      <div class="col-12 col-md-5">
        <input class="form-control" name="CostCenter[]" placeholder="Cost Center" value="" readonly>
      </div>` : '' }
    </div>
  `;
  }


  function addSaiRow(selectedName = '') {
    const idx = $('#int-wrap .int-line').length; 
    $('#int-wrap').append(buildIntRow(idx));

    
    const $sel = $('#int-wrap .int-line').last().find('.sai-select');
    initSaiSelect($sel);

   
    if (selectedName) {
      const opt = new Option(selectedName, selectedName, true, true);
      $sel.append(opt).trigger('change', {
        silentCC: true
      });

      
      if (idx === 0) {
        $.get('{{route("ref.employee_by_name") }}', {
            name: selectedName
          })
          .done(res => {
            $('#int-wrap .int-line').first()
              .find('input[name="CostCenter[]"]').val(res?.cc || '');
          });
      }
    }
  }


  $(document)
    .off('click.addInt', '#add-int')
    .on('click.addInt', '#add-int', function() {
      addSaiRow('');
    });

  
  $(document)
    .off('change.saiSelect', '#int-wrap .sai-select')
    .on('change.saiSelect', '#int-wrap .sai-select', function(e, meta) {
      if (meta && meta.silentCC) return;
      const $row = $(this).closest('.int-line');
      const idx = $row.index();
      const name = $(this).val() || '';
      if (idx === 0) {
        if (!name) {
          $row.find('input[name="CostCenter[]"]').val('');
          return;
        }
        $.get('{{ route("ref.employee_by_name") }}', {
            name
          })
          .done(res => {
          const cc = res?.cc ? String(res.cc).trim() : '';
          $row.find('input[name="CostCenter[]"]').val(cc);
          if (cc === '') {
           
            Swal.fire({
              icon: 'warning',
              title: 'Warning',
              text: 'This SAI member does not have a Cost Center.',
            });
          }
        })
          .fail(() => {
            $row.find('input[name="CostCenter[]"]').val('');
          });
      }
    });


  
  $(document)
    .off('show.bs.modal.membersBtn', '#memberModal')
    .on('show.bs.modal.membersBtn', '#memberModal', function() {
      $('#spinner_members').show();
      $('#svg_members').hide();
    })
    .off('hidden.bs.modal.membersBtn', '#memberModal')
    .on('hidden.bs.modal.membersBtn', '#memberModal', function() {
      $('#spinner_members').hide();
      $('#svg_members').show();
    });

  
  $(document)
    .off('show.bs.modal.prefillMembers', '#memberModal')
    .on('show.bs.modal.prefillMembers', '#memberModal', function() {
      const hid = $('#member-form [name="ID_Report"]').val() || $('#ID_Report').val();
      if (!hid) {
        $('#spinner_members').hide();
        $('#svg_members').show();
        return;
      }

      
      $('#ext-wrap').empty().append('<input class="form-control mb-2" name="ExternalMembers[]">');
      $('#int-wrap').empty();

      $.get('{{ route("sales_report_entertain.get_details") }}', {
          ID_Report: hid
        })
        .done(resp => {
          if (!resp || !resp.success) {
            addSaiRow('');
            return;
          }

          const m = resp.data?.members || {};
          const ext = Array.isArray(m.CustomerMember) ? m.CustomerMember : [];
          const sai = Array.isArray(m.SAIMember) ? m.SAIMember : [];

          
          if (ext.length) {
            $('#ext-wrap').empty();
            ext.forEach(v => $('#ext-wrap').append(
              `<input class="form-control mb-2" name="ExternalMembers[]" value="${String(v).replace(/"/g,'&quot;')}">`
            ));
          }

          
          if (sai.length) {
            sai.forEach(name => addSaiRow(String(name)));
          } else {
            addSaiRow('');
          }
        })
        .fail(() => {
          
          $('#int-wrap').empty();
          addSaiRow('');
        })
        .always(() => {
          $('#spinner_members').hide();
          $('#svg_members').show();
        });
    });

  function submitMembers(btn){
  
  const $row0    = $('#int-wrap .int-line').first();
  const firstSai = (($row0.find('.sai-select').val() || '') + '').trim();
  const firstCc  = (($row0.find('input[name="CostCenter[]"]').val() || '') + '').trim();
  if (firstSai && !firstCc) {
    Swal.fire('Warning', 'This SAI member does not have a Cost Center.', 'warning');
    return false; 
  }
    const $btn = btn ? $(btn) : $('#btn_save_members');
    $btn.prop('disabled', true);
    $('#spinner_save_members').show();
    $('#svg_save_members').hide();
    $('#btn_text_save_members').text('Saving...');

    const hid = $('#member-form [name="ID_Report"]').val() || $('#ID_Report').val();
    if (!hid) {
      Swal.fire('Error', 'ID Report not found.', 'error');
      $btn.prop('disabled', false);
      $('#spinner_save_members').hide();
      $('#svg_save_members').show();
      $('#btn_text_save_members').text('Save');
      return false;
    }

    $.ajax({
        url: '{{ url("sales-report-entertain/update-members") }}/' + encodeURIComponent(hid),
        type: 'POST',
        data: $('#member-form').serialize() + '&_method=PUT',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      })
      .done(function(res) {
        if (res && res.success) {
          Swal.fire('Success', 'Members saved.', 'success');
          if (typeof bootstrap !== 'undefined') {
            const el = document.getElementById('memberModal');
            (bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el)).hide();
          } else {
            $('#memberModal').modal('hide');
          }
          if (typeof loadDetailTable === 'function') loadDetailTable();
        } else {
          Swal.fire('Failed', (res && res.message) || 'Failed to save members', 'error');
        }
      })
      .fail(function(xhr) {
        Swal.fire('Error', xhr.responseText || 'Failed to save members', 'error');
      })
      .always(function() {
        $btn.prop('disabled', false);
        $('#spinner_save_members').hide();
        $('#svg_save_members').show();
        $('#btn_text_save_members').text('Save');
      });

    return false;
  }

  function initSaiSelect($el) {
    $el.select2({
      ajax: {
        url: '{{route("ref.employee_names") }}',
        dataType: 'json',
        delay: 250,
        data: params => ({
          q: params.term || '',
          page: params.page || 1
        }),
        processResults: d => ({
          results: d.results,
          pagination: {
            more: d.pagination.more
          }
        })
      },
      placeholder: '-- Pilih SAI Member --',
      allowClear: true,
      minimumInputLength: 0,
      width: '100%',
      dropdownParent: $('#memberModal') 
    });
  }
</script>