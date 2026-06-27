<x-app-layout>
    <div class="min-h-screen text-white font-sans selection:bg-purple-500/30 overflow-hidden" 
         x-data="{ loaded: false }" x-init="setTimeout(() => loaded = true, 100)">
        <main class="w-full overflow-x-hidden overflow-y-auto relative min-h-screen">

            @include('student.dashboard.partials.dashboard-bg')

            <div class="max-w-7xl mx-auto p-4 lg:p-8 space-y-8 relative z-10">
                @include('student.dashboard.partials.dashboard-hero')
                @include('student.dashboard.partials.dashboard-stats')
            </div>

        </main>
    </div>

    @include('student.dashboard.partials.dashboard-styles')
    @include('student.dashboard.partials.dashboard-modals')
</x-app-layout>