<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="<?php echo base_url(); ?>" class="brand-link">
        <img src="<?php echo base_url('assets/images/logo-transparent.png'); ?>" class="brand-image" style="width:32px;height:32px;object-fit:contain;border-radius:6px;">
        <span class="brand-text">FASHIONER</span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?php echo base_url('assets/images/anonym.png'); ?>" class="img-circle elevation-2" style="width:32px;height:32px;">
            </div>
            <div class="info">
                <?php
                    $userDisplayName = isset($current_user->display_name) && !empty($current_user->display_name) ? $current_user->display_name : ($this->settings_lib->item('auth.use_usernames') ? $current_user->username : $current_user->email);
                ?>
                <a href="#" class="d-block" style="font-size:0.85rem;font-weight:500;"><?php echo $userDisplayName; ?></a>
            </div>
        </div>

        <nav class="mt-2">
            <?php
            $uri2 = $this->uri->segment(2);
            $uri3 = $this->uri->segment(3);

            // Helper for active class
            function fa($seg2, $seg3 = null) {
                $uri2 = $GLOBALS['uri2'];
                $uri3 = $GLOBALS['uri3'];
                if ($seg3 !== null) {
                    return ($uri2 == $seg2 && $uri3 == $seg3) ? ' active' : '';
                }
                return ($uri2 == $seg2) ? ' active' : '';
            }

            function faOpen($seg2, $seg3 = null) {
                $uri2 = $GLOBALS['uri2'];
                $uri3 = $GLOBALS['uri3'];
                if ($seg3 !== null) {
                    return ($uri2 == $seg2 && $uri3 == $seg3) ? ' menu-is-opening menu-open' : '';
                }
                return ($uri2 == $seg2) ? ' menu-is-opening menu-open' : '';
            }

            $a = site_url(SITE_AREA);
            ?>

            <!-- Dashboard -->
            <li class="nav-item">
                <a href="<?php echo $a; ?>" class="nav-link<?php echo fa(''); ?>">
                    <i class="nav-icon fas fa-th-large"></i>
                    <p>Dashboard</p>
                </a>
            </li>

            <!-- Order Baju -->
            <li class="nav-item<?php echo faOpen('content'); ?>">
                <a href="<?php echo $a.'/content'; ?>" class="nav-link<?php echo fa('content'); ?>">
                    <i class="nav-icon fas fa-tshirt"></i>
                    <p>Order Baju <i class="right fas fa-angle-left"></i></p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="<?php echo $a.'/content/order_baju'; ?>" class="nav-link<?php echo fa('content','order_baju'); ?>">
                            <i class="nav-icon far fa-circle"></i><p>Order Baju</p>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Master -->
            <li class="nav-item<?php echo faOpen('master'); ?>">
                <a href="<?php echo $a.'/master'; ?>" class="nav-link<?php echo fa('master'); ?>">
                    <i class="nav-icon fas fa-database"></i>
                    <p>Master <i class="right fas fa-angle-left"></i></p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="<?php echo $a.'/master/jenis_baju'; ?>" class="nav-link<?php echo fa('master','jenis_baju'); ?>">
                            <i class="nav-icon far fa-circle"></i><p>Jenis Baju</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo $a.'/master/ukuran'; ?>" class="nav-link<?php echo fa('master','ukuran'); ?>">
                            <i class="nav-icon far fa-circle"></i><p>Ukuran</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo $a.'/master/warna'; ?>" class="nav-link<?php echo fa('master','warna'); ?>">
                            <i class="nav-icon far fa-circle"></i><p>Warna</p>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Transaksi -->
            <li class="nav-item<?php echo faOpen('transaksi'); ?>">
                <a href="<?php echo $a.'/transaksi/transaksi'; ?>" class="nav-link<?php echo fa('transaksi'); ?>">
                    <i class="nav-icon fas fa-file-invoice"></i>
                    <p>Transaksi <i class="right fas fa-angle-left"></i></p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="<?php echo $a.'/transaksi/transaksi'; ?>" class="nav-link<?php echo fa('transaksi','transaksi'); ?>">
                            <i class="nav-icon far fa-circle"></i><p>Daftar Transaksi</p>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Laporan -->
            <?php
            $isLaporan = ($uri2 == 'reports' || $uri2 == 'laporan-dokumen' || $uri2 == 'laporan-database' || $uri2 == 'laporan-history');
            $lapClass = $isLaporan ? ' menu-is-opening menu-open' : '';
            $lapActive = $isLaporan ? ' active' : '';
            ?>
            <li class="nav-item<?php echo $lapClass; ?>">
                <a href="#" class="nav-link<?php echo $lapActive; ?>">
                    <i class="nav-icon fas fa-chart-bar"></i>
                    <p>Laporan <i class="right fas fa-angle-left"></i></p>
                </a>
                <ul class="nav nav-treeview">
                    <!-- Laporan Transaksi -->
                    <li class="nav-item<?php echo faOpen('reports'); ?>">
                        <a href="#" class="nav-link<?php echo fa('reports'); ?>">
                            <i class="nav-icon far fa-circle"></i><p>Laporan Transaksi <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?php echo $a.'/reports/report_pdf'; ?>" class="nav-link<?php echo fa('reports','report_pdf'); ?>">
                                    <i class="nav-icon far fa-dot-circle"></i><p>PDF</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?php echo $a.'/reports/report_excel'; ?>" class="nav-link<?php echo fa('reports','report_excel'); ?>">
                                    <i class="nav-icon far fa-dot-circle"></i><p>Excel</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <!-- Laporan Dokumen -->
                    <li class="nav-item<?php echo faOpen('laporan-dokumen'); ?>">
                        <a href="#" class="nav-link<?php echo fa('laporan-dokumen'); ?>">
                            <i class="nav-icon far fa-circle"></i><p>Laporan Dokumen <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?php echo $a.'/laporan-dokumen/pdf'; ?>" class="nav-link<?php echo fa('laporan-dokumen','pdf'); ?>">
                                    <i class="nav-icon far fa-dot-circle"></i><p>PDF</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?php echo $a.'/laporan-dokumen/excel'; ?>" class="nav-link<?php echo fa('laporan-dokumen','excel'); ?>">
                                    <i class="nav-icon far fa-dot-circle"></i><p>Excel</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <!-- Laporan Database -->
                    <li class="nav-item<?php echo faOpen('laporan-database'); ?>">
                        <a href="#" class="nav-link<?php echo fa('laporan-database'); ?>">
                            <i class="nav-icon far fa-circle"></i><p>Laporan Database <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?php echo $a.'/laporan-database/pdf'; ?>" class="nav-link<?php echo fa('laporan-database','pdf'); ?>">
                                    <i class="nav-icon far fa-dot-circle"></i><p>PDF</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?php echo $a.'/laporan-database/excel'; ?>" class="nav-link<?php echo fa('laporan-database','excel'); ?>">
                                    <i class="nav-icon far fa-dot-circle"></i><p>Excel</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <!-- Riwayat Cetak Laporan -->
                    <li class="nav-item">
                        <a href="<?php echo $a.'/laporan-history'; ?>" class="nav-link<?php echo fa('laporan-history'); ?>">
                            <i class="nav-icon far fa-circle"></i><p>Riwayat Cetak Laporan</p>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Backup -->
            <li class="nav-item<?php echo faOpen('backup'); ?>">
                <a href="<?php echo $a.'/backup'; ?>" class="nav-link<?php echo fa('backup'); ?>">
                    <i class="nav-icon fas fa-download"></i>
                    <p>Backup <i class="right fas fa-angle-left"></i></p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="<?php echo $a.'/backup/per_id'; ?>" class="nav-link<?php echo fa('backup','per_id'); ?>">
                            <i class="nav-icon far fa-circle"></i><p>Backup Dokumen ID</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo $a.'/backup/per_folder'; ?>" class="nav-link<?php echo fa('backup','per_folder'); ?>">
                            <i class="nav-icon far fa-circle"></i><p>Backup Dokumen Folder</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo $a.'/backup/database'; ?>" class="nav-link<?php echo fa('backup','database'); ?>">
                            <i class="nav-icon far fa-circle"></i><p>Backup Database</p>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Pengaturan -->
            <li class="nav-item<?php echo faOpen('settings'); ?>">
                <a href="<?php echo $a.'/settings'; ?>" class="nav-link<?php echo fa('settings'); ?>">
                    <i class="nav-icon fas fa-cog"></i>
                    <p>Pengaturan <i class="right fas fa-angle-left"></i></p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="<?php echo $a.'/settings'; ?>" class="nav-link<?php echo fa('settings','settings') ?: fa('settings','index'); ?>">
                            <i class="nav-icon far fa-circle"></i><p>Settings</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo $a.'/developer'; ?>" class="nav-link<?php echo fa('developer'); ?>">
                            <i class="nav-icon far fa-circle"></i><p>Developer</p>
                        </a>
                    </li>
                </ul>
            </li>

        </nav>
    </div>
</aside>
