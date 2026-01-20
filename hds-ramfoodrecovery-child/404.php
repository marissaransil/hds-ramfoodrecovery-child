<?php 
	global $eltd_options;
?>

<?php get_header(); ?>

	<?php get_template_part( 'title' ); ?>

	<div class="container">
	<?php if($eltd_options['overlapping_content'] == 'yes') {?>
		<div class="overlapping_content"><div class="overlapping_content_inner">
	<?php } ?>
		<div class="container_inner eltd_404_page default_template_holder">
				<h4>The page you are looking for does not exist. It may have been moved, or removed altogether. You may want to try one or more of the following:</h4> <br />
                <ul>
                  <li>Return to the site’s <a href="/home">homepage</a></li>
                  <li>Use our site's search tool (accessible from the menu at the top of the page)</li>
                  <li>Reference our <a href="/site-map">site map</a></li>
                  <li>Use our <a href="/contact">contact page</a> to reach a department directly</li>
                  <li>Finally, for other web site related issues, please <a href="http://www.google.com/recaptcha/mailhide/d?k=01CDw-KGlyC7xDotGl3P_g0A==&c=mU6rxsoc-FnEuurN2kUV9t3jPx3XRMpGzsPrJyi5i5A=">contact the webmaster</a></li>
                </ul>
				<a class="qbutton with-shadow" href="<?php echo esc_url(home_url()); ?>/"><?php if($eltd_options['404_backlabel'] != ""): echo esc_html($eltd_options['404_backlabel']); else: ?> <?php _e('Back to homepage', 'eltd'); ?> <?php endif;?></a>
		</div>
		<?php if($eltd_options['overlapping_content'] == 'yes') {?>
				</div></div>
		<?php } ?>
	</div>
<?php get_footer(); ?>