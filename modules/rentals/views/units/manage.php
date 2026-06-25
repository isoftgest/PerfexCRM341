<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper"><div class="content"><div class="panel_s"><div class="panel-body">
<h4><?php echo html_escape($title); ?></h4>
<?php if (rentals_check_license(false)) { ?><a href="<?php echo admin_url(uri_string().'/form'); ?>" class="btn btn-primary m-bottom-15"><?php echo _l('new_record'); ?></a><?php } ?>
<table class="table dt-table"><thead><tr><th><?php echo _l('id'); ?></th><th><?php echo _l('property_id'); ?></th><th><?php echo _l('type'); ?></th><th><?php echo _l('name'); ?></th><th><?php echo _l('reference'); ?></th><th><?php echo _l('default_price'); ?></th><th><?php echo _l('active'); ?></th><th><?php echo _l('actions'); ?></th></tr></thead><tbody>
<?php foreach ($items as $item) { ?><tr><td><?php echo html_escape($item['id'] ?? ''); ?></td><td><?php echo html_escape($item['property_id'] ?? ''); ?></td><td><?php echo html_escape($item['type'] ?? ''); ?></td><td><?php echo html_escape($item['name'] ?? ''); ?></td><td><?php echo html_escape($item['reference'] ?? ''); ?></td><td><?php echo html_escape($item['default_price'] ?? ''); ?></td><td><?php echo html_escape($item['active'] ?? ''); ?></td><td><a href="<?php echo admin_url(uri_string().'/form/'.$item['id']); ?>" class="btn btn-default btn-icon"><i class="fa fa-pencil"></i></a></td></tr><?php } ?>
</tbody></table></div></div></div></div><?php init_tail(); ?>
