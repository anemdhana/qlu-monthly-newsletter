<?php
/**
 * Home — Jnanodayam programme hub (Devi Navaratrula Jnanam).
 *
 * @package QLU_Magazine
 */

get_header();

$banner = null;
$days_en = get_posts([
	'post_type' => 'qlu_jnanodayam',
	'posts_per_page' => 20,
	'meta_key' => 'qlu_day',
	'orderby' => 'meta_value_num',
	'order' => 'ASC',
	'meta_query' => [
		'relation' => 'AND',
		['key' => 'qlu_series', 'value' => 'devi-navaratri'],
		['key' => 'qlu_lang', 'value' => 'en'],
	],
]);
?>
<main class="qlu-content qlu-programme" id="main">
	<?php while (have_posts()) : the_post(); ?>
		<?php if (has_post_thumbnail()) : ?>
			<figure class="qlu-programme-banner">
				<?php the_post_thumbnail('large'); ?>
			</figure>
		<?php endif; ?>
		<article <?php post_class(); ?>>
			<?php the_content(); ?>
		</article>
	<?php endwhile; ?>

	<p class="week-label"><?php esc_html_e('Days', 'qlu-magazine'); ?></p>
	<ul class="qlu-day-list">
		<?php foreach ($days_en as $day_post) : ?>
			<?php
			$day_n = (int) get_post_meta($day_post->ID, 'qlu_day', true);
			$sub = (string) get_post_meta($day_post->ID, 'qlu_day_subtitle', true);
			$te = get_posts([
				'post_type' => 'qlu_jnanodayam',
				'posts_per_page' => 1,
				'meta_query' => [
					'relation' => 'AND',
					['key' => 'qlu_series', 'value' => 'devi-navaratri'],
					['key' => 'qlu_day', 'value' => $day_n],
					['key' => 'qlu_lang', 'value' => 'te'],
				],
			]);
			$en_url = get_permalink($day_post);
			$te_url = $te ? get_permalink($te[0]) : '';
			?>
			<li>
				<strong><?php printf(esc_html__('Day %d', 'qlu-magazine'), $day_n); ?></strong>
				<?php if ($sub !== '') : ?>
					<em><?php echo esc_html($sub); ?></em>
				<?php endif; ?>
				<span class="day-langs">
					<a href="<?php echo esc_url($en_url); ?>">English</a>
					<?php if ($te_url) : ?>
						· <a href="<?php echo esc_url($te_url); ?>">తెలుగు</a>
					<?php endif; ?>
				</span>
			</li>
		<?php endforeach; ?>
	</ul>

	<?php if (!$days_en) : ?>
		<p><?php esc_html_e('No readings imported yet.', 'qlu-magazine'); ?></p>
	<?php endif; ?>
</main>
<?php
get_footer();
