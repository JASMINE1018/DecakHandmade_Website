<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#F9F6F1">
    <title>Our Story — Decak Handmade</title>

    <script>
        window.__OUR_STORY_CONFIG__ = {
            homeUrl: @json(route('home')),
            catalogUrl: @json(route('catalog.index')),
            storyUrl: @json(route('story.index')),
            userUrl: @json(route('user.dashboard')),
            cartUrl: @json(route('user.cart')),
            testimonialUrl: @json(route('home').'#testimoni'),
            assetUrl: @json(asset('')),
        };
    </script>

    @viteReactRefresh
    @vite(['resources/js/our-story/main.jsx'])
</head>
<body class="bg-[#F9F6F1]">
    <div id="our-story-root"></div>

    <noscript>
        <p class="p-6 text-center text-sm text-[#5C554F]">
            This page requires JavaScript to display our story.
        </p>
    </noscript>
</body>
</html>
