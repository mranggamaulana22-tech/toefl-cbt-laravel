<div x-cloak x-show="!loaded" class="space-y-6">
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-3">
                <div class="h-7 w-56 animate-pulse rounded-xl bg-slate-200/80 dark:bg-slate-700/70"></div>
                <div class="h-5 w-full max-w-2xl animate-pulse rounded-xl bg-slate-200/80 dark:bg-slate-700/70"></div>
            </div>
            <div class="flex items-center gap-2">
                <div class="h-10 w-32 animate-pulse rounded-xl bg-slate-200/80 dark:bg-slate-700/70"></div>
                <div class="h-10 w-32 animate-pulse rounded-xl bg-slate-200/80 dark:bg-slate-700/70"></div>
            </div>
        </div>
    </div>

    <x-skeleton variant="panel" class="h-44 rounded-3xl" />

    <div class="grid gap-4 md:grid-cols-3">
        <x-skeleton variant="panel" class="h-32 rounded-2xl" />
        <x-skeleton variant="panel" class="h-32 rounded-2xl" />
        <x-skeleton variant="panel" class="h-32 rounded-2xl" />
    </div>

    <x-skeleton variant="panel" class="h-40 rounded-3xl" />
</div>
