@extends('layouts.login') 
@section('content')
<script src="/public/assets/js/jquery/jquery.min.js"></script>    
<div class="w-lg-500px bg-body rounded shadow-sm p-10 p-lg-15 mx-auto">
    <a href="/" class="d-flex flex-center flex-row mb-8">
        <img alt="Logo" src="/public/assets/media/logos/favicon.ico" style="width:20%;"/> 
    </a> 
        <form class="form w-100" novalidate="novalidate" id="kt_reset_form"/>
            @csrf
            
            <div class="mb-7 text-center"> 
                <h2 class="text-dark">Reset Password</h2> 
                <div class="text-gray-400 fw-bold fs-6">Please enter your email or
                <a href="login" class="link-primary fw-bolder">login here</a></div> 
            </div> 
            <div class="d-flex align-items-center mb-7">
                <div class="border-bottom border-gray-300 mw-50 w-100"></div>
                <span class="fw-bold text-gray-400 fs-7 mx-2">RESET</span>
                <div class="border-bottom border-gray-300 mw-50 w-100"></div>
            </div>
            
             
            <div class="fv-row mb-7">
                <label class="form-label fw-bolder text-dark fs-6">Email</label>
                <input class="form-control form-control-lg form-control-solid" type="email" placeholder="" name="email" autocomplete="off" value="{{ old('email') }}"/>
            </div>
                           
            <div class="row fv-row mb-7"> 
                <div id="captcha_div"></div> 
            </div> 
        
        <div class="text-center">
            <button type="button" id="kt_reset_submit" class="btn btn-lg btn-flex flex-center btn-primary w-100 mb-5">
                <span class="indicator-label">{{ __('Reset') }}</span>
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

callCaptcha()
function callCaptcha() {
    var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content')  ; 
    var string = "&_token="+token ;  
        $.ajax({
        type : 'GET',    
        url : "{{ route('get_captcha') }}",
        data : string, 
        success:function(data) {  
            $("#captcha_div").html(data); 
        } })  
}

$(document).ready(function() {  
    
    "use strict";
    var KTSignupGeneral=function(){
        var e,t,a,s,r=function(){
            return 100===s.getScore()};
            return{init:function(){
                e=document.querySelector("#kt_reset_form"),
                t=document.querySelector("#kt_reset_submit"), 
                s=KTPasswordMeter.getInstance(
                e.querySelector('[data-kt-password-meter="true"]')),
                a=FormValidation.formValidation(
                e,{fields:{ 
                    email:{validators:{notEmpty:{message:"Email is required!"},
                    emailAddress:{message:"Please enter valid email!"}}}}, 
        plugins:{
            trigger:
                            new FormValidation.plugins.Trigger({event:{password:!1}}),
                            bootstrap:new FormValidation.plugins.Bootstrap5({
                                rowSelector:".fv-row",eleInvalidClass:"",eleValidClass:""})}}),
                                t.addEventListener("click",(function(r){r.preventDefault(),
                                    a.validate().then((
                                        function(a){"Valid"==a?(t.setAttribute("data-kt-indicator","on"),
                                        t.disabled=!0,setTimeout((function(){
                                            check_robot()
                                        }),100)):
                                        Swal.fire({
                                            text:"Please enter valid email!",
                                            icon:"error",
                                            buttonsStyling:!1,
                                            confirmButtonText:"Ok, got it!",
                                            customClass:{confirmButton:"btn btn-primary"}})}))})),
                                            e.querySelector('input[name="password"]').addEventListener("input",(
                                                function(){this.value.length>0&&a.updateFieldStatus("password","NotValidated")
                                            })
                                        )}
                                    }}();KTUtil.onDOMContentLoaded((function(){KTSignupGeneral.init()})); 
     
            $('#email').focus();    
            function check_robot() {
                var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content')  ;  
                var str = $( "#kt_reset_form" ).serialize();       
                var string = "&_token="+token+"&"+str ;   
                $.ajax({
                    type : 'POST',    
                    url : "{{ route('check_robot') }}",
                    data : string,
                    dataType : 'json',
                    success:function(data) { 
                        if (data.status_process == 0) { 
                            document.getElementById("kt_reset_submit").setAttribute("data-kt-indicator", "off"); 
                            document.getElementById("kt_reset_submit").removeAttribute("disabled");
                            Swal.fire({
                                text: data.msg,
                                icon:"error",
                                buttonsStyling:!1,
                                confirmButtonText:"Close",
                                customClass:{confirmButton:"btn btn-primary" }}).then(function(){
                                    callCaptcha();
                                }) ;
                        } else {
                            reset()
                        }
                }, 
                    error: function( jqXHR, textStatus ) { 
                        document.getElementById("kt_reset_submit").setAttribute("data-kt-indicator", "off"); 
                        document.getElementById("kt_reset_submit").removeAttribute("disabled"); 
                        callCaptcha();
                        Swal.fire({
                                text: 'Mohon refresh halaman dan ulangi!',
                                icon:"error",
                                buttonsStyling:!1,
                                confirmButtonText:"Close",
                                customClass:{confirmButton:"btn btn-primary" }}) ;
                }  }) 
            }

            function reset() {   
                var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content')  ;  
                var str = $( "#kt_reset_form" ).serialize();       
                var string = "&_token="+token+"&"+str ;   
                $.ajax({
                    type : 'POST',    
                    url : "{{ route('confirm_reset') }}",
                    data : string,
                    dataType : 'json',
                    success:function(data) { 
                        document.getElementById("kt_reset_submit").setAttribute("data-kt-indicator", "off"); 
                        document.getElementById("kt_reset_submit").removeAttribute("disabled");
                        if (data.status_process == 0) {  
                            Swal.fire({
                                text: data.msg,
                                icon:"error",
                                buttonsStyling:!1,
                                confirmButtonText:"Close",
                                customClass:{confirmButton:"btn btn-primary" }}).then(function(){
                                    callCaptcha();
                                }) ;
                        } else {
                            Swal.fire({
                                text: data.msg,
                                icon:"success",
                                buttonsStyling:!1,
                                confirmButtonText:"Close",
                                customClass:{confirmButton:"btn btn-primary" }}).then(function(){
                                    window.location = "login" ;  
                                }) ;
                        }
                }, 
                    error: function( jqXHR, textStatus ) { 
                        document.getElementById("kt_reset_submit").setAttribute("data-kt-indicator", "off"); 
                        document.getElementById("kt_reset_submit").removeAttribute("disabled"); 
                        callCaptcha();
                        Swal.fire({
                                text: 'Mohon refresh halaman dan ulangi!',
                                icon:"error",
                                buttonsStyling:!1,
                                confirmButtonText:"Close",
                                customClass:{confirmButton:"btn btn-primary" }}) ;
                } 
            }) 
    }  
            
})
</script> 
@endsection
