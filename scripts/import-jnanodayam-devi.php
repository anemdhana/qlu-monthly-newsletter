<?php
/**
 * Import Devi Navaratri day HTML (chapters JSON) into qlu_jnanodayam posts.
 *
 * Source dir: /tmp/jnanodayam-devi/ (docker cp from QLU Devi HTML folder)
 */

declare(strict_types=1);

require '/var/www/html/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$src = '/tmp/jnanodayam-devi';
$series = 'devi-navaratri';
$series_title = 'Devi Navaratrula Jnanam';

$day_subtitles = [
	1 => 'Bathukamma · The Asura and the Hara Centre',
	2 => 'The Empty Vessel · Shakti and Chaitanya',
	3 => 'Sura and Asura · Svara and Apasvara',
	4 => 'Cause, Symbol, and a New Birth',
	5 => 'Two Navaratris · Aham and Sarvam',
	6 => 'Parashakti · Mahishasura Mardini',
	7 => 'The Sword of Knowledge · Sumbha and Nisumbha',
	8 => 'Devi Shakti · Nature and the Power of Words',
	9 => 'The First Principle · Shakti, Chaitanya, Knowledge',
	10 => 'Vijayadashami — Divine Shakti, Self-Knowledge, and Spiritual Practice',
];

if (!post_type_exists('qlu_jnanodayam')) {
	fwrite(STDERR, "CPT qlu_jnanodayam missing\n");
	exit(1);
}

/**
 * @return list<array{title:string,paras:list<string>,minutes?:int,words?:int}>
 */
function qlu_extract_chapters(string $html): array {
	if (!preg_match('/const chapters=(\[.*?\]);\s*(?:const nav|const )/s', $html, $m)) {
		throw new RuntimeException('chapters array not found');
	}
	$json = $m[1];
	$data = json_decode($json, true);
	if (!is_array($data)) {
		throw new RuntimeException('JSON decode failed: ' . json_last_error_msg());
	}
	return $data;
}

/**
 * @param list<array{title:string,paras:list<string>}> $chapters
 */
function qlu_chapters_to_html(array $chapters): string {
	$out = '';
	foreach ($chapters as $ch) {
		$title = esc_html((string) ($ch['title'] ?? ''));
		$out .= "<!-- wp:heading {\"level\":2} -->\n<h2 class=\"wp-block-heading\">{$title}</h2>\n<!-- /wp:heading -->\n\n";
		foreach ($ch['paras'] ?? [] as $para) {
			$p = esc_html((string) $para);
			$out .= "<!-- wp:paragraph -->\n<p>{$p}</p>\n<!-- /wp:paragraph -->\n\n";
		}
	}
	return trim($out);
}

function qlu_upsert_reading(
	string $title,
	string $slug,
	string $content,
	string $series,
	int $day,
	string $lang,
	string $subtitle,
	int $author,
	?int $thumb_id
): int {
	$existing = get_posts([
		'post_type' => 'qlu_jnanodayam',
		'post_status' => 'any',
		'posts_per_page' => 1,
		'meta_query' => [
			'relation' => 'AND',
			['key' => 'qlu_series', 'value' => $series],
			['key' => 'qlu_day', 'value' => $day],
			['key' => 'qlu_lang', 'value' => $lang],
		],
	]);

	$payload = [
		'post_title' => $title,
		'post_name' => $slug,
		'post_content' => $content,
		'post_status' => 'publish',
		'post_type' => 'qlu_jnanodayam',
		'post_author' => $author,
		'post_excerpt' => $subtitle,
	];

	if ($existing) {
		$payload['ID'] = (int) $existing[0]->ID;
		$id = wp_update_post($payload, true);
	} else {
		$id = wp_insert_post($payload, true);
	}

	if (is_wp_error($id)) {
		throw new RuntimeException($id->get_error_message());
	}

	$id = (int) $id;
	update_post_meta($id, 'qlu_series', $series);
	update_post_meta($id, 'qlu_day', $day);
	update_post_meta($id, 'qlu_lang', $lang);
	update_post_meta($id, 'qlu_day_subtitle', $subtitle);
	if ($thumb_id) {
		set_post_thumbnail($id, $thumb_id);
	}
	return $id;
}

$users = get_users(['number' => 1, 'orderby' => 'ID', 'order' => 'ASC']);
$author = $users ? (int) $users[0]->ID : 1;

$thumb_id = null;
$banner = $src . '/assets/devi-navaratrula-jnanam-banner.png';
if (is_readable($banner)) {
	$existing_att = get_posts([
		'post_type' => 'attachment',
		'numberposts' => 1,
		'meta_key' => '_qlu_import_src',
		'meta_value' => 'devi-navaratrula-jnanam-banner.png',
	]);
	if ($existing_att) {
		$thumb_id = (int) $existing_att[0]->ID;
	} else {
		$tmp = wp_tempnam('devi-banner.png');
		copy($banner, $tmp);
		$att = media_handle_sideload([
			'name' => 'devi-navaratrula-jnanam-banner.png',
			'tmp_name' => $tmp,
		], 0, 'Devi Navaratrula Jnanam');
		if (!is_wp_error($att)) {
			$thumb_id = (int) $att;
			update_post_meta($thumb_id, '_qlu_import_src', 'devi-navaratrula-jnanam-banner.png');
		}
	}
}

$count = 0;
for ($day = 1; $day <= 10; $day++) {
	$num = str_pad((string) $day, 2, '0', STR_PAD_LEFT);
	$subtitle = $day_subtitles[$day];

	foreach (['en', 'te'] as $lang) {
		$file = "{$src}/day-{$num}-{$lang}.html";
		if (!is_readable($file)) {
			fwrite(STDERR, "Missing {$file}\n");
			exit(1);
		}
		$html = file_get_contents($file);
		if ($html === false) {
			fwrite(STDERR, "Read fail {$file}\n");
			exit(1);
		}
		try {
			$chapters = qlu_extract_chapters($html);
			$content = qlu_chapters_to_html($chapters);
		} catch (Throwable $e) {
			fwrite(STDERR, "Day {$day} {$lang}: " . $e->getMessage() . "\n");
			exit(1);
		}

		$title = $lang === 'te'
			? "{$series_title} · రోజు {$day}"
			: "{$series_title} · Day {$day}";
		$slug = "devi-navaratri-day-{$num}-{$lang}";

		$id = qlu_upsert_reading($title, $slug, $content, $series, $day, $lang, $subtitle, $author, $thumb_id);
		$words = 0;
		foreach ($chapters as $ch) {
			$words += (int) ($ch['words'] ?? 0);
		}
		echo "day={$day} lang={$lang} id={$id} chapters=" . count($chapters) . " words~{$words} " . get_permalink($id) . "\n";
		$count++;
	}
}

// Programme page
$page = get_page_by_path('devi-navaratrula-jnanam');
$page_content = <<<'HTML'
<!-- wp:paragraph -->
<p>Self Knowledge Reading · జ్ఞానోదయం · Devi Navratrula Jnanam. Full day readings in English and Telugu.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Choose a day below. On each reading, switch language with Te | En, and move Previous / Next day.</p>
<!-- /wp:paragraph -->
HTML;

$page_payload = [
	'post_title' => 'Devi Navaratrula Jnanam',
	'post_name' => 'devi-navaratrula-jnanam',
	'post_content' => $page_content,
	'post_status' => 'publish',
	'post_type' => 'page',
	'post_author' => $author,
];
if ($page) {
	$page_payload['ID'] = $page->ID;
	$page_id = wp_update_post($page_payload, true);
} else {
	$page_id = wp_insert_post($page_payload, true);
}
if (is_wp_error($page_id)) {
	fwrite(STDERR, $page_id->get_error_message() . "\n");
	exit(1);
}
$page_id = (int) $page_id;
if ($thumb_id) {
	set_post_thumbnail($page_id, $thumb_id);
}

update_option('show_on_front', 'page');
update_option('page_on_front', $page_id);
update_option('permalink_structure', '/%postname%/');
flush_rewrite_rules(true);

echo "programme_page=" . get_permalink($page_id) . "\n";
echo "imported={$count}\n";
