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
    <section class="content">

        @php
            $companyPlan = $company->companyPlan;
            $free_days = $companyPlan->free_days;

            $expire_date = date("Y-m-d", strtotime($companyPlan->created_at . "+" . $free_days . " days"));
            $left_days = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($expire_date));

        @endphp

        <a href="{{ url('trial_mode') }}" class="hidden-print"><i class="fa fa-angle-double-left"></i> {{ trans('fields.back_to_list') }}</a>
        <div class="row m-t-20">
            <div class="col-md-8 col-md-offset-2">
                <!-- Default box -->
                <form method="post" action="{{ url('save_trial_mode') }}">
                    {{ csrf_field() }}
                    <div class="col-md-12">
                        <div class="row display-flex-wrap">
                            <!-- load the view from the application if it exists, otherwise load the one in the package -->
                            <div class="box col-md-12 padding-10 p-t-20">
                                <div class="form-group col-xs-12 required">
                                    <label>{{ trans('fields.billing_status') }}</label>
                                    <select name="billing_status" style="width: 100%"
                                            class="form-control select2_field select2-hidden-accessible" tabindex="-1"
                                            aria-hidden="true">
                                        <option value="free" @if($companyPlan->billing_status == 'free') selected @endif>{{ trans('fields.free') }}</option>
                                        <option value="unlimited"  @if($companyPlan->billing_status == 'unlimited') selected @endif>{{ trans('fields.unlimited') }}</option>
                                        <option value="rejected"  @if($companyPlan->billing_status == 'rejected') selected @endif>{{ trans('fields.rejected') }}</option>
                                        {{--<option value="expired">expired</option>--}}
                                    </select>
                                </div>
                                <!-- load the view from type and view_namespace attribute if set -->
                                <div class="form-group col-xs-12 required">
                                    <label>{{ trans('fields.left_days') }}</label>
                                    <input type="text" name="left_days" value="{{ $left_days }}" class="form-control">
                                </div>
                                <div class="hidden ">
                                    <input type="hidden" name="id" value="{{ $company->id }}" class="form-control">
                                </div>
                            </div>
                        </div><!-- /.box-body -->
                        <div class="">
                            <div id="saveActions" class="form-group">
                                <input type="hidden" name="save_action" value="save_and_back">
                                <div class="btn-group">
                                    <button type="submit" class="btn btn-success">
                                        <span class="fa fa-save" role="presentation" aria-hidden="true"></span> &nbsp;
                                        <span data-value="save_and_back">{{ trans('backpack::crud.save_action_save_and_back') }}</span>
                                    </button>
                                </div>
                                <a href="{{ url('trial_mode') }}" class="btn btn-default"><span
                                        class="fa fa-ban"></span> &nbsp;{{ trans('backpack::crud.cancel') }}</a>
                            </div>

                        </div><!-- /.box-footer-->
                    </div><!-- /.box -->
                </form>
            </div>
        </div>

    </section>
@endsection
