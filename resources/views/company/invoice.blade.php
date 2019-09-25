@extends('backpack::layout')

@section('header')
    <section class="content-header">
        <h1>{{ $title }} #{{ $data->invoice_number }}</h1>
        <ol class="breadcrumb">
            <li><a href="{{ backpack_url() }}">{{ config('backpack.base.project_name') }}</a></li>
            <li class="active">{{ $title }}</li>
        </ol>
    </section>
@endsection


@section('content')
    @php
        if (backpack_user()->role == \App\User::ROLE_COMPANY) {
            $company = backpack_user()->company;
        } else {
            $company = $data->company;
        }
    @endphp
    <div class="row">
        <section class="invoice">
            <!-- title row -->
            <div class="row">
                <div class="col-xs-12">
                    <h2 class="page-header">
                        <img src="{{ asset('frontend/img/logo-BB.png') }}" style="max-height: 30px"> BioDactil
                        <small class="pull-right">Date: {{ date('m/d/Y') }}</small>
                    </h2>
                </div>
                <!-- /.col -->
            </div>
            <!-- info row -->
            <div class="row invoice-info">
                <div class="col-sm-4 invoice-col">
                    {{ trans('fields.from') }}
                    <address>
                        <strong>{{ trans('fields.name') }}: </strong>{{ $company->name }}<br>
                        <strong>{{ trans('fields.country') }}:</strong> {{ $company->country }}<br>
                        <strong>{{ trans('fields.address') }}:</strong> {{ $company->address }}<br>
                        <strong>{{ trans('fields.vat_number') }}:</strong> {{ $company->vat_number }}<br>
                        <strong>{{ trans('fields.email') }}:</strong> {{ backpack_user()->email }}<br>

                    </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-4 invoice-col">
                    {{ trans('fields.to') }}
                    <address>
                        {{--<strong>Biodactil</strong><br>--}}
                        {{--{{ trans('fields.country') }}: Spain<br>--}}
                        {{--{{ trans('fields.address') }}: Madrid<br>--}}
                        {{--{{ trans('fields.email') }}: admin@biodactil.com--}}

                        C/ Oliva 21<br>
                        Oficina 4 planta 2 Edif. 2 FITENI VIII<br>
                        28230 Las Rozas de Madrid, MADRID<br>
                        Tel.: 902 324 061<br>
                    </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-4 invoice-col">
                    <b>{{ trans('fields.invoice') }} # {{ $data->invoice_number }}</b><br>
                    <br>
                    {{--<b>Order ID:</b> 4F3S8J<br>--}}
                    <b>Payment Due:</b> {{ date("m/d/Y", strtotime($data->created_at)) }}<br>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->

            <!-- Table row -->
            <div class="row">
                <div class="col-xs-12 table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>{{ trans('fields.invoice_number') }}</th>
                            <th>{{ trans('fields.description') }}</th>
                            <th>{{ trans('fields.currency') }}</th>
                            <th>{{ trans('fields.amount') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>{{ $data->invoice_number }}</td>
                            <td>Biodactil Invoice</td>
                            <td>{{ $data->currency }}</td>
                            <td>{{ $data->amount }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->

            <div class="row">
                <!-- accepted payments column -->
                <div class="col-xs-6">
                    <p class="lead">{{ trans('fields.payment_method') }}: </p>
                    @if($data->payment_type == 'visa')
                    <img src="{{ asset('vendor/adminlte/dist/img/credit/visa.png') }}" alt="Visa">
                    @elseif($data->payment_type == 'paypal')
                    <img src="{{ asset('vendor/adminlte/dist/img/credit/paypal2.png') }}" alt="Paypal">
                    @endif

                </div>
                <!-- /.col -->
                {{--<div class="col-xs-6">--}}
                    {{--<p class="lead">Amount Due 2/22/2014</p>--}}

                    {{--<div class="table-responsive">--}}
                        {{--<table class="table">--}}
                            {{--<tr>--}}
                                {{--<th style="width:50%">Subtotal:</th>--}}
                                {{--<td>${{ $data->amount }}</td>--}}
                            {{--</tr>--}}
                            {{--<tr>--}}
                                {{--<th>Tax (9.3%)</th>--}}
                                {{--<td>$10.34</td>--}}
                            {{--</tr>--}}
                            {{--<tr>--}}
                                {{--<th>Total:</th>--}}
                                {{--<td>$265.24</td>--}}
                            {{--</tr>--}}
                        {{--</table>--}}
                    {{--</div>--}}
                {{--</div>--}}
                <!-- /.col -->
            </div>
            <!-- /.row -->

            <!-- this row will not appear when printing -->
            <div class="row no-print">
                <div class="col-xs-12">
                    <a type="button" href="{{ url('transaction/' . $data->id . '/invoice/pdf') }}" class="btn btn-primary pull-right" style="margin-right: 5px;">
                        <i class="fa fa-download"></i> {{ trans('fields.generate_pdf') }}
                    </a>
                    <a class="btn btn-default pull-right" href="{{ url('transaction') }}"  style="margin-right: 5px;">Back</a>
                </div>
            </div>
        </section>
    </div>

@endsection
@section('after_styles')

@endsection
@section('after_scripts')

@endsection
