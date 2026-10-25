@extends('layouts.admin')

@section('title', 'Pesanan Masuk')
@section('subtitle', 'Total pesanan: '.$orders->total())

@section('content')
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" class="flex flex-wrap items-center gap-2">
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-brand-500">
                    <x-icon name="search" class="h-4 w-4" />
                </span>
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari no. pesanan atau pelanggan"
                       class="w-64 rounded-full border border-brand-300 bg-white py-2 pl-9 pr-3 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-200">
            </div>
            <select name="status" class="rounded-lg border border-brand-300 bg-white px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-200">
                <option value="">Semua status</option>
                @foreach ($statuses as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button class="admin-btn-secondary">Terapkan</button>
        </form>

        <a href="{{ route('admin.orders.create') }}" class="admin-btn-primary">
            <x-icon name="plus" class="h-4 w-4" />
            Catat pesanan
        </a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-line bg-white">
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>No. pesanan</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td class="font-medium text-ink">
                                <a href="{{ route('admin.orders.show', $order) }}" class="hover:text-brand-700">{{ $order->order_number }}</a>
                            </td>
                            <td class="text-slate-600">{{ wib($order->created_at) }}</td>
                            <td class="text-slate-600">
                                <p>{{ $order->customer?->name }}</p>
                                <p class="text-xs text-slate-400">{{ $order->customer?->phone }}</p>
                            </td>
                            <td class="text-slate-600">{{ rupiah($order->grand_total) }}</td>
                            <td><x-status-badge :status="$order->status" /></td>
                            <td>
                                <div class="flex justify-end">
                                    <a href="{{ route('admin.orders.show', $order) }}"
                                       class="admin-icon-btn admin-icon-btn-neutral"
                                       title="Lihat pesanan {{ $order->order_number }}" aria-label="Lihat pesanan {{ $order->order_number }}">
                                        <x-icon name="eye" class="h-4 w-4" />
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-muted">Belum ada pesanan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-line px-5 py-3">
            {{ $orders->links() }}
        </div>
    </div>
@endsection
