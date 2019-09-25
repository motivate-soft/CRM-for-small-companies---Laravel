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
        $month_labels = array();
        $month_values = array();
        foreach ($transaction_item as $key => $item) {
            array_push($month_labels, \App\Models\Action::getMonths((int)$key));
            array_push($month_values, count($item));
        }
    @endphp
    {{--<div class="row">--}}
        {{--<div class="col-md-3 col-sm-6 col-xs-12">--}}
            {{--<div class="info-box">--}}
                {{--<span class="info-box-icon bg-aqua"><i class="ion ion-ios-gear-outline"></i></span>--}}

                {{--<div class="info-box-content">--}}
                    {{--<span class="info-box-text">CPU Traffic</span>--}}
                    {{--<span class="info-box-number">90<small>%</small></span>--}}
                {{--</div>--}}
                {{--<!-- /.info-box-content -->--}}
            {{--</div>--}}
            {{--<!-- /.info-box -->--}}
        {{--</div>--}}
        {{--<!-- /.col -->--}}
        {{--<div class="col-md-3 col-sm-6 col-xs-12">--}}
            {{--<div class="info-box">--}}
                {{--<span class="info-box-icon bg-red"><i class="fa fa-google-plus"></i></span>--}}

                {{--<div class="info-box-content">--}}
                    {{--<span class="info-box-text">Likes</span>--}}
                    {{--<span class="info-box-number">41,410</span>--}}
                {{--</div>--}}
                {{--<!-- /.info-box-content -->--}}
            {{--</div>--}}
            {{--<!-- /.info-box -->--}}
        {{--</div>--}}
        {{--<!-- /.col -->--}}

        {{--<!-- fix for small devices only -->--}}
        {{--<div class="clearfix visible-sm-block"></div>--}}

        {{--<div class="col-md-3 col-sm-6 col-xs-12">--}}
            {{--<div class="info-box">--}}
                {{--<span class="info-box-icon bg-green"><i class="ion ion-ios-cart-outline"></i></span>--}}

                {{--<div class="info-box-content">--}}
                    {{--<span class="info-box-text">Sales</span>--}}
                    {{--<span class="info-box-number">760</span>--}}
                {{--</div>--}}
                {{--<!-- /.info-box-content -->--}}
            {{--</div>--}}
            {{--<!-- /.info-box -->--}}
        {{--</div>--}}
        {{--<!-- /.col -->--}}
        {{--<div class="col-md-3 col-sm-6 col-xs-12">--}}
            {{--<div class="info-box">--}}
                {{--<span class="info-box-icon bg-yellow"><i class="ion ion-ios-people-outline"></i></span>--}}

                {{--<div class="info-box-content">--}}
                    {{--<span class="info-box-text">New Members</span>--}}
                    {{--<span class="info-box-number">2,000</span>--}}
                {{--</div>--}}
                {{--<!-- /.info-box-content -->--}}
            {{--</div>--}}
            {{--<!-- /.info-box -->--}}
        {{--</div>--}}
        {{--<!-- /.col -->--}}
    {{--</div>--}}
    <!-- /.row -->

    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans('fields.monthly_transaction_report') }}</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-8">
                            <p class="text-center">
                                <strong>{{ trans('fields.transactions') }}: {{ $month_labels[0] }}, {{ date('Y') }} - {{ end($month_labels) }}, {{ date('Y') }}</strong>
                            </p>

                            <div class="chart">
                                <!-- Sales Chart Canvas -->
                                <canvas id="salesChart" style="height: 180px;"></canvas>
                            </div>
                            <!-- /.chart-responsive -->
                        </div>
                        <!-- /.col -->
                        <div class="col-md-4">
                            <p class="text-center">
                                <strong>{{ trans('fields.transaction_method') }}</strong>
                            </p>

                            <!-- /.progress-group -->
                            <div class="progress-group">
                                <span class="progress-text">{{ trans('fields.visa') }}</span>
                                <span class="progress-number"><b>{{ $visa_num  }}</b>/{{ $paypal_num + $visa_num }}</span>

                                <div class="progress sm">
                                    <div class="progress-bar progress-bar-red" style="width: {{ $visa_num/($paypal_num + $visa_num) * 100 }}%"></div>
                                </div>
                            </div>
                            <!-- /.progress-group -->
                            <div class="progress-group">
                                <span class="progress-text">{{ trans('fields.paypal') }}</span>
                                <span class="progress-number"><b>{{ $paypal_num  }}</b>/{{ $paypal_num + $visa_num }}</span>

                                <div class="progress sm">
                                    <div class="progress-bar progress-bar-green" style="width: {{ $paypal_num/($paypal_num + $visa_num) * 100 }}%"></div>
                                </div>
                            </div>

                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- ./box-body -->
                <div class="box-footer">
                    <div class="row">
                        <div class="col-sm-3 col-xs-6">
                            <div class="description-block border-right">
                                <span class="description-percentage text-green"><i class="fa fa-caret-up"></i></span>
                                <h5 class="description-header">{{ $year_transaction }}</h5>
                                <span class="description-text">{{ trans('fields.year') }}</span>
                            </div>
                            <!-- /.description-block -->
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-3 col-xs-6">
                            <div class="description-block border-right">
                                <span class="description-percentage text-yellow"><i class="fa fa-caret-left"></i></span>
                                <h5 class="description-header">{{ $month_transaction }}</h5>
                                <span class="description-text">{{ trans('fields.month') }}</span>
                            </div>
                            <!-- /.description-block -->
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-3 col-xs-6">
                            <div class="description-block border-right">
                                <span class="description-percentage text-green"><i class="fa fa-caret-up"></i> </span>
                                <h5 class="description-header">{{ $week_transaction }}</h5>
                                <span class="description-text">{{ trans('fields.week') }}</span>
                            </div>
                            <!-- /.description-block -->
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-3 col-xs-6">
                            <div class="description-block">
                                <span class="description-percentage text-red"><i class="fa fa-caret-down"></i></span>
                                <h5 class="description-header">{{ $day_transaction }}</h5>
                                <span class="description-text">{{ trans('fields.day') }}</span>
                            </div>
                            <!-- /.description-block -->
                        </div>
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.box-footer -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->

    <!-- Main row -->
    <div class="row">
        <!-- Left col -->
        <div class="col-md-8">

            <!-- TABLE: LATEST ORDERS -->
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans('fields.latest_transactions') }}</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                @php
                    $last_transaction = \App\Models\PaymentTransaction::orderBy('created_at', 'DESC')->take(10)->get();
                @endphp
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table no-margin">
                            <thead>
                            <tr>
                                <th>{{ trans('fields.invoice_number') }}</th>
                                <th>{{ trans('fields.company') }}</th>
                                <th>{{ trans('fields.payment_type') }}</th>
                                <th>{{ trans('fields.currency') }}</th>
                                <th>{{ trans('fields.amount') }}</th>
                                <th>{{ trans('fields.date') }}</th>
                                <th>{{ trans('fields.action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($last_transaction as $transaction)
                            <tr>
                                <td>{{ $transaction->invoice_number }}</td>
                                <td>{{ $transaction->company->name }}</td>
                                <td>{{ $transaction->payment_type }}</td>
                                <td>{{ $transaction->currency }}</td>
                                <td>{{ $transaction->amount }}</td>
                                <td> {{ \Carbon\Carbon::parse($transaction->created_at)->format('d M Y') }}</td>
                                <td><a href="{{ url('transaction/' . $transaction->id . '/invoice') }}" class="label label-success">{{ trans('fields.invoice') }}</a></td>
                            </tr>
                           @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- /.table-responsive -->
                </div>
                <!-- /.box-body -->
                <div class="box-footer clearfix">
                    <a href="{{ url('transaction') }}" class="btn btn-sm btn-info btn-flat pull-right">{{ trans('fields.view_all_transaction') }}</a>
                </div>
                <!-- /.box-footer -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->

        @php
            $no_paid = collect();
            $free = collect();
            $premium = collect();
            $unlimited = collect();
            $expired = collect();

            $companies = \App\Models\Company::all();

            foreach ($companies as $company) {
                if (!$company->companyPlan) {
                    $no_paid = $no_paid->push($company);
                } else {
                    $companyPlan = $company->companyPlan;
                    $plan = explode('_', explode('-', $companyPlan->company_plan_id)[1])[1];

                    if (date('Y-m-d') < date("Y-m-d", strtotime ($companyPlan->created_at ."-" . $companyPlan->free_days . " days"))) {
                        $expired = $expired->push($company);
                    } else {
                        if ($companyPlan->billing_status == 'free') {
                            $free = $free->push($company);
                        }
                        if ($companyPlan->billing_status == 'unlimited') {
                            $unlimited = $unlimited->push($company);
                        }
                    }
                }
            }
        @endphp
        <div class="col-md-4">
            <!-- /.info-box -->
            <div class="info-box bg-green">
                <span class="info-box-icon"><i class="ion ion-ios-pricetag-outline"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">{{ trans('fields.unlimited') }}</span>
                    <span class="info-box-number">{{ count($unlimited) }}</span>

                    <div class="progress">
                        <div class="progress-bar" style="width: {{ count($unlimited)/$company->count()*100 }}%"></div>
                    </div>
                    <span class="progress-description">
                    {{ count($unlimited)/$company->count()*100 }}%
                  </span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
            <!-- Info Boxes Style 2 -->
            <div class="info-box bg-yellow">
                <span class="info-box-icon"><i class="ion ion-ios-pricetag-outline"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">{{ trans('fields.free') }}</span>
                    <span class="info-box-number">{{ count($free) }}</span>

                    <div class="progress">
                        <div class="progress-bar" style="width: {{ count($free)/$company->count()*100 }}%"></div>
                    </div>
                    <span class="progress-description">
                    {{ count($free)/$company->count()*100 }}%
                  </span>
                </div>
                <!-- /.info-box-content -->
            </div>


            {{--<div class="info-box bg-aqua">--}}
                {{--<span class="info-box-icon"><i class="ion ion-ios-pricetag-outline"></i></span>--}}

                {{--<div class="info-box-content">--}}
                    {{--<span class="info-box-text">{{ trans('fields.business') }}</span>--}}
                    {{--<span class="info-box-number">{{ count($business) }}</span>--}}

                    {{--<div class="progress">--}}
                        {{--<div class="progress-bar" style="width: {{ count($business)/$company->count()*100 }}%"></div>--}}
                    {{--</div>--}}
                    {{--<span class="progress-description">--}}
                    {{--{{ count($business)/$company->count()*100 }}%--}}
                  {{--</span>--}}
                {{--</div>--}}
                {{--<!-- /.info-box-content -->--}}
            {{--</div>--}}

            <div class="info-box bg-red">
                <span class="info-box-icon"><i class="ion ion-ios-pricetag-outline"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">{{ trans('fields.expired') }}</span>
                    <span class="info-box-number">{{ count($expired) }}</span>

                    <div class="progress">
                        <div class="progress-bar" style="width: {{ count($expired)/$company->count()*100 }}%"></div>
                    </div>
                    <span class="progress-description">
                    {{ count($expired)/$company->count()*100 }}%
                  </span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
            <div class="info-box bg-purple">
                <span class="info-box-icon"><i class="ion ion-ios-pricetag-outline"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">{{ trans('fields.no_pay') }}</span>
                    <span class="info-box-number">{{ count($no_paid) }}</span>

                    <div class="progress">
                        <div class="progress-bar" style="width: {{ count($no_paid)/$company->count()*100 }}%"></div>
                    </div>
                    <span class="progress-description">
                    {{ count($no_paid)/$company->count()*100 }}%
                  </span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->

@endsection




@section('after_styles')

@endsection

@section('after_scripts')
    <script src="{{ asset('vendor/adminlte/bower_components/chart.js/Chart.js') }}"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    {{--<script src="{{ asset('vendor/adminlte/dist/js/pages/dashboard2.js') }}"></script>--}}
    <script>


        $(document).ready(function(){
            var salesChartCanvas = $('#salesChart').get(0).getContext('2d');
            var salesChart       = new Chart(salesChartCanvas);
            var salesChartData = {
                labels  : JSON.parse('{!! json_encode($month_labels) !!}'),
                datasets: [
                    {
                        label               : '{{ trans('fields.transaction') }}',
                        fillColor           : 'rgba(60,141,188,0.9)',
                        strokeColor         : 'rgba(60,141,188,0.8)',
                        pointColor          : '#3b8bba',
                        pointStrokeColor    : 'rgba(60,141,188,1)',
                        pointHighlightFill  : '#fff',
                        pointHighlightStroke: 'rgba(60,141,188,1)',
                        data                : JSON.parse('{!! json_encode($month_values) !!}')
                    },
                    // {
                    //     label               : 'Digital Goods',
                    //     fillColor           : 'rgba(60,141,188,0.9)',
                    //     strokeColor         : 'rgba(60,141,188,0.8)',
                    //     pointColor          : '#3b8bba',
                    //     pointStrokeColor    : 'rgba(60,141,188,1)',
                    //     pointHighlightFill  : '#fff',
                    //     pointHighlightStroke: 'rgba(60,141,188,1)',
                    //     data                : [28, 48, 40, 19, 86, 27, 90]
                    // }
                ]
            };
            var salesChartOptions = {
                // Boolean - If we should show the scale at all
                showScale               : true,
                // Boolean - Whether grid lines are shown across the chart
                scaleShowGridLines      : false,
                // String - Colour of the grid lines
                scaleGridLineColor      : 'rgba(0,0,0,.05)',
                // Number - Width of the grid lines
                scaleGridLineWidth      : 1,
                // Boolean - Whether to show horizontal lines (except X axis)
                scaleShowHorizontalLines: true,
                // Boolean - Whether to show vertical lines (except Y axis)
                scaleShowVerticalLines  : true,
                // Boolean - Whether the line is curved between points
                bezierCurve             : true,
                // Number - Tension of the bezier curve between points
                bezierCurveTension      : 0.3,
                // Boolean - Whether to show a dot for each point
                pointDot                : false,
                // Number - Radius of each point dot in pixels
                pointDotRadius          : 4,
                // Number - Pixel width of point dot stroke
                pointDotStrokeWidth     : 1,
                // Number - amount extra to add to the radius to cater for hit detection outside the drawn point
                pointHitDetectionRadius : 20,
                // Boolean - Whether to show a stroke for datasets
                datasetStroke           : true,
                // Number - Pixel width of dataset stroke
                datasetStrokeWidth      : 2,
                // Boolean - Whether to fill the dataset with a color
                datasetFill             : true,
                // String - A legend template
                {{--legendTemplate          : "<ul class='<%=name.toLowerCase()%>-legend'><% for (var i=0; i<datasets.length; i++){%><li><span style='background-color:<%=datasets[i].lineColor%>'></span><%=datasets[i].label%></li><%}%></ul>",--}}
                  // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
                maintainAspectRatio     : true,
                 // Boolean - whether to make the chart responsive to window resizing
                 responsive              : true
            };
            salesChart.Line(salesChartData, salesChartOptions);
        })
    </script>
@endsection
