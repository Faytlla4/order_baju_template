<?php
$checkSegment = $this->uri->segment(4);
$masterUrl = site_url(SITE_AREA . '/master');
?>
<ul class="nav nav-pills">
	<li class="nav-item">
		<a class="nav-link <?php echo $checkSegment == '' ? 'active' : ''; ?>" href="<?php echo $masterUrl; ?>">List Master</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo $checkSegment == 'create' ? 'active' : ''; ?>" href="<?php echo $masterUrl . '/create'; ?>">Tambah Baru</a>
	</li>
</ul>
