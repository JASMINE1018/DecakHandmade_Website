import { CheckIcon, HeartIcon, ShoppingBagIcon, WhatsAppIcon } from './icons';
import QuantityStepper from './QuantityStepper';
import { formatRupiah } from '../utils';

export default function ProductInfo({
    product,
    selectedColor,
    quantity,
    wishlisted,
    notice,
    onColorChange,
    onQuantityChange,
    onAddToCart,
    onToggleWishlist,
}) {
    const isAvailable = product.stock > 0;
    const addedToCart = Boolean(notice);
    const whatsappMessage = encodeURIComponent(
        `Hello Decak Handmade, I'd like a custom consultation for ${product.name} (${product.sku}).`,
    );
    const whatsappHref = `https://wa.me/${product.whatsappNumber}?text=${whatsappMessage}`;

    return (
        <section aria-labelledby="product-title" className="flex flex-col">
            <p className="text-[11px] font-semibold uppercase tracking-[0.22em] text-muted">{product.sku}</p>

            <h1 id="product-title" className="mt-2 text-3xl font-bold tracking-tight text-ink sm:text-4xl">
                {product.name}
            </h1>

            <div className="mt-4 flex flex-wrap items-center justify-between gap-x-6 gap-y-2">
                <p className="text-3xl font-bold tracking-tight text-[#C86A4B]">{formatRupiah(product.price)}</p>

                {isAvailable ? (
                    <span className="text-sm font-semibold text-success-ink">
                        In stock · {product.stock} items
                    </span>
                ) : (
                    <span className="text-sm font-semibold text-slate-600">Out of stock</span>
                )}
            </div>

            <p className="mt-5 max-w-2xl text-sm leading-7 text-muted sm:text-base">{product.description}</p>

            {/* Colour selector */}
            <div className="mt-7">
                <div className="flex items-center justify-between gap-4">
                    <p className="text-sm font-semibold text-ink">Color</p>
                    <p className="text-sm text-muted">{selectedColor}</p>
                </div>

                <div className="mt-3 flex flex-wrap gap-2.5" role="group" aria-label="Select product color">
                    {product.colors.map((color) => {
                        const isSelected = selectedColor === color.name;

                        return (
                            <button
                                key={color.name}
                                type="button"
                                onClick={() => onColorChange(color.name)}
                                aria-pressed={isSelected}
                                className={`inline-flex items-center gap-2.5 rounded-full border px-3.5 py-2 text-sm font-medium transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C86A4B]/40 focus-visible:ring-offset-2 ${
                                    isSelected
                                        ? 'border-[#C86A4B] bg-brand-50 text-ink'
                                        : 'border-line bg-white text-ink hover:border-brand-300'
                                }`}
                            >
                                <span
                                    aria-hidden="true"
                                    className="h-6 w-6 rounded-full border shadow-inner"
                                    style={{ backgroundColor: color.swatch, borderColor: color.border }}
                                />
                                {color.name}
                                {isSelected && <CheckIcon className="h-4 w-4 text-[#C86A4B]" />}
                            </button>
                        );
                    })}
                </div>
            </div>

            {/* Minimalist product specifications */}
            <dl className="mt-7 grid grid-cols-2 gap-x-6 gap-y-4">
                <div>
                    <dt className="text-sm font-medium text-muted">Material</dt>
                    <dd className="mt-1 text-sm font-normal leading-6 text-ink">{product.material}</dd>
                </div>
                <div>
                    <dt className="text-sm font-medium text-muted">Size</dt>
                    <dd className="mt-1 text-sm font-normal leading-6 text-ink">{product.dimensions}</dd>
                </div>
            </dl>

            {/* Quantity */}
            <QuantityStepper quantity={quantity} stock={product.stock} onChange={onQuantityChange} />

            {/* Actions */}
            <div className="mt-6 flex items-stretch gap-3">
                <button
                    type="button"
                    onClick={onAddToCart}
                    className="inline-flex min-h-[52px] flex-1 items-center justify-center gap-2 rounded-full bg-[#C86A4B] px-6 py-3.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-[#B85C3F] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C86A4B]/50 focus-visible:ring-offset-2"
                >
                    {addedToCart ? (
                        <>
                            <CheckIcon className="h-5 w-5" />
                            Added to cart
                        </>
                    ) : (
                        <>
                            <ShoppingBagIcon className="h-5 w-5" />
                            Add to cart
                        </>
                    )}
                </button>

                <button
                    type="button"
                    onClick={onToggleWishlist}
                    aria-pressed={wishlisted}
                    aria-label={wishlisted ? 'Remove from wishlist' : 'Add to wishlist'}
                    className={`inline-flex h-[52px] w-[52px] shrink-0 items-center justify-center rounded-full border transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C86A4B]/40 focus-visible:ring-offset-2 ${
                        wishlisted
                            ? 'border-[#C86A4B] bg-brand-50 text-[#C86A4B]'
                            : 'border-line bg-white text-ink hover:border-brand-300 hover:bg-brand-50 hover:text-[#C86A4B]'
                    }`}
                >
                    <HeartIcon filled={wishlisted} />
                </button>
            </div>

            {/* Custom consultation */}
            <a
                href={whatsappHref}
                target="_blank"
                rel="noreferrer"
                className="mt-3 inline-flex min-h-[52px] w-full items-center justify-center gap-2 rounded-full bg-[#5A2A18] px-5 py-3.5 text-center text-sm font-semibold text-white shadow-sm transition-colors hover:bg-[#4A2213] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#5A2A18]/40 focus-visible:ring-offset-2"
            >
                <WhatsAppIcon className="h-5 w-5" />
                Specialized Custom Consultation (via WA)
            </a>

            {/* Screen-reader announcement only; visual feedback happens on the button. */}
            <p className="sr-only" role="status" aria-live="polite" aria-atomic="true">
                {notice}
            </p>
        </section>
    );
}
