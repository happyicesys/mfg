<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

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
    <style>
        body {
            background-image: url("/img/background.jpg");
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
    </style>
    @livewireStyles
    @stack('styles')
    <link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet"/>
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
                    <main class="page-content" style="margin-top: 10px;">
                        @auth
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
    <script src="{{ asset('js/app.js') }}" defer></script>
    @livewireScripts
    @stack('scripts')
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>
</body>
</html>
