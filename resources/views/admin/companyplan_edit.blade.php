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
            $company_plan = $company->companyPlan;

            if ($company_plan) {
                 $plan = $company_plan->plan;
                 $company_plan_id = $company_plan->company_plan_id;
                 $plan_type = explode('_', explode('-', $company_plan_id)[1])[1];
                 $free_days = $company_plan->free_days;
            } else {
                $default_currency_id = \App\Models\Currency::where('short_key', 'USD')->first()->id;
                $plan = \App\Models\Plan::where('currency_id', $default_currency_id)->first();
                 $free_month = $plan->free_month;
                $expre_date = date('Y-m-d', strtotime(date('Y-m-d') . "+" . $free_month . " month"));
                $free_days = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($expre_date));
            }

            $plan_list = json_decode($plan->data, true);
            $currency = $plan->currency;
            $plan_labels = \App\Models\Plan::getPlanLabelList();



        @endphp

        {{--<a href="{{ url('trial_mode') }}" class="hidden-print"><i--}}
                {{--class="fa fa-angle-double-left"></i> {{ trans('fields.back_to_list') }}</a>--}}
        <div class="row m-t-20">
            <div class="col-md-8 col-md-offset-2">
                <!-- Default box -->
                <form method="post" action="{{ url('save_company_plan') }}">
                    {{ csrf_field() }}
                    <div class="col-md-12">
                        <div class="row display-flex-wrap">
                            <!-- load the view from the application if it exists, otherwise load the one in the package -->
                            <div class="box col-md-12 padding-10 p-t-20">
                                <div class="form-group col-xs-12 required">
                                    <label>{{ trans('fields.name') }}</label>
                                    <input type="text" name="name" value="{{ $company->name }}" class="form-control" disabled>
                                </div>
                                <div class="form-group col-xs-12 required">
                                    <label>{{ trans('fields.email') }}</label>
                                    <input type="text" name="email" value="{{ $company->email }}" class="form-control" disabled>
                                </div>

                                <div class="form-group col-xs-12 required free_days_form @if($company_plan && $company_plan->billing_status == 'unlimited') hide @endif">
                                    <label>{{ trans('fields.free_days') }}</label>
                                    <input type="number" name="free_days" value="{{ $free_days }}" class="form-control" required>
                                </div>
                                <input type="text" style="display: none" name="id" value="{{ $company->id }}" class="form-control">
                                <input type="text"  style="display: none" name="plan_id" value="{{ $plan->id }}" class="form-control">

                                <div class="form-group col-xs-12 required plan_form @if($company_plan && $company_plan->billing_status == 'unlimited') hide @endif">
                                    <label>{{ trans('fields.plan') }}</label>
                                    <select name="plan_type" style="width: 100%"
                                            class="form-control select2_field select2-hidden-accessible" tabindex="-1"
                                            aria-hidden="true">
                                        @for($i = 0; $i < count($plan_list); $i++)
                                            <option value="{{ $i }}"
                                                @if(isset($plan_type) && $plan_type == $i)
                                                    selected
                                                @endif
                                            > {{ $plan_labels[$i] }}: &nbsp;

                                                {{ $plan_list[$i]['min'] }}
                                                ~ {{ $plan_list[$i]['max'] }} {{ trans('fields.employees') }}
                                                / {{ $plan_list[$i]['price'] }} {{ $currency->symbol }}
                                            </option>
                                        @endfor
                                        {{--<option value="expired">expired</option>--}}
                                    </select>
                                </div>


                                <div class="form-group col-xs-12 required">
                                    <label>{{ trans('fields.billing_status') }}</label>
                                    <select name="billing_status" style="width: 100%"  id="billing_status"
                                            class="form-control select2_field select2-hidden-accessible" tabindex="-1"
                                            aria-hidden="true">
                                        <option value="free"
                                                @if($company_plan && $company_plan->billing_status == 'free') selected @endif>{{ trans('fields.free') }}</option>
                                        <option value="unlimited"
                                                @if($company_plan && $company_plan->billing_status == 'unlimited') selected @endif>{{ trans('fields.unlimited') }}</option>
                                        <option value="pending"
                                                @if($company_plan && $company_plan->billing_status == 'pending') selected @endif>{{ trans('fields.pending') }}</option>
                                        {{--<option value="rejected"--}}
                                                {{--@if($company_plan && $company_plan->billing_status == 'rejected') selected @endif>{{ trans('fields.rejected') }}</option>--}}
                                        {{--<option value="expired">expired</option>--}}
                                    </select>
                                </div>
                            </div>
                        </div><!-- /.box-body -->
                        <div class="">
                            <div id="saveActions" class="form-group">
                                <input type="hidden" name="save_action" value="save_and_back">
                                <div class="btn-group">
                                    <button type="submit" class="btn btn-success">
                                        <span class="fa fa-save" role="presentation" aria-hidden="true"></span> &nbsp;
                                        <span
                                            data-value="save_and_back">{{ trans('backpack::crud.save_action_save_and_back') }}</span>
                                    </button>
                                </div>
                                <a href="{{ url('company_palns') }}" class="btn btn-default"><span
                                        class="fa fa-ban"></span> &nbsp;{{ trans('backpack::crud.cancel') }}</a>
                            </div>

                        </div><!-- /.box-footer-->
                    </div><!-- /.box -->
                </form>
            </div>
        </div>

    </section>
@endsection

@section('after_scripts')
    <script>
        $('#billing_status').on('change', function () {
            if ($(this).val() === 'unlimited') {
                $('.plan_form').addClass('hide');
                $('.free_days_form').addClass('hide');

            } else {
                $('.plan_form').removeClass('hide');
                $('.free_days_form').removeClass('hide');
            }
        })
    </script>

@endsection
