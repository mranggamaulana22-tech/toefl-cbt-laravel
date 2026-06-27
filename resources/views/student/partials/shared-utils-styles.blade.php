{{-- Shared utility styles used across student pages --}}
<style>
    {{-- x-cloak: Hide Alpine elements during initialization --}}
    [x-cloak] { display: none !important; }

    {{-- Common scrollbar styling --}}
    ::-webkit-scrollbar { width: 5px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #4f46e5; }

    {{-- Hide scrollbar variant --}}
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

    {{-- Common animations --}}
    @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0; } }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes fadeSlideUp { from { opacity: 0; transform: translateY(28px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes scaleIn { from { opacity: 0; transform: scale(0.93); } to { opacity: 1; transform: scale(1); } }
    @keyframes countUp { from { opacity: 0; transform: translateY(10px) scale(0.9); } to { opacity: 1; transform: translateY(0) scale(1); } }
    @keyframes rowSlideIn { from { opacity: 0; transform: translateX(-16px); } to { opacity: 1; transform: translateX(0); } }
    @keyframes shimmer { 0% { background-position: -400px 0; } 100% { background-position: 400px 0; } }
    @keyframes flame-pulse { 0%, 100% { transform: scale(1); filter: drop-shadow(0 0 10px rgba(239, 68, 68, 0.5)); } 50% { transform: scale(1.1); filter: drop-shadow(0 0 25px rgba(249, 115, 22, 0.8)); } }
</style>
