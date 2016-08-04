<?php
/*
Plugin Name: Scribbler
Plugin URI: http://michiel.wordpress.com
Description: This plugin allows you to animate handwritten text
Version: 1.0
Author: Michiel van der Blonk
Author URI: http://michiel.wordpress.com
Author Email: blonkm@gmail.com
*/


/*
Copyright (c) 2016 Michiel van der Blonk

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

// Stop direct call
if ( preg_match( '#' . basename( __FILE__ ) . '#', $_SERVER['PHP_SELF'] ) ) {
    die( 'You are not allowed to call this page directly.' );
}

if ( !class_exists( 'Scribbler' ) ) {
    class Scribbler
    {

        // Constructor
        function __construct()
        {
            $this->plugin_name = basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ );

            // Activate for New Installs
            register_activation_hook( $this->plugin_name, array( &$this, 'activate' ) );

            // Activate for Updates
            add_action( 'plugins_loaded', array( &$this, 'activate' ) );

            // Add the script and style files
            add_action( 'wp_enqueue_scripts', array( &$this, 'load_resources' ) );

			add_action( 'admin_menu', array(&$this, 'add_admin_menu') );
			add_action( 'admin_init', array(&$this, 'settings_init') );
			add_filter( 'the_content', array(&$this, 'print_canvas'));
			//add_action( 'wp_content', array(&$this, 'run'));    
		}

        // Add scripts and styles
        function load_resources()
        {
            //if ( is_admin() ) return; else echo "3.14";

            // Scripts
            $cdn = 'https://cdnjs.cloudflare.com/ajax/libs/';
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script( 'cufon', $cdn . 'cufon/1.09i/cufon-yui.min.js');
            wp_enqueue_script( 'raphael', $cdn . '/raphael/2.2.1/raphael.js');

            wp_enqueue_script( 'scribbler_font', plugins_url( '/Tangerine_400.font.js', __FILE__ ) );
            wp_enqueue_script( 'scribbler_js', plugins_url( '/scribbler.js', __FILE__ ) );

            // Styles
            wp_enqueue_style( 'scribbler_css', plugins_url( '/scribbler.css', __FILE__ ) );

        }

		function add_admin_menu(  ) { 
			add_options_page( 'Scribbler', 'Scribbler', 'manage_options', 'scribbler', array(&$this, 'options_page') );
		}

		function settings_init(  ) { 
			register_setting( 'pluginPage', 'wpscr_settings' );
			add_settings_section(
				'wpscr_pluginPage_section', 
				__( 'Options', 'wordpress' ), 
				array(&$this, 'settings_section_callback'), 
				'pluginPage'
			);
			add_settings_field( 
				'wpscr_text', 
				__( 'Text', 'wordpress' ), 
				array(&$this, 'text_render'), 
				'pluginPage', 
				'wpscr_pluginPage_section' 
			);
		}

		function text_render(  ) { 
			$options = get_option( 'wpscr_settings' );
			?>
			<input type='text' name='wpscr_settings[wpscr_text]' value='<?php echo $options['wpscr_text']; ?>'>
			<?php
		}

		function settings_section_callback(  ) {  
			echo __( 'Here you can change the text to be animated', 'wordpress' );
		}

		function options_page(  ) { 
			?>
			<form action="options.php" method="post">

				<h2>Scribbler</h2>

				<?php
				settings_fields( 'pluginPage' );
				do_settings_sections( 'pluginPage' );
				submit_button();
				?>
			</form>
			<?php
		}

		function print_canvas($the_content) {
			if (is_front_page()) {
				$options = get_option( 'wpscr_settings' );
				$text = $options['wpscr_text'];
				return $the_content . '<div id="pen" data-text="'.$text.'"></div>';
			}
			else
				return $the_content;
		}
	}
}
if ( class_exists( 'Scribbler' ) ) {
    global $scribbler;
    $scribbler = new Scribbler();
}