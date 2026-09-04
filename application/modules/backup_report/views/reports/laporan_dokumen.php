<?php
Assets::add_css('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css', 'external');
Assets::add_js('plugins/datatables/jquery.dataTables.min.js', 'external');
Assets::add_js('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js', 'external');

$page_type   = isset($page_type) ? $page_type : 'pdf';
$site_url    = site_url();
$filter_url  = site_url(SITE_AREA . '/laporan-dokumen/filter');
$export_url  = ($page_type === 'excel')
    ? site_url(SITE_AREA . '/laporan-dokumen/cetak-excel')
    : site_url(SITE_AREA . '/laporan-dokumen/cetak-pdf');
$export_id   = ($page_type === 'excel') ? 'btn-excel' : 'btn-pdf';
$reset_url   = site_url(SITE_AREA . '/laporan-dokumen/' . $page_type);

$inline_js = "
$(function() {
    var filterUrl = '{$filter_url}';
    var exportUrl = '{$export_url}';
    var exportId  = '{$export_id}';
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
                order: [[1, 'desc']],
                columnDefs: [{ orderable: false, targets: 0 }],
                destroy: true
            });
        }
    }

    function loadData() {
        var mulai = $('#tgl_mulai').val();
        var akhir = $('#tgl_akhir').val();
        if (filterBusy) return;
        filterBusy = true;
        $.ajax({
            url: filterUrl,
            method: 'GET',
            data: { tgl_mulai: mulai, tgl_akhir: akhir },
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
        window.location.href = '{$reset_url}';
    });

    $(document).on('click', '#' + exportId, function(e) {
        e.preventDefault();
        var mulai = $('#tgl_mulai').val();
        var akhir = $('#tgl_akhir').val();
        window.location.href = exportUrl + '?tgl_mulai=' + encodeURIComponent(mulai) + '&tgl_akhir=' + encodeURIComponent(akhir);
    });
});
";
Assets::add_js($inline_js, 'inline');
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-file-pdf text-danger"></i> LAPORAN DOKUMEN</h3>
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
                    <button type="submit" class="btn btn-primary" style="margin-left:10px;"><i class="fas fa-search"></i> Filter</button>
                    <a href="<?php echo $reset_url; ?>" class="btn btn-secondary" style="margin-left:5px;"><i class="fas fa-undo"></i> Reset</a>
                </form>
            </div>
        </div>

        <?php echo $this->load->view('reports/_data_dokumen', array(
            'rows'        => $rows,
            'tgl_mulai'   => $tgl_mulai,
            'tgl_akhir'   => $tgl_akhir,
            'show_pdf'    => ($page_type === 'pdf'),
            'show_excel'  => ($page_type === 'excel'),
            'export_url'  => $export_url . '?tgl_mulai=' . rawurlencode($tgl_mulai) . '&tgl_akhir=' . rawurlencode($tgl_akhir),
        ), true); ?>
    </div>
</div>
