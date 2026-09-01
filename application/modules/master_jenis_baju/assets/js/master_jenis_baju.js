$('#master_jenis_baju_table').bfDataTable({
    url: site_url + 'admin/master/jenis_baju/get_data',
    targetUrl: site_url + 'admin/master/jenis_baju/edit/',
    filterCols: [0, 1, 2],
    sortCols: { created_on: 'desc', id: 'desc' },
    lengthMenu: [10, 100, 1000],
    columns: [
        { data: 'nama_jenis' },
        { data: 'urutan' },
        { data: 'keterangan' },
        { data: 'status', render: function(data) { return data == 1 ? 'Aktif' : 'Non Aktif'; } },
        {
            data: 'id',
            orderable: false,
            render: function(data, type, row) {
                var editUrl = site_url + 'admin/master/master_jenis_baju/edit/' + data;
                var editBtn = '<a href="' + editUrl + '" class="btn btn-warning btn-xs" title="Edit"><i class="fas fa-edit"></i> Edit</a> ';
                return editBtn;
            }
        }
    ],
});