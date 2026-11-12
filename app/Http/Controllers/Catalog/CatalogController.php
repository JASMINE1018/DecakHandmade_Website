<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(
        Request $request,
        ProductController $products,
        FilterBarController $filterBar,
        SortBarController $sortBar,
    ): View {
        return view('catalog.catalog-product.index', [
            'products' => $products->index($request),
            'categories' => $filterBar->categories(),
            'selectedCategories' => $filterBar->selectedCategories($request),
            'sortOptions' => $sortBar->options(),
        ]);
    }
}
