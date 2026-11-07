import { useEffect, useMemo, useState } from 'react';

import Footer from './components/Footer';
import Navbar from './components/Navbar';
import ProductGallery from './components/ProductGallery';
import ProductInfo from './components/ProductInfo';
import { product as defaultProduct } from './data/product';
import { resolveAppUrl } from './utils';

/** Start with an empty cart so the badge only appears after the first add-to-cart. */
const INITIAL_CART_COUNT = 0;

export default function ProductDetailPage({ product = defaultProduct, appUrl = '', assetUrl = '' }) {
    const [cartCount, setCartCount] = useState(INITIAL_CART_COUNT);
    const [selectedColor, setSelectedColor] = useState(product.colors[0].name);
    const [quantity, setQuantity] = useState(1);
    const [activeImage, setActiveImage] = useState(0);

    // Small enhancements that make the demo feel like a real storefront.
    const [wishlisted, setWishlisted] = useState(false);
    const [cartNotice, setCartNotice] = useState(null);

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

    const handleAddToCart = () => {
        setCartCount((currentCount) => currentCount + quantity);
        setCartNotice({
            id: Date.now(),
            message: `Added ${quantity} × ${product.name} (${selectedColor}) to your cart.`,
        });
    };

    const handleToggleWishlist = () => {
        setWishlisted((current) => !current);
    };

    return (
        <div className="flex min-h-screen flex-col bg-cream">
            <Navbar cartCount={cartCount} appUrl={appUrl} />

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
        </div>
    );
}
