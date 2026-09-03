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
                <?php $userDisplayName = isset($current_user->display_name) && !empty($current_user->display_name) ? $current_user->display_name : ($this->settings_lib->item('auth.use_usernames') ? $current_user->username : $current_user->email); ?>
                <a href="#" class="d-block" style="font-size:0.85rem;font-weight:500;"><?php echo $userDisplayName; ?></a>
            </div>
        </div>

        <nav class="mt-2">
            <?php
            $uri2 = $this->uri->segment(2);
            $uri3 = $this->uri->segment(3);
            $a = site_url(SITE_AREA);
            ?>

            <!-- Dashboard -->
            <li class="nav-item">
                <a href="<?php echo $a; ?>" class="nav-link<?php if (!$uri2 && !$uri3) echo ' active'; ?>">
                    <i class="nav-icon fas fa-th-large"></i><p>Dashboard</p>
                </a>
            </li>

            <!-- Order Baju -->
            <li style="border-top:1px solid rgba(248,245,239,0.08);margin:6px 14px 0;padding-top:6px;" class="nav-item<?php if ($uri2 == 'content') echo ' menu-is-opening menu-open'; ?>">
                <a href="#" class="nav-link" data-widget="treeview">
                    <i class="nav-icon fas fa-tshirt"></i><p>Order Baju <i class="right fas fa-angle-left"></i></p>
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
            <li style="border-top:1px solid rgba(248,245,239,0.08);margin:6px 14px 0;padding-top:6px;" class="nav-item<?php if ($uri2 == 'master') echo ' menu-is-opening menu-open'; ?>">
                <a href="#" class="nav-link" data-widget="treeview">
                    <i class="nav-icon fas fa-database"></i><p>Master <i class="right fas fa-angle-left"></i></p>
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
            <li style="border-top:1px solid rgba(248,245,239,0.08);margin:6px 14px 0;padding-top:6px;" class="nav-item<?php if ($uri2 == 'transaksi') echo ' menu-is-opening menu-open'; ?>">
                <a href="#" class="nav-link" data-widget="treeview">
                    <i class="nav-icon fas fa-file-invoice"></i><p>Transaksi <i class="right fas fa-angle-left"></i></p>
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
            <li style="border-top:1px solid rgba(248,245,239,0.08);margin:6px 14px 0;padding-top:6px;" class="nav-item<?php if ($isLaporan) echo ' menu-is-opening menu-open'; ?>">
                <a href="#" class="nav-link" data-widget="treeview">
                    <i class="nav-icon fas fa-chart-bar"></i><p>Laporan <i class="right fas fa-angle-left"></i></p>
                </a>
                <ul class="nav nav-treeview">
                    <!-- Laporan Transaksi -->
                    <li class="nav-item<?php if ($uri2 == 'reports') echo ' menu-is-opening menu-open'; ?>">
                        <a href="#" class="nav-link" data-widget="treeview">
                            <i class="nav-icon far fa-circle"></i><p>Laporan Transaksi <i class="right fas fa-angle-left"></i></p>
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
                    <!-- Laporan Dokumen -->
                    <li class="nav-item<?php if ($uri2 == 'laporan-dokumen') echo ' menu-is-opening menu-open'; ?>">
                        <a href="#" class="nav-link" data-widget="treeview">
                            <i class="nav-icon far fa-circle"></i><p>Laporan Dokumen <i class="right fas fa-angle-left"></i></p>
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
                    <!-- Laporan Database -->
                    <li class="nav-item<?php if ($uri2 == 'laporan-database') echo ' menu-is-opening menu-open'; ?>">
                        <a href="#" class="nav-link" data-widget="treeview">
                            <i class="nav-icon far fa-circle"></i><p>Laporan Database <i class="right fas fa-angle-left"></i></p>
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
                    <!-- Riwayat Cetak Laporan -->
                    <li class="nav-item">
                        <a href="<?php echo $a.'/laporan-history'; ?>" class="nav-link<?php if ($uri2 == 'laporan-history') echo ' active'; ?>">
                            <i class="nav-icon far fa-circle"></i><p>Riwayat Cetak Laporan</p>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Backup -->
            <li style="border-top:1px solid rgba(248,245,239,0.08);margin:6px 14px 0;padding-top:6px;" class="nav-item<?php if ($uri2 == 'backup') echo ' menu-is-opening menu-open'; ?>">
                <a href="#" class="nav-link" data-widget="treeview">
                    <i class="nav-icon fas fa-download"></i><p>Backup <i class="right fas fa-angle-left"></i></p>
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

            <!-- Pengaturan -->
            <li style="border-top:1px solid rgba(248,245,239,0.08);margin:6px 14px 0;padding-top:6px;" class="nav-item<?php if ($uri2 == 'settings' || $uri2 == 'developer') echo ' menu-is-opening menu-open'; ?>">
                <a href="#" class="nav-link" data-widget="treeview">
                    <i class="nav-icon fas fa-cog"></i><p>Pengaturan <i class="right fas fa-angle-left"></i></p>
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
