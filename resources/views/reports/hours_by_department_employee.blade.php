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
                                <th>{{ trans('fields.department') }}</th>
                                <th>{{ trans('fields.employees') }}</th>
                                <th>{{ trans('fields.worked_hours') }}</th>
                                <th>{{ trans('fields.total_worked_hours') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($table as $department_id => $employees)
                                @foreach($employees as $employee_id => $times)
                                    <tr>
                                        @if($loop->first)
                                            <td rowspan="{{ $employees->count() }}"><b>{{ \App\Models\Department::find($department_id)->name }}</b></td>
                                        @endif
                                        <td>{{ \App\Models\Employee::find($employee_id)->user->name }}</td>
                                        <td>{{ \App\Models\Action::getTotalTimeByEmployees($times) }}</td>
                                        @if($loop->first)
                                            <td rowspan="{{ $employees->count() }}"><b>{{ \App\Models\Action::getTotalTimeByEmployees($employees, false) }}</b></td>
                                        @endif
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