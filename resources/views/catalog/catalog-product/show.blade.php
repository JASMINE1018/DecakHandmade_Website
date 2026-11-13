<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ ucwords(str_replace('-', ' ', $slug)) }} — Decak Handmade</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script>
        window.__PRODUCT_DETAIL_CONFIG__ = {
            appUrl: @json(url('/')),
            assetUrl: @json(asset('')),
            csrfToken: @json(csrf_token()),
            checkoutUrl: @json(route('user.checkout')),
            catalogUrl: @json(route('catalog.index')),
        };
        @if(!empty($productPayload))
        window.__PRODUCT_DETAIL_PRODUCT__ = @json($productPayload);
        @endif
    </script>

    @viteReactRefresh
    @vite(['resources/js/product-detail/main.jsx'])
</head>
<body class="bg-cream">
    <div id="product-detail-root"></div>

    <noscript>
        <p class="p-6 text-center text-sm text-ink">
            This page requires JavaScript to display product details.
        </p>
    </noscript>
</body>
</html>
