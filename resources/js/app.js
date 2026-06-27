import './bootstrap';

import Alpine from 'alpinejs';
import { studentNavState } from './modules/student-theme';

window.Alpine = Alpine;

// Global theme store (akses di semua komponen Alpine)
document.addEventListener('alpine:init', () => {
	Alpine.store('theme', {
		// Ambil data dari localStorage, default ke dark jika kosong
		isDark: localStorage.getItem('theme') === 'dark' || !localStorage.getItem('theme'),

		toggle() {
			this.isDark = !this.isDark;
			localStorage.setItem('theme', this.isDark ? 'dark' : 'light');
			// Tambahkan/hapus class .dark di elemen HTML untuk Tailwind
			if (this.isDark) {
				document.documentElement.classList.add('dark');
			} else {
				document.documentElement.classList.remove('dark');
			}
		}
	});
});

Alpine.data('studentNav', studentNavState);

Alpine.start();
