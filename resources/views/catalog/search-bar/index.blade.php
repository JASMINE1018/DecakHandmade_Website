{{-- ═══════════════════════════════════════════════════════════ --}}
{{--  SEARCH BAR                                                --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<div class="relative mb-6 w-full sm:w-80">
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="Search for a bag, pouch, or pillow.."
           class="w-full rounded-xl border border-line bg-white py-3 pl-11 pr-4 text-sm text-ink shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-200">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-muted">
        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
    </svg>
</div>
