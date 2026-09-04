<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="small-box" style="background:linear-gradient(135deg,#FFFDF9,#EFE7D8);border-left:4px solid #C8A96B;">
            <div class="inner">
                <h3 style="color:#403A34;"><?php echo $total_order; ?></h3>
                <p style="color:#8C8175;">Total Order</p>
            </div>
            <div class="icon"><i class="fas fa-tshirt" style="color:#C8A96B;"></i></div>
            <a href="<?php echo site_url(SITE_AREA.'/content/order_baju'); ?>" class="small-box-footer" style="color:#8A6A47;">Lihat detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="small-box" style="background:linear-gradient(135deg,#FFFDF9,#EFE7D8);border-left:4px solid #8A6A47;">
            <div class="inner">
                <h3 style="color:#403A34;"><?php echo $total_customer; ?></h3>
                <p style="color:#8C8175;">Total Pelanggan</p>
            </div>
            <div class="icon"><i class="fas fa-users" style="color:#8A6A47;"></i></div>
            <a href="#customer-table" class="small-box-footer" style="color:#8A6A47;">Lihat detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="small-box" style="background:linear-gradient(135deg,#FFFDF9,#EFE7D8);border-left:4px solid #403A34;">
            <div class="inner">
                <h3 style="color:#403A34;"><?php echo $total_transaksi; ?></h3>
                <p style="color:#8C8175;">Total Transaksi</p>
            </div>
            <div class="icon"><i class="fas fa-file-invoice" style="color:#403A34;"></i></div>
            <a href="<?php echo site_url(SITE_AREA.'/transaksi/transaksi'); ?>" class="small-box-footer" style="color:#8A6A47;">Lihat detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="small-box" style="background:linear-gradient(135deg,#FFFDF9,#EFE7D8);border-left:4px solid #C8A96B;">
            <div class="inner">
                <h3 style="color:#403A34;">Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></h3>
                <p style="color:#8C8175;">Total Pendapatan</p>
            </div>
            <div class="icon"><i class="fas fa-money-bill-wave" style="color:#C8A96B;"></i></div>
        </div>
    </div>
</div>

<!-- Charts Row: Donut + Bar -->
<div class="row mb-4">
    <div class="col-lg-5 col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title" style="font-size:0.95rem;font-weight:600;">Status Order</h3>
            </div>
            <div class="card-body" style="text-align:center;">
                <canvas id="statusChart" height="220"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-7 col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title" style="font-size:0.95rem;font-weight:600;">Order Per Bulan</h3>
            </div>
            <div class="card-body">
                <canvas id="monthlyChart" height="220"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders + Activity Timeline -->
<div class="row mb-4">
    <div class="col-lg-6 col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title" style="font-size:0.95rem;font-weight:600;">Order Terbaru</h3>
            </div>
            <div class="card-body" style="padding:0;">
                <?php if (!empty($recent_orders)): ?>
                <div class="table-responsive">
                    <table class="table table-sm mb-0" style="font-size:0.82rem;">
                        <thead>
                            <tr>
                                <th style="font-weight:600;">Kode</th>
                                <th style="font-weight:600;">Pelanggan</th>
                                <th style="font-weight:600;">Total</th>
                                <th style="font-weight:600;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_orders as $order): ?>
                            <tr>
                                <td><?php echo $order->kode_order; ?></td>
                                <td><?php echo $order->nama_customer; ?></td>
                                <td>Rp <?php echo number_format($order->total_harga, 0, ',', '.'); ?></td>
                                <td>
                                    <?php
                                    $badge_class = 'badge-secondary';
                                    if ($order->status_order == 'Selesai') $badge_class = 'badge-success';
                                    elseif ($order->status_order == 'Diproses') $badge_class = 'badge-warning';
                                    elseif ($order->status_order == 'Diambil') $badge_class = 'badge-primary';
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?>"><?php echo $order->status_order; ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted" style="padding:16px;text-align:center;font-size:0.82rem;">Belum ada order.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title" style="font-size:0.95rem;font-weight:600;">Aktivitas Terbaru</h3>
            </div>
            <div class="card-body" style="padding:12px 20px;">
                <?php if (!empty($recent_activity)): ?>
                <div class="timeline" style="font-size:0.82rem;">
                    <?php foreach ($recent_activity as $act): ?>
                    <div class="timeline-item" style="margin-bottom:14px;position:relative;padding-left:20px;border-left:2px solid #E4D6C2;">
                        <div class="time" style="color:#8C8175;font-size:0.75rem;margin-bottom:2px;">
                            <i class="fas fa-clock" style="margin-right:4px;"></i><?php echo date('d M Y H:i', strtotime($act->created_on)); ?>
                        </div>
                        <div class="timeline-header" style="color:#403A34;font-weight:500;">
                            <?php echo $act->activity; ?>
                        </div>
                        <div style="color:#8C8175;font-size:0.75rem;margin-top:2px;">
                            <i class="fas fa-user" style="margin-right:3px;"></i><?php echo $act->username ? $act->username : 'System'; ?>
                            <span style="margin-left:6px;padding:1px 6px;background:rgba(200,169,107,0.12);border-radius:3px;color:#8A6A47;"><?php echo $act->module; ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p class="text-muted" style="padding:16px;text-align:center;font-size:0.82rem;">Belum ada aktivitas.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Customer Data -->
<div class="row">
    <div class="col-12">
        <div class="card" id="customer-table">
            <div class="card-header">
                <h3 class="card-title" style="font-size:0.95rem;font-weight:600;">Data Pelanggan</h3>
            </div>
            <div class="card-body" style="padding:0;">
                <?php if (!empty($customers)): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="font-weight:600;">No</th>
                                <th style="font-weight:600;">Nama Pelanggan</th>
                                <th style="font-weight:600;">Jumlah Order</th>
                                <th style="font-weight:600;">Total Belanja</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($customers as $i => $cust): ?>
                            <tr>
                                <td><?php echo $i + 1; ?></td>
                                <td><?php echo $cust->nama_customer; ?></td>
                                <td><?php echo $cust->order_count; ?> order</td>
                                <td>Rp <?php echo number_format($cust->total_spend, 0, ',', '.'); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted" style="padding:16px;text-align:center;font-size:0.82rem;">Belum ada data pelanggan.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Status Order Doughnut Chart
    var statusCtx = document.getElementById('statusChart');
    if (statusCtx) {
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Diproses', 'Diambil', 'Selesai'],
                datasets: [{
                    data: [<?php echo $status_diproses; ?>, <?php echo $status_diambil; ?>, <?php echo $status_selesai; ?>],
                    backgroundColor: ['#C8A96B', '#8A6A47', '#403A34'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { font: { family: 'Inter', size: 12 }, padding: 16 } }
                },
                cutout: '65%'
            }
        });
    }

    // Monthly Order Bar Chart
    var monthlyCtx = document.getElementById('monthlyChart');
    if (monthlyCtx) {
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Order',
                    data: [<?php
                        for ($m = 1; $m <= 12; $m++) {
                            $row = $this->db->query("SELECT COUNT(*) as cnt FROM order_baju WHERE EXTRACT(MONTH FROM tanggal_order) = {$m}")->row();
                            echo ($row ? $row->cnt : '0') . ($m < 12 ? ',' : '');
                        }
                    ?>],
                    backgroundColor: 'rgba(200,169,107,0.6)',
                    borderColor: '#C8A96B',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { font: { family: 'Inter', size: 11 }, stepSize: 1 } },
                    x: { ticks: { font: { family: 'Inter', size: 11 } } }
                }
            }
        });
    }
});
</script>
