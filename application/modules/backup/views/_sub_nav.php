<?php
$checkSegment = $this->uri->segment(3);
?>
<div class="btn-group btn-group-sm">
	<a href="<?php echo site_url(SITE_AREA . '/backup/per_id'); ?>" class="btn <?php echo ($checkSegment === 'per_id') ? 'btn-primary' : 'btn-default'; ?>">
		<i class="fas fa-folder-open"></i> Backup Dokumen ID
	</a>
	<a href="<?php echo site_url(SITE_AREA . '/backup/per_folder'); ?>" class="btn <?php echo ($checkSegment === 'per_folder') ? 'btn-primary' : 'btn-default'; ?>">
		<i class="fas fa-folder"></i> Backup Dokumen Folder
	</a>
	<a href="<?php echo site_url(SITE_AREA . '/backup/database'); ?>" class="btn <?php echo ($checkSegment === 'database') ? 'btn-primary' : 'btn-default'; ?>">
		<i class="fas fa-database"></i> Backup Database
	</a>
</div>
