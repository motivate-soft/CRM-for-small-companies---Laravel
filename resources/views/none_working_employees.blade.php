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
            </div>
        </div>
    </div>
@endsection

@include('reports.reports_export_buttons')