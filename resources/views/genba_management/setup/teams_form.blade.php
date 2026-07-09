<div class="col-xxl-12">
    <div id="form_loader" style="text-align: center;">
        <div class="lds-roller mt-10 mb-10" id="lds-roller-form">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
    <div id="form_label">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Schedule</div>
                <div class="card-toolbar">
                    <button class="btn btn-success btn-sm" id="btn_back_home" onclick="backHome()">
                        <span id="svg_back_home" class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <defs />
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <path
                                        d="M3.95709826,8.41510662 L11.47855,3.81866389 C11.7986624,3.62303967 12.2013376,3.62303967 12.52145,3.81866389 L20.0429,8.41510557 C20.6374094,8.77841684 21,9.42493654 21,10.1216692 L21,19.0000642 C21,20.1046337 20.1045695,21.0000642 19,21.0000642 L4.99998155,21.0000673 C3.89541205,21.0000673 2.99998155,20.1046368 2.99998155,19.0000673 L2.99999828,10.1216672 C2.99999935,9.42493561 3.36258984,8.77841732 3.95709826,8.41510662 Z M10,13 C9.44771525,13 9,13.4477153 9,14 L9,17 C9,17.5522847 9.44771525,18 10,18 L14,18 C14.5522847,18 15,17.5522847 15,17 L15,14 C15,13.4477153 14.5522847,13 14,13 L10,13 Z"
                                        fill="#000000" />
                                </g>
                            </svg>
                        </span>
                        <span id="spinner_back_home" class="spinner-border spinner-border-sm svg-icon svg-icon-2"
                            style="display: none;"></span>
                        <span id="btn_text_back_home">Back</span>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row" id="form">
                    <div class="col">
                        <input type="hidden" id="sysID" value="0">
                        <div class="col-md-12 mb-5">
                            <label class="form-label" for="title">Title</label>
                            <input type="text" class="form-control" id="title">
                            {{-- <span class="form-text text-muted">Please enter your full name</span> --}}
                        </div>
                        <div class="col-md-12 mb-5">
                            <label class="form-label" for="description">Description</label>
                            <textarea style="height: 80px;" class="form-control" id="description" cols="30" rows="10"></textarea>
                            {{-- <span class="form-text text-muted">Please enter your contact number</span> --}}
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-5">
                                <label class="form-label" for="event_type">Event type</label>
                                <select class="form-select" id=event_type>
                                    <option value="Genba">Genba Management</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-5">
                                <label class="form-label" for="">Schedule Status</label>
                                <select class="form-select" id="status">
                                    <option value="Draft"> Draft</option>
                                    <option value="Published"> Published</option>
                                    <option value="Complete"> Complete</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="row">
                            <input type="hidden" id="SysID" value="0">
                            <div class="col-md-3 mb-5">
                                <label class="form-label" for="genba_date">Start Date</label>
                                <input type="date" name="genba_date" class="form-control" id="genba_date">
                            </div>
                            <div class="col-md-3 mb-5">
                                <label class="form-label" for="genba_time">Start time</label>
                                <input type="time" name="genba_time" class="form-control" id="genba_time">
                            </div>
                            <div class="col-md-3 mb-5">
                                <label class="form-label" for="end_date">End Date</label>
                                <input type="date" name="end_date" class="form-control" id="end_date">
                            </div>
                            <div class="col-md-3 mb-5">
                                <label class="form-label" for="end_time">End Time</label>
                                <input type="time" name="end_time" class="form-control" id="end_time">
                            </div>
                            <div class="col-md-6 mb-5">
                                <label class="form-label" for="execution_date">Execution Date</label>
                                <input type="date" name="execution_date" class="form-control"
                                    id="execution_date">
                            </div>
                            <div class="col-md-6 mb-5">
                                <label class="form-label" for="execution_status">Execution Status</label>
                                <input type="text" name="execution_status" class="form-control"
                                    id="execution_status">
                            </div>
                            <div class="col-md-12 mb-5">
                                <label class="form-label" for="execution_status">Marking Color</label>
                                <div id="colorContainer"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pt-5 pb-5">
                    <hr style="color: gray">
                    <button type="button" class="btn btn-primary btn-border btn-outline-primary btn-sm"
                        id="btn_submit_schedule" onclick="submit_schedule()">
                        <span
                            class="svg-icon svg-icon-primary svg-icon-2x"><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo1/dist/../src/media/svg/icons/General/Save.svg--><svg
                                xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24" />
                                    <path
                                        d="M17,4 L6,4 C4.79111111,4 4,4.7 4,6 L4,18 C4,19.3 4.79111111,20 6,20 L18,20 C19.2,20 20,19.3 20,18 L20,7.20710678 C20,7.07449854 19.9473216,6.94732158 19.8535534,6.85355339 L17,4 Z M17,11 L7,11 L7,4 L17,4 L17,11 Z"
                                        fill="#000000" fill-rule="nonzero" />
                                    <rect fill="#000000" opacity="0.3" x="12" y="4" width="3" height="5"
                                        rx="0.5" />
                                </g>
                            </svg><!--end::Svg Icon--></span>
                        <span id="spinner_submit_schedule"
                            class="spinner-border spinner-border-sm svg-icon svg-icon-2"
                            style="display: none;"></span>
                        <span id="btn_text_submit_schedule">Submit</span>
                    </button>
                    <button type="button" class="btn btn-primary btn-border btn-outline-primary btn-sm"
                        id="btn_generate_team" onclick="generate_team()">
                        <i class="svg-icon svg-icon-primary fas fa-users" id="svg_generate_team"></i>
                        <span id="spinner_generate_team" class="spinner-border spinner-border-sm svg-icon svg-icon-2x"
                            style="display: none;"></span>
                        <span id="btn_text_generate_team">Generate Team</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="col-xxl-12 mt-4" id="Team">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Team Member</div>
                </div>
                <div class="card-body">
                    <div class="col" id="form-team">
                        <input type="hidden" id="id_teams" value="">
                        <div class="col-md-6 mb-5">
                            <label>Team Name</label>
                            <input type="text" class="form-control" id="team_name">
                            {{-- <span class="form-text text-muted">Please enter your full name</span> --}}
                        </div>
                        <div class="col-md-6 mb-5">
                            <label>Role Team</label>
                            <input type="text" class="form-control" id="team_role">
                            {{-- <span class="form-text text-muted">Please enter your contact number</span> --}}
                        </div>
                    </div>
                    <div class="pt-5 pb-5">
                        <hr style="color: gray">
                        <button type="button" class="btn btn-primary btn-border btn-outline-primary btn-sm"
                            id="btn_submit_search" onclick="submit_teams()">
                            <span class="svg-icon svg-icon-primary svg-icon-2x">
                                <svg id="svg_submit_schedule" xmlns="http://www.w3.org/2000/svg"
                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                    viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <polygon points="0 0 24 0 24 24 0 24" />
                                        <path
                                            d="M17,4 L6,4 C4.79111111,4 4,4.7 4,6 L4,18 C4,19.3 4.79111111,20 6,20 L18,20 C19.2,20 20,19.3 20,18 L20,7.20710678 C20,7.07449854 19.9473216,6.94732158 19.8535534,6.85355339 L17,4 Z M17,11 L7,11 L7,4 L17,4 L17,11 Z"
                                            fill="#000000" fill-rule="nonzero" />
                                        <rect fill="#000000" opacity="0.3" x="12" y="4" width="3"
                                            height="5" rx="0.5" />
                                    </g>
                                </svg><!--end::Svg Icon--></span>
                            <span id="spinner_submit_search"
                                class="spinner-border spinner-border-sm svg-icon svg-icon-2"
                                style="display: none;"></span>
                            <span id="btn_text_submit_search">Submit</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-12 mt-4">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Detail</div>
                    <div class="card-toolbar">
                        <button type="button" class="btn btn-primary btn-sm me-3" id="btn_add_document"
                            onclick="add_document_member()">
                            <span id="svg_add_document" class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
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
                            <span id="spinner_add_document" class="spinner-border spinner-border-sm align-middle ms-2"
                                style="display: none;"></span>
                            <span id="btn_text_add_document">Add Member</span>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table align-middle table-row-dashed table-striped gy-2 fs-7" id="kt">
                        <thead>
                            <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                <th class="min-w-20px pe-2">No</th>
                                <th class="min-w-20px">Member Name</th>
                                <th class="min-w-100px">Member Email</th>
                                <th class="min-w-20px">action</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr class="text-start text-gray-900 fw-bolder fs-8 text-uppercase gs-0">
                                <th class="min-w-20px pe-2">No</th>
                                <th class="min-w-20px">Member Name</th>
                                <th class="min-w-20px">Member Email</th>
                                <th class="min-w-20px">action</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="card-footer text-muted"></div>
            </div>
        </div>
    </div>
    <script>
        var warna = ['#198754', '#0d6efd', '#ffc107', '#0dcaf0', '#dc3545'];
        var value = ['fc-event-primary', 'fc-event-light', 'fc-event-warning', 'fc-event-info',
            'fc-event-danger'
        ];

        var container = document.getElementById("colorContainer");
        var selectedColorText = document.getElementById("selectedColor");

        var div = document.createElement("div");
        div.className = "color-option";
        for (var i = 0; i < warna.length; i++) {

            var input = document.createElement("input");
            input.type = "radio";
            input.name = "color";
            input.value = value[i];
            input.className = "color-radio";
            input.style.backgroundColor = warna[i];

            // Event listener saat radio button dipilih
            var label = document.createElement("label");
            label.className = "color-label";
            label.innerText = "";

            div.appendChild(input);
            div.appendChild(label);
            container.appendChild(div);
        }
    </script>

    <script>
        function submit_schedule() {
            var button = document.getElementById('btn_submit_schedule');
            var svg = document.getElementById('svg_submit_schedule');
            var spinner = document.getElementById('spinner_submit_schedule');
            var buttonText = document.getElementById('btn_text_submit_schedule');
            svg.style.display = 'none';
            spinner.style.display = 'inline-block';
            buttonText.textContent = 'Please Wait...';
            button.disabled = true;
            var sysID = $("#sysID").val();
            var genba_date = $("#genba_date").val();
            var genba_time = $("#genba_time").val();

            var end_date = $("#end_date").val();
            var end_time = $("#end_time").val();
            var execution_date = $("#execution_date").val();
            var execution_status = $("#execution_status").val();
            var title = $("#title").val();
            var description = $("#description").val();
            var event_type = $("#event_type").val();
            var status = $("#status").val();
            var color = $("input[name='color']:checked").val();
            if (genba_date == "" || end_date == "" || title == "" ||
                description == "") {
                Toast.fire({
                    position: 'top-end',
                    title: "Please fill all field",
                    icon: "error"
                })
                svg.style.display = 'inline-block';
                spinner.style.display = 'none';
                buttonText.textContent = 'Submit';
                button.disabled = false;
                return false;
            } else if (color == undefined) {
                color = "fc-event-primary";
            }
            var data = {
                _token: $("[name=_token]").val(),
                trc_unix_id: sysID,
                event_type: event_type,
                genba_date: genba_date + " " + genba_time,
                end_date: end_date + " " + end_time,
                execution_date: execution_date,
                execution_status: execution_status,
                title: title,
                description: description,
                status: status,
                color: color,
            }
            var target = "#Team";
            $("html, body").animate({
                scrollTop: $(target).offset().top
            }, 800)

            $.ajax({
                type: "POST",
                url: "{{ route('schedule.createSchedule') }}",
                data: data,
                success: function(data) {
                    svg.style.display = 'inline-block';
                    spinner.style.display = 'none';
                    buttonText.textContent = 'Submit';
                    button.disabled = false;
                    if (data.code != 200) {
                        Toast.fire({
                            position: 'top-end',
                            title: data.status,
                            icon: "error"
                        })
                    }
                },
                error: function(jqXHR, textStatus) {
                    svg.style.display = 'inline-block';
                    spinner.style.display = 'none';
                    buttonText.textContent = 'Save';
                    button.disabled = false;
                }
            });

        }

        function submit_teams() {
            var button = document.getElementById('btn_submit_search');
            var svg = document.getElementById('svg_submit_search');
            var spinner = document.getElementById('spinner_submit_search');
            var buttonText = document.getElementById('btn_text_submit_search');
            svg.style.display = 'none';
            spinner.style.display = 'inline-block';
            buttonText.textContent = 'Please Wait...';
            button.disabled = true;
            var id = $("#id_teams").val();
            var team_name = $("#team_name").val();
            var team_role = $("#team_role").val();
            var data = {
                _token: $("[name=_token]").val(),
                trc_unix_id: id,
                team_name: team_name,
                team_role: team_role,
            }
            $.ajax({
                type: "POST",
                url: "{{ route('team.InsertTeam') }}",
                data: data,
                success: function(data) {
                    svg.style.display = 'inline-block';
                    spinner.style.display = 'none';
                    buttonText.textContent = 'Submit';
                    button.disabled = false;
                    if (data.code != 200) {
                        Toast.fire({
                            position: 'top-end',
                            title: data.status,
                            icon: "error"
                        })
                    }
                }
            });
        }

        function get_member_team() {
            var frontTable = $("#kt_doc_table").DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                deferLoading: 57,
                language: {
                    'processing': '<div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>'
                },
                info: false,
                order: [],
                columnDefs: [{
                    orderable: false,
                    targets: 0
                }],
                ajax: {
                    url: "{{ route('team.get_member_team') }}",
                    type: 'POST',
                    data: function(d) {
                        d._token = $("[name=_token]").val();
                    },
                    cache: false,
                    dataType: 'json',
                    error: function(xhr, error, thrown) {
                        console.log("DataTables Error:", xhr.responseText);
                    }
                },
                columns: [{
                        data: 'no',
                        className: 'text-center'
                    },
                    {
                        data: 'nik',
                    },
                    {
                        data: 'name'
                    }, {
                        data: 'action',
                        className: 'text-center',
                        orderable: false
                    },
                ],

            });


            setTimeout(function() {
                frontTable.ajax.reload();
            }, 500)

            return true;
        }

        function generate_team() {
            var button = document.getElementById('btn_generate_geam');
            var svg = document.getElementById('svg_generate_geam');
            var spinner = document.getElementById('spinner_generate_geam');
            var buttonText = document.getElementById('btn_text_generate_geam');
            svg.style.display = 'none';
            spinner.style.display = 'inline-block';
            buttonText.textContent = 'Please Wait...';
            button.disabled = true;
            var sysID = $("#sysID").val();


            var data = {
                _token: $("[name=_token]").val(),
                trc_unix_id: sysID,
                event_type: event_type,
                genba_date: genba_date + " " + genba_time,
                end_date: end_date + " " + end_time,
                execution_date: execution_date,
                execution_status: execution_status,
                title: title,
                description: description,
                status: status,
                color: color,
            }
            var target = "#Team";
            $("html, body").animate({
                scrollTop: $(target).offset().top
            }, 800)


        }
    </script>
