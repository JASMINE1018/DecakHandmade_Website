@extends('dashboard-user.layouts.app')

@section('title', 'Alamat Tersimpan - Decak Handmade')

@section('content')
<div x-data="{
    isModalOpen: false,
    modalTitle: 'Tambah alamat baru',
    addresses: [
        {
            id: 1,
            label: 'Rumah',
            name: 'Ayu Pramesti',
            phone: '0812 3456 7890',
            address: 'Jl. Melati No. 18, RT 02/RW 04, Sukajadi',
            city: 'Kota Bandung',
            province: 'Jawa Barat',
            postalCode: '40162',
            isPrimary: true
        },
        {
            id: 2,
            label: 'Studio / Kantor',
            name: 'Ayu Pramesti',
            phone: '0812 9876 5432',
            address: 'Jl. Riau No. 45, Citarum, Kec. Bandung Wetan',
            city: 'Kota Bandung',
            province: 'Jawa Barat',
            postalCode: '40115',
            isPrimary: false
        }
    ],
    form: {
        id: null,
        label: '',
        name: '',
        phone: '',
        address: '',
        city: '',
        province: '',
        postalCode: '',
        isPrimary: false
    },
    openAddModal() {
        this.modalTitle = 'Tambah alamat baru';
        this.form = {
            id: null,
            label: 'Rumah',
            name: 'Ayu Pramesti',
            phone: '0812 3456 7890',
            address: '',
            city: 'Kota Bandung',
            province: 'Jawa Barat',
            postalCode: '',
            isPrimary: false
        };
        this.isModalOpen = true;
    },
    openEditModal(item) {
        this.modalTitle = 'Ubah alamat pengiriman';
        this.form = { ...item };
        this.isModalOpen = true;
    },
    saveAddress() {
        if (this.form.id) {
            const index = this.addresses.findIndex(a => a.id === this.form.id);
            if (index !== -1) {
                this.addresses[index] = { ...this.form };
            }
        } else {
            const newId = Date.now();
            if (this.form.isPrimary) {
                this.addresses.forEach(a => a.isPrimary = false);
            }
            this.addresses.push({ ...this.form, id: newId });
        }
        this.isModalOpen = false;
    },
    setPrimary(id) {
        this.addresses.forEach(a => a.isPrimary = (a.id === id));
    },
    deleteAddress(id) {
        if (confirm('Apakah Anda yakin ingin menghapus alamat ini?')) {
            this.addresses = this.addresses.filter(a => a.id !== id);
        }
    }
}">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-[#7a6f68] mb-3">
        <a href="{{ route('user.dashboard') }}" class="hover:text-[#2c2420]">Beranda</a>
        <span>/</span>
        <a href="{{ route('user.dashboard') }}" class="hover:text-[#2c2420]">Akun saya</a>
        <span>/</span>
        <span class="text-[#2c2420] font-medium">Alamat tersimpan</span>
    </nav>

    <!-- Header Greeting -->
    <div class="flex flex-col sm:flex-row sm:items-baseline justify-between gap-2 mb-8">
        <div>
            <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-[#2c2420]">
                Alamat tersimpan
            </h1>
            <p class="text-sm text-[#7a6f68] mt-1">
                Kelola alamat pengiriman untuk kemudahan dan ketepatan kirim pesanan Anda.
            </p>
        </div>
        <div>
            <button type="button" @click="openAddModal" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#c06547] text-white text-sm font-semibold hover:bg-[#a8543b] transition-all shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Tambah alamat baru</span>
            </button>
        </div>
    </div>

    <!-- Main Grid Content -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Sidebar Navigation -->
        @include('dashboard-user.partials.sidebar')

        <!-- Right Content Area -->
        <section class="lg:col-span-8 xl:col-span-9">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <template x-for="item in addresses" :key="item.id">
                    <div class="rounded-2xl border p-5 sm:p-6 transition-all flex flex-col justify-between"
                        :class="item.isPrimary ? 'border-[#c06547] bg-[#faf7f2] shadow-xs' : 'border-[#e0ddd8] bg-white'">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold uppercase tracking-wider text-[#5e5449] bg-[#f5eee6] px-2.5 py-1 rounded-md" x-text="item.label">
                                        Rumah
                                    </span>
                                    <template x-if="item.isPrimary">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-[#fdf3ee] text-[#c06547] border border-[#f0c9b9]">
                                            Alamat Utama
                                        </span>
                                    </template>
                                </div>
                            </div>

                            <div class="space-y-1.5 text-sm text-[#5e5449]">
                                <h3 class="font-bold text-base text-[#2c2420]" x-text="item.name">Ayu Pramesti</h3>
                                <p class="text-xs text-[#7a6f68]" x-text="item.phone">0812 3456 7890</p>
                                <p class="text-sm text-[#453c34] leading-relaxed pt-1" x-text="item.address"></p>
                                <p class="text-xs text-[#7a6f68]">
                                    <span x-text="item.city"></span>, <span x-text="item.province"></span> <span x-text="item.postalCode"></span>
                                </p>
                            </div>
                        </div>

                        <!-- Card Action Footer -->
                        <div class="pt-5 mt-4 border-t border-[#f0ebe4] flex items-center justify-between gap-2">
                            <div class="flex items-center gap-3">
                                <button type="button" @click="openEditModal(item)" class="text-xs font-semibold text-[#5e5449] hover:text-[#c06547] transition-colors">
                                    Ubah alamat
                                </button>
                                <template x-if="!item.isPrimary">
                                    <button type="button" @click="deleteAddress(item.id)" class="text-xs font-medium text-[#7a6f68] hover:text-[#d94648] transition-colors">
                                        Hapus
                                    </button>
                                </template>
                            </div>

                            <template x-if="!item.isPrimary">
                                <button type="button" @click="setPrimary(item.id)" class="text-xs font-semibold text-[#c06547] hover:text-[#a8543b] transition-colors">
                                    Jadikan utama
                                </button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Empty State -->
            <div x-show="addresses.length === 0" class="rounded-2xl border border-dashed border-[#d3c9c1] p-12 text-center bg-white">
                <svg class="w-12 h-12 mx-auto text-[#a99e96] mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <h3 class="text-base font-bold text-[#2c2420]">Belum ada alamat tersimpan</h3>
                <p class="text-xs text-[#7a6f68] mt-1 mb-4">Tambahkan alamat pertama Anda untuk mempercepat pesanan.</p>
                <button type="button" @click="openAddModal" class="px-4 py-2 rounded-lg bg-[#c06547] text-white text-xs font-semibold hover:bg-[#a8543b]">
                    Tambah alamat
                </button>
            </div>
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
                    <h3 class="text-xl font-bold text-[#2c2420]" x-text="modalTitle">Tambah alamat baru</h3>
                    <button type="button" @click="isModalOpen = false" class="text-[#7a6f68] hover:text-[#2c2420]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="saveAddress" class="mt-5 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-[#5e5449] mb-1.5">Label alamat (Contoh: Rumah, Kantor)</label>
                        <input type="text" x-model="form.label" required placeholder="Rumah"
                            class="w-full px-3.5 py-2.5 rounded-lg border border-[#e0ddd8] bg-white text-sm text-[#2c2420] focus:border-[#c06547] focus:ring-2 focus:ring-[#f0c9b9] outline-none">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-[#5e5449] mb-1.5">Nama penerima</label>
                            <input type="text" x-model="form.name" required
                                class="w-full px-3.5 py-2.5 rounded-lg border border-[#e0ddd8] bg-white text-sm text-[#2c2420] focus:border-[#c06547] focus:ring-2 focus:ring-[#f0c9b9] outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#5e5449] mb-1.5">Nomor telepon</label>
                            <input type="text" x-model="form.phone" required
                                class="w-full px-3.5 py-2.5 rounded-lg border border-[#e0ddd8] bg-white text-sm text-[#2c2420] focus:border-[#c06547] focus:ring-2 focus:ring-[#f0c9b9] outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#5e5449] mb-1.5">Alamat lengkap</label>
                        <textarea x-model="form.address" rows="3" required placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan/kecamatan"
                            class="w-full px-3.5 py-2.5 rounded-lg border border-[#e0ddd8] bg-white text-sm text-[#2c2420] focus:border-[#c06547] focus:ring-2 focus:ring-[#f0c9b9] outline-none"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-[#5e5449] mb-1.5">Provinsi</label>
                            <input type="text" x-model="form.province" required
                                class="w-full px-3.5 py-2 rounded-lg border border-[#e0ddd8] bg-white text-sm text-[#2c2420] focus:border-[#c06547] focus:ring-2 focus:ring-[#f0c9b9] outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#5e5449] mb-1.5">Kota / Kab.</label>
                            <input type="text" x-model="form.city" required
                                class="w-full px-3.5 py-2 rounded-lg border border-[#e0ddd8] bg-white text-sm text-[#2c2420] focus:border-[#c06547] focus:ring-2 focus:ring-[#f0c9b9] outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#5e5449] mb-1.5">Kode pos</label>
                            <input type="text" x-model="form.postalCode" required
                                class="w-full px-3.5 py-2 rounded-lg border border-[#e0ddd8] bg-white text-sm text-[#2c2420] focus:border-[#c06547] focus:ring-2 focus:ring-[#f0c9b9] outline-none">
                        </div>
                    </div>

                    <div class="pt-2 flex items-center gap-2">
                        <input type="checkbox" id="modalPrimary" x-model="form.isPrimary" class="rounded border-[#dec9ba] text-[#c06547] focus:ring-[#c06547]/30">
                        <label for="modalPrimary" class="text-xs text-[#5e5449] select-none cursor-pointer">
                            Jadikan sebagai alamat utama
                        </label>
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-3">
                        <button type="button" @click="isModalOpen = false" class="px-4 py-2.5 rounded-xl border border-[#e0ddd8] text-[#5e5449] text-sm font-semibold hover:bg-[#faf7f2]">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#c06547] text-white text-sm font-semibold hover:bg-[#a8543b] transition-colors shadow-xs">
                            Simpan alamat
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
