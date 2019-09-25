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

    @php
        $check_user = backpack_user()->company;
        $subscriptions = \Illuminate\Support\Facades\Session::get('plan_id');
    @endphp
    <div class="row">
        <div class="col-md-12">

            {{--<div class="row">--}}
            {{--<div class="col-md-12">--}}
            {{--<div class="alert alert-danger">--}}
            {{--Please select your plan and pricing.--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}

            @php
                $company_plan = backpack_user()->company->companyPlan;


                if ($company_plan) {
                     $plan = $company_plan->plan;
                     $company_plan_id = $company_plan->company_plan_id;
                     $plan_type = explode('_', explode('-', $company_plan_id)[1])[1];
                } else {
                    $default_currency_id = \App\Models\Currency::where('short_key', 'USD')->first()->id;
                    $plan = \App\Models\Plan::where('currency_id', $default_currency_id)->first();
                }

                $plan_list = json_decode($plan->data, true);
                $plan_labels = \App\Models\Plan::getPlanLabelList();
                $currency = $plan->currency;

            @endphp

            {{--<h3>Manage Subscription</h3>--}}
            <div class="row">
                <div class="col-md-6">
                    <div class="box box-primary" style="min-height: 394px; font-size: 17px;">
                        {{--<div class="panel-heading">--}}
                        {{--<h4>{{ trans('fields.select_plan') }}</h4>--}}
                        {{--</div>--}}
                        <div class="box-header with-border">
                            <h3 class="box-title">{{ trans('fields.select_plan') }}</h3>
                        </div>

                        <div class="box-body row col-sm-offset-1">
                            <form class="col-sm-10 padding-50" action="{{ url('billing/addplan/' . $plan->id ) }}">
                                <div class="form-group row">
                                    <label for="inputPassword" class="col-sm-2 col-form-label">{{ trans('fields.plan') }}: </label>
                                    <div class="col-sm-8">
                                        <select class="form-control plan_selector" name="plan_type">
                                            @for($i = 0; $i < count($plan_list); $i++)
                                                <option value="{{ $i }}"
                                                    {{--@if($plan_type && $plan_type == $i)--}}
                                                        {{--selected--}}
                                                    {{--@endif--}}
                                                > {{ $plan_labels[$i] }}: &nbsp;

                                                    {{ $plan_list[$i]['min'] }}
                                                    ~ {{ $plan_list[$i]['max'] }} {{ trans('fields.employees') }}
                                                    / {{ $plan_list[$i]['price'] }} {{ $currency->symbol }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                    <button class="col-sm-2 btn btn-primary">{{ trans('fields.start') }}</button>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-12 plan_box">
                                        {{--@if($subscription)--}}
                                            {{--<h4>Current Plan:{{ $plan_labels[$subscription->type] }}</h4>--}}
                                            {{--<div>--}}
                                                {{--<label class="control-label">{{ trans('fields.price') }}: </label>--}}
                                                {{--@if($subscription)--}}
                                                {{--{{ $plan_list[$subscription->type]['price'] }} {{ $currency->symbol }}/{{ trans('fields.month') }}--}}
                                                {{--<br>--}}
                                                {{--<label>{{ trans('fields.min').':' . $plan_list[$subscription->type]['min']}}</label> ~ <label>{{ trans('fields.max').':' . $plan_list[$subscription->type]['max']}}</label> {{ trans('fields.employees') }}--}}
                                                {{--@endif--}}
                                                {{--@else--}}
                                                {{--<label class="control-label">{{ trans('fields.price') }}: </label>--}}
                                                {{--{{ trans('fields.free') }}--}}
                                                {{--<br>--}}
                                                {{--<label>{{ trans('fields.min').':' . $plan_list[0]['min']}} </label> ~ <label>{{ trans('fields.max').':' . $plan_list[0]['max']}} </label> {{ trans('fields.employees') }}--}}
                                                {{--@endif--}}
                                                {{--<br>--}}
                                                {{--<label>{{ trans('fields.subscription_date') }}: </label> {{ $subscription->subscription_date }}--}}
                                                {{--<br>--}}
                                                {{--<label>{{ trans('fields.expire_date') }}: </label>--}}
                                                {{--@php--}}
                                                    {{--if($subscription->type == 0)--}}
                                                        {{--$expire_date = date("Y-m-d", strtotime($subscription->subscription_date . "+" . $subscription->plan->free_month . " month"));--}}
                                                    {{--else {--}}
                                                        {{--$expire_date = date("Y-m-d", strtotime($subscription->subscription_date . "+1 month"));--}}
                                                    {{--}--}}
                                                {{--@endphp--}}
                                                {{--{{ $expire_date }}--}}
                                                {{--<br>--}}
                                                {{--<label>{{ trans('fields.left_days') }}:</label>  {{ \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($expire_date)) }}--}}
                                                {{--<br>--}}
                                                {{--<label>Payment Status: </label> {{ trans('fields.' . $subscription->status) }}--}}

                                                @if($company_plan)
                                                <table class="plan_summary table  table-striped table-primary table-condensed">
                                                    <thead>
                                                        <tr>
                                                            <th>Current Plan</th>
                                                            <th>{{ $plan_labels[$plan_type] }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><label>{{ trans('fields.price') }}:</label></td>
                                                            <td>{{ $plan_list[$plan_type]['price'] }} {{ $currency->symbol }}
                                                                /{{ trans('fields.month') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><label>{{ trans('fields.employees') }}:</label></td>
                                                            <td>{{ trans('fields.min').':' . $plan_list[$plan_type]['min']}}
                                                                ~ {{ trans('fields.max').':' . $plan_list[$plan_type]['max']}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><label>{{ trans('fields.subscription_date') }}:</label></td>
                                                            <td> {{ date('Y-m-d',strtotime($company_plan->created_at)) }} </td>
                                                        </tr>

                                                        @php
                                                            $free_days = $company_plan->free_days;
                                                            $expire_date = date("Y-m-d", strtotime($company_plan->created_at . "+" . $free_days ." days"));
                                                            $left_days = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($expire_date));
                                                        @endphp

                                                        @if($company_plan->billing_status == 'free')
                                                        <tr>
                                                            <td><label>{{ trans('fields.expire_date') }}:</label></td>
                                                            <td>{{ $expire_date }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><label>{{ trans('fields.left_days') }}:</label></td>
                                                            <td> <span class="label @if($left_days < 5)
                                                                    label-danger
                                                                    @else
                                                                    label-success
                                                                    @endif
                                                                    ">{{ $left_days }}</span></td>
                                                        </tr>
                                                        @endif
                                                        <tr>
                                                            <td><label>Payment Status: </label></td>
                                                            <td>{{ trans('fields.' . $company_plan->billing_status) }}</td>
                                                        </tr>
                                                    </tbody>

                                                </table>
                                                @endif
                                            {{--</div>--}}
                                        {{--@endif--}}
                                    </div>
                                </div>
                                {{--<div class="form-group row text-center">--}}
                                    {{--<button class="btn btn-primary">{{ trans('fields.activate') }}</button>--}}
                                {{--</div>--}}
                            </form>
                        </div>
                    </div>
                </div>



                @php
                    $stripeModel = \App\Models\StripeModel::retrieveStripeSubscription();
                    $paypalModel = backpack_user()->company->paypalModel;
                @endphp
                <div class="col-md-6">
                    <div class="box box-success" style="min-height: 394px">
                        <div class="box-header with-border">
                            <h3 class="box-title">
                                {{ trans('fields.payment_method') }}
                            </h3>
                        </div>
                        <div class="panel-body">
                            @if($company_plan && $stripeModel)
                                <div class="row padding-20">
                                    <div class="col-sm-12">
                                        {{--<p>Your paypal payment method is verified.</p>--}}
                                    </div>
                                    <div class="col-sm-12">
                                        <form action="{{ url('billing/subscription/inactive/visa') }}" method="post">
                                            {{ csrf_field() }}
                                            <button id="submit__paypal_button"
                                                    class="pull-left btn btn-block btn-primary">
                                                {{ trans('fields.inactive') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @elseif($company_plan && $paypalModel)
                                <div class="row padding-20">
                                    <div class="col-sm-12">
                                        {{--<p>Your paypal payment method is verified.</p>--}}
                                    </div>
                                    <div class="col-sm-12">
                                        <form action="{{ url('billing/subscription/inactive/paypal') }}" method="post">
                                            {{ csrf_field() }}
                                            <button id="submit__paypal_button"
                                                    class="pull-left btn btn-block btn-primary">
                                                {{ trans('fields.inactive') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @else
                            <div class="nav-tabs-custom">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a href="#tab_1" data-toggle="tab"><img
                                                alt="Credit Card Logos"
                                                src="http://www.credit-card-logos.com/images/visa_credit-card-logos/new_visa_medium.gif"
                                                border="0" style="max-width: 50px"/></a></li>
                                    <li><a href="#tab_2" data-toggle="tab"><img
                                                src="{{ asset('vendor/adminlte/dist/img/paypal.jfif') }}"
                                                style="max-width: 70px;"> </a></li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="tab_1">
                                        <form method="post" id="visa_form"
                                              action="{{ url('billing/post_strip_payment') }}">
                                            {{ csrf_field() }}
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label
                                                        class="control-label">{{ trans('fields.card_number') }}</label>
                                                    <input type="number" name="visa_number" onkeyup="validate_visa()"
                                                           id="visa_number" class="form-control"  data-stripe="number" >
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label class="control-label">{{ trans('fields.month') }}</label>
                                                    <select class="form-control" name="month"  data-stripe="exp-month" >
                                                        <option value="1">1</option>
                                                        <option value="2">2</option>
                                                        <option value="3">3</option>
                                                        <option value="4">4</option>
                                                        <option value="5">5</option>
                                                        <option value="6">6</option>
                                                        <option value="7">7</option>
                                                        <option value="8">8</option>
                                                        <option value="9">9</option>
                                                        <option value="10">10</option>
                                                        <option value="11">11</option>
                                                        <option value="12">12</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="control-label">{{ trans('fields.year') }}</label>
                                                    <select class="form-control" name="year" data-stripe="exp-year">
                                                        <option value="2019">2019</option>
                                                        <option value="2020">2020</option>
                                                        <option value="2021">2021</option>
                                                        <option value="2022">2022</option>
                                                        <option value="2023">2023</option>
                                                        <option value="2024">2024</option>
                                                        <option value="2025">2025</option>
                                                        <option value="2026">2026</option>
                                                        <option value="2027">2027</option>
                                                        <option value="2028">2028</option>
                                                        <option value="2029">2029</option>
                                                        <option value="2030">2030</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="control-label">CVC</label>
                                                    <input class="form-control" onkeyup="validate_cvc()" id="visa_cvc"
                                                           type="text" name="cvc" maxlength="3" data-stripe="cvc">
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <p class="visa-errors" style="color: red"></p>
                                            </div>
                                            <div class="row padding-15">
                                                <div class="col-md-12">
                                                    <button id="submit_visa_button" type="button"
                                                            class="pull-left btn btn-block btn-primary">
                                                        {{ trans('fields.complete_payment') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <!-- /.tab-pane -->
                                    <div class="tab-pane" id="tab_2" style="min-height: 180px">
                                        <div class="row from-group">
                                            <div class="col-md-12">
                                                <p class="padding-15">In order to complete your transaction, we will
                                                    transfer you over to PayPal's secure servers.
                                                </p>
                                                <div class="col-sm-12">
                                                    <form action="{{ url('billing/post_paypal_payment') }}"
                                                          method="post" id="submit_paypal_form">
                                                        {{ csrf_field() }}
                                                        <button id="submit__paypal_button"
                                                                class="pull-left btn btn-block btn-primary">
                                                            {{ trans('fields.processed') }}
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.tab-content -->
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('after_styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/css/style_pricing.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/css/demo01.css') }}">
    <style>
        .plan_summary tr td,.plan_summary tr th  {
            text-align: center;
        }

        /*.plan_summary tr td:first-child {*/
            /*text-align: right !important;*/
        /*}*/

        /*.plan_summary tr td:nth-child(2) {*/
            /*text-align: left !important;*/
        /*}*/
    </style>
@endsection
@section('after_scripts')
    <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
    <script>

        Stripe.setPublishableKey('{!! env('STRIPE_KEY') !!}');

        {{--var plans = JSON.parse('{!! json_encode($plan_list) !!}');--}}
        {{--var free_month = '{!! $plans->free_month !!}';--}}
        visa_state = false;
        cvc_state = false;
        $('#submit_visa_button').on('click', function (e) {
            visa_state = validate_visa();
            cvc_state = validate_cvc();
            if (visa_state === true && cvc_state === true) {
                $('#submit_visa_button').attr('disabled', true);
                $form = $('#visa_form');
                Stripe.card.createToken($form, stripeResponseHandler);
            }
        });

        $('#submit_paypal_form').submit(function () {
            $('#submit__paypal_button').attr('disabled', true);
        });

        function validate_visa() {
            inputtxt = $('#visa_number').val();

            var cardno = /^(?:4[0-9]{12}(?:[0-9]{3})?)$/;
            if (inputtxt.match(cardno)) {
                visa_state = true;
                $('#visa_number').css('border-color', '#ccc');
                return true;
            } else {
                visa_state = false;
                $('#visa_number').css('border-color', 'red');
                return false;
            }
        }

        function validate_cvc() {
            inputtxt = $('#visa_cvc').val();

            var cardno = /^([0-9]{3})$/;
            if (inputtxt.match(cardno)) {
                cvc_state = true;
                $('#visa_cvc').css('border-color', '#ccc');
                return true;
            } else {
                cvc_state = false;

                $('#visa_cvc').css('border-color', 'red');
                return false;
            }
        }


        var stripeResponseHandler = function(status, response) {
            var $form = $('#visa_form');
            if (response.error) {
                $form.find('.visa-errors').text(response.error.message);
                $form.find('#submit_visa_button').prop('disabled', false);
            } else {
                var token = response.id;
                $form.append($('<input type="hidden" name="stripeToken" />').val(token));
                $form.get(0).submit();
            }
        };
    </script>
@endsection
