<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package TMF
 * @since TMF 1.0
 */


?>
<?php do_action('tmf_after_content');?>
	</div><!-- #content -->
<?php do_action('tmf_before_footer');?>
	<footer id="colophon" class="site-footer" role="contentinfo">
		<div class="site-info">
			<?php 
			$default =  __('Powered by <a href="https://wordpress.org"> Wordpress</a> And <a href="https://tmftheme.com">The Modern Framework.</a>',
		'tmf');
			echo get_theme_mod( 'footer_text', $default );?>
		</div><!-- .site-info -->
		<?php do_action('tmf_footer');?>
	</footer><!-- #colophon -->
<?php do_action('tmf_after_footer');?>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
