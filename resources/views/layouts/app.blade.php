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
    <link rel="stylesheet" href="/fa/css/font-awesome.min.css">
    <link href="{{ asset('css/demonstrators.css') }}" rel="stylesheet">
</head>
<body>
    <nav class="nav">
        <div class="nav-left">
            <a class="nav-item" href="/">Demonstrators</a>
            @if (Auth::user() and Auth::user()->is_admin)
                <a class="nav-item" href="/admin/contracts">Contracts</a>
                <a class="nav-item" href="#">Import</a>
            @endif
        </div>
        <div class="nav-right">
            @if (Auth::guest())
                <a class="nav-item" href="/login">Login</a>
            @else
                <form class="nav-item" method="POST" action="/logout">
                    {{ csrf_field() }}
                    <button class="button is-primary">Logout</button>
                </form>
            @endif
        </div>
    </nav>
    <section id="app" class="section">
    @yield('content')
    </section>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/demonstrators.js') }}"></script>
    @yield('scripts')
</body>
</html>
