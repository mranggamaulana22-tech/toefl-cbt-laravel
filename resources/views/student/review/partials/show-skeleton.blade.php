<div x-cloak x-show="!loaded" class="space-y-6">
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="space-y-3">
                <div class="h-7 w-56 animate-pulse rounded-xl bg-slate-200/80 dark:bg-slate-700/70"></div>
                <div class="h-4 w-full max-w-2xl animate-pulse rounded-xl bg-slate-200/80 dark:bg-slate-700/70"></div>
            </div>
            <div class="flex items-center gap-2">
                <div class="h-10 w-24 animate-pulse rounded-xl bg-slate-200/80 dark:bg-slate-700/70"></div>
                <div class="h-10 w-24 animate-pulse rounded-xl bg-slate-200/80 dark:bg-slate-700/70"></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-2 md:grid-cols-4 md:gap-4">
        <x-skeleton variant="panel" class="h-20 md:h-28" />
        <x-skeleton variant="panel" class="h-20 md:h-28" />
        <x-skeleton variant="panel" class="h-20 md:h-28" />
        <x-skeleton variant="panel" class="h-20 md:h-28" />
    </div>

    <div class="flex flex-col gap-6 lg:flex-row">
        <div class="flex-1 space-y-4">
            <x-skeleton variant="panel" class="h-16 rounded-2xl" />
            <x-skeleton variant="panel" class="h-[360px] rounded-3xl" />
            <div class="grid gap-3 sm:grid-cols-2">
                <x-skeleton variant="panel" class="h-24 rounded-2xl" />
                <x-skeleton variant="panel" class="h-24 rounded-2xl" />
            </div>
            <x-skeleton variant="panel" class="h-28 rounded-2xl" />
        </div>
        <div class="lg:w-1/4 space-y-4">
            <x-skeleton variant="panel" class="h-16 rounded-2xl" />
            <x-skeleton variant="panel" class="h-[360px] rounded-3xl" />
        </div>
    </div>
</div>
