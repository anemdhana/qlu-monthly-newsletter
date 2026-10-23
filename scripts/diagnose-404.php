<?php
require '/var/www/html/wp-load.php';

echo 'permalink_structure=' . get_option('permalink_structure') . "\n";
echo 'theme=' . wp_get_theme()->get_stylesheet() . "\n";
echo 'cpt_exists=' . (post_type_exists('qlu_issue') ? 'yes' : 'no') . "\n";

$rules = get_option('rewrite_rules');
echo 'rewrite_rules_count=' . (is_array($rules) ? count($rules) : 0) . "\n";
if (is_array($rules)) {
	foreach ($rules as $k => $v) {
		if (str_contains((string) $k, 'issue') || str_contains((string) $v, 'qlu_issue')) {
			echo "RULE {$k} => {$v}\n";
		}
	}
}

$htaccess = '/var/www/html/.htaccess';
echo 'htaccess_exists=' . (file_exists($htaccess) ? 'yes' : 'no') . "\n";
if (file_exists($htaccess)) {
	echo "--- htaccess ---\n" . file_get_contents($htaccess) . "\n---\n";
}

$p = get_page_by_path('october-2026-te', OBJECT, 'qlu_issue');
if (!$p) {
	$posts = get_posts([
		'post_type' => 'qlu_issue',
		'name' => 'october-2026-te',
		'post_status' => 'any',
		'numberposts' => 1,
	]);
	$p = $posts[0] ?? null;
}
if ($p) {
	echo 'post_id=' . $p->ID . " status={$p->post_status} name={$p->post_name}\n";
	echo 'permalink=' . get_permalink($p) . "\n";
} else {
	echo "post october-2026-te not found\n";
}
