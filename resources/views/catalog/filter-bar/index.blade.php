{{-- ── Filter Sidebar ── --}}
<div class="space-y-6">
    <div class="rounded-2xl border border-[#e5dfd8] bg-white p-5 shadow-2xs">
        {{-- Filter header + Reset --}}
        <div class="flex items-center justify-between pb-3 border-b border-[#e5dfd8]">
            <h3 class="text-sm font-bold text-[#2c2420]">Filter</h3>
            <a href="{{ route('catalog.index') }}" class="text-xs font-medium text-[#7a6f68] transition-colors hover:text-[#c06547]">
                Atur ulang
            </a>
        </div>

        {{-- Category checkboxes --}}
        <div class="pt-4">
            <h4 class="mb-3 text-xs font-semibold text-[#5e5449]">Kategori</h4>
            <ul class="space-y-3">
                @php
                    $isAll = empty($selectedCategories) || in_array('all', $selectedCategories);
                @endphp

                @foreach ($categories as $value => $label)
                    <li>
                        <label class="flex cursor-pointer items-center gap-2.5 text-xs text-[#2c2420] transition-colors hover:text-[#c06547]">
                            @if ($value === 'all')
                                <input type="checkbox"
                                       name="categories[]"
                                       value="all"
                                       {{ $isAll ? 'checked' : '' }}
                                       class="h-4 w-4 rounded border-[#d3c9c1] text-[#c06547] focus:ring-[#c06547]/30"
                                       onchange="toggleAllCheckboxes(this)">
                            @else
                                <input type="checkbox"
                                       name="categories[]"
                                       value="{{ $value }}"
                                       {{ in_array($value, $selectedCategories) ? 'checked' : '' }}
                                       class="category-checkbox h-4 w-4 rounded border-[#d3c9c1] text-[#c06547] focus:ring-[#c06547]/30"
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
                class="mt-6 w-full rounded-xl border border-[#d3c9c1] bg-white px-4 py-2.5 text-xs font-semibold text-[#2c2420] transition-colors hover:bg-[#f5eee6] hover:border-[#c06547] focus:outline-none">
            Terapkan filter
        </button>
    </div>

    {{-- Artisan quote badge --}}
    <p class="text-xs text-[#7a6f68] leading-relaxed px-1">
        Setiap karya punya sedikit perbedaan. Itulah indahnya buatan tangan.
    </p>
</div>
