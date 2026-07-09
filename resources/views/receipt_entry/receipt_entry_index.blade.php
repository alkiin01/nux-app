@extends('../layouts/app') 
 

@section('subhead')
    <title>{{ $head_title }}</title>  
    <script type="text/javascript"> 
        $(document).ready(function(){   
            const urlParams = new URLSearchParams(window.location.search);   
            var ref_doc =  urlParams.get('ref_doc');   
            if(ref_doc == '' || ref_doc == null){ 
                $("#kt_activity_home_tab").addClass('show active');   
                window.history.pushState('', '', '<?php echo env('BASE_URL') ?>/receipt_entry');    
            }else{ 
                $('#temp_id').val(ref_doc);  
                document_preview(ref_doc, 0) ;
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
                    <a id="kt_form_tab" class="nav-link justify-content-center text-active-gray-800" data-bs-toggle="tab" role="tab" href="#kt_form">Preview</a>
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
                                <a href="#" onclick="docSearch(3, this);" class="card bgi-no-repeat card-xl-stretch mb-5 card-front" 
                                style="background-position: right top; background-size: 30% auto; background-image: 
                                url(<?= env('APP_ASSETS') ?>assets/media/svg/shapes/abstract-4.svg)">
                                    <div class="card-body">  
                                        <div class="text-gray-900 fw-bolder fs-4 mb-2 mt-2" id="total_document"></div>
                                        <div class="fw-bold text-gray-900">All Document</div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-sm-6">
                                <a href="#" onclick="docSearch(0, this);" class="card bgi-no-repeat card-xl-stretch mb-5 card-front" 
                                style="background-position: right top; background-size: 30% auto; background-image: url(<?= env('APP_ASSETS') ?>assets/media/svg/shapes/abstract-2.svg)">
                                    <div class="card-body"> 
                                        <div class="text-gray-900 fw-bolder fs-4 mb-2 mt-2" id="total_draft"></div>
                                        <div class="fw-bold text-gray-900">Draft</div>
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
                                                            <option value='3' selected>All</option> 
                                                            <option value='0'>Draft</option>  
                                                            <option value='1'>Received</option>  
                                                    </select> 
                                                </div> 
                                                <div class="mb-10"> 
                                                    <label class="form-label fs-5 fw-bold mb-3">Vendor :</label> 
                                                    <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Select option" data-allow-clear="true" id="SelectVendorID" 
                                                    data-hide-search="false" onchange="resetFrontCard()" data-dropdown-parent="#kt-toolbar-filter"></select> 
                                                </div>  
                                                <div class="d-flex justify-content-end"> 
                                                    <button type="submit" id="submit-filter" class="btn btn-primary" data-kt-menu-dismiss="true" data-kt-goodreceive-table-filter="filter">Apply</button>
                                                </div>
                                            </div>
                                        </div>    
                                    </div>  
                                    <button type="button" class="btn btn-light-primary btn-sm me-3"
                                            id="btn_scan_document" onclick="scan_document()">
                                            <span id="svg_scan_document" class="svg-icon svg-icon-2">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                                    viewBox="0 0 24 24" version="1.1">
                                                    <defs />
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <rect fill="#000000" x="4" y="11" width="16" height="2"
                                                            rx="1" />
                                                        <rect fill="#000000" opacity="0.3"
                                                            transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) "
                                                            x="4" y="11" width="16" height="2" rx="1" />
                                                    </g>
                                                </svg>
                                            </span>
                                            <span id="spinner_scan_document"
                                                class="spinner-border spinner-border-sm align-middle ms-2"
                                                style="display: none;"></span>
                                            <span id="btn_text_scan_document">Scan Doc</span>
                                        </button>
                                    {{-- <button type="button" class="btn btn-light-primary btn-sm me-3" id="btn_add_document" onclick="add_document()">
                                        <span id="svg_add_document" class="svg-icon svg-icon-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"> 
                                                <defs/>
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"/>
                                                    <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) " x="4" y="11" width="16" height="2" rx="1"/>
                                                </g>
                                            </svg> 
                                        </span> 
                                        <span id="spinner_add_document" class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;"></span>       
                                        <span id="btn_text_add_document">Create</span>
                                    </button>   --}}
                                </div>
                            </div>  
                            <div class="card-body pt-0"> 
                                <table class="table align-middle table-row-dashed table-striped gy-2 fs-7" id="kt_doc_table">
                                    <thead>
                                        <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                        <th class="min-w-20px pe-2">No</th>
                                            <th class="min-w-20px">View</th> 
                                            <th class="min-w-50px">Legal</th> 
                                            <th class="min-w-30px">Date</th>  
                                            <th class="min-w-150px">Vendor</th>
                                        </tr> 
                                    </thead>  
                                    <tfoot>
                                        <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                        <th class="min-w-20px pe-2">No</th>
                                            <th class="min-w-20px">View</th> 
                                            <th class="min-w-50px">Legal</th> 
                                            <th class="min-w-30px">Date</th>  
                                            <th class="min-w-150px">Vendor</th>
                                        </tr> 
                                    </tfoot> 
                                </table> 
                            </div> 
                        </div> 
                    </div>  
                </div>   
             </div>

                <div id="kt_form" class="card-body p-0 tab-pane fade show" role="tabpanel" aria-labelledby="kt_form_tab">
                    <div class="d-flex flex-column-fluid">
                        <div id="kt_content_container" class="container-xxl">  
                            
                            <div class="card">
                                <div class="card-header card-header-stretch">
                                    <div class="card-title d-flex align-items-center">  
                                        <button class="btn btn-light-success btn-sm ms-2 mr-2 mt-2" onclick="backHome()">
                                            <span class="svg-icon svg-icon-primary svg-icon-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"> 
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24"/>
                                                        <path d="M21.4451171,17.7910156 C21.4451171,16.9707031 21.6208984,13.7333984 19.0671874,11.1650391 C17.3484374,9.43652344 14.7761718,9.13671875 11.6999999,9 L11.6999999,4.69307548 C11.6999999,4.27886191 11.3642135,3.94307548 10.9499999,3.94307548 C10.7636897,3.94307548 10.584049,4.01242035 10.4460626,4.13760526 L3.30599678,10.6152626 C2.99921905,10.8935795 2.976147,11.3678924 3.2544639,11.6746702 C3.26907199,11.6907721 3.28437331,11.7062312 3.30032452,11.7210037 L10.4403903,18.333467 C10.7442966,18.6149166 11.2188212,18.596712 11.5002708,18.2928057 C11.628669,18.1541628 11.6999999,17.9721616 11.6999999,17.7831961 L11.6999999,13.5 C13.6531249,13.5537109 15.0443703,13.6779456 16.3083984,14.0800781 C18.1284272,14.6590944 19.5349747,16.3018455 20.5280411,19.0083314 L20.5280247,19.0083374 C20.6363903,19.3036749 20.9175496,19.5 21.2321404,19.5 L21.4499999,19.5 C21.4499999,19.0068359 21.4451171,18.2255859 21.4451171,17.7910156 Z" fill="#000000" fill-rule="nonzero"/>
                                                    </g>
                                                </svg>
                                            </span>
                                            <span>Back</span>
                                        </button>  
                                    </div> 
                                    <div class="card-toolbar m-0">
                                        <ul class="nav nav-tabs nav-line-tabs nav-stretch fs-6 border-0 fw-bolder" role="tablist">
                                            <li class="nav-item" role="presentation"style="width: 100px; text-align:center;">
                                                <a id="kt_form_header_tab" onclick="getHeader()" class="nav-link justify-content-center text-active-gray-800 active" data-bs-toggle="tab" 
                                                role="tab" href="#kt_form_header">
                                                Header</a>
                                            </li>
                                            <li class="nav-item" role="presentation" style="width: 100px; text-align:center;">
                                                <a id="kt_form_detail_tab" onclick="getForm()" class="nav-link justify-content-center text-active-gray-800" data-bs-toggle="tab" role="tab" href="#kt_form_detail">
                                                    Detail</a>
                                            </li>
                                            <li class="nav-item" role="presentation" style="width: 100px; text-align:center;">
                                                <a id="kt_form_attachment_tab" onclick="getAttachmentList()" class="nav-link justify-content-center text-active-gray-800" data-bs-toggle="tab" role="tab" href="#kt_form_attachment">
                                                    Attach</a>
                                            </li> 
                                            <li class="nav-item" role="presentation"style="width: 100px; text-align:center;">
                                                <a id="kt_form_preview_tab" onclick="getPreview()" class="nav-link justify-content-center text-active-gray-800" data-bs-toggle="tab" role="tab" href="#kt_form_preview">
                                                    Preview</a>
                                            </li> 
                                            <li class="nav-item" role="presentation"style="width: 100px; text-align:center;">
                                                <a id="kt_tag_label_tab" onclick="getTagLabel()" class="nav-link justify-content-center text-active-gray-800" data-bs-toggle="tab" role="tab" href="#kt_tag_label">
                                                    Tag Label</a>
                                            </li> 
                                        </ul>
                                    </div>
                                </div>   
  
                                <div class="tab-content">   
                                    <div id="kt_form_header" class="tab-pane fade" role="tabpanel" aria-labelledby="kt_form_header_tab">
                                        <div id="div_form"></div>  
                                    </div>
                                    <div id="kt_form_detail" class="tab-pane fade" role="tabpanel" aria-labelledby="kt_form_detail_tab">
                                        <div id="div_form_detail"></div>  
                                    </div>
                                    <div id="kt_form_attachment" class="tab-pane fade" role="tabpanel" aria-labelledby="kt_form_attachment_tab">
                                        <div id="attachment_list"></div>
                                    </div>
                                    <div id="kt_form_preview" class="tab-pane fade" role="tabpanel" aria-labelledby="kt_form_preview_tab" style="text-align: center">
                                        <div class="lds-roller mt-20 mb-10" id="lds-roller-preview"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
                                        <div id="file_view" class="p-5"></div>
                                    </div>
                                    <div id="kt_tag_label" class="tab-pane fade" role="tabpanel" aria-labelledby="kt_tag_label_tab" style="text-align: center">
                                        <div class="lds-roller mt-20 mb-10" id="lds-roller-tag-label"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
                                        <div id="tag_label_view" class="p-5"></div>
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
                    </div>PackLine
                </div>
            </div>
        </div>


        <div class="modal fade" id="kt_modal_po_list" tabindex="-1" aria-hidden="true"> 
                <div class="modal-dialog modal-xl"> 
                    <div class="modal-content"> 
                        <div class="modal-header pb-0 border-0 justify-content-end"> 
                            <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" id="btn_close_po_list"> 
                                <span class="svg-icon svg-icon-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                                        <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                                    </svg>
                                </span> 
                            </div> 
                        </div> 
                        <div class="modal-body scroll-y mx-5 mx-xl-18 pt-0 pb-15"> 
                            <div class="text-center mb-15"> 
                                <h1 class="mb-3">Listing PO</h1> 
                                <div class="text-muted fw-bold fs-5">Please make sure all data is correct !</div>
                            </div>   
                            
                            <div class="card">
                                <hr>
                                <div class="card-body pt-0">   
                                    <div class="row">   
                                        <div class="col-md-6"> 
                                            <form>   
                                                <div class="form-group mb-5"> 
                                                    <label>PO Number <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" id="detail_ponum_list_table_search" name="detail_ponum_list_table_search"/>
                                                </div> 
                                            </form>
                                        </div> 

                                        <div class="col-md-6"> 
                                            <form>  
                                                <div class="form-group mb-5"> 
                                                    <label>PartNum <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="detail_po_list_table_search" name="detail_po_list_table_search"/>
                                                </div> 
                                            </form> 
                                        </div>  
                                    </div>  
                                </div>  
                                
                                <hr>
                            
                                <div class="card-body pt-0"> 
                                    <table class="table align-middle table-row-dashed table-striped gy-2 fs-7" id="kt_detail_po_list_table">
                                        <thead>
                                            <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                                <th class="min-w-20px pe-2">No</th> 
                                                <th class="min-w-20px">Action</th> 
                                                <th class="min-w-100px">PartNum</th> 
                                                <th class="min-w-30px">Qty</th> 
                                                <th class="min-w-50px">PO</th>   
                                            </tr> 
                                        </thead>  
                                        <tfoot>
                                            <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                                <th class="min-w-20px pe-2">No</th> 
                                                <th class="min-w-20px">Action</th> 
                                                <th class="min-w-100px">PartNum</th> 
                                                <th class="min-w-30px">Qty</th> 
                                                <th class="min-w-50px">PO</th>    
                                            </tr> 
                                        </tfoot> 
                                    </table> 
                                </div> 
                            </div>

                            <script>

                                function getPOList() {  
                                    $("#kt_modal_po_list").modal('show'); 
                                    var PONum = $("#PONum").val() ;
                                    var PONumD = $("#PONumDetail").val() ;
                                    if (PONum <= 0) {
                                        $("#detail_ponum_list_table_search").val(PONumD);
                                    } else {
                                        $("#detail_ponum_list_table_search").val(PONumD);
                                    } 
                                    refresh_detail_po_list_table();
                                } 

                                function detail_po_list_table() {
                                    if ($.fn.DataTable.isDataTable('#kt_detail_po_list_table')) {
                                        $('#kt_detail_po_list_table').DataTable().destroy();
                                    }
                                    var detailPOListTable = $("#kt_detail_po_list_table").DataTable({
                                        processing: true,
                                        serverSide: true,
                                        responsive: false, 
                                        deferLoading: 57,
                                        language : { 
                                        'processing': '<div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>'
                                        },     
                                        info:!1, 
                                        ajax: {
                                            url: "{{ route('receipt_entry.detail_po_list_table') }}",
                                            type: 'POST',
                                            data	: function ( d ) { d._token = $("[name=_token]").val(), d.ponum = $("#detail_ponum_list_table_search").val(), d.partnum = $("#detail_po_list_table_search").val(); }, 
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
                                            { data: 'PONum' } 
                                        ]
                                    }) 
                                    setTimeout(function(){ 
                                        detailPOListTable.ajax.reload();
                                    },500) 
                                }

                                function refresh_detail_po_list_table() { 
                                    if ($.fn.DataTable.isDataTable('#kt_detail_po_list_table')) {
                                        $('#kt_detail_po_list_table').DataTable().destroy();
                                    } 
                                    detail_po_list_table();
                                }

                                document.getElementById("detail_po_list_table_search").addEventListener("keydown", function(event) { 
                                    if (event.key === "Enter") { 
                                        event.preventDefault();  
                                        refresh_detail_po_list_table(); 
                                    } 
                                });

                                document.getElementById("detail_ponum_list_table_search").addEventListener("keydown", function(event) { 
                                    if (event.key === "Enter") { 
                                        event.preventDefault();  
                                        refresh_detail_po_list_table(); 
                                    } 
                                }); 
                            </script> 
                        </div> 
                    </div> 
                </div> 
            </div>
        
     <input type="text" hidden id="temp_id"> 
     <input type="text" hidden id="PackLine"> 
     <input type="text" hidden id="l_order_rel"> 
     <input type="text" hidden id="pack_line" value="">  
<script>
    function scan_document() {
            var button = document.getElementById('btn_scan_document');
            var svg = document.getElementById('svg_scan_document');
            var spinner = document.getElementById('spinner_scan_document');
            var buttonText = document.getElementById('btn_text_scan_document');
            svg.style.display = 'none';
            spinner.style.display = 'inline-block';
            buttonText.textContent = 'Please Wait...';
            button.disabled = true;

            $("#lds-roller-form").css("display", "");
            $("#form").css("display", "none");
            $("#form_loader").css("display", "");
            document.getElementById('kt_form_tab').click();
            var token = $("[name=_token]").val();
            var string = "&_token=" + token;
            $.ajax({
                type: 'POST',
                url: "{{ route('receipt_entry.scan_document') }}",
                data: string,
                cache: false,
                success: function(data) {
                    $("#div_form").html(data);
                    $("#kt_form_header").addClass('show active');
                    $("#kt_form_header_tab").addClass('active');

                    $("#kt_form_detail").removeClass('show active');
                    $("#kt_form_detail_tab").removeClass('active');

                    $("#kt_form_attachment").removeClass('show active');
                    $("#kt_form_attachment_tab").removeClass('active');

                    $("#kt_form_preview").removeClass('show active');
                    $("#kt_form_preview_tab").removeClass('active');

                    $("#kt_tag_label").removeClass('show active');
                    $("#kt_tag_label_tab").removeClass('active');

                    $("#kt_form_scan_detail_tab").closest('li').hide();
                    $("#kt_form_detail_tab").closest('li').show();
                    setTimeout(function() {
                        $("#form").css("display", "");
                        $("#form_loader").css("display", "none");
                        $("#lds-roller-form").css("display", "none");
                    }, 500)

                    svg.style.display = 'inline-block';
                    spinner.style.display = 'none';
                    buttonText.textContent = 'Scan Doc';
                    button.disabled = false;
                },
                error: function(jqXHR, textStatus) {
                    document.getElementById('kt_activity_home_tab').click();
                    Swal.fire({
                        text: data.code + "Please reload and try again! ",
                        icon: "error",
                        buttonsStyling: !1,
                        confirmButtonText: "Close",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    })
                }
            })
        }
  $(function(){  
        $('#SelectVendorID').select2({
            ajax: {
                type: 'POST',
                url: "{{ route('receipt_entry.get_vendor_list') }}",
                dataType: 'json',
                delay: 250, // delay for search
                data: function(params) {
                    var query = {
                        search: params.term,  
                        category_id: $("#LineCategory").val(),
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
    })     
  
    function getAttachmentList() { 
        $("#lds-roller-form-attachment").css("display", "");  
        $("#form-attachment").css("display", "none");   
        $("#form_attachment_loader").css("display", "");      
        var temp_id = $("#temp_id").val();  
        var token = $("[name=_token]").val(); 
        var string = "&_token="+token+"&trc_unix_id="+temp_id ;
        $.ajax({
            type	: 'POST',
            url	: "{{ route('receipt_entry.get_attachment_list') }}",
            data	: string,
            cache	: false,
            success : function(data){   
                $("#attachment_list").html(data);  
                setTimeout(function(){
                    $("#form-attachment").css("display", "");   
                    $("#form_attachment_loader").css("display", "none");   
                    $("#lds-roller-form-attachment").css("display", "none");   
                },500)  
        } }) 
    }

    function getCountDocument() {
        var token = $("[name=_token]").val(); 
        var SelectVendorID =  $("#SelectVendorID").val();
        var string = "&_token="+token+"&SelectVendorID="+SelectVendorID ;
        $.ajax({
            type	: 'POST',
            url	: "{{ route('receipt_entry.get_count_document') }}",
            data	: string,
            cache	: false,
            dataType	: 'json',
            success : function(data){   
                $("#total_draft").text(data.total_draft+" Document"); 
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
                url: "{{ route('receipt_entry.front_table') }}",
                type: 'POST',
                data	: function ( d ) { d._token = $("[name=_token]").val(), d.status_id = $("#status_id").val(), d.SelectVendorID = $("#SelectVendorID").val(), d.front_table_search = $("#front_table_search").val(); }, 
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
                { data: 'LegalNumber' }, 
                { data: 'EntryDate' }, 
                { data: 'Vendor' } 
            ] 
        })     
        setTimeout(function(){ 
            frontTable.ajax.reload();
        },500) 
    }

        document.getElementById("front_table_search").addEventListener("keydown", function(event) { 
                if (event.key === "Enter") { 
                    event.preventDefault();  
                    refresh_front_table();  getCountDocument();
                } 
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

    function resetFrontCard() {
        document.querySelectorAll('.card-front').forEach(function(el) {
            el.classList.remove('bg-light-success');
        }); 
    }

    function backHome() { 
        document.getElementById('kt_activity_home_tab').click(); 
        $("#temp_id").val('');
        $("#VendorNum").val('');
        $("#PackLine").val('');
        $("#pack_line").val('');
        refresh_front_table();
        window.history.pushState('', '', '<?php echo env('BASE_URL') ?>/receipt_entry');    
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

    function detail_table() {
        var detailTable = $("#kt_detail_table").DataTable({
            processing: true,
            serverSide: true,
            responsive: false, 
            deferLoading: 57,
            language : { 
              'processing': '<div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>'
            },     
            info:!1, 
            ajax: {
                url: "{{ route('receipt_entry.detail_table') }}",
                type: 'POST',
                data	: function ( d ) { d._token = $("[name=_token]").val(), d.temp_id = $("#temp_id").val(), d.detail_table_search = $("#detail_table_search").val(); }, 
                cache	: false,
                dataType : 'json'
            },
            columns: [
                { data: 'no', className: 'text-center' }, 
                {
                    data: 'action',  
                },
                { data: 'PartNum' },
                { data: 'Qty' },
                { data: 'PONum' },
                { data: 'Status' }
            ]
        })

        setTimeout(function(){ 
            detailTable.ajax.reload();
        },500) 
    }

    // function refresh_detail_table() { 
    //     if ($.fn.DataTable.isDataTable('#kt_detail_table')) {
    //         $('#kt_detail_table').DataTable().destroy();
    //     } 
    //     detail_table();
    // }
    function refresh_detail_table() {
            if ($.fn.DataTable.isDataTable('#kt_detail_table')) {
                $('#kt_detail_table').DataTable().clear().destroy();
                detail_table()
            } else {
                detail_table();
            }
        }

    document.getElementById("detail_table_search").addEventListener("keydown", function(event) { 
        if (event.key === "Enter") { 
            event.preventDefault();  
            refresh_detail_table(); 
        } 
    });   

    function getPreview() {
        $("#lds-roller-preview").css("display", ""); 
        $("#file_view").html("");       
        var temp_id = $("#temp_id").val(); 
        var token = $("[name=_token]").val(); 
        var string = "&_token="+token+"&trc_unix_id="+temp_id ;
        $.ajax({
            type	: 'POST',
            url	: "{{ route('receipt_entry.print_view') }}",
            data	: string,
            cache	: false,
            success : function(data){  
                setTimeout(function(){
                    $("#lds-roller-preview").css("display", "none");   
                    $("#file_view").html(data);   
                },500)
        } }) 
    }

    function getTagLabel() {
        $("#lds-roller-tag-label").css("display", ""); 
        $("#tag_label_view").html("");       
        var temp_id = $("#temp_id").val(); 
        var token = $("[name=_token]").val(); 
        var string = "&_token="+token+"&trc_unix_id="+temp_id ;
        $.ajax({
            type	: 'POST',
            url	: "{{ route('receipt_entry.print_tag_label_view') }}",
            data	: string,
            cache	: false,
            success : function(data){  
                setTimeout(function(){
                    $("#lds-roller-tag-label").css("display", "none");   
                    $("#tag_label_view").html(data);   
                },500)
        } }) 
    }

    // function add_document() {   
    //     var button = document.getElementById('btn_add_document');
    //     var svg = document.getElementById('svg_add_document');
    //     var spinner = document.getElementById('spinner_add_document');
    //     var buttonText = document.getElementById('btn_text_add_document'); 
    //     svg.style.display = 'none';
    //     spinner.style.display = 'inline-block';
    //     buttonText.textContent = 'Please Wait...'; 
    //     button.disabled = true;

    //     $("#lds-roller-form").css("display", "");  
    //     $("#form").css("display", "none");   
    //     $("#form_loader").css("display", ""); 
    //     document.getElementById('kt_form_tab').click() ;   
    //     var token = $("[name=_token]").val(); 
    //     var string = "&_token="+token ;
    //     $.ajax({
    //         type	: 'POST',
    //         url	: "{{ route('receipt_entry.add_document') }}",
    //         data	: string,
    //         cache	: false, 
    //         success : function(data){    
    //             $("#div_form").html(data);    
    //             $("#kt_form_header").addClass('show active');
    //             $("#kt_form_header_tab").addClass('active');  

    //             $("#kt_form_detail").removeClass('show active');
    //             $("#kt_form_detail_tab").removeClass('active');  

    //             $("#kt_form_attachment").removeClass('show active');
    //             $("#kt_form_attachment_tab").removeClass('active');  

    //             $("#kt_form_preview").removeClass('show active');
    //             $("#kt_form_preview_tab").removeClass('active');  

    //             $("#kt_tag_label").removeClass('show active');
    //             $("#kt_tag_label_tab").removeClass('active');  
                
                
    //             setTimeout(function(){
    //                 $("#form").css("display", "");   
    //                 $("#form_loader").css("display", "none");   
    //                 $("#lds-roller-form").css("display", "none");   
    //             },500) 

    //         svg.style.display = 'inline-block';
    //         spinner.style.display = 'none';
    //         buttonText.textContent = 'Create'; 
    //         button.disabled = false; 
    //     },
    //         error: function( jqXHR, textStatus ) {
    //             document.getElementById('kt_activity_home_tab').click(); 
    //             Swal.fire({
    //                 text: data.code + "Please reload and try again! ",
    //                 icon:"error",
    //                 buttonsStyling:!1,
    //                 confirmButtonText:"Close",
    //                 customClass:{confirmButton:"btn btn-primary"}
    //             }) 
    //     } 
    //  }) 
    // }
 
    function getHeader() {
        $("#lds-roller-form").css("display", "");  
        $("#form").css("display", "none");   
        $("#form_loader").css("display", "");   
        var token = $("[name=_token]").val(); 
        var trc_unix_id = $("#temp_id").val(); 
        var string = "&_token="+token+"&trc_unix_id="+trc_unix_id ;
        $.ajax({
            type	: 'POST',
            url	: "{{ route('receipt_entry.get_header_attr') }}",
            data	: string,
            cache	: false, 
            dataType: 'json',
            success : function(data){ 
                $("#LegarNumber").val(data.LegarNumber);
                $("#PONum").val(data.PONum);
                $("#PackSlip").val(data.PackSlip);
                $("#VendorName").val(data.VendorName);
                $("#EntryDate").val(data.EntryDate);
                $("#ArrivedDate").val(data.ArrivedDate); 
                $("#ReceiptComment").val(data.ReceiptComment); 
                refresh_detail_table(); 
                setTimeout(function(){
                    $("#form").css("display", "");   
                    $("#form_loader").css("display", "none");   
                    $("#lds-roller-form").css("display", "none");   
                    $("#pack_line").val(""); 
                },500) 
        },
            error: function( jqXHR, textStatus ) {
                document.getElementById('kt_activity_home_tab').click();  
                Swal.fire({
                    text: "Please reload and try again! ",
                    icon:"error",
                    buttonsStyling:!1,
                    confirmButtonText:"Close",
                    customClass:{confirmButton:"btn btn-primary"}
                }) 
            } 
        })
    }

    function document_preview(trc_unix_id, no) 
        {        
            if (no > 0) 
            {
                var svg = document.getElementById('svg_document_preview_' + no);
                var spinner = document.getElementById('spinner_document_preview_' + no); 
                svg.style.display = 'none';
                spinner.style.display = 'inline-block';  
            } 
            $("#lds-roller-form").css("display", "");  
            $("#form").css("display", "none");   
            $("#form_loader").css("display", "");   
            document.getElementById('kt_form_tab').click() ;  
            var token = $("[name=_token]").val(); 
            var string = "&_token="+token+"&trc_unix_id="+trc_unix_id ;
            $.ajax({
                type	: 'POST',
                url	: "{{ route('receipt_entry.get_preview_doc') }}",
                data	: string,
                cache	: false, 
                success : function(data){
                    if (no > 0) 
                    {
                        svg.style.display = 'inline-block';
                        spinner.style.display = 'none';   
                    }
                    $("#div_form").html(data);    
                    $("#kt_form_header").addClass('show active');
                    $("#kt_form_header_tab").addClass('active');  

                    $("#kt_form_detail").removeClass('show active');
                    $("#kt_form_detail_tab").removeClass('active');  

                    $("#kt_form_attachment").removeClass('show active');
                    $("#kt_form_attachment_tab").removeClass('active');  

                    $("#kt_form_preview").removeClass('show active');
                    $("#kt_form_preview_tab").removeClass('active');  

                    $("#kt_tag_label").removeClass('show active');
                    $("#kt_tag_label_tab").removeClass('active'); 
                    
                    
                    setTimeout(function(){
                        $("#form").css("display", "");   
                        $("#form_loader").css("display", "none");   
                        $("#lds-roller-form").css("display", "none");   
                    },500) 
            },
                error: function( jqXHR, textStatus ) {
                    document.getElementById('kt_activity_home_tab').click(); 
                    if (no > 0) 
                    {
                        svg.style.display = 'inline-block';
                        spinner.style.display = 'none';  
                    }
                    Swal.fire({
                        text: "Please reload and try again! ",
                        icon:"error",
                        buttonsStyling:!1,
                        confirmButtonText:"Close",
                        customClass:{confirmButton:"btn btn-primary"}
                    }) 
                } 
            }) 
        }; 

        
        

        function getForm() 
        {           
            $("#lds-roller-form-detail").css("display", "");  
            $("#form-detail").css("display", "none");   
            $("#form_detail_loader").css("display", "");  
            var token = $("[name=_token]").val(); 
            var trc_unix_id = $("#temp_id").val(); 
            var pack_line = $("#pack_line").val(); 

            var button = document.getElementById('btn_add_detail_document');
            var svg = document.getElementById('svg_add_detail_document');
            var spinner = document.getElementById('spinner_add_detail_document');
            var buttonText = document.getElementById('btn_text_add_detail_document'); 
            svg.style.display = 'none';
            spinner.style.display = 'inline-block';
            buttonText.textContent = 'Please Wait...'; 
            button.disabled = true;

            var string = "&_token="+token+"&trc_unix_id="+trc_unix_id+"&detail_id="+pack_line ; 
            $.ajax({
                type	: 'POST',
                url	: "{{ route('receipt_entry.get_preview_doc_detail') }}",
                data	: string,
                cache	: false, 
                success : function(data){   
                    $("#div_form_detail").html(data);      
                    setTimeout(function(){
                        $("#form-detail").css("display", "");   
                        $("#form_detail_loader").css("display", "none");   
                        $("#lds-roller-form-detail").css("display", "none");  
                        svg.style.display = 'inline-block';
                        spinner.style.display = 'none';
                        buttonText.textContent = 'Add Line'; 
                        button.disabled = false;  
                    },500) 
            },
                error: function( jqXHR, textStatus ) {   
                    svg.style.display = 'inline-block';
                    spinner.style.display = 'none';
                    buttonText.textContent = 'Create'; 
                    button.disabled = false; 
                    Swal.fire({
                        text: "Please reload and try again! ",
                        icon:"error",
                        buttonsStyling:!1,
                        confirmButtonText:"Close",
                        customClass:{confirmButton:"btn btn-primary"}
                    }) 
                } 
            }) 
        }; 

        function getDetail(id)  { 
            $("#pack_line").val(id); 
            document.getElementById('kt_form_detail_tab').click() ;   
        }; 
  
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