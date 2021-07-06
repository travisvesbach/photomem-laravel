<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Styles -->
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">

        <!-- Scripts -->
        <script src="{{ mix('js/app.js') }}" defer></script>

        <!-- Prevent search engines from listing/ranking the site -->
        <meta name="robots" content="noindex,nofollow">

        @livewireStyles
    </head>
    <body>
        <header>
            <h1>
                <a href="/">PhotoMem</a>
            </h1>
            <div class="nav">
                <a href="/" class="nav-link">Home</a>
                <a href="{{ route('directories') }}" class="nav-link">Directories</a>
                <a href="{{ route('about') }}" class="nav-link">About</a>
            </div>
        </header>

        <div class="page-title">
            <h2>@yield('title')</h2>
        </div>

        <div class="main-content">
            @yield('content')
        </div>

        @livewireScripts
    </body>
</html>
