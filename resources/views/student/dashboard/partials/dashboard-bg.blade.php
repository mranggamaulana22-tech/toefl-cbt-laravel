{{-- BACKGROUND SECTION --}}
<div class="fixed inset-0 z-0">
    <div x-show="$store.bg.enabled" class="ai-bg-img absolute inset-0 w-full h-full z-0"></div>
    <div x-show="$store.bg.enabled" class="ai-bg-gradient"></div>
    <div x-show="!$store.bg.enabled" :class="$store.theme?.isDark ? 'bg-[#0b0d13]' : 'bg-white'" class="absolute inset-0 w-full h-full transition-colors duration-500"></div>
</div>

{{-- AMBIENT GLOW --}}
<div class="hidden md:block fixed top-0 right-0 w-[500px] h-[500px] bg-indigo-600/10 blur-[120px] rounded-full pointer-events-none z-0"></div>
<div class="hidden md:block fixed bottom-0 left-0 w-[300px] h-[300px] bg-purple-600/10 blur-[100px] rounded-full pointer-events-none z-0"></div>
