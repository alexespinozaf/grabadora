<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge"> <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-control" content="max-age=0">

    <meta http-equiv="origin-trial" content="Ak9s/YIB2dWwMYnD18RemABxvIETHvGN9/AaDBEpR9fCEg7VPURwMfVMVeMW6HXqKTLChC/1u4AIZSV81Bsn0A4AAAB1eyJvcmlnaW4iOiJodHRwczovL2VvbC5jZnJkLmNsOjQ0MyIsImZlYXR1cmUiOiJVbnJlc3RyaWN0ZWRTaGFyZWRBcnJheUJ1ZmZlciIsImV4cGlyeSI6MTcwOTg1NTk5OSwiaXNTdWJkb21haW4iOnRydWV9">
{{--    <link rel="stylesheet" href="{{ asset('/css/app.css') }}">--}}
    <link rel="stylesheet" href="{{ asset('/css/recorder.css') }}">

    <title>{{ config('app.name', 'Recorder') }}</title>

    <link rel="preconnect" href="//fonts.gstatic.com">
    <link rel="stylesheet" media="all" href="//fonts.googleapis.com/css2?family=Barlow:wght@200;300;400;500;600;700;800&display=swap">
    <link rel="stylesheet" media="all" href="//fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" media="all" href="//cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" media="all" href="//cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    @yield('css')
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</head>
<body>
    @yield('content')

    @yield('js')
</body>
</html>
