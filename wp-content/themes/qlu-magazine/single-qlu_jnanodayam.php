<?php
/**
 * Single Jnanodayam reading (blog / reader).
 *
 * @package QLU_Magazine
 */

get_header();

while (have_posts()) :
	the_post();
	$day = (int) get_post_meta(get_the_ID(), 'qlu_day', true);
	$lang = (string) get_post_meta(get_the_ID(), 'qlu_lang', true);
	$sub = (string) get_post_meta(get_the_ID(), 'qlu_day_subtitle', true);
	$sib = qlu_jnanodayam_sibling_url(get_the_ID());
	$prev = qlu_jnanodayam_adjacent_url(get_the_ID(), -1);
	$next = qlu_jnanodayam_adjacent_url(get_the_ID(), 1);
	?>
<main class="qlu-content qlu-reader" id="main">
	<article <?php post_class(); ?>>
		<header class="qlu-reader-head">
			<p class="qlu-kicker">Devi Navaratrula Jnanam · <?php echo $lang === 'te' ? 'రోజు' : 'Day'; ?> <?php echo esc_html((string) $day); ?></p>
			<h1 class="qlu-reader-title"><?php echo esc_html($sub !== '' ? $sub : get_the_title()); ?></h1>
			<nav class="qlu-lang-switch" aria-label="<?php esc_attr_e('Language', 'qlu-magazine'); ?>">
				<?php if ($sib) : ?>
					<?php if ($lang === 'te') : ?>
						<span aria-current="page">Te</span> | <a href="<?php echo esc_url($sib); ?>">En</a>
					<?php else : ?>
						<a href="<?php echo esc_url($sib); ?>">Te</a> | <span aria-current="page">En</span>
					<?php endif; ?>
				<?php endif; ?>
			</nav>
		</header>
		<div class="qlu-reader-body">
			<?php the_content(); ?>
		</div>
		<nav class="qlu-reader-nav" aria-label="<?php esc_attr_e('Day navigation', 'qlu-magazine'); ?>">
			<span>
				<?php if ($prev) : ?>
					<a href="<?php echo esc_url($prev); ?>">← <?php echo $lang === 'te' ? 'మునుపటి రోజు' : 'Previous day'; ?></a>
				<?php endif; ?>
			</span>
			<a href="<?php echo esc_url(home_url('/')); ?>"><?php echo $lang === 'te' ? 'కార్యక్రమం' : 'Programme'; ?></a>
			<span>
				<?php if ($next) : ?>
					<a href="<?php echo esc_url($next); ?>"><?php echo $lang === 'te' ? 'తదుపరి రోజు' : 'Next day'; ?> →</a>
				<?php endif; ?>
			</span>
		</nav>
	</article>
</main>
	<?php
endwhile;

get_footer();
