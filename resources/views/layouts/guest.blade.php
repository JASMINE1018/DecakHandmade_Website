<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Masuk') — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-cream p-4 font-sans text-ink antialiased">
    <div class="pointer-events-none fixed inset-0 -z-10 bg-gradient-to-br from-brand-100/70 via-cream to-peach/70"></div>
    @yield('content')
</body>
</html>
