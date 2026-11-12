/**
 * Small helpers used across the product detail page.
 */

export function formatRupiah(value) {
    return `Rp${new Intl.NumberFormat('id-ID').format(Number(value) || 0)}`;
}

/** Join a Laravel `asset('')` base URL with a public path. */
export function resolveAsset(baseUrl, path) {
    const base = (baseUrl || '/').replace(/\/+$/, '');
    const cleanPath = String(path || '').replace(/^\/+/, '');

    return `${base}/${cleanPath}`;
}

/** Join a Laravel `url('/')` base URL with an application path. */
export function resolveAppUrl(baseUrl, path = '') {
    const base = (baseUrl || '').replace(/\/+$/, '');
    const cleanPath = String(path || '').replace(/^\/+/, '');

    return cleanPath ? `${base}/${cleanPath}` : `${base}/`;
}
