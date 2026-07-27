import './bootstrap';

import Alpine from 'alpinejs';
import { studentNavState } from './modules/student-theme';
import { examSession } from './modules/exam-session';
import { practiceSession } from './modules/practice-session';
import crudModal from './modules/crud-modal';


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

	// NOTE: store 'bg' TIDAK didaftarkan di sini — store itu sudah
	// didaftarkan dengan benar (lengkap dengan toggle() + localStorage
	// persistence) di resources/views/layouts/app.blade.php.
	// Mendaftarkannya lagi di sini akan menimpa/merusak store yang sudah ada.

	Alpine.data('examSession', (config) => examSession(config));
	Alpine.data('practiceSession', (config) => practiceSession(config));

	// Modal Edit/Lihat berbasis AJAX, dipakai di halaman admin
	// (admin/questions/index, admin/practice-questions/index, dst).
	// Lihat resources/js/modules/crud-modal.js untuk detail & cara pakai.
	Alpine.data('crudModal', (config) => crudModal(config));
});

Alpine.data('studentNav', studentNavState);

Alpine.start();