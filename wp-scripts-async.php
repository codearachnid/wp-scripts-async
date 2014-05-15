<?php
/*
Plugin Name: WordPress Scripts Async
Plugin URI:
Description: Extending wp_enqueue_script to use RequireJS for asynchronous loading
Version: 0.1
Author: Timothy Wood (@codearachnid)
Author URI: http://codearachnid.com
Text Domain: wp-scripts-async
*/


global $wp_scripts_async;

add_action( 'plugins_loaded', 'wp_scripts_async_init' );
function wp_scripts_async_init(){
    global $wp_scripts_async;
    $wp_scripts_async = new WP_Scripts_Async;
}

add_action( 'wp_loaded', 'wp_scripts_async_load_textdomain' );
function wp_scripts_async_load_textdomain() {
	load_plugin_textdomain( 'wp-scripts-async', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}


if( !class_exists( 'WP_Scripts_Async' ) ) {
	class WP_Scripts_Async extends WP_Scripts {

		private $path = null;

		function __construct() {
			$this->path = plugin_dir_path( __FILE__ );
			add_action( 'init', array( $this, 'include_dependancies' ) );
			add_action( 'wp_print_scripts', array( $this, 'intercept_print_scripts' ), 9999 );
		}

		function include_dependancies(){
			do_action( 'WP_Scripts_Async/include_dependancies' );
            wp_enqueue_script( 'debug-requirejs', plugins_url( 'debug.js', __FILE__ ), array(), '2.1.11' );
//			add_filter( 'print_head_scripts', '__return_false', 9999 );
//			add_filter( 'print_footer_scripts', '__return_false', 9999 );

			include $this->path . 'wp-script-require.php';
		}

		function register_profile(){
			global $wp_scripts;
			$wp_script_require = WP_Script_Require::instance();
			foreach ( $wp_scripts->registered as $wp_script ) {
				$wp_script_require->register( $wp_script->handle, $wp_script->src, $wp_script->deps );
			}
		}

		function register_queue(){
			global $wp_scripts;
			WP_Script_Require::instance()->enqueue( $wp_scripts->queue );
		}

		function intercept_print_scripts() {
			global $wp_scripts;
			$wp_script_require = WP_Script_Require::instance();




			$this->register_profile();
			$this->register_queue();

			$wp_script_require->save_file_queue();

// DEBUGGING
//            echo '<br /><br /><br /><br /><br /><br /><br /><pre>';
//			echo $wp_script_require->get_config();
//            echo $wp_script_require->get_queue();
//			print_r( $wp_scripts->queue );
//			echo '</pre>';

            echo $wp_script_require->get_script();
            printf( '<script>%s</script>', $wp_script_require->get_queue() );

		}

		public static function instance() {
			if ( null == self::$_this ) {
				self::$_this = new self;
			}

			return self::$_this;
		}
	}
}
