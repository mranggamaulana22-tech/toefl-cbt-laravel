<div x-cloak x-show="!loaded" class="overflow-hidden rounded-3xl border border-slate-200 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
    <div class="grid gap-6 lg:grid-cols-[1.4fr_0.9fr] lg:items-start">
        <div class="space-y-4">
            <div class="space-y-3">
                <x-skeleton variant="chip" class="w-40" />
                <x-skeleton variant="title" class="w-full max-w-xl" />
            </div>
            <div class="grid gap-3 sm:grid-cols-2">
                <x-skeleton variant="panel" class="h-20 rounded-2xl" />
                <x-skeleton variant="panel" class="h-20 rounded-2xl" />
            </div>
            <x-skeleton variant="panel" class="h-24 rounded-2xl" />
        </div>
        <x-skeleton variant="panel" class="h-80 rounded-3xl border border-indigo-300 bg-indigo-400/20 dark:bg-indigo-400/20" />
    </div>
</div>
