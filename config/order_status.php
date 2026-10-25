<?php

use App\Models\Order;

return [

    'labels' => Order::STATUSES,

    /*
    | Transisi yang diizinkan. Kunci = status asal, nilai = status tujuan.
    | Status akhir (completed, cancelled) tidak memiliki transisi keluar.
    */
    'transitions' => [
        Order::STATUS_PROCESSING => [Order::STATUS_SHIPPED, Order::STATUS_CANCELLED],
        Order::STATUS_SHIPPED => [Order::STATUS_COMPLETED, Order::STATUS_PROCESSING, Order::STATUS_CANCELLED],
        Order::STATUS_COMPLETED => [],
        Order::STATUS_CANCELLED => [],
    ],

    'cancel_reasons' => [
        'customer_request' => 'Permintaan pelanggan',
        'out_of_stock' => 'Stok tidak tersedia',
        'mistake' => 'Kesalahan input',
        'other' => 'Lainnya',
    ],

];
