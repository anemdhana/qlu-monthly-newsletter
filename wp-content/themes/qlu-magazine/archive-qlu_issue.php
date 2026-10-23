<?php
/**
 * Issues archive.
 *
 * @package QLU_Magazine
 */

get_header();
?>
<main class="qlu-content" id="main">
	<?php if (have_posts()) : ?>
		<ul class="qlu-issue-list">
			<?php while (have_posts()) : the_post(); ?>
				<?php
				$month = (string) get_post_meta(get_the_ID(), 'qlu_issue_month', true);
				$lang = (string) get_post_meta(get_the_ID(), 'qlu_issue_lang', true);
				?>
				<li>
					<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					<?php if ($month !== '' || $lang !== '') : ?>
						<span class="qlu-meta">
							— <?php echo esc_html(trim($month . ' / ' . strtoupper($lang), ' /')); ?>
						</span>
					<?php endif; ?>
				</li>
			<?php endwhile; ?>
		</ul>
	<?php else : ?>
		<p><?php esc_html_e('No issues published yet.', 'qlu-magazine'); ?></p>
	<?php endif; ?>
</main>
<?php
get_footer();
