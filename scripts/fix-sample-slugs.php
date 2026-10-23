<?php
require '/var/www/html/wp-load.php';

$posts = get_posts([
	'post_type' => 'qlu_issue',
	'numberposts' => 10,
	'post_status' => 'any',
	'meta_key' => 'qlu_issue_month',
	'meta_value' => '2026-10',
]);

foreach ($posts as $p) {
	$lang = (string) get_post_meta($p->ID, 'qlu_issue_lang', true);
	$slug = $lang === 'en' ? 'october-2026-en' : 'october-2026-te';
	wp_update_post([
		'ID' => $p->ID,
		'post_name' => $slug,
	]);
	echo "{$lang} -> " . get_permalink($p->ID) . "\n";
}
