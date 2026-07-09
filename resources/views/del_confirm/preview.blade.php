@extends('layouts.app')  
@section('subhead')
    <title>{{ $head_title }}</title>  
    <script src="{{ asset('public/assets/js/jquery/jquery.min.js') }}"></script> 
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 

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
                <div class="card shadow">
                    <div class="card-header">  
                            <ul class="nav nav-tabs nav-line-tabs nav-stretch fs-6 border-0">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#kt_tab_pane_document">Preview</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_label">Tag Label</a>
                                </li> 
                            </ul> 
                        <div class="card-toolbar">
                            <div class="d-flex justify-content-end" data-kt-goodreceive-table-toolbar="base">   
    
                                <a href="/del_confirm" type="button" class="btn me-2 btn-sm btn-outline btn-outline-dashed btn-outline-success btn-active-light-success text-muted">
                                    <span class="svg-icon svg-icon-muted svg-icon-2x"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M14.6 4L6.6 12L14.6 20H10.6L3.3 12.7C2.9 12.3 2.9 11.7 3.3 11.3L10.6 4H14.6Z" fill="black"/>
                                        <path opacity="0.3" d="M21.6 4L13.6 12L21.6 20H17.6L10.3 12.7C9.9 12.3 9.9 11.7 10.3 11.3L17.6 4H21.6Z" fill="black"/>
                                        </svg>
                                    </span>
                                    Home
                                </a>

                                <!-- <button type="button" id="btn-revise-document" class="btn btn-sm btn-outline btn-outline-dashed btn-outline-success btn-active-light-success" onclick="return reviseConfirm()">
                                    <span class="indicator-label">
                                        <span class="svg-icon svg-icon-muted svg-icon-2x">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path opacity="0.3" fill-rule="evenodd" clip-rule="evenodd" d="M2 4.63158C2 3.1782 3.1782 2 4.63158 2H13.47C14.0155 2 14.278 2.66919 13.8778 3.04006L12.4556 4.35821C11.9009 4.87228 11.1726 5.15789 10.4163 5.15789H7.1579C6.05333 5.15789 5.15789 6.05333 5.15789 7.1579V16.8421C5.15789 17.9467 6.05333 18.8421 7.1579 18.8421H16.8421C17.9467 18.8421 18.8421 17.9467 18.8421 16.8421V13.7518C18.8421 12.927 19.1817 12.1387 19.7809 11.572L20.9878 10.4308C21.3703 10.0691 22 10.3403 22 10.8668V19.3684C22 20.8218 20.8218 22 19.3684 22H4.63158C3.1782 22 2 20.8218 2 19.3684V4.63158Z" fill="black"/>
                                                <path d="M10.9256 11.1882C10.5351 10.7977 10.5351 10.1645 10.9256 9.77397L18.0669 2.6327C18.8479 1.85165 20.1143 1.85165 20.8953 2.6327L21.3665 3.10391C22.1476 3.88496 22.1476 5.15129 21.3665 5.93234L14.2252 13.0736C13.8347 13.4641 13.2016 13.4641 12.811 13.0736L10.9256 11.1882Z" fill="black"/>
                                                <path d="M8.82343 12.0064L8.08852 14.3348C7.8655 15.0414 8.46151 15.7366 9.19388 15.6242L11.8974 15.2092C12.4642 15.1222 12.6916 14.4278 12.2861 14.0223L9.98595 11.7221C9.61452 11.3507 8.98154 11.5055 8.82343 12.0064Z" fill="black"/>
                                            </svg>
                                        </span>
                                        Revise
                                    </span>
                                    <span class="indicator-progress">Please wait...<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>   -->

                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <input type="hidden" id="ref_doc" name="ref_doc" value="{{ $ref_doc }}" readonly>
                        <input type="hidden" id="ref_doc_po" name="ref_doc_po" value="{{ $ref_doc_po }}" readonly>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade active show" id="kt_tab_pane_document" role="tabpanel">
                                <div id="div-document-preview"></div> 
                            </div>
                            <div class="tab-pane fade" id="kt_tab_pane_label" role="tabpanel">
                                <div id="div-label-preview"></div> 
                            </div> 
                        </div>
                    </div> 
                </div>  
            </div>  
        </div>  
    </div> 
</div>   
 

<script> 
    print_view();
    function print_view(){  
        var trc_unix_id = '<?php echo $ref_doc ?>';  
        var token = $("[name=_token]").val(); 
        var string = "&_token="+token+"&trc_unix_id="+trc_unix_id ;
        $.ajax({
        type	: 'POST',
        url	: "{{ route('delcon.print_view') }}",
        data	: string,
        cache	: false,
        success : function(data){
            $("#div-document-preview").html(data); 
            refresh_label_table();  
        } }) 
    }; 

    function refresh_label_table() {    
        var trc_unix_id = '<?php echo $ref_doc ?>';  
        var token = $("[name=_token]").val(); 
        var string = "&_token="+token+"&trc_unix_id="+trc_unix_id ;
            $.ajax({
            type	: 'POST',
            url	: "{{ route('delcon.print_label_view') }}",
            data	: string, 
            cache	: false,
            success : function(data){ 
                $("#div-label-preview").html(data);    
                } 
            })
        };

    function reviseConfirm() {
        var token = $("[name=_token]").val(); 
        var ref_doc = $("#ref_doc").val(); 
        var ref_doc_po = $("#ref_doc_po").val();
        $("#btn-revise-document").attr("data-kt-indicator", "on");
        $("#btn-revise-document").attr("disabled", "disabled"); 
        var string = "&_token="+token+"&ref_doc="+ref_doc+"&ref_doc_po="+ref_doc_po ; 
        $.ajax({
        type	: 'POST',
        url    : "{{ route('delcon.checking_revise') }}",
        data	: string,
        cache	: false,
        dataType : 'json',
        success	: function(data){   
            $("#btn-revise-document").removeAttr("data-kt-indicator", "on");
            $("#btn-revise-document").removeAttr("disabled", "disabled");   
            if(data.process==1){   
                Swal.fire({
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    icon: 'warning',
                    title: 'Revise Document?', 
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Revise',
                    }).then(function(isConfirm) { 
                        if (isConfirm.value === true) {
                            reviseDocument();
                        }  
                }) 
            } else {
                Toast.fire({
                    icon: 'error',
                    title: data.msg
                })
            }    
        },
        error: function (data) { 
                Toast.fire({
                    icon: 'error',
                    title: 'Please reload and try again!'
                })
                $("#btn-revise-document").removeAttr("data-kt-indicator", "on");
                $("#btn-revise-document").removeAttr("disabled", "disabled");
            }  
        })   
    };

    function reviseDocument() {    
        var token = $("[name=_token]").val(); 
        var ref_doc = $("#ref_doc").val(); 
        var ref_doc_po = $("#ref_doc_po").val(); 
        var ref_form = "<?php echo $ref_form ?>"; 
        var string = "&_token="+token+"&ref_doc="+ref_doc+"&ref_doc_po="+ref_doc_po ; 
        $.ajax({
            url	: "{{ route('delcon.return_to_draft') }}",
            type	: 'POST', 
            data	: string, 
            typeData : 'json',  
            success: function (response) {    
                var response = JSON.parse(response)  
                if (response.process==1) { 
                    window.location.href = 'open_doc?ref_doc='+response.ref_doc+'&ref_form='+ref_form ;   
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

</script>

@endsection