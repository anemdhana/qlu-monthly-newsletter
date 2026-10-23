<?php
require '/var/www/html/wp-load.php';

$theme = 'qlu-magazine';
if (!wp_get_theme($theme)->exists()) {
	fwrite(STDERR, "Theme {$theme} not found\n");
	exit(1);
}

switch_theme($theme);
update_option('permalink_structure', '/%postname%/');
flush_rewrite_rules(true);

echo 'active=' . wp_get_theme()->get('Name') . "\n";

$posts = get_posts(['post_type' => 'qlu_issue', 'numberposts' => 10, 'post_status' => 'publish']);
foreach ($posts as $p) {
	$lang = get_post_meta($p->ID, 'qlu_issue_lang', true);
	$month = get_post_meta($p->ID, 'qlu_issue_month', true);
	echo "{$lang} {$month} #{$p->ID} " . get_permalink($p->ID) . "\n";
}
echo 'archive=' . get_post_type_archive_link('qlu_issue') . "\n";
