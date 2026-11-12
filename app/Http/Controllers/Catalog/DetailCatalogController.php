<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DetailCatalogController extends Controller
{
    public function show(string $slug): View
    {
        return view('catalog.catalog-product.show', [
            'slug' => $slug,
        ]);
    }
}
