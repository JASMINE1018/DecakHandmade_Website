@extends('layouts.admin')

@section('title', 'Profil')
@section('subtitle', 'Perbarui data akun dan kata sandi')

@section('content')
    <div class="grid max-w-4xl gap-6 md:grid-cols-2">
        <form method="POST" action="{{ route('admin.profile.update') }}" class="admin-card">
            @csrf
            @method('PUT')

            <h2 class="mb-4 text-sm font-semibold text-ink">Data akun</h2>

            <div class="space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-ink">Nama</label>
                    <input name="name" value="{{ old('name', $admin->name) }}" required maxlength="100" class="admin-input">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-ink">Email</label>
                    <input name="email" type="email" value="{{ old('email', $admin->email) }}" required class="admin-input">
                </div>
            </div>

            <button class="admin-btn-primary mt-5 w-full">Simpan profil</button>
        </form>

        <form method="POST" action="{{ route('admin.profile.password.update') }}" class="admin-card">
            @csrf
            @method('PUT')

            <h2 class="mb-4 text-sm font-semibold text-ink">Ganti kata sandi</h2>

            <div class="space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-ink">Kata sandi saat ini</label>
                    <input name="current_password" type="password" required class="admin-input">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-ink">Kata sandi baru</label>
                    <input name="password" type="password" required minlength="12" class="admin-input">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-ink">Ulangi kata sandi baru</label>
                    <input name="password_confirmation" type="password" required class="admin-input">
                </div>
                <p class="text-xs text-muted">Minimal 12 karakter, mengandung huruf besar, huruf kecil, dan angka.</p>
            </div>

            <button class="admin-btn-secondary mt-5 w-full">Perbarui kata sandi</button>
        </form>
    </div>
@endsection
