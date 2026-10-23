<?php
/**
 * Replace GitHub Pages Devi links in magazine issues with local Jnanodayam URLs.
 */
require '/var/www/html/wp-load.php';

$old = 'https://anemdhana.github.io/quantum-life-university/Swadhyaya/9-Day-Devi-Navaratri-Dr-Newton/index.html';
$programme = home_url('/');
$day1_en = get_posts([
	'post_type' => 'qlu_jnanodayam',
	'posts_per_page' => 1,
	'meta_query' => [
		'relation' => 'AND',
		['key' => 'qlu_series', 'value' => 'devi-navaratri'],
		['key' => 'qlu_day', 'value' => 1],
		['key' => 'qlu_lang', 'value' => 'en'],
	],
]);
$day1_te = get_posts([
	'post_type' => 'qlu_jnanodayam',
	'posts_per_page' => 1,
	'meta_query' => [
		'relation' => 'AND',
		['key' => 'qlu_series', 'value' => 'devi-navaratri'],
		['key' => 'qlu_day', 'value' => 1],
		['key' => 'qlu_lang', 'value' => 'te'],
	],
]);
$en_url = $day1_en ? get_permalink($day1_en[0]) : $programme;
$te_url = $day1_te ? get_permalink($day1_te[0]) : $programme;

$issues = get_posts([
	'post_type' => 'qlu_issue',
	'post_status' => 'any',
	'posts_per_page' => 20,
	'meta_key' => 'qlu_issue_month',
	'meta_value' => '2026-10',
]);

foreach ($issues as $post) {
	$lang = (string) get_post_meta($post->ID, 'qlu_issue_lang', true);
	$content = $post->post_content;
	$changed = false;

	if (str_contains($content, $old)) {
		$label = $lang === 'te'
			? 'Devi Navaratrula Jnanam — Programme (Days 1–10)'
			: 'Devi Navaratrula Jnanam — Programme (Days 1–10)';
		$day_links = $lang === 'te'
			? '<a href="' . esc_url($te_url) . '">రోజు 1 · తెలుగు</a> · <a href="' . esc_url($en_url) . '">Day 1 · English</a>'
			: '<a href="' . esc_url($en_url) . '">Day 1 · English</a> · <a href="' . esc_url($te_url) . '">రోజు 1 · తెలుగు</a>';

		// Replace the old single link paragraph with programme + day links.
		$patterns = [
			'#<p><a href="' . preg_quote($old, '#') . '">[^<]*</a></p>#u',
			'#href="' . preg_quote($old, '#') . '"#u',
		];
		$replacements = [
			'<p><a href="' . esc_url($programme) . '">' . esc_html($label) . '</a></p>' . "\n" .
			'<!-- /wp:paragraph -->' . "\n\n" .
			'<!-- wp:paragraph -->' . "\n" .
			'<p>' . $day_links . '</p>',
			'href="' . esc_url($programme) . '"',
		];
		$new = preg_replace($patterns[0], $replacements[0], $content, 1, $count1);
		if ($count1 === 0) {
			$new = str_replace($old, $programme, $content);
		}
		$content = $new;
		$changed = true;
	}

	// Upcoming section wording.
	$content2 = str_replace(
		[
			'దేవి నవరాత్రి సిరీస్ సారాంశాలు — పై లింక్ ద్వారా చదవండి',
			'Devi Navaratri day-wise summaries — use the link above',
		],
		[
			'దేవి నవరాత్రి పూర్తి పఠనం — <a href="' . esc_url($programme) . '">Jnanodayam Programme</a>',
			'Full Devi Navaratri readings — <a href="' . esc_url($programme) . '">Jnanodayam Programme</a>',
		],
		$content
	);
	if ($content2 !== $content) {
		$content = $content2;
		$changed = true;
	}

	// Themes intro wording.
	$content3 = str_replace(
		[
			'రోజువారీ సారాంశాలు చదవండి:',
			'Read day-wise summaries:',
		],
		[
			'పూర్తి రోజువారీ పఠనం (Jnanodayam):',
			'Full day readings (Jnanodayam):',
		],
		$content
	);
	if ($content3 !== $content) {
		$content = $content3;
		$changed = true;
	}

	if ($changed) {
		wp_update_post([
			'ID' => $post->ID,
			'post_content' => $content,
		]);
		echo "updated id={$post->ID} lang={$lang}\n";
	} else {
		echo "skip id={$post->ID} lang={$lang}\n";
	}
}

echo "programme=" . $programme . "\n";
echo "day1_en=" . $en_url . "\n";
echo "day1_te=" . $te_url . "\n";
