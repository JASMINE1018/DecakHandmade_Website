@extends('layouts.catalog')

@section('title', 'Catalog')

@section('content')

    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{--  BREADCRUMB                                                --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        <nav class="mb-6 text-sm text-muted">
            <ol class="flex items-center gap-1.5">
                <li>
                    <a href="{{ url('/') }}" class="transition-colors hover:text-brand-600">Home</a>
                </li>
                <li class="text-line">/</li>
                <li class="font-medium text-ink">Catalog</li>
            </ol>
        </nav>

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
