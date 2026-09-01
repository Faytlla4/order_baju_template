<?php
$seg = $this->uri->segment(4);
$baseUrl = site_url(SITE_AREA . '/transaksi');
?>
<ul class="nav nav-pills">
	<li class="nav-item">
		<a class="nav-link <?php echo $seg == '' ? 'active' : ''; ?>" href="<?php echo $baseUrl; ?>">Daftar Order</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo $seg == 'create' ? 'active' : ''; ?>" href="<?php echo $baseUrl . '/create'; ?>">Tambah Order</a>
	</li>
</ul>
