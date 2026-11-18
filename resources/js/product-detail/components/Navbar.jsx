import { useState } from 'react';

import { CloseIcon, MenuIcon, SearchIcon, ShoppingBagIcon, UserIcon } from './icons';
import { resolveAppUrl } from '../utils';

const FOCUS_RING =
    'focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#B86B4B]';

function SearchForm({ catalogUrl, inputId = 'detail-nav-search' }) {
    return (
        <form action={catalogUrl} method="GET" role="search" className="w-full">
            <label htmlFor={inputId} className="sr-only">
                Search products
            </label>
            <div className="flex items-center rounded-full border border-[#D6CFC5] bg-transparent px-3.5 py-1.5 text-xs transition-colors focus-within:border-[#B86B4B]">
                <input
                    id={inputId}
                    type="search"
                    name="search"
                    placeholder="Search products..."
                    className="min-w-0 flex-1 appearance-none border-0 bg-transparent p-0 font-[inherit] text-xs text-[#5C554F] shadow-none placeholder:text-[#8C837A] focus:border-0 focus:outline-none focus:ring-0"
                />
                <button
                    type="submit"
                    aria-label="Search"
                    className={`ml-2 inline-flex text-[#8C837A] transition-colors hover:text-[#B86B4B] ${FOCUS_RING}`}
                >
                    <SearchIcon className="h-3.5 w-3.5" />
                </button>
            </div>
        </form>
    );
}

export default function Navbar({ cartCount = 0, appUrl = '', onOpenCart = null }) {
    const [mobileOpen, setMobileOpen] = useState(false);

    const href = (path) => resolveAppUrl(appUrl, path);

    const navigation = [
        { label: 'HOME', href: href('/home'), active: false },
        { label: 'CATALOG', href: href('/catalog'), active: true },
        { label: 'OUR STORY', href: href('/our-story'), active: false },
    ];

    const closeMobileMenu = () => setMobileOpen(false);

    return (
        <header className="sticky top-0 z-50 border-b border-[#E5E0D8] bg-[#F9F6F1]/95 backdrop-blur-md">
            <div className="mx-auto grid h-20 max-w-7xl grid-cols-[1fr_auto] items-center gap-4 px-4 sm:px-6 md:grid-cols-[1fr_auto_1fr] md:px-10 lg:px-16">
                {/* Logo */}
                <a
                    href={href('/home')}
                    aria-label="Decak Handmade — Home"
                    className={`flex w-fit flex-col items-start leading-none ${FOCUS_RING}`}
                >
                    <span
                        className="text-[1.75rem] leading-[0.85] tracking-tight text-[#B86B4B]"
                        style={{ fontFamily: "'Playfair Display', Georgia, serif" }}
                    >
                        decak
                    </span>
                    <span className="mt-1.5 text-[9px] font-medium uppercase tracking-[0.25em] text-[#B86B4B]">
                        HANDMADE
                    </span>
                </a>

                {/* Desktop navigation */}
                <nav aria-label="Main navigation" className="hidden md:block md:justify-self-center">
                    <ul className="flex items-center gap-7 lg:gap-9">
                        {navigation.map((item) => (
                            <li key={item.label}>
                                <a
                                    href={item.href}
                                    aria-current={item.active ? 'page' : undefined}
                                    className={`inline-block border-b pb-1 text-[11px] font-medium uppercase tracking-[0.18em] transition-colors ${FOCUS_RING} ${
                                        item.active
                                            ? 'border-[#B86B4B] text-[#B86B4B]'
                                            : 'border-transparent text-[#2B2520] hover:text-[#B86B4B]'
                                    }`}
                                >
                                    {item.label}
                                </a>
                            </li>
                        ))}
                    </ul>
                </nav>

                {/* Actions */}
                <div className="flex items-center justify-self-end gap-3 sm:gap-4 md:gap-5">
                    <div className="hidden w-28 md:block lg:w-36 xl:w-44">
                        <SearchForm catalogUrl={href('/catalog')} />
                    </div>

                    <a
                        href={href('/user')}
                        aria-label="My account"
                        className={`inline-flex p-1 text-[#2B2520] transition-colors hover:text-[#B86B4B] ${FOCUS_RING}`}
                    >
                        <UserIcon className="h-[18px] w-[18px]" strokeWidth={1.5} />
                    </a>

                    <button
                        type="button"
                        onClick={() => (onOpenCart ? onOpenCart() : (window.location.href = href('/user/cart')))}
                        aria-label={`Shopping cart${cartCount > 0 ? `, ${cartCount} item${cartCount === 1 ? '' : 's'}` : ''}`}
                        className={`relative inline-flex p-1 text-[#2B2520] transition-colors hover:text-[#B86B4B] cursor-pointer ${FOCUS_RING}`}
                    >
                        <ShoppingBagIcon className="h-[18px] w-[18px]" />
                        {cartCount > 0 && (
                            <span
                                key={cartCount}
                                className="absolute -right-1.5 -top-1.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-[#B86B4B] px-1 text-[9px] font-bold leading-none text-white ring-2 ring-[#F9F6F1]"
                            >
                                {cartCount > 99 ? '99+' : cartCount}
                            </span>
                        )}
                    </button>

                    <button
                        type="button"
                        onClick={() => setMobileOpen((open) => !open)}
                        aria-expanded={mobileOpen}
                        aria-controls="detail-mobile-menu"
                        aria-label={mobileOpen ? 'Close menu' : 'Open menu'}
                        className={`inline-flex p-1 text-[#2B2520] transition-colors hover:text-[#B86B4B] md:hidden ${FOCUS_RING}`}
                    >
                        {mobileOpen ? <CloseIcon /> : <MenuIcon />}
                    </button>
                </div>
            </div>

            {/* Mobile navigation */}
            {mobileOpen && (
                <div id="detail-mobile-menu" className="border-t border-[#E5E0D8] bg-[#F9F6F1] px-6 py-6 md:hidden">
                    <nav aria-label="Mobile navigation">
                        <ul className="flex flex-col">
                            {navigation.map((item) => (
                                <li key={item.label}>
                                    <a
                                        href={item.href}
                                        onClick={closeMobileMenu}
                                        aria-current={item.active ? 'page' : undefined}
                                        className={`flex items-center justify-between border-b border-[#E5E0D8] py-3.5 text-[11px] font-medium uppercase tracking-[0.18em] transition-colors ${FOCUS_RING} ${
                                            item.active ? 'text-[#B86B4B]' : 'text-[#2B2520]'
                                        }`}
                                    >
                                        <span>{item.label}</span>
                                        {item.active && <span aria-hidden="true" className="h-px w-6 bg-[#B86B4B]" />}
                                    </a>
                                </li>
                            ))}
                            <li>
                                <a
                                    href={href('/user')}
                                    onClick={closeMobileMenu}
                                    className={`flex items-center justify-between border-b border-[#E5E0D8] py-3.5 text-[11px] font-medium uppercase tracking-[0.18em] transition-colors text-[#2B2520] ${FOCUS_RING}`}
                                >
                                    <span>MY ACCOUNT</span>
                                </a>
                            </li>
                        </ul>
                    </nav>

                    <div className="mt-5">
                        <SearchForm catalogUrl={href('/catalog')} inputId="detail-search-mobile" />
                    </div>
                </div>
            )}
        </header>
    );
}
