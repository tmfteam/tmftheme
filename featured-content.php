<?php
/**
 * The template for displaying featured content
 *
 * @package TMF
 * @since TMF 1.0
 */
?>

<div id="featured-content" class="featured-content">
	<div class="featured-content-inner">
	<?php
		/**
		 * Fires before the TMF featured content.
		 *
		 * @since TMF 1.0
		 */
		do_action( 'tmf_featured_posts_before' );

		$featured_posts = tmf_base::get_featured_posts();
		foreach ( (array) $featured_posts as $order => $post ) :
			setup_postdata( $post );

			// Include the featured content template.
			get_template_part( 'template-parts/content', 'featured-post' );
		endforeach;

		/**
		 * Fires after the TMF featured content.
		 *
		 * @since TMF 1.0
		 */
		do_action( 'tmf_featured_posts_after' );

		wp_reset_postdata();
	?>
	</div><!-- .featured-content-inner -->
</div><!-- #featured-content .featured-content -->
