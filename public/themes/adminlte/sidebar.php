<aside class="main-sidebar sidebar-dark-primary elevation-1">
    <a href="<?php echo base_url(); ?>" class="brand-link">
        <img src="<?php echo base_url('assets/images/logo-transparent.png'); ?>" class="brand-image">
        <div class="brand-text">
            <span>FASHIONER</span>
            <small class="brand-subtitle">Fashion Management System</small>
        </div>
    </a>

    <div class="sidebar">
        <?php $userDisplayName = isset($current_user->display_name) && !empty($current_user->display_name) ? $current_user->display_name : ($this->settings_lib->item('auth.use_usernames') ? $current_user->username : $current_user->email); ?>
        <div class="user-panel">
            <div class="image">
                <img src="<?php echo base_url('assets/images/anonym.png'); ?>" class="img-circle elevation-2">
            </div>
            <div class="info">
                <?php
                	$userDisplayName = isset($current_user->display_name) && !empty($current_user->display_name) ? $current_user->display_name : ($this->settings_lib->item('auth.use_usernames') ? $current_user->username : $current_user->email);
                ?>
                <a href="#" class="d-block"><?php echo $userDisplayName; ?></a>
                <small>Administrator</small>
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
            if (!class_exists('Contexts', false)) {
                require_once APPPATH . '../bonfire/modules/ui/libraries/Contexts.php';
                new Contexts();
            }
            $navMenus = Contexts::render_menu('text', 'normal');

            // Menu SK Tidak Mampu sementara disembunyikan dari navigasi.
            // Source code dan fitur tetap dipertahankan.
            // Untuk menampilkan kembali, hapus baris preg_replace berikut.
            $navMenus = preg_replace(
                '/<li class=\'nav-item\'>\s*<a href=\'[^\']*sk_tidak_mampu[^\']*\'[^>]*>.*?<\/li>\s*/is',
                '',
                $navMenus
            );

            // Hapus context "reports" dan "developer" dari menu utama sidebar
            // (akan dipindahkan ke dalam Settings).
            // Menggunakan pendekatan string-based untuk menghapus elemen <li> secara akurat.
            foreach (array('/reports', '/developer') as $ctxPath) {
                $marker = strpos($ctxPath, 'reports') !== false ? '/reports' : '/developer';
                $searchPos = strpos($navMenus, "href='" . site_url(SITE_AREA . $marker) . "'");
                if ($searchPos !== false) {
                    // Cari pembuka <li> sebelum link
                    $liOpenPos = strrpos(substr($navMenus, 0, $searchPos), '<li');
                    if ($liOpenPos !== false) {
                        // Hitung kedalaman nested <li> untuk menemukan closing yang tepat
                        $depth = 0;
                        $scanPos = $liOpenPos;
                        $len = strlen($navMenus);
                        while ($scanPos < $len) {
                            $nextLiOpen = strpos($navMenus, '<li', $scanPos);
                            $nextLiClose = strpos($navMenus, '</li>', $scanPos);
                            if ($nextLiClose === false) break;
                            if ($nextLiOpen !== false && $nextLiOpen < $nextLiClose) {
                                $depth++;
                                $scanPos = $nextLiOpen + 3;
                            } else {
                                $depth--;
                                $scanPos = $nextLiClose + 5;
                                if ($depth <= 0) {
                                    // Sertakan whitespace setelah closing </li>
                                    while ($scanPos < $len && ctype_space($navMenus[$scanPos])) {
                                        $scanPos++;
                                    }
                                    $navMenus = substr($navMenus, 0, $liOpenPos) . substr($navMenus, $scanPos);
                                    break;
                                }
                            }
                        }
                    }
                }
            }

            // --- CONTENT dropdown (replace empty context) ---
            $isContent = ($this->uri->segment(2) == 'content');
            $isContentOrder = ($this->uri->segment(2) == 'content' && $this->uri->segment(3) == 'order_baju');
            $contentParentClass = ($isContent || $isContentOrder) ? "nav-item menu-is-opening menu-open" : "nav-item";
            $contentParentLink  = ($isContent && !$isContentOrder) ? ' active' : '';
            $contentOrderActive = $isContentOrder ? ' active' : '';

            $contentSection = "<li class='{$contentParentClass}'>\n"
                . "<a href='" . site_url(SITE_AREA . '/content') . "' class='nav-link{$contentParentLink}'>\n"
                . "<i class='nav-icon fas fa-tshirt'></i>\n"
                . "<p>\nOrder Baju\n<i class='right fas fa-angle-left'></i>\n</p>\n"
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
            $isLaporan = $isPdf || $isExcel || in_array($this->uri->segment(2), array('laporan-dokumen', 'laporan-database', 'laporan-history'), true);
            $laporanParentClass = $isLaporan ? "nav-item menu-is-opening menu-open" : "nav-item";
            $laporanParentLink  = $isLaporan ? ' active' : '';
            $laporanPdfActive   = $isPdf ? ' active' : '';
            $laporanExcelActive = $isExcel ? ' active' : '';

            $laporanSection = "<li class='{$laporanParentClass}'>\n"
                . "<a href='" . site_url(SITE_AREA . '/reports') . "' class='nav-link{$laporanParentLink}'>\n"
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

            // --- Section BACKUP (dropdown) ---
            $isBackup       = ($this->uri->segment(2) == 'backup');
            $isBackupDb     = ($this->uri->segment(2) == 'backup' && in_array($this->uri->segment(3), array('database', 'database-page'), true));
            $isBackupPerId  = ($this->uri->segment(2) == 'backup' && $this->uri->segment(3) == 'per_id');
            $isBackupPerFld = ($this->uri->segment(2) == 'backup' && $this->uri->segment(3) == 'per_folder');
            $backupParentClass = $isBackup ? "nav-item menu-is-opening menu-open" : "nav-item";
            $backupParentLink  = $isBackup ? ' active' : '';
            $backupDbActive    = $isBackupDb ? ' active' : '';
            $backupPerIdActive = $isBackupPerId ? ' active' : '';
            $backupPerFldActive= $isBackupPerFld ? ' active' : '';

            $backupSection = "<li class='{$backupParentClass}'>\n"
                . "<a href='" . site_url(SITE_AREA . '/backup') . "' class='nav-link{$backupParentLink}'>\n"
                . "<i class='nav-icon fas fa-download'></i>\n"
                . "<p>\nBackup\n<i class='right fas fa-angle-left'></i>\n</p>\n"
                . "</a>\n"
                . "<ul class='nav nav-treeview'>\n"
                . "<li class='nav-item'>\n"
                . "<a href='" . site_url(SITE_AREA . '/backup/per_id') . "' class='nav-link{$backupPerIdActive}'>\n"
                . "<i class='nav-icon far fa-circle'></i>\n<p>Backup Dokumen ID</p>\n"
                . "</a>\n"
                . "</li>\n"
                . "<li class='nav-item'>\n"
                . "<a href='" . site_url(SITE_AREA . '/backup/per_folder') . "' class='nav-link{$backupPerFldActive}'>\n"
                . "<i class='nav-icon far fa-circle'></i>\n<p>Backup Dokumen Folder</p>\n"
                . "</a>\n"
                . "</li>\n"
                . "<li class='nav-item'>\n"
                . "<a href='" . site_url(SITE_AREA . '/backup/database') . "' class='nav-link{$backupDbActive}'>\n"
                . "<i class='nav-icon far fa-circle'></i>\n<p>Backup Database</p>\n"
                . "</a>\n"
                . "</li>\n"
                . "</ul>\n"
                . "</li>\n";

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

            // --- Section RIWAYAT CETAK LAPORAN (standalone) ---
            $isRiwayat = ($this->uri->segment(2) == 'laporan-history');
            $riwayatClass = $isRiwayat ? ' active' : '';

            $riwayatSection = "<li class='nav-item'>\n"
                . "<a href='" . site_url(SITE_AREA . '/laporan-history') . "' class='nav-link{$riwayatClass}'>\n"
                . "<i class='nav-icon fas fa-history'></i>\n"
                . "<p>\nRiwayat Cetak Laporan\n</p>\n"
                . "</a>\n"
                . "</li>\n";

            // Keep every report destination under one compact Laporan accordion.
            $laporanSection = str_replace(
                "</ul>\n</li>\n",
                $laporanDokumenSection . $laporanDatabaseSection . $riwayatSection . "</ul>\n</li>\n",
                $laporanSection
            );
            $pos2 = strrpos($navMenus, '</ul>');
            if ($pos2 !== false) {
                $navMenus = substr($navMenus, 0, $pos2) . $laporanSection . substr($navMenus, $pos2);
            }

            // Keep Backup after the complete Laporan group, matching the sidebar order.
            $pos3 = strrpos($navMenus, '</ul>');
            if ($pos3 !== false) {
                $navMenus = substr($navMenus, 0, $pos3) . $backupSection . substr($navMenus, $pos3);
            }

            // --- Inject Reports and Developer as sub-items under Settings ---
            // Reports dan Developer dipindahkan dari menu utama sidebar ke dalam Settings.
            $isSettings = ($this->uri->segment(2) == 'settings');
            $isReportsCtx = ($this->uri->segment(2) == 'reports');
            $isDeveloperCtx = ($this->uri->segment(2) == 'developer');

            // Build Reports sub-items
            $isRptPdf   = ($isReportsCtx && $this->uri->segment(3) == 'report_pdf');
            $isRptExcel = ($isReportsCtx && $this->uri->segment(3) == 'report_excel');
            $isRptMain  = ($isReportsCtx && empty($this->uri->segment(3)));
            $reportsSubItems = '';
            if ($this->auth->has_permission('Bonfire.reports.View') || $this->auth->has_permission('Reports.Reports.View') || $isReportsCtx) {
                $rptActive = $isRptMain ? ' active' : '';
                $reportsSubItems .= "<li class='nav-item'>\n"
                    . "<a href='" . site_url(SITE_AREA . '/reports') . "' class='nav-link{$rptActive}'>\n"
                    . "<i class='nav-icon far fa-circle'></i>\n<p>Reports</p>\n"
                    . "</a>\n"
                    . "</li>\n";
            }
            if ($this->auth->has_permission('Bonfire.report_pdf.View') || $this->auth->has_permission('Report_pdf.Reports.View') || $isRptPdf) {
                $rptPdfActive = $isRptPdf ? ' active' : '';
                $reportsSubItems .= "<li class='nav-item'>\n"
                    . "<a href='" . site_url(SITE_AREA . '/reports/report_pdf') . "' class='nav-link{$rptPdfActive}'>\n"
                    . "<i class='nav-icon far fa-circle'></i>\n<p>Laporan Transaksi PDF</p>\n"
                    . "</a>\n"
                    . "</li>\n";
            }
            if ($this->auth->has_permission('Bonfire.report_excel.View') || $this->auth->has_permission('Report_excel.Reports.View') || $isRptExcel) {
                $rptExcelActive = $isRptExcel ? ' active' : '';
                $reportsSubItems .= "<li class='nav-item'>\n"
                    . "<a href='" . site_url(SITE_AREA . '/reports/report_excel') . "' class='nav-link{$rptExcelActive}'>\n"
                    . "<i class='nav-icon far fa-circle'></i>\n<p>Laporan Transaksi Excel</p>\n"
                    . "</a>\n"
                    . "</li>\n";
            }

            // Build Developer sub-items
            $isDevDb      = ($isDeveloperCtx && $this->uri->segment(3) == 'database');
            $isDevBuilder = ($isDeveloperCtx && $this->uri->segment(3) == 'builder');
            $isDevLogs    = ($isDeveloperCtx && $this->uri->segment(3) == 'logs');
            $isDevSysinfo = ($isDeveloperCtx && $this->uri->segment(3) == 'sysinfo');
            $isDevTrans   = ($isDeveloperCtx && $this->uri->segment(3) == 'translate');
            $devSubItems = '';
            if ($this->auth->has_permission('Bonfire.database.View') || $this->auth->has_permission('Database.Developer.View') || $isDevDb) {
                $devDbActive = $isDevDb ? ' active' : '';
                $devSubItems .= "<li class='nav-item'>\n"
                    . "<a href='" . site_url(SITE_AREA . '/developer/database') . "' class='nav-link{$devDbActive}'>\n"
                    . "<i class='nav-icon far fa-circle'></i>\n<p>Database Tools</p>\n"
                    . "</a>\n"
                    . "</li>\n";
            }
            if ($this->auth->has_permission('Bonfire.builder.View') || $this->auth->has_permission('Builder.Developer.View') || $isDevBuilder) {
                $devBuilderActive = $isDevBuilder ? ' active' : '';
                $devSubItems .= "<li class='nav-item'>\n"
                    . "<a href='" . site_url(SITE_AREA . '/developer/builder') . "' class='nav-link{$devBuilderActive}'>\n"
                    . "<i class='nav-icon far fa-circle'></i>\n<p>Code Builder</p>\n"
                    . "</a>\n"
                    . "</li>\n";
            }
            if ($this->auth->has_permission('Bonfire.logs.View') || $this->auth->has_permission('Logs.Developer.View') || $isDevLogs) {
                $devLogsActive = $isDevLogs ? ' active' : '';
                $devSubItems .= "<li class='nav-item'>\n"
                    . "<a href='" . site_url(SITE_AREA . '/developer/logs') . "' class='nav-link{$devLogsActive}'>\n"
                    . "<i class='nav-icon far fa-circle'></i>\n<p>Logs</p>\n"
                    . "</a>\n"
                    . "</li>\n";
            }
            if ($this->auth->has_permission('Bonfire.sysinfo.View') || $this->auth->has_permission('Sysinfo.Developer.View') || $isDevSysinfo) {
                $devSysinfoActive = $isDevSysinfo ? ' active' : '';
                $devSubItems .= "<li class='nav-item'>\n"
                    . "<a href='" . site_url(SITE_AREA . '/developer/sysinfo') . "' class='nav-link{$devSysinfoActive}'>\n"
                    . "<i class='nav-icon far fa-circle'></i>\n<p>System Information</p>\n"
                    . "</a>\n"
                    . "</li>\n";
            }
            if ($this->auth->has_permission('Bonfire.translate.View') || $this->auth->has_permission('Translate.Developer.View') || $isDevTrans) {
                $devTransActive = $isDevTrans ? ' active' : '';
                $devSubItems .= "<li class='nav-item'>\n"
                    . "<a href='" . site_url(SITE_AREA . '/developer/translate') . "' class='nav-link{$devTransActive}'>\n"
                    . "<i class='nav-icon far fa-circle'></i>\n<p>Translate</p>\n"
                    . "</a>\n"
                    . "</li>\n";
            }

            // Build the Reports grouping inside Settings (if any sub-items)
            $reportsGroup = '';
            if (!empty($reportsSubItems)) {
                $reportsGroup = "<li class='nav-item'>\n"
                    . "<a href='#' class='nav-link'>\n"
                    . "<i class='nav-icon far fa-circle'></i>\n"
                    . "<p>\nReports\n<i class='right fas fa-angle-left'></i>\n</p>\n"
                    . "</a>\n"
                    . "<ul class='nav nav-treeview'>\n"
                    . $reportsSubItems
                    . "</ul>\n"
                    . "</li>\n";
            }

            // Build the Developer grouping inside Settings (if any sub-items)
            $developerGroup = '';
            if (!empty($devSubItems)) {
                $isDevAny = $isDevDb || $isDevBuilder || $isDevLogs || $isDevSysinfo || $isDevTrans;
                $devGroupClass = $isDevAny ? "nav-item menu-is-opening menu-open" : "nav-item";
                $devGroupActive = $isDevAny ? ' active' : '';
                $developerGroup = "<li class='{$devGroupClass}'>\n"
                    . "<a href='#' class='nav-link{$devGroupActive}'>\n"
                    . "<i class='nav-icon far fa-circle'></i>\n"
                    . "<p>\nDeveloper\n<i class='right fas fa-angle-left'></i>\n</p>\n"
                    . "</a>\n"
                    . "<ul class='nav nav-treeview'>\n"
                    . $devSubItems
                    . "</ul>\n"
                    . "</li>\n";
            }

            // Inject Reports and Developer into the Settings <li> submenu.
            // Find the Settings context <li> and insert Reports/Developer before its closing </ul>.
            $settingsUrl = site_url(SITE_AREA . '/settings');
            $settingsLiPos = strpos($navMenus, "href='" . $settingsUrl . "'");
            if ($settingsLiPos !== false) {
                // Cari pembuka <li> dari settings
                $settingsLiOpen = strrpos(substr($navMenus, 0, $settingsLiPos), '<li');
                if ($settingsLiOpen !== false) {
                    // Hitung seimbang <li>/</li> dan <ul>/</ul> untuk menemukan penutup yang tepat
                    $depthLi = 0;
                    $depthUl = 0;
                    $scanPos = $settingsLiOpen;
                    $len = strlen($navMenus);
                    $insertPos = -1;
                    while ($scanPos < $len) {
                        $liOpen  = strpos($navMenus, '<li', $scanPos);
                        $liClose = strpos($navMenus, '</li>', $scanPos);
                        $ulOpen  = strpos($navMenus, '<ul', $scanPos);
                        $ulClose = strpos($navMenus, '</ul>', $scanPos);

                        $nextPositions = array_filter(array($liOpen, $liClose, $ulOpen, $ulClose), function($p) { return $p !== false; });
                        if (empty($nextPositions)) break;
                        $nextPos = min($nextPositions);

                        if (($liOpen !== false && $liOpen === $nextPos)) {
                            $depthLi++;
                            $scanPos = $liOpen + 3;
                        } elseif (($ulOpen !== false && $ulOpen === $nextPos)) {
                            $depthUl++;
                            $scanPos = $ulOpen + 3;
                        } elseif (($liClose !== false && $liClose === $nextPos)) {
                            $depthLi--;
                            $scanPos = $liClose + 5;
                        } elseif (($ulClose !== false && $ulClose === $nextPos)) {
                            $depthUl--;
                            $scanPos = $ulClose + 5;
                            // Ketika treeview settings ditutup (depthUl kembali ke 0) dan
                            // masih dalam settings <li> (depthLi=1), ini titik injection.
                            if ($depthLi === 1 && $depthUl === 0) {
                                $insertPos = $ulClose;
                                break;
                            }
                        }
                    }
                    if ($insertPos > 0) {
                        // Insert Reports dan Developer sebelum penutup </ul> dari treeview settings
                        $navMenus = substr($navMenus, 0, $insertPos) . $reportsGroup . $developerGroup . substr($navMenus, $insertPos);
                    }
                }
            }

            // Keep the generated permission-aware Settings menu at the bottom,
            // after the custom main menu sections.
            $settingsBlock = '';
            $settingsUrl = site_url(SITE_AREA . '/settings');
            $settingsLinkPos = strpos($navMenus, "href='" . $settingsUrl . "'");
            if ($settingsLinkPos !== false) {
                $settingsOpenPos = strrpos(substr($navMenus, 0, $settingsLinkPos), '<li');
                if ($settingsOpenPos !== false) {
                    $depth = 0;
                    $scanPos = $settingsOpenPos;
                    $navLength = strlen($navMenus);
                    $settingsEndPos = false;
                    while ($scanPos < $navLength) {
                        $nextOpen = strpos($navMenus, '<li', $scanPos);
                        $nextClose = strpos($navMenus, '</li>', $scanPos);
                        if ($nextClose === false) {
                            break;
                        }
                        if ($nextOpen !== false && $nextOpen < $nextClose) {
                            $depth++;
                            $scanPos = $nextOpen + 3;
                        } else {
                            $depth--;
                            $scanPos = $nextClose + 5;
                            if ($depth === 0) {
                                $settingsEndPos = $scanPos;
                                break;
                            }
                        }
                    }
                    if ($settingsEndPos !== false) {
                        $settingsBlock = substr($navMenus, $settingsOpenPos, $settingsEndPos - $settingsOpenPos);
                        $navMenus = substr($navMenus, 0, $settingsOpenPos) . substr($navMenus, $settingsEndPos);

                        // Rebuild only the Settings parent to prevent generated styles from
                        // moving its icon into the SISTEM heading. Keep its permission-aware submenu.
                        $settingsTreeStart = strpos($settingsBlock, '<ul');
                        $settingsTreeEnd = strrpos($settingsBlock, '</ul>');
                        if ($settingsTreeStart !== false && $settingsTreeEnd !== false) {
                            $settingsTree = substr($settingsBlock, $settingsTreeStart, $settingsTreeEnd - $settingsTreeStart + 5);
                            $settingsBlock = "<li class='nav-item settings-menu-item'>\n"
                                . "<a href='" . $settingsUrl . "' class='nav-link" . ($isSettings ? ' active' : '') . "'>\n"
                                . "<i class='nav-icon fas fa-cog'></i>\n"
                                . "<p>Settings<i class='right fas fa-angle-left'></i></p>\n"
                                . "</a>\n"
                                . $settingsTree
                                . "\n</li>\n";
                        }
                    }
                }
            }

            // Add the two visual section labels used by the reference design.
            $firstUlEnd = strpos($navMenus, '>');
            if ($firstUlEnd !== false) {
                $dashboardActive = ($this->uri->segment(2) === '') ? ' active' : '';
                $dashboardSection = "<li class='nav-item'>\n"
                    . "<a href='" . site_url(SITE_AREA) . "' class='nav-link{$dashboardActive}'>\n"
                    . "<i class='nav-icon fas fa-th-large'></i>\n<p>Dashboard</p>\n"
                    . "</a>\n"
                    . "</li>\n";
                $navMenus = substr($navMenus, 0, $firstUlEnd + 1)
                    . "\n<li class='nav-header'>MENU UTAMA</li>\n"
                    . $dashboardSection
                    . substr($navMenus, $firstUlEnd + 1);
            }
            if ($settingsBlock !== '') {
                $lastUlClose = strrpos($navMenus, '</ul>');
                if ($lastUlClose !== false) {
                    $navMenus = substr($navMenus, 0, $lastUlClose)
                        . "\n<li class='nav-header system-nav-header'>SISTEM</li>\n"
                        . $settingsBlock
                        . substr($navMenus, $lastUlClose);
                }
            }

            echo $navMenus;
            ?>
        </nav>
    </div>
</aside>
