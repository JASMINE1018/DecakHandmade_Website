@extends('layouts.admin')

@section('title', 'Admin')
@section('subtitle', 'Kelola akun yang dapat mengakses panel')

@section('content')
    <div class="grid gap-6 xl:grid-cols-3" x-data="{
        editModalOpen: false,
        editAction: '',
        editAdmin: {
            name: '',
            email: '',
            is_active: true
        },
        openEdit(admin, updateUrl) {
            this.editAdmin = {
                name: admin.name,
                email: admin.email,
                is_active: Boolean(admin.is_active)
            };
            this.editAction = updateUrl;
            this.editModalOpen = true;
        }
    }">
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
                                            <button type="button"
                                                    @click="openEdit({{ Js::from($admin) }}, '{{ route('admin.admins.update', $admin) }}')"
                                                    class="text-xs font-semibold text-brand-700 hover:underline">
                                                Edit
                                            </button>

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

        <!-- Modal Edit Admin -->
        <div x-cloak x-show="editModalOpen" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div x-show="editModalOpen"
                 x-transition.opacity.duration.200ms
                 @click="editModalOpen = false"
                 class="fixed inset-0 bg-black/40 backdrop-blur-sm"></div>

            <div class="relative flex min-h-screen items-center justify-center p-4">
                <div x-show="editModalOpen"
                     x-transition.duration.200ms
                     class="relative z-10 w-full max-w-md rounded-2xl border border-line bg-white p-6 shadow-2xl">
                    <div class="flex items-center justify-between pb-3 border-b border-line">
                        <h3 class="text-base font-semibold text-ink">Edit Admin</h3>
                        <button type="button" @click="editModalOpen = false" class="text-muted hover:text-ink">
                            <x-icon name="x" class="h-5 w-5" />
                        </button>
                    </div>

                    <form method="POST" :action="editAction" class="mt-4 space-y-3">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Nama</label>
                            <input name="name" x-model="editAdmin.name" required maxlength="100" class="admin-input">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Email</label>
                            <input name="email" type="email" x-model="editAdmin.email" required class="admin-input">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Kata sandi baru (opsional)</label>
                            <input name="password" type="password" placeholder="Minimal 12 karakter" minlength="12" class="admin-input">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Konfirmasi kata sandi baru</label>
                            <input name="password_confirmation" type="password" placeholder="Ulangi kata sandi" class="admin-input">
                        </div>
                        <label class="flex items-center gap-2 text-sm text-muted pt-1">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" x-model="editAdmin.is_active" class="rounded border-line text-brand-600 focus:ring-brand-200">
                            Aktif
                        </label>
                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-line">
                            <button type="button" @click="editModalOpen = false" class="admin-btn-secondary">Batal</button>
                            <button type="submit" class="admin-btn-primary">Simpan perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
