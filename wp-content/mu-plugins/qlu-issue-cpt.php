<?php
/**
 * Plugin Name: QLU Issue CPT
 * Description: Registers the Monthly Magazine issue post type and language meta (Te/En).
 * Author: Quantum Life University
 * Version: 0.1.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
	exit;
}

add_action('init', static function (): void {
	register_post_type('qlu_issue', [
		'labels' => [
			'name' => 'Magazine Issues',
			'singular_name' => 'Magazine Issue',
			'add_new_item' => 'Add New Issue',
			'edit_item' => 'Edit Issue',
			'new_item' => 'New Issue',
			'view_item' => 'View Issue',
			'search_items' => 'Search Issues',
			'not_found' => 'No issues found',
			'menu_name' => 'Issues',
		],
		'public' => true,
		'has_archive' => true,
		'rewrite' => [
			'slug' => 'issues',
			'with_front' => false,
		],
		'menu_icon' => 'dashicons-book-alt',
		'show_in_rest' => true,
		'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'custom-fields'],
	]);

	register_post_meta('qlu_issue', 'qlu_issue_month', [
		'type' => 'string',
		'single' => true,
		'show_in_rest' => true,
		'description' => 'Issue id YYYY-MM (content window = previous calendar month).',
		'auth_callback' => static function (): bool {
			return current_user_can('edit_posts');
		},
	]);

	register_post_meta('qlu_issue', 'qlu_issue_lang', [
		'type' => 'string',
		'single' => true,
		'show_in_rest' => true,
		'description' => 'Issue language: te | en (parallel issues model D1=A).',
		'auth_callback' => static function (): bool {
			return current_user_can('edit_posts');
		},
		'default' => 'te',
	]);
});

add_action('add_meta_boxes', static function (): void {
	add_meta_box(
		'qlu_issue_meta',
		'QLU Issue',
		static function (WP_Post $post): void {
			$month = (string) get_post_meta($post->ID, 'qlu_issue_month', true);
			$lang = (string) get_post_meta($post->ID, 'qlu_issue_lang', true);
			if ($lang === '') {
				$lang = 'te';
			}
			wp_nonce_field('qlu_issue_meta_save', 'qlu_issue_meta_nonce');
			echo '<p><label for="qlu_issue_month"><strong>Issue month</strong> (YYYY-MM)</label><br />';
			printf(
				'<input type="text" id="qlu_issue_month" name="qlu_issue_month" value="%s" placeholder="2026-04" pattern="\\d{4}-\\d{2}" style="width:100%%;max-width:16rem" />',
				esc_attr($month)
			);
			echo '</p><p><label for="qlu_issue_lang"><strong>Language</strong></label><br />';
			echo '<select id="qlu_issue_lang" name="qlu_issue_lang">';
			foreach (['te' => 'Telugu (te)', 'en' => 'English (en)'] as $value => $label) {
				printf(
					'<option value="%s"%s>%s</option>',
					esc_attr($value),
					selected($lang, $value, false),
					esc_html($label)
				);
			}
			echo '</select></p>';
			echo '<p class="description">Create one issue post per language (parallel Te/En). Same YYYY-MM on both.</p>';
		},
		'qlu_issue',
		'side',
		'high'
	);
});

add_action('save_post_qlu_issue', static function (int $post_id): void {
	if (!isset($_POST['qlu_issue_meta_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash((string) $_POST['qlu_issue_meta_nonce'])), 'qlu_issue_meta_save')) {
		return;
	}
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}
	if (!current_user_can('edit_post', $post_id)) {
		return;
	}

	$month = isset($_POST['qlu_issue_month']) ? sanitize_text_field(wp_unslash((string) $_POST['qlu_issue_month'])) : '';
	if ($month !== '' && preg_match('/^\d{4}-\d{2}$/', $month)) {
		update_post_meta($post_id, 'qlu_issue_month', $month);
	}

	$lang = isset($_POST['qlu_issue_lang']) ? sanitize_text_field(wp_unslash((string) $_POST['qlu_issue_lang'])) : 'te';
	if (!in_array($lang, ['te', 'en'], true)) {
		$lang = 'te';
	}
	update_post_meta($post_id, 'qlu_issue_lang', $lang);
});
