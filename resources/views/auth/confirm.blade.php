@extends('layouts.login') 
@section('content')
<script src="/public/assets/js/jquery/jquery.min.js"></script>   
<div class="w-lg-500px bg-body rounded shadow-sm p-10 p-lg-15 mx-auto">
    <a href="/" class="d-flex flex-center flex-row mb-8">
        <img alt="Logo" src="/public/assets/media/logos/favicon.ico" style="width:20%;"/> 
    </a> 
        <form class="form w-100" novalidate="novalidate" id="kt_confirm_form">
            @csrf
            <div class="mb-10 text-center"> 
                <h3 class="text-dark">Account Reset</h3> 
                <div class="text-gray-400 fw-bold fs-6">Reset your password or
                <a href="login" class="link-primary fw-bolder">login here</a></div> 
            </div>  
            <div class="d-flex align-items-center mb-10">
                <div class="border-bottom border-gray-300 mw-50 w-100"></div>
                <span class="fw-bold text-gray-400 fs-7 mx-2">RESET</span>
                <div class="border-bottom border-gray-300 mw-50 w-100"></div>
            </div>   
        <div class="mb-10 fv-row" data-kt-password-meter="true"> 
            <div class="mb-1"> 
                <label class="form-label fw-bolder text-dark fs-6">Password</label> 
                <div class="position-relative mb-3">
                    <input class="form-control form-control-lg form-control-solid" type="password" placeholder="" name="password" autocomplete="off" />
                    <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2" data-kt-password-meter-control="visibility">
                        <i class="bi bi-eye-slash fs-2"></i>
                        <i class="bi bi-eye fs-2 d-none"></i>
                    </span>
                </div> 
                <div class="d-flex align-items-center mb-3" data-kt-password-meter-control="highlight">
                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px"></div>
                </div> 
            </div> 
            <div class="text-muted">Gunakan 8 karakter atau lebih, kombinasi angka &amp; huruf.</div> 
        </div> 

        <div class="fv-row mb-20">
            <label class="form-label fw-bolder text-dark fs-6">Confirm Password</label>
            <input class="form-control form-control-lg form-control-solid" type="password" placeholder="" name="confirm-password" autocomplete="off" />
        </div>    

        <div class="text-center">
            <button type="button" id="kt_confirm_submit" class="btn btn-lg btn-flex flex-center btn-primary w-100 mb-5">
                <span class="indicator-label">{{ __('Confirm') }}</span>
                <span class="indicator-progress">Please wait...
                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
            </button>
            <div id="div_processing"></div> 
        </div> 
        </form> 
    </div>
    
@endsection 

@section('script')  
<script>  
$(document).ready(function() {  
    
    "use strict";
    var KTSignupGeneral=function(){
        var e,t,a,s,r=function(){
            return 100===s.getScore()};
            return{init:function(){
                e=document.querySelector("#kt_confirm_form"),
                t=document.querySelector("#kt_confirm_submit"), 
                s=KTPasswordMeter.getInstance(
                e.querySelector('[data-kt-password-meter="true"]')),
                a=FormValidation.formValidation(
                e,{fields:{  
                    password:{validators:{notEmpty:{message:"The password is required"},
                    callback:{message:"Please enter valid password",
                    callback:function(e){if(e.value.length<8) return r()}}}},
                    "confirm-password":{validators:{notEmpty:{message:"The password confirmation is required"},
                    identical:{compare:function(){
                        return e.querySelector('[name="password"]').value},
                        message:"The password and its confirm are not the same"}}},
                        toc:{validators:{notEmpty:{message:"You must accept the terms and conditions"}}}},
                        plugins:{trigger:
                            new FormValidation.plugins.Trigger({event:{password:!1}}),
                            bootstrap:new FormValidation.plugins.Bootstrap5({
                                rowSelector:".fv-row",eleInvalidClass:"",eleValidClass:""})}}),
                                t.addEventListener("click",(function(r){r.preventDefault(),
                                    a.revalidateField("password"),a.validate().then((
                                        function(a){"Valid"==a?(t.setAttribute("data-kt-indicator","on"),
                                        t.disabled=!0,setTimeout((function(){
                                            change_password()
                                        }),100)):
                                        Swal.fire({
                                            text:"Please enter valid password!",
                                            icon:"error",
                                            buttonsStyling:!1,
                                            confirmButtonText:"Ok, got it!",
                                            customClass:{confirmButton:"btn btn-primary"}})}))})),
                                            e.querySelector('input[name="password"]').addEventListener("input",(
                                                function(){this.value.length>0&&a.updateFieldStatus("password","NotValidated")
                                            })
                                        )}
                                    }}();KTUtil.onDOMContentLoaded((function(){KTSignupGeneral.init()})); 
     
            $('#password').focus();     

            function change_password() {   
                var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content')  ;  
                var email = "<?php echo $email ?>" ;
                var verification_code = "<?php echo $verification_code ?>" ;
                var str = $( "#kt_confirm_form" ).serialize();       
                var string = "&_token="+token+"&email="+email+"&verification_code="+verification_code+"&"+str ;   
                $.ajax({
                    type : 'POST',    
                    url : "{{ route('change_password') }}",
                    data : string,
                    dataType : 'json',
                    success:function(data) { 
                        document.getElementById("kt_confirm_submit").setAttribute("data-kt-indicator", "off"); 
                        document.getElementById("kt_confirm_submit").removeAttribute("disabled");
                        if (data.status_process == 0) {  
                            Swal.fire({
                                text: data.msg,
                                icon:"error",
                                buttonsStyling:!1,
                                confirmButtonText:"Close",
                                customClass:{confirmButton:"btn btn-primary" }}) ;
                        } else {
                            window.location = "login?after_reset=1&email="+email ; 
                        }
                }, 
                    error: function( jqXHR, textStatus ) { 
                        document.getElementById("kt_confirm_submit").setAttribute("data-kt-indicator", "off"); 
                        document.getElementById("kt_confirm_submit").removeAttribute("disabled"); 
                        Swal.fire({
                                text: 'Please reload and try again!',
                                icon:"error",
                                buttonsStyling:!1,
                                confirmButtonText:"Close",
                                customClass:{confirmButton:"btn btn-primary" }}) ;
                } 
            }) 
    }  
            
    $('#kt_sign_up_form').on('keyup', function(e) { 
        $(".message-feedback").remove();
        if (e.keyCode === 13) {
            $("#kt_sign_in_submit").click();
        }
    }) 
  
})
    </script> 
@endsection
