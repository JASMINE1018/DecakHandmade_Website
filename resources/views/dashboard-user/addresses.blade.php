@extends('dashboard-user.layouts.app')

@section('title', 'Saved Addresses - Decak Handmade')

@section('content')
@php
    $addressList = $addresses->map(function($a) {
        return [
            'id' => $a->id,
            'label' => $a->label,
            'name' => $a->recipient_name,
            'phone' => $a->phone,
            'address' => $a->full_address,
            'city' => $a->city ?? '',
            'province' => $a->province ?? '',
            'postalCode' => $a->postal_code ?? '',
            'isPrimary' => (bool)$a->is_primary,
            'updateUrl' => route('user.addresses.update', $a),
            'deleteUrl' => route('user.addresses.destroy', $a),
            'primaryUrl' => route('user.addresses.primary', $a),
        ];
    });
@endphp

<div x-data="{
    isModalOpen: false,
    modalTitle: 'Add New Address',
    formAction: '{{ route('user.addresses.store') }}',
    formMethod: 'POST',
    form: {
        id: null,
        label: 'Home',
        recipient_name: '{{ addslashes($customer->name ?? '') }}',
        phone: '{{ addslashes($customer->phone ?? '') }}',
        full_address: '',
        city: 'Bandung',
        province: 'West Java',
        postal_code: '',
        is_primary: false
    },
    openAddModal() {
        this.modalTitle = 'Add New Address';
        this.formAction = '{{ route('user.addresses.store') }}';
        this.formMethod = 'POST';
        this.form = {
            id: null,
            label: 'Home',
            recipient_name: '{{ addslashes($customer->name ?? '') }}',
            phone: '{{ addslashes($customer->phone ?? '') }}',
            full_address: '',
            city: 'Bandung',
            province: 'West Java',
            postal_code: '',
            is_primary: false
        };
        this.isModalOpen = true;
    },
    openEditModal(item) {
        this.modalTitle = 'Edit Shipping Address';
        this.formAction = item.updateUrl;
        this.formMethod = 'PUT';
        this.form = {
            id: item.id,
            label: item.label,
            recipient_name: item.name,
            phone: item.phone,
            full_address: item.address,
            city: item.city,
            province: item.province,
            postal_code: item.postalCode,
            is_primary: item.isPrimary
        };
        this.isModalOpen = true;
    }
}">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-[#7a6f68] mb-3">
        <a href="{{ route('user.dashboard') }}" class="hover:text-[#2c2420]">Home</a>
        <span>/</span>
        <a href="{{ route('user.dashboard') }}" class="hover:text-[#2c2420]">My account</a>
        <span>/</span>
        <span class="text-[#2c2420] font-medium">Saved addresses</span>
    </nav>

    <!-- Header Greeting -->
    <div class="flex flex-col sm:flex-row sm:items-baseline justify-between gap-2 mb-8">
        <div>
            <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-[#2c2420]">
                Saved addresses
            </h1>
            <p class="text-sm text-[#7a6f68] mt-1">
                Manage your shipping addresses for smooth and accurate delivery.
            </p>
        </div>
        <div>
            <button type="button" @click="openAddModal" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#c06547] text-white text-sm font-semibold hover:bg-[#a8543b] transition-all shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Add new address</span>
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 rounded-xl bg-[#eaf0e7] border border-[#cfdfca] flex items-center gap-2 text-xs text-[#5a7355]">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <!-- Main Grid Content -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Sidebar Navigation -->
        @include('dashboard-user.partials.sidebar')

        <!-- Right Content Area -->
        <section class="lg:col-span-8 xl:col-span-9">
            @if($addresses->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @foreach($addresses as $item)
                    @php
                        $jsonItem = [
                            'id' => $item->id,
                            'label' => $item->label,
                            'name' => $item->recipient_name,
                            'phone' => $item->phone,
                            'address' => $item->full_address,
                            'city' => $item->city ?? '',
                            'province' => $item->province ?? '',
                            'postalCode' => $item->postal_code ?? '',
                            'isPrimary' => (bool)$item->is_primary,
                            'updateUrl' => route('user.addresses.update', $item),
                        ];
                    @endphp
                    <div class="rounded-2xl border p-5 sm:p-6 transition-all flex flex-col justify-between {{ $item->is_primary ? 'border-[#c06547] bg-[#faf7f2] shadow-xs' : 'border-[#e0ddd8] bg-white' }}">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold uppercase tracking-wider text-[#5e5449] bg-[#f5eee6] px-2.5 py-1 rounded-md">
                                        {{ $item->label }}
                                    </span>
                                    @if($item->is_primary)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-[#fdf3ee] text-[#c06547] border border-[#f0c9b9]">
                                            Primary Address
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="space-y-1.5 text-sm text-[#5e5449]">
                                <h3 class="font-bold text-base text-[#2c2420]">{{ $item->recipient_name }}</h3>
                                <p class="text-xs text-[#7a6f68]">{{ $item->phone }}</p>
                                <p class="text-sm text-[#453c34] leading-relaxed pt-1">{{ $item->full_address }}</p>
                                <p class="text-xs text-[#7a6f68]">
                                    {{ $item->city ? $item->city . ', ' : '' }}{{ $item->province ?? '' }} {{ $item->postal_code ?? '' }}
                                </p>
                            </div>
                        </div>

                        <!-- Card Action Footer -->
                        <div class="pt-5 mt-4 border-t border-[#f0ebe4] flex items-center justify-between gap-2">
                            <div class="flex items-center gap-3">
                                <button type="button" @click='openEditModal(@json($jsonItem))' class="text-xs font-semibold text-[#5e5449] hover:text-[#c06547] transition-colors">
                                    Edit address
                                </button>
                                @if(!$item->is_primary)
                                    <form method="POST" action="{{ route('user.addresses.destroy', $item) }}" onsubmit="return confirm('Are you sure you want to delete this address?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-medium text-[#7a6f68] hover:text-[#d94648] transition-colors">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </div>

                            @if(!$item->is_primary)
                                <form method="POST" action="{{ route('user.addresses.primary', $item) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-xs font-semibold text-[#c06547] hover:text-[#a8543b] transition-colors">
                                        Set as primary
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            @else
            <!-- Empty State -->
            <div class="rounded-2xl border border-dashed border-[#d3c9c1] p-12 text-center bg-white">
                <svg class="w-12 h-12 mx-auto text-[#a99e96] mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <h3 class="text-base font-bold text-[#2c2420]">No saved addresses yet</h3>
                <p class="text-xs text-[#7a6f68] mt-1 mb-4">Add your first shipping address to speed up your checkout.</p>
                <button type="button" @click="openAddModal" class="px-4 py-2 rounded-lg bg-[#c06547] text-white text-xs font-semibold hover:bg-[#a8543b]">
                    Add address
                </button>
            </div>
            @endif
        </section>
    </div>

    <!-- Modal Form Tambah / Edit Alamat -->
    <div x-cloak x-show="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div x-show="isModalOpen" 
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="isModalOpen = false"
            class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity"></div>

        <!-- Modal Dialog Container -->
        <div class="relative min-h-screen flex items-center justify-center p-4 text-center sm:p-0">
            <div x-show="isModalOpen"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-lg w-full p-6 sm:p-8 z-10">
                
                <div class="flex items-center justify-between pb-4 border-b border-[#e5dfd8]">
                    <h3 class="text-xl font-bold text-[#2c2420]" x-text="modalTitle">Add New Address</h3>
                    <button type="button" @click="isModalOpen = false" class="text-[#7a6f68] hover:text-[#2c2420]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form method="POST" :action="formAction" class="mt-5 space-y-4">
                    @csrf
                    <template x-if="formMethod === 'PUT'">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div>
                        <label class="block text-xs font-semibold text-[#5e5449] mb-1.5">Address label (e.g. Home, Office)</label>
                        <input type="text" name="label" x-model="form.label" required placeholder="Home"
                            class="w-full px-3.5 py-2.5 rounded-lg border border-[#e0ddd8] bg-white text-sm text-[#2c2420] focus:border-[#c06547] focus:ring-2 focus:ring-[#f0c9b9] outline-none">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-[#5e5449] mb-1.5">Recipient name</label>
                            <input type="text" name="recipient_name" x-model="form.recipient_name" required
                                class="w-full px-3.5 py-2.5 rounded-lg border border-[#e0ddd8] bg-white text-sm text-[#2c2420] focus:border-[#c06547] focus:ring-2 focus:ring-[#f0c9b9] outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#5e5449] mb-1.5">Phone number</label>
                            <input type="text" name="phone" x-model="form.phone" required
                                class="w-full px-3.5 py-2.5 rounded-lg border border-[#e0ddd8] bg-white text-sm text-[#2c2420] focus:border-[#c06547] focus:ring-2 focus:ring-[#f0c9b9] outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#5e5449] mb-1.5">Full address details</label>
                        <textarea name="full_address" x-model="form.full_address" rows="3" required placeholder="Street name, house number, building/unit, district"
                            class="w-full px-3.5 py-2.5 rounded-lg border border-[#e0ddd8] bg-white text-sm text-[#2c2420] focus:border-[#c06547] focus:ring-2 focus:ring-[#f0c9b9] outline-none"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-[#5e5449] mb-1.5">Province</label>
                            <input type="text" name="province" x-model="form.province" required
                                class="w-full px-3.5 py-2 rounded-lg border border-[#e0ddd8] bg-white text-sm text-[#2c2420] focus:border-[#c06547] focus:ring-2 focus:ring-[#f0c9b9] outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#5e5449] mb-1.5">City</label>
                            <input type="text" name="city" x-model="form.city" required
                                class="w-full px-3.5 py-2 rounded-lg border border-[#e0ddd8] bg-white text-sm text-[#2c2420] focus:border-[#c06547] focus:ring-2 focus:ring-[#f0c9b9] outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#5e5449] mb-1.5">Postal code</label>
                            <input type="text" name="postal_code" x-model="form.postal_code" required
                                class="w-full px-3.5 py-2 rounded-lg border border-[#e0ddd8] bg-white text-sm text-[#2c2420] focus:border-[#c06547] focus:ring-2 focus:ring-[#f0c9b9] outline-none">
                        </div>
                    </div>

                    <div class="pt-2 flex items-center gap-2">
                        <input type="checkbox" name="is_primary" value="1" id="modalPrimary" x-model="form.is_primary" class="rounded border-[#dec9ba] text-[#c06547] focus:ring-[#c06547]/30">
                        <label for="modalPrimary" class="text-xs text-[#5e5449] select-none cursor-pointer">
                            Set as primary address
                        </label>
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-3">
                        <button type="button" @click="isModalOpen = false" class="px-4 py-2.5 rounded-xl border border-[#e0ddd8] text-[#5e5449] text-sm font-semibold hover:bg-[#faf7f2]">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#c06547] text-white text-sm font-semibold hover:bg-[#a8543b] transition-colors shadow-xs">
                            Save address
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
