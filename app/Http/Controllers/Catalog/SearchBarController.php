<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class SearchBarController extends Controller
{
    /**
     * Ambil kata kunci pencarian dari request.
     */
    public function term(Request $request): ?string
    {
        $term = trim((string) $request->query('search', ''));

        return $term === '' ? null : $term;
    }

    /**
     * Terapkan kata kunci pencarian ke query produk.
     */
    public function apply(Request $request, Builder $query): Builder
    {
        $term = $this->term($request);

        if ($term === null) {
            return $query;
        }

        return $query->where(function (Builder $query) use ($term) {
            $query->where('name', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
                ->orWhere('sku', 'like', "%{$term}%");
        });
    }
}
