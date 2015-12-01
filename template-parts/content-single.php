<?php
/**
 * Template part for displaying single posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package TMF
 * @since TMF 1.0
 */

?>

<article itemscope itemtype= "http://schema.org/BlogPosting" id="post-<?php the_ID(); ?>" <?php post_class('entry'); ?>>
	<header class="entry-header">
		<?php the_title( '<h1 itemprop="headline" class="entry-title">', '</h1>' ); ?>
		
		<div class="entry-meta">
			<?php tmf_layout::posted_on(); ?>
		</div><!-- .entry-meta -->
		
		<?php tmf_layout::post_thumbnail(); ?>

	</header><!-- .entry-header -->

	<div itemprop="articleBody" class="entry-content">
		<span id="before-content" class="genericon"></span>
		<?php 
		do_action('tmf_entry_content');
		the_content(); ?>
		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'TMF' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php tmf_layout::entry_footer(); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->

