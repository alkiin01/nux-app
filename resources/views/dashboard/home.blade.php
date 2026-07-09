@extends('../layouts/app') 
 

@section('subhead')
    <title>{{ $head_title }}</title>
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
                    <div class="card">
                        <!--begin::Body-->
                        <div class="card-body p-lg-17">
                            <!--begin::About-->
                            <div class="mb-18">
                                <!--begin::Wrapper-->
                                <div class="mb-5">
                                    <!--begin::Top-->
                                    <div class="text-center mb-15">
                                        <!--begin::Title-->
                                        <h3 class="fs-2hx text-dark mb-5">About Us</h3>
                                        <!--end::Title-->
                                        <!--begin::Text-->

                                        <div class="fs-5 text-muted">NUX (<strong>N</strong>ext <strong>U</strong>ser e<strong>X</strong>perience), dirancang untuk membawa pengalaman pengguna ke tingkat selanjutnya. Dengan mengutamakan inovasi, kesederhanaan, dan efisiensi, NUX hadir sebagai solusi yang memberdayakan pengguna untuk mencapai produktivitas optimal dalam setiap interaksi. Filosofi kami adalah menciptakan platform yang intuitif dan berorientasi masa depan, di mana teknologi bukan hanya alat, tetapi mitra dalam perjalanan menuju kesuksesan.</div>

                                        <br> <br>

                                        <div class="fs-5 text-muted" style="font-style: italic;">NUX (<strong>N</strong>ext <strong>U</strong>ser e<strong>X</strong>perience), is designed to elevate user experiences to the next level. By prioritizing innovation, simplicity, and efficiency, NUX serves as a solution that empowers users to achieve optimal productivity in every interaction. Our philosophy is to create an intuitive and future-oriented platform where technology is not just a tool but a partner in the journey toward success.</div>

                                        <br> <br>

                                        <div class="fs-5 text-muted">NUX (<strong>N</strong>ext <strong>U</strong>ser e<strong>X</strong>perience), ถูกออกแบบมาเพื่อยกระดับประสบการณ์ของผู้ใช้งานให้ก้าวไปอีกขั้น ด้วยการให้ความสำคัญกับนวัตกรรม ความเรียบง่าย และประสิทธิภาพ NUX จึงเป็นโซลูชันที่ช่วยให้ผู้ใช้งานสามารถบรรลุประสิทธิผลสูงสุดในทุกการใช้งาน ปรัชญาของเราคือการสร้างแพลตฟอร์มที่ใช้งานง่ายและมุ่งสู่อนาคต ซึ่งเทคโนโลยีไม่ได้เป็นเพียงเครื่องมือ แต่เป็นคู่คิดในเส้นทางสู่ความสำเร็จ.</div>

                                        

                                        <!--end::Text-->
                                    </div> 
                                    <!--end::Container-->
                                </div> 
                                <!--end::Description-->
                            </div> 
                            <!--end::Card-->
                        </div>
                        <!--end::Body-->
                    </div> 

        </div> 
    </div> 
</div>
  


@endsection
 
