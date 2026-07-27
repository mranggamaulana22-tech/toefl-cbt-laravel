{{-- ===== JAVASCRIPT LOGIC =====
     Logic dipindahkan ke resources/js/modules/practice-session.js (compiled Vite,
     di-cache browser). File ini hanya menyiapkan data dari server. --}}
<script>
    window.practiceConfig = {
        totalQuestions: {{ $questions->count() }},
        questionCategories: @js($questions->pluck('category')->values()->all()),
        questionIds: @js($questions->pluck('id')->values()->all()),
        progressKey: 'practice_progress_user_{{ auth()->id() }}',
        progressLoadUrl: '{{ route('api.v1.practice.progress.show') }}',
        progressSaveUrl: '{{ route('api.v1.practice.progress.save') }}',
        progressClearUrl: '{{ route('api.v1.practice.progress.clear') }}',
        csrfToken: '{{ csrf_token() }}',
    };
</script>