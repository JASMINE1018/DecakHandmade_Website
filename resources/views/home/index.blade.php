@extends('layouts.catalog')

@section('title', 'Made with Love')

@push('head')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400..700;1,9..144,400..700&family=Lora:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">
@endpush

@section('content')

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{--  HERO                                                      --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <section class="relative overflow-hidden">
        {{-- Decorative background --}}
        <div aria-hidden="true" class="pointer-events-none absolute inset-0">
            <div class="absolute -left-32 -top-32 h-96 w-96 rounded-full bg-brand-100/70 blur-3xl"></div>
            <div class="absolute -right-24 top-24 h-[28rem] w-[28rem] rounded-full bg-peach/60 blur-3xl"></div>
            <div class="absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-cream to-transparent"></div>
            <div class="absolute inset-0 opacity-[0.35]"
                 style="background-image: radial-gradient(#d9c9b6 1px, transparent 1px); background-size: 22px 22px; mask-image: radial-gradient(ellipse at 20% 30%, black, transparent 70%);"></div>
        </div>

        <div class="relative mx-auto grid max-w-7xl grid-cols-1 gap-12 px-4 pb-16 pt-12 sm:px-6 lg:grid-cols-[1.03fr_0.97fr] lg:items-center lg:gap-16 lg:px-8 lg:pb-24 lg:pt-16"
             x-data="heroCarousel({{ count($heroSlides) }})">

            {{-- ── Left: Copy ── --}}
            <div class="relative z-10 min-w-0">
                <span class="inline-flex items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.24em] text-brand-600">
                    <span class="h-px w-8 bg-brand-400"></span>
                    Made with love
                </span>

                <h1 class="mt-6 font-lora text-[2.7rem] leading-[1.03] tracking-[-0.02em] text-ink sm:text-6xl lg:text-[4.2rem]">
                    An Aesthetic Touch <span class="italic">&amp; the Warmth</span> of Handcrafted Pieces for Your Special Moments
                </h1>

                <p class="mt-6 max-w-xl text-base leading-relaxed text-muted sm:text-lg">
                    A collection of exclusive handmade products made with care and love.
                </p>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
                    <a href="{{ route('catalog.index') }}"
                       class="group inline-flex items-center justify-center gap-2.5 rounded-full bg-brand-600 px-7 py-3.5 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition-all hover:-translate-y-0.5 hover:bg-brand-700 hover:shadow-xl">
                        Start Shopping
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 transition-transform group-hover:translate-x-1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>

                    <a href="#our-story"
                       class="inline-flex items-center justify-center gap-2.5 rounded-full border border-ink/20 bg-transparent px-7 py-3.5 text-sm font-semibold text-ink transition-colors hover:border-brand-500 hover:bg-white/70 hover:text-brand-700">
                        About Decak
                    </a>
                </div>

                {{-- Slider dots --}}
                <div class="mt-9 flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        @foreach ($heroSlides as $index => $slide)
                            <button type="button"
                                    @click="go({{ $index }})"
                                    aria-label="Go to slide {{ $index + 1 }}"
                                    :class="active === {{ $index }} ? 'w-8 bg-brand-600' : 'w-2 bg-brand-200 hover:bg-brand-300'"
                                    class="h-2 rounded-full transition-all duration-300 {{ $index === 0 ? 'w-8 bg-brand-600' : 'w-2 bg-brand-200' }}"></button>
                        @endforeach
                    </div>
                    <span class="text-[11px] font-medium uppercase tracking-[0.2em] text-muted">
                        <span x-text="String(active + 1).padStart(2, '0')">01</span>
                        <span class="text-line">/</span>
                        {{ str_pad((string) count($heroSlides), 2, '0', STR_PAD_LEFT) }}
                    </span>
                </div>
            </div>

            {{-- ── Right: Image carousel ── --}}
            <div class="relative mx-auto w-full min-w-0 max-w-[34rem] lg:max-w-none">
                <div class="hero-arch relative overflow-hidden border border-white/80 bg-sand shadow-[0_40px_90px_-45px_rgba(90,44,32,0.6)]">
                    <div class="relative aspect-[4/5] w-full">
                        @foreach ($heroSlides as $index => $slide)
                            <img src="{{ $slide['image'] }}"
                                 alt="{{ $slide['title'] }} — {{ $slide['caption'] }}"
                                 class="absolute inset-0 h-full w-full object-cover transition-all duration-700 ease-out {{ $index === 0 ? 'opacity-100' : 'opacity-0' }}"
                                 :class="active === {{ $index }} ? 'opacity-100 scale-100' : 'opacity-0 scale-[1.04]'">
                        @endforeach

                        {{-- Soft readability gradient --}}
                        <div class="absolute inset-x-0 bottom-0 h-2/5 bg-gradient-to-t from-ink/60 via-ink/10 to-transparent"></div>

                        {{-- Slide caption --}}
                        @foreach ($heroSlides as $index => $slide)
                            <div x-show="active === {{ $index }}" x-cloak x-transition.opacity.duration.500ms
                                 class="absolute bottom-6 left-6 right-24 text-white">
                                <p class="text-[10px] font-semibold uppercase tracking-[0.24em] text-white/70">Featured collection</p>
                                <p class="mt-1 font-serif text-xl leading-tight sm:text-2xl">{{ $slide['title'] }}</p>
                                <p class="mt-1 text-xs text-white/75">{{ $slide['caption'] }}</p>
                            </div>
                        @endforeach

                        {{-- Next / previous controls --}}
                        <div class="absolute bottom-5 right-5 flex items-center gap-2">
                            <button type="button" @click="prev()" aria-label="Previous slide"
                                    class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/40 bg-white/15 text-white backdrop-blur transition-colors hover:bg-white hover:text-ink">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                                </svg>
                            </button>
                            <button type="button" @click="next()" aria-label="Next slide"
                                    class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/40 bg-white/15 text-white backdrop-blur transition-colors hover:bg-white hover:text-ink">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{--  VALUE PROPOSITION & STORY                                 --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <section id="our-story" class="border-y border-line/70 bg-white/60">
        <div class="mx-auto grid max-w-7xl grid-cols-1 gap-10 px-4 py-16 sm:px-6 sm:py-24 lg:grid-cols-[0.92fr_1.08fr] lg:items-center lg:gap-16 lg:px-8">

            {{-- Left image --}}
            <div class="relative mx-auto w-full min-w-0 max-w-lg lg:max-w-none">
                <div class="overflow-hidden rounded-[2rem] border border-white bg-sand shadow-[0_30px_70px_-45px_rgba(90,44,32,0.5)]">
                    <img src="{{ asset('images/home/story-bag.jpg') }}"
                         alt="A handcrafted woven bag in warm terracotta tones"
                         class="aspect-[4/5] w-full object-cover">
                </div>

                <div aria-hidden="true" class="absolute -right-4 -top-4 -z-10 h-28 w-28 rounded-full bg-peach/80 blur-2xl"></div>
            </div>

            {{-- Right copy --}}
            <div class="min-w-0">
                <h2 class="max-w-xl font-serif text-4xl leading-[1.08] tracking-tight text-ink sm:text-5xl">
                    Made Wholeheartedly from Selected Materials
                </h2>

                <p class="mt-5 max-w-xl text-base leading-relaxed text-muted">
                    At Decak Handmade, every piece is crafted with precision, creativity, and attention to every detail. We believe that handmade products are more than just items; they are creations that carry a story and meaning.
                </p>

                <div class="mt-8 grid gap-x-8 gap-y-6 sm:grid-cols-3">
                    <div>
                        <span class="flex items-center gap-2.5 text-brand-500">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                            </svg>
                            <span class="h-px w-8 bg-brand-400"></span>
                        </span>
                        <h3 class="mt-3 text-sm font-semibold text-ink">Made with Heart</h3>
                        <p class="mt-1.5 text-xs leading-relaxed text-muted">Every product is a reflection of dedication and great care.</p>
                    </div>

                    <div>
                        <span class="flex items-center gap-2.5 text-brand-500">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z" />
                            </svg>
                            <span class="h-px w-8 bg-brand-400"></span>
                        </span>
                        <h3 class="mt-3 text-sm font-semibold text-ink">Meaningful Details</h3>
                        <p class="mt-1.5 text-xs leading-relaxed text-muted"> Prioritizing the finest details in every process.</p>
                    </div>

                    <div>
                        <span class="flex items-center gap-2.5 text-brand-500">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.05 4.575a1.575 1.575 0 1 0-3.15 0v3m3.15-3v-1.5a1.575 1.575 0 0 1 3.15 0v2.925m-6.3 1.575H5.475a1.575 1.575 0 1 0 0 3.15h1.425m1.575-1.575v-1.5a1.575 1.575 0 0 1 3.15 0v1.5m-4.725 0h4.725m-4.725 0H4.875A2.625 2.625 0 0 0 2.25 15v.75a6.75 6.75 0 0 0 6.75 6.75h5.25A6.75 6.75 0 0 0 21 15.75V15a2.625 2.625 0 0 0-2.625-2.625h-2.1" />
                            </svg>
                            <span class="h-px w-8 bg-brand-400"></span>
                        </span>
                        <h3 class="mt-3 text-sm font-semibold text-ink">A Personal Work</h3>
                        <p class="mt-1.5 text-xs leading-relaxed text-muted">Creating products that align with your unique story.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{--  FEATURED PRODUCTS                                         --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <section id="featured" class="relative overflow-hidden py-16 sm:py-24">
        <div aria-hidden="true" class="pointer-events-none absolute -right-24 top-10 h-80 w-80 rounded-full bg-brand-50 blur-3xl"></div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="font-lora text-4xl leading-tight tracking-tight text-ink sm:text-5xl">
                        Discover Your Favorite Works
                    </h2>
                    <p class="mt-3 max-w-xl text-sm leading-relaxed text-muted">
                        Every detail is crafted with love, to accompany your special story.
                    </p>
                </div>

                <a href="{{ route('catalog.index') }}" class="group inline-flex items-center gap-2 text-sm font-semibold text-brand-700 transition-colors hover:text-brand-600">
                    See all
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 transition-transform group-hover:translate-x-1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            </div>

            <div class="mt-10" x-data="productCarousel({{ $featuredProducts->count() }})">
                <div x-ref="track" @scroll.debounce.100ms="onScroll()"
                     class="no-scrollbar flex snap-x snap-mandatory gap-6 overflow-x-auto pb-2">
                    @foreach ($featuredProducts as $product)
                        <article data-product-card class="group w-[82%] shrink-0 snap-start sm:w-[calc(50%-12px)] xl:w-[calc(25%-18px)]">
                            <div class="relative overflow-hidden rounded-[1.6rem] border border-line/70 bg-sand">
                                <a href="{{ $product['url'] }}" class="block aspect-[4/5] overflow-hidden" aria-label="View {{ $product['name'] }}">
                                    <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}"
                                         class="h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-105">
                                </a>

                                <span class="absolute right-3 top-3 inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/90 text-ink shadow-sm backdrop-blur transition-colors group-hover:bg-brand-600 group-hover:text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                    </svg>
                                </span>
                            </div>

                            <div class="mt-4 px-0.5">
                                <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-brand-600">{{ $product['category'] }}</p>
                                <h3 class="mt-1 line-clamp-2 text-base font-semibold leading-snug text-ink">{{ $product['name'] }}</h3>
                                <p class="mt-1.5 text-sm font-bold text-brand-600">Rp{{ number_format($product['price'], 0, ',', '.') }}</p>

                                <a href="{{ $product['url'] }}"
                                   class="mt-3 inline-flex w-full items-center justify-center gap-2 rounded-full bg-brand-600 px-5 py-2.5 text-xs font-semibold text-white transition-colors hover:bg-brand-700">
                                    Buy Now
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                    </svg>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- Pagination dots + arrows --}}
                <div class="mt-7 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <template x-for="page in pageList" :key="page">
                            <button type="button" @click="goTo(page)"
                                    :aria-label="'Go to product page ' + (page + 1)"
                                    :class="current === page ? 'w-8 bg-brand-600' : 'w-2 bg-brand-200 hover:bg-brand-300'"
                                    class="h-2 rounded-full transition-all duration-300"></button>
                        </template>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" @click="goTo(current - 1)" aria-label="Previous products"
                                class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-line bg-white text-ink transition-colors hover:border-brand-300 hover:bg-brand-50 hover:text-brand-700">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                            </svg>
                        </button>
                        <button type="button" @click="goTo(current + 1)" aria-label="Next products"
                                class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-line bg-white text-ink transition-colors hover:border-brand-300 hover:bg-brand-50 hover:text-brand-700">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{--  CATEGORIES                                                --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <section id="categories" class="border-y border-line/70 bg-white/50 py-16 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <h2 class="font-lora text-4xl leading-tight tracking-tight text-ink sm:text-5xl">
                    Explore Decak Categories
                </h2>
                <p class="mt-3 text-sm leading-relaxed text-muted">
                    Discover handmade pieces that suit your style and needs.
                </p>
            </div>

            <div class="mt-10 grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($categories as $category)
                    <a href="{{ $category['url'] }}" class="group block rounded-[1.75rem] border border-line/70 bg-sand p-3.5 transition-all duration-300 hover:-translate-y-1 hover:border-brand-200 hover:shadow-[0_24px_50px_-30px_rgba(90,44,32,0.4)]">
                        <div class="overflow-hidden rounded-[1.35rem] bg-sand-deep">
                            <img src="{{ $category['image'] }}" alt="{{ $category['name'] }}"
                                 class="aspect-[4/3] w-full object-cover transition-transform duration-500 ease-out group-hover:scale-105">
                        </div>

                        <div class="flex items-end justify-between gap-3 px-1.5 pb-1 pt-4">
                            <div>
                                <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-brand-600">Collection</p>
                                <h3 class="mt-1 font-serif text-2xl leading-tight text-ink">{{ $category['name'] }}</h3>
                                <p class="mt-1.5 text-xs leading-relaxed text-muted">{{ $category['description'] }}</p>
                            </div>

                            <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white text-ink shadow-sm transition-colors group-hover:bg-brand-600 group-hover:text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                </svg>
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{--  JOURNAL / SOCIAL PROOF                                    --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <section id="journal" class="py-16 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div class="max-w-2xl">
                    <h2 class="font-lora text-4xl leading-tight tracking-tight text-ink sm:text-5xl">
                        Exploring Close By, with Wonder
                    </h2>
                    <p class="mt-3 text-sm leading-relaxed text-muted">
                        Our stories, reviews, collaborations, and journeys.
                    </p>
                </div>
            </div>

            <div class="mt-10 grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                {{-- Story --}}
                <article class="group flex flex-col overflow-hidden rounded-[1.75rem] border border-line/70 bg-white">
                    <a href="{{ route('catalog.index') }}" class="block overflow-hidden" aria-label="Read the story behind Decak">
                        <img src="{{ asset('images/home/workshop.jpg') }}" alt="Craft tools and colourful tape on a work desk"
                             class="aspect-[4/3] w-full object-cover transition-transform duration-500 group-hover:scale-105">
                    </a>
                    <div class="flex flex-1 flex-col p-5">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-brand-600">Studio story</p>
                        <h3 class="mt-2 font-serif text-xl leading-snug text-ink">The Story Behind Our First Stitch</h3>
                        <p class="mt-2 flex-1 text-xs leading-relaxed text-muted">How a small kitchen table, three spools of thread, and a lot of curiosity became Decak.</p>
                        <a href="{{ route('catalog.index') }}" class="mt-4 inline-flex items-center gap-1.5 text-xs font-semibold text-brand-700 transition-colors hover:text-brand-600">
                            Read the story
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>
                </article>

                {{-- Workshop --}}
                <article class="group flex flex-col overflow-hidden rounded-[1.75rem] border border-line/70 bg-white">
                    <a href="{{ route('catalog.index') }}" class="block overflow-hidden" aria-label="See highlights from the Decak workshop">
                        <img src="{{ asset('images/home/studio.jpg') }}" alt="Friends sharing ideas around a studio table"
                             class="aspect-[4/3] w-full object-cover transition-transform duration-500 group-hover:scale-105">
                    </a>
                    <div class="flex flex-1 flex-col p-5">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-brand-600">Workshop</p>
                        <h3 class="mt-2 font-serif text-xl leading-snug text-ink">A Slow Weekend at the Decak Studio</h3>
                        <p class="mt-2 flex-1 text-xs leading-relaxed text-muted">A peek into our dyeing, stitching, and packaging rituals — with coffee, always.</p>
                        <a href="{{ route('catalog.index') }}" class="mt-4 inline-flex items-center gap-1.5 text-xs font-semibold text-brand-700 transition-colors hover:text-brand-600">
                            See the highlights
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>
                </article>

                {{-- Press --}}
                <article class="group flex flex-col overflow-hidden rounded-[1.75rem] border border-line/70 bg-white">
                    <a href="{{ route('catalog.index') }}" class="block overflow-hidden" aria-label="Read the Rumah and Craft feature">
                        <img src="{{ asset('images/home/interior.jpg') }}" alt="A warm living room styled with handmade home accessories"
                             class="aspect-[4/3] w-full object-cover transition-transform duration-500 group-hover:scale-105">
                    </a>
                    <div class="flex flex-1 flex-col p-5">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-brand-600">Press mention</p>
                        <h3 class="mt-2 font-serif text-xl leading-snug text-ink">As Seen In Rumah &amp; Craft</h3>
                        <p class="mt-2 flex-1 text-xs leading-relaxed text-muted">“A quiet reminder that everyday objects can still be made with care.”</p>
                        <a href="{{ route('catalog.index') }}" class="mt-4 inline-flex items-center gap-1.5 text-xs font-semibold text-brand-700 transition-colors hover:text-brand-600">
                            Read the feature
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>
                </article>

                {{-- Inline CTA card --}}
                <article class="relative flex flex-col overflow-hidden rounded-[1.75rem] bg-brand-600 p-6 text-white shadow-lg shadow-brand-900/10">
                    <div aria-hidden="true" class="pointer-events-none absolute -right-10 -top-10 h-36 w-36 rounded-full bg-white/10"></div>
                    <div aria-hidden="true" class="pointer-events-none absolute -bottom-12 -left-8 h-32 w-32 rounded-full bg-white/10"></div>

                    <p class="text-[10px] font-semibold uppercase tracking-[0.22em] text-white/70">Say hello</p>
                    <h3 class="mt-3 font-serif text-3xl leading-tight">Get to know us</h3>
                    <p class="mt-3 flex-1 text-sm leading-relaxed text-white/80">
                        Visit the studio, ask about a custom order, or simply say hello. We would love to meet you.
                    </p>
                    <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener"
                       class="mt-5 inline-flex items-center justify-center gap-2 rounded-full bg-cream px-5 py-3 text-xs font-semibold text-brand-700 transition-colors hover:bg-white">
                        Get to know us
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </article>
            </div>

            {{-- Quote highlight --}}
            <div class="mt-6 overflow-hidden rounded-[2rem] border border-brand-100 bg-sand px-6 py-8 sm:px-10">
                <div class="grid gap-6 lg:grid-cols-[1fr_auto] lg:items-center">
                    <div>
                        <div class="flex items-center gap-1 text-brand-500">
                            @for ($i = 0; $i < 5; $i++)
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                                    <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401Z" clip-rule="evenodd" />
                                </svg>
                            @endfor
                        </div>
                        <blockquote class="mt-3 max-w-3xl font-serif text-2xl leading-snug text-ink sm:text-3xl">
                            “The pouch arrived wrapped like a gift, with a tiny handwritten note. It feels like something made just for me.”
                        </blockquote>
                        <p class="mt-3 text-[11px] font-semibold uppercase tracking-[0.18em] text-muted">
                            Nadia · Yogyakarta · Verified customer
                        </p>
                    </div>

                    <div class="flex items-center gap-4 rounded-2xl bg-cream/80 px-5 py-4">
                        <div class="flex -space-x-2">
                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full border-2 border-cream bg-brand-200 text-[10px] font-bold text-brand-800">RA</span>
                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full border-2 border-cream bg-peach text-[10px] font-bold text-brand-800">DN</span>
                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full border-2 border-cream bg-brand-500 text-[10px] font-bold text-white">SR</span>
                        </div>
                        <div>
                            <p class="text-lg font-bold leading-none text-ink">4.9<span class="text-xs font-medium text-muted">/5</span></p>
                            <p class="mt-1 text-[11px] text-muted">Loved by 1,200+ customers</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('floating')
    {{-- ── Floating WhatsApp chat widget ── --}}
    <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener" aria-label="Chat with us on WhatsApp"
       class="group fixed bottom-6 right-6 z-50 inline-flex items-center gap-3">
        {{-- Tooltip label --}}
        <span class="rounded-xl bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-lg ring-1 ring-black/5 transition-shadow duration-200 group-hover:shadow-xl">
            Chat with us!
        </span>

        {{-- WhatsApp action button --}}
        <span class="relative inline-flex h-14 w-14 shrink-0 items-center justify-center transition-transform duration-200 group-hover:scale-105">
            {{-- Glowing pulse rings --}}
            <span aria-hidden="true"
                  class="absolute inset-0 animate-glow rounded-full bg-[#25D366]/45 motion-reduce:animate-none group-hover:[animation-play-state:paused]"></span>
            <span aria-hidden="true"
                  class="absolute inset-0 animate-glow rounded-full bg-[#25D366]/30 [animation-delay:1.2s] motion-reduce:animate-none group-hover:[animation-play-state:paused]"></span>

            {{-- Main button --}}
            <span class="relative inline-flex h-14 w-14 items-center justify-center rounded-full bg-[#25D366] text-white shadow-lg shadow-[#25D366]/40 transition-colors duration-200 animate-heartbeat motion-reduce:animate-none group-hover:[animation-play-state:paused] group-hover:bg-[#1fb95a]">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-7 w-7">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z" />
                </svg>
            </span>
        </span>
    </a>
@endpush
