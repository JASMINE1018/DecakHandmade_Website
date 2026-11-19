<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Jumlah kartu produk yang ditampilkan pada carousel landing page.
     */
    private const FEATURED_LIMIT = 8;

    /**
     * Gambar cadangan ketika produk belum memiliki foto yang diunggah.
     *
     * @var array<string, string>
     */
    private const PRODUCT_FALLBACKS = [
        'handmade-bag' => 'images/products/tote-senja-natural-front.svg',
        'bag-charm' => 'images/home/bag-charm.jpg',
        'pouch' => 'images/products/tote-senja-natural-detail.svg',
        'pillow' => 'images/home/pillow.jpg',
        'home-accessories' => 'images/home/interior.jpg',
    ];

    public function index(): View
    {
        return view('home.index', [
            'heroSlides' => $this->heroSlides(),
            'featuredProducts' => $this->featuredProducts(),
            'categories' => $this->categories(),
            'whatsappUrl' => $this->whatsappUrl(),
        ]);
    }

    /**
     * Slide pada hero carousel.
     *
     * @return array<int, array{image: string, title: string, caption: string}>
     */
    private function heroSlides(): array
    {
        return [
            [
                'image' => asset('images/home/hero-display.webp'),
                'title' => 'Cekik Bag Series',
                'caption' => '100% Handmade Batik Fabric Strap',
            ],
            [
                'image' => asset('images/home/Rectangle.webp'),
                'title' => 'The Bunga Series',
                'caption' => 'Hand-stitched floral tote, one stitch at a time.',
            ],
            [
                'image' => asset('images/home/story-materials.webp'),
                'title' => 'Selected Materials',
                'caption' => 'Woven textures that carry the warmth of home.',
            ],
        ];
    }

    /**
     * Produk unggulan untuk landing page, dilengkapi kartu kurasi ketika
     * katalog masih memiliki sedikit produk.
     *
     * @return Collection<int, array{name: string, price: int, category: string, image: string, url: string, badge: string|null}>
     */
    private function featuredProducts(): Collection
    {
        $products = Product::query()
            ->active()
            ->with('category')
            ->latest('id')
            ->take(self::FEATURED_LIMIT)
            ->get()
            ->values();

        $cards = $products->map(function (Product $product, int $index): array {
            $gallery = is_array($product->gallery) ? array_values(array_filter($product->gallery)) : [];

            return [
                'name' => $product->name,
                'price' => 'Rp'.number_format((int) $product->base_price, 0, ',', '.'),
                'raw_price' => (int) $product->base_price,
                'category' => $product->category?->name ?? 'Handmade',
                'image' => $gallery !== []
                    ? $product->getImageUrl('card')
                    : $this->fallbackImage($product->category?->slug, $index),
                'url' => route('catalog.product.show', $product->slug),
                'badge' => $index === 0 ? 'Best Seller' : null,
            ];
        });

        $curated = collect($this->curatedProducts());

        return $cards
            ->concat($curated->slice(0, max(0, self::FEATURED_LIMIT - $cards->count())))
            ->take(self::FEATURED_LIMIT)
            ->values();
    }

    /**
     * Kartu kurasi agar landing page tetap terlihat lengkap saat katalog
     * belum diisi. Kartu ini hanya tampil ketika produk asli kurang dari 8.
     *
     * @return array<int, array{name: string, price: int, category: string, image: string, url: string, badge: string|null}>
     */
    private function curatedProducts(): array
    {
        $catalogUrl = route('catalog.index');

        return [
            [
                'name' => 'Bag Charm Cekik Keychain Gantungan Kunci Gajah Bebek Kelinci Batik',
                'price' => 'Rp70.000',
                'raw_price' => 70000,
                'category' => 'Bag Charm',
                'image' => asset('images/home/prod-bag-charm.webp'),
                'url' => $catalogUrl,
                'badge' => 'Best Seller',
            ],
            [
                'name' => 'Decak Bantal Leher Travel Batik DuoTone',
                'price' => 'Rp102.500',
                'raw_price' => 102500,
                'category' => 'Pillow',
                'image' => asset('images/home/prod-neck-pillow.webp'),
                'url' => $catalogUrl,
                'badge' => null,
            ],
            [
                'name' => 'Mukena Travel Mini + Mogu Pouch Mini Batik',
                'price' => 'Rp200.000',
                'raw_price' => 200000,
                'category' => 'Pouch',
                'image' => asset('images/home/prod-mukena-pouch.webp'),
                'url' => $catalogUrl,
                'badge' => null,
            ],
            [
                'name' => 'Cushion Cover Patchwork Ecoprint Natural Pastel',
                'price' => 'Rp275.000 - Rp1.275.000',
                'raw_price' => 275000,
                'category' => 'Pillow',
                'image' => asset('images/home/prod-cushion-patchwork.jpg'),
                'url' => $catalogUrl,
                'badge' => null,
            ],
        ];
    }

    /**
     * Empat kategori utama pada landing page.
     *
     * @return array<int, array{name: string, description: string, image: string, url: string}>
     */
    private function categories(): array
    {
        $summaries = [
            'handmade-bag' => 'Everyday carry, shaped by hand.',
            'bag-charm' => 'Small details that tell your story.',
            'pillow' => 'Softness for slower, warmer rooms.',
            'home-accessories' => 'Little rituals, thoughtfully made.',
        ];

        $images = [
            'handmade-bag' => 'images/home/cat-bag.jpg',
            'bag-charm' => 'images/home/cat-bag-hanger.jpg',
            'pillow' => 'images/home/cat-pillow.jpg',
            'home-accessories' => 'images/home/cat-house-accessories.jpg',
        ];

        $names = [
            'handmade-bag' => 'Bag',
            'bag-charm' => 'Bag Hanger',
            'pillow' => 'Pillow',
            'home-accessories' => 'House Accessories',
        ];

        return collect($summaries)
            ->map(fn (string $description, string $slug): array => [
                'name' => $names[$slug],
                'description' => $description,
                'image' => asset($images[$slug]),
                'url' => route('catalog.index', ['categories' => [$slug]]),
            ])
            ->values()
            ->all();
    }

    private function fallbackImage(?string $categorySlug, int $index): string
    {
        $fallback = self::PRODUCT_FALLBACKS[$categorySlug ?? ''] ?? null;

        if ($fallback !== null) {
            return asset($fallback);
        }

        $rotation = [
            'images/products/tote-senja-natural-front.svg',
            'images/products/tote-senja-cokelat-front.svg',
            'images/products/tote-senja-natural-detail.svg',
        ];

        return asset($rotation[$index % count($rotation)]);
    }

    private function whatsappUrl(): string
    {
        $phone = (string) (Setting::query()->where('key', 'store_phone')->value('value') ?? '0812-3456-7890');
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        } elseif (str_starts_with($digits, '8')) {
            $digits = '62'.$digits;
        }

        if ($digits === '') {
            $digits = '6281234567890';
        }

        $message = 'Hello Decak Handmade! I would like to inquire about your handmade products.';

        return 'https://wa.me/'.$digits.'?text='.rawurlencode($message);
    }
}
