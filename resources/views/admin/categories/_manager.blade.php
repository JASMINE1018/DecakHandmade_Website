<div class="grid gap-6 xl:grid-cols-3">
    <div class="min-w-0 xl:col-span-2">
        <div class="rounded-2xl border border-line bg-white">
            <div>
                <table class="admin-table admin-table-compact">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th class="hidden sm:table-cell">Slug</th>
                            <th>Produk</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td class="break-words font-medium text-ink">{{ $category->name }}</td>
                                <td class="hidden break-words text-muted sm:table-cell">{{ $category->slug }}</td>
                                <td class="text-slate-600">{{ $category->products_count }}</td>
                                <td>
                                    <span class="admin-badge {{ $category->is_active ? 'admin-badge-success' : 'admin-badge-muted' }}">
                                        {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-3">
                                        <details class="relative inline-block text-left">
                                            <summary class="cursor-pointer text-xs font-semibold text-brand-700 hover:underline">Edit</summary>
                                            <div class="absolute right-0 z-20 mt-2 w-72 rounded-xl border border-line bg-white p-3 text-left shadow-lg">
                                                <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="space-y-2">
                                                    @csrf
                                                    @method('PUT')
                                                    <input name="name" value="{{ $category->name }}" required maxlength="120" class="admin-input">
                                                    <label class="flex items-center gap-2 text-xs text-muted">
                                                        <input type="hidden" name="is_active" value="0">
                                                        <input type="checkbox" name="is_active" value="1" @checked($category->is_active) class="rounded border-line text-brand-600 focus:ring-brand-200">
                                                        Aktif
                                                    </label>
                                                    <button class="admin-btn-primary w-full">Simpan</button>
                                                </form>
                                            </div>
                                        </details>

                                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Hapus kategori ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-xs font-medium text-danger hover:underline">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-10 text-center text-muted">Belum ada kategori.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-line px-5 py-3">
                {{ $categories->links() }}
            </div>
        </div>
    </div>

    <div class="admin-card h-fit min-w-0">
        <h2 class="text-sm font-semibold text-ink">Tambah kategori</h2>
        <p class="mt-1 text-xs text-muted">Nama kategori akan otomatis menjadi slug.</p>

        <form method="POST" action="{{ route('admin.categories.store') }}" class="mt-4 space-y-3">
            @csrf
            <input name="name" value="{{ old('name') }}" required maxlength="120" placeholder="Nama kategori" class="admin-input">
            <label class="flex items-center gap-2 text-sm text-muted">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" checked class="rounded border-line text-brand-600 focus:ring-brand-200">
                Aktif
            </label>
            <button class="admin-btn-primary w-full">Simpan kategori</button>
        </form>
    </div>
</div>
