@include('student.partials.shared-utils-styles')

<style>
    /* ═══════════════════════════════════════════════════════
       ANIMASI ONLY — struktur HTML tidak diubah sama sekali
       ═══════════════════════════════════════════════════════ */
    @keyframes _glowPulse {
        0%, 100% { opacity: 0.10; }
        50%       { opacity: 0.18; }
    }

    .review-index-page .fixed.top-0.right-0 {
        animation: _glowPulse 7s ease-in-out infinite;
    }
    .review-index-page .fixed.bottom-0.left-0 {
        animation: _glowPulse 9s 2s ease-in-out infinite;
    }

    .review-index-page [x-show="loaded"] > div:nth-child(1) {
        animation: fadeInUp  0.50s 0.10s cubic-bezier(.22,.68,0,1.2) both;
    }
    .review-index-hero {
        animation: scaleIn 0.55s 0.22s cubic-bezier(.22,.68,0,1.2) both;
    }
    .review-index-page [x-show="loaded"] > .grid {
        animation: fadeInUp  0.50s 0.34s cubic-bezier(.22,.68,0,1.2) both;
    }
    .review-index-table {
        animation: fadeInUp  0.50s 0.44s cubic-bezier(.22,.68,0,1.2) both;
    }

    .review-index-stat {
        transition: transform 0.22s cubic-bezier(.22,.68,0,1.2),
                    box-shadow 0.22s ease,
                    border-color 0.22s ease;
    }
    .review-index-stat:hover {
        transform: translateY(-3px);
        box-shadow: 0 16px 40px rgba(15,23,42,0.10);
        border-color: #c7d2fe;
    }

    .review-index-hero {
        transition: box-shadow 0.30s ease, transform 0.30s cubic-bezier(.22,.68,0,1.2);
    }
    .review-index-hero:hover {
        box-shadow: 0 24px 70px rgba(30,64,175,0.28);
        transform: translateY(-2px);
    }

    .review-index-row {
        opacity: 0;
        animation: rowSlideIn 0.40s cubic-bezier(.22,.68,0,1.2) forwards;
        transition: background 0.18s ease;
    }
    .review-index-row:hover {
        background: #f8faff;
    }
    @media (prefers-color-scheme: dark) {
        .review-index-row:hover {
            background: #23293a !important;
        }
    }

    .review-index-row:nth-child(1) { animation-delay: 0.54s; }
    .review-index-row:nth-child(2) { animation-delay: 0.60s; }
    .review-index-row:nth-child(3) { animation-delay: 0.66s; }
    .review-index-row:nth-child(4) { animation-delay: 0.72s; }
    .review-index-row:nth-child(5) { animation-delay: 0.78s; }
    .review-index-row:nth-child(n+6) { animation-delay: 0.82s; }

    .review-index-page a {
        transition: transform 0.15s cubic-bezier(.22,.68,0,1.2),
                    box-shadow 0.15s ease,
                    background-color 0.18s ease,
                    border-color 0.18s ease,
                    color 0.18s ease !important;
    }
    .review-index-page a:active {
        transform: scale(0.97) !important;
    }

    .review-index-page a.bg-violet-600 {
        position: relative; overflow: hidden;
    }
    .review-index-page a.bg-violet-600::after {
        content: '';
        position: absolute; top: 0; left: -100%;
        width: 55%; height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
        transition: left 0.5s ease;
        pointer-events: none;
    }
    .review-index-page a.bg-violet-600:hover::after { left: 160%; }
    .review-index-page a.bg-violet-600:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(124,58,237,0.32);
    }

    .review-index-page a.bg-indigo-600 {
        position: relative; overflow: hidden;
    }
    .review-index-page a.bg-indigo-600::after {
        content: '';
        position: absolute; top: 0; left: -100%;
        width: 55%; height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
        transition: left 0.45s ease;
        pointer-events: none;
    }
    .review-index-page a.bg-indigo-600:hover::after { left: 160%; }
    .review-index-page a.bg-indigo-600:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 22px rgba(79,70,229,0.32);
    }

    .review-index-page a.border-slate-200:hover {
        transform: translateY(-1px);
    }

    #_rip-overlay {
        position: fixed; inset: 0; z-index: 9999;
        background: #0b0d13;
        opacity: 0; pointer-events: none;
        transition: opacity 0.25s ease;
    }
    #_rip-overlay.on { opacity: 1; pointer-events: all; }
</style>
