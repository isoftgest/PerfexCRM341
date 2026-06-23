<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper"><div class="content"><div class="panel_s"><div class="panel-body">
<?php echo form_open(current_url()); ?>
<?php echo render_input('rental_id', _l('rental_id'), isset($item) ? $item->rental_id : ''); ?>
<?php echo render_input('clientid', _l('clientid'), isset($item) ? $item->clientid : ''); ?>
<?php echo render_input('payment_month', _l('payment_month'), isset($item) ? $item->payment_month : ''); ?>
<?php echo render_input('due_date', _l('due_date'), isset($item) ? $item->due_date : ''); ?>
<?php echo render_input('amount', _l('amount'), isset($item) ? $item->amount : ''); ?>
<?php echo render_input('amount_paid', _l('amount_paid'), isset($item) ? $item->amount_paid : ''); ?>
<?php echo render_input('status', _l('status'), isset($item) ? $item->status : ''); ?>
<?php echo render_input('payment_date', _l('payment_date'), isset($item) ? $item->payment_date : ''); ?>
<?php echo render_input('payment_method', _l('payment_method'), isset($item) ? $item->payment_method : ''); ?>
<?php echo render_input('reference', _l('reference'), isset($item) ? $item->reference : ''); ?>
<?php echo render_input('notes', _l('notes'), isset($item) ? $item->notes : ''); ?>
<button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
<?php echo form_close(); ?>
</div></div></div></div><?php init_tail(); ?>
