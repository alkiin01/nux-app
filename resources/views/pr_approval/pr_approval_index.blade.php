@extends('../layouts/app') 
 
@section('subhead')
    <title>{{ $head_title }}</title>   
    <script type="text/javascript"> 
        $(document).ready(function(){   
            const urlParams = new URLSearchParams(window.location.search);   
            var ref_doc =  urlParams.get('ref_doc');   
            if(ref_doc == '' || ref_doc == null){ 
                $("#kt_activity_home_tab").addClass('show active');   
                window.history.pushState('', '', '<?php echo env('BASE_URL') ?>/pr_approval');    
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

                <div class="post d-flex flex-column-fluid" id="kt_post"> 
                    <div id="kt_content_container" class="container-xxl">
                        <div class="row g-5 g-xl-8 mb-2">
                            <div class="col-xl-3 col-lg-6 col-sm-6">
                                <a href="#" onclick="docSearch(1, this);" class="card bgi-no-repeat card-xl-stretch mb-xl-8 card-front" style="background-position: right top; background-size: 30% auto; background-image: url(<?= env('APP_ASSETS') ?>assets/media/svg/shapes/abstract-4.svg)">
                                    <div class="card-body">  
                                        <div class="text-gray-900 fw-bolder fs-4 mb-2 mt-2" id="total_check"></div>
                                        <div class="fw-bold text-gray-900">Waiting Check</div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-sm-6">
                                <a href="#" onclick="docSearch(2, this);" class="card bgi-no-repeat card-xl-stretch mb-xl-8 card-front" style="background-position: right top; background-size: 30% auto; background-image: url(<?= env('APP_ASSETS') ?>assets/media/svg/shapes/abstract-2.svg)">
                                    <div class="card-body"> 
                                        <div class="text-gray-900 fw-bolder fs-4 mb-2 mt-2" id="total_approve"></div>
                                        <div class="fw-bold text-gray-900">Waiting Approve</div>
                                    </div> 
                                </a> 
                            </div>
                            <div class="col-xl-3 col-lg-6 col-sm-6"> 
                                <a href="#" onclick="docSearch(3, this);" class="card bgi-no-repeat card-xl-stretch mb-xl-8 card-front" style="background-position: right top; background-size: 30% auto; background-image: url(<?= env('APP_ASSETS') ?>assets/media/svg/shapes/abstract-1.svg)">
                                    <div class="card-body">  
                                        <div class="text-gray-900 fw-bolder fs-4 mb-2 mt-2" id="total_legal"></div>
                                        <div class="fw-bold text-gray-900">Waiting Legalized</div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-sm-6">
                                <a href="#" onclick="docSearch(4, this);" class="card bgi-no-repeat card-xl-stretch mb-xl-8 card-front card-front-1" style="background-position: right top; background-size: 30% auto; background-image: url(<?= env('APP_ASSETS') ?>assets/media/svg/shapes/abstract-3.svg)">
                                    <div class="card-body"> 
                                        <div class="text-gray-900 fw-bolder fs-4 mb-2 mt-2" id="total_document"></div>
                                        <div class="fw-bold text-gray-900">All Status</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
           
             
                    <div class="d-flex flex-column-fluid mt-lg-5 mt-sm-5" > 
                        <div id="kt_content_container" class="container-xxl">  
                            <div class="card col-xxl-12 card-sticky">  
                                <div class="card-header border-0 pt-6"> 
                                    <div class="card-title"> 
                                        <div class="d-flex align-items-center position-relative my-1"> 
                                            <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                                    <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                                                </svg>
                                            </span> 
                                            <input type="text" data-kt-goodreceive-table-filter="search" id="front_table_search" class="form-control form-control-solid w-250px ps-15 text-sm form-control-sm" placeholder="Search PR Number" />
                                        </div> 
                                    </div> 
                                    <div class="card-toolbar"> 
                                <button type="button" id="btn_export_excel"
                                    class="btn btn-light-success btn-sm p-0 me-2 d-none"
                                    title="Export Excel"
                                    style="width:40px; height:35px; align-items:center; display:flex; justify-content:center;"
                                    onclick="exportPrExcel(event)">
                                    <span class="svg-icon svg-icon-2 p-0 m-0"
                                        style="display:inline-block; align-items:center; justify-content:center;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24" aria-hidden="true"
                                            style="display:flex; align-items:center; justify-content:center;">
                                            <path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12V7l-4-4Z"
                                                fill="currentColor" opacity="0.3" />
                                            <path d="M10.5 10.5L8 14l2.5 3.5h1.6L9.8 14l2.3-3.5h-1.6Z"
                                                fill="currentColor" />
                                            <path d="M14 3v4h4" fill="currentColor" />
                                        </svg>
                                    </span>
                                    <span id="spinner_export" class="spinner-border spinner-border-sm align-middle" style="display:none;"></span>
                                </button>
                                        <div class="d-flex justify-content-end" data-kt-goodreceive-table-toolbar="base"> 
                                            <button type="button" class="btn btn-light-primary  btn-sm me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                            <!--begin::Svg Icon | path: icons/duotune/general/gen031.svg-->
                                            <span class="svg-icon svg-icon-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black" />
                                                </svg>
                                            </span>
                                        Filter</button>
                                        <!-- <button class="btn btn-success btn-sm text-sm ms-2" style="width: 100px;" onclick="sendEmail()">Email</button> -->

                                            <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true" id="kt-toolbar-filter">
                                                <div class="px-7 py-5">
                                                    <div class="fs-4 text-dark fw-bolder">Filter Options</div>
                                                </div> 
                                                <div class="separator border-gray-200"></div> 
                                                <div class="px-7 py-5"> 
                                                    <div class="mb-5"> 
                                                        <label class="form-label fs-5 fw-bold mb-3">Status:</label> 
                                                        <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Select option" data-allow-clear="false" id="status_id" data-hide-search="true" onchange="resetFrontCard()"> 
                                                                <option value='1'>Waiting Check</option> 
                                                                <option value='2'>Waiting Approve</option> 
                                                                <option value='3'>Waiting Legalize</option> 
                                                                <option value='4' selected>All Status</option> 
                                                        </select> 
                                                    </div> 
                                                    <div class="mb-10"> 
                                                        <label class="form-label fs-5 fw-bold mb-3">Section:</label> 
                                                        <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Select option" data-allow-clear="true" id="section_id" data-hide-search="false" data-dropdown-parent="#kt-toolbar-filter">
                                                            <option value='0' selected>All Section</option>
                                                            <?php foreach ($section_list AS $row) {  ?>
                                                                <option value='{{ $row->id }}'>{{ $row->desc }}</option> 
                                                            <?php } ?>  
                                                        </select>  
                                                    </div>    
                                                    <div class="d-flex justify-content-end">
                                                        {{-- <button type="reset" class="btn btn-light btn-active-light-primary me-2" data-kt-menu-dismiss="true" data-kt-goodreceive-table-filter="reset">Reset</button> --}}
                                                        <button type="submit" id="submit-filter" class="btn btn-primary" data-kt-menu-dismiss="true" data-kt-goodreceive-table-filter="filter">Apply</button>
                                                    </div>
                                                </div>
                                            </div>   
                                            {{-- <button type="button" class="btn btn-light-primary me-3" id="b_export_data">
                                            <span class="svg-icon svg-icon-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <rect opacity="0.3" x="12.75" y="4.25" width="12" height="2" rx="1" transform="rotate(90 12.75 4.25)" fill="black" />
                                                    <path d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z" fill="black" />
                                                    <path d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z" fill="#C4C4C4" />
                                                </svg>
                                            </span>
                                        Export</button>   --}}
                                        </div> 
                                        <div class="d-flex justify-content-end align-items-center d-none" data-kt-goodreceive-table-toolbar="selected">
                                            <div class="fw-bolder me-5">
                                            <span class="me-2" data-kt-goodreceive-table-select="selected_count"></span>Selected</div>
                                            <button type="button" class="btn btn-danger" data-kt-goodreceive-table-select="delete_selected">Delete Selected</button>
                                        </div>
                                    </div>
                                </div> 
                                <div class="card-body pt-0"> 
                                    <table class="table align-middle table-row-dashed table-striped fs-7 gy-2" id="kt_doc_table">
                                        <thead>
                                            <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                                <th class="min-w-20px pe-2">No</th>
                                                <th class="min-w-40px">PR</th>
                                                <th class="min-w-80px">DocDate</th>
                                                <th class="min-w-90px">Amount</th>
                                                <th class="min-w-100px">Check</th>  
                                                <th class="min-w-100px">Approve</th>  
                                                <th class="min-w-100px">Legalize</th>  
                                                <th class="text-end min-w-70px">View</th>
                                            </tr> 
                                        </thead>  
                                        <tfoot>
                                            <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                                <th class="min-w-20px pe-2">No</th>
                                                <th class="min-w-40px">PR</th>
                                                <th class="min-w-80px">DocDate</th>
                                                <th class="min-w-90px">Amount</th>
                                                <th class="min-w-100px">Check</th>  
                                                <th class="min-w-100px">Approve</th>  
                                                <th class="min-w-100px">Legalize</th>  
                                                <th class="text-end min-w-70px">View</th>
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
                            <div class="card">
                                <div class="card-header card-header-stretch">
                                    <div class="card-title d-flex align-items-center">
                                        {{-- <button class="btn btn-primary btn-sm text-sm" style="width: 100px;" onclick="getApprovalForm()">Approve</button> --}}
                                        <div id="button_approve"></div>
                                        <button class="btn btn-success btn-sm text-sm ms-2" style="width: 100px;" onclick="backHome()">Back</button>
                                    </div>
                                    <div class="card-toolbar m-0">
                                        <ul class="nav nav-tabs nav-line-tabs nav-stretch fs-6 border-0 fw-bolder" role="tablist">
                                            <li class="nav-item" role="presentation" onclick="getPreview()">
                                                <a id="kt_activity_file_tab" class="nav-link tab_preview justify-content-center text-active-gray-800 active" data-bs-toggle="tab" 
                                                role="tab" href="#kt_activity_file">Preview</a>
                                            </li>
                                            <li class="nav-item" role="presentation" onclick="getAttachmentList()">
                                                <a id="kt_activity_attachment_tab"  class="nav-link tab_preview justify-content-center text-active-gray-800" data-bs-toggle="tab" role="tab" href="#kt_activity_attachment">Attachment</a>
                                            </li>
                                            <li class="nav-item" role="presentation" onclick="getCommentList()">
                                                <a id="kt_activity_comment_tab" class="nav-link tab_preview justify-content-center text-active-gray-800" data-bs-toggle="tab" role="tab" href="#kt_activity_comment">Comment</a>
                                            </li> 
                                        </ul>
                                    </div>
                                </div> 
                    
                                <div class="card-body">
                                    <div class="tab-content">
                                        <div id="kt_activity_file" class="card-body tab_preview p-0 tab-pane fade show active" role="tabpanel" aria-labelledby="kt_activity_file_tab" style="text-align: center">
                                            <div class="lds-roller mt-20" id="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
                                            <div id="file_view"></div>
                                        </div>
                                        <div id="kt_activity_attachment" class="card-body tab_preview p-0 tab-pane fade show" role="tabpanel" aria-labelledby="kt_activity_attachment_tab" style="text-align: center">
                                            <div class="lds-roller mt-20" id="lds-roller-attachment"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
                                             <div id="attachment_list"></div>
                                        </div>
                                            <div id="kt_activity_comment" class="card-body tab_preview p-0 tab-pane fade show" role="tabpanel" aria-labelledby="kt_activity_comment_tab" style="text-align: center"> 
                                                <div class="lds-roller mt-20" id="lds-roller-comment"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
                                                <div class="card-body" id="kt_drawer_chat_messenger_body">
                                                    <div class="scroll-y me-n5 pe-5" data-kt-element="messages" data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-height="300px" data-kt-scroll-dependencies="#kt_drawer_chat_messenger_header, #kt_drawer_chat_messenger_footer" data-kt-scroll-wrappers="#kt_drawer_chat_messenger_body" data-kt-scroll-offset="0px">
                                                        <div id="comment_list"></div> 
                                                    </div> 
                                                </div> 

                                                <div class="card-footer pt-4" id="kt_drawer_chat_messenger_footer">
                                                    <textarea id="input_comment" class="form-control form-control-flush mb-3" rows="1" data-kt-element="input" placeholder="Type a message"></textarea>
                                                    <div class="d-flex flex-stack">
                                                        <div class="d-flex align-items-center me-2">
                                                            <button class="btn btn-sm btn-icon btn-active-light-primary me-1" type="button" data-bs-toggle="tooltip" title="Coming soon">
                                                                <i class="bi bi-paperclip fs-3"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-icon btn-active-light-primary me-1" type="button" data-bs-toggle="tooltip" title="Coming soon">
                                                                <i class="bi bi-upload fs-3"></i>
                                                            </button>
                                                        </div> 
                                                        <button class="btn btn-primary" type="button" data-kt-element="send" onclick="sentComment()">Send</button>
                                                    </div> 
                                                </div>

                                            </div> 
                                        </div>
                                    </div> 
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>  
            </div>  

            <div class="modal fade" id="kt_modal_approval" tabindex="-1" aria-hidden="true"> 
                <div class="modal-dialog mw-650px"> 
                    <div class="modal-content"> 
                        <div class="modal-header pb-0 border-0 justify-content-end"> 
                            <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal"> 
                                <span class="svg-icon svg-icon-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                                        <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                                    </svg>
                                </span> 
                            </div> 
                        </div> 
                        <div class="modal-body scroll-y mx-5 mx-xl-18 pt-0 pb-15"> 
                            <div class="text-center mb-5"> 
                                <h1 class="mb-3">APPROVAL FORM</h1> 
                                <div class="text-muted fw-bold fs-5">Please make sure all data is correct !</div>
                            </div> 
                            
                            <div class="d-flex flex-stack p-14">   
                                <label class="form-check form-check-custom form-check-solid ml-16">
                                    <input class="form-check-input approval_checkbox" type="checkbox" value="A" onclick="checkboxAction(this)"/>
                                    <span class="form-check-label text-gray-600">Approve</span>
                                </label> 
                                <label class="form-check form-check-custom form-check-solid mr-16">
                                    <input class="form-check-input approval_checkbox" type="checkbox" value="R" onclick="checkboxAction(this)"/>
                                    <span class="form-check-label text-gray-600">Reject</span>
                                </label> 
                            </div>  

                            <textarea class="form-control form-control-solid mb-8" rows="3" placeholder="" id="approve_msg"></textarea> 
                             
                            <div class="d-flex flex-stack"> 
                                <div class="me-5 fw-bold">
                                    <label class="fs-6"></label>
                                    <div class="fs-7 text-muted"></div>
                                </div> 
                                <label class="form-check form-switch form-check-custom form-check-solid"> 
                                    <button class="btn btn-primary btn-sm text-sm" id="btn_submit_approval" onclick="submitApproval()">
                                        <span id="btn_text_submit_approval">Submit</span>
                                        <span id="spinner_submit_approval" class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;"></span>        
                                    </button>
                                </label> 
                            </div> 
                        </div> 
                    </div> 
                </div> 
            </div>

     <input type="text" hidden id="temp_id">

<script>

function sendEmail() {  
        var token = $("[name=_token]").val(); 
        var button = document.getElementById('btn_submit_approval');
        var spinner = document.getElementById('spinner_submit_approval');
        var buttonText = document.getElementById('btn_text_submit_approval'); 
        spinner.style.display = 'inline-block';
        buttonText.textContent = 'Please Wait...'; 
        button.disabled = true; 
        var string = "&_token="+token ;
        $.ajax({
            type	: 'POST',
            url	: "{{ route('pr_approval.send_email_pr') }}",
            data	: string,
            cache	: false,
            dataType	: 'json',
            success : function(data){     
                spinner.style.display = 'none';
                buttonText.textContent = 'Submit'; 
                button.disabled = false;
            } 
        }) 
    }

    function submitApproval() {
        var temp_id = $("#temp_id").val(); 
        var token = $("[name=_token]").val(); 
        var approve_msg = $("#approve_msg").val().replace(/['"]/g, '');  
        var approve_sts = $('.approval_checkbox:checked').val();

        const checkboxes = document.querySelectorAll('.approval_checkbox'); 
        let oneChecked = false;
        checkboxes.forEach(function(item) {
            if (item.checked) {
                oneChecked = true;
            }
        }); 
        if (!oneChecked) {
            Toast.fire({
                position: 'top-end',
                title: "Please select either 'Approve' or 'Reject' before submitting.",
                icon:"error"
            }) 
            return false;
        } 

        var button = document.getElementById('btn_submit_approval');
        var spinner = document.getElementById('spinner_submit_approval');
        var buttonText = document.getElementById('btn_text_submit_approval'); 
        spinner.style.display = 'inline-block';
        buttonText.textContent = 'Please Wait...'; 
        button.disabled = true;

        var string = "&_token="+token+"&trc_unix_id="+temp_id+"&approve_sts="+approve_sts+"&approve_msg="+approve_msg ;
        $.ajax({
            type	: 'POST',
            url	: "{{ route('pr_approval.submit_approval') }}",
            data	: string,
            cache	: false,
            dataType	: 'json',
            success : function(data){     
                if(data.code == 200) {
                    backHome();
                    $("#kt_modal_approval").modal('hide');
                    $("#temp_id").val("");
                } else {
                    Swal.fire({
                        text: data.code + "Please reload and try again! "+ data.desc,
                        icon:"error",
                        buttonsStyling:!1,
                        confirmButtonText:"Close",
                        customClass:{confirmButton:"btn btn-primary"  
                    }}) 
                }
                spinner.style.display = 'none';
                buttonText.textContent = 'Submit'; 
                button.disabled = false;
            } 
        }) 
    }

    function checkboxAction(checkbox) {
        const checkboxes = document.querySelectorAll('.approval_checkbox'); 
        checkboxes.forEach((cb) => {
            if (cb !== checkbox) {
                cb.checked = false;
            }
        }); 
    }

    function getCountDocument() {
        var token = $("[name=_token]").val(); 
        var section_id = $("#section_id").val(); 
        var string = "&_token="+token+"&section_id="+section_id ;
        $.ajax({
            type	: 'POST',
            url	: "{{ route('pr_approval.get_count_document') }}",
            data	: string,
            cache	: false,
            dataType	: 'json',
            success : function(data){   
                $("#total_check").text(data.total_check+" Document");
                $("#total_approve").text(data.total_approve+" Document");
                $("#total_legal").text(data.total_legal+" Document");
                $("#total_document").text(data.total_document+" Document");
            } 
        }) 
    }

    function resetFrontCard() {
        document.querySelectorAll('.card-front').forEach(function(el) {
            el.classList.remove('bg-light-success');
        }); 
    }
    function backHome() {
        document.getElementById('submit-filter').click();
        document.getElementById('kt_activity_home_tab').click(); 
        $("#temp_id").val('');
        window.history.pushState('', '', '<?php echo env('BASE_URL') ?>/pr_approval');    
    }

    function getApprovalForm() {
        $("#kt_modal_approval").modal('show');
        const checkboxes = document.querySelectorAll('.approval_checkbox'); 
        checkboxes.forEach(function(item) {  
            item.checked = false; 
        });
    }

    function getPreview() {
        $("#lds-roller").css("display", ""); 
        $("#file_view").html("");       
        var temp_id = $("#temp_id").val(); 
        var token = $("[name=_token]").val(); 
        var string = "&_token="+token+"&trc_unix_id="+temp_id ;
        $.ajax({
            type	: 'POST',
            url	: "{{ route('pr_approval.print_view') }}",
            data	: string,
            cache	: false,
            success : function(data){  
                setTimeout(function(){
                    $("#lds-roller").css("display", "none");   
                    $("#file_view").html(data);  
                    getBApprove(); 
                },500)
        } }) 
    }

    function getBApprove() {  
        $("#button_approve").html("");       
        var temp_id = $("#temp_id").val(); 
        var token = $("[name=_token]").val(); 
        var string = "&_token="+token+"&trc_unix_id="+temp_id ;
        $.ajax({
            type	: 'POST',
            url	: "{{ route('pr_approval.get_button_approve') }}",
            data	: string,
            cache	: false,
            dataType	: 'json',
            success : function(data){   
                $("#button_approve").html(data.button_approve);    
        } }) 
    }

    function getAttachmentList() { 
        $("#lds-roller-attachment").css("display", ""); 
        $("#attachment_list").html("");      
        var temp_id = $("#temp_id").val(); 
        document.getElementById('kt_activity_preview_tab').click() ; 
        var token = $("[name=_token]").val(); 
        var string = "&_token="+token+"&trc_unix_id="+temp_id ;
        $.ajax({
            type	: 'POST',
            url	: "{{ route('pr_approval.get_attachment_list') }}",
            data	: string,
            cache	: false,
            success : function(data){ 
                  
                setTimeout(function(){
                    $("#lds-roller-attachment").css("display", "none");  
                    $("#attachment_list").html(data); 
                },500)
        } }) 
    }
    

    function getCommentList() { 
        $("#lds-roller-comment").css("display", ""); 
        $("#comment_list").html("");      
        var temp_id = $("#temp_id").val(); 
        document.getElementById('kt_activity_preview_tab').click() ; 
        var token = $("[name=_token]").val(); 
        var string = "&_token="+token+"&trc_unix_id="+temp_id ;
        $.ajax({
            type	: 'POST',
            url	: "{{ route('pr_approval.get_comment_list') }}",
            data	: string,
            cache	: false,
            success : function(data){  
                setTimeout(function(){
                    $("#lds-roller-comment").css("display", "none");  
                    $("#comment_list").html(data); 
                },500)
        } }) 
    }

    function sentComment() {     
        var temp_id = $("#temp_id").val(); 
        var input_comment = $("#input_comment").val().replace(/['"]/g, ''); 
        document.getElementById('kt_activity_preview_tab').click() ; 
        var token = $("[name=_token]").val(); 
        var string = "&_token="+token+"&trc_unix_id="+temp_id+"&comment="+input_comment ;
        $.ajax({
            type	: 'POST',
            url	: "{{ route('pr_approval.sent_comment') }}",
            data	: string,
            cache	: false,
            dataType : 'json',
            success : function(data){  
                if (data.status == 1) {
                    getCommentList() ; 
                    $("#input_comment").val("")
                } else {
                    Swal.fire({
                    text:"Please reload and try again!",
                    icon:"error",
                    buttonsStyling:!1,
                    confirmButtonText:"Close",
                    customClass:{confirmButton:"btn btn-primary" }})
                } 
        },
            error: function (data) {  
                Swal.fire({
                text:"Please reload and try again!",
                icon:"error",
                buttonsStyling:!1,
                confirmButtonText:"Close",
                customClass:{confirmButton:"btn btn-primary" }})
            }
     }) 
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

    $(document).ready(function () {
        var frontTable = $("#kt_doc_table").DataTable({
            processing: true,
            serverSide: true,
            responsive: false, 
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
                url: "{{ route('pr_approval.front_table') }}",
                type: 'POST',
                data	: function ( d ) { d._token = $("[name=_token]").val(), d.status_id = $("#status_id").val(), d.section_id = $("#section_id").val(), d.front_table_search = $("#front_table_search").val(); }, 
                cache	: false,
                dataType : 'json'
            },
            columns: [
                { data: 'no', className: 'text-center' }, 
                { data: 'prnum' },
                { data: 'docdate' },
                { data: 'amount' },
                { data: 'check' }, 
                { data: 'approve' },  
                { data: 'legal' },  
                {
                    data: 'action', 
                    className: 'text-center',
                    orderable: false
                }
            ],
            initComplete: function(settings, json) {
                getCountDocument();
            }
        })  
         
        $("#front_table_search").keyup(function(event){
                if(event.keyCode == 13){ frontTable.ajax.reload(); getCountDocument(); } 
        });

        $("#submit-filter").click(function(){
                frontTable.ajax.reload();  
                getCountDocument();
        }); 
    }) ;

function document_preview(trc_unix_id) 
    {      
        $("#lds-roller").css("display", ""); 
        $("#file_view").html("");      
        document.getElementById('kt_activity_preview_tab').click() ;  
        var token = $("[name=_token]").val(); 
        var string = "&_token="+token+"&trc_unix_id="+trc_unix_id+"&ref_form="+trc_unix_id ;
        $.ajax({
            type	: 'POST',
            url	: "{{ route('pr_approval.print_view') }}",
            data	: string,
            cache	: false,
            success : function(data){   
                $(".tab_preview").removeClass('active'); 
                $("#kt_activity_file").addClass('show active');
                $("#kt_activity_file_tab").addClass('active'); 
                $("#temp_id").val(trc_unix_id); 
                $("#file_view").html(data);   
                getBApprove();
                setTimeout(function(){
                    $("#lds-roller").css("display", "none");  
                    window.history.pushState('', '', '<?php echo env('BASE_URL') ?>/pr_approval?ref_doc='+trc_unix_id); 
                },500)
        } }) 
    };

$('#b_export_data').click( function(){        
    var token = $("[name=_token]").val();
    // var flow_id = $("#input_trc_type_id").val();
    var status_id = $("#status_id").val();
    var front_table_search = $("#front_table_search").val();
    // var position = $("#statud_doc_id").val();
    var string = "?_token="+token+"&status_id="+status_id+"&section_id="+section_id+"&front_table_search="+front_table_search ; 
    window.open("<?php echo route('export_po') ?>"+string); 
});


  // EXPORT EXCEL  

  function setButtonLoadingPR(isLoading = true) {
  const btn  = document.getElementById('btn_export_excel');
  if (!btn) return;
  const icon = btn.querySelector('.svg-icon');
  const spin = document.getElementById('spinner_export');

  if (isLoading) {
    if (icon) icon.style.display = 'none';          
    if (spin) spin.style.display = 'inline-block';  
    btn.disabled = true;
  } else {
    if (spin) spin.style.display = 'none';          
    if (icon) icon.style.display = 'inline-block';  
    btn.disabled = false;
  }
}

function exportPrExcel(ev) {
    setButtonLoadingPR(true);

    const btn = document.getElementById('btn_export_excel');
    const spinner = document.getElementById('spinner_export');

    const status_id = document.getElementById('status_id')?.value || '4';
    const section_id = document.getElementById('section_id')?.value || '0';
    const front_table_search = document.getElementById('front_table_search')?.value || '';

    if (btn) btn.disabled = true;
    if (spinner) spinner.style.display = 'inline-block';

    const params = new URLSearchParams({
        status_id,
        section_id,
        front_table_search
    });

    const url = `{{ route('pr_approval.export_excel') }}?` + params.toString();

    fetch(url, {
        method: 'GET',
        credentials: 'same-origin',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(async (resp) => {
        if (!resp.ok) throw new Error(await resp.text() || 'Export gagal.');
        const cd = resp.headers.get('Content-Disposition') || '';
        const blob = await resp.blob();

        let filename = 'PR_Export.xlsx';
        const m = cd.match(/filename\*?=(?:UTF-8'')?["']?([^"';]+)["']?/i);
        if (m && m[1]) filename = decodeURIComponent(m[1]);

        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        link.remove();
        URL.revokeObjectURL(link.href);
    })
    .catch((err) => {
        console.error(err);
        Swal.fire({
            text: 'Export gagal. Silakan coba lagi.',
            icon: 'error',
            confirmButtonText: 'Close',
            customClass: { confirmButton: 'btn btn-primary' }
        });
    })
    .finally(() => {
        if (spinner) spinner.style.display = 'none';
        if (btn) btn.disabled = false;
    })
    .finally(() => setButtonLoadingPR(false));
}

function updateExportVisibilityPR() {
  const val = (document.getElementById('section_id')?.value || '').toUpperCase();
  const btn = document.getElementById('btn_export_excel');
  if (!btn) return;
  
  if (val === 'TMF') {
    btn.classList.remove('d-none');
    btn.style.display = ''; 
  } else {
    btn.classList.add('d-none');
    btn.style.display = 'none';
  }
}


document.addEventListener('DOMContentLoaded', updateExportVisibilityPR);
$(document).on('change', '#section_id', updateExportVisibilityPR);
$('#submit-filter').on('click', updateExportVisibilityPR);
</script>
        

@endsection