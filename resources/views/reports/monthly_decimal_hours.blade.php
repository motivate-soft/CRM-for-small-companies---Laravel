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

    @include('reports.filters.filter2')

    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{ trans('fields.employee') }}</th>
                                @for($i = 1; $i <= Carbon\Carbon::createFromFormat('d/m/Y', '01/' . $filters['date'])->daysInMonth; $i++)
                                    <th>{{ $i }}</th>
                                @endfor
                                <th>{{ trans('fields.total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($table as $employee_id => $date)
                                <tr>
                                    <td>{{ \App\Models\Employee::find($employee_id)->user->name }}</td>
                                    @php
                                        $total = 0;
                                    @endphp
                                    @for($day = 1; $day <= Carbon\Carbon::createFromFormat('d/m/Y', '01/' . $filters['date'])->daysInMonth; $day++)
                                        <td>
                                            @php
                                            if(isset($date[$day])) {
                                                $seconds = 0;
                                                $date[$day] = $date[$day]->reverse()->values();
                                                for($i = 0; $i < $date[$day]->count(); $i++){
                                                    if($date[$day][$i]->option->type == 'in' && isset($date[$day][$i + 1]) && $date[$day][$i + 1]->option->type == 'out' && \Carbon\Carbon::parse($date[$day][$i]->datetime)->toDateString() == \Carbon\Carbon::parse($date[$day][$i + 1]->datetime)->toDateString()) {
                                                        $seconds += (int)\Carbon\Carbon::parse($date[$day][$i]->datetime)->diffInSeconds(\Carbon\Carbon::parse($date[$day][++$i]->datetime));
                                                    }
                                                }
                                                $time = \App\Models\Action::convertToDecimal(\Carbon\Carbon::now()->diff(\Carbon\Carbon::now()->addSeconds($seconds))->format('%H:%I:%S'));
                                                $total += (float)$time;
                                                echo $time;
                                            }
                                            @endphp
                                        </td>
                                    @endfor
                                    <td>{{ $total }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('reports.reports_export_buttons')