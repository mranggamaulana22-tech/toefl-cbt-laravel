{{-- resources/views/student/ai-analysis/partials/styles.blade.php --}}
@push('styles')
    @include('student.partials.shared-bg-styles')
    @include('student.partials.shared-utils-styles')
    <style>
        :root {
            --ai-bg-main: #f5f6fa;
            --ai-bg-dark: #0b0d13;
            --ai-bg-gradient-light: linear-gradient(to top, rgba(255,255,255,0.7), rgba(255,255,255,0.1), transparent);
            --ai-bg-gradient-dark: linear-gradient(to top right, #0b0d13, transparent, transparent);
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
    </style>
@endpush