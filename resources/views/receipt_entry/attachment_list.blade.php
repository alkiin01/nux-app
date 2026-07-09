        <div class="card-body">  
            <div id="form_attachment_loader" style="text-align: center;">
                <div class="lds-roller mt-10 mb-10" id="lds-roller-form-attachment"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div> 
            </div> 
            <div class="row g-6 g-xl-9 mb-6 mb-xl-9" id="form-attachment" style="display: none;">  
                <?php if ($count > 0) { foreach ($list AS $row) { 
                    $ext = explode(".", $row->XFileName) ;
                    $ext = $ext[1] ;
                    ?>
                    <div class="col-md-6 col-lg-4 col-xl-3" onclick="showAttachment('<?= $row->XFileRefNum ?>', '<?= $ext ?>')">
                        <div class="card border-primary border border-dashed h-100">
                            <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                                <a href="#" class="text-gray-800 text-hover-primary d-flex flex-column">
                                    <div class="symbol symbol-60px mb-5">
                                        <img src="<?= env('APP_ASSETS') ?>assets/media/svg/files/doc.svg" alt="" style="width: 100%;"/>
                                    </div> 
                                    <div class="fs-5 fw-bolder mb-2">
                                    <?php echo 'File '.$ext ;  ?>    
                                    </div> 
                                </a> 
                                <div class="fs-7 fw-bold text-gray-400">{{ $row->XFileDesc }}</div> 
                                <input type="text" hidden readonly id="DrawSeq" value="{{ $row->XFileRefNum }}">
                                <input type="text" hidden readonly id="DrawDesc" value="{{ $row->XFileDesc }}">
                            </div> 
                        </div> 
                    </div> 
                <?php } } else { ?>  
                <div class="col-md-12 col-lg-12 col-xl-12"> 
                    <div class="card h-100 flex-center bg-light-primary border-primary border border-dashed p-8"> 
                        <img src="<?php echo env('APP_ASSETS') ?>assets/media/svg/files/upload.svg" class="" alt="" /> <br>
                        <a href="#" class="text-hover-primary fs-5 fw-bolder mb-2">No File Upload</a> 
                        <div class="fs-7 fw-bold text-gray-400">We didn't find the file</div> 
                    </div> 
                </div>
                <?php } ?> 
            </div>  
            <hr>  
            <div class="col-md-12" style="text-align: right;">     
                <button type="button" class="btn btn-primary btn-sm mr-2 mt-2" onclick="getFormAttachment()">
                    <span class="svg-icon svg-icon-primary svg-icon-2" id="svg_add_attachment">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">  
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"/>
                                <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) " x="4" y="11" width="16" height="2" rx="1"/>
                            </g>
                        </svg>
                    </span>
                    <span id="btn_text">Add Attachment</span>   
                </button>   
            </div> 
        </div> 
        <div class="modal bg-white fade" tabindex="-1" id="kt_modal_show">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content shadow-none">
                    <div class="modal-header">
                        <h5 class="modal-title">Attachment Preview</h5>  
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                            <span class="svg-icon svg-icon-2x"></span>
                        </div> 
                    </div> 
                    <div class="modal-body text-center">
                        <div class="lds-roller mt-10" id="lds-roller-attachment-show"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
                        <div id="attachment_view"></div>
                    </div> 
                    <div class="modal-footer">  
                        <div class="col-md-12" style="text-align: right;">   
                            <button type="button" class="btn btn-light-danger btn-sm mr-2 mt-2" id="btn_delete_attachment" onclick="delete_attachment()">
                                <span class="svg-icon svg-icon-primary svg-icon-2" id="svg_delete_attachment">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">  
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24"/>
                                            <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"/>
                                            <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"/>
                                        </g>
                                    </svg>
                                </span>
                                <span id="btn_text_delete_attachment">Delete</span>
                                <span id="spinner_delete_attachment" class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;"></span>
                            </button>   
                            
                            <button type="button" class="btn btn-primary btn-sm mr-2 mt-2" data-bs-dismiss="modal" id="btn_close_form_preview">
                                <span class="svg-icon svg-icon-primary svg-icon-2" id="svg_close_attachment">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">  
                                        <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
                                            <rect x="0" y="7" width="16" height="2" rx="1"/>
                                            <rect opacity="0.3" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000) " x="0" y="7" width="16" height="2" rx="1"/>
                                        </g>
                                    </svg>
                                </span>
                                <span id="btn_text_close">Close</span>  
                            </button> 
                        </div>
                    </div> 
                </div>
            </div>
        </div> 
        <div class="modal fade" id="kt_modal_attachment_form" tabindex="-1" aria-hidden="true"> 
            <div class="modal-dialog mw-650px"> 
                <div class="modal-content"> 
                    <div class="modal-header pb-0 border-0 justify-content-end"> 
                        <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" id="btn_close_form_attachment"> 
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
                            <h1 class="mb-3">Attachment Form</h1> 
                            <div class="text-muted fw-bold fs-5">Please make sure all data is correct !</div>
                        </div>   
                        <form id="uploadForm" enctype="multipart/form-data"> 
                            <div class="form-group mb-5"> 
                                <label>Description <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="" id="description_attachment"/> 
                            </div> 
                            <div class="form-group mb-5"> 
                                <label>File <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" placeholder="" id="file_attachment"/> 
                            </div> 
                        </form>
                        <hr> 
                        <div class="col-md-12" style="text-align: right;">     
                            <button type="button" class="btn btn-primary btn-sm mr-2 mt-2" id="btn_submit_attachment" onclick="submitAttachment()">
                                <span class="svg-icon svg-icon-primary svg-icon-2" id="svg_submit_attachment">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">  
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <polygon points="0 0 24 0 24 24 0 24"/>
                                            <path d="M17,4 L6,4 C4.79111111,4 4,4.7 4,6 L4,18 C4,19.3 4.79111111,20 6,20 L18,20 C19.2,20 20,19.3 20,18 L20,7.20710678 C20,7.07449854 19.9473216,6.94732158 19.8535534,6.85355339 L17,4 Z M17,11 L7,11 L7,4 L17,4 L17,11 Z" fill="#000000" fill-rule="nonzero"/>
                                            <rect fill="#000000" opacity="0.3" x="12" y="4" width="3" height="5" rx="0.5"/>
                                        </g>
                                    </svg>
                                </span>
                                <span id="btn_text_submit_attachment">Submit</span>
                                <span id="spinner_submit_attachment" class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;"></span>    
                            </button>  
                        </div>
                    </div> 
                </div> 
            </div> 
        </div>

<script>  
    function submitAttachment() {  
        const formData = new FormData();
        const fileInput = document.getElementById('file_attachment').files[0];
        const description = document.getElementById('description_attachment').value.replace(/[\'\"\,~\.\?]/g, '') ;
        const temp_id = document.getElementById('temp_id').value ;

        formData.append('file', fileInput);
        formData.append('description', description);
        formData.append('temp_id', temp_id);

        var button = document.getElementById('btn_submit_attachment');
        var svg = document.getElementById('svg_submit_attachment');
        var spinner = document.getElementById('spinner_submit_attachment');
        var buttonText = document.getElementById('btn_text_submit_attachment'); 

        svg.style.display = 'none';
        spinner.style.display = 'inline-block';
        buttonText.textContent = 'Please Wait...'; 
        button.disabled = true;

        fetch("{{ route('receipt_entry.upload_attachment') }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            svg.style.display = 'inline-block';
            spinner.style.display = 'none';
            buttonText.textContent = 'Submit'; 
            button.disabled = false; 
            if (data.code === 200) {
                if (data.transaction_code === 200) {
                    $("#btn_close_form_attachment").click(); 
                    getAttachmentList();
                    Toast.fire({
                        position: 'top-end',
                        title: 'File berhasil diunggah dan diproses.',
                        icon:"success"
                    }) 
                } else {
                    Toast.fire({
                        position: 'top-end',
                        title: 'Diproses Sebagian',
                        text: 'File berhasil diunggah tetapi ada kendala dalam pemrosesan: ' + (data.transaction_status || 'Status tidak tersedia'),
                        icon:"info"
                    }) ;
                }
            } else {
                Toast.fire({
                    position: 'top-end',
                    title: 'Diproses Sebagian',
                    text: 'Gagal mengunggah file: ' + (data.desc || 'Terjadi kesalahan pada server.'),
                    icon:"error"
                });
            }
        })
        .catch(error => {
            svg.style.display = 'inline-block';
            spinner.style.display = 'none';
            buttonText.textContent = 'Submit'; 
            Toast.fire({
                position: 'top-end',
                title: 'Diproses Sebagian',
                text: 'Gagal mengunggah file. Silakan coba lagi.',
                icon:"error"
            }); 
            console.error('Error:', error);
        });
    }

    function showAttachment(id, ext) { 
        var token = $("[name=_token]").val(); 
        var string = "&_token="+token+"+&seqNum="+id ;
        $("#kt_modal_show").modal('show'); 
        $("#lds-roller-attachment-show").css("display", "");  
        $('#attachment_view').empty();
        $.ajax({
            type	: 'POST',
            url	: "{{ route('receipt_entry.show_attachment') }}",
            data	: string,
            cache	: false,
            dataType	: 'json',
            success : function(data){   
                $("#lds-roller-attachment-show").css("display", "none");   
                if (ext == "pdf") {  
                    const base64String = data.draw ;  
                    const pdfContainer = document.getElementById('attachment_view');
                    const embedElement = document.createElement('iframe');  
                    embedElement.src = `data:application/pdf;base64,${base64String}`;
                    embedElement.type = 'application/pdf';
                    embedElement.width = '100%';
                    embedElement.height = '600px';
                    pdfContainer.appendChild(embedElement);
                } else {
                    var attachment = 'data:image/'+ext+';base64,'+data.draw ; 
                    const imgElement = $('<img>', { 
                        class: 'profile-user-img img-responsive',
                        style: 'background: white; width: 100%;',
                        alt: '', 
                        src: attachment  
                    }); 
                    imgElement.appendTo('#attachment_view');  
                }  
            } 
        }) 
    } 
    
    function getFormAttachment() { 
        var token = $("[name=_token]").val(); 
        var string = "&_token="+token ;
        $("#kt_modal_attachment_form").modal('show'); 
    }


    function delete_attachment() { 
        var Descr = $("#DrawDesc").val() ; 
            return Swal.fire({
                text: "Yakin Hapus Lampiran ? "+ Descr ,
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
                    var poNum = $("#PONum").val() ; 
                    var rowMod = "D";  
                    var DrawSeq = $("#DrawSeq").val() ;
                    var temp_id = $("#temp_id").val();  
                    var token = $("[name=_token]").val(); 
                    var string = "&_token="+token+"&temp_id="+temp_id+"&poNum="+poNum+"&DrawSeq="+DrawSeq+"&rowMod="+rowMod ; 
                    var button = document.getElementById('btn_delete_attachment');
                    var svg = document.getElementById('svg_delete_attachment');
                    var spinner = document.getElementById('spinner_delete_attachment');
                    var buttonText = document.getElementById('btn_text_delete_attachment'); 
                    svg.style.display = 'none';
                    spinner.style.display = 'inline-block';
                    buttonText.textContent = 'Please Wait...'; 
                    button.disabled = true;   
                    $.ajax({
                        type	: 'POST',
                        url	: "{{ route('receipt_entry.delete_attachment') }}",
                        data	: string,
                        cache	: false, 
                        dataType : 'json',
                        success : function(data){     
                            svg.style.display = 'inline-block';
                            spinner.style.display = 'none';
                            buttonText.textContent = 'Delete'; 
                            button.disabled = false; 

                            if (data.code == 200) {
                                if (data.transaction_code == 200) {
                                    $("#btn_close_form_preview").click(); 
                                    getAttachmentList();
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
                                buttonText.textContent = 'Delete'; 
                                button.disabled = false; 
                        } 
                    })
                    
                } else { 
                    console.log('Penghapusan dibatalkan');
                }
            });
        }
</script>