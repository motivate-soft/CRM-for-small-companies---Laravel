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
            {{ trans('backpack::base.update_account_info') }}
        </li>

    </ol>

</section>
@endsection

@section('content')
<div class="row">
    <div class="col-md-3">
        @include('backpack::auth.account.sidemenu')
    </div>
    <div class="col-md-9">

        <form class="form" action="{{ route('backpack.account.info') }}" method="post" enctype="multipart/form-data">

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

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required">{{ trans('backpack::base.name') }}</label>
                                <input required class="form-control" type="text" name="name" value="{{ old('name') ? old('name') : $user->name }}">
                            </div>
                            <div class="form-group">
                                {{--<label class="required">{{ config('backpack.base.authentication_column_name') }}</label>--}}
                                <label class="required">{{ trans('fields.email') }}</label>
                                <input required class="form-control" type="{{ backpack_authentication_column()=='email'?'email':'text' }}" name="{{ backpack_authentication_column() }}" value="{{ old(backpack_authentication_column()) ? old(backpack_authentication_column()) : $user->{backpack_authentication_column()} }}">
                            </div>

                            @if(backpack_user()->role == \App\User::ROLE_COMPANY)
                                <div class="form-group">
                                    <label>{{ trans('fields.language') }}</label>
                                    <select name="language_id" class="form-control">
                                        @foreach(\App\Models\Language::getActiveLanguagesArray() as $key => $item)
                                            <option
                                                @if( (!$user->company->language_id && $loop->first) || ($item['id'] == $user->company->language_id) )
                                                    selected
                                                @endif
                                                value="{{ $item['id'] }}">{{ $item['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>{{ trans('fields.timezone') }}</label>
                                    <select name="timezone" class="form-control">
                                        @foreach(timezone_identifiers_list() as $key => $item)
                                            <option
                                                    @if( (!$user->company->timezone && $loop->first) || ($item == $user->company->timezone) )
                                                    selected
                                                    @endif
                                                    value="{{ $item }}">{{ $item }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @elseif(backpack_user()->role == \App\User::ROLE_EMPLOYEE)
                                <div class="form-group">
                                    <label>{{ trans('fields.language') }}</label>
                                    <select name="language_id" class="form-control">
                                        @foreach(\App\Models\Language::getActiveLanguagesArray() as $key => $item)
                                            <option
                                                    @if( (!$user->employee->language_id && $loop->first) || ($item['id'] == $user->employee->language_id) )
                                                    selected
                                                    @endif
                                                    value="{{ $item['id'] }}">{{ $item['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                {{-- Photo --}}
                                <div class="form-group">
                                    <label>{{ trans('fields.photo') }}</label>
                                    <input class="form-control" type="file" name="photo_file">
                                    <input type="hidden" name="photo" value="{{ old('photo') ?: $user->employee->photo }}">
                                    @if($user->employee->photo)
                                        <div class="col-md-12 m-t-10 img-signatory-holder">
                                            <div class="row">
                                                <img src="{{ $user->employee->photo }}" alt="Photo" class="img-signatory img-responsive img-thumbnail col-md-6 m-b-10">
                                                <div class="col-md-6">
                                                    <button type="button" class="btn btn-danger btn-xs btn-remove-signatory">{{ trans('fields.remove') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        @if(backpack_user()->role == \App\User::ROLE_COMPANY)
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ trans('fields.vat_number') }}</label>
                                    <input class="form-control" type="text" name="vat_number" value="{{ old('vat_number') ?: $user->company->vat_number }}">
                                </div>
                                <div class="form-group">
                                    <label>{{ trans('fields.address') }}</label>
                                    <input class="form-control" type="text" name="address" value="{{ old('address') ?: $user->company->address }}">
                                </div>
                                <div class="form-group">
                                    <label>{{ trans('fields.country') }}</label>
                                    <input class="form-control" type="text" name="country" value="{{ old('country') ?: $user->company->country }}">
                                </div>
                                <div class="form-group">
                                    <label>{{ trans('fields.signatory') }}</label>
                                    <input class="form-control" type="file" name="signatory_file">
                                    <input type="hidden" name="signatory" value="{{ old('signatory') ?: $user->company->signatory }}">
                                    @if($user->company->signatory)
                                        <div class="col-md-12 m-t-10 img-signatory-holder">
                                            <div class="row">
                                                <img src="{{ $user->company->signatory }}" alt="" class="img-signatory img-responsive img-thumbnail col-md-6 m-b-10">
                                                <div class="col-md-6">
                                                    <button type="button" class="btn btn-danger btn-xs btn-remove-signatory">{{ trans('fields.remove') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>{{ trans('fields.access_token') }}</label>
                                    <input class="form-control" type="text" readonly value="{{ $user->company->access_company_token }}">
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group m-b-0">
                                <button type="submit" class="btn btn-success"><span class="ladda-label"><i class="fa fa-save"></i> {{ trans('backpack::base.save') }}</span></button>
                                <a href="{{ backpack_url() }}" class="btn btn-default"><span class="ladda-label">{{ trans('backpack::base.cancel') }}</span></a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </form>

    </div>
</div>
@endsection

@push('after_scripts')
    <script>
        jQuery(document).ready(function($) {
            $('.btn-remove-signatory').on('click', function () {

                let button = $(this);

                $('input[name=signatory]').val('');
                $('.img-signatory-holder').remove();

            });
        });
    </script>
@endpush