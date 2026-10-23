<?php
/**
 * Plugin Name: QLU Jnanodayam Reader
 * Description: Self-knowledge reading posts (series days) — Te/En blog-style reader.
 * Author: Quantum Life University
 * Version: 0.2.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
	exit;
}

add_action('init', static function (): void {
	register_post_type('qlu_jnanodayam', [
		'labels' => [
			'name' => 'Jnanodayam',
			'singular_name' => 'Jnanodayam reading',
			'add_new_item' => 'Add Reading',
			'edit_item' => 'Edit Reading',
			'menu_name' => 'Jnanodayam',
		],
		'public' => true,
		'has_archive' => true,
		'rewrite' => [
			'slug' => 'jnanodayam',
			'with_front' => false,
		],
		'menu_icon' => 'dashicons-book',
		'show_in_rest' => true,
		'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'custom-fields'],
	]);

	register_post_meta('qlu_jnanodayam', 'qlu_series', [
		'type' => 'string',
		'single' => true,
		'show_in_rest' => true,
		'default' => 'devi-navaratri',
		'auth_callback' => static fn (): bool => current_user_can('edit_posts'),
	]);

	register_post_meta('qlu_jnanodayam', 'qlu_day', [
		'type' => 'integer',
		'single' => true,
		'show_in_rest' => true,
		'auth_callback' => static fn (): bool => current_user_can('edit_posts'),
	]);

	register_post_meta('qlu_jnanodayam', 'qlu_lang', [
		'type' => 'string',
		'single' => true,
		'show_in_rest' => true,
		'default' => 'en',
		'auth_callback' => static fn (): bool => current_user_can('edit_posts'),
	]);

	register_post_meta('qlu_jnanodayam', 'qlu_day_subtitle', [
		'type' => 'string',
		'single' => true,
		'show_in_rest' => true,
		'auth_callback' => static fn (): bool => current_user_can('edit_posts'),
	]);
});

/**
 * Sibling reading URL for the other language (same series + day).
 */
function qlu_jnanodayam_sibling_url(int $post_id): string {
	$series = (string) get_post_meta($post_id, 'qlu_series', true);
	$day = (int) get_post_meta($post_id, 'qlu_day', true);
	$lang = (string) get_post_meta($post_id, 'qlu_lang', true);
	$want = $lang === 'te' ? 'en' : 'te';

	$found = get_posts([
		'post_type' => 'qlu_jnanodayam',
		'post_status' => 'publish',
		'posts_per_page' => 1,
		'meta_query' => [
			'relation' => 'AND',
			['key' => 'qlu_series', 'value' => $series],
			['key' => 'qlu_day', 'value' => $day],
			['key' => 'qlu_lang', 'value' => $want],
		],
	]);

	return $found ? (string) get_permalink($found[0]) : '';
}

/**
 * Adjacent day URL within series + language.
 */
function qlu_jnanodayam_adjacent_url(int $post_id, int $delta): string {
	$series = (string) get_post_meta($post_id, 'qlu_series', true);
	$day = (int) get_post_meta($post_id, 'qlu_day', true);
	$lang = (string) get_post_meta($post_id, 'qlu_lang', true);
	$target = $day + $delta;
	if ($target < 1) {
		return '';
	}

	$found = get_posts([
		'post_type' => 'qlu_jnanodayam',
		'post_status' => 'publish',
		'posts_per_page' => 1,
		'meta_query' => [
			'relation' => 'AND',
			['key' => 'qlu_series', 'value' => $series],
			['key' => 'qlu_day', 'value' => $target],
			['key' => 'qlu_lang', 'value' => $lang],
		],
	]);

	return $found ? (string) get_permalink($found[0]) : '';
}
