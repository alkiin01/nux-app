@extends('../layouts/app')
@section('subhead')
    <title>{{ $head_title }}</title>

    <script type="text/javascript">
        $(document).ready(function() {

            const urlParams = new URLSearchParams(window.location.search);
            var ref_form = urlParams.get('ref_form');
            var ref_tab = urlParams.get('ref_tab');
            var ref_doc = urlParams.get('ref_doc');
            var revise = urlParams.get('revise');
            if (ref_doc == null) {
                $("#kt_activity_home_tab").addClass('show active');
            } else {
                $('#temp_id').val(ref_doc);
                document_preview(ref_doc);
            }
            console.log(FullCalendar.Calendar())
            KTCalendarBasic.init();

        })
    </script>
    <link href="/assets/plugins/custom/fullcalendar/fullcalendar.bundle.css" rel="stylesheet" type="text/css">
    <script src="/assets/plugins/custom/fullcalendar/fullcalendar.bundle.js"></script>
@endsection
</link>
<script src="/assets/js/jquery/jquery.min.js"></script>

<style>
    .fc-event:not(.fc-event-draggable) {
        cursor: pointer !important;
    }

    .color-option {
        display: flex;
        align-items: center;
        /* Jarak antar pilihan */
        margin: 5px 0;
    }

    .color-radio {
        appearance: none;
        width: 25px;
        height: 25px;
        border-radius: 50%;
        cursor: pointer;
        margin-right: 10px;
        border: 2px solid #bbb;
    }

    .color-radio:checked {
        transform: scale(1.2);
        border: 2px solid #fff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
    }

    .color-label {
        font-size: 16px;
        font-weight: bold;
    }


    .fc-event-danger {
        background-color: #dc3545 !important;
        border-color: #dc3545 !important;
        color: #fff !important;
    }

    .fc-event-danger.fc-event-solid-warning {
        background-color: #ffc107 !important;
        /* Bootstrap Warning */
        border-color: #ffc107 !important;
        color: #212529 !important;
    }

    .fc-event-success {
        background-color: #198754 !important;
        border-color: #198754 !important;
        color: #fff !important;
    }

    .fc-event-primary {
        background-color: #0d6efd !important;
        border-color: #0d6efd !important;
        color: #fff !important;
    }

    .fc-event-light {
        background-color: #f8f9fa !important;
        border-color: #f8f9fa !important;
        color: #212529 !important;
    }

    .fc-event-light.fc-event-solid-primary {
        background-color: #0d6efd !important;
        /* Darker shade of Bootstrap Primary */
        border-color: #0d6efd !important;
        color: #fff !important;
    }

    .fc-event-warning {
        background-color: #ffc107 !important;
        border-color: #ffc107 !important;
        color: #212529 !important;
    }

    .fc-event-info {
        background-color: #0dcaf0 !important;
        border-color: #0dcaf0 !important;
        color: #fff !important;
    }

    .fc-event-solid-danger.fc-event-light {
        background-color: #dc3545 !important;
        border-color: #dc3545 !important;
        color: #fff !important;
    }

    .fc-event-solid-info.fc-event-light {
        background-color: #f8f9fa !important;
        border-color: #0dcaf0 !important;
        color: #fff !important;
    }
</style>


@section('subcontent')
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
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
                    <a id="kt_activity_home_tab" class="nav-link justify-content-center text-active-gray-800 active"
                        data-bs-toggle="tab" role="tab" href="#kt_activity_home">Home</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a id="kt_activity_preview_tab" class="nav-link justify-content-center text-active-gray-800"
                        data-bs-toggle="tab" role="tab" href="#kt_activity_preview">Preview</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="tab-content">
            <div id="kt_activity_home" class="card-body p-0 tab-pane fade show active" role="tabpanel"
                aria-labelledby="kt_activity_home_tab">
                <div class="post d-flex flex-column-fluid" id="kt_post">
                    <div id="kt_content_container" class="container-xxl">
                        <div class="row g-5 g-xl-8 mb-2">
                            <div class="card col-xxl-12 card-custom gutter-b">
                                <div class="card-header">
                                    <div class="card-title">

                                    </div>
                                    <div class="card-toolbar">
                                        <button type="button" class="btn btn-primary btn-sm me-3" id="btn_add_document"
                                            onclick="add_document()">
                                            <span id="svg_add_document" class="svg-icon svg-icon-2">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                                    viewBox="0 0 24 24" version="1.1">
                                                    <defs />
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <rect fill="#000000" x="4" y="11" width="16" height="2"
                                                            rx="1" />
                                                        <rect fill="#000000" opacity="0.3"
                                                            transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) "
                                                            x="4" y="11" width="16" height="2" rx="1" />
                                                    </g>
                                                </svg>
                                            </span>
                                            <span id="spinner_add_document"
                                                class="spinner-border spinner-border-sm align-middle ms-2"
                                                style="display: none;"></span>
                                            <span id="btn_text_add_document">Add Schedule</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column-fluid mt-lg-5 mt-sm-5">
                    <div id="kt_content_container" class="container-xxl">
                        <div class="row g-5 g-xl-8">
                            <div class="card col-md-8 card-stretch gutter-b">
                                <div class="card-body">
                                    <div data-toggle="calendar" class="" id="kt_calendar"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="kt_activity_preview" class="card-body p-0 tab-pane fade show" role="tabpanel"
                aria-labelledby="kt_activity_preview_tab">
                <div class="d-flex flex-column-fluid">
                    <div id="kt_content_container" class="container-xxl">
                        <div id="div-form-teams"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function backHome() {
            var button = document.getElementById('btn_back_home');
            var svg = document.getElementById('svg_back_home');
            var spinner = document.getElementById('spinner_back_home');
            var buttonText = document.getElementById('btn_text_back_home');
            svg.style.display = 'none';
            spinner.style.display = 'inline-block';
            buttonText.textContent = 'Please Wait...';
            button.disabled = true;
            // KTCalendarBasic.init();

            setTimeout(function() {
                svg.style.display = 'inline-block';
                spinner.style.display = 'none';
                buttonText.textContent = 'Back';
                button.disabled = false;
                document.getElementById('kt_activity_home_tab').click();
            }, 300)

        }

        function add_document() {
            var button = document.getElementById('btn_add_document');
            var svg = document.getElementById('svg_add_document');
            var spinner = document.getElementById('spinner_add_document');
            var buttonText = document.getElementById('btn_text_add_document');
            svg.style.display = 'none';
            spinner.style.display = 'inline-block';
            buttonText.textContent = 'Please Wait...';
            button.disabled = true;


            $("#div-form-teams").html("");
            var token = $("[name=_token]").val();
            var trc_unix_id = 0;
            var data = {
                _token: token
            };
            $.ajax({
                type: "post",
                url: "{{ route('team.add_document') }}",
                data: data,
                cache: false,
                success: function(data) {
                    svg.style.display = 'inline-block';
                    spinner.style.display = 'none';
                    buttonText.textContent = 'Add Schedule';
                    button.disabled = false;
                    document.getElementById('kt_activity_preview_tab').click();
                    $("#div-form-teams").html(data);
                    $("#lds-roller-form").css("display", "");
                    $("#form_label").css("display", "none");
                    $("#form_loader").css("display", "");
                    $("#team_name").val("");
                    $("#team_role").val("");
                    $("#id_teams").val("0");
                    setTimeout(function() {
                        $("#form_label").css("display", "");
                        $("#form_loader").css("display", "none");
                        $("#lds-roller-form").css("display", "none");
                        $("#createNew").val(1);
                    }, 500)
                },
                error: function(jqXHR, textStatus) {
                    svg.style.display = 'inline-block';
                    spinner.style.display = 'none';
                    buttonText.textContent = 'Add Schedule';
                    button.disabled = false;
                    Toast.fire({
                        position: 'top-end',
                        title: " Please reload and try again! ",
                        icon: "error"
                    })
                }
            });
        }

        var KTCalendarBasic = function() {

            return {
                //main function to initiate the module
                init: function() {
                    var todayDate = moment().startOf('day');
                    var YM = todayDate.format('YYYY-MM');
                    var YESTERDAY = todayDate.clone().subtract(1, 'day').format('YYYY-MM-DD');
                    var TODAY = todayDate.format('YYYY-MM-DD');
                    var TOMORROW = todayDate.clone().add(1, 'day').format('YYYY-MM-DD');

                    var calendarEl = document.getElementById('kt_calendar');
                    var calendar = new FullCalendar.Calendar(calendarEl, {
                        themeSystem: 'bootstrap5',
                        isRTL: KTUtil.isRTL(),
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth,timeGridWeek,timeGridDay'
                        },
                        height: 600,
                        contentHeight: 780,
                        aspectRatio: 3,
                        nowIndicator: true,
                        now: TODAY + 'T09:25:00',

                        views: {
                            dayGridMonth: {
                                buttonText: 'month'
                            },
                            timeGridWeek: {
                                buttonText: 'week'
                            },
                            timeGridDay: {
                                buttonText: 'day'
                            }
                        },
                        initialView: 'dayGridMonth',
                        initialDate: TODAY,
                        editable: false,
                        dayMaxEvents: true, // allow "more" link when too many events
                        navLinks: true,
                        events: '{{ route('schedule.get_schedule') }}',
                        eventClick: function(info) {
                            var id = info.event.id;
                            var eventTitle = info.event.title;
                            var eventStart = info.event.start.toISOString().split('T')[0];
                            var eventDescription = info.event.extendedProps.description ||
                                'No Description';
                            var eventLocation = info.event.extendedProps.location ||
                                'No Location';
                            var data = {
                                trc_unix_id: id,
                                _token: $("[name=_token]").val(),
                            }
                            $.ajax({
                                type: "POST",
                                url: "{{ route('schedule.get_schedule_by_id') }}",
                                data: data,
                                dataType: "json",
                                success: function(data) {
                                    console.log(data);
                                    add_document()

                                }
                            });
                        },
                        // events: [{
                        //         title: 'All Day Event',
                        //         start: YM + '-01',
                        //         description: 'Toto lorem ipsum dolor sit incid idunt ut',
                        //         className: "fc-event-danger fc-event-solid-warning"
                        //     },
                        //     {
                        //         title: 'Reporting',
                        //         start: YM + '-14T13:30:00',
                        //         description: 'Lorem ipsum dolor incid idunt ut labore',
                        //         end: YM + '-14',
                        //         className: "fc-event-success"
                        //     },
                        //     {
                        //         title: 'Company Trip',
                        //         start: YM + '-02',
                        //         description: 'Lorem ipsum dolor sit tempor incid',
                        //         end: YM + '-03',
                        //         className: "fc-event-primary"
                        //     },
                        //     {
                        //         title: 'ICT Expo 2017 - Product Release',
                        //         start: YM + '-03',
                        //         description: 'Lorem ipsum dolor sit tempor inci',
                        //         end: YM + '-05',
                        //         className: "fc-event-light fc-event-solid-primary"
                        //     },
                        //     {
                        //         title: 'Dinner',
                        //         start: YM + '-12',
                        //         description: 'Lorem ipsum dolor sit amet, conse ctetur',
                        //         end: YM + '-10'
                        //     },
                        //     {
                        //         id: 999,
                        //         title: 'Repeating Event',
                        //         start: YM + '-09T16:00:00',
                        //         description: 'Lorem ipsum dolor sit ncididunt ut labore',
                        //         className: "fc-event-danger"
                        //     },
                        //     {
                        //         id: 1000,
                        //         title: 'Repeating Event',
                        //         description: 'Lorem ipsum dolor sit amet, labore',
                        //         start: YM + '-16T16:00:00'
                        //     },
                        //     {
                        //         title: 'Conference',
                        //         start: YESTERDAY,
                        //         end: TOMORROW,
                        //         description: 'Lorem ipsum dolor eius mod tempor labore',
                        //         className: "fc-event-primary"
                        //     },
                        //     {
                        //         title: 'Meeting',
                        //         start: TODAY + 'T10:30:00',
                        //         end: TODAY + 'T12:30:00',
                        //         description: 'Lorem ipsum dolor eiu idunt ut labore'
                        //     },
                        //     {
                        //         title: 'Lunch',
                        //         start: TODAY + 'T12:00:00',
                        //         className: "fc-event-info",
                        //         description: 'Lorem ipsum dolor sit amet, ut labore'
                        //     },
                        //     {
                        //         title: 'Meeting',
                        //         start: TODAY + 'T14:30:00',
                        //         className: "fc-event-warning",
                        //         description: 'Lorem ipsum conse ctetur adipi scing'
                        //     },
                        //     {
                        //         title: 'Happy Hour',
                        //         start: TODAY + 'T17:30:00',
                        //         className: "fc-event-info",
                        //         description: 'Lorem ipsum dolor sit amet, conse ctetur'
                        //     },
                        //     {
                        //         title: 'Dinner',
                        //         start: TOMORROW + 'T05:00:00',
                        //         className: "fc-event-solid-danger fc-event-light",
                        //         description: 'Lorem ipsum dolor sit ctetur adipi scing'
                        //     },
                        //     {
                        //         title: 'Birthday Party',
                        //         start: TOMORROW + 'T07:00:00',
                        //         className: "fc-event-primary",
                        //         description: 'Lorem ipsum dolor sit amet, scing'
                        //     },
                        //     {
                        //         title: 'Click for Google',
                        //         url: 'http://google.com/',
                        //         start: YM + '-28',
                        //         className: "fc-event-solid-info fc-event-light",
                        //         description: 'Lorem ipsum dolor sit amet, labore'
                        //     }
                        // ],

                        eventDidMount: function(info) {
                            var element = $(info.el);

                            if (info.event.extendedProps && info.event.extendedProps.description) {
                                if (element.hasClass('fc-day-grid-event')) {
                                    element.data('content', info.event.extendedProps.description);
                                    element.data('placement', 'top');
                                    KTApp.initPopover(element);
                                } else if (element.hasClass('fc-time-grid-event')) {
                                    element.find('.fc-title').append('<div class="fc-description">' +
                                        info.event.extendedProps.description + '</div>');
                                } else if (element.find('.fc-list-item-title').length !== 0) {
                                    element.find('.fc-list-item-title').append(
                                        '<div class="fc-description">' + info.event.extendedProps
                                        .description + '</div>');
                                }
                            }
                        }
                    });

                    calendar.render();
                }
            };
        }();
    </script>
@endsection
