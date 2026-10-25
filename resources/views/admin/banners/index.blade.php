@extends('layouts.admin')

@section('title', 'Promosi & Banner')
@section('subtitle', 'Banner tampil di storefront sesuai jadwal dan status aktif')

@section('content')
    <div class="mb-4 flex justify-end">
        <a href="{{ route('admin.banners.create') }}" class="admin-btn-primary">
            <x-icon name="plus" class="h-4 w-4" />
            Tambah banner
        </a>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($banners as $banner)
            <div class="overflow-hidden rounded-2xl border border-line bg-white">
                <img src="{{ app(\App\Services\ImageStorageService::class)->url($banner->image_path) }}" alt="{{ $banner->title }}" class="h-40 w-full object-cover">
                <div class="p-4">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <h2 class="text-sm font-semibold text-ink">{{ $banner->title }}</h2>
                            @if ($banner->text)
                                <p class="mt-1 text-xs text-muted">{{ $banner->text }}</p>
                            @endif
                        </div>
                        <span class="admin-badge {{ $banner->is_active ? 'admin-badge-success' : 'admin-badge-muted' }}">
                            {{ $banner->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    <p class="mt-2 text-xs text-muted">
                        Jadwal:
                        {{ $banner->starts_at?->format('d M Y') ?? 'sekarang' }}
                        –
                        {{ $banner->ends_at?->format('d M Y') ?? 'tanpa batas' }}
                    </p>
                    <p class="text-xs text-slate-400">Urutan: {{ $banner->sort_order }}</p>

                    <div class="mt-3 flex items-center gap-3 border-t border-line pt-3">
                        <a href="{{ route('admin.banners.edit', $banner) }}" class="text-xs font-semibold text-brand-700 hover:underline">Edit</a>

                        <form method="POST" action="{{ route('admin.banners.toggle', $banner) }}">
                            @csrf
                            @method('PATCH')
                            <button class="text-xs font-medium text-slate-600 hover:underline">{{ $banner->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                        </form>

                        <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}" class="ml-auto" onsubmit="return confirm('Hapus banner ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-xs font-medium text-danger hover:underline">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-brand-300 bg-white p-10 text-center text-sm text-muted">
                Belum ada banner. Tambahkan banner untuk ditampilkan di storefront.
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $banners->links() }}
    </div>
@endsection
