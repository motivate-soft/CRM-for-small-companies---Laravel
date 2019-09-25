<!-- schedule -->
<div @include('crud::inc.field_wrapper_attributes') >
    <hr>
    <label>{{ trans('fields.month') }}</label>
    <div class="row">
        @foreach(\App\Models\Action::getMonths() as $key => $month)
            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
                <div class="checkbox">
                    <label>
                        <input type="checkbox"
                               @if(isset($field['value']['months']) && in_array($key, $field['value']['months'])) checked @endif
                               name="{{ $field['name'] }}[months][]"
                               value="{{ $key }}">{{ $month }}
                    </label>
                </div>
            </div>
        @endforeach
    </div>
    <hr>
    <label>{{ trans('fields.day') }}</label>

        @foreach(\App\Models\Action::getDaysOfWeek() as $key => $day)
        <div class="row">
            <div class="col-lg-2 col-md-2">
                <div class="checkbox">
                    <label>
                        <input type="checkbox"
                               @if (old('data.days.'.$key.'.check') ?? $field['value']['days'][$key]['check'] ?? false)
                                checked="checked"
                               @endif
                               name="{{ $field['name'] }}[days][{{ $key }}][check]">{{ $day }}
                    </label>
                </div>
            </div>

            <div class="col-lg-10 col-md-10">
                @for($i = 0; $i < 3; $i++)
                    <div class="col-lg-4 col-md-12">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                                <input type="time"
                                       class="form-control"
                                       value="{{ old("data.days.$key.times.$i.from") ?? $field['value']['days'][$key]['times'][$i]['from'] ?? '00:00' }}"
                                       name="{{ $field['name'] }}[days][{{ $key }}][times][{{ $i }}][from]">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                                <input type="time"
                                       class="form-control"
                                       value="{{ old("data.days.$key.times.$i.to") ?? $field['value']['days'][$key]['times'][$i]['to'] ?? '00:00' }}"
                                       name="{{ $field['name'] }}[days][{{ $key }}][times][{{ $i }}][to]">
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    @endforeach
    <hr>
</div>