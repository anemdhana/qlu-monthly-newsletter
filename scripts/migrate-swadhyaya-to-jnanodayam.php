<?php
/**
 * One-off: rename post_type qlu_swadhyaya → qlu_jnanodayam and flush rewrites.
 */
require '/var/www/html/wp-load.php';

global $wpdb;
$n = $wpdb->query(
	"UPDATE {$wpdb->posts} SET post_type = 'qlu_jnanodayam' WHERE post_type = 'qlu_swadhyaya'"
);
echo "updated_posts={$n}\n";

flush_rewrite_rules(true);

$sample = get_posts([
	'post_type' => 'qlu_jnanodayam',
	'posts_per_page' => 1,
	'meta_key' => 'qlu_day',
	'meta_value' => 1,
	'meta_query' => [['key' => 'qlu_lang', 'value' => 'en']],
]);
if ($sample) {
	echo 'sample=' . get_permalink($sample[0]) . "\n";
}
echo 'home=' . home_url('/') . "\n";
