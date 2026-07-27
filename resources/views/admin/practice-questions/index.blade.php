<x-app-layout>
    <div class="py-8 min-h-screen bg-slate-50 dark:bg-[#0b0d13]"
         x-data="crudModal({
            baseUrl: '/admin/practice-questions',
            editTitle: 'Edit Soal Latihan',
            viewTitle: 'Detail Soal Latihan',
         })">

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
            <div class="border rounded-2xl p-6 shadow-sm mb-6 bg-white border-slate-200 dark:bg-[#111827] dark:border-white/10">
                @php $canAddPracticeQuestion = ($stats['total_questions'] ?? 0) < 140; @endphp
                <div class="flex flex-col lg:flex-row lg:items-end gap-4">
                    <form method="GET" action="{{ route('admin.practice-questions.index') }}" class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-[0.18em] text-slate-500 mb-2">Kategori</label>
                            <select name="category" class="w-full px-4 py-2.5 rounded-lg text-sm font-medium bg-white text-slate-900 border-slate-200 dark:!bg-[#1e293b] dark:!text-white dark:border-white/10">
                                <option value="">Semua Kategori</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat }}" @selected($category === $cat)>{{ ucfirst($cat) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition active:scale-95 shadow-sm">
                            Cari
                        </button>
                        <a href="{{ route('admin.practice-questions.index') }}" class="px-5 py-2.5 text-sm font-semibold rounded-lg transition text-center active:scale-95 bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-white/5 dark:text-slate-300 dark:hover:bg-white/10">
                            Reset
                        </a>
                    </form>

                    <div class="flex flex-col sm:flex-row gap-2 lg:justify-end">
                        <a href="{{ route('admin.practice-questions.export.csv', ['category' => $category]) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg transition active:scale-95 shadow-sm">
                            Export CSV
                        </a>
                        @if($canAddPracticeQuestion)
                            <a href="{{ route('admin.practice-questions.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition active:scale-95 shadow-sm">
                                <i class="fas fa-plus"></i> Tambah Soal Latihan
                            </a>
                        @else
                            <span class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-300 text-slate-600 text-sm font-semibold rounded-lg shadow-sm cursor-not-allowed dark:bg-white/10 dark:text-slate-400">
                                Bank Soal Penuh
                            </span>
                        @endif
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
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b bg-slate-50 border-slate-200 dark:bg-white/5 dark:border-white/5">
                                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Kategori</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Soal</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Audio</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Jawaban</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-white/5">
                                @foreach ($practiceQuestions as $question)
                                    @include('admin.practice-questions.partials.row', ['question' => $question])
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile View --}}
                    <div class="md:hidden divide-y divide-slate-200 dark:divide-white/5">
                        @foreach ($practiceQuestions as $question)
                            <div class="px-4 py-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <div class="mb-2 flex items-center gap-2">
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
                                    <button type="button" @click="openEditModal({{ $question->id }})" class="flex-1 inline-flex items-center justify-center px-3 py-2 rounded-lg border-2 border-blue-400 bg-blue-600 text-white text-xs font-bold transition active:scale-95 shadow-md hover:bg-blue-700">EDIT</button>
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
    </div>
</x-app-layout>