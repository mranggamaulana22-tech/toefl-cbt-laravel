<x-app-layout>
    <div class="min-h-screen text-white font-sans selection:bg-purple-500/30 relative overflow-hidden" 
         x-data="examSession()" x-init="initExam()">

        {{-- ===== BACKGROUND SYSTEM ===== --}}
        <div x-show="$store.bg.enabled" class="ai-bg-img absolute inset-0 w-full h-full z-0"></div>
        <div x-show="$store.bg.enabled" class="ai-bg-gradient"></div>
        <div x-show="!$store.bg.enabled" :class="$store.theme?.isDark ? 'bg-[#0b0d13]' : 'bg-white'" class="absolute inset-0 w-full h-full transition-colors duration-500 z-0"></div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col md:flex-row gap-6 relative z-10 pt-8 md:pt-12">
            
            {{-- ===== KONTEN UTAMA ===== --}}
            <div class="w-full transition-all duration-300 ease-out" :class="showNavigator ? 'md:w-3/4' : 'md:w-full'">
                <div x-ref="questionTop" class="overflow-hidden rounded-3xl border border-white/10 bg-[#161f30] shadow-[0_18px_50px_rgba(0,0,0,0.4)]">
                    @include('student.exam.partials.exam-header')
                    @include('student.exam.partials.exam-content')
                    @include('student.exam.partials.exam-modals')
                </div>
            </div>

            {{-- ===== NAVIGATOR SIDEBAR ===== --}}
            @include('student.exam.partials.exam-sidebar')

        </div>

        {{-- ===== JAVASCRIPT LOGIC & STYLES ===== --}}
        @include('student.exam.partials.exam-scripts')
        @include('student.exam.partials.exam-styles')
</x-app-layout>
