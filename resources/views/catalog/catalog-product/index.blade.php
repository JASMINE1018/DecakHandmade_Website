@extends('layouts.catalog')

@section('title', 'Catalog')

@section('content')

    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{--  BREADCRUMB & HEADER                                        --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        <nav class="mb-3 text-xs text-[#7a6f68]">
            <ol class="flex items-center gap-1.5">
                <li>
                    <a href="{{ route('home') }}" class="transition-colors hover:text-[#c06547]">Beranda</a>
                </li>
                <li class="text-[#d3c9c1]">/</li>
                <li class="font-medium text-[#2c2420]">Katalog produk</li>
            </ol>
        </nav>

        <div class="mb-8">
            <h1 class="text-3xl font-bold tracking-tight text-[#2c2420] sm:text-4xl">
                Karya untuk keseharian
            </h1>
            <p class="mt-2 text-sm text-[#7a6f68]">
                Dibuat perlahan, dipilih untuk menemani Anda lebih lama.
            </p>
        </div>

        <form id="catalogFilterForm" action="{{ route('catalog.index') }}" method="GET">
            @include('catalog.search-bar.index')

            {{-- ═══════════════════════════════════════════════════════════ --}}
            {{--  SIDEBAR FILTER + PRODUCT GRID                             --}}
            {{-- ═══════════════════════════════════════════════════════════ --}}
            <div class="flex flex-col gap-8 lg:flex-row">

                {{-- ── Filter Sidebar ── --}}
                <aside class="w-full shrink-0 lg:w-64">
                    @include('catalog.filter-bar.index')
                </aside>

                {{-- ── Product Grid ── --}}
                <div class="min-w-0 flex-1">
                    @include('catalog.catalog-product._toolbar')
                    @include('catalog.catalog-product._grid')
                </div>
            </div>
        </form>
    </div>

    {{-- Checkbox toggle logic --}}
    <script>
        function toggleAllCheckboxes(allCheckbox) {
            const checkboxes = document.querySelectorAll('.category-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = false;
            });
        }

        function uncheckAll() {
            const allCheckbox = document.querySelector('input[value="all"]');
            if (allCheckbox) {
                allCheckbox.checked = false;
            }

            // If no category is checked, re-check "All"
            const checkboxes = document.querySelectorAll('.category-checkbox');
            const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
            if (!anyChecked && allCheckbox) {
                allCheckbox.checked = true;
            }
        }
    </script>

@endsection
