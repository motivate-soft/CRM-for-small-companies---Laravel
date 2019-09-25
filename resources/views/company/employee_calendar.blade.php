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
                                        <h4 class="box-title">National Holiday</h4>
                                    </div>
                                    <div class="box-body">
                                        <!-- the events -->
                                        <div id="external-events">
                                            @if(count($holidays) > 0)
                                                <div type="holiday" class="external-event" style="color: white; background:red">Holiday</div>
                                            @endif

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
                                <div class="box box-solid">
                                    <div class="box-header with-border">
                                        <h4 class="box-title">WorkingSpace Events</h4>
                                    </div>
                                    <div class="box-body">
                                        <!-- the events -->
                                        <div id="external-events">

                                            @foreach($event_types as $type)
                                                <div type="event" type_id="{{ $type->id }}" class="external-event" style="color: white; background: {{ $type->color }}">{{ $type->name }}</div>
                                            @endforeach
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
                                <!-- /. box -->
                                {{--<div class="box box-solid">--}}
                                {{--<div class="box-header with-border">--}}
                                {{--<h3 class="box-title">Create Event</h3>--}}
                                {{--</div>--}}
                                {{--<div class="box-body">--}}
                                {{--<div class="btn-group" style="width: 100%; margin-bottom: 10px;">--}}
                                {{--<!--<button type="button" id="color-chooser-btn" class="btn btn-info btn-block dropdown-toggle" data-toggle="dropdown">Color <span class="caret"></span></button>-->--}}
                                {{--<ul class="fc-color-picker" id="color-chooser">--}}
                                {{--<li><a class="text-aqua" href="#"><i class="fa fa-square"></i></a></li>--}}
                                {{--<li><a class="text-blue" href="#"><i class="fa fa-square"></i></a></li>--}}
                                {{--<li><a class="text-light-blue" href="#"><i class="fa fa-square"></i></a>--}}
                                {{--</li>--}}
                                {{--<li><a class="text-teal" href="#"><i class="fa fa-square"></i></a></li>--}}
                                {{--<li><a class="text-yellow" href="#"><i class="fa fa-square"></i></a></li>--}}
                                {{--<li><a class="text-orange" href="#"><i class="fa fa-square"></i></a></li>--}}
                                {{--<li><a class="text-green" href="#"><i class="fa fa-square"></i></a></li>--}}
                                {{--<li><a class="text-lime" href="#"><i class="fa fa-square"></i></a></li>--}}
                                {{--<li><a class="text-red" href="#"><i class="fa fa-square"></i></a></li>--}}
                                {{--<li><a class="text-purple" href="#"><i class="fa fa-square"></i></a></li>--}}
                                {{--<li><a class="text-fuchsia" href="#"><i class="fa fa-square"></i></a></li>--}}
                                {{--<li><a class="text-muted" href="#"><i class="fa fa-square"></i></a></li>--}}
                                {{--<li><a class="text-navy" href="#"><i class="fa fa-square"></i></a></li>--}}
                                {{--</ul>--}}
                                {{--</div>--}}
                                {{--<!-- /btn-group -->--}}
                                {{--<div class="input-group">--}}
                                {{--<input id="new-event" type="text" class="form-control"--}}
                                {{--placeholder="Event Title">--}}

                                {{--<div class="input-group-btn">--}}
                                {{--<button id="add-new-event" type="button"--}}
                                {{--class="btn btn-primary btn-flat">Add</button>--}}
                                {{--</div>--}}
                                {{--<!-- /btn-group -->--}}
                                {{--</div>--}}
                                {{--<!-- /input-group -->--}}
                                {{--</div>--}}
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
            {{--var working_place_id = '{!! $working_place_id !!}';--}}
            $('#calendar').fullCalendar({
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
                        id: 'h_{{ $holiday[0]->id }}',
                        title: '{{ $holiday[0]->name }}',
                        start: '{{ $holiday[0]->start_date }}',
                        end: '{{ $holiday[0]->end_date }}',
                        backgroundColor: 'red', //red
                        borderColor: 'red', //red
                        type: 'holiday',
                        comment: '',
                        allDay: true
                    },
                        @endforeach

                        @foreach($events as $event)
                    {
                        id: 'e_{{ $event[0]->id }}',
                        title: '{{ $event[0]->name }}',
                        start: '{{ $event[0]->start_date }}',
                        end: '{{ $event[0]->end_date }}',
                        backgroundColor: '{{ $event[0]->event_type->color }}', //red
                        borderColor: '{{ $event[0]->event_type->color }}', //red
                        type: 'event',
                        comment: '{{ $event[0]->comment }}', //red
                        allDay: true
                    },
                    @endforeach
                ],
                resizable: true,
                editable: true,
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
