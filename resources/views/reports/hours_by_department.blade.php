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
                                <th>{{ trans('fields.worked_hours') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($table as $department_id => $time)
                                <tr>
                                    <td>{{ \App\Models\Department::find($department_id)->name }}</td>
                                    <td>{{ $time }}</td>
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