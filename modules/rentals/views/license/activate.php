<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?><div id="wrapper"><div class="content"><div class="panel_s"><div class="panel-body">
<h4><?php echo _l('rentals_license_activation'); ?></h4>
<?php echo form_open(admin_url('rentals_license')); ?>
<?php echo render_input('license_key', _l('rentals_license_key'), isset($license->license_key) ? $license->license_key : ''); ?>
<button class="btn btn-primary" type="submit"><?php echo _l('rentals_activate_license'); ?></button>
<?php echo form_close(); ?>
</div></div></div></div><?php init_tail(); ?>
