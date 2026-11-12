@extends('dashboard-user.layouts.app')

@section('title', 'Change Password - Decak Handmade')

@section('content')
<div x-data="{
    showCurrent: false,
    showNew: false,
    showConfirm: false,
}">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-[#7a6f68] mb-3">
        <a href="{{ route('user.dashboard') }}" class="hover:text-[#2c2420]">Home</a>
        <span>/</span>
        <a href="{{ route('user.dashboard') }}" class="hover:text-[#2c2420]">My account</a>
        <span>/</span>
        <span class="text-[#2c2420] font-medium">Change password</span>
    </nav>

    <!-- Header Greeting -->
    <div class="flex flex-col sm:flex-row sm:items-baseline justify-between gap-2 mb-8">
        <div>
            <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-[#2c2420]">
                Change password
            </h1>
            <p class="text-sm text-[#7a6f68] mt-1">
                Keep your Decak Handmade account safe by updating your password periodically.
            </p>
        </div>
    </div>

    <!-- Main Grid Content -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Sidebar Navigation -->
        @include('dashboard-user.partials.sidebar')

        <!-- Right Content Area -->
        <section class="lg:col-span-8 xl:col-span-9 max-w-2xl">
            
            <div class="bg-white rounded-2xl border border-[#e0ddd8] p-6 sm:p-8 shadow-xs">
                
                <!-- Success Toast Alert -->
                @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-[#eaf0e7] border border-[#cfdfca] flex items-start justify-between gap-3 text-xs text-[#5a7355]">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0 text-[#5a7355]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
                @endif

                <!-- Error Toast Alert -->
                @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-[#fdf3ee] border border-[#f0c9b9] text-xs text-[#c06547] space-y-1">
                    @foreach($errors->all() as $error)
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <span>{{ $error }}</span>
                        </div>
                    @endforeach
                </div>
                @endif

                <form method="POST" action="{{ route('user.password.update') }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <!-- Current Password -->
                    <div>
                        <label class="block text-xs font-semibold text-[#5e5449] mb-1.5">Current password</label>
                        <div class="relative">
                            <input :type="showCurrent ? 'text' : 'password'" name="current_password" required
                                placeholder="Enter current password"
                                class="w-full px-3.5 py-2.5 rounded-lg border border-[#e0ddd8] bg-white text-sm text-[#2c2420] focus:border-[#c06547] focus:ring-2 focus:ring-[#f0c9b9] outline-none pr-10">
                            <button type="button" @click="showCurrent = !showCurrent" class="absolute inset-y-0 right-0 flex items-center pr-3 text-[#7a6f68] hover:text-[#2c2420]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path x-show="!showCurrent" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    <path x-show="showCurrent" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- New Password -->
                    <div>
                        <label class="block text-xs font-semibold text-[#5e5449] mb-1.5">New password</label>
                        <div class="relative">
                            <input :type="showNew ? 'text' : 'password'" name="password" required
                                placeholder="Minimum 8 characters combination"
                                class="w-full px-3.5 py-2.5 rounded-lg border border-[#e0ddd8] bg-white text-sm text-[#2c2420] focus:border-[#c06547] focus:ring-2 focus:ring-[#f0c9b9] outline-none pr-10">
                            <button type="button" @click="showNew = !showNew" class="absolute inset-y-0 right-0 flex items-center pr-3 text-[#7a6f68] hover:text-[#2c2420]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path x-show="!showNew" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    <path x-show="showNew" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label class="block text-xs font-semibold text-[#5e5449] mb-1.5">Confirm new password</label>
                        <div class="relative">
                            <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation" required
                                placeholder="Re-enter new password"
                                class="w-full px-3.5 py-2.5 rounded-lg border border-[#e0ddd8] bg-white text-sm text-[#2c2420] focus:border-[#c06547] focus:ring-2 focus:ring-[#f0c9b9] outline-none pr-10">
                            <button type="button" @click="showConfirm = !showConfirm" class="absolute inset-y-0 right-0 flex items-center pr-3 text-[#7a6f68] hover:text-[#2c2420]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path x-show="!showConfirm" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    <path x-show="showConfirm" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Security Tips Box -->
                    <div class="rounded-xl bg-[#faf7f2] border border-[#ebdcd0] p-4 text-xs text-[#5e5449] space-y-1.5">
                        <div class="font-bold text-[#2c2420]">Password security guidelines:</div>
                        <ul class="list-disc list-inside space-y-1 text-[#7a6f68]">
                            <li>At least 8 characters or more.</li>
                            <li>Combine uppercase, lowercase letters, and numbers for optimal security.</li>
                            <li>Avoid using birth dates or easily guessable personal info.</li>
                        </ul>
                    </div>

                    <div class="pt-3">
                        <button type="submit" class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-[#c06547] text-white text-sm font-semibold hover:bg-[#a8543b] transition-all shadow-xs">
                            Update password
                        </button>
                    </div>
                </form>

            </div>

        </section>
    </div>
</div>
@endsection
