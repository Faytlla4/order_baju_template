$('#transaksi_order_table').bfDataTable({
    url: site_url + 'admin/transaksi/order_baju/get_data',
    targetUrl: site_url + 'admin/transaksi/order_baju/create',
    filterCols: [0, 1, 2, 8],
    sortCols: { id: 'desc' },
    lengthMenu: [10, 100, 1000],
    columns: [
        { data: 'kode_order', render: function(data) {
            return '<span>' + data + '</span> <button type="button" class="btn btn-xs btn-info btn-copy-kode" data-kode="' + data + '"><i class="far fa-copy"></i> Copy</button>';
        } },
        { data: 'nama_customer' },
        { data: 'produk' },
        { data: 'jenis_nama' },
        { data: 'ukuran_nama' },
        { data: 'warna_nama' },
        { data: 'jumlah' },
        { data: 'total_harga', render: function(data) { return 'Rp ' + Number(data).toLocaleString('id-ID', {minimumFractionDigits: 0}); } },
        { data: 'status_order' },
        { data: 'tanggal_order' },
    ],
});

$('#jumlah, #sel_harga').on('input', function() {
    var jumlah = parseFloat($('#jumlah').val()) || 0;
    var harga = parseFloat($('#sel_harga').val()) || 0;
    var total = jumlah * harga;
    $('#total_harga_display').val('Rp ' + total.toLocaleString('id-ID', {minimumFractionDigits: 0}));
    $('#total_harga').val(total.toFixed(2));
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