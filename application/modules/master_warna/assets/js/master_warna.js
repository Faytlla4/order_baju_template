$('#master_warna_table').bfDataTable({
    url: site_url + 'admin/master/warna/get_data',
    targetUrl: site_url + 'admin/master/warna/edit/',
    filterCols: [2],
    sortCols: { created_on: 'desc', id: 'desc' },
    lengthMenu: [10, 100, 1000],
    columns: [
        { data: 'kode_order' },
        { data: 'nama_customer' },
        { data: 'nama_warna' },
        {
            data: 'id',
            orderable: false,
            render: function(data, type, row) {
                var editUrl = site_url + 'admin/master/master_warna/edit/' + data;
                var editBtn = '<a href="' + editUrl + '" class="btn btn-warning btn-xs" title="Edit"><i class="fas fa-edit"></i> Edit</a> ';
                return editBtn;
            }
        }
    ],
});

// Customer terisi otomatis dari Kode Order
if ($('#kode_order_warna').length && $('#customer_warna').length) {
    $('#kode_order_warna').on('change keyup', function() {
        var kode = $.trim(this.value);
        var $cust = $('#customer_warna');
        if (!kode) { $cust.val(''); return; }
        $.post(site_url + 'admin/master/warna/lookup_customer', { kode_order: kode }, function(res) {
            $cust.val(res.found ? res.customer : '');
        }, 'json');
    });
}