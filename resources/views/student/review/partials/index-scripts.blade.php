<script>
    /* ── Smooth page-leave transition ── */
    const _ripOverlay = document.getElementById('_rip-overlay');

    function _ripGo(e, href) {
        if (!href || href === '#' || e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) return;
        e.preventDefault();
        if (_ripOverlay) _ripOverlay.classList.add('on');
        setTimeout(() => { window.location.href = href; }, 260);
    }

    document.addEventListener('DOMContentLoaded', () => {
        /* Intercept semua link di dalam halaman ini */
        document.querySelectorAll('.review-index-page a[href]').forEach(a => {
            const href = a.getAttribute('href');
            if (href && !href.startsWith('#') && !href.startsWith('mailto') && !href.startsWith('tel')) {
                a.addEventListener('click', e => _ripGo(e, href));
            }
        });

        /* Pagination links dari Laravel */
        document.querySelectorAll('nav[role="navigation"] a, .pagination a').forEach(a => {
            const href = a.getAttribute('href');
            if (href && !href.startsWith('#')) {
                a.addEventListener('click', e => _ripGo(e, href));
            }
        });
    });

    /* Hapus overlay saat browser back/forward */
    window.addEventListener('pageshow', () => {
        if (_ripOverlay) _ripOverlay.classList.remove('on');
    });
</script>
