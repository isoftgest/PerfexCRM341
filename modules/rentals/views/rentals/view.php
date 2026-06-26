<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper"><div class="content"><div class="panel_s"><div class="panel-body">
<h4><?php echo _l('view_rental'); ?> #<?php echo html_escape($rental->id); ?></h4>
<p><strong><?php echo _l('tenant'); ?>:</strong> <?php echo html_escape($rental->tenant_name); ?></p>
<p><strong><?php echo _l('property'); ?>:</strong> <?php echo html_escape($rental->property_name); ?></p>
<p><strong><?php echo _l('unit'); ?>:</strong> <?php echo html_escape($rental->unit_name); ?></p>
<p><strong><?php echo _l('monthly_price'); ?>:</strong> <?php echo app_format_money($rental->monthly_price, get_base_currency()); ?></p>
<?php if (rentals_is_admin_or_can('rentals', 'edit')) { ?><a class="btn btn-default" href="<?php echo admin_url('rentals/form/'.$rental->id); ?>"><?php echo _l('edit_rental'); ?></a><?php } ?>
<?php if (rentals_is_admin_or_can('rentals_payments', 'create')) { ?><a class="btn btn-primary" href="<?php echo admin_url('rentals/generate_month/'.$rental->id); ?>"><?php echo _l('generate_monthly_payment'); ?></a><?php } ?>
<hr><h4><?php echo _l('price_history'); ?></h4>
<table class="table dt-table"><thead><tr><th><?php echo _l('old_price'); ?></th><th><?php echo _l('new_price'); ?></th><th><?php echo _l('change_date'); ?></th><th><?php echo _l('change_reason'); ?></th></tr></thead><tbody>
<?php foreach ($history as $row) { ?><tr><td><?php echo html_escape($row['old_price']); ?></td><td><?php echo html_escape($row['new_price']); ?></td><td><?php echo html_escape($row['change_date']); ?></td><td><?php echo html_escape($row['reason']); ?></td></tr><?php } ?>
</tbody></table>
</div></div></div></div><?php init_tail(); ?>
