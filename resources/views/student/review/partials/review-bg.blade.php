<div x-show="$store.bg.enabled" class="ai-bg-img absolute inset-0 w-full h-full z-0"></div>
<div x-show="$store.bg.enabled" class="ai-bg-gradient"></div>
<div x-show="!$store.bg.enabled" :class="$store.theme?.isDark ? 'bg-[#0b0d13]' : 'bg-white'" class="absolute inset-0 w-full h-full transition-colors duration-500 z-0"></div>

@include('student.partials.shared-bg-styles')
