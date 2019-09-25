@extends('backpack::layout')

@section('header')
    <section class="content-header">
        <h1>{{ $title }}</h1>
        <ol class="breadcrumb">
            <li><a href="{{ backpack_url() }}">{{ config('backpack.base.project_name') }}</a></li>
            <li class="active">{{ $title }}</li>
        </ol>
    </section>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <section class="content">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="box box-solid">
                                    <div class="box-header with-border">
                                        <h4 class="box-title">{{ trans('fields.company_event') }}s</h4>
                                    </div>
                                    <div class="box-body">
                                        <!-- the events -->
                                        <div id="external-events">
                                            {{--@if(count($holidays) > 0)--}}
                                            <div type="holiday" class="external-event" style="color: white; background:red">{{ trans('fields.national_holiday') }}</div>
                                            <div type="holiday" class="external-event" style="color: white; background:green">{{ trans('fields.company_event') }}</div>
                                            {{--@endif--}}
                                        </div>
                                    </div>
                                    <!-- /.box-body -->
                                </div>
                                <div class="box box-solid">
                                    <div class="box-header with-border">
                                        {{--<h4 class="box-title">{{trans('fields.employee_events')}}</h4>--}}
                                    </div>
                                    <div class="box-body">
                                        <!-- the events -->
                                        <div id="external-events">
                                            @foreach($event_mandatory_types as $type)
                                                @if($type->has_to_appear)
                                                    <div type="event" type_id="{{ $type->id }}" class="external-event" style="color: white; background: {{ $type->color }}">{{ trans('fields.'.$type->type) }}</div>
                                                @endif
                                            @endforeach
                                            <hr>
                                            @foreach($event_types as $type)
                                                @if($type->has_to_appear)
                                                    <div type="event" type_id="{{ $type->id }}" class="external-event" style="color: white; background: {{ $type->color }}">{{ $type->name }}</div>
                                                @endif
                                            @endforeach

                                        </div>
                                    </div>
                                    <!-- /.box-body -->
                                </div>

                                <div class="box box-solid">
                                    <div class="box-header with-border">
                                        {{--<h4 class="box-title">{{trans('fields.employee_events')}}</h4>--}}
                                    </div>
                                    <div class="box-body">
                                        <!-- the events -->
                                        <div id="external-events">

                                        </div>
                                    </div>
                                    <!-- /.box-body -->
                                </div>

                            </div>
                            <!-- /.col -->
                            <div class="col-md-9">
                                <div class="box box-primary">
                                    <div class="box-body no-padding">
                                        <!-- THE CALENDAR -->
                                        <div id="calendar"></div>
                                    </div>
                                    <!-- /.box-body -->
                                </div>
                                <!-- /. box -->
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->
                    </section>

                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="add_holiday" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Holiday</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input class="form-control" placeholder="Holiday Name">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary save_object">Save</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <div class="modal fade" id="add_event" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Event</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        {{--<label class="control-label">Name</label>--}}
                        <input class="form-control" placeholder="Event Name">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary save_object">Save</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>


    <div class="modal fade" id="delete_event" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title event_title">Event</h4>
                </div>
                <div class="modal-body">
                    <p class="event_comment"></p>
                </div>
                <div class="modal-footer">
                    {{--<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>--}}
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@endsection
@section('after_styles')
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/bower_components/fullcalendar/dist/fullcalendar.min.css') }}">
    <!-- <link rel="stylesheet" href="{{ asset('vendor/backpack/bower_components/fullcalendar/dist/fullcalendar.print.min.css') }}" media="print"> -->
@endsection
@section('after_scripts')
    <script src="{{ asset('vendor/adminlte/bower_components/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/bower_components/moment/moment.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/bower_components/fullcalendar/dist/fullcalendar.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/bower_components/fullcalendar/dist/locale-all.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/bower_components/fullcalendar/dist/gcal.js') }}"></script>

    <script>
        $(function() {
            /* initialize the external events
            -----------------------------------------------------------------*/
            function init_events(ele) {
                ele.each(function() {
                    // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
                    // it doesn't need to have a start or end
                    var eventObject = {
                        title: $.trim($(this).text()) // use the element's text as the event title
                    };
                    // store the Event Object in the DOM element so we can get to it later
                    $(this).data('eventObject', eventObject)
                    // make the event draggable using jQuery UI
                    $(this).draggable({
                        zIndex: 1070,
                        revert: true, // will cause the event to go back to its
                        revertDuration: 0 //  original position after the drag
                    })
                })
            }
            init_events($('#external-events div.external-event'));
            /* initialize the calendar
            -----------------------------------------------------------------*/
            //Date for the calendar events (dummy data)
            var date = new Date();
            var d = date.getDate(),
                m = date.getMonth(),
                y = date.getFullYear();
            var event_id;
            var copied_object;
            var type;
            var type_id;
            {{--var working_place_id = '{!! $working_place_id !!}';--}}
            $('#calendar').fullCalendar({
                locale: '{{ $language }}',
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                buttonText: {
                    today: "{{trans('fields.today')}}",
                    month: "{{trans('fields.month')}}",
                    week: "{{trans('fields.week')}}",
                    day: "{{trans('fields.day')}}",
                },
                //Random default events
                events: [
                        @foreach($holidays as $holiday)
                    {
                        id: 'h_{{ $holiday->id }}',
                        title: '{{ $holiday->name }}',
                        start: '{{ $holiday->start_date }}',
                        end: '{{ (new DateTime($holiday->end_date))->add(new DateInterval("P1D"))->format('Y-m-d') }}',
                        backgroundColor: 'red', //red
                        borderColor: 'red', //red
                        type: 'holiday',
                        comment: '',
                        allDay: true
                    },
                        @endforeach
                        @foreach($events as $event)
                        {{--@if($event->event_type->has_to_appear == true)--}}
                    {
                        id: 'e_{{ $event->id }}',
                        title: '{{ $event->name }}',
                        start: '{{ $event->start_date }}',
                        end: '{{ (new DateTime($event->end_date))->add(new DateInterval("P1D"))->format('Y-m-d') }}',
                        {{--end: '{{ $event->end_date }}',--}}
                        backgroundColor: 'green',
                        borderColor: 'green',
                        type: 'event',
                        comment: '{{ $event->comment }}', //red
                        allDay: true
                    },
                        {{--@endif--}}
                        @endforeach
                        @foreach($employee_holidays as $event)
                        @if($event->event_type->has_to_appear == true)
                    {
                        id: 'e_{{ $event->id }}',
                        title: '{{ trans('fields.holiday') }}',
                        start: '{{ $event->start_date }}',
                        end: '{{ (new DateTime($event->end_date))->add(new DateInterval("P1D"))->format('Y-m-d') }}',
                        {{--end: '{{ $event->end_date }}',--}}
                        backgroundColor: '{{ $event->event_type->color }}',
                        borderColor: '{{ $event->event_type->color }}',
                        type: 'event',
                        comment: '{{ $event->comment }}', //red
                        allDay: true
                    },
                        @endif
                        @endforeach
                        @foreach($employee_absense as $event)
                        @if($event->event_type->has_to_appear == true)
                    {
                        id: 'e_{{ $event->id }}',
                        title: '{{ trans('fields.absences') }}',
                        start: '{{ $event->start_date }}',
                        {{--end: '{{ $event->end_date }}',--}}
                        end: '{{ (new DateTime($event->end_date))->add(new DateInterval("P1D"))->format('Y-m-d') }}',
                        backgroundColor: '{{ $event->event_type->color }}',
                        borderColor: '{{ $event->event_type->color }}',
                        type: 'event',
                        comment: '{{ $event->comment }}', //red
                        allDay: true
                    },
                        @endif
                        @endforeach
                        @foreach($employee_medical as $medical)
                        @if($medical->event_type->has_to_appear == true)
                    {
                        id: 'e_{{ $medical->id }}',
                        title: '{{ trans('fields.medical_day') }}',
                        start: '{{ $medical->date }}',
                        {{--end: '{{ date('Y-m-d', strtotime($medical->date .' +1 day')) }}',--}}
                        backgroundColor: '{{ $medical->event_type->color }}',
                        borderColor: '{{ $medical->event_type->color }}',
                        type: 'event',
                        comment: '{{ $medical->comment }}', //red
                        allDay: true
                    },
                        @endif
                        @endforeach
                        @foreach($employee_expense as $event)
                        @if($event->event_type->has_to_appear == true)
                    {
                        id: 'e_{{ $event->id }}',
                        title: '{{ trans('fields.expense') }}',
                        start: '{{ $event->date }}',
                        {{--                                end: '{{ \Carbon\Carbon::createFromFormat('Y-m-d', $event->end_date)->addDay(1) }}',--}}
                                {{--end: '{{ date('Y-m-d', strtotime($event->date .' +1 day')) }}',--}}
                        backgroundColor: '{{ $event->event_type->color }}',
                        borderColor: '{{ $event->event_type->color }}',
                        type: 'event',
                        comment: '{{ $event->comment }}', //red
                        allDay: true
                    },
                        @endif
                        @endforeach
                        @foreach($employee_others as $event)
                        @if($event->event_type->has_to_appear == true)
                    {
                        id: 'e_{{ $event->id }}',
                        {{--title: '{{ trans('fields.absences') }}',--}}
                        title: '{{ $event->name }}',
                        start: '{{ $event->start_date }}',
                        {{--end: '{{ $event->end_date }}',--}}
                        end: '{{ (new DateTime($event->end_date))->add(new DateInterval("P1D"))->format('Y-m-d') }}',
                        backgroundColor: '{{ $event->event_type->color }}',
                        borderColor: '{{ $event->event_type->color }}',
                        type: 'event',
                        comment: '{{ $event->comment }}', //red
                        allDay: true
                    },
                    @endif
                    @endforeach
                ],
                resizable: false,
                editable: false,
                droppable: false, // this allows things to be dropped onto the calendar !!!
                drop: function(date, allDay) {
                    var originalEventObject = $(this).data('eventObject');
                    // we need to copy it, so that multiple events don't have a reference to the same object
                    var copiedEventObject = $.extend({}, originalEventObject);
                    // assign it the date that was reported
                    copiedEventObject.start = date;
                    copiedEventObject.allDay = allDay;
                    copiedEventObject.backgroundColor = $(this).css('background-color');
                    copiedEventObject.borderColor = $(this).css('border-color');
                    type = $(copiedEventObject.allDay.target).attr('type');
                    if (type === 'event') {
                        type_id = $(copiedEventObject.allDay.target).attr('type_id');
                        $('#add_event').modal('show');
                    } else {
                        $('#add_holiday').modal('show');
                    }
                    copied_object = copiedEventObject;
                    copied_object.type = type;
                },
                eventDrop: function(info) {
                    update_event_item(info);
                },
                eventResize: function(info) {
                    // console.log(info.end.toISOString());
                    // console.log(info);
                },
                eventClick: function(calEvent, jsEvent, view) {
                    dispDetail(calEvent)
                }
            });
            function dispDetail(item) {
                var r = /\d+/;
                event_id = item.id.match(r)[0];
                type = item.type;
                if (type === 'event') {
                    type_id = $(item.allDay.target).attr('type_id');
                    $('.event_title').html(item.title);
                    $('.event_comment').html(item.comment);
                } else {
                    $('.event_title').html(item.title);
                    $('.event_comment').html('');
                }
                $('#delete_event').modal('show');
            }
            /* ADDING EVENTS */
            var currColor = '#3c8dbc' //Red by default
            //Color chooser button
            var colorChooser = $('#color-chooser-btn');
            $('#color-chooser > li > a').click(function(e) {
                e.preventDefault()
                //Save color
                currColor = $(this).css('color')
                //Add color effect to button
                $('#add-new-event').css({
                    'background-color': currColor,
                    'border-color': currColor
                })
            });
            $('#add-new-event').click(function(e) {
                console.log('rrrr');
                e.preventDefault()
                //Get value and make sure it is not null
                var val = $('#new-event').val()
                if (val.length == 0) {
                    return
                }
                //Create events
                var event = $('<div />')
                event.css({
                    'background-color': currColor,
                    'border-color': currColor,
                    'color': '#fff'
                }).addClass('external-event')
                event.html(val)
                $('#external-events').prepend(event)
                //Add draggable funtionality
                init_events(event)
                //Remove event from text input
                $('#new-event').val('')
            })
        })
    </script>

@endsection