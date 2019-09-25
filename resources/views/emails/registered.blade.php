@component('mail::message')

@component('mail::button', ['url' => route('backpack.auth.register.confirm', ['code' => $code])])
    {{ trans('fields.your_code_is') }}: {{ $code }}
@endcomponent

@endcomponent
