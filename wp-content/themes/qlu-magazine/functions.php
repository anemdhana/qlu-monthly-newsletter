<?php
/**
 * QLU Magazine theme bootstrap.
 *
 * @package QLU_Magazine
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
	exit;
}

define('QLU_MAGAZINE_VERSION', '0.3.1');

add_action('after_setup_theme', static function (): void {
	add_theme_support('title-tag');
	add_theme_support('post-thumbnails');
	add_theme_support('editor-styles');
	add_theme_support('wp-block-styles');
	add_theme_support('responsive-embeds');
	add_theme_support('custom-logo', [
		'height' => 120,
		'width' => 120,
		'flex-height' => true,
		'flex-width' => true,
	]);
	add_editor_style('assets/css/qlu.css');

	register_nav_menus([
		'primary' => __('Primary', 'qlu-magazine'),
		'footer' => __('Footer', 'qlu-magazine'),
	]);
});

add_action('wp_enqueue_scripts', static function (): void {
	wp_enqueue_style(
		'qlu-magazine-fonts',
		'https://fonts.googleapis.com/css2?family=Noto+Sans+Telugu:wght@400;600;700&family=Source+Sans+3:ital,wght@0,400;0,600;0,700;1,400&display=swap',
		[],
		null
	);
	wp_enqueue_style(
		'qlu-magazine',
		get_stylesheet_directory_uri() . '/assets/css/qlu.css',
		['qlu-magazine-fonts'],
		QLU_MAGAZINE_VERSION
	);
});

add_action('init', static function (): void {
	register_block_pattern_category('qlu-magazine', [
		'label' => __('QLU Magazine', 'qlu-magazine'),
	]);
});

/**
 * Soft empty-section lines for editors (Te / En).
 */
function qlu_magazine_empty_section_line(string $lang = 'te'): string {
	return $lang === 'en'
		? 'No special report this month.'
		: 'ఈ నెలలో ప్రత్యేక నివేదిక లేదు.';
}
