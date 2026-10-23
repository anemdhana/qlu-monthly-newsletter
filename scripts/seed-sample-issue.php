<?php
/**
 * One-off: create sample Te + En magazine issues with all sections.
 * Run: docker compose exec -T wordpress php /tmp/seed-sample-issue.php
 */

declare(strict_types=1);

require '/var/www/html/wp-load.php';

if (!post_type_exists('qlu_issue')) {
	fwrite(STDERR, "CPT qlu_issue missing. Is mu-plugin loaded?\n");
	exit(1);
}

$month = '2026-10';
$author = 1;
$users = get_users(['number' => 1, 'orderby' => 'ID', 'order' => 'ASC']);
if ($users) {
	$author = (int) $users[0]->ID;
}

$te_title = 'నెలవారీ పత్రిక — అక్టోబర్ 2026';
$en_title = 'Monthly Magazine — October 2026';

$te_content = <<<'HTML'
<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Summary of the themes covered in that month</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list">
<li>అహింసా ధ్యానం — రోజువారీ అభ్యాసంలో నిశ్శబ్దం ఎలా పెరుగుతుంది</li>
<li>స్వాధ్యాయం — గ్రంథ పఠనం ద్వారా అంతఃస్థిరత</li>
<li>కరుణ — కుటుంబం, సంఘం వద్ద చిన్న చర్యలు</li>
<li>జ్ఞానోదయం సిరీస్ — “అంతరాత్మ వెలుగు” థీమ్</li>
<li>సాధనా సమయం — ఉదయం 20 నిమిషాల నియమం</li>
</ul>
<!-- /wp:list -->

<!-- wp:paragraph -->
<p><em>ఈ నెల థీమ్:</em> నిశ్శబ్దంలో స్పష్టత — బయటి శబ్దం తగ్గినప్పుడు అంతర్గత మార్గదర్శకత్వం స్పష్టమవుతుంది.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Highlight classes of the month</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><strong>12 సెప్టెంబర్</strong> — జ్ఞానోదయం: అహింసా ఆలోచన. కోపం వచ్చినప్పుడు శ్వాసను గమనించడం ఎలా సహాయపడుతుందో చర్చ.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>19 సెప్టెంబర్</strong> — ప్రత్యేక సెషన్: స్వాధ్యాయ నోట్స్ ఎలా ఉంచాలి. సాధకులు తమ అనుభవాలు పంచుకున్నారు.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>26 సెప్టెంబర్</strong> — ధ్యానం ప్రాక్టికల్: 15 నిమిషాల నిశ్శబ్ద సెట్టింగ్. రికార్డింగ్: Jnanodayam ప్లేలిస్ట్.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Few Experiences from Participants</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><strong>ర. (హైదరాబాద్)</strong> — ఈ నెల ధ్యానం తర్వాత ఇంట్లో చర్చలు మెత్తగా జరుగుతున్నాయి. ఒకసారి కోపం వచ్చినా, ఒక నిమిషం ఆగి మాట్లాడగలుగుతున్నాను.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>స. (విజయవాడ)</strong> — స్వాధ్యాయం రోజూ చదవడం మొదలుపెట్టాను. మొదట కష్టంగా అనిపించింది; ఇప్పుడు ఉదయం అలవాటు అయింది.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>అ. (చెన్నై)</strong> — ఆన్‌లైన్ క్లాస్‌లో కూడా సంఘ భావన కనిపిస్తోంది. ఒంటరిగా ఉన్నా ఒంటరికి కాదని అనిపిస్తుంది.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Dr. Newton's visits and classes in other places</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list">
<li><strong>5–6 సెప్టెంబర్, బెంగళూరు</strong> — సాయంత్రం సత్సంగ్; స్థానిక సంఘంతో ప్రశ్నోత్తరాలు.</li>
<li><strong>14 సెప్టెంబర్, విశాఖపట్నం</strong> — ఉదయం ధ్యానం + చిన్న ఉపన్యాసం.</li>
<li><strong>21 సెప్టెంబర్</strong> — ఆన్‌లైన్ అతిథి సెషన్ (విదేశాల్లోని సాధకులు).</li>
</ul>
<!-- /wp:list -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Vanya Maitreya Highlights</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>ఈ నెల వన్య మైత్రేయ కార్యక్రమాల్లో పిల్లలతో కరుణ గీతాలు, చిన్న సేవా కార్యం జరిగాయి. పాల్గొన్న కుటుంబాలు సంతోషంగా అనుభవాలు పంచుకున్నారు.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Goloka, Sahasrara and QLU Updates</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list">
<li><strong>గోలోక</strong> — తోట పనులు కొనసాగాయి; వాలంటీర్లు వారాంతాల్లో సహాయం చేశారు.</li>
<li><strong>సహస్రార</strong> — లైబ్రరీ అల్మారాలు క్రమబద్ధం; కొత్త స్వాధ్యాయ పుస్తకాలు చేరాయి.</li>
<li><strong>QLU</strong> — నెలవారీ పత్రిక వెబ్ ప్రచురణ ప్రారంభం (ఈ సంచిక నమూనా).</li>
</ul>
<!-- /wp:list -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Upcoming Programs/Announcements</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list">
<li><strong>1 అక్టోబర్</strong> — ఈ సంచిక ప్రచురణ</li>
<li><strong>5 అక్టోబర్</strong> — జ్ఞానోదయం కొత్త నెల ప్రారంభం (రిజిస్ట్రేషన్: సెప్టెంబర్ 28 లోపు)</li>
<li><strong>12 అక్టోబర్</strong> — సామూహిక ధ్యానం (ఆన్‌లైన్ + క్యాంపస్)</li>
<li><strong>26 అక్టోబర్</strong> — వన్య మైత్రేయ కుటుంబ సమావేశం</li>
</ul>
<!-- /wp:list -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Closing</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>ఈ నెల మీ సాధనకు శుభాకాంక్షలు. ప్రశ్నలు లేదా అనుభవాలు పంచుకోవాలంటే సంప్రదించండి — Quantum Life University.</p>
<!-- /wp:paragraph -->
HTML;

$en_content = <<<'HTML'
<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Summary of the themes covered in that month</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list">
<li>Ahimsa meditation — how silence grows in daily practice</li>
<li>Swadhyaya — steadiness through sacred reading</li>
<li>Compassion — small acts at home and in sangha</li>
<li>Jnanodayam series — theme “Inner light”</li>
<li>Sadhana timing — a 20-minute morning rhythm</li>
</ul>
<!-- /wp:list -->

<!-- wp:paragraph -->
<p><em>Theme of the month:</em> Clarity in silence — when outer noise softens, inner guidance becomes clearer.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Highlight classes of the month</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><strong>12 September</strong> — Jnanodayam: Ahimsa in thought. How noticing the breath helps when anger rises.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>19 September</strong> — Special session: keeping Swadhyaya notes. Participants shared what works for them.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>26 September</strong> — Meditation practical: 15 minutes of quiet sitting. Recording: Jnanodayam playlist.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Few Experiences from Participants</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><strong>R. (Hyderabad)</strong> — After this month’s meditation, home conversations feel softer. Even when irritation comes, I can pause a minute before speaking.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>S. (Vijayawada)</strong> — I started daily Swadhyaya reading. It felt hard at first; now it is a morning habit.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>A. (Chennai)</strong> — Even in online classes there is a sense of sangha. Alone at home, I do not feel alone.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Dr. Newton's visits and classes in other places</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list">
<li><strong>5–6 September, Bengaluru</strong> — Evening satsang; Q&amp;A with the local sangha.</li>
<li><strong>14 September, Visakhapatnam</strong> — Morning meditation plus a short talk.</li>
<li><strong>21 September</strong> — Online guest session for overseas sadhakas.</li>
</ul>
<!-- /wp:list -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Vanya Maitreya Highlights</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>This month’s Vanya Maitreya activities included compassion songs with children and a small seva project. Families who joined shared joyful experiences.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Goloka, Sahasrara and QLU Updates</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list">
<li><strong>Goloka</strong> — Garden work continued; volunteers helped on weekends.</li>
<li><strong>Sahasrara</strong> — Library shelves reorganised; new Swadhyaya books arrived.</li>
<li><strong>QLU</strong> — Monthly magazine web publishing begins (this sample issue).</li>
</ul>
<!-- /wp:list -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Upcoming Programs/Announcements</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list">
<li><strong>1 October</strong> — This issue published</li>
<li><strong>5 October</strong> — Jnanodayam new-month start (register by 28 September)</li>
<li><strong>12 October</strong> — Collective meditation (online + campus)</li>
<li><strong>26 October</strong> — Vanya Maitreya family gathering</li>
</ul>
<!-- /wp:list -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Closing</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Blessings for your sadhana this month. For questions or to share experiences, reach Quantum Life University.</p>
<!-- /wp:paragraph -->
HTML;

/**
 * @return int Post ID
 */
function qlu_seed_issue(string $title, string $content, string $month, string $lang, int $author): int {
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

	update_post_meta($id, 'qlu_issue_month', $month);
	update_post_meta($id, 'qlu_issue_lang', $lang);

	return (int) $id;
}

$te_id = qlu_seed_issue($te_title, $te_content, $month, 'te', $author);
$en_id = qlu_seed_issue($en_title, $en_content, $month, 'en', $author);

flush_rewrite_rules(false);

echo "OK month={$month}\n";
echo "te_id={$te_id} url=" . get_permalink($te_id) . "\n";
echo "en_id={$en_id} url=" . get_permalink($en_id) . "\n";
echo "archive=" . get_post_type_archive_link('qlu_issue') . "\n";
