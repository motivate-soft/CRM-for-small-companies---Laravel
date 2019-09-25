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
                                        <h4 class="box-title">{{ trans('fields.company_event') }}</h4>
                                    </div>
                                    <div class="box-body">
                                        <!-- the events -->
                                        <div id="external-events">
                                            @if(count($holidays) > 0)
                                                <div type="holiday" class="external-event" style="color: white; background:red">{{ trans('fields.holidays') }}</div>
                                            @endif
                                            <div type="event" type_id="" class="external-event" style="color: white; background: green">{{ trans('fields.others') }}</div>
                                            {{--<div class="checkbox">--}}
                                            {{--<label for="drop-remove">--}}
                                            {{--<input type="checkbox" id="drop-remove">--}}
                                            {{--remove after drop--}}
                                            {{--</label>--}}
                                            {{--</div>--}}
                                        </div>
                                    </div>
                                    <!-- /.box-body -->
                                </div>
                                {{--<div class="box box-solid">--}}
                                {{--<div class="box-header with-border">--}}
                                {{--<h4 class="box-title">WorkingSpace Events</h4>--}}
                                {{--</div>--}}
                                {{--<div class="box-body">--}}
                                {{--<!-- the events -->--}}
                                {{--<div id="external-events">--}}

                                {{--@foreach($event_types as $type)--}}
                                {{--<div type="event" type_id="" class="external-event" style="color: white; background: green">{{ trans('fields.others') }}</div>--}}
                                {{--@endforeach--}}

                                {{--</div>--}}
                                {{--</div>--}}
                                {{--<!-- /.box-body -->--}}
                                {{--</div>--}}

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
                    <h4 class="modal-title event_title">Delete Event</h4>
                </div>
                <div class="modal-body">
                    <p class="event_comment">Do you want to delete this event?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary delete_object">Delete</button>
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
            init_events($('#external-events div.external-event'))
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
            var working_place_id = '{!! $working_place_id !!}';
            var workingplace_type = '{!! $workingplace_type !!}';
            $('#calendar').fullCalendar({
                locale: '{{ $language }}',
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                buttonText: {
                    today: 'today',
                    month: 'month',
                    week: 'week',
                    day: 'day'
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
                    {
                        id: 'e_{{ $event->id }}',
                        title: '{{ $event->name }}',
                        start: '{{ $event->start_date }}',
                        end: '{{ (new DateTime($event->end_date))->add(new DateInterval("P1D"))->format('Y-m-d') }}',
                        backgroundColor: 'green', //red
                        borderColor: 'green', //red
                        comment: '{{ $event->comment }}', //red
                        type: 'event',
                        allDay: true
                    },
                    @endforeach
                ],
                resizable: true,
                editable: true,
                droppable: true, // this allows things to be dropped onto the calendar !!!
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
                    console.log(info);
                    update_event_item(info);
                },
                eventResize: function(info) {
                    // console.log(info.end.toISOString());
                    // console.log(info);
                    update_event_item(info);
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
            function update_event_item(item) {
                var r = /\d+/;
                var e_start = item.start.toISOString();
                var e_end = item.end;
                if (e_end !== null) {
                    e_end = item.end.toISOString();
                }
                var e_name = item.title;
                var e_type = item.type;
                var e_id = item.id.match(r)[0];
                $.ajax({
                    url:  '{{ url('department_event/update_event') }}',
                    type: 'post',
                    data: {
                        id: e_id,
                        name: e_name,
                        type: e_type,
                        start_date: e_start,
                        end_date: e_end,
                    },
                    success: function (res) {
                    }
                })
            }
            $('.delete_object').on('click', function () {
                $.ajax({
                    url: '{{ url('department_event/delete_event') }}',
                    type: 'post',
                    data: {
                        id: event_id,
                        working_place_id: working_place_id,
                        type: type
                    },
                    success: function (res) {
                        $('#delete_event').modal('hide');
                        if (type === 'event') {
                            $('#calendar').fullCalendar('removeEvents', 'e_' + event_id)
                        } else if (type === 'holiday') {
                            $('#calendar').fullCalendar('removeEvents', 'h_' + event_id)
                        }
                    },
                    error: function () {
                        $('#delete_event').modal('hide');
                    }
                })
            });
            $('.save_object').on('click', function () {
                var name = $(this).parent().parent().find('input').val();
                if (name === '') { return }
                $(this).parent().parent().find('input').val('');
                $.ajax({
                    url: '{{ url('department_event/save_event') }}',
                    type: 'post',
                    data: {
                        name: name,
                        type: type,
                        type_id: type_id,
                        start_date: copied_object.start.toISOString(),
                        working_place_id: working_place_id,
                        workingplace_type: workingplace_type
                    },
                    success: function (res) {
                        copied_object.title = name;
                        if (type === 'event') {
                            copied_object.id = 'e_' + res;
                        } else if (type === 'holiday') {
                            copied_object.id = 'h_' + res;
                        }
                        $('#calendar').fullCalendar('renderEvent', copied_object, true);
                        if ($('#drop-remove').is(':checked')) {
                            $(this).remove()
                        }
                        $('#add_holiday').modal('hide');
                        $('#add_event').modal('hide');
                    } ,
                    error: function () {
                        $('#add_event').modal('hide');
                        $('#add_holiday').modal('hide');
                    }
                })
            });
            /* ADDING EVENTS */
            var currColor = '#3c8dbc' //Red by default
            //Color chooser button
            var colorChooser = $('#color-chooser-btn')
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