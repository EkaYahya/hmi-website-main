</div><!-- /admin-content -->
</main>
</div><!-- /admin-layout -->

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="<?= asset('/assets/js/main.js') ?>"></script>

<script>
    // Admin sidebar responsive toggle
    (function () {
        const mq = window.matchMedia('(max-width: 768px)');
        const toggle = document.querySelector('.sidebar-toggle');
        if (toggle) {
            toggle.style.display = mq.matches ? 'block' : 'none';
            mq.addEventListener('change', e => toggle.style.display = e.matches ? 'block' : 'none');
        }
    })();
</script>
</body>

</html>