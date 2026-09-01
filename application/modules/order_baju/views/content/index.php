<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><?php echo lang('order_baju_area_title'); ?></h3>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-2 col-sm-4">
                        <div class="form-group mb-0">
                            <label>Status</label>
                            <select id="status_order_filter" class="form-control">
                                <option value="">Semua</option>
                                <option value="Diproses">Diproses</option>
                                <option value="Diambil">Diambil</option>
                                <option value="Selesai">Selesai</option>
                            </select>
                        </div>
                    </div>
                </div>
                <table id="order_baju_table" class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Customer</th>
                            <th>Produk</th>
                            <th>Jenis</th>
                            <th>Ukuran</th>
                            <th>Warna</th>
                            <th>Jumlah</th>
                            <th>Harga</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
