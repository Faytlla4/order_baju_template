$('#master_jenis_baju_table').bfDataTable({
    url: site_url + 'admin/master/master_jenis_baju/get_data',
    targetUrl: site_url + 'admin/master/master_jenis_baju/edit',
    filterCols: [0, 1, 2],
    sortCols: { created_on: 'desc', id: 'desc' },
    lengthMenu: [10, 100, 1000],
    columns: [
        { data: 'nama_jenis' },
        { data: 'urutan' },
        { data: 'keterangan' },
        { data: 'status', render: function(data) { return data == 1 ? 'Aktif' : 'Non Aktif'; } },
    ],
});