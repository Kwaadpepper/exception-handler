<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>{{ $appName }} Exception Handler</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<body>
    <h1>{{ __('error_occured_in_app', ['appName' => $appName]) }}</h1>
    <p>{{ __('check_trace') }}</p>

    <h2>{{ $ename }}</h2>
    <h3>{!! nl2br(strip_tags(e($emessage))) !!}</h3>
    <p>{{ __('uri') }} : {{ $uri }}</p>
    <p>{{ __('date') }} : {{ date('Y-m-d H:i:s') }}</p>
    <p>{{ __('code') }} : {{ $code }}</p>
    <p>{{ __('file') }} : {{ $file }}</p>
    <p>{{ __('line') }} : {{ $line }}</p><br>
    <p>{{ __('anonymized_stack_trace') }} :</p>
    <p>{!! nl2br($stackTrace) !!}</p>
</body>

</html>
