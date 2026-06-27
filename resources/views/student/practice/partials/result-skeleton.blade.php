<div x-show="!loaded" class="space-y-6 animate-pulse">
    <div class="h-36 bg-slate-200/60 rounded-3xl" :class="$store.theme?.isDark ? 'bg-slate-800/60' : 'bg-slate-200/60'"></div>
    <div class="grid gap-6 lg:grid-cols-[1fr_0.9fr]">
        <div class="h-64 bg-slate-200/60 rounded-3xl" :class="$store.theme?.isDark ? 'bg-slate-800/60' : 'bg-slate-200/60'"></div>
        <div class="h-64 bg-slate-200/60 rounded-3xl" :class="$store.theme?.isDark ? 'bg-slate-800/60' : 'bg-slate-200/60'"></div>
    </div>
</div>
