/** Join a Laravel `asset('')` base URL with a public path. */
export function resolveAsset(baseUrl, path) {
    const base = (baseUrl || '/').replace(/\/+$/, '');
    const cleanPath = String(path || '').replace(/^\/+/, '');

    return `${base}/${cleanPath}`;
}
