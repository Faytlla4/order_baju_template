<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="<?php echo base_url(); ?>" class="brand-link">
        <img src="<?php echo base_url('assets/images/logo.png'); ?>" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">
            <?php echo html_escape($this->settings_lib->item('site.subtitle')); ?>
        </span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?php echo base_url('assets/images/anonym.png'); ?>" class="img-circle elevation-2">
            </div>
            <div class="info">
                <?php
                	$userDisplayName = isset($current_user->display_name) && !empty($current_user->display_name) ? $current_user->display_name : ($this->settings_lib->item('auth.use_usernames') ? $current_user->username : $current_user->email);
                ?>
                <a href="#" class="d-block"><?php echo $userDisplayName; ?></a>
            </div>
        </div>

        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <nav class="mt-2">
            <?php
            $navMenus = Contextslte::render_menu('text', 'normal');

            // --- Transaksi menu ---
            $isTransaksi = $this->uri->segment(2) == 'transaksi';
            $transaksiLink = $isTransaksi ? ' active' : '';
            $transaksiParentClass = $isTransaksi ? "nav-item menu-is-opening menu-open" : "nav-item";
            $transaksiChildLink1 = $isTransaksi ? ' active' : '';
            $transaksiMenu = "<li class='{$transaksiParentClass}'>\n"
                . "<a href='" . site_url(SITE_AREA . '/transaksi/transaksi') . "' class='nav-link{$transaksiLink}'>\n"
                . "<i class='nav-icon fas fa-shopping-cart'></i>\n"
                . "<p>\nTransaksi\n<i class='right fas fa-angle-left'></i>\n</p>\n"
                . "</a>\n"
                . "<ul class='nav nav-treeview'>\n"
                . "<li class='nav-item'>\n"
                . "<a href='" . site_url(SITE_AREA . '/transaksi/transaksi') . "' class='nav-link{$transaksiChildLink1}'>\n"
                . "<i class='nav-icon far fa-circle'></i>\n<p>Daftar Transaksi</p>\n"
                . "</a>\n"
                . "</li>\n"
                . "</ul>\n"
                . "</li>\n";

            $pos = strrpos($navMenus, '</ul>');
            if ($pos !== false) {
                $navMenus = substr($navMenus, 0, $pos) . $transaksiMenu . substr($navMenus, $pos);
            }

            // --- Section LAPORAN TRANSAKSI (dropdown) ---
            $isPdf   = ($this->uri->segment(2) == 'reports' && $this->uri->segment(3) == 'report_pdf');
            $isExcel = ($this->uri->segment(2) == 'reports' && $this->uri->segment(3) == 'report_excel');
            $isLaporan = $isPdf || $isExcel;
            $laporanParentClass = $isLaporan ? "nav-item menu-is-opening menu-open" : "nav-item";
            $laporanParentLink  = $isLaporan ? ' active' : '';
            $laporanPdfActive   = $isPdf ? ' active' : '';
            $laporanExcelActive = $isExcel ? ' active' : '';

            $laporanSection = "<li class='{$laporanParentClass}'>\n"
                . "<a href='#' class='nav-link{$laporanParentLink}'>\n"
                . "<i class='nav-icon fas fa-file-invoice'></i>\n"
                . "<p>\nLaporan Transaksi\n<i class='right fas fa-angle-left'></i>\n</p>\n"
                . "</a>\n"
                . "<ul class='nav nav-treeview'>\n"
                . "<li class='nav-item'>\n"
                . "<a href='" . site_url(SITE_AREA . '/reports/report_pdf') . "' class='nav-link{$laporanPdfActive}'>\n"
                . "<i class='nav-icon far fa-circle'></i>\n<p>Laporan Transaksi PDF</p>\n"
                . "</a>\n"
                . "</li>\n"
                . "<li class='nav-item'>\n"
                . "<a href='" . site_url(SITE_AREA . '/reports/report_excel') . "' class='nav-link{$laporanExcelActive}'>\n"
                . "<i class='nav-icon far fa-circle'></i>\n<p>Laporan Transaksi Excel</p>\n"
                . "</a>\n"
                . "</li>\n"
                . "</ul>\n"
                . "</li>\n";

            $pos2 = strrpos($navMenus, '</ul>');
            if ($pos2 !== false) {
                $navMenus = substr($navMenus, 0, $pos2) . $laporanSection . substr($navMenus, $pos2);
            }

            echo $navMenus;
            ?>
        </nav>
    </div>
</aside>
