<?php
/**
 * Plugin Name: Iodine
 * Description: Iodine is a necessary supplement to the theme Sea Salt Press. It enables some shortcodes including one for svg icons.
 * Version: 1.0
 * Author: Shamai Greenfield
 */

 
define( 'IODINE_PATH', plugin_dir_path( __FILE__ ) );

class Iodine_Plugin {

	protected $theme;

	/**
	 * Fired when file is loaded.
	 */
	function __construct() {
		
		add_action( 'init', array( $this, 'init' ) );
		
	}

	
	function init() {
		
		/**
		 * Add shortcodes to Widgets
		 */
		add_filter('widget_text', 'do_shortcode');
 


		/**
		 * Use a shortcode in a menu item description. This can allow you to put things like [logo] or [searchform] in description
		 */
		function shortcode_menu_description( $item_output, $item ) {
		    if ( !empty($item->description)) {
		         $output = do_shortcode($item->description);  
		         if ( $output != $item->description )
		               $item_output = $output;   
		        }
		    return $item_output;
		}
		add_filter("walker_nav_menu_start_el", "shortcode_menu_description" , 10 , 99);

		/*
		 * [searchform]
		*/
		add_shortcode('searchform', 'search_form_shortcode');
		function search_form_shortcode(){
			return get_search_form(false);
		}
		
		/*
		 * [year]
		*/
		function year_shortcode() {
		  $year = date('Y');
		  return $year;
		}
		add_shortcode('year', 'year_shortcode');

		
		
		/*
		 * [logo] 
		 * outputs logo in link in either h1 or p. Also output svg logo inline
		*/
		if( ! function_exists('seasaltpress_logo')){
		
			function seasaltpress_logo( $return_image_url = false){
				
				if ( has_custom_logo() ) {
							
					$logo = get_custom_logo();
				}
					
			 else { //no theme mod found. Get site title instead.
			 	$no_image = true;
			 	$logo = '<div class="site-title"><a href="' . esc_url( home_url( '/' ) ) . '" rel="home">' . get_bloginfo( 'name') .'
					</a></div>
					';
			
				}//theme mod 
					
				//now we output logo_output or both on customier or url for login page
					//if we are in the customizer preview get both.
					if( is_customize_preview() ){
				
						return '<div class="site-logo"><h1>' . get_custom_logo() . '<div class="site-title"><a href="' . esc_url( home_url( '/' ) ) . '" rel="home">' . get_bloginfo( 'name') .'
							</a></div>' . '</h1></div>';
					}
					
				//return logo url for use in css (like on login page) or something other than actual hmtl output of logo. Not inline though... You can use javascript to turn into svg...
					if( $return_image_url ){
						return  $no_image == true ? false : wp_get_attachment_url($logo_id);
					}
					
			
					
					if(is_front_page() && ! has_custom_logo() ){
						return '<div class="site-logo"><h1>' . $logo . '</h1></div>';
					}
					
					else{
						return '<div class="site-logo"><p>' . $logo . '</p></div>';
					}
					
			}
		}
		//whether exists or not we now allow it to be used as a shortcode
		add_shortcode('logo', 'seasaltpress_logo');
		

		
		include( plugin_dir_path( __FILE__ ) . ( 'includes/icon-functions.php' ));

		
	
	}//init

}
new Iodine_Plugin;