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

    @include('reports.filters.filter3')

    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{ trans('fields.employee') }}</th>
                                @foreach(\App\Models\Action::getMonths() as $month)
                                    <th>{{ $month }}</th>
                                @endforeach
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
                                @foreach(\App\Models\Action::getMonths() as $month)
                                    @php
                                        $month_total = 0;
                                    @endphp
                                    <td>
                                        @php
                                            if(isset($date[$month])) {
                                                $seconds = 0;
                                                $date[$month] = $date[$month]->reverse()->values();
                                                for($i = 0; $i < $date[$month]->count(); $i++){
                                                    if($date[$month][$i]->option->type == 'in' && isset($date[$month][$i + 1]) && $date[$month][$i + 1]->option->type == 'out' && \Carbon\Carbon::parse($date[$month][$i]->datetime)->toDateString() == \Carbon\Carbon::parse($date[$month][$i + 1]->datetime)->toDateString()) {
                                                        $seconds += (int)\Carbon\Carbon::parse($date[$month][$i]->datetime)->diffInSeconds(\Carbon\Carbon::parse($date[$month][++$i]->datetime));
                                                    }
                                                }
                                                $month_total += $seconds;
                                                $total += $seconds;

                                                $time = \Carbon\Carbon::now()->addSeconds($month_total);

                                                $hours = $time->diffInHours();
                                                $minutes = $time->subHours($hours)->diffInMinutes();

                                                echo "{$hours}h{$minutes}m";

                                            }
                                        @endphp
                                    </td>
                                @endforeach
                                <td>
                                    @php
                                        $time = \Carbon\Carbon::now()->addSeconds($total);

                                        $hours = $time->diffInHours();
                                        $minutes = $time->subHours($hours)->diffInMinutes();

                                        echo "{$hours}h{$minutes}m";
                                    @endphp
                                </td>
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