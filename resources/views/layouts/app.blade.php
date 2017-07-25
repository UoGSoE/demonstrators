<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Demonstrators') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/bulmaswatch.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="{{ asset('css/demonstrators.css') }}" rel="stylesheet">
</head>
<body>
    <nav class="nav">
        <div class="nav-left">
            <a class="nav-item" href="/">Demonstrators</a>
        </div>
        <div class="nav-right">
            @if (Auth::guest())
                <a class="nav-item" href="/login">Login</a>
            @else
                <a class="nav-item" href="/logout">Logout</a>
            @endif
        </div>
    </nav>
    <section class="section">
    @yield('content')
    </section>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script
      src="https://code.jquery.com/jquery-3.2.1.min.js"
      integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
      crossorigin="anonymous"></script>
    <script src="{{ asset('js/demonstrators.js') }}"></script>
</body>
</html>
