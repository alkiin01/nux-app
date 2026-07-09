<div class="col-xxl-12">    
        <div id="form_loader" style="text-align: center;">
            <div class="lds-roller mt-10 mb-10" id="lds-roller-form"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div> 
        </div> 
        <div id="form_label">  
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Header</div>
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
                            <span id="btn_text_add_document">Add New</span>
                        </button>  

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
                </div>
                <div class="card-body">
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <form id="form-segment-1"> 
                                <div class="form-group mb-3">
                                    <label>Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="InptActualClockinDate" name="InptActualClockinDate" value="<?php date_default_timezone_set('Asia/Jakarta'); echo date('Y-m-d');  ?>"/> 
                                </div> 
                                <div class="form-group mb-3">
                                    <label>Employee <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-solid text-sm" data-kt-select2="true"  data-allow-clear="false" id="InptEmployeeID" name="InptEmployeeID" data-hide-search="true" onchange="save_header()">  
                                    </select>
                                </div>  
                            </form>
                        </div>   

                        <div class="col-md-6">
                            <form id="form-segment-2">   
                                <div class="form-group mb-3">
                                    <label>Actual Shift <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-solid text-sm" data-kt-select2="true"  data-allow-clear="false" id="InptShiftID" name="InptShiftID" data-hide-search="true" onchange="save_header()">    
                                    </select>   
                                </div>  
                                <div class="form-group mb-3">
                                    <label>Pay Hours <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control bg-light-primary" id="InptPayHours" name="InptPayHours" readonly/>  
                                </div>   
                            </form>
                        </div> 
                    </div> 
                </div> 
            </div>
 
            <div class="card mt-5"> 
                <div class="card-header">
                    <div class="card-title"> 
                        <div class="d-flex align-items-center position-relative my-1"> 
                            <div id="div-btn-save"></div> 
                        </div> 
                    </div> 

                    <div class="card-toolbar">   
                        <div id="div-btn-submit"></div>  
                        &nbsp; 
                        <div id="div-btn-delete"></div>   
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <form id="form-segment-3"> 

                                <div class="form-group mb-3">
                                    <label>Production Type <span class="text-danger">*</span></label> 
                                    <select class="form-select form-select-solid text-sm bg-light" id="InptlaborTypePseudo" name="InptlaborTypePseudo"  data-kt-select2="true" data-allow-clear="false" data-hide-search="true" onchange="set_labor_type()"/>
                                        <option value="P" selected>Production</option>
                                        <option value="I">Indirect</option>
                                        <!-- <option value="S">Setup</option>
                                        <option value="P">Project</option>
                                        <option value="V">Service</option> -->
                                    </select> 
                                </div> 

                                <div class="form-group mb-3">
                                    <label>Job Number <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-solid text-sm bg-light" id="InptJobNum" name="InptJobNum" data-kt-select2="true" data-allow-clear="false" data-hide-search="true" onchange="set_jobnum()">
                                    </select> 
                                </div>  

                                <div class="form-group mb-3">
                                    <label>Part Num <span class="text-danger">*</span></label> 
                                    <select class="form-select form-select-solid text-sm bg-light" id="InptPartNum" name="InptPartNum"  data-kt-select2="true" data-allow-clear="false" data-hide-search="true" onchange="set_partnum(this.value)"/></select> 
                                </div>  

                                <div class="form-group mb-3"> 
                                    <label>Part Name</label>
                                    <input type="text" class="form-control bg-light-primary" id="InptPartName" name="InptPartName" readonly />
                                </div>  
                            </form>
                        </div>

                        <div class="col-md-6">
                            <form id="form-segment-4"> 
                                <div class="form-group mb-3">
                                    <label>Qty Plan</label>
                                    <input type="number" class="form-control bg-light-primary" id="InptQtyPlan" name="InptQtyPlan" readonly/> 
                                </div>
                                <div class="form-group mb-3">
                                    <label>Model <span class="text-danger">*</span></label> 
                                    <input type="text" class="form-control bg-light-primary" id="InptModel" name="InptModel" readonly/>  
                                </div>
                                <div class="form-group mb-3">
                                    <label>Category <span class="text-danger">*</span></label> 
                                    <input type="text" class="form-control bg-light-primary" id="InptLineCategory" name="InptLineCategory" readonly/>  
                                </div>
                                <div class="form-group mb-3">
                                    <label>Indirect Code <span class="text-danger">*</span></label> 
                                    <select class="form-select form-select-solid text-sm bg-light" id="InptIndirectCode" name="InptIndirectCode"  data-kt-select2="true" data-allow-clear="true" data-hide-search="true" onchange="set_indirect_job()"/></select> 
                                </div> 
                            </form>
                        </div> 
                    </div>
                    <hr>  

                    <div class="row mt-5 mb-5">
                        <div class="col-md-6">
                            <form id="form-segment-5">   
                                <div class="form-group mb-3">
                                    <label>Clock In <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" id="InptClockIn" name="InptClockIn"/> 
                                </div> 
                            </form>
                        </div>

                        <div class="col-md-6">
                            <form id="form-segment-6">  
                                <div class="form-group mb-3">
                                    <label>Clock Out <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" id="InptClockOut" name="InptClockOut"/> 
                                </div>  
                            </form>
                        </div> 
                    
                        <div class="col-md-6">
                            <form id="form-segment-7"> 
                                <div class="form-group mb-3">
                                    <label>Labor Qty <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="InptLaborQty" name="InptLaborQty"/> 
                                </div>
                                <div class="form-group mb-3">
                                    <label>Scrap Qty</label>
                                    <input type="number" class="form-control" id="InptScrapQty" name="InptScrapQty" oninput="validateQtyScrap(this)"/> 
                                    <script>
                                      function validateQtyScrap(input) {
                                            const inputQty = document.getElementById('InptScrapQty').value ; 
                                            if (inputQty <= 0) {
                                                var newOption = new Option('Select option', null, true, true);     
                                                $('#InptScrapReasonCode').append(newOption).trigger('change'); 
                                            }
                                        }; 
                                    </script>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Non Conform Qty</label>
                                    <input type="number" class="form-control" id="InptDiscrepQty" name="InptDiscrepQty" oninput="validateQtyDiscrep(this)"/> 
                                    <script>
                                      function validateQtyDiscrep(input) {
                                            const inputQty = document.getElementById('InptDiscrepQty').value ; 
                                            if (inputQty <= 0) {
                                                var newOption = new Option('Select option', null, true, true);     
                                                $('#InptDiscrpRsnCode').append(newOption).trigger('change'); 
                                            }
                                        }; 
                                    </script>
                                </div> 
                            </form> 
                        </div>

                        <div class="col-md-6">
                            <form id="form-segment-8">    
                                <div class="form-group row mb-3">
                                    <label>Labor Hrs <span class="text-danger">*</span></label>
                                    <div class="col-9">
                                        <input type="text" class="form-control col-2" id="InptLaborHrs" name="InptLaborHrs" oninput="validateDecimal(this)"/> 
                                        <script>
                                            function validateDecimal(input) { 
                                                input.value = input.value.replace(/[^0-9.]/g, ''); 
                                                const parts = input.value.split('.');
                                                if (parts.length > 2) {
                                                    input.value = parts[0] + '.' + parts.slice(1).join('');
                                                }
                                                if (parts[1] && parts[1].length > 2) {
                                                    input.value = parts[0] + '.' + parts[1].substring(0, 2);
                                                }
                                            }
                                        </script>
                                    </div>
                                    <div class="col-3">
                                        <button type="button" class="btn btn-primary" id="btn_change_time" onclick="change_time()" style="width: 100%;">
                                            <span id="svg_change_time" class="svg-icon svg-icon-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"> 
                                                    <defs/>
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24"/>
                                                        <rect fill="#000000" opacity="0.3" x="7" y="4" width="10" height="4"/>
                                                        <path d="M7,2 L17,2 C18.1045695,2 19,2.8954305 19,4 L19,20 C19,21.1045695 18.1045695,22 17,22 L7,22 C5.8954305,22 5,21.1045695 5,20 L5,4 C5,2.8954305 5.8954305,2 7,2 Z M8,12 C8.55228475,12 9,11.5522847 9,11 C9,10.4477153 8.55228475,10 8,10 C7.44771525,10 7,10.4477153 7,11 C7,11.5522847 7.44771525,12 8,12 Z M8,16 C8.55228475,16 9,15.5522847 9,15 C9,14.4477153 8.55228475,14 8,14 C7.44771525,14 7,14.4477153 7,15 C7,15.5522847 7.44771525,16 8,16 Z M12,12 C12.5522847,12 13,11.5522847 13,11 C13,10.4477153 12.5522847,10 12,10 C11.4477153,10 11,10.4477153 11,11 C11,11.5522847 11.4477153,12 12,12 Z M12,16 C12.5522847,16 13,15.5522847 13,15 C13,14.4477153 12.5522847,14 12,14 C11.4477153,14 11,14.4477153 11,15 C11,15.5522847 11.4477153,16 12,16 Z M16,12 C16.5522847,12 17,11.5522847 17,11 C17,10.4477153 16.5522847,10 16,10 C15.4477153,10 15,10.4477153 15,11 C15,11.5522847 15.4477153,12 16,12 Z M16,16 C16.5522847,16 17,15.5522847 17,15 C17,14.4477153 16.5522847,14 16,14 C15.4477153,14 15,14.4477153 15,15 C15,15.5522847 15.4477153,16 16,16 Z M16,20 C16.5522847,20 17,19.5522847 17,19 C17,18.4477153 16.5522847,18 16,18 C15.4477153,18 15,18.4477153 15,19 C15,19.5522847 15.4477153,20 16,20 Z M8,18 C7.44771525,18 7,18.4477153 7,19 C7,19.5522847 7.44771525,20 8,20 L12,20 C12.5522847,20 13,19.5522847 13,19 C13,18.4477153 12.5522847,18 12,18 L8,18 Z M7,4 L7,8 L17,8 L17,4 L7,4 Z" fill="#000000"/>
                                                    </g>
                                                </svg>
                                            </span>     
                                            <span id="spinner_change_time" class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;"></span>     
                                        </button>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label>Actual Resource Grp <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-solid text-sm" data-kt-select2="true"  data-allow-clear="false" id="InptResourceGrpID" name="InptResourceGrpID" data-hide-search="true" />    
                                    </select>   
                                </div>

                                <div class="form-group mb-3">
                                    <label>Actual Resource <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-solid text-sm" data-kt-select2="true"  data-allow-clear="false" id="InptResourceID" name="InptResourceID" data-hide-search="true"/>    
                                    </select>   
                                </div>  
                            </form> 
                        </div>
                    </div>  
                    <hr>

                    <div class="row mb-5">
                        <div class="col-md-6">
                            <form id="form-segment-9"> 
                                <div class="form-group mb-3">
                                    <label>Scrap Reason Code <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-solid text-sm bg-light" id="InptScrapReasonCode" name="InptScrapReasonCode" data-kt-select2="true" data-allow-clear="false" data-hide-search="true"/>
                                    </select> 
                                </div>  
                            </form>
                        </div>

                        <div class="col-md-6">
                            <form id="form-segment-10"> 
                                <div class="form-group mb-3">
                                    <label>Discrapent Reason Code <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-solid text-sm bg-light" 
                                            id="InptDiscrpRsnCode" name="InptDiscrpRsnCode" 
                                            data-kt-select2="true"  
                                            data-allow-clear="false" 
                                            data-hide-search="true"/>
                                    </select> 
                                </div> 
                            </form>
                        </div>

                        <div class="col-md-12">
                            <form> 
                                <div class="form-group mb-3">
                                    <label>Labor Note <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="InptLaborNote" id="InptLaborNote"></textarea> 
                                </div>   

                            </form>
                        </div>

                    </div>  
                    <hr/> 
                </div> 
            </div>  
    </div>   
</div>   

    

<script>  
    function check_document_status() {
        var token = $("[name=_token]").val(); 
        var laborHedSeq = $("#laborHedSeq").val();     
        var laborDtlSeq = $("#laborDtlSeq").val();      
        var string = "&_token=" + token + "&laborHedSeq=" + laborHedSeq + "&laborDtlSeq=" + laborDtlSeq  ;   
        $.ajax({
            type	: 'POST',
            url	: "{{ route('time_entry.check_document_status') }}",
            data	: string,
            cache	: false,
            dataType	: 'json',
            success : function(data) {   
                $("#div-btn-save").html(data.buttonSave);
                $("#div-btn-submit").html(data.buttonSubmit);
                $("#div-btn-delete").html(data.buttonDelete); 
                $("#TimeStatus").val(data.TimeStatus);  
            },
            error: function( jqXHR, textStatus ) {
                $("#div-btn-save").html('');
                $("#div-btn-submit").html('');
                $("#div-btn-delete").html('');
                Toast.fire({
                    position: 'top-end',
                    title: " Please check all field! ",
                    icon:"error"
                }) 
            }
        }) 
    }

    function submit_form() {
        var token = $("[name=_token]").val(); 
        var laborHedSeq = $("#laborHedSeq").val();     
        var laborDtlSeq = $("#laborDtlSeq").val();   
        var InptJobNum = $("#InptJobNum").val();   
        
        var button = document.getElementById('btn_submit_form');
        var svg = document.getElementById('svg_submit_form');
        var spinner = document.getElementById('spinner_submit_form');
        var buttonText = document.getElementById('btn_text_submit_form'); 
        var InptJobNum = InptJobNum.replace(/ /g, '_SPACE_');
        svg.style.display = 'none' ;
        spinner.style.display = 'inline-block' ;
        buttonText.textContent = 'Please Wait...' ; 
        button.disabled = true ; 

        var string = "&_token=" + token + "&laborHedSeq=" + laborHedSeq + "&laborDtlSeq=" + laborDtlSeq + "&InptJobNum=" + InptJobNum  ;   
        $.ajax({
            type	: 'POST',
            url	: "{{ route('time_entry.submit_form') }}",
            data	: string,
            cache	: false,
            dataType	: 'json',
            success : function(data) {   
                svg.style.display = 'inline-block';
                spinner.style.display = 'none';
                buttonText.textContent = 'Submit'; 
                button.disabled = false;  

                if (data.code == 200) {
                    check_document_status();
                } else {
                    Toast.fire({
                        position: 'top-end',
                        title: data.status,
                        icon: "error"
                    })
                }
            },
            error: function( jqXHR, textStatus ) {
                $("#div-btn-save").html('');
                $("#div-btn-submit").html('');
                Toast.fire({
                    position: 'top-end',
                    title: " Please check all field! ",
                    icon:"error"
                }) 
            }
        }) 
    }

    function recall_form() {
        var token = $("[name=_token]").val(); 
        var laborHedSeq = $("#laborHedSeq").val();     
        var laborDtlSeq = $("#laborDtlSeq").val();   
        
        var button = document.getElementById('btn_recall_form');
        var svg = document.getElementById('svg_recall_form');
        var spinner = document.getElementById('spinner_recall_form');
        var buttonText = document.getElementById('btn_text_recall_form'); 

        svg.style.display = 'none' ;
        spinner.style.display = 'inline-block' ;
        buttonText.textContent = 'Please Wait...' ; 
        button.disabled = true ; 

        var string = "&_token=" + token + "&laborHedSeq=" + laborHedSeq + "&laborDtlSeq=" + laborDtlSeq  ;   
        $.ajax({
            type	: 'POST',
            url	: "{{ route('time_entry.recall_form') }}",
            data	: string,
            cache	: false,
            dataType	: 'json',
            success : function(data) {   
                svg.style.display = 'inline-block';
                spinner.style.display = 'none';
                buttonText.textContent = 'Recall'; 
                button.disabled = false;  

                if (data.code == 200) {
                    check_document_status();
                } else {
                    Toast.fire({
                        position: 'top-end',
                        title: data.status,
                        icon: "error"
                    })
                }
            },
            error: function( jqXHR, textStatus ) {
                $("#div-btn-save").html('');
                $("#div-btn-submit").html('');
                Toast.fire({
                    position: 'top-end',
                    title: " Please check all field! ",
                    icon:"error"
                }) 
            }
        }) 
    }

    function change_time() {
        if($("#TimeStatus").val() == 1) {
            Toast.fire({
                position: 'top-end',
                title: "Silahkan direcall terlebih dulu",
                icon:"error"
            }) 
            return false ;
        }
        if($("#createNew").val() == 0) { return false ;} 
        var token = $("[name=_token]").val(); 
        var laborHedSeq = $("#laborHedSeq").val();     
        var laborDtlSeq = $("#laborDtlSeq").val();     
        var InptShiftID = $("#InptShiftID").val();     
        var shiftDescription = $("#InptShiftID").text();       
        var InptClockIn = $("#InptClockIn").val();       
        var InptClockOut = $("#InptClockOut").val();    
        var InptlaborTypePseudo = $("#InptlaborTypePseudo").val();       
        var string = "&_token=" + token + "&InptShiftID=" + InptShiftID + "&InptlaborTypePseudo=" + InptlaborTypePseudo + "&InptClockIn=" + InptClockIn + "&InptClockOut=" + InptClockOut + "&laborHedSeq=" + laborHedSeq + "&laborDtlSeq=" + laborDtlSeq + "&shiftDescription=" + shiftDescription ;   
         
        var button = document.getElementById('btn_change_time');
        var svg = document.getElementById('svg_change_time');
        var spinner = document.getElementById('spinner_change_time'); 
        svg.style.display = 'none' ;
        spinner.style.display = 'inline-block' ; 
        button.disabled = true ;  

        $.ajax({
            type	: 'POST',
            url	: "{{ route('time_entry.change_time') }}",
            data	: string,
            cache	: false,
            dataType	: 'json',
            success : function(data) {   
                if (data.code == 200) {
                    svg.style.display = 'inline-block';
                    spinner.style.display = 'none'; 
                    button.disabled = false;   
                    $("#InptLaborHrs").val(data.laborHrs);    
                } else {
                    svg.style.display = 'inline-block';
                    spinner.style.display = 'none'; 
                    button.disabled = false;    
                    Toast.fire({
                        position: 'top-end',
                        title: data.status,
                        icon: "error"
                    })
                }
            },
            error: function( jqXHR, textStatus ) {
                svg.style.display = 'inline-block';
                spinner.style.display = 'none'; 
                button.disabled = false;  
                Toast.fire({
                    position: 'top-end',
                    title: " Please check all field! ",
                    icon:"error"
                }) 
            }
        }) 
    } 
    
    function get_resorce_id() {  
        var token = $("[name=_token]").val(); 
        var InptJobNum = $("#InptJobNum").val();   
        var InptJobNum = InptJobNum.replace(/ /g, '_SPACE_');
        var string = "&_token=" + token + "&InptJobNum=" + InptJobNum ;  
            $.ajax({
                type : 'POST',
                url	: "{{ route('time_entry.get_jobnum_attr') }}",
                data	: string,
                cache	: false,
                dataType	: 'json',
                success : function(data) {  

                    var newDiscrpRsnCode = new Option("Select Option", null, true, true);     
                    $('#InptScrapReasonCode').append(newDiscrpRsnCode).trigger('change');  

                    var newScrapReasonCode = new Option("Select Option", null, true, true);     
                    $('#InptDiscrpRsnCode').append(newScrapReasonCode).trigger('change'); 

                    var newPartNum = new Option(data.PartNum, data.PartNum, true, true);     
                    $('#InptPartNum').append(newPartNum).trigger('change');
                     
                    if ($("#InptIndirectCode").val() === null || $("#InptIndirectCode").val() == 'null') {
                        Toast.fire({
                            position: 'top-end',
                            title: "(6) Indirect Code harus diisi!",
                            icon:"error"
                        })   
                    } else {
                        submit_detail();
                    } 
                },
                error: function( jqXHR, textStatus ) { 
                    Toast.fire({
                        position: 'top-end',
                        title: " Please check all field! ",
                        icon:"error"
                    }) 
                }
            })
        }
 
    function set_labor_type() {  
        if($("#TimeStatus").val() == 1) {
            Toast.fire({
                position: 'top-end',
                title: "Silahkan direcall terlebih dulu",
                icon:"error"
            }) 
            return false ;
        } 
        if($("#createNew").val() == 0) { return false ; } 
        $("#createNew").val(0) ;  

        var newInptJobNum = new Option("Select Option", null, true, true);     
        $('#InptJobNum').append(newInptJobNum).trigger('change'); 

        var newInptIndirectCode = new Option("Select Option", null, true, true);     
        $('#InptIndirectCode').append(newInptIndirectCode).trigger('change');

        $("#InptQtyPlan").val("");
        $("#InptPartName").val(""); 
        $("#InptLineCategory").val("");  

        var newPartNum = new Option("Select Option", null, true, true);     
        $('#InptPartNum').append(newPartNum).trigger('change');

        var newResourceGroup = new Option("Select Option", null, true, true);     
        $('#InptResourceGrpID').append(newResourceGroup).trigger('change');
        
        var newResourceID = new Option("Select Option", null, true, true);     
        $('#InptResourceID').append(newResourceID).trigger('change');
    
        $("#InptModel").val("");   
        $("#InptLaborQty").val("");   
        $("#InptScrapQty").val("");   
        $("#InptDiscrepQty").val("");   

        var newDiscrpRsnCode = new Option("Select Option", null, true, true);     
        $('#InptScrapReasonCode').append(newDiscrpRsnCode).trigger('change'); 

        var newScrapReasonCode = new Option("Select Option", null, true, true);     
        $('#InptDiscrpRsnCode').append(newScrapReasonCode).trigger('change'); 
        
        var newIndirectCode = new Option("Select Option", null, true, true);     
        $('#InptIndirectCode').append(newIndirectCode).trigger('change'); 

        setTimeout (function() {
            $("#createNew").val(1) ;
        },500)  
    }

    function transisi_proses() {
        var newPartNum = new Option("Select Option", null, true, true);     
        $('#InptPartNum').append(newPartNum).trigger('change');
    }

    $("#InptClockIn").keypress(function(){
        setTimeout(function(){
            change_time();
        },500)
        
    })
    $("#InptClockOut").keypress(function(){
        setTimeout(function(){
            change_time();
        },500)
    })

    function set_jobnum() { 
        if($("#createNew").val() == 0) { return false ;} 
        var InptJobNum = $("#InptJobNum").val();    

        var InptJobNum = InptJobNum.replace(/ /g, '_SPACE_');  
        $("#InptLineCategory").val(InptJobNum.split('~')[4]) ; 

        var newResourceGroup = new Option(InptJobNum.split('~')[2], InptJobNum.split('~')[2], true, true);     
        $('#InptResourceGrpID').append(newResourceGroup).trigger('change');
        
        var newResourceID = new Option(InptJobNum.split('~')[3], InptJobNum.split('~')[3], true, true);     
        $('#InptResourceID').append(newResourceID).trigger('change');

        if ($("#InptlaborTypePseudo").val() == 'I') { 
            var newPartNum = new Option(InptJobNum.split('~')[5].replace(/_SPACE_/g, ' '), InptJobNum.split('~')[5].replace(/_SPACE_/g, ' '), true, true);     
            $('#InptPartNum').append(newPartNum).trigger('change'); 
        } else { 
            var newPartNum = new Option("Select Option", null, true, true);     
            $('#InptPartNum').append(newPartNum).trigger('change'); 
        }  
        submit_detail()
    }

    function submit_detail() {     

        if($("#TimeStatus").val() == 1) {
            Toast.fire({
                position: 'top-end',
                title: "Silahkan direcall terlebih dulu",
                icon:"error"
            }) 
            return false ;
        }
        if($("#createNew").val() == 0) { return false ;} 
            if($("#InptlaborTypePseudo").val() == 'I') { 
                if ($("#InptResourceGrpID").val() === null || $("#InptResourceGrpID").val() == 'null' || $("#InptResourceGrpID").val() == '' || $("#InptResourceID").val() === null || $("#InptResourceID").val() == 'null' || $("#InptResourceID").val() == 'null') 
                {  
                    get_resorce_id();
                    return false ;
                } else if ($("#InptPartNum").val() === null || $("#InptPartNum").val() == 'null' || $("#InptPartNum").val() == '') {
                    Toast.fire({
                        position: 'top-end',
                        title: "PartNum Code harus diisi!",
                        icon:"error"
                    }) 
                } else if ($("#InptIndirectCode").val() === null || $("#InptIndirectCode").val() == 'null' || $("#InptIndirectCode").val() == '') {
                    Toast.fire({
                        position: 'top-end',
                        title: "(1) Indirect Code harus diisi!",
                        icon:"error"
                    }) 
                } 
            }  
        if($("#InptJobNum").val() === null || $("#InptJobNum").val() === '') { return false ;}  
        var token = $("[name=_token]").val(); 
        var InptJobNum = $("#InptJobNum").val();    
        var laborHedSeq = $("#laborHedSeq").val();    
        var laborDtlSeq = $("#laborDtlSeq").val();    
        var InptActualClockinDate = $("#InptActualClockinDate").val();   
        var InptlaborTypePseudo = $("#InptlaborTypePseudo").val();    
        var InptIndirectCode = $("#InptIndirectCode").val();    
        var InptResourceGrpID = $("#InptResourceGrpID").val();    
        var InptResourceID = $("#InptResourceID").val();      
        var InptJobNum = InptJobNum.replace(/ /g, '_SPACE_');

        var string = "&_token=" + token + "&InptlaborTypePseudo=" + InptlaborTypePseudo + "&laborHedSeq=" + laborHedSeq + "&laborDtlSeq=" + laborDtlSeq + "&InptJobNum=" + InptJobNum + "&InptIndirectCode=" + InptIndirectCode + "&InptResourceGrpID=" + InptResourceGrpID + "&InptResourceID=" + InptResourceID + "&InptActualClockinDate=" + InptActualClockinDate ;   
         
        var button = document.getElementById('btn_save_header');
        var svg = document.getElementById('svg_save_header');
        var spinner = document.getElementById('spinner_save_header');
        var buttonText = document.getElementById('btn_text_save_header'); 

        svg.style.display = 'none' ;
        spinner.style.display = 'inline-block' ;
        buttonText.textContent = 'Please Wait...' ; 
        button.disabled = true ;   
        $("#createNew").val(0);
        $.ajax({
            type	: 'POST',
            url	: "{{ route('time_entry.submit_detail') }}",
            data	: string,
            cache	: false,
            dataType	: 'json',
            success : function(data) {   
                $("#laborDtlSeq").val(data.laborDtlSeq); 
                $("#isCopart").val(data.isCoPart);
                svg.style.display = 'inline-block';
                spinner.style.display = 'none';
                buttonText.textContent = 'Save'; 
                button.disabled = false;    
                if (data.code == 200) {   
                    if($("#InptlaborTypePseudo").val() === 'P') {  
                        $("#InptQtyPlan").val("");
                        $("#InptPartName").val(""); 
                        $("#InptLineCategory").val("");  

                        var newPartNum = new Option("Select Option", null, true, true);     
                        $('#InptPartNum').append(newPartNum).trigger('change');

                        var newResourceGroup = new Option("Select Option", null, true, true);     
                        $('#InptResourceGrpID').append(newResourceGroup).trigger('change');
                        
                        var newResourceID = new Option("Select Option", null, true, true);     
                        $('#InptResourceID').append(newResourceID).trigger('change');
                    
                        $("#InptModel").val("");   
                        $("#InptLaborQty").val("");   
                        $("#InptScrapQty").val("");   
                        $("#InptDiscrepQty").val("");   

                        var newDiscrpRsnCode = new Option("Select Option", null, true, true);     
                        $('#InptScrapReasonCode').append(newDiscrpRsnCode).trigger('change'); 

                        var newScrapReasonCode = new Option("Select Option", null, true, true);     
                        $('#InptDiscrpRsnCode').append(newScrapReasonCode).trigger('change');  
                        
                    } else {
                        
                    }
                    Toast.fire({
                        position: 'top-end',
                        title: "Data berhasil diupdate!",
                        icon: "success"
                    }); 
                } else { 
                    if($("#InptlaborTypePseudo").val() === 'I') { 
                        if ($("#InptPartNum").val() === null || $("#InptPartNum").val() == '') { 
                            Toast.fire({
                                position: 'top-end',
                                title: "PartNum harus diisi!",
                                icon: "error"
                            })
                            return false ; 
                        } else if ($("#InptIndirectCode").val() === null) {
                            Toast.fire({
                                    position: 'top-end',
                                    title: "(2) Indirect Code harus diisi!",
                                    icon: "error"
                                })
                                return false ;
                        } else {
                            Toast.fire({
                                position: 'top-end',
                                title: data.status,
                                icon: "error"
                            })
                        }
                        } else {
                            Toast.fire({
                                position: 'top-end',
                                title: data.status,
                                icon: "error"
                            })
                        }
                   
                }
                $("#createNew").val(1);
            },
            error: function( jqXHR, textStatus ) {
                $("#createNew").val(1);
                svg.style.display = 'inline-block';
                spinner.style.display = 'none';
                buttonText.textContent = 'Save'; 
                button.disabled = false;   
                if($("#laborHedSeq").val() === null || $("#laborHedSeq").val() === '') { 
                    var newOption = new Option('Select option', '', true, true);     
                    $('#InptJobNum').append(newOption).trigger('change'); 
                    Toast.fire({
                        position: 'top-end',
                        title: "Employee dan Shift harus diisi dan pastikan sudah tersimpan ke system!",
                        icon: "error"
                    });   
                } else if($("#InptlaborTypePseudo").val() === 'I') { 
                        if ($("#InptIndirectCode").val() === null) { 
                            Toast.fire({
                                position: 'top-end',
                                title: "(3) Indirect Code harus diisi!",
                                icon: "error"
                            })
                            return false ;
                        }
                    } else {
                        Toast.fire({
                        position: 'top-end',
                        title: " Please check all field! ",
                        icon:"error"
                    }) 
                }
                return false();
            }
        }) 
    } 


    function set_indirect_job() {  
        if($("#TimeStatus").val() == 1) {
            Toast.fire({
                position: 'top-end',
                title: "Silahkan direcall terlebih dulu",
                icon:"error"
            }) 
            return false ;
        }  
        if ($("#createNew").val() == 0 || $("#InptResourceGrpID").val() == 'P' || $("#InptResourceGrpID").val() === null || $("#InptResourceGrpID").val() === null || $("#InptJobNum").val() === null || $("#InptJobNum").val() === '' || $("#InptIndirectCode").val() === null) { 
            return false ;
        }  
        if($("#createNew").val() == 0) { return false ;} 
        var token = $("[name=_token]").val(); 
        var InptJobNum = $("#InptJobNum").val();    
        var laborHedSeq = $("#laborHedSeq").val();    
        var laborDtlSeq = $("#laborDtlSeq").val();    
        var InptActualClockinDate = $("#InptActualClockinDate").val();   
        var InptlaborTypePseudo = $("#InptlaborTypePseudo").val();    
        var InptIndirectCode = $("#InptIndirectCode").val();    
        var InptResourceGrpID = $("#InptResourceGrpID").val();    
        var InptResourceID = $("#InptResourceID").val();    
        var InptJobNum = InptJobNum.replace(/ /g, '_SPACE_');
        var string = "&_token=" + token + "&InptlaborTypePseudo=" + InptlaborTypePseudo + "&laborHedSeq=" + laborHedSeq + "&laborDtlSeq=" + laborDtlSeq + "&InptJobNum=" + InptJobNum + "&InptIndirectCode=" + InptIndirectCode + "&InptResourceGrpID=" + InptResourceGrpID + "&InptResourceID=" + InptResourceID + "&InptActualClockinDate=" + InptActualClockinDate ;   
         
        var button = document.getElementById('btn_save_header');
        var svg = document.getElementById('svg_save_header');
        var spinner = document.getElementById('spinner_save_header');
        var buttonText = document.getElementById('btn_text_save_header'); 

        svg.style.display = 'none' ;
        spinner.style.display = 'inline-block' ;
        buttonText.textContent = 'Please Wait...' ; 
        button.disabled = true ;   

        $.ajax({
            type	: 'POST',
            url	: "{{ route('time_entry.submit_detail') }}",
            data	: string,
            cache	: false,
            dataType	: 'json',
            success : function(data) {   
                svg.style.display = 'inline-block';
                spinner.style.display = 'none';
                buttonText.textContent = 'Save'; 
                button.disabled = false;    
                if (data.code == 200) {  
                    $("#laborDtlSeq").val(data.laborDtlSeq);  
                    $("#isCopart").val(data.isCoPart);  
                    Toast.fire({
                        position: 'top-end',
                        title: "Data berhasil diupdate!",
                        icon: "success"
                    }); 
                } else { 
                    Toast.fire({
                        position: 'top-end',
                        title: data.status,
                        icon: "error"
                    }) 
                }
            },
            error: function( jqXHR, textStatus ) {
                svg.style.display = 'inline-block';
                spinner.style.display = 'none';
                buttonText.textContent = 'Save'; 
                button.disabled = false;   
                if($("#laborHedSeq").val() === null || $("#laborHedSeq").val() === '') { 
                    var newOption = new Option('Select option', '', true, true);     
                    $('#InptJobNum').append(newOption).trigger('change'); 
                    Toast.fire({
                        position: 'top-end',
                        title: "Employee dan Shift harus diisi dan pastikan sudah tersimpan ke system!",
                        icon: "error"
                    });   
                } else if($("#InptlaborTypePseudo").val() === 'I') { 
                        if ($("#InptIndirectCode").val() === null) { 
                            Toast.fire({
                                position: 'top-end',
                                title: "(4) Indirect Code harus diisi!",
                                icon: "error"
                            })
                            return false ;
                        }
                    } else {
                        Toast.fire({
                        position: 'top-end',
                        title: " Please check all field! ",
                        icon:"error"
                    }) 
                }
                return false();
                
            }
        }) 
    } 

    function save_header() {
        if($("#createNew").val() == 0) { return false ;} 
        if($("#InptEmployeeID").val() === null || $("#InptEmployeeID").val() === '' || $("#InptShiftID").val() === null || $("#InptShiftID").val() == '') { return false ;}  
        var token = $("[name=_token]").val(); 
        var laborHedSeq = $("#laborHedSeq").val(); 
        var laborDtlSeq = $("#laborDtlSeq").val();   
        var formData = '';
        for (var i = 1; i < 8 ; i++) {
            formData += $('#form-segment-' + i).serialize() + '&';  
        } 
        formData = formData.slice(0, -1); 
        var string = "&_token=" + token + "&laborHedSeq=" + laborHedSeq + "&laborDtlSeq=" + laborDtlSeq + '&' + formData;   

        var button = document.getElementById('btn_save_header');
        var svg = document.getElementById('svg_save_header');
        var spinner = document.getElementById('spinner_save_header');
        var buttonText = document.getElementById('btn_text_save_header'); 
        svg.style.display = 'none' ;
        spinner.style.display = 'inline-block' ;
        buttonText.textContent = 'Please Wait...' ; 
        button.disabled = true ;  
        $.ajax({
            type	: 'POST',
            url	: "{{ route('time_entry.submit_header') }}",
            data	: string,
            cache	: false,
            dataType	: 'json',
            success : function(data) {   
                if (data.code == 200) {
                    svg.style.display = 'inline-block';
                    spinner.style.display = 'none';
                    buttonText.textContent = 'Save';  
                    button.disabled = false;  
                    $("#laborHedSeq").val(data.laborHedSeq); 
                    $("#InptPayHours").val(data.payHour); 
                    $("#InptClockIn").val(data.clockInTime); 
                    $("#InptClockOut").val(data.clockOutTime); 
                    $("#InptLaborHrs").val(data.payHour);
                    check_document_status(); 
                    Toast.fire({
                        position: 'top-end',
                        title: data.status,
                        icon: "success"
                    }); 
                } else {
                    svg.style.display = 'inline-block';
                    spinner.style.display = 'none';
                    buttonText.textContent = 'Save'; 
                    button.disabled = false;    
                    Toast.fire({
                        position: 'top-end',
                        title: data.status,
                        icon: "error"
                    })
                }
            },
            error: function( jqXHR, textStatus ) {
                svg.style.display = 'inline-block';
                spinner.style.display = 'none';
                buttonText.textContent = 'Save'; 
                button.disabled = false;  

                if($("#InptActualClockinDate").val() === '') {
                    Toast.fire({
                        position: 'top-end',
                        title: "Date Harus diisi!",
                        icon: "error"
                    });  
                } else
                if($("#InptEmployeeID").val() === null || $("#InptEmployeeID").val() === '') {
                    Toast.fire({
                        position: 'top-end',
                        title: "Employee Harus diisi!",
                        icon: "error"
                    });  
                } else
                if($("#InptShiftID").val() === null || $("#InptShiftID").val() == '') {
                    Toast.fire({
                        position: 'top-end',
                        title: "Shift Harus diisi!",
                        icon: "error"
                    });  
                } else  
                {
                    Toast.fire({
                        position: 'top-end',
                        title: " Please check all field! ",
                        icon:"error"
                    }) 
                }
            }
        }) 
    } 

    function save_time_entry() {
        var token = $("[name=_token]").val(); 
        var laborHedSeq = $("#laborHedSeq").val(); 
        var laborDtlSeq = $("#laborDtlSeq").val();   
        var InptResourceGrpDescription = $("#InptResourceGrpID").text();    
        var InptScrapReasonCode = $("#InptScrapReasonCode").val();  
        var InptDiscrpRsnCode = $("#InptDiscrpRsnCode").val(); 
        var isCopart = $("#isCopart").val();  
        var InptLaborNote = $("#InptLaborNote").val();  
        var formData = '';
        for (var i = 1; i <= 8; i++) {
            formData += $('#form-segment-' + i).serialize() + '&';  
        } 
        formData = formData.slice(0, -1); 
        var string = "&_token=" + token + "&isCopart=" + isCopart + "&laborHedSeq=" + laborHedSeq + "&laborDtlSeq=" + laborDtlSeq + "&InptScrapReasonCode=" + InptScrapReasonCode + "&InptDiscrpRsnCode=" + InptDiscrpRsnCode + "&InptResourceGrpDescription=" + InptResourceGrpDescription + '&' + formData + "&InptLaborNote=" + InptLaborNote ;   
        if($("#InptActualClockinDate").val() === '') {
            Toast.fire({
                position: 'top-end',
                title: "Date Harus diisi!",
                icon: "error"
            });
            return false() ;
        } 
        if($("#InptEmployeeID").val() === null) {
            Toast.fire({
                position: 'top-end',
                title: "Employee Harus diisi!",
                icon: "error"
            });
            return false() ;
        } 
        if($("#InptShiftID").val() === null) {
            Toast.fire({
                position: 'top-end',
                title: "Shift Harus diisi!",
                icon: "error"
            });
            return false() ;
        } 

        if($("#InptJobNum").val() === null) {
            Toast.fire({
                position: 'top-end',
                title: "Job Harus diisi!",
                icon: "error"
            });
            return false() ;
        } 
        if($("#InptClockIn").val() === '') {
            Toast.fire({
                position: 'top-end',
                title: "Clock In Harus diisi!",
                icon: "error"
            });
            return false() ;
        } 
        if($("#InptClockOut").val() === '') {
            Toast.fire({
                position: 'top-end',
                title: "Clock Out Harus diisi!",
                icon: "error"
            });
            return false() ;
        } 
        if($("#InptLaborQty").val() <= 0 && $("#InptlaborTypePseudo").val() == 'P') {
            Toast.fire({
                position: 'top-end',
                title: "Labor Qty Harus diisi!",
                icon: "error"
            });
            return false() ;
        } 
        if($("#InptLaborHrs").val() <= 0) {
            Toast.fire({
                position: 'top-end',
                title: "Labor Hrs Harus diisi!",
                icon: "error"
            });
            return false() ;
        } 
        if($("#InptResourceGrpID").val() === null) {
            Toast.fire({
                position: 'top-end',
                title: "Resource Grp Harus diisi!",
                icon: "error"
            });
            return false() ;
        } 
        if($("#InptResourceID").val() === null) {
            Toast.fire({
                position: 'top-end',
                title: "Resource Harus diisi!",
                icon: "error"
            });
            return false() ;
        } 
        var button = document.getElementById('btn_save_header');
        var svg = document.getElementById('svg_save_header');
        var spinner = document.getElementById('spinner_save_header');
        var buttonText = document.getElementById('btn_text_save_header'); 
        svg.style.display = 'none' ;
        spinner.style.display = 'inline-block' ;
        buttonText.textContent = 'Please Wait...' ; 
        button.disabled = true ;  
        $.ajax({
            type	: 'POST',
            url	: "{{ route('time_entry.submit_detail_complete') }}",
            data	: string,
            cache	: false,
            dataType	: 'json',
            success : function(data) {    
                svg.style.display = 'inline-block';
                spinner.style.display = 'none';
                buttonText.textContent = 'Save';  
                button.disabled = false;    
                if (data.code == 200) {  
                    Toast.fire({
                        position: 'top-end',
                        title: 'Data Berhasil Disimpan!',
                        icon: "success"
                    }); 
                } else { 
                    Toast.fire({
                        position: 'top-end',
                        title: data.status,
                        icon: "error"
                    })
                }
            },
            error: function( jqXHR, textStatus ) {
                svg.style.display = 'inline-block';
                spinner.style.display = 'none';
                buttonText.textContent = 'Save'; 
                button.disabled = false;  
                Toast.fire({
                    position: 'top-end',
                    title: " Please check all field! ",
                    icon:"error"
                }) 
            }
        }) 
    } 

</script>

<script>

    function btn_delete_header() { 
            var button = document.getElementById('btn_delete_header');
            var svg = document.getElementById('svg_delete_header');
            var spinner = document.getElementById('spinner_delete_header');
            var buttonText = document.getElementById('btn_text_delete_header'); 
            svg.style.display = 'none';
            spinner.style.display = 'inline-block';
            buttonText.textContent = 'Please Wait...'; 
            button.disabled = true;   
            Swal.fire({
                icon: 'warning',
                title: 'Delete Data?',
                text: "Hapus Time Entry", 
                showCancelButton: true,
                confirmButtonText: 'Confirm',
                }).then(function(isConfirm) { 
                if (isConfirm.value === true) {
                    delete_header();
                } else {
                    svg.style.display = 'inline-block';
                    spinner.style.display = 'none';
                    buttonText.textContent = 'Delete'; 
                    button.disabled = false;  
                }
            }) 
        }  

    function delete_header() {
        var token = $("[name=_token]").val(); 
        var laborHedSeq = $("#laborHedSeq").val(); 
        var laborDtlSeq = $("#laborDtlSeq").val();   
        var string = "&_token=" + token + "&laborHedSeq=" + laborHedSeq + "&laborDtlSeq=" + laborDtlSeq ;   
         
        var button = document.getElementById('btn_delete_header');
        var svg = document.getElementById('svg_delete_header');
        var spinner = document.getElementById('spinner_delete_header');
        var buttonText = document.getElementById('btn_text_delete_header'); 
        svg.style.display = 'none' ;
        spinner.style.display = 'inline-block' ;
        buttonText.textContent = 'Please Wait...' ; 
        button.disabled = true ;  
        $.ajax({
            type	: 'POST',
            url	: "{{ route('time_entry.delete_header') }}",
            data	: string,
            cache	: false,
            dataType	: 'json',
            success : function(data) {   
                if (data.code == 200) {
                    svg.style.display = 'inline-block';
                    spinner.style.display = 'none';
                    buttonText.textContent = 'Delete';  
                    button.disabled = false;  
                    $("#laborHedSeq").val(""); 
                    $("#InptPayHours").val(""); 
                    backHome();
                    Toast.fire({
                        position: 'top-end',
                        title: "Data berhasil dihapus!",
                        icon: "success"
                    }); 
                } else {
                    svg.style.display = 'inline-block';
                    spinner.style.display = 'none';
                    buttonText.textContent = 'Delete'; 
                    button.disabled = false;    
                    Toast.fire({
                        position: 'top-end',
                        title: data.status,
                        icon: "error"
                    })
                }
            },
            error: function( jqXHR, textStatus ) {
                svg.style.display = 'inline-block';
                spinner.style.display = 'none';
                buttonText.textContent = 'Delete'; 
                button.disabled = false;  
                Toast.fire({
                    position: 'top-end',
                    title: " Please check all field! ",
                    icon:"error"
                }) 
            }
        }) 
    } 

    function set_partnum(partNum) { 
        // if($("#createNew").val() == 0) { return false ;} 
        var token = $("[name=_token]").val(); 
        var laborHedSeq = $("#laborHedSeq").val(); 
        var laborDtlSeq = $("#laborDtlSeq").val();    
        var InptJobNum = $("#InptJobNum").val();   
        var isCopart = $("#isCopart").val();   
        var InptJobNum = InptJobNum.replace(/ /g, '_SPACE_');
        var string = "&_token=" + token + "&isCopart=" + isCopart + "&laborHedSeq=" + laborHedSeq + "&laborDtlSeq=" + laborDtlSeq + "&InptJobNum=" + InptJobNum + "&InptPartNum=" + partNum ;  
        $.ajax({
            type : 'POST',
            url	: "{{ route('time_entry.get_partnum_attr') }}",
            data	: string,
            cache	: false,
            dataType	: 'json',
            success : function(data) {   
                if (data.code == 200) { 

                    $("#InptQtyPlan").val(data.qty_plan);
                    $("#InptPartName").val(data.item_name.replace(/__/g, ",")); 
                    $("#InptLineCategory").val(data.category);  
                    var newResourceGroup = new Option(data.home_line, data.home_line, true, true);     
                    $('#InptResourceGrpID').append(newResourceGroup).trigger('change');
                    var newResourceID = new Option(data.home_line_detail_id, data.home_line_detail_id, true, true);     
                    $('#InptResourceID').append(newResourceID).trigger('change');
                    $("#InptModel").val(data.model_name);   
                    $("#InptLaborQty").val(data.QtyCompleted);   
                    $("#InptScrapQty").val(data.ScrapQty);   
                    $("#InptDiscrepQty").val(data.DiscrepQty);    
                    $("#InptLaborNote").text(data.InptLaborNote);  

                    var newDiscrpRsnCode = new Option((data.DiscrpRsnCodeDesc == '' ? 'Select Option' : data.DiscrpRsnCodeDesc), (data.DiscrpRsnCode == '' ? null : data.DiscrpRsnCode), true, true);     
                    $('#InptDiscrpRsnCode').append(newDiscrpRsnCode).trigger('change');   
                    var newScrapReasonCode = new Option((data.ScrapReasonCodeDesc == '' ? 'Select Option' : data.ScrapReasonCodeDesc), (data.ScrapReasonCode == '' ? null : data.ScrapReasonCode), true, true);     
                    $('#InptScrapReasonCode').append(newScrapReasonCode).trigger('change'); 
                     

                } else { 
                    Toast.fire({
                        position: 'top-end',
                        title: data.status,
                        icon: "error"
                    })
                }
            },
            error: function( jqXHR, textStatus ) { 
                Toast.fire({
                    position: 'top-end',
                    title: " Please check all field! ",
                    icon:"error"
                }) 
            }
        })
    } 

    $('#InptEmployeeID').select2({
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
    $('#InptlaborTypePseudo').select2() ; 
    $('#InptJobNum').select2({
        ajax: {
            type: 'POST',
            url: "{{ route('time_entry.get_job_list') }}",
            dataType: 'json',
            delay: 250, 
            data: function(params) {
                var query = {
                    search: params.term,   
                    _token: $("[name=_token]").val(),
                    JobDate: $("#InptActualClockinDate").val(),
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

    $('#InptShiftID').select2({
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
  
    $('#InptResourceGrpID').change(function(){
        var newOption = new Option('Select option', '0', true, true);     
        $('#InptResourceID').append(newOption).trigger('change'); 
    })

    $('#InptResourceID').select2({
        ajax: {
            type: 'POST',
            url: "{{ route('time_entry.get_resource') }}",
            dataType: 'json',
            delay: 250, 
            data: function(params) {
                var query = {
                    search: params.term,  
                    line: $("#InptResourceGrpID").val(),
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

    $('#InptResourceGrpID').select2({
        ajax: {
            type: 'POST',
            url: "{{ route('time_entry.get_resource_group') }}",
            dataType: 'json',
            delay: 250, // delay for search
            data: function(params) {
                var query = {
                    search: params.term,  
                    category_id: $("#InptLineCategory").val(),
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

    $('#InptDiscrpRsnCode').select2({
        ajax: {
            type: 'POST',
            url: "{{ route('time_entry.get_reason_code_scrap_list') }}",
            dataType: 'json',
            delay: 250, 
            data: function(params) {
                var query = {
                    search: params.term,  
                    qty: $("#InptDiscrepQty").val(),
                    typeField: 'D',
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

    $('#InptScrapReasonCode').select2({
        ajax: {
            type: 'POST',
            url: "{{ route('time_entry.get_reason_code_scrap_list') }}",
            dataType: 'json',
            delay: 250, 
            data: function(params) {
                var query = {
                    search: params.term,  
                    qty: $("#InptScrapQty").val(),
                    typeField: 'S',
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

    $('#InptIndirectCode').select2({
        ajax: {
            type: 'POST',
            url: "{{ route('time_entry.get_indirect_code_list') }}",
            dataType: 'json',
            delay: 250, 
            data: function(params) {
                var query = {
                    search: params.term,  
                    InptlaborTypePseudo : $("#InptlaborTypePseudo").val(), 
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

    $('#InptPartNum').select2({
        ajax: {
            type: 'POST',
            url: "{{ route('time_entry.get_part_num_list') }}",
            dataType: 'json',
            delay: 250, 
            data: function(params) {
                var query = {
                    search: params.term,  
                    laborHedSeq : $("#laborHedSeq").val(), 
                    laborDtlSeq : $("#laborDtlSeq").val(), 
                    InptJobNum : $("#InptJobNum").val().replace(/ /g, '_SPACE_'), 
                    isCopart : $("#isCopart").val(), 
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

</script>