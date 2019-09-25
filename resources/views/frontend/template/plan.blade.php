
@php
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    $details = json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip={$ip}"));
    if ($details->geoplugin_status == '200') {
        $currency_code = $details->geoplugin_currencyCode;
        $symbol = $details->geoplugin_currencySymbol;
    } else {
        $currency_code = 'USD';
        $symbol = '$';
    }

    $plan = \App\Models\Currency::getPlanData($currency_code);
    $planlabels = \App\Models\Plan::getPlanLabelList();
    $plan_data = json_decode($plan->data, true);
    $currency_symbol = $plan->currency->symbol;

@endphp
<section id="plan" class="section section-padded">
    {{--<div class="cut cut-top-left"></div>--}}
    <div class="container">
        <h2 class="m-b-30">{{ trans('fields.make_plan') }}</h2>
        <div class="row">
            @for($i = 0; $i < count($plan_data); $i++)
                <div class="
                @if(count($plan_data) == 5)
                        col-md-5th-1 col-sm-4 col-md-offset-0 col-sm-offset-2
                @elseif(count($plan_data) == 4)
                        col-md-3
                @elseif(count($plan_data) == 3)
                        col-md-4
                @endif
                    ">
                    <div class="pricing-table-1 premium">
                        <div class="pricing-table-header text-center"><h2><strong>{{ $planlabels[$i] }}</strong></h2>
                            {{--<p>LOREM IPSUM DOLOR SIT</p>--}}
                            <i class="fa fa-star"></i>
                            @for($j = 0; $j < $i; $j++)
                                <i class="fa fa-star"></i>
                            @endfor
                        </div>
                        <div class="pricing-table-body">
                            <ul class="pricing-table-ul">
                                <li><i class="fa fa-check"></i> <strong>{{ $plan_data[$i]['min'] }} ~ {{ $plan_data[$i]['max'] }}</strong> {{  trans('fields.employees') }}</li>
                                <li><i class="fa fa-check"></i> <strong>{{ $plan->free_month }} {{ trans('fields.months') }}</strong> {{ trans('fields.free') }}</li>
                            </ul>
                            <div class="price">{{ $currency_symbol }}{{ $plan_data[$i]['price'] }}
                                <small>{{ trans('fields.per_month') }}</small>
                            </div>
                            <a href="{{ url('frontend/addplan/' . $plan->id . '/' . $i) }}" class="btn view-more" style="text-transform: uppercase;">{{ trans('fields.start') }}</a>
                        </div>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</section>
