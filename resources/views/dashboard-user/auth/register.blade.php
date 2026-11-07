<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Daftar Akun - Decak Handmade</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #faf7f2;
            color: #2c2420;
        }
    </style>
</head>
<body class="min-h-screen bg-[#faf7f2] flex flex-col justify-between antialiased selection:bg-[#f0c9b9] selection:text-[#2c2420]"
    x-data="{ showPassword: false, showConfirm: false }">

    <div class="w-full flex-1 flex flex-col lg:flex-row">
        
        <!-- Left Side: Editorial Brand & Artisan Story -->
        <div class="lg:w-1/2 p-6 sm:p-10 lg:p-14 flex flex-col justify-between bg-[#faf7f2]">
            <!-- Brand Logo Header -->
            <div class="mb-8">
                <a href="{{ route('user.dashboard') }}" class="inline-block">
                    <span class="text-2xl sm:text-3xl font-extrabold tracking-tight text-[#b94e32] lowercase">decak handmade</span>
                </a>
            </div>

            <!-- Hero Artisan Bag Showcase Image -->
            <div class="w-full max-w-xl mx-auto lg:mx-0 my-auto">
                <div class="relative overflow-hidden rounded-3xl shadow-sm border border-[#e8dfd5]/80 bg-[#f5eee6]">
                    <img src="{{ asset('images/auth-artisan.png') }}" 
                         alt="Decak Handmade - Small things crafted with all one's heart" 
                         class="w-full h-auto aspect-4/5 sm:aspect-square lg:aspect-4/5 object-cover object-center">
                </div>

                <!-- Editorial Headline & Poetic Subcopy -->
                <div class="mt-8 sm:mt-10 max-w-md">
                    <h2 class="text-2xl sm:text-3xl lg:text-[34px] font-bold text-[#b94e32] leading-tight tracking-tight">
                        Small things,<br>
                        crafted with all one's heart.
                    </h2>
                    <p class="mt-3.5 text-xs sm:text-sm text-[#7a6f68] leading-relaxed">
                        Simple objects that accompany your day.<br>
                        Carefully stitched, for longer wear.
                    </p>
                </div>
            </div>

            <!-- Empty spacer for desktop symmetry -->
            <div class="hidden lg:block h-6"></div>
        </div>

        <!-- Right Side: Clean Focus Form -->
        <div class="lg:w-1/2 bg-white flex flex-col justify-between p-6 sm:p-12 lg:p-16 border-t lg:border-t-0 lg:border-l border-[#e8dfd5]/70">
            
            <!-- Top Nav Link: Back to Store -->
            <div class="flex items-center justify-between mb-8 sm:mb-10">
                <a href="{{ route('user.dashboard') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-[#5e5449] hover:text-[#2c2420] transition-colors group">
                    <svg class="w-4 h-4 text-[#7a6f68] group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Back to the store</span>
                </a>
            </div>

            <!-- Center Form Container -->
            <div class="w-full max-w-md mx-auto my-auto">
                
                <!-- Headline & Subtext -->
                <div class="mb-7">
                    <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-[#2c2420]">
                        Start from here.
                    </h1>
                    <p class="mt-2 text-xs sm:text-sm text-[#7a6f68]">
                        Create an account for a more personalized shopping experience.
                    </p>
                </div>

                <!-- Validation Errors Alert -->
                @if($errors->any())
                    <div class="mb-5 p-3.5 rounded-xl bg-[#fdf3ee] border border-[#f0c9b9] text-xs text-[#c06547] space-y-1">
                        @foreach($errors->all() as $error)
                            <p class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <span>{{ $error }}</span>
                            </p>
                        @endforeach
                    </div>
                @endif

                <!-- Register Form -->
                <form method="POST" action="{{ route('user.register.store') }}" class="space-y-4">
                    @csrf

                    <!-- Fullname Field -->
                    <div>
                        <label for="name" class="block text-xs font-semibold text-[#2c2420] mb-1.5">Fullname</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus
                            placeholder="Ayu Pramesti"
                            class="w-full px-4 py-3 rounded-xl border border-[#c9b8ab] bg-white text-sm text-[#2c2420] placeholder-[#b5aca4] focus:border-[#a04e37] focus:ring-2 focus:ring-[#f0c9b9] outline-none transition-all">
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-xs font-semibold text-[#2c2420] mb-1.5">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                            placeholder="ayu.pramesti@email.com"
                            class="w-full px-4 py-3 rounded-xl border border-[#c9b8ab] bg-white text-sm text-[#2c2420] placeholder-[#b5aca4] focus:border-[#a04e37] focus:ring-2 focus:ring-[#f0c9b9] outline-none transition-all">
                    </div>

                    <!-- Password Field with Toggle -->
                    <div>
                        <label for="password" class="block text-xs font-semibold text-[#2c2420] mb-1.5">Password</label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" id="password" name="password" required autocomplete="new-password"
                                placeholder="••••••••••••"
                                class="w-full px-4 py-3 rounded-xl border border-[#c9b8ab] bg-white text-sm text-[#2c2420] placeholder-[#b5aca4] focus:border-[#a04e37] focus:ring-2 focus:ring-[#f0c9b9] outline-none transition-all pr-11">
                            <button type="button" @click="showPassword = !showPassword" 
                                class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-[#a04e37] hover:text-[#7f3724] transition-colors focus:outline-none"
                                title="Lihat/Sembunyikan kata sandi">
                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path>
                                </svg>
                                <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                        <p class="text-[11px] text-[#7a6f68] mt-1.5">
                            Gunakan minimal 8 karakter, huruf dan angka.
                        </p>
                    </div>

                    <!-- Confirm Password Field with Toggle -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-[#2c2420] mb-1.5">Confirm password</label>
                        <div class="relative">
                            <input :type="showConfirm ? 'text' : 'password'" id="password_confirmation" name="password_confirmation" required autocomplete="new-password"
                                placeholder="••••••••••••"
                                class="w-full px-4 py-3 rounded-xl border border-[#c9b8ab] bg-white text-sm text-[#2c2420] placeholder-[#b5aca4] focus:border-[#a04e37] focus:ring-2 focus:ring-[#f0c9b9] outline-none transition-all pr-11">
                            <button type="button" @click="showConfirm = !showConfirm" 
                                class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-[#a04e37] hover:text-[#7f3724] transition-colors focus:outline-none"
                                title="Lihat/Sembunyikan konfirmasi kata sandi">
                                <svg x-show="!showConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path>
                                </svg>
                                <svg x-show="showConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Terms Checkbox -->
                    <div class="pt-1">
                        <label class="flex items-center gap-2 cursor-pointer select-none text-xs text-[#5e5449]">
                            <input type="checkbox" name="terms" value="1" required {{ old('terms', true) ? 'checked' : '' }}
                                class="w-4 h-4 rounded border-[#dec9ba] text-[#4f90cd] focus:ring-[#4f90cd]/30 accent-[#4f90cd]">
                            <span>I agree to the <a href="#" class="text-[#2c2420] underline">Terms & Privacy Policy</a>.</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-3">
                        <button type="submit" 
                            class="w-full py-3.5 px-6 rounded-xl bg-[#a04e37] text-white font-semibold text-sm hover:bg-[#883d29] transition-colors shadow-xs">
                            Create an account
                        </button>
                    </div>

                    <!-- Switch to Login Link -->
                    <div class="pt-4 text-center text-xs text-[#7a6f68]">
                        Already have an account? 
                        <a href="{{ route('user.login') }}" class="text-[#4f90cd] hover:underline font-medium ml-1">
                            Login
                        </a>
                    </div>
                </form>
            </div>

            <!-- Footer Meta Copyright -->
            <div class="pt-8 text-center text-[11px] text-[#9e948c]">
                © 2026 decak handmade · <a href="#" class="hover:underline">Privacy policy</a>
            </div>

        </div>

    </div>

</body>
</html>
