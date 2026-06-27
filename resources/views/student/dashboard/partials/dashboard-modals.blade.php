{{-- MODAL STREAK (POPUP) --}}
@if(session('show_streak_modal'))
<div x-data="{ open: true }" 
     x-init="setTimeout(() => open = true, 500)"
     x-show="open" 
     x-cloak
     class="fixed inset-0 z-[99] flex items-center justify-center p-4 overflow-hidden">
    
    {{-- Overlay Gelap --}}
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-500" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         class="fixed inset-0 bg-slate-950/90 backdrop-blur-md"></div>

    {{-- Kartu Modal --}}
    <div x-show="open" 
         x-transition:enter="transition duration-700 cubic-bezier(0.34, 1.56, 0.64, 1)" 
         x-transition:enter-start="opacity-0 scale-50 translate-y-20" 
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         class="relative w-full max-w-[340px] transform overflow-hidden rounded-[3rem] bg-[#1a1033] border border-white/10 p-10 text-center shadow-[0_0_50px_rgba(0,0,0,0.5)]">
        
        <div class="absolute top-10 left-1/2 -translate-x-1/2 w-32 h-32 bg-orange-500/20 blur-[50px] rounded-full"></div>

        <div class="relative flex justify-center mb-4">
            <lottie-player 
                src="{{ asset('animations/fire.json') }}" 
                background="transparent" 
                speed="1.2" 
                style="width: 200px; height: 200px;" 
                loop 
                autoplay>
            </lottie-player>
        </div>

        <div class="relative space-y-2">
            <p class="text-[11px] font-black uppercase tracking-[0.4em] text-orange-500/80">Daily Streak Up!</p>
            <h2 class="text-7xl font-black text-white italic tracking-tighter leading-none">
                {{ session('show_streak_modal') }}x
            </h2>
            <p class="text-sm text-slate-400 font-medium px-2 py-2">
                @if(session('show_streak_modal') <= 1)
                    Selamat! Kamu memulai api pertamamu hari ini.
                @else
                    Luar biasa! Kamu sudah bertahan selama {{ session('show_streak_modal') }} hari.
                @endif
            </p>
        </div>

        <div class="mt-8">
            <button @click="open = false" 
                    class="group relative w-full py-4 bg-orange-600 hover:bg-orange-500 text-white font-bold rounded-2xl transition-all duration-300 shadow-lg shadow-orange-600/30 active:scale-95 overflow-hidden">
                <span class="relative z-10 flex items-center justify-center gap-2 text-sm uppercase tracking-widest">
                    Lanjutkan Belajar <i class="fas fa-chevron-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                </span>
                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></div>
            </button>
        </div>
    </div>
</div>

<style>
    @keyframes shimmer {
        100% { transform: translateX(100%); }
    }
</style>
@endif
