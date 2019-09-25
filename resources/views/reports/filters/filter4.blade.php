<div class="box box-primary">
    <div class="box-body">
        <div class="row">
            <form action="" method="get">
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
                        <label>{{ trans('fields.schedule') }}</label>
                        <select class="form-control select2" name="schedule">
                            @foreach(\App\Models\Schedule::all() as $schedule)
                                <option value="{{ $schedule->id }}" {{ $schedule->id == $filters['schedule'] ? 'selected=selected' : '' }}>{{ $schedule->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-3">
                    <div class="form-group">
                        <label>{{ trans('fields.date') }}</label>
                        <input class="form-control pull-right" id="datepicker" type="text" name="date" autocomplete="off" value="{{ $filters['date'] }}">
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
    </div>
</div>

@push('after_styles')
    <link href="{{ asset('vendor/backpack/select2/select2.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('vendor/backpack/select2/select2-bootstrap-dick.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css') }}">
@endpush

@push('after_scripts')
    <script src="{{ asset('vendor/backpack/select2/select2.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
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

            var dateInput = $('#datepicker').datepicker({
                format: "mm/yyyy",
                weekStart: 1,
                startDate : new Date(2009,1-1,1,0,0,0),
                endDate : new Date(),
                startView: 1,
                minViewMode: 1,
                maxViewMode: 2,
                language: "en",
                autoclose: true
            })

        });
    </script>
@endpush