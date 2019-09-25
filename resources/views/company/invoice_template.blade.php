<!DOCTYPE html>
<html>
<head>
    <title>Biodactil | {{ trans('fields.invoice') }}</title>
    <!-- Tell the browser to be responsive to screen width -->
    <!-- Bootstrap 3.3.7 -->
{{--    <link rel="stylesheet" href="{{ asset('vendor/adminlte/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">--}}
    <!-- Font Awesome -->
    {{--<link rel="stylesheet" href="{{ asset('vendor/adminlte/bower_components/font-awesome/css/font-awesome.min.css')}}">--}}
    <!-- Ionicons -->
    {{--<link rel="stylesheet" href="{{ asset('vendor/adminlte/bower_components/Ionicons/css/ionicons.min.css') }}">--}}
    <!-- Theme style -->
    <style>
        .container {
            width: 100%;
        }

        .row {
            width: 100%;
            /*display: flex;*/
            /*flex-wrap: wrap;*/
            margin-top: 5px;
        }

        /* content */
        .row div {
            /*background-color: #EF5350;*/
            padding: 2%;
            /*border: 1px solid white;*/
            border-radius: 5px;
            /*text-align: right;*/
            /*color: white;*/
            /*transition: background-color 1s;*/
        }

        /*.row div:nth-child(2n):hover {*/
        /*background-color: #42A5F5;*/
        /*}*/

        /*.row div:nth-child(2n+1):hover {*/
        /*background-color: #66BB6A;*/
        /*}*/

        /* 1/12 */
        .col-1 {
            width: 8.33%;
        }

        /* 2/12 */
        .col-2 {
            width: 16.66%;
        }

        /* 3/12 */
        .col-3 {
            width: 25%;
        }

        /* 4/12 */
        .col-4 {
            width: 33.33%
        }

        /* 5/12 */
        .col-5 {
            width: 41.66%;
        }

        /* 6/12 */
        .col-6 {
            width: 50%;
        }

        /* 7/12 */
        .col-7 {
            width: 58.33%;
        }

        /* 8/12 */
        .col-8 {
            width: 66.66%;
        }

        /* 9/12 */
        .col-9 {
            width: 75%;
        }

        /* 10/12 */
        .col-10 {
            width: 83.33%;
        }

        /* 11/12 */
        .col-11 {
            width: 91.66%;
        }

        /* 12/12 */
        .col-12 {
            width: 100%;
        }

        table thead th, table tr td {
            text-align: left;
            border: 1px solid  #ddd;
            padding: 5px;
        }

        table {
            border-collapse: collapse;
        }

    </style>
</head>
<body style="padding: 20px;">
<div class="wrapper">
    <!-- Main content -->
    @php
        if (backpack_user()->role == \App\User::ROLE_COMPANY) {
            $company = backpack_user()->company;
        } else {
            $company = $data->company;
        }
    @endphp
    <section class="invoice">
        <!-- title row -->
        <div class="row" style="border-bottom: 1px solid #ddd;">
            <div class="col-12"  style="padding: 5px !important;">
                <h2 class="page-header">
                    <img src="{{ public_path() . '/frontend/img/logo-BB.png' }}" style="max-height: 30px"> BioDactil
                    <small class="pull-right" style="float: right">Date: {{ date('m/d/Y') }}</small>
                </h2>
            </div>
            <!-- /.col -->
        </div>
        <!-- info row -->
        <div class="row invoice-info" style="display: inline-block !important;">
            <div class="col-4 invoice-col"  style="float: left !important;">
               <h3> {{ trans('fields.from') }}</h3>
                <address>
                    <strong>{{ trans('fields.name') }}:</strong> {{ $company->name }}<br>
                    <strong>{{ trans('fields.country') }}:</strong> {{ $company->country }}<br>
                    <strong>{{ trans('fields.address') }}:</strong> {{ $company->address }}<br>
                    <strong>{{ trans('fields.vat_number') }}:</strong> {{ $company->vat_number }}<br>
                    <strong>{{ trans('fields.email') }}:</strong> {{ backpack_user()->email }}<br>
                </address>
            </div>
            <!-- /.col -->
            <div class="col-4 invoice-col" style="float: right !important; text-align: right">
                <h3>{{ trans('fields.to') }}</h3>
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
            <div class="col-4 invoice-col" >
                <b>{{ trans('fields.invoice') }}: # {{ $data->invoice_number }}</b><br>
                {{--<b>Order ID:</b> 4F3S8J<br>--}}
                <b>{{ trans('fields.payment_due') }}:</b> {{ date("m/d/Y", strtotime($data->created_at)) }}<br>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

        <!-- Table row -->
        <div class="row">
            <div class="col-12 table-responsive">
                <table class="table table-striped col-12">
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
                        <td>Biodactil Monthly Payment Invoice</td>
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
            <div class="col-5">
                <p class="lead">{{ trans('fields.payment_method') }}:</p>
                @if($data->payment_type == 'visa')
                    <img src="{{ public_path() . '/vendor/adminlte/dist/img/credit/visa.png' }}" alt="Visa">
                @elseif($data->payment_type == 'paypal')
                    <img src="{{ public_path() . '/vendor/adminlte/dist/img/credit/paypal2.png' }}" alt="Paypal">
                @endif
            </div>
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- ./wrapper -->
</body>
</html>
