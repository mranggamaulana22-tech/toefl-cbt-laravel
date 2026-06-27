{{-- Shared background styles used across student pages --}}
<style>
    .ai-bg-img {
        position: absolute; inset: 0; z-index: 0; opacity: 0.98;
        background-image: url('{{ asset('images/classroom_light.png') }}');
        background-size: cover; background-position: center; background-repeat: no-repeat;
        background-attachment: scroll; transition: opacity 0.5s;
    }
    .dark .ai-bg-img { background-image: url('{{ asset('images/classroom_dark.png') }}'); }
    .ai-bg-gradient {
        position: absolute; top: 0; left: 0; right: 0; height: 50%; z-index: 1; pointer-events: none;
        background: linear-gradient(to bottom, rgba(255,255,255,0.4), rgba(255,255,255,0.05), transparent);
        transition: opacity 0.5s;
    }
    .dark .ai-bg-gradient { background: linear-gradient(to bottom, rgba(11,13,19,0.3), rgba(11,13,19,0.05), transparent); }
</style>
