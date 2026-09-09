<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * AdminLTE Sidebar
 * Project: order_baju_template
 *
 * Sidebar ini sengaja tidak menggunakan:
 *
 *     Contexts::render_menu()
 *
 * dan tidak melakukan manipulasi HTML hasil generator menu.
 *
 * Alasannya:
 * - Struktur sidebar aplikasi sudah ditentukan secara eksplisit.
 * - Semua URL diarahkan langsung ke route aplikasi.
 * - Parent menu hanya berfungsi sebagai dropdown.
 * - Child menu langsung menuju controller/module masing-masing.
 * - Menu Laporan Database sengaja disembunyikan.
 * - Settings dan Developer tidak ditampilkan.
 *
 * Jangan menambahkan Contexts::render_menu() kembali ke file ini
 * kecuali seluruh struktur menu memang ingin dikembalikan ke
 * sistem menu generator Bonfire.
 */

/*
|--------------------------------------------------------------------------
| Current URL
|--------------------------------------------------------------------------
|
| Digunakan hanya untuk menentukan menu active/open.
|
*/
$currentUri = trim($this->uri->uri_string(), '/');

/*
|--------------------------------------------------------------------------
| Helper URL
|--------------------------------------------------------------------------
*/
$adminUrl = function ($path = '') {
    $path = trim((string) $path, '/');

    if ($path === '') {
        return site_url(SITE_AREA);
    }

    return site_url(SITE_AREA . '/' . $path);
};

/*
|--------------------------------------------------------------------------
| Active helpers
|--------------------------------------------------------------------------
*/
$isAdminRoot = ($currentUri === trim(SITE_AREA, '/'));

$isOrderBaju = (
    $currentUri === trim(SITE_AREA . '/content', '/') ||
    strpos(
        $currentUri,
        trim(SITE_AREA . '/content/order_baju', '/') . '/'
    ) === 0 ||
    $currentUri === trim(SITE_AREA . '/content/order_baju', '/')
);

$isMaster = (
    $currentUri === trim(SITE_AREA . '/master', '/') ||
    strpos(
        $currentUri,
        trim(SITE_AREA . '/master/', '/')
    ) === 0
);

$isJenisBaju = (
    $currentUri === trim(SITE_AREA . '/master/jenis_baju', '/') ||
    strpos(
        $currentUri,
        trim(SITE_AREA . '/master/jenis_baju/', '/')
    ) === 0
);

$isUkuran = (
    $currentUri === trim(SITE_AREA . '/master/ukuran', '/') ||
    strpos(
        $currentUri,
        trim(SITE_AREA . '/master/ukuran/', '/')
    ) === 0
);

$isWarna = (
    $currentUri === trim(SITE_AREA . '/master/warna', '/') ||
    strpos(
        $currentUri,
        trim(SITE_AREA . '/master/warna/', '/')
    ) === 0
);

$isTransaksi = (
    $currentUri === trim(SITE_AREA . '/transaksi', '/') ||
    strpos(
        $currentUri,
        trim(SITE_AREA . '/transaksi/', '/')
    ) === 0
);

$isLaporanTransaksi = (
    $currentUri === trim(SITE_AREA . '/reports', '/') ||
    strpos(
        $currentUri,
        trim(SITE_AREA . '/reports/', '/')
    ) === 0
);

$isLaporanDokumen = (
    $currentUri === trim(SITE_AREA . '/laporan-dokumen', '/') ||
    strpos(
        $currentUri,
        trim(SITE_AREA . '/laporan-dokumen/', '/')
    ) === 0
);

$isRiwayatLaporan = (
    $currentUri === trim(SITE_AREA . '/laporan-history', '/') ||
    strpos(
        $currentUri,
        trim(SITE_AREA . '/laporan-history/', '/')
    ) === 0
);

$isBackup = (
    $currentUri === trim(SITE_AREA . '/backup', '/') ||
    strpos(
        $currentUri,
        trim(SITE_AREA . '/backup/', '/')
    ) === 0
);

/*
|--------------------------------------------------------------------------
| Specific active states
|--------------------------------------------------------------------------
*/
$isOrderBajuPage = (
    $currentUri === trim(SITE_AREA . '/content/order_baju', '/') ||
    strpos(
        $currentUri,
        trim(SITE_AREA . '/content/order_baju/', '/')
    ) === 0
);

$isReportPdf = (
    $currentUri === trim(SITE_AREA . '/reports/report_pdf', '/') ||
    strpos(
        $currentUri,
        trim(SITE_AREA . '/reports/report_pdf/', '/')
    ) === 0
);

$isReportExcel = (
    $currentUri === trim(SITE_AREA . '/reports/report_excel', '/') ||
    strpos(
        $currentUri,
        trim(SITE_AREA . '/reports/report_excel/', '/')
    ) === 0
);

$isLaporanDokumenPdf = (
    $currentUri === trim(SITE_AREA . '/laporan-dokumen/pdf', '/') ||
    strpos(
        $currentUri,
        trim(SITE_AREA . '/laporan-dokumen/pdf/', '/')
    ) === 0
);

$isLaporanDokumenExcel = (
    $currentUri === trim(SITE_AREA . '/laporan-dokumen/excel', '/') ||
    strpos(
        $currentUri,
        trim(SITE_AREA . '/laporan-dokumen/excel/', '/')
    ) === 0
);

$isBackupPerId = (
    $currentUri === trim(SITE_AREA . '/backup/per_id', '/') ||
    strpos(
        $currentUri,
        trim(SITE_AREA . '/backup/per_id/', '/')
    ) === 0
);

$isBackupPerFolder = (
    $currentUri === trim(SITE_AREA . '/backup/per_folder', '/') ||
    strpos(
        $currentUri,
        trim(SITE_AREA . '/backup/per_folder/', '/')
    ) === 0
);

$isBackupDatabase = (
    $currentUri === trim(SITE_AREA . '/backup/database', '/') ||
    strpos(
        $currentUri,
        trim(SITE_AREA . '/backup/database/', '/')
    ) === 0
);

/*
|--------------------------------------------------------------------------
| Parent state
|--------------------------------------------------------------------------
*/
$orderBajuOpen = $isOrderBaju;
$masterOpen = $isMaster;
$transaksiOpen = $isTransaksi;
$laporanTransaksiOpen = $isLaporanTransaksi;
$laporanDokumenOpen = $isLaporanDokumen;
$backupOpen = $isBackup;

/*
|--------------------------------------------------------------------------
| CSS helpers
|--------------------------------------------------------------------------
*/
$orderBajuParentClass = $orderBajuOpen
    ? 'nav-item menu-is-opening menu-open'
    : 'nav-item';

$masterParentClass = $masterOpen
    ? 'nav-item menu-is-opening menu-open'
    : 'nav-item';

$transaksiParentClass = $transaksiOpen
    ? 'nav-item menu-is-opening menu-open'
    : 'nav-item';

$laporanTransaksiParentClass = $laporanTransaksiOpen
    ? 'nav-item menu-is-opening menu-open'
    : 'nav-item';

$laporanDokumenParentClass = $laporanDokumenOpen
    ? 'nav-item menu-is-opening menu-open'
    : 'nav-item';

$backupParentClass = $backupOpen
    ? 'nav-item menu-is-opening menu-open'
    : 'nav-item';
?>

<style>
    /*
     * Sidebar section heading.
     */
    .main-sidebar .main-menu-header {
        color: #8A6A47 !important;
    }

    /*
     * Brand.
     */
    .main-sidebar .brand-link {
        display: flex;
        align-items: center;
        min-height: 58px;
    }

    .main-sidebar .brand-image {
        width: 34px;
        height: 34px;
        object-fit: contain;
        margin-left: 4px;
        margin-right: 10px;
        opacity: 1;
    }

    .main-sidebar .brand-text {
        display: flex;
        flex-direction: column;
        line-height: 1.1;
    }

    .main-sidebar .brand-subtitle {
        display: block;
        margin-top: 4px;
        font-size: 10px;
        font-weight: 400;
        opacity: .65;
    }

    /*
     * Parent menu.
     */
    .main-sidebar .nav-sidebar > .nav-item > .nav-link {
        cursor: pointer;
    }

    /*
     * Child menu indentation.
     */
    .main-sidebar .nav-treeview {
        padding-left: 0;
    }

    .main-sidebar .nav-treeview > .nav-item > .nav-link {
        padding-left: 2.5rem;
    }

    .main-sidebar .nav-treeview > .nav-item > .nav-link .nav-icon {
        font-size: .65rem;
    }

    /*
     * Header spacing.
     */
    .main-sidebar .nav-header {
        padding-left: 1rem;
        padding-right: 1rem;
    }

    /*
     * Prevent the sidebar's parent links from looking like normal
     * destination links because they are dropdown controls.
     */
    .main-sidebar .sidebar-parent-link {
        user-select: none;
    }
</style>

<aside class="main-sidebar sidebar-dark-primary elevation-1">

    <!-- ==========================================================
         BRAND
         ========================================================== -->
    <a href="<?php echo html_escape($adminUrl('')); ?>" class="brand-link">

        <img
            src="<?php echo html_escape(base_url('assets/images/logo-transparent.png')); ?>"
            class="brand-image"
            alt="Fashioner"
        >

        <div class="brand-text">
            <span>FASHIONER</span>
            <small class="brand-subtitle">
                Fashion Management System
            </small>
        </div>

    </a>


    <!-- ==========================================================
         SIDEBAR
         ========================================================== -->
    <div class="sidebar">

        <?php
        $userDisplayName = '';

        if (
            isset($current_user->display_name) &&
            !empty($current_user->display_name)
        ) {
            $userDisplayName = $current_user->display_name;
        } elseif (
            isset($current_user->username) &&
            !empty($current_user->username)
        ) {
            $userDisplayName = $current_user->username;
        } elseif (
            isset($current_user->email) &&
            !empty($current_user->email)
        ) {
            $userDisplayName = $current_user->email;
        } else {
            $userDisplayName = 'Administrator';
        }
        ?>

        <!-- ======================================================
             USER PANEL
             ====================================================== -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">

            <div class="image">
                <img
                    src="<?php echo html_escape(base_url('assets/images/anonym.png')); ?>"
                    class="img-circle elevation-2"
                    alt="User"
                >
            </div>

            <div class="info">

                <a href="#" class="d-block">
                    <?php echo html_escape($userDisplayName); ?>
                </a>

                <small>
                    Administrator
                </small>

            </div>

        </div>


        <!-- ======================================================
             SEARCH
             ====================================================== -->
        <div class="form-inline">

            <div
                class="input-group"
                data-widget="sidebar-search"
            >

                <input
                    class="form-control form-control-sidebar"
                    type="search"
                    placeholder="Search"
                    aria-label="Search"
                >

                <div class="input-group-append">

                    <button
                        type="button"
                        class="btn btn-sidebar"
                        aria-label="Search sidebar"
                    >
                        <i class="fas fa-search fa-fw"></i>
                    </button>

                </div>

            </div>

        </div>


        <!-- ======================================================
             NAVIGATION
             ====================================================== -->
        <nav class="mt-2">

            <ul
                class="nav nav-pills nav-sidebar flex-column"
                data-widget="treeview"
                role="menu"
                data-accordion="false"
            >

                <!-- ==================================================
                     MENU UTAMA
                     ================================================== -->
                <li class="nav-header main-menu-header">
                    MENU UTAMA
                </li>


                <!-- ==================================================
                     DASHBOARD
                     ================================================== -->
                <li class="nav-item">

                    <a
                        href="<?php echo html_escape($adminUrl('')); ?>"
                        class="nav-link<?php echo $isAdminRoot ? ' active' : ''; ?>"
                    >

                        <i class="nav-icon fas fa-th-large"></i>

                        <p>
                            Dashboard
                        </p>

                    </a>

                </li>


                <!-- ==================================================
                     ORDER BAJU
                     ================================================== -->
                <li class="<?php echo $orderBajuParentClass; ?>">

                    <a
                        href="#"
                        class="nav-link sidebar-parent-link<?php echo $orderBajuOpen ? ' active' : ''; ?>"
                        aria-expanded="<?php echo $orderBajuOpen ? 'true' : 'false'; ?>"
                    >

                        <i class="nav-icon fas fa-tshirt"></i>

                        <p>
                            Order Baju
                            <i class="right fas fa-angle-left"></i>
                        </p>

                    </a>

                    <ul class="nav nav-treeview">

                        <li class="nav-item">

                            <a
                                href="<?php echo html_escape($adminUrl('content/order_baju')); ?>"
                                class="nav-link<?php echo $isOrderBajuPage ? ' active' : ''; ?>"
                            >

                                <i class="nav-icon far fa-circle"></i>

                                <p>
                                    Order Baju
                                </p>

                            </a>

                        </li>

                    </ul>

                </li>


                <!-- ==================================================
                     MASTER
                     ================================================== -->
                <li class="<?php echo $masterParentClass; ?>">

                    <a
                        href="#"
                        class="nav-link sidebar-parent-link<?php echo $masterOpen ? ' active' : ''; ?>"
                        aria-expanded="<?php echo $masterOpen ? 'true' : 'false'; ?>"
                    >

                        <i class="nav-icon fas fa-database"></i>

                        <p>
                            Master
                            <i class="right fas fa-angle-left"></i>
                        </p>

                    </a>

                    <ul class="nav nav-treeview">

                        <!-- Jenis Baju -->
                        <li class="nav-item">

                            <a
                                href="<?php echo html_escape($adminUrl('master/jenis_baju')); ?>"
                                class="nav-link<?php echo $isJenisBaju ? ' active' : ''; ?>"
                            >

                                <i class="nav-icon far fa-circle"></i>

                                <p>
                                    Jenis Baju
                                </p>

                            </a>

                        </li>

                        <!-- Ukuran -->
                        <li class="nav-item">

                            <a
                                href="<?php echo html_escape($adminUrl('master/ukuran')); ?>"
                                class="nav-link<?php echo $isUkuran ? ' active' : ''; ?>"
                            >

                                <i class="nav-icon far fa-circle"></i>

                                <p>
                                    Ukuran
                                </p>

                            </a>

                        </li>

                        <!-- Warna -->
                        <li class="nav-item">

                            <a
                                href="<?php echo html_escape($adminUrl('master/warna')); ?>"
                                class="nav-link<?php echo $isWarna ? ' active' : ''; ?>"
                            >

                                <i class="nav-icon far fa-circle"></i>

                                <p>
                                    Warna
                                </p>

                            </a>

                        </li>

                    </ul>

                </li>


                <!-- ==================================================
                     TRANSAKSI
                     ================================================== -->
                <li class="<?php echo $transaksiParentClass; ?>">

                    <a
                        href="#"
                        class="nav-link sidebar-parent-link<?php echo $transaksiOpen ? ' active' : ''; ?>"
                        aria-expanded="<?php echo $transaksiOpen ? 'true' : 'false'; ?>"
                    >

                        <i class="nav-icon fas fa-shopping-cart"></i>

                        <p>
                            Transaksi
                            <i class="right fas fa-angle-left"></i>
                        </p>

                    </a>

                    <ul class="nav nav-treeview">

                        <li class="nav-item">

                            <a
                                href="<?php echo html_escape($adminUrl('transaksi/transaksi')); ?>"
                                class="nav-link<?php echo $isTransaksi ? ' active' : ''; ?>"
                            >

                                <i class="nav-icon far fa-circle"></i>

                                <p>
                                    Daftar Transaksi
                                </p>

                            </a>

                        </li>

                    </ul>

                </li>


                <!-- ==================================================
                     LAPORAN TRANSAKSI
                     ================================================== -->
                <li class="<?php echo $laporanTransaksiParentClass; ?>">

                    <a
                        href="#"
                        class="nav-link sidebar-parent-link<?php echo $laporanTransaksiOpen ? ' active' : ''; ?>"
                        aria-expanded="<?php echo $laporanTransaksiOpen ? 'true' : 'false'; ?>"
                    >

                        <i class="nav-icon fas fa-file-invoice"></i>

                        <p>
                            Laporan Transaksi
                            <i class="right fas fa-angle-left"></i>
                        </p>

                    </a>

                    <ul class="nav nav-treeview">

                        <!-- PDF -->
                        <li class="nav-item">

                            <a
                                href="<?php echo html_escape($adminUrl('reports/report_pdf')); ?>"
                                class="nav-link<?php echo $isReportPdf ? ' active' : ''; ?>"
                            >

                                <i class="nav-icon far fa-circle"></i>

                                <p>
                                    Laporan Transaksi PDF
                                </p>

                            </a>

                        </li>

                        <!-- Excel -->
                        <li class="nav-item">

                            <a
                                href="<?php echo html_escape($adminUrl('reports/report_excel')); ?>"
                                class="nav-link<?php echo $isReportExcel ? ' active' : ''; ?>"
                            >

                                <i class="nav-icon far fa-circle"></i>

                                <p>
                                    Laporan Transaksi Excel
                                </p>

                            </a>

                        </li>

                    </ul>

                </li>


                <!-- ==================================================
                     LAPORAN DOKUMEN
                     ================================================== -->
                <li class="<?php echo $laporanDokumenParentClass; ?>">

                    <a
                        href="#"
                        class="nav-link sidebar-parent-link<?php echo $laporanDokumenOpen ? ' active' : ''; ?>"
                        aria-expanded="<?php echo $laporanDokumenOpen ? 'true' : 'false'; ?>"
                    >

                        <i class="nav-icon fas fa-file-alt"></i>

                        <p>
                            Laporan Dokumen
                            <i class="right fas fa-angle-left"></i>
                        </p>

                    </a>

                    <ul class="nav nav-treeview">

                        <!-- Cetak PDF -->
                        <li class="nav-item">

                            <a
                                href="<?php echo html_escape($adminUrl('laporan-dokumen/pdf')); ?>"
                                class="nav-link<?php echo $isLaporanDokumenPdf ? ' active' : ''; ?>"
                            >

                                <i class="nav-icon far fa-circle"></i>

                                <p>
                                    Cetak PDF
                                </p>

                            </a>

                        </li>

                        <!-- Cetak Excel -->
                        <li class="nav-item">

                            <a
                                href="<?php echo html_escape($adminUrl('laporan-dokumen/excel')); ?>"
                                class="nav-link<?php echo $isLaporanDokumenExcel ? ' active' : ''; ?>"
                            >

                                <i class="nav-icon far fa-circle"></i>

                                <p>
                                    Cetak Excel
                                </p>

                            </a>

                        </li>

                    </ul>

                </li>


                <!-- ==================================================
                     LAPORAN DATABASE
                     ==================================================

                     SENGAJA TIDAK DITAMPILKAN.

                     Route dan backend tetap dipertahankan.
                     Jadi fitur tidak dihapus dari aplikasi, hanya
                     tidak muncul pada navigasi sidebar.
                     ================================================== -->


                <!-- ==================================================
                     RIWAYAT CETAK LAPORAN
                     ================================================== -->
                <li class="nav-item">

                    <a
                        href="<?php echo html_escape($adminUrl('laporan-history')); ?>"
                        class="nav-link<?php echo $isRiwayatLaporan ? ' active' : ''; ?>"
                    >

                        <i class="nav-icon fas fa-history"></i>

                        <p>
                            Riwayat Cetak Laporan
                        </p>

                    </a>

                </li>


                <!-- ==================================================
                     BACKUP
                     ================================================== -->
                <li class="<?php echo $backupParentClass; ?>">

                    <a
                        href="#"
                        class="nav-link sidebar-parent-link<?php echo $backupOpen ? ' active' : ''; ?>"
                        aria-expanded="<?php echo $backupOpen ? 'true' : 'false'; ?>"
                    >

                        <i class="nav-icon fas fa-download"></i>

                        <p>
                            Backup
                            <i class="right fas fa-angle-left"></i>
                        </p>

                    </a>

                    <ul class="nav nav-treeview">

                        <!-- Backup Dokumen ID -->
                        <li class="nav-item">

                            <a
                                href="<?php echo html_escape($adminUrl('backup/per_id')); ?>"
                                class="nav-link<?php echo $isBackupPerId ? ' active' : ''; ?>"
                            >

                                <i class="nav-icon far fa-circle"></i>

                                <p>
                                    Backup Dokumen ID
                                </p>

                            </a>

                        </li>

                        <!-- Backup Dokumen Folder -->
                        <li class="nav-item">

                            <a
                                href="<?php echo html_escape($adminUrl('backup/per_folder')); ?>"
                                class="nav-link<?php echo $isBackupPerFolder ? ' active' : ''; ?>"
                            >

                                <i class="nav-icon far fa-circle"></i>

                                <p>
                                    Backup Dokumen Folder
                                </p>

                            </a>

                        </li>

                        <!-- Backup Database -->
                        <li class="nav-item">

                            <a
                                href="<?php echo html_escape($adminUrl('backup/database')); ?>"
                                class="nav-link<?php echo $isBackupDatabase ? ' active' : ''; ?>"
                            >

                                <i class="nav-icon far fa-circle"></i>

                                <p>
                                    Backup Database
                                </p>

                            </a>

                        </li>

                    </ul>

                </li>

            </ul>

        </nav>

    </div>

</aside>