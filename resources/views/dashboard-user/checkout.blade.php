@extends('dashboard-user.layouts.app')

@section('title', 'Checkout - Decak Handmade')

@section('content')
<div x-data="{
    cart: @js($cart),
    addresses: @js($addresses),
    selectedAddressId: @js($primaryAddress?->id ?? ''),
    form: {
        recipient_name: @js($primaryAddress?->recipient_name ?? $customer->name ?? ''),
        phone: @js($primaryAddress?->phone ?? $customer->phone ?? ''),
        full_address: @js($primaryAddress?->full_address ?? ''),
        province: @js($primaryAddress?->province ?? ''),
        city: @js($primaryAddress?->city ?? ''),
        subdistrict: @js($primaryAddress?->subdistrict ?? ''),
        postal_code: @js($primaryAddress?->postal_code ?? ''),
        payment_method: 'bank',
        customer_note: '',
        save_as_primary: {{ $primaryAddress ? 'false' : 'true' }},
    },
    errors: {},
    generalError: null,
    isSubmitting: false,
    orderSuccess: false,
    createdOrder: null,
    copied: false,
    expirySeconds: 300,
    isExpired: false,
    timerDisplay: '05:00',
    timerInterval: null,
    startTimer() {
        this.expirySeconds = 300;
        this.isExpired = false;
        if (this.timerInterval) clearInterval(this.timerInterval);
        const update = () => {
            const m = String(Math.floor(this.expirySeconds / 60)).padStart(2, '0');
            const s = String(this.expirySeconds % 60).padStart(2, '0');
            this.timerDisplay = `${m}:${s}`;
        };
        update();
        this.timerInterval = setInterval(() => {
            if (this.expirySeconds > 0) {
                this.expirySeconds--;
                update();
            } else {
                this.isExpired = true;
                clearInterval(this.timerInterval);
            }
        }, 1000);
    },
    copyToClipboard(text) {
        if (!text) return;
        navigator.clipboard.writeText(text).then(() => {
            this.copied = true;
            setTimeout(() => { this.copied = false; }, 2000);
        });
    },
    csrf: document.querySelector('meta[name=csrf-token]')?.content ?? '',
    applyAddress(id) {
        const addr = this.addresses.find(a => String(a.id) === String(id));
        if (!addr) return;
        this.form.recipient_name = addr.recipient_name || '';
        this.form.phone = addr.phone || '';
        this.form.full_address = addr.full_address || '';
        this.form.province = addr.province || '';
        this.form.city = addr.city || '';
        this.form.subdistrict = addr.subdistrict || '';
        this.form.postal_code = addr.postal_code || '';
    },
    get items() {
        return this.cart.items || [];
    },
    get subtotal() {
        return this.cart.subtotal || 0;
    },
    get totalItemsCount() {
        return this.cart.total_quantity || 0;
    },
    formatRupiah(num) {
        return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
    },
    async submitOrder() {
        this.errors = {};
        this.generalError = null;
        this.isSubmitting = true;
        try {
            const res = await fetch('{{ route('user.checkout.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.csrf,
                },
                body: JSON.stringify(this.form),
            });
            const data = await res.json();
            if (!res.ok) {
                if (res.status === 422 && data.errors) {
                    this.errors = data.errors;
                    this.generalError = data.message || 'Please check your input details.';
                } else {
                    this.generalError = data.message || 'Failed to place order. Please try again.';
                }
                return;
            }
            this.createdOrder = data;
            this.orderSuccess = true;
            if (data.payment_method === 'ewallet') {
                this.startTimer();
            }
            window.dispatchEvent(new CustomEvent('cart-updated', { detail: { items: [], subtotal: 0, total_quantity: 0, count: 0 } }));
        } catch (_) {
            this.generalError = 'Network error occurred. Please try again.';
        } finally {
            this.isSubmitting = false;
        }
    }
}">
    <!-- Top Step Bar & Title -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-[#2c2420]">
            Checkout
        </h1>

        <!-- Step Indicator -->
        <div class="flex items-center gap-3 text-xs font-semibold">
            <a href="{{ route('user.cart') }}" class="flex items-center gap-2 text-[#7a6f68] hover:text-[#c06547]">
                <span class="w-6 h-6 rounded-full bg-[#c06547] text-white flex items-center justify-center text-xs">✓</span>
                <span class="text-[#c06547]">Cart</span>
            </a>
            <span class="w-8 h-px bg-[#c06547]"></span>

            <div class="flex items-center gap-2 text-[#2c2420]">
                <span class="w-6 h-6 rounded-full bg-[#c06547] text-white flex items-center justify-center text-xs" x-text="orderSuccess ? '✓' : '2'"></span>
                <span class="text-[#c06547] font-bold">Checkout</span>
            </div>
            <span class="w-8 h-px" :class="orderSuccess ? 'bg-[#c06547]' : 'bg-[#d3c9c1]'"></span>

            <div class="flex items-center gap-2" :class="orderSuccess ? 'text-[#c06547] font-bold' : 'text-[#a99e96]'">
                <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs" :class="orderSuccess ? 'bg-[#c06547] text-white' : 'bg-[#e0ddd8] text-[#7a6f68]'">3</span>
                <span>Complete</span>
            </div>
        </div>
    </div>

    <div x-show="generalError" x-cloak class="mb-6 rounded-xl border border-[#f0c9b9] bg-[#fdf3ee] px-4 py-3 text-sm text-[#b93826]" x-text="generalError"></div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- Left Form Column -->
        <div class="lg:col-span-7 xl:col-span-8 space-y-8">

            <!-- Recipient Information Section -->
            <div>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                    <h2 class="text-xl font-bold text-[#2c2420]">Shipping & recipient details</h2>

                    <template x-if="addresses.length > 0">
                        <select
                            x-model="selectedAddressId"
                            @change="applyAddress($event.target.value)"
                            class="px-3 py-2 rounded-lg border border-[#e0ddd8] bg-white text-xs font-medium text-[#2c2420] focus:border-[#c06547] outline-none"
                        >
                            <template x-for="addr in addresses" :key="addr.id">
                                <option :value="addr.id" x-text="(addr.label || 'Address') + ' — ' + addr.recipient_name + (addr.is_primary ? ' (Primary)' : '')"></option>
                            </template>
                        </select>
                    </template>
                </div>

                <form @submit.prevent="submitOrder" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-[#5e5449] mb-1.5">Recipient name</label>
                            <input type="text" x-model="form.recipient_name"
                                class="w-full px-3.5 py-2.5 rounded-lg border border-[#e0ddd8] bg-white text-sm text-[#2c2420] focus:border-[#c06547] focus:ring-2 focus:ring-[#f0c9b9] outline-none">
                            <p x-show="errors.recipient_name" class="mt-1 text-xs text-[#b93826]" x-text="errors.recipient_name?.[0]"></p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#5e5449] mb-1.5">Phone number / WhatsApp</label>
                            <input type="text" x-model="form.phone"
                                class="w-full px-3.5 py-2.5 rounded-lg border border-[#e0ddd8] bg-white text-sm text-[#2c2420] focus:border-[#c06547] focus:ring-2 focus:ring-[#f0c9b9] outline-none">
                            <p x-show="errors.phone" class="mt-1 text-xs text-[#b93826]" x-text="errors.phone?.[0]"></p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#5e5449] mb-1.5">Full address</label>
                        <input type="text" x-model="form.full_address" placeholder="Street, house number, details"
                            class="w-full px-3.5 py-2.5 rounded-lg border border-[#e0ddd8] bg-white text-sm text-[#2c2420] focus:border-[#c06547] focus:ring-2 focus:ring-[#f0c9b9] outline-none">
                        <p x-show="errors.full_address" class="mt-1 text-xs text-[#b93826]" x-text="errors.full_address?.[0]"></p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-[#5e5449] mb-1.5">Province</label>
                            <input type="text" x-model="form.province"
                                class="w-full px-3.5 py-2.5 rounded-lg border border-[#e0ddd8] bg-white text-sm text-[#2c2420] focus:border-[#c06547] focus:ring-2 focus:ring-[#f0c9b9] outline-none">
                            <p x-show="errors.province" class="mt-1 text-xs text-[#b93826]" x-text="errors.province?.[0]"></p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#5e5449] mb-1.5">City / Regency</label>
                            <input type="text" x-model="form.city"
                                class="w-full px-3.5 py-2.5 rounded-lg border border-[#e0ddd8] bg-white text-sm text-[#2c2420] focus:border-[#c06547] focus:ring-2 focus:ring-[#f0c9b9] outline-none">
                            <p x-show="errors.city" class="mt-1 text-xs text-[#b93826]" x-text="errors.city?.[0]"></p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#5e5449] mb-1.5">Postal code</label>
                            <input type="text" x-model="form.postal_code"
                                class="w-full px-3.5 py-2.5 rounded-lg border border-[#e0ddd8] bg-white text-sm text-[#2c2420] focus:border-[#c06547] focus:ring-2 focus:ring-[#f0c9b9] outline-none">
                            <p x-show="errors.postal_code" class="mt-1 text-xs text-[#b93826]" x-text="errors.postal_code?.[0]"></p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#5e5449] mb-1.5">Order note (optional)</label>
                        <textarea x-model="form.customer_note" rows="2" placeholder="e.g. Please wrap neatly as a gift"
                            class="w-full px-3.5 py-2.5 rounded-lg border border-[#e0ddd8] bg-white text-sm text-[#2c2420] focus:border-[#c06547] focus:ring-2 focus:ring-[#f0c9b9] outline-none"></textarea>
                    </div>

                    <div class="pt-1 flex items-center gap-2">
                        <input type="checkbox" id="primaryAddress" x-model="form.save_as_primary" class="rounded border-[#dec9ba] text-[#c06547] focus:ring-[#c06547]/30">
                        <label for="primaryAddress" class="text-xs text-[#5e5449] select-none cursor-pointer">
                            Save as primary address
                        </label>
                    </div>
                </form>
            </div>

            <div class="h-px bg-[#e5dfd8]"></div>

            <!-- Payment Method Section -->
            <div>
                <h2 class="text-xl font-bold text-[#2c2420] mb-4">Payment method</h2>

                <div class="space-y-3">
                    <label @click="form.payment_method = 'bank'" class="flex items-center gap-3 p-4 rounded-xl border border-[#e0ddd8] bg-white cursor-pointer hover:border-[#c06547]/50 transition-colors" :class="form.payment_method === 'bank' ? 'border-[#c06547] ring-1 ring-[#c06547]/30' : ''">
                        <input type="radio" name="payment" value="bank" x-model="form.payment_method" class="text-[#c06547] focus:ring-[#c06547]/30">
                        <div>
                            <span class="block text-sm font-semibold text-[#2c2420]">Bank transfer</span>
                            <span class="block text-xs text-[#7a6f68]">Payment instructions are sent after the order is placed.</span>
                        </div>
                    </label>

                    <label @click="form.payment_method = 'ewallet'" class="flex items-center gap-3 p-4 rounded-xl border border-[#e0ddd8] bg-white cursor-pointer hover:border-[#c06547]/50 transition-colors" :class="form.payment_method === 'ewallet' ? 'border-[#c06547] ring-1 ring-[#c06547]/30' : ''">
                        <input type="radio" name="payment" value="ewallet" x-model="form.payment_method" class="text-[#c06547] focus:ring-[#c06547]/30">
                        <div>
                            <span class="block text-sm font-semibold text-[#2c2420]">E-Wallet / QRIS</span>
                            <span class="block text-xs text-[#7a6f68]">QRIS & E-Wallet confirmation with admin.</span>
                        </div>
                    </label>
                </div>

                <p class="text-xs text-[#7a6f68] mt-4">
                    By placing your order, you agree to Decak Handmade's Terms & Privacy Policy.
                </p>
            </div>

        </div>

        <!-- Right Summary Column -->
        <div class="lg:col-span-5 xl:col-span-4">
            <div class="rounded-2xl bg-[#f5eee6]/80 border border-[#e8dfd5] p-6 shadow-xs space-y-6">
                <h3 class="text-xl font-bold text-[#2c2420]">Order summary</h3>

                <!-- Product List in Checkout -->
                <div class="space-y-4">
                    <template x-for="item in items" :key="item.key">
                        <div class="flex items-start justify-between gap-3 pb-3 border-b border-[#e5dfd8]/60 last:border-0 last:pb-0">
                            <div class="flex items-start gap-3">
                                <img :src="item.image" :alt="item.name" class="w-14 h-14 object-cover rounded-xl border border-[#e0ddd8] shadow-2xs bg-white shrink-0">
                                <div>
                                    <h4 class="text-sm font-bold text-[#2c2420]" x-text="item.name"></h4>
                                    <p class="text-xs text-[#7a6f68]">
                                        <span x-show="item.variant_name" x-text="item.variant_name + ' · '"></span>
                                        <span x-text="item.quantity + ' × ' + formatRupiah(item.unit_price)"></span>
                                    </p>
                                    <template x-for="c in item.customizations" :key="c.option_id">
                                        <p class="text-[11px] text-[#7a6f68]" x-text="c.option_name + (c.value ? ': ' + c.value : '')"></p>
                                    </template>
                                </div>
                            </div>
                            <span class="text-sm font-bold text-[#2c2420] shrink-0" x-text="formatRupiah(item.line_total)"></span>
                        </div>
                    </template>
                </div>

                <div class="h-px bg-[#e5dfd8]"></div>

                <!-- Price Calculations (Shipping hidden for now) -->
                <div class="space-y-2.5 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-[#7a6f68]">Subtotal (<span x-text="totalItemsCount"></span> items)</span>
                        <span class="font-medium text-[#2c2420]" x-text="formatRupiah(subtotal)"></span>
                    </div>
                    <div class="h-px bg-[#e5dfd8] my-2"></div>
                    <div class="flex items-center justify-between text-base font-bold text-[#2c2420]">
                        <span>Total</span>
                        <span class="text-[#c06547]" x-text="formatRupiah(subtotal)"></span>
                    </div>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="button" @click="submitOrder" :disabled="isSubmitting || items.length === 0"
                        class="w-full inline-flex items-center justify-center py-3.5 px-4 rounded-xl bg-[#c06547] text-white font-semibold text-sm hover:bg-[#a8543b] transition-colors shadow-xs disabled:opacity-50 cursor-pointer">
                        <span x-show="!isSubmitting">Place order</span>
                        <span x-show="isSubmitting" class="flex items-center gap-2">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- Modal Detail Pembayaran setelah Place Order -->
    <div x-cloak x-show="orderSuccess" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div x-show="orderSuccess"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity"></div>

        <!-- Modal Dialog Container -->
        <div class="relative min-h-screen flex items-center justify-center p-4 text-center sm:p-0">
            <div x-show="orderSuccess"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-md w-full p-6 sm:p-8 z-10">

                <div class="flex items-center justify-between pb-4 border-b border-[#e5dfd8]">
                    <div>
                        <h3 class="text-lg font-bold text-[#2c2420]">Payment details</h3>
                        <p class="text-xs text-[#7a6f68]" x-text="createdOrder?.order_number"></p>
                    </div>
                    <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Order Placed
                    </span>
                </div>

                <!-- Total Amount Banner -->
                <div class="mt-4 p-4 rounded-xl bg-[#faf7f2] border border-[#ebdcd0] flex items-center justify-between">
                    <div>
                        <span class="text-xs text-[#7a6f68]">Total Tagihan</span>
                        <div class="text-lg font-bold text-[#c06547]" x-text="formatRupiah(createdOrder?.grand_total)"></div>
                    </div>
                    <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-[#faeee5] text-[#b45339] uppercase"
                        x-text="createdOrder?.payment_status || 'PENDING'">
                    </span>
                </div>

                <!-- Case 1: QRIS / E-Wallet -->
                <template x-if="createdOrder?.payment_method === 'ewallet'">
                    <div class="mt-5 flex flex-col items-center text-center space-y-3">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#f4ebe1] text-[#7a4836] text-xs font-semibold">
                                Scan QRIS
                            </span>
                            <!-- Countdown Badge -->
                            <template x-if="!isExpired">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-[#fdf3ee] text-[#b93826] text-xs font-semibold animate-pulse">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span x-text="timerDisplay"></span>
                                </span>
                            </template>
                            <template x-if="isExpired">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-red-100 text-red-700 text-xs font-semibold">
                                    Expired
                                </span>
                            </template>
                        </div>

                        <!-- QR Code Container -->
                        <div class="relative p-4 bg-white rounded-2xl border-2 border-[#ebdcd0] shadow-sm flex flex-col items-center justify-center">
                            <template x-if="createdOrder?.payment_qris_url">
                                <div class="relative">
                                    <img :src="createdOrder.payment_qris_url" alt="QRIS Midtrans"
                                        class="w-52 h-52 object-contain rounded-lg transition-all"
                                        :class="isExpired ? 'opacity-20 grayscale' : ''">
                                    <template x-if="isExpired">
                                        <div class="absolute inset-0 flex flex-col items-center justify-center text-center p-2">
                                            <p class="text-xs font-bold text-red-600">Kode QR Kedaluwarsa</p>
                                            <p class="text-[10px] text-[#7a6f68] mt-1">Batas waktu 5 menit telah habis.</p>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <template x-if="!createdOrder?.payment_qris_url">
                                <div class="w-52 h-52 flex flex-col items-center justify-center p-4 bg-[#f8f6f3] rounded-lg text-center border border-dashed border-[#dec9ba]">
                                    <p class="text-xs text-[#5e5449] font-medium">QRIS generated by Midtrans</p>
                                </div>
                            </template>
                        </div>

                        <p class="text-xs text-[#5e5449] max-w-xs leading-relaxed" x-show="!isExpired">
                            Buka aplikasi BCA Mobile, GoPay, OVO, ShopeePay, DANA, atau mobile banking Anda dan pindai kode QR di atas (berlaku 5 menit).
                        </p>
                    </div>
                </template>

                <!-- Case 2: Bank Transfer -->
                <template x-if="createdOrder?.payment_method !== 'ewallet'">
                    <div class="mt-5 space-y-3">
                        <div class="p-4 rounded-xl border border-[#ebdcd0] bg-[#faf7f2] space-y-3">
                            <div class="flex items-center justify-between pb-2 border-b border-[#e5dfd8]">
                                <span class="text-xs text-[#7a6f68]">Bank Tujuan</span>
                                <span class="text-sm font-bold text-[#2c2420]" x-text="createdOrder?.payment_bank || 'BCA'"></span>
                            </div>

                            <div>
                                <span class="text-xs text-[#7a6f68]">Nomor Rekening / Virtual Account</span>
                                <div class="flex items-center justify-between mt-1">
                                    <span class="text-base sm:text-lg font-mono font-bold text-[#c06547] tracking-wider"
                                        x-text="createdOrder?.payment_va_number || '1234567890'"></span>
                                    <button type="button" @click="copyToClipboard(createdOrder?.payment_va_number)"
                                        class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-white border border-[#dec9ba] text-[#2c2420] hover:bg-[#ebdcd0] transition-colors">
                                        <span x-text="copied ? 'Tersalin!' : 'Salin'"></span>
                                    </button>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-[#e5dfd8] flex items-center justify-between">
                                <span class="text-xs text-[#7a6f68]">Atas Nama</span>
                                <span class="text-xs font-medium text-[#2c2420]" x-text="createdOrder?.payment_holder || 'Decak Handmade'"></span>
                            </div>
                        </div>

                        <div class="p-3 rounded-lg bg-[#fcf9f5] border border-[#ebdcd0] text-[11px] text-[#7a6f68] leading-relaxed">
                            Transfer sesuai dengan nominal total tagihan. Verifikasi pembayaran akan diproses secara otomatis setelah transfer berhasil.
                        </div>
                    </div>
                </template>

                <!-- Action Buttons -->
                <div class="mt-6 pt-4 border-t border-[#e5dfd8] flex flex-col gap-2">
                    <a :href="createdOrder?.orders_url || '{{ route('user.orders') }}'"
                        class="w-full py-2.5 px-4 rounded-xl bg-[#2c2420] text-white text-xs font-semibold hover:bg-black transition-colors text-center">
                        Lihat Daftar Pesanan
                    </a>
                    <a href="{{ route('catalog.index') }}"
                        class="w-full py-2 px-4 rounded-xl border border-[#d3c9c1] text-[#2c2420] text-xs font-semibold hover:bg-[#f5eee6] transition-colors text-center">
                        Lanjut Belanja
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
