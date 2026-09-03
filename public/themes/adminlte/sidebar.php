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

            // --- CONTENT dropdown (replace empty context) ---
            $isContent = ($this->uri->segment(2) == 'content');
            $isContentOrder = ($this->uri->segment(2) == 'content' && $this->uri->segment(3) == 'order_baju');
            $contentParentClass = ($isContent || $isContentOrder) ? "nav-item menu-is-opening menu-open" : "nav-item";
            $contentParentLink  = ($isContent && !$isContentOrder) ? ' active' : '';
            $contentOrderActive = $isContentOrder ? ' active' : '';

            $contentSection = "<li class='{$contentParentClass}'>\n"
                . "<a href='" . site_url(SITE_AREA . '/content') . "' class='nav-link{$contentParentLink}'>\n"
                . "<i class='nav-icon fas fa-tachometer-alt'></i>\n"
                . "<p>\nContent\n<i class='right fas fa-angle-left'></i>\n</p>\n"
                . "</a>\n"
                . "<ul class='nav nav-treeview'>\n"
                . "<li class='nav-item'>\n"
                . "<a href='" . site_url(SITE_AREA . '/content/order_baju') . "' class='nav-link{$contentOrderActive}'>\n"
                . "<i class='nav-icon far fa-circle'></i>\n<p>Order Baju</p>\n"
                . "</a>\n"
                . "</li>\n"
                . "</ul>\n"
                . "</li>\n";

            $pos0 = strrpos($navMenus, '</ul>');
            if ($pos0 !== false) {
                $navMenus = substr($navMenus, 0, $pos0) . $contentSection . substr($navMenus, $pos0);
            }

            // --- MASTER dropdown (replace empty context) ---
            $isMaster       = ($this->uri->segment(2) == 'master');
            $isMasterJenis  = ($this->uri->segment(2) == 'master' && $this->uri->segment(3) == 'jenis_baju');
            $isMasterUkuran = ($this->uri->segment(2) == 'master' && $this->uri->segment(3) == 'ukuran');
            $isMasterWarna  = ($this->uri->segment(2) == 'master' && $this->uri->segment(3) == 'warna');
            $masterParentClass = $isMaster ? "nav-item menu-is-opening menu-open" : "nav-item";
            $masterParentLink  = $isMaster && !$isMasterJenis && !$isMasterUkuran && !$isMasterWarna ? ' active' : '';
            $masterJenisActive  = $isMasterJenis ? ' active' : '';
            $masterUkuranActive = $isMasterUkuran ? ' active' : '';
            $masterWarnaActive  = $isMasterWarna ? ' active' : '';

            $masterSection = "<li class='{$masterParentClass}'>\n"
                . "<a href='" . site_url(SITE_AREA . '/master') . "' class='nav-link{$masterParentLink}'>\n"
                . "<i class='nav-icon fas fa-database'></i>\n"
                . "<p>\nMaster\n<i class='right fas fa-angle-left'></i>\n</p>\n"
                . "</a>\n"
                . "<ul class='nav nav-treeview'>\n"
                . "<li class='nav-item'>\n"
                . "<a href='" . site_url(SITE_AREA . '/master/jenis_baju') . "' class='nav-link{$masterJenisActive}'>\n"
                . "<i class='nav-icon far fa-circle'></i>\n<p>Jenis Baju</p>\n"
                . "</a>\n"
                . "</li>\n"
                . "<li class='nav-item'>\n"
                . "<a href='" . site_url(SITE_AREA . '/master/ukuran') . "' class='nav-link{$masterUkuranActive}'>\n"
                . "<i class='nav-icon far fa-circle'></i>\n<p>Ukuran</p>\n"
                . "</a>\n"
                . "</li>\n"
                . "<li class='nav-item'>\n"
                . "<a href='" . site_url(SITE_AREA . '/master/warna') . "' class='nav-link{$masterWarnaActive}'>\n"
                . "<i class='nav-icon far fa-circle'></i>\n<p>Warna</p>\n"
                . "</a>\n"
                . "</li>\n"
                . "</ul>\n"
                . "</li>\n";

            $pos00 = strrpos($navMenus, '</ul>');
            if ($pos00 !== false) {
                $navMenus = substr($navMenus, 0, $pos00) . $masterSection . substr($navMenus, $pos00);
            }

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

            // --- Section BACKUP (standalone dropdown) ---
            $isBackup    = ($this->uri->segment(2) == 'backup');
            $isBackupDoc = ($this->uri->segment(2) == 'backup' && $this->uri->segment(3) == 'document');
            $isBackupDb  = ($this->uri->segment(2) == 'backup' && $this->uri->segment(3) == 'database');
            $backupParentClass = $isBackup ? "nav-item menu-is-opening menu-open" : "nav-item";
            $backupParentLink  = $isBackup && !$isBackupDoc && !$isBackupDb ? ' active' : '';
            $backupDocActive   = $isBackupDoc ? ' active' : '';
            $backupDbActive    = $isBackupDb ? ' active' : '';

            $backupSection = "<li class='{$backupParentClass}'>\n"
                . "<a href='" . site_url(SITE_AREA . '/backup') . "' class='nav-link{$backupParentLink}'>\n"
                . "<i class='nav-icon fas fa-download'></i>\n"
                . "<p>\nBackup\n<i class='right fas fa-angle-left'></i>\n</p>\n"
                . "</a>\n"
                . "<ul class='nav nav-treeview'>\n"
                . "<li class='nav-item'>\n"
                . "<a href='" . site_url(SITE_AREA . '/backup/document') . "' class='nav-link{$backupDocActive}'>\n"
                . "<i class='nav-icon far fa-circle'></i>\n<p>Backup Dokumen</p>\n"
                . "</a>\n"
                . "</li>\n"
                . "<li class='nav-item'>\n"
                . "<a href='" . site_url(SITE_AREA . '/backup/database') . "' class='nav-link{$backupDbActive}'>\n"
                . "<i class='nav-icon far fa-circle'></i>\n<p>Backup Database</p>\n"
                . "</a>\n"
                . "</li>\n"
                . "</ul>\n"
                . "</li>\n";

            $pos3 = strrpos($navMenus, '</ul>');
            if ($pos3 !== false) {
                $navMenus = substr($navMenus, 0, $pos3) . $backupSection . substr($navMenus, $pos3);
            }

            // --- Section LAPORAN DOKUMEN (dropdown) ---
            $isLapDoc    = ($this->uri->segment(2) == 'laporan-dokumen');
            $lapDocParentClass = $isLapDoc ? "nav-item menu-is-opening menu-open" : "nav-item";
            $lapDocParentLink  = $isLapDoc ? ' active' : '';

            $laporanDokumenSection = "<li class='{$lapDocParentClass}'>\n"
                . "<a href='" . site_url(SITE_AREA . '/laporan-dokumen') . "' class='nav-link{$lapDocParentLink}'>\n"
                . "<i class='nav-icon fas fa-file-pdf-o'></i>\n"
                . "<p>\nLaporan Dokumen\n<i class='right fas fa-angle-left'></i>\n</p>\n"
                . "</a>\n"
                . "<ul class='nav nav-treeview'>\n"
                . "<li class='nav-item'>\n"
                . "<a href='" . site_url(SITE_AREA . '/laporan-dokumen/pdf') . "' class='nav-link'>\n"
                . "<i class='nav-icon far fa-circle'></i>\n<p>Cetak PDF</p>\n"
                . "</a>\n"
                . "</li>\n"
                . "<li class='nav-item'>\n"
                . "<a href='" . site_url(SITE_AREA . '/laporan-dokumen/excel') . "' class='nav-link'>\n"
                . "<i class='nav-icon far fa-circle'></i>\n<p>Cetak Excel</p>\n"
                . "</a>\n"
                . "</li>\n"
                . "</ul>\n"
                . "</li>\n";

            $pos4 = strrpos($navMenus, '</ul>');
            if ($pos4 !== false) {
                $navMenus = substr($navMenus, 0, $pos4) . $laporanDokumenSection . substr($navMenus, $pos4);
            }

            // --- Section LAPORAN DATABASE (dropdown) ---
            $isLapDb    = ($this->uri->segment(2) == 'laporan-database');
            $lapDbParentClass = $isLapDb ? "nav-item menu-is-opening menu-open" : "nav-item";
            $lapDbParentLink  = $isLapDb ? ' active' : '';

            $laporanDatabaseSection = "<li class='{$lapDbParentClass}'>\n"
                . "<a href='" . site_url(SITE_AREA . '/laporan-database') . "' class='nav-link{$lapDbParentLink}'>\n"
                . "<i class='nav-icon fas fa-database'></i>\n"
                . "<p>\nLaporan Database\n<i class='right fas fa-angle-left'></i>\n</p>\n"
                . "</a>\n"
                . "<ul class='nav nav-treeview'>\n"
                . "<li class='nav-item'>\n"
                . "<a href='" . site_url(SITE_AREA . '/laporan-database/pdf') . "' class='nav-link'>\n"
                . "<i class='nav-icon far fa-circle'></i>\n<p>Cetak PDF</p>\n"
                . "</a>\n"
                . "</li>\n"
                . "<li class='nav-item'>\n"
                . "<a href='" . site_url(SITE_AREA . '/laporan-database/excel') . "' class='nav-link'>\n"
                . "<i class='nav-icon far fa-circle'></i>\n<p>Cetak Excel</p>\n"
                . "</a>\n"
                . "</li>\n"
                . "</ul>\n"
                . "</li>\n";

            $pos5 = strrpos($navMenus, '</ul>');
            if ($pos5 !== false) {
                $navMenus = substr($navMenus, 0, $pos5) . $laporanDatabaseSection . substr($navMenus, $pos5);
            }

            // --- Section RIWAYAT CETAK LAPORAN (standalone) ---
            $isRiwayat = ($this->uri->segment(2) == 'laporan-history');
            $riwayatClass = $isRiwayat ? ' active' : '';

            $riwayatSection = "<li class='nav-item'>\n"
                . "<a href='" . site_url(SITE_AREA . '/laporan-history') . "' class='nav-link{$riwayatClass}'>\n"
                . "<i class='nav-icon fas fa-history'></i>\n"
                . "<p>\nRiwayat Cetak Laporan\n</p>\n"
                . "</a>\n"
                . "</li>\n";

            $pos6 = strrpos($navMenus, '</ul>');
            if ($pos6 !== false) {
                $navMenus = substr($navMenus, 0, $pos6) . $riwayatSection . substr($navMenus, $pos6);
            }

            echo $navMenus;
            ?>
        </nav>
    </div>
</aside>
