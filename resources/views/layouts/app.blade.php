<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} @yield('title')</title>

    <!-- Favicons -->
    <link href="{{ asset('images/favicon32x32.png') }}" rel="shortcut icon" type="image/x-icon" sizes="32x32">
    <link href="{{ asset('images/favicon256x256.png') }}" rel="apple-touch-icon" type="image/x-icon" sizes="256x256">

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    @stack('scripts-top')
</head>
<body>
@yield('content')

<script src="{{ asset('js/app.js') }}"></script>

@stack('scripts-bottom')
</body>
</html>
