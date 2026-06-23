<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper"><div class="content"><div class="panel_s"><div class="panel-body">
<?php echo form_open(current_url()); ?>
<?php echo render_input('property_id', _l('property_id'), isset($item) ? $item->property_id : ''); ?>
<?php echo render_input('type', _l('type'), isset($item) ? $item->type : ''); ?>
<?php echo render_input('name', _l('name'), isset($item) ? $item->name : ''); ?>
<?php echo render_input('reference', _l('reference'), isset($item) ? $item->reference : ''); ?>
<?php echo render_input('description', _l('description'), isset($item) ? $item->description : ''); ?>
<?php echo render_input('default_price', _l('default_price'), isset($item) ? $item->default_price : ''); ?>
<?php echo render_input('active', _l('active'), isset($item) ? $item->active : ''); ?>
<button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
<?php echo form_close(); ?>
</div></div></div></div><?php init_tail(); ?>
