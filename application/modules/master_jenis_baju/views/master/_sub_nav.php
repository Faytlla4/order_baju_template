<?php
	$checkSegment = $this->uri->segment(4);
	$areaUrl = SITE_AREA . '/master/jenis_baju';
?>
<div class="float-sm-right">
    <a href="<?php echo site_url($areaUrl); ?>" id='list' class="btn btn-flat btn-<?php echo $checkSegment == '' ? 'primary' : 'default'; ?>">
        <?php echo lang('master_jenis_baju_list'); ?>
    </a>
    <?php if ($this->auth->has_permission('Master_jenis_baju.Master.Create')): ?>
    <a href="<?php echo site_url($areaUrl . '/create'); ?>" id='create_new' class="btn btn-flat btn-<?php echo $checkSegment == 'create' ? 'primary' : 'default'; ?>">
        <?php echo lang('master_jenis_baju_new'); ?>
    </a>
    <?php endif;?>
</div>
