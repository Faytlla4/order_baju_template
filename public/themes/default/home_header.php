<!-- FASHIONER Navbar -->
<nav class="f-navbar" role="navigation" aria-label="Main navigation">
    <a href="<?php echo site_url(); ?>" class="f-navbar-brand">
        <img src="<?php echo base_url('assets/images/logo-transparent.png'); ?>" alt="Logo" class="f-navbar-logo">
        <div class="f-navbar-brand-text">
            <span class="f-navbar-title">FASHIONER</span>
            <span class="f-navbar-subtitle">CLOTHING MANAGEMENT SYSTEM</span>
        </div>
    </a>
    <div class="f-navbar-links">
        <a href="<?php echo site_url(); ?>" class="f-nav-link active">Beranda</a>
        <a href="#tentang" class="f-nav-link">Tentang</a>
        <a href="#fitur" class="f-nav-link">Fitur</a>
        <a href="#kontak" class="f-nav-link">Kontak</a>
    </div>
    <a href="<?php echo site_url(LOGIN_URL); ?>" class="f-btn-login">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
        Login
    </a>
</nav>
