<x-app-layout>
<div class="min-h-screen font-sans selection:bg-purple-500/30 overflow-hidden" x-data="aiAnalysisPage()" x-init="init()">
    <main class="w-full overflow-x-hidden overflow-y-auto relative min-h-screen">
        
        {{-- Background Effects --}}
        <div class="fixed inset-0 z-0">
            <div x-show="$store.bg.enabled" class="ai-bg-img absolute inset-0 w-full h-full z-0"></div>
            <div x-show="$store.bg.enabled" class="ai-bg-gradient"></div>
            <div x-show="!$store.bg.enabled" :class="$store.theme?.isDark ? 'bg-[#0b0d13]' : 'bg-white'" class="absolute inset-0 w-full h-full transition-colors duration-500"></div>
        </div>
        <div class="fixed top-0 right-0 w-[500px] h-[500px] bg-indigo-600/10 blur-[120px] rounded-full pointer-events-none z-0"></div>
        <div class="fixed bottom-0 left-0 w-[300px] h-[300px] bg-purple-600/10 blur-[100px] rounded-full pointer-events-none z-0"></div>

        <div class="w-full max-w-[96%] xl:max-w-[98%] mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-24 relative z-10">
            
            {{-- Header Titles --}}
            <div class="mb-10 text-center animate-fade-in-up">
                <h1 class="text-4xl md:text-5xl font-black mb-4 text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-400 dark:to-purple-400">
                    AI Analysis
                </h1>
                <p class="text-base md:text-lg text-slate-600 dark:text-slate-400 font-medium max-w-2xl mx-auto">
                    Analisa hasil latihan dan ujianmu secara otomatis dengan AI.
                </p>
            </div>

            {{-- Error Notifier --}}
            <div x-show="error" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="mb-4 p-4 rounded bg-red-100 text-red-700 max-w-6xl mx-auto shadow-sm" x-text="error" x-cloak></div>
            
            {{-- Import Loading Skeleton --}}
            @include('student.ai-analysis.partials.skeleton')

            {{-- Import Konten (Tampil setelah loading selesai) --}}
            <div x-show="!loading" 
                 x-transition:enter="transition ease-out duration-700 delay-150"
                 x-transition:enter-start="opacity-0 translate-y-8"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-cloak>
                
                @include('student.ai-analysis.partials.navigation')
                @include('student.ai-analysis.partials.content')

            </div>
        </div>
    </main>
</div>

{{-- Import Script & Styles --}}
@include('student.ai-analysis.partials.scripts')
@include('student.ai-analysis.partials.styles')

</x-app-layout>