<div class="box box-primary">
    <div class="box-body">
        <div class="row">
            <form action="{{ url()->current() }}" method="get">
                <div class="col-lg-2 col-md-3 col-sm-3">
                    <div class="form-group">
                        <label>{{ trans('fields.employee') }}</label>
                        <select class="form-control select2" name="employee">
                            <option value="all">{{ trans('fields.all') }}</option>
                            @foreach(\App\Models\Employee::all() as $employee)
                                <option value="{{ $employee->id }}" {{ $employee->id == $filters['employee'] ? 'selected=selected' : '' }}>{{ $employee->user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-3">
                    <div class="form-group">
                        <label>{{ trans('fields.work_center') }}</label>
                        <select class="form-control select2" name="workcenter">
                            <option value="all">{{ trans('fields.all') }}</option>
                            @foreach(\App\Models\Workcenter::all() as $workcenter)
                                <option value="{{ $workcenter->id }}" {{ $workcenter->id == $filters['workcenter'] ? 'selected=selected' : '' }}>{{ $workcenter->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-3">
                    <div class="form-group">
                        <label>{{ trans('fields.date_range') }}</label>
                        <input class="form-control pull-right" id="daterangepicker" type="text" name="date_range">
                    </div>
                </div>
                <div class="col-lg-1 col-md-2 col-sm-3">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary form-control"><i class="fa fa-search" aria-hidden="true"></i></button>
                    </div>
                </div>
            </form>
        </div>

        <div class="row">
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="callout callout-purple bg-purple m-b-0">
                    <h4 class="m-b-0">{{ trans('fields.total_time') }}: {{ $total_time }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>

@push('after_styles')
    <link href="{{ asset('vendor/backpack/select2/select2.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('vendor/backpack/select2/select2-bootstrap-dick.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
@endpush

@push('after_scripts')
    <script src="{{ asset('vendor/backpack/select2/select2.js') }}"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
    <script>
        jQuery(document).ready(function($) {
            $('.select2').each(function (i, obj) {
                if (!$(obj).data("select2"))
                {
                    $(obj).select2({
                        allowClear: true,
                        closeOnSelect: false
                    });
                }
            });

            @php
                if($filters['date_range']) {
                    $start_date = trim(explode('-', $filters['date_range'])[0]);
                    $end_date = trim(explode('-', $filters['date_range'])[1]);
                }
            @endphp

            var dateRangeInput = $('#daterangepicker').daterangepicker({
                    "locale": {
                        // "format": "DD/MM/YYYY",
                        "firstDay": 1
                    },
                    timePicker: false,
                    ranges: {
                        '{{ trans('fields.today') }}': [moment().startOf('day'), moment().endOf('day')],
                        '{{ trans('fields.yesterday') }}': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        '{{ trans('fields.last_7_days') }}': [moment().subtract(6, 'days'), moment()],
                        '{{ trans('fields.last_30_days') }}': [moment().subtract(29, 'days'), moment()],
                        '{{ trans('fields.this_month') }}': [moment().startOf('month'), moment().endOf('month')],
                        '{{ trans('fields.last_month') }}': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    },
                    alwaysShowCalendars: true,
                    @if ($filters['date_range'])
                        startDate: moment("{{ $start_date }}"),
                        endDate: moment("{{ $end_date }}"),
                    @endif
                    autoUpdateInput: true
                });

        });
    </script>
@endpush