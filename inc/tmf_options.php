<?php
/**
 * The Options Handler
 *
 * This class adds customizer options,
 * Displays an options page
 * Retrieves user's options
 *
 * @package TMF
 * @since TMF 1.0
 */

 class tmf_options {
	public $color_scheme ; //name of current color scheme
	public $color_scheme_colors = array(); //an array of colors contained in the current color scheme
	public $layout = array();
	
 
	function __construct() {
	
		$this->color_scheme = get_theme_mod( 'color_scheme', 'default' );
		
		$this->color_scheme_colors = $this->get_color_scheme();
		
		$this->layout = get_theme_mod( 'main_layout', 'default' );
				
		add_action( 'admin_menu', array ( $this , 'add_theme_page' ));
		
		add_action( 'customize_register', array ( $this , 'customize_register' ));
		
		add_action( 'customize_preview_init', array ( $this, 'customize_preview_js' ));
		
		add_action( 'wp_enqueue_scripts',array ( $this, 'color_scheme_css' ));
		
		add_action( 'wp_enqueue_scripts', array($this,'header_background_color_css'), 11 );
		
		add_action( 'customize_controls_print_footer_scripts', array($this,'color_scheme_css_template') );
		
		add_action( 'customize_controls_enqueue_scripts', array($this, 'customize_control_js') );
		


		
		
		
	}
	

	/**
	 * Add postMessage support for site title and description for the Theme Customizer.
	 *
	 *
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	*/
	function customize_register( $wp_customize ) {
	$color_scheme = $this->get_color_scheme();

	$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	// Add a new section where TMF specific settings will be inserted
	$wp_customize->add_section('tmf_options',
		array(
				'title' => __('TMF options','tmf'),
				'priority' => 35,
			)
	);
	

	// Add Footer setting and control.
	$wp_customize->add_setting( 'footer_text', array(
		'default'           => __('Powered by <a href="https://wordpress.org"> Wordpress</a> And <a href="https://tmftheme.com">The Modern Framework.</a>',
		'tmf'),
		'sanitize_callback' => array($this,
									'sanitize_footer_text'),
	) );

	$wp_customize->add_control( 'footer_text', array(
		'label'    => __( 'The Footer Text', 'tmf' ),
		'section'  => 'tmf_options',
		'priority' => 100,
	) );
	
	// Add Layout setting and control.
	$wp_customize->add_setting( 'main_layout', array(
		'default'           => 'default',
		'sanitize_callback' => array($this, 'sanitize_layout'),
	) );

	$wp_customize->add_control( 'main_layout', array(
		'label'    => __( 'Main Layout', 'tmf' ),
		'section'  => 'tmf_options',
		'type'     => 'radio',
		'choices'  => $this->get_layout_choices(),
		'priority' => 1,
	) );
	
	// Add color scheme setting and control.
	$wp_customize->add_setting( 'color_scheme', array(
		'default'           => 'default',
		'sanitize_callback' => array($this,
									'sanitize_color_scheme'),
	) );

	$wp_customize->add_control( 'color_scheme', array(
		'label'    => __( 'Base Color Scheme', 'tmf' ),
		'section'  => 'colors',
		'type'     => 'select',
		'choices'  => $this->get_color_scheme_choices(),
		'priority' => 1,
	) );


	}
	
	/*
	* A couple of helper functions to help us sanitize the user options.
	*
	*/
	
	function sanitize_featured_content($option) {
		$option = trim($option);
		if($option == 'grid')
			return 'grid';
		return 'slider';
	}
	
	function sanitize_footer_text( $option ) {
	
		return trim($option);
		
	}
	
	function sanitize_layout( $option ) {
		$layouts = $this->get_layout_choices();

		if ( ! array_key_exists( $option, $layouts ) ) {
			$option = 'default';
		}

		return $option;
	}
	
	function sanitize_color_scheme( $option ) {
		$color_schemes = $this->get_color_scheme_choices();

		if ( ! array_key_exists( $option, $color_schemes ) ) {
			$option = 'default';
		}

		return $option;
	}
	/*
	 * gets the colors contained in the current color scheme
	 */
	
	function get_color_scheme() {
		$color_schemes       = $this->get_color_schemes();
		$current = get_theme_mod( 'color_scheme', 'default' );

		if ( array_key_exists( $current, $color_schemes ) ) {
			$this->color_scheme_colors = $color_schemes[ $current ]['colors'];
			return $color_schemes[ $current ]['colors'];
		}

		$this->color_scheme_colors = $color_schemes['default']['colors'];
		return $color_schemes['default']['colors'];
	}
	
	/**
	 * Register color schemes for Twenty Fifteen.
	 *
	 * Can be filtered with {@see 'tmf_color_schemes'}.
	 *
	 * The order of colors in a colors array:
	 * 1. Main Background Color.
	 * 2. Header Background Color.
	 * 3. Content/Box Background Color.
	 * 4. Main Text and Link Color.
	 * 5. Header Text and Link Color.
	 * 6. Link Color
	 * 7. Header Text COlor
	 *
	 * @since TMF 1.0
	 *
	 * @return array An associative array of color scheme options.
	 */
	
	function get_color_schemes() {
	
	return apply_filters( 'tmf_color_schemes', array(
		'default' => array(
			'label'  => __( 'Default', 'tmf' ),
			'colors' => array(
				'#E0E0E0',
				'#23282d',
				'#ffffff',
				'#23282d',
				'#ffffff',
				'#55c3dc',
				'#23282d',
			),
		),
		'brown' => array(
			'label'  => __( 'Brown', 'tmf' ),
			'colors' => array(
				'#554447',
				'#271B1D',
				'#FCE5E8',
				'#23282d',
				'#ffffff',
				'#55c3dc',
				'#271B1D',
			),
		),
		'dark'    => array(
			'label'  => __( 'Dark', 'tmf' ),
			'colors' => array(
				'#111111',
				'#202020',
				'#202020',
				'#bebebe',
				'#bebebe',
				'#55c3dc',
				'#111111',
			),
		),
		'yellow'  => array(
			'label'  => __( 'Yellow', 'tmf' ),
			'colors' => array(
				'#f4ca16',
				'#ffdf00',
				'#FDFDBD',
				'#111111',
				'#ffffff',
				'#0955F5',
				'#55c3dc',
			),
		),
		'pink'    => array(
			'label'  => __( 'Pink', 'tmf' ),
			'colors' => array(
				'#F8C3F4',
				'#D60CC6',
				'#F5D4F2',
				'#352712',
				'#ffffff',
				'#D60CC6',
				'#D60CC6',
			),
		),
		'purple'  => array(
			'label'  => __( 'Purple', 'tmf' ),
			'colors' => array(
				'#9066CF',
				'#2C1E5A',
				'#EBD7FD',
				'#2e2256',
				'#ffffff',
				'#2C1E5A',
				'#501F99',
			),
		),
		'green'  => array(
			'label'  => __( 'Green', 'tmf' ),
			'colors' => array(
				'#B3F8C1',
				'#0C8825',
				'#D7F8DB',
				'#2e2256',
				'#ffffff',
				'#0C8825',
				'#0C8825',
			),
		),
		'gray'  => array(
			'label'  => __( 'Gray', 'tmf' ),
			'colors' => array(
				'#e2e1de',
				'#414642',
				'#ffffff',
				'#1D1C1C',
				'#ffffff',
				'#55c3dc',
				'#414642',
			),
		),
		'red'  => array(
			'label'  => __( 'Red', 'tmf' ),
			'colors' => array(
				'#EEACB4',
				'#DD0D0D',
				'#FFE0E2',
				'#2e2256',
				'#ffffff',
				'#AD0E0E',
				'#B41B2E',
			),
		),
		'orange'  => array(
			'label'  => __( 'Orange', 'tmf' ),
			'colors' => array(
				'#FAC1C1',
				'#B42D0B',
				'#F8D8CF',
				'#2e2256',
				'#ffffff',
				'#B42D0B',
				'#B30A03',
			),
		),
		'blue'   => array(
			'label'  => __( 'Blue', 'tmf' ),
			'colors' => array(
				'#C0DDF5',
				'#55c3dc',
				'#DCF2F7',
				'#22313f',
				'#ffffff',
				'#55c3dc',
				'#55c3dc',
			),
		),
	) );
	
	}
	
	/*
	 * Returns an array of color scheme labels to be shown in the theme customizer
	 * @return array
	 * @since TMF 1.0
	 */
	
	function get_color_scheme_choices() {
		$color_schemes                = $this->get_color_schemes();
		$color_scheme_control_options = array();

		foreach ( $color_schemes as $color_scheme => $value ) {
			$color_scheme_control_options[ $color_scheme ] = $value['label'];
		}

		return $color_scheme_control_options;
	}

	
	/**
	 * Returns CSS for the color schemes.
	 *
	 * @since TMF 1.0
	 *
	 * @param array $colors Color scheme colors.
	 * @return string Color scheme CSS.
	 */
	function get_color_scheme_css( $colors ) {
		$colors = wp_parse_args( $colors, array(
			'background_color'            => '',
			'header_background_color'     => '',
			'box_background_color'        => '',
			'textcolor'                   => '',
			'secondary_textcolor'         => '',
			'border_color'                => '',
			'border_focus_color'          => '',
			'header_textcolor'           => '',
			'sidebar_border_color'        => '',
			'sidebar_border_focus_color'  => '',
			'secondary_header_textcolor' => '',
			'link_color'   => '',
			'header_textcolor2'           => '',
		) );
		$rgb = $this->hex2rgb($colors['header_background_color']);
		$rgb_10=$rgb['r'] .',' . $rgb['g'] .',' . $rgb['b'] . ',1.0';
		$rgb_9=$rgb['r'] .',' . $rgb['g'] .',' . $rgb['b'] . ',0.9';
		$rgb_8=$rgb['r'] .',' . $rgb['g'] .',' . $rgb['b'] . ',0.8';
		$rgb_75=$rgb['r'] .',' . $rgb['g'] .',' . $rgb['b'] . ',0.75';
		$rgb_7=$rgb['r'] .',' . $rgb['g'] .',' . $rgb['b'] . ',0.7';
		$rgb_5=$rgb['r'] .',' . $rgb['g'] .',' . $rgb['b'] . ',0.5';
		$rgb_4=$rgb['r'] .',' . $rgb['g'] .',' . $rgb['b'] . ',0.4';
		$rgb_3= $rgb['r'] .',' . $rgb['g'] .',' . $rgb['b'] . ',0.3';
		$rgb_25=$rgb['r'] .',' . $rgb['g'] .',' . $rgb['b'] . ',0.25';
		$rgb_2=$rgb['r'] .',' . $rgb['g'] .',' . $rgb['b'] . ',0.2';
		$rgb_1=$rgb['r'] .',' . $rgb['g'] .',' . $rgb['b'] . ',0.1';
		$rgb_075=$rgb['r'] .',' . $rgb['g'] .',' . $rgb['b'] . ',0.075';
		$rgb_05=$rgb['r'] .',' . $rgb['g'] .',' . $rgb['b'] . ',0.05';
		$rgb_025=$rgb['r'] .',' . $rgb['g'] .',' . $rgb['b'] . ',0.025';
		$rgb_01=$rgb['r'] .',' . $rgb['g'] .',' . $rgb['b'] . ',0.01';
		$link = $this->hex2rgb($colors['link_color']);
		$link_6=$link['r'] .',' . $link['g'] .',' . $link['b'] . ',0.6';
		$link_75=$link['r'] .',' . $link['g'] .',' . $link['b'] . ',0.75';
		$home_page = get_theme_mod('homepage_bg_image');

	$css = <<<CSS
	/* Color Scheme */

	/* Body And Its Elements */
	body {
		background-color: {$colors['background_color']};
		color: {$colors['textcolor']};
	}
	
	.home-page {
		background-image: url({$home_page});
	}
	
	a {
		color: {$colors['link_color']};
	}
	
	a:visited {
		color: rgba({$link_75});
	}
	
	a:hover,
	a:active {
		color: rgba({$link_6});
	}

	/* Header And Footer */
	#masthead,
	.main-navigation ul ul,
	#colophon {
		background-color: {$colors['header_background_color']};
		color: {$colors['header_textcolor']};
	}
	/* Child Themes that don't need the header colored just add a class="home-page" to your header etc */
	.home-page #masthead,
	.home-page #colophon {
		background-color: transparent;
	}
	
	#colophon a{
		color: #ccc;
	}
	
	.site-title a,
	.site-title a:hover,
	.site-title a:visited,
	.main-navigation a,
	.main-navigation a:visited,
	.main-navigation a:hover,
	.secondary-navigation a,
	.secondary-navigation a:hover,
	.secondary-navigation a:visited{
		color: {$colors['header_textcolor']};
	}

	/* Content */
	.entry-title a {
	color: rgba({$rgb_9});
	}
	
	.entry-meta a, .entry-footer a {
	color: #767676;
	}
	
	.entry-footer {
	border-bottom: 2px solid rgba({$rgb_2});
	}

	/* Box/Content Background Color */
	.content-area,
	.widget,
	.post-navigation,
	.pagination,
	.hentry,
	.page-header,
	.page-content,
	.comments-area,
	.widecolumn {
		background-color: {$colors['box_background_color']};
	}
	
	
	@media screen and (min-width: 710px) {
	.main-navigation ul ul {
	background: rgba({$rgb_7});
	}
	
	.site-title a,
	.site-title a:hover,
	.site-title a:visited,
	.site-description,
	.secondary-navigation a,
	.secondary-navigation a:hover,
	.secondary-navigation a:visited
	{
		color: {$colors['header_textcolor2']};
	}
	
	
	}


CSS;

	return $css;
}

	/**
	 * Enqueues front-end CSS for color scheme.
	 *
	 * @since TMF 1.0
	 *
	 * @see wp_add_inline_style()
	 */
function color_scheme_css() {
	
	$color_scheme = $this->get_color_scheme();
	
	// Convert main and sidebar text hex color to rgba.
	$color_textcolor_rgb         = $this->hex2rgb( $color_scheme[3] );
	$color_header_textcolor_rgb = $this->hex2rgb( $color_scheme[4] );
	$colors = array(
		'background_color'            => $color_scheme[0],
		'header_background_color'     => $color_scheme[1],
		'box_background_color'        => $color_scheme[2],
		'textcolor'                   => $color_scheme[3],
		'header_textcolor'           => $color_scheme[4],
		'link_color'   				 => $color_scheme[5],
		'header_textcolor2'   		=> $color_scheme[6],
	);

	$color_scheme_css = $this->get_color_scheme_css( $colors );

	wp_add_inline_style( 'tmf-style', $color_scheme_css );
}

	/**
	 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
	 */
	function customize_preview_js() {
		wp_enqueue_script( 'tmf_customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20130508', true );
		
	}
	
	/**
 * Binds JS listener to make Customizer color_scheme control.
 *
 * Passes color scheme data as colorScheme global.
 *
 * @since Twenty Fifteen 1.0
 */
function customize_control_js() {
	wp_enqueue_script( 'color-scheme-control', get_template_directory_uri() . '/js/color-scheme-control.js', array( 'customize-controls', 'iris', 'underscore', 'wp-util' ), '20141216', true );
	wp_localize_script( 'color-scheme-control', 'colorScheme', $this->get_color_schemes() );
}

	function add_theme_page() {
		add_theme_page('The Modern Framework', 'The Modern Framework', 'manage_options','tmf_theme_page', array($this, 'show_theme_page'));
	}
	
	function show_theme_page() {
		if( !current_user_can('manage_options'))
			wp_die(__('You do not sufficient permissions to access this page.','tmf'));
?>
		<?php if( isset( $_POST['mt_submitted']) && $_POST['mt_submitted']== 'Y') { //form submitted
	
			//Save the posted values and add a settings saved message on the screen
			$this->options = array_merge($this->options,$_POST);
			update_option('tmf_options', $this->options );
?>		
			<div class="updated">
				<p><strong> <?php _e('Settings Saved', 'tmf');?></strong></p>
			</div>
<?php
		} //End if
?>
<div class="wrap">
	<h2>The Modern FrameWork - TMF</h2>
	<div class="tc-box">
		<p>Congratulations for making the right decision. TMF is here to stay, and as a reward, here are a couple of resources to help you get started.</p>
		<ul>
		<li>
			Make sure to visit the <a href="<?php echo esc_url( home_url( '/' ) ); ?>wp-admin/customize.php?return=%2Fwp-admin%2Fthemes.php%3Fpage%3Dtmf_theme_page">theme customizer </a>so as to turn the theme into something even more beautiful. Here are some of the things that you will be able to change.
			<ol>
				<li>The Footer Text </li>
				<li>The layout option, left or right sidebar. Or none. </li>
				<li>The color scheme, we have a lot of options, you can also create your own color scheme. </li>
				<li>The header and background images. </li>
				<li>Custom code to insert into the header or footer. (Tip: This can be your google analytics code and google console code.) </li>
				<li>Your google+ link(can be used in posts to tell google that your are the author of the article.) </li>
				<li>Your Social Media Links (will be needed if you enable the social links widget.)</li>
			</ol>
		</li>
		
		<li>
		<h2>A Couple Of Articles To Help You Make The Most Of WordPress</h2>
			<ol>
			<li>10 Plugins that every WordPress Blog should use. Plus they are all free.</li>
			<li>50 ways to speed up your WordPress Blog. Google loves fast loading sites, and slow loading websites reduce your conversion rate.</li>
			<li>How often should you post?</li>
			<li>Long Vs Short posts? Ask the experts.</li>
			<li>New to blogging? Forget Google!</li>
			</ol>
			
		</li>
		</ul>
	</div>
		
	
</div>
		

<?php
	}


	function get_layout_choices() {
		$layouts                = $this->get_layouts();
		$layout_control_options = array();

		foreach ( $layouts as $layout => $value ) {
			$layout_control_options[ $layout ] = $value['label'];
		}

		return $layout_control_options;
	}
	
		function get_layout_class() {
		$layout_option = get_theme_mod( 'main_layout', 'default' );
	
		$layouts = $this->get_layouts();
		
		if ( array_key_exists( $layout_option, $layouts ) ) {
			return $layouts[ $layout_option ]['class'];
		}
		
		return $layouts['default']['class'];

	}
	
	function get_layouts() {
	
	return apply_filters( 'tmf_layouts', array(
		'default' => array(
			'label'  => __( 'Default', 'tmf' ),
			'order' => array(
				'body',
				'sidebar-1',
			),
			'class' => 'default',
		),
		'left-sidebar' => array(
			'label'  => __( 'Left Sidebar', 'tmf' ),
			'order' => array(
				'body',
				'sidebar-1',
			),
			'class' => 'left-sidebar',
		),
		
		'no-sidebar' => array(
			'label'  => __( 'No Sidebar', 'tmf' ),
			'order' => array(
				'body',
				'sidebar-1',
			),
			'class' => 'no-sidebar',
		)
	) );
	
	}
	
	/**
 * Convert HEX to RGB.
 *
 * @since Twenty Fifteen 1.0
 *
 * @param string $color The original color, in 3- or 6-digit hexadecimal form.
 * @return array Array containing RGB (red, green, and blue) values for the given
 *               HEX code, empty array otherwise.
 */
function hex2rgb( $color ) {
	$color = trim( $color, '#' );

	if ( strlen( $color ) == 3 ) {
		$r = hexdec( substr( $color, 0, 1 ).substr( $color, 0, 1 ) );
		$g = hexdec( substr( $color, 1, 1 ).substr( $color, 1, 1 ) );
		$b = hexdec( substr( $color, 2, 1 ).substr( $color, 2, 1 ) );
	} else if ( strlen( $color ) == 6 ) {
		$r = hexdec( substr( $color, 0, 2 ) );
		$g = hexdec( substr( $color, 2, 2 ) );
		$b = hexdec( substr( $color, 4, 2 ) );
	} else {
		return array();
	}

	return array( 'r' => $r, 'g' => $g, 'b' => $b );
}

/**
 * Output an Underscore template for generating CSS for the color scheme.
 *
 * The template generates the css dynamically for instant display in the Customizer
 * preview.
 *
 * @since TMF 1.0
 */
function color_scheme_css_template() {
	$colors = array(
		'background_color'            => '{{ data.background_color }}',
		'header_background_color'     => '{{ data.header_background_color }}',
		'box_background_color'        => '{{ data.box_background_color }}',
		'textcolor'                   => '{{ data.textcolor }}',
		'secondary_textcolor'         => '{{ data.secondary_textcolor }}',
		'border_color'                => '{{ data.border_color }}',
		'border_focus_color'          => '{{ data.border_focus_color }}',
		'header_textcolor'           => '{{ data.header_textcolor }}',
		'sidebar_border_color'        => '{{ data.sidebar_border_color }}',
		'sidebar_border_focus_color'  => '{{ data.sidebar_border_focus_color }}',
		'secondary_sidebar_textcolor' => '{{ data.secondary_sidebar_textcolor }}',
		'meta_box_background_color'   => '{{ data.meta_box_background_color }}',
	);
	?>
	<script type="text/html" id="tmpl-tmf-color-scheme">
		<?php echo $this->get_color_scheme_css( $colors ); ?>
	</script>
	<?php
}

/**
 * Enqueues front-end CSS for the header background color.
 *
 * @since Twenty Fifteen 1.0
 *
 * @see wp_add_inline_style()
 */
function header_background_color_css() {
	$color_scheme            = $this->get_color_scheme();
	$default_color           = $color_scheme[1];
	$header_background_color = get_theme_mod( 'header_background_color', $default_color );

	// Don't do anything if the current color is the default.
	if ( $header_background_color === $default_color ) {
		return;
	}

	$css = '
		/* Custom Header Background Color */

		@media screen and (min-width: 710px) {
			.site-branding {
				background-color: %1$s;
			}

		}
	';

	wp_add_inline_style( 'tmf-style', sprintf( $css, $header_background_color ) );
}


}
