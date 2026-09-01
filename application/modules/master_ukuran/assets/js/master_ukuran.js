$('#master_ukuran_table').bfDataTable({
    url: site_url + 'admin/master/master_ukuran/get_data',
    targetUrl: site_url + 'admin/master/master_ukuran/edit',
    filterCols: [2],
    sortCols: { created_on: 'desc', id: 'desc' },
    lengthMenu: [10, 100, 1000],
    columns: [
        { data: 'kode_order' },
        { data: 'nama_customer' },
        { data: 'nama_ukuran' },
    ],
});

// Customer terisi otomatis dari Kode Order
if ($('#kode_order_ukuran').length && $('#customer_ukuran').length) {
    $('#kode_order_ukuran').on('change keyup', function() {
        var kode = $.trim(this.value);
        var $cust = $('#customer_ukuran');
        if (!kode) { $cust.val(''); return; }
        $.post(site_url + 'admin/master/master_ukuran/lookup_customer', { kode_order: kode }, function(res) {
            $cust.val(res.found ? res.customer : '');
        }, 'json');
    });
}