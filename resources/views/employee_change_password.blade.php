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
    <div class="row">
        <div class="col-md-4">
            <form class="form" action="{{ route('employee_change_password', ['id' => $id]) }}" method="post">

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
                                $label = trans('backpack::base.new_password');
                                $field = 'new_password';
                            @endphp
                            <label class="required">{{ $label }}</label>
                            <input autocomplete="new-password" required class="form-control" type="password" name="{{ $field }}" id="{{ $field }}" value="" placeholder="{{ $label }}">
                        </div>

                        <div class="form-group">
                            @php
                                $label = trans('backpack::base.confirm_password');
                                $field = 'confirm_password';
                            @endphp
                            <label class="required">{{ $label }}</label>
                            <input autocomplete="new-password" required class="form-control" type="password" name="{{ $field }}" id="{{ $field }}" value="" placeholder="{{ $label }}">
                        </div>

                        <div class="form-group m-b-0">

                            <button type="submit" class="btn btn-success"><span class="ladda-label"><i class="fa fa-save"></i> {{ trans('backpack::base.change_password') }}</span></button>
                            <a href="{{ backpack_url() }}/employees" class="btn btn-default"><span class="ladda-label">{{ trans('backpack::base.cancel') }}</span></a>

                        </div>

                    </div>

                </div>

            </form>
        </div>
    </div>

@endsection



