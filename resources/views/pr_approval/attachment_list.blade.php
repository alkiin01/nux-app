                                            <div class="row g-6 g-xl-9 mb-6 mb-xl-9">
                                                <?php if ($count > 0) { foreach ($list AS $row) { 
                                                    $ext = explode(".", $row->XFileName) ;
                                                    $ext =strtolower(end($ext)) ;
                                                    ?>
                                                    <div class="col-md-6 col-lg-4 col-xl-3" onclick="showAttachment('<?= $row->XFileRefNum ?>', '<?= $ext ?>')">
                                                        <div class="card border-primary border border-dashed h-100">
                                                            <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                                                                <a href="#" class="text-gray-800 text-hover-primary d-flex flex-column">
                                                                    <div class="symbol symbol-60px mb-5">
                                                                        <img src="<?= env('APP_ASSETS') ?>assets/media/svg/files/doc.svg" alt="" />
                                                                    </div> 
                                                                    <div class="fs-5 fw-bolder mb-2">
                                                                    <?php echo 'File '.$ext ;  ?>    
                                                                    </div> 
                                                                </a> 
                                                                <div class="fs-7 fw-bold text-gray-400">{{ $row->XFileDesc }}</div> 
                                                            </div> 
                                                        </div> 
                                                    </div> 
                                                <?php } } else { ?> 

                                                <div class="col-md-12 col-lg-12 col-xl-12"> 
                                                    <div class="card h-100 flex-center bg-light-primary border-primary border border-dashed p-8"> 
                                                        <img src="<?= env('APP_ASSETS') ?>assets/media/svg/files/upload.svg class=" alt="" /> 
                                                        <a href="#" class="text-hover-primary fs-5 fw-bolder mb-2">No File Upload</a> 
                                                        <div class="fs-7 fw-bold text-gray-400">We didn't find the file</div> 
                                                    </div> 
                                                </div>
                                                <?php } ?> 
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
                                                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <script>  
                                                function showAttachment(id, ext) { 
                                                    console.log(ext)
                                                    var token = $("[name=_token]").val(); 
                                                    var string = "&_token="+token+"+&seqNum="+id ;
                                                    $("#kt_modal_show").modal('show'); 
                                                    $("#lds-roller-attachment-show").css("display", "");  
                                                    $('#attachment_view').empty();
                                                        $.ajax({
                                                            type	: 'POST',
                                                            url	: "{{ route('po_approval.show_attachment') }}",
                                                            data	: string,
                                                            cache	: false,
                                                            dataType	: 'json',
                                                            success : function(data){   
                                                                $("#lds-roller-attachment-show").css("display", "none");   
                                                                if (ext.toLowerCase() == "pdf") {  
                                                                    console.log(ext);
                                                                    const base64String = data.draw;
                                                                    const byteCharacters = atob(base64String);
                                                                    const byteNumbers = new Array(byteCharacters.length);

                                                                    for (let i = 0; i < byteCharacters.length; i++) {
                                                                        byteNumbers[i] = byteCharacters.charCodeAt(i);
                                                                    }

                                                                    const byteArray = new Uint8Array(byteNumbers);
                                                                    const blob = new Blob([byteArray], { type: 'application/pdf' });
                                                                    const blobUrl = URL.createObjectURL(blob);

                                                                    const pdfContainer = document.getElementById('attachment_view');
                                                                    pdfContainer.innerHTML = ""; // Bersihkan kontainer sebelum menambahkan

                                                                    const embedElement = document.createElement('embed');
                                                                    embedElement.src = blobUrl;
                                                                    embedElement.type = 'application/pdf';
                                                                    embedElement.width = '100%';
                                                                    embedElement.height = '600px';

                                                                    pdfContainer.appendChild(embedElement);

                                                                    // const base64String = data.draw ;  
                                                                    // const pdfContainer = document.getElementById('attachment_view');
                                                                    // const embedElement = document.createElement('iframe');  
                                                                    // embedElement.src = `data:application/pdf;base64,${base64String}`;
                                                                    // embedElement.type = 'application/pdf';
                                                                    // embedElement.width = '100%';
                                                                    // embedElement.height = '600px';
                                                                    // pdfContainer.appendChild(embedElement);
                                                                } else {
                                                                    var attachment = 'data:image/'+ext+';base64,'+data.draw ; 
                                                                    const imgElement = $('<img>', { 
                                                                        class: 'profile-user-img img-responsive',
                                                                        style: 'background: white;',
                                                                        alt: '', 
                                                                        src: attachment  
                                                                    }); 
                                                                    imgElement.appendTo('#attachment_view');  
                                                                }  
                                                            } 
                                                        }) 
                                                }
                                            </script>