<?php
	$checkSegment = $this->uri->segment(4);
	$areaUrl = SITE_AREA . '/master/master_warna';
?>
<div class="float-sm-right">
    <a href="<?php echo site_url($areaUrl); ?>" id='list' class="btn btn-flat btn-<?php echo $checkSegment == '' ? 'primary' : 'default'; ?>">
        <?php echo lang('master_warna_list'); ?>
    </a>
    <?php if ($this->auth->has_permission('Master_warna.Master.Create')): ?>
    <a href="<?php echo site_url($areaUrl . '/create'); ?>" id='create_new' class="btn btn-flat btn-<?php echo $checkSegment == 'create' ? 'primary' : 'default'; ?>">
        <?php echo lang('master_warna_new'); ?>
    </a>
    <?php endif;?>
</div>