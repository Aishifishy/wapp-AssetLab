<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'AssetLab') }}@yield('title')</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Alpine.js -->
        <script src="//unpkg.com/alpinejs" defer></script>
    </head>
    <body class="antialiased">
        <div class="min-h-screen bg-gradient-custom flex flex-col">
            @include('layouts.header')

            <!-- Page Content -->
            <main class="flex-grow">
                @yield('content')
            </main>
            
            @include('layouts.footer')
        </div>

        @stack('scripts')
    </body>
</html>