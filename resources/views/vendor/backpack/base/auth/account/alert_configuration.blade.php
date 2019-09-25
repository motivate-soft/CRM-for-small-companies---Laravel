@extends('backpack::layout')

@section('after_styles')
    <style media="screen">
        .backpack-profile-form .required::after {
            content: ' *';
            color: red;
        }
    </style>
@endsection

@section('header')
    <section class="content-header">

        <h1>
            {{ trans('backpack::base.my_account') }}
        </h1>

        <ol class="breadcrumb">

            <li>
                <a href="{{ backpack_url() }}">{{ config('backpack.base.project_name') }}</a>
            </li>

            <li>
                <a href="{{ route('backpack.account.info') }}">{{ trans('backpack::base.my_account') }}</a>
            </li>

            <li class="active">
                {{ trans('fields.alert') }}
            </li>

        </ol>

    </section>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-3">
            @include('backpack::auth.account.sidemenu')
        </div>
        <div class="col-md-6">

            <form class="form" action="{{ route('backpack.account.alert') }}" method="post">

                {!! csrf_field() !!}

                <div class="box padding-10">

                    <div class="box-body backpack-profile-form">

                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if ($errors->count())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $e)
                                        <li>{{ $e }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="form-group">
                            @php
                                $label = trans('fields.notify_punctuality_exceed_in');
                                $field = 'punctuality_time';
                            @endphp
                            <label>{{$label}}</label>
                            <select class="form-control select2" style="width: 100%;" name="{{$field}}">
                                @foreach($options as $option)
                                    <option value="{{$option}}" {{ ( $option == $punctuality_time) ? 'selected' : '' }}>{{$option}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            @php
                                $label = trans('fields.notify_employee');
                                $field = 'notify_employee_punctuality';
                            @endphp
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="{{$field}}" {{($notify_employee_punctuality == 1) ? 'checked' : ''}}>
                                    {{$label}}
                                </label>
                            </div>
                            @php
                                $label = trans('fields.notify_company');
                                $field = 'notify_company_punctuality';
                            @endphp
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="{{$field}}" {{($notify_company_punctuality == 1) ? 'checked' : ''}}>
                                    {{$label}}
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            @php
                                $label = trans('fields.notify_exceed_working_time_in');
                                $field = 'exceed_working_time';
                            @endphp
                            <label>{{$label}}</label>
                            <select class="form-control select2" style="width: 100%;" name="{{$field}}">
                                @foreach($options as $option)
                                    <option value="{{$option}}" {{ ( $option == $exceed_working_time) ? 'selected' : '' }}>{{$option}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            @php
                                $label = trans('fields.notify_employee');
                                $field = 'notify_employee_exceed';
                            @endphp
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="{{$field}}" {{($notify_employee_exceed == 1) ? 'checked' : ''}}>
                                    {{$label}}
                                </label>
                            </div>
                            @php
                                $label = trans('fields.notify_company');
                                $field = 'notify_company_exceed';
                            @endphp
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="{{$field}}" {{($notify_company_exceed == 1) ? 'checked' : ''}}>
                                    {{$label}}
                                </label>
                            </div>
                        </div>
                        <div class="form-group m-b-0">
                            <button type="submit" class="btn btn-success"><span class="ladda-label"><i class="fa fa-save"></i> {{ trans('backpack::base.save') }}</span></button>
                            <a href="{{ backpack_url() }}" class="btn btn-default"><span class="ladda-label">{{ trans('backpack::base.cancel') }}</span></a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
