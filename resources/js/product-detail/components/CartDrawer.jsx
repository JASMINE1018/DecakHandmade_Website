import { CloseIcon, ShoppingBagIcon } from './icons';
import { formatRupiah, resolveAppUrl } from '../utils';

export default function CartDrawer({
    open,
    onClose,
    cart,
    loading,
    error,
    onUpdateQty,
    onRemoveItem,
    appUrl = '',
}) {
    if (!open) {
        return null;
    }

    const items = cart?.items ?? [];
    const subtotal = cart?.subtotal ?? 0;

    return (
        <div className="relative z-[70]" role="dialog" aria-modal="true" aria-label="Keranjang Belanja">
            <div
                onClick={onClose}
                className="fixed inset-0 bg-[#2c2420]/45 backdrop-blur-[2px] transition-opacity"
            />

            <aside className="fixed inset-y-0 right-0 flex w-full max-w-md flex-col border-l border-line bg-cream shadow-2xl">
                <div className="flex items-start justify-between border-b border-line px-6 py-5">
                    <div>
                        <p className="text-xs font-semibold uppercase tracking-wider text-[#C86A4B]">
                            Keranjang Belanja
                        </p>
                        <h2 className="mt-0.5 text-2xl font-bold text-ink">
                            {items.length > 0 ? 'Produk' : 'Keranjang Belanja'}
                        </h2>
                    </div>
                    <button
                        type="button"
                        onClick={onClose}
                        aria-label="Tutup keranjang"
                        className="inline-flex h-9 w-9 items-center justify-center rounded-full text-muted transition-colors hover:bg-brand-50 hover:text-ink"
                    >
                        <CloseIcon />
                    </button>
                </div>

                {error && (
                    <div className="mx-6 mt-4 rounded-xl border border-[#f0c9b9] bg-[#fdf3ee] px-4 py-2.5 text-xs text-[#b93826]">
                        {error}
                    </div>
                )}

                {items.length === 0 ? (
                    <div className="flex flex-1 flex-col items-center justify-center px-6 text-center">
                        <div className="mb-5 flex h-20 w-20 items-center justify-center rounded-full border border-line bg-white text-[#C86A4B] shadow-xs">
                            <ShoppingBagIcon />
                        </div>
                        <h3 className="text-lg font-bold text-ink">Keranjang Kosong</h3>
                        <p className="mt-1.5 text-sm text-muted">
                            Belum ada produk di keranjang belanja Anda.
                        </p>
                        <a
                            href={resolveAppUrl(appUrl, '/catalog')}
                            onClick={onClose}
                            className="mt-6 inline-flex w-full max-w-xs items-center justify-center rounded-xl bg-[#C86A4B] px-6 py-3 text-sm font-semibold text-white shadow-xs transition-colors hover:bg-[#b55a3c]"
                        >
                            Mulai Belanja
                        </a>
                    </div>
                ) : (
                    <div className="flex flex-1 flex-col overflow-hidden">
                        <div className="flex-1 divide-y divide-line overflow-y-auto px-6 py-4">
                            {items.map((item) => (
                                <div key={item.key} className="flex items-start gap-4 py-4">
                                    <img
                                        src={item.image}
                                        alt={item.name}
                                        className="h-18 w-18 shrink-0 rounded-xl border border-line bg-white object-cover"
                                    />
                                    <div className="min-w-0 flex-1">
                                        <div className="flex items-start justify-between gap-2">
                                            <h4 className="truncate text-sm font-bold text-ink">{item.name}</h4>
                                            <button
                                                type="button"
                                                onClick={() => onRemoveItem(item.key)}
                                                disabled={loading}
                                                aria-label="Hapus item"
                                                className="shrink-0 text-xs text-muted transition-colors hover:text-[#b93826]"
                                            >
                                                Hapus
                                            </button>
                                        </div>
                                        {item.variant_name && (
                                            <p className="mt-0.5 text-xs text-muted">Varian: {item.variant_name}</p>
                                        )}
                                        <div className="mt-3 flex items-center justify-between gap-2">
                                            <span className="text-sm font-bold text-[#C86A4B]">
                                                {formatRupiah(item.unit_price)}
                                            </span>
                                            <div className="inline-flex items-center rounded-lg border border-line bg-white">
                                                <button
                                                    type="button"
                                                    onClick={() => onUpdateQty(item.key, item.quantity - 1)}
                                                    disabled={loading}
                                                    className="flex h-7 w-7 items-center justify-center rounded-l-lg text-sm text-ink hover:bg-brand-50"
                                                >
                                                    –
                                                </button>
                                                <span className="w-8 text-center text-xs font-semibold text-ink">
                                                    {item.quantity}
                                                </span>
                                                <button
                                                    type="button"
                                                    onClick={() => onUpdateQty(item.key, item.quantity + 1)}
                                                    disabled={loading || item.quantity >= item.max_stock}
                                                    className="flex h-7 w-7 items-center justify-center rounded-r-lg text-sm text-ink hover:bg-brand-50 disabled:opacity-40"
                                                >
                                                    +
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>

                        <div className="space-y-4 border-t border-line bg-white px-6 py-5">
                            <div className="space-y-2 text-sm">
                                <div className="flex items-center justify-between text-muted">
                                    <span>Subtotal</span>
                                    <span className="font-semibold text-ink">{formatRupiah(subtotal)}</span>
                                </div>
                                <div className="flex items-center justify-between border-t border-line pt-2.5 text-base font-bold">
                                    <span className="text-ink">Total</span>
                                    <span className="text-[#C86A4B]">{formatRupiah(subtotal)}</span>
                                </div>
                            </div>

                            <div className="space-y-2.5">
                                <button
                                    type="button"
                                    onClick={onClose}
                                    className="w-full rounded-xl border border-line bg-white px-5 py-2.5 text-sm font-semibold text-ink transition-colors hover:bg-brand-50"
                                >
                                    Lanjut Belanja
                                </button>
                                <a
                                    href={resolveAppUrl(appUrl, '/user/checkout')}
                                    className="flex w-full items-center justify-center rounded-xl bg-[#C86A4B] px-5 py-3 text-sm font-semibold text-white shadow-xs transition-colors hover:bg-[#b55a3c]"
                                >
                                    Checkout
                                </a>
                            </div>
                        </div>
                    </div>
                )}
            </aside>
        </div>
    );
}
