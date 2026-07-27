{{-- ===== JAVASCRIPT LOGIC =====
     Logic dipindahkan ke resources/js/modules/exam-session.js (compiled Vite,
     di-cache browser). File ini hanya menyiapkan data dari server. --}}
<script>
    window.examConfig = {
        totalQuestions: {{ $questions->count() }},
        questionCategories: @js($questions->pluck('category')->values()->all()),
        questionIds: @js($questions->pluck('id')->values()->all()),
        progressKey: 'exam_progress_user_{{ auth()->id() }}',
    };
</script>