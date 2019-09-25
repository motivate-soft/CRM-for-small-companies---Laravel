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
            <div class="box">
                <div class="box-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{ trans('fields.employee') }}</th>
                                @for($i = 1; $i <= Carbon\Carbon::createFromFormat('d/m/Y', '01/' . $filters['date'])->daysInMonth; $i++)
                                    <th>{{ $i }}</th>
                                @endfor
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
                                                $schedule = \App\Models\Schedule::find($filters['schedule']);

                                                if(isset($date[$day]) && $schedule) {
                                                    $schedule_data = \App\Models\Schedule::find($filters['schedule'])->data;
                                                    $date[$day] = $date[$day]->reverse()->values();

                                                    foreach ($date[$day] as $action){
                                                        if($action->option->type == 'in') {
                                                            if(
                                                                ($schedule->is_device || in_array(\Carbon\Carbon::parse($action->datetime)->month, $schedule_data['months'])) &&
                                                                array_key_exists(\Carbon\Carbon::parse($action->datetime)->dayOfWeekIso, $schedule_data['days'])
                                                            ) {
                                                                foreach($schedule_data['days'] as $key => $day_time) {
                                                                    if(\Carbon\Carbon::parse($action->datetime)->dayOfWeekIso == $key) {

                                                                        $color = 'green';
                                                                        foreach($day_time['times'] as $time) {
                                                                            if(
                                                                                strtotime($time['from']) < strtotime(\Carbon\Carbon::parse($action->datetime)->format('H:i')) &&
                                                                                strtotime($time['to']) > strtotime(\Carbon\Carbon::parse($action->datetime)->format('H:i')))
                                                                            {
                                                                                $color = 'red'; break;
                                                                            }
                                                                        }

                                                                        echo '<span style="color: ' . $color . '">' . \Carbon\Carbon::parse($action->datetime)->format('H:i') . '</span><br>';
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            @endphp
                                        </td>
                                    @endfor
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