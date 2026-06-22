<!-- FOOTER -->
<footer class="footer">
    <div class="footer-grid">
        <div class="footer-brand">
            <h3><?php $fLogo = getSetting('logo_image');
            if ($fLogo): ?><img src="<?= asset('/' . $fLogo) ?>" alt="Logo"
                        style="height:28px;width:28px;object-fit:contain;vertical-align:middle;margin-right:6px;"><?php else: ?>🟢
                <?php endif; ?>HMI Komisariat IT Telkom
            </h3>
            <p>Rumah Bagi Intelektual Muslim. Berdiri sejak 5 Februari 1947, HMI terus berkomitmen mempertahankan
                semangat keindonesiaan dan keislaman di lingkungan kampus.</p>
        </div>
        <div class="footer-links">
            <h4>Navigasi</h4>
            <ul>
                <li><a href="<?= url('/') ?>">Beranda</a></li>
                <li><a href="<?= url('/profil') ?>">Profil</a></li>
                <li><a href="<?= url('/sejarah') ?>">Sejarah</a></li>
                <li><a href="<?= url('/gagasan') ?>">Gagasan</a></li>
            </ul>
        </div>
        <div class="footer-links">
            <h4>Informasi</h4>
            <ul>
                <li><a href="<?= url('/berita') ?>">Berita</a></li>
                <li><a href="<?= url('/event') ?>">Event</a></li>
                <li><a href="<?= url('/dokumen') ?>">Dokumen</a></li>
                <li><a href="<?= url('/hotline') ?>">Hubungi Kami</a></li>
            </ul>
        </div>
        <div class="footer-links">
            <h4>Kaderisasi</h4>
            <ul>
                <li><a href="<?= url('/daftar-kader') ?>">Daftar Kader</a></li>
                <li><a href="<?= url('/login') ?>">Login</a></li>
                <li><a href="<?= lmsUrl('/index.php') ?>">LMS</a></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <span>&copy;
            <?= date('Y') ?> HMI Komisariat IT Telkom. Hak Cipta Dilindungi.
        </span>
        <span>Dibangun dengan ❤️ oleh Kader IT 2019</span>
    </div>
</footer>

<!-- Scripts -->
<script src="<?= asset('/assets/js/main.js') ?>"></script>
</body>

</html>