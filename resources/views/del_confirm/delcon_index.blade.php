@extends('../layouts/app') 
 

@section('subhead')
    <title>{{ $head_title }}</title>  
    <script src="{{ asset('<?= env('APP_ASSET') ?>assets/js/jquery/jquery.min.js') }}"></script> 
    <script type="text/javascript">
        $(function () { 
            $(".number_format").keypress(function (e) {
                var keyCode = e.keyCode || e.which;  
                var regex = /^[0-9]+$/; 
                var isValid = regex.test(String.fromCharCode(keyCode)); 
                return isValid;
            });   
            $(".replace_quote").keypress(function (e) {
                var keyCode = e.keyCode || e.which;  
                var regex = /[0-9,A-Z,a-z, ]/g; 
                var isValid = regex.test(String.fromCharCode(keyCode)); 
                return isValid;
            }); 
            $(".datepickr").flatpickr({ 
                dateFormat: "Y-m-d",
            });  
        });
        </script>
@endsection
   
@section('subcontent')  
<div class="content d-flex flex-column flex-column-fluid" id="kt_content"> 
    <div class="toolbar" id="kt_toolbar"> 
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack"> 
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">{{ $head_title }} 
                <span class="h-20px border-gray-200 border-start ms-3 mx-2"></span> 
                <small class="text-muted fs-7 fw-bold my-1 ms-1">#{{ auth()->user()->full_name }}</small>
                </h1> 
            </div>  
        </div> 
    </div>

    <div class="post d-flex flex-column-fluid" id="kt_post"> 
        <div id="kt_content_container" class="container-xxl">  

            <div class="py-5">   
                    <div class="tab-content" id="myTabContent">  
                        <div class="tab-pane fade active show" id="kt_tab_pane_1" role="tabpanel"> 
                            <div class="card col-xxl-12">  
                                <div class="card-header border-0 pt-6"> 
                                    <div class="card-title"> 
                                        <div class="d-flex align-items-center position-relative my-1"> 
                                            <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                                    <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                                                </svg>
                                            </span> 
                                            <input type="text" data-kt-goodreceive-table-filter="search" id="front_table_search" class="form-control form-control-solid w-250px ps-15" placeholder="Search PO/SJ/DC" />
                                        </div> 
                                    </div> 
                                    <div class="card-toolbar"> 
                                        <div class="d-flex justify-content-end" data-kt-goodreceive-table-toolbar="base"> 
                                            <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                            <!--begin::Svg Icon | path: icons/duotune/general/gen031.svg-->
                                            <span class="svg-icon svg-icon-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black" />
                                                </svg>
                                            </span>
                                           Filter</button>
                                            <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true" id="kt-toolbar-filter">
                                                <div class="px-7 py-5">
                                                    <div class="fs-4 text-dark fw-bolder">Filter Options</div>
                                                </div> 
                                                <div class="separator border-gray-200"></div> 
                                                <div class="px-7 py-5"> 
                                                <div class="mb-10"> 
                                                    <label class="form-label fs-5 fw-bold mb-3">Month:</label> 
                                                    <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Select option" data-allow-clear="true" id="range_date" data-hide-search="true"> 
                                                    <?php 
                                                        date_default_timezone_set('Asia/Jakarta');
                                                        $month = date('m'); 
                                                        $current_month = date('Y-m'); 
                                                        $d = strtotime("-4 Months"); 
                                                        $dValue = date("Y-m-d", $d) ;  
                                                        $dt = strtotime(date("Y-m-d", $d)); 
                                                        ?> 
                                                            <?php for($i=1; $i<=4; $i++) {  
                                                                $month = strtotime("+$i month", $dt) ; 
                                                                $monthValue = date("m", $month) ; 
                                                                $dateObj   = DateTime::createFromFormat('!m', $monthValue) ;
                                                                $monthName = $dateObj->format('F') ; 
                                                                $year = date("Y", $month) ;  
                                                                $monthRun = date("Y-m", $month) ;  
                                                                $yearValue = substr($year,2,2).date("m", $month) ;        
                                                            ?>
                                                            <option value='<?php echo $yearValue ?>' <?php echo ($current_month == $monthRun ? 'selected' : '') ?>>
                                                            <?php echo $monthName.' '.$year ?></option>
                                                        <?php } ?> 
                                                        </select> 
                                                    </div> 
                                                    <div class="mb-10"> 
                                                        <label class="form-label fs-5 fw-bold mb-3">Category:</label> 
                                                        <select class="form-select form-select-solid fw-bolder" id="input_trc_type_id" data-kt-select2="true" data-placeholder="Select option" data-allow-clear="true" data-hide-search="true">
                                                            <option></option>
                                                            <option value="28" selected>PO SBC</option>
                                                            <option value="75">PO PS</option> 
                                                            <option value="76">PO PP</option> 
                                                            <option value="30">PO RM</option> 
                                                        </select> 
                                                    </div>
                                                    <div class="mb-10"> 
                                                        <label class="form-label fs-5 fw-bold mb-3">Status:</label> 
                                                        <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Select option" data-allow-clear="true" id="statud_doc_id" data-hide-search="true">  
                                                            <option value="3" selected>Confirm</option>   
                                                            <option value="1">Draft</option>
                                                        </select> 
                                                    </div>  
                                                    <div class="d-flex justify-content-end">
                                                        <button type="reset" class="btn btn-light btn-active-light-primary me-2" data-kt-menu-dismiss="true" data-kt-goodreceive-table-filter="reset">Reset</button>
                                                        <button type="submit" id="submit-filter" class="btn btn-primary" data-kt-menu-dismiss="true" data-kt-goodreceive-table-filter="filter">Apply</button>
                                                    </div>
                                                </div>
                                            </div>   
                                            <button type="button" class="btn btn-light-primary me-3" id="b_create_data">
                                            <span class="svg-icon svg-icon-2">
                                                <span class="svg-icon svg-icon-muted svg-icon-2hx">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 26 26" fill="none">
                                                    <path d="M20 8H16C15.4 8 15 8.4 15 9V16H10V17C10 17.6 10.4 18 11 18H16C16 16.9 16.9 16 18 16C19.1 16 20 16.9 20 18H21C21.6 18 22 17.6 22 17V13L20 8Z" fill="black"/>
                                                    <path opacity="0.3" d="M20 18C20 19.1 19.1 20 18 20C16.9 20 16 19.1 16 18C16 16.9 16.9 16 18 16C19.1 16 20 16.9 20 18ZM15 4C15 3.4 14.6 3 14 3H3C2.4 3 2 3.4 2 4V13C2 13.6 2.4 14 3 14H15V4ZM6 16C4.9 16 4 16.9 4 18C4 19.1 4.9 20 6 20C7.1 20 8 19.1 8 18C8 16.9 7.1 16 6 16Z" fill="black"/>
                                                    </svg>
                                                </span>
                                            </span>
                                         Create</button>  
                                        </div>  
                                    </div>
                                </div> 
                                <div class="card-body pt-0"> 
                                    <table class="table align-middle table-row-dashed table-striped fs-6 gy-5 no-footer" id="kt_goodreceives_table">
                                        <thead>
                                            <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                                <th class="min-w-20px pe-2">No</th>
                                                <th class="min-w-125px">DocNum</th>
                                                <th class="min-w-125px">DocDate</th>
                                                <th class="min-w-125px">PONum</th>
                                                <th class="min-w-125px">ShipNum</th> 
                                                <th class="min-w-125px">Status</th> 
                                                <th class="text-end min-w-70px">View</th>
                                            </tr> 
                                        </thead>  
                                    </table> 
                                </div> 
                            </div>  
                        </div> 
                    </div> 
            </div> 
             
        </div> 
    </div>  
</div>
 
    <div class="modal bg-white fade" tabindex="-1" data-bs-backdrop="static" id="kt_modal_show" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content shadow-none">
                <div class="modal-header">
                    <h5 class="modal-title">Create Delivery Confirm</h5>   
                </div> 
                <div class="modal-body">  

                    <div class="px-7 py-5  text-center">
                        <div class="fs-4 text-dark fw-bolder">Purchase Order Selection</div>
                    </div>  
                    <div class="separator border-gray-200"></div>   
                        <div class="card"> 
                            <div class="card-title mb-10">  
                                <div class="row mt-10">  
                                    <div class="col-md-3 px-5"> 
                                        <label class="form-label fs-5 fw-bold mb-3">Month:</label> 
                                            <select class="form-select form-select-solid fw-bolder" onchange="update_detail_po()" data-kt-select2="true" data-placeholder="Select option" data-allow-clear="true" id="range_date_form" data-hide-search="true"> 
                                                    <?php 
                                                        date_default_timezone_set('Asia/Jakarta');
                                                        $month = date('m'); 
                                                        $current_month = date('Y-m'); 
                                                        $d = strtotime("-4 Months"); 
                                                        $dValue = date("Y-m-d", $d) ;  
                                                        $dt = strtotime(date("Y-m-d", $d)); 
                                                    for($i=1; $i<=4; $i++) {  
                                                        $month = strtotime("+$i month", $dt) ; 
                                                        $monthValue = date("m", $month) ; 
                                                        $dateObj   = DateTime::createFromFormat('!m', $monthValue) ;
                                                        $monthName = $dateObj->format('F') ; 
                                                        $year = date("Y", $month) ;  
                                                        $monthRun = date("Y-m", $month) ;  
                                                        $yearValue = substr($year,2,2).date("m", $month) ;        
                                                    ?>
                                                    <option value='<?php echo $yearValue ?>' <?php echo ($current_month == $monthRun ? 'selected' : '') ?>>
                                                    <?php echo $monthName.' '.$year ?></option>
                                                    <?php } ?> 
                                            </select>  
                                    </div>
                                    <div class="col-md-3 px-5"> 
                                        <label class="form-label fs-5 fw-bold mb-3">Category:</label> 
                                        <select class="form-select form-select-solid fw-bolder" id="flow_id_form" data-kt-select2="true" data-placeholder="Select..." data-allow-clear="true" data-hide-search="true" onchange="update_detail_po()"> 
                                            @foreach ($doc_access_list as $item)  
                                                <option value="<?= '200_'.$item->trc_type_id.'_'.$item->trc_type_id_next ?>"><?= $item->descr ?></option>
                                            @endforeach 
                                        </select>  
                                    </div>

                                    <div class="col-md-3 px-5">  
                                        <label class="form-label fs-5 fw-bold mb-3">PO Number:</label> 
                                        <select class="form-select form-select-solid" id="search_po_form" data-dropdown-parent="#kt_modal_show" onchange="show_detail_po()">   
                                        </select>  
                                    </div>  

                                    <div class="col-md-3 px-5">  
                                        <label class="form-label fs-5 fw-bold mb-3">Search Item :</label> 
                                        <div class="d-flex align-items-center position-relative"> 
                                            <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                                    <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                                                </svg>
                                            </span> 
                                            <input type="text" data-kt-goodreceive-table-filter="search" id="search_item" class="form-control form-control-solid w-100 ps-15"/>
                                        </div>   
                                    </div>   
                                </div>
                            </div>  

                            <div class="px-3"> 
                                <table class="table table-row-dashed table-striped fs-7 no-footer" id="kt_item_po_table" style="width: 100%;">
                                    <thead>
                                        <tr class="text-gray-800 fw-bolder fs-7 gs-0" style="border-bottom: 2px solid #181c32;">
                                            <th class="min-w-20px pe-2">No</th> 
                                            <th class="text-start min-w-170px">ItemNo</th>
                                            <th class="text-start min-w-255px">ItemName</th>
                                            <th class="text-end min-w-55px">Qty</th>
                                            <th class="text-end min-w-75px px-3">Balance</th>   
                                        </tr> 
                                    </thead>  
                                </table> 
                            </div>    
                        </div>  
                    </div>    
                <div class="modal-footer">  
                    <button type="submit" class="btn btn-xs btn-outline btn-outline-dashed btn-outline-primary btn-active-light-primary me-2" data-bs-dismiss="modal"><span class="indicator-label">
                            <span class="svg-icon svg-icon-muted svg-icon-2x">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M14.6 4L6.6 12L14.6 20H10.6L3.3 12.7C2.9 12.3 2.9 11.7 3.3 11.3L10.6 4H14.6Z" fill="black"></path>
                                    <path opacity="0.3" d="M21.6 4L13.6 12L21.6 20H17.6L10.3 12.7C9.9 12.3 9.9 11.7 10.3 11.3L17.6 4H21.6Z" fill="black"></path>
                                </svg>
                            </span>
                            Close
                        </span>
                        <span class="indicator-progress">Please wait...<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button> 
                    <button type="submit" id="btn-select-po" class="btn btn-xs btn-outline btn-outline-dashed btn-outline-primary btn-active-light-primary" onclick="return selectPO()"><span class="indicator-label">
                        <span class="svg-icon svg-icon-muted svg-icon-2x"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="black"/>
                            <path opacity="0.5" d="M12.4343 12.4343L10.75 10.75C10.3358 10.3358 9.66421 10.3358 9.25 10.75C8.83579 11.1642 8.83579 11.8358 9.25 12.25L12.2929 15.2929C12.6834 15.6834 13.3166 15.6834 13.7071 15.2929L19.25 9.75C19.6642 9.33579 19.6642 8.66421 19.25 8.25C18.8358 7.83579 18.1642 7.83579 17.75 8.25L13.5657 12.4343C13.2533 12.7467 12.7467 12.7467 12.4343 12.4343Z" fill="black"/>
                            <path d="M8.43431 12.4343L6.75 10.75C6.33579 10.3358 5.66421 10.3358 5.25 10.75C4.83579 11.1642 4.83579 11.8358 5.25 12.25L8.29289 15.2929C8.68342 15.6834 9.31658 15.6834 9.70711 15.2929L15.25 9.75C15.6642 9.33579 15.6642 8.66421 15.25 8.25C14.8358 7.83579 14.1642 7.83579 13.75 8.25L9.56569 12.4343C9.25327 12.7467 8.74673 12.7467 8.43431 12.4343Z" fill="black"/>
                        </svg></span>
                            Select PO
                        </span>
                        <span class="indicator-progress">Please wait...<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button> 

                </div>

            </div>
        </div>
    </div>


<script> 
    $(document).ready(function () {  
        var frontTable = $("#kt_goodreceives_table").DataTable({
            processing: true,
            serverSide: true,
            responsive: true, 
            deferLoading: 57,
            language : { 
                'processing': '<div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>'
            },     
            info:!1,
                order:[],
                columnDefs: [
                        {
                            orderable:!1,
                            targets:0
                        },{
                            orderable:!1,
                            targets:6
                        }],
            ajax: {
                url: "{{ route('delcon.front_table') }}",
                type: 'POST',
                data	: function ( d ) { d._token = $("[name=_token]").val(), d.flow_id = $("#input_trc_type_id").val(), d.range_date = $("#range_date").val(), d.front_table_search = $("#front_table_search").val(), d.position = $("#statud_doc_id").val() ; }, 
                cache	: false,
                dataType : 'json'
            },
            columns: [
                { data: 'no', className: 'text-center' }, 
                { data: 'docnum' },
                { data: 'docdate' },
                { data: 'ponum' },
                { data: 'shipnum' },
                { data: 'status', className: 'text-center' }, 
                {
                    data: 'action', 
                    className: 'text-center',
                    orderable: false
                }
            ]
        })   

        setTimeout(function(){ 
            frontTable.ajax.reload();    
        },500)  

        $("#front_table_search").keyup(function(event){
                if(event.keyCode == 13){ frontTable.ajax.reload();  } 
        }); 

        $("#submit-filter").click(function(){
                frontTable.ajax.reload();  
        });   

    $("#search_po_form").select2({ 
        placeholder: "Select PO...", 
        minimumInputLength: 0,
        ajax: {
            url: "{{ route('delcon.listing_po') }}",
            type: "post",
            dataType: 'json',
            quietMillis: 250,
            data: function(params) { 
                    return {
                        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),   
                        searchTerm : params.term, 
                        flow_id : $("#flow_id_form").val(), 
                        range_date : $("#range_date_form").val(),
                        page: params.page || 1           
                    };
            },
            results: function(data, page) {
                return {results: data};
            },
            cache: true
        },
        formatResult: function(element){
            return element.text + ' (' + element.id + ')';
        },
        formatSelection: function(element){
            return element.text + ' (' + element.id + ')';
        },
        escapeMarkup: function(m) {
            return m;
        }
    })

}) ;

function update_detail_po() {
    $("#search_po_form").val('').trigger('change');
    show_detail_po();
}


function selectPO() {
    var flow_id_form = $("#flow_id_form").val();
    var search_po_form = $("#search_po_form").val();
    var token = $("[name=_token]").val(); 
    var string = "&_token="+token+"&flow_id="+flow_id_form+"&search_po_form="+search_po_form ;
    $("#btn-select-po").attr("data-kt-indicator", "on");
    $("#btn-select-po").attr("disabled", "disabled");   
    $.ajax({
            type	: 'POST',
            typeData	: 'json',
            url	: "{{ route('delcon.proceed_to_draft') }}",
            data	: string,
            cache	: false,
            success : function(response) {  
                $("#btn-select-po").removeAttr("data-kt-indicator", "on");
                $("#btn-select-po").removeAttr("disabled", "disabled");
                var response = JSON.parse(response) 
                window.location.href = 'del_confirm/open_doc?ref_doc='+response.ref_doc+'&ref_form='+response.ref_form ;  
        },
        error: function (data) { 
            Toast.fire({
                icon: 'error',
                title: 'Please reload and try again!'
            })
            $("#btn-select-po").removeAttr("data-kt-indicator", "on");
            $("#btn-select-po").removeAttr("disabled", "disabled");
        } 
    })

} 

$('#b_create_data').click( function(){        
     $("#kt_modal_show").modal('show');
}); 
 

function show_detail_po () {
    var item_po_table = $("#kt_item_po_table").DataTable({
            processing: true,
            serverSide: true,
            responsive: true, 
            deferLoading: 57,
            stateSave: true,
            "bDestroy": true,
            language : { 
                'processing': '<div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>'
            },      
            ajax: {
                url: "{{ route('delcon.item_po_list') }}",
                type: 'POST',
                data	: function ( d ) { d._token = $("[name=_token]").val(), d.search_item = $("#search_item").val(), 
                d.search_po_form = $("#search_po_form").val() ; }, 
                cache	: false,
                dataType : 'json',
            },
            columns: [
                { data: 'no', className: 'text-center' },  
                { data: 'item_no', className: 'text-start' },
                { data: 'item_name', className: 'text-start' },
                { data: 'qty', className: 'text-end px-3' },
                { data: 'balance', className: 'text-end px-3' } 
            ]
        })
        setTimeout(function(){ 
            item_po_table.ajax.reload();  
        },500)  
    }

    $("#search_item").keyup(function(event) { 
        if(event.keyCode == 13){ show_detail_po ();  } 
    }); 

    
 

</script>

@endsection