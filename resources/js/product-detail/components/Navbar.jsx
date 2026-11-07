import { useState } from 'react';

import { ArrowRightIcon, CloseIcon, MenuIcon, ShoppingBagIcon } from './icons';
import { resolveAppUrl } from '../utils';

export default function Navbar({ cartCount = 0, appUrl = '' }) {
    const [mobileOpen, setMobileOpen] = useState(false);

    const href = (path) => resolveAppUrl(appUrl, path);

    const links = [
        { label: 'Home', href: href('/') },
        { label: 'Catalog', href: href('/catalog'), active: true },
        { label: 'Our story', href: '#our-story' },
        { label: 'My account', href: '#my-account' },
    ];

    return (
        <header className="sticky top-0 z-50 border-b border-line/60 bg-cream/90 backdrop-blur-md">
            <nav aria-label="Main navigation" className="mx-auto flex h-20 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                {/* Brand */}
                <a href={href('/')} className="flex items-center gap-3">
                    <span className="flex h-10 w-10 items-center justify-center rounded-full bg-[#C86A4B] text-sm font-bold text-white shadow-sm">
                        DH
                    </span>
                    <span className="text-lg font-bold tracking-tight text-ink">
                        Decak <span className="text-[#C86A4B]">Handmade</span>
                    </span>
                </a>

                {/* Desktop navigation */}
                <ul className="hidden items-center gap-8 text-sm font-medium md:flex">
                    {links.map((link) => (
                        <li key={link.label}>
                            <a
                                href={link.href}
                                className={`transition-colors hover:text-[#C86A4B] ${
                                    link.active ? 'text-[#C86A4B]' : 'text-ink'
                                }`}
                            >
                                {link.label}
                            </a>
                        </li>
                    ))}
                </ul>

                {/* Actions */}
                <div className="flex items-center gap-2 sm:gap-3">
                    <button
                        type="button"
                        aria-label={`Shopping cart${cartCount > 0 ? `, ${cartCount} item${cartCount === 1 ? '' : 's'}` : ''}`}
                        className="relative inline-flex h-10 w-10 items-center justify-center rounded-full border border-line bg-white text-ink transition-colors hover:bg-brand-50 hover:text-[#C86A4B] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C86A4B]/40"
                    >
                        <ShoppingBagIcon />

                        {/* The badge is mounted only when the cart actually has items. */}
                        {cartCount > 0 && (
                            <span
                                key={cartCount}
                                className="absolute -right-1.5 -top-1.5 flex h-5 min-w-5 items-center justify-center rounded-full bg-[#C86A4B] px-1 text-[10px] font-bold leading-none text-white ring-2 ring-cream"
                            >
                                {cartCount > 99 ? '99+' : cartCount}
                            </span>
                        )}
                    </button>

                    <a
                        href={href('/catalog')}
                        className="hidden items-center gap-2 rounded-full bg-[#C86A4B] px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-[#B85C3F] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C86A4B]/40 focus-visible:ring-offset-2 sm:inline-flex"
                    >
                        Shop Now
                        <ArrowRightIcon />
                    </a>

                    <button
                        type="button"
                        onClick={() => setMobileOpen((open) => !open)}
                        aria-expanded={mobileOpen}
                        aria-controls="mobile-menu"
                        aria-label={mobileOpen ? 'Close menu' : 'Open menu'}
                        className="inline-flex h-10 w-10 items-center justify-center rounded-full border border-line bg-white text-ink transition-colors hover:bg-brand-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C86A4B]/40 md:hidden"
                    >
                        {mobileOpen ? <CloseIcon /> : <MenuIcon />}
                    </button>
                </div>
            </nav>

            {/* Mobile navigation */}
            {mobileOpen && (
                <div id="mobile-menu" className="border-t border-line bg-cream px-4 pb-4 md:hidden">
                    <ul className="space-y-1 pt-3 text-sm font-medium">
                        {links.map((link) => (
                            <li key={link.label}>
                                <a
                                    href={link.href}
                                    onClick={() => setMobileOpen(false)}
                                    className={`block rounded-xl px-3 py-2.5 transition-colors hover:bg-brand-50 hover:text-[#C86A4B] ${
                                        link.active ? 'bg-brand-50 text-[#C86A4B]' : 'text-ink'
                                    }`}
                                >
                                    {link.label}
                                </a>
                            </li>
                        ))}
                    </ul>

                    <a
                        href={href('/catalog')}
                        onClick={() => setMobileOpen(false)}
                        className="mt-3 flex w-full items-center justify-center gap-2 rounded-full bg-[#C86A4B] px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-[#B85C3F]"
                    >
                        Shop Now
                        <ArrowRightIcon />
                    </a>
                </div>
            )}
        </header>
    );
}
