@extends('backpack::layout')

@section('header')
    <style>
        .fc-content {
            height: 20px;
        }
        .fc-title {
            font-weight: bold;
            font-size: 15px;
        }
        .fc-event-container {
            height: 100px;
        }
    </style>
    <section class="content-header">
        <h1>
            {{ trans('backpack::base.dashboard') }}<small>{{ trans('backpack::base.first_page_you_see') }}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ backpack_url() }}">{{ config('backpack.base.project_name') }}</a></li>
            <li class="active">{{ trans('backpack::base.dashboard') }}</li>
        </ol>
    </section>
@endsection


@section('content')
    @if(backpack_user()->role == \App\User::ROLE_ADMIN)
        <div class="row">
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3>{{ $total_companies }}</h3>
                        <p>Company Registrations</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3>{{ $recent_used_companies }}</h3>
                        <p>Recent Used Companies</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-red">
                    <div class="inner">
                        <h3>{{ $recent_used_employees }}</h3>
                        <p>Recent Used Employees</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <!-- New Companies LIST -->
                <div class="box box-danger">
                    <div class="box-header with-border">
                        <h3 class="box-title">New Companies</h3>

                        <div class="box-tools pull-right">
                            <span class="label label-danger">{{count($new_companies)}} New Companies</span>
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body no-padding">
                        <ul class="users-list clearfix">
                            @foreach($new_companies as $company)
                                <li>
                                    {{--@if($company->company->signatory)--}}
                                    {{--<img class="profile-user-img img-responsive img-circle" src="{{$company->company->signatory}}" alt="User Image">--}}
                                    {{--@else--}}
                                    <img class="profile-user-img img-responsive img-circle" src="{{$company->getAvatar()}}" alt="User Image">
                                    {{--@endif--}}
                                    <a class="users-list-name" href="#">{{$company->name}}</a>
                                    <span class="users-list-date">{{$company->created_at}}</span>
                                </li>
                            @endforeach
                        </ul>
                        <!-- /.Companies-list -->
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer text-center">
                        <a href="/companies" class="uppercase">{{trans('fields.view_all')}}</a>
                    </div>
                    <!-- /.box-footer -->
                </div>
                <!--/.box -->
            </div>
            <!-- /.col -->
            <div class="col-md-6">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Companies</h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table no-margin">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($companies as $company)
                                    <tr>
                                        <td>{{$company->name}}</td>
                                        <td><span class="label label-success">Trial</span></td>

                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- /.table-responsive -->
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>
        </div>
    @elseif(backpack_user()->role == \App\User::ROLE_COMPANY)
        <div class="row">
            <div class="col-md-12">
                <div class="box box-success">
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
        <div class="row">
            <div class="col-xs-4">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{trans('fields.last_in_working_employees')}}</h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>{{trans('fields.name')}}</th>
                                <th>{{trans('fields.photo')}}</th>
                                <th>{{trans('fields.last_check_in')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($last_employees as $index => $employee)
                                <tr>
                                    <td>{{$index+1}}</td>
                                    <td>{{$employee['name']}}</td>
                                    </td>
                                    <td>
                                        @if($employee['photo'])
                                            <img src="{{$employee['photo']}}" style="max-height: 25px; width:auto;border-radius: 3px;"><img>
                                        @endif
                                    </td>
                                    <td>{{$employee['last_check']}}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="box box-danger">
                    <div class="box-header">
                        <h3 class="box-title">{{trans('fields.not_working_employees')}}</h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>{{trans('fields.name')}}</th>
                                <th>{{trans('fields.photo')}}</th>
                                <th>{{trans('fields.last_check_in')}}</th>
                                <th>{{trans('fields.last_check_out')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($not_working_employees as $index => $employee)
                                <tr>
                                    <td>{{$index+1}}</td>
                                    <td>{{$employee['name']}}</td>
                                    <td>
                                        @if($employee['photo'])
                                            <img src="{{$employee['photo']}}" style="max-height: 25px; width:auto;border-radius: 3px;"><img>
                                        @endif
                                    </td>
                                    <td>{{$employee['last_check_in']}}</td>
                                    <td>{{$employee['last_check_out']}}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                    <div class="box-footer text-center">
                        <a href="{{backpack_url('none_working_employees')}}" class="uppercase">{{trans('fields.view_all')}}</a>
                    </div>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">{{ trans('fields.employees_on_holiday_or_others') }}</h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>{{trans('fields.name')}}</th>
                                <th>{{trans('fields.email')}}</th>
                                <th>{{trans('fields.photo')}}</th>
                                {{--<th>Last Login</th>--}}
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($employees_on_holiday as $index => $employee)
                                <tr>
                                    <td>{{$index+1}}</td>
                                    <td>{{$employee->name}}</td>
                                    <td>{{$employee->email}}
                                    </td>
                                    <td>
                                        @if($employee->employee->photo)
                                            <img src="{{$employee->employee->photo}}" style="max-height: 25px; width:auto;border-radius: 3px;"><img>
                                        @endif
                                    </td>
                                    {{--                                <td>{{$employee->last_login_at}}</td>--}}
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="delete_event" data-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title event_title">Event</h4>
                    </div>
                    <div class="modal-body">
                        <h5 class="text-green">{{trans('fields.employee')}}: </h5><p class="event_employee_name"></p>
                        <h5 class="text-green">{{trans('fields.comment')}}: </h5><p class="event_comment"></p>
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
    @elseif(backpack_user()->role == \App\User::ROLE_EMPLOYEE)
        <div class="row">
            <div class="col-md-12">
                <div class="box box-success">
                    <div class="box-header">
                        <h3 class="box-title">{{trans('fields.last_communication')}}</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover">
                            <tr>
                                <th>#</th>
                                <th>{{trans('fields.type')}}</th>
                                <th>{{trans('fields.date')}}</th>
                                <th>{{trans('fields.status')}}</th>
                                <th>{{trans('fields.comment')}}</th>
                                <th>{{trans('fields.date_range')}}</th>
                            </tr>
                            @foreach($lastCommunication as $item)
                                <tr>

                                    <td>{{$index++}}. </td>
                                    <td>{{trans('fields.'.$item->type)}}</td>
                                    <td>{{$item->updated_at}}</td>
                                    <td>
                                        @if(!$item->status)
                                            <span class="label">-</span>
                                        @else
                                            @if($item->status == 'approved')
                                                @if($item->cancel_state == 'approved')
                                                    <span class="label label-info">{{trans('fields.cancelled')}}</span>
                                                @else
                                                    <span class="label label-success">{{trans('fields.approved')}}</span>
                                                @endif
                                            @endif
                                            @if($item->status == 'pending')
                                                <span class="label label-warning">{{trans('fields.pending')}}</span>
                                            @endif
                                            @if($item->status == 'rejected')
                                                <span class="label label-danger">{{trans('fields.rejected')}}</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->comment)
                                            {{$item->comment}}
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->start_date)
                                            <p class="text-aqua" style="display: inline-block">{{$item->start_date->format('Y-m-d')}}</p> ~ <p class="text-green" style="display: inline-block">{{$item->end_date->format('Y-m-d')}}</p>
                                        @else
                                            {{$item->date}}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
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
    @endif

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
    @if(backpack_user()->role == \App\User::ROLE_EMPLOYEE)
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
                        // left: 'prev,next today',
                        left: '',
                        center: 'title',
                        right: 'month,agendaWeek,agendaDay',
                        // right: ''
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
                $('.fc-month-button').css('display', 'none');
                $('.fc-agendaDay-button').css('display', 'none');
                $('.fc-agendaWeek-button').css('display', 'none');
                $('.fc-agendaWeek-button').trigger('click');

                $('.fc-scroller').css('display', 'none');
            })

        </script>
    @endif
    @if(backpack_user()->role == \App\User::ROLE_COMPANY)
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
                {{--                var working_place_id = '{!! $working_place_id !!}';--}}
                {{--                var workingplace_type = '{!! $workingplace_type !!}';--}}
                $('#calendar').fullCalendar({
                    locale: '{{ $language }}',
                    header: {
                        left: '',
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
                            @foreach($employee_holidays as $event)
                            @if($event->event_type->has_to_appear == true)
                        {
                            id: 'e_{{ $event->id }}',
                            title: '{{$event->employee->user->name}}',
                            start: '{{ $event->start_date }}',
                            end: '{{ (new DateTime($event->end_date))->add(new DateInterval("P1D"))->format('Y-m-d') }}',
                            {{--end: '{{ $event->end_date }}',--}}
                            backgroundColor: '{{ $event->event_type->color }}',
                            borderColor: '{{ $event->event_type->color }}',
                            type: 'event',
                            employee_name: '{{$event->employee->user->name}}',
                            comment: '{{ $event->comment }}', //red
                            allDay: true
                        },
                            @endif
                            @endforeach
                            @foreach($employee_absense as $event)
                            @if($event->event_type->has_to_appear == true)
                        {
                            id: 'e_{{ $event->id }}',
                            title: '{{$event->employee->user->name}}',
                            start: '{{ $event->start_date }}',
                            {{--end: '{{ $event->end_date }}',--}}
                            end: '{{ (new DateTime($event->end_date))->add(new DateInterval("P1D"))->format('Y-m-d') }}',
                            backgroundColor: '{{ $event->event_type->color }}',
                            borderColor: '{{ $event->event_type->color }}',
                            type: 'event',
                            employee_name: '{{$event->employee->user->name}}',
                            comment: '{{ $event->comment }}', //red
                            allDay: true
                        },
                            @endif
                            @endforeach
                            @foreach($employee_medical as $medical)
                            @if($medical->event_type->has_to_appear == true)
                        {
                            id: 'e_{{ $medical->id }}',
                            title: '{{$event->employee->user->name}}',
                            start: '{{ $medical->date }}',
                            {{--end: '{{ date('Y-m-d', strtotime($medical->date .' +1 day')) }}',--}}
                            backgroundColor: '{{ $medical->event_type->color }}',
                            borderColor: '{{ $medical->event_type->color }}',
                            type: 'event',
                            employee_name: '{{$event->employee->user->name}}',
                            comment: '{{ $medical->comment }}', //red
                            allDay: true
                        },
                            @endif
                            @endforeach
                            @foreach($employee_expense as $event)
                            @if($event->event_type->has_to_appear == true)
                        {
                            id: 'e_{{ $event->id }}',
                            title: '{{$event->employee->user->name}}',
                            start: '{{ $event->date }}',
                            {{--                                end: '{{ \Carbon\Carbon::createFromFormat('Y-m-d', $event->end_date)->addDay(1) }}',--}}
                                    {{--end: '{{ date('Y-m-d', strtotime($event->date .' +1 day')) }}',--}}
                            backgroundColor: '{{ $event->event_type->color }}',
                            borderColor: '{{ $event->event_type->color }}',
                            type: 'event',
                            employee_name: '{{$event->employee->user->name}}',
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
                            title: '{{$event->employee->user->name}}',
                            start: '{{ $event->start_date }}',
                            {{--end: '{{ $event->end_date }}',--}}
                            end: '{{ (new DateTime($event->end_date))->add(new DateInterval("P1D"))->format('Y-m-d') }}',
                            backgroundColor: '{{ $event->event_type->color }}',
                            borderColor: '{{ $event->event_type->color }}',
                            type: 'event',
                            employee_name: '{{$event->employee->user->name}}',
                            comment: '{{ $event->comment }}', //red
                            allDay: true
                        },
                        @endif
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
                    console.log(item);
                    var r = /\d+/;
                    event_id = item.id.match(r)[0];
                    type = item.type;
                    if (type === 'event') {
                        type_id = $(item.allDay.target).attr('type_id');
                        $('.event_title').html(item.title);
                        $('.event_employee_name').html(item.employee_name);
                        $('.event_comment').html(item.comment);
                    } else {
                        $('.event_title').html(item.title);
                        $('.event_employee_name').html(item.employee_name);
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
                $('.fc-month-button').css('display', 'none');
                $('.fc-agendaDay-button').css('display', 'none');
                $('.fc-agendaWeek-button').css('display', 'none');
                $('.fc-agendaWeek-button').trigger('click');

                $('.fc-scroller').css('display', 'none');
            })
        </script>
    @endif
@endsection
