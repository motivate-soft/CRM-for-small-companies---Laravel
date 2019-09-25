@extends('backpack::layout')

@section('header')
    <section class="content-header">
        <h1 style="display: inline-block">{{ $title }} </h1> <h5 style="color:grey; display: inline-block"> for {{$employee_name}}</h5>
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
                            <th>{{trans('fields.start_date')}}</th>
                            <th>{{trans(('fields.end_date'))}}</th>
                            <th>{{trans(('fields.real_holiday_days'))}}</th>
                            <th>{{trans(('fields.status'))}}</th>
                            <th>{{trans(('fields.created_at'))}}</th>
                            <th>{{trans(('fields.cancel_state'))}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($holidays as $index => $holiday)
                            <tr>
                                <td>{{$index+1}}</td>
                                <td>{{date('d M Y', strtotime($holiday->start_date))}}</td>
                                <td>{{date('d M Y', strtotime($holiday->end_date))}}</td>
                                <td>{{$holiday->real_holiday_days}}</td>
                                <td>{{ucfirst($holiday->status)}}</td>
                                <td>{{date('d M Y', strtotime($holiday->created_at))}}</td>
                                <td>
                                    @if($holiday->cancel_state == 'approved')
                                        {{trans('fields.cancelled')}}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </table>
                    <a href="{{backpack_url('employee_holiday_days')}}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('reports.reports_export_buttons')