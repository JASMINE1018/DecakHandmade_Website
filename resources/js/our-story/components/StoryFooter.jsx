import Reveal from './Reveal';
import { InstagramIcon } from './icons';
import { FONTS, WHATSAPP_URL } from '../theme';

const FOCUS_RING =
    'focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#B86B4B]';

export default function StoryFooter({ homeUrl, catalogUrl, storyUrl, testimonialUrl }) {
    const navigationLinks = [
        { label: 'Home', href: homeUrl },
        { label: 'Catalog', href: catalogUrl },
        { label: 'Our Story', href: storyUrl },
        { label: 'Testimonials', href: testimonialUrl },
    ];

    const helpLinks = [
        { label: 'FAQ', href: '#faq' },
        { label: 'How to Order', href: '#how-to-order' },
        { label: 'Returns', href: '#returns' },
    ];

    const socialLinks = [
        {
            label: 'Instagram Decak Handmade',
            href: 'https://instagram.com/decak.handmade',
            icon: InstagramIcon,
        },
    ];

    return (
        <footer className="bg-[#2A231E] px-8 pb-10 pt-16 text-[#D8D2CB] md:px-16">
            <Reveal className="mx-auto grid max-w-6xl grid-cols-1 gap-10 border-b border-[#3D342D] pb-14 md:grid-cols-12">
                {/* Brand */}
                <div className="md:col-span-5">
                    <h2
                        className="mb-3 text-xl text-white"
                        style={{ fontFamily: FONTS.display }}
                    >
                        Decak Handmade
                    </h2>
                    <p className="max-w-xs text-xs leading-relaxed text-[#A89F96]">
                        Handmade pieces with a personal touch for every special story.
                    </p>
                </div>

                {/* Navigation */}
                <nav aria-label="Footer navigation" className="md:col-span-2">
                    <h3 className="mb-4 text-xs font-semibold tracking-wider text-white">Navigation</h3>
                    <ul className="space-y-3 text-xs">
                        {navigationLinks.map((link) => (
                            <li key={link.label}>
                                <a
                                    href={link.href}
                                    className={`text-[#A89F96] transition-colors hover:text-white ${FOCUS_RING}`}
                                >
                                    {link.label}
                                </a>
                            </li>
                        ))}
                    </ul>
                </nav>

                {/* Help */}
                <nav aria-label="Help" className="md:col-span-2">
                    <h3 className="mb-4 text-xs font-semibold tracking-wider text-white">Help</h3>
                    <ul className="space-y-3 text-xs">
                        {helpLinks.map((link) => (
                            <li key={link.label}>
                                <a
                                    href={link.href}
                                    className={`text-[#A89F96] transition-colors hover:text-white ${FOCUS_RING}`}
                                >
                                    {link.label}
                                </a>
                            </li>
                        ))}
                    </ul>
                </nav>

                {/* Contact */}
                <div className="md:col-span-3">
                    <h3 className="mb-4 text-xs font-semibold tracking-wider text-white">Contact Us</h3>
                    <ul className="space-y-3 text-xs text-[#A89F96]">
                        <li>
                            <a
                                href="https://instagram.com/decak.handmade"
                                target="_blank"
                                rel="noreferrer"
                                className={`transition-colors hover:text-white ${FOCUS_RING}`}
                            >
                                @decak.handmade
                            </a>
                        </li>
                        <li>
                            <a
                                href={WHATSAPP_URL}
                                target="_blank"
                                rel="noreferrer"
                                className={`transition-colors hover:text-white ${FOCUS_RING}`}
                            >
                                WhatsApp customer service
                            </a>
                        </li>
                        <li>WhatsApp number coming soon</li>
                    </ul>
                </div>
            </Reveal>

            <Reveal className="mx-auto flex max-w-6xl flex-col items-center justify-between gap-4 pt-8 text-[11px] text-[#8C837A] sm:flex-row" delay={80}>
                <p>&copy; 2026 Decak Handmade. All rights reserved.</p>

                <div className="flex items-center gap-2.5">
                    {socialLinks.map(({ label, href, icon: Icon }) => (
                        <a
                            key={label}
                            href={href}
                            target="_blank"
                            rel="noreferrer"
                            aria-label={label}
                            className={`flex h-6 w-6 items-center justify-center rounded-full bg-white text-[#2A231E] transition-colors hover:bg-[#E5E0D8] ${FOCUS_RING}`}
                        >
                            <Icon className="h-3 w-3" />
                        </a>
                    ))}
                </div>
            </Reveal>
        </footer>
    );
}
