<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @php
        $profile = \App\Models\Profile::where('is_primary', 1)->first();
    @endphp
    <style>
        body {
            background-image: url("{{ $profile->profileSetting->theme_background_url}}");
            background-repeat: no-repeat;
            background-size:cover;
            max-width: 100%;
            max-height: 100%;
            height: auto;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        td, th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
            /* font-size: 13px; */
        }
        th {
            background-color:black;
            color:white;
        }
        th:first-child, td:first-child {
            position:sticky;
            left:0px;
        }
        td:first-child {
            background-color:lightgrey;
        }

        .chiller-theme .sidebar-wrapper {
            /* background: #31353D; */
            /* background: #5a889d; */
            background: {{$profile->profileSetting->theme_sidebar_background_color}};
            color: lightgrey;
            /* color: black; */
        }

        .chiller-theme .sidebar-wrapper .sidebar-header,
        .chiller-theme .sidebar-wrapper .sidebar-search,
        .chiller-theme .sidebar-wrapper .sidebar-menu {
            border-top: 1px solid #3a3f48;
        }

        .chiller-theme .sidebar-wrapper .sidebar-header .user-info .user-role,
        .chiller-theme .sidebar-wrapper .sidebar-header .user-info .user-status,
        .chiller-theme .sidebar-wrapper .sidebar-search input.search-menu,
        .chiller-theme .sidebar-wrapper .sidebar-search .input-group-text,
        .chiller-theme .sidebar-wrapper .sidebar-brand>a,
        .chiller-theme .sidebar-wrapper .sidebar-menu ul li a,
        .chiller-theme .sidebar-footer>a {
            color: #ffffff;
        }

        .chiller-theme .sidebar-wrapper .sidebar-menu ul li:hover>a,
        .chiller-theme .sidebar-wrapper .sidebar-menu ul li.active>a,
        .chiller-theme .sidebar-wrapper .sidebar-menu  ul li.sidebar-dropdown.active>a,
        .chiller-theme .sidebar-wrapper .sidebar-header .user-info,
        .chiller-theme .sidebar-wrapper .sidebar-brand>a:hover,
        .chiller-theme .sidebar-footer>a:hover i {
            /* color: #00A3E0; */
            /* color: black; */
            color: {{$profile->profileSetting->theme_sidebar_font_color}};
        }

        .page-wrapper.chiller-theme.toggled #close-sidebar:hover {
            /* color: #00A3E0; */
            /* color: black; */
            color: {{$profile->profileSetting->theme_sidebar_font_color}};
        }

        .chiller-theme .sidebar-wrapper ul li:hover a i,
        .chiller-theme .sidebar-wrapper .sidebar-dropdown .sidebar-submenu li a:hover:before,
        .chiller-theme .sidebar-wrapper .sidebar-search input.search-menu:focus+span,
        .chiller-theme .sidebar-wrapper .sidebar-menu .sidebar-dropdown.active a i {
            /* color: #00A3E0; */
            /* color: black; */
            color: {{$profile->profileSetting->theme_sidebar_font_color}};
            text-shadow:0px 0px 10px rgba(104, 208, 41, 0.6);
        }
    </style>
    @livewireStyles
    @stack('styles')
</head>
<body>
    <div id="app">
        @include('layouts.topnav')

        {{-- <div class="body"> --}}
            @auth
            <div class="page-wrapper chiller-theme toggled">
                <a id="show-sidebar" class="btn btn-sm btn-dark d-flex align-items-center justify-content-center" href="#">
                    <i class="fas fa-bars"></i>
                </a>
                <div class="container-fluid">
                    @include('layouts.sidenav')

            @else
                <div class="container-fluid">
            @endauth
                    <main class="page-content" style="margin-top: 3px;">
                        @auth
                            <div class="row">
                                <span class="ml-auto badge badge-pill badge-light mr-3 mb-2" style="font-size: 18px;">
                                    <i class="fas fa-user"></i>
                                    {{auth()->user()->name}}
                                </span>
                            </div>
                            {{ $slot }}
                        @else
                            @yield('content')
                        @endauth
                    </main>
                </div>
                </div>
            </div>
        {{-- </div> --}}
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    @livewireScripts
    @livewireChartsScripts
    @stack('scripts')
</body>
</html>
