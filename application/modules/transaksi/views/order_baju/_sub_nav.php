<?php
	$active_tab = isset($active_tab) ? $active_tab : 'daftar';
	$baseUrl = SITE_AREA . '/transaksi/transaksi';
?>
<div class="float-sm-right">
    <a href="<?php echo site_url($baseUrl); ?>" id="tab-daftar" class="btn btn-flat btn-<?php echo $active_tab === 'daftar' ? 'primary' : 'default'; ?>" data-tab="daftar">
        Daftar Transaksi
    </a>
    <a href="<?php echo site_url($baseUrl . '?tab=proses'); ?>" id="tab-proses" class="btn btn-flat btn-<?php echo $active_tab === 'proses' ? 'primary' : 'default'; ?>" data-tab="proses">
        Proses Transaksi
    </a>
</div>
