<x-app-layout>
    <div class="py-8 min-h-screen bg-slate-50 dark:bg-[#0b0d13]"
         x-data="{ ...crudModal({ baseUrl: '/admin/paket-soal/{{ $paketSoal->id }}/questions', editTitle: 'Edit Soal Ujian', viewTitle: 'Detail Soal Ujian' }), showImportModal: false }">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-5 flex items-center justify-between gap-3">
                <div>
                    <a href="{{ route('paket-soal.index') }}"
                       class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-1.5 text-xs font-semibold transition active:scale-95 bg-white border-slate-200 text-slate-600 hover:border-indigo-300 hover:text-indigo-600 dark:bg-white/5 dark:border-white/10 dark:text-slate-300 dark:hover:border-indigo-500/40 dark:hover:text-indigo-400">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                        Daftar Paket
                    </a>
                    <h1 class="text-lg font-bold text-slate-900 dark:text-white mt-2">
                        Soal Ujian &mdash; {{ $paketSoal->nama }}
                    </h1>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 shadow-sm">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('import_warnings'))
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
                        'capacity' => ['icon' => '📦', 'title' => 'Melebihi Kapasitas Paket'],
                        'system' => ['icon' => '⚙️', 'title' => 'Gangguan Sistem Sementara'],
                    ];
                    $errorLabel = $errorLabels[$errorType] ?? $errorLabels['validation'];
                @endphp
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
                    ['label' => 'Total Soal', 'val' => $stats['total_questions'], 'sub' => 'Semua kategori soal', 'color' => 'slate'],
                    ['label' => 'Listening', 'val' => $stats['listening_count'], 'sub' => 'Soal audio comprehension', 'color' => 'blue'],
                    ['label' => 'Structure', 'val' => $stats['structure_count'], 'sub' => 'Grammar & syntax', 'color' => 'purple'],
                    ['label' => 'Reading', 'val' => $stats['reading_count'], 'sub' => 'Reading comprehension', 'color' => 'emerald'],
                ] as $index => $stat)
                    <x-stat-card :label="$stat['label']" :value="$stat['val']" :sub="$stat['sub']" :color="$stat['color']" :delay="$index * 100" />
                @endforeach
            </div>

            {{-- Filter & Action Bar --}}
            <div class="border rounded-2xl p-6 shadow-sm mb-6 bg-white border-slate-200 dark:bg-[#111827] dark:border-white/10">
                @php $canAddQuestion = ($stats['total_questions'] ?? 0) < 140; @endphp
                <div class="flex flex-col lg:flex-row lg:items-end gap-4">
                    <form method="GET" action="{{ route('paket-soal.questions.index', $paketSoal) }}" class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                        <div>
                            <label for="category" class="block text-xs font-bold uppercase tracking-[0.18em] text-slate-500 mb-2">Filter Kategori</label>
                            <select name="category" id="category" class="w-full px-4 py-2.5 rounded-lg text-sm font-medium bg-white border-slate-200 text-slate-900 dark:bg-[#1e293b] dark:border-white/10 dark:text-white">
                                <option value="">Semua Kategori</option>
                                <option value="listening" @selected($category === 'listening')>🎧 Listening</option>
                                <option value="structure" @selected($category === 'structure')>📝 Structure</option>
                                <option value="reading" @selected($category === 'reading')>📖 Reading</option>
                            </select>
                        </div>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition active:scale-95 shadow-sm">
                            Terapkan Filter
                        </button>
                        <a href="{{ route('paket-soal.questions.index', $paketSoal) }}" class="px-5 py-2.5 text-sm font-semibold rounded-lg transition text-center active:scale-95 border bg-slate-100 text-slate-700 hover:bg-slate-200 border-transparent dark:bg-white/5 dark:text-slate-300 dark:hover:bg-white/10 dark:border-white/10">
                            Reset
                        </a>
                    </form>

                    <div class="flex flex-col sm:flex-row flex-wrap gap-2 lg:justify-end">
                        <a href="{{ route('paket-soal.questions.import.template', $paketSoal) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-lg transition active:scale-95 border bg-white text-slate-700 border-slate-200 hover:border-slate-300 dark:bg-white/5 dark:text-slate-300 dark:border-white/10 dark:hover:bg-white/10">
                            <i class="fas fa-download"></i> Download Template
                        </a>
                        <button type="button" @click="showImportModal = true" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold rounded-lg transition active:scale-95 shadow-sm">
                            <i class="fas fa-file-import"></i> Import Excel
                        </button>
                        <a href="{{ route('paket-soal.questions.export.csv', [$paketSoal, 'category' => $category]) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg transition active:scale-95 shadow-sm">
                            <i class="fas fa-file-csv"></i> Export Excel
                        </a>
                        @if($canAddQuestion)
                            <a href="{{ route('paket-soal.questions.create', $paketSoal) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition active:scale-95 shadow-sm">
                                <i class="fas fa-plus"></i> Tambah Soal
                            </a>
                        @else
                            <span class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-300 text-slate-600 text-sm font-semibold rounded-lg shadow-sm cursor-not-allowed dark:bg-white/10 dark:text-slate-400">
                                <i class="fas fa-ban"></i> Bank Soal Penuh
                            </span>
                        @endif
                    </div>
                </div>

                @unless($canAddQuestion)
                    <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-300">
                        Bank soal paket ini sudah mencapai 140 soal. Tambah soal baru dinonaktifkan sementara.
                    </div>
                @endunless
            </div>

            {{-- Questions Table --}}
            <div class="border rounded-2xl overflow-hidden shadow-sm relative bg-white border-slate-200 dark:bg-[#111827] dark:border-white/10">

                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b bg-gray-50 border-slate-200 dark:bg-white/5 dark:border-white/5">
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-[0.12em] text-slate-500">No</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Kategori</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Pertanyaan</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-[0.12em] text-slate-500 text-center">Jawaban</th>
                                <th class="px-6 py-3 text-center text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-white/5">
                            @forelse($questions as $q)
                                @include('admin.questions.partials.row', ['q' => $q, 'no' => $questions->firstItem() + $loop->index, 'paketSoal' => $paketSoal])
                            @empty
                                <tr>
                                    <td colspan="5" class="p-12 text-center text-slate-500 italic">Belum ada soal ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Card View --}}
                <div class="md:hidden divide-y divide-slate-200 dark:divide-white/5">
                    @foreach($questions as $mq)
                        @php $q = $mq; $rowNo = $questions->firstItem() + $loop->index; @endphp
                        <div class="px-4 py-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <x-category-badge :category="$q->category" :solid="true" />
                                    <p class="mt-2 text-sm font-medium text-slate-700 dark:text-slate-300 line-clamp-2">{{ Str::limit($q->question_text, 65) }}</p>
                                </div>
                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-sm font-bold flex-shrink-0 bg-slate-100 text-slate-700 dark:bg-indigo-500 dark:text-white">
                                    {{ $q->correct_answer }}
                                </span>
                            </div>
                            <div class="mt-4 flex gap-2">
                                <button type="button" @click="openViewModal({{ $q->id }})" class="flex-1 rounded-lg px-3 py-2 text-center text-xs font-semibold transition active:scale-95 bg-slate-100 text-slate-700 dark:bg-white/5 dark:text-slate-300">Lihat</button>
                                <button type="button" @click="openEditModal({{ $q->id }}, { row_no: {{ $rowNo }} })" class="flex-1 rounded-lg bg-blue-50 dark:bg-blue-500/10 px-3 py-2 text-center text-xs font-semibold text-blue-700 transition active:scale-95">Edit</button>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($questions->hasPages())
                    <div class="px-6 py-4 border-t bg-slate-50 border-slate-200 dark:bg-white/[0.03] dark:border-white/5">
                        {{ $questions->links() }}
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
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Import Soal Ujian &mdash; {{ $paketSoal->nama }}</h3>
                    <button type="button" @click="showImportModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition text-xl leading-none">&times;</button>
                </div>

                <form action="{{ route('paket-soal.questions.import', $paketSoal) }}" method="POST" enctype="multipart/form-data" class="p-6"
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
                        <p class="mt-2 text-amber-700 dark:text-amber-400 font-semibold">
                            ⚠️ Paket ini sudah punya {{ $stats['total_questions'] }}/140 soal (L:{{ $stats['listening_count'] }}/50, S:{{ $stats['structure_count'] }}/40, R:{{ $stats['reading_count'] }}/50). Import akan ditolak jika membuat kategori manapun melebihi batas.
                        </p>
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
</x-app-layout>