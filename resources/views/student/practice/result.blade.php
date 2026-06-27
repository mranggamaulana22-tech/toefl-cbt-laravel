<x-app-layout>
<div class="min-h-screen font-sans selection:bg-purple-500/30 overflow-hidden" 
     x-data="{ loaded: false }" 
     x-init="setTimeout(() => loaded = true, 400)">
    
    <main class="w-full overflow-x-hidden overflow-y-auto relative min-h-screen">
        {{-- Background Handler --}}
        <div class="fixed inset-0 z-0">
            <div x-show="$store.bg.enabled" class="ai-bg-img absolute inset-0 w-full h-full z-0"></div>
            <div x-show="$store.bg.enabled" class="ai-bg-gradient"></div>
            <div x-show="!$store.bg.enabled" :class="$store.theme?.isDark ? 'bg-[#0b0d13]' : 'bg-white'" class="absolute inset-0 w-full h-full transition-colors duration-500 z-0"></div>
        </div>
        
        {{-- Ambient Glow --}}
        <div class="hidden md:block fixed top-0 right-0 w-[600px] h-[600px] bg-indigo-600/10 blur-[130px] rounded-full pointer-events-none z-0"></div>
        <div class="hidden md:block fixed bottom-0 left-0 w-[400px] h-[400px] bg-emerald-600/10 blur-[100px] rounded-full pointer-events-none z-0"></div>

        {{-- UPDATE: Diubah ke max-w-4xl agar lebarnya sama persis dengan Exam Result --}}
        <div class="py-8 relative z-10 w-full">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                
                {{-- Skeleton Loading --}}
                @include('student.practice.partials.result-skeleton')

                {{-- Main Content --}}
                <div x-show="loaded" 
                     x-transition:enter="transition ease-out duration-700"
                     x-transition:enter-start="opacity-0 translate-y-12"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-cloak>
                    
                    {{-- MAIN CARD: Glassmorphism persis Exam Result --}}
                    <div class="overflow-hidden rounded-3xl border shadow-[0_18px_50px_rgba(0,0,0,0.3)] backdrop-blur-xl transition-colors duration-500 mb-8"
                         :class="$store.theme?.isDark ? 'bg-[#0f172a]/95 border-white/10' : 'bg-white/95 border-slate-200'">
                        
                        {{-- HEADER GRADIENT --}}
                        <div class="px-6 py-5 md:px-8 text-white relative overflow-hidden"
                             :class="$store.theme?.isDark ? 'bg-gradient-to-r from-emerald-600/90 via-teal-600/90 to-sky-600/90' : 'bg-gradient-to-r from-emerald-500 via-teal-500 to-sky-500'">
                            <div class="absolute right-0 top-0 opacity-10 translate-x-1/4 -translate-y-1/4">
                                <svg width="400" height="400" viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" stroke="currentColor" stroke-width="2" fill="none"/></svg>
                            </div>

                            <div class="relative z-10">
                                <p class="text-xs font-bold uppercase tracking-[0.22em] text-white/80 animate-fade-in">Submission Completed</p>
                                <h2 class="mt-2 text-3xl font-black tracking-tight">Latihan selesai dengan baik!</h2>
                                <p class="mt-2 text-sm text-white/80">Luar biasa! Kamu baru saja menyelesaikan sesi latihan TOEFL. Cek skor prediksimu di bawah.</p>
                            </div>
                        </div>

                        <div class="p-6 md:p-8">
                            <div class="grid gap-6 lg:grid-cols-[1fr_0.9fr] lg:items-center">
                                
                                {{-- INNER LEFT CARD: Skor Prediksi --}}
                                <div class="space-y-6">
                                    <div class="rounded-3xl border p-6 transition-colors duration-500"
                                         :class="$store.theme?.isDark ? 'bg-black/40 border-white/5' : 'bg-slate-50 border-slate-200'">
                                        <p class="text-xs font-bold uppercase tracking-[0.2em] mb-4" :class="$store.theme?.isDark ? 'text-slate-500' : 'text-slate-400'">Skor Prediksi</p>
                                        <div class="flex items-center gap-4">
                                            <div class="relative">
                                                {{-- Update ukuran radius lingkaran agar muat di kotak yg lebih kecil --}}
                                                <svg class="w-24 h-24 transform -rotate-90">
                                                    <circle cx="48" cy="48" r="42" stroke="currentColor" stroke-width="8" fill="transparent" :class="$store.theme?.isDark ? 'text-slate-700' : 'text-slate-200'" />
                                                    <circle cx="48" cy="48" r="42" stroke="currentColor" stroke-width="8" fill="transparent" 
                                                        stroke-dasharray="264" 
                                                        :stroke-dashoffset="264 - ({{ $result->score_total }} / 677) * 264" 
                                                        class="text-emerald-500 transition-all duration-1000 ease-out" />
                                                </svg>
                                                <div class="absolute inset-0 flex items-center justify-center">
                                                    <span class="text-2xl font-black" :class="$store.theme?.isDark ? 'text-white' : 'text-slate-900'">{{ $result->score_total }}</span>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="flex items-end gap-1.5">
                                                    <span class="text-5xl font-black tracking-tighter" :class="$store.theme?.isDark ? 'text-white' : 'text-slate-900'">{{ $result->score_total }}</span>
                                                    <span class="pb-1.5 text-sm font-bold text-slate-400">/ 677</span>
                                                </div>
                                                <p class="text-xs font-semibold mt-1" :class="$store.theme?.isDark ? 'text-slate-400' : 'text-slate-500'">Total Skor Latihan</p>
                                            </div>
                                        </div>

                                        <div class="mt-6 grid gap-3 sm:grid-cols-3">
                                            <div class="group rounded-2xl border p-4 transition-all hover:shadow-lg hover:-translate-y-1" :class="$store.theme?.isDark ? 'bg-white/5 border-white/10' : 'bg-white border-slate-200'">
                                                <p class="text-[10px] font-bold uppercase tracking-[0.18em]" :class="$store.theme?.isDark ? 'text-slate-500' : 'text-slate-400'">Listening</p>
                                                <p class="mt-2 text-2xl font-black" :class="$store.theme?.isDark ? 'text-white' : 'text-slate-900'">{{ $result->correct_listening }}</p>
                                            </div>
                                            <div class="group rounded-2xl border p-4 transition-all hover:shadow-lg hover:-translate-y-1" :class="$store.theme?.isDark ? 'bg-white/5 border-white/10' : 'bg-white border-slate-200'">
                                                <p class="text-[10px] font-bold uppercase tracking-[0.18em]" :class="$store.theme?.isDark ? 'text-slate-500' : 'text-slate-400'">Structure</p>
                                                <p class="mt-2 text-2xl font-black" :class="$store.theme?.isDark ? 'text-white' : 'text-slate-900'">{{ $result->correct_structure }}</p>
                                            </div>
                                            <div class="group rounded-2xl border p-4 transition-all hover:shadow-lg hover:-translate-y-1" :class="$store.theme?.isDark ? 'bg-white/5 border-white/10' : 'bg-white border-slate-200'">
                                                <p class="text-[10px] font-bold uppercase tracking-[0.18em]" :class="$store.theme?.isDark ? 'text-slate-500' : 'text-slate-400'">Reading</p>
                                                <p class="mt-2 text-2xl font-black" :class="$store.theme?.isDark ? 'text-white' : 'text-slate-900'">{{ $result->correct_reading }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- INNER RIGHT CARD: Langkah Berikutnya --}}
                                <div class="h-full">
                                    <div class="rounded-3xl border p-6 text-white sticky top-8 transition-colors duration-500 h-full flex flex-col justify-center"
                                         :class="$store.theme?.isDark ? 'bg-gradient-to-br from-indigo-900/90 via-indigo-800/90 to-purple-900/90 border-indigo-500/30' : 'bg-gradient-to-br from-indigo-600 to-purple-700 border-indigo-300 shadow-[0_16px_40px_rgba(79,70,229,0.25)]'">
                                        
                                        <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-indigo-200">Langkah Berikutnya</p>
                                        <h3 class="mt-2 text-2xl font-black leading-tight">Terus asah kemampuanmu!</h3>
                                        <p class="mt-3 text-sm leading-relaxed text-indigo-100/90">Skor prediksimu telah disimpan ke dalam riwayat sistem. Persiapkan dirimu untuk ujian resmi TOEFL CBT.</p>

                                        <div class="mt-6 flex flex-col gap-3">
                                            <a href="{{ route('dashboard') }}" 
                                               class="inline-flex items-center justify-center rounded-2xl bg-white px-5 py-3 text-sm font-bold text-indigo-700 transition-all hover:bg-indigo-50 hover:scale-[1.02] active:scale-95 shadow-lg">
                                                <i class="fas fa-home mr-2 text-indigo-500"></i> Kembali ke Dashboard
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-2 mb-6">
                        <p class="text-xs font-medium" :class="$store.theme?.isDark ? 'text-slate-500' : 'text-slate-400'">Hasil ini disimpan di riwayat latihanmu secara otomatis.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

@push('styles')
    @include('student.partials.shared-bg-styles')
    @include('student.partials.shared-utils-styles')
    <style>
        .ai-skeleton {
            background: linear-gradient(90deg, transparent 25%, rgba(150, 150, 150, 0.1) 50%, transparent 75%);
            background-size: 200% 100%;
            animation: skeleton-move 1.5s infinite;
        }
        @keyframes skeleton-move {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
    </style>
@endpush
</x-app-layout>