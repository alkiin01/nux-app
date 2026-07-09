@extends('../layouts/app') 
 

@section('subhead')
    <title>{{ $head_title }}</title>  
@endsection
  
<script src="/public/assets/js/jquery/jquery.min.js"></script> 

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
                            <input type="text" data-kt-goodreceive-table-filter="search" id="front_table_search" class="form-control form-control-solid w-250px ps-15" placeholder="Search GR/SJ" />
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
                                    <div class="d-flex justify-content-end">
                                        <button type="reset" class="btn btn-light btn-active-light-primary me-2" data-kt-menu-dismiss="true" data-kt-goodreceive-table-filter="reset">Reset</button>
                                        <button type="submit" id="submit-filter" class="btn btn-primary" data-kt-menu-dismiss="true" data-kt-goodreceive-table-filter="filter">Apply</button>
                                    </div>
                                </div>
                            </div>   
                            <button type="button" class="btn btn-light-primary me-3" id="b_export_data">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.3" x="12.75" y="4.25" width="12" height="2" rx="1" transform="rotate(90 12.75 4.25)" fill="black" />
                                    <path d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z" fill="black" />
                                    <path d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z" fill="#C4C4C4" />
                                </svg>
                            </span>
                         Export</button>  
                        </div> 
                        <div class="d-flex justify-content-end align-items-center d-none" data-kt-goodreceive-table-toolbar="selected">
                            <div class="fw-bolder me-5">
                            <span class="me-2" data-kt-goodreceive-table-select="selected_count"></span>Selected</div>
                            <button type="button" class="btn btn-danger" data-kt-goodreceive-table-select="delete_selected">Delete Selected</button>
                        </div>
                    </div>
                </div> 
                <div class="card-body pt-0"> 
                    <table class="table align-middle table-row-dashed table-striped fs-6 gy-5 no-footer" id="kt_goodreceives_table">
                        <thead>
                            <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                <th class="min-w-20px pe-2">No</th>
                                <th class="min-w-125px">DocNum</th>
                                <th class="min-w-125px">ShipNum</th> 
                                <th class="min-w-125px">DocDate</th> 
                                <th class="text-end min-w-70px">View</th>
                            </tr> 
                        </thead>  
                    </table> 
                </div> 
            </div> 
        </div> 
    </div> 
</div>
    
<div class="modal bg-white fade" tabindex="-1" id="kt_modal_show">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content shadow-none">
            <div class="modal-header">
                <h5 class="modal-title">GR Preview</h5>  
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <span class="svg-icon svg-icon-2x"></span>
                </div> 
            </div>

            <div class="modal-body text-center">
                <div class="lds-roller mt-10" id="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
                <div id="file_view"></div>
            </div> 
            <div class="modal-footer"> 
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
     
    $(document).ready(function () {
        var frontTable = $("#kt_goodreceives_table").DataTable({
            processing: true,
            serverSide: true,
            responsive: false, 
            deferLoading: 57,
            language : { 
                'processing': '<div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div>'
            },     
            info:!1,
                order:[],
                columnDefs: [
                        {
                            orderable:!1,
                            targets:0
                        }],
            ajax: {
                url: "{{ route('gr_portal.front_table') }}",
                type: 'POST',
                data	: function ( d ) { d._token = $("[name=_token]").val(), d.range_date = $("#range_date").val(), d.front_table_search = $("#front_table_search").val(); }, 
                cache	: false,
                dataType : 'json'
            },
            columns: [
                { data: 'no', className: 'text-center' }, 
                { data: 'docnum' },
                { data: 'shipnum' }, 
                { data: 'docdate' }, 
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
    }) ;

function document_preview(trc_unix_id, ref_form) 
    {     
        $("#lds-roller").css("display", ""); 
        $("#file_view").html("");    
            $("#kt_modal_show").modal('show');
            var token = $("[name=_token]").val(); 
            var string = "&_token="+token+"&trc_unix_id="+trc_unix_id+"&ref_form="+ref_form ;
            $.ajax({
                type	: 'POST',
                url	: "{{ route('gr_portal.print_view') }}",
                data	: string,
                cache	: false,
                success : function(data){ 
                    $("#file_view").html(data);   
                    setTimeout(function(){
                        $("#lds-roller").css("display", "none"); 
                    },500)
            } }) 
    };

$('#b_export_data').click( function(){        
    var token = $("[name=_token]").val();
    // var flow_id = $("#input_trc_type_id").val();
    var range_date = $("#range_date").val();
    var front_table_search = $("#front_table_search").val();
    // var position = $("#statud_doc_id").val();
    var string = "?_token="+token+"&range_date="+range_date+"&front_table_search="+front_table_search ; 
    window.open("<?php echo route('export_gr') ?>"+string); 
});

</script>
        

@endsection