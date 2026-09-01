var filterStatusOrder = { status: '' };

$('#order_baju_table').bfDataTable({
    url: site_url + 'admin/content/order_baju/get_data',
    filterCols: [0, 1, 2, 8],
    sortCols: { id: 'desc' },
    lengthMenu: [10, 100, 1000],
    params: filterStatusOrder,
    columns: [
        { data: 'kode_order', render: function(data, type, row) {
            var link = '<a href="' + site_url + 'admin/content/order_baju/edit/' + row.id + '">' + data + '</a>';
            return link + ' <button type="button" class="btn btn-xs btn-info btn-copy-kode" data-kode="' + data + '"><i class="far fa-copy"></i> Copy</button>';
        } },
        { data: 'nama_customer' },
        { data: 'produk' },
        { data: 'jenis_nama' },
        { data: 'ukuran_nama' },
        { data: 'warna_nama' },
        { data: 'jumlah', render: function(data) { return data ? data : '-'; } },
        { data: 'harga', render: function(data) { return data ? 'Rp ' + Number(data).toLocaleString('id-ID', {minimumFractionDigits: 0}) : '-'; } },
        { data: 'total_harga', render: function(data) { return data ? 'Rp ' + Number(data).toLocaleString('id-ID', {minimumFractionDigits: 0}) : '-'; } },
        { data: 'status_order' },
    ],
});

$('#status_order_filter').on('change', function() {
    filterStatusOrder.status = this.value;
    var table = $('#order_baju_table').DataTable();
    if (table) {
        table.ajax.reload();
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

$('#tanggal_order').dateDropper({
    modal: true,
    large: true
});