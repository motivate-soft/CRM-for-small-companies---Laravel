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
        $strip_msg = \Illuminate\Support\Facades\Session::get('strip');
        if ($strip_msg){
            if ($strip_msg == 'success') {
                \Prologue\Alerts\Facades\Alert::success('Your payment successfully transferred. You can start now working with this plan.');

            } else {
                \Prologue\Alerts\Facades\Alert::error('There is some problem in your payment.');
            }
            \Illuminate\Support\Facades\Session::forget('strip');
        }
        $check_user = backpack_user()->company;
        $subscriptions = \Illuminate\Support\Facades\Session::get('plan_id');
    @endphp
    <div class="row">
        <div class="col-md-12">
            {{--<div class="alert alert-info">--}}
                {{--Please complete your profile before start working.--}}
            {{--</div>--}}
            <form class="form" action="{{ route('backpack.account.info') }}" method="post"
                  enctype="multipart/form-data">

                {!! csrf_field() !!}

                <div class="box padding-10">
                    <div class="box-body backpack-profile-form">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="required">{{ trans('backpack::base.name') }}</label>
                                    <input required class="form-control" type="text" name="name"
                                           value="{{ old('name') ? old('name') : $user->name }}">
                                </div>
                                <div class="form-group">
                                    <label
                                        class="required">{{ config('backpack.base.authentication_column_name') }}</label>
                                    <input required class="form-control"
                                           type="{{ backpack_authentication_column()=='email'?'email':'text' }}"
                                           name="{{ backpack_authentication_column() }}"
                                           value="{{ old(backpack_authentication_column()) ? old(backpack_authentication_column()) : $user->{backpack_authentication_column()} }}">
                                </div>
                                {{--<div class="form-group">--}}
                                    {{--<label>{{ trans('fields.language') }}</label>--}}
                                    {{--<select name="language_id" class="form-control">--}}
                                        {{--@foreach(\App\Models\Language::getActiveLanguagesArray() as $key => $item)--}}
                                            {{--<option--}}
                                                {{--@if( (!$user->company->language_id && $loop->first) || ($item['id'] == $user->company->language_id) )--}}
                                                {{--selected--}}
                                                {{--@endif--}}
                                                {{--value="{{ $item['id'] }}">{{ $item['name'] }}--}}
                                            {{--</option>--}}
                                        {{--@endforeach--}}
                                    {{--</select>--}}
                                {{--</div>--}}
                                {{--<div class="form-group">--}}
                                    {{--<label>{{ trans('fields.timezone') }}</label>--}}
                                    {{--<select name="timezone" class="form-control">--}}
                                        {{--@foreach(timezone_identifiers_list() as $key => $item)--}}
                                            {{--<option--}}
                                                {{--@if( (!$user->company->timezone && $loop->first) || ($item == $user->company->timezone) )--}}
                                                {{--selected--}}
                                                {{--@endif--}}
                                                {{--value="{{ $item }}">{{ $item }}--}}
                                            {{--</option>--}}
                                        {{--@endforeach--}}
                                    {{--</select>--}}
                                {{--</div>--}}
                                <div class="form-group">
                                    <label>{{ trans('fields.currency') }}</label>
                                    <select name="currency_id" class="form-control">
                                        @foreach(\App\Models\Currency::getCurrencyList() as $currency_item)
                                            <option
                                                @if( (!$user->company->currency && $loop->first) || ($user->company->currency && $currency_item->id == $user->company->id) )
                                                selected
                                                @endif
                                                value="{{ $currency_item->id }}">{{ $currency_item->short_key }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">


                                <div class="form-group">
                                    <label>{{ trans('fields.vat_number') }}</label>
                                    <input class="form-control" type="text" name="vat_number"
                                           value="{{ old('vat_number') ?: $user->company->vat_number }}" required>
                                </div>
                                <div class="form-group">
                                    <label>{{ trans('fields.address') }}</label>
                                    <input class="form-control" type="text" name="address"
                                           value="{{ old('address') ?: $user->company->address }}" required>
                                </div>
                                <div class="form-group">
                                    <label>{{ trans('fields.country') }}</label>
                                    <input class="form-control" type="text" name="country"
                                           value="{{ old('country') ?: $user->company->country }}" required>
                                </div>
                                <div class="form-group">
                                    <label>{{ trans('fields.signatory') }}</label>
                                    <input class="form-control" type="file" name="signatory_file">
                                    <input type="hidden" name="signatory"
                                           value="{{ old('signatory') ?: $user->company->signatory }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group m-b-0">
                                    <button type="submit" class="btn btn-success"><span class="ladda-label"><i
                                                class="fa fa-save"></i> {{ trans('backpack::base.save') }}</span>
                                    </button>
                                    <a href="{{ backpack_url() }}" class="btn btn-default"><span
                                            class="ladda-label">{{ trans('backpack::base.cancel') }}</span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection


@section('after_styles')

@endsection



@section('after_scripts')

@endsection
