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
                                        <time class="pull-right">
                                            <i class="fa fa-clock-o"></i>
                                            {{ \App\Models\Action::getTotalByEmployeeActions($items) }}
                                        </time>
                                    </h4>
                                </div>
                                <div id="item-{{ $employee_id }}" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                    <div class="box-body">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>{{ trans('fields.action') }}</th>
                                                    <th>{{ trans('fields.date_and_time') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($items as $item)
                                                    <tr>
                                                        <td>{{ $item->option->type }} {{ $item->option ? '(' . $item->option->name . ')' : '' }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($item->datetime)->format('m/d/Y H:i:s') }}</td>
                                                    </tr>
                                                @endforeach
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