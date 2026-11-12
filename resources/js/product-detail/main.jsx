import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';

import '../../css/app.css';
import ProductDetailPage from './ProductDetailPage';
import { product as fallbackProduct } from './data/product';

const config = window.__PRODUCT_DETAIL_CONFIG__ ?? {};
const product = window.__PRODUCT_DETAIL_PRODUCT__ ?? fallbackProduct;
const rootElement = document.getElementById('product-detail-root');

if (rootElement) {
    createRoot(rootElement).render(
        <StrictMode>
            <ProductDetailPage
                product={product}
                appUrl={config.appUrl ?? ''}
                assetUrl={config.assetUrl ?? '/'}
            />
        </StrictMode>,
    );
}
