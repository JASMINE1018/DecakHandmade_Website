@props(['status'])

@php
    $styles = [
        'processing' => 'bg-brand-500 text-white',
        'shipped' => 'bg-brand-100 text-brand-800',
        'completed' => 'bg-success-soft text-success-ink',
        'cancelled' => 'bg-danger/10 text-danger',
    ];
@endphp

<span class="admin-badge {{ $styles[$status] ?? 'bg-slate-100 text-slate-700' }}">
    {{ \App\Models\Order::STATUSES[$status] ?? $status }}
</span>
