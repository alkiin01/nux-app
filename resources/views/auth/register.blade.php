@extends('layouts.login') 
@section('content')
<script src="/public/assets/js/jquery/jquery.min.js"></script>   
<script type="text/javascript">
$(function () { 
        $("#phone-num").keypress(function (e) {
            var keyCode = e.keyCode || e.which;  
            var regex = /^[0-9]+$/; 
            var isValid = regex.test(String.fromCharCode(keyCode)); 
            return isValid;
        });    
    });
 
$(document).ready(function(){  
    $.fn.capitalize = function () {
    $.each(this, function () {
    var split = this.value.split(' ');
    for (var i = 0, len = split.length; i < len; i++) {
    split[i] = split[i].charAt(0).toUpperCase() + split[i].slice(1);  }
    this.value = split.join(' '); });
    return this; }; 

    $(".form_input_cap").keyup(function(){ 
        $(this).capitalize();   
        $(this).val($(this).val().replace(/['",]/g, ''));
    });

})
</script>

<div class="w-lg-600px bg-body rounded shadow-sm p-10 p-lg-15 mx-auto">
    <a href="/" class="d-flex flex-center flex-row mb-12">
        <img alt="Logo" src="/public/assets/media/logos/horizon-logo.png" style="width: 100%;"/> 
    </a> 
        <form class="form w-100" novalidate="novalidate" id="kt_sign_up_form" method="POST" action="{{ route('register') }}">
            @csrf
            <div class="mb-10 text-center"> 
                <h1 class="text-dark mb-3">Formulir pendaftaran akun</h1> 
                <div class="text-gray-400 fw-bold fs-4">sudah punya akun?
                <a href="login" class="link-primary fw-bolder">Klik di sini</a></div> 
            </div>  
            <div class="d-flex align-items-center mb-10">
                <div class="border-bottom border-gray-300 mw-50 w-100"></div>
                <span class="fw-bold text-gray-400 fs-7 mx-2">PENDAFTARAN</span>
                <div class="border-bottom border-gray-300 mw-50 w-100"></div>
            </div>
            
            <div class="row fv-row mb-7"> 
                <div class="col-xl-6">
                    <label class="form-label fw-bolder text-dark fs-6">Nama Depan</label>
                    <input class="form-control form-control-lg form-control-solid form_input_cap" type="text" name="first-name" id="first-name" autocomplete="off" value="{{ old('first-name') }}"/>
                </div> 
                <div class="col-xl-6">
                    <label class="form-label fw-bolder text-dark fs-6">Nama Belakang</label>
                    <input class="form-control form-control-lg form-control-solid form_input_cap" type="text" name="last-name" id="last-name" autocomplete="off" value="{{ old('last-name') }}"/>
                </div> 
            </div>
            
            <div class="fv-row mb-7">
                <label class="form-label fw-bolder text-dark fs-6">Ponsel</label>
                <input class="form-control form-control-lg form-control-solid @error('phone-num') is-invalid @enderror" type="text" placeholder="Ex. 628128880646" name="phone-num" id="phone-num" autocomplete="off" value="{{ old('phone-num') }}"/>
                @error('phone-num') 
                <div class="fv-plugins-message-container invalid-feedback message-feedback">
                    <div data-field="phone-num" data-validator="notEmpty">{{ $message }}</div>
                </div> 
                @enderror
            </div>

            <div class="fv-row mb-7">
                <label class="form-label fw-bolder text-dark fs-6">Email</label>
                <input class="form-control form-control-lg form-control-solid" type="email" placeholder="" name="email" autocomplete="off" value="{{ old('email') }}"/>
            </div>
                            
        <div class="mb-10 fv-row" data-kt-password-meter="true"> 
            <div class="mb-1"> 
                <label class="form-label fw-bolder text-dark fs-6">Kata Sandi</label> 
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
            <div class="text-muted">Gunakan 8 atau lebih karakter, setidaknya kombinasi angka &amp; huruf.</div> 
        </div> 

        <div class="fv-row mb-5">
            <label class="form-label fw-bolder text-dark fs-6">Konfirmasi Kata Sandi</label>
            <input class="form-control form-control-lg form-control-solid" type="password" placeholder="" name="confirm-password" autocomplete="off" />
        </div>  
        
        <div class="row fv-row mb-7"> 
            <div id="captcha_div"></div> 
        </div>
  
        <div class="fv-row mt-5 mb-20">
            <label class="form-check form-check-custom form-check-solid form-check-inline">
                <input class="form-check-input" type="checkbox" name="toc" value="1" />
                <span class="form-check-label fw-bold text-gray-700 fs-6">Saya setuju
                <a href="#" class="ms-1 link-primary">Syarat dan Ketentuan</a>.</span>
            </label>
        </div> 

        <div class="text-center">
            <button type="button" id="kt_sign_up_submit" class="btn btn-lg btn-flex flex-center btn-primary w-100 mb-5">
                <span class="indicator-label">{{ __('Register') }}</span>
                <span class="indicator-progress">Mohon tunggu...
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
                e=document.querySelector("#kt_sign_up_form"),
                t=document.querySelector("#kt_sign_up_submit"), 
                s=KTPasswordMeter.getInstance(
                e.querySelector('[data-kt-password-meter="true"]')),
                a=FormValidation.formValidation(
                e,{fields:{
                    "first-name":{validators:{notEmpty:{message:"Nama depan harus diisi"}}},
                    "last-name":{validators:{notEmpty:{message:"Nama belakang harus diisi"}}},
                    "phone-num":{validators:{notEmpty:{message:"Nomor Ponsel harus diisi"}}}, 
                    email:{validators:{notEmpty:{message:"Alamat Email harus diisi"},
                    emailAddress:{message:"Alamat Email tidak benar"}}},
                    password:{validators:{notEmpty:{message:"Kata sandi harus diisi"},
                    callback:{message:"Kata sandi kombinasi angka & huruf",
                    callback:function(e){if(e.value.length<8) return r()}}}},
                    "confirm-password":{validators:{notEmpty:{message:"Konfirmasi kata sandi harus diisi"},
                    identical:{compare:function(){
                        return e.querySelector('[name="password"]').value},
                        message:"Konfirmasi kata sandi tidak tidak sama"}}},
                        toc:{validators:{notEmpty:{message:"Anda harus menyetujui Syarat dan Kondisi"}}}},
                        plugins:{trigger:
                            new FormValidation.plugins.Trigger({event:{password:!1}}),
                            bootstrap:new FormValidation.plugins.Bootstrap5({
                                rowSelector:".fv-row",eleInvalidClass:"",eleValidClass:""})}}),
                                t.addEventListener("click",(function(r){r.preventDefault(),
                                    a.revalidateField("password"),a.validate().then((
                                        function(a){"Valid"==a?(t.setAttribute("data-kt-indicator","on"),
                                        t.disabled=!0,setTimeout((function(){
                                            check_robot()
                                        }),100)):
                                        Swal.fire({
                                            text:"Mohon semua kolom diisi dengan benar!",
                                            icon:"error",
                                            buttonsStyling:!1,
                                            confirmButtonText:"Ok, mengerti!",
                                            customClass:{confirmButton:"btn btn-primary"}})}))})),
                                            e.querySelector('input[name="password"]').addEventListener("input",(
                                                function(){this.value.length>0&&a.updateFieldStatus("password","NotValidated")
                                            })
                                        )}
                                    }}();KTUtil.onDOMContentLoaded((function(){KTSignupGeneral.init()})); 
     
            $('#first-name').focus();    
            function check_robot() {
                var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content')  ;  
                var str = $( "#kt_sign_up_form" ).serialize();       
                var string = "&_token="+token+"&"+str ;   
                $.ajax({
                    type : 'POST',    
                    url : "{{ route('check_robot') }}",
                    data : string,
                    dataType : 'json',
                    success:function(data) { 
                        if (data.status_process == 0) { 
                            document.getElementById("kt_sign_up_submit").setAttribute("data-kt-indicator", "off"); 
                            document.getElementById("kt_sign_up_submit").removeAttribute("disabled");
                            Swal.fire({
                                text: data.msg,
                                icon:"error",
                                buttonsStyling:!1,
                                confirmButtonText:"Close",
                                customClass:{confirmButton:"btn btn-primary" }}).then(function(){
                                    callCaptcha();
                                }) ;
                        } else {
                            register()
                        }
                }, 
                    error: function( jqXHR, textStatus ) { 
                        document.getElementById("kt_sign_up_submit").setAttribute("data-kt-indicator", "off"); 
                        document.getElementById("kt_sign_up_submit").removeAttribute("disabled"); 
                        callCaptcha();
                        Swal.fire({
                                text: 'Mohon refresh halaman dan ulangi!',
                                icon:"error",
                                buttonsStyling:!1,
                                confirmButtonText:"Close",
                                customClass:{confirmButton:"btn btn-primary" }}) ;
                }  }) 
            }

            function register() {   
                var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content')  ;  
                var str = $( "#kt_sign_up_form" ).serialize();       
                var string = "&_token="+token+"&"+str ;   
                $.ajax({
                    type : 'POST',    
                    url : "{{ route('register_account') }}",
                    data : string,
                    dataType : 'json',
                    success:function(data) { 
                        document.getElementById("kt_sign_up_submit").setAttribute("data-kt-indicator", "off"); 
                        document.getElementById("kt_sign_up_submit").removeAttribute("disabled"); 
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
                            window.location = "login?after_register=1" ; 
                        }
                }, 
                    error: function( jqXHR, textStatus ) { 
                        document.getElementById("kt_sign_up_submit").setAttribute("data-kt-indicator", "off"); 
                        document.getElementById("kt_sign_up_submit").removeAttribute("disabled"); 
                        Swal.fire({
                                text: 'Please reload and try again!',
                                icon:"error",
                                buttonsStyling:!1,
                                confirmButtonText:"Close",
                                customClass:{confirmButton:"btn btn-primary" }}).then(function(){
                                    callCaptcha();
                                }) ;
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
