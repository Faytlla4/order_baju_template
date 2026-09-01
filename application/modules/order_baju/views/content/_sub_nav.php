<?php
	$checkSegment = $this->uri->segment(4);
	$areaUrl = SITE_AREA . '/content/order_baju';
?>
<div class="float-sm-right">
    <a href="<?php echo site_url($areaUrl); ?>" id='list' class="btn btn-flat btn-<?php echo $checkSegment == '' ? 'primary' : 'default'; ?>">
        <?php echo lang('order_baju_list'); ?>
    </a>
</div>
