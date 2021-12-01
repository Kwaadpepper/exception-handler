{{ $appName }} Exception Handler

{{ __('error_occured_in_app_check_trace', [
    'appName' => $appName,
]) }}
{{ __('check_trace') }}

{{ $ename }}
{{ $emessage }}
{{ __('uri') }} : {{ $uri }}
{{ __('date') }} : {{ date('Y-m-d H:i:s') }}
{{ __('code') }} : {{ $code }}
{{ __('file') }} : {{ $file }}
{{ __('line') }} : {{ $line }}

{{ __('anonymized_stack_trace') }} :
{!! nl2br($stackTrace) !!}
