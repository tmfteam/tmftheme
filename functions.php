<?php
/**
 * The Base Class
 *
 * If creating a child theme, just extend this class from your functions.php 
 * file then overide the methods and properties you want to alter
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *@link https://codex.wordpress.org/Plugin_API
 *
 * @package TMF
 * @since TMF 1.0
 */
 define('TMF_VERSION','1.0');
 
 require get_template_directory() . '/inc/tmf_options.php';
 
 require get_template_directory() . '/inc/tmf_layout.php';
 
 $content_width = apply_filters('tmf_width',600);
 
 $tmf = new tmf_base;
 
 class tmf_base {
	public $tmf_layout;
	public $tmf_options;
 
	function __construct() {
		global $tmf_option_array;
		
		add_action( 'after_setup_theme', array($this ,
												'theme_setup'));
		
		add_action( 'widgets_init', array($this ,
												'register_sidebars'));
												
		add_action( 'widgets_init', create_function('','return register_widget("TMF_Ephemera_Widget");'));
		
 
		add_action( 'wp_enqueue_scripts', array($this ,
										 'enqueue_scripts'));
										 
		add_action('wp-footer', array($this, 'footer_data'));
		
		add_filter( 'get_search_form', array($this,'search_form_modify') );
		
		add_filter( 'body_class', array($this,'body_classes' ));
		
		$this->tmf_options = new tmf_options;
		$this->tmf_layout = new tmf_layout();
		
}
	
	function theme_setup() {
		$color_scheme = $this->tmf_options->get_color_scheme();;
		/*
		 * Makes TMF available for translation.
		 *
		 * Translations can be added to the /languages/ directory.
		 */
		load_theme_textdomain('tmf', get_template_directory(). '/languages');
		
		add_editor_style( array( 'css/editor-style.css') );
		
		// Adds RSS feed links to <head> for posts and comments.
		add_theme_support( 'automatic-feed-links' );
		
		// Enable support for Post Thumbnails, and declare two sizes.
		add_theme_support( 'post-thumbnails' );
		set_post_thumbnail_size( 672, 372, true );
		add_image_size( 'tmf-full-width', 1038, 576, true );
		
		$color_scheme = $this->tmf_options->get_color_scheme();
			
		add_theme_support( 'custom-header', apply_filters( 'tmf_custom_header_args', array(
			'default-image'          => '',
			'default-text-color'     => '000000',
			'width'                  => 1000,
			'height'                 => 250,
			'flex-height'            => false,
			'wp-head-callback'       => 'tmf_layout::header_style'

		) ) );
		
		/*
		 * Switches default core markup for search form, comment form,
		 * and comments to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
				'search-form', 
				'comment-form',
				'comment-list',
				'gallery', 
				'caption'
				) );
		
		/*
		 * This theme supports all available post formats by default.
		 * See https://codex.wordpress.org/Post_Formats
		 */
		add_theme_support( 'post-formats', array(
			'aside', 
			'audio', 
			'chat', 
			'gallery', 
			'image', 
			'link', 
			'quote', 
			'status', 
			'video'
		) );

		// This theme uses wp_nav_menu() in 2 locations.
		register_nav_menus( array(
				'primary' => esc_html__( 'Primary Menu', 'tmf' ),
				'secondary' => esc_html__( 'Secondary Menu', 'tmf' ),
		) );
		
		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );
		
		// Set up the WordPress core custom background feature.
		add_theme_support( 'custom-background', apply_filters( 'tmf_custom_background_args', array(
			'default-color' => $color_scheme[0],
			'default-image' => '',
			'wp-head-callback'=> 'tmf_layout::backround_style'
		
	) ) );
	
	// Add support for featured content.
	add_theme_support( 'featured-content', array(
		'featured_content_filter' => 'tmf_get_featured_posts',
		'max_posts' => 3,
	) );
	}
	
	/**
	 * Register widget area.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
	 */
	 
	function register_sidebars() {
	
		register_sidebar( array(
			'name'          => esc_html__( 'Primary sidebar', 'tmf' ),
			'id'            => 'sidebar-1',
			'description'   => '',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );
	
		register_sidebar( array(
			'name'          => esc_html__( 'Secondary sidebar', 'tmf' ),
			'id'            => 'sidebar-2',
			'description'   => 'Only shown on some child themes',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );
		
	}
	
	function enqueue_scripts() {
		
		// Add Genericons font, used in the main stylesheet.
		wp_enqueue_style( 'genericons', get_template_directory_uri() . '/css/genericons/genericons.css', array(), '3.0.3' );
		
		// Add Google fonts, used in the main stylesheet.
		wp_enqueue_style( 'tmf-fonts', $this->fonts_url() );
		
		// Loads our main stylesheet with dashicons
		wp_enqueue_style( 'tmf-style', get_stylesheet_uri(), 'genericons' );

		wp_enqueue_script( 'tmf-functions', get_template_directory_uri() . '/js/functions.js', array('jquery'), '20120206', true );
		
		if(is_admin()) {
		wp_enqueue_style('tmf-admin', get_template_directory_uri() . '/css/admin.css');
		}
		
		//wp_enqueue_script( 'tm-navigation', get_template_directory_uri() . '/js/functions.js', array(), '20120206', true );

		
			/*
			 * Adds JavaScript to pages with the comment form to support
			 * sites with threaded comments (when in use).
			 */
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
		
		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		 add_theme_support( 'post-thumbnails' );
	
	}
	
	function footer_data() {
		get_theme_mod( 'color_scheme', 'default' );
	}
	
	/**
	 * Getter function for Featured Content Plugin.
	 *
	 * @since TMF 1.0
	 *
	 * @return array An array of WP_Post objects.
	 */
	 static function get_featured_posts() {

	return apply_filters( 'tmf_get_featured_posts', array() );
	
}

	/**
	 * A helper conditional function that returns a boolean value.
	 *
	 * @since Twenty Fourteen 1.0
	 *
	 * @return bool Whether there are featured posts.
	 */
	 static function has_featured_posts($minimum= 1) {
	return ! is_paged() && (bool) tmf_base::get_featured_posts();
}

/**
 * Add a `screen-reader-text` class to the search form's submit button.
 *
 * @since Twenty Fifteen 1.0
 *
 * @param string $html Search form HTML.
 * @return string Modified search form HTML.
 */
function search_form_modify( $html ) {
	return str_replace( 'class="search-submit"', 'class="search-submit screen-reader-text"', $html );
}


/**
 * Extend the default WordPress body classes.
 *
 * Adds body classes to denote:
 * 1. Single or multiple authors.
 * 2. Presence of header image except in Multisite signup and activate pages.
 * 3. Index views.
 * 4. Full-width content layout.
 * 5. Presence of footer widgets.
 * 6. Single views.
 * 7. Featured content layout.
 *
 * @since Twenty Fourteen 1.0
 *
 * @param array $classes A list of existing body class values.
 * @return array The filtered body class list.
 */
function body_classes( $classes ) {
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	if ( get_header_image() ) {
		$classes[] = 'header-image';
	} elseif ( ! in_array( $GLOBALS['pagenow'], array( 'wp-activate.php', 'wp-signup.php' ) ) ) {
		$classes[] = 'masthead-fixed';
	}

	if ( is_archive() || is_search() || is_home() ) {
		$classes[] = 'list-view';
	}

	if ( ( ! is_active_sidebar( 'sidebar-2' ) )
		|| is_page_template( 'page-templates/full-width.php' )
		|| is_page_template( 'page-templates/contributors.php' )
		|| is_attachment() ) {
		$classes[] = 'full-width';
	}

	if ( is_active_sidebar( 'sidebar-3' ) ) {
		$classes[] = 'footer-widgets';
	}

	if ( is_singular() && ! is_front_page() ) {
		$classes[] = 'singular';
	}

	if ( is_front_page() && 'slider' == get_theme_mod( 'featured_content_layout' ) ) {
		$classes[] = 'slider';
	} elseif ( is_front_page() ) {
		$classes[] = 'grid';
	}
	
	$classes[] = $this->tmf_options->get_layout_class();

	return $classes;
}

/**
 * Register Google fonts for TMF
 *
 * @since TMF 1.0
 *
 * @return string Google fonts URL for the theme.
 */
function fonts_url() {
	$fonts_url = '';
	$fonts     = array();
	$subsets   = 'latin,latin-ext';

	/*
	 * Translators: If there are characters in your language that are not supported
	 * by Noto Sans, translate this to 'off'. Do not translate into your own language.
	 */
	if ( 'off' !== _x( 'on', 'Noto Sans font: on or off', 'tmf' ) ) {
		$fonts[] = 'Noto Sans:400italic,700italic,400,700';
	}

	/*
	 * Translators: If there are characters in your language that are not supported
	 * by Noto Serif, translate this to 'off'. Do not translate into your own language.
	 */
	if ( 'off' !== _x( 'on', 'Noto Serif font: on or off', 'tmf' ) ) {
		$fonts[] = 'Noto Serif:400italic,700italic,400,700';
	}

	/*
	 * Translators: If there are characters in your language that are not supported
	 * by Inconsolata, translate this to 'off'. Do not translate into your own language.
	 */
	if ( 'off' !== _x( 'on', 'Inconsolata font: on or off', 'tmf' ) ) {
		$fonts[] = 'Inconsolata:400,700';
	}

	/*
	 * Translators: To add an additional character subset specific to your language,
	 * translate this to 'greek', 'cyrillic', 'devanagari' or 'vietnamese'. Do not translate into your own language.
	 */
	$subset = _x( 'no-subset', 'Add new subset (greek, cyrillic, devanagari, vietnamese)', 'tmf' );

	if ( 'cyrillic' == $subset ) {
		$subsets .= ',cyrillic,cyrillic-ext';
	} elseif ( 'greek' == $subset ) {
		$subsets .= ',greek,greek-ext';
	} elseif ( 'devanagari' == $subset ) {
		$subsets .= ',devanagari';
	} elseif ( 'vietnamese' == $subset ) {
		$subsets .= ',vietnamese';
	}

	if ( $fonts ) {
		$fonts_url = add_query_arg( array(
			'family' => urlencode( implode( '|', $fonts ) ),
			'subset' => urlencode( $subsets ),
		), 'https://fonts.googleapis.com/css' );
	}

	return $fonts_url;
}

function list_authors() {
	$contributor_ids = get_users( array(
		'fields'  => 'ID',
		'orderby' => 'post_count',
		'order'   => 'DESC',
		'who'     => 'authors',
	) );

	foreach ( $contributor_ids as $contributor_id ) :
		$post_count = count_user_posts( $contributor_id );

		// Move on if user has not published a post (yet).
		if ( ! $post_count ) {
			continue;
		}
	?>

	<div class="contributor">
		<div class="contributor-info">
			<div class="contributor-avatar"><?php echo get_avatar( $contributor_id, 132 ); ?></div>
			<div class="contributor-summary">
				<h2 class="contributor-name"><?php echo get_the_author_meta( 'display_name', $contributor_id ); ?></h2>
				<p class="contributor-bio">
					<?php echo get_the_author_meta( 'description', $contributor_id ); ?>
				</p>
				<a class="button contributor-posts-link" href="<?php echo esc_url( get_author_posts_url( $contributor_id ) ); ?>">
					<?php printf( _n( '%d Article', '%d Articles', $post_count, 'tmf' ), $post_count ); ?>
				</a>
			</div><!-- .contributor-summary -->
		</div><!-- .contributor-info -->
	</div><!-- .contributor -->

	<?php
	endforeach;
}





	
 }
 




/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

/**
 * Load Our Custom Widgets.
 */
require get_template_directory() . '/inc/widgets.php';

