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
                <img src="<?php echo base_url('assets/images/anonym.png'); ?>" class="img-circle" style="width:36px;height:36px;">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?php echo $userDisplayName; ?></a>
                <small><?php echo ucfirst($this->settings_lib->item('auth.use_usernames') ? 'Administrator' : 'Member'); ?></small>
            </div>
        </div>

        <nav class="mt-2">
            <?php
            $uri2 = $this->uri->segment(2);
            $uri3 = $this->uri->segment(3);
            $a = site_url(SITE_AREA);
            ?>

            <li class="nav-header">MENU UTAMA</li>

            <!-- Dashboard -->
            <li class="nav-item">
                <a href="<?php echo $a; ?>" class="nav-link<?php if (!$uri2 && !$uri3) echo ' active'; ?>">
                    <i class="nav-icon fas fa-th-large"></i><p>Dashboard</p>
                </a>
            </li>

            <!-- Order Baju -->
            <li class="nav-item<?php if ($uri2 == 'content') echo ' menu-is-opening menu-open'; ?>">
                <a href="#" class="nav-link" data-fdb-toggle="treeview">
                    <i class="nav-icon fas fa-tshirt"></i><p>Order Baju <i class="right fas fa-angle-left nav-arrow"></i></p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="<?php echo $a.'/content/order_baju'; ?>" class="nav-link<?php if ($uri2 == 'content' && $uri3 == 'order_baju') echo ' active'; ?>">
                            <i class="nav-icon far fa-circle"></i><p>Order Baju</p>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Master -->
            <li class="nav-item<?php if ($uri2 == 'master') echo ' menu-is-opening menu-open'; ?>">
                <a href="#" class="nav-link" data-fdb-toggle="treeview">
                    <i class="nav-icon fas fa-layer-group"></i><p>Master <i class="right fas fa-angle-left nav-arrow"></i></p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="<?php echo $a.'/master/jenis_baju'; ?>" class="nav-link<?php if ($uri2 == 'master' && $uri3 == 'jenis_baju') echo ' active'; ?>">
                            <i class="nav-icon far fa-circle"></i><p>Jenis Baju</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo $a.'/master/ukuran'; ?>" class="nav-link<?php if ($uri2 == 'master' && $uri3 == 'ukuran') echo ' active'; ?>">
                            <i class="nav-icon far fa-circle"></i><p>Ukuran</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo $a.'/master/warna'; ?>" class="nav-link<?php if ($uri2 == 'master' && $uri3 == 'warna') echo ' active'; ?>">
                            <i class="nav-icon far fa-circle"></i><p>Warna</p>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Transaksi -->
            <li class="nav-item<?php if ($uri2 == 'transaksi') echo ' menu-is-opening menu-open'; ?>">
                <a href="#" class="nav-link" data-fdb-toggle="treeview">
                    <i class="nav-icon fas fa-file-invoice-dollar"></i><p>Transaksi <i class="right fas fa-angle-left nav-arrow"></i></p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="<?php echo $a.'/transaksi/transaksi'; ?>" class="nav-link<?php if ($uri2 == 'transaksi' && $uri3 == 'transaksi') echo ' active'; ?>">
                            <i class="nav-icon far fa-circle"></i><p>Daftar Transaksi</p>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Laporan -->
            <?php $isLaporan = ($uri2 == 'reports' || $uri2 == 'laporan-dokumen' || $uri2 == 'laporan-database' || $uri2 == 'laporan-history'); ?>
            <li class="nav-item<?php if ($isLaporan) echo ' menu-is-opening menu-open'; ?>">
                <a href="#" class="nav-link" data-fdb-toggle="treeview">
                    <i class="nav-icon fas fa-chart-line"></i><p>Laporan <i class="right fas fa-angle-left nav-arrow"></i></p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item<?php if ($uri2 == 'reports') echo ' menu-is-opening menu-open'; ?>">
                        <a href="#" class="nav-link" data-fdb-toggle="treeview">
                            <i class="nav-icon far fa-circle"></i><p>Laporan Transaksi <i class="right fas fa-angle-left nav-arrow"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?php echo $a.'/reports/report_pdf'; ?>" class="nav-link<?php if ($uri2 == 'reports' && $uri3 == 'report_pdf') echo ' active'; ?>">
                                    <i class="nav-icon far fa-dot-circle"></i><p>PDF</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?php echo $a.'/reports/report_excel'; ?>" class="nav-link<?php if ($uri2 == 'reports' && $uri3 == 'report_excel') echo ' active'; ?>">
                                    <i class="nav-icon far fa-dot-circle"></i><p>Excel</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item<?php if ($uri2 == 'laporan-dokumen') echo ' menu-is-opening menu-open'; ?>">
                        <a href="#" class="nav-link" data-fdb-toggle="treeview">
                            <i class="nav-icon far fa-circle"></i><p>Laporan Dokumen <i class="right fas fa-angle-left nav-arrow"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?php echo $a.'/laporan-dokumen/pdf'; ?>" class="nav-link<?php if ($uri2 == 'laporan-dokumen' && $uri3 == 'pdf') echo ' active'; ?>">
                                    <i class="nav-icon far fa-dot-circle"></i><p>PDF</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?php echo $a.'/laporan-dokumen/excel'; ?>" class="nav-link<?php if ($uri2 == 'laporan-dokumen' && $uri3 == 'excel') echo ' active'; ?>">
                                    <i class="nav-icon far fa-dot-circle"></i><p>Excel</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item<?php if ($uri2 == 'laporan-database') echo ' menu-is-opening menu-open'; ?>">
                        <a href="#" class="nav-link" data-fdb-toggle="treeview">
                            <i class="nav-icon far fa-circle"></i><p>Laporan Database <i class="right fas fa-angle-left nav-arrow"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?php echo $a.'/laporan-database/pdf'; ?>" class="nav-link<?php if ($uri2 == 'laporan-database' && $uri3 == 'pdf') echo ' active'; ?>">
                                    <i class="nav-icon far fa-dot-circle"></i><p>PDF</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?php echo $a.'/laporan-database/excel'; ?>" class="nav-link<?php if ($uri2 == 'laporan-database' && $uri3 == 'excel') echo ' active'; ?>">
                                    <i class="nav-icon far fa-dot-circle"></i><p>Excel</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo $a.'/laporan-history'; ?>" class="nav-link<?php if ($uri2 == 'laporan-history') echo ' active'; ?>">
                            <i class="nav-icon far fa-circle"></i><p>Riwayat Cetak Laporan</p>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Backup -->
            <li class="nav-item<?php if ($uri2 == 'backup') echo ' menu-is-opening menu-open'; ?>">
                <a href="#" class="nav-link" data-fdb-toggle="treeview">
                    <i class="nav-icon fas fa-folder-open"></i><p>Backup <i class="right fas fa-angle-left nav-arrow"></i></p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="<?php echo $a.'/backup/per_id'; ?>" class="nav-link<?php if ($uri2 == 'backup' && $uri3 == 'per_id') echo ' active'; ?>">
                            <i class="nav-icon far fa-circle"></i><p>Backup Dokumen ID</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo $a.'/backup/per_folder'; ?>" class="nav-link<?php if ($uri2 == 'backup' && $uri3 == 'per_folder') echo ' active'; ?>">
                            <i class="nav-icon far fa-circle"></i><p>Backup Dokumen Folder</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo $a.'/backup/database'; ?>" class="nav-link<?php if ($uri2 == 'backup' && $uri3 == 'database') echo ' active'; ?>">
                            <i class="nav-icon far fa-circle"></i><p>Backup Database</p>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-header">SISTEM</li>

            <!-- Pengaturan -->
            <li class="nav-item<?php if ($uri2 == 'settings' || $uri2 == 'developer') echo ' menu-is-opening menu-open'; ?>">
                <a href="#" class="nav-link" data-fdb-toggle="treeview">
                    <i class="nav-icon fas fa-sliders-h"></i><p>Pengaturan <i class="right fas fa-angle-left nav-arrow"></i></p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="<?php echo $a.'/settings'; ?>" class="nav-link<?php if ($uri2 == 'settings') echo ' active'; ?>">
                            <i class="nav-icon far fa-circle"></i><p>Settings</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo $a.'/developer'; ?>" class="nav-link<?php if ($uri2 == 'developer') echo ' active'; ?>">
                            <i class="nav-icon far fa-circle"></i><p>Developer</p>
                        </a>
                    </li>
                </ul>
            </li>

        </nav>
    </div>
</aside>
