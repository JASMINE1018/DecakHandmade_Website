import { useState } from 'react';

import { CloseIcon, MenuIcon, SearchIcon, ShoppingBagIcon, UserIcon } from './icons';
import { FONTS } from '../theme';

const FOCUS_RING =
    'focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#B86B4B]';

function SearchForm({ catalogUrl, inputId = 'story-search' }) {
    return (
        <form action={catalogUrl} method="GET" role="search" className="w-full">
            <label htmlFor={inputId} className="sr-only">
                Cari produk
            </label>
            <div className="flex items-center rounded-full border border-[#D6CFC5] bg-transparent px-4 py-1.5 text-xs transition-colors focus-within:border-[#B86B4B]">
                <input
                    id={inputId}
                    type="search"
                    name="search"
                    placeholder="Cari produk..."
                    className="min-w-0 flex-1 appearance-none border-0 bg-transparent p-0 font-[inherit] text-xs text-[#5C554F] shadow-none placeholder:text-[#8C837A] focus:border-0 focus:outline-none focus:ring-0"
                />
                <button
                    type="submit"
                    aria-label="Cari"
                    className={`ml-2 inline-flex text-[#8C837A] transition-colors hover:text-[#B86B4B] ${FOCUS_RING}`}
                >
                    <SearchIcon className="h-[14px] w-[14px]" />
                </button>
            </div>
        </form>
    );
}

export default function StoryHeader({ homeUrl, catalogUrl, storyUrl }) {
    const [mobileOpen, setMobileOpen] = useState(false);

    const navigation = [
        { label: 'Beranda', href: homeUrl, active: false },
        { label: 'Katalog', href: catalogUrl, active: false },
        { label: 'Cerita Kami', href: storyUrl, active: true },
    ];

    const closeMobileMenu = () => setMobileOpen(false);

    return (
        <header className="story-header sticky top-0 z-40 border-b border-[#E5E0D8] bg-[#F9F6F1]">
            <div className="grid h-20 grid-cols-[1fr_auto] items-center gap-4 px-6 md:grid-cols-[1fr_auto_1fr] md:px-10 lg:px-16">
                {/* Logo */}
                <a
                    href={homeUrl}
                    aria-label="Decak Handmade — Beranda"
                    className={`flex w-fit flex-col items-start leading-none ${FOCUS_RING}`}
                >
                    <span
                        className="text-[1.75rem] leading-[0.85] tracking-tight text-[#B86B4B]"
                        style={{ fontFamily: FONTS.display }}
                    >
                        decak
                    </span>
                    <span className="mt-1.5 text-[9px] font-medium uppercase tracking-[0.25em] text-[#B86B4B]">
                        HANDMADE
                    </span>
                </a>

                {/* Desktop navigation */}
                <nav aria-label="Navigasi utama" className="hidden md:block md:justify-self-center">
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
                <div className="flex items-center justify-self-end gap-4 sm:gap-5">
                    <div className="hidden w-28 md:block lg:w-32 xl:w-40">
                        <SearchForm catalogUrl={catalogUrl} />
                    </div>

                    <a
                        href="#akun"
                        aria-label="Akun saya"
                        className={`inline-flex text-[#2B2520] transition-colors hover:text-[#B86B4B] ${FOCUS_RING}`}
                    >
                        <UserIcon className="h-[18px] w-[18px]" strokeWidth={1.5} />
                    </a>

                    <a
                        href="#keranjang"
                        aria-label="Keranjang belanja"
                        className={`inline-flex text-[#2B2520] transition-colors hover:text-[#B86B4B] ${FOCUS_RING}`}
                    >
                        <ShoppingBagIcon className="h-[18px] w-[18px]" strokeWidth={1.5} />
                    </a>

                    <button
                        type="button"
                        onClick={() => setMobileOpen((open) => !open)}
                        aria-expanded={mobileOpen}
                        aria-controls="story-mobile-menu"
                        aria-label={mobileOpen ? 'Tutup menu' : 'Buka menu'}
                        className={`inline-flex text-[#2B2520] transition-colors hover:text-[#B86B4B] md:hidden ${FOCUS_RING}`}
                    >
                        {mobileOpen ? <CloseIcon /> : <MenuIcon />}
                    </button>
                </div>
            </div>

            {/* Mobile navigation */}
            {mobileOpen && (
                <div id="story-mobile-menu" className="border-t border-[#E5E0D8] bg-[#F9F6F1] px-6 py-6 md:hidden">
                    <nav aria-label="Navigasi seluler">
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
                        </ul>
                    </nav>

                    <div className="mt-5">
                        <SearchForm catalogUrl={catalogUrl} inputId="story-search-mobile" />
                    </div>
                </div>
            )}
        </header>
    );
}
