<script type="text/javascript"> 
        $(document).ready(function(){     
            var DocNum = $("#InptDocNum").val() ;
            if (DocNum == '') {
                var token = $("[name=_token]").val();  
                var DocDate = $("#InptDocDate").val();  
                var string = "&_token=" + token + "&DocDate=" + DocDate ;  
                $.ajax({
                    type	: 'POST',
                    url	: "{{ route('inventory_move_in.get_new_docnum') }}",
                    data	: string,
                    cache	: false,
                    dataType	: 'json',
                    success : function(data) {    
                        $("#InptDocNum").val(data.DocNum);
                        detail_table();
                    },
                    error: function( jqXHR, textStatus ) { 
                        Toast.fire({
                            position: 'top-end',
                            title: " Please reload and tr again! ",
                            icon:"error"
                        }) 
                    }
                })
            }
        })
</script>
<div class="col-xxl-12">    
        <div id="form_loader" style="text-align: center;">
            <div class="lds-roller mt-10 mb-10" id="lds-roller-form"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div> 
        </div> 
        <div id="form_label">  
            <div class="card">
                <div class="card-header"> 
                    <div class="card-title"> 
                        <div class="d-flex align-items-center position-relative my-1"> 
                            <button type="button" class="btn btn-primary btn-sm" id="btn_submit_form" onclick="submit_form()">
                                <span id="svg_submit_form" class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                                        <g stroke="none" fill="none">
                                            <polygon points="0 0 24 0 24 24 0 24"/>
                                            <path d="M17,4 L6,4 C4.79111111,4 4,4.7 4,6 L4,18 C4,19.3 4.79111111,20 6,20 L18,20 C19.2,20 20,19.3 20,18 L20,7.20710678 C20,7.07449854 19.9473216,6.94732158 19.8535534,6.85355339 L17,4 Z M17,11 L7,11 L7,4 L17,4 L17,11 Z" fill="#000000"/>
                                            <rect fill="#000000" opacity="0.3" x="12" y="4" width="3" height="5" rx="0.5"/>
                                        </g>
                                    </svg>
                                </span> 
                                <span id="spinner_submit_form" class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;"></span>       
                                <span id="btn_text_submit_form">Save</span>
                            </button>
                        </div> 
                    </div> 
 

                    <div class="card-toolbar">    
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
                                    <label>To Warehouse <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-solid" data-kt-select2="true" data-placeholder="Select option" data-allow-clear="false" id="ToWarehouseID" name="ToWarehouseID" data-hide-search="false"/>  
                                            <option value="05-03-01" selected>Warehouse FG</option>
                                            <option value="05-02-01">PC Store 1</option>
                                            <option value="05-02-02">PC Store 2</option>
                                            <option value="05-02-05">After Nut</option>
                                            <option value="05-01-02">Blank</option>
                                            <option value="05-14-01">Warehouse Subcont External</option>
                                        <!-- <?php foreach ($wh_list AS $row) { ?>
                                            <option value="<?= $row->WarehouseCode ?>"><?= $row->Description ?></option>
                                        <?php } ?> -->
                                    </select>  
                                </div>  

                                <div class="form-group mb-3">
                                    <label>Bin</label>
                                    <select class="form-select form-select-solid" data-kt-select2="true" data-placeholder="Select option" data-allow-clear="false" id="ToBinID" name="ToBinID" data-hide-search="false"/>   
                                    </select> 
                                </div> 

                                <div class="form-group mb-3">
                                    <label>Lot</label>
                                    <input type="text" class="form-control bg-light-primary" id="LotNum" name="LotNum" value="A" readonly/>  
                                </div>  
                            </form>
                        </div> 

                        <div class="col-md-6">
                            <form id="form-segment-2">
                                <div class="form-group mb-3">
                                    <label>Scan Barcode </label> 
                                    <input type="text" class="form-control" id="InptBarcode" name="InptBarcode"/>
                                </div>  

                                <div class="form-group mb-3">
                                    <label>Part Num <span class="text-danger">*</span></label> 
                                <input type="text" class="form-control bg-light-primary" id="InptPartNum"
                                    name="InptPartNum" readonly />
                                <input type="text" id="DocNumReference" name="DocNumReference" readonly hidden />
                                <input type="text" id="DocNumReferenceLine" name="DocNumReferenceLine" readonly
                                    hidden />
                                <input type="text" id="DocNumReferenceLineRel" name="DocNumReferenceLineRel"
                                    readonly hidden />
                                <input type="text" id="WarehouseFrom" name="WarehouseFrom" readonly hidden />
                                </div>    
                                <div class="form-group mb-3">
                                    <label>Qty</label>
                                    <input type="text" class="form-control bg-light-primary" id="InptQty" name="InptQty" readonly/>  
                                </div>    
                            </form>
                        </div>  
                    </div> 
                    <hr>
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <form id="form-segment-3"> 
                                <div class="form-group mb-3">
                                    <label>DocNum <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control bg-light-primary" id="InptDocNum" name="InptDocNum" readonly/> 
                                </div>   
                            </form>
                        </div>    
                        <div class="col-md-6">
                            <form id="form-segment-4">   
                                <div class="form-group mb-5">
                                    <label>Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="InptDocDate" name="InptDocDate" value="<?php date_default_timezone_set('Asia/Jakarta'); echo date('Y-m-d');  ?>"/> 
                                </div> 
                            </form>
                        </div> 
                    </div>  
                </div> 
            </div>
 
            <div class="card mt-5">  
                <div class="card-header border-1 pt-6 pb-6 mb-5"> 
                    <div class="card-title"> 
                        <div class="d-flex align-items-center position-relative my-1"> 
                            <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                    <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                                </svg>
                            </span> 
                            <input type="text" data-kt-goodreceive-table-filter="search" id="detail_table_search" class="form-control form-control-solid w-250px ps-15 text-sm form-control-sm" placeholder="Search PartNum"/>
                        </div> 
                    </div>   
                </div> 
                <div class="card-body">
                    <table class="table align-middle table-row-dashed table-striped gy-2 fs-7" id="kt_form_table"> 
                        <thead>
                            <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                <th class="min-w-20px pe-2">No</th>
                                <th class="min-w-20px pe-2">Delete</th>
                                <th class="min-w-20px">PartNum</th>  
                                <th class="min-w-100px">Qty</th> 
                                <th class="min-w-20px">From WH</th>   
                                <th class="min-w-100px">To Wh</th>   
                            </tr> 
                        </thead>  
                        <tfoot>
                            <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                <th class="min-w-20px pe-2">No</th>
                                <th class="min-w-20px pe-2">Delete</th>
                                <th class="min-w-20px">PartNum</th>  
                                <th class="min-w-100px">Qty</th> 
                                <th class="min-w-20px">From WH</th>   
                                <th class="min-w-100px">To Wh</th>   
                            </tr> 
                        </tfoot> 
                    </table> 
                </div> 
            </div>  
    </div>   
</div>   
 
<input type="text" name="ToWarehouseIDFromTag" id="ToWarehouseIDFromTag" value="" hidden/>

<script>  
    $('#ToWarehouseID').select2() ; 
    document.getElementById('InptBarcode').addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            var status = 0 ;
            var barcodeValue = document.getElementById('InptBarcode').value;  
            if (barcodeValue.length == 0 || !barcodeValue.includes('~')) 
            {
                Toast.fire({
                    position: 'top-end',
                    title: "Barcode Tidak Di Kenal!",
                    icon: "error"
                })
                $("#InptBarcode").val("") ;
            } else { 
                var split = barcodeValue.split('~');
                if (barcodeValue.includes('~')) { 
                    if (barcodeValue.includes('MIT')) {
                        var splitValues = barcodeValue.split('~'); 
                        var indexCount = splitValues.length;
                        if (splitValues[0]) {
                            var status = 1 ;
                            document.getElementById('InptPartNum').value = splitValues[0];
                        } else { var status = 0 ; }
                        if (splitValues[1]) {
                            var status = 1 ;
                            document.getElementById('InptQty').value = splitValues[1];
                        } else { var status = 0 ; } 
                        if (splitValues[2]) {
                            var status = 1 ;
                            document.getElementById('ToWarehouseIDFromTag').value = splitValues[2];
                        } else { var status = 0 ; } 
                        if (splitValues[3]) { 
                            var status = 1 ;
                            document.getElementById('ToBinID').value = splitValues[3];
                        } else { var status = 0 ; }
                        if (splitValues[4]) { 
                            var status = 1 ;
                            document.getElementById('LotNum').value = splitValues[4]; 
                        } else { var status = 0 ; }
                        if (splitValues[5]) { 
                            var status = 1 ;
                            document.getElementById('DocNumReference').value = splitValues[5];
                        } else { var status = 0 ; }
                        if (splitValues[6]) { 
                            var status = 1 ;
                            document.getElementById('DocNumReferenceLine').value = splitValues[6];
                        } else { var status = 0 ; }
                        if (splitValues[8]) { 
                            var status = 1 ;
                            document.getElementById('DocNumReferenceLineRel').value = splitValues[8];
                        } else { var status = 0 ; } 
                        if (barcodeValue.includes('MIT') && status == 1) {  
                            submit_form_mit();
                        } 
                    } else if (split.length >= 10) {
                        var splitValues = barcodeValue.split('~');
                        if (splitValues[0]) {
                            var status = 1;
                            document.getElementById('InptPartNum').value = splitValues[0];
                        } else {
                            var status = 0;
                        }
                        if (splitValues[1]) {
                            var status = 1;
                            document.getElementById('InptQty').value = splitValues[1];
                        } else {
                            var status = 0;
                        }
                        if (splitValues[2]) {
                            var status = 1;
                            document.getElementById('ToWarehouseIDFromTag').value = splitValues[2];
                        } else {
                            var status = 0;
                        }
                        if (splitValues[7]) {
                            var status = 1;
                            document.getElementById('WarehouseFrom').value = splitValues[7];
                        } else {
                            var status = 0;
                        }
                        if (splitValues[4]) {
                            var status = 1;
                            document.getElementById('LotNum').value = splitValues[4];
                        } else {
                            var status = 0;
                        }
                        if (splitValues[5]) {
                            var status = 1;
                            document.getElementById('DocNumReference').value = splitValues[5];
                        } else {
                            var status = 0;
                        }
                        if (splitValues[9]) {
                            var status = 1;
                            document.getElementById('DocNumReferenceLine').value = splitValues[9];
                        } else {
                            var status = 0;
                        }
                        if (splitValues[10]) {
                            var status = 1;
                            document.getElementById('DocNumReferenceLineRel').value = splitValues[10];
                        } else {
                            var status = 0;
                        }
                        setTimeout(function() {
                            submit_form_packlist();
                        }, 500);

                    } else {
                        var splitValues = barcodeValue.split('~');  
                        if (splitValues[0]) {
                            var status = 1 ;
                            document.getElementById('InptPartNum').value = splitValues[0];
                        } else { var status = 0 ; }
                        if (splitValues[1]) {
                            var status = 1 ;
                            document.getElementById('InptQty').value = splitValues[1];
                        } else { var status = 0 ; } 
                        if (splitValues[2]) {
                            var status = 1 ;
                            document.getElementById('ToWarehouseIDFromTag').value = splitValues[2];
                        } else { var status = 0 ; } 
                        if (splitValues[5]) { 
                            var status = 1 ;
                            document.getElementById('LotNum').value = splitValues[5]; 
                        } else { var status = 0 ; }
                        if (splitValues[4]) { 
                            var status = 1 ;
                            document.getElementById('DocNumReference').value = splitValues[4];
                        } else { var status = 0 ; }
                        if (splitValues[6]) { 
                            var status = 1 ;
                            document.getElementById('DocNumReferenceLine').value = splitValues[6];
                        } else { var status = 0 ; }
                        if (splitValues[7]) { 
                            var status = 1 ;
                            document.getElementById('DocNumReferenceLineRel').value = splitValues[7];
                        } else { var status = 0 ; }  
                        setTimeout(function(){
                            submit_form_job(); 
                        },500);
                    }
                }
            }
        }
    });
    $(document).ready(function(){
        $("#ToBinID").append(`<option value="GENERAL">GENERAL BIN</option>`);
    })
    $("#ToWarehouseID").on('change',function(){
        const value = $(this).val()
        $.ajax({
            url:"{{ url('inventory_move_in.show-bin') }}",
            type:"POST",
            data:{
                _token:"{{ csrf_token() }}",
                warehouseCode:value
            },success:function(response){
                $("#ToBinID").empty();
                response.forEach(item => {
                    $("#ToBinID").append(
                        `<option value="${item.BinNum}">${item.Description}</option>`
                    );
                });
                $("#ToBinID").trigger('change.select2');
            }
        })
    })
    function submit_form() { 
        var InptBarcode = $("#InptBarcode").val(); 
        if (InptBarcode.length == 0 || !InptBarcode.includes('~')) 
        {
            Toast.fire({
                position: 'top-end',
                title: "Barcode Tidak Di Kenal!",
                icon: "error"
            })
            $("#InptBarcode").val("") ;
        } else { 
            var status = 0 ;
            var barcodeValue = document.getElementById('InptBarcode').value;  
            if (barcodeValue.includes('~')) 
            { 
                var split = barcodeValue.split('~');
                if (barcodeValue.includes('MIT')) 
                {
                    var splitValues = barcodeValue.split('~'); 
                    var indexCount = splitValues.length;
                    if (splitValues[0]) {
                        var status = 1 ;
                        document.getElementById('InptPartNum').value = splitValues[0];
                    } else { var status = 0 ; }
                    if (splitValues[1]) {
                        var status = 1 ;
                        document.getElementById('InptQty').value = splitValues[1];
                    } else { var status = 0 ; }  
                    if (splitValues[2]) {
                        var status = 1 ;
                        document.getElementById('ToWarehouseIDFromTag').value = splitValues[2];
                    } else { var status = 0 ; } 
                    if (splitValues[3]) { 
                        var status = 1 ;
                        document.getElementById('ToBinID').value = splitValues[3];
                    } else { var status = 0 ; }
                    if (splitValues[4]) { 
                        var status = 1 ;
                        document.getElementById('LotNum').value = splitValues[4]; 
                    } else { var status = 0 ; }
                    if (splitValues[5]) { 
                        var status = 1 ;
                        document.getElementById('DocNumReference').value = splitValues[5];
                    } else { var status = 0 ; }
                    if (splitValues[6]) { 
                        var status = 1 ;
                        document.getElementById('DocNumReferenceLine').value = splitValues[6];
                    } else { var status = 0 ; }
                    if (splitValues[8]) { 
                        var status = 1 ;
                        document.getElementById('DocNumReferenceLineRel').value = splitValues[8];
                    } else { var status = 0 ; }  
                    setTimeout(function(){
                        submit_form_mit(); 
                    }, 500);
                } else if (split.length >= 10) {
                    var splitValues = barcodeValue.split('~');
                    if (splitValues[0]) {
                        var status = 1;
                        document.getElementById('InptPartNum').value = splitValues[0];
                    } else {
                        var status = 0;
                    }
                    if (splitValues[1]) {
                        var status = 1;
                        document.getElementById('InptQty').value = splitValues[1];
                    } else {
                        var status = 0;
                    }
                    if (splitValues[7]) {
                        var status = 1;
                        document.getElementById('ToWarehouseIDFromTag').value = splitValues[7];
                    } else {
                        var status = 0;
                    }
                    if (splitValues[4]) {
                        var status = 1;
                        document.getElementById('LotNum').value = splitValues[4];
                    } else {
                        var status = 0;
                    }
                    if (splitValues[5]) {
                        var status = 1;
                        document.getElementById('DocNumReference').value = splitValues[5];
                    } else {
                        var status = 0;
                    }
                    if (splitValues[9]) {
                        var status = 1;
                        document.getElementById('DocNumReferenceLine').value = splitValues[9];
                    } else {
                        var status = 0;
                    }
                    if (splitValues[10]) {
                        var status = 1;
                        document.getElementById('DocNumReferenceLineRel').value = splitValues[10];
                    } else {
                        var status = 0;
                    }
                    setTimeout(function() {
                        submit_form_packlist();
                    }, 500);

                } else {
                    var splitValues = barcodeValue.split('~'); 
                    var indexCount = splitValues.length;
                    if (splitValues[0]) {
                        var status = 1 ;
                        document.getElementById('InptPartNum').value = splitValues[0];
                    } else { var status = 0 ; }
                    if (splitValues[1]) {
                        var status = 1 ;
                        document.getElementById('InptQty').value = splitValues[1];
                    } else { var status = 0 ; } 
                    if (splitValues[2]) {
                        var status = 1 ;
                        document.getElementById('ToWarehouseIDFromTag').value = splitValues[2];
                    } else { var status = 0 ; } 
                    if (splitValues[5]) { 
                        var status = 1 ;
                        document.getElementById('LotNum').value = splitValues[5]; 
                    } else { var status = 0 ; }
                    if (splitValues[4]) { 
                        var status = 1 ;
                        document.getElementById('DocNumReference').value = splitValues[4];
                    } else { var status = 0 ; }
                    if (splitValues[6]) { 
                        var status = 1 ;
                        document.getElementById('DocNumReferenceLine').value = splitValues[6];
                    } else { var status = 0 ; }
                    if (splitValues[7]) { 
                        var status = 1 ;
                        document.getElementById('DocNumReferenceLineRel').value = splitValues[7];
                    } else { var status = 0 ; }  
                    setTimeout(function(){
                        submit_form_job(); 
                    },500);
                }
            }
        }
    }

    function submit_form_packlist() {
        var token = $("[name=_token]").val();
        var button = document.getElementById('btn_submit_form');
        var svg = document.getElementById('svg_submit_form');
        var spinner = document.getElementById('spinner_submit_form');
        var buttonText = document.getElementById('btn_text_submit_form');
        var ToWarehouseIDFromTag = $('#ToWarehouseIDFromTag').val();
        var WarehouseFrom = $('#WarehouseFrom').val();
        svg.style.display = 'none';
        spinner.style.display = 'inline-block';
        buttonText.textContent = 'Please Wait...';
        button.disabled = true;
        var formData = '';
        for (var i = 1; i < 5; i++) {
            formData += $('#form-segment-' + i).serialize() + '&';
        }
        formData = formData.slice(0, -1);
        var string = "&_token=" + token + "&ToWarehouseIDFromTag=" + ToWarehouseIDFromTag + '&' + formData;
        $.ajax({
            type: 'POST',
            url: "{{ route('inventory_move_in.submit_form_packlist') }}",
            data: string,
            cache: false,
            dataType: 'json',
            success: function(data) {
                svg.style.display = 'inline-block';
                spinner.style.display = 'none';
                buttonText.textContent = 'Submit';
                button.disabled = false;
                $("#InptBarcode").val("");
                $("#InptPartNum").val("");
                $("#InptQty").val("");
                $("#DocNumReference").val("");
                $("#DocNumReferenceLine").val("");
                $("#DocNumReferenceLineRel").val("");
                if (data.code == 200) {
                    Toast.fire({
                        position: 'top-end',
                        title: "Data berhasil tersimpan!",
                        icon: "success"
                    })
                    refresh_detail_table();
                } else {
                    Toast.fire({
                        position: 'top-end',
                        title: data.status,
                        icon: "error"
                    })
                }
            },
            error: function(jqXHR, textStatus) {
                svg.style.display = 'inline-block';
                spinner.style.display = 'none';
                buttonText.textContent = 'Submit';
                button.disabled = false;
                var errorMsg = "Error";
                if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                    errorMsg = jqXHR.responseJSON.message;
                } else if (jqXHR.responseJSON && jqXHR.responseJSON.status) {
                    errorMsg = jqXHR.responseJSON.status;
                } else if (jqXHR.statusText) {
                    errorMsg = jqXHR.statusText;
                }
                Toast.fire({
                    position: 'top-end',
                    title: errorMsg,
                    icon: "error"
                })
            }
        })
    }

    function submit_form_job() { 
        var token = $("[name=_token]").val();  
        var button = document.getElementById('btn_submit_form');
        var svg = document.getElementById('svg_submit_form');
        var spinner = document.getElementById('spinner_submit_form');
        var buttonText = document.getElementById('btn_text_submit_form'); 
        var ToWarehouseIDFromTag = $('#ToWarehouseIDFromTag').val();  
         
        svg.style.display = 'none' ;
        spinner.style.display = 'inline-block' ;
        buttonText.textContent = 'Please Wait...' ; 
        button.disabled = true ;  
        var formData = '';
        for (var i = 1; i < 5 ; i++) {
            formData += $('#form-segment-' + i).serialize() + '&';  
        } 

        formData = formData.slice(0, -1);  
        var string = "&_token=" + token + "&ToWarehouseIDFromTag=" + ToWarehouseIDFromTag + '&' + formData ;  

        $.ajax({
            type	: 'POST',
            url	: "{{ route('inventory_move_in.submit_form_job') }}",
            data	: string,
            cache	: false,
            dataType	: 'json',
            success : function(data) {   
                svg.style.display = 'inline-block';
                spinner.style.display = 'none';
                buttonText.textContent = 'Submit'; 
                button.disabled = false;    
                $("#InptBarcode").val("");
                $("#InptPartNum").val("");
                $("#InptQty").val("");  
                $("#DocNumReference").val(""); 
                $("#DocNumReferenceLine").val("");  
                $("#DocNumReferenceLineRel").val("");  
                if (data.code == 200) {  
                   Toast.fire({
                        position: 'top-end',
                        title: "Data berhasil tersimpan!",
                        icon: "success"
                    })
                    refresh_detail_table();
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

    function delete_item(trc_id, no, DocRef) { 
            var button = document.getElementById('btn_delete_item_' + no);
            var svg = document.getElementById('svg_delete_item_' + no);
            var spinner = document.getElementById('spinner_delete_item_' + no); 
            svg.style.display = 'none';
            spinner.style.display = 'inline-block'; 
            button.disabled = true;   
            Swal.fire({
                icon: 'warning',
                title: 'Delete Data ?',
                text: "Hapus Label : " + DocRef, 
                showCancelButton: true,
                confirmButtonText: 'Confirm',
                }).then(function(isConfirm) { 
                if (isConfirm.value === true) {
                    execute_delete_item(trc_id, no);
                } else {
                    svg.style.display = 'inline-block';
                    spinner.style.display = 'none'; 
                    button.disabled = false;  
                }
            }) 
        }  

    function execute_delete_item(trc_id, no) { 
        var token = $("[name=_token]").val();   
        var button = document.getElementById('btn_delete_item_' + no);
        var svg = document.getElementById('svg_delete_item_' + no);
        var spinner = document.getElementById('spinner_delete_item_' + no);  
        svg.style.display = 'none' ;
        spinner.style.display = 'inline-block' ; 
        button.disabled = true ;   
        var string = "&_token=" + token + '&trc_id=' + trc_id ;   
        $.ajax({
            type	: 'POST',
            url	: "{{ route('inventory_move_in.submit_delete_item') }}",
            data	: string,
            cache	: false,
            dataType	: 'json',
            success : function(data) {   
                svg.style.display = 'inline-block';
                spinner.style.display = 'none'; 
                button.disabled = false;     
                if (data.code == 200) {  
                   Toast.fire({
                        position: 'top-end',
                        title: "Data berhasil dihapus!",
                        icon: "success"
                    })
                    refresh_detail_table();
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
                button.disabled = false;     
                Toast.fire({
                    position: 'top-end',
                    title: " Please check all field! ",
                    icon:"error"
                }) 
            }
        }) 
    } 

    function submit_form_mit() {
        
        var token = $("[name=_token]").val();  
        var button = document.getElementById('btn_submit_form');
        var svg = document.getElementById('svg_submit_form');
        var spinner = document.getElementById('spinner_submit_form');
        var buttonText = document.getElementById('btn_text_submit_form'); 
        var ToWarehouseIDFromTag = $('#ToWarehouseIDFromTag').val();  
        
        svg.style.display = 'none' ;
        spinner.style.display = 'inline-block' ;
        buttonText.textContent = 'Please Wait...' ; 
        button.disabled = true ;  
        var formData = '';
        for (var i = 1; i < 5 ; i++) {
            formData += $('#form-segment-' + i).serialize() + '&';  
        } 

        formData = formData.slice(0, -1);  
        var string = "&_token=" + token + "&ToWarehouseIDFromTag=" + ToWarehouseIDFromTag + '&' + formData ;  

        $.ajax({
            type	: 'POST',
            url	: "{{ route('inventory_move_in.submit_form_mit') }}",
            data	: string,
            cache	: false,
            dataType	: 'json',
            success : function(data) {   
                svg.style.display = 'inline-block';
                spinner.style.display = 'none';
                buttonText.textContent = 'Submit'; 
                button.disabled = false;    
                $("#InptBarcode").val("");
                $("#InptPartNum").val("");
                $("#InptQty").val("");  
                $("#DocNumReference").val(""); 
                $("#DocNumReferenceLine").val("");  
                $("#DocNumReferenceLineRel").val("");  
                if (data.code == 200) {  
                   Toast.fire({
                        position: 'top-end',
                        title: "Data berhasil tersimpan!",
                        icon: "success"
                    })
                    refresh_detail_table();
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

    function submit_search(){ 
        var button = document.getElementById('btn_submit_search');
        var svg = document.getElementById('svg_submit_search');
        var spinner = document.getElementById('spinner_submit_search');
        var buttonText = document.getElementById('btn_text_submit_search'); 
        svg.style.display = 'none';
        spinner.style.display = 'inline-block';
        buttonText.textContent = 'Please Wait...'; 
        button.disabled = true;  
        refresh_detail_table();
    };  
    $("#detail_table_search").keyup(function(event){
            if(event.keyCode == 13){ refresh_detail_table();  } 
    });  
    function refresh_detail_table() {  
        if ($.fn.DataTable.isDataTable('#kt_form_table')) {
            $('#kt_form_table').DataTable().destroy();
        }  
        detail_table();
    } 

    function detail_table() {  
        var detailTable = $("#kt_form_table").DataTable({
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
                url: "{{ route('inventory_move_in.detail_table') }}",
                type: 'POST',
                data: function (d) { 
                    d._token = $("[name=_token]").val(); 
                    d.DocNum = $("#InptDocNum").val();  
                    d.search = $("#detail_table_search").val();
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
                { data: 'PartNum' },
                { data: 'Qty' },
                { data: 'FromWarehouseDesc'},
                { data: 'ToWarehouseDesc' } 
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
            detailTable.ajax.reload();   
            $("#InptBarcode").focus();
        },500) 

        return true ;
    } 

</script>