@extends('layouts.admin')

@section('title', 'Admin')
@section('subtitle', 'Kelola akun yang dapat mengakses panel')

@section('content')
    <div class="grid gap-6 xl:grid-cols-3">
        <div class="xl:col-span-2">
            <div class="overflow-hidden rounded-2xl border border-line bg-white">
                <div class="overflow-x-auto">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Login terakhir</th>
                                <th>Status</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($admins as $admin)
                                <tr>
                                    <td class="font-medium text-ink">{{ $admin->name }}</td>
                                    <td class="text-slate-600">{{ $admin->email }}</td>
                                    <td class="text-muted">{{ $admin->last_login_at ? wib($admin->last_login_at) : 'Belum pernah' }}</td>
                                    <td>
                                        <span class="admin-badge {{ $admin->is_active ? 'admin-badge-success' : 'admin-badge-muted' }}">
                                            {{ $admin->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="flex items-center justify-end gap-3">
                                            <details class="relative inline-block text-left">
                                                <summary class="cursor-pointer text-xs font-semibold text-brand-700 hover:underline">Edit</summary>
                                                <div class="absolute right-0 z-20 mt-2 w-72 rounded-xl border border-line bg-white p-3 text-left shadow-lg">
                                                    <form method="POST" action="{{ route('admin.admins.update', $admin) }}" class="space-y-2">
                                                        @csrf
                                                        @method('PUT')
                                                        <input name="name" value="{{ $admin->name }}" required maxlength="100" class="admin-input">
                                                        <input name="email" type="email" value="{{ $admin->email }}" required class="admin-input">
                                                        <input name="password" type="password" placeholder="Kata sandi baru (opsional)" minlength="12" class="admin-input">
                                                        <input name="password_confirmation" type="password" placeholder="Ulangi kata sandi baru" class="admin-input">
                                                        <label class="flex items-center gap-2 text-xs text-muted">
                                                            <input type="hidden" name="is_active" value="0">
                                                            <input type="checkbox" name="is_active" value="1" @checked($admin->is_active) class="rounded border-line text-brand-600 focus:ring-brand-200">
                                                            Aktif
                                                        </label>
                                                        <button class="admin-btn-primary w-full">Simpan</button>
                                                    </form>
                                                </div>
                                            </details>

                                            @if ($admin->is_active && ! $admin->is(auth('admin')->user()))
                                                <form method="POST" action="{{ route('admin.admins.destroy', $admin) }}" onsubmit="return confirm('Nonaktifkan admin ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="text-xs font-medium text-danger hover:underline">Nonaktifkan</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-line px-5 py-3">
                    {{ $admins->links() }}
                </div>
            </div>
        </div>

        <div class="admin-card h-fit">
            <h2 class="text-sm font-semibold text-ink">Tambah admin</h2>
            <p class="mt-1 text-xs text-muted">Kata sandi minimal 12 karakter, kombinasi huruf besar/kecil dan angka.</p>

            <form method="POST" action="{{ route('admin.admins.store') }}" class="mt-4 space-y-3">
                @csrf
                <input name="name" value="{{ old('name') }}" required maxlength="100" placeholder="Nama" class="admin-input">
                <input name="email" type="email" value="{{ old('email') }}" required placeholder="Email" class="admin-input">
                <input name="password" type="password" required minlength="12" placeholder="Kata sandi" class="admin-input">
                <input name="password_confirmation" type="password" required placeholder="Ulangi kata sandi" class="admin-input">
                <label class="flex items-center gap-2 text-sm text-muted">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-line text-brand-600 focus:ring-brand-200">
                    Aktif
                </label>
                <button class="admin-btn-primary w-full">Tambah admin</button>
            </form>
        </div>
    </div>
@endsection
