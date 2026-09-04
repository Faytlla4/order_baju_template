<?php
/**
 * FASHIONER — Public landing page
 * Subject: garment / tailor workshop. Signature: measuring-tape ornament,
 * asymmetric split (text left, fabric swatch grid right), numbered eyebrows,
 * paper-pattern backdrop.
 */
$hero_label = 'CLOTHING MANAGEMENT SYSTEM';
$brand = 'FASHIONER';
$tagline = 'Solusi Manajemen Pakaian yang Lebih Mudah, Cepat & Terstruktur';
$desc = 'Kelola data pakaian, transaksi, laporan, hingga backup dokumen dan database dengan sistem yang modern dan terintegrasi.';
?>
<section class="f-hero">
    <div class="f-hero-measure" aria-hidden="true">
        <div class="f-hero-measure-track">
            <?php for ($i = 0; $i < 60; $i++): ?>
                <?php if ($i % 5 === 0): ?>
                    <span class="f-tick f-tick--major"><span class="f-tick-num"><?php echo $i; ?></span></span>
                <?php else: ?>
                    <span class="f-tick"></span>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
    </div>

    <div class="f-hero-inner">
        <div class="f-hero-copy">
            <p class="f-hero-eyebrow">
                <span class="f-hero-eyebrow-num">01</span>
                <span><?php echo $hero_label; ?></span>
            </p>
            <h1 class="f-hero-brand"><?php echo $brand; ?></h1>
            <p class="f-hero-tagline"><em>Potong. Jahit. Kirim.</em><br>Manajemen pesanan garment dari pola ke pelanggan.</p>
            <p class="f-hero-desc"><?php echo $desc; ?></p>
            <div class="f-hero-cta">
                <a href="<?php echo site_url(LOGIN_URL); ?>" class="f-btn-login-hero">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Masuk ke Sistem
                </a>
                <a href="#fitur" class="f-btn-ghost-hero">Lihat Fitur <i class="fas fa-arrow-right" style="font-size:0.75rem"></i></a>
            </div>
        </div>

        <div class="f-hero-visual" aria-hidden="true">
            <div class="f-fabric-grid">
                <div class="f-swatch f-swatch--linen"><span class="f-swatch-label">Linen</span><span class="f-swatch-code">#EFE7D8</span></div>
                <div class="f-swatch f-swatch--ivory"><span class="f-swatch-label">Ivory</span><span class="f-swatch-code">#F8F5EF</span></div>
                <div class="f-swatch f-swatch--gold"><span class="f-swatch-label">Gold</span><span class="f-swatch-code">#C8A96B</span></div>
                <div class="f-swatch f-swatch--brown"><span class="f-swatch-label">Brown</span><span class="f-swatch-code">#8A6A47</span></div>
                <div class="f-swatch f-swatch--ink"><span class="f-swatch-label">Ink</span><span class="f-swatch-code">#2A2520</span></div>
                <div class="f-swatch f-swatch--sand"><span class="f-swatch-label">Sand</span><span class="f-swatch-code">#D9C7A8</span></div>
            </div>
            <div class="f-pattern-overlay"></div>
        </div>
    </div>
</section>

<section class="f-features" id="fitur">
    <div class="f-features-grid">
        <article class="f-feature-card">
            <div class="f-feature-num">01</div>
            <div class="f-feature-swatch f-feature-swatch--linen">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20.38 3.46 16 2 12 3.46 8 2 3.62 3.46a2 2 0 0 0-1.34 1.89v13.3a2 2 0 0 0 2.66 1.88L8 19l4-1.46L16 19l4.38-1.46a2 2 0 0 0 1.34-1.89V5.35a2 2 0 0 0-1.34-1.89z"/><line x1="12" y1="2" x2="12" y2="17.54"/></svg>
            </div>
            <h3 class="f-feature-title">Kelola Data Pakaian</h3>
            <p class="f-feature-desc">Atur jenis, ukuran, warna, dan detail pakaian dengan mudah.</p>
        </article>
        <article class="f-feature-card">
            <div class="f-feature-num">02</div>
            <div class="f-feature-swatch f-feature-swatch--gold">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            </div>
            <h3 class="f-feature-title">Transaksi Terstruktur</h3>
            <p class="f-feature-desc">Catat dan kelola setiap transaksi secara rapi dan akurat.</p>
        </article>
        <article class="f-feature-card">
            <div class="f-feature-num">03</div>
            <div class="f-feature-swatch f-feature-swatch--brown">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
            </div>
            <h3 class="f-feature-title">Laporan Lengkap</h3>
            <p class="f-feature-desc">Hasilkan laporan transaksi, dokumen, dan database dalam sekali klik.</p>
        </article>
        <article class="f-feature-card">
            <div class="f-feature-num">04</div>
            <div class="f-feature-swatch f-feature-swatch--ink">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
            </div>
            <h3 class="f-feature-title">Backup Aman</h3>
            <p class="f-feature-desc">Backup dokumen dan database kapan saja dengan aman.</p>
        </article>
    </div>
</section>

<section class="f-tentang" id="tentang">
    <div class="f-tentang-inner">
        <div class="f-tentang-text">
            <p class="f-hero-eyebrow"><span class="f-hero-eyebrow-num">05</span><span>TENTANG KAMI</span></p>
            <h2 class="f-tentang-title">Mempermudah Gestão<br>Usaha garment Anda</h2>
            <p class="f-tentang-desc">FASHIONER adalah sistem manajemen garment berbasis web yang dirancang untuk membantu usaha kecil dan menengah di Indonesia dalam mengelola pesanan, transaksi, dan laporan secara lebih terstruktur.</p>
            <p class="f-tentang-desc">Dikembangkan dengan mempertimbangkan kebutuhan pelaku usaha garment Sidoarjo dan sekitarnya, sistem ini hadir sebagai solusi praktis untuk menggantikan pencatatan manual yang sering kali rumit dan rentan kesalahan.</p>
        </div>
        <div class="f-tentang-stats">
            <div class="f-stat-card">
                <span class="f-stat-num">100%</span>
                <span class="f-stat-label">Web-Based</span>
            </div>
            <div class="f-stat-card">
                <span class="f-stat-num">24/7</span>
                <span class="f-stat-label">Akses Online</span>
            </div>
            <div class="f-stat-card">
                <span class="f-stat-num">∞</span>
                <span class="f-stat-label">Tanpa Batas</span>
            </div>
        </div>
    </div>
</section>

<section class="f-kontak" id="kontak">
    <div class="f-kontak-inner">
        <p class="f-hero-eyebrow"><span class="f-hero-eyebrow-num">06</span><span>HUBUNGI KAMI</span></p>
        <h2 class="f-kontak-title">Butuh Bantuan?</h2>
        <p class="f-kontak-desc">Tim kami siap membantu Anda. Jangan ragu untuk menghubungi kami kapan saja.</p>
        <div class="f-kontak-cards">
            <div class="f-kontak-card">
                <div class="f-kontak-icon"><i class="fas fa-envelope"></i></div>
                <h4>Email</h4>
                <p>support@fashioner.id</p>
            </div>
            <div class="f-kontak-card">
                <div class="f-kontak-icon"><i class="fas fa-phone"></i></div>
                <h4>Telepon</h4>
                <p>+62 812 3456 7890</p>
            </div>
            <div class="f-kontak-card">
                <div class="f-kontak-icon"><i class="fas fa-map-marker-alt"></i></div>
                <h4>Alamat</h4>
                <p>Sidoarjo, Jawa Timur</p>
            </div>
        </div>
    </div>
</section>

<footer class="f-footer">&copy; <?php echo date('Y'); ?> FASHIONER. All rights reserved.</footer>
