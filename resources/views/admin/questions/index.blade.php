<x-app-layout>
    <div class="py-8 min-h-screen bg-slate-50 dark:bg-[#0b0d13]"
         x-data="crudModal({
            baseUrl: '/questions',
            editTitle: 'Edit Soal Ujian',
            viewTitle: 'Detail Soal Ujian',
         })">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 shadow-sm">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                </div>
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
                    <form method="GET" action="{{ route('questions.index') }}" class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
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
                        <a href="{{ route('questions.index') }}" class="px-5 py-2.5 text-sm font-semibold rounded-lg transition text-center active:scale-95 border bg-slate-100 text-slate-700 hover:bg-slate-200 border-transparent dark:bg-white/5 dark:text-slate-300 dark:hover:bg-white/10 dark:border-white/10">
                            Reset
                        </a>
                    </form>

                    <div class="flex flex-col sm:flex-row gap-2 lg:justify-end">
                        <a href="{{ route('questions.export.csv', ['category' => $category]) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg transition active:scale-95 shadow-sm">
                            <i class="fas fa-file-csv"></i> Export CSV
                        </a>
                        @if($canAddQuestion)
                            <a href="{{ route('questions.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition active:scale-95 shadow-sm">
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
                        Bank soal ujian sudah mencapai 140 soal. Tambah soal baru dinonaktifkan sementara.
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
                                @include('admin.questions.partials.row', ['q' => $q, 'no' => $questions->firstItem() + $loop->index])
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
    </div>
</x-app-layout>