import { useEffect, useRef, useState } from 'react';

/**
 * Reveal content once it enters the viewport.
 *
 * Keeps the motion in CSS so the component only manages visibility.
 * Elements are revealed immediately when IntersectionObserver is not
 * available or the visitor prefers reduced motion.
 */
export default function Reveal({
    as: Tag = 'div',
    variant = 'up',
    delay = 0,
    className = '',
    style,
    children,
    ...props
}) {
    const ref = useRef(null);
    const [isVisible, setIsVisible] = useState(false);

    useEffect(() => {
        const node = ref.current;

        if (!node) {
            return;
        }

        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (prefersReducedMotion || !('IntersectionObserver' in window)) {
            setIsVisible(true);

            return;
        }

        const observer = new IntersectionObserver(
            ([entry]) => {
                if (entry.isIntersecting) {
                    setIsVisible(true);
                    observer.disconnect();
                }
            },
            {
                threshold: 0.05,
                rootMargin: '0px 0px -6% 0px',
            },
        );

        observer.observe(node);

        return () => observer.disconnect();
    }, []);

    const content = variant.startsWith('curtain') ? (
        <div className="story-reveal-mask">{children}</div>
    ) : (
        children
    );

    return (
        <Tag
            ref={ref}
            data-reveal={variant}
            className={[className, isVisible ? 'is-revealed' : ''].filter(Boolean).join(' ')}
            style={{ '--story-delay': `${delay}ms`, ...style }}
            {...props}
        >
            {content}
        </Tag>
    );
}
