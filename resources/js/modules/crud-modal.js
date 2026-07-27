/**
 * crud-modal.js
 *
 * Alpine.data() reusable untuk modal Edit/Lihat berbasis AJAX.
 * Dipakai di halaman admin manapun yang punya pola: klik Edit/Lihat -> modal
 * fetch partial HTML -> submit form via AJAX -> ganti baris tabel tanpa reload.
 *
 * Cara pakai di Blade:
 *   x-data="crudModal({
 *       baseUrl: '/questions',
 *       editTitle: 'Edit Soal Ujian',
 *       viewTitle: 'Detail Soal Ujian',
 *   })"
 *
 * Lalu di tombol:
 *   @click="openEditModal(id)"                     -> fetch {baseUrl}/{id}/edit
 *   @click="openEditModal(id, { row_no: 3 })"       -> fetch {baseUrl}/{id}/edit?row_no=3
 *   @click="openViewModal(id)"                      -> fetch {baseUrl}/{id}
 *
 * Di form edit (partial), submit-nya:
 *   @submit.prevent="submitEditForm($event, id)"
 *
 * Server WAJIB balas JSON:
 *   - edit/show: { "html": "<...form/detail...>" }
 *   - update sukses: { "success": true, "row_html": "<tr>...</tr>" }
 */
export default function crudModal(options = {}) {
    const baseUrl = options.baseUrl || '';

    return {
        modalOpen: false,
        modalMode: null,
        modalTitle: '',
        modalHtml: '',
        isSubmitting: false,

        openEditModal(id, extraParams = {}) {
            this.modalMode = 'edit';
            this.modalTitle = options.editTitle || 'Edit Data';
            this.modalHtml = '<p class="text-sm text-slate-400 text-center py-10">Memuat form...</p>';
            this.modalOpen = true;

            const query = new URLSearchParams(extraParams).toString();
            const url = `${baseUrl}/${id}/edit${query ? '?' + query : ''}`;

            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            })
                .then((r) => r.json())
                .then((data) => {
                    this.modalHtml = data.html;
                })
                .catch(() => {
                    this.modalHtml = '<p class="text-sm text-red-500 text-center py-10">Gagal memuat form. Coba lagi.</p>';
                });
        },

        openViewModal(id) {
            this.modalMode = 'view';
            this.modalTitle = options.viewTitle || 'Detail Data';
            this.modalHtml = '<p class="text-sm text-slate-400 text-center py-10">Memuat detail...</p>';
            this.modalOpen = true;

            fetch(`${baseUrl}/${id}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            })
                .then((r) => r.json())
                .then((data) => {
                    this.modalHtml = data.html;
                })
                .catch(() => {
                    this.modalHtml = '<p class="text-sm text-red-500 text-center py-10">Gagal memuat detail. Coba lagi.</p>';
                });
        },

        closeModal() {
            this.modalOpen = false;
            this.modalHtml = '';
            this.modalMode = null;
        },

        submitEditForm(event, id) {
            this.isSubmitting = true;
            const form = event.target;
            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                },
                body: formData,
            })
                .then((r) => r.json())
                .then((data) => {
                    this.isSubmitting = false;
                    if (data.success) {
                        const oldRow = document.getElementById('row-' + id);
                        if (oldRow) {
                            oldRow.outerHTML = data.row_html;
                        }
                        this.closeModal();
                    } else {
                        alert('Gagal menyimpan perubahan.');
                    }
                })
                .catch(() => {
                    this.isSubmitting = false;
                    alert('Terjadi kesalahan saat menyimpan. Coba lagi.');
                });
        },
    };
}