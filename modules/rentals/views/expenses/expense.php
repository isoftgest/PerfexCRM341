<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper"><div class="content"><div class="panel_s"><div class="panel-body">
<?php echo form_open(current_url()); ?>
<?php echo render_input('property_id', _l('property_id'), isset($item) ? $item->property_id : ''); ?>
<?php echo render_input('expense_date', _l('expense_date'), isset($item) ? $item->expense_date : ''); ?>
<?php echo render_input('concept', _l('concept'), isset($item) ? $item->concept : ''); ?>
<?php echo render_input('amount', _l('amount'), isset($item) ? $item->amount : ''); ?>
<?php echo render_input('supplier', _l('supplier'), isset($item) ? $item->supplier : ''); ?>
<?php echo render_input('reference', _l('reference'), isset($item) ? $item->reference : ''); ?>
<?php echo render_input('notes', _l('notes'), isset($item) ? $item->notes : ''); ?>
<button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
<?php echo form_close(); ?>
</div></div></div></div><?php init_tail(); ?>
