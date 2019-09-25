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
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <section class="content">
                        <div class="row">

                        </div>
                        <!-- /.row -->
                    </section>

                </div>
            </div>
        </div>
    </div>

@endsection
@section('after_styles')

@endsection
@section('after_scripts')

@endsection
