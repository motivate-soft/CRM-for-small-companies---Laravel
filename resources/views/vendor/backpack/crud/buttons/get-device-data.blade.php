@if (!$entry->mandatory)
    <a href="javascript:void(0)" onclick="getDeviceData(this)" data-route="{{ route('get_device_data', ['id' => $entry->getKey()]) }}" class="btn btn-xs btn-default" data-button-type="get_device_data"><i class="fa fa-download"></i> {{ trans('fields.get_data') }}</a>
@endif

<script>
    if (typeof getDeviceData != 'function') {
        $("[data-button-type=get_device_data]").unbind('click');

        function getDeviceData(button) {
            // e.preventDefault();
            var button = $(button);
            var route = button.attr('data-route');
            var row = $("#crudTable a[data-route='"+route+"']").closest('tr');

            $.ajax({
                url: route,
                type: 'GET',
                success: function(result) {
                    // Show an alert with the result
                    new PNotify({
                        title: "{{ trans('fields.device_get_confirmation_title') }}",
                        text: "{{ trans('fields.device_get_confirmation_message') }}",
                        type: "success"
                    });

                    // Hide the modal, if any
                    $('.modal').modal('hide');

                },
                error: function(result) {
                    // Show an alert with the result
                    new PNotify({
                        title: "{{ trans('backpack::crud.ajax_error_title') }}",
                        text: "{{ trans('backpack::crud.ajax_error_text') }}",
                        type: "warning"
                    });
                }
            });
        }
    }

</script>