@extends('layouts.app')  
@section('subhead')
    <title>{{ $head_title }}</title>  
    <script src="{{ asset('public/assets/js/jquery/jquery.min.js') }}"></script> 
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                dateFormat: "d/m/Y",
            });  
            $(".number_format_coma").priceFormat({
                prefix: '',
                centsSeparator: '.',
                thousandsSeparator: ',',
                allowNegative: true,
                centsLimit: 0  
            }) 
        });
        </script>
        <style> 
            .blink_me {
              animation: blinker 1s linear infinite;
            }
            
            @keyframes blinker {
              50% {
                opacity: 0.7;
              }
            }  
            
            .on-editing {
                color: red;
            } 
        </style> 

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
                        <div class="tab-pane fade active show fs-7" id="kt_tab_pane_2" role="tabpanel"> 
                            <div class="card col-xxl-12">   
                                <div class="card-header py-6 mb-6"> 
                                    <div class="card-title"> 
                                        <div class="d-flex align-items-center position-relative my-1">
                                            <h2 class="fw-bolder text-dark">Properties</h2>  
                                        </div>
                                    </div>
                                    <div class="card-toolbar"> 
                                        <div class="d-flex justify-content-end" data-kt-goodreceive-table-toolbar="base">   
                                            <a href="/del_confirm" type="button" class="btn me-2 btn-xs btn-outline btn-outline-dashed btn-outline-success btn-active-light-success text-muted">
                                                <span class="svg-icon svg-icon-muted svg-icon-2x"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path d="M14.6 4L6.6 12L14.6 20H10.6L3.3 12.7C2.9 12.3 2.9 11.7 3.3 11.3L10.6 4H14.6Z" fill="black"/>
                                                    <path opacity="0.3" d="M21.6 4L13.6 12L21.6 20H17.6L10.3 12.7C9.9 12.3 9.9 11.7 10.3 11.3L17.6 4H21.6Z" fill="black"/>
                                                    </svg>
                                                </span>
                                                Back
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body pt-0"> 
                                    <div class="w-100">  
                                        <form id="header_form"> 
                                        <div class="row g-0"> 
                                            <input type="hidden" id="ref_doc" name="ref_doc" value="{{ $ref_doc }}" readonly>
                                            <input type="hidden" id="ref_doc_po" name="ref_doc_po" value="{{ $ref_doc_po }}" readonly>

                                            <div class="col-md-6 mb-10 ps-5"> 
                                                    <div class="fv-row mb-5">
                                                        <label class="form-label">Doc. Number</label>
                                                        <input name="input_docnum" id="input_docnum" class="form-control form-control-solid" value="{{ $docnum }}" readonly/>
                                                    </div>  

                                                    <div class="fv-row mb-5">
                                                        <label class="form-label">Doc. Date</label>
                                                        <div class="position-relative d-flex align-items-center">
                                                            <span class="svg-icon position-absolute ms-4 mb-1 svg-icon-2">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                                    <path opacity="0.3" d="M21 22H3C2.4 22 2 21.6 2 21V5C2 4.4 2.4 4 3 4H21C21.6 4 22 4.4 22 5V21C22 21.6 21.6 22 21 22Z" fill="black"></path>
                                                                    <path d="M6 6C5.4 6 5 5.6 5 5V3C5 2.4 5.4 2 6 2C6.6 2 7 2.4 7 3V5C7 5.6 6.6 6 6 6ZM11 5V3C11 2.4 10.6 2 10 2C9.4 2 9 2.4 9 3V5C9 5.6 9.4 6 10 6C10.6 6 11 5.6 11 5ZM15 5V3C15 2.4 14.6 2 14 2C13.4 2 13 2.4 13 3V5C13 5.6 13.4 6 14 6C14.6 6 15 5.6 15 5ZM19 5V3C19 2.4 18.6 2 18 2C17.4 2 17 2.4 17 3V5C17 5.6 17.4 6 18 6C18.6 6 19 5.6 19 5Z" fill="black"></path>
                                                                    <path d="M8.8 13.1C9.2 13.1 9.5 13 9.7 12.8C9.9 12.6 10.1 12.3 10.1 11.9C10.1 11.6 10 11.3 9.8 11.1C9.6 10.9 9.3 10.8 9 10.8C8.8 10.8 8.59999 10.8 8.39999 10.9C8.19999 11 8.1 11.1 8 11.2C7.9 11.3 7.8 11.4 7.7 11.6C7.6 11.8 7.5 11.9 7.5 12.1C7.5 12.2 7.4 12.2 7.3 12.3C7.2 12.4 7.09999 12.4 6.89999 12.4C6.69999 12.4 6.6 12.3 6.5 12.2C6.4 12.1 6.3 11.9 6.3 11.7C6.3 11.5 6.4 11.3 6.5 11.1C6.6 10.9 6.8 10.7 7 10.5C7.2 10.3 7.49999 10.1 7.89999 10C8.29999 9.90003 8.60001 9.80003 9.10001 9.80003C9.50001 9.80003 9.80001 9.90003 10.1 10C10.4 10.1 10.7 10.3 10.9 10.4C11.1 10.5 11.3 10.8 11.4 11.1C11.5 11.4 11.6 11.6 11.6 11.9C11.6 12.3 11.5 12.6 11.3 12.9C11.1 13.2 10.9 13.5 10.6 13.7C10.9 13.9 11.2 14.1 11.4 14.3C11.6 14.5 11.8 14.7 11.9 15C12 15.3 12.1 15.5 12.1 15.8C12.1 16.2 12 16.5 11.9 16.8C11.8 17.1 11.5 17.4 11.3 17.7C11.1 18 10.7 18.2 10.3 18.3C9.9 18.4 9.5 18.5 9 18.5C8.5 18.5 8.1 18.4 7.7 18.2C7.3 18 7 17.8 6.8 17.6C6.6 17.4 6.4 17.1 6.3 16.8C6.2 16.5 6.10001 16.3 6.10001 16.1C6.10001 15.9 6.2 15.7 6.3 15.6C6.4 15.5 6.6 15.4 6.8 15.4C6.9 15.4 7.00001 15.4 7.10001 15.5C7.20001 15.6 7.3 15.6 7.3 15.7C7.5 16.2 7.7 16.6 8 16.9C8.3 17.2 8.6 17.3 9 17.3C9.2 17.3 9.5 17.2 9.7 17.1C9.9 17 10.1 16.8 10.3 16.6C10.5 16.4 10.5 16.1 10.5 15.8C10.5 15.3 10.4 15 10.1 14.7C9.80001 14.4 9.50001 14.3 9.10001 14.3C9.00001 14.3 8.9 14.3 8.7 14.3C8.5 14.3 8.39999 14.3 8.39999 14.3C8.19999 14.3 7.99999 14.2 7.89999 14.1C7.79999 14 7.7 13.8 7.7 13.7C7.7 13.5 7.79999 13.4 7.89999 13.2C7.99999 13 8.2 13 8.5 13H8.8V13.1ZM15.3 17.5V12.2C14.3 13 13.6 13.3 13.3 13.3C13.1 13.3 13 13.2 12.9 13.1C12.8 13 12.7 12.8 12.7 12.6C12.7 12.4 12.8 12.3 12.9 12.2C13 12.1 13.2 12 13.6 11.8C14.1 11.6 14.5 11.3 14.7 11.1C14.9 10.9 15.2 10.6 15.5 10.3C15.8 10 15.9 9.80003 15.9 9.70003C15.9 9.60003 16.1 9.60004 16.3 9.60004C16.5 9.60004 16.7 9.70003 16.8 9.80003C16.9 9.90003 17 10.2 17 10.5V17.2C17 18 16.7 18.4 16.2 18.4C16 18.4 15.8 18.3 15.6 18.2C15.4 18.1 15.3 17.8 15.3 17.5Z" fill="black"></path>
                                                                </svg>
                                                            </span> 
                                                            <input name="input_docdate" id="input_docdate" class="form-control form-control-solid ps-12 flatpickr-input"  type="text" readonly="readonly" value="{{ $docdate }}" readonly>
                                                        </div> 
                                                    </div> 
                                                
                                                    <div class="fv-row mb-15">
                                                        <label class="form-label">Remark</label>
                                                        <input name="input_remark" id="input_remark" class="input_form replace_quote form-control form-control-solid" value="{{ $remark }}"/>
                                                    </div>   
                                            </div>

                                                <div class="col-md-6 mb-10 ps-5">    
                                                    <div class="fv-row mb-5">
                                                        <label class="form-label">PO Number</label>
                                                        <input name="input_ponum" id="input_ponum" class="form-control form-control-solid" value="{{ $ponum }}" readonly/>
                                                    </div> 

                                                    <div class="fv-row mb-5">
                                                        <label class="form-label required">Ship Number</label>
                                                        <input name="input_shipnum" id="input_shipnum" class="input_form form-control form-control-solid" value="{{ $shipnum }}"/>
                                                    </div> 
                                                
                                                    <div class="fv-row mb-5">
                                                        <label class="d-flex align-items-center form-label">
                                                            <span class="required">Ship Date</span> 
                                                        </label> 
                                                        <div class="position-relative d-flex align-items-center">
                                                            <span class="svg-icon position-absolute ms-4 mb-1 svg-icon-2">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                                    <path opacity="0.3" d="M21 22H3C2.4 22 2 21.6 2 21V5C2 4.4 2.4 4 3 4H21C21.6 4 22 4.4 22 5V21C22 21.6 21.6 22 21 22Z" fill="black"></path>
                                                                    <path d="M6 6C5.4 6 5 5.6 5 5V3C5 2.4 5.4 2 6 2C6.6 2 7 2.4 7 3V5C7 5.6 6.6 6 6 6ZM11 5V3C11 2.4 10.6 2 10 2C9.4 2 9 2.4 9 3V5C9 5.6 9.4 6 10 6C10.6 6 11 5.6 11 5ZM15 5V3C15 2.4 14.6 2 14 2C13.4 2 13 2.4 13 3V5C13 5.6 13.4 6 14 6C14.6 6 15 5.6 15 5ZM19 5V3C19 2.4 18.6 2 18 2C17.4 2 17 2.4 17 3V5C17 5.6 17.4 6 18 6C18.6 6 19 5.6 19 5Z" fill="black"></path>
                                                                    <path d="M8.8 13.1C9.2 13.1 9.5 13 9.7 12.8C9.9 12.6 10.1 12.3 10.1 11.9C10.1 11.6 10 11.3 9.8 11.1C9.6 10.9 9.3 10.8 9 10.8C8.8 10.8 8.59999 10.8 8.39999 10.9C8.19999 11 8.1 11.1 8 11.2C7.9 11.3 7.8 11.4 7.7 11.6C7.6 11.8 7.5 11.9 7.5 12.1C7.5 12.2 7.4 12.2 7.3 12.3C7.2 12.4 7.09999 12.4 6.89999 12.4C6.69999 12.4 6.6 12.3 6.5 12.2C6.4 12.1 6.3 11.9 6.3 11.7C6.3 11.5 6.4 11.3 6.5 11.1C6.6 10.9 6.8 10.7 7 10.5C7.2 10.3 7.49999 10.1 7.89999 10C8.29999 9.90003 8.60001 9.80003 9.10001 9.80003C9.50001 9.80003 9.80001 9.90003 10.1 10C10.4 10.1 10.7 10.3 10.9 10.4C11.1 10.5 11.3 10.8 11.4 11.1C11.5 11.4 11.6 11.6 11.6 11.9C11.6 12.3 11.5 12.6 11.3 12.9C11.1 13.2 10.9 13.5 10.6 13.7C10.9 13.9 11.2 14.1 11.4 14.3C11.6 14.5 11.8 14.7 11.9 15C12 15.3 12.1 15.5 12.1 15.8C12.1 16.2 12 16.5 11.9 16.8C11.8 17.1 11.5 17.4 11.3 17.7C11.1 18 10.7 18.2 10.3 18.3C9.9 18.4 9.5 18.5 9 18.5C8.5 18.5 8.1 18.4 7.7 18.2C7.3 18 7 17.8 6.8 17.6C6.6 17.4 6.4 17.1 6.3 16.8C6.2 16.5 6.10001 16.3 6.10001 16.1C6.10001 15.9 6.2 15.7 6.3 15.6C6.4 15.5 6.6 15.4 6.8 15.4C6.9 15.4 7.00001 15.4 7.10001 15.5C7.20001 15.6 7.3 15.6 7.3 15.7C7.5 16.2 7.7 16.6 8 16.9C8.3 17.2 8.6 17.3 9 17.3C9.2 17.3 9.5 17.2 9.7 17.1C9.9 17 10.1 16.8 10.3 16.6C10.5 16.4 10.5 16.1 10.5 15.8C10.5 15.3 10.4 15 10.1 14.7C9.80001 14.4 9.50001 14.3 9.10001 14.3C9.00001 14.3 8.9 14.3 8.7 14.3C8.5 14.3 8.39999 14.3 8.39999 14.3C8.19999 14.3 7.99999 14.2 7.89999 14.1C7.79999 14 7.7 13.8 7.7 13.7C7.7 13.5 7.79999 13.4 7.89999 13.2C7.99999 13 8.2 13 8.5 13H8.8V13.1ZM15.3 17.5V12.2C14.3 13 13.6 13.3 13.3 13.3C13.1 13.3 13 13.2 12.9 13.1C12.8 13 12.7 12.8 12.7 12.6C12.7 12.4 12.8 12.3 12.9 12.2C13 12.1 13.2 12 13.6 11.8C14.1 11.6 14.5 11.3 14.7 11.1C14.9 10.9 15.2 10.6 15.5 10.3C15.8 10 15.9 9.80003 15.9 9.70003C15.9 9.60003 16.1 9.60004 16.3 9.60004C16.5 9.60004 16.7 9.70003 16.8 9.80003C16.9 9.90003 17 10.2 17 10.5V17.2C17 18 16.7 18.4 16.2 18.4C16 18.4 15.8 18.3 15.6 18.2C15.4 18.1 15.3 17.8 15.3 17.5Z" fill="black"></path>
                                                                </svg>
                                                            </span> 
                                                            <input name="input_shipdate" id="input_shipdate" class="input_form datepickr form-control form-control-solid ps-12 flatpickr-input" placeholder="Select Date" type="text" readonly="readonly" value="{{ $shipdate }}">
                                                        </div>
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div> 
                                            </div> 
                                        </div>
                                    </form>
                                </div> 
                            </div>
                            <div class="card-footer text-end">
                                <div id="div-head-btn"> 
                                    <button type="submit" id="btn-doc-delete" class="btn div-head-btn btn-xs btn-outline btn-outline-dashed btn-outline-primary btn-active-light-primary me-2" onclick="return confirmDeleteDoc()"><span class="indicator-label">
                                            <span class="svg-icon svg-icon-muted svg-icon-2x">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="black"/>
                                                    <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="black"/>
                                                    <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="black"/>
                                                </svg>
                                            </span>
                                            Delete
                                        </span>
                                        <span class="indicator-progress">Please wait...<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                    </button> 
                                    <button type="submit" id="btn-doc-confirm" class="btn div-head-btn btn-xs btn-outline btn-outline-dashed btn-outline-primary btn-active-light-primary" onclick="return docConfirm()"><span class="indicator-label">
                                        <span class="svg-icon svg-icon-muted svg-icon-2x"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="black"/>
                                            <path opacity="0.5" d="M12.4343 12.4343L10.75 10.75C10.3358 10.3358 9.66421 10.3358 9.25 10.75C8.83579 11.1642 8.83579 11.8358 9.25 12.25L12.2929 15.2929C12.6834 15.6834 13.3166 15.6834 13.7071 15.2929L19.25 9.75C19.6642 9.33579 19.6642 8.66421 19.25 8.25C18.8358 7.83579 18.1642 7.83579 17.75 8.25L13.5657 12.4343C13.2533 12.7467 12.7467 12.7467 12.4343 12.4343Z" fill="black"/>
                                            <path d="M8.43431 12.4343L6.75 10.75C6.33579 10.3358 5.66421 10.3358 5.25 10.75C4.83579 11.1642 4.83579 11.8358 5.25 12.25L8.29289 15.2929C8.68342 15.6834 9.31658 15.6834 9.70711 15.2929L15.25 9.75C15.6642 9.33579 15.6642 8.66421 15.25 8.25C14.8358 7.83579 14.1642 7.83579 13.75 8.25L9.56569 12.4343C9.25327 12.7467 8.74673 12.7467 8.43431 12.4343Z" fill="black"/>
                                        </svg></span>
                                            Confirm
                                        </span>
                                        <span class="indicator-progress">Please wait...<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                    </button> 
                                    <div id="div-head-btn-update"></div>
                                </div> 
                                
                            </div>
                        </div> 

                            <div class="card col-xxl-12 mt-5">   
                                <div class="card-header border-0 pt-6"> 
                                    <div class="w-100"> 
                                        <div class="pb-0 pb-lg-12 separator my-2 mb-5"> 
                                            <h2 class="fw-bolder text-dark">Detail Line</h2>  
                                        </div> 
                                    </div>

                                    <div class="card-title"> 
                                        <div class="d-flex align-items-center position-relative my-1"> 
                                            <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                                    <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                                                </svg>
                                            </span> 
                                            <input type="text" data-kt-goodreceive-table-filter="search" id="front_table_search" class="form-control form-control-solid w-250px ps-15" placeholder="Search Item" />
                                        </div> 
                                    </div> 
                                    <div class="card-toolbar"> 
                                        <div class="d-flex justify-content-end" data-kt-goodreceive-table-toolbar="base">    
                                            <div id="div-detail-btn-update"></div> 
                                    </div>  
                                </div>
                            </div>  
                                <div class="card-body">  
                                    <table class="table align-middle table-row-dashed table-striped" id="kt_item_dc_table">
                                        <thead>
                                            <tr class="text-start text-gray-600 fw-bolder gs-0">
                                                <th class="min-w-20px text-center">No</th> 
                                                <th class="min-w-125px">ItemNo</th>
                                                <th class="min-w-125px">ItemName</th>
                                                <th class="min-w-40px text-right">Qty</th>
                                                <th class="min-w-40px">Bal</th>   
                                                <th class="min-w-40px">Del</th>  
                                                <th class="min-w-20px">Pack</th>  
                                            </tr> 
                                        </thead>  
                                        <tfoot>
                                            <tr class="text-gray-600 fw-bolder gs-0">
                                                <th class="min-w-20px text-center">No</th> 
                                                <th class="min-w-125px">ItemNo</th>
                                                <th class="min-w-125px">ItemName</th>
                                                <th class="min-w-40px text-right">Qty</th>
                                                <th class="min-w-40px">Bal</th>   
                                                <th class="min-w-40px">Del</th>  
                                                <th class="min-w-20px">Pack</th>  
                                            </tr> 
                                        </tfoot>  
                                    </table>  
                                </div>  
                            </div> 
                        </div>

                        <div class="tab-pane fade" id="kt_tab_pane_6" role="tabpanel">Sint sit mollit irure quis est nostrud cillum consequat Lorem esse do quis dolor esse fugiat sunt do. Eu ex commodo veniam Lorem aliquip laborum occaecat qui Lorem esse mollit dolore anim cupidatat. eserunt officia id Lorem nostrud aute id commodo elit eiusmod enim irure amet eiusmod qui reprehenderit nostrud tempor. Fugiat ipsum excepteur in aliqua non et quis aliquip ad irure in labore cillum elit enim. Consequat aliquip incididunt ipsum et minim laborum laborum laborum et cillum labore. Deserunt adipisicing cillum id nulla minim nostrud labore eiusmod et amet.</div>

                    </div> 
            </div> 
             
        </div> 
    </div>  
</div> 

    <div class="modal fade" tabindex="-1" data-bs-backdrop="static" id="kt_modal_pack" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Packaging List</h5>   
                </div> 
                <div class="modal-body">     
                        <div class="card mt-5"> 
                            <div class="card-title mb-10">  
                                <div class="row mt-10">   
                                    <input type="hidden" id="ref_doc_pack" readonly> 
                                    <div class="col-md-6 px-5">  
                                        <label class="form-label fs-5 fw-bold mb-3">Serial Number :</label> 
                                        <div class="d-flex align-items-center position-relative"> 
                                            <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path opacity="0.3" d="M18 22C19.7 22 21 20.7 21 19C21 18.5 20.9 18.1 20.7 17.7L15.3 6.30005C15.1 5.90005 15 5.5 15 5C15 3.3 16.3 2 18 2H6C4.3 2 3 3.3 3 5C3 5.5 3.1 5.90005 3.3 6.30005L8.7 17.7C8.9 18.1 9 18.5 9 19C9 20.7 7.7 22 6 22H18Z" fill="black"/>
                                                    <path d="M18 2C19.7 2 21 3.3 21 5H9C9 3.3 7.7 2 6 2H18Z" fill="black"/>
                                                    <path d="M9 19C9 20.7 7.7 22 6 22C4.3 22 3 20.7 3 19H9Z" fill="black"/>
                                                    </svg>
                                            </span> 
                                            <input type="text" data-kt-goodreceive-table-filter="search" id="serial_number" class="form-control form-control-solid w-100 ps-15"/>
                                        </div>   
                                    </div> 
                                    <div class="col-md-6 px-5">  
                                        <label class="form-label fs-5 fw-bold mb-3">Qty :</label> 
                                        <div class="d-flex align-items-center position-relative"> 
                                            <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path opacity="0.3" d="M18.041 22.041C18.5932 22.041 19.041 21.5932 19.041 21.041C19.041 20.4887 18.5932 20.041 18.041 20.041C17.4887 20.041 17.041 20.4887 17.041 21.041C17.041 21.5932 17.4887 22.041 18.041 22.041Z" fill="black"/>
                                                    <path opacity="0.3" d="M6.04095 22.041C6.59324 22.041 7.04095 21.5932 7.04095 21.041C7.04095 20.4887 6.59324 20.041 6.04095 20.041C5.48867 20.041 5.04095 20.4887 5.04095 21.041C5.04095 21.5932 5.48867 22.041 6.04095 22.041Z" fill="black"/>
                                                    <path opacity="0.3" d="M7.04095 16.041L19.1409 15.1409C19.7409 15.1409 20.141 14.7409 20.341 14.1409L21.7409 8.34094C21.9409 7.64094 21.4409 7.04095 20.7409 7.04095H5.44095L7.04095 16.041Z" fill="black"/>
                                                    <path d="M19.041 20.041H5.04096C4.74096 20.041 4.34095 19.841 4.14095 19.541C3.94095 19.241 3.94095 18.841 4.14095 18.541L6.04096 14.841L4.14095 4.64095L2.54096 3.84096C2.04096 3.64096 1.84095 3.04097 2.14095 2.54097C2.34095 2.04097 2.94096 1.84095 3.44096 2.14095L5.44096 3.14095C5.74096 3.24095 5.94096 3.54096 5.94096 3.84096L7.94096 14.841C7.94096 15.041 7.94095 15.241 7.84095 15.441L6.54096 18.041H19.041C19.641 18.041 20.041 18.441 20.041 19.041C20.041 19.641 19.641 20.041 19.041 20.041Z" fill="black"/>
                                                </svg>
                                            </span> 
                                            <input type="text" data-kt-goodreceive-table-filter="search" id="qty_pack" class="form-control number_format_coma form-control-solid w-100 ps-15"/>
                                        </div>   
                                    </div>   
                                </div>
                            </div>  

                            <div class="card-footer mb-10 text-end">    

                                <button type="button" id="btn-generate-label" class="btn btn-sm btn-outline btn-outline-dashed btn-outline-info btn-active-light-info" onclick="return generateTagLabel()">
                                    <span class="indicator-label">
                                        <span class="svg-icon svg-icon-muted svg-icon-2x">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path d="M14.5 20.7259C14.6 21.2259 14.2 21.826 13.7 21.926C13.2 22.026 12.6 22.0259 12.1 22.0259C9.5 22.0259 6.9 21.0259 5 19.1259C1.4 15.5259 1.09998 9.72592 4.29998 5.82592L5.70001 7.22595C3.30001 10.3259 3.59999 14.8259 6.39999 17.7259C8.19999 19.5259 10.8 20.426 13.4 19.926C13.9 19.826 14.4 20.2259 14.5 20.7259ZM18.4 16.8259L19.8 18.2259C22.9 14.3259 22.7 8.52593 19 4.92593C16.7 2.62593 13.5 1.62594 10.3 2.12594C9.79998 2.22594 9.4 2.72595 9.5 3.22595C9.6 3.72595 10.1 4.12594 10.6 4.02594C13.1 3.62594 15.7 4.42595 17.6 6.22595C20.5 9.22595 20.7 13.7259 18.4 16.8259Z" fill="black"/>
                                                <path opacity="0.3" d="M2 3.62592H7C7.6 3.62592 8 4.02592 8 4.62592V9.62589L2 3.62592ZM16 14.4259V19.4259C16 20.0259 16.4 20.4259 17 20.4259H22L16 14.4259Z" fill="black"/>
                                            </svg>
                                        </span>
                                        Generate
                                    </span>
                                    <span class="indicator-progress">Please wait...<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button> 

                                    <button type="button" id="btn-add-label" class="btn btn-sm btn-outline btn-outline-dashed btn-outline-info btn-active-light-info" onclick="return addTagLabel()">
                                        <span class="indicator-label">
                                            <span class="svg-icon svg-icon-muted svg-icon-2x">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path opacity="0.3" d="M11 13H7C6.4 13 6 12.6 6 12C6 11.4 6.4 11 7 11H11V13ZM17 11H13V13H17C17.6 13 18 12.6 18 12C18 11.4 17.6 11 17 11Z" fill="black"/>
                                                    <path d="M22 12C22 17.5 17.5 22 12 22C6.5 22 2 17.5 2 12C2 6.5 6.5 2 12 2C17.5 2 22 6.5 22 12ZM17 11H13V7C13 6.4 12.6 6 12 6C11.4 6 11 6.4 11 7V11H7C6.4 11 6 11.4 6 12C6 12.6 6.4 13 7 13H11V17C11 17.6 11.4 18 12 18C12.6 18 13 17.6 13 17V13H17C17.6 13 18 12.6 18 12C18 11.4 17.6 11 17 11Z" fill="black"/>
                                                </svg>
                                            </span>
                                            Add
                                        </span>
                                        <span class="indicator-progress">Please wait...<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                    </button> 

                            </div>

                            <div class="px-3"> 
                                <table class="table table-row-dashed table-striped fs-7 no-footer" id="kt_packaging_table" style="width: 100%;">
                                    <thead>
                                        <tr class="text-gray-800 fw-bolder fs-7 gs-0" style="border-bottom: 2px solid #181c32;">
                                            <th class="min-w-20px">No</th>
                                            <th class="text-start min-w-155px">Serial Number</th> 
                                            <th class="text-end min-w-55px float-right" style="text-align:right;">Qty</th>
                                            <th class="text-end min-w-75px px-2">Action</th>   
                                        </tr> 
                                    </thead>  
                                </table> 
                            </div>    
                        </div>  
                    </div>    
                <div class="modal-footer">  

                    <button type="button" id="btn-clear-label" class="btn btn-sm btn-outline btn-outline-dashed btn-outline-primary btn-active-light-primary" onclick="return confirmClearTaglLabel()">
                        <span class="indicator-label">
                            <span class="svg-icon svg-icon-muted svg-icon-2x">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="black"/>
                                    <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="black"/>
                                    <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="black"/>
                                </svg>
                            </span>
                            Clear
                        </span>
                        <span class="indicator-progress">Please wait...<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button> 

                    <button type="button" class="btn btn-sm btn-outline btn-outline-dashed btn-outline-primary btn-active-light-primary" data-bs-dismiss="modal" onclick="return cancelDetailUpdate()">
                        <span class="svg-icon svg-icon-muted svg-icon-2x">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"> 
                                <path opacity="0.3" d="M12 10.6L14.8 7.8C15.2 7.4 15.8 7.4 16.2 7.8C16.6 8.2 16.6 8.80002 16.2 9.20002L13.4 12L12 10.6ZM10.6 12L7.8 14.8C7.4 15.2 7.4 15.8 7.8 16.2C8 16.4 8.3 16.5 8.5 16.5C8.7 16.5 8.99999 16.4 9.19999 16.2L12 13.4L10.6 12Z" fill="black"/>
                                <path d="M22 12C22 17.5 17.5 22 12 22C6.5 22 2 17.5 2 12C2 6.5 6.5 2 12 2C17.5 2 22 6.5 22 12ZM13.4 12L16.2 9.20001C16.6 8.80001 16.6 8.19999 16.2 7.79999C15.8 7.39999 15.2 7.39999 14.8 7.79999L12 10.6L9.2 7.79999C8.8 7.39999 8.2 7.39999 7.8 7.79999C7.4 8.19999 7.4 8.80001 7.8 9.20001L10.6 12L7.8 14.8C7.4 15.2 7.4 15.8 7.8 16.2C8 16.4 8.3 16.5 8.5 16.5C8.7 16.5 9 16.4 9.2 16.2L12 13.4L14.8 16.2C15 16.4 15.3 16.5 15.5 16.5C15.7 16.5 16 16.4 16.2 16.2C16.6 15.8 16.6 15.2 16.2 14.8L13.4 12Z" fill="black"/>
                            </svg>
                        </span>
                    Close</button>  
                </div>

            </div>
        </div>
    </div>

<input type="text" id="doc_detail_id" name="doc_detail_id" readonly hidden/> 
<input type="text" id="on_editing" name="on_editing" value="0" readonly hidden/>
<input type="text" id="source_unix_id" name="source_unix_id" readonly hidden/>  

<script> 
    
    $(document).ready(function () {  
        var frontTable = $("#kt_item_dc_table").DataTable({
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
                        }],
            ajax: {
                url: "{{ route('delcon.detail_order') }}",
                type: 'POST',
                data	: function ( d ) { d._token = $("[name=_token]").val(), d.ref_doc = $("#ref_doc").val(), d.ref_doc_po = $("#ref_doc_po").val(), d.front_table_search = $("#front_table_search").val() ; }, 
                cache	: false,
                dataType : 'json'
            },
            columns: [
                { data: 'no', className: 'text-center' }, 
                { data: 'itemno' },
                { data: 'itemname' },
                { data: 'qty' },
                { data: 'balance' },
                { data: 'delivery' },
                { data: 'pack' }
            ]
        })   

        setTimeout(function(){ 
            frontTable.ajax.reload();    
        },100)  

        $("#front_table_search").keyup(function(event){
            if(event.keyCode == 13){ 
                frontTable.ajax.reload();  
                $("#on_editing").val(0); 
                $("#doc_detail_id").val("");  
                $(".btn-detail-update").remove(); 
            } 
        }); 

        $("#submit-filter").click(function(){
                frontTable.ajax.reload();  
                $("#on_editing").val(0); 
                $("#doc_detail_id").val("");  
                $(".btn-detail-update").remove(); 
        });  
        
        $('select[name="kt_item_dc_table_length"]').on('change', function() { 
            $("#on_editing").val(0); 
            $("#doc_detail_id").val("");  
            $(".btn-detail-update").remove();    
        });

        $(".pagination").click(function () { 
            $("#on_editing").val(0); 
            $("#doc_detail_id").val("");  
            $(".btn-detail-update").remove();     
        })
   
 
        $(".input_form").keyup(function(event){
            var a = $("#div-head-btn-update").find("cancel-head-update");
            var elementCount = $( "#div-head-btn-update" ).find( "*" ).length;  
            if (elementCount==0) {
                $(".div-head-btn").css("display", "none")
                $("#div-head-btn-update").append('<button type="submit" id="cancel-head-update" class="btn btn-head-update btn-xs btn-outline btn-outline-dashed btn-outline-success btn-active-light-success mx-3" onclick="return cancelHeadUpdate()"><span class="indicator-label"><span class="svg-icon svg-icon-muted svg-icon-2x"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path opacity="0.3" d="M12 10.6L14.8 7.8C15.2 7.4 15.8 7.4 16.2 7.8C16.6 8.2 16.6 8.80002 16.2 9.20002L13.4 12L12 10.6ZM10.6 12L7.8 14.8C7.4 15.2 7.4 15.8 7.8 16.2C8 16.4 8.30001 16.5 8.50001 16.5C8.70001 16.5 9.00002 16.4 9.20002 16.2L12 13.4L10.6 12Z" fill="black"/><path d="M21 22H3C2.4 22 2 21.6 2 21V3C2 2.4 2.4 2 3 2H21C21.6 2 22 2.4 22 3V21C22 21.6 21.6 22 21 22ZM13.4 12L16.2 9.20001C16.6 8.80001 16.6 8.19999 16.2 7.79999C15.8 7.39999 15.2 7.39999 14.8 7.79999L12 10.6L9.20001 7.79999C8.80001 7.39999 8.19999 7.39999 7.79999 7.79999C7.39999 8.19999 7.39999 8.80001 7.79999 9.20001L10.6 12L7.79999 14.8C7.39999 15.2 7.39999 15.8 7.79999 16.2C7.99999 16.4 8.3 16.5 8.5 16.5C8.7 16.5 9.00001 16.4 9.20001 16.2L12 13.4L14.8 16.2C15 16.4 15.3 16.5 15.5 16.5C15.7 16.5 16 16.4 16.2 16.2C16.6 15.8 16.6 15.2 16.2 14.8L13.4 12Z" fill="black"/></svg></span>Cancel</span><span class="indicator-progress">Please wait...<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span></button><button type="submit" id="save-head-update" class="btn btn-head-update btn-xs btn-outline btn-outline-dashed btn-outline-success btn-active-light-success" onclick="return saveHead()"><span class="indicator-label"><span class="svg-icon svg-icon-muted svg-icon-2x"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path opacity="0.3" d="M10.3 14.3L11 13.6L7.70002 10.3C7.30002 9.9 6.7 9.9 6.3 10.3C5.9 10.7 5.9 11.3 6.3 11.7L10.3 15.7C9.9 15.3 9.9 14.7 10.3 14.3Z" fill="black"/><path d="M21 22H3C2.4 22 2 21.6 2 21V3C2 2.4 2.4 2 3 2H21C21.6 2 22 2.4 22 3V21C22 21.6 21.6 22 21 22ZM11.7 15.7L17.7 9.70001C18.1 9.30001 18.1 8.69999 17.7 8.29999C17.3 7.89999 16.7 7.89999 16.3 8.29999L11 13.6L7.70001 10.3C7.30001 9.89999 6.69999 9.89999 6.29999 10.3C5.89999 10.7 5.89999 11.3 6.29999 11.7L10.3 15.7C10.5 15.9 10.8 16 11 16C11.2 16 11.5 15.9 11.7 15.7Z" fill="black"/></svg></span>&nbsp;Save&nbsp;</span><span class="indicator-progress">Please wait...<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span></button>');
            } 
        })

        $(".input_form").change(function(event){ 
            var elementCount = $( "#div-head-btn-update" ).find( "*" ).length;  
            if (elementCount==0) {
                $(".div-head-btn").css("display", "none")
                $("#div-head-btn-update").append('<button type="submit" id="cancel-head-update" class="btn btn-head-update btn-xs btn-outline btn-outline-dashed btn-outline-success btn-active-light-success mx-3" onclick="return cancelHeadUpdate()"><span class="indicator-label"><span class="svg-icon svg-icon-muted svg-icon-2x"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path opacity="0.3" d="M12 10.6L14.8 7.8C15.2 7.4 15.8 7.4 16.2 7.8C16.6 8.2 16.6 8.80002 16.2 9.20002L13.4 12L12 10.6ZM10.6 12L7.8 14.8C7.4 15.2 7.4 15.8 7.8 16.2C8 16.4 8.30001 16.5 8.50001 16.5C8.70001 16.5 9.00002 16.4 9.20002 16.2L12 13.4L10.6 12Z" fill="black"/><path d="M21 22H3C2.4 22 2 21.6 2 21V3C2 2.4 2.4 2 3 2H21C21.6 2 22 2.4 22 3V21C22 21.6 21.6 22 21 22ZM13.4 12L16.2 9.20001C16.6 8.80001 16.6 8.19999 16.2 7.79999C15.8 7.39999 15.2 7.39999 14.8 7.79999L12 10.6L9.20001 7.79999C8.80001 7.39999 8.19999 7.39999 7.79999 7.79999C7.39999 8.19999 7.39999 8.80001 7.79999 9.20001L10.6 12L7.79999 14.8C7.39999 15.2 7.39999 15.8 7.79999 16.2C7.99999 16.4 8.3 16.5 8.5 16.5C8.7 16.5 9.00001 16.4 9.20001 16.2L12 13.4L14.8 16.2C15 16.4 15.3 16.5 15.5 16.5C15.7 16.5 16 16.4 16.2 16.2C16.6 15.8 16.6 15.2 16.2 14.8L13.4 12Z" fill="black"/></svg></span>Cancel</span><span class="indicator-progress">Please wait...<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span></button><button type="submit" id="save-head-update" class="btn btn-head-update btn-xs btn-outline btn-outline-dashed btn-outline-success btn-active-light-success" onclick="return saveHead()"><span class="indicator-label"><span class="svg-icon svg-icon-muted svg-icon-2x"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path opacity="0.3" d="M10.3 14.3L11 13.6L7.70002 10.3C7.30002 9.9 6.7 9.9 6.3 10.3C5.9 10.7 5.9 11.3 6.3 11.7L10.3 15.7C9.9 15.3 9.9 14.7 10.3 14.3Z" fill="black"/><path d="M21 22H3C2.4 22 2 21.6 2 21V3C2 2.4 2.4 2 3 2H21C21.6 2 22 2.4 22 3V21C22 21.6 21.6 22 21 22ZM11.7 15.7L17.7 9.70001C18.1 9.30001 18.1 8.69999 17.7 8.29999C17.3 7.89999 16.7 7.89999 16.3 8.29999L11 13.6L7.70001 10.3C7.30001 9.89999 6.69999 9.89999 6.29999 10.3C5.89999 10.7 5.89999 11.3 6.29999 11.7L10.3 15.7C10.5 15.9 10.8 16 11 16C11.2 16 11.5 15.9 11.7 15.7Z" fill="black"/></svg></span>&nbsp;Save&nbsp;</span><span class="indicator-progress">Please wait...<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span></button>');
            } 
        })
  

}) ;

    function confirmDeleteDoc()
        { 
            Swal.fire({
                icon: 'warning',
                title: 'Delete Document?',  
                showCancelButton: true,
                confirmButtonText: 'Confirm',
                }).then(function(isConfirm) { 
                if (isConfirm.value === true) {
                    deleteDoc();
                }
            })

        }

        function deleteDoc() {
            var token = $("[name=_token]").val();      
            var header_form = $("#header_form").serialize() ;      
            var string = "&_token="+token+"&"+header_form ; 
            $("#btn-doc-delete").attr("data-kt-indicator", "on");
            $("#btn-doc-delete").attr("disabled", "disabled");   
            $.ajax({
                type	: 'POST',
                url    : "{{ route('delcon.document_delete') }}",
                data	: string,
                cache	: false,
                dataType : 'json',
                success	: function(response){    
                    $("#btn-doc-delete").removeAttr("data-kt-indicator", "on");
                    $("#btn-doc-delete").removeAttr("disabled", "disabled");  
                    if(response.process==1){   
                        window.location.href = '/del_confirm' ;   
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: response.msg
                        })
                    }
                }, error: function (data) { 
                        Toast.fire({
                            icon: 'error',
                            title: 'Please reload and try again!'
                        })
                        $("#btn-doc-delete").removeAttr("data-kt-indicator", "on");
                        $("#btn-doc-delete").removeAttr("disabled", "disabled");
                    }
            })    
        };


    function confirmClearTaglLabel()
        { 
            Swal.fire({
                icon: 'warning',
                title: 'Delete All Tag Label?', 
                showCancelButton: true,
                confirmButtonText: 'Confirm',
                }).then(function(isConfirm) { 
                if (isConfirm.value === true) {
                    clearTagLabel();
                }
            })

        }

    function clearTagLabel() {
        var token = $("[name=_token]").val();    
        var ref_doc_pack = $("#ref_doc_pack").val();  
        $("#btn-clear-label").attr("data-kt-indicator", "on");
        $("#btn-clear-label").attr("disabled", "disabled");
        var string = "&_token="+token+"&ref_doc_pack="+ref_doc_pack ; 
        $.ajax({
            type	: 'POST',
            url    : "{{ route('delcon.clear_tag_label') }}",
            data	: string,
            cache	: false,
            dataType : 'json',
            success	: function(data){  
                $("#btn-clear-label").removeAttr("data-kt-indicator", "on");
                $("#btn-clear-label").removeAttr("disabled", "disabled");
                if (data.process==0) {
                    Toast.fire({
                        icon: 'error',
                        title: data.msg
                    })  
                } else {
                    Toast.fire({
                        icon: 'success',
                        title: data.msg
                    })  
                    tag_lable_table()
                }
            },
            error: function (data) { 
                Toast.fire({
                    icon: 'error',
                    title: 'Please reload and try again!'
                })
                setTimeout(() => {
                    $("#btn-clear-label").removeAttr("data-kt-indicator", "on");
                    $("#btn-clear-label").removeAttr("disabled", "disabled");
                }, 250);
                
            } 
        })
    }

    function generateTagLabel() {
        var token = $("[name=_token]").val();    
        var ref_doc_pack = $("#ref_doc_pack").val(); 
        var qty = $("#qty_pack").val().replace(/\,/g,'');  
        var serial_number = $("#serial_number").val();    

        if (qty == '' || qty == null || qty <= 0) {
            Toast.fire({
                icon: 'error',
                title: 'Qty is required !'
            })   
            return false ;
        } 
        $("#btn-generate-label").attr("data-kt-indicator", "on");
        $("#btn-generate-label").attr("disabled", "disabled");
        var string = "&_token="+token+"&ref_doc_pack="+ref_doc_pack+"&qty="+qty+"&serial_number="+serial_number ;  
        $.ajax({
            type	: 'POST',
            url    : "{{ route('delcon.generate_tag_label') }}",
            data	: string,
            cache	: false,
            dataType : 'json',
            success	: function(data){  
                $("#btn-generate-label").removeAttr("data-kt-indicator", "on");
                $("#btn-generate-label").removeAttr("disabled", "disabled");
                if (data.process==0) {
                    Toast.fire({
                        icon: 'error',
                        title: data.msg
                    })  
                } else {
                    Toast.fire({
                        icon: 'success',
                        title: data.msg
                    })  
                    tag_lable_table()
                }
            },
            error: function (data) { 
                Toast.fire({
                    icon: 'error',
                    title: 'Please reload and try again!'
                })
                setTimeout(() => {
                    $("#btn-generate-label").removeAttr("data-kt-indicator", "on");
                    $("#btn-generate-label").removeAttr("disabled", "disabled");
                }, 250);
                
            } 
        })
    }

    function addTagLabel() {
        var token = $("[name=_token]").val();    
        var ref_doc_pack = $("#ref_doc_pack").val(); 
        var qty = $("#qty_pack").val().replace(/\,/g,'');  
        var serial_number = $("#serial_number").val();   
        if (qty == '' || qty == null || qty <= 0) {
            Toast.fire({
                icon: 'error',
                title: 'Qty is required !'
            })   
            return false ;
        } 
        $("#btn-add-label").attr("data-kt-indicator", "on");
        $("#btn-add-label").attr("disabled", "disabled");
        var string = "&_token="+token+"&ref_doc_pack="+ref_doc_pack+"&qty="+qty+"&serial_number="+serial_number ; 
        $.ajax({
            type	: 'POST',
            url    : "{{ route('delcon.add_tag_label') }}",
            data	: string,
            cache	: false,
            dataType : 'json',
            success	: function(data){  
                $("#btn-add-label").removeAttr("data-kt-indicator", "on");
                $("#btn-add-label").removeAttr("disabled", "disabled");
                if (data.process==0) {
                    Toast.fire({
                        icon: 'error',
                        title: data.msg
                    })  
                } else {
                    Toast.fire({
                        icon: 'success',
                        title: data.msg
                    })  
                    tag_lable_table()
                }
            },
            error: function (data) { 
                Toast.fire({
                    icon: 'error',
                    title: 'Please reload and try again!'
                })
                setTimeout(() => {
                    $("#btn-add-label").removeAttr("data-kt-indicator", "on");
                    $("#btn-add-label").removeAttr("disabled", "disabled");
                }, 250);
                
            } 
        })
    }

    function docConfirm() {
        var token = $("[name=_token]").val();      
        var header_form = $("#header_form").serialize() ;   
        var ref_doc = $("#ref_doc").val() ;   
        var ref_form = "<?php echo $ref_form ?>" ;   
        var string = "&_token="+token+"&"+header_form ; 
        $("#btn-doc-confirm").attr("data-kt-indicator", "on");
        $("#btn-doc-confirm").attr("disabled", "disabled");  
        $.ajax({
        type	: 'POST',
        url    : "{{ route('delcon.checking_all_rule') }}",
        data	: string,
        cache	: false,
        dataType : 'json',
        success	: function(data){    
            if(data.process==0){
                Toast.fire({
                    icon: 'error',
                    title: data.msg
                })
                $("#btn-doc-confirm").removeAttr("data-kt-indicator", "on");
                $("#btn-doc-confirm").removeAttr("disabled", "disabled"); 
            } else { 
                $.ajax({
                    type	: 'POST',
                    url    : "{{ route('delcon.document_confirm') }}",
                    data	: string,
                    cache	: false,
                    dataType : 'json',
                    success	: function(response){    
                        $("#btn-doc-confirm").removeAttr("data-kt-indicator", "on");
                        $("#btn-doc-confirm").removeAttr("disabled", "disabled");  
                        if(response.process==1){   
                            Swal.fire({
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                icon: 'success',
                                title: 'Success Confirm', 
                                showCancelButton: true,
                                confirmButtonText: 'Preview',
                                }).then(function(isConfirm) { 
                                if (isConfirm.value === true) {
                                    window.location.href = 'open_doc?ref_doc='+ref_doc+'&ref_form='+ref_form ;  
                                } else {
                                    cancelConfirm();
                                } 

                            })
                            
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: response.msg
                            })
                        }
                    },
                        error: function (data) { 
                            Toast.fire({
                                icon: 'error',
                                title: 'Please reload and try again!'
                            })
                            $("#btn-doc-confirm").removeAttr("data-kt-indicator", "on");
                            $("#btn-doc-confirm").removeAttr("disabled", "disabled");
                        }
                }) 
            } 
        },
        error: function (data) { 
                Toast.fire({
                    icon: 'error',
                    title: 'Please reload and try again!'
                })
                $("#btn-doc-confirm").removeAttr("data-kt-indicator", "on");
                $("#btn-doc-confirm").removeAttr("disabled", "disabled");
            }  
        })   
    };

    function cancelConfirm() {    
        var token = $("[name=_token]").val(); 
        var ref_doc = $("#ref_doc").val(); 
        var ref_doc_po = $("#ref_doc_po").val();
        $("#btn-doc-confirm").attr("data-kt-indicator", "on");
        $("#btn-doc-confirm").attr("disabled", "disabled"); 
        var string = "&_token="+token+"&ref_doc="+ref_doc+"&ref_doc_po="+ref_doc_po ; 
        $.ajax({
            url	: "{{ route('delcon.cancel_confirm') }}",
            type	: 'POST', 
            data	: string, 
            typeData : 'json',  
            success: function (response) {   
                $("#btn-doc-confirm").removeAttr("data-kt-indicator", "on");
                $("#btn-doc-confirm").removeAttr("disabled", "disabled");
                var response = JSON.parse(response) 
                if (response.process==1) {
                    Toast.fire({
                        icon: 'success',
                        title: response.msg
                    })  
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: response.msg
                    }) 
                }
            }, error: function( jqXHR, textStatus ) {
                $("#btn-doc-confirm").removeAttr("data-kt-indicator", "on");
                $("#btn-doc-confirm").removeAttr("disabled", "disabled");
                    Toast.fire({
                        icon: 'error',
                        title: 'Please reload and try again'
                    }) 
                }
        })
    } 

    function confirm_delete_tag_label(id, dName)
    { 
        Swal.fire({
            icon: 'warning',
            title: 'Delete Tag Label?',
            text: "Qty : " + dName, 
            showCancelButton: true,
            confirmButtonText: 'Confirm',
            }).then(function(isConfirm) { 
            if (isConfirm.value === true) {
                delete_tag_label(id);
            }
        })

    }

    function delete_tag_label (id) {    
        var token = $("[name=_token]").val();   
        var string = "&_token="+token+"&id="+id ; 
        $.ajax({
            url	: "{{ route('delcon.destroy_tag_label') }}",
            type	: 'POST', 
            data	: string, 
            typeData : 'json',  
            success: function (response) {   
                var response = JSON.parse(response) 
                if (response.process==1) {
                    Toast.fire({
                        icon: 'success',
                        title: response.msg
                    }) 
                    tag_lable_table()
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: response.msg
                    }) 
                }
            }, error: function( jqXHR, textStatus ) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Please reload and try again'
                    }) 
                }
        })
    }

    function updateTagLabel (id) {    
        var token = $("[name=_token]").val();  
        var qty = $("#qty_pack_"+id).val().replace(/\,/g,'');    
        var sn = $("#sn_"+id).val() ;     
        var string = "&_token="+token+"&id="+id+"&qty="+qty+"&sn="+sn ; 
        $.ajax({
            url	: "{{ route('delcon.store_tag_label') }}",
            type	: 'POST', 
            data	: string, 
            typeData : 'json',  
            success: function (response) {   
                var response = JSON.parse(response) 
                if (response.process==1) {
                    Toast.fire({
                        icon: 'success',
                        title: response.msg
                    }) 
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: response.msg
                    }) 
                }
            }, error: function( jqXHR, textStatus ) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Please reload and try again'
                    }) 
                }
        })
    }

    function saveDetail (id) {    
        var token = $("[name=_token]").val();  
        var qty = $("#"+id).val().replace(/\,/g,'');   

        $("#save-detail-update").attr("data-kt-indicator", "on");
        $("#save-detail-update").attr("disabled", "disabled"); 
        var string = "&_token="+token+"&id="+id+"&qty="+qty ; 
        $.ajax({
            url	: "{{ route('delcon.store_detail') }}",
            type	: 'POST', 
            data	: string, 
            typeData : 'json',  
            success: function (response) {   
                var response = JSON.parse(response) 
                if (response.process==1) {
                    Toast.fire({
                        icon: 'success',
                        title: response.msg
                    })
                    $(".btn-detail-update").remove();      
                    $(".detailItemS").prop("disabled", false);
                    $(".detailItemS").css("cursor", "auto"); 
                    $(".detailItemS").css("color", "#7e8299"); 
                    $(".detailItemS").show();    
                    $("#pallet_"+id).val(0);   
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: response.msg
                    })
                    $("#save-detail-update").removeAttr("data-kt-indicator", "on");
                    $("#save-detail-update").removeAttr("disabled", "disabled"); 
                }
            }, error: function( jqXHR, textStatus ) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Please reload and try again'
                    })
                    console.clear();
                    $("#save-detail-update").removeAttr("data-kt-indicator", "on");
                    $("#save-detail-update").removeAttr("disabled", "disabled"); 
                }
        })
    }

    function calculateSource(event, id) {    
        var x = event.which || event.keyCode; 
        var idX = "'"+id+"'" ; 
        if( x == 27 ){ cancelDetailUpdate(); } else
        if( x == 13 ){ $("#save-detail-update").click(); } else {   
        var elementCount = $( "#div-detail-btn-update" ).find( "*" ).length;  
            if (elementCount==0) { 
                $("#div-detail-btn-update").append('<button type="submit" id="cancel-detail-update" class="btn btn-detail-update btn-xs btn-outline btn-outline-dashed btn-outline-success btn-active-light-success m-3" onclick="return cancelDetailUpdate()"><span class="indicator-label"><span class="svg-icon svg-icon-muted svg-icon-2x"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path opacity="0.3" d="M12 10.6L14.8 7.8C15.2 7.4 15.8 7.4 16.2 7.8C16.6 8.2 16.6 8.80002 16.2 9.20002L13.4 12L12 10.6ZM10.6 12L7.8 14.8C7.4 15.2 7.4 15.8 7.8 16.2C8 16.4 8.30001 16.5 8.50001 16.5C8.70001 16.5 9.00002 16.4 9.20002 16.2L12 13.4L10.6 12Z" fill="black"/><path d="M21 22H3C2.4 22 2 21.6 2 21V3C2 2.4 2.4 2 3 2H21C21.6 2 22 2.4 22 3V21C22 21.6 21.6 22 21 22ZM13.4 12L16.2 9.20001C16.6 8.80001 16.6 8.19999 16.2 7.79999C15.8 7.39999 15.2 7.39999 14.8 7.79999L12 10.6L9.20001 7.79999C8.80001 7.39999 8.19999 7.39999 7.79999 7.79999C7.39999 8.19999 7.39999 8.80001 7.79999 9.20001L10.6 12L7.79999 14.8C7.39999 15.2 7.39999 15.8 7.79999 16.2C7.99999 16.4 8.3 16.5 8.5 16.5C8.7 16.5 9.00001 16.4 9.20001 16.2L12 13.4L14.8 16.2C15 16.4 15.3 16.5 15.5 16.5C15.7 16.5 16 16.4 16.2 16.2C16.6 15.8 16.6 15.2 16.2 14.8L13.4 12Z" fill="black"/></svg></span>Cancel</span><span class="indicator-progress">Please wait...<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span></button><button type="submit" id="save-detail-update" class="btn btn-detail-update btn-xs btn-outline btn-outline-dashed btn-outline-success btn-active-light-success" onclick="return saveDetail('+idX+')"><span class="indicator-label"><span class="svg-icon svg-icon-muted svg-icon-2x"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path opacity="0.3" d="M10.3 14.3L11 13.6L7.70002 10.3C7.30002 9.9 6.7 9.9 6.3 10.3C5.9 10.7 5.9 11.3 6.3 11.7L10.3 15.7C9.9 15.3 9.9 14.7 10.3 14.3Z" fill="black"/><path d="M21 22H3C2.4 22 2 21.6 2 21V3C2 2.4 2.4 2 3 2H21C21.6 2 22 2.4 22 3V21C22 21.6 21.6 22 21 22ZM11.7 15.7L17.7 9.70001C18.1 9.30001 18.1 8.69999 17.7 8.29999C17.3 7.89999 16.7 7.89999 16.3 8.29999L11 13.6L7.70001 10.3C7.30001 9.89999 6.69999 9.89999 6.29999 10.3C5.89999 10.7 5.89999 11.3 6.29999 11.7L10.3 15.7C10.5 15.9 10.8 16 11 16C11.2 16 11.5 15.9 11.7 15.7Z" fill="black"/></svg></span>&nbsp;Save&nbsp;</span><span class="indicator-progress">Please wait...<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span></button>');
                $("#on_editing").val(1); 
                $("#doc_detail_id").val(id); 
                $(".detailItemS").prop("disabled", true); 
                $(".detailItemS").css("cursor", "no-drop");   
                $(".detailItemS-"+id).prop("disabled", false);
                $(".detailItemS-"+id).css("cursor", "auto");
                $(".detailItemS-"+id).css("color", "red"); 
                $(".detailItemS-"+id).show();  
            }  
            $("#"+id).focus() ; 
        } 
        var bal_po =  parseInt($("#bal_"+id).val().replace(/\,/g,'')) ;   
        var bal_be =  parseInt($("#balbe_"+id).val().replace(/\,/g,'')) ;   
        var qty_di =  parseInt($("#"+id).val().replace(/\,/g,'')) ;    
        var qty_di = (isNaN(qty_di) ? 0 : qty_di) ;
        console.log(qty_di);
        var bal_di = parseInt(bal_be - qty_di).toFixed(0) ;      
        if (bal_di<0) {
            $("#result_"+id).val(0) ; 
            $("#"+id).val(bal_be) ; 
        } else {
            $("#result_"+id).val(bal_di) ; 
        } 
        $(".number_format_coma").priceFormat({
            prefix: '',
            centsSeparator: '.',
            thousandsSeparator: ',',
            allowNegative: true,
            centsLimit: 0  
         })   
         $("#"+id).focus() ; 
     };
 
     function cancelDetailUpdate() {
        $("th").click();
        $("#on_editing").val(0); 
        $("#doc_detail_id").val("");  
        $(".btn-detail-update").remove();     
     }  

     function packForm(event, id) {           
        var token = $("[name=_token]").val();      
        var string = "&_token="+token+"&ref_doc="+id ; 
        $.ajax({
            type	: 'POST',
            url    : "{{ route('delcon.get_ref_doc_id') }}",
            data	: string,
            cache	: false,
            dataType : 'json',
            success	: function(response){  
               if (response.process == 1) {
                $("#kt_modal_pack").modal('show'); 
                $("#ref_doc_pack").val(response.id);
                tag_lable_table()
               } else {
                Toast.fire({
                    icon: 'error',
                    title: 'Please reload and try again'
                })
               }
                
            }, error: function( jqXHR, textStatus ) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Please reload and try again'
                    }) 
                }
        })
        
    }; 

    function tag_lable_table() {   
        $.fn.dataTable.ext.errMode = 'none';        
        var transaction_table_print = $('#kt_packaging_table').DataTable({
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
                        }],
            ajax: {
                url: "{{ route('delcon.tag_lable_table') }}",
                type: 'POST',
                data	: function ( d ) { d._token = $("[name=_token]").val(), d.ref_doc = $("#ref_doc_pack").val() ; }, 
                cache	: false,
                dataType : 'json'
            },
            columns: [
                { data: 'no', className: 'text-center' }, 
                { data: 'sn' }, 
                { data: 'qty', className: 'text-end'},
                { data: 'action', className: 'text-end' } 
            ]
            })
            transaction_table_print.ajax.reload();   
    } 
 
    function cancelHeadUpdate () {    
        var token = $("[name=_token]").val(); 
        var ref_doc = $("#ref_doc").val(); 
        var ref_doc_po = $("#ref_doc_po").val();
        $("#cancel-head-update").attr("data-kt-indicator", "on");
        $("#cancel-head-update").attr("disabled", "disabled"); 
        var string = "&_token="+token+"&ref_doc="+ref_doc+"&ref_doc_po="+ref_doc_po ; 
        $.ajax({
            url	: "{{ route('delcon.get_head_properties') }}",
            type	: 'POST', 
            data	: string, 
            typeData : 'json',  
            success: function (response) {   
                var response = JSON.parse(response) 
                $("#input_docnum").val(response.docnum)
                $("#input_docdate").val(response.docdate)
                $("#input_shipnum").val(response.shipnum)
                $("#input_shipdate").val(response.shipdate)
                $("#input_remark").val(response.remark)
                $(".btn-head-update").remove();     
                $(".div-head-btn").css("display", "")
            }
        })
    }

    function saveHead () {    
        var token = $("[name=_token]").val();  
        var header_form = $("#header_form").serialize();  
        if ($("#input_shipnum").val().length == 0) {
            Toast.fire({ icon: 'error', title: 'Ship Num. is required' });
            return false ;
        }
        if ($("#input_shipdate").val().length == 0) {
            Toast.fire({ icon: 'error', title: 'Ship Date is required' });
            return false ;
        }
        $("#save-head-update").attr("data-kt-indicator", "on");
        $("#save-head-update").attr("disabled", "disabled"); 
        var string = "&_token="+token+"&"+header_form ; 
        $.ajax({
            url	: "{{ route('delcon.store_head') }}",
            type	: 'POST', 
            data	: string, 
            typeData : 'json',  
            success: function (response) {   
                var response = JSON.parse(response) 
                if (response.process==1) {
                    Toast.fire({
                        icon: 'success',
                        title: response.msg
                    })
                    $(".btn-head-update").remove();     
                    $(".div-head-btn").css("display", "")
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: response.msg
                    })
                    $("#save-head-update").removeAttr("data-kt-indicator", "on");
                    $("#save-head-update").removeAttr("disabled", "disabled"); 
                }
            }, error: function( jqXHR, textStatus ) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Please reload and try again'
                    })
                    console.clear();
                }
        })
    } 
  

</script>

@endsection