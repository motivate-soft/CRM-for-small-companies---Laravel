@extends('backpack::layout')

@section('header')
    <section class="content-header">
        <h1>
            {{ trans('fields.record') }}
            <small>{{ trans('fields.record_desc') }}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ backpack_url() }}">{{ config('backpack.base.project_name') }}</a></li>
            <li class="active">{{ trans('backpack::base.dashboard') }}</li>
        </ol>
    </section>
@endsection


@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="box">
                <div class="box-body record-buttons-holder">

                    @foreach($actions_options as $action)
                        <button type="button"
                                class="btn btn-block btn-warning btn-flat"
                                data-id="{{ $action->id }}"
                                data-key="{{ $action->key}}"
                                data-event-id="{{ $action->event_id}}"
                                data-type="{{ $action->type}}">
                            <i class="fa fa-sign-{{ $action->type }}"></i> {{ $action->name }}
                        </button>
                    @endforeach

                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="askGPS">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">ASK</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <p>Do you need to get GPS location?</p>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button id="btn_ok" type="button" class="btn btn-success" data-dismiss="modal" style="width: 100px">
                        OK
                    </button>
                    <button id="btn_no" type="button" class="btn btn-danger" data-dismiss="modal"
                            style="width: 100px; margin-left: 20px">NO
                    </button>
                </div>
            </div>

        </div>
    </div>
    </div>
@endsection

@push('after_scripts')
    <script>

        let gps;

        jQuery(document).ready(function ($) {

            function disableButtonsByAction(event_id, type) {

                $('.record-buttons-holder button').each(function (i, e) {
                    let el = $(e);

                    switch (type) {
                        case 'in':
                            if (el.data('type') == 'out') {
                                el.prop('disabled', false).removeClass('disabled');
                            } else {
                                el.prop('disabled', true).addClass('disabled');
                            }
                            break;
                        case 'out':
                            if (el.data('event-id') == event_id && el.data('type') == 'in') {
                                el.prop('disabled', false).removeClass('disabled');
                            } else {
                                el.prop('disabled', true).addClass('disabled');
                            }
                            break;
                        default:
                            if (i === 0) {
                                el.prop('disabled', false).removeClass('disabled');
                            } else {
                                el.prop('disabled', true).addClass('disabled');
                            }
                    }

                });

            }

            disableButtonsByAction('{{ $last_action_option_event_id }}', '{{ $last_action_option_type }}');

            $('.record-buttons-holder button').on('click', function () {

                let button = $(this);

                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function (position) {
                        let latitude = position.coords.latitude.toFixed(4);
                        let longitude = position.coords.longitude.toFixed(4);

                        gps = latitude.toString() + "," + longitude.toString();
                    });
                } else {
                    gps = "";
                }

                $.ajax({
                    method: 'POST',
                    url: "{{ url('record') }}",
                    data: {
                        option_id: button.data('id'),
                        location: gps
                    },
                    timeout: 15000,
                    success: function (response) {
                        if (response.status) {
                            disableButtonsByAction(button.data('event-id'), button.data('type'));
                        }
                    }
                });
            });
        });
    </script>
@endpush
