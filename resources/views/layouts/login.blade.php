<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'TEMPLATE') }}</title> 
    <link rel="shortcut icon" href="<?= env('APP_ASSETS') ?>assets/media/logos/apple-icon.png"/> 
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" /> 
    <link href="<?= env('APP_ASSETS') ?>assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="<?= env('APP_ASSETS') ?>assets/css/style.bundle.css" rel="stylesheet" type="text/css" /> 
    
    <meta property="og:site_name" content="EPICOR PORTAL">
	<meta property="og:title" content="EPICOR PORTAL" />
	<meta property="og:description" content="PT Summit Adyawinsa Indonesia" />
	<meta property="og:image" itemprop="image" content="<?= env('APP_ASSETS') ?>assets/media/logos/apple-icon.png"/>
	<meta property="og:type" content="website" />
	<meta property="og:updated_time" content="1440432930" />
    
    <meta name="csrf-token" content="{{ csrf_token() }}">  
</head>                                

<body id="kt_body" class="bg-body text-sm"> 
    <div class="d-flex flex-column flex-root"> 
            <div class="d-flex flex-column flex-column-fluid bgi-position-y-bottom position-x-center bgi-no-repeat bgi-size-contain bgi-attachment-fixed"> 
                <div class="d-flex flex-center flex-column flex-column-fluid p-3 pb-lg-3"> 
                    @yield('content')  
                </div>  
           </div>  
    </div>
    @yield('script')
    <script>var hostUrl = "/<?= env('APP_ASSETS') ?>>assets/";</script> 
    <script src="<?= env('APP_ASSETS') ?>assets/plugins/global/plugins.bundle.js"></script>
    <script src="<?= env('APP_ASSETS') ?>assets/js/scripts.bundle.js"></script>   
    @yield('script_ext') 
</body>
</html>
