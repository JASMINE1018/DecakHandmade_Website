<label class="flex items-center gap-2 text-sm text-muted">
    <span class="whitespace-nowrap">Sort by</span>
    <select name="sort" onchange="this.form.submit()"
            class="rounded-lg border border-line bg-white px-3 py-2 text-sm text-ink focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-200">
        @foreach ($sortOptions as $value => $label)
            <option value="{{ $value }}" @selected(request('sort', 'newest') === $value)>{{ $label }}</option>
        @endforeach
    </select>
</label>
