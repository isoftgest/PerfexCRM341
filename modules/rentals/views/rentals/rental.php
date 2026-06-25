<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper"><div class="content"><div class="panel_s"><div class="panel-body">
<?php echo form_open(current_url()); ?>
<?php echo render_input('contract_reference', _l('contract_reference'), isset($item) ? $item->contract_reference : ''); ?>
<?php echo render_input('clientid', _l('clientid'), isset($item) ? $item->clientid : ''); ?>
<?php echo render_input('property_id', _l('property_id'), isset($item) ? $item->property_id : ''); ?>
<?php echo render_input('unit_id', _l('unit_id'), isset($item) ? $item->unit_id : ''); ?>
<?php echo render_input('start_date', _l('start_date'), isset($item) ? $item->start_date : ''); ?>
<?php echo render_input('end_date', _l('end_date'), isset($item) ? $item->end_date : ''); ?>
<?php echo render_input('monthly_price', _l('monthly_price'), isset($item) ? $item->monthly_price : ''); ?>
<?php echo render_input('status', _l('status'), isset($item) ? $item->status : ''); ?>
<?php echo render_input('notes', _l('notes'), isset($item) ? $item->notes : ''); ?>
<button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
<?php echo form_close(); ?>
</div></div></div></div><?php init_tail(); ?>
