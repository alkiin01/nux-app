@extends('../layouts/app') 
 

@section('subhead')
    <title>{{ $head_title }}</title>  
@endsection 

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
 
    <div class="d-flex flex-column flex-root"> 
        <div class="d-flex flex-column flex-column-fluid">
            <div class="d-flex flex-column flex-column-fluid text-center p-10 py-lg-15"> 
                <div class="pt-lg-10 mb-10"> 
                    <h1 class="fw-bolder fs-2qx text-gray-800 mb-10">Comming Soon</h1>   
                </div> 
                <div class="d-flex flex-row-auto bgi-no-repeat bgi-position-x-center bgi-size-contain bgi-position-y-bottom min-h-100px min-h-lg-350px" 
                style="background-image: url(public/assets/media/illustrations/sketchy-1/17.png"></div>
            </div>  
        </div> 
    </div>
         
@endsection