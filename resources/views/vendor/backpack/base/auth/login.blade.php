
@extends('frontend.template.layout')
@section('content')
    <section id="register-form" class="section gray-bg section-padded">
        <div class="container">
            <div class="row text-left title register-form-box">
                <form class="form-horizontal popup-form register_form" role="form" method="post" action="{{ route('backpack.auth.login') }}">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="col-xs-12" id="erroresFormOk">

                                </div>
                                <div class="col-xs-12" id="erroresFormKo">
                                    {{--@if ($errors->has('name'))--}}
                                    {{--<p>{{ $errors->first('name') }}</p>--}}
                                    {{--@endif--}}
                                    {{--@if ($errors->has(backpack_authentication_column()))--}}
                                    {{--<p>{{ $errors->first(backpack_authentication_column()) }}</p>--}}
                                    {{--@endif--}}
                                    {{--@if ($errors->has('password'))--}}
                                    {{--<p>{{ $errors->first('password') }}</p>--}}
                                    {{--@endif--}}
                                    {{--@if ($errors->has('password_confirmation'))--}}
                                    {{--<p>{{ $errors->first('password_confirmation') }}</p>--}}
                                    {{--@endif--}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label class="control-label">{{ config('backpack.base.authentication_column_name') }}</label>

                                <div>
                                    <input type="text" class="form-control  form-white" name="email" value="{{ old('email') }}">

                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <label class="control-label">{{ trans('backpack::base.password') }}</label>

                                <div>
                                    <input type="password" class="form-control  form-white" name="password">

                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row ">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="">
                                    <input class="btn btn-block btn-form btn-primary" id="submit_register" name="submit" type="submit" value="{{ trans('backpack::base.login') }}" >
                                </div>
                            </div>
                            <div class="form-group text-center">
                                <a href="{{ route('backpack.auth.password.reset') }}">{{ trans('backpack::base.forgot_your_password') }}</a>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </section>
@endsection

@section('after_scripts')
<script src="https://www.gstatic.com/firebasejs/4.6.2/firebase.js"></script>
<script>
    $(function() {
        var config = {
            apiKey: "AIzaSyBjpweFh63mmXB-5mOHmIVUfIrW0oN_G3A",
            authDomain: "fcc-book-trading-e8821.firebaseapp.com",
            databaseURL: "https://fcc-book-trading-e8821.firebaseio.com",
            projectId: "fcc-book-trading-e8821",
            storageBucket: "fcc-book-trading-e8821.appspot.com",
            messagingSenderId: "160797425400",
            appId: "1:160797425400:web:ec6e870a9fc3971f"
        };

        firebase.initializeApp(config);

        const messaging = firebase.messaging();
        messaging
            .requestPermission()
            .then(function () {
                // get the token in the form of promise
                return messaging.getToken()
            })
            .then(function (token) {
                console.log(token);
                $("input[name='device_token']").val(token);
            })
            .catch(function (err) {
                console.log("Unable to get permission to notify.", err);
            });
    })
</script>
@endsection
