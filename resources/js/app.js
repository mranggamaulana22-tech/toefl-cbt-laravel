import './bootstrap';

import Alpine from 'alpinejs';
import { studentNavState } from './modules/student-theme';
import { examSession } from './modules/exam-session';
import { practiceSession } from './modules/practice-session';
import crudModal from './modules/crud-modal';
import voicePicker from './modules/voice-picker';


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

});

Alpine.data('examSession', (config) => examSession(config));
Alpine.data('practiceSession', (config) => practiceSession(config));
Alpine.data('crudModal', (config) => crudModal(config));
Alpine.data('voicePicker', (config) => voicePicker(config));
Alpine.data('studentNav', studentNavState);

window.voicePickerModal = voicePicker;
window.crudModalFactory = crudModal;
window.showAdminNotification = (options = {}) => {
	const isDark = document.documentElement.classList.contains('dark');

	if (window.Swal) {
		return window.Swal.fire({
			icon: options.icon || 'info',
			title: options.title || 'Informasi',
			text: options.text || '',
			confirmButtonText: 'Mengerti',
			confirmButtonColor: options.confirmButtonColor || '#4f46e5',
			background: isDark ? '#111827' : '#ffffff',
			color: isDark ? '#f8fafc' : '#0f172a',
			customClass: {
				popup: 'rounded-2xl border border-slate-200 shadow-2xl dark:border-white/10',
				confirmButton: 'rounded-xl px-5 py-2.5 font-bold'
			}
		});
	}

	return window.alert(options.text || options.title || 'Informasi');
};

window.confirmAdminDelete = (form) => {
	const isDark = document.documentElement.classList.contains('dark');

	if (!window.Swal) {
		console.error('SweetAlert2 belum tersedia. Penghapusan dibatalkan.');
		return false;
	}

	window.Swal.fire({
		icon: 'warning',
		title: 'Hapus Soal?',
		text: `Soal ini akan dihapus permanen. Histori nilai tetap tersimpan.`,
		showCancelButton: true,
		confirmButtonText: 'Hapus Permanen',
		cancelButtonText: 'Batal',
		confirmButtonColor: '#dc2626',
		cancelButtonColor: '#475569',
		background: isDark ? '#111827' : '#ffffff',
		color: isDark ? '#f8fafc' : '#0f172a',
		customClass: {
			popup: 'rounded-2xl border border-slate-200 shadow-2xl dark:border-white/10',
			confirmButton: 'rounded-xl px-5 py-2.5 font-bold',
			cancelButton: 'rounded-xl px-5 py-2.5 font-bold'
		}
	}).then((result) => {
		if (result.isConfirmed) {
			form.submit();
		}
	});

	return false;
};

Alpine.start();