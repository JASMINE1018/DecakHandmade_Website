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

Alpine.start();
