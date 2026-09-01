<?php
Assets::add_js('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js', 'external');
$report_excel_url = site_url(SITE_AREA . '/reports/report_excel');
$report_excel_xls_url = site_url(SITE_AREA . '/reports/report_excel/excel');
$report_excel_filter_url = site_url(SITE_AREA . '/reports/report_excel/filter');
$inline_js = "
$(function() {
    function initDataTable(selector, order) {
        if ($(selector).length) {
            $(selector).DataTable({
                language: {
                    search: 'Cari:',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
                    infoEmpty: 'Tidak ada data',
                    infoFiltered: '(disaring dari _MAX_ total data)',
                    zeroRecords: 'Tidak ada data yang cocok',
                    paginate: { first: 'Pertama', last: 'Terakhir', next: 'Selanjutnya', previous: 'Sebelumnya' }
                },
                pageLength: 10,
                order: order || [],
                columnDefs: [{ orderable: false, targets: 0 }],
                destroy: true
            });
        }
    }

    initDataTable('#tbl-transaksi');
    initDataTable('#tbl-history', [[0, 'asc']]);

    var baseUrl = '{$report_excel_url}';
    var filterUrl = '{$report_excel_filter_url}';
    var periodeEl = document.getElementById('periode');
    var rangeEl = document.getElementById('custom-range');
    var errorEl = document.getElementById('filter-error');
    var filterBusy = false;

    function showError(msg) {
        if (errorEl) {
            errorEl.textContent = msg;
            errorEl.style.display = '';
        }
    }

    function hideError() {
        if (errorEl) {
            errorEl.style.display = 'none';
        }
    }

    // Konversi DD-MM-YYYY -> YYYY-MM-DD untuk dibandingkan.
    function toIso(v) {
        var m = /^(\d{2})-(\d{2})-(\d{4})$/.exec(v || '');
        return m ? m[3] + '-' + m[2] + '-' + m[1] : '';
    }

    function applyFilter() {
        var p = document.getElementById('periode');
        if (!p) return;
        var params = { periode: p.value };
        var st = document.getElementById('status');
        if (st) params.status = st.value;
        if (p.value === 'custom') {
            var m = document.getElementById('tgl_mulai');
            var a = document.getElementById('tgl_akhir');
            var mv = m ? m.value.trim() : '';
            var av = a ? a.value.trim() : '';
            if (mv) params.tgl_mulai = mv;
            if (av) params.tgl_akhir = av;
        }
        window.location.href = baseUrl + '?' + $.param(params);
    }

    // Satu-satunya gerbang refresh untuk Custom: hanya refresh bila
    // range LENGKAP dan VALID (mulai <= akhir).
    function maybeRefresh() {
        var p = document.getElementById('periode');
        if (!p || p.value !== 'custom') return;

        var m = document.getElementById('tgl_mulai');
        var a = document.getElementById('tgl_akhir');
        var mv = m ? m.value.trim() : '';
        var av = a ? a.value.trim() : '';

        if (!mv || !av) {
            // Belum lengkap -> jangan refresh.
            hideError();
            return;
        }

        var mi = toIso(mv);
        var ai = toIso(av);
        if (!mi || !ai || mi > ai) {
            // Tidak valid -> jangan refresh, tampilkan error.
            showError('Tanggal Mulai tidak boleh setelah Tanggal Akhir.');
            return;
        }

        hideError();
        if (filterBusy) return;
        filterBusy = true;
        $.ajax({
            url: filterUrl,
            method: 'GET',
            data: { periode: 'custom', tgl_mulai: mv, tgl_akhir: av, status: (document.getElementById('status') || {}).value || '' },
            dataType: 'json'
        }).done(function(res) {
            filterBusy = false;
            if (res && res.ok) {
                var \$card = $('#card-data');
                if (\$card.length) {
                    \$card.replaceWith(res.html);
                    initDataTable('#tbl-transaksi');
                }
            } else if (res && res.error) {
                showError(res.error);
            }
        }).fail(function() {
            filterBusy = false;
            showError('Gagal memuat data. Coba lagi.');
        });
    }

    if (periodeEl) {
        periodeEl.addEventListener('change', function() {
            if (rangeEl) rangeEl.style.display = (this.value === 'custom') ? '' : 'none';
            hideError();
            if (this.value !== 'custom') {
                applyFilter();
            }
        });
    }

    var statusEl = document.getElementById('status');
    if (statusEl) {
        statusEl.addEventListener('change', function() {
            hideError();
            var p = document.getElementById('periode');
            if (p && p.value === 'custom') {
                maybeRefresh();
            } else {
                applyFilter();
            }
        });
    }

    if ($('#dp_mulai').length && $.fn.datetimepicker) {
        $('#dp_mulai').datetimepicker({ format: 'DD-MM-YYYY' });
    }
    if ($('#dp_akhir').length && $.fn.datetimepicker) {
        $('#dp_akhir').datetimepicker({ format: 'DD-MM-YYYY' });
    }

    // Satu handler bersama untuk kedua date picker: tidak refresh terpisah.
    $(document).on('change.datetimepicker', '#dp_mulai, #dp_akhir', function() {
        if (document.getElementById('periode') && document.getElementById('periode').value === 'custom') {
            maybeRefresh();
        }
    });

    // Delegasi: tombol Excel bisa diganti oleh refresh AJAX.
    $(document).on('click', '#btn-excel', function(e) {
        e.preventDefault();
        var p = document.getElementById('periode');
        if (!p) return;
        var st = document.getElementById('status');
        var params = 'periode=' + p.value;
        if (st) params += '&status=' + encodeURIComponent(st.value);
        if (p.value === 'custom') {
            var m = document.getElementById('tgl_mulai');
            var a = document.getElementById('tgl_akhir');
            params += '&tgl_mulai=' + encodeURIComponent(m ? m.value : '')
                    + '&tgl_akhir=' + encodeURIComponent(a ? a.value : '');
        }
        window.location.href = '{$report_excel_xls_url}' + '?' + params;
    });
});
";
Assets::add_js($inline_js, 'inline');
?>

<link rel="stylesheet" href="<?php echo base_url('assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css'); ?>">

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-file-excel text-success"></i> LAPORAN TRANSAKSI EXCEL</h3>
            </div>
            <div class="card-body">
                <div class="row align-items-end">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Periode</label>
                            <select name="periode" id="periode" class="form-control">
                                <option value="all" <?php echo ($periode == 'all') ? 'selected' : ''; ?>>Semua</option>
                                <option value="today" <?php echo ($periode == 'today') ? 'selected' : ''; ?>>Hari Ini</option>
                                <option value="month" <?php echo ($periode == 'month') ? 'selected' : ''; ?>>Bulan Ini</option>
                                <option value="custom" <?php echo ($periode == 'custom') ? 'selected' : ''; ?>>Custom</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="" <?php echo ($status === '' || $status === 'all') ? 'selected' : ''; ?>>Semua</option>
                                <option value="Diproses" <?php echo ($status == 'Diproses') ? 'selected' : ''; ?>>Diproses</option>
                                <option value="Diambil" <?php echo ($status == 'Diambil') ? 'selected' : ''; ?>>Diambil</option>
                                <option value="Selesai" <?php echo ($status == 'Selesai') ? 'selected' : ''; ?>>Selesai</option>
                            </select>
                        </div>
                    </div>
                    <div id="custom-range" class="col-md-5" <?php echo ($periode == 'custom') ? '' : 'style="display:none;"'; ?>>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tanggal Mulai</label>
                                    <div class="input-group date" id="dp_mulai" data-target-input="nearest">
                                        <input type="text" name="tgl_mulai" id="tgl_mulai" class="form-control datetimepicker-input" data-target="#dp_mulai" placeholder="DD-MM-YYYY" value="<?php echo html_escape($tgl_mulai ? date('d-m-Y', strtotime($tgl_mulai)) : ''); ?>" />
                                        <div class="input-group-append" data-target="#dp_mulai" data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="far fa-calendar-alt"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tanggal Akhir</label>
                                    <div class="input-group date" id="dp_akhir" data-target-input="nearest">
                                        <input type="text" name="tgl_akhir" id="tgl_akhir" class="form-control datetimepicker-input" data-target="#dp_akhir" placeholder="DD-MM-YYYY" value="<?php echo html_escape($tgl_akhir ? date('d-m-Y', strtotime($tgl_akhir)) : ''); ?>" />
                                        <div class="input-group-append" data-target="#dp_akhir" data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="far fa-calendar-alt"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex align-items-end">
                            <a href="<?php echo site_url(SITE_AREA . '/reports/report_excel'); ?>" class="btn btn-secondary"><i class="fas fa-undo"></i> Reset</a>
                        </div>
                    </div>
                </div>
                <div id="filter-error" class="alert alert-danger mt-2" style="display:none;"></div>
            </div>
        </div>

        <?php echo $this->load->view('reports/_data', array(
            'periode_label' => $periode_label,
            'rows'          => $rows,
            'grand_total'   => $grand_total,
            'status'        => $status,
        ), true); ?>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Riwayat Laporan Excel</h3>
            </div>
            <div class="card-body table-responsive pt-0">
                <?php if (empty($history_list)) : ?>
                    <div class="alert alert-info mb-0"><i class="fas fa-info-circle"></i> Tidak ada riwayat laporan Excel.</div>
                <?php else : ?>
                    <table id="tbl-history" class="table table-bordered table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width:40px">No</th>
                                <th>Dibuat Pada</th>
                                <th>Periode</th>
                                <th>Jumlah Transaksi</th>
                                <th>Total Nilai</th>
                                <th style="width:80px">Excel</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $history_no = 0; ?>
                            <?php foreach ($history_list as $h) : $history_no++; ?>
                            <tr>
                                <td><?php echo $history_no; ?></td>
                                <td><?php echo html_escape($h->dibuat_pada); ?></td>
                                <td><?php echo html_escape(ucwords($h->periode)); ?></td>
                                <td class="text-center"><?php echo (int) $h->jumlah_transaksi; ?></td>
                                <td class="text-right"><?php echo 'Rp ' . number_format((float) $h->total_nilai, 0, ',', '.'); ?></td>
                                <td>
                                    <?php if (!empty($h->nama_file)) : ?>
                                        <a href="<?php echo site_url(SITE_AREA . '/reports/report_excel/download_excel/' . (int) $h->id); ?>" class="btn btn-sm btn-outline-success" target="_blank">
                                            <i class="fas fa-file-excel"></i> Excel
                                        </a>
                                    <?php else : ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
