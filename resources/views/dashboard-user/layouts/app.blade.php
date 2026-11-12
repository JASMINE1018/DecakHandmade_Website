<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Decak Handmade')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #faf7f2;
            color: #2c2420;
        }
        .bg-terracotta {
            background-color: #c06547;
        }
        .bg-terracotta-hover:hover {
            background-color: #a8543b;
        }
        .text-terracotta {
            color: #c06547;
        }
        .border-terracotta {
            border-color: #c06547;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col antialiased selection:bg-[#f0c9b9] selection:text-[#2c2420]">
    @include('dashboard-user.partials.navbar')

    <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
        @yield('content')
    </main>

    @include('dashboard-user.partials.footer')

    @stack('modals')
    @stack('scripts')
</body>
</html>
