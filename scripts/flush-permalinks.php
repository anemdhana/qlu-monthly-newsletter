<?php
require '/var/www/html/wp-load.php';
update_option('permalink_structure', '/%postname%/');
flush_rewrite_rules(true);

$te = get_posts([
	'post_type' => 'qlu_issue',
	'numberposts' => 1,
	'meta_key' => 'qlu_issue_lang',
	'meta_value' => 'te',
]);
$en = get_posts([
	'post_type' => 'qlu_issue',
	'numberposts' => 1,
	'meta_key' => 'qlu_issue_lang',
	'meta_value' => 'en',
]);

echo 'theme=' . wp_get_theme()->get('Name') . "\n";
echo 'archive=' . get_post_type_archive_link('qlu_issue') . "\n";
if ($te) {
	echo 'te=' . get_permalink($te[0]->ID) . "\n";
}
if ($en) {
	echo 'en=' . get_permalink($en[0]->ID) . "\n";
}
