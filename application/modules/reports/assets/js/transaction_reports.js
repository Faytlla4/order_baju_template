$(function () {
    'use strict';

    $('#reports_table').DataTable({
        ordering: true,
        paging: true,
        info: true,
        pageLength: 10,
        language: {
            emptyTable: 'Belum ada laporan.',
            zeroRecords: 'Tidak ada data yang cocok.'
        }
    });

    function filters() {
        return {
            date_from: $('#report-date-from').val(),
            date_to:   $('#report-date-to').val(),
            status:    $('#report-status').val()
        };
    }

    function qs(data) {
        return Object.keys(data)
            .filter(function (key) { return data[key]; })
            .map(function (key) {
                return encodeURIComponent(key) + '=' + encodeURIComponent(data[key]);
            })
            .join('&');
    }

    function validateFilters(data) {
        if (data.date_from && data.date_to && data.date_from > data.date_to) {
            Swal.fire('Peringatan', 'Tanggal awal tidak boleh melebihi tanggal akhir.', 'warning');
            return false;
        }
        return true;
    }

    /* Common AJAX export handler — calls endpoint, then redirects to download URL */
    function doExport(endpoint, label) {
        var data = filters();
        if (!validateFilters(data)) {
            return;
        }

        Swal.fire({
            title: 'Membuat ' + label + '…',
            text: 'Mohon tunggu sebentar.',
            allowOutsideClick: false,
            didOpen: function () { Swal.showLoading(); }
        });

        $.ajax({
            url:      site_url + 'admin/reports/transaction_reports/' + endpoint,
            type:     'POST',
            data:     data,
            dataType: 'json',
            success: function (response) {
                Swal.close();
                if (response.ok) {
                    window.location = response.url;
                } else {
                    Swal.fire('Gagal', response.message || 'Export gagal.', 'error');
                }
            },
            error: function (xhr) {
                Swal.close();
                var msg = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : 'Export gagal. Cek log server.';
                Swal.fire('Error', msg, 'error');
            }
        });
    }

    $('#btn-show-report').on('click', function () {
        var data = filters();
        if (!validateFilters(data)) { return; }
        var query = qs(data);
        window.location = site_url + 'admin/reports/transaction_reports' + (query ? '?' + query : '');
    });

    /* Export Excel → POST /export/excel → download .xlsx */
    $('#btn-export-excel').on('click', function () {
        doExport('exportExcel', 'Excel');
    });

    /* Export PDF → POST /export/pdf → download .pdf */
    $('#btn-export-pdf').on('click', function () {
        doExport('exportPdf', 'PDF');
    });
});
