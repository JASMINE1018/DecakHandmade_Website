<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class FilterBarController extends Controller
{
    /**
     * Daftar kategori yang ditampilkan pada sidebar filter.
     *
     * @return array<string, string>
     */
    public function categories(): array
    {
        return [
            'all' => 'All Products',
            'handmade-bag' => 'Handmade Bag',
            'bag-charm' => 'Bag Charm',
            'pouch' => 'Pouch',
            'pillow' => 'Pillow',
            'home-accessories' => 'Home Accessories',
        ];
    }

    /**
     * Ambil kategori yang dipilih dari request.
     *
     * @return array<int, string>
     */
    public function selectedCategories(Request $request): array
    {
        $selected = $request->query('categories', []);

        if (! is_array($selected)) {
            $selected = [$selected];
        }

        return array_values(array_filter($selected, fn ($value) => is_string($value) && $value !== ''));
    }

    /**
     * Terapkan filter kategori ke query produk.
     */
    public function apply(Request $request, Builder $query): Builder
    {
        $selected = $this->selectedCategories($request);

        if ($selected === [] || in_array('all', $selected, true)) {
            return $query;
        }

        return $query->whereHas('category', fn (Builder $query) => $query->whereIn('slug', $selected));
    }
}
