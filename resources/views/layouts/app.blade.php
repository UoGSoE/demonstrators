<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>School of Engineering - Teaching Assistants</title>

    <!-- Styles -->
    <link href="{{ asset('css/bulmaswatch.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="/fa/css/font-awesome.min.css">
    <link href="{{ asset('css/demonstrators.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{!! asset('/css/datatables.min.css') !!}" />
    <link rel="shortcut icon" href="{!! asset('images/favicon.ico') !!}" />
    <link rel="stylesheet" href="{!! asset('/css/vue-multiselect.min.css') !!}" />
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">
            <a href="{{route('home')}}" class="navbar-item">
                <img src="{{asset('images/logo.gif')}}" alt="UOG-Logo">
                <p class="navbar-item">
                    School of Engineering - Teaching Assistants
                </p>
            </a>
            @if (Auth::user() and Auth::user()->is_admin)
                <a class="navbar-item" href="{{ route('admin.edit_contracts') }}">
                    <span class="icon"><i class="fa fa-chevron-right" aria-hidden="true"></i></span>
                    <span>Students</span>
                </a>
                <a class="navbar-item" href="{{ route('admin.staff.index') }}">
                    <span class="icon"><i class="fa fa-chevron-right" aria-hidden="true"></i></span>
                    <span>Staff</span>
                    </a>
                <a class="navbar-item" href="{{ route('admin.requests') }}">
                    <span class="icon"><i class="fa fa-chevron-right" aria-hidden="true"></i></span>
                    <span>Requests</span>
                </a>
                <a class="navbar-item" href="{{ route('import.index') }}">
                    <span class="icon"><i class="fa fa-chevron-right" aria-hidden="true"></i></span>
                    <span>Import</span>
                </a>
                <div class="navbar-item has-dropdown is-hoverable">
                    <a class="navbar-link">
                        <span class="icon"><i class="fa fa-chevron-right" aria-hidden="true"></i></span>
                        <span>Reports</span>
                    </a>
                    <div class="navbar-dropdown">
                        <a href="{{route('admin.reports.output1')}}" class="navbar-item">Full Data Set</a>
                        <a href="{{route('admin.reports.output2')}}" class="navbar-item">Applications (By Course)</a>
                        <a href="{{route('admin.reports.output3')}}" class="navbar-item">Working Students</a>
                        <a href="{{route('admin.reports.output4')}}" class="navbar-item">Working Students (By Course)</a>
                        <a href="{{route('admin.reports.output5')}}" class="navbar-item">Requests With No Applications</a>
                        <a href="{{route('admin.reports.output6')}}" class="navbar-item">Applications Older Than 3 Days</a>
                        <a href="{{route('admin.reports.output7')}}" class="navbar-item">Unaccepted Applications</a>
                    </div>
                </div>
            @endif
        </div>
        <div class="navbar-end">
            @if (Auth::guest())
                <a class="navbar-item" href="/login">Login</a>
            @else
                <form class="navbar-item" method="POST" action="/logout">
                    {{ csrf_field() }}
                    <button style="box-shadow:none;" class="button is-focus is-primary">Logout</button>
                </form>
            @endif
        </div>
    </nav>
    @include('partials.notifications')
    <section id="app" class="section">
        <noscript>
            This site needs javascript enabled in order to work
        </noscript>
    @yield('content')
    </section>
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{!! asset('/js/datatables.min.js') !!}"></script>
    <script src="{{ asset('js/demonstrators.js') }}"></script>
    @yield('scripts')
</body>
</html>
