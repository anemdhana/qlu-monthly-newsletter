<?php
/**
 * Main fallback template.
 *
 * @package QLU_Magazine
 */

get_header();
?>
<main class="qlu-content" id="main">
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
			<article <?php post_class(); ?>>
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<?php the_excerpt(); ?>
			</article>
		<?php endwhile; ?>
	<?php else : ?>
		<p><?php esc_html_e('No issues yet.', 'qlu-magazine'); ?></p>
	<?php endif; ?>
</main>
<?php
get_footer();
