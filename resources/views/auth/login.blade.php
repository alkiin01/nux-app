@extends('layouts.login') 
@section('content') 
<script src="<?= env('APP_ASSETS') ?>assets/js/jquery/jquery.min.js"></script>   
    <div class="w-lg-500px bg-body rounded shadow-sm p-10 p-lg-15 mx-auto"> 
        <a href="/" class="d-flex flex-center flex-row mb-8">
            <img alt="Logo" src="<?= env('APP_ASSETS') ?>assets/media/logos/epicor-logo.png" style="width:50%;"/> 
        </a> 

    <form class="form  text-sm w-80" novalidate="novalidate" id="kt_sign_in_form" method="POST" action="{{ route('login') }}">
        @csrf 
        <div class="text-center mb-7">  
            <div class="text-center mb-7"> 
                <h2 class="text-dark">WELCOME </h2> 
                <div class="text-gray-400 fw-bold">PT SUMMIT ADYAWINSA INDONESIA</div>  
            </div> 
        </div> 
        <div class="d-flex align-items-center mb-7">
            <div class="border-bottom border-gray-300 mw-50 w-100"></div>
            <span class="fw-bold text-gray-400 mx-2">LOGIN</span>
            <div class="border-bottom border-gray-300 mw-50 w-100"></div>
        </div>
        <div class="fv-row mb-7"> 
            <label class="form-label fs-6 fw-bolder text-dark">Username</label> 
            <input class="form-control form-control-lg form-control-solid @error('username') is-invalid @enderror" 
            type="text" id="username" name="username" value="{{ old('username') }}" autocomplete="off" /> 
            @error('username') 
            <strong style="color: red;"></strong> 
            <div class="fv-plugins-message-container invalid-feedback message-feedback">
                <div data-field="username" data-validator="usernameAddress">{{ $message }}</div>
            </div>
            @enderror
        </div> 

        
        <div class="fv-row mb-7"> 
            <div class="d-flex flex-stack mb-2"> 
                <label class="form-label fw-bolder text-dark fs-6 mb-0">Password</label>  
            </div> 
            <input class="form-control form-control-lg form-control-solid @error('password') is-invalid @enderror" type="password" name="password" autocomplete="off" /> 
            @error('password') 
            <div class="fv-plugins-message-container invalid-feedback message-feedback">
                <div data-field="password" data-validator="notEmpty">{{ $message }}</div>
            </div> 
            @enderror
        </div>  
        <!-- <div class="row fv-row mb-5 text-center"> 
            <div id="captcha_div"></div> 
        </div>  -->
        <div class="fv-row mb-5"> 
            <div class="d-flex"> 
                <label class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span class="form-check-label">Remember me</span>
                </label> 
            </div>
        </div> 
        <div class="text-center"> 
            <button type="button" id="kt_sign_in_submit" class="btn btn-lg btn-flex flex-center btn-primary w-100 mb-5">
                <span class="indicator-label">Login</span>
                <span class="indicator-progress">Please wait...
                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
            </button>  
            <div id="div_processing"></div>   
        </div> 

        

    </div> 
</div> 
@endsection

@section('script')  
 
<script> 
function myFunction() {
    var checkBox = document.getElementById("remember"); 
    if (checkBox.checked == true){
    return 1 ;
    } else {
    return 0 ;
    }
}
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
var KTSigninGeneral=function(){   
    var t,e,i;
    return{init:function(){
        t=document.querySelector("#kt_sign_in_form"),
        e=document.querySelector("#kt_sign_in_submit"),
        i=FormValidation.formValidation(
        t,{fields:{
            email:{ validators:{notEmpty:{message:"Email is required!"}}},
            password:{ validators:{notEmpty:{message:"Password is required!"}}}}, 
        plugins:{
            trigger:
            new FormValidation.plugins.Trigger,bootstrap:
            new FormValidation.plugins.Bootstrap5({rowSelector:".fv-row"})}}),
            e.addEventListener("click",(function(n){n.preventDefault(),i.validate().then((
                function(i){"Valid"==i?(
            e.setAttribute("data-kt-indicator","on"),e.disabled=!0,setTimeout((function(){  
                check_robot()
                }),100)):
                Swal.fire({
                    text:"Please, all fields must be filled!",
                    icon:"error",
                    buttonsStyling:!1,
                    confirmButtonText:"Close",
                    customClass:{confirmButton:"btn btn-primary" }})}))}))}}}();
            KTUtil.onDOMContentLoaded((function(){ 
                KTSigninGeneral.init()
            }));
   
            $('#email').focus();   

            function check_robot() {
                var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content')  ;  
                var str = $( "#kt_sign_in_form" ).serialize();       
                var string = "&_token="+token+"&"+str ;   
                $.ajax({
                    type : 'POST',    
                    url : "{{ route('check_robot_login') }}",
                    data : string,
                    dataType : 'json',
                    success:function(data) { 
                        if (data.status_process == 0) { 
                            document.getElementById("kt_sign_in_submit").setAttribute("data-kt-indicator", "off"); 
                            document.getElementById("kt_sign_in_submit").removeAttribute("disabled");
                            Swal.fire({
                                text: data.msg,
                                icon:"error",
                                buttonsStyling:!1,
                                confirmButtonText:"Close",
                                customClass:{confirmButton:"btn btn-primary" }}).then(function(){
                                    callCaptcha();
                                }) ;
                        } else {
                            login()
                        }
                }, 
                    error: function( jqXHR, textStatus ) { 
                        document.getElementById("kt_sign_in_submit").setAttribute("data-kt-indicator", "off"); 
                        document.getElementById("kt_sign_in_submit").removeAttribute("disabled"); 
                        callCaptcha();
                        Swal.fire({
                                text: 'Please, refresh and try again!',
                                icon:"error",
                                buttonsStyling:!1,
                                confirmButtonText:"Close",
                                customClass:{confirmButton:"btn btn-primary" }}) ;
                } 
             }) 
            }

            async function login() {   
                $("#div_processing").append('<button type="submit" id="processing" hidden></button>');
                $("#processing").click();
            } 
            
            $('#kt_sign_in_form').on('keyup', function(e) { 
                $(".message-feedback").remove();
                if (e.keyCode === 13) {
                    $("#kt_sign_in_submit").click();
                }
            }) 
 
  
        })
    </script> 
@endsection