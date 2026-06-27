export function getStoredTheme(storageKey, fallback = 'light') {
    try {
        return localStorage.getItem(storageKey) || fallback;
    } catch (error) {
        return fallback;
    }
}

export function setStoredTheme(storageKey, mode) {
    const isDark = mode === 'dark';
    document.documentElement.classList.toggle('dark', isDark);

    try {
        localStorage.setItem(storageKey, isDark ? 'dark' : 'light');
    } catch (error) {
        // Ignore storage failures (private mode or blocked storage).
    }

    return isDark;
}
