<?php
$seg = $this->uri->segment(4);
$baseUrl = site_url(SITE_AREA . '/reports');
?>
<ul class="nav nav-pills">
	<li class="nav-item">
		<a class="nav-link <?php echo ($seg == '' || $seg == 'index') ? 'active' : ''; ?>" href="<?php echo $baseUrl; ?>">Laporan Order</a>
	</li>
</ul>
