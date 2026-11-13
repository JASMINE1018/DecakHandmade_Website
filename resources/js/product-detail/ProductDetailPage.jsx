import { useCallback, useEffect, useMemo, useState } from 'react';

import CartDrawer from './components/CartDrawer';
import Footer from './components/Footer';
import Navbar from './components/Navbar';
import ProductGallery from './components/ProductGallery';
import ProductInfo from './components/ProductInfo';
import { product as defaultProduct } from './data/product';
import { resolveAppUrl } from './utils';

export default function ProductDetailPage({ product = defaultProduct, appUrl = '', assetUrl = '' }) {
    const [cart, setCart] = useState({ items: [], subtotal: 0, total_quantity: 0, count: 0 });
    const [cartOpen, setCartOpen] = useState(false);
    const [cartLoading, setCartLoading] = useState(false);
    const [cartError, setCartError] = useState(null);

    const [selectedColor, setSelectedColor] = useState(product.colors[0].name);
    const [quantity, setQuantity] = useState(1);
    const [activeImage, setActiveImage] = useState(0);

    // Small enhancements that make the demo feel like a real storefront.
    const [wishlisted, setWishlisted] = useState(Boolean(product.isWishlisted));
    const [wishlistLoading, setWishlistLoading] = useState(false);
    const [cartNotice, setCartNotice] = useState(null);

    const csrfToken =
        window.__PRODUCT_DETAIL_CONFIG__?.csrfToken ??
        document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ??
        '';

    const fetchCartSummary = useCallback(async () => {
        try {
            const res = await fetch(resolveAppUrl(appUrl, '/cart/summary'), {
                headers: { Accept: 'application/json' },
            });
            if (res.ok) {
                setCart(await res.json());
            }
        } catch {
            // ignore network error on initial load
        }
    }, [appUrl]);

    useEffect(() => {
        fetchCartSummary();
    }, [fetchCartSummary]);

    const cartCount = cart.total_quantity ?? 0;

    const images = useMemo(
        () => product.gallery[selectedColor] ?? product.gallery[product.colors[0].name] ?? [],
        [product, selectedColor],
    );

    useEffect(() => {
        if (!cartNotice) {
            return undefined;
        }

        const timeout = window.setTimeout(() => setCartNotice(null), 2200);

        return () => window.clearTimeout(timeout);
    }, [cartNotice]);

    const handleColorChange = (colorName) => {
        setSelectedColor(colorName);
        // Each colour has its own gallery, so start again from the first photo.
        setActiveImage(0);
    };

    const handleQuantityChange = (nextQuantity) => {
        const safeQuantity = Math.min(Math.max(1, Number(nextQuantity) || 1), product.stock);
        setQuantity(safeQuantity);
    };

    const handleAddToCart = async () => {
        if (!product.id) {
            setCartNotice({
                id: Date.now(),
                message: `Added ${quantity} × ${product.name} (${selectedColor}) to your cart.`,
            });
            setCartOpen(true);
            return;
        }

        const activeVariant = product.colors.find((c) => c.name === selectedColor);
        setCartError(null);
        setCartLoading(true);

        try {
            const res = await fetch(resolveAppUrl(appUrl, '/cart/items'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    product_id: product.id,
                    variant_id: activeVariant?.id ?? null,
                    quantity,
                }),
            });

            const data = await res.json();
            if (!res.ok) {
                setCartError(data.message ?? 'Gagal menambahkan ke keranjang.');
                setCartOpen(true);
                return;
            }

            setCart(data);
            setCartNotice({
                id: Date.now(),
                message: `Added ${quantity} × ${product.name} (${selectedColor}) to your cart.`,
            });
            setCartOpen(true);
        } catch {
            setCartError('Terjadi kesalahan jaringan.');
            setCartOpen(true);
        } finally {
            setCartLoading(false);
        }
    };

    const handleUpdateCartQty = async (key, nextQty) => {
        setCartError(null);
        setCartLoading(true);
        try {
            const res = await fetch(resolveAppUrl(appUrl, `/cart/items/${encodeURIComponent(key)}`), {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ quantity: nextQty }),
            });
            const data = await res.json();
            if (!res.ok) {
                setCartError(data.message ?? 'Gagal memperbarui jumlah.');
            } else {
                setCart(data);
            }
        } catch {
            setCartError('Terjadi kesalahan jaringan.');
        } finally {
            setCartLoading(false);
        }
    };

    const handleRemoveCartItem = async (key) => {
        setCartError(null);
        setCartLoading(true);
        try {
            const res = await fetch(resolveAppUrl(appUrl, `/cart/items/${encodeURIComponent(key)}`), {
                method: 'DELETE',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
            });
            if (res.ok) {
                setCart(await res.json());
            }
        } finally {
            setCartLoading(false);
        }
    };

    const handleToggleWishlist = async () => {
        if (!product.id || wishlistLoading) {
            return;
        }

        setWishlistLoading(true);
        try {
            const res = await fetch(resolveAppUrl(appUrl, `/user/wishlist/${product.id}/toggle`), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
            });

            if (res.status === 401) {
                window.location.href = resolveAppUrl(appUrl, '/user/login');
                return;
            }

            if (res.ok) {
                const data = await res.json();
                setWishlisted(data.status === 'added');
            }
        } catch (e) {
            console.error(e);
        } finally {
            setWishlistLoading(false);
        }
    };

    return (
        <div className="flex min-h-screen flex-col bg-cream">
            <Navbar
                cartCount={cartCount}
                appUrl={appUrl}
                onOpenCart={() => {
                    fetchCartSummary();
                    setCartOpen(true);
                }}
            />

            <main className="mx-auto w-full max-w-7xl flex-1 px-4 py-6 sm:px-6 lg:px-8 lg:py-10">
                {/* Breadcrumb */}
                <nav aria-label="Breadcrumb" className="mb-6 text-sm text-muted">
                    <ol className="flex flex-wrap items-center gap-1.5">
                        <li>
                            <a href={resolveAppUrl(appUrl, '/')} className="transition-colors hover:text-[#C86A4B]">
                                Home
                            </a>
                        </li>
                        <li aria-hidden="true" className="text-line">
                            /
                        </li>
                        <li>
                            <a
                                href={resolveAppUrl(appUrl, '/catalog')}
                                className="transition-colors hover:text-[#C86A4B]"
                            >
                                Catalog
                            </a>
                        </li>
                        <li aria-hidden="true" className="text-line">
                            /
                        </li>
                        <li aria-current="page" className="font-medium text-ink">
                            {product.name}
                        </li>
                    </ol>
                </nav>

                <div className="grid items-start gap-8 lg:grid-cols-2 lg:gap-12 xl:gap-16">
                    <div className="lg:sticky lg:top-28">
                        <ProductGallery
                            images={images}
                            activeIndex={activeImage}
                            onSelect={setActiveImage}
                            productName={product.name}
                            assetUrl={assetUrl}
                        />
                    </div>

                    <ProductInfo
                        product={product}
                        selectedColor={selectedColor}
                        quantity={quantity}
                        wishlisted={wishlisted}
                        notice={cartNotice?.message ?? ''}
                        onColorChange={handleColorChange}
                        onQuantityChange={handleQuantityChange}
                        onAddToCart={handleAddToCart}
                        onToggleWishlist={handleToggleWishlist}
                    />
                </div>
            </main>

            <Footer />

            <CartDrawer
                open={cartOpen}
                onClose={() => setCartOpen(false)}
                cart={cart}
                loading={cartLoading}
                error={cartError}
                onUpdateQty={handleUpdateCartQty}
                onRemoveItem={handleRemoveCartItem}
                appUrl={appUrl}
            />
        </div>
    );
}
