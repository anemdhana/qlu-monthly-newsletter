<?php
/**
 * Single magazine issue.
 *
 * @package QLU_Magazine
 */

get_header();
?>
<main class="qlu-content" id="main">
	<?php while (have_posts()) : the_post(); ?>
		<article <?php post_class(); ?>>
			<?php the_content(); ?>
		</article>
	<?php endwhile; ?>
</main>
<?php
get_footer();
