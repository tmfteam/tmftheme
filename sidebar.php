<?php
/**
 * The sidebar containing the main widget area.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package TMF
 * @since TMF 1.0
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>

<div id="secondary" class="primary-sidebar widget-area" role="complementary" aria-label=<?php _e( 'Primary Sidebar', 'tmf' ); ?>>
<?php do_action('tmf_sidebar');?>
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</div><!-- #secondary -->
