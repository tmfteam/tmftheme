<?php
/**
 * The Layouts Handler
 *
 *
 * @package TMF
 * @since TMF 1.0
 */

 class tmf_layout {
 
	public $layout =array();
	public $first_class;
	public $second_class;
	public $third_class;
	public $first;
	public $second;
	public $third;
	
 
	function __construct() {
	
	add_action( 'edit_category', array($this, 'category_transient_flusher'));
	add_action( 'save_post',     array($this, 'category_transient_flusher') );
	//add_action( 'after_setup_theme', 'tmf_layout::header_style' );
		

	}
	

	
	static function paging_nav() {
		global $wp_query, $wp_rewrite;
		
		// Don't print empty markup if there's only one page.
		if ( $wp_query->max_num_pages < 2 ) {
			return;
		}
		
		$paged        = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
		$pagenum_link = html_entity_decode( get_pagenum_link() );
		$query_args   = array();
		$url_parts    = explode( '?', $pagenum_link );
		
		$pagenum_link = remove_query_arg( array_keys( $query_args ), $pagenum_link );
		$pagenum_link = trailingslashit( $pagenum_link ) . '%_%';

		$format  = $wp_rewrite->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
		$format .= $wp_rewrite->using_permalinks() ? user_trailingslashit( $wp_rewrite->pagination_base . '/%#%', 'paged' ) : '?paged=%#%';
		
		// Set up paginated links.
		$links = paginate_links( array(
			'base'     => $pagenum_link,
			'format'   => $format,
			'total'    => $wp_query->max_num_pages,
			'current'  => $paged,
			'mid_size' => 1,
			'add_args' => array_map( 'urlencode', $query_args ),
			'prev_text' => __( '&larr; Previous', 'tmf' ),
			'next_text' => __( 'Next &rarr;', 'tmf' ),
		) );

		if ( $links ) :
		?>
		
			<nav class="navigation paging-navigation" role="navigation">
				<h1 class="screen-reader-text"><?php _e( 'Posts navigation', 'tmf' ); ?></h1>
				<div class="pagination loop-pagination">
					<?php echo $links; ?>
				</div><!-- .pagination -->
			</nav><!-- .navigation -->
		<?php
		endif;
	
	}
	
	static function post_nav() {
	
		// Don't print empty markup if there's nowhere to navigate.
		$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
		$next     = get_adjacent_post( false, '', false );

		if ( ! $next && ! $previous ) {
			return;
		}

		?>
		<nav class="navigation post-navigation" role="navigation">
			<h1 class="screen-reader-text"><?php _e( 'Post navigation', 'tmf' ); ?></h1>
			<div class="nav-links">
			<?php
				if ( is_attachment() ) :
					previous_post_link( '%link', __( '<span class="meta-nav">Published In </span>%title', 'tmf' ) );
				else :
					previous_post_link( '%link', __( '<span class="meta-nav">Previous Post </span>%title', 'tmf' ) );
					next_post_link( '%link', __( '<span class="meta-nav">Next Post </span>%title', 'tmf' ) );
				endif;
			?>
			</div><!-- .nav-links -->
		</nav><!-- .navigation -->
	<?php
}

	/**
	 * Print HTML with meta information for the current post-date/time and author.
	 *
	 * @since TMF 1.0
	 */
	static function posted_on() {
		do_action('tmf_entry_header');
		if ( is_sticky() && is_home() && ! is_paged() ) {
			echo '<span class="featured-post">' . __( 'Sticky ', 'tmf' ) . '</span>';
		}
		

		// Set up and print post meta information.
		printf( '<span class="entry-date"><a href="%1$s" rel="bookmark"><time itemprop="datePublished" class="entry-date" datetime="%2$s">%3$s</time></a></span> <span class="byline">|  <span itemprop="author" class="author vcard"><a class="url fn n" href="%4$s" rel="author"> %5$s</a></span></span>',
			esc_url( get_permalink() ),
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() ),
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			get_the_author()
		);
		
		if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			echo '<span class="comments-link alignright"><span class="genericon genericon-comment"></span>';
			comments_popup_link( esc_html__( 'Leave a comment', 'tmf' ), esc_html__( '1 Comment', 'tmf' ), esc_html__( '% Comments', 'tmf' ) );
			echo '</span>';
		}	

	}
	
	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 */
	static function entry_footer() {
		do_action('tmf_entry_footer');
		// Hide category and tag text for pages.
		if ( 'post' === get_post_type() ) {
		
			/* translators: used between list items, there is a space after the comma */
			$categories_list = get_the_category_list( esc_html__( ', ', 'tmf' ) );
			if ( $categories_list && tmf_layout::categorized_blog() ) {
				printf( '<span class="cat-links genericon genericon-category">' . __( '%1$s Posted in  %2$s ', 'tmf' ) . '</span>', '<span class="screen-reader-text">','</span>'. $categories_list ); // WPCS: XSS OK.
			}

			/* translators: used between list items, there is a space after the comma */
			$tags_list = get_the_tag_list( '', esc_html__( ', ', 'tmf' ) );
			if ( $tags_list ) {
				printf( '<br /><span class="tags-links genericon genericon-tag">' . __( '%1$s Tagged in %2$s', 'tmf' ) . '</span>', '<span class="screen-reader-text">','</span>' . $tags_list ); // WPCS: XSS OK.
			}
		}


		edit_post_link(
			sprintf(
				/* translators: %s: Name of current post */
				esc_html__( ' Edit %s', 'tmf' ),
				the_title( '<span class="screen-reader-text">"', '"</span>', false )
			),
			'<span class="edit-link genericon genericon-edit">',
			'</span>'
		);
	}

	/**
	 * Returns true if a blog has more than 1 category.
	 *
	 * @return bool
	 */
	 static function categorized_blog() {
		if ( false === ( $all_the_cool_cats = get_transient( 'tmf_categories' ) ) ) {
			// Create an array of all the categories that are attached to posts.
			$all_the_cool_cats = get_categories( array(
				'fields'     => 'ids',
				'hide_empty' => 1,
				// We only need to know if there is more than one category.
				'number'     => 2,
			) );

			// Count the number of categories that are attached to the posts.
			$all_the_cool_cats = count( $all_the_cool_cats );

			set_transient( 'tmf_categories', $all_the_cool_cats );
		}

		if ( $all_the_cool_cats > 1 ) {
			// This blog has more than 1 category so _s_categorized_blog should return true.
			return true;
		} else {
			// This blog has only 1 category so _s_categorized_blog should return false.
			return false;
		}
	}

	/**
	 * Flush out the transients used in categorized_blog().
	 */
	function category_transient_flusher() {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		// Like, beat it. Dig?
		delete_transient( 'tmf_categories' );
	 }


	
	/**
	 * Display an optional post thumbnail.
	 *
	 * Wraps the post thumbnail in an anchor element on index
	 * views, or a div element when on single views.
	 *
	 * @since TMF 1.0
	 * 
	 */
	 static function post_thumbnail() {
		if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
			return;
		}

		if ( is_singular() ) :
		?>

			<div class="post-thumbnail">
				<?php
					if ( ( ! is_active_sidebar( 'sidebar-2' ) || is_page_template( 'page-templates/full-width.php' ) ) ) {
						the_post_thumbnail( 'tmf-full-width', array( 'alt' => get_the_title(),
									'itemprop'=>'thumbnailUrl', ));
					} else {
						the_post_thumbnail('large', array( 'alt' => get_the_title(),
									'itemprop'=>'thumbnailUrl',));
					}
				?>
			</div>

		<?php else : ?>

			<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true">
				<?php
					if ( ( ! is_active_sidebar( 'sidebar-2' ) || is_page_template( 'page-templates/full-width.php' ) ) ) {
						the_post_thumbnail( 'large', array( 'alt' => get_the_title(),
									'class' => 'align-center'));
					} else {
							the_post_thumbnail( 'post-thumbnail', array( 'alt' => get_the_title(),
									'class' => 'align-center'));
		}
				?>
			</a>

	<?php endif; // End is_singular()
}

	/**
	 * Styles the header image and text displayed on the blog
	 *
	 * 
	 */
	 static function header_style() {
		$header_image = get_header_image();

		// If no custom options for text are set, let's bail.
		if ( empty( $header_image ) && display_header_text() ) {
			return;
		}

		// If we get this far, we have custom styles. Let's do this.
		?>
		<style type="text/css" id="tmf-header-css">
		<?php
		// Short header for when there is no Custom Header and Header Text is hidden.
		if ( empty( $header_image ) && ! display_header_text() ) :
	?>

		@media screen and (min-width: 710px) {
			.site-branding {
				min-height: 56px;
			}
		}

	<?php
		endif;

		// Has a Custom Header been added?
		if ( ! empty( $header_image ) ) :
	?>
	@media screen and (min-width: 710px) {
		.site-branding {

			/*
			 * No shorthand so the Customizer can override individual properties.
			 * @see https://core.trac.wordpress.org/ticket/31460
			 */
			background-image: url(<?php header_image(); ?>);
			background-repeat: no-repeat;
			background-position: 50% 50%;
			-webkit-background-size: cover;
			-moz-background-size:    cover;
			-o-background-size:      cover;
			background-size:         cover;
		}
	}

		
	<?php
		endif;
		

		// Has the text been hidden?
		if ( ! display_header_text() ) :
	?>
		.site-title,
		.site-description {
			clip: rect(1px, 1px, 1px, 1px);
			position: absolute;
		}
	<?php endif; ?>
	<?php
		$header_text_color = get_header_textcolor();

		// If no custom options for text are set, let's bail
		// get_header_textcolor() options: HEADER_TEXTCOLOR is default, hide text (returns 'blank') or any hex value.
	if ( HEADER_TEXTCOLOR === $header_text_color ) {
		return;
	}
	?>
	.site-title a,
	.site-description,
	.secondary-navigation a{
			color: #<?php echo esc_attr( $header_text_color ); ?>!important;
	}
		
	</style>
	<?php
}



	static function show_secondary_nav() {
	
			return '<nav id="secondary-navigation" class="navigation secondary-navigation" role="navigation">' . 
			
				wp_nav_menu( array( 'theme_location' => 'secondary', 'menu_id' => 'secondary-menu' ) ) . '
			
			</nav><!-- #secondary-navigation -->';

	}
	

	/**
	 * Styles the header image and text displayed on the blog
	 *
	 * 
	 */
	 static function backround_style() {
		$header_image = get_background_image();

		
		if ( empty( $header_image )) {
			return;
		}

		// If we get this far, we have custom styles. Let's do this.
		?>
		<style type="text/css" id="tmf-body-css">


	@media screen and (min-width: 710px) {
		body {

			/*
			 * No shorthand so the Customizer can override individual properties.
			 * @see https://core.trac.wordpress.org/ticket/31460
			 */
			background-image: url(<?php background_image(); ?>);
			background-repeat: no-repeat;
			background-position: 50% 50%;
			-webkit-background-size: cover;
			-moz-background-size:    cover;
			-o-background-size:      cover;
			background-size:         cover;
			background-attachment:   fixed;
		} <?php if (get_theme_mod('home_bg_image')) {?>
		
		.home-page {
			background-image: url(<?php get_the; ?>);
			background-repeat: no-repeat;
			background-position: 50% 50%;
			-webkit-background-size: cover;
			-moz-background-size:    cover;
			-o-background-size:      cover;
			background-size:         cover;
			background-attachment:   fixed;
		
		}
		
		<?php }?>
	}

		
	</style>
	<?php
}

}
