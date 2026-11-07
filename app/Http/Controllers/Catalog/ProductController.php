<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private readonly SearchBarController $searchBar,
        private readonly FilterBarController $filterBar,
        private readonly SortBarController $sortBar,
    ) {}

    /**
     * Ambil daftar produk untuk halaman catalog.
     */
    public function index(Request $request): LengthAwarePaginator
    {
        $query = Product::active()->with([
            'category',
            'variants' => fn ($query) => $query->where('is_active', true),
        ]);

        $query = $this->searchBar->apply($request, $query);
        $query = $this->filterBar->apply($request, $query);
        $query = $this->sortBar->apply($request, $query);

        return $query->paginate(12)->withQueryString();
    }
}
