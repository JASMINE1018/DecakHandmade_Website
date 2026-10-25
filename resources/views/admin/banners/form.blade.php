@extends('layouts.admin')

@section('title', $banner->exists ? 'Edit Banner' : 'Tambah Banner')
@section('subtitle', $banner->exists ? $banner->title : 'Banner baru untuk storefront')

@section('content')
    @php
        $isEdit = $banner->exists;
    @endphp

    <form method="POST" action="{{ $isEdit ? route('admin.banners.update', $banner) : route('admin.banners.store') }}"
          enctype="multipart/form-data" class="grid max-w-4xl gap-6 md:grid-cols-3">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="space-y-4 md:col-span-2">
            <div class="admin-card">
                <div class="space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-ink">Judul</label>
                        <input name="title" value="{{ old('title', $banner->title) }}" required maxlength="120" class="admin-input">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-ink">Teks banner</label>
                        <input name="text" value="{{ old('text', $banner->text) }}" maxlength="300" class="admin-input">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-ink">Tautan</label>
                        <input name="link_url" value="{{ old('link_url', $banner->link_url) }}" maxlength="255" placeholder="https://... atau /produk" class="admin-input">
                        <p class="mt-1 text-xs text-muted">Gunakan URL http/https atau path internal yang diawali /.</p>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-ink">Gambar {{ $isEdit ? '(biarkan kosong jika tidak diganti)' : '' }}</label>
                        <input type="file" name="image" accept="image/jpeg,image/png" @required(! $isEdit)
                               class="w-full rounded-lg border border-line bg-white px-3 py-2 text-sm">
                        <p class="mt-1 text-xs text-muted">JPG/PNG, maksimal 2 MB. Disimpan sebagai WebP 1600×533.</p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-ink">Mulai tampil</label>
                            <input type="date" name="starts_at" value="{{ old('starts_at', $banner->starts_at?->format('Y-m-d')) }}" class="admin-input">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-ink">Berhenti tampil</label>
                            <input type="date" name="ends_at" value="{{ old('ends_at', $banner->ends_at?->format('Y-m-d')) }}" class="admin-input">
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-ink">Urutan</label>
                            <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $banner->sort_order ?? 0) }}" class="admin-input">
                        </div>
                        <label class="flex items-end gap-2 pb-2 text-sm text-ink">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $banner->is_active ?? true)) class="rounded border-line text-brand-600 focus:ring-brand-200">
                            Aktif
                        </label>
                    </div>
                </div>

                <div class="mt-5 flex justify-end gap-2">
                    <a href="{{ route('admin.banners.index') }}" class="admin-btn-secondary">Batal</a>
                    <button class="admin-btn-primary">Simpan</button>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            @if ($isEdit)
                <div class="admin-card p-3">
                    <p class="mb-2 text-xs font-medium text-muted">Gambar saat ini</p>
                    <img src="{{ app(\App\Services\ImageStorageService::class)->url($banner->image_path) }}" alt="{{ $banner->title }}" class="w-full rounded-lg object-cover">
                </div>
            @endif
            <div class="admin-card text-xs text-muted">
                <p class="font-medium text-ink">Catatan</p>
                <p class="mt-2">Banner aktif dan dalam rentang tanggal akan ditampilkan storefront. Jika tanggal kosong, banner dianggap selalu tampil.</p>
            </div>
        </div>
    </form>
@endsection
