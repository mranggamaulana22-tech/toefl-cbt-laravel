import { getStoredTheme, setStoredTheme } from './theme-storage';

export const STUDENT_THEME_STORAGE_KEY = 'theme';

export function getStoredStudentTheme() {
    return getStoredTheme(STUDENT_THEME_STORAGE_KEY, 'light');
}

export function setStudentTheme(mode) {
    return setStoredTheme(STUDENT_THEME_STORAGE_KEY, mode);
}

export function studentNavState() {
    return {
        open: false,
        darkMode: document.documentElement.classList.contains('dark'),
        toggleTheme() {
            this.darkMode = !this.darkMode;
            setStudentTheme(this.darkMode ? 'dark' : 'light');
            // Sinkronkan Alpine store jika sudah ada
            if (window.Alpine && Alpine.store && Alpine.store('theme')) {
                Alpine.store('theme').isDark = this.darkMode;
            }
        },
    };
}
