const footerLinks = [
    { label: 'Help', href: '#help' },
    { label: 'Shipping & returns', href: '#shipping-returns' },
    { label: '@decak.handmade', href: 'https://instagram.com/decak.handmade', external: true },
];

export default function Footer() {
    return (
        <footer className="mt-16 border-t border-white/10 bg-[#5A2A18] text-white/70">
            <div className="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-8 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
                <p className="text-center text-xs sm:text-left">
                    &copy; 2026 decak handmade. Made with love
                </p>

                <nav aria-label="Footer links">
                    <ul className="flex flex-wrap items-center justify-center gap-x-5 gap-y-2 text-xs">
                        {footerLinks.map((link) => (
                            <li key={link.label}>
                                <a
                                    href={link.href}
                                    {...(link.external ? { target: '_blank', rel: 'noreferrer' } : {})}
                                    className="transition-colors hover:text-white"
                                >
                                    {link.label}
                                </a>
                            </li>
                        ))}
                    </ul>
                </nav>
            </div>
        </footer>
    );
}
