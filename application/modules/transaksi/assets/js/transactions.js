function trxFormatRupiah(angka) {
    return 'Rp' + Number(angka || 0).toLocaleString('id-ID');
}

function trxEscape(value) {
    return $('<div>')
        .text(
            value == null
                ? ''
                : String(value)
        )
        .html();
}

$(function () {

    'use strict';

    var table =
        $('#transactions_table')
            .DataTable({

                ordering: true,

                /*
                 * Search must remain enabled because the application
                 * provides a custom invoice/customer search field.
                 */
                searching: true,

                /*
                 * Hide DataTables' default search input because the UI
                 * already provides #trx-search.
                 */
                dom: 'rtip',

                paging: true,

                info: true,

                pageLength: 10,

                lengthMenu: [
                    10,
                    25,
                    50,
                    100
                ],

                language: {
                    emptyTable:
                        'Belum ada data.',

                    zeroRecords:
                        'Tidak ada data yang cocok.'
                },

                columnDefs: [
                    {
                        orderable: false,
                        targets: [6]
                    }
                ]
            });


    /*
     * ------------------------------------------------------------
     * FILTER
     * ------------------------------------------------------------
     */

    function currentFilters() {

        return {

            start:
                $('#trx-filter-start')
                    .val()
                    || '',

            end:
                $('#trx-filter-end')
                    .val()
                    || '',

            payment:
                $('#trx-filter-payment')
                    .val()
                    || '',

            order:
                $('#trx-filter-order')
                    .val()
                    || ''
        };
    }


    function validateFilters(filters) {

        if (
            filters.start
            &&
            filters.end
            &&
            filters.start > filters.end
        ) {

            Swal.fire(
                'Peringatan',
                'Tanggal mulai tidak boleh melebihi tanggal akhir.',
                'warning'
            );

            return false;
        }

        return true;
    }


    function applyFilters() {

        var filters =
            currentFilters();

        if (
            ! validateFilters(
                filters
            )
        ) {
            return false;
        }

        /*
         * DataTables custom filter.
         *
         * The date/status values are stored on each <tr> as data-*
         * attributes, so no additional backend request is required.
         */
        $.fn.dataTable.ext.search.push(
            function (
                settings,
                data,
                dataIndex
            ) {

                if (
                    settings.nTable
                    !==
                    $('#transactions_table')[0]
                ) {
                    return true;
                }

                var row =
                    table
                        .row(
                            dataIndex
                        )
                        .nodes()
                        .to$();

                var date =
                    String(
                        row.attr(
                            'data-date'
                        )
                        || ''
                    );

                var payment =
                    String(
                        row.attr(
                            'data-payment-status'
                        )
                        || ''
                    );

                var order =
                    String(
                        row.attr(
                            'data-order-status'
                        )
                        || ''
                    );

                if (
                    filters.start
                    &&
                    (
                        ! date
                        ||
                        date < filters.start
                    )
                ) {
                    return false;
                }

                if (
                    filters.end
                    &&
                    (
                        ! date
                        ||
                        date > filters.end
                    )
                ) {
                    return false;
                }

                if (
                    filters.payment
                    &&
                    payment
                    !==
                    filters.payment
                ) {
                    return false;
                }

                if (
                    filters.order
                    &&
                    order
                    !==
                    filters.order
                ) {
                    return false;
                }

                return true;
            }
        );

        table.draw();

        /*
         * Remove only the filter we just added.
         */
        $.fn.dataTable.ext.search.pop();

        return true;
    }


    /*
     * Search invoice/customer.
     */
    $('#trx-search').on(
        'keyup',
        function () {

            table
                .search(
                    this.value
                )
                .draw();
        }
    );


    /*
     * Explicit Apply button.
     */
    $('#btn-apply-trx-filter').on(
        'click',
        function () {

            applyFilters();
        }
    );


    /*
     * ------------------------------------------------------------
     * DETAIL
     * ------------------------------------------------------------
     */

    $('#transactions_table').on(
        'click',
        '.btn-detail',
        function () {

            var row =
                $(this)
                    .closest('tr');

            var id =
                row.data('id');

            var items =
                TRX_DETAILS[id]
                || [];


            $('#detail-invoice')
                .text(
                    row.data(
                        'invoice'
                    )
                );


            $('#detail-customer')
                .text(
                    row.data(
                        'customer'
                    )
                );


            $('#detail-date')
                .text(
                    row.data(
                        'date'
                    )
                );


            $('#detail-method')
                .text(
                    row.data(
                        'payment-method'
                    )
                    || '-'
                );


            $('#detail-paystatus')
                .html(
                    row
                        .find(
                            '.badge-payment'
                        )
                        .prop(
                            'outerHTML'
                        )
                );


            $('#detail-orderstatus')
                .html(
                    row
                        .find(
                            '.badge-order'
                        )
                        .prop(
                            'outerHTML'
                        )
                );


            var rows = '';


            items.forEach(
                function (item) {

                    rows +=
                        '<tr>'

                        + '<td>'
                        + trxEscape(
                            item.product_name
                        )
                        + '</td>'

                        + '<td>'
                        + trxEscape(
                            item.size
                            || '-'
                        )
                        + '</td>'

                        + '<td>'
                        + trxFormatRupiah(
                            item.price
                        )
                        + '</td>'

                        + '<td>'
                        + trxEscape(
                            item.quantity
                        )
                        + '</td>'

                        + '<td>'
                        + trxFormatRupiah(
                            item.subtotal
                        )
                        + '</td>'

                        + '</tr>';
                }
            );


            if (! rows) {

                rows =
                    '<tr>'

                    + '<td '
                    + 'colspan="5" '
                    + 'class="text-center '
                    + 'text-muted">'
                    + 'Tidak ada item.'
                    + '</td>'

                    + '</tr>';
            }


            $('#detail-items')
                .html(
                    rows
                );


            $('#detail-total')
                .text(
                    trxFormatRupiah(
                        row.data(
                            'total'
                        )
                    )
                );


            $('#modal-detail')
                .modal(
                    'show'
                );
        }
    );


    /*
     * ------------------------------------------------------------
     * EDIT STATUS
     * ------------------------------------------------------------
     */

    $('#transactions_table').on(
        'click',
        '.btn-edit-status',
        function () {

            var row =
                $(this)
                    .closest('tr');


            $('#es-id')
                .val(
                    row.data(
                        'id'
                    )
                );


            $('#es-invoice')
                .text(
                    row.data(
                        'invoice'
                    )
                );


            $('#es-payment_status')
                .val(
                    row.data(
                        'payment-status'
                    )
                )
                .trigger(
                    'change'
                );


            $('#es-order_status')
                .val(
                    row.data(
                        'order-status'
                    )
                )
                .trigger(
                    'change'
                );


            $('#modal-edit-status')
                .modal(
                    'show'
                );
        }
    );


    /*
     * Save transaction status.
     */
    $('.btn-save-status').on(
        'click',
        function (e) {

            e.preventDefault();

            var button =
                $(this);

            var id =
                $('#es-id')
                    .val();

            var payment =
                $('#es-payment_status')
                    .val();

            var order =
                $('#es-order_status')
                    .val();


            if (! id) {

                Swal.fire(
                    'Gagal',
                    'ID transaksi tidak valid.',
                    'error'
                );

                return;
            }


            button.prop(
                'disabled',
                true
            );


            $.ajax({

                url:
                    site_url
                    +
                    'admin/transaksi/transactions/update_status',

                type:
                    'POST',

                dataType:
                    'json',

                data: {

                    id:
                        id,

                    payment_status:
                        payment,

                    order_status:
                        order
                }

            })
            .done(
                function (response) {

                    if (
                        response.ok
                    ) {

                        $('#modal-edit-status')
                            .modal(
                                'hide'
                            );

                        Swal.fire(
                            'Berhasil',
                            response.message,
                            'success'
                        )
                        .then(
                            function () {

                                location.reload();
                            }
                        );

                    } else {

                        Swal.fire(
                            'Gagal',
                            response.message
                            ||
                            'Status gagal diperbarui.',
                            'error'
                        );
                    }
                }
            )
            .fail(
                function (xhr) {

                    Swal.fire(
                        'Error',

                        xhr.responseJSON
                        &&
                        xhr.responseJSON.message
                            ?
                            xhr.responseJSON.message
                            :
                            'Server error saat memperbarui status.',

                        'error'
                    );
                }
            )
            .always(
                function () {

                    button.prop(
                        'disabled',
                        false
                    );
                }
            );
        }
    );


    /*
     * ------------------------------------------------------------
     * CANCEL TRANSACTION
     * ------------------------------------------------------------
     */

    $('#transactions_table').on(
        'click',
        '.btn-cancel-order',
        function () {

            var row =
                $(this)
                    .closest('tr');

            var id =
                row.data(
                    'id'
                );

            var invoice =
                row.data(
                    'invoice'
                );


            Swal.fire({

                title:
                    'Batalkan Transaksi?',

                text:
                    'Invoice "'
                    +
                    invoice
                    +
                    '" akan dibatalkan dan stok dikembalikan.',

                icon:
                    'warning',

                showCancelButton:
                    true,

                confirmButtonColor:
                    '#d33',

                confirmButtonText:
                    'Ya, Batalkan',

                cancelButtonText:
                    'Kembali'

            })
            .then(
                function (result) {

                    if (
                        ! result.isConfirmed
                    ) {
                        return;
                    }


                    $.ajax({

                        url:
                            site_url
                            +
                            'admin/transaksi/transactions/cancel',

                        type:
                            'POST',

                        dataType:
                            'json',

                        data: {
                            id:
                                id
                        }

                    })
                    .done(
                        function (response) {

                            if (
                                response.ok
                            ) {

                                Swal.fire(
                                    'Berhasil',
                                    response.message,
                                    'success'
                                )
                                .then(
                                    function () {

                                        location.reload();
                                    }
                                );

                            } else {

                                Swal.fire(
                                    'Gagal',
                                    response.message
                                    ||
                                    'Pembatalan gagal.',
                                    'error'
                                );
                            }
                        }
                    )
                    .fail(
                        function (xhr) {

                            Swal.fire(
                                'Error',

                                xhr.responseJSON
                                &&
                                xhr.responseJSON.message
                                    ?
                                    xhr.responseJSON.message
                                    :
                                    'Server error saat membatalkan transaksi.',

                                'error'
                            );
                        }
                    );
                }
            );
        }
    );


    /*
     * ------------------------------------------------------------
     * PRODUCT INPUT
     * ------------------------------------------------------------
     */

    $('#pick_product').on(
        'change',
        function () {

            var opt =
                $(this)
                    .find(
                        ':selected'
                    );


            $('#pick_size')
                .val(
                    opt.data(
                        'size'
                    )
                    || ''
                );


            $('#pick_price')
                .val(
                    opt.data(
                        'price'
                    )
                    || ''
                );
        }
    );


    /*
     * Add product detail row.
     */
    $('#btn-add-product').on(
        'click',
        function () {

            var opt =
                $('#pick_product')
                    .find(
                        ':selected'
                    );


            var productId =
                opt.val()
                || '';


            var name =
                opt.data(
                    'name'
                )
                ||
                opt.text()
                    .trim();


            var size =
                $('#pick_size')
                    .val()
                || '';


            var price =
                parseFloat(
                    $('#pick_price')
                        .val()
                )
                || 0;


            var qty =
                parseInt(
                    $('#pick_qty')
                        .val(),
                    10
                )
                || 0;


            if (
                ! productId
                ||
                ! name
                ||
                qty < 1
                ||
                price < 0
            ) {

                Swal.fire(
                    'Peringatan',
                    'Pilih produk dan isi data produk dengan benar.',
                    'warning'
                );

                return;
            }


            $('#details-empty-row')
                .remove();


            var subtotal =
                price
                *
                qty;


            var html =

                '<tr '
                +
                'class="detail-row" '
                +
                'data-price="'
                +
                price
                +
                '" '
                +
                'data-qty="'
                +
                qty
                +
                '">'

                +

                '<td>'

                +

                '<input '
                +
                'type="hidden" '
                +
                'name="product_id[]" '
                +
                'value="'
                +
                trxEscape(
                    productId
                )
                +
                '">'

                +

                trxEscape(
                    name
                )

                +

                '<input '
                +
                'type="hidden" '
                +
                'name="product_name[]" '
                +
                'value="'
                +
                trxEscape(
                    name
                )
                +
                '">'

                +

                '</td>'

                +

                '<td>'

                +

                '<input '
                +
                'type="text" '
                +
                'name="size[]" '
                +
                'value="'
                +
                trxEscape(
                    size
                )
                +
                '" '
                +
                'class="form-control '
                +
                'form-control-sm '
                +
                'detail-size" '
                +
                'maxlength="10">'

                +

                '</td>'

                +

                '<td>'

                +

                '<input '
                +
                'type="number" '
                +
                'name="price[]" '
                +
                'value="'
                +
                price
                +
                '" '
                +
                'min="0" '
                +
                'step="1000" '
                +
                'class="form-control '
                +
                'form-control-sm '
                +
                'detail-price">'

                +

                '</td>'

                +

                '<td>'

                +

                '<input '
                +
                'type="number" '
                +
                'name="quantity[]" '
                +
                'value="'
                +
                qty
                +
                '" '
                +
                'min="1" '
                +
                'class="form-control '
                +
                'form-control-sm '
                +
                'detail-qty">'

                +

                '</td>'

                +

                '<td '
                +
                'class="cell-subtotal '
                +
                'text-right">'

                +
                trxFormatRupiah(
                    subtotal
                )

                +

                '</td>'

                +

                '<td '
                +
                'class="text-center">'

                +

                '<button '
                +
                'type="button" '
                +
                'class="btn btn-danger '
                +
                'btn-xs '
                +
                'btn-remove-product">'

                +

                '<i '
                +
                'class="fas fa-trash">'
                +
                '</i>'

                +

                '</button>'

                +

                '</td>'

                +

                '</tr>';


            $('#details_body')
                .append(
                    html
                );


            trxRecalcSummary();


            $('#pick_product')
                .val('')
                .trigger(
                    'change'
                );


            $('#pick_size')
                .val('');


            $('#pick_price')
                .val('');


            $('#pick_qty')
                .val(1);
        }
    );


    /*
     * Remove detail row.
     */
    $('#details_body').on(
        'click',
        '.btn-remove-product',
        function () {

            $(this)
                .closest('tr')
                .remove();


            if (
                $('.detail-row')
                    .length === 0
            ) {

                $('#details_body')
                    .html(

                        '<tr '
                        +
                        'id="details-empty-row">'
                        +

                        '<td '
                        +
                        'colspan="6" '
                        +
                        'class="text-center '
                        +
                        'text-muted">'
                        +

                        'Belum ada produk.'

                        +

                        '</td>'

                        +
                        '</tr>'
                    );
            }


            trxRecalcSummary();
        }
    );


    /*
     * Recalculate detail subtotal.
     */
    $('#details_body').on(
        'input change',
        '.detail-price, .detail-qty, .detail-size',
        function () {

            var tr =
                $(this)
                    .closest('tr');


            var price =
                parseFloat(
                    tr.find(
                        '.detail-price'
                    ).val()
                )
                || 0;


            var qty =
                parseInt(
                    tr.find(
                        '.detail-qty'
                    ).val(),
                    10
                )
                || 0;


            tr.attr(
                'data-price',
                price
            );


            tr.attr(
                'data-qty',
                qty
            );


            tr.find(
                '.cell-subtotal'
            )
            .text(
                trxFormatRupiah(
                    price * qty
                )
            );


            trxRecalcSummary();
        }
    );


    $('#discount').on(
        'input change',
        function () {

            trxRecalcSummary();
        }
    );


    function trxRecalcSummary() {

        var subtotal = 0;


        $('.detail-row').each(
            function () {

                subtotal +=

                    (
                        parseFloat(
                            $(this)
                                .attr(
                                    'data-price'
                                )
                        )
                        ||
                        0
                    )

                    *

                    (
                        parseInt(
                            $(this)
                                .attr(
                                    'data-qty'
                                ),
                            10
                        )
                        ||
                        0
                    );
            }
        );


        var discount =
            Math.max(
                0,
                parseFloat(
                    $('#discount')
                        .val()
                )
                ||
                0
            );


        discount =
            Math.min(
                discount,
                subtotal
            );


        $('#summary-subtotal')
            .text(
                trxFormatRupiah(
                    subtotal
                )
            );


        $('#summary-discount')
            .text(
                trxFormatRupiah(
                    discount
                )
            );


        $('#summary-total')
            .text(
                trxFormatRupiah(
                    subtotal - discount
                )
            );
    }


    /*
     * ------------------------------------------------------------
     * CREATE TRANSACTION
     * ------------------------------------------------------------
     */

    $('#form-transaksi').on(
        'submit',
        function (e) {

            e.preventDefault();

            var form =
                this;


            if (
                ! form.checkValidity()
            ) {

                form.reportValidity();

                return;
            }


            if (
                $('.detail-row')
                    .length === 0
            ) {

                Swal.fire(
                    'Peringatan',
                    'Tambahkan minimal satu produk.',
                    'warning'
                );

                return;
            }


            var submit =
                $('.btn-save-trx');


            submit.prop(
                'disabled',
                true
            );


            $.ajax({

                url:
                    site_url
                    +
                    'admin/transaksi/transactions/store',

                type:
                    'POST',

                data:
                    $(form).serialize(),

                dataType:
                    'json'

            })
            .done(
                function (response) {

                    if (
                        response.ok
                    ) {

                        Swal.fire(
                            'Berhasil',
                            response.message,
                            'success'
                        )
                        .then(
                            function () {

                                window.location =
                                    response.redirect
                                    ||
                                    (
                                        site_url
                                        +
                                        'admin/transaksi/transactions'
                                    );
                            }
                        );

                    } else {

                        Swal.fire(
                            'Gagal',
                            response.message
                            ||
                            'Transaksi gagal disimpan.',
                            'error'
                        );
                    }
                }
            )
            .fail(
                function (xhr) {

                    Swal.fire(
                        'Error',

                        xhr.responseJSON
                        &&
                        xhr.responseJSON.message
                            ?
                            xhr.responseJSON.message
                            :
                            'Server error saat menyimpan transaksi.',

                        'error'
                    );
                }
            )
            .always(
                function () {

                    submit.prop(
                        'disabled',
                        false
                    );
                }
            );
        }
    );


    /*
     * Date picker compatibility.
     */
    if (
        $.fn.dateDropper
    ) {

        $('#transaction_date')
            .dateDropper({

                modal:
                    true,

                large:
                    true,

                format:
                    'Y-m-d'
            });
    }


    /*
     * Initial summary.
     */
    trxRecalcSummary();

});
