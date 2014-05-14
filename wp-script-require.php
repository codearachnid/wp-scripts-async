<?php

if( !class_exists( 'WP_Script_Require' ) ) {
	class WP_Script_Require {

		private static $_this = null;
		private $config = array(
			'paths' => array(),
			'deps' => array(),
			'shim'  => array()
		);
		private $queue = array();
		private $file = array(
			'path' => null,
			'uri' => null,
			'bootstrap' => 'require.config',
			'require' => 'require.min.js'
		);

		function __construct() {
			// because I like working with objects better than arrays
			$this->file = (object) $this->file;
			$this->config = (object) $this->config;
			$this->file->uri = plugins_url( '/', __FILE__ );
			$this->file->path = plugin_dir_path( __FILE__ );
		}

		public function register( $key, $src, $deps = array() ) {
            if( !empty( $src ) )
			    $this->config->paths[ $key ] = $src;
			if( ! empty( $deps ) ) {
                $this->config->deps[ $key ] = $deps;
                $this->config->shim[ $key ] = (object) array(
                    'deps' => $deps
                );
            }
		}

		public function enqueue( $queued_scripts ){
            $queued_scripts = !is_array( $queued_scripts ) ?
                array( $queued_scripts ) :
                $queued_scripts;
            $this->queue = array_merge( $this->queue, $queued_scripts );
            wp_dequeue_script( $queued_scripts );
		}

		function get_script() {
			$bootstrap = apply_filters( 'WP_Script_Require/uri/bootstrap', trailingslashit( WP_CONTENT_URL ) . $this->file->bootstrap );
			$require   = apply_filters( 'WP_Script_Require/uri/require', $this->file->uri . $this->file->require );

			return sprintf( '<script data-main="%s" src="%s"></script>', $bootstrap, $require );
		}

		public function get_config() {
			// prevent the json_decode from escaping the paths
			$path_profile = $this->encode( $this->config->paths );
            $shim_profile = $this->encode( $this->config->shim );
            // TODO explore set baseurl to cut down on config?
//			return sprintf( 'require.config({ baseUrl: "/wp-includes/js", paths: %s, shim: %s });', $path_profile, $shim_profile );
            return sprintf( 'require.config({ paths: %s, shim: %s });', $path_profile, $shim_profile );
		}

		public function get_queue(){
			global $wp_scripts;
			$deps = array();
			foreach($this->queue as $queued){
				if( !empty( $this->config->deps[ $queued ] ) )
					$deps = array_merge( $deps, $this->config->deps[ $queued ] );
			}
			$queued_scripts = $this->encode( $this->queue );
			$queued_deps = $this->encode( $deps );
			$raw_deps = implode( ',', $deps );
			return sprintf( 'require(%s, function (%s) { require(%s); });', $queued_deps, $raw_deps, $queued_scripts );
		}

		function save_file_queue(){

            if( !is_admin() )
                return false;

			global $wp_filesystem;

			// protect if the the global filesystem isn't setup yet
			if( is_null( $wp_filesystem ) )
				WP_Filesystem();

            return $wp_filesystem->put_contents(
				apply_filters( 'WP_Script_Require/path/bootstrap', trailingslashit( WP_CONTENT_DIR ) . $this->file->bootstrap . '.js' ),
				$this->get_config(),
				FS_CHMOD_FILE
			);
		}

		private function encode( $data ){
			// check php version to determine best method to json_encode without escaped slashes
			return version_compare( PHP_VERSION, '5.2.4', '>=' ) ?
				json_encode( $data, JSON_UNESCAPED_SLASHES ) :
				str_replace( '\/', '/', json_encode( $data ) );
		}


		/**
		 * Static Singleton Factory Method
		 *
		 * @return static $_this instance
		 * @readlink http://eamann.com/tech/the-case-for-singletons/
		 */
		public static function instance() {
			if ( null == self::$_this )
				self::$_this = new self;

			return self::$_this;
		}

	}
}