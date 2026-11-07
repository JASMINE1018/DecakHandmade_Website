{{-- ── Filter Sidebar ── --}}
<div class="rounded-2xl border border-line bg-white p-5">

    {{-- Filter header + Reset --}}
    <div class="flex items-center justify-between">
        <h3 class="text-sm font-bold text-ink">Filter</h3>
        <a href="{{ route('catalog.index') }}" class="text-xs font-medium text-brand-600 transition-colors hover:text-brand-700">
            Reset
        </a>
    </div>

    <hr class="my-4 border-line">

    {{-- Category checkboxes --}}
    <div>
        <h4 class="mb-3 text-xs font-semibold uppercase tracking-wide text-muted">Category</h4>
        <ul class="space-y-2.5">
            @php
                $isAll = empty($selectedCategories) || in_array('all', $selectedCategories);
            @endphp

            @foreach ($categories as $value => $label)
                <li>
                    <label class="flex cursor-pointer items-center gap-2.5 text-sm text-ink transition-colors hover:text-brand-600">
                        @if ($value === 'all')
                            <input type="checkbox"
                                   name="categories[]"
                                   value="all"
                                   {{ $isAll ? 'checked' : '' }}
                                   class="h-4 w-4 rounded border-line text-brand-600 focus:ring-brand-200"
                                   onchange="toggleAllCheckboxes(this)">
                        @else
                            <input type="checkbox"
                                   name="categories[]"
                                   value="{{ $value }}"
                                   {{ in_array($value, $selectedCategories) ? 'checked' : '' }}
                                   class="category-checkbox h-4 w-4 rounded border-line text-brand-600 focus:ring-brand-200"
                                   onchange="uncheckAll()">
                        @endif
                        <span>{{ $label }}</span>
                    </label>
                </li>
            @endforeach
        </ul>
    </div>

    {{-- Apply Filter button --}}
    <button type="submit"
            class="mt-6 w-full rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-brand-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-300 focus-visible:ring-offset-2">
        Apply Filter
    </button>
</div>
