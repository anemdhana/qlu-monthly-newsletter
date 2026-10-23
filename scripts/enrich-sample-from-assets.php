<?php
/**
 * Import newsletter images and rebuild October 2026 Te/En sample issues.
 *
 * Expects images in /tmp/qlu-import/ (docker cp assets/import there).
 */

declare(strict_types=1);

require '/var/www/html/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

if (!post_type_exists('qlu_issue')) {
	fwrite(STDERR, "CPT qlu_issue missing\n");
	exit(1);
}

$import_dir = '/tmp/qlu-import';
$month = '2026-10';

$files = [
	'logo' => 'qlu-logo.png',
	'founders' => 'global-assets-20261021-2fskhw.png',
	'themes' => 'summary-of-the-themes-covered-in-that-month-20261021-k1yoys.png',
	'highlight' => 'highlight-classes-of-the-month-20261022-u1snxe.jpg',
	'visits' => 'dr-newton-s-visits-and-classes-in-other-places-20261022-3zdjv2.jpg',
];

/**
 * @return array{id:int,url:string}
 */
function qlu_sideload(string $path, string $title): array {
	if (!is_readable($path)) {
		fwrite(STDERR, "Missing file: {$path}\n");
		exit(1);
	}

	$existing = get_posts([
		'post_type' => 'attachment',
		'post_status' => 'inherit',
		'numberposts' => 1,
		'meta_key' => '_qlu_import_src',
		'meta_value' => basename($path),
	]);
	if ($existing) {
		$id = (int) $existing[0]->ID;
		return ['id' => $id, 'url' => (string) wp_get_attachment_url($id)];
	}

	$tmp = wp_tempnam(basename($path));
	copy($path, $tmp);
	$file_array = [
		'name' => basename($path),
		'tmp_name' => $tmp,
	];
	$id = media_handle_sideload($file_array, 0, $title);
	if (is_wp_error($id)) {
		@unlink($tmp);
		fwrite(STDERR, $id->get_error_message() . "\n");
		exit(1);
	}
	update_post_meta($id, '_qlu_import_src', basename($path));
	return ['id' => (int) $id, 'url' => (string) wp_get_attachment_url($id)];
}

function qlu_figure(int $id, string $url, string $caption, string $alt): string {
	$caption_e = esc_html($caption);
	$alt_e = esc_attr($alt);
	$url_e = esc_url($url);
	return <<<HTML
<!-- wp:image {"id":{$id},"sizeSlug":"large","linkDestination":"none","className":"qlu-photo"} -->
<figure class="wp-block-image size-large qlu-photo"><img src="{$url_e}" alt="{$alt_e}" class="wp-image-{$id}"/><figcaption class="wp-element-caption">{$caption_e}</figcaption></figure>
<!-- /wp:image -->
HTML;
}

$media = [];
foreach ($files as $key => $name) {
	$media[$key] = qlu_sideload($import_dir . '/' . $name, 'QLU ' . $key);
	echo "imported {$key} id={$media[$key]['id']}\n";
}

set_theme_mod('custom_logo', $media['logo']['id']);

$authors = get_users(['number' => 1, 'orderby' => 'ID', 'order' => 'ASC']);
$author = $authors ? (int) $authors[0]->ID : 1;

$fig_founders_te = qlu_figure($media['founders']['id'], $media['founders']['url'], 'డా. న్యూటన్ కొండవేటి & డా. లక్ష్మీ — QLU వ్యవస్థాపకులు', 'Dr. Newton and Dr. Lakshmi');
$fig_founders_en = qlu_figure($media['founders']['id'], $media['founders']['url'], 'Dr. Newton Kondaveti & Dr. Lakshmi — Founders of QLU', 'Dr. Newton and Dr. Lakshmi');
$fig_themes_te = qlu_figure($media['themes']['id'], $media['themes']['url'], 'దేవి నవరాత్రుల జ్ఞానం సిరీస్', 'Devi Navratula Jnanam Series');
$fig_themes_en = qlu_figure($media['themes']['id'], $media['themes']['url'], 'Devi Navratula Jnanam Series', 'Devi Navratula Jnanam Series');
$fig_highlight_te = qlu_figure($media['highlight']['id'], $media['highlight']['url'], 'ఈ నెల హైలైట్ క్లాస్ నుంచి', 'Highlight class moment');
$fig_highlight_en = qlu_figure($media['highlight']['id'], $media['highlight']['url'], 'From a highlight class this month', 'Highlight class moment');
$fig_visits_te = qlu_figure($media['visits']['id'], $media['visits']['url'], 'డా. న్యూటన్ పర్యటనలు / తరగతులు', 'Dr. Newton visits and classes');
$fig_visits_en = qlu_figure($media['visits']['id'], $media['visits']['url'], 'Dr. Newton’s visits and classes', 'Dr. Newton visits and classes');

$programme_url = esc_url(home_url('/'));
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
$day1_en_url = $day1_en ? esc_url(get_permalink($day1_en[0])) : $programme_url;
$day1_te_url = $day1_te ? esc_url(get_permalink($day1_te[0])) : $programme_url;

$te_content = <<<HTML
<!-- wp:group {"className":"qlu-intro","layout":{"type":"constrained"}} -->
<div class="wp-block-group qlu-intro">
<!-- wp:paragraph {"align":"center","className":"qlu-kicker"} -->
<p class="has-text-align-center qlu-kicker">Quantum Life University · అక్టోబర్ 2026</p>
<!-- /wp:paragraph -->
{$fig_founders_te}
<!-- wp:paragraph -->
<p>డా. న్యూటన్ కొండవేటి (MBBS, MD) మరియు డా. లక్ష్మీ న్యూటన్ (MBBS) — Quantum Life University వ్యవస్థాపకులు. వైద్య నేపథ్యం నుంచి ధ్యానం, ఆధ్యాత్మిక విజ్ఞానం, సమగ్ర సాధన వైపు నడిపిన జంట. ఈ సంచిక సెప్టెంబర్ నెల బోధనలు, అనుభవాలు, పర్యటనల సారాంశం.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Summary of the themes covered in that month</h2>
<!-- /wp:heading -->

<!-- wp:columns {"className":"qlu-two-col"} -->
<div class="wp-block-columns qlu-two-col">
<!-- wp:column -->
<div class="wp-block-column">
{$fig_themes_te}
</div>
<!-- /wp:column -->
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:paragraph -->
<p><strong>దేవి నవరాత్రుల జ్ఞానం సిరీస్</strong> — ఈ నెల ప్రధాన థీమ్. పూర్తి రోజువారీ పఠనం (Jnanodayam):</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p><a href="{$programme_url}">Devi Navaratrula Jnanam — Programme (Days 1–10)</a></p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p><a href="{$day1_te_url}">రోజు 1 · తెలుగు</a> · <a href="{$day1_en_url}">Day 1 · English</a></p>
<!-- /wp:paragraph -->
<!-- wp:list -->
<ul class="wp-block-list">
<li>దేవి తత్త్వం — అంతరాత్మ శక్తి</li>
<li>నవరాత్రి రోజుల బోధనల సారం</li>
<li>ధ్యానం + స్వాధ్యాయం కలయిక</li>
<li>సాధనలో భక్తి మరియు విజ్ఞానం</li>
</ul>
<!-- /wp:list -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Highlight classes of the month</h2>
<!-- /wp:heading -->

{$fig_highlight_te}

<!-- wp:paragraph -->
<p>దేవి నవరాత్రి సిరీస్‌లోని ప్రత్యేక సెషన్లు — లోతైన బోధన, సాధకుల ప్రశ్నలు, ధ్యాన ప్రాక్టికల్. రికార్డింగ్‌లు సంబంధిత ప్లేలిస్ట్‌లో ఉన్నాయి.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Few Experiences from Participants</h2>
<!-- /wp:heading -->

<!-- wp:quote {"className":"qlu-pullquote"} -->
<blockquote class="wp-block-quote qlu-pullquote"><p>నవరాత్రి రోజుల్లో ప్రతి రోజు సారాంశం చదవడం నా సాధనకు క్రమం ఇచ్చింది. ఇంట్లో కూడా ప్రశాంతత పెరిగింది.</p><cite>సాధకురాలు · హైదరాబాద్</cite></blockquote>
<!-- /wp:quote -->

<!-- wp:quote {"className":"qlu-pullquote"} -->
<blockquote class="wp-block-quote qlu-pullquote"><p>ఆన్‌లైన్‌లోనూ సంఘ భావన కనిపించింది. ఒంటరిగా ఉన్నా ఒంటరి కాదనిపించింది.</p><cite>సాధకుడు · చెన్నై</cite></blockquote>
<!-- /wp:quote -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Dr. Newton's visits and classes in other places</h2>
<!-- /wp:heading -->

{$fig_visits_te}

<!-- wp:paragraph -->
<p>ఈ నెల డా. న్యూటన్ పర్యటనలు మరియు ఇతర ప్రాంతాల తరగతుల ఫోటో సారాంశం. స్థానిక సంఘాలతో సత్సంగ్, ధ్యానం, ప్రశ్నోత్తరాలు కొనసాగాయి.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Vanya Maitreya Highlights</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>వన్య మైత్రేయ కార్యక్రమాల్లో పిల్లలు, కుటుంబాలతో కరుణ మరియు సేవా కార్యాలు. ఈ నెల ప్రత్యేక ఫోటో సెట్ రాకపోయినా, సంఘ కార్యకలాపాలు కొనసాగాయి.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Goloka, Sahasrara and QLU Updates</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list">
<li><strong>QLU</strong> — నెలవారీ పత్రిక వెబ్ ప్రచురణ; దేవి సిరీస్ సారాంశాలు పాఠకులకు అందుబాటులో.</li>
<li><strong>గోలోక / సహస్రార</strong> — క్యాంపస్ సేవా మరియు లైబ్రరీ క్రమం కొనసాగింది.</li>
</ul>
<!-- /wp:list -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Upcoming Programs/Announcements</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list">
<li>దేవి నవరాత్రి పూర్తి పఠనం — <a href="{$programme_url}">Jnanodayam Programme</a></li>
<li>తదుపరి జ్ఞానోదయం / సామూహిక ధ్యాన తేదీలు — సంఘ ప్రకటనలు చూడండి</li>
<li>ఫోటోలు / అనుభవాలు పంపాలంటే ఎడిటోరియల్ టీమ్‌ను సంప్రదించండి</li>
</ul>
<!-- /wp:list -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Closing</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>ఈ నెల మీ సాధనకు శుభాకాంక్షలు. — Quantum Life University</p>
<!-- /wp:paragraph -->
HTML;

$en_content = <<<HTML
<!-- wp:group {"className":"qlu-intro","layout":{"type":"constrained"}} -->
<div class="wp-block-group qlu-intro">
<!-- wp:paragraph {"align":"center","className":"qlu-kicker"} -->
<p class="has-text-align-center qlu-kicker">Quantum Life University · October 2026</p>
<!-- /wp:paragraph -->
{$fig_founders_en}
<!-- wp:paragraph -->
<p>Dr. Newton Kondaveti (MBBS, MD) and Dr. Lakshmi Newton (MBBS) founded Quantum Life University. From medical training they turned toward meditation, spiritual science, and holistic practice. This issue summarizes September’s teachings, experiences, and visits.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Summary of the themes covered in that month</h2>
<!-- /wp:heading -->

<!-- wp:columns {"className":"qlu-two-col"} -->
<div class="wp-block-columns qlu-two-col">
<!-- wp:column -->
<div class="wp-block-column">
{$fig_themes_en}
</div>
<!-- /wp:column -->
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:paragraph -->
<p><strong>Devi Navratula Jnanam Series</strong> — the main theme this month. Full day readings (Jnanodayam):</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p><a href="{$programme_url}">Devi Navaratrula Jnanam — Programme (Days 1–10)</a></p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p><a href="{$day1_en_url}">Day 1 · English</a> · <a href="{$day1_te_url}">రోజు 1 · తెలుగు</a></p>
<!-- /wp:paragraph -->
<!-- wp:list -->
<ul class="wp-block-list">
<li>Devi tattva — inner Shakti</li>
<li>Essence of the Navaratri-day teachings</li>
<li>Meditation joined with Swadhyaya</li>
<li>Bhakti and knowledge in sadhana</li>
</ul>
<!-- /wp:list -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Highlight classes of the month</h2>
<!-- /wp:heading -->

{$fig_highlight_en}

<!-- wp:paragraph -->
<p>Standout sessions from the Devi Navaratri series — deep teaching, sadhaka questions, and meditation practice. Recordings are on the related playlist.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Few Experiences from Participants</h2>
<!-- /wp:heading -->

<!-- wp:quote {"className":"qlu-pullquote"} -->
<blockquote class="wp-block-quote qlu-pullquote"><p>Reading each day’s summary during Navaratri gave my practice a steady rhythm. Home feels quieter too.</p><cite>Sadhaka · Hyderabad</cite></blockquote>
<!-- /wp:quote -->

<!-- wp:quote {"className":"qlu-pullquote"} -->
<blockquote class="wp-block-quote qlu-pullquote"><p>Even online, there was a sense of sangha. Alone at home, I did not feel alone.</p><cite>Sadhaka · Chennai</cite></blockquote>
<!-- /wp:quote -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Dr. Newton's visits and classes in other places</h2>
<!-- /wp:heading -->

{$fig_visits_en}

<!-- wp:paragraph -->
<p>Photo highlight from Dr. Newton’s visits and classes elsewhere this month — satsang, meditation, and Q&amp;A with local sanghas.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Vanya Maitreya Highlights</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Vanya Maitreya activities continued with families and children — compassion and seva. A dedicated photo set was not available for this sample; text only this month.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Goloka, Sahasrara and QLU Updates</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list">
<li><strong>QLU</strong> — Monthly magazine on the web; Devi series summaries available to readers.</li>
<li><strong>Goloka / Sahasrara</strong> — Campus seva and library work continued.</li>
</ul>
<!-- /wp:list -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Upcoming Programs/Announcements</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list">
<li>Full Devi Navaratri readings — <a href="{$programme_url}">Jnanodayam Programme</a></li>
<li>Next Jnanodayam / collective meditation dates — watch sangha notices</li>
<li>To send photos or experiences, contact the editorial team</li>
</ul>
<!-- /wp:list -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Closing</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Blessings for your sadhana this month. — Quantum Life University</p>
<!-- /wp:paragraph -->
HTML;

/**
 * @return int
 */
function qlu_upsert_issue(string $title, string $slug, string $content, string $month, string $lang, int $author, int $thumb_id): int {
	$existing = get_posts([
		'post_type' => 'qlu_issue',
		'post_status' => 'any',
		'posts_per_page' => 1,
		'meta_query' => [
			'relation' => 'AND',
			['key' => 'qlu_issue_month', 'value' => $month],
			['key' => 'qlu_issue_lang', 'value' => $lang],
		],
	]);

	$payload = [
		'post_title' => $title,
		'post_name' => $slug,
		'post_content' => $content,
		'post_status' => 'publish',
		'post_type' => 'qlu_issue',
		'post_author' => $author,
	];

	if ($existing) {
		$payload['ID'] = (int) $existing[0]->ID;
		$id = wp_update_post($payload, true);
	} else {
		$id = wp_insert_post($payload, true);
	}

	if (is_wp_error($id)) {
		fwrite(STDERR, $id->get_error_message() . "\n");
		exit(1);
	}

	update_post_meta((int) $id, 'qlu_issue_month', $month);
	update_post_meta((int) $id, 'qlu_issue_lang', $lang);
	set_post_thumbnail((int) $id, $thumb_id);

	return (int) $id;
}

$te_id = qlu_upsert_issue(
	'నెలవారీ పత్రిక — అక్టోబర్ 2026',
	'october-2026-te',
	$te_content,
	$month,
	'te',
	$author,
	$media['themes']['id']
);
$en_id = qlu_upsert_issue(
	'Monthly Magazine — October 2026',
	'october-2026-en',
	$en_content,
	$month,
	'en',
	$author,
	$media['themes']['id']
);

flush_rewrite_rules(false);

echo "te=" . get_permalink($te_id) . "\n";
echo "en=" . get_permalink($en_id) . "\n";
echo "logo_set=1\n";
