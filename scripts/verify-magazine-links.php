<?php
require '/var/www/html/wp-load.php';
foreach ([7, 8] as $id) {
	$p = get_post($id);
	if (!$p) {
		echo "missing {$id}\n";
		continue;
	}
	echo "=== post {$id} ===\n";
	if (preg_match_all('#href="([^"]+)"#', $p->post_content, $m)) {
		foreach (array_unique($m[1]) as $u) {
			if (str_contains($u, 'github') || str_contains($u, 'jnanodayam') || str_contains($u, 'localhost') || $u === '/' || preg_match('#^https?://#', $u)) {
				echo "  {$u}\n";
			}
		}
	}
	echo str_contains($p->post_content, 'anemdhana.github.io') ? "HAS_OLD_GITHUB\n" : "no_github\n";
}
