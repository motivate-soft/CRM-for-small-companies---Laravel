@extends('backpack::layout')

@section('before_styles')
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/') }}/bower_components/bootstrap-daterangepicker/daterangepicker.css">
@endsection

@section('header')
    <section class="content-header">
        <h1>
            {{ trans('fields.holidays') }}
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ backpack_url() }}">{{ config('backpack.base.project_name') }}</a></li>
            <li><a href="{{ backpack_url('holidays') }}">{{ trans('fields.holidays') }}</a></li>
            <li class="active">{{ trans('backpack::crud.add') }}</li>
        </ol>
    </section>
@endsection

@section('content')
    <a href="{{backpack_url('holidays')}}" class="hidden-print">
        <i class="fa fa-angle-double-left"></i> {{trans('backpack::crud.back_to_all')}}  <span>{{trans('fields.holidays')}}</span>
    </a>
    <section class="content">
        <div class="row m-t-20">
            <div class="col-md-8 col-md-offset-2">
                <form method="post" action="{{url('holidays/store1')}}">
                    {!! csrf_field() !!}
                    @if ($errors->any())
                    <div class="callout callout-danger">
                        <h4>Please fix the following errors:</h4>
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{$error}}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <div class="col-md-12">
                        <div class="row display-flex-wrap">
                            <div class="box col-md-12 padding-10 p-t-20">
                                <div class="alert alert-success alert-dismissible hidden">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h4><i class="icon fa fa-check"></i> {{trans('fields.alert')}}!</h4>
                                    <p id="msg"></p>
                                </div>
                                <div class="form-group col-xs-12">
                                    <label>{{trans('fields.date')}}</label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right" id="date_range">
                                        <input type="hidden" name="start_date" value="">
                                        <input type="hidden" name="end_date" value="">
                                    </div>
                                </div>
                                <div class="form-group col-xs-12">
                                    <label>{{trans('fields.spent_holidays_from')}}</label>
                                    <div class="input-group">
                                        <select class="form-control select2" style="width: 100%;">
                                            <option selected="selected" value="{{date('Y')}}">{{date('Y')}}</option>
{{--                                            <option value="{{date('Y') -1 }}">{{date('Y') -1 }}</option>--}}
                                        </select>
                                        @php
                                        $user_id = backpack_user()->id;
										if(!App\Models\EmployeeHolidayDays::where('user_id', $user_id)->where('year', date('Y'))->first()){
                                            $test = new App\Models\EmployeeHolidayDays();
                                            $test->spend_holidays = 0;
                                            $test->user_id = $user_id;
                                            $test->year = date('Y');
                                            $test->save();
                                        }
                                        $spend_holidays = App\Models\EmployeeHolidayDays::where('user_id', $user_id)->where('year', date('Y'))->first()->spend_holidays;
                                        $holiday_days = App\User::find($user_id)->holiday_days;
                                        $left_holiday_days = $holiday_days - $spend_holidays;
                                        @endphp
                                        {{date('Y')}}, {{$left_holiday_days}} Days left
                                    </div>
                                </div>
                                <div class="form-group col-xs-12 required">
                                    <label>{{trans('fields.comment')}}</label>
                                    <textarea class="form-control" name="comment"></textarea>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div id="saveActions" class="form-group">
                                <div class="btn-group">
                                    <button type="submit" class="btn btn-success">
                                        <span class="fa fa-save" role="presentation" aria-hidden="true"></span> &nbsp;
                                        <span data-value="save_and_back">{{trans('backpack::crud.save_action_save_and_back')}}</span>
                                    </button>
                                    <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aira-expanded="false" aria-expanded="false">
                                        <span class="caret"></span>
                                        <span class="sr-only">▼</span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a href="javascript:void(0);" data-value="save_and_new">{{trans('backpack::crud.save_action_save_and_new')}}</a></li>
                                    </ul>
                                </div>
                                <a href="{{url('holidays')}}" class="btn btn-default"><span class="fa fa-ban"></span> &nbsp;{{trans('backpack::crud.cancel')}}</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </section>
@endsection

@section('after_scripts')
    <script src="{{ asset('vendor/adminlte') }}/bower_components/moment/min/moment.min.js"></script>
    <script src="{{ asset('vendor/adminlte') }}/bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script>
        $('#date_range').daterangepicker({
            alwaysShowCalendars: true
        });
        $('#date_range').on('apply.daterangepicker', function(ev, picker) {
            console.log(picker.startDate.format('YYYY-MM-DD'));
            console.log(picker.endDate.format('YYYY-MM-DD'));
            $.ajax({
                url: '{{ url('holidays/validate1') }}',
                type: 'post',
                data: {
                    start_date: picker.startDate.format('YYYY-MM-DD'),
                    end_date: picker.endDate.format('YYYY-MM-DD')
                },
                success: function (res) {
                    console.log(res);
                    $('.alert').removeClass('hidden');
                    if(res['status'] == 'success') {
                        $('#msg').html(res['msg']);
                        $("input[name='start_date']").val(picker.startDate.format('YYYY-MM-DD'));
                        $("input[name='end_date']").val(picker.endDate.format('YYYY-MM-DD'));
                    }else{
                        $('#msg').html(res['msg']);
                    }
                }
            });
        });
    </script>
@endsection
