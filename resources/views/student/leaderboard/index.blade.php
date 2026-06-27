<x-app-layout>
    <div class="min-h-screen text-white font-sans selection:bg-purple-500/30 overflow-hidden">
        <main class="w-full overflow-x-hidden overflow-y-auto relative min-h-screen">

            @include('student.leaderboard.partials.leaderboard-bg')

            <div class="max-w-6xl mx-auto p-4 lg:p-8 space-y-8 relative z-10">
                @include('student.leaderboard.partials.leaderboard-hero')

                <section class="mt-6">
                    @include('student.leaderboard.partials.leaderboard-list')
                </section>
            </div>

        </main>
    </div>

    @include('student.leaderboard.partials.leaderboard-styles')
</x-app-layout>
