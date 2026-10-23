<?php
/**
 * Build qlu_issue Te+En posts from content/magazine/YYYY-MM/ section folders.
 *
 * Expects issue tree at: /tmp/magazine-issue/  (docker cp of content/magazine/YYYY-MM)
 * Env/args: month via CLI argv[1] e.g. 2026-10
 *
 * Convention: docs/MAGAZINE-SEED.md
 */

declare(strict_types=1);

require '/var/www/html/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$month = $argv[1] ?? getenv('QLU_MAGAZINE_MONTH') ?: '';
if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
	fwrite(STDERR, "Usage: php import-magazine-from-folder.php YYYY-MM\n");
	exit(1);
}

$root = '/tmp/magazine-issue';
if (!is_dir($root)) {
	fwrite(STDERR, "Missing {$root} — docker cp content/magazine/{$month} there first.\n");
	exit(1);
}

if (!post_type_exists('qlu_issue')) {
	fwrite(STDERR, "CPT qlu_issue missing\n");
	exit(1);
}

const QLU_MAG_SECTIONS = [
	'00-masthead' => ['heading' => null, 'empty_ok' => true],
	'01-themes' => ['heading' => 'Summary of the themes covered in that month'],
	'02-highlight-classes' => ['heading' => 'Highlight classes of the month'],
	'03-experiences' => ['heading' => 'Few Experiences from Participants'],
	'04-visits' => ['heading' => "Dr. Newton's visits and classes in other places"],
	'05-vanya' => ['heading' => 'Vanya Maitreya Highlights'],
	'06-campus' => ['heading' => 'Goloka, Sahasrara and QLU Updates'],
	'07-upcoming' => ['heading' => 'Upcoming Programs/Announcements'],
	'08-closing' => ['heading' => 'Closing'],
];

const QLU_IMG_EXT = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

function qlu_mag_empty(string $lang): string {
	return $lang === 'en'
		? 'No special report this month.'
		: 'ఈ నెలలో ప్రత్యేక నివేదిక లేదు.';
}

function qlu_mag_read(string $path): string {
	if (!is_readable($path)) {
		return '';
	}
	$t = file_get_contents($path);
	return $t === false ? '' : trim($t);
}

/**
 * Plain text / light markdown → Gutenberg HTML blocks.
 */
function qlu_mag_text_to_blocks(string $text): string {
	$text = str_replace(["\r\n", "\r"], "\n", $text);
	$text = trim($text);
	if ($text === '') {
		return '';
	}

	$blocks = '';
	$paras = preg_split("/\n{2,}/", $text) ?: [];
	foreach ($paras as $para) {
		$para = trim($para);
		if ($para === '') {
			continue;
		}
		$lines = preg_split("/\n/", $para) ?: [];
		$is_list = true;
		foreach ($lines as $line) {
			if (!preg_match('/^\s*[-*]\s+/u', $line)) {
				$is_list = false;
				break;
			}
		}
		if ($is_list && count($lines) > 0) {
			$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\">\n";
			foreach ($lines as $line) {
				$item = preg_replace('/^\s*[-*]\s+/u', '', $line) ?? $line;
				$blocks .= '<li>' . esc_html($item) . "</li>\n";
			}
			$blocks .= "</ul>\n<!-- /wp:list -->\n\n";
			continue;
		}

		// Inline **bold**
		$html = esc_html($para);
		$html = preg_replace('/\*\*(.+?)\*\*/u', '<strong>$1</strong>', $html) ?? $html;
		$html = nl2br($html, false);
		$blocks .= "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->\n\n";
	}
	return $blocks;
}

/**
 * @return list<array{path:string,stem:string}>
 */
function qlu_mag_list_images(string $dir): array {
	$out = [];
	foreach (scandir($dir) ?: [] as $name) {
		if ($name === '.' || $name === '..') {
			continue;
		}
		$path = $dir . '/' . $name;
		if (!is_file($path)) {
			continue;
		}
		$ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
		if (!in_array($ext, QLU_IMG_EXT, true)) {
			continue;
		}
		$stem = pathinfo($name, PATHINFO_FILENAME);
		if (strcasecmp($stem, 'featured') === 0) {
			continue; // handled separately for masthead
		}
		// Skip caption/alt sidecar mistaken as images — they are .txt
		$out[] = ['path' => $path, 'stem' => $stem];
	}
	usort($out, static fn ($a, $b) => strnatcasecmp($a['stem'], $b['stem']));
	return $out;
}

/**
 * @return array{id:int,url:string}
 */
function qlu_mag_sideload(string $path, string $title): array {
	$src_key = 'mag:' . md5($path . '|' . (string) filesize($path));
	$existing = get_posts([
		'post_type' => 'attachment',
		'post_status' => 'inherit',
		'numberposts' => 1,
		'meta_key' => '_qlu_import_src',
		'meta_value' => $src_key,
	]);
	if ($existing) {
		$id = (int) $existing[0]->ID;
		return ['id' => $id, 'url' => (string) wp_get_attachment_url($id)];
	}

	$tmp = wp_tempnam(basename($path));
	copy($path, $tmp);
	$id = media_handle_sideload([
		'name' => basename($path),
		'tmp_name' => $tmp,
	], 0, $title);
	if (is_wp_error($id)) {
		@unlink($tmp);
		throw new RuntimeException($id->get_error_message());
	}
	update_post_meta($id, '_qlu_import_src', $src_key);
	return ['id' => (int) $id, 'url' => (string) wp_get_attachment_url($id)];
}

function qlu_mag_figure(int $id, string $url, string $caption, string $alt): string {
	$caption_e = esc_html($caption);
	$alt_e = esc_attr($alt);
	$url_e = esc_url($url);
	$cap_html = $caption !== ''
		? "<figcaption class=\"wp-element-caption\">{$caption_e}</figcaption>"
		: '';
	return <<<HTML
<!-- wp:image {"id":{$id},"sizeSlug":"large","linkDestination":"none","className":"qlu-photo"} -->
<figure class="wp-block-image size-large qlu-photo"><img src="{$url_e}" alt="{$alt_e}" class="wp-image-{$id}"/>{$cap_html}</figure>
<!-- /wp:image -->

HTML;
}

/**
 * @return array{html:string, featured:?int}
 */
function qlu_mag_build_lang(string $root, string $lang): array {
	$html = '';
	$featured = null;

	foreach (QLU_MAG_SECTIONS as $folder => $meta) {
		$dir = $root . '/' . $folder;
		$heading = $meta['heading'];

		$body = '';
		$images_html = '';

		if (is_dir($dir)) {
			$body = qlu_mag_text_to_blocks(qlu_mag_read($dir . '/content.' . $lang . '.txt'));

			if ($folder === '00-masthead') {
				foreach (QLU_IMG_EXT as $ext) {
					$feat = $dir . '/featured.' . $ext;
					if (is_readable($feat)) {
						$media = qlu_mag_sideload($feat, "Magazine featured {$lang}");
						$featured = $media['id'];
						$cap = qlu_mag_read($dir . '/featured.caption.' . $lang . '.txt');
						$alt = qlu_mag_read($dir . '/featured.alt.' . $lang . '.txt') ?: $cap ?: 'Cover';
						$images_html .= qlu_mag_figure($media['id'], $media['url'], $cap, $alt);
						break;
					}
				}
			}

			foreach (qlu_mag_list_images($dir) as $img) {
				$stem = $img['stem'];
				$media = qlu_mag_sideload($img['path'], "Magazine {$folder} {$stem}");
				$cap = qlu_mag_read($dir . '/' . $stem . '.caption.' . $lang . '.txt');
				$alt = qlu_mag_read($dir . '/' . $stem . '.alt.' . $lang . '.txt') ?: $cap ?: $stem;
				$images_html .= qlu_mag_figure($media['id'], $media['url'], $cap, $alt);
			}
		}

		$has_content = $body !== '' || $images_html !== '';
		if (!$has_content) {
			if ($folder === '00-masthead') {
				continue;
			}
			$body = "<!-- wp:paragraph -->\n<p>" . esc_html(qlu_mag_empty($lang)) . "</p>\n<!-- /wp:paragraph -->\n\n";
		}

		if ($heading !== null) {
			$html .= '<!-- wp:heading {"level":2} -->' . "\n";
			$html .= '<h2 class="wp-block-heading">' . esc_html($heading) . "</h2>\n";
			$html .= "<!-- /wp:heading -->\n\n";
		}

		// Photos after heading, then body (or body then photos for masthead intro)
		if ($folder === '00-masthead') {
			$html .= $images_html . $body;
		} else {
			$html .= $images_html . $body;
		}
	}

	return ['html' => trim($html), 'featured' => $featured];
}

function qlu_mag_upsert(
	string $title,
	string $slug,
	string $content,
	string $month,
	string $lang,
	int $author,
	?int $thumb
): int {
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
		throw new RuntimeException($id->get_error_message());
	}
	$id = (int) $id;
	update_post_meta($id, 'qlu_issue_month', $month);
	update_post_meta($id, 'qlu_issue_lang', $lang);
	if ($thumb) {
		set_post_thumbnail($id, $thumb);
	}
	return $id;
}

$meta = [
	'title_te' => "నెలవారీ పత్రిక — {$month}",
	'title_en' => "Monthly Magazine — {$month}",
	'slug_te' => "{$month}-te",
	'slug_en' => "{$month}-en",
];
$json_path = $root . '/issue.json';
if (is_readable($json_path)) {
	$data = json_decode((string) file_get_contents($json_path), true);
	if (is_array($data)) {
		foreach (['title_te', 'title_en', 'slug_te', 'slug_en'] as $k) {
			if (!empty($data[$k]) && is_string($data[$k])) {
				$meta[$k] = $data[$k];
			}
		}
	}
}

$users = get_users(['number' => 1, 'orderby' => 'ID', 'order' => 'ASC']);
$author = $users ? (int) $users[0]->ID : 1;

try {
	$te = qlu_mag_build_lang($root, 'te');
	$en = qlu_mag_build_lang($root, 'en');
	$te_id = qlu_mag_upsert($meta['title_te'], $meta['slug_te'], $te['html'], $month, 'te', $author, $te['featured'] ?? $en['featured']);
	$en_id = qlu_mag_upsert($meta['title_en'], $meta['slug_en'], $en['html'], $month, 'en', $author, $en['featured'] ?? $te['featured']);
} catch (Throwable $e) {
	fwrite(STDERR, $e->getMessage() . "\n");
	exit(1);
}

flush_rewrite_rules(false);

echo "month={$month}\n";
echo 'te=' . get_permalink($te_id) . "\n";
echo 'en=' . get_permalink($en_id) . "\n";
