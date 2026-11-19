import Alpine from 'alpinejs';

window.Alpine = Alpine;

window.orderForm = function () {
    return {
        products: window.__PRODUCTS || [],
        items: [],
        customerMode: 'existing',

        init() {
            const old = window.__OLD || {};
            this.customerMode = old.customer_mode === 'new' ? 'new' : 'existing';

            const oldItems = Object.values(old.items || {});

            this.items = oldItems.length
                ? oldItems.map((item) => ({
                    product_id: item.product_id || '',
                    variant_id: item.variant_id || '',
                    quantity: item.quantity || 1,
                    note: item.note || '',
                    values: Object.values(item.customizations || {}).reduce((values, customization) => {
                        if (customization.option_id) {
                            values[customization.option_id] = customization.value || '';
                        }
                        return values;
                    }, {}),
                }))
                : [this.blankItem()];
        },

        blankItem() {
            return { product_id: '', variant_id: '', quantity: 1, note: '', values: {} };
        },

        addItem() {
            this.items.push(this.blankItem());
        },

        removeItem(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
            }
        },

        productFor(item) {
            return this.products.find((product) => String(product.id) === String(item.product_id)) || null;
        },

        variantsFor(item) {
            const product = this.productFor(item);
            return product ? product.variants : [];
        },

        optionsFor(item) {
            const product = this.productFor(item);
            return product ? product.options : [];
        },

        onProductChange(item) {
            item.variant_id = '';
            item.values = {};
        },

        unitPrice(item) {
            const product = this.productFor(item);
            if (!product) {
                return 0;
            }

            let unit = Number(product.base_price);
            const variant = product.variants.find((v) => String(v.id) === String(item.variant_id));
            if (variant) {
                unit += Number(variant.price_adjustment);
            }

            product.options.forEach((option) => {
                if (item.values[option.id]) {
                    unit += Number(option.price);
                }
            });

            return unit;
        },

        lineTotal(item) {
            return this.unitPrice(item) * Math.max(1, Number(item.quantity) || 1);
        },

        formatRp(value) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(Number(value) || 0);
        },

        get subtotal() {
            return this.items.reduce((total, item) => total + this.lineTotal(item), 0);
        },
    };
};

window.heroCarousel = function (total) {
    return {
        active: 0,
        total: Math.max(1, Number(total) || 1),
        timer: null,

        init() {
            this.startAutoplay();
        },

        startAutoplay() {
            this.stopAutoplay();
            this.timer = setInterval(() => {
                this.next();
            }, 6000);
        },

        stopAutoplay() {
            if (this.timer) {
                clearInterval(this.timer);
                this.timer = null;
            }
        },

        next() {
            this.active = (this.active + 1) % this.total;
        },

        prev() {
            this.active = (this.active - 1 + this.total) % this.total;
        },

        go(index) {
            this.active = Math.max(0, Math.min(this.total - 1, Number(index) || 0));
        },
    };
};

window.productCarousel = function (total) {
    return {
        total: Math.max(1, Number(total) || 1),
        pages: 1,
        current: 0,

        init() {
            this.recalculate();
            window.addEventListener('resize', () => this.recalculate());
        },

        get pageList() {
            return Array.from({ length: this.pages }, (_, index) => index);
        },

        perPage() {
            const track = this.$refs.track;
            const card = track ? track.querySelector('[data-product-card]') : null;

            if (!track || !card) {
                return 1;
            }

            const gap = parseFloat(getComputedStyle(track).columnGap || '0') || 0;

            return Math.max(1, Math.round((track.clientWidth + gap) / (card.offsetWidth + gap)));
        },

        recalculate() {
            this.pages = Math.max(1, Math.ceil(this.total / this.perPage()));
            this.current = Math.min(this.current, this.pages - 1);
        },

        goTo(page) {
            const target = Math.max(0, Math.min(this.pages - 1, page));
            const track = this.$refs.track;
            const card = track ? track.querySelector('[data-product-card]') : null;

            this.current = target;

            if (!track || !card) {
                return;
            }

            const gap = parseFloat(getComputedStyle(track).columnGap || '0') || 0;
            const step = this.perPage() * (card.offsetWidth + gap);

            track.scrollTo({ left: target * step, behavior: 'smooth' });
        },

        onScroll() {
            const track = this.$refs.track;
            const card = track ? track.querySelector('[data-product-card]') : null;

            if (!track || !card) {
                return;
            }

            const gap = parseFloat(getComputedStyle(track).columnGap || '0') || 0;
            const step = this.perPage() * (card.offsetWidth + gap);

            this.current = step > 0
                ? Math.min(this.pages - 1, Math.round(track.scrollLeft / step))
                : 0;
        },
    };
};

// Safe Scroll Reveal Observer
function initScrollReveal() {
    const targets = document.querySelectorAll('.reveal-on-scroll, .img-reveal, .line-expand');
    if (!targets.length) {
        return;
    }

    if (!('IntersectionObserver' in window)) {
        targets.forEach((el) => el.classList.add('is-revealed'));
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-revealed');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.08,
        rootMargin: '0px 0px -20px 0px'
    });

    const windowH = window.innerHeight || document.documentElement.clientHeight;
    targets.forEach((el) => {
        const rect = el.getBoundingClientRect();
        // If already in viewport or above it (e.g. Hero on page load), reveal immediately
        if (rect.top <= windowH) {
            requestAnimationFrame(() => {
                el.classList.add('is-revealed');
            });
        } else {
            observer.observe(el);
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initScrollReveal);
} else {
    initScrollReveal();
}
window.addEventListener('load', initScrollReveal);

Alpine.start();

