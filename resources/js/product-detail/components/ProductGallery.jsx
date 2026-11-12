import { resolveAsset } from '../utils';

export default function ProductGallery({
    images = [],
    activeIndex = 0,
    onSelect,
    productName,
    assetUrl = '',
}) {
    const safeIndex = Math.min(Math.max(activeIndex, 0), Math.max(images.length - 1, 0));
    const activeImage = images[safeIndex];

    if (!activeImage) {
        return null;
    }

    return (
        <section aria-label={`Photo gallery for ${productName}`}>
            <div className="relative overflow-hidden rounded-[2rem] border border-line bg-peach shadow-sm">
                <img
                    key={activeImage.src}
                    src={resolveAsset(assetUrl, activeImage.src)}
                    alt={activeImage.alt}
                    className="aspect-square w-full object-cover"
                    loading="eager"
                />

                <span className="absolute bottom-5 right-5 rounded-full bg-ink/70 px-3 py-1 text-[11px] font-medium text-white backdrop-blur">
                    {String(safeIndex + 1).padStart(2, '0')} / {String(images.length).padStart(2, '0')}
                </span>
            </div>

            {/* Thumbnail selector */}
            <div className="mt-4 grid grid-cols-3 gap-3" role="group" aria-label="Select product photo">
                {images.map((image, index) => {
                    const isActive = index === safeIndex;

                    return (
                        <button
                            key={image.src}
                            type="button"
                            onClick={() => onSelect(index)}
                            aria-label={`Show photo: ${image.label}`}
                            aria-pressed={isActive}
                            className={`group relative aspect-square overflow-hidden rounded-2xl border-2 bg-white transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C86A4B]/50 focus-visible:ring-offset-2 ${
                                isActive
                                    ? 'border-[#C86A4B] ring-2 ring-[#C86A4B]/20 ring-offset-2 ring-offset-cream'
                                    : 'border-line hover:border-brand-300'
                            }`}
                        >
                            <img
                                src={resolveAsset(assetUrl, image.src)}
                                alt=""
                                className="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                loading="lazy"
                            />
                        </button>
                    );
                })}
            </div>
        </section>
    );
}
