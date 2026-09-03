<?php
Assets::add_css('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css', 'external');
Assets::add_js('plugins/datatables/jquery.dataTables.min.js', 'external');
Assets::add_js('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js', 'external');

$filter_url = site_url(SITE_AREA . '/laporan-history/filter');

$inline_js = "
$(function() {
    var filterUrl = '{$filter_url}';
    var filterBusy = false;

    function initDataTable() {
        if ($('#tbl-data').length) {
            $('#tbl-data').DataTable({
                language: {
                    search: 'Cari:', lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
                    infoEmpty: 'Tidak ada data', zeroRecords: 'Tidak ada data yang cocok',
                    paginate: { first: 'Pertama', last: 'Terakhir', next: 'Selanjutnya', previous: 'Sebelumnya' }
                },
                pageLength: 10,
                order: [[6, 'desc']],
                columnDefs: [{ orderable: false, targets: 0 }],
                destroy: true
            });
        }
    }

    function loadData() {
        var mulai = $('#tgl_mulai').val();
        var akhir = $('#tgl_akhir').val();
        var jenis  = $('#jenis_laporan').val();
        if (filterBusy) return;
        filterBusy = true;
        $.ajax({
            url: filterUrl,
            method: 'GET',
            data: { tgl_mulai: mulai, tgl_akhir: akhir, jenis: jenis },
            dataType: 'json'
        }).done(function(res) {
            filterBusy = false;
            if (res && res.ok) {
                var \$card = $('#card-data');
                if (\$card.length) {
                    \$card.replaceWith(res.html);
                    initDataTable();
                }
            }
        }).fail(function() {
            filterBusy = false;
        });
    }

    initDataTable();

    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        loadData();
    });

    $('#btnReset').on('click', function() {
        window.location.href = '" . site_url(SITE_AREA . '/laporan-history') . "';
    });
});
";
Assets::add_js($inline_js, 'inline');
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-history text-info"></i> RIWAYAT CETAK LAPORAN</h3>
            </div>
            <div class="card-body">
                <form id="filterForm" class="form-inline">
                    <div class="form-group">
                        <label>Tanggal Mulai</label>
                        <input type="date" class="form-control" id="tgl_mulai" name="tgl_mulai" value="<?php echo html_escape($tgl_mulai); ?>">
                    </div>
                    <div class="form-group" style="margin-left:10px;">
                        <label>Tanggal Akhir</label>
                        <input type="date" class="form-control" id="tgl_akhir" name="tgl_akhir" value="<?php echo html_escape($tgl_akhir); ?>">
                    </div>
                    <div class="form-group" style="margin-left:10px;">
                        <label>Jenis Laporan</label>
                        <select class="form-control" id="jenis_laporan" name="jenis_laporan">
                            <option value="">Semua</option>
                            <option value="Dokumen" <?php echo ($jenis === 'Dokumen') ? 'selected' : ''; ?>>Dokumen</option>
                            <option value="Database" <?php echo ($jenis === 'Database') ? 'selected' : ''; ?>>Database</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" style="margin-left:10px;"><i class="fas fa-search"></i> Filter</button>
                    <a href="<?php echo site_url(SITE_AREA . '/laporan-history'); ?>" class="btn btn-secondary" style="margin-left:5px;" id="btnReset"><i class="fas fa-undo"></i> Reset</a>
                </form>
            </div>
        </div>

        <?php echo $this->load->view('reports/_data_history', array(
            'rows'      => $rows,
            'tgl_mulai' => $tgl_mulai,
            'tgl_akhir' => $tgl_akhir,
            'jenis'     => $jenis,
        ), true); ?>
    </div>
</div>
