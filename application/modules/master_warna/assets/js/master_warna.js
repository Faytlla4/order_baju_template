$('#master_warna_table').bfDataTable({
    url: site_url + 'admin/master/master_warna/get_data',
    targetUrl: site_url + 'admin/master/master_warna/edit',
    filterCols: [2],
    sortCols: { created_on: 'desc', id: 'desc' },
    lengthMenu: [10, 100, 1000],
    columns: [
        { data: 'kode_order' },
        { data: 'nama_customer' },
        { data: 'nama_warna' },
    ],
});

// Customer terisi otomatis dari Kode Order
if ($('#kode_order_warna').length && $('#customer_warna').length) {
    $('#kode_order_warna').on('change keyup', function() {
        var kode = $.trim(this.value);
        var $cust = $('#customer_warna');
        if (!kode) { $cust.val(''); return; }
        $.post(site_url + 'admin/master/master_warna/lookup_customer', { kode_order: kode }, function(res) {
            $cust.val(res.found ? res.customer : '');
        }, 'json');
    });
}