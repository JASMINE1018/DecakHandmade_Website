@extends('layouts.catalog')

@section('title', 'Made with Love')

@push('head')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@endpush

@section('content')

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{--  HERO                                                      --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <section class="relative overflow-hidden bg-[#F9F6F1]">
        <div class="grid min-h-[540px] grid-cols-1 md:grid-cols-2"
             x-data="heroCarousel({{ count($heroSlides) }})">

            {{-- ── Left: Copy ── --}}
            <div class="reveal-on-scroll flex flex-col justify-center px-6 py-12 sm:px-10 md:py-16 lg:px-20 xl:px-24">
                <span class="inline-flex items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.24em] text-brand-600">
                    <span class="h-px w-8 bg-brand-400"></span>
                    Made with love
                </span>

                <h1 class="mt-6 text-3xl font-semibold leading-[1.12] tracking-tight text-ink sm:text-4xl lg:text-5xl">
                    An Aesthetic Touch <span class="italic font-normal">&amp; the Warmth</span> of Handcrafted Pieces for Your Special Moments
                </h1>

                <p class="mt-5 max-w-lg text-sm leading-relaxed text-muted sm:text-base">
                    A collection of exclusive handmade products made with care and love.
                </p>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
                    <a href="{{ route('catalog.index') }}"
                       class="inline-flex items-center justify-center rounded-[4px] bg-[#B86B4B] px-6 py-3 text-xs font-medium text-white shadow-sm transition-all duration-300 hover:-translate-y-0.5 hover:bg-[#9E583B] hover:shadow-md hover:shadow-[#B86B4B]/20">
                        View catalog
                    </a>

                    <a href="#our-story"
                       class="inline-flex items-center justify-center rounded-[4px] border border-[#B86B4B]/50 bg-white/60 px-6 py-3 text-xs font-medium text-[#B86B4B] transition-all duration-300 hover:-translate-y-0.5 hover:border-[#B86B4B] hover:bg-white hover:shadow-sm">
                        About Decak
                    </a>
                </div>

                {{-- Slider dots --}}
                <div class="mt-8 flex items-center gap-2">
                    @foreach ($heroSlides as $index => $slide)
                        <button type="button"
                                @click="go({{ $index }})"
                                aria-label="Go to slide {{ $index + 1 }}"
                                :class="active === {{ $index }} ? 'w-6 bg-[#B86B4B]' : 'w-2 bg-[#D1C7BD] hover:bg-[#B8ADA2]'"
                                class="h-2 rounded-full transition-all duration-500 ease-out {{ $index === 0 ? 'w-6 bg-[#B86B4B]' : 'w-2 bg-[#D1C7BD]' }}"></button>
                    @endforeach
                </div>
            </div>

            {{-- ── Right: Image carousel (Full width/height on its column like our-story) ── --}}
            <div class="relative min-h-[360px] w-full overflow-hidden bg-[#E7E1D7] md:min-h-full"
                 @mouseenter="stopAutoplay()" @mouseleave="startAutoplay()">
                @foreach ($heroSlides as $index => $slide)
                    <img src="{{ $slide['image'] }}"
                         alt="{{ $slide['title'] }} — {{ $slide['caption'] }}"
                         class="absolute inset-0 block h-full w-full object-cover transition-all duration-1000 ease-out {{ $index === 0 ? 'opacity-100 scale-100' : 'opacity-0 scale-105 pointer-events-none' }}"
                         :class="active === {{ $index }} ? 'opacity-100 scale-100' : 'opacity-0 scale-105 pointer-events-none'">
                @endforeach

                {{-- Next / previous controls positioned at middle sides --}}
                <button type="button" @click="prev()" aria-label="Previous slide"
                        class="absolute left-4 top-1/2 -translate-y-1/2 inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/80 text-ink shadow backdrop-blur transition-all duration-300 hover:scale-105 hover:bg-white">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                    </svg>
                </button>
                <button type="button" @click="next()" aria-label="Next slide"
                        class="absolute right-4 top-1/2 -translate-y-1/2 inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/80 text-ink shadow backdrop-blur transition-all duration-300 hover:scale-105 hover:bg-white">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </button>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{--  VALUE PROPOSITION & STORY                                 --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <section id="our-story" class="relative overflow-hidden bg-[#F9F6F1]">
        <div class="grid min-h-[500px] grid-cols-1 md:grid-cols-2">

            {{-- Left image (Full column edge-to-edge like our-story) --}}
            <div class="relative min-h-[360px] w-full overflow-hidden bg-[#E7E1D7] md:min-h-full">
                <img src="{{ asset('images/home/story-materials.webp') }}"
                     alt="Handmade bag with selected materials and batik details"
                     class="img-reveal block h-full w-full object-cover transition-transform duration-700 ease-out hover:scale-105">
            </div>

            {{-- Right copy --}}
            <div class="reveal-on-scroll flex flex-col justify-center px-6 py-12 sm:px-10 md:py-16 lg:px-20 xl:px-24">
                <h2 class="max-w-xl text-3xl font-semibold leading-[1.12] tracking-tight text-ink sm:text-4xl lg:text-5xl">
                    Made Wholeheartedly from Selected Materials
                </h2>

                <p class="mt-5 max-w-xl text-sm leading-relaxed text-muted sm:text-base">
                    At Decak Handmade, every piece is crafted with precision, creativity, and attention to every detail. We believe that handmade products are more than just items; they are creations that carry a story and meaning.
                </p>

                <div class="mt-8 grid gap-x-6 gap-y-6 sm:grid-cols-3">
                    <div class="group">
                        <span class="flex items-center gap-2.5 text-[#B86B4B]">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 shrink-0 transition-transform duration-300 group-hover:scale-110">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                            </svg>
                            <span class="line-expand h-px w-6 bg-[#B86B4B]/40"></span>
                        </span>
                        <h3 class="mt-3 text-xs font-semibold text-ink">Made with Heart</h3>
                        <p class="mt-1 text-xs leading-relaxed text-muted">Every product is a reflection of dedication and great care.</p>
                    </div>

                    <div class="group">
                        <span class="flex items-center gap-2.5 text-[#B86B4B]">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 shrink-0 transition-transform duration-300 group-hover:scale-110">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z" />
                            </svg>
                            <span class="line-expand h-px w-6 bg-[#B86B4B]/40"></span>
                        </span>
                        <h3 class="mt-3 text-xs font-semibold text-ink">Meaningful Details</h3>
                        <p class="mt-1 text-xs leading-relaxed text-muted">Prioritizing the finest details in every process.</p>
                    </div>

                    <div class="group">
                        <span class="flex items-center gap-2.5 text-[#B86B4B]">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 shrink-0 transition-transform duration-300 group-hover:scale-110">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.05 4.575a1.575 1.575 0 1 0-3.15 0v3m3.15-3v-1.5a1.575 1.575 0 0 1 3.15 0v2.925m-6.3 1.575H5.475a1.575 1.575 0 1 0 0 3.15h1.425m1.575-1.575v-1.5a1.575 1.575 0 0 1 3.15 0v1.5m-4.725 0h4.725m-4.725 0H4.875A2.625 2.625 0 0 0 2.25 15v.75a6.75 6.75 0 0 0 6.75 6.75h5.25A6.75 6.75 0 0 0 21 15.75V15a2.625 2.625 0 0 0-2.625-2.625h-2.1" />
                            </svg>
                            <span class="line-expand h-px w-6 bg-[#B86B4B]/40"></span>
                        </span>
                        <h3 class="mt-3 text-xs font-semibold text-ink">A Personal Work</h3>
                        <p class="mt-1 text-xs leading-relaxed text-muted">Creating products that align with your unique story.</p>
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
            <div class="reveal-on-scroll flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-3xl font-semibold leading-tight tracking-tight text-ink sm:text-4xl">
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
                        <article data-product-card style="transition-delay: {{ $loop->index * 120 }}ms" class="reveal-on-scroll group flex w-[82%] shrink-0 flex-col snap-start transition-all duration-300 sm:w-[calc(50%-12px)] xl:w-[calc(25%-18px)] hover:-translate-y-2">
                            <div class="relative overflow-hidden rounded-[1.25rem] bg-sand shadow-sm transition-shadow duration-300 group-hover:shadow-md">
                                <a href="{{ $product['url'] }}" class="block aspect-square overflow-hidden" aria-label="View {{ $product['name'] }}">
                                    <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}"
                                         class="h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105">
                                </a>
                            </div>

                            <div class="mt-4 flex flex-1 flex-col justify-between px-0.5">
                                <div>
                                    <h3 class="line-clamp-2 text-xs font-semibold leading-snug text-ink transition-colors group-hover:text-[#B86B4B]">{{ $product['name'] }}</h3>
                                    <p class="mt-2 text-xs font-semibold text-brand-600">{{ is_numeric($product['price']) ? 'Rp'.number_format($product['price'], 0, ',', '.') : $product['price'] }}</p>
                                </div>

                                <a href="{{ $product['url'] }}"
                                   class="mt-4 inline-flex w-full items-center justify-center rounded-xl bg-[#C16D54] px-4 py-2.5 text-xs font-medium text-white shadow-sm transition-all duration-300 hover:bg-[#a85942] hover:shadow-md hover:shadow-[#C16D54]/25">
                                    View more
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- Pagination dots + arrows --}}
                <div class="mt-8 flex items-center justify-center gap-3">
                    <button type="button" @click="goTo(current - 1)" aria-label="Previous products"
                            class="inline-flex h-8 w-8 items-center justify-center text-ink/70 transition-colors hover:text-ink">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                        </svg>
                    </button>

                    <div class="flex items-center gap-1.5">
                        <template x-for="page in pageList" :key="page">
                            <button type="button" @click="goTo(page)"
                                    :aria-label="'Go to product page ' + (page + 1)"
                                    :class="current === page ? 'w-2 bg-[#C16D54]' : 'w-2 bg-[#D1C7BD] hover:bg-[#B8ADA2]'"
                                    class="h-2 rounded-full transition-all duration-300"></button>
                        </template>
                    </div>

                    <button type="button" @click="goTo(current + 1)" aria-label="Next products"
                            class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-[#C16D54] text-white shadow-sm transition-colors hover:bg-[#a85942]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{--  CATEGORIES                                                --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <section id="categories" class="border-y border-line/70 bg-white/50 py-16 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="reveal-on-scroll max-w-2xl">
                <h2 class="text-3xl font-semibold leading-tight tracking-tight text-ink sm:text-4xl">
                    Explore Decak Categories
                </h2>
                <p class="mt-3 text-sm leading-relaxed text-muted">
                    Discover handmade pieces that suit your style and needs.
                </p>
            </div>

            <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($categories as $index => $category)
                    <a href="{{ $category['url'] }}"
                       style="transition-delay: {{ $index * 100 }}ms"
                       class="reveal-on-scroll group block overflow-hidden rounded-xl bg-white shadow-sm transition-all duration-300 hover:-translate-y-2 hover:shadow-md">
                        <div class="overflow-hidden">
                            <img src="{{ $category['image'] }}" alt="{{ $category['name'] }}"
                                 class="aspect-[4/3] w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105">
                        </div>

                        <div class="flex items-center justify-between px-3.5 py-3">
                            <h3 class="text-xs font-semibold text-ink transition-colors group-hover:text-[#B86B4B]">{{ $category['name'] }}</h3>

                            <span class="text-[#B86B4B] transition-transform duration-300 group-hover:translate-x-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
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
                <div class="reveal-on-scroll max-w-2xl">
                    <h2 class="text-3xl font-semibold leading-tight tracking-tight text-ink sm:text-4xl">
                        Exploring Close By, with Wonder
                    </h2>
                    <p class="mt-3 text-sm leading-relaxed text-muted">
                        Our stories, reviews, collaborations, and journeys.
                    </p>
                </div>
            </div>

            <div class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                {{-- Card 1: What they say about Decak --}}
                <article style="transition-delay: 0ms" class="reveal-on-scroll flex flex-col overflow-hidden rounded-xl bg-white shadow-sm transition-all duration-300 hover:-translate-y-1.5 hover:shadow-md">
                    <div class="overflow-hidden">
                        <img src="{{ asset('images/home/testimonial-lalisa.webp') }}" alt="Customer product Decak Handmade"
                             class="aspect-[16/10] w-full object-cover transition-transform duration-700 ease-out hover:scale-105">
                    </div>
                    <div class="flex flex-1 flex-col p-4">
                        <p class="text-[9px] font-semibold uppercase tracking-[0.16em] text-[#B86B4B]">WHAT THEY SAY ABOUT DECAK</p>
                        <p class="mt-2 text-xs italic leading-relaxed text-[#2B2520]">
                            “The product is very neatly made and of high quality. I really appreciate the personal touch provided.”
                        </p>
                        <p class="mt-4 text-[10px] text-muted">Kadin — Handmade Wallet</p>
                    </div>
                </article>

                {{-- Card 2: Our Customers --}}
                <article style="transition-delay: 120ms" class="reveal-on-scroll flex flex-col overflow-hidden rounded-xl bg-white shadow-sm transition-all duration-300 hover:-translate-y-1.5 hover:shadow-md">
                    <div class="overflow-hidden">
                        <img src="{{ asset('images/home/customer-terry.webp') }}" alt="Customer portrait"
                             class="aspect-[16/10] w-full object-cover transition-transform duration-700 ease-out hover:scale-105">
                    </div>
                    <div class="flex flex-1 flex-col p-4">
                        <p class="text-[9px] font-semibold uppercase tracking-[0.16em] text-[#B86B4B]">OUR CUSTOMERS</p>
                        <p class="mt-2 text-xs italic leading-relaxed text-[#2B2520]">
                            “I will use this product every day.”
                        </p>
                        <p class="mt-4 text-[10px] text-muted">Terry — Bag</p>
                    </div>
                </article>

                {{-- Card 3: Let's Collaborate --}}
                <article style="transition-delay: 240ms" class="reveal-on-scroll flex flex-col overflow-hidden rounded-xl bg-white shadow-sm transition-all duration-300 hover:-translate-y-1.5 hover:shadow-md">
                    <div class="overflow-hidden">
                        <img src="{{ asset('images/home/collab-workshop.jpg') }}" alt="Collaboration workshop with Decak Handmade"
                             class="aspect-[16/10] w-full object-cover transition-transform duration-700 ease-out hover:scale-105">
                    </div>
                    <div class="flex flex-1 flex-col p-4">
                        <p class="text-[9px] font-semibold uppercase tracking-[0.16em] text-[#B86B4B]">LET'S COLLABORATE</p>
                        <p class="mt-2 text-xs leading-relaxed text-muted">
                            Interested in collaborating with Decak Handmade? Let's create something special together.
                        </p>
                        <div class="mt-4">
                            <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener"
                               class="inline-flex rounded-xl bg-[#C16D54] px-5 py-2 text-xs font-medium text-white shadow-sm transition-all duration-300 hover:bg-[#a85942] hover:shadow-md hover:shadow-[#C16D54]/25">
                                Contact us
                            </a>
                        </div>
                    </div>
                </article>

                {{-- Card 4: Our Milestone --}}
                <article style="transition-delay: 360ms" class="reveal-on-scroll flex flex-col overflow-hidden rounded-xl bg-white shadow-sm transition-all duration-300 hover:-translate-y-1.5 hover:shadow-md">
                    <div class="overflow-hidden">
                        <img src="{{ asset('images/home/milestone-podcast.webp') }}" alt="Decak Handmade journey and milestone"
                             class="aspect-[16/10] w-full object-cover transition-transform duration-700 ease-out hover:scale-105">
                    </div>
                    <div class="flex flex-1 flex-col p-4">
                        <p class="text-[9px] font-semibold uppercase tracking-[0.16em] text-[#B86B4B]">OUR MILESTONE</p>
                        <p class="mt-2 text-xs leading-relaxed text-muted">
                            Achievement data coming soon: We are currently putting together the story of Decak Handmade's journey for you.
                        </p>
                        <p class="mt-4 text-[10px] text-muted">— Galeri IKM, Surabaya, 2024</p>
                    </div>
                </article>
            </div>

            {{-- Slider navigation dots + arrows --}}
            <div class="mt-8 flex items-center gap-3">
                <button type="button" aria-label="Previous page" class="inline-flex h-8 w-8 items-center justify-center text-ink/70 transition-colors hover:text-ink">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                    </svg>
                </button>
                <button type="button" aria-label="Next page" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-[#C16D54] text-white shadow-sm transition-colors hover:bg-[#a85942]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </button>
            </div>
        </div>
    </section>

@endsection

@push('floating')
    {{-- ── Floating WhatsApp chat widget with pulse animation ── --}}
    <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener" aria-label="Contact Us via WhatsApp"
       class="group fixed bottom-6 right-6 z-50 inline-flex items-center gap-2.5">
        {{-- Tooltip label --}}
        <span class="rounded-lg bg-[#241F1E] px-3.5 py-1.5 text-xs font-medium text-white shadow-lg transition-all duration-300 group-hover:scale-105 group-hover:bg-[#1a1615]">
            Contact Us via WhatsApp
        </span>

        {{-- WhatsApp action button with breathing ring --}}
        <span class="relative inline-flex h-12 w-12 shrink-0 items-center justify-center">
            <span aria-hidden="true"
                  class="absolute inset-0 animate-glow rounded-full bg-[#25D366]/40 motion-reduce:animate-none group-hover:[animation-play-state:paused]"></span>
            <span aria-hidden="true"
                  class="absolute inset-0 animate-glow rounded-full bg-[#25D366]/25 [animation-delay:1.2s] motion-reduce:animate-none group-hover:[animation-play-state:paused]"></span>

            <span class="relative inline-flex h-12 w-12 items-center justify-center rounded-full bg-[#25D366] text-white shadow-lg shadow-[#25D366]/40 transition-transform duration-300 group-hover:scale-110">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z" />
                </svg>
            </span>
        </span>
    </a>
@endpush
