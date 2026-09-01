$(function () {
    'use strict';

    function rupiah(value) {
        return 'Rp' + Number(value || 0).toLocaleString('id-ID');
    }

    var table = $('#products_table').DataTable({
        ordering: true,
        searching: false,
        paging: true,
        info: true,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        language: {
            emptyTable: 'Belum ada data.',
            zeroRecords: 'Tidak ada data yang cocok.'
        },
        columnDefs: [{ orderable: false, targets: [0, 9] }]
    });

    $('#prd-search').on('keyup', function () {
        table.search(this.value).draw();
    });

    $('#prd-filter-category').on('change', function () {
        table.column(3).search(this.value).draw();
    });

    $('#prd-filter-status').on('change', function () {
        table.column(8).search(this.value).draw();
    });

    $('.custom-file-input').on('change', function () {
        var fileName = this.files && this.files.length ? this.files[0].name : 'Pilih File';
        $(this).next('.custom-file-label').text(fileName);
    });

    $('#products_table').on('click', '.btn-detail', function () {
        var d = $(this).data();
        $('#detail-code').text(d.code);
        $('#detail-name').text(d.name);
        $('#detail-category').text(d.category);
        $('#detail-brand').text(d.brand || '-');
        $('#detail-size').text(d.size || '-');
        $('#detail-price').text(rupiah(d.price));
        $('#detail-stock').text(Number(d.stock || 0) > 0 ? d.stock : 'Habis');
        $('#detail-status').html(d.status === 'Aktif'
            ? '<span class="badge badge-success">Aktif</span>'
            : '<span class="badge badge-secondary">Nonaktif</span>');
        $('#detail-description').text(d.description || '-');
        if (d.image) {
            $('#detail-image').attr('src', base_url + d.image).show();
            $('#detail-image-placeholder').find('i').hide();
        } else {
            $('#detail-image').hide().attr('src', '');
            $('#detail-image-placeholder').find('i').show();
        }
        $('#modal-detail').modal('show');
    });

    $('#products_table').on('click', '.btn-edit', function () {
        var d = $(this).data();
        $('#edit-id').val(d.id);
        $('#edit-code').val(d.code);
        $('#edit-name').val(d.name);
        $('#edit-category_id').val(d.categoryId || d.category).trigger('change');
        $('#edit-brand').val(d.brand);
        $('#edit-size').val(d.size);
        $('#edit-price').val(d.price);
        $('#edit-stock').val(d.stock);
        $('#edit-description').val(d.description);
        $('#edit-status').val(d.status).trigger('change');
        $('#modal-edit').modal('show');
    });

    function showResponseError(xhr) {
        var message = 'Server error.';
        if (xhr.responseJSON && xhr.responseJSON.message) {
            message = xhr.responseJSON.message;
        }
        Swal.fire('Error', message, 'error');
    }

    function api(action, data) {
        $.ajax({
            url: site_url + 'admin/master/products/' + action,
            type: 'POST',
            data: data,
            dataType: 'json',
            processData: !(data instanceof FormData),
            contentType: data instanceof FormData ? false : 'application/x-www-form-urlencoded; charset=UTF-8',
            success: function (r) {
                if (r.ok) {
                    Swal.fire('Berhasil', r.message, 'success').then(function () {
                        location.reload();
                    });
                } else {
                    Swal.fire('Gagal', r.message || 'Operasi gagal.', 'error');
                }
            },
            error: showResponseError
        });
    }

    $('#products_table').on('click', '.btn-delete', function () {
        var id = $(this).data('id');
        var name = $(this).data('name');
        Swal.fire({
            title: 'Hapus Produk?',
            text: 'Produk "' + name + '" akan dihapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then(function (result) {
            if (result.isConfirmed) {
                api('delete', { id: id });
            }
        });
    });

    $('#products_table').on('click', '.btn-toggle-status', function () {
        api('toggle', { id: $(this).closest('tr').data('id') });
    });

    $('#form-create').on('submit', function (e) {
        e.preventDefault();
        if (!this.checkValidity()) {
            this.reportValidity();
            return;
        }
        api('create', new FormData(this));
    });

    $('#form-edit').on('submit', function (e) {
        e.preventDefault();
        if (!this.checkValidity()) {
            this.reportValidity();
            return;
        }
        api('update', new FormData(this));
    });
});
