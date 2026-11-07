/**
 * Static demo content for the Tote Senja product detail page.
 * Swap this module for an API response when wiring the page to the backend.
 */
export const product = {
    sku: 'HANDMADE TOTE - DCK-T01',
    name: 'Tote Senja',
    price: 189000,
    stock: 12,
    description:
        'Handcrafted from sturdy 12 oz cotton canvas, Tote Senja is made for everyday use. Every stitch is carefully finished to create a strong, lightweight bag with the natural character of Decak Handmade.',
    material: '12 oz cotton canvas, cotton lining',
    dimensions: '35 × 38 × 8 cm · 60 cm strap',
    colors: [
        {
            name: 'Natural',
            swatch: '#E4D3B8',
            border: '#C6B08C',
        },
        {
            name: 'Chocolate',
            swatch: '#5A2A18',
            border: '#3F1C0F',
        },
    ],
    gallery: {
        Natural: [
            {
                src: 'images/products/tote-senja-natural-front.svg',
                alt: 'Natural Tote Senja, front view',
                label: 'Front view',
            },
            {
                src: 'images/products/tote-senja-natural-detail.svg',
                alt: 'Stitch and label detail of the natural Tote Senja',
                label: 'Stitch detail',
            },
            {
                src: 'images/products/tote-senja-natural-strap.svg',
                alt: 'Strap and canvas texture detail of the natural Tote Senja',
                label: 'Strap & texture',
            },
        ],
        Chocolate: [
            {
                src: 'images/products/tote-senja-cokelat-front.svg',
                alt: 'Chocolate Tote Senja, front view',
                label: 'Front view',
            },
            {
                src: 'images/products/tote-senja-cokelat-detail.svg',
                alt: 'Stitch and label detail of the chocolate Tote Senja',
                label: 'Stitch detail',
            },
            {
                src: 'images/products/tote-senja-cokelat-strap.svg',
                alt: 'Strap and canvas texture detail of the chocolate Tote Senja',
                label: 'Strap & texture',
            },
        ],
    },
    whatsappNumber: '6281234567890', // Replace with the studio's real WhatsApp number.
};
