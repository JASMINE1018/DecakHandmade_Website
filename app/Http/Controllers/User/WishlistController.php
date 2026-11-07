<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    protected function getCustomer(): Customer
    {
        return Auth::guard('customer')->user() ?? Auth::user();
    }

    public function index()
    {
        $customer = $this->getCustomer();
        $wishlistItems = $customer->wishlistProducts()->with('variants')->get()->map(function ($product) {
            $firstImg = null;
            if (is_array($product->gallery) && count($product->gallery) > 0) {
                $firstImg = '/storage/' . $product->gallery[0];
            }

            $stockStatus = 'Tersedia';
            $stockBadge = 'bg-[#eaf0e7] text-[#5a7355]';

            if ($product->stock <= 0) {
                $stockStatus = 'Habis';
                $stockBadge = 'bg-[#fdf3ee] text-[#b93826]';
            } elseif ($product->stock <= $product->min_stock) {
                $stockStatus = 'Tersisa ' . $product->stock . ' buah';
                $stockBadge = 'bg-[#fdf3ee] text-[#c06547]';
            }

            return [
                'id' => $product->id,
                'title' => $product->name,
                'material' => $product->description ? \Illuminate\Support\Str::limit($product->description, 45) : 'Karya handmade Decak',
                'price' => $product->base_price,
                'stockStatus' => $stockStatus,
                'stockBadge' => $stockBadge,
                'image' => $firstImg ?? '/images/products/tote-senja.png',
            ];
        });

        return view('dashboard-user.wishlist', compact('customer', 'wishlistItems'));
    }

    public function toggle(Request $request, Product $product)
    {
        $customer = $this->getCustomer();

        $existing = Wishlist::where('customer_id', $customer->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $status = 'removed';
            $message = 'Produk dihapus dari wishlist.';
        } else {
            Wishlist::create([
                'customer_id' => $customer->id,
                'product_id' => $product->id,
            ]);
            $status = 'added';
            $message = 'Produk ditambahkan ke wishlist.';
        }

        if ($request->wantsJson()) {
            return response()->json([
                'status' => $status,
                'message' => $message,
                'count' => $customer->wishlists()->count(),
            ]);
        }

        return back()->with('success', $message);
    }

    public function destroy(Product $product)
    {
        $customer = $this->getCustomer();

        Wishlist::where('customer_id', $customer->id)
            ->where('product_id', $product->id)
            ->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('user.wishlist')->with('success', 'Produk berhasil dihapus dari wishlist.');
    }
}
