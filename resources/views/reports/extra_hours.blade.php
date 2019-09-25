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

    @include('reports.filters.filter4')

    <div class="row">
        <div class="col-md-12">
            <div class="box box-solid">
                <div class="box-body">
                    <div class="box-group" id="accordion">
                        @foreach($table as $employee_id => $items)
                            <div class="panel box box-primary">
                                <div class="box-header with-border">
                                    <h4 class="box-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#item-{{ $employee_id }}" aria-expanded="false" class="collapsed">
                                            {{ \App\Models\Employee::find($employee_id)->user->name }}
                                        </a>
                                        {{--<time class="pull-right">--}}
                                            {{--<i class="fa fa-clock-o"></i>--}}
                                            {{--@php--}}
                                                {{--$seconds = 0;--}}
                                                {{--foreach ($items as $item){--}}
                                                    {{--for($i = 0; $i < $item->count(); $i++){--}}
                                                        {{--if(isset($item[$i + 1])) {--}}
                                                            {{--$seconds += (int)\Carbon\Carbon::parse($item[$i]->datetime)->diffInSeconds(\Carbon\Carbon::parse($item[++$i]->datetime));--}}
                                                        {{--}--}}
                                                    {{--}--}}
                                                {{--}--}}
                                                {{--echo \Carbon\Carbon::now()->diff(\Carbon\Carbon::now()->addSeconds($seconds))->format('%H:%I:%S');--}}
                                            {{--@endphp--}}
                                        {{--</time>--}}
                                    </h4>
                                </div>
                                <div id="item-{{ $employee_id }}" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                    <div class="box-body">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>{{ trans('fields.day') }}</th>
                                                    <th>{{ trans('fields.employee_in') }}</th>
                                                    <th>{{ trans('fields.employee_out') }}</th>
                                                    <th>{{ trans('fields.total_pauses') }}</th>
                                                    <th>{{ trans('fields.schedule_working_time') }}</th>
                                                    <th>{{ trans('fields.actual_working_time') }}</th>
                                                    <th>{{ trans('fields.total_extra_work') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @for($day = 1; $day <= Carbon\Carbon::createFromFormat('d/m/Y', '01/' . $filters['date'])->daysInMonth; $day++)
                                                <tr>
                                                    <td>{{ \App\Models\Action::getDaysOfWeek(Carbon\Carbon::createFromFormat('d/m/Y', $day . '/' . $filters['date'])->format('N')) }} {{ Carbon\Carbon::createFromFormat('d/m/Y', $day . '/' . $filters['date'])->format('d') }}</td>
                                                    <td>
                                                        @php
                                                            if(isset($items[$day])) {
                                                                foreach ($items[$day] as $time) {
                                                                    if($time->option->type == 'in') {
                                                                        echo \Carbon\Carbon::parse($time->datetime)->format('H:i:s'); break;
                                                                    }
                                                                }
                                                            }
                                                        @endphp
                                                    </td>
                                                    <td>
                                                        @php
                                                            if(isset($items[$day])) {
                                                                foreach ($items[$day]->reverse() as $time) {
                                                                    if($time->option->type == 'out') {
                                                                        echo \Carbon\Carbon::parse($time->datetime)->format('H:i:s'); break;
                                                                    }
                                                                }
                                                            }
                                                        @endphp
                                                    </td>
                                                    <td>
                                                        @php
                                                            if(isset($items[$day])) {
                                                                echo \App\Models\Action::getTotalPausesByEmployeeActions($items[$day]);
                                                            }
                                                        @endphp
                                                    </td>
                                                    <td>
                                                        @php
                                                            if(isset($items[$day])) {
                                                                $day_of_week = Carbon\Carbon::createFromFormat('d/m/Y', $day . '/' . $filters['date'])->dayOfWeekIso;
                                                                if(isset($working_times[$day_of_week])) {
                                                                    $schedule_time = \Carbon\Carbon::parse($working_times[$day_of_week])->format('H:i:s');
                                                                    echo $schedule_time;
                                                                }
                                                            }
                                                        @endphp
                                                    </td>
                                                    <td>
                                                        @php
                                                            if(isset($items[$day])) {
                                                                $seconds = 0;
                                                                for($i = 0; $i < $items[$day]->count(); $i++){
                                                                    if(isset($items[$day][$i + 1])) {
                                                                        $seconds += (int)\Carbon\Carbon::parse($items[$day][$i]->datetime)->diffInSeconds(\Carbon\Carbon::parse($items[$day][++$i]->datetime));
                                                                    }
                                                                }
                                                                $actual_time = \Carbon\Carbon::now()->diff(\Carbon\Carbon::now()->addSeconds($seconds))->format('%H:%I:%S');
                                                                echo $actual_time;
                                                            }
                                                        @endphp
                                                    </td>
                                                    <td>
                                                        @php
                                                            if(isset($items[$day])) {
                                                                $actual_time = \Carbon\Carbon::parse($actual_time);
                                                                $schedule_time = \Carbon\Carbon::parse($schedule_time);

                                                                if($actual_time->gt($schedule_time)) {
                                                                    $seconds = $schedule_time->diffInSeconds($actual_time);
                                                                    echo \Carbon\Carbon::now()->diff(\Carbon\Carbon::now()->addSeconds($seconds))->format('%H:%I:%S');
                                                                }
                                                            }
                                                        @endphp
                                                    </td>
                                                </tr>
                                            @endfor
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('reports.reports_export_buttons')