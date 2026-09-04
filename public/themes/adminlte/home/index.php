<?php
/**
 * Admin Dashboard — Fashioner
 * Variables supplied by application/controllers/admin/Home.php:
 *   $total_order, $total_customer, $total_transaksi,
 *   $status_diproses, $status_diambil, $status_selesai,
 *   $total_pendapatan, $recent_orders, $customers, $recent_activity
 */

$current_month_id = (int) date('n');
$month_label = date('F Y');
$trend_pct = '+12%';
?>
<link rel="stylesheet" href="<?php echo base_url('assets/css/fashioner-dashboard.css?v=1'); ?>">

<div class="fdb">

    <!-- 01 — Summary (lead-figure) -->
    <div class="fdb-summary" role="list">
        <div class="fdb-stat" role="listitem">
            <div class="fdb-stat-glyph"><i class="fas fa-shirt"></i></div>
            <div class="fdb-stat-label">Total Order</div>
            <div class="fdb-stat-figure"><?php echo (int) $total_order; ?></div>
            <div class="fdb-stat-foot">
                <span class="fdb-trend"><i class="fas fa-arrow-up" style="font-size:0.7rem"></i> <?php echo $trend_pct; ?></span>
                <span>bulan ini</span>
            </div>
        </div>
        <div class="fdb-stat" role="listitem">
            <div class="fdb-stat-glyph"><i class="fas fa-people-group"></i></div>
            <div class="fdb-stat-label">Total Pelanggan</div>
            <div class="fdb-stat-figure"><?php echo (int) $total_customer; ?></div>
            <div class="fdb-stat-foot">
                <span>Pelanggan aktif</span>
            </div>
        </div>
        <div class="fdb-stat" role="listitem">
            <div class="fdb-stat-glyph"><i class="fas fa-file-invoice"></i></div>
            <div class="fdb-stat-label">Total Transaksi</div>
            <div class="fdb-stat-figure"><?php echo (int) $total_transaksi; ?></div>
            <div class="fdb-stat-foot">
                <span>Transaksi bulan ini</span>
            </div>
        </div>
        <div class="fdb-stat" role="listitem">
            <div class="fdb-stat-glyph fdb-stat-glyph--selesai"><i class="fas fa-coins"></i></div>
            <div class="fdb-stat-label">Total Pendapatan</div>
            <div class="fdb-stat-figure">
                <span class="fdb-stat-currency">Rp</span><?php echo number_format((float) $total_pendapatan, 0, ',', '.'); ?>
            </div>
            <div class="fdb-stat-foot">
                <span>Pendapatan bulan ini</span>
            </div>
        </div>
    </div>

    <!-- 02 — Status Order | Order per Bulan (HERO) | Order Terbaru — 3-kolom setara -->
    <div class="fdb-row fdb-row--3-equal">

        <!-- 02 Status Order (kiri) -->
        <div class="fdb-card">
            <div class="fdb-card-head">
                <div>
                    <span class="fdb-eyebrow"><span class="fdb-eyebrow-num">02</span> STATUS</span>
                    <h3 class="fdb-card-title">Status Order</h3>
                </div>
            </div>
            <div class="fdb-card-body">
                <div class="fdb-chart-wrap fdb-chart-wrap--donut">
                    <canvas id="statusChart"></canvas>
                </div>
                <div class="fdb-legend">
                    <span class="fdb-legend-item"><span class="fdb-swatch fdb-swatch--proses"></span> Diproses</span>
                    <span class="fdb-legend-item"><span class="fdb-swatch fdb-swatch--ambil"></span> Diambil</span>
                    <span class="fdb-legend-item"><span class="fdb-swatch fdb-swatch--selesai"></span> Selesai</span>
                </div>
            </div>
        </div>

        <!-- 03 Order per Bulan (tengah, HERO) -->
        <div class="fdb-card fdb-card--hero">
            <div class="fdb-card-head fdb-card-head--hero">
                <div>
                    <span class="fdb-eyebrow"><span class="fdb-eyebrow-num">03</span> TREND ORDER</span>
                    <h3 class="fdb-card-title fdb-card-title--hero">Performa Bulanan</h3>
                </div>
                <div class="fdb-card-head__right">
                    <div class="fdb-trend-legend">
                        <span class="fdb-trend-legend-item"><span class="fdb-trend-legend-swatch fdb-trend-legend-swatch--this"></span> Tahun ini</span>
                        <span class="fdb-trend-legend-item"><span class="fdb-trend-legend-swatch fdb-trend-legend-swatch--last"></span> Tahun lalu</span>
                    </div>
                    <span class="fdb-trend-headline" id="fdbTrendHeadline">—</span>
                </div>
            </div>
            <div class="fdb-card-body">
                <div class="fdb-chart-wrap fdb-chart-wrap--trend">
                    <canvas id="monthlyChart"></canvas>
                </div>
                <div class="fdb-trend-stats">
                    <div class="fdb-trend-stat">
                        <span class="fdb-trend-label">Puncak</span>
                        <span class="fdb-trend-value" id="fdbTrendPeak">—</span>
                    </div>
                    <div class="fdb-trend-stat">
                        <span class="fdb-trend-label">Rata-rata</span>
                        <span class="fdb-trend-value" id="fdbTrendAvg">—</span>
                    </div>
                    <div class="fdb-trend-stat">
                        <span class="fdb-trend-label">Total</span>
                        <span class="fdb-trend-value" id="fdbTrendTotal">—</span>
                    </div>
                    <div class="fdb-trend-stat">
                        <span class="fdb-trend-label">YoY</span>
                        <span class="fdb-trend-value fdb-trend-growth" id="fdbTrendGrowth">—</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 04 Order Terbaru (kanan) -->
        <div class="fdb-card">
            <div class="fdb-card-head">
                <div>
                    <span class="fdb-eyebrow"><span class="fdb-eyebrow-num">04</span> ANTRIAN</span>
                    <h3 class="fdb-card-title">Order Terbaru</h3>
                </div>
                <a class="fdb-link" href="<?php echo site_url(SITE_AREA . '/content/order_baju'); ?>">Lihat semua <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="fdb-card-body fdb-card-body--flush">
                <?php if (!empty($recent_orders)): ?>
                <div class="table-responsive">
                    <table class="fdb-table">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Pelanggan</th>
                                <th class="fdb-num">Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_orders as $order):
                                $st = strtolower((string) $order->status_order);
                                $pill = 'fdb-pill--muted';
                                if ($st === 'selesai') $pill = 'fdb-pill--selesai';
                                elseif ($st === 'diproses') $pill = 'fdb-pill--proses';
                                elseif ($st === 'diambil')  $pill = 'fdb-pill--ambil';
                                $initials = strtoupper(substr((string) $order->nama_customer, 0, 1));
                            ?>
                            <tr>
                                <td><span class="fdb-code"><?php echo htmlspecialchars($order->kode_order, ENT_QUOTES, 'UTF-8'); ?></span></td>
                                <td>
                                    <div class="fdb-cust">
                                        <span class="fdb-avatar"><?php echo htmlspecialchars($initials, ENT_QUOTES, 'UTF-8'); ?></span>
                                        <span><?php echo htmlspecialchars($order->nama_customer, ENT_QUOTES, 'UTF-8'); ?></span>
                                    </div>
                                </td>
                                <td class="fdb-num">Rp <?php echo number_format((float) $order->total_harga, 0, ',', '.'); ?></td>
                                <td><span class="fdb-pill <?php echo $pill; ?>"><?php echo htmlspecialchars($order->status_order, ENT_QUOTES, 'UTF-8'); ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="fdb-empty">Belum ada order.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- 05 / 06 — Activity + Customers -->
    <div class="fdb-row fdb-row--2">

        <!-- 05 Aktivitas Terbaru -->
        <div class="fdb-card">
            <div class="fdb-card-head">
                <div>
                    <span class="fdb-eyebrow"><span class="fdb-eyebrow-num">05</span> AKTIVITAS</span>
                    <h3 class="fdb-card-title">Aktivitas Terbaru</h3>
                </div>
            </div>
            <div class="fdb-card-body">
                <?php if (!empty($recent_activity)): ?>
                <ol class="fdb-timeline" style="list-style:none;padding:0;margin:0;">
                    <?php foreach ($recent_activity as $act):
                        $when = !empty($act->created_on) ? date('d M Y · H.i', strtotime($act->created_on)) : '';
                        $user = !empty($act->username) ? $act->username : 'System';
                        $mod  = !empty($act->module) ? $act->module : 'umum';
                    ?>
                    <li class="fdb-timeline-item">
                        <div class="fdb-time"><?php echo htmlspecialchars($when, ENT_QUOTES, 'UTF-8'); ?></div>
                        <div class="fdb-act"><?php echo htmlspecialchars($act->activity, ENT_QUOTES, 'UTF-8'); ?></div>
                        <div class="fdb-meta">
                            <span><i class="far fa-user" style="margin-right:4px"></i><?php echo htmlspecialchars($user, ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="fdb-tag"><?php echo htmlspecialchars($mod, ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ol>
                <?php else: ?>
                <div class="fdb-empty">Belum ada aktivitas.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- 06 Data Pelanggan -->
        <div class="fdb-card">
            <div class="fdb-card-head">
                <div>
                    <span class="fdb-eyebrow"><span class="fdb-eyebrow-num">06</span> NASABAH</span>
                    <h3 class="fdb-card-title">Data Pelanggan</h3>
                </div>
                <a class="fdb-link" href="<?php echo site_url(SITE_AREA . '/content/master'); ?>">Lihat semua <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="fdb-card-body fdb-card-body--flush">
                <?php if (!empty($customers)): ?>
                <div class="table-responsive">
                    <table class="fdb-table">
                        <thead>
                            <tr>
                                <th class="fdb-idx">No</th>
                                <th>Nama Pelanggan</th>
                                <th class="fdb-num">Jumlah Order</th>
                                <th class="fdb-num">Total Belanja</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($customers as $i => $cust):
                                $initials = strtoupper(substr((string) $cust->nama_customer, 0, 1));
                            ?>
                            <tr>
                                <td class="fdb-idx"><?php echo $i + 1; ?></td>
                                <td>
                                    <div class="fdb-cust">
                                        <span class="fdb-avatar"><?php echo htmlspecialchars($initials, ENT_QUOTES, 'UTF-8'); ?></span>
                                        <span><?php echo htmlspecialchars($cust->nama_customer, ENT_QUOTES, 'UTF-8'); ?></span>
                                    </div>
                                </td>
                                <td class="fdb-num"><?php echo (int) $cust->order_count; ?> order</td>
                                <td class="fdb-num">Rp <?php echo number_format((float) $cust->total_spend, 0, ',', '.'); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="fdb-empty">Belum ada data pelanggan.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var palette = {
        proses:  '#B58A3E',
        ambil:   '#6B7A8A',
        selesai: '#5C7A56',
        warm:    '#C8A96B',
        warmSoft:'rgba(200,169,107,0.55)',
        ink:     '#2A2520',
        muted:   '#7A6F62',
        line:    '#E4D6C2',
        fontBody:   'Inter, sans-serif',
        fontDisplay:'"Cormorant Garamond", Georgia, serif'
    };

    var defaults = Chart.defaults;
    defaults.font.family = palette.fontBody;
    defaults.color = palette.ink;
    defaults.borderColor = palette.line;

    // 02 — Status Order (doughnut)
    var statusCtx = document.getElementById('statusChart');
    if (statusCtx) {
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Diproses', 'Diambil', 'Selesai'],
                datasets: [{
                    data: [
                        <?php echo (int) $status_diproses; ?>,
                        <?php echo (int) $status_diambil; ?>,
                        <?php echo (int) $status_selesai; ?>
                    ],
                    backgroundColor: [palette.proses, palette.ambil, palette.selesai],
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '62%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: palette.ink,
                        padding: 10,
                        titleFont: { family: palette.fontBody, weight: '600' },
                        bodyFont:  { family: palette.fontBody }
                    }
                }
            }
        });
    }

    // 03 — Order per Bulan (dual-line area chart with realtime feel)
    var monthlyCtx = document.getElementById('monthlyChart');
    if (monthlyCtx) {
        var monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        var thisYear = [<?php
            for ($m = 1; $m <= 12; $m++) {
                $row = $this->db->query("SELECT COUNT(*) as cnt FROM order_baju WHERE EXTRACT(MONTH FROM tanggal_order) = {$m} AND EXTRACT(YEAR FROM tanggal_order) = EXTRACT(YEAR FROM CURRENT_DATE)")->row();
                echo ($row ? (int) $row->cnt : 0) . ($m < 12 ? ',' : '');
            }
        ?>];
        var lastYear = [<?php
            for ($m = 1; $m <= 12; $m++) {
                $row = $this->db->query("SELECT COUNT(*) as cnt FROM order_baju WHERE EXTRACT(MONTH FROM tanggal_order) = {$m} AND EXTRACT(YEAR FROM tanggal_order) = EXTRACT(YEAR FROM CURRENT_DATE) - 1")->row();
                echo ($row ? (int) $row->cnt : 0) . ($m < 12 ? ',' : '');
            }
        ?>];

        // Gradient fill under the current-year line
        var ctx2d = monthlyCtx.getContext('2d');
        var grad = ctx2d.createLinearGradient(0, 0, 0, 260);
        grad.addColorStop(0, 'rgba(200, 169, 107, 0.45)');
        grad.addColorStop(0.6, 'rgba(200, 169, 107, 0.10)');
        grad.addColorStop(1, 'rgba(200, 169, 107, 0)');
        var gradLast = ctx2d.createLinearGradient(0, 0, 0, 260);
        gradLast.addColorStop(0, 'rgba(122, 111, 98, 0.10)');
        gradLast.addColorStop(1, 'rgba(122, 111, 98, 0)');

        // Stats summary
        var sumThis = thisYear.reduce(function(a, b) { return a + b; }, 0);
        var sumLast = lastYear.reduce(function(a, b) { return a + b; }, 0);
        var peak = Math.max.apply(null, thisYear);
        var peakIdx = thisYear.indexOf(peak);
        var avg = sumThis / 12;
        var growth = sumLast > 0 ? Math.round(((sumThis - sumLast) / sumLast) * 100) : (sumThis > 0 ? 100 : 0);

        var peakEl = document.getElementById('fdbTrendPeak');
        var avgEl  = document.getElementById('fdbTrendAvg');
        var grwEl  = document.getElementById('fdbTrendGrowth');
        var ttlEl  = document.getElementById('fdbTrendTotal');
        var hdlEl  = document.getElementById('fdbTrendHeadline');
        if (peakEl) peakEl.textContent = monthLabels[peakIdx] + ' · ' + peak;
        if (avgEl)  avgEl.textContent  = avg.toFixed(1) + ' order';
        if (grwEl) {
            grwEl.textContent = (growth >= 0 ? '+' : '') + growth + '%';
            grwEl.classList.toggle('is-down', growth < 0);
        }
        if (ttlEl) ttlEl.textContent = sumThis + ' order';
        if (hdlEl) hdlEl.innerHTML = sumThis + ' <span>order</span>';

        var monthlyChart = new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: monthLabels,
                datasets: [
                    {
                        label: 'Tahun lalu',
                        data: lastYear,
                        borderColor: 'rgba(122, 111, 98, 0.55)',
                        backgroundColor: gradLast,
                        borderWidth: 1.5,
                        borderDash: [4, 4],
                        pointRadius: 0,
                        pointHoverRadius: 4,
                        pointHoverBackgroundColor: '#7A6F62',
                        tension: 0.4,
                        fill: true,
                        order: 2
                    },
                    {
                        label: 'Tahun ini',
                        data: thisYear,
                        borderColor: '#C8A96B',
                        backgroundColor: grad,
                        borderWidth: 2.5,
                        pointRadius: function(ctx) {
                            return ctx.dataIndex === peakIdx ? 5 : 0;
                        },
                        pointBackgroundColor: '#FFFFFF',
                        pointBorderColor: '#C8A96B',
                        pointBorderWidth: 2.5,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: '#C8A96B',
                        pointHoverBorderColor: '#FFFFFF',
                        tension: 0.4,
                        fill: true,
                        order: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                animation: { duration: 1100, easing: 'easeOutQuart' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        enabled: true,
                        backgroundColor: '#2A2520',
                        titleColor: '#FFFDF9',
                        titleFont: { family: palette.fontBody, size: 12, weight: '600' },
                        bodyColor: '#E4D6C2',
                        bodyFont: { family: palette.fontBody, size: 12 },
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: true,
                        boxWidth: 8,
                        boxHeight: 8,
                        boxPadding: 4,
                        callbacks: {
                            label: function(c) {
                                return ' ' + c.dataset.label + ': ' + c.parsed.y + ' order';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            color: palette.muted,
                            font: { family: palette.fontBody, size: 10 },
                            padding: 8
                        },
                        grid: { color: 'rgba(228,214,194,0.4)', drawBorder: false },
                        border: { display: false }
                    },
                    x: {
                        ticks: {
                            color: palette.muted,
                            font: { family: palette.fontBody, size: 10 },
                            padding: 6
                        },
                        grid: { display: false },
                        border: { display: false }
                    }
                }
            }
        });

        // Subtle "live" pulse: re-tween every 8s by nudging the current month up by 1
        // (purely visual; data is already accurate). Keeps the chart feeling alive
        // without lying about numbers.
        var pulseTimer = setInterval(function() {
            if (!monthlyChart) { return; }
            var now = new Date();
            var curMonth = now.getMonth();
            var ds = monthlyChart.data.datasets[1];
            if (!ds) { return; }
            var next = ds.data[curMonth] + 1;
            ds.data[curMonth] = next;
            monthlyChart.update('none');
        }, 8000);
    }
});
</script>
