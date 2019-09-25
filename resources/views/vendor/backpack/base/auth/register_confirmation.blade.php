@extends('backpack::layout_guest')

@section('content')
    <div class="row m-t-40">
        <div class="col-md-4 col-md-offset-4">
            <h3 class="text-center m-b-20">{{ trans('fields.confirm_registration') }}</h3>
            <div class="box">
                <div class="box-body">
                    <form class="col-md-12 p-t-10" role="form" method="POST" action="{{ route('backpack.auth.register.confirm') }}">
                        {!! csrf_field() !!}

                        <div class="form-group{{ $errors->has('registration_code') ? ' has-error' : '' }}">
                            <label class="control-label">{{ trans('fields.code') }}</label>

                            <div>
                                <input type="text" class="form-control" name="registration_code" value="{{ old('registration_code') ?? request()->get('code') ?? '' }}">

                                @if ($errors->has('registration_code'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('registration_code') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <div>
                                <button type="submit" class="btn btn-block btn-primary">
                                    {{ trans('backpack::base.register') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @if (backpack_users_have_email())
                <div class="text-center m-t-10"><a href="{{ route('backpack.auth.password.reset') }}">{{ trans('backpack::base.forgot_your_password') }}</a></div>
            @endif
            <div class="text-center m-t-10"><a href="{{ route('backpack.auth.login') }}">{{ trans('backpack::base.login') }}</a></div>
        </div>
    </div>
@endsection
