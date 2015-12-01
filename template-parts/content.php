<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package TMF
 * @since TMF 1.0
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	
	<header class="entry-header">

	
		<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

		<div class="entry-meta">
			<?php tmf_layout::posted_on(); ?>
		</div><!-- .entry-meta -->
		
		<div class="post-thumnail">
			<?php tmf_layout::post_thumbnail(); ?>
		</div>
	</header><!-- .entry-header -->

	<div class="entry-content">
	
		<?php
			do_action('tmf_entry_content');
			if (is_sticky()) {
				the_content();
			}
			else {
			the_excerpt();
			}
		?>

		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'tmf' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php tmf_layout::entry_footer(); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
