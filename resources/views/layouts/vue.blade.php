<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel=stylesheet>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css">
    <link href="{{ asset('css/kp-app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/pages/main.css') }}" rel="stylesheet">
    <link href="{{ asset('css/pages/kpPage.css') }}" rel="stylesheet">
    <title>КП генератор</title>
</head>
<body>
@yield('content')
<script src="{{ asset('js/kp-app.js') }}"></script>
</body>
</html>