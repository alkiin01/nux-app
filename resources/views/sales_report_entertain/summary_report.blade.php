<div class="col-xxl-12 overflow-hidden">
  {{-- CARD HEADER --}}
  <div class="card mt-5 overflow-hidden">
    <div class="card-header">
      <div class="card-title">{{ $head_title }}</div>
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
  </div>

  <div class="card mt-5 overflow-hidden">
    <div class="card-header border-1 pt-6 pb-6 mb-5">
      <div class="w-100">
        <div class="row g-3 align-items-end mx-0">
          <div class="col-12 col-md-3">
  <label class="form-label mb-1 fs-7 text-muted fw-semibold">Period</label>
  <div class="d-flex gap-2 align-items-center">
    <select id="filter_period_type" class="form-select form-select-sm rounded-3" style="max-width:120px;">
      <option value="month" selected>Month</option>
      <option value="year">Year</option>
    </select>

    
    <input type="month" id="filter_month"
           class="form-control form-control-sm rounded-3"
           placeholder="mm/yyyy">

   
    <select id="filter_year"
        class="form-select form-select-sm rounded-3"
        style="display:none;"></select>
  </div>
</div>


          {{-- NEW: Group By --}}
          <div class="col-12 col-md-2">
            <label for="filter_group_by" class="form-label mb-1 fs-7 text-muted fw-semibold">Group By</label>
            <select id="filter_group_by" class="form-select form-select-sm rounded-3">
              <option value="customer" selected>Customer</option>
              <option value="category">Category</option>
              <option value="cost_center">Cost Center</option>
              <option value="num_ca">Num CA</option>
            </select>
          </div>

          {{-- FIELD KEDUA (dinamis; hanya satu yang tampil sesuai group_by) --}}
          <div class="col-12 col-md-4">
            <label class="form-label mb-1 fs-7 text-muted fw-semibold" id="filter_value_label">Customer</label>

            {{-- Customer (dari DB) --}}
            <select id="filter_customer"
              class="form-select form-select-sm rounded-3 w-100"
              data-control="select2"
              data-placeholder="Select Option"
              data-allow-clear="true"
              data-hide-search="true"
              data-selection-css-class="form-select form-select-sm rounded-3"
              data-dropdown-css-class="form-select-sm"
              data-container-css-class="form-select-sm">
              <option value="">All Customers</option>
              @isset($customers)
              @foreach($customers as $c)
              @php $name = $c->Name; @endphp
              <option value="{{ $name }}">{{ $name }}</option>
              @endforeach
              <option value="Others">Others</option>
              @endisset
            </select>

            {{-- Category (samakan dengan category di form header) --}}
            <select id="filter_category"
              class="form-select form-select-sm rounded-3 w-100 mt-0"
              data-control="select2"
              data-placeholder="Select Option"
              data-allow-clear="true"
              data-hide-search="true"
              data-selection-css-class="form-select form-select-sm rounded-3"
              data-dropdown-css-class="form-select-sm"
              data-container-css-class="form-select-sm"
              style="display:none;">
              <option value="">All Categories</option>
              @isset($categories)
              @foreach($categories as $cat)
              @php
              $catName = is_object($cat)
              ? trim($cat->Category ?? $cat->category ?? $cat->Name ?? $cat->name ?? '')
              : trim((string)$cat);
              @endphp
              @if($catName !== '')
              <option value="{{ $catName }}">{{ $catName }}</option>
              @endif
              @endforeach
              @endisset
            </select>

            {{-- Cost Center (pilih SAI Member → CC auto) --}}
            <div id="cc_wrapper" class="row g-2 mt-0" style="display:none;">
              <div class="col-12 col-md-6">
                <label for="filter_sai_member" class="form-label mb-1 fs-7 text-muted fw-semibold">SAI Member</label>
                <select id="filter_sai_member" class="form-select form-select-sm rounded-3">
                  <option value="">Select Option</option>
                </select>
              </div>
              <div class="col-12 col-md-6">
                <label for="filter_cost_center" class="form-label mb-1 fs-7 text-muted fw-semibold">Cost Center</label>
                <input type="text" id="filter_cost_center" class="form-control form-control-sm rounded-3" readonly>
                <input type="hidden" id="filter_cost_center_code">
                <input type="hidden" id="filter_cost_center_name">
              </div>
            </div>

            {{-- Num CA (input manual) --}}
            <input type="text" id="filter_num_ca" class="form-control form-control-sm rounded-3 mt-0" style="display:none;">
          </div>

          <div class="col-12 col-md-2 ms-md-auto d-flex justify-content-md-end gap-2">

  
  <button type="button" id="btn_export_excel"
          class="btn btn-light-success btn-sm p-0"
          title="Export Excel"
          style="width:40px; height:35px; align-items:center; display:flex; justify-content:center;"
          onclick="exportSummaryExcel(event)">
    <span class="svg-icon svg-icon-2 p-0 m-0"
      style="display:inline-block; align-items:center; justify-content:center;">
  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
       viewBox="0 0 24 24" aria-hidden="true"
       style="display:flex; align-items:center; justify-content:center;">
    <path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12V7l-4-4Z"
          fill="currentColor" opacity="0.3"/>
    <path d="M10.5 10.5L8 14l2.5 3.5h1.6L9.8 14l2.3-3.5h-1.6Z"
          fill="currentColor"/>
    <path d="M14 3v4h4" fill="currentColor"/>
  </svg>
</span>
    <span id="spinner_export" class="spinner-border spinner-border-sm align-middle" style="display:none;"></span>
  </button>

  
  <button type="button" id="btn_apply_filter"
          class="btn btn-light-primary btn-sm p-0"
          title="Apply Filter"
          style="width:40px; height:35px; align-items:center; display:flex; justify-content:center;">
    <span class="svg-icon svg-icon-2 p-0 m-0"
          style="display:inline-block; align-items:center; justify-content:center;">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
           fill="currentColor" class="bi bi-funnel p-0 m-0"
           viewBox="0 0 16 16"
           style="display:flex; align-items:center; justify-content:center;">
        <path d="M1.5 1.5h13a.5.5 0 0 1 .39.812L10 8.21V13a1 1 0 0 1-1.447.894l-2-1A1 1 0 0 1 6 12V8.21L1.11 2.312A.5.5 0 0 1 1.5 1.5"/>
      </svg>
    </span>
    <span id="spinner_apply" class="spinner-border spinner-border-sm align-middle" style="display:none;"></span>
  </button>

  <button type="button" id="btn_reset_filter"
          class="btn btn-light btn-sm p-0"
          title="Reset Filter"
          style="width:40px; height:35px; align-items:center; display:flex; justify-content:center;">
    <span class="svg-icon svg-icon-2 p-0 m-0"
          style="display:inline-block; align-items:center; justify-content:center;">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
           fill="currentColor" class="bi bi-arrow-counterclockwise p-0 m-0"
           viewBox="0 0 16 16"
           style="display:flex; align-items:center; justify-content:center;">
        <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 1 1 .908-.418A6 6 0 1 1 8 2v1z"/>
        <path d="M8 0a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 1 1 .708-.708L7.5 4.293V.5A.5.5 0 0 1 8 0z"/>
      </svg>
    </span>
    <span id="spinner_reset" class="spinner-border spinner-border-sm align-middle" style="display:none;"></span>
  </button>

  <a id="download_link" style="display:none;"></a>
</div>

        </div>

      </div>
    </div>

    <div class="card-body pt-0">
      <div class="table-responsive">
        <table
          class="table align-middle table-row-dashed table-striped gy-2 fs-7 w-100 text-center"
          id="kt_doc_table_summary">
          <thead>
            <tr class="text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
              <th class="min-w-20px pe-2 pe-4">No</th>
              <th class="min-w-150px pe-4" id="th_group_label">Customer</th>
              <th class="min-w-120px pe-4">Total Reports</th>
              <th class="min-w-150px pe-4">Total Amount</th>
            </tr>
          </thead>
          <tbody></tbody>
          <tfoot>
            <tr class="text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
              <th class="min-w-20px pe-2 pe-4">No</th>
              <th class="min-w-150px pe-4" id="tf_group_label">Customer</th>
              <th class="min-w-120px pe-4">Total Reports</th>
              <th class="min-w-150px pe-4">Total Amount</th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>



<script>

  function toggleBtnState(btn, spinnerId, loading){
    const spn = document.getElementById(spinnerId);
    if (!btn || !spn) return;
    btn.disabled = !!loading;
    spn.style.display = loading ? 'inline-block' : 'none';
  }

 
  function formatRupiah(n) {
    const num = Number(n || 0);
    return 'Rp ' + num.toLocaleString('id-ID', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    });
  }

  function monthLabelFromKey(key) {
    if (!key) return '-';
    const [y, m] = key.split('-').map(x => parseInt(x, 10));
    const dt = new Date(y || 2000, (m || 1) - 1, 1);
    return dt.toLocaleString('id-ID', {
      month: 'long'
    });
  }
  
  function monthToRange() {
  const raw = $('#filter_month').val();
  const pad = n => String(n).padStart(2, '0');
  const fmt = d => `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;

  if (!raw) {
    
    const now = new Date();
    const y = now.getFullYear();
    const start = new Date(y, 0, 1);    
    const end   = new Date(y, 11, 31); 
    return {
      start: fmt(start),
      end: fmt(end),
      year: y,
      monthKey: null
    };
  }

 
  const [y, mm] = raw.split('-').map(Number);
  const start = new Date(y, mm - 1, 1);
  const end   = new Date(y, mm, 0);
  return {
    start: fmt(start),
    end: fmt(end),
    year: y,
    monthKey: `${y}-${pad(mm)}`
  };
}

  function staticMonthKeysForYear(year) {
    const keys = [];
    for (let m = 1; m <= 12; m++) {
      const mm = String(m).padStart(2, '0');
      keys.push(`${year}-${mm}`);
    }
    return keys;
  }

  
  let summaryTable;

  function syncTFootWidths(api) {
    const headTh = api.table().header().querySelectorAll('th');
    const footTh = api.table().footer().querySelectorAll('th');
    headTh.forEach((th, i) => {
      if (footTh[i]) footTh[i].style.width = th.getBoundingClientRect().width + 'px';
    });
  }

  function buildStaticHeader() {

    $('#kt_doc_table_summary thead').html(`
      <tr class="text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
        <th class="min-w-20px pe-2 pe-4">No</th>
        <th class="min-w-120px pe-4">Month</th>
        <th class="min-w-200px pe-4" id="th_group_label">Customer</th>
        <th class="min-w-120px pe-4">Total Reports</th>
        <th class="min-w-150px pe-4">Total Amount</th>
      </tr>`);
    $('#kt_doc_table_summary tfoot').html(`
      <tr class="text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
        <th class="min-w-20px pe-2 pe-4">No</th>
        <th class="min-w-120px pe-4">Month</th>
        <th class="min-w-200px pe-4" id="tf_group_label">Customer</th>
        <th class="min-w-120px pe-4">Total Reports</th>
        <th class="min-w-150px pe-4">Total Amount</th>
      </tr>`);
  }

  function setGroupHeader() {
    $('#th_group_label').text('Customer');
    $('#tf_group_label').text('Customer');
  }

  function initSummaryTable() {
    if ($.fn.DataTable.isDataTable('#kt_doc_table_summary')) {
      $('#kt_doc_table_summary').DataTable().clear().destroy();
      $('#kt_doc_table_summary tbody').empty();
    }

    buildStaticHeader();
    setGroupHeader();

    summaryTable = $('#kt_doc_table_summary').DataTable({
      data: [],
      autoWidth: false,
      responsive: true,
      ordering: true,
      order: [],
      columnDefs: [{
          targets: [0, 1, 3, 4],
          className: 'text-center'
        },
        {
          targets: 0,
          width: 60
        }, 
        {
          targets: 1,
          width: 140
        }, 
        {
          targets: 2,
          width: '40%',
          className: 'text-center'
        }, 
        {
          targets: 3,
          width: 140
        }, 
        {
          targets: 4,
          width: 180
        },
      ],
      columns: [{
          data: null,
          render: (d, t, r, m) => m.row + 1
        }, 
        {
          data: 'MonthLabel'
        }, 
        {
          data: 'Customer'
        }, 
        {
          data: 'TotalTransactions'
        }, 
        {
          data: 'TotalAmount',
          render: d => formatRupiah(d)
        }, 
      ],
      language: {
        emptyTable: 'No data available in table'
      },
      initComplete: function() {
        syncTFootWidths(this.api());
      }
    });

    summaryTable.on('draw.dt order.dt column-visibility.dt columns-adjust.dt responsive-resize.dt', function() {
      syncTFootWidths(summaryTable);
    });

    $(window).off('resize.summary').on('resize.summary', function() {
      summaryTable.columns.adjust();
      syncTFootWidths(summaryTable);
    });

    summaryTable.columns.adjust();
    syncTFootWidths(summaryTable);
  }

  
  function getGroupBy() {
    return $('#filter_group_by').val() || 'customer';
  }

  function showCtl(sel) {
    const $el = $(sel);
    if ($.fn.select2 && $el.data('select2')) $el.next('.select2-container').show();
    else $el.show();
  }

  function hideCtl(sel) {
    const $el = $(sel);
    if ($.fn.select2 && $el.data('select2')) $el.next('.select2-container').hide();
    else $el.hide();
  }

  function clearSelect(sel) {
    if ($.fn.select2 && $(sel).data('select2')) $(sel).val('').trigger('change');
    else $(sel).val('');
  }

  function applyGroupByUI() {
    const gb = getGroupBy();

    setGroupHeader();

    $('#filter_value_label').text(
      gb === 'customer' ? 'Customer' :
      gb === 'category' ? 'Category' :
      gb === 'cost_center' ? 'Cost Center' : 'Num CA'
    );

    hideCtl('#filter_customer');
    hideCtl('#filter_category');
    $('#cc_wrapper').hide();
    $('#filter_num_ca').hide();

    if (gb === 'customer') {
      showCtl('#filter_customer');
      clearSelect('#filter_category');
      $('#filter_sai_member').val('');
      $('#filter_cost_center, #filter_cost_center_code, #filter_cost_center_name').val('');
      $('#filter_num_ca').val('');
    } else if (gb === 'category') {
      showCtl('#filter_category');
      clearSelect('#filter_customer');
      $('#filter_sai_member').val('');
      $('#filter_cost_center, #filter_cost_center_code, #filter_cost_center_name').val('');
      $('#filter_num_ca').val('');
   } else if (gb === 'cost_center') {
  $('#cc_wrapper').show();
  clearSelect('#filter_customer');
  clearSelect('#filter_category');
  $('#filter_num_ca').val('');

  
  if ($.fn.select2 && $('#filter_sai_member').data('select2')) {
    $('#filter_sai_member').next('.select2-container').css('width','100%').show();
  }
  reinitSaiMemberSelect();
} else {
      $('#filter_num_ca').show();
      clearSelect('#filter_customer');
      clearSelect('#filter_category');
      $('#filter_sai_member').val('');
      $('#filter_cost_center, #filter_cost_center_code, #filter_cost_center_name').val('');
    }
  }

  function initSaiMemberSelect() {
  $('#filter_sai_member').select2({
    width: '100%',
    placeholder: 'Select Option',
    allowClear: true,
    ajax: {
      url: '{{ route("ref.employee_names") }}',
      dataType: 'json',
      delay: 250,
      data: params => ({ q: params.term || '', page: params.page || 1 }),
      processResults: data => data,
      cache: true
    },
    minimumResultsForSearch: 1,
    
    selectionCssClass: 'form-select form-select-sm rounded-3',
    dropdownCssClass:  'form-select-sm',
    containerCssClass: 'form-select-sm'
  });

  
  $('#filter_sai_member').on('select2:select', function (e) {
    const name = e.params?.data?.text || e.params?.data?.id || '';
    if (!name) return;
    const base = '{{ route("ref.employee_by_name") }}';
    fetch(base + '?name=' + encodeURIComponent(name))
      .then(r => r.json())
      .then(({ cc }) => {
        const code = cc || '';
        $('#filter_cost_center').val(code);
        $('#filter_cost_center_code').val(code);
        $('#filter_cost_center_name').val('');
      })
      .catch(() => {
        $('#filter_cost_center').val('');
        $('#filter_cost_center_code').val('');
        $('#filter_cost_center_name').val('');
      });
  });

  
  $('#filter_sai_member').on('select2:clear', function () {
    $('#filter_cost_center').val('');
    $('#filter_cost_center_code').val('');
    $('#filter_cost_center_name').val('');
  });
}

function reinitSaiMemberSelect() {
  const $sel = $('#filter_sai_member');
  if ($.fn.select2 && $sel.data('select2')) {
    
    $sel.off('select2:select select2:clear');
   
    $sel.select2('destroy');
  }

  initSaiMemberSelect();
}

  function getGroupValue() {
    const gb = getGroupBy();
    if (gb === 'customer') {
      const v = $('#filter_customer').val();
      return (v === '' || v === undefined) ? null : v;
    }
    if (gb === 'category') {
      const v = $('#filter_category').val();
      return (v === '' || v === undefined) ? null : v;
    }
    if (gb === 'cost_center') {
      const code = $('#filter_cost_center_code').val() || '';
      return code || null;
    }
    const n = ($('#filter_num_ca').val() || '').trim();
    return n === '' ? null : n;
  }


  window.loadSummary = function() {
    const {
      start,
      end,
      year
    } = periodToRange();
    const start_date = start;
    const end_date = end;
    const group_by = getGroupBy();
    const group_value = getGroupValue();
    const customer = (group_by === 'customer') ? (group_value || null) : null;

    $('#btn_apply_filter').prop('disabled', true);

    return $.ajax({
      url: "{{ route('sales_report_entertain.summary.data') }}",
      type: 'GET',
      data: {
        start_date,
        end_date,
        group_by,
        group_value,
        customer
      },
      success: function(res) {
        if (!res || !res.success) {
          Swal.fire({
            icon: 'error',
            title: 'Failed',
            text: 'Failed to load summary data.'
          });
          return;
        }

        const longRows = Array.isArray(res.rows) ? res.rows : [];

        
        const monthsWithData = Array.from(
          new Set(
            longRows
            .map(r => (r.Month || '').toString())
            .filter(m => m && m.startsWith(String(year))) 
          )
        ).sort(); 

       
        const agg = {};
        longRows.forEach(r => {
          const cust = (r.Customer ?? r.Group ?? '-').toString();
          const mKey = (r.Month || '').toString();
          if (!mKey || !mKey.startsWith(String(year))) return;

          const key = `${mKey}|${cust}`;
          if (!agg[key]) {
            agg[key] = {
              Month: mKey,
              Customer: cust,
              TotalTransactions: 0,
              TotalAmount: 0
            };
          }
          agg[key].TotalTransactions += (Number(r.Usage) || 0);
          agg[key].TotalAmount += (Number(r.Amount) || 0);
        });

        
        const rows = [];
        monthsWithData.forEach(mKey => {
          const monthItems = Object.values(agg).filter(x => x.Month === mKey);
          
          monthItems.sort((a, b) => b.TotalAmount - a.TotalAmount);
          monthItems.forEach(item => {
            rows.push({
              MonthLabel: monthLabelFromKey(mKey),
              Customer: item.Customer,
              TotalTransactions: item.TotalTransactions,
              TotalAmount: item.TotalAmount
            });
          });
        });

        
        if (!$.fn.DataTable.isDataTable('#kt_doc_table_summary')) {
          initSummaryTable();
        } else {
          
          buildStaticHeader();
          setGroupHeader();
        }

        summaryTable.clear().rows.add(rows).draw();
        summaryTable.columns.adjust();
        syncTFootWidths(summaryTable);
      },
      error: function() {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'An error occurred while fetching summary data.'
        });
      },
      complete: function() {
        $('#btn_apply_filter').prop('disabled', false);
      }
    });
  };


  function ensureYearSelect2() {
  const $fy = $('#filter_year');
  if (!$fy.length) return;

 
  if ($fy.children().length <= 1) {
    const now = new Date().getFullYear();
    const start = now - 5, end = now + 2;      
    $fy.empty().append('<option value=""></option>');
    for (let y = end; y >= start; y--) {
      $fy.append(new Option(String(y), String(y)));
    }
    if (!$fy.val()) $fy.val(String(now));       
  }

 
  if ($.fn.select2 && !$fy.data('select2')) {
    $fy.select2({
      width: '100%',
      placeholder: 'Select Year',
      allowClear: true,
      minimumResultsForSearch: 0,       
      tags: true,                               
      createTag: function(params) {
        const term = $.trim(params.term || '');
        if (!/^\d{4}$/.test(term)) return null; 
        const y = parseInt(term, 10);
        if (y < 1900 || y > 2100) return null;  
        const exists = $fy.find('option').filter(function(){
          return $(this).val() === term;
        }).length > 0;
        if (exists) return null;
        return { id: term, text: term, newTag: true };
      },
      insertTag: function(data, tag){ data.unshift(tag); }
    });
    
    $fy.trigger('change');
  }
}



  $(function() {
  $(document).off('.summary');

  if ($.fn.select2) {
    $('#filter_customer').select2({
      width: '100%',
      placeholder: 'Select Option',
      allowClear: true,
      minimumResultsForSearch: Infinity,
      selectionCssClass: 'form-select-sm',
      dropdownCssClass: 'form-select-sm',
      containerCssClass: 'form-select-sm'
    });
    if ($('#filter_category').length) {
      $('#filter_category').select2({
        width: '100%',
        placeholder: 'Select Option',
        allowClear: true,
        minimumResultsForSearch: Infinity,
        selectionCssClass: 'form-select-sm',
        dropdownCssClass: 'form-select-sm',
        containerCssClass: 'form-select-sm'
      });
    }
  }

  
  initSaiMemberSelect();
  ensureYearSelect2();
  applyPeriodUI();

  $(document).on('change.summary', '#filter_period_type', applyPeriodUI);

  $(document).on('change.summary', '#filter_group_by', applyGroupByUI);

  $(document).on('click.summary', '#btn_apply_filter', function() {
    loadSummary();
  });

  $(document).on('click.summary', '#btn_apply_filter', function(ev) {
  const btn = ev.currentTarget;
  toggleBtnState(btn, 'spinner_apply', true);
  const jq = loadSummary();            
  if (jq && jq.always) {
    jq.always(function(){
      toggleBtnState(btn, 'spinner_apply', false);
    });
  } else {
    toggleBtnState(btn, 'spinner_apply', false);
  }
});

$(document).on('click.summary', '#btn_reset_filter', function(ev) {
  const btn = ev.currentTarget;
  toggleBtnState(btn, 'spinner_reset', true);

 
  $('#filter_month').val('');
  if ($.fn.select2) {
    $('#filter_customer').val('').trigger('change');
    $('#filter_category').val('').trigger('change');
    $('#filter_sai_member').val('').trigger('change');
  } else {
    $('#filter_customer').val('');
    $('#filter_category').val('');
    $('#filter_sai_member').val('');
  }
  $('#filter_group_by').val('customer');
  $('#filter_cost_center').val('');
  $('#filter_cost_center_code').val('');
  $('#filter_cost_center_name').val('');
  $('#filter_num_ca').val('');

  $('#filter_period_type').val('month');  
$('#filter_month').val('');
if ($.fn.select2) {
  $('#filter_year').val('').trigger('change');
} else {
  $('#filter_year').val('');
}
applyPeriodUI();

  applyGroupByUI();
  buildStaticHeader();
  setGroupHeader();

  if ($.fn.DataTable.isDataTable('#kt_doc_table_summary')) {
    summaryTable.clear().draw();
    summaryTable.columns.adjust();
    syncTFootWidths(summaryTable);
  } else {
    initSummaryTable();
  }
  

  
  setTimeout(function(){
    toggleBtnState(btn, 'spinner_reset', false);
  }, 300);
});

  applyGroupByUI();
  initSummaryTable();
});

  
  function exportSummaryExcel(ev) {
    const btn = ev?.currentTarget || document.getElementById('btn_export_excel');
    const $spinner = $('#spinner_export');
    const $a = document.getElementById('download_link');

    
    const {
      start,
      end,
      monthKey
    } = periodToRange();
    const group_by = getGroupBy();
    const group_value = getGroupValue();
    const customer = (group_by === 'customer') ? (group_value || null) : null;

    const baseUrl = "{{ route('sales_report_entertain.summary.export') }}";
    const qs = new URLSearchParams({
      start_date: start,
      end_date: end,
      group_by,
      group_value: group_value ?? '',
      customer: customer ?? ''
    }).toString();
    const fullUrl = `${baseUrl}?${qs}`;

    const doDownload = () => {
      if (btn) {
        btn.disabled = true;
        $spinner.show();
      }
     
      $a.href = fullUrl;
      
      $a.setAttribute('download', '');
      $a.click();

      
      setTimeout(() => {
        if (btn) {
          btn.disabled = false;
          $spinner.hide();
        }
      }, 1200);
    };

    if (typeof Swal !== 'undefined' && Swal && Swal.fire) {
      Swal.fire({
        title: 'Are you sure you want to download the summary?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, download',
        cancelButtonText: 'Cancel'
      }).then((res) => {
        if (res.isConfirmed) doDownload();
      });
    } else {
      if (confirm('Are you sure you want to download the summary?')) doDownload();
    }
  }

  window.addEventListener('pageshow', function (e) {
  const nav = performance.getEntriesByType && performance.getEntriesByType('navigation')[0];
  const fromBFCache = e.persisted || (nav && nav.type === 'back_forward');

  if (fromBFCache) {
    ensureYearSelect2(); 
    applyPeriodUI();     
  }

  if (fromBFCache && getGroupBy() === 'cost_center') {
    reinitSaiMemberSelect();
  }
});

function applyPeriodUI() {
  const mode = $('#filter_period_type').val() || 'month';
  if (mode === 'year') {
    ensureYearSelect2();          
    hideCtl('#filter_month');
    showCtl('#filter_year');
  } else {
    hideCtl('#filter_year');
    showCtl('#filter_month');
  }
}



function periodToRange() {
  const mode = $('#filter_period_type').val() || 'month';
  const pad  = n => String(n).padStart(2, '0');
  const fmt  = d => `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;

  if (mode === 'year') {
    
    const y = parseInt($('#filter_year').val(), 10);
    const year = Number.isInteger(y) ? y : (new Date()).getFullYear();
    const start = new Date(year, 0, 1);
    const end   = new Date(year, 11, 31);
    return {
      start: fmt(start),
      end: fmt(end),
      year,
      monthKey: null
    };
  }

  
  const raw = $('#filter_month').val();
  if (!raw) {
    
    const now = new Date();
    const y = now.getFullYear();
    const start = new Date(y, 0, 1);
    const end   = new Date(y, 11, 31);
    return {
      start: fmt(start),
      end: fmt(end),
      year: y,
      monthKey: null
    };
  }

  const [y, mm] = raw.split('-').map(Number);
  const start = new Date(y, mm - 1, 1);
  const end   = new Date(y, mm, 0);
  return {
    start: fmt(start),
    end: fmt(end),
    year: y,
    monthKey: `${y}-${pad(mm)}`
  };
}

</script>