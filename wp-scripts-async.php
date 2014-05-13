<?php
/*

Plugin Name: WordPress Scripts Async
Plugin URI:
Description: Extending wp_enqueue_script to use RequireJS for asynchronous loading
Version: 0.1
Author: Timothy Wood (@codearachnid)
Author URI: http://codearachnid.com
Text Domain: wp_require_enqueue_script

*/


global $wp_scripts_async;

add_action( 'plugins_loaded', 'wp_scripts_async_init' );
function wp_scripts_async_init(){
    global $wp_scripts_async;
    $wp_scripts_async = new WP_Scripts_Async;
}



class WP_Scripts_Async extends WP_Scripts {

//    private static $_this = null;

    function __construct(){
//        add_action('wp_enqueue_scripts', array( $this, 'enqueue_requirejs') );
        add_action('wp_print_scripts', array($this,'intercept_print_scripts'));
    }

    function intercept_print_scripts(){
        global $wp_scripts;
        echo '<hr />';
        foreach( $wp_scripts->queue as $wp_script ){

        }
        echo '<hr />';
        echo '<pre>';
        print_r($wp_scripts);
        echo '</pre>';
    }

    function enqueue_requirejs(){
        wp_enqueue_script('requirejs', '//cdnjs.cloudflare.com/ajax/libs/require.js/2.1.11/require.min.js', array(), '2.1.11' );
    }

    public static function instance() {
        if ( null == self::$_this ) {
            self::$_this = new self;
        }

        return self::$_this;
    }
} 