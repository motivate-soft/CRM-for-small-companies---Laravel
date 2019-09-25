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

    @include('reports.filters.filter1')

    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{ trans('fields.employee') }}</th>
                                <th>{{ trans('fields.date_and_weekday') }}</th>
                                <th>{{ trans('fields.schedule_interval') }}</th>
                                <th>{{ trans('fields.clocking_type') }}</th>
                                <th>{{ trans('fields.partial_hours') }}</th>
                                <th>{{ trans('fields.total_pauses') }}</th>
                                <th>{{ trans('fields.daily_total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $index = 1;
                            @endphp
                            @foreach($table as $employee_id => $date)
                                @foreach($date as $datetime => $actions)
                                    @php
                                        $actions = $actions->reverse()->values();
                                    @endphp
                                    <tr>
                                        <td><b>{{ $loop->first ? \App\Models\Employee::find($employee_id)->user->name : '' }}</b></td>
                                        <td>{{ $datetime }}<br>{{ \App\Models\Action::getDaysOfWeek(\Carbon\Carbon::parse($datetime)->format('N')) }}</td>
                                        <td>
                                            @for($i = 0; $i < $actions->count(); $i++)
                                                @if($actions[$i]->option->type == 'in' && isset($actions[$i + 1]))
                                                    {{ \Carbon\Carbon::parse($actions[$i]->datetime)->toTimeString() }} >> {{ \Carbon\Carbon::parse($actions[++$i]->datetime)->toTimeString() }} <br>
                                                @endif
                                            @endfor
                                        </td>
                                        <td>
                                            @for($i = 0; $i < $actions->count(); $i++)
                                                @if($actions[$i]->option->type == 'in' && isset($actions[$i + 1]))
                                                    {{ $actions[$i]->option->type }} {{ $actions[$i]->option ? '(' . $actions[$i]->option->name . ')' : '' }} >> {{ $actions[++$i]->option->type }} {{ $actions[$i]->option ? '(' . $actions[$i]->option->name . ')' : '' }} <br>
                                                @endif
                                            @endfor
                                        </td>
                                        <td>
                                            {!! \App\Models\Action::getPartialHoursByEmployeeActions($actions) !!}
                                        </td>
                                        <td>
                                            {{ \App\Models\Action::getTotalPausesByEmployeeActions($actions->reverse()->values()) }}
                                        </td>
                                        <td>
                                            {{ \App\Models\Action::getDailyTotalByEmployeeActions($actions) }}
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('reports.reports_export_buttons')