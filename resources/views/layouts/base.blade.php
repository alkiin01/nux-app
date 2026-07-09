<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"> 
<head><base href="<?= config('APP_ASSETS') ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @yield('head')   
	<link rel="shortcut icon" href="<?= env('APP_ASSETS') ?>assets/media/logos/apple-icon.png"/> 
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />    
	<link href="<?= env('APP_ASSETS') ?>assets/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />  
	<link href="<?= env('APP_ASSETS') ?>assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
	<link href="<?= env('APP_ASSETS') ?>assets/css/style.bundle.css" rel="stylesheet" type="text/css" /> 
    <meta name="csrf-token" content="{{ csrf_token() }}"> 
	<style> 
        .fa-spinner:before {
            font-size: 80px; 
            color: black;
        }
        #kt_goodreceives_table_processing {  
            width: 100%;
            height: 100%;
            box-shadow: none;
            text-align: center;
            vertical-align: middle;
            opacity: 0.4 ;
        }

        .lds-roller {
                display: inline-block;
                position: relative;
                width: 80px;
                height: 80px;
                }
                .lds-roller div {
                animation: lds-roller 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
                transform-origin: 40px 40px;
                }
                .lds-roller div:after {
                content: " ";
                display: block;
                position: absolute;
                width: 7px;
                height: 7px;
                border-radius: 50%;
                background: black;
                margin: -4px 0 0 -4px;
                }
                .lds-roller div:nth-child(1) {
                animation-delay: -0.036s;
                }
                .lds-roller div:nth-child(1):after {
                top: 63px;
                left: 63px;
                }
                .lds-roller div:nth-child(2) {
                animation-delay: -0.072s;
                }
                .lds-roller div:nth-child(2):after {
                top: 68px;
                left: 56px;
                }
                .lds-roller div:nth-child(3) {
                animation-delay: -0.108s;
                }
                .lds-roller div:nth-child(3):after {
                top: 71px;
                left: 48px;
                }
                .lds-roller div:nth-child(4) {
                animation-delay: -0.144s;
                }
                .lds-roller div:nth-child(4):after {
                top: 72px;
                left: 40px;
                }
                .lds-roller div:nth-child(5) {
                animation-delay: -0.18s;
                }
                .lds-roller div:nth-child(5):after {
                top: 71px;
                left: 32px;
                }
                .lds-roller div:nth-child(6) {
                animation-delay: -0.216s;
                }
                .lds-roller div:nth-child(6):after {
                top: 68px;
                left: 24px;
                }
                .lds-roller div:nth-child(7) {
                animation-delay: -0.252s;
                }
                .lds-roller div:nth-child(7):after {
                top: 63px;
                left: 17px;
                }
                .lds-roller div:nth-child(8) {
                animation-delay: -0.288s;
                }
                .lds-roller div:nth-child(8):after {
                top: 56px;
                left: 12px;
                }
                @keyframes lds-roller {
                0% {
                    transform: rotate(0deg);
                }
                100% {
                    transform: rotate(360deg);
                }
                }

    </style>
    
</head>    
@yield('body')  

<script src="<?= env('APP_ASSETS') ?>assets/plugins/custom/datatables/datatables.bundle.js"></script> 
<script src="<?= env('APP_ASSETS') ?>assets/js/custom/apps/customers/list/export.js"></script> 
<script src="<?= env('APP_ASSETS') ?>assets/js/jquery.priceformat.min.js"></script>

</html>