<?php
/**
 * Created by PhpStorm.
 * User: codearachnid
 * Date: 5/11/14
 * Time: 8:38 PM
 */

class WP_Script_Require {

    private $path = null;

    function __construct(){
        $this->path = plugin_dir_path( __FILE__ );
    }

    function get_config(){
        $requireJS = sprintf('require.config({ paths: %s });', json_encode( $require->config->paths ) );
        return $requireJS;
    }

} 