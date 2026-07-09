<script>
    $(document).ready(function () { 
        setTimeout(function() {
                $("#QRScan").focus();
                const ourRadio = document.querySelector('input[type="radio"][value="Our"]');
                const supplierRadio = document.querySelector('input[type="radio"][value="Supplier"]');
                const overrideCheckbox = document.querySelector('input[type="checkbox"][value="Override"]');

                const ourQty = document.getElementById("OurQty");
                const ium = document.getElementById("IUM");
                const vendorQty = document.getElementById("VendorQty");
                const pum = document.getElementById("PUM"); 
         
                if (ourRadio.checked) {
                    ourQty.disabled = false;
                    ium.disabled = false;
                    vendorQty.disabled = true;
                    pum.disabled = true;
                } else if (supplierRadio.checked) {
                    ourQty.disabled = true;
                    ium.disabled = true;
                    vendorQty.disabled = false;
                    pum.disabled = true;
                } else {
                    ourQty.disabled = true;
                    ium.disabled = true;
                    vendorQty.disabled = true;
                    pum.disabled = true;
                }

                if (overrideCheckbox.checked) {
                    ourQty.disabled = false;
                    ium.disabled = false;
                    vendorQty.disabled = false;
                    pum.disabled = true;
                }

            },500) 

        var PackLine = '<?php echo $PackLine ?>' ; 
        if (PackLine > 0) { 
            $("#btn_delete_line_gr").removeClass('disabled').prop('disabled', false) ; 
        } else { 
            $("#btn_delete_line_gr").addClass('disabled').prop('disabled', true) ; 
        }  
    })
   </script>
   <div class="card-body">  
        <div id="form_detail_loader" style="text-align: center;">
            <div class="lds-roller mt-10 mb-10" id="lds-roller-form-detail"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div> 
        </div>

        <div class="row" id="form-detail" style="display: none;">  
           

            <div class="col-md-6 mb-5">
                <form>    
                {{-- <div class="form-group mb-5">
                    <label>Scan QR<span class="text-danger">*</span></label>
                    <div class="d-flex">
                        <input type="text" class="form-control me-2" id="QRScan" style="flex: 0 0 75%;"/>  
                            <button type="button" class="btn btn-primary btn-sm me-3" id="btn_search_po_list" style="flex: 0 0 20%;" onclick="getPOList()">
                                <span id="svg_search_po_list" class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"> 
                                        <defs/>
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24"/>
                                            <path d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                            <path d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z" fill="#000000" fill-rule="nonzero"/>
                                        </g>
                                    </svg> 
                                </span> 
                                <span id="spinner_search_po_list" class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;"></span>    
                            </button> 
                        </div>
                    </div>  --}}
                    
                    <script>
                        document.getElementById("QRScan").addEventListener("keydown", function(event) { 
                            if (event.key === "Enter") {
                                event.preventDefault();  
                                let qrValue = event.target.value; 
                                let splitValues = qrValue.split("~"); 
                                console.log(splitValues);
                                if (splitValues.length < 6) { 
                                    Toast.fire({
                                        position: 'top-end',
                                        title: "Barcode Tidak Di Kenal!",
                                        icon: "error"
                                    })
                                    $("#QRScan").val("") ;
                                    return;
                                }  
                                document.getElementById("OurQty").value = splitValues[1]; 
                                
                                // document.getElementById("LotNum").value = splitValues[4]; 
                                 document.getElementById("lotTag").value = splitValues[4]; 

                                document.getElementById("seqnum").value = splitValues[9]; 
                                document.getElementById("PONumDetail").value = splitValues[5]; 
                                document.getElementById("POLineDetail").value = splitValues[6];
                                get_po_line_info();
                            }
                        });
                    </script>

                    <div class="form-group mb-5"> 
                        <div class="d-flex">
                            <div class="col-6 form-group pl-2"> 
                                <label>PO Number <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="PONumDetail" name="PONumDetail" value="{{ $PONum }}"/>
                            </div>    

                            <div class="col-6 form-group" style="padding-left: 5px;"> 
                                <label>PO Line <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="POLineDetail" name="POLineDetail" value="{{ $POLine }}"/>
                            </div> 
                        </div>
                    </div> 

                    <div class="form-group mb-5"> 
                        <label>Part<span class="text-danger">*</span></label>
                        <input type="text" class="form-control  bg-light-primary" id="PartNum" name="PartNum" value="{{ $PartNum }}" readonly/>
                    </div> 

                    <div class="form-group mb-5"> 
                        <label>Description<span class="text-danger">*</span></label>
                        <input type="text" class="form-control  bg-light-primary" id="PartDescription" name="PartDescription" value="{{ $PartDescription }}" readonly/>
                    </div> 

                </form> 
            </div>

            <div class="col-md-6 mb-5">  
                <form>  
                    <div hidden>
                        <div class="d-flex">
                            <label class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input qtyOption" type="radio" name="qtyOption" value="Our" onclick="checkboxAction('Our')" {{ $checkedOur }}/>
                                <span class="form-check-label text-gray-600">Our</span>
                            </label> 
                            <label class="form-check form-check-custom form-check-solid p-3">
                                <input class="form-check-input qtyOption" type="radio" name="qtyOption" value="Supplier" onclick="checkboxAction('Supplier')" {{ $checkedSupplier }}/>
                                <span class="form-check-label text-gray-600">Supplier</span>
                            </label>
                        </div>
                        <input type="hidden" name="seqnum" id="seqnum" value="" />
                    </div>

                    {{-- <div class="form-group mb-5">
                        <div class="d-flex flex-stack">
                            <label class="form-check form-check-custom form-check-solid pt-8 pb-3">
                                <input class="form-check-input convOverride" type="checkbox" value="Override" onclick="checkboxAction(this)" {{ $checkedOverride }}/>
                                <span class="form-check-label text-gray-600">Override Conversion</span>
                            </label>
                        </div>
                    </div> --}}

                    <div class="form-group mb-5">
                        <label>Our Qty<span class="text-danger">*</span></label>
                        <div class="d-flex">
                            <input type="number" class="form-control me-2" id="OurQty" name="OurQty" style="flex: 0 0 70%;" value="{{ $OurQty }}"  disabled />
                            <select class="form-select bg-light-primary" id="IUM" style="flex: 0 0 30%;" onchange="get_qty_info()" disabled>
                                <?php foreach ($UomList AS $row) { ?>
                                    <option value="{{ $row->UOMCode }}" <?php echo $row->selected ?>>{{ $row->UOMCode }}</option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group mb-5">
                        <label>Supplier Qty<span class="text-danger">*</span></label>
                        <div class="d-flex">
                            <input type="number" class="form-control me-2" id="VendorQty" name="VendorQty" style="flex: 0 0 70%;" value="{{ $VendorQty }}" disabled/>
                            <select class="form-select bg-light-primary" id="PUM" style="flex: 0 0 30%;" disabled>
                                <?php foreach ($PumList AS $row) { ?>
                                    <option value="{{ $row->UOMCode }}" <?php echo $row->selected ?>>{{ $row->UOMCode }}</option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <script>

                        document.getElementById('OurQty').addEventListener('blur', function() {
                            if ($("#OurQty").val() > 0) {
                                get_qty_info(); 
                            } 
                        });  

                        document.getElementById("OurQty").addEventListener("keydown", function(event) { 
                            if (event.key === "Enter") {
                                event.preventDefault();  
                                if ($("#OurQty").val() > 0) {
                                    get_qty_info(); 
                                }  
                            }
                        })

                        document.getElementById('VendorQty').addEventListener('blur', function() {
                            if ($("#VendorQty").val() > 0) {
                                get_qty_info(); 
                            } 
                        }); 

                        document.getElementById("VendorQty").addEventListener("keydown", function(event) { 
                            if (event.key === "Enter") {
                                event.preventDefault();  
                                if ($("#VendorQty").val() > 0) {
                                    get_qty_info(); 
                                }  
                            }
                        })

                        function get_qty_info() { 
                            var qtyOption = $('.qtyOption:checked').map(function() { return $(this).val(); }).get();
                            var inputOurQty = $("#OurQty").val() ; 
                            var ium = $("#IUM").val() ; 
                            var vendorQty = $("#VendorQty").val() ; 
                            var pum = $("#PUM").val() ;  
                            var convOverride = ($('.convOverride:checked').val() == 'Override' ? true : false); 
                            if (convOverride === true) { 
                                return false;
                            } 
                            if (qtyOption == 'Our' && inputOurQty <= 0) { 
                                return false;
                            } else if (qtyOption == 'Supplier' && vendorQty <= 0) {
                                return false;
                            }
                            
                            var poNum = $("#PONumDetail").val() ;
                            var poLine = $("#POLineDetail").val() ; 
                            var packLine = $("#InptPackLine").val() ;  
                            var warehouseCode = $("#WareHouseCode").val() ; 
                            var binNum = $("#BinNum").val() ; 
                            var LotNum = $("#LotNum").val() ; 
                            var tranReference = $("#tranReference").val() ; 
                            var receivedComplete = $("#receivedComplete").val() ;   
                            var received = $("#received").val() ; 
                            var receiptDate = $("#EntryDate").val() ;  
                            var temp_id = $("#temp_id").val(); 
                            var token = $("[name=_token]").val(); 
                            var JobNum = $("#JobNum").val();  
                            var trc_unix_id = $("#temp_id").val();  
                            var PartNum = $("#PartNum").val();  
                            var lotNum = $("#LotNum").val();  
                            var AssemblySeq = $("#AssemblySeq").val();  
                            var JobSeq = $("#JobSeq").val();  

                            

                            if (poNum <= 0 || poNum == null) {
                                $("#PONumDetail").focus();  
                                Toast.fire({ 
                                    position: 'top-end',
                                    title: "Silahkan PO Number di isi!",
                                    icon:"error"
                                }) 
                                return false;
                            } 
                            if (poLine == '' || poLine == null) {
                                $("#POLineDetail").focus();  
                                Toast.fire({ 
                                    position: 'top-end',
                                    title: "Silahkan PO Line di isi!",
                                    icon:"error"
                                }) 
                                return false;
                            }  

                            document.getElementById("IUM").disabled = false;
                            document.getElementById("PUM").disabled = false;
                            document.getElementById("OurQty").disabled = false;
                            document.getElementById("VendorQty").disabled = false;

                            var string = "&_token="+token+"&temp_id="+temp_id+"&poNum="+poNum+"&poLine="+poLine+"&packLine="+packLine+"&qtyOption="+qtyOption+"&inputOurQty="+inputOurQty+"&ium="+ium+"&vendorQty="+vendorQty+"&pum="+pum+"&convOverride="+convOverride+"&warehouseCode="+warehouseCode+"&binNum="+binNum+"&tranReference="+tranReference+"&receivedComplete="+receivedComplete+"&received="+received+"&receiptDate="+receiptDate+"&JobNum="+JobNum+"&PartNum="+PartNum+"&lotNum="+lotNum+"&AssemblySeq="+AssemblySeq+"&JobSeq="+JobSeq ;
                            var button = document.getElementById('btn_update_line_gr');
                            var svg = document.getElementById('svg_update_line_gr');
                            var spinner = document.getElementById('spinner_update_line_gr');
                            var buttonText = document.getElementById('btn_text_update_line_gr'); 
                            svg.style.display = 'none';
                            spinner.style.display = 'inline-block';
                            buttonText.textContent = 'Please Wait...'; 
                            button.disabled = true;  
                            $.ajax({
                                type	: 'POST',
                                url	: "{{ route('receipt_entry.get_qty_info') }}",
                                data	: string,
                                cache	: false, 
                                dataType : 'json',
                                success : function(data){    

                                    svg.style.display = 'inline-block';
                                    spinner.style.display = 'none';
                                    buttonText.textContent = 'Update Line'; 
                                    button.disabled = false; 

                                    const ourRadio = document.querySelector('input[type="radio"][value="Our"]');
                                    const supplierRadio = document.querySelector('input[type="radio"][value="Supplier"]');
                                    const overrideCheckbox = document.querySelector('input[type="checkbox"][value="Override"]');

                                    const ourQty = document.getElementById("OurQty");
                                    const ium = document.getElementById("IUM");
                                    const vendorQty = document.getElementById("VendorQty");
                                    const pum = document.getElementById("PUM"); 
 
                                     if (ourRadio.checked) { 
                                        ourQty.disabled = false;
                                        ium.disabled = false;
                                        vendorQty.disabled = true;
                                        pum.disabled = true;
                                    } else if (supplierRadio.checked) { 
                                        ourQty.disabled = true;
                                        ium.disabled = true;
                                        vendorQty.disabled = false;
                                        pum.disabled = true;
                                    } else { 
                                        ourQty.disabled = true;
                                        ium.disabled = true;
                                        vendorQty.disabled = true;
                                        pum.disabled = true;
                                    }

                                    if (overrideCheckbox.checked) { 
                                        ourQty.disabled = false;
                                        ium.disabled = false;
                                        vendorQty.disabled = false;
                                        pum.disabled = true;
                                    }


                                    if (data.code == 200) {
                                        if (data.transaction_code == 200) { 
                                            if (qtyOption == 'Our') {
                                                $("#VendorQty").val(data.thisTranQty) ; 
                                            } else {
                                                $("#OurQty").val(data.thisTranQty) ; 
                                            }
                                        } else {
                                            Toast.fire({
                                                position: 'top-end',
                                                title: data.transaction_status,
                                                icon:"error"
                                            })
                                        }
                                    } else {
                                        Toast.fire({
                                            position: 'top-end',
                                            title: data.status,
                                            icon:"error"
                                        })
                                    }
                                    
                                },
                                    error: function( jqXHR, textStatus ) { 
                                        Toast.fire({
                                            position: 'top-end',
                                            title: " Please reload and try again! ",
                                            icon:"error"
                                        })  
                                        svg.style.display = 'inline-block';
                                        spinner.style.display = 'none';
                                        buttonText.textContent = 'Update Line'; 
                                        button.disabled = false; 
                                } 
                            })
                        }
                    </script> 

                    <div class="form-group mb-5">
                        <label for="JobNum">JobNum</label>
                        <input type="text" class="form-control bg-light-primary" id="JobNum" value="{{ $JobNum }}" readonly/>
                    </div>
                </form>
            </div> 

            <script> 

                    function checkboxAction(checkbox) {    
                        var qtyOption = $('.qtyOption:checked').map(function() { return $(this).val(); }).get();  
                        const ourRadio = document.querySelector('input[type="radio"][value="Our"]');
                        const supplierRadio = document.querySelector('input[type="radio"][value="Supplier"]');
                        const overrideCheckbox = document.querySelector('input[type="checkbox"][value="Override"]');

                        const ourQty = document.getElementById("OurQty");
                        const ium = document.getElementById("IUM");
                        const vendorQty = document.getElementById("VendorQty");
                        const pum = document.getElementById("PUM"); 

                        // Handle Override checkbox action
                        if (ourRadio.checked) {
                            // Our selected: enable OurQty and IUM, disable VendorQty and PUM
                            ourQty.disabled = false;
                            ium.disabled = false;
                            vendorQty.disabled = true;
                            pum.disabled = true;
                        } else if (supplierRadio.checked) {
                            // Supplier selected: enable VendorQty and PUM, disable OurQty and IUM
                            ourQty.disabled = true;
                            ium.disabled = true;
                            vendorQty.disabled = false;
                            pum.disabled = true;
                        } else {
                            // Default case: all fields disabled
                            ourQty.disabled = true;
                            ium.disabled = true;
                            vendorQty.disabled = true;
                            pum.disabled = true;
                        }

                        if (overrideCheckbox.checked) {
                            // Override checked: enable OurQty, IUM, and VendorQty, keep PUM disabled
                            ourQty.disabled = false;
                            ium.disabled = false;
                            vendorQty.disabled = false;
                            pum.disabled = true;
                        }
                    }


            </script>

            <hr>

            <div class="col-md-6 mb-5">
                <form> 
                    <div class="form-group mb-5"> 
                        <label>Pack Line<span class="text-danger">*</span></label> 
                        <input type="text" class="form-control bg-light-primary" id="InptPackLine" value="{{ $PackLine }}" readonly/>  
                    </div> 
                    <div class="form-group mb-5">
                        <label for="LotNum">LotNum <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="LotNum" value="{{ $LotNum }}"/>
                    </div>  
                    <input type="hidden" id="lotTag" name="lotTag" value="" /> 
                </form>
            </div>

            <div class="col-md-6 mb-5">
                <form> 
                    <div class="form-group mb-5"> 
                        <label>To Warehouse<span class="text-danger">*</span></label>
                        <div class="d-flex">  
                            <select class="form-select bg-transparent" data-kt-select2="true" data-allow-clear="false" data-hide-search="true" name="WareHouseCode" id="WareHouseCode">
                                <?php foreach ($WHList AS $row) { ?>
                                    <option value="{{ $row->WarehouseCode }}" <?php echo $row->selected ?>>{{ $row->Description }}</option>
                                <?php } ?>
                            </select>
                        </div>
                    </div> 
                    <div class="form-group mb-5">
                        <label for="LotNum">Bin<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="BinNum" value="{{ $BinNum }}"/>
                    </div>  
                </form>
            </div> 

            <div hidden>
            <hr>
 
                <div class="col-md-6 mb-5">
                    <form>    
                        <div class="form-group mb-5"> 
                            <label>Assembly Seq</label>
                            <input type="number" class="form-control bg-light-primary" id="AssemblySeq" name="AssemblySeq" value="{{ $AssemblySeq }}" readonly/>
                        </div>
                    </form>
                </div>

                <div class="col-md-6 mb-5">
                    <form> 
                        <div class="form-group mb-5"> 
                            <label>Job Seq</label>
                            <input type="number" class="form-control bg-light-primary" id="JobSeq" name="JobSeq" value="{{ $JobSeq }}" readonly/>
                        </div>

                        <div class="form-group mb-5"> 
                            <label>Job Required Qty</label>
                            <input type="number" class="form-control bg-light-primary" id="JobRequiredQty" name="JobRequiredQty" value="{{ $JobRequiredQty }}" readonly/>
                        </div>  
                    </form> 
                </div>
            </div>
            <hr>  


            {{-- <div class="col-md-6 mb-5" style="text-align: left;"> 
            <button type="button" class="btn btn-light-success btn-sm me-3" id="btn_add_detail_document" onclick="addNewForm()" >
                    <span id="svg_add_detail_document" class="svg-icon svg-icon-2">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"> 
                            <defs/>
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"/>
                                <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) " x="4" y="11" width="16" height="2" rx="1"/>
                            </g>
                        </svg> 
                    </span> 
                    <span id="spinner_add_detail_document" class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;"></span>       
                    <span id="btn_text_add_detail_document">Add Line</span>
                </button> 
            </div> --}}
            {{-- <div class="col-md-6" style="text-align: right;">   
                <button type="button" class="btn btn-light-danger btn-sm mr-2 mt-2" id="btn_delete_line_gr" onclick="delete_line_gr_confirm(document.getElementById('PartNum').value)">
                    <span class="svg-icon svg-icon-primary svg-icon-2" id="svg_delete_line_gr">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">  
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"/>
                                <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"/>
                                <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"/>
                            </g>
                        </svg>
                    </span>
                    <span id="btn_text_delete_line_gr">Delete Line</span>
                    <span id="spinner_delete_line_gr" class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;"></span>
                </button>  

                <button type="button" class="btn btn-primary btn-sm mr-2 mt-2" id="btn_update_line_gr" onclick="update_line_gr()">
                    <span class="svg-icon svg-icon-primary svg-icon-2" id="svg_update_line_gr">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">  
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <polygon points="0 0 24 0 24 24 0 24"/>
                                <path d="M17,4 L6,4 C4.79111111,4 4,4.7 4,6 L4,18 C4,19.3 4.79111111,20 6,20 L18,20 C19.2,20 20,19.3 20,18 L20,7.20710678 C20,7.07449854 19.9473216,6.94732158 19.8535534,6.85355339 L17,4 Z M17,11 L7,11 L7,4 L17,4 L17,11 Z" fill="#000000" fill-rule="nonzero"/>
                                <rect fill="#000000" opacity="0.3" x="12" y="4" width="3" height="5" rx="0.5"/>
                            </g>
                        </svg>
                    </span>
                    <span id="btn_text_update_line_gr">Update Line</span>
                    <span id="spinner_update_line_gr" class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;"></span>    
                </button>  
            </div> --}}
        </div>
    </div>  
  
    <script> 
        function selectPOList(PONum, POLine) {  
            document.getElementById('btn_close_po_list').click() ;   
            $("#PONumDetail").val(PONum);
            $("#POLineDetail").val(POLine); 
            get_po_line_info();
        }

        function addNewForm()  { 
            $("#pack_line").val(""); 
            document.getElementById('kt_form_detail_tab').click() ;   
        }; 
        document.getElementById("PONumDetail").addEventListener("keypress", function(event) { 
            if (event.key === "Enter") {
                event.preventDefault();  
                if ($("#POLineDetail").val() > 0) {
                    get_po_line_info(); 
                }  
            }
        })
        document.getElementById("POLineDetail").addEventListener("keypress", function(event) { 
            if (event.key === "Enter") {
                event.preventDefault();  
                if ($("#PONumDetail").val() > 0) {
                    get_po_line_info();
                }  
            }
        })
        document.getElementById('PONumDetail').addEventListener('blur', function() {
            if ($("#POLineDetail").val() > 0) {
                get_po_line_info(); 
            } 
        }); 
        document.getElementById('POLineDetail').addEventListener('blur', function() {
            if ($("#PONumDetail").val() > 0) {
                get_po_line_info();
            } 
        });

         $('#IUM').select2({
            minimumResultsForSearch: Infinity
        });
         $('#PUM').select2({
            minimumResultsForSearch: Infinity
        }); 
        $('#WareHouseCode').select2();

        function get_po_line_info() { 
            var button = document.getElementById('btn_search_po_list');
            var svg = document.getElementById('svg_search_po_list');
            var spinner = document.getElementById('spinner_search_po_list');
            var poNum = $("#PONumDetail").val() ;
            var poLine = $("#POLineDetail").val() ;
            var vendorNum = $("#VendorNum").val() ;
            var temp_id = $("#temp_id").val() ;
            var packSlip = $("#PackSlip").val().replace(/[\'\"\,~\.\?]/g, '') ;    
            if (PONum <= 0 || poLine <= 0) { 
                return false;
            }    
            var token = $("[name=_token]").val();  
            var string = "&_token="+token+"&temp_id="+temp_id+"&poNum="+poNum+"&poLine="+poLine+"&packSlip="+packSlip+"&vendorNum="+vendorNum ;
            svg.style.display = 'none';
            spinner.style.display = 'inline-block'; 
            button.disabled = true;   
            $.ajax({
                type	: 'POST',
                url	: "{{ route('receipt_entry.get_po_line_info') }}",
                data	: string,
                cache	: false, 
                dataType : 'json',
                success : function(data){  
                    svg.style.display = 'inline-block';
                    spinner.style.display = 'none'; 
                    button.disabled = false;    
                    if (data.code == 200) {
                        if (data.transaction_code == 200) {    
                            $('#WareHouseCode').val(data.whseDefault).trigger('change');
                            $("#BinNum").val(data.binDefault);  
                            $("#PartNum").val(data.partNum);  
                            $("#PartDescription").val(data.partDescription);      
                            $('#IUM').val(data.ium).trigger('change');
                            $('#PUM').val(data.pum).trigger('change');  
                            $("#JobNum").val(data.jobNum);  
                            $("#JobSeq").val(data.jobSeq);  
                            $("#AssemblySeq").val(data.assemblySeq);  
                            $("#JobRequiredQty").val(data.jobRequiredQty);  
                            $("#QRScan").val("") ;
                            $("#OurQty").focus() ;
                        } else {
                            $('#WareHouseCode').val("05-00-00").trigger('change');
                            $("#BinNum").val("");  
                            $("#PartNum").val("");  
                            $("#PartDescription").val("");      
                            $('#IUM').val("PCS").trigger('change');
                            $('#PUM').val("PCS").trigger('change');  
                            $("#JobNum").val("");  
                            $("#JobSeq").val("");  
                            $("#AssemblySeq").val("");  
                            $("#JobRequiredQty").val("");  
                            $("#OurQty").val("") ; 
                            $("#QRScan").val("") ;
                            $("#QRScan").focus() ;
                            Toast.fire({
                                position: 'top-end',
                                title: data.transaction_status,
                                icon:"error"
                            })
                        }
                    } else {
                        Toast.fire({
                            position: 'top-end',
                            title: data.status,
                            icon:"error"
                        })
                    }
                    
                },
                    error: function( jqXHR, textStatus ) { 
                        Toast.fire({
                            position: 'top-end',
                            title: " Please reload and try again! ",
                            icon:"error"
                        }) 
                        svg.style.display = 'inline-block';
                        spinner.style.display = 'none'; 
                        button.disabled = false;  
                } 
            })
        }

        function delete_line_gr_confirm(partNum) { 
            document.getElementById("IUM").disabled = false;
            document.getElementById("PUM").disabled = false;
            document.getElementById("OurQty").disabled = false;
            document.getElementById("VendorQty").disabled = false;
            return Swal.fire({
                text: "Yakin Hapus ? "+ partNum,
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
                    var poNum = $("#PONumDetail").val() ;
                    var poLine = $("#POLineDetail").val() ; 
                    var packLine = $("#InptPackLine").val() ;  
                    var inputOurQty = $("#OurQty").val() ; 
                    var ium = $("#IUM").val() ; 
                    var vendorQty = $("#VendorQty").val() ; 
                    var pum = $("#PUM").val() ;  
                    var warehouseCode = $("#WareHouseCode").val() ; 
                    var binNum = $("#BinNum").val() ; 
                    var LotNum = $("#LotNum").val() ; 
                    var tranReference = $("#tranReference").val() ; 
                    var receivedComplete = $("#receivedComplete").val() ;  
                    var qtyOption = $('.qtyOption:checked').map(function() { return $(this).val(); }).get();
                    var convOverride = ($('.convOverride:checked').val() == 'Override' ? true : false);
                    var received = $("#received").val() ; 
                    var receiptDate = $("#EntryDate").val() ;  
                    var temp_id = $("#temp_id").val(); 
                    var token = $("[name=_token]").val(); 
                    var JobNum = $("#JobNum").val();  
                    var trc_unix_id = $("#temp_id").val();  
                    var PartNum = $("#PartNum").val()
                    var PartName = $("#PartDescription").val();  
                    var lotNum = $("#LotNum").val();   
                    var AssemblySeq = $("#AssemblySeq").val();  
                    var JobSeq = $("#JobSeq").val();  
                    var seqnum = $("#seqnum").val();  
                    var rowMod = "D";
                    if (poNum <= 0 || poNum == null) {
                        $("#PONumDetail").focus();  
                        Toast.fire({ 
                            position: 'top-end',
                            title: "Silahkan PO Number di isi!",
                            icon:"error"
                        }) 
                        return false;
                    } 
                    if (poLine == '' || poLine == null) {
                        $("#POLineDetail").focus();  
                        Toast.fire({ 
                            position: 'top-end',
                            title: "Silahkan PO Line di isi!",
                            icon:"error"
                        }) 
                        return false;
                    }  
                    var button = document.getElementById('btn_delete_line_gr');
                    var svg = document.getElementById('svg_delete_line_gr');
                    var spinner = document.getElementById('spinner_delete_line_gr');
                    var buttonText = document.getElementById('btn_text_delete_line_gr'); 
                    svg.style.display = 'none';
                    spinner.style.display = 'inline-block';
                    buttonText.textContent = 'Please Wait...'; 
                    button.disabled = true;   
                    $.ajax({
                        type	: 'POST',
                        url	: "{{ route('receipt_entry.update_line_gr') }}",
                        data	: {
                            _token: token,
                temp_id: temp_id,
                poNum: poNum,
                poLine: poLine,
                packLine: packLine,
                qtyOption: qtyOption,
                inputOurQty: inputOurQty,
                ium: ium,
                vendorQty: vendorQty,
                pum: pum,
                convOverride: convOverride,
                warehouseCode: warehouseCode,
                binNum: binNum,
                tranReference: tranReference,
                receivedComplete: receivedComplete,
                received: received,
                receiptDate: receiptDate,
                JobNum: JobNum,
                PartNum: PartNum,
                PartName: PartName,
                lotNum: lotNum,
                AssemblySeq: AssemblySeq,
                rowMod: rowMod,
                JobSeq: JobSeq,
                seqnum: seqnum
                        },
                        cache	: false, 
                        dataType : 'json',
                        success : function(data){     
                            svg.style.display = 'inline-block';
                            spinner.style.display = 'none';
                            buttonText.textContent = 'Delete Line'; 
                            button.disabled = false; 

                            if (data.code == 200) {
                                if (data.transaction_code == 200) {
                                    $("#pack_line").val(""); 
                                    getForm(); 
                                    Toast.fire({
                                        position: 'top-end',
                                        title: "Data berhasil update",
                                        icon:"success"
                                    }) 
                                } else {
                                    Toast.fire({
                                        position: 'top-end',
                                        title: data.transaction_status,
                                        icon:"error"
                                    })
                                }
                            } else {
                                Toast.fire({
                                    position: 'top-end',
                                    title: data.status,
                                    icon:"error"
                                })
                            }
                            
                        },
                            error: function( jqXHR, textStatus ) { 
                                Toast.fire({
                                    position: 'top-end',
                                    title: " Please reload and try again! ",
                                    icon:"error"
                                })

                                svg.style.display = 'inline-block';
                                spinner.style.display = 'none';
                                buttonText.textContent = 'Delete Line'; 
                                button.disabled = false; 
                        } 
                    })
                    
                } else { 
                    console.log('Penghapusan dibatalkan');
                }
            });
        } 

        function update_line_gr() { 
            document.getElementById("IUM").disabled = false;
            document.getElementById("PUM").disabled = false;
            document.getElementById("OurQty").disabled = false;
            document.getElementById("VendorQty").disabled = false;
            var poNum = $("#PONumDetail").val() ;
            var poLine = $("#POLineDetail").val() ; 
            var packLine = $("#InptPackLine").val() ;  
            var inputOurQty = $("#OurQty").val() ; 
            var ium = $("#IUM").val() ; 
            var vendorQty = $("#VendorQty").val() ; 
            var pum = $("#PUM").val() ;  
            var warehouseCode = $("#WareHouseCode").val() ; 
            var binNum = $("#BinNum").val() ; 
            var LotNum = $("#LotNum").val() ; 
            var tranReference = $("#tranReference").val() ; 
            var receivedComplete = $("#receivedComplete").val() ;  
            var qtyOption = $('.qtyOption:checked').map(function() { return $(this).val(); }).get();
            var convOverride = ($('.convOverride:checked').val() == 'Override' ? true : false);
            var received = $("#received").val() ; 
            var receiptDate = $("#EntryDate").val() ;  
            var temp_id = $("#temp_id").val(); 
            var token = $("[name=_token]").val(); 
            var JobNum = $("#JobNum").val();  
            var trc_unix_id = $("#temp_id").val();  
            var PartNum = $("#PartNum").val();  
            var PartName = $("#PartDescription").val();
            var lotNum = $("#LotNum").val();  
            var AssemblySeq = $("#AssemblySeq").val();  
            var JobSeq = $("#JobSeq").val();  
            var lotTag = $("#lotTag").val();
            var seqnum = $("#seqnum").val();
            if (poNum <= 0 || poNum == null) {
                $("#PONumDetail").focus();  
                Toast.fire({ 
                    position: 'top-end',
                    title: "Silahkan PO Number di isi!",
                    icon:"error"
                }) 
                return false;
            } 
            if (poLine == '' || poLine == null) {
                $("#POLineDetail").focus();  
                Toast.fire({ 
                    position: 'top-end',
                    title: "Silahkan PO Line di isi!",
                    icon:"error"
                }) 
                return false;
            }  
            var button = document.getElementById('btn_update_line_gr');
            var svg = document.getElementById('svg_update_line_gr');
            var spinner = document.getElementById('spinner_update_line_gr');
            var buttonText = document.getElementById('btn_text_update_line_gr'); 
            svg.style.display = 'none';
            spinner.style.display = 'inline-block';
            buttonText.textContent = 'Please Wait...'; 
            button.disabled = true;  
            $.ajax({
                type	: 'POST',
                url	: "{{ route('receipt_entry.update_line_gr') }}",
                data	: {
                    _token: token,
                temp_id: temp_id,
                poNum: poNum,
                poLine: poLine,
                packLine: packLine,
                qtyOption: qtyOption,
                inputOurQty: inputOurQty,
                ium: ium,
                vendorQty: vendorQty,
                pum: pum,
                convOverride: convOverride,
                warehouseCode: warehouseCode,
                binNum: binNum,
                tranReference: tranReference,
                receivedComplete: receivedComplete,
                received: received,
                receiptDate: receiptDate,
                JobNum: JobNum,
                PartNum: PartNum,
                PartName: PartName,
                lotNum: lotNum,
                AssemblySeq: AssemblySeq,
                // rowMod: rowMod,
                JobSeq: JobSeq,
                seqnum: seqnum
                },
                cache	: false, 
                dataType : 'json',
                success : function(data){    

                    svg.style.display = 'inline-block';
                    spinner.style.display = 'none';
                    buttonText.textContent = 'Update GR'; 
                    button.disabled = false; 

                    const ourRadio = document.querySelector('input[type="radio"][value="Our"]');
                    const supplierRadio = document.querySelector('input[type="radio"][value="Supplier"]');
                    const overrideCheckbox = document.querySelector('input[type="checkbox"][value="Override"]');

                    const ourQty = document.getElementById("OurQty");
                    const ium = document.getElementById("IUM");
                    const vendorQty = document.getElementById("VendorQty");
                    const pum = document.getElementById("PUM"); 

                    // Handle Override checkbox action
                    if (ourRadio.checked) {
                        // Our selected: enable OurQty and IUM, disable VendorQty and PUM
                        ourQty.disabled = false;
                        ium.disabled = false;
                        vendorQty.disabled = true;
                        pum.disabled = true;
                    } else if (supplierRadio.checked) {
                        // Supplier selected: enable VendorQty and PUM, disable OurQty and IUM
                        ourQty.disabled = true;
                        ium.disabled = true;
                        vendorQty.disabled = false;
                        pum.disabled = true;
                    } else {
                        // Default case: all fields disabled
                        ourQty.disabled = true;
                        ium.disabled = true;
                        vendorQty.disabled = true;
                        pum.disabled = true;
                    }

                    if (overrideCheckbox.checked) {
                        // Override checked: enable OurQty, IUM, and VendorQty, keep PUM disabled
                        ourQty.disabled = false;
                        ium.disabled = false;
                        vendorQty.disabled = false;
                        pum.disabled = true;
                    }


                    if (data.code == 200) {
                        if (data.transaction_code == 200) {
                            $("#InptPackLine").val(data.packLine);   
                            Toast.fire({
                                position: 'top-end',
                                title: "Data berhasil update",
                                icon:"success"
                            }) 
                            $("#btn_delete_line_gr").removeClass('disabled').prop('disabled', false) ; 
                        } else {
                            Toast.fire({
                                position: 'top-end',
                                title: data.transaction_status,
                                icon:"error"
                            })
                        }
                    } else {
                        Toast.fire({
                            position: 'top-end',
                            title: data.status,
                            icon:"error"
                        })
                    }
                    
                },
                    error: function( jqXHR, textStatus ) { 
                        Toast.fire({
                            position: 'top-end',
                            title: " Please reload and try again! ",
                            icon:"error"
                        })
                        document.getElementById("IUM").disabled = true;
                        document.getElementById("PUM").disabled = true;  
                        svg.style.display = 'inline-block';
                        spinner.style.display = 'none';
                        buttonText.textContent = 'Update GR'; 
                        button.disabled = false; 
                } 
            })
        }
        
    </script>