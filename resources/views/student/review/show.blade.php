<x-app-layout>
    @include('student.review.partials.review-bg')

    <div class="review-page py-6 min-h-screen relative overflow-hidden" x-data="practiceReviewPage()" x-init="init()">
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            @include('student.review.partials.show-skeleton')

            <div x-cloak x-show="loaded" class="space-y-6">
                <div class="sm:hidden rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-indigo-600">Review AI Latihan</p>
                            <p class="mt-1 truncate text-sm font-semibold text-slate-900">{{ $practiceResult->submitted_at?->format('d M Y H:i') ?? $practiceResult->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('student.review.index') }}" class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-[11px] font-semibold text-slate-700 transition hover:border-indigo-200 hover:text-indigo-700">
                                Sesi
                            </a>
                            <a href="{{ route('student.results.practices') }}" class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-[11px] font-semibold text-slate-700 transition hover:border-emerald-200 hover:text-emerald-700">
                                Riwayat
                            </a>
                        </div>
                    </div>
                </div>

                <div class="hidden sm:flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.22em] text-indigo-600">Review AI Latihan</p>
                        <h2 class="mt-1 text-xl font-semibold leading-tight text-slate-900">{{ $practiceResult->submitted_at?->format('d M Y H:i') ?? $practiceResult->created_at->format('d M Y H:i') }}</h2>
                        <p class="mt-2 text-sm text-slate-500">Halaman ini hanya tersedia untuk latihan. Ujian resmi tidak punya review AI.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('student.review.index') }}" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-indigo-200 hover:text-indigo-700">
                            Pilih Sesi Lain
                        </a>
                        <a href="{{ route('student.results.practices') }}" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-emerald-200 hover:text-emerald-700">
                            Riwayat Latihan
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-4 gap-1.5 md:gap-4">
                    <div class="review-stat-total rounded-xl border border-slate-200 bg-white p-2 shadow-sm md:rounded-2xl md:p-5">
                        <p class="text-[9px] font-bold uppercase tracking-[0.08em] text-slate-400 md:text-xs md:tracking-[0.18em]">Total</p>
                        <p class="mt-1 text-lg font-black text-slate-900 md:mt-3 md:text-3xl">{{ $summary['total'] }}</p>
                    </div>
                    <div class="review-stat-ok rounded-xl border border-emerald-200 bg-emerald-50 p-2 shadow-sm md:rounded-2xl md:p-5">
                        <p class="text-[9px] font-bold uppercase tracking-[0.08em] text-emerald-500 md:text-xs md:tracking-[0.18em]">Benar</p>
                        <p class="mt-1 text-lg font-black text-emerald-700 md:mt-3 md:text-3xl">{{ $summary['correct'] }}</p>
                    </div>
                    <div class="review-stat-wrong rounded-xl border border-rose-200 bg-rose-50 p-2 shadow-sm md:rounded-2xl md:p-5">
                        <p class="text-[9px] font-bold uppercase tracking-[0.08em] text-rose-500 md:text-xs md:tracking-[0.18em]">Salah</p>
                        <p class="mt-1 text-lg font-black text-rose-700 md:mt-3 md:text-3xl">{{ $summary['wrong'] }}</p>
                    </div>
                    <div class="review-stat-quota rounded-xl border border-indigo-200 bg-indigo-50 p-2 shadow-sm md:rounded-2xl md:p-5">
                        <p class="text-[9px] font-bold uppercase tracking-[0.08em] text-indigo-500 md:text-xs md:tracking-[0.18em]">Sisa AI</p>
                        <p class="mt-1 text-lg font-black text-indigo-700 md:mt-3 md:text-3xl" x-text="quotaRemaining"></p>
                    </div>
                </div>

                <div class="flex flex-col gap-6 lg:flex-row">
                    <div class="flex-1 space-y-4">
                        <div x-ref="questionTop" class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
                            
                            <div class="border-b border-slate-200 bg-slate-50 p-4">
                                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                    <div class="flex items-center gap-3 flex-wrap">
                                        <span class="font-bold text-slate-700">Soal nomor: <span x-text="activeQuestion + 1" class="text-indigo-700 text-xl"></span></span>
                                        <span class="inline-flex items-center rounded-2xl bg-slate-900 px-3 py-1 font-mono text-sm text-white shadow-sm">
                                            <span x-text="navigatorLabel()"></span>
                                        </span>
                                        <span class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold uppercase tracking-wide text-indigo-700" x-text="questionCategories[activeQuestion] || '-'"></span>
                                        <span class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-bold uppercase tracking-wide text-slate-700">
                                            <span x-text="reviewCache[currentItemId()] ? 'Sudah direview' : 'Belum direview'"></span>
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <button type="button" @click="showNavigator = !showNavigator" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-700 transition hover:border-indigo-200 hover:text-indigo-700 sm:px-3 sm:py-2 sm:text-sm">
                                            <svg x-show="!showNavigator" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm1 3a1 1 0 100 2h12a1 1 0 100-2H4z" clip-rule="evenodd" />
                                            </svg>
                                            <svg x-show="showNavigator" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                            <span x-text="showNavigator ? 'Sembunyikan Navigasi' : 'Tampilkan Navigasi'"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="min-h-[420px] p-6 transition-all duration-200 ease-out md:p-8" :class="questionAnimating ? 'opacity-70 translate-y-1' : 'opacity-100 translate-y-0'">
                                @foreach($items as $item)
                                    <div x-show="selectedItemId === {{ $item->id }}" x-cloak x-transition.opacity.duration.150ms class="space-y-6">
                                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                            <div class="max-w-3xl">
                                                <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Soal {{ $item->question_order }}</p>
                                                <p class="mt-2 text-sm text-slate-500">Kategori: {{ ucfirst($item->category) }}</p>
                                            </div>
                                            <div class="review-answer-state rounded-2xl border px-4 py-3 text-sm font-semibold {{ $item->is_correct ? 'is-correct border-emerald-200 bg-emerald-50 text-emerald-700' : 'is-wrong border-rose-200 bg-rose-50 text-rose-700' }}">
                                                {{ $item->is_correct ? 'Jawaban benar' : 'Jawaban salah' }}
                                            </div>
                                        </div>

                                        @php
                                            $readingPassage = data_get($item->question_snapshot, 'passage') ?: $item->practiceQuestion?->passage;
                                            $audioPath = data_get($item->question_snapshot, 'audio_path') ?: $item->practiceQuestion?->audio_path;
                                            $audioTranscript = data_get($item->question_snapshot, 'audio_transcript') ?: $item->practiceQuestion?->audio_transcript;
                                            $questionText = data_get($item->question_snapshot, 'question_text') ?: $item->practiceQuestion?->question_text;
                                            $correctAnswer = data_get($item->question_snapshot, 'correct_answer') ?: $item->practiceQuestion?->correct_answer;
                                            $optionA = data_get($item->question_snapshot, 'option_a') ?: $item->practiceQuestion?->option_a;
                                            $optionB = data_get($item->question_snapshot, 'option_b') ?: $item->practiceQuestion?->option_b;
                                            $optionC = data_get($item->question_snapshot, 'option_c') ?: $item->practiceQuestion?->option_c;
                                            $optionD = data_get($item->question_snapshot, 'option_d') ?: $item->practiceQuestion?->option_d;
                                            $category = data_get($item->question_snapshot, 'category') ?: $item->practiceQuestion?->category;
                                        @endphp

                                        @if($category === 'reading' && $readingPassage)
                                            <div x-data="{ openPassage: true }" class="review-reading-block rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                                <button type="button" @click="openPassage = !openPassage" class="flex w-full items-center justify-between rounded-xl border border-slate-200 bg-white px-4 py-3 text-left text-sm font-semibold text-slate-700 transition hover:border-indigo-200 hover:text-indigo-700">
                                                    <span>Teks Cerita Reading</span>
                                                    <span x-text="openPassage ? 'Tutup' : 'Buka'"></span>
                                                </button>

                                                <div x-show="openPassage" x-transition.opacity.duration.150ms class="mt-4 rounded-xl border border-slate-200 bg-white p-5 text-sm italic leading-relaxed text-slate-600">
                                                    {!! nl2br(e($readingPassage)) !!}
                                                </div>
                                            </div>
                                        @endif

                                        @if($audioPath)
                                            <div class="review-audio-block rounded-2xl border border-sky-200 bg-sky-50 p-4">
                                                <p class="text-xs font-bold uppercase tracking-[0.18em] text-sky-700">Audio Listening</p>
                                                <audio controls class="mt-3 w-full">
                                                    <source src="{{ asset('storage/' . $audioPath) }}">
                                                    Browser kamu tidak mendukung audio player.
                                                </audio>

                                                <div class="mt-4 rounded-xl border border-sky-100 bg-white p-4">
                                                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Transcript</p>
                                                    <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700">{{ $audioTranscript ?: '-' }}</p>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="review-question-block rounded-2xl border border-slate-200 bg-white p-4">
                                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Teks Soal</p>
                                            <p class="mt-2 text-base font-medium leading-relaxed text-slate-900">{{ $questionText }}</p>
                                        </div>

                                        <div class="grid gap-3 md:grid-cols-2">
                                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                                <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Jawaban User</p>
                                                <p class="mt-2 text-base font-semibold text-slate-900">{{ $item->user_answer ?? '-' }}</p>
                                            </div>
                                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                                <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Kunci Jawaban</p>
                                                <p class="mt-2 text-base font-semibold text-emerald-700">{{ $correctAnswer }}</p>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 gap-3">
                                            @foreach(['A' => $optionA, 'B' => $optionB, 'C' => $optionC, 'D' => $optionD] as $opt => $optionText)
                                                @php
                                                    $isUser = ($item->user_answer ?? '') === $opt;
                                                    $isCorrect = $correctAnswer === $opt;
                                                    $cardClass = 'border-slate-200 hover:border-indigo-200 hover:bg-slate-50';

                                                    if ($isCorrect && $isUser) {
                                                        $cardClass = 'border-emerald-400 bg-emerald-50';
                                                    } elseif ($isCorrect) {
                                                        $cardClass = 'border-emerald-300 bg-emerald-50';
                                                    } elseif ($isUser) {
                                                        $cardClass = 'border-rose-300 bg-rose-50';
                                                    }
                                                @endphp
                                                <div class="flex items-center rounded-2xl border-2 p-4 transition-all {{ $cardClass }}">
                                                    <span class="mr-4 flex h-8 w-8 items-center justify-center rounded-full border-2 font-bold {{ $isCorrect ? 'bg-emerald-600 text-white border-emerald-600' : ($isUser ? 'bg-rose-600 text-white border-rose-600' : 'text-slate-400 border-slate-300') }}">
                                                        {{ $opt }}
                                                    </span>
                                                    <div class="flex-1">
                                                        <p class="text-sm font-medium text-slate-700">{{ $optionText }}</p>
                                                        <p class="mt-1 text-xs text-slate-500">
                                                            @if($isCorrect && $isUser)
                                                                Jawaban kamu (benar)
                                                            @elseif($isCorrect)
                                                                Kunci jawaban
                                                            @elseif($isUser)
                                                                Jawaban kamu
                                                            @else
                                                                Opsi {{ $opt }}
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="border-t border-slate-100 pt-5">
                                            <div class="grid grid-cols-3 items-center gap-3">
                                                <div class="justify-self-start">
                                                    <button type="button" @click="prevQuestion()" :disabled="activeQuestion === 0" class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-rose-600 text-base font-bold text-white shadow-sm transition hover:bg-rose-700 disabled:cursor-not-allowed disabled:opacity-40 sm:h-11 sm:w-11 sm:text-lg" title="Soal sebelumnya" aria-label="Soal sebelumnya">
                                                        <span>❮</span>
                                                    </button>
                                                </div>

                                                <div class="justify-self-center">
                                                    <button type="button"
                                                        class="review-ai-button inline-flex items-center gap-2 rounded-2xl px-3.5 py-2 text-xs font-semibold text-white transition disabled:cursor-not-allowed disabled:opacity-60 sm:px-5 sm:py-3 sm:text-sm"
                                                        :disabled="loadingId === {{ $item->id }} || (quotaRemaining <= 0 && !reviewCache[{{ $item->id }}])"
                                                        @click="loadReview({{ $item->id }})">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-white opacity-90">
                                                            <rect x="6" y="3" width="6" height="6" rx="2" transform="rotate(45 6 3)"/>
                                                            <rect x="12" y="15" width="6" height="6" rx="2" transform="rotate(45 12 15)"/>
                                                        </svg>
                                                        <span x-show="loadingId !== {{ $item->id }}">Review AI</span>
                                                        <span x-show="loadingId === {{ $item->id }}">Memuat...</span>
                                                    </button>
                                                </div>

                                                <div class="justify-self-end">
                                                    <button type="button" @click="nextQuestion()" :disabled="activeQuestion === totalQuestions - 1" class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-600 text-base font-bold text-white shadow-sm transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-40 sm:h-11 sm:w-11 sm:text-lg" title="Soal berikutnya" aria-label="Soal berikutnya">
                                                        <span>❯</span>
                                                    </button>
                                                </div>
                                            </div>
                                            <p class="mt-3 text-xs text-slate-500 text-center">Review AI hanya untuk sesi latihan. Review tersimpan 14 hari dan bisa dipakai ulang jika soal sama.</p>
                                        </div>

                                        <div class="review-ai-block rounded-3xl border border-slate-200 bg-slate-50 p-5" x-show="reviewCache[{{ $item->id }}]" x-cloak>
                                            <div class="flex items-center justify-between gap-3">
                                                <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Review AI</p>
                                                <span class="rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-500 shadow-sm" x-show="reviewCache[{{ $item->id }}]?.cached">Data sementara</span>
                                            </div>
                                            <div class="mt-4 space-y-4 text-sm leading-7 text-slate-700">
                                                <p><span class="font-bold text-slate-900">Catatan personal:</span> <span x-text="reviewCache[{{ $item->id }}]?.personal_note"></span></p>
                                                <p><span class="font-bold text-slate-900">Ringkasan:</span> <span x-text="reviewCache[{{ $item->id }}]?.summary"></span></p>
                                                <p><span class="font-bold text-slate-900">Kenapa bisa salah:</span> <span x-text="reviewCache[{{ $item->id }}]?.mistake_reason"></span></p>
                                                <p><span class="font-bold text-slate-900">Konsep inti:</span> <span x-text="reviewCache[{{ $item->id }}]?.correct_concept"></span></p>
                                                <p><span class="font-bold text-slate-900">Analisis opsi:</span> <span x-text="reviewCache[{{ $item->id }}]?.option_analysis"></span></p>
                                                <p><span class="font-bold text-slate-900">Contoh cepat:</span> <span x-text="reviewCache[{{ $item->id }}]?.example"></span></p>
                                                <p><span class="font-bold text-slate-900">Langkah berikutnya:</span> <span x-text="reviewCache[{{ $item->id }}]?.next_step"></span></p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="lg:w-1/4" x-show="showNavigator" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-2">
                        <div class="sticky top-24 rounded-3xl border border-slate-200 bg-white p-5 shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Navigator Soal</p>
                                    <p class="mt-1 text-sm text-slate-500">Hijau = benar, merah = salah</p>
                                </div>
                                <button type="button" @click="showNavigator = false" class="rounded-lg border border-slate-200 bg-white px-2 py-1 text-xs font-semibold text-slate-500 transition hover:border-indigo-200 hover:text-indigo-700">
                                    Tutup
                                </button>
                            </div>

                            <div class="mt-4 grid grid-cols-6 gap-2 sm:grid-cols-7 lg:grid-cols-5">
                                @foreach($items as $item)
                                    <button type="button"
                                        @click="selectItem({{ $item->id }})"
                                        class="review-nav-item inline-flex h-9 w-9 items-center justify-center rounded-lg border text-xs font-bold transition {{ $item->is_correct ? 'review-nav-item-correct bg-emerald-500/10 text-emerald-600 border-emerald-500/20' : 'review-nav-item-wrong bg-rose-500/10 text-rose-600 border-rose-500/20' }}"
                                        :class="selectedItemId === {{ $item->id }} ? 'ring-2 ring-indigo-500 ring-offset-2 !bg-indigo-600 !text-white !border-indigo-600' : ''">
                                        {{ $item->question_order }}
                                    </button>
                                @endforeach
                            </div>

                            <div class="mt-5 rounded-2xl bg-slate-50 p-4 text-sm text-slate-600">
                                <p class="font-bold text-slate-900">Cara pakai</p>
                                <p class="mt-2 leading-6">Klik nomor soal untuk berpindah. Setelah itu tekan tombol Review AI untuk melihat alasan jawaban kamu benar atau salah.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('student.review.partials.show-scripts')
    @include('student.review.partials.show-styles')
</x-app-layout>