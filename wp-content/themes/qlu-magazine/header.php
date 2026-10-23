<?php
/**
 * Site header / masthead.
 *
 * @package QLU_Magazine
 */

$lang = 'en';
$month = '';
$te_url = '';
$en_url = '';

if (is_singular('qlu_jnanodayam')) {
	$meta_lang = (string) get_post_meta(get_the_ID(), 'qlu_lang', true);
	if (in_array($meta_lang, ['te', 'en'], true)) {
		$lang = $meta_lang;
	}
} elseif (is_singular('qlu_issue')) {
	$meta_lang = (string) get_post_meta(get_the_ID(), 'qlu_issue_lang', true);
	if (in_array($meta_lang, ['te', 'en'], true)) {
		$lang = $meta_lang;
	}
	$month = (string) get_post_meta(get_the_ID(), 'qlu_issue_month', true);
	if ($month !== '') {
		$siblings = get_posts([
			'post_type' => 'qlu_issue',
			'post_status' => 'publish',
			'posts_per_page' => 2,
			'meta_key' => 'qlu_issue_month',
			'meta_value' => $month,
		]);
		foreach ($siblings as $sib) {
			$sib_lang = (string) get_post_meta($sib->ID, 'qlu_issue_lang', true);
			if ($sib_lang === 'te') {
				$te_url = get_permalink($sib);
			}
			if ($sib_lang === 'en') {
				$en_url = get_permalink($sib);
			}
		}
	}
}

$body_class = 'lang-' . $lang;
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
</head>
<body <?php body_class($body_class); ?>>
<?php wp_body_open(); ?>
<div class="qlu-site">
	<header class="qlu-masthead">
		<?php if (has_custom_logo()) : ?>
			<div class="qlu-logo"><?php the_custom_logo(); ?></div>
		<?php endif; ?>
		<p class="qlu-wordmark">Quantum Life University</p>
		<?php if (is_singular('qlu_jnanodayam')) : ?>
			<h1>Devi Navaratrula Jnanam</h1>
			<p class="qlu-meta"><?php esc_html_e('Jnanodayam · Self Knowledge Reading', 'qlu-magazine'); ?></p>
		<?php elseif (is_front_page()) : ?>
			<h1>Devi Navaratrula Jnanam</h1>
			<p class="qlu-meta"><?php esc_html_e('Jnanodayam · Self Knowledge Reading', 'qlu-magazine'); ?></p>
		<?php elseif (is_singular('qlu_issue')) : ?>
			<h1><?php echo $lang === 'en' ? esc_html__('Monthly Magazine', 'qlu-magazine') : esc_html('నెలవారీ పత్రిక'); ?></h1>
			<?php if ($month !== '') : ?>
				<p class="qlu-meta"><?php echo esc_html($month); ?></p>
			<?php endif; ?>
		<?php else : ?>
			<h1><?php bloginfo('name'); ?></h1>
			<?php if (get_bloginfo('description')) : ?>
				<p class="qlu-meta"><?php bloginfo('description'); ?></p>
			<?php endif; ?>
		<?php endif; ?>
		<?php if (is_singular('qlu_issue') && $te_url && $en_url) : ?>
		<nav class="qlu-lang-switch" aria-label="<?php esc_attr_e('Language', 'qlu-magazine'); ?>">
			<a href="<?php echo esc_url($te_url); ?>"<?php echo $lang === 'te' ? ' aria-current="page"' : ''; ?>>Te</a>
			|
			<a href="<?php echo esc_url($en_url); ?>"<?php echo $lang === 'en' ? ' aria-current="page"' : ''; ?>>En</a>
		</nav>
		<?php elseif (is_front_page() || is_singular('qlu_jnanodayam')) : ?>
		<nav class="qlu-lang-switch">
			<a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Programme', 'qlu-magazine'); ?></a>
		</nav>
		<?php endif; ?>
	</header>
