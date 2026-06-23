<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper"><div class="content"><div class="panel_s"><div class="panel-body">
<?php echo form_open(current_url()); ?>
<?php echo render_input('rental_id', _l('rental_id'), isset($item) ? $item->rental_id : ''); ?>
<?php echo render_input('clientid', _l('clientid'), isset($item) ? $item->clientid : ''); ?>
<?php echo render_input('amount', _l('amount'), isset($item) ? $item->amount : ''); ?>
<?php echo render_input('deposit_date', _l('deposit_date'), isset($item) ? $item->deposit_date : ''); ?>
<?php echo render_input('status', _l('status'), isset($item) ? $item->status : ''); ?>
<?php echo render_input('returned_amount', _l('returned_amount'), isset($item) ? $item->returned_amount : ''); ?>
<?php echo render_input('returned_date', _l('returned_date'), isset($item) ? $item->returned_date : ''); ?>
<?php echo render_input('notes', _l('notes'), isset($item) ? $item->notes : ''); ?>
<button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
<?php echo form_close(); ?>
</div></div></div></div><?php init_tail(); ?>
