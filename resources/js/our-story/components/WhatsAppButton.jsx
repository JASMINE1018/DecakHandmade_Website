import { WhatsAppIcon } from './icons';
import { WHATSAPP_URL } from '../theme';

export default function WhatsAppButton() {
    return (
        <a
            href={WHATSAPP_URL}
            target="_blank"
            rel="noreferrer"
            aria-label="Hubungi Decak Handmade melalui WhatsApp"
            className="story-whatsapp group fixed bottom-6 right-6 z-50 inline-flex items-center rounded-full focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#25D366]"
        >
            <span className="relative inline-flex h-14 w-14 shrink-0 items-center justify-center transition-transform duration-200 group-hover:scale-105">
                {/* Glowing pulse rings */}
                <span
                    aria-hidden="true"
                    className="pointer-events-none absolute inset-0 animate-glow rounded-full bg-[#25D366]/45 motion-reduce:animate-none group-hover:[animation-play-state:paused]"
                />
                <span
                    aria-hidden="true"
                    className="pointer-events-none absolute inset-0 animate-glow rounded-full bg-[#25D366]/30 [animation-delay:1.2s] motion-reduce:animate-none group-hover:[animation-play-state:paused]"
                />

                {/* Main button */}
                <span className="relative inline-flex h-14 w-14 items-center justify-center rounded-full bg-[#25D366] text-white shadow-lg shadow-[#25D366]/40 transition-colors duration-200 animate-heartbeat motion-reduce:animate-none group-hover:[animation-play-state:paused] group-hover:bg-[#1fb95a]">
                    <WhatsAppIcon className="h-7 w-7" />
                </span>
            </span>
        </a>
    );
}
