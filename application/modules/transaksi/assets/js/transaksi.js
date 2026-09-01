var filterStatusTransaksi = { status: '' };

$('#transaksi_order_table').bfDataTable({
    url: site_url + 'admin/transaksi/transaksi/get_data',
    filterCols: [0, 1, 2],
    sortCols: { id: 'desc' },
    lengthMenu: [10, 100, 1000],
    params: filterStatusTransaksi,
    columns: [
        { data: 'kode_order', render: function(data, type, row) {
            var editUrl = site_url + 'admin/transaksi/transaksi/edit/' + row.id;
            var link = '<a href="' + editUrl + '">' + data + '</a>';
            return link + ' <button type="button" class="btn btn-xs btn-warning btn-edit-transaksi" data-id="' + (parseInt(row.id, 10) || 0) + '" title="Edit Transaksi"><i class="fas fa-edit"></i> Edit</button>' +
                ' <button type="button" class="btn btn-xs btn-info btn-copy-kode" data-kode="' + data + '"><i class="far fa-copy"></i> Copy</button>' +
                ' <button type="button" class="btn btn-xs btn-primary btn-detail-transaksi" data-id="' + (parseInt(row.id, 10) || 0) + '" title="Lihat Detail"><i class="fas fa-eye"></i> Detail</button>';
        } },
        { data: 'nama_customer' },
        { data: 'produk' },
        { data: 'jenis_nama' },
        { data: 'ukuran_nama' },
        { data: 'warna_nama' },
        { data: 'jumlah' },
        { data: 'harga', render: function(data) { return data ? 'Rp ' + Number(data).toLocaleString('id-ID', {minimumFractionDigits: 0}) : '-'; } },
        { data: 'total_harga', render: function(data) { return data ? 'Rp ' + Number(data).toLocaleString('id-ID', {minimumFractionDigits: 0}) : '-'; } },
        { data: 'dokumen_count', render: function(data, type, row) {
            var count = parseInt(data, 10) || 0;
            if (!count) {
                return '<span class="text-muted">Tidak ada dokumen</span>';
            }
            return '<button type="button" class="btn btn-xs btn-secondary btn-lihat-dokumen" data-id="' + (parseInt(row.id, 10) || 0) + '" data-kode="' + $('<div>').text(row.kode_order || '').html() + '"><i class="fas fa-folder-open"></i> ' + count + ' Dokumen</button>';
        } },
        { data: 'status_transaksi' },
        { data: 'created_on', render: function(data) {
            if (!data) {
                return '-';
            }
            var parts = String(data).split(' ');
            var date = parts[0] ? parts[0].split('-') : [];
            var dateStr = date.length === 3 ? date[2] + '-' + date[1] + '-' + date[0] : parts[0];
            var timeStr = parts[1] ? parts[1].substring(0, 5) : '';
            return timeStr ? dateStr + ' ' + timeStr : dateStr;
        } },
    ],
});

$('#status_transaksi_filter').on('change', function() {
    filterStatusTransaksi.status = this.value;
    var table = $('#transaksi_order_table').DataTable();
    if (table) {
        table.ajax.reload();
    }
});

$(document).on('click', '.btn-edit-transaksi', function() {
    var id = parseInt($(this).attr('data-id') || '0', 10) || 0;
    if (id) {
        window.location.href = site_url + 'admin/transaksi/transaksi/edit/' + id;
    }
});

$(document).on('click', '.btn-copy-kode', function() {
    var kode = $(this).data('kode');
    var done = function() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1500,
                title: 'Kode order berhasil disalin: ' + kode
            });
        } else {
            alert('Kode order berhasil disalin: ' + kode);
        }
    };
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(kode).then(done);
    } else {
        var dummy = $('<input>').val(kode).appendTo('body').select();
        document.execCommand('copy');
        dummy.remove();
        done();
    }
});

// Copy kode order dari dalam Modal Detail (tanpa reload).
$(document).on('click', '.btn-copy-detail-kode', function() {
    var kode = $(this).attr('data-kode') || '';
    var done = function() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1500,
                title: 'Kode order berhasil disalin.'
            });
        } else {
            alert('Kode order berhasil disalin.');
        }
    };
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(kode).then(done);
    } else {
        var dummy = $('<input>').val(kode).appendTo('body').select();
        document.execCommand('copy');
        dummy.remove();
        done();
    }
});

// Preview total (server tetap menghitung ulang saat save)
if ($('#jumlah').length && $('#harga').length && $('#total_harga_display').length) {
    var formatRp = function(n) {
        return 'Rp ' + Number(n || 0).toLocaleString('id-ID', {minimumFractionDigits: 0});
    };
    var hitungTotal = function() {
        var jumlah = parseFloat($('#jumlah').val()) || 0;
        var harga  = parseFloat($('#harga').val()) || 0;
        $('#total_harga_display').val(formatRp(jumlah * harga));
    };
    $('#jumlah, #harga').on('input change keyup', hitungTotal);
}

// --- Dynamic Dokumen Upload ---
$(document).on('click', '#btn-tambah-dokumen', function() {
    var tpl = '<div class="dokumen-row mb-2" style="display:flex;gap:8px;align-items:center;">' +
        '<input type="file" name="dokumen[]" class="form-control-file dokumen-input" style="flex:1;" ' +
        'accept=".pdf,.png,.jpg,.jpeg,.jfif,.gif,.doc,.docx,.xls,.xlsx" />' +
        '<button type="button" class="btn btn-danger btn-sm btn-hapus-dokumen" title="Hapus"><i class="fas fa-times"></i></button>' +
        '</div>';
    $('#dokumen-list').append(tpl);
});

$(document).on('click', '.btn-hapus-dokumen', function() {
    $(this).closest('.dokumen-row').remove();
});

$(document).on('click', '.btn-lihat-dokumen', function() {
    var transId = parseInt($(this).attr('data-id') || '0', 10) || 0;
    var $body = $('#dokumenModalBody');

    $body.html('<p class="text-muted mb-0"><i class="fas fa-spinner fa-spin"></i> Memuat dokumen...</p>');
    $('#dokumenModal').modal('show');

    if (!transId) {
        $body.html('<p class="text-muted mb-0">Transaksi tidak valid.</p>');
        return;
    }

    $.getJSON(site_url + 'admin/transaksi/transaksi/get_dokumen_list/' + transId)
        .done(function(res) {
            if (!res || !res.ok) {
                $body.html('<p class="text-muted mb-0">' + (res && res.error ? $('<div>').text(res.error).html() : 'Gagal memuat dokumen.') + '</p>');
                return;
            }
            renderDokumenModal($body, res);
        })
        .fail(function() {
            $body.html('<p class="text-muted mb-0">Gagal memuat dokumen. Silakan coba lagi.</p>');
        });
});

$(document).on('click', '.btn-detail-transaksi', function() {
    var transId = parseInt($(this).attr('data-id') || '0', 10) || 0;
    var $body = $('#detailTransaksiBody');

    $('#detailTransaksiModal').modal('show');
    $body.html('<p class="text-muted mb-0"><i class="fas fa-spinner fa-spin"></i> Memuat detail...</p>');

    if (!transId) {
        $body.html('<p class="text-muted mb-0">Transaksi tidak valid.</p>');
        return;
    }

    $.getJSON(site_url + 'admin/transaksi/transaksi/detail/' + transId)
        .done(function(res) {
            if (!res || !res.ok) {
                $body.html('<p class="text-muted mb-0">' + (res && res.error ? $('<div>').text(res.error).html() : 'Gagal memuat detail.') + '</p>');
                return;
            }
            renderDetailTransaksi($body, res.detail);
        })
        .fail(function() {
            $body.html('<p class="text-muted mb-0">Gagal memuat detail. Silakan coba lagi.</p>');
        });
});

function renderDetailTransaksi($body, d) {
    if (!d) {
        $body.html('<p class="text-muted mb-0">Detail tidak tersedia.</p>');
        return;
    }

    var esc = function(s) {
        return $('<div>').text(s === null || s === undefined ? '' : s).html();
    };
    var formatRp = function(n) {
        return (n !== null && n !== '' && n !== undefined) ? 'Rp ' + Number(n).toLocaleString('id-ID', {minimumFractionDigits: 0}) : '-';
    };

    var html = '';
    html += '<table class="table table-sm table-bordered mb-3">';
    html += '<tr><th style="width:30%">Kode Order</th><td>' + esc(d.kode_order) +
        ' <button type="button" class="btn btn-xs btn-info btn-copy-detail-kode" data-kode="' + esc(d.kode_order).replace(/"/g, '&quot;') + '" title="Salin Kode Order"><i class="far fa-copy"></i> Copy</button></td></tr>';
    html += '<tr><th>Customer</th><td>' + esc(d.nama_customer) + '</td></tr>';
    html += '<tr><th>Produk</th><td>' + esc(d.produk) + '</td></tr>';
    html += '<tr><th>Jenis Baju</th><td>' + esc(d.jenis_nama) + '</td></tr>';
    html += '<tr><th>Ukuran</th><td>' + esc(d.ukuran_nama) + '</td></tr>';
    html += '<tr><th>Warna</th><td>' + esc(d.warna_nama) + '</td></tr>';
    html += '<tr><th>Jumlah</th><td>' + esc(d.jumlah) + '</td></tr>';
    html += '<tr><th>Harga</th><td>' + formatRp(d.harga) + '</td></tr>';
    html += '<tr><th>Total</th><td>' + formatRp(d.total_harga) + '</td></tr>';
    html += '<tr><th>Status</th><td>' + esc(d.status_transaksi) + '</td></tr>';
    html += '<tr><th>Tanggal</th><td>' + esc(d.tanggal) + '</td></tr>';
    html += '</table>';

    html += '<h6 class="font-weight-bold">Dokumen</h6>';
    var files = d.dokumen || [];
    if (!files.length) {
        html += '<p class="text-muted mb-0">Tidak ada dokumen.</p>';
    } else {
        html += '<div class="dokumen-list" style="max-height:40vh;overflow-y:auto;">';
        for (var i = 0; i < files.length; i++) {
            html += renderDokumenItem(files[i], i + 1);
        }
        html += '</div>';
    }
    $body.html(html);
}

function renderDokumenModal($body, res) {
    var files = res.files || [];
    var html = '';
    if (res.kode) {
        html += '<p class="mb-2"><strong>Kode Order:</strong> ' + $('<div>').text(res.kode).html() + '</p>';
    }
    if (!files.length) {
        html += '<p class="text-muted mb-0">Tidak ada dokumen.</p>';
    } else {
        html += '<div class="dokumen-list" style="max-height:60vh;overflow-y:auto;">';
        for (var i = 0; i < files.length; i++) {
            html += renderDokumenItem(files[i], i + 1);
        }
        html += '</div>';
    }
    $body.html(html);
}

function renderDokumenItem(f, no) {
    var safeName = $('<div>').text(f.nama).html();
    var sizeLabel = f.ukuran ? formatBytes(f.ukuran) : '';
    var info = $('<div>').text(f.tipe || '').html() + (sizeLabel ? ' &mdash; ' + sizeLabel : '');
    var ext = (f.ext || '').toLowerCase();
    var previewable = ['jpg', 'jpeg', 'jfif', 'png', 'gif', 'webp', 'pdf'].indexOf(ext) >= 0;

    if (!f.exists) {
        return '<div class="dokumen-item mb-2 border rounded p-2">' +
            '<div class="d-flex align-items-center justify-content-between">' +
                '<div><strong>' + no + '. ' + safeName + '</strong><br><span class="text-muted">' + info + '</span></div>' +
                '<span class="text-danger"><i class="fas fa-exclamation-circle"></i> File tidak ditemukan</span>' +
            '</div></div>';
    }

    var viewBtn = '';
    var downloadBtn = '<a href="' + f.download_url + '" class="btn btn-sm btn-outline-secondary"><i class="fas fa-download"></i> Download</a>';
    var preview = '';

    if (previewable) {
        viewBtn = '<a href="' + f.view_url + '" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i> Lihat</a>';
        if (ext === 'pdf') {
            preview = '<div class="mt-2"><iframe src="' + f.view_url + '" style="width:100%;height:320px;border:1px solid #dee2e6;border-radius:4px;" loading="lazy"></iframe></div>';
        } else {
            preview = '<div class="mt-2 text-center"><img src="' + f.view_url + '" alt="' + safeName + '" style="max-height:260px;max-width:100%;object-fit:contain;border:1px solid #dee2e6;border-radius:4px;" loading="lazy" /></div>';
        }
    } else {
        preview = '<div class="mt-2 alert alert-light border small mb-0"><i class="far fa-file-alt"></i> Format tidak didukung preview di browser. Gunakan tombol Download.</div>';
    }

    return '<div class="dokumen-item mb-2 border rounded p-2">' +
        '<div class="d-flex align-items-center justify-content-between">' +
            '<div><strong>' + no + '. ' + safeName + '</strong><br><span class="text-muted">' + info + '</span></div>' +
            '<div class="d-flex" style="gap:6px;">' + viewBtn + downloadBtn + '</div>' +
        '</div>' + preview +
    '</div>';
}

function formatBytes(bytes) {
    if (!bytes || bytes <= 0) return '';
    var units = ['B', 'KB', 'MB', 'GB'];
    var i = 0;
    var v = bytes;
    while (v >= 1024 && i < units.length - 1) { v /= 1024; i++; }
    return v.toFixed(v >= 10 || i === 0 ? 0 : 1) + ' ' + units[i];
}
