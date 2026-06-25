<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper"><div class="content"><div class="panel_s"><div class="panel-body">
<?php echo form_open(current_url()); ?>
<?php echo render_input('name', _l('name'), isset($item) ? $item->name : ''); ?>
<?php echo render_input('reference', _l('reference'), isset($item) ? $item->reference : ''); ?>
<?php echo render_input('address', _l('address'), isset($item) ? $item->address : ''); ?>
<?php echo render_input('city', _l('city'), isset($item) ? $item->city : ''); ?>
<?php echo render_input('province', _l('province'), isset($item) ? $item->province : ''); ?>
<?php echo render_input('postal_code', _l('postal_code'), isset($item) ? $item->postal_code : ''); ?>
<?php echo render_input('country', _l('country'), isset($item) ? $item->country : ''); ?>
<?php echo render_input('owner_name', _l('owner_name'), isset($item) ? $item->owner_name : ''); ?>
<?php echo render_input('owner_phone', _l('owner_phone'), isset($item) ? $item->owner_phone : ''); ?>
<?php echo render_input('owner_email', _l('owner_email'), isset($item) ? $item->owner_email : ''); ?>
<?php echo render_input('notes', _l('notes'), isset($item) ? $item->notes : ''); ?>
<?php echo render_input('active', _l('active'), isset($item) ? $item->active : ''); ?>
<button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
<?php echo form_close(); ?>
</div></div></div></div><?php init_tail(); ?>
