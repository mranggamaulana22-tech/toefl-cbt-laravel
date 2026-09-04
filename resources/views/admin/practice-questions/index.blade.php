<x-app-layout>
    <div class="py-8 min-h-screen bg-slate-50 dark:bg-[#0b0d13]"
         x-data="{ ...window.crudModalFactory({ baseUrl: '/admin/practice-questions', editTitle: 'Edit Soal Latihan', viewTitle: 'Detail Soal Latihan' }), showImportModal: false }">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('import_warnings'))
                <div class="mb-5 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 shadow-sm dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-300 flex items-center justify-between gap-3">
                    <span class="font-semibold">⚠️ Import berhasil, tapi ada beberapa file audio yang tidak ditemukan.</span>
                    <button type="button" onclick="showImportWarningDetail()" class="flex-shrink-0 text-xs font-bold underline hover:no-underline">
                        Lihat Detail
                    </button>
                </div>

                @push('scripts')
                <script>
                    const importWarningList = @json(session('import_warnings'));

                    function showImportWarningDetail() {
                        const isDarkMode = document.documentElement.classList.contains('dark');
                        const listHtml = '<ul style="text-align:left; padding-left:1.2em; margin:0; max-height:320px; overflow-y:auto;">'
                            + importWarningList.map(w => `<li style="margin-bottom:6px;">${w}</li>`).join('')
                            + '</ul><p style="margin-top:12px; font-size:12px; color:#94a3b8;">Upload audio untuk soal-soal tersebut secara manual lewat tombol Edit.</p>';

                        Swal.fire({
                            icon: 'warning',
                            title: 'File Audio Tidak Ditemukan',
                            html: listHtml,
                            width: 620,
                            confirmButtonText: 'Mengerti',
                            confirmButtonColor: '#d97706',
                            background: isDarkMode ? '#111827' : '#ffffff',
                            color: isDarkMode ? '#f3f4f6' : '#1f2937',
                        });
                    }

                    document.addEventListener('DOMContentLoaded', showImportWarningDetail);
                </script>
                @endpush
            @endif

            @if (session('import_errors'))
                @php
                    $errorType = session('import_error_type', 'validation');
                    $errorLabels = [
                        'file_format' => ['icon' => '📁', 'title' => 'Format File Bermasalah'],
                        'file_empty' => ['icon' => '📋', 'title' => 'File Tidak Berisi Data'],
                        'validation' => ['icon' => '✏️', 'title' => 'Ada Kesalahan pada Isi Data'],
                        'system' => ['icon' => '⚙️', 'title' => 'Gangguan Sistem Sementara'],
                    ];
                    $errorLabel = $errorLabels[$errorType] ?? $errorLabels['validation'];
                @endphp
                {{-- Ringkasan singkat tetap tampil inline (tidak merusak layout),
                     detail lengkap ditampilkan lewat popup SweetAlert2 di bawah. --}}
                <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 shadow-sm dark:border-red-500/20 dark:bg-red-500/10 dark:text-red-300 flex items-center justify-between gap-3">
                    <span class="font-semibold">{{ $errorLabel['icon'] }} {{ $errorLabel['title'] }} &mdash; import dibatalkan, tidak ada data yang tersimpan.</span>
                    <button type="button" onclick="showImportErrorDetail()" class="flex-shrink-0 text-xs font-bold underline hover:no-underline">
                        Lihat Detail
                    </button>
                </div>

                @push('scripts')
                <script>
                    const importErrorList = @json(session('import_errors'));
                    const importErrorTitle = @json($errorLabel['icon'] . ' ' . $errorLabel['title']);

                    function showImportErrorDetail() {
                        const isDarkMode = document.documentElement.classList.contains('dark');
                        const listHtml = '<ul style="text-align:left; padding-left:1.2em; margin:0; max-height:320px; overflow-y:auto;">'
                            + importErrorList.map(e => `<li style="margin-bottom:6px;">${e}</li>`).join('')
                            + '</ul>';

                        Swal.fire({
                            icon: 'error',
                            title: importErrorTitle,
                            html: listHtml,
                            width: 620,
                            confirmButtonText: 'Mengerti',
                            confirmButtonColor: '#dc2626',
                            background: isDarkMode ? '#111827' : '#ffffff',
                            color: isDarkMode ? '#f3f4f6' : '#1f2937',
                        });
                    }

                    document.addEventListener('DOMContentLoaded', showImportErrorDetail);
                </script>
                @endpush
            @endif

            {{-- Stat Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                @foreach([
                    ['label' => 'Total Soal', 'val' => $stats['total_questions'], 'sub' => 'Semua latihan tersedia', 'color' => 'slate'],
                    ['label' => 'Listening', 'val' => $stats['listening_count'], 'sub' => 'Soal audio', 'color' => 'blue'],
                    ['label' => 'Structure', 'val' => $stats['structure_count'], 'sub' => 'Grammar & syntax', 'color' => 'purple'],
                    ['label' => 'Reading', 'val' => $stats['reading_count'], 'sub' => 'Reading comprehension', 'color' => 'emerald'],
                ] as $index => $stat)
                    <x-stat-card :label="$stat['label']" :value="$stat['val']" :sub="$stat['sub']" :color="$stat['color']" :delay="$index * 100" />
                @endforeach
            </div>

            {{-- Filter & Action Bar --}}
            <div class="border rounded-2xl p-5 shadow-sm mb-6 bg-white border-slate-200 dark:bg-[#111827] dark:border-white/10">
                @php $canAddPracticeQuestion = ($stats['total_questions'] ?? 0) < 140; @endphp
                <div class="flex flex-col gap-4">
                    <form method="GET" action="{{ route('admin.practice-questions.index') }}" class="flex flex-col sm:flex-row sm:items-end gap-3">
                        <div class="flex-1 min-w-0">
                            <label class="block text-[11px] font-bold uppercase tracking-[0.14em] text-slate-500 mb-1.5">Kategori</label>
                            <select name="category" class="w-full px-3.5 py-2 rounded-lg text-sm font-medium bg-white text-slate-900 border-slate-200 dark:!bg-[#1e293b] dark:!text-white dark:border-white/10">
                                <option value="">Semua Kategori</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat }}" @selected($category === $cat)>{{ ucfirst($cat) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex gap-2 shrink-0">
                            <button type="submit" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition active:scale-95 shadow-sm">
                                <i class="fas fa-search text-[11px]"></i> Cari
                            </button>
                            <a href="{{ route('admin.practice-questions.index') }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 text-xs font-semibold rounded-lg transition active:scale-95 bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-white/5 dark:text-slate-300 dark:hover:bg-white/10">
                                <i class="fas fa-rotate-left text-[11px]"></i> Reset
                            </a>
                        </div>
                    </form>

                    <div class="h-px bg-slate-100 dark:bg-white/5"></div>

                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('admin.practice-questions.import.template') }}" title="Download Template"
                            class="inline-flex h-9 items-center justify-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 active:scale-[0.97] dark:border-white/10 dark:bg-white/5 dark:text-slate-300 dark:hover:bg-white/10">
                            <i class="fas fa-download text-[11px]"></i> Template
                        </a>
                        <button type="button" @click="showImportModal = true" title="Import Excel"
                            class="inline-flex h-9 items-center justify-center gap-1.5 rounded-lg bg-amber-600 px-3 text-xs font-semibold text-white shadow-sm transition hover:bg-amber-700 active:scale-[0.97]">
                            <i class="fas fa-file-import text-[11px]"></i> Import
                        </button>
                        <a href="{{ route('admin.practice-questions.export.csv', ['category' => $category]) }}" title="Export Excel"
                            class="inline-flex h-9 items-center justify-center gap-1.5 rounded-lg bg-emerald-600 px-3 text-xs font-semibold text-white shadow-sm transition hover:bg-emerald-700 active:scale-[0.97]">
                            <i class="fas fa-file-export text-[11px]"></i> Export
                        </a>

                        @include('admin.partials.voice-picker-modal', [
                            'getUrl' => route('admin.practice-questions.voice-settings.get'),
                            'saveUrl' => route('admin.practice-questions.voice-settings.save'),
                            'initialWoman' => $initialWoman,
                            'initialMan' => $initialMan,
                        ])

                        <button type="button"
                            onclick="generateAllPracticeAudio(this)" title="Generate Semua Audio yang Kosong"
                            class="inline-flex h-9 items-center justify-center gap-1.5 rounded-lg bg-purple-600 px-3 text-xs font-semibold text-white shadow-sm transition hover:bg-purple-700 active:scale-[0.97]">
                            <i class="fas fa-bolt text-[11px]"></i>
                            <span class="generate-all-label">Generate Audio</span>
                        </button>

                        <span class="mx-1 hidden h-6 w-px bg-slate-200 dark:bg-white/10 sm:inline-block"></span>

                        @if($canAddPracticeQuestion)
                            <a href="{{ route('admin.practice-questions.create') }}"
                                class="inline-flex h-9 items-center justify-center gap-1.5 rounded-lg bg-indigo-600 px-3 text-xs font-semibold text-white shadow-sm transition hover:bg-indigo-700 active:scale-[0.97]">
                                <i class="fas fa-plus text-[11px]"></i> Tambah Soal
                            </a>
                        @else
                            <span class="inline-flex h-9 items-center justify-center gap-1.5 rounded-lg bg-slate-200 px-3 text-xs font-semibold text-slate-500 cursor-not-allowed dark:bg-white/10 dark:text-slate-400">
                                <i class="fas fa-ban text-[11px]"></i> Bank Penuh
                            </span>
                        @endif

                        <form method="POST" action="{{ route('admin.practice-questions.destroy-all') }}"
                            onsubmit="return openBulkPracticeDeleteModal(this);" class="ml-auto">
                            @csrf
                            @method('DELETE')
                            <button type="submit" @disabled(($stats['total_questions'] ?? 0) === 0) title="Hapus Semua Soal"
                                class="inline-flex h-9 items-center justify-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-3 text-xs font-semibold text-red-600 transition hover:bg-red-100 active:scale-[0.97] disabled:cursor-not-allowed disabled:opacity-40 dark:border-red-500/20 dark:bg-red-500/10 dark:text-red-300 dark:hover:bg-red-500/20">
                                <i class="fas fa-trash text-[11px]"></i> Hapus Semua
                            </button>
                        </form>
                    </div>
                </div>

                @unless($canAddPracticeQuestion)
                    <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-300">
                        Bank soal latihan sudah mencapai 140 soal. Tambah soal baru dinonaktifkan sementara.
                    </div>
                @endunless
            </div>

            {{-- Table Container --}}
            <div class="border rounded-2xl overflow-hidden shadow-sm relative bg-white border-slate-200 dark:bg-[#111827] dark:border-white/10">

                @if ($practiceQuestions->count() > 0)
                    <div class="hidden md:block overflow-x-auto">
                        <table class="admin-data-table w-full text-sm">
                            <thead>
                                <tr class="border-b bg-slate-50 border-slate-200 dark:bg-white/5 dark:border-white/5">
                                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-[0.12em] text-slate-500">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Kategori</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Soal</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Audio</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Jawaban</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-white/5">
                                @foreach ($practiceQuestions as $question)
                                    @include('admin.practice-questions.partials.row', ['question' => $question, 'no' => $practiceQuestions->firstItem() + $loop->index])
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile View --}}
                    <div class="md:hidden divide-y divide-slate-200 dark:divide-white/5">
                        @foreach ($practiceQuestions as $question)
                            @php $rowNo = $practiceQuestions->firstItem() + $loop->index; @endphp
                            <div class="px-4 py-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <div class="mb-2 flex items-center gap-2">
                                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-md text-xs font-bold bg-slate-100 text-slate-600 dark:bg-white/5 dark:text-slate-300">{{ $rowNo }}</span>
                                            <x-category-badge :category="$question->category" :solid="true" />
                                            <span class="text-xs text-slate-400 dark:text-slate-500">{{ $question->audio_path ? 'Audio tersedia' : 'Tanpa audio' }}</span>
                                        </div>
                                        <p class="text-sm font-medium text-slate-700 dark:text-slate-300 line-clamp-2">{{ $question->question_text }}</p>
                                    </div>
                                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-sm font-bold flex-shrink-0 bg-slate-100 text-slate-700 dark:bg-white/5 dark:text-slate-300">
                                        {{ $question->correct_answer }}
                                    </span>
                                </div>
                                <div class="mt-4 flex gap-2">
                                    <button type="button" @click="openViewModal({{ $question->id }})" class="flex-1 inline-flex items-center justify-center px-3 py-2 rounded-lg border-2 border-slate-300 bg-white text-slate-700 text-xs font-bold transition active:scale-95 hover:bg-slate-50 dark:bg-white/5 dark:text-slate-300 dark:border-white/10">LIHAT</button>
                                    <button type="button" @click="openEditModal({{ $question->id }}, { row_no: {{ $rowNo }} })" class="flex-1 inline-flex items-center justify-center px-3 py-2 rounded-lg border-2 border-blue-400 bg-blue-600 text-white text-xs font-bold transition active:scale-95 shadow-md hover:bg-blue-700">EDIT</button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t px-6 py-4 bg-slate-50 border-slate-200 dark:bg-white/[0.03] dark:border-white/5">
                        {{ $practiceQuestions->links() }}
                    </div>
                @else
                    <div class="px-6 py-12 text-center">
                        <p class="text-sm text-slate-500 italic">Belum ada soal latihan.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- MODAL: Edit / Lihat Soal --}}
        <div x-show="modalOpen"
             x-cloak
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm px-4 py-8"
             @keydown.escape.window="closeModal()">
            <div @click.away="closeModal()"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="w-full max-w-4xl max-h-[90vh] overflow-y-auto rounded-2xl border shadow-2xl bg-white border-slate-200 dark:bg-[#111827] dark:border-white/10">
                <div class="sticky top-0 z-10 flex items-center justify-between px-6 py-4 border-b bg-white border-slate-200 dark:bg-[#111827] dark:border-white/10">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white" x-text="modalTitle"></h3>
                    <button type="button" @click="closeModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition text-xl leading-none">&times;</button>
                </div>
                <div class="p-6" x-html="modalHtml"></div>
            </div>
        </div>

        {{-- MODAL: Import Excel --}}
        <div x-show="showImportModal"
             x-cloak
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm px-4"
             @keydown.escape.window="showImportModal = false">
            <div @click.away="showImportModal = false"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="w-full max-w-lg rounded-2xl border shadow-2xl bg-white border-slate-200 dark:bg-[#111827] dark:border-white/10">
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-white/10">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Import Soal Latihan</h3>
                    <button type="button" @click="showImportModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition text-xl leading-none">&times;</button>
                </div>

                <form action="{{ route('admin.practice-questions.import') }}" method="POST" enctype="multipart/form-data" class="p-6"
                      x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
                    @csrf

                    <div class="mb-4 rounded-lg bg-slate-50 dark:bg-white/5 px-4 py-3 text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        <p class="font-semibold mb-1">Cara pakai:</p>
                        <ol class="list-decimal list-inside space-y-0.5">
                            <li>Download template Excel, isi data soal sesuai format.</li>
                            <li>Untuk soal listening, isi kolom "Nama File Audio" (misal: <code class="bg-white dark:bg-black/20 px-1 rounded">listening_01.mp3</code>).</li>
                            <li>Kumpulkan file audio ke dalam folder bernama <code class="bg-white dark:bg-black/20 px-1 rounded">audio/</code>, lalu ZIP bersama file Excel-nya.</li>
                            <li>Upload file .xlsx (tanpa audio) atau .zip (dengan audio) di bawah ini.</li>
                        </ol>
                    </div>

                    <label for="import_file" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500 mb-2">
                        File Excel (.xlsx) atau ZIP
                    </label>
                    <input type="file" name="file" id="import_file" accept=".xlsx,.zip" required
                        class="w-full text-sm text-slate-600 dark:text-slate-300 file:mr-4 file:px-4 file:py-2 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-500/10 dark:file:text-indigo-300">
                    @error('file')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror

                    <p class="mt-2 text-[11px] text-slate-400">
                        Semua baris divalidasi terlebih dulu. Jika ada satu baris saja yang salah, seluruh import akan dibatalkan dan tidak ada data yang tersimpan.
                    </p>

                    <div class="mt-6 flex gap-2">
                        <button type="submit" :disabled="isSubmitting"
                            class="flex-1 inline-flex items-center justify-center rounded-lg bg-amber-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-amber-700 disabled:opacity-50">
                            <span x-show="!isSubmitting">Import Sekarang</span>
                            <span x-show="isSubmitting">Memproses...</span>
                        </button>
                        <button type="button" @click="showImportModal = false"
                            class="px-5 py-2.5 rounded-lg font-semibold text-sm transition bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-white/5 dark:text-slate-300 dark:hover:bg-white/10">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="bulk-delete-modal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-slate-950/70 px-4 backdrop-blur-sm"
        role="dialog" aria-modal="true" aria-labelledby="bulk-delete-title">
        <div class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-white/10 dark:bg-[#111827]">
            <div class="flex items-start gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-600 dark:bg-red-500/10 dark:text-red-400">
                    <i class="fas fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h2 id="bulk-delete-title" class="text-lg font-bold text-slate-900 dark:text-white">Hapus Semua Soal Practice</h2>
                    <p class="mt-1 text-sm leading-6 text-slate-600 dark:text-slate-300">
                        Tindakan ini menghapus bank soal dan file audio secara permanen. Histori nilai tetap tersimpan.
                    </p>
                </div>
            </div>
            <div class="mt-5 rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-500/20 dark:bg-red-500/10">
                <p class="text-xs font-bold uppercase tracking-[0.14em] text-red-700 dark:text-red-300">Kode verifikasi</p>
                <p id="bulk-delete-captcha" class="mt-2 select-all text-center font-mono text-2xl font-black tracking-[0.35em] text-red-700 dark:text-red-300"></p>
            </div>
            <div id="bulk-delete-captcha-step">
                <label for="bulk-delete-captcha-input" class="mt-5 block text-sm font-semibold text-slate-700 dark:text-slate-200">Ketik kode di atas</label>
                <input id="bulk-delete-captcha-input" type="text" autocomplete="off" spellcheck="false"
                class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-center font-mono text-lg font-bold uppercase tracking-[0.25em] text-slate-900 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20 dark:border-white/10 dark:bg-white/5 dark:text-white"
                onkeydown="if (event.key === 'Enter') { event.preventDefault(); verifyBulkPracticeDelete(); }">
                <p id="bulk-delete-error" class="mt-2 hidden text-center text-sm font-semibold text-red-600 dark:text-red-400">Kode tidak cocok.</p>
            </div>
            <div id="bulk-delete-confirm-step" class="mt-5 hidden rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-500/20 dark:bg-red-500/10">
                <p class="text-sm font-bold text-red-800 dark:text-red-200">Kode benar.</p>
                <p class="mt-1 text-sm leading-6 text-red-700 dark:text-red-300">Lanjutkan menghapus semua soal latihan secara permanen?</p>
            </div>
            <div class="mt-6 flex gap-3">
                <button type="button" onclick="closeBulkPracticeDeleteModal()"
                    class="flex-1 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:text-slate-200 dark:hover:bg-white/5">Batal</button>
                <button id="bulk-delete-action-button" type="button" onclick="verifyBulkPracticeDelete()"
                    class="flex-1 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-red-700">Verifikasi</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let bulkDeleteForm = null;
        let bulkDeleteCaptcha = '';

        function openBulkPracticeDeleteModal(form) {
            bulkDeleteForm = form;
            bulkDeleteCaptcha = Math.random().toString(36).slice(2, 8).toUpperCase();
            document.getElementById('bulk-delete-captcha').textContent = bulkDeleteCaptcha;
            document.getElementById('bulk-delete-captcha-input').value = '';
            document.getElementById('bulk-delete-error').classList.add('hidden');
            document.getElementById('bulk-delete-captcha-step').classList.remove('hidden');
            document.getElementById('bulk-delete-confirm-step').classList.add('hidden');
            document.getElementById('bulk-delete-action-button').textContent = 'Verifikasi';
            document.getElementById('bulk-delete-action-button').onclick = verifyBulkPracticeDelete;
            const modal = document.getElementById('bulk-delete-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            window.setTimeout(() => document.getElementById('bulk-delete-captcha-input').focus(), 0);
            return false;
        }

        function closeBulkPracticeDeleteModal() {
            const modal = document.getElementById('bulk-delete-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            bulkDeleteForm = null;
        }

        function verifyBulkPracticeDelete() {
            const input = document.getElementById('bulk-delete-captcha-input');
            const error = document.getElementById('bulk-delete-error');

            if (input.value.trim().toUpperCase() !== bulkDeleteCaptcha) {
                error.classList.remove('hidden');
                input.focus();
                return;
            }

            document.getElementById('bulk-delete-captcha-step').classList.add('hidden');
            document.getElementById('bulk-delete-confirm-step').classList.remove('hidden');
            document.getElementById('bulk-delete-action-button').textContent = 'Hapus Permanen';
            document.getElementById('bulk-delete-action-button').onclick = submitBulkPracticeDelete;
        }

        function submitBulkPracticeDelete() {
            const form = bulkDeleteForm;
            closeBulkPracticeDeleteModal();
            form.submit();
        }

        function generateQuestionAudio(questionId, rowNo, btnEl) {
            btnEl.disabled = true;
            btnEl.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';

            fetch(`/admin/practice-questions/${questionId}/generate-audio`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ row_no: rowNo }),
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        const oldRow = document.getElementById('row-' + questionId);
                        if (oldRow) oldRow.outerHTML = data.row_html;
                        window.showAdminNotification({
                            icon: 'success',
                            title: 'Audio Berhasil Dibuat',
                            text: 'Audio soal berhasil digenerate dari transcript.',
                            confirmButtonColor: '#059669',
                        });
                    } else {
                        window.showAdminNotification({ icon: 'error', title: 'Generate Gagal', text: data.message || 'Gagal generate audio.', confirmButtonColor: '#dc2626' });
                        btnEl.disabled = false;
                        btnEl.innerHTML = '<i class="fas fa-bolt"></i> Generate Audio';
                    }
                })
                .catch(() => {
                    window.showAdminNotification({ icon: 'error', title: 'Terjadi Kesalahan', text: 'Audio tidak berhasil dibuat. Coba lagi.', confirmButtonColor: '#dc2626' });
                    btnEl.disabled = false;
                    btnEl.innerHTML = '<i class="fas fa-bolt"></i> Generate Audio';
                });
        }

        function generateAllPracticeAudio(btnEl) {
            const label = btnEl.querySelector('.generate-all-label');
            btnEl.disabled = true;
            label.textContent = 'Memproses...';

            fetch('/admin/practice-questions/generate-audio-batch', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Content-Type': 'application/json',
                },
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        const failedCount = Object.keys(data.failed || {}).length;
                        window.showAdminNotification({
                            icon: failedCount > 0 ? 'warning' : 'success',
                            title: failedCount > 0 ? 'Generate Selesai dengan Catatan' : 'Generate Berhasil',
                            text: `Berhasil membuat ${data.processed} dari ${data.total} audio.${failedCount > 0 ? ` ${failedCount} soal gagal diproses.` : ''}`,
                            confirmButtonColor: failedCount > 0 ? '#d97706' : '#059669',
                        }).then(() => window.location.reload());
                    } else {
                        window.showAdminNotification({ icon: 'error', title: 'Generate Batch Gagal', text: 'Tidak dapat memproses audio batch.', confirmButtonColor: '#dc2626' });
                    }
                })
                .catch(() => window.showAdminNotification({ icon: 'error', title: 'Terjadi Kesalahan', text: 'Audio batch tidak berhasil diproses. Coba lagi.', confirmButtonColor: '#dc2626' }))
                .finally(() => {
                    btnEl.disabled = false;
                    label.textContent = 'Generate Semua yang Kosong';
                });
        }
    </script>
    @endpush
</x-app-layout>