<?php
/**
 * SVG icons related functions and filters
 *
 * @package WordPress
 * @subpackage Sea_Salt_Press
 * @since 1.0
 */


/**
 * seasaltpress_include_svg_icons function.
 *
 * @access public
 * @return void
 * Include the icons if the theme has not included its own already. Aslo include simple css for them.
 */
if ( ! file_exists( get_parent_theme_file_path( '/assets/icons/symbol-defs.svg' ) ) ) {

	function iodine_include_svg_icons() {

		$svg_icons = IODINE_PATH . 'assets/icons/symbol-defs.svg';

		// If it exists, include it.
		if ( file_exists( $svg_icons ) ) {
			require_once( $svg_icons );

		}
	}

	add_action( 'wp_footer', 'iodine_include_svg_icons', 9999 );
	add_action( 'wp_head', 'iodine_svg_icon_css' );
}

/**
 * Return SVG markup. Can be used in shortcode too.
 *
 * @param array $args {
 *     Parameters needed to display an SVG.
 *
 * @type string $icon Required SVG icon filename.
 * @type string $title Optional SVG title.
 * @type string $desc Optional SVG description.
 * }
 * @return string SVG markup.
 */
$slug = get_option( 'stylesheet' );
if ( ! function_exists( $slug . '_get_svg' ) ) {
	function iodine_get_svg( $args = array() ) {
		// Make sure $args are an array.
		if ( empty( $args ) ) {
			return __( 'Please define default parameters in the form of an array.', 'seasaltpress' );
		}

		// Define an icon.
		if ( false === array_key_exists( 'icon', $args ) ) {
			return __( 'Please define an SVG icon filename.', 'seasaltpress' );
		}

		// Set defaults.
		$defaults = array(
			'icon'     => '',
			'title'    => '',
			'desc'     => '',
			'fallback' => false,
		);

		// Parse args.
		$args = wp_parse_args( $args, $defaults );

		// Set aria hidden.
		$aria_hidden = ' aria-hidden="true"';

		// Set ARIA.
		$aria_labelledby = '';

		/*
		 * Sea Salt Press doesn't use the SVG title or description attributes; non-decorative icons are described with .screen-reader-text.
		 *
		 * However, child themes can use the title and description to add information to non-decorative SVG icons to improve accessibility.
		 *
		 * Example 1 with title: <?php echo seasaltpress_get_svg( array( 'icon' => 'arrow-right', 'title' => __( 'This is the title', 'textdomain' ) ) ); ?>
		 *
		 * Example 2 with title and description: <?php echo seasaltpress_get_svg( array( 'icon' => 'arrow-right', 'title' => __( 'This is the title', 'textdomain' ), 'desc' => __( 'This is the description', 'textdomain' ) ) ); ?>
		 *
		 * See https://www.paciellogroup.com/blog/2013/12/using-aria-enhance-svg-accessibility/.
		 */
		if ( $args['title'] ) {
			$aria_hidden     = '';
			$unique_id       = uniqid();
			$aria_labelledby = ' aria-labelledby="title-' . $unique_id . '"';

			if ( $args['desc'] ) {
				$aria_labelledby = ' aria-labelledby="title-' . $unique_id . ' desc-' . $unique_id . '"';
			}
		}

		// Begin SVG markup.
		$svg = '<svg class="icon icon-' . esc_attr( $args['icon'] ) . '"' . $aria_hidden . $aria_labelledby . ' role="img">';

		// Display the title.
		if ( $args['title'] ) {
			$svg .= '<title id="title-' . $unique_id . '">' . esc_html( $args['title'] ) . '</title>';

			// Display the desc only if the title is already set.
			if ( $args['desc'] ) {
				$svg .= '<desc id="desc-' . $unique_id . '">' . esc_html( $args['desc'] ) . '</desc>';
			}
		}

		/*
		 * Display the icon.
		 *
		 * The whitespace around `<use>` is intentional - it is a work around to a keyboard navigation bug in Safari 10.
		 *
		 * See https://core.trac.wordpress.org/ticket/38387.
		 */
		$svg .= ' <use href="#icon-' . esc_html( $args['icon'] ) . '" xlink:href="#icon-' . esc_html( $args['icon'] ) . '"></use> ';

		// Add some markup to use as a fallback for browsers that do not support SVGs.
		if ( $args['fallback'] ) {
			$svg .= '<span class="svg-fallback icon-' . esc_attr( $args['icon'] ) . '"></span>';
		}

		$svg .= '</svg>';

		return $svg;
	}
	add_shortcode( 'svg', 'iodine_get_svg' );
}
else{
	add_shortcode( 'svg', $slug . '_get_svg' );
}



/**
 * Add dropdown icon if menu item has children.
 * Seasaltpress added svg shortcode ability to titles.
 *
 * @param  string $title The menu item's title.
 * @param  object $item The current menu item.
 * @param  array  $args An array of wp_nav_menu() arguments.
 * @param  int    $depth Depth of menu item. Used for padding.
 *
 * @return string $title The menu item's title with dropdown icon.
 */
if ( ! function_exists( $slug . '_dropdown_icon_to_menu_link' ) ) {
	function iodine_dropdown_icon_to_menu_link( $title, $item, $args, $depth ) {
		if ( 'top' === $args->theme_location ) {
			foreach ( $item->classes as $value ) {
				if ( 'menu-item-has-children' === $value || 'page_item_has_children' === $value ) {
					$title = $title . ' ' . seasaltpress_get_svg( array( 'icon' => 'angle-down' ) );
				}
			}
		}

		return do_shortcode( $title );
	}

	add_filter( 'nav_menu_item_title', 'iodine_dropdown_icon_to_menu_link', 10, 4 );
}
else{
	add_filter( 'nav_menu_item_title', $slug . '_dropdown_icon_to_menu_link', 10, 4 );
}

//allow shortcodes in title
function iodine_title_shortcode( $title, $id = null ) {
	if ( ! is_admin() ) {
		return do_shortcode( $title );
	} else {
		return $title;
	}
}
add_filter( 'the_title', 'iodine_title_shortcode', 10, 2 );

// We need some CSS to position the icons
function iodine_svg_icon_css() {
	echo "<style type='text/css'>
     /* iodine icon fix */
	.icon {
		display: inline-block;
		stroke-width: 0;
	    stroke: currentColor;
		height: 1em;
		vertical-align: middle;
		width: 1em;
		position: relative;
  }

svg{
	fill: currentColor;
}	
</style>
	";
}


