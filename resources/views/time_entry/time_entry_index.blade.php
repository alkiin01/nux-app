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
        $('input[type=number]').on('wheel', function (e) {
                    $(this).blur(); // Menghilangkan fokus agar tidak scroll
                });
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
                </h1> ˚
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
                <div class="post d-flex flex-column-fluid mb-0" id="kt_post"> 
                    <div id="kt_content_container" class="container-xxl">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6 border-1">
                                        <a href="#" onclick="docSearch(0, this);" class="card bgi-no-repeat card-xl-stretch mb-5 card-front" 
                                        style="background-position: right top; background-size: 30% auto; background-image: 
                                        url(<?= env('APP_ASSETS') ?>assets/media/svg/shapes/abstract-4.svg); border:solid 1px #e4e6ef; box-shadow: 0 4px 8px rgba(128, 128, 128, 0.5);">
                                            <div class="card-body">  
                                                <div class="text-gray-900 fw-bolder fs-4 mb-2 mt-2" id="total_document"></div>
                                                <div class="fw-bold text-gray-900">All Document</div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <a href="#" onclick="docSearch(1, this);" class="card bgi-no-repeat card-xl-stretch mb-5 card-front" 
                                        style="background-position: right top; background-size: 30% auto; background-image: url(<?= env('APP_ASSETS') ?>assets/media/svg/shapes/abstract-2.svg); border:solid 1px #e4e6ef; box-shadow: 0 4px 8px rgba(128, 128, 128, 0.5);">
                                            <div class="card-body"> 
                                                <div class="text-gray-900 fw-bolder fs-4 mb-2 mt-2" id="total_draft"></div>
                                                <div class="fw-bold text-gray-900">Draft</div>
                                            </div> 
                                        </a> 
                                    </div>  
                                </div> 
                             
                                <hr />
                            
                                <div class="row" id="form"> 
                                    <div class="col-md-6 mb-5 mt-5">
                                        <form>
                                            <div> 
                                                <div class="form-group mb-3">
                                                    <label class="mb-2 text-sm">JO Date <span class="text-danger">*</span></label>
                                                    <input type="date" class="form-control bg-light-primary" id="JoDate" name="JoDate" value="<?php date_default_timezone_set('Asia/Jakarta'); echo date('Y-m-d');  ?>"/> 
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label class="mb-2 text-sm">Category <span class="text-danger">*</span></label> 
                                                    <select class="form-select form-select-solid" data-kt-select2="true" data-allow-clear="false" id="LineCategory" name="LineCategory" data-hide-search="true"> 
                                                        <option value='STP' selected>Stamping</option> 
                                                        <option value='ASSY'>Assy</option>  
                                                        <option value='PPIC'>Shearing</option>  
                                                        <option value='ENG'>Engineering</option>  
                                                    </select> 
                                                </div>  
                                                <div class="form-group mb-3">
                                                    <label class="mb-2 text-sm">Shift</label>
                                                    <select class="form-select form-select-solid text-sm" data-kt-select2="true"  data-allow-clear="true" id="ShiftID" name="ShiftID" data-hide-search="true">  
                                                    </select>  
                                                </div> 
                                            </div> 
                                        </form> 
                                    </div>

                                    <div class="col-md-6  mt-5">
                                        <form>
                                            <div>  
                                                <div class="form-group mb-3"> 
                                                    <label class="mb-2 text-sm">Line <span class="text-danger">*</span></label> 
                                                    <select class="form-select form-select-solid" data-kt-select2="true" data-placeholder="Select option" data-allow-clear="false" id="ResourceGroupID" name="ResourceGroupID" data-hide-search="false"/>  
                                                    </select> 
                                                </div>  

                                                <div class="form-group mb-3"> 
                                                    <label class="mb-2 text-sm">Machine</label> 
                                                    <select class="form-select form-select-solid" data-kt-select2="true" data-placeholder="Select option" data-allow-clear="true" id="ResourceID" name="ResourceID" data-hide-search="false"/>  
                                                    </select> 
                                                </div>  

                                                <div class="form-group mb-3"> 
                                                    <label class="mb-2 text-sm">Operator Name</label> 
                                                    <select class="form-select form-select-solid" data-kt-select2="true" data-placeholder="Select option" data-allow-clear="true" id="EmployeeID" name="EmployeeID" data-hide-search="false"/>  
                                                    </select> 
                                                </div>  

                                            </div> 
                                        </form> 
                                    </div>

                                    <div class="pt-5 pb-5"> 
                                        <hr style="color: gray">
                                        <button type="button" class="btn btn-primary btn-border btn-outline-primary btn-sm" id="btn_submit_search" onclick="submit_search()">
                                            <span id="svg_submit_search" class="svg-icon svg-icon-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <defs/>
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24"/>
                                                        <path d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                                        <path d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z" fill="#000000" fill-rule="nonzero"/>
                                                    </g>
                                                </svg>
                                            </span> 
                                            <span id="spinner_submit_search" class="spinner-border spinner-border-sm svg-icon svg-icon-2" style="display: none;"></span>       
                                            <span id="btn_text_submit_search">Submit</span>
                                        </button>
                                        
                                        <!-- <button type="button" class="btn btn-primary btn-border btn-outline-primary btn-sm" id="btn_export_production_sch" onclick="export_production_sch()">
                                            <span id="svg_export_production_sch" class="svg-icon svg-icon-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"> 
                                                    <defs/>
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <polygon points="0 0 24 0 24 24 0 24"/>
                                                        <path d="M5.74714567,13.0425758 C4.09410362,11.9740356 3,10.1147886 3,8 C3,4.6862915 5.6862915,2 9,2 C11.7957591,2 14.1449096,3.91215918 14.8109738,6.5 L17.25,6.5 C19.3210678,6.5 21,8.17893219 21,10.25 C21,12.3210678 19.3210678,14 17.25,14 L8.25,14 C7.28817895,14 6.41093178,13.6378962 5.74714567,13.0425758 Z" fill="#000000" opacity="0.3"/>
                                                        <path d="M11.1288761,15.7336977 L11.1288761,17.6901712 L9.12120481,17.6901712 C8.84506244,17.6901712 8.62120481,17.9140288 8.62120481,18.1901712 L8.62120481,19.2134699 C8.62120481,19.4896123 8.84506244,19.7134699 9.12120481,19.7134699 L11.1288761,19.7134699 L11.1288761,21.6699434 C11.1288761,21.9460858 11.3527337,22.1699434 11.6288761,22.1699434 C11.7471877,22.1699434 11.8616664,22.1279896 11.951961,22.0515402 L15.4576222,19.0834174 C15.6683723,18.9049825 15.6945689,18.5894857 15.5161341,18.3787356 C15.4982803,18.3576485 15.4787093,18.3380775 15.4576222,18.3202237 L11.951961,15.3521009 C11.7412109,15.173666 11.4257142,15.1998627 11.2472793,15.4106128 C11.1708299,15.5009075 11.1288761,15.6153861 11.1288761,15.7336977 Z" fill="#000000" fill-rule="nonzero" transform="translate(11.959697, 18.661508) rotate(-270.000000) translate(-11.959697, -18.661508) "/>
                                                    </g>
                                                </svg> 
                                            </span> 
                                            <span id="spinner_export_production_sch" class="spinner-border spinner-border-sm svg-icon svg-icon-2" style="display: none;"></span>       
                                            <span id="btn_text_export_production_sch">Download</span>
                                        </button>  -->

                                    </div>
                                </div> 
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
                                            <input type="text" data-kt-goodreceive-table-filter="search" id="front_table_search" class="form-control form-control-solid w-250px ps-15 text-sm form-control-sm" placeholder="Search JO/PartNum" />
                                        </div> 
                                    </div>  
                                    
                                    <div class="card-toolbar">  
                                        <button type="button" class="btn btn-primary btn-sm me-3" id="btn_add_document" onclick="add_document()">
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
                                        </button>  
                                    </div>
                                </div>  
                                <div class="card-body pt-0"> 
                                    <table class="table align-middle table-row-dashed table-striped gy-2 fs-7" id="kt_doc_table">
                                        <thead>
                                            <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                                <th class="min-w-20px pe-2">No</th>
                                                <th class="min-w-20px">Open</th>  
                                                <th class="min-w-100px">JONum</th> 
                                                <th class="min-w-100px">Product</th>  
                                                <th class="min-w-20px">Line</th>  
                                                <th class="min-w-20px">Plan</th> 
                                                <th class="min-w-20px">Act</th> 
                                                <th class="min-w-20px">Receive</th>  
                                            </tr> 
                                        </thead>  
                                        <tfoot>
                                            <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                                <th class="min-w-20px pe-2">No</th>
                                                <th class="min-w-20px">Open</th>  
                                                <th class="min-w-100px">JONum</th> 
                                                <th class="min-w-100px">Product</th>  
                                                <th class="min-w-20px">Line</th>  
                                                <th class="min-w-20px">Plan</th> 
                                                <th class="min-w-20px">Act</th> 
                                                <th class="min-w-20px">Receive</th>  
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
                             <div id="div-form-time-entry"></div>
                        </div>
                    </div>
                </div>   
            </div>  
        </div>  

        <input type="text" hidden id="laborHedSeq">  
        <input type="text" hidden id="laborDtlSeq">   
        <input type="text" hidden id="isCopart" value="0">
        <input type="text" hidden id="createNew" value="0">
        <input type="text" hidden id="TimeStatus" value="0">

     <script>   

        function open_document(laborHedSeq, laborDtlSeq, PartNum, ShiftID, Shift, ClockInDate, EmployeeNum, EmployeeName, InptJobNum, InptJobNumDesc, LaborType, LaborTypeDesc, LaborHrs, ClockInTime, ClockOutTime, isCopart, IndirectCode, IndirectCodeDesc, PayHours, no) {
            var button = document.getElementById('btn_form_view_doc_'+no);
            var svg = document.getElementById('svg_form_view_doc_'+no);
            var spinner = document.getElementById('spinner_form_view_doc_'+no); 

            svg.style.display = 'none';
            spinner.style.display = 'inline-block'; 
            button.disabled = true; 

            $("#div-form-time-entry").html("");
            var token = $("[name=_token]").val(); 

            $("#laborHedSeq").val(laborHedSeq); 
            $("#laborDtlSeq").val(laborDtlSeq);  
            $("#isCopart").val(isCopart); 

            var string = "&_token="+token ;
            $.ajax({
                type	: 'POST',
                url	: "{{ route('time_entry.add_document') }}",
                data	: string,
                cache	: false, 
                success : function(data){   
                    svg.style.display = 'inline-block';
                    spinner.style.display = 'none'; 
                    button.disabled = false;  
                    document.getElementById('kt_activity_preview_tab').click() ; 
                    $("#div-form-time-entry").html(data);  
                    $("#lds-roller-form").css("display", "");  
                    $("#form_label").css("display", "none");   
                    $("#form_loader").css("display", ""); 
                    setTimeout (function() {
                        $("#form_label").css("display", "");   
                        $("#form_loader").css("display", "none");   
                        $("#lds-roller-form").css("display", "none");   
                        
                        $("#InptPayHours").val(PayHours);   
                        $("#InptLaborHrs").val(LaborHrs);   
                        $("#InptClockIn").val(ClockInTime);   
                        $("#InptClockOut").val(ClockOutTime);   

                        var newEmployee = new Option(EmployeeName, EmployeeNum, true, true);     
                        $('#InptEmployeeID').append(newEmployee).trigger('change');

                        var newShift = new Option(Shift, ShiftID, true, true);     
                        $('#InptShiftID').append(newShift).trigger('change');

                        var newJobNum = new Option(InptJobNumDesc, InptJobNum, true, true);     
                        $('#InptJobNum').append(newJobNum).trigger('change');

                        var newLaborType = new Option(LaborTypeDesc, LaborType, true, true);     
                        $('#InptlaborTypePseudo').append(newLaborType).trigger('change');

                        $("#createNew").val(1) ;

                        var newPartNum = new Option(PartNum, PartNum, true, true);     
                        $('#InptPartNum').append(newPartNum).trigger('change'); 

                        var newInptIndirectCode = new Option(IndirectCodeDesc, IndirectCode, true, true);     
                        $('#InptIndirectCode').append(newInptIndirectCode).trigger('change');

                        setTimeout (function() { 
                            check_document_status(); 
                        },800);
                    },500) 
                },
                error: function( jqXHR, textStatus ) {
                    svg.style.display = 'inline-block';
                    spinner.style.display = 'none';
                    buttonText.textContent = 'Create'; 
                    button.disabled = false;  
                    Toast.fire({
                        position: 'top-end',
                        title: " Please reload and try again! ",
                        icon:"error"
                    }) 
                }
            }) 
        }
 
        function form_view_doc(laborHedSeq, laborDtlSeq, PartNum, ShiftID, Shift, ClockInDate, EmployeeNum, EmployeeName, InptJobNum, InptJobNumDesc, LaborType, LaborTypeDesc, LaborHrs, ClockInTime, ClockOutTime, no) {   
                
        };   

        function add_document() {
            var button = document.getElementById('btn_add_document');
            var svg = document.getElementById('svg_add_document');
            var spinner = document.getElementById('spinner_add_document');
            var buttonText = document.getElementById('btn_text_add_document'); 
            svg.style.display = 'none';
            spinner.style.display = 'inline-block';
            buttonText.textContent = 'Please Wait...'; 
            button.disabled = true;    
            $("#div-form-time-entry").html("");
            var token = $("[name=_token]").val(); 

            $("#laborHedSeq").val(""); 
            $("#laborDtlSeq").val("");  
            $("#isCopart").val(0); 

            var string = "&_token="+token ;
            $.ajax({
                type	: 'POST',
                url	: "{{ route('time_entry.add_document') }}",
                data	: string,
                cache	: false, 
                success : function(data){   
                    svg.style.display = 'inline-block';
                    spinner.style.display = 'none';
                    buttonText.textContent = 'Create'; 
                    button.disabled = false; 
                    document.getElementById('kt_activity_preview_tab').click() ; 
                    $("#div-form-time-entry").html(data);  
                    $("#lds-roller-form").css("display", "");  
                    $("#form_label").css("display", "none");   
                    $("#form_loader").css("display", ""); 
                    setTimeout (function() {
                        $("#form_label").css("display", "");   
                        $("#form_loader").css("display", "none");   
                        $("#lds-roller-form").css("display", "none");   
                        check_document_status();
                        $("#createNew").val(1) ;
                    },500) 
                },
                error: function( jqXHR, textStatus ) {
                    svg.style.display = 'inline-block';
                    spinner.style.display = 'none';
                    buttonText.textContent = 'Create'; 
                    button.disabled = false;  
                    Toast.fire({
                        position: 'top-end',
                        title: " Please reload and try again! ",
                        icon:"error"
                    }) 
                }
            }) 
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
            $("#laborHedSeq").val(""); 
            $("#laborDtlSeq").val("");  
            $("#isCopart").val(0); 
            $("#createNew").val(0) ;
            refresh_front_table(); 
            setTimeout(function(){
                svg.style.display = 'inline-block';
                spinner.style.display = 'none';
                buttonText.textContent = 'Back'; 
                button.disabled = false; 
                document.getElementById('kt_activity_home_tab').click(); 
            }, 300) 
        }  
 
        
        $(function(){  
            $('#LineCategory').change(function(){
                var newOption = new Option('Select option', '0', true, true);     
                $('#ResourceGroupID').append(newOption).trigger('change'); 
            })

            $('#ResourceGroupID').change(function(){
                var newOption = new Option('Select option', '0', true, true);     
                $('#ResourceID').append(newOption).trigger('change'); 
            })

            $('#ResourceID').select2({
                ajax: {
                    type: 'POST',
                    url: "{{ route('time_entry.get_resource') }}",
                    dataType: 'json',
                    delay: 250, 
                    data: function(params) {
                        var query = {
                            search: params.term,  
                            line: $("#ResourceGroupID").val(),
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

            $('#ShiftID').select2({
                ajax: {
                    type: 'POST',
                    url: "{{ route('time_entry.get_shift_list') }}",
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

            $('#EmployeeID').select2({
                ajax: {
                    type: 'POST',
                    url: "{{ route('time_entry.get_employee_list') }}",
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

            $('#ResourceGroupID').select2({
                ajax: {
                    type: 'POST',
                    url: "{{ route('time_entry.get_resource_group') }}",
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
        });
    </script>

<script>    
     
    $(document).ready(function () {
        front_table() ;  getCountDocument() ;
    })

    function getCountDocument() {
        var token = $("[name=_token]").val(); 
        var JoDate = $("#JoDate").val();
        var LineCategory = $("#LineCategory").val();
        var ResourceGroupID = $("#ResourceGroupID").val();
        var ResourceID = $("#ResourceID").val();
        var ShiftID = $("#ShiftID").val();
        var EmployeeID = $("#EmployeeID").val();
        var string = "&_token="+token+"&JoDate="+JoDate+"&LineCategory="+LineCategory+"&ResourceGroupID="+ResourceGroupID+"&ResourceID="+ResourceID+"&ShiftID="+ShiftID+"&EmployeeID="+EmployeeID ;
        $.ajax({
            type	: 'POST',
            url	: "{{ route('time_entry.get_count_document') }}",
            data	: string,
            cache	: false,
            dataType	: 'json',
            success : function(data){   
                $("#total_draft").text(data.total_draft+" Document"); 
                $("#total_document").text(data.total_document+" Document");
            } 
        }) 
    }

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
                url: "{{ route('time_entry.front_table') }}",
                type: 'POST',
                data: function (d) { 
                    d._token = $("[name=_token]").val();
                    d.JoDate = $("#JoDate").val();
                    d.LineCategory = $("#LineCategory").val();
                    d.ResourceGroupID = $("#ResourceGroupID").val();
                    d.ResourceID = $("#ResourceID").val();
                    d.ShiftID = $("#ShiftID").val();
                    d.EmployeeID = $("#EmployeeID").val();
                    d.front_table_search = $("#front_table_search").val();
                }, 
                cache: false,
                dataType: 'json'
            },
            columns: [
                { data: 'no', className: 'text-center' },
                {
                    data: 'action', 
                    className: 'text-center',
                    orderable: false
                }, 
                { data: 'JobNum' },
                { data: 'PartNum' },
                { data: 'ResourceID' }, 
                { data: 'PlanQty' },
                { data: 'QtyCompleted' },
                { data: 'ReceivedQty' } 
            ],
            initComplete: function(settings, json) {
                getCountDocument();
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

        return true ;
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
</script> 
@endsection