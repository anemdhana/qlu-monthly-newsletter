<?php
/**
 * Site footer.
 *
 * @package QLU_Magazine
 */

$month = '';
if (is_singular('qlu_issue')) {
	$month = (string) get_post_meta(get_the_ID(), 'qlu_issue_month', true);
}
?>
	<footer class="qlu-footer">
		<p>
			Quantum Life University
			<?php if ($month !== '') : ?>
				· <?php echo esc_html($month); ?>
			<?php endif; ?>
		</p>
		<p>
			<a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Magazine home', 'qlu-magazine'); ?></a>
			·
			<a href="<?php echo esc_url(get_post_type_archive_link('qlu_issue') ?: home_url('/issues/')); ?>"><?php esc_html_e('All issues', 'qlu-magazine'); ?></a>
		</p>
	</footer>
</div>
<?php wp_footer(); ?>
</body>
</html>
