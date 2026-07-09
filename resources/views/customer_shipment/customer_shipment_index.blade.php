@extends('../layouts/app') 
 

@section('subhead')
    <title>{{ $head_title }}</title>  
    <script type="text/javascript"> 
        $(document).ready(function(){   
            const urlParams = new URLSearchParams(window.location.search); 
            var ref_form = urlParams.get('ref_form');
            var ref_tab = urlParams.get('ref_tab');  
            var ref_doc =  urlParams.get('ref_doc'); 
            var revise = urlParams.get('revise');   
            if(ref_doc == null){ 
                $("#kt_activity_home_tab").addClass('show active');   
            }else{ 
                $('#temp_id').val(ref_doc);
                document_preview(ref_doc) ;
            } 
        })
    </script>
@endsection
  
<script src="<?= env('APP_ASSETS') ?>assets/js/jquery/jquery.min.js"></script> 

@section('subcontent')   
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
    
    <div hidden> 
        <div class="card-toolbar m-0">
            <ul class="nav nav-tabs nav-line-tabs nav-stretch fs-6 border-0 fw-bolder" role="tablist">
                <li class="nav-item" role="presentation">
                    <a id="kt_activity_home_tab" class="nav-link justify-content-center text-active-gray-800 active" data-bs-toggle="tab" 
                    role="tab" href="#kt_activity_home">Home</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a id="kt_activity_preview_tab" class="nav-link justify-content-center text-active-gray-800" data-bs-toggle="tab" role="tab" href="#kt_activity_preview">Preview</a>
                </li> 
            </ul>
        </div>
    </div> 

    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">  
        <div class="tab-content">   
            <div id="kt_activity_home" class="card-body p-0 tab-pane fade show active" role="tabpanel" aria-labelledby="kt_activity_home_tab"> 
                <div class="post d-flex flex-column-fluid mb-5" id="kt_post"> 
                    <div id="kt_content_container" class="container-xxl">
                        <div class="row g-2 g-xl-8">
                            <div class="col-xl-6 col-lg-6 col-sm-6">
                                <a href="#" onclick="docSearch(0, this);" class="card bgi-no-repeat card-xl-stretch mb-5 card-front" 
                                style="background-position: right top; background-size: 30% auto; background-image: 
                                url(<?= env('APP_ASSETS') ?>assets/media/svg/shapes/abstract-4.svg)">
                                    <div class="card-body">  
                                        <div class="text-gray-900 fw-bolder fs-4 mb-2 mt-2" id="total_document"></div>
                                        <div class="fw-bold text-gray-900">All Document MMKI</div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-sm-6">
                                <a href="#" onclick="docSearch(1, this);" class="card bgi-no-repeat card-xl-stretch mb-5 card-front" 
                                style="background-position: right top; background-size: 30% auto; background-image: url(<?= env('APP_ASSETS') ?>assets/media/svg/shapes/abstract-2.svg)">
                                    <div class="card-body"> 
                                        <div class="text-gray-900 fw-bolder fs-4 mb-2 mt-2" id="total_check"></div>
                                        <div class="fw-bold text-gray-900">Draft Shipment MMKI</div>
                                    </div> 
                                </a> 
                            </div>  
                        </div>
                    </div>
                </div> 

                    <div class="d-flex flex-column-fluid mt-lg-5 mt-sm-5" > 
                        <div id="kt_content_container" class="container-xxl">  
                            <div class="card col-xxl-12 card-sticky">  
                                <div class="card-header border-1 pt-6 pb-6 mb-5"> 
                                    <div class="card-title"> 
                                        <div class="d-flex align-items-center position-relative my-1"> 
                                            <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                                    <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                                                </svg>
                                            </span> 
                                            <input type="text" data-kt-goodreceive-table-filter="search" id="front_table_search" class="form-control form-control-solid w-250px ps-15 text-sm form-control-sm" placeholder="Search Pack Number" />
                                        </div> 
                                    </div> 
                                    <div class="card-toolbar"> 
                                        <div class="d-flex justify-content-end" data-kt-goodreceive-table-toolbar="base"> 
                                            <button type="button" class="btn btn-light-primary btn-sm me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
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
                                                        <label class="form-label fs-5 fw-bold mb-3">Status:</label> 
                                                        <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Select option" data-allow-clear="false" id="status_id" data-hide-search="true" onchange="resetFrontCard()"> 
                                                                <option value='0'>All Document</option> 
                                                                <option value='1'>Draft</option>  
                                                        </select> 
                                                    </div>  
                                                    <div class="d-flex justify-content-end"> 
                                                        <button type="submit" id="submit-filter" class="btn btn-primary" data-kt-menu-dismiss="true" data-kt-goodreceive-table-filter="filter">Apply</button>
                                                    </div>
                                                </div>
                                            </div>    
                                        </div> 
                                        <div class="d-flex justify-content-end align-items-center d-none" data-kt-goodreceive-table-toolbar="selected">
                                            <div class="fw-bolder me-5">
                                            <span class="me-2" data-kt-goodreceive-table-select="selected_count"></span>Selected</div>
                                            <button type="button" class="btn btn-danger" data-kt-goodreceive-table-select="delete_selected">Delete Selected</button>
                                        </div>
                                        <button type="button" class="btn btn-light-primary btn-sm me-3" id="btn_add_document" onclick="add_document()">
                                            <span id="svg_add_document" class="svg-icon svg-icon-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <title>Stockholm-icons / Navigation / Plus</title>
                                                    <desc>Created with Sketch.</desc>
                                                    <defs/>
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"/>
                                                        <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) " x="4" y="11" width="16" height="2" rx="1"/>
                                                    </g>
                                                </svg> 
                                            </span> 
                                            <span id="spinner_add_document" class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;"></span>       
                                            <span id="btn_text_add_document">Create</span>
                                        </button>  
                                    </div>
                                </div>  
                                <div class="card-body pt-0"> 
                                    <table class="table align-middle table-row-dashed table-striped gy-2 fs-7" id="kt_doc_table">
                                        <thead>
                                            <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                                <th class="min-w-20px pe-2">No</th>
                                                <th class="min-w-20px">View</th> 
                                                <th class="min-w-100px">Pack</th>
                                                <th class="min-w-100px">Date</th> 
                                                <th class="min-w-100px">Status</th> 
                                            </tr> 
                                        </thead>  
                                        <tfoot>
                                            <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                                <th class="min-w-20px pe-2">No</th>
                                                <th class="min-w-20px">View</th> 
                                                <th class="min-w-100px">Pack</th>
                                                <th class="min-w-100px">Date</th> 
                                                <th class="min-w-100px">Status</th> 
                                            </tr> 
                                        </tfoot> 
                                    </table> 
                                </div> 
                            </div> 
                        </div>  
                    </div>  
                </div>

                <div id="kt_activity_preview" class="card-body p-0 tab-pane fade show" role="tabpanel" aria-labelledby="kt_activity_preview_tab">
                    <div class="d-flex flex-column-fluid">
                        <div id="kt_content_container" class="container-xxl">  
                            <div class="tab-content">
                                <div id="kt_activity_file" class="card-body tab_preview p-0 tab-pane fade show active" role="tabpanel" aria-labelledby="kt_activity_file_tab">
                                    <div class="card">
                                        <div class="card-header card-header-stretch">
                                            <div class="card-title d-flex align-items-center">
                                                {{-- <button class="btn btn-primary btn-sm text-sm" style="width: 100px;" onclick="getApprovalForm()">Approve</button> --}}
                                                <div id="button_approve"></div>
                                                <button class="btn btn-light-success btn-sm ms-2 mr-2 mt-2" onclick="backHome()">Back</button> 
                                                <button class="btn btn-success btn-sm ms-2 mr-2 mt-2" id="btn_ready_to_print" onclick="ready_to_print()">
                                                    <span id="btn_text_ready_to_print">Confirm</span>
                                                    <span id="spinner_ready_to_print" class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;"></span>        
                                                </button> 
                                            </div>
                                            <div class="">
                                                <ul class="nav nav-tabs nav-line-tabs nav-stretch fs-6 border-0 fw-bolder" role="tablist" hidden>
                                                    <li class="nav-item" role="presentation" onclick="getPreview()">
                                                        <a id="kt_activity_file_tab" class="nav-link tab_preview justify-content-center text-active-gray-800 active" data-bs-toggle="tab" 
                                                        role="tab" href="#kt_activity_file">Form Input</a>
                                                    </li> 
                                                </ul>
                                            </div>
                                        </div>   
 
                                        <div class="card-body">
                                            <div id="form_loader" style="text-align: center;">
                                                <div class="lds-roller mt-10 mb-10" id="lds-roller-form"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div> 
                                            </div>
                                            <div class="row" id="form"> 
                                                <div class="col-md-6 mb-5">
                                                    <form>
                                                        <div> 
                                                            <div class="form-group mb-5">
                                                                <label>Pack Number <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control bg-light-primary" id="PackNum" readonly/> 
                                                            </div>
                                                            <div class="form-group mb-5">
                                                                <label for="exampleInputPassword1">Legal Number <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control bg-light-primary" id="LegalNumber" readonly/>
                                                            </div>  
                                                            <!-- <div class="form-group mb-5"> 
                                                                <label>Select SO Number <span class="text-danger">*</span></label>
                                                                <input type="number" class="form-control" id="OrderNum"/>
                                                            </div> -->
                                                            <div class="form-group mb-5">
                                                                <label>SO Number <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control bg-light-primary" id="OrderNumSet" readonly/> 
                                                            </div>
                                                        </div> 
                                                    </form> 
                                                </div>
                                                <div class="col-md-6">
                                                    <form>
                                                        <div>  
                                                            <!-- <div class="form-group mb-5">
                                                                <label>SAI Label<span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control"  id="int_label" placeholder="Scan Barcode"/> 
                                                            </div> -->
                                                            <div class="form-group">
                                                                <label>PONumber Barcode<span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="ext_label" placeholder="PONumber"/>
                                                            </div> 
                                                            <div class="form-group">
                                                                <label>Partnumber Barcode<span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="ext_label" placeholder="Part Number Barcode"/>
                                                            </div> 
                                                              

                                                        </div>
                                                        <div class="pt-5 pb-5"> 
                                                            <hr style="color: gray">
                                                            <button type="button" class="btn btn-light-primary btn-sm mr-2 mt-2" id="btn_set_order_number" onclick="set_order_number()">
                                                                <span id="btn_text_order_number">Update SO</span>
                                                                <span id="spinner_order_number" class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;"></span>    
                                                            </button> 

                                                            <button type="button" class="btn btn-primary btn-sm mr-2 mt-2" id="btn_submit_label" onclick="btn_submit_label()">
                                                                <span id="btn_text">Submit Label</span>
                                                                <span id="spinner" class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;"></span>
                                                            </button>
 
                                                        </div>
                                                    </form> 
                                                </div>
                                            </div> 
                                        </div>  
                                    </div>
                                
                                    <div class="card mt-10">
                                        <div class="card-header border-1 pt-6 pb-6  mb-5"> 
                                            <div class="card-title"> 
                                                <div class="d-flex align-items-center position-relative my-1"> 
                                                    <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                            <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                                            <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                                                        </svg>
                                                    </span> 
                                                    <input type="text" data-kt-goodreceive-table-filter="search" id="detail_table_search" class="form-control form-control-solid w-250px ps-15 text-sm form-control-sm" placeholder="PartNum" />
                                                </div> 
                                            </div>  
                                        </div>  
                                            <div class="card-body pt-0"> 
                                                <table class="table align-middle table-row-dashed table-striped gy-2 fs-7" id="kt_doc_table_2">
                                                    <thead>
                                                        <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                                            <th class="min-w-20px pe-2">No</th>
                                                            <th class="min-w-100px">View</th> 
                                                            <th class="min-w-20px">PartNum</th> 
                                                            <th class="min-w-100px">Qty</th> 
                                                            <th class="min-w-100px">Lot</th> 
                                                        </tr> 
                                                    </thead>  
                                                    <tfoot>
                                                        <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                                            <th class="min-w-20px pe-2">No</th>
                                                            <th class="min-w-100px">View</th> 
                                                            <th class="min-w-20px">PartNum</th> 
                                                            <th class="min-w-100px">Qty</th> 
                                                            <th class="min-w-100px">Lot</th> 
                                                        </tr> 
                                                    </tfoot> 
                                                </table> 
                                            </div> 
                                        </div> 
                                </div> 
                            </div>
                        </div>
                    </div>
                </div>   
            </div>  
        </div>    

        <div class="modal bg-white fade" tabindex="-1" id="kt_modal_show">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content shadow-none">
                    <div class="modal-header">
                        <h5 class="modal-title">List Of Lot</h5>  
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                            <span class="svg-icon svg-icon-2x"></span>
                        </div> 
                    </div>
        
                    <div class="modal-body text-center"> 
                         <div class="card mt-10">
                            <div class="card-header border-1 pt-6 pb-6  mb-5"> 
                                <div class="card-title"> 
                                    <div class="d-flex align-items-center position-relative my-1"> 
                                        <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                                <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                                            </svg>
                                        </span> 
                                        <input type="text" data-kt-goodreceive-table-filter="search" id="release_table_search" class="form-control form-control-solid w-250px ps-15 text-sm form-control-sm" placeholder="Part/Lot" />
                                    </div> 
                                </div>  
                            </div>  
                                <div class="card-body pt-0"> 
                                    <table class="table align-middle table-row-dashed table-striped gy-2 fs-7" id="kt_doc_table_3">
                                        <thead>
                                            <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                                <th class="min-w-20px pe-2">No</th>
                                                <th class="min-w-100px">Delete</th> 
                                                <th class="min-w-20px">PartNum</th> 
                                                <th class="min-w-100px">Qty</th> 
                                                <th class="min-w-100px">Lot</th> 
                                            </tr> 
                                        </thead>  
                                        <tfoot>
                                            <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                                <th class="min-w-20px pe-2">No</th>
                                                <th class="min-w-100px">Delete</th> 
                                                <th class="min-w-20px">PartNum</th> 
                                                <th class="min-w-100px">Qty</th> 
                                                <th class="min-w-100px">Lot</th> 
                                            </tr> 
                                        </tfoot> 
                                    </table> 
                                </div> 
                        </div>
                    </div> 

                    <div class="modal-footer"> 
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="refresh_detail_table()">Close</button>
                    </div>
                </div>
            </div>
        </div>
        
     <input type="text" hidden id="temp_id"> 
     <input type="text" hidden id="l_order_line"> 
     <input type="text" hidden id="l_order_rel"> 
     <input type="text" hidden id="pack_line"> 
<script>   
 

    function delete_lot_confirm(packNum, packLine, sysID) {
        return Swal.fire({
            text: "Yakin Hapus? ",
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
                delete_lot(packNum, packLine, sysID);
            } else { 
                console.log('Penghapusan dibatalkan');
            }
        });
    } 

    function ready_to_print() {
        var button = document.getElementById('btn_ready_to_print');
        var spinner = document.getElementById('spinner_ready_to_print');
        var buttonText = document.getElementById('btn_text_ready_to_print'); 
        spinner.style.display = 'inline-block';
        buttonText.textContent = 'Please Wait...'; 
        button.disabled = true;

        var token = $("[name=_token]").val();   
        var temp_id = $("#temp_id").val(); 
        var string = "&_token="+token+"&trc_unix_id="+temp_id ;
        $.ajax({
            type	: 'POST',
            url	: "{{ route('customer_shipment.ready_to_print') }}",
            data	: string,
            cache	: false,
            dataType	: 'json',
            success : function(data){   
                if (data.code == 200) {
                    $("#temp_id").val(""); 
                    backHome();
                } else {
                    Toast.fire({
                        position: 'bottom-end',
                        title: data.code + " Please reload and try again! ",
                        icon:"error"
                    }) 
                }
                spinner.style.display = 'none';
                buttonText.textContent = 'Confirm'; 
                button.disabled = false;
            },
            error: function( jqXHR, textStatus ) { 
                Toast.fire({
                    position: 'bottom-end',
                    title: data.code + " Please reload and try again! ",
                    icon:"error"
                }) 
                spinner.style.display = 'none';
                buttonText.textContent = 'Confirm'; 
                button.disabled = false;
            }
        }) 
    }

    function un_ready_to_print() {
        var token = $("[name=_token]").val();   
        var temp_id = $("#temp_id").val(); 
        var string = "&_token="+token+"&trc_unix_id="+temp_id ;
        $.ajax({
            type	: 'POST',
            url	: "{{ route('customer_shipment.un_ready_to_print') }}",
            data	: string,
            cache	: false,
            dataType	: 'json',
            success : function(data){    

            },
            error: function( jqXHR, textStatus ) { 
                position: 'bottom-end',
                Toast.fire({
                    title: data.code + " Please reload and try again! ",
                    icon:"error"
                }) 
            }
        }) 
    }

    function delete_lot(packNum, packLine, sysID) {
        var token = $("[name=_token]").val();   
        var string = "&_token="+token+"&packNum="+packNum+"&packLine="+packLine+"&sysID="+sysID ;
        $.ajax({
            type	: 'POST',
            url	: "{{ route('customer_shipment.check_before_delete') }}",
            data	: string,
            cache	: false,
            dataType	: 'json',
            success : function(data){   
                if (data.code == 200) {
                    refresh_detail_release_table(packLine);
                } else {
                    Toast.fire({
                        position: 'bottom-end',
                        title: data.code + " Please reload and try again! ",
                        icon:"error"
                    }) 
                }
            },
            error: function( jqXHR, textStatus ) { 
                Toast.fire({
                    position: 'bottom-end',
                    title: data.code + " Please reload and try again! ",
                    icon:"error"
                }) 
            }
        }) 
    }

    function delete_document_confirm(trc_unix_id, packNum) {
        return Swal.fire({
            text: "Yakin Hapus "+ packNum +" ? ",
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
                delete_document(trc_unix_id);
            } else { 
                console.log('Penghapusan dibatalkan');
            }
        });
    }
 
    

    function getCountDocument() {
        var token = $("[name=_token]").val(); 
        var string = "&_token="+token ;
        $.ajax({
            type	: 'POST',
            url	: "{{ route('customer_shipment.get_count_document') }}",
            data	: string,
            cache	: false,
            dataType	: 'json',
            success : function(data){   
                $("#total_check").text(data.total_check+" Document");
                $("#total_approve").text(data.total_approve+" Document"); 
                $("#total_document").text(data.total_document+" Document");
            } 
        }) 
    }

    $(document).ready(function () {
        front_table() ; getCountDocument(); 
    })

    function front_table() {
        var frontTable = $("#kt_doc_table").DataTable({
            processing: true,
            serverSide: true,
            responsive: false, 
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
                        }],
            ajax: {
                url: "{{ route('customer_shipment.front_table') }}",
                type: 'POST',
                data	: function ( d ) { d._token = $("[name=_token]").val(), d.status_id = $("#status_id").val(), d.section_id = $("#section_id").val(), d.front_table_search = $("#front_table_search").val(); }, 
                cache	: false,
                dataType : 'json'
            },
            columns: [
                { data: 'no', className: 'text-center' }, 
                {
                    data: 'action', 
                    className: 'text-center',
                    orderable: false
                },
                { data: 'PackNum' },
                { data: 'ShipDate' },
                { data: 'ReadyToPrint_c' } 
            ] 
        })     
        setTimeout(function(){ 
            frontTable.ajax.reload();
        },500) 
    }

        $("#front_table_search").keyup(function(event){
                if(event.keyCode == 13){ refresh_front_table();  getCountDocument();} 
        });

        $("#submit-filter").click(function(){
            refresh_front_table(); getCountDocument(); 
        }); 

    function refresh_front_table() {  
        if ($.fn.DataTable.isDataTable('#kt_doc_table')) {
            $('#kt_doc_table').DataTable().destroy();
        } 
        front_table();
    }

    function delete_document(trc_unix_id) {
        var token = $("[name=_token]").val();   
        var string = "&_token="+token+"&trc_unix_id="+trc_unix_id ;
        $.ajax({
            type	: 'POST',
            url	: "{{ route('customer_shipment.delete_document') }}",
            data	: string,
            cache	: false,
            dataType	: 'json',
            success : function(data){   
                if (data.code == 200) {
                    refresh_front_table();
                } else {
                    Toast.fire({
                        position: 'bottom-end',
                        title: data.code + " Please reload and try again! ",
                        icon:"error"
                    }) 
                }
            },
            error: function( jqXHR, textStatus ) { 
                Toast.fire({
                    position: 'bottom-end',
                    title: data.code + " Please reload and try again! ",
                    icon:"error"
                }) 
            }
        }) 
    }

    function resetFrontCard() {
        document.querySelectorAll('.card-front').forEach(function(el) {
            el.classList.remove('bg-light-success');
        }); 
    }

    function backHome() { 
        document.getElementById('kt_activity_home_tab').click(); 
        $("#temp_id").val('');
        refresh_front_table();
        window.history.pushState('', '', '<?php echo env('BASE_URL') ?>/customer_shipment');    
    }  
 
    function docSearch(id, element) {  
        $("#status_id").val(id); 
        $('#status_id').val(id).trigger('change');
        document.getElementById('submit-filter').click();   
        document.querySelectorAll('.card-front').forEach(function(el) {
            el.classList.remove('bg-light-success');
        }); 
        element.classList.add('bg-light-success');
    } 

    function detail_release_table() { 
        var detailReleaseTable = $("#kt_doc_table_3").DataTable({
            processing: true,
            serverSide: true,
            responsive: false, 
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
                        }],
            ajax: {
                url: "{{ route('customer_shipment.detail_release_table') }}",
                type: 'POST',
                data	: function ( d ) { d._token = $("[name=_token]").val(), d.trc_unix_id = $("#temp_id").val(), d.pack_line = $("#pack_line").val(), d.detail_table_search = $("#release_table_search").val(); }, 
                cache	: false,
                dataType : 'json'
            },
            columns: [
                { data: 'no', className: 'text-center' }, 
                {
                    data: 'action', 
                    className: 'text-center',
                    orderable: false
                },
                { data: 'PartNum' },
                { data: 'Qty' },
                { data: 'LotNum' } 
            ]
        })

        setTimeout(function(){ 
            detailReleaseTable.ajax.reload();
        },500) 
    }

    function refresh_detail_release_table(pack_line) { 
        $("#pack_line").val(pack_line);
        $("#kt_modal_show").modal('show');
        if ($.fn.DataTable.isDataTable('#kt_doc_table_3')) {
            $('#kt_doc_table_3').DataTable().destroy();
        } 
        detail_release_table()
    }

    function detail_table() {
        var detailTable = $("#kt_doc_table_2").DataTable({
            processing: true,
            serverSide: true,
            responsive: false, 
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
                        }],
            ajax: {
                url: "{{ route('customer_shipment.detail_table') }}",
                type: 'POST',
                data	: function ( d ) { d._token = $("[name=_token]").val(), d.trc_unix_id = $("#temp_id").val(), d.detail_table_search = $("#detail_table_search").val(); }, 
                cache	: false,
                dataType : 'json'
            },
            columns: [
                { data: 'no', className: 'text-center' }, 
                {
                    data: 'action', 
                    className: 'text-center',
                    orderable: false
                },
                { data: 'PartNum' },
                { data: 'Qty' },
                { data: 'LotNum' } 
            ]
        })

        setTimeout(function(){ 
            detailTable.ajax.reload();
        },500) 
    }

    function refresh_detail_table() { 
        if ($.fn.DataTable.isDataTable('#kt_doc_table_2')) {
            $('#kt_doc_table_2').DataTable().destroy();
        } 
        detail_table();
    }

    $("#detail_table_search").keyup(function(event){
        if(event.keyCode == 13){ refresh_detail_table(); } 
    });

    function getPreview() {
        $("#lds-roller-form").css("display", "");     
        var temp_id = $("#temp_id").val(); 
        var token = $("[name=_token]").val(); 
        var string = "&_token="+token+"&trc_unix_id="+temp_id ;
        $.ajax({
            type	: 'POST',
            url	: "{{ route('customer_shipment.get_preview_doc') }}",
            data	: string,
            cache	: false,
            dataType : 'json',
            success : function(data){  
                window.history.pushState('', '', '<?php echo env('BASE_URL') ?>/customer_shipment?ref_doc='+temp_id+'&ref_tab='+ref_tab);  
                $("#PackNum").val(data.PackNum) ;
                $("#LegalNumber").val(data.LegalNumber) ;
                setTimeout(function(){
                    $("#lds-roller-form").css("display", "none");    
                },500)
        } }) 
    }

    function add_document() {      

        var button = document.getElementById('btn_add_document');
        var svg = document.getElementById('svg_add_document');
        var spinner = document.getElementById('spinner_add_document');
        var buttonText = document.getElementById('btn_text_add_document'); 
        svg.style.display = 'none';
        spinner.style.display = 'inline-block';
        buttonText.textContent = 'Please Wait...'; 
        button.disabled = true;

        $("#lds-roller-form").css("display", "");  
        $("#form").css("display", "none");   
        $("#form_loader").css("display", ""); 
        document.getElementById('kt_activity_preview_tab').click() ;  

        var token = $("[name=_token]").val(); 
        var string = "&_token="+token ;
        $.ajax({
            type	: 'POST',
            url	: "{{ route('customer_shipment.add_document') }}",
            data	: string,
            cache	: false,
            dataType : 'json',
            success : function(data){   
                 if (data.code == 200) {
                    document_preview(data.trc_unix_id) ;
                 } else {
                    document.getElementById('kt_activity_home_tab').click(); 
                    Swal.fire({
                        text: data.code + "Please reload and try again! "+ data.desc,
                        icon:"error",
                        buttonsStyling:!1,
                        confirmButtonText:"Close",
                        customClass:{confirmButton:"btn btn-primary"  
                    }}) 
                 }
            svg.style.display = 'inline-block';
            spinner.style.display = 'none';
            buttonText.textContent = 'Create'; 
            button.disabled = false; 
        },
            error: function( jqXHR, textStatus ) {
                document.getElementById('kt_activity_home_tab').click(); 
                Swal.fire({
                    text: data.code + "Please reload and try again! ",
                    icon:"error",
                    buttonsStyling:!1,
                    confirmButtonText:"Close",
                    customClass:{confirmButton:"btn btn-primary"}
                }) 
        } 
     }) 
    }

    function set_order_number() {   
        var temp_id = $("#temp_id").val(); 
        var OrderNum = $("#OrderNum").val(); 
        var OrderNumSet = $("#OrderNumSet").val();  
        var token = $("[name=_token]").val(); 

        if (OrderNum == OrderNumSet) {
            $("#OrderNum").val(""); 
            spinner.style.display = 'none';
            buttonText.textContent = 'Update SO'; 
            button.disabled = false;
            return false;
        }

        if (OrderNum <= 0) {
            $("#OrderNum").focus();  
            Toast.fire({ 
                position: 'bottom-end',
                title: "Silahkan di isi SO Number!",
                icon:"error"
            }) 
            return false;
        }

        var string = "&_token="+token+"&trc_unix_id="+temp_id+"&OrderNum="+OrderNum ; 
            if (OrderNum != OrderNumSet) { 
                var text = OrderNumSet == 0 ? "Simpan nomor SO ?" : "Ganti nomor SO ?";
                if (confirm(text) == true) {
                    var button = document.getElementById('btn_set_order_number');
                    var spinner = document.getElementById('spinner_order_number');
                    var buttonText = document.getElementById('btn_text_order_number'); 
                    spinner.style.display = 'inline-block';
                    buttonText.textContent = 'Please Wait...'; 
                    button.disabled = true;
                    
                    $.ajax({
                        type	: 'POST',
                        url	: "{{ route('customer_shipment.set_order_number') }}",
                        data	: string,
                        cache	: false,
                        dataType : 'json',
                        success : function(data){   
                            if (data.code != 200) {  
                                Toast.fire({ 
                                    position: 'bottom-end',
                                    title: data.code + " "+ data.desc,
                                    icon:"error"
                                }) 
                            } else {
                                $("#OrderNumSet").val(data.orderNum);  
                                $("#OrderNum").val(""); 
                            }
                            spinner.style.display = 'none';
                            buttonText.textContent = 'Update SO'; 
                            button.disabled = false;
                    },
                    error: function( jqXHR, textStatus ) { 
                        Toast.fire({
                            position: 'bottom-end',
                            title: data.code + " Please reload and try again! ",
                            icon:"error"
                        })
                        spinner.style.display = 'none';
                        buttonText.textContent = 'Update SO'; 
                        button.disabled = false; 
                    } 
                }) 
            }
        }
    }

    

    function document_preview(trc_unix_id) 
        {       
            $("#lds-roller-form").css("display", "");  
            $("#form").css("display", "none");   
            $("#form_loader").css("display", "");   
            document.getElementById('kt_activity_preview_tab').click() ;  
            var token = $("[name=_token]").val(); 
            var string = "&_token="+token+"&trc_unix_id="+trc_unix_id+"&ref_form="+trc_unix_id ;
            $.ajax({
                type	: 'POST',
                url	: "{{ route('customer_shipment.get_preview_doc') }}",
                data	: string,
                cache	: false,
                dataType: 'json',
                success : function(data){   
                    $(".tab_preview").removeClass('active'); 
                    $("#kt_activity_file").addClass('show active');
                    $("#kt_activity_file_tab").addClass('active'); 
                    $("#temp_id").val(trc_unix_id); 
                    $("#PackNum").val(data.PackNum) ;
                    $("#OrderNumSet").val(data.OrderNum) ; 
                    $("#LegalNumber").val(data.LegalNumber) ; 
                    refresh_detail_table();
                    un_ready_to_print();
                    setTimeout(function(){
                        $("#form").css("display", "");   
                        $("#form_loader").css("display", "none");   
                        $("#lds-roller-form").css("display", "none");  
                        window.history.pushState('', '', '<?php echo env('BASE_URL') ?>/customer_shipment?ref_doc='+trc_unix_id+'&ref_tab='+data.ref_tab);   
                    },500)
            } }) 
        };

 
        function submit_label() { 
            var button = document.getElementById('btn_submit_label');
            var spinner = document.getElementById('spinner');
            var buttonText = document.getElementById('btn_text');   
            var trc_unix_id = $("#temp_id").val(); 
            var int_label = $("#int_label").val(); 
            var ext_label_input = $("#ext_label").val();  
            if (ext_label_input.includes("~")) {
                var ext_label = ext_label_input.split("~")[1];  
            } else {
                var ext_label = ext_label_input ;
            }
            var token = $("[name=_token]").val(); 
            var OrderNum = $("#OrderNumSet").val();     
            var labelQty = int_label.split("~")[1] ;   

            if (OrderNum.length === 0) {
                alert('SO Number harus di isi') ;
                return false ;
            }
            if (int_label.length === 0) {
                alert('SAI Label harus di isi') ;
                return false ;
            }   
            var string = "&_token="+token+"&trc_unix_id="+trc_unix_id+"&OrderNum="+OrderNum+"&int_label="+int_label+"&ext_label="+ext_label ;    
                $.ajax({
                    type : 'POST',
                    url	: "{{ route('customer_shipment.submit_label') }}",
                    data	: string,
                    dataType : 'json',
                    cache	: false,
                    success : function(data) { 
                        if (data.process_status == 1) {   
                            if (data.ready_to_post == 1) {    
                                post_detail(OrderNum, data.order_line, data.order_rel, data.part_num, data.part_name, labelQty, data.po_num, ext_label, data.lot_num) ;
                            } else {
                                Toast.fire({ 
                                    position: 'bottom-end',
                                    title: "Silahkan scan label customer!",
                                    icon:"warning"
                                })
                                $("#ext_label").focus(); 
                                spinner.style.display = 'none';
                                buttonText.textContent = 'Submit Label'; 
                                button.disabled = false;
                            } 
                        } else {
                            Toast.fire({ 
                                position: 'bottom-end',
                                title: data.msg_process,
                                icon:"error"
                            })
                            spinner.style.display = 'none';
                            buttonText.textContent = 'Submit Label'; 
                            button.disabled = false;
                        }
                    }, 
                         error: function (data) { 
                            Toast.fire({ 
                                position: 'bottom-end',
                                title: 'Reload and try again !',
                                icon: "error"
                            })
                            spinner.style.display = 'none';
                            buttonText.textContent = 'Submit Label'; 
                            button.disabled = false;
                    } 
                })
            }  

            function submit_label_by_slip_no() { 
                var button = document.getElementById('btn_submit_label');
                var spinner = document.getElementById('spinner');
                var buttonText = document.getElementById('btn_text');  

                var trc_unix_id = $("#temp_id").val(); 
                var int_label = $("#int_label").val(); 
                var ext_label_input = $("#ext_label").val();  
                if (ext_label_input.includes("~")) {
                    var ext_label = ext_label_input.split("~")[1];  
                } else {
                    var ext_label = ext_label_input ;
                }
                var token = $("[name=_token]").val();  

                var OrderNum = $("#OrderNumSet").val();    
                var labelQty = int_label.split("~")[1] ;    

                if (OrderNum.length === 0) {
                    alert('SO Number harus di isi') ;
                    return false ;
                }
                if (int_label.length === 0) {
                    alert('SAI Label harus di isi') ;
                    return false ;
                }   
                var string = "&_token="+token+"&trc_unix_id="+trc_unix_id+"&OrderNum="+OrderNum+"&int_label="+int_label+"&ext_label="+ext_label ;    
                    $.ajax({
                        type : 'POST',
                        url	: "{{ route('customer_shipment.submit_label_by_slip_no') }}",
                        data	: string,
                        dataType	: 'json',
                        cache	: false,
                        success : function(data) { 
                            if (data.process_status == 1) {   
                                if (data.ready_to_post == 1) {    
                                    post_detail(OrderNum, data.order_line, data.order_rel, data.part_num, data.part_name, labelQty, data.po_num, ext_label, data.lot_num) ;
                                } else {
                                    Toast.fire({ 
                                        position: 'bottom-end',
                                        title: data.msg_process,
                                        icon:"error"
                                    })
                                    spinner.style.display = 'none';
                                    buttonText.textContent = 'Submit Label'; 
                                    button.disabled = false;
                                } 
                            } else {
                                Toast.fire({ 
                                    position: 'bottom-end',
                                    title: data.msg_process,
                                    icon:"error"
                                })
                                spinner.style.display = 'none';
                                buttonText.textContent = 'Submit Label'; 
                                button.disabled = false;
                            }
                        }, 
                            error: function (data) { 
                                Toast.fire({ 
                                    position: 'bottom-end',
                                    title: 'Reload and try again !',
                                    icon: "error"
                                })
                                spinner.style.display = 'none';
                                buttonText.textContent = 'Submit Label'; 
                                button.disabled = false;
                        } 
                    })
                } 


            function post_detail(OrderNum, order_line, order_rel, part_num, part_name, labelQty, po_num, ext_label, lot_num) { 

                var button = document.getElementById('btn_submit_label');
                var spinner = document.getElementById('spinner');
                var buttonText = document.getElementById('btn_text');  

                var trc_unix_id = $("#temp_id").val();  
                var token = $("[name=_token]").val();    
                $.ajax({
                    url :"{{ route('customer_shipment.post_detail') }}",
                    type : 'POST',
                    data	: "_token="+token+"&trc_unix_id="+trc_unix_id+"&orderNum="+OrderNum+"&orderLine="+order_line+"&orderRel="+order_rel+"&partNum="+part_num+"&lineDesc="+part_name+"&displayInvQty="+labelQty+"&poNum="+po_num+"&ext_label="+ext_label+"&lot_num="+lot_num,
                    cache	: false,
                    success:function(data) { 
                        if (data.code == 200) {   
                            Toast.fire({ 
                                position: 'bottom-end',
                                title: data.code + " "+ data.desc,
                                icon:"success"
                            })  
                            refresh_detail_table();
                        } else {
                            Toast.fire({ 
                                position: 'bottom-end',
                                title: data.code + " "+ data.desc,
                                icon:"error"
                            })  
                        }   
                        $("#l_order_line").val(""); 
                        $("#l_order_rel").val("");  
                        $("#int_label").val(""); 
                        $("#ext_label").val(""); 
                        $("#int_label").focus(); 
                        spinner.style.display = 'none';
                        buttonText.textContent = 'Submit Label'; 
                        button.disabled = false;
                    }
                })    
            } 

        $("#btn_submit_label").click(function (event) {
            btn_submit_label();
        })
 

        function btn_submit_label() {  
            var button = document.getElementById('btn_submit_label');
            var spinner = document.getElementById('spinner');
            var buttonText = document.getElementById('btn_text'); 
            spinner.style.display = 'inline-block';
            buttonText.textContent = 'Please Wait...'; 
            button.disabled = true;

            var int_label = $("#int_label").val();
            var ext_label = $("#ext_label").val(); 
            
            if (int_label.length > 0 && int_label.includes("~")  && ext_label.length == 0) { 
                submit_label();
            } else if (int_label.length > 0 && int_label.includes("~") && ext_label.length > 0) {
                submit_label_by_slip_no();
            } else { 
                Toast.fire({ 
                    position: 'bottom-end',
                    title: "Silahkan lakukan scan barcode!",
                    icon:"error"
                })  
                spinner.style.display = 'none';
                buttonText.textContent = 'Submit Label'; 
                button.disabled = false;
            }
        }

        $("#int_label").keyup(function (event) {
            var int_label = $(this).val();
            var ext_label = $("#ext_label"); 
            if (event.keyCode === 13 && int_label.length > 0) {
                if (int_label.includes("~")) { 
                    btn_submit_label();
                } 
            }
        });

        $("#ext_label").keyup(function (event) {
            var int_label = $("#int_label");  
            var ext_label = $(this).val(); 
            if (event.keyCode === 13 && ext_label.length > 0) {
                if (int_label.val().length === 0) {
                    int_label.focus();
                } else {
                    btn_submit_label();
                } 
            }
        });
 

</script>
        

@endsection