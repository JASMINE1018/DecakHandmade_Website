<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class SortBarController extends Controller
{
    /**
     * Pilihan urutan produk pada dropdown sort.
     *
     * @return array<string, string>
     */
    public function options(): array
    {
        return [
            'newest' => 'Newest',
            'oldest' => 'Oldest',
            'price_low' => 'Price: Low to High',
            'price_high' => 'Price: High to Low',
            'name_asc' => 'Name: A–Z',
            'name_desc' => 'Name: Z–A',
        ];
    }

    /**
     * Terapkan urutan produk ke query.
     */
    public function apply(Request $request, Builder $query): Builder
    {
        return match ($request->query('sort', 'newest')) {
            'oldest' => $query->oldest(),
            'price_low' => $query->orderBy('base_price'),
            'price_high' => $query->orderByDesc('base_price'),
            'name_asc' => $query->orderBy('name'),
            'name_desc' => $query->orderByDesc('name'),
            default => $query->latest(),
        };
    }
}
