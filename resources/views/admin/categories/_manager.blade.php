<div class="grid gap-6 xl:grid-cols-3" x-data="{
    categoryModalOpen: false,
    categoryAction: '',
    category: {
        name: '',
        is_active: true
    },
    openEdit(item, url) {
        this.category = {
            name: item.name,
            is_active: Boolean(item.is_active)
        };
        this.categoryAction = url;
        this.categoryModalOpen = true;
    }
}">
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
                                        <button type="button"
                                                @click="openEdit({{ Js::from($category) }}, '{{ route('admin.categories.update', $category) }}')"
                                                class="text-xs font-semibold text-brand-700 hover:underline">
                                            Edit
                                        </button>

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

    <!-- Modal Edit Kategori -->
    <div x-cloak x-show="categoryModalOpen" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div x-show="categoryModalOpen"
             x-transition.opacity.duration.200ms
             @click="categoryModalOpen = false"
             class="fixed inset-0 bg-black/40 backdrop-blur-sm"></div>

        <div class="relative flex min-h-screen items-center justify-center p-4">
            <div x-show="categoryModalOpen"
                 x-transition.duration.200ms
                 class="relative z-10 w-full max-w-md rounded-2xl border border-line bg-white p-6 shadow-2xl">
                <div class="flex items-center justify-between pb-3 border-b border-line">
                    <h3 class="text-base font-semibold text-ink">Edit Kategori</h3>
                    <button type="button" @click="categoryModalOpen = false" class="text-muted hover:text-ink">
                        <x-icon name="x" class="h-5 w-5" />
                    </button>
                </div>

                <form method="POST" :action="categoryAction" class="mt-4 space-y-3">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Nama kategori</label>
                        <input name="name" x-model="category.name" required maxlength="120" class="admin-input">
                    </div>
                    <label class="flex items-center gap-2 text-sm text-muted pt-1">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" x-model="category.is_active" class="rounded border-line text-brand-600 focus:ring-brand-200">
                        Aktif
                    </label>
                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-line">
                        <button type="button" @click="categoryModalOpen = false" class="admin-btn-secondary">Batal</button>
                        <button type="submit" class="admin-btn-primary">Simpan perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
