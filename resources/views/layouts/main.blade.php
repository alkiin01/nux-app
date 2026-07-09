@extends('../layouts/base')

@section('body')

    <body id="kt_body"
        class="header-fixed header-tablet-and-mobile-fixed toolbar-enabled toolbar-fixed aside-enabled aside-fixed"
        style="--kt-toolbar-height:55px;--kt-toolbar-height-tablet-and-mobile:55px">
        <div class="d-flex flex-column flex-root">
            <div class="page d-flex flex-row flex-column-fluid">
                <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
                    @yield('content')
                    <div class="footer py-4 d-flex flex-lg-column" id="kt_footer">
                        <div class="container-xxl d-flex flex-column flex-md-row flex-stack">
                            <div class="text-dark order-2 order-md-1">
                                <span class="text-gray-400 fw-bold me-1">&copy; ICT - </span>
                                <a href="https://summitadyawinsa.co.id" target="_blank"
                                    class="text-muted text-hover-primary fw-bold me-2 fs-6">PT SAI 2024</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
            <span class="svg-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)"
                        fill="black" />
                    <path
                        d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z"
                        fill="black" />
                </svg>
            </span>
        </div>

        <script>var hostUrl = "<?= env('APP_ASSETS') ?>assets/";</script>
        <script src="<?= env('APP_ASSETS') ?>assets/plugins/global/plugins.bundle.js"></script>
        <script src="<?= env('APP_ASSETS') ?>assets/js/scripts.bundle.js"></script>
        <script src="<?= env('APP_ASSETS') ?>assets/js/custom/widgets.js"></script>
        <script src="<?= env('APP_ASSETS') ?>assets/js/custom/documentation/forms/inputmask.js"></script>

        @yield('sub_footer')


        <script>
            $(document).ready(function () {
                Inputmask("Rp 999.999.999,99", {
                    "numericInput": true
                }).mask(".price_format");   
                Inputmask({
                    "mask": "9",
                    "repeat": 10,
                    "greedy": false
                }).mask(".number_format");
                $('input[type=number]').on('wheel', function (e) {
                    $(this).blur(); // Menghilangkan fokus agar tidak scroll
                });
            })

            // Initialize Toast only if Swal is available
            if (typeof Swal !== 'undefined') {
                var Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                })
            } else {
                // Fallback Toast implementation
                var Toast = {
                    fire: function (options) {
                        console.warn('SweetAlert2 not loaded, using alert fallback', options);
                        alert(options.title || options.message || 'Action result');
                    }
                };
            }
        </script>
    </body>
@endsection