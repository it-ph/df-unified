@extends('layouts.master')

@section('title')
    Event Calendar
@endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/daterangepicker/daterangepicker.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/calendar/dist/fullcalendar.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/custom-calendar.css') }}" id="app-style" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @component('components.breadcrumb_w_button')
        @slot('li_1')
            Manage / Event Calendar
        @endslot
        @slot('title')
            Event Calendar
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-md-12">
            @include('notifications.success')
            @include('notifications.error')
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="col-md-3 mb-2">
                        <input type="hidden" class="form-control" name="daterange" id="daterange">
                    </div>
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>

    @include('pages.admin.events.event-modal')
@endsection

@section('script')
    <!-- Required datatable js -->
    <script src="{{ asset('assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/libs/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/libs/daterangepicker/moment.min.js') }}"></script>
    <script src="{{ asset('assets/libs/daterangepicker/moment.min.js') }}"></script>
    <script src="{{ asset('assets/libs/daterangepicker/daterangepicker.min.js') }}"></script>
    <script src="{{ asset('assets/libs/calendar/dist/fullcalendar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/select2.js') }}"></script>
</head>
@endsection

@section('custom-js')
    <script src="{{asset('scripts/events.js')}}"></script>
    <script>
        !function($) {
        "use strict";

        var CalendarApp = function() {
            this.$body = $("body")
            this.$calendar = $('#calendar'),
            this.$calendarObj = null
        };

        /* Initializing */
        CalendarApp.prototype.init = function() {
            var defaultEvents =  [
                // events
                @foreach($events as $event)
                {
                    id: "{{ $event->id }}",
                    client_id: "{{ $event->client_id }}",
                    title: "{{ $event->title }}",
                    description: "{{ $event->description }}",
                    event_type: "{{ $event->event_type }}",
                    start: "{{ $event->start }}",
                    end: "{{ $event->end }}",
                    color: "{{ $event->color }}",
                },
                @endforeach
            ];

            var $this = this;
            $this.$calendarObj = $this.$calendar.fullCalendar({
                // slotDuration: '00:15:00', /* If we want to split day time each 15minutes */
                // minTime: '08:00:00',
                // maxTime: '19:00:00',
                defaultView: 'month',
                contentHeight: 'auto',
                handleWindowResize: true,

                buttonText: {
                    day: 'Day',
                    week: 'Week',
                },

                header: {
                    left: 'prev,next today',
                    // left: 'none',
                    center: 'title',
                    // center: 'title',
                    right: 'month,listWeek,listDay,listMonth'
                    // right: 'prev,next today'
                },
                events: defaultEvents,
                editable: false,
                droppable: false, // this allows things to be dropped onto the calendar !!!
                eventLimit: true, // allow "more" link when too many events
                views: {
                    month:
                    {
                        eventLimit: 4
                    }
                },
                selectable: true,
                selectHelper: true,
                select: function (start,end) {
                    resetForm();
                    $('#eventModal').modal('show');
                    $('#eventModalTitle').text('Create New Event');
                    $('#btn_delete').hide();

                    // set minDate to current date
                    // var s_date = start.format('YYYY-MM-DD');
                    // var c_time = moment().format('HH:mm');
                    // var new_event = moment(s_date + ' ' + c_time);

                    eventDate(start, end);
                    $('#datefilter').daterangepicker({
                        buttonClasses: ['btn', 'btn-sm'],
                        applyClass: 'btn-primary',
                        cancelClass: 'btn-danger',
                        timePicker: true,
                        startDate: start,
                        endDate: end,
                        minDate: moment(),
                    }, eventDate);

                    $('#datefilter').on('apply.daterangepicker', function(ev, picker) {
                        check_availability();
                    });
                },
                eventClick:  function(event)
                {
                    resetForm();
                    $('#eventModal').modal('show');
                    $('#eventModalTitle').text('Update Event');
                    $('#btn_delete').show();
                    $("#edit_id").val(event.id);
                    $("#client_id").val(event.client_id).trigger("change");
                    $("#title").val(event.title);
                    $("#description").val(event.description);
                    $("#event_type").val(event.event_type);

                    eventDate(event.start, event.end);
                    $('#datefilter').daterangepicker({
                        buttonClasses: ['btn', 'btn-sm'],
                        applyClass: 'btn-primary',
                        cancelClass: 'btn-danger',
                        timePicker: true,
                        startDate: event.start,
                        endDate: event.end,
                        minDate: moment(),
                    }, eventDate);
                    $("#color").val(event.color);

                    // removed to allow user to adjust range withouth checking conflicts
                    // $('#datefilter').on('apply.daterangepicker', function(ev, picker) {
                    //     check_availability();
                    // });
                },
                selectAllow: function(selectInfo) {
                    return (selectInfo.start >= getDateWithoutTime(new Date()));
                }
            });
        },

        //init CalendarApp
        $.CalendarApp = new CalendarApp, $.CalendarApp.Constructor = CalendarApp

        }(window.jQuery),

        //initializing CalendarApp
        function($) {
            "use strict";
            $('#calendar').hide();
            $('#calendar').fadeIn(1000);
            $.CalendarApp.init()
        }(window.jQuery);

        //get date without the time of day
        function getDateWithoutTime(dt) {
            dt.setHours(0,0,0,0);
            return dt;
        }

        function eventDate(start, end) {
            $('#datefilter span').html(start.format('MMM D, YYYY hh:mm A') + ' - ' + end.format('MMM D, YYYY hh:mm A'));
            $('#date_range').val(start.format('MMM D, YYYY hh:mm A') + ' - ' + end.format('MMM D, YYYY hh:mm A'));
        }

        function check_availability() {
            $('#btn_save').prop("disabled", true);
            $('#datefilterError').hide();
            $('#datefilterError').text('');
            var event_date = $('#date_range').val();
            var client_id = $('#client_id').val();

            // limit the user to select same datetime
            date_range = event_date.split(" - ");
            if(date_range[0] == date_range[1])
            {
                $('#btn_save').prop("disabled", true);
                $('#datefilterError').show();
                $('#datefilterError').text('DATE FROM must be greater than and not equal to DATE TO.');
            }
            else
            {
                $.ajax({
                    type: "GET",
                    url: "{{ url('event/isconflict') }}",
                    data: {
                        date_range: event_date,
                        client_id: client_id,
                    },
                    dataType: "json",
                    success: function (response) {
                        if(response.is_conflict == 1)
                        {
                            $('#btn_save').prop("disabled", true);
                            $('#datefilterError').show();
                            $('#datefilterError').text('Event Date has conflict');
                        }
                        else
                        {
                            $('#btn_save').prop("disabled", false);
                            $('#datefilterError').hide();
                            $('#datefilterError').text('');
                        }
                    }
                });
            }
        }
    </script>
@endsection
