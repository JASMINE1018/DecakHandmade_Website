@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
    <div class="w-full max-w-md rounded-2xl border border-line bg-white p-8 shadow-xl shadow-brand-900/10">
        <div class="mb-6 text-center">
            <span class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-brand-500 text-lg font-bold text-white">DH</span>
            <h1 class="text-xl font-semibold text-ink">Decak Handmade</h1>
            <p class="mt-1 text-sm text-muted">Masuk ke panel admin</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-lg border border-danger/20 bg-danger/10 px-3 py-2 text-sm text-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.store') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="mb-1 block text-sm font-medium text-ink">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="admin-input">
            </div>

            <div>
                <label for="password" class="mb-1 block text-sm font-medium text-ink">Kata sandi</label>
                <input id="password" name="password" type="password" required class="admin-input">
            </div>

            <label class="flex items-center gap-2 text-sm text-muted">
                <input type="checkbox" name="remember" value="1" class="rounded border-line text-brand-600 focus:ring-brand-200">
                Ingat saya
            </label>

            <button type="submit" class="admin-btn-primary w-full">
                Masuk
            </button>
        </form>
    </div>
@endsection
