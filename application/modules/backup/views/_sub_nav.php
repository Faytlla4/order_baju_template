<?php
$areaUrl = SITE_AREA . '/backup';
$checkSegment = $this->uri->segment(4);
?>
<div class="float-sm-right">
	<a href="<?php echo site_url($areaUrl); ?>" class="btn btn-sm <?php echo ($checkSegment === '' || $checkSegment === null) ? 'btn-primary' : 'btn-default'; ?>">
		<i class="fas fa-database"></i> Backup
	</a>
</div>
