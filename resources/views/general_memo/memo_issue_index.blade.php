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
                        <div class="card">
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
                                        <input type="text" data-kt-goodreceive-table-filter="search" id="front_table_search" class="form-control form-control-solid w-250px ps-15 text-sm form-control-sm" placeholder="Search JO/PartNum" />
                                    </div> 
                                </div>    
                                    <button type="button" class="btn btn-light-primary btn-sm" id="btn_print_all" onclick="detail()">
                                        <span id="svg_add_manual" class="svg-icon svg-icon-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"> 
                                                <defs/>
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"/>
                                                    <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) " x="4" y="11" width="16" height="2" rx="1"/>
                                                </g>
                                            </svg> 
                                        </span>
                                        <span id="spinner_add_manual" class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;"></span> 
                                        <span id="btn_text_add_manual">Add Memo</span>
                                    </button> 
                            </div>  
                            <div class="card-body pt-0"> 
                                <table class="table align-middle table-row-dashed table-striped gy-2 fs-7" id="kt_doc_table">
                                    <thead>
                                        <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                            <th class="min-w-20px pe-2">No</th>
                                            <th class="min-w-30px">Date</th> 
                                            <th class="min-w-20px">Class</th>
                                            <th class="min-w-100px">PartNum</th> 
                                            <th class="min-w-100px">PartName</th> 
                                            <th class="min-w-20px">Qty</th> 
                                            <th class="min-w-20px">Warehouse</th> 
                                            <th class="min-w-20px">Bin</th> 
                                            <th class="min-w-20px">Lot</th> 
                                            <th class="min-w-20px">Remark</th> 
                                            <th class="min-w-20px">Approval Pos</th> 
                                            <th class="min-w-20px">&nbsp;</th> 
                                        </tr> 
                                    </thead>  
                                    <tfoot>
                                        <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                            <th class="min-w-20px pe-2">No</th>
                                            <th class="min-w-30px">Date</th> 
                                            <th class="min-w-20px">Class</th>
                                            <th class="min-w-100px">PartNum</th> 
                                            <th class="min-w-100px">PartName</th> 
                                            <th class="min-w-20px">Qty</th> 
                                            <th class="min-w-20px">Warehouse</th> 
                                            <th class="min-w-20px">Bin</th> 
                                            <th class="min-w-20px">Lot</th> 
                                            <th class="min-w-20px">Remark</th> 
                                            <th class="min-w-20px">Approval Pos</th> 
                                            <th class="min-w-20px">&nbsp;</th> 
                                        </tr> 
                                    </tfoot> 
                                </table> 
                            </div> 
                        </div> 
                        <hr style="color: gray">
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
                                            <button class="btn btn-success btn-sm" id="btn_back_home" onclick="backHome()">
                                                <span id="svg_back_home" class="svg-icon svg-icon-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"> 
                                                        <defs/>
                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                            <rect x="0" y="0" width="24" height="24"/>
                                                            <path d="M3.95709826,8.41510662 L11.47855,3.81866389 C11.7986624,3.62303967 12.2013376,3.62303967 12.52145,3.81866389 L20.0429,8.41510557 C20.6374094,8.77841684 21,9.42493654 21,10.1216692 L21,19.0000642 C21,20.1046337 20.1045695,21.0000642 19,21.0000642 L4.99998155,21.0000673 C3.89541205,21.0000673 2.99998155,20.1046368 2.99998155,19.0000673 L2.99999828,10.1216672 C2.99999935,9.42493561 3.36258984,8.77841732 3.95709826,8.41510662 Z M10,13 C9.44771525,13 9,13.4477153 9,14 L9,17 C9,17.5522847 9.44771525,18 10,18 L14,18 C14.5522847,18 15,17.5522847 15,17 L15,14 C15,13.4477153 14.5522847,13 14,13 L10,13 Z" fill="#000000"/>
                                                        </g>
                                                    </svg>
                                                </span> 
                                                <span id="spinner_back_home" class="spinner-border spinner-border-sm svg-icon svg-icon-2" style="display: none;"></span>       
                                                <span id="btn_text_back_home">Back</span>
                                            </button>  
                                        </div> 

                                        <div class="pt-5 pb-5"> 
                                            <button class="btn btn-primary btn-sm text-sm" id="btn_create_label" onclick="saveUpdateMemo()">
                                                <span id="svg_create_label" class="svg-icon svg-icon-2" style="margin-left: -7px;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"> 
                                                        <defs/>
                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                            <polygon points="0 0 24 0 24 24 0 24"/>
                                                            <path d="M17,4 L6,4 C4.79111111,4 4,4.7 4,6 L4,18 C4,19.3 4.79111111,20 6,20 L18,20 C19.2,20 20,19.3 20,18 L20,7.20710678 C20,7.07449854 19.9473216,6.94732158 19.8535534,6.85355339 L17,4 Z M17,11 L7,11 L7,4 L17,4 L17,11 Z" fill="#000000" fill-rule="nonzero"/>
                                                            <rect fill="#000000" opacity="0.3" x="12" y="4" width="3" height="5" rx="0.5"/>
                                                        </g>
                                                    </svg>
                                                </span>
                                                <span id="btn_text_create_label">Save</span>
                                                <span id="spinner_create_label" class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;"></span>        
                                            </button>
                                            <button class="btn btn-warning btn-sm text-sm" id="reset_memo" onclick="resetMemo()">
                                                <span id="svg_create_label" class="svg-icon svg-icon-2" style="margin-left: -7px;">
                                                       <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"> 
                                                            <defs/>
                                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                <polygon points="0 0 24 0 24 24 0 24"/>
                                                                <path d="M5.85714286,2 L13.7364114,2 C14.0910962,2 14.4343066,2.12568431 14.7051108,2.35473959 L19.4686994,6.3839416 C19.8056532,6.66894833 20,7.08787823 20,7.52920201 L20,20.0833333 C20,21.8738751 19.9795521,22 18.1428571,22 L5.85714286,22 C4.02044787,22 4,21.8738751 4,20.0833333 L4,3.91666667 C4,2.12612489 4.02044787,2 5.85714286,2 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                                                <path d="M10.5857864,13 L9.17157288,11.5857864 C8.78104858,11.1952621 8.78104858,10.5620972 9.17157288,10.1715729 C9.56209717,9.78104858 10.1952621,9.78104858 10.5857864,10.1715729 L12,11.5857864 L13.4142136,10.1715729 C13.8047379,9.78104858 14.4379028,9.78104858 14.8284271,10.1715729 C15.2189514,10.5620972 15.2189514,11.1952621 14.8284271,11.5857864 L13.4142136,13 L14.8284271,14.4142136 C15.2189514,14.8047379 15.2189514,15.4379028 14.8284271,15.8284271 C14.4379028,16.2189514 13.8047379,16.2189514 13.4142136,15.8284271 L12,14.4142136 L10.5857864,15.8284271 C10.1952621,16.2189514 9.56209717,16.2189514 9.17157288,15.8284271 C8.78104858,15.4379028 8.78104858,14.8047379 9.17157288,14.4142136 L10.5857864,13 Z" fill="#000000"/>
                                                            </g>
                                                        </svg>
                                                </span>
                                                <span id="btn_text_create_label">Roll Back</span>
                                                <span id="spinner_create_label" class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;"></span>        
                                            </button>
                                            <button class="btn btn-danger btn-sm text-sm" id="delete_memo" onclick="deleteMemo()">
                                                <span id="svg_create_label" class="svg-icon svg-icon-2" style="margin-left: -7px;">
                                                       <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"> 
                                                            <defs/>
                                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                <polygon points="0 0 24 0 24 24 0 24"/>
                                                                <path d="M5.85714286,2 L13.7364114,2 C14.0910962,2 14.4343066,2.12568431 14.7051108,2.35473959 L19.4686994,6.3839416 C19.8056532,6.66894833 20,7.08787823 20,7.52920201 L20,20.0833333 C20,21.8738751 19.9795521,22 18.1428571,22 L5.85714286,22 C4.02044787,22 4,21.8738751 4,20.0833333 L4,3.91666667 C4,2.12612489 4.02044787,2 5.85714286,2 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                                                <path d="M10.5857864,13 L9.17157288,11.5857864 C8.78104858,11.1952621 8.78104858,10.5620972 9.17157288,10.1715729 C9.56209717,9.78104858 10.1952621,9.78104858 10.5857864,10.1715729 L12,11.5857864 L13.4142136,10.1715729 C13.8047379,9.78104858 14.4379028,9.78104858 14.8284271,10.1715729 C15.2189514,10.5620972 15.2189514,11.1952621 14.8284271,11.5857864 L13.4142136,13 L14.8284271,14.4142136 C15.2189514,14.8047379 15.2189514,15.4379028 14.8284271,15.8284271 C14.4379028,16.2189514 13.8047379,16.2189514 13.4142136,15.8284271 L12,14.4142136 L10.5857864,15.8284271 C10.1952621,16.2189514 9.56209717,16.2189514 9.17157288,15.8284271 C8.78104858,15.4379028 8.78104858,14.8047379 9.17157288,14.4142136 L10.5857864,13 Z" fill="#000000"/>
                                                            </g>
                                                        </svg>
                                                </span>
                                                <span id="btn_text_create_label">Delete</span>
                                                <span id="spinner_create_label" class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;"></span>        
                                            </button>
                                        </div>

                                    </div>  

                                    <div class="card-body">
                                        <div class="row" id="form"> 
                                            <div class="col-md-6 mb-5">
                                                <form>
                                                    <div> 
                                                        <div class="form-group mb-3">
                                                            <label class="mb-2 text-sm">Part Class <span class="text-danger">*</span></label> 
                                                            <input type="hidden" class="form-control bg-light-primary" id="IdMemo" value="3"> 
                                                            <input type="hidden" class="form-control bg-light-primary" id="MemoDate" value="<?php date_default_timezone_set('Asia/Jakarta'); echo date('Y-m-d');  ?>"/> 
                                                            <select class="form-select form-select-solid" data-kt-select2="true" data-allow-clear="false" id="PartClass" data-hide-search="true"> 
                                                                <option value=''>&nbsp;</option> 
                                                                <option value='RM'>RM</option> 
                                                                <option value='SFG1'>SFG1</option> 
                                                                <option value='SFG2'>SFG2</option> 
                                                                <option value='FG'>FG</option> 
                                                                <!-- <option value='STP' selected>Stamping</option>  -->
                                                            </select> 
                                                        </div>  
                                                        <div class="form-group mb-3"> 
                                                            <label class="mb-2 text-sm">Part <span class="text-danger">*</span></label> 
                                                            <select class="form-select form-select-solid" data-kt-select2="true" data-placeholder="&nbsp;" data-allow-clear="false" id="PartNum" data-hide-search="false"/>  
                                                            </select> 
                                                        </div>  
                                                        <div class="form-group mb-3">
                                                            <label class="mb-2 text-sm">Qty <span class="text-danger">*</span></label>
                                                            <input type="number" class="form-control bg-light-primary" id="QtyRequest" value="1"/> 
                                                        </div>
                                                        <div class="form-group mb-3">
                                                            <label class="mb-2 text-sm">Remark <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" id="Keterangan"/> 
                                                        </div>
                                                        <div class="form-group mb-3"> 
                                                            <div class="d-flex">
                                                            <div class="col-4 form-group pl-2"> 
                                                                <label class="mb-2 text-sm">Warehouse <span class="text-danger">*</span></label> 
                                                                <select class="form-select form-select-solid" data-kt-select2="true" data-placeholder="&nbsp;" data-allow-clear="true" id="WarehouseCode" data-hide-search="false"/>  
                                                                </select> 
                                                            </div>    
                                                            <div class="col-4 form-group pl-2" style="padding-left: 5px;"> 
                                                                <label class="mb-2 text-sm">Bin <span class="text-danger">*</span></label> 
                                                                <select class="form-select form-select-solid" data-kt-select2="true" data-placeholder="&nbsp;" data-allow-clear="true" id="BinNum" data-hide-search="false"/>  
                                                                </select> 
                                                            </div>    
                                                            <div class="col-4 form-group pl-2" style="padding-left: 5px;"> 
                                                                <label class="mb-2 text-sm">Lot Number <span class="text-danger">*</span></label> 
                                                                <select class="form-select form-select-solid" data-kt-select2="true" data-placeholder="&nbsp;" data-allow-clear="true" id="LotNum" data-hide-search="false"/>  
                                                                </select> 
                                                            </div>  
                                                            </div>  
                                                        </div>  
                                                    </div> 
                                                </form> 
                                            </div>

                                            <div class="col-md-6">
                                                <form>
                                                    <div>  
                                                        <div class="form-group mb-3"> 
                                                            <div class="d-flex">
                                                                <div class="col-8 form-group pl-2"> 
                                                                    <label class="mb-2 text-sm">Dept.Head Pengaju <span class="text-danger">*</span></label> 
                                                                    <select class="form-select form-select-solid Approval" data-kt-select2="true" data-placeholder="&nbsp;" data-allow-clear="true" id="Approval1" data-hide-search="false"/>  
                                                                    </select> 
                                                                </div>    
                                                                <div class="col-4 form-group pl-2" style="padding-left: 5px;"> 
                                                                    <label class="mb-2 text-sm">Approval <span class="text-danger">*</span></label> 
                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid">
                                                                        <input class="form-check-input approval-checkbox" type="checkbox" id="Approval1Status">
                                                                        <span class="form-check-label approval-time" id="Approval1Time"></span>
                                                                    </label> 
                                                                </div>    
                                                            </div>
                                                        </div>  
                                                        <div class="form-group mb-3"> 
                                                            <div class="d-flex">
                                                                <div class="col-8 form-group pl-2"> 
                                                                    <label class="mb-2 text-sm">PIC Warehouse <span class="text-danger">*</span></label> 
                                                                    <select class="form-select form-select-solid Approval" data-kt-select2="true" data-placeholder="&nbsp;" data-allow-clear="true" id="Approval2" data-hide-search="false"/>  
                                                                    </select> 
                                                                </div>    
                                                                <div class="col-4 form-group pl-2" style="padding-left: 5px;"> 
                                                                    <label class="mb-2 text-sm">Approval <span class="text-danger">*</span></label> 
                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid">
                                                                        <input class="form-check-input approval-checkbox" type="checkbox" id="Approval2Status">
                                                                        <span class="form-check-label approval-time" id="Approval2Time"></span>
                                                                    </label> 
                                                                </div>    
                                                            </div>
                                                        </div>  
                                                        <div class="form-group mb-3"> 
                                                            <div class="d-flex">
                                                                <div class="col-8 form-group pl-2"> 
                                                                    <label class="mb-2 text-sm">Dept.Head PPIC <span class="text-danger">*</span></label> 
                                                                    <select class="form-select form-select-solid Approval" data-kt-select2="true" data-placeholder="&nbsp;" data-allow-clear="true" id="Approval3" data-hide-search="false"/>  
                                                                    </select> 
                                                                </div>    
                                                                <div class="col-4 form-group pl-2" style="padding-left: 5px;"> 
                                                                    <label class="mb-2 text-sm">Approval <span class="text-danger">*</span></label> 
                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid">
                                                                        <input class="form-check-input approval-checkbox" type="checkbox" id="Approval3Status">
                                                                        <span class="form-check-label approval-time" id="Approval3Time"></span>
                                                                    </label> 
                                                                </div>    
                                                            </div>
                                                        </div>  
                                                        <div class="form-group mb-3"> 
                                                            <div class="d-flex">
                                                                <div class="col-8 form-group pl-2"> 
                                                                    <label class="mb-2 text-sm">Administrator <span class="text-danger">*</span></label> 
                                                                    <select class="form-select form-select-solid Approval" data-kt-select2="true" data-placeholder="&nbsp;" data-allow-clear="true" id="Approval4" data-hide-search="false"/>  
                                                                    </select> 
                                                                </div>    
                                                                <div class="col-4 form-group pl-2" style="padding-left: 5px;"> 
                                                                    <label class="mb-2 text-sm">Approval <span class="text-danger">*</span></label> 
                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid">
                                                                        <input class="form-check-input approval-checkbox" type="checkbox" id="Approval4Status">
                                                                        <span class="form-check-label approval-time" id="Approval4Time"></span>
                                                                    </label> 
                                                                </div>    
                                                            </div>
                                                        </div>  
                                                        <div class="form-group mb-3"> 
                                                            <div class="d-flex">
                                                                <div class="col-8 form-group pl-2"> 
                                                                    <label class="mb-2 text-sm">Leader Warehouse <span class="text-danger">*</span></label> 
                                                                    <select class="form-select form-select-solid Approval" data-kt-select2="true" data-placeholder="&nbsp;" data-allow-clear="true" id="Approval5" data-hide-search="false"/>  
                                                                    </select> 
                                                                </div>    
                                                                <div class="col-4 form-group pl-2" style="padding-left: 5px;"> 
                                                                    <label class="mb-2 text-sm">Approval <span class="text-danger">*</span></label> 
                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid">
                                                                        <input class="form-check-input approval-checkbox" type="checkbox" id="Approval5Status">
                                                                        <span class="form-check-label approval-time" id="Approval5Time"></span>
                                                                    </label> 
                                                                </div>    
                                                            </div>
                                                        </div>  
                                                    </div> 
                                                </form> 
                                            </div>

                                        </div> 
                                    </div>
                                </div>
                                <hr style="color: gray">
                            </div> 
                        </div>
                    </div>
                </div>
            </div>   
            
        </div>    

    <script>
        $(document).ready(function () {
            front_table() ;  
        })

        function front_table() { 
            var frontTable = $("#kt_doc_table").DataTable({
                processing: true,
                serverSide: true,
                responsive: false, 
                deferLoading: 57,
                language: { 
                    'processing': '<div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>'
                },     
                info: false,
                order: [],
                columnDefs: [
                    {
                        orderable: false,
                        targets: 0
                    }
                ],
                ajax: {
                    url: "{{ route('memo_misc_issue.front_table') }}",
                    type: 'POST',
                    data: function (d) { 
                        d._token = $("[name=_token]").val();
                        d.front_table_search = $("#front_table_search").val();
                    }, 
                    cache: false,
                    dataType: 'json'
                },
                columns: [
                    { data: 'no', className: 'text-center' },
                    { data: 'memo_date' },
                    { data: 'part_class' },
                    { data: 'part_num' },
                    { data: 'part_name' },
                    { data: 'qty_request' },
                    { data: 'warehouse_description' },
                    { data: 'bin_num' },
                    { data: 'lot_num' },
                    { data: 'remark' },
                    // { data: 'id' },
                    { data: 'approval_seq' },
                    {
                        data: 'action', 
                        className: 'text-center',
                        orderable: false
                    }, 
                ],
                initComplete: function(settings, json) {
                    var button = document.getElementById('btn_submit_search');
                    var svg = document.getElementById('svg_submit_search');
                    var spinner = document.getElementById('spinner_submit_search');
                    var buttonText = document.getElementById('btn_text_submit_search'); 
                    setTimeout(function(){
                        svg.style.display = 'inline-block';
                        spinner.style.display = 'none';
                        buttonText.textContent = 'Submit'; 
                        button.disabled = false; 
                    },500)
                }
            });

            setTimeout(function(){ 
                frontTable.ajax.reload();   
            },500) 

            return true;
        }

        function submit_search(){ 
            var button = document.getElementById('btn_submit_search');
            var svg = document.getElementById('svg_submit_search');
            var spinner = document.getElementById('spinner_submit_search');
            var buttonText = document.getElementById('btn_text_submit_search'); 
            svg.style.display = 'none';
            spinner.style.display = 'inline-block';
            buttonText.textContent = 'Please Wait...'; 
            button.disabled = true;  
            refresh_front_table();
        }; 

        $("#front_table_search").keyup(function(event){
                if(event.keyCode == 13){ refresh_front_table();  } 
        }); 

        function refresh_front_table() {  
            if ($.fn.DataTable.isDataTable('#kt_doc_table')) {
                $('#kt_doc_table').DataTable().destroy();
            }  
            front_table();
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
                    url: "{{ route('production_schedule.detail_table') }}",
                    type: 'POST',
                    data	: function ( d ) { d._token = $("[name=_token]").val(), d.trc_unix_id = $("#temp_id").val(), d.detail_table_search = $("#detail_table_search").val(); }, 
                    cache	: false,
                    dataType : 'json'
                },
                columns: [
                    { data: 'no', className: 'text-center' },  
                    { data: 'item_no' }, 
                    { data: 'created_by' },
                    { data: 'plan' },
                    {
                        data: 'action', 
                        className: 'text-center',
                        orderable: false
                    }
                ]
            })

            setTimeout(function(){ 
                detailTable.ajax.reload();
            },500) 
        }

        $("#detail_table_search").keyup(function(event){
            if(event.keyCode == 13){ refresh_detail_table(); } 
        });

        function refresh_detail_table() { 
            if ($.fn.DataTable.isDataTable('#kt_doc_table_2')) {
                $('#kt_doc_table_2').DataTable().destroy();
            } 
            detail_table();
        }

    </script>

    <script>  
        function resetMemo() {  
            if (!confirm('Apakah Anda yakin ingin mereset memo ini?')) {
                return; // Jika user klik cancel, batalkan eksekusi
            }            
            var token = $("[name=_token]").val(); 
            var id_memo = $("#IdMemo").val(); 

            var button = document.getElementById('btn_create_label');
            var svg = document.getElementById('svg_create_label');
            var spinner = document.getElementById('spinner_create_label');
            var buttonText = document.getElementById('btn_text_create_label'); 
            
            svg.style.display = 'none';
            spinner.style.display = 'inline-block';
            buttonText.textContent = 'Please Wait...'; 
            button.disabled = true; 
            
            // PERBAIKI: Gunakan object, bukan string concatenation
            var formData = {
                _token: token,
                id_memo: id_memo,
            };
            
            $.ajax({
                type: 'POST',
                url: "{{ route('memo_misc_issue.reset_memo') }}",
                data: formData,
                cache: false,
                dataType: 'json',
                success: function(data){     
                    // Reset button state
                    setTimeout(function(){
                        svg.style.display = 'inline-block';
                        spinner.style.display = 'none';
                        buttonText.textContent = 'Save'; 
                        button.disabled = false;  
                    }, 300);

                    // Handle response
                    if(data.process_status == 500){     
                        Toast.fire({
                            position: 'top-end',
                            title: data.msg_process,
                            icon: "error"
                        });
                    } else {     
                        Toast.fire({
                            position: 'top-end',
                            title: data.msg_process,
                            icon: "success"
                        });
                        refresh_detail_table();
                        $("#kt_modal_form").modal('hide'); 
                    }
                    location.reload();
                },
                error: function(xhr, status, error) {
                    // Handle AJAX error
                    setTimeout(function(){
                        svg.style.display = 'inline-block';
                        spinner.style.display = 'none';
                        buttonText.textContent = 'Save'; 
                        button.disabled = false;  
                    }, 300);
                    
                    Toast.fire({
                        position: 'top-end',
                        title: 'Terjadi kesalahan: ' + error,
                        icon: "error"
                    });
                    
                    console.error('AJAX Error:', error);
                    console.error('Status:', status);
                    console.error('Response:', xhr.responseText);
                }
            }); 
        }
        function deleteMemo() {  
            if (!confirm('Apakah Anda yakin ingin mereset memo ini?')) {
                return; // Jika user klik cancel, batalkan eksekusi
            }            
            var token = $("[name=_token]").val(); 
            var id_memo = $("#IdMemo").val(); 

            var button = document.getElementById('btn_create_label');
            var svg = document.getElementById('svg_create_label');
            var spinner = document.getElementById('spinner_create_label');
            var buttonText = document.getElementById('btn_text_create_label'); 
            
            svg.style.display = 'none';
            spinner.style.display = 'inline-block';
            buttonText.textContent = 'Please Wait...'; 
            button.disabled = true; 
            
            // PERBAIKI: Gunakan object, bukan string concatenation
            var formData = {
                _token: token,
                id_memo: id_memo,
            };
            
            $.ajax({
                type: 'POST',
                url: "{{ route('memo_misc_issue.delete_memo') }}",
                data: formData,
                cache: false,
                dataType: 'json',
                success: function(data){     
                    // Reset button state
                    setTimeout(function(){
                        svg.style.display = 'inline-block';
                        spinner.style.display = 'none';
                        buttonText.textContent = 'Save'; 
                        button.disabled = false;  
                    }, 300);

                    // Handle response
                    if(data.process_status == 500){     
                        Toast.fire({
                            position: 'top-end',
                            title: data.msg_process,
                            icon: "error"
                        });
                    } else {     
                        Toast.fire({
                            position: 'top-end',
                            title: data.msg_process,
                            icon: "success"
                        });
                        refresh_detail_table();
                        $("#kt_modal_form").modal('hide'); 
                    }
                    location.reload();
                },
                error: function(xhr, status, error) {
                    // Handle AJAX error
                    setTimeout(function(){
                        svg.style.display = 'inline-block';
                        spinner.style.display = 'none';
                        buttonText.textContent = 'Save'; 
                        button.disabled = false;  
                    }, 300);
                    
                    Toast.fire({
                        position: 'top-end',
                        title: 'Terjadi kesalahan: ' + error,
                        icon: "error"
                    });
                    
                    console.error('AJAX Error:', error);
                    console.error('Status:', status);
                    console.error('Response:', xhr.responseText);
                }
            }); 
        }
    
        function saveUpdateMemo() {  
            var token = $("[name=_token]").val(); 
            var id_memo = $("#IdMemo").val(); 
            var memo_date = $("#MemoDate").val(); 
            var part_class = $("#PartClass").val(); 
            var part_num = $("#PartNum").val(); 
            var qty_request = $("#QtyRequest").val(); 
            var keterangan = $("#Keterangan").val(); 
            var warehouse_code = $("#WarehouseCode").val(); 
            var bin_num = $("#BinNum").val(); 
            var lot_num = $("#LotNum").val(); 
            var approval1 = $("#Approval1").val(); 
            var approval2 = $("#Approval2").val(); 
            var approval3 = $("#Approval3").val(); 
            var approval4 = $("#Approval4").val();
            var approval5 = $("#Approval5").val();
            var approval1status = $("#Approval1Status").is(":checked") ? 1 : 0; 
            var approval2status = $("#Approval2Status").is(":checked") ? 1 : 0; 
            var approval3status = $("#Approval3Status").is(":checked") ? 1 : 0; 
            var approval4status = $("#Approval4Status").is(":checked") ? 1 : 0; 
            var approval5status = $("#Approval5Status").is(":checked") ? 1 : 0; 

            var button = document.getElementById('btn_create_label');
            var svg = document.getElementById('svg_create_label');
            var spinner = document.getElementById('spinner_create_label');
            var buttonText = document.getElementById('btn_text_create_label'); 
            
            svg.style.display = 'none';
            spinner.style.display = 'inline-block';
            buttonText.textContent = 'Please Wait...'; 
            button.disabled = true; 
            
            // PERBAIKI: Gunakan object, bukan string concatenation
            var formData = {
                _token: token,
                id_memo: id_memo,
                memo_date: memo_date,
                part_class: part_class,
                part_num: part_num,
                qty_request: qty_request,
                warehouse_code: warehouse_code,
                bin_num: bin_num,
                lot_num: lot_num,
                keterangan: keterangan,
                approval1: approval1,
                approval2: approval2,
                approval3: approval3,
                approval4: approval4,
                approval5: approval5,
                approval1status: approval1status,
                approval2status: approval2status,
                approval3status: approval3status,
                approval4status: approval4status,
                approval5status: approval5status
            };
            
            $.ajax({
                type: 'POST',
                url: "{{ route('memo_misc_issue.save_memo') }}",
                data: formData,
                cache: false,
                dataType: 'json',
                success: function(data){     
                    // Reset button state
                    setTimeout(function(){
                        svg.style.display = 'inline-block';
                        spinner.style.display = 'none';
                        buttonText.textContent = 'Save'; 
                        button.disabled = false;  
                    }, 300);

                    // Handle response
                    if(data.process_status == 500){     
                        Toast.fire({
                            position: 'top-end',
                            title: data.msg_process,
                            icon: "error"
                        });
                    } else {     
                        Toast.fire({
                            position: 'top-end',
                            title: data.msg_process,
                            icon: "success"
                        });
                        refresh_detail_table();
                        $("#kt_modal_form").modal('hide'); 
                    }
                    location.reload();
                },
                error: function(xhr, status, error) {
                    // Handle AJAX error
                    setTimeout(function(){
                        svg.style.display = 'inline-block';
                        spinner.style.display = 'none';
                        buttonText.textContent = 'Save'; 
                        button.disabled = false;  
                    }, 300);
                    
                    Toast.fire({
                        position: 'top-end',
                        title: 'Terjadi kesalahan: ' + error,
                        icon: "error"
                    });
                    
                    console.error('AJAX Error:', error);
                    console.error('Status:', status);
                    console.error('Response:', xhr.responseText);
                }
            }); 
        }
        $(function(){  
            $('#PartNum').change(function(){
                var newOption = new Option('Select option', '0', true, true);     
                $('#PartBin').append(newOption).trigger('change'); 
            })
            $('#PartNum').select2({
                ajax: {
                    type: 'POST',
                    url: "{{ route('memo_misc_issue.get_part_number') }}",
                    dataType: 'json',
                    delay: 250, // delay for search
                    data: function(params) {
                        var query = {
                            search: params.term,  
                            category_id: $("#PartClass").val(),
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
            $('#PartClass').change(function(){
                var newOption = new Option('Select option', '0', true, true);     
                $('#PartNum').append(newOption).trigger('change'); 
            })
            $('#WarehouseCode').select2({
                ajax: {
                    type: 'POST',
                    url: "{{ route('memo_misc_issue.get_warehouse') }}",
                    dataType: 'json',
                    delay: 250, 
                    data: function(params) {
                        var query = {
                            search: params.term,  
                            line: $("#PartNum").val(),
                            qty: $("#QtyRequest").val(),
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
            $('#BinNum').select2({
                ajax: {
                    type: 'POST',
                    url: "{{ route('memo_misc_issue.get_bin') }}",
                    dataType: 'json',
                    delay: 250, 
                    data: function(params) {
                        var query = {
                            search: params.term,  
                            line: $("#PartNum").val(),
                            warehouse: $("#WarehouseCode").val(),
                            qty: $("#QtyRequest").val(),
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
            $('#LotNum').select2({
                ajax: {
                    type: 'POST',
                    url: "{{ route('memo_misc_issue.get_part_bin') }}",
                    dataType: 'json',
                    delay: 250, 
                    data: function(params) {
                        var query = {
                            search: params.term,  
                            line: $("#PartNum").val(),
                            warehouse: $("#WarehouseCode").val(),
                            bin: $("#BinNum").val(),
                            qty: $("#QtyRequest").val(),
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
            $('.Approval').select2({
                ajax: {
                    type: 'POST',
                    url: "{{ route('memo_misc_issue.get_approval') }}",
                    dataType: 'json',
                    delay: 250, 
                    data: function(params) {
                        var query = {
                            search: params.term,  
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
            // $('#Approval1').select2({
            //     ajax: {
            //         type: 'POST',
            //         url: "{{ route('memo_misc_issue.get_approval') }}",
            //         dataType: 'json',
            //         delay: 250, 
            //         data: function(params) {
            //             var query = {
            //                 search: params.term, 
            //                 id_memo: $("#IdMemo").val(),
            //                 sequance:'1',
            //                 _token: $("[name=_token]").val(),
            //                 page: params.page || 1  
            //             };
            //             return query;
            //         },
            //         processResults: function(data, params) {
            //             params.page = params.page || 1; 
            //             return {
            //                 results: $.map(data.items, function(item) {  
            //                     return {
            //                         id: item.id,
            //                         text: item.name  
            //                     };
            //                 }),
            //                 pagination: {
            //                     more: data.pagination.more 
            //                 }
            //             };
            //         },
            //         cache: true
            //     },
            //     placeholder: 'Select option', 
            // });

        });
        function detail(element){
            // Aktifkan tab preview
            document.getElementById('kt_activity_preview_tab').click();  
            $(".tab_preview").removeClass('active'); 
            $("#kt_activity_file").addClass('show active');
            $("#kt_activity_file_tab").addClass('active'); 
            
            // Ambil data dari button
            var $el = $(element);
            
            // Set values ke form
            $("#IdMemo").val($el.data('id_memo'));
            $("#MemoDate").val($el.data('memo_date'));
            $("#MemoDate").val($el.data('memo_date') == '' ? new Date().toISOString().split('T')[0] : $el.data('memo_date'));
            $("#PartClass").val($el.data('part_class')).trigger('change');
            
            // Untuk PartNum (Select2 dengan AJAX), kita perlu cara khusus
            var partNum = $el.data('part_num');
            var partName = $el.data('part_name'); // Anda perlu tambahkan data-part_name di controller
            
            if (partNum) {
                // Buat option baru dan set ke Select2
                var newOption = new Option(partNum + ' - ' + partName, partNum, true, true);
                $('#PartNum').append(newOption).trigger('change');
            }
            
            $("#QtyRequest").val($el.data('qty_request'));
            $("#Keterangan").val($el.data('keterangan'));

            var WarehouseCode = $el.data('warehouse_code');
            var Description = $el.data('warehouse_description'); // Anda perlu tambahkan data-part_name di controller
            if (WarehouseCode) {
                var newOption1 = new Option(Description, WarehouseCode, true, true);
                $('#WarehouseCode').append(newOption1).trigger('change');
            }

            var BinNum = $el.data('bin_num');
            if (BinNum) {
                // Buat option baru dan set ke Select2
                var newOption2 = new Option(BinNum, BinNum, true, true);
                $('#BinNum').append(newOption2).trigger('change');
            }

            var LotNum = $el.data('lot_num');
            if (LotNum) {
                // Buat option baru dan set ke Select2
                var newOption3 = new Option(LotNum, LotNum, true, true);
                $('#LotNum').append(newOption3).trigger('change');
            }

            // FETCH DATA APPROVAL BERDASARKAN id_memo
            if ($el.data('id_memo')) {
                fetchApprovalData($el.data('id_memo'));
            }

            
            // Debug
            console.log('PartNum:', partNum);
            console.log('PartName:', partName);
        }
        // Function untuk fetch data approval
        function fetchApprovalData(idMemo) {
            $.ajax({
                url: "{{ route('memo_misc_issue.get_approval_by_memo') }}",
                type: 'POST',
                data: {
                    id_memo: idMemo,
                    _token: $("[name=_token]").val()
                },
                success: function(response) {
                    console.log('Approval Response:', response);
                    
                    if (response.success && response.data) {
                        var approvalData = response.data;
                        
                        // Set nilai untuk setiap approval (1-5)
                        for (let i = 1; i <= 5; i++) {
                            // Set Select2 value
                            if (approvalData[`Approval${i}_id`] && approvalData[`Approval${i}_name`]) {
                                setSelect2Value(`#Approval${i}`, approvalData[`Approval${i}_id`], approvalData[`Approval${i}_name`]);
                            }
                            
                            // REVISI: Panggil function baru untuk set status
                            setApprovalStatus(i, approvalData[`Approval${i}_status`], approvalData[`Approval${i}_updated_at`]);

                            if(approvalData[`next_approval`]==i && approvalData['nik'] == approvalData[`Approval${i}_id`]){
                                $(`#Approval${i}Status`).prop('disabled', false);
                            } else {
                                $(`#Approval${i}Status`).prop('disabled', true);
                            }
                            if(approvalData[`next_approval`]>i){
                                $(`#Approval${i}`).prop('disabled', true);
                            } else {
                                $(`#Approval${i}`).prop('disabled', false);
                            }
                            if(approvalData[`next_approval`]>1){
                                $(`#PartClass`).prop('disabled', true);
                                $(`#PartNum`).prop('disabled', true);
                                $(`#QtyRequest`).prop('disabled', true);
                                $(`#Keterangan`).prop('disabled', true);
                                $(`#delete_memo`).prop('disabled', true);
                            }else{
                                $(`#PartClass`).prop('disabled', false);
                                $(`#PartNum`).prop('disabled', false);
                                $(`#QtyRequest`).prop('disabled', false);
                                $(`#Keterangan`).prop('disabled', false);
                                $(`#delete_memo`).prop('disabled', false);
                            }
                            if(approvalData[`next_approval`]==6){
                                $(`#reset_memo`).prop('disabled', true);
                            }else{
                                $(`#reset_memo`).prop('disabled', false);
                            }
                            if(approvalData[`next_approval`]<=1||approvalData[`next_approval`]==6||approvalData[`next_approval`]==''){
                                $(`#WarehouseCode`).prop('disabled', false);
                                $(`#BinNum`).prop('disabled', false);
                                $(`#LotNum`).prop('disabled', false);
                            }else{
                                $(`#WarehouseCode`).prop('disabled', true);
                                $(`#BinNum`).prop('disabled', true);
                                $(`#LotNum`).prop('disabled', true);
                            }

                        }
                        
                    } else {
                        console.warn('Tidak ada data approval ditemukan');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching approval data:', error);
                }
            });
        }

        // Function helper untuk set Select2 value
        function setSelect2Value(selector, id, text) {
            console.log('Setting Select2:', selector, id, text); // Debug
            
            // Clear existing selection
            $(selector).val(null).trigger('change');
            
            // Create new option and set as selected
            if (id && text) {
                var newOption = new Option(text, id, true, true);
                $(selector).append(newOption).trigger('change');
                
                // Trigger change event lagi untuk memastikan
                $(selector).trigger('change');
                
                console.log('Select2 berhasil di-set'); // Debug
            }
        }

        function setApprovalStatus(approvalNumber, status, updatedAt) {
            const checkbox = $(`#Approval${approvalNumber}Status`);
            const timeLabel = $(`#Approval${approvalNumber}Time`);
            
            if (status == 'approved' || status == 1) {
                checkbox.prop('checked', true);
                if (updatedAt) {
                    const date = new Date(updatedAt);
                    timeLabel.text(date.toLocaleDateString('id-ID') + ' ' + 
                                date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }));
                } else {
                    timeLabel.text('Approved');
                }
                timeLabel.css('color', 'green');
            } else {
                checkbox.prop('checked', false);
                timeLabel.text('');
                timeLabel.css('color', 'orange');
            }
        }

        function backHome() { 
            var button = document.getElementById('btn_back_home');
            var svg = document.getElementById('svg_back_home');
            var spinner = document.getElementById('spinner_back_home');
            var buttonText = document.getElementById('btn_text_back_home'); 
            svg.style.display = 'none';
            spinner.style.display = 'inline-block';
            buttonText.textContent = 'Please Wait...'; 
            button.disabled = true;  
            $("#temp_id").val(''); 
            refresh_front_table(); 
            setTimeout(function(){
                svg.style.display = 'inline-block';
                spinner.style.display = 'none';
                buttonText.textContent = 'Back'; 
                button.disabled = false; 
                document.getElementById('kt_activity_home_tab').click(); 
            }, 300) 
            location.reload();
        }  
    </script>
    
@endsection