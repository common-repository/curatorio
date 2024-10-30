<?php
/**
 *  Plugin Name: Curator.io
 *  Plugin URI: https://curator.io/wordpress-plugin/
 *  Description: A free social media wall and post aggregator which pulls together all your media channels in a brandable feed that can be embedded anywhere.
 *  Author: Thomas Garrood
 *  Version: 1.9
 *  Text Domain: curator.io
 *  @since 1.1
 */

if ( !class_exists('CuratorPlugin') ) :

class CuratorPlugin {

	public $dir;
	public $uri;
	public $temp_uri;
	public $stylesheet_dir;
	public $stylesheet_uri;
	public $shortcode;
	public $settings;
	public $version;

	public function __construct() {
		$this->define_constants();
		$this->includes();

		$this->dir = WP_DIR;
		$this->uri = WP_URI;
		$this->temp_uri = WP_TEMP_URL;
		$this->stylesheet_dir = WP_STYLESHEET_DIR;
		$this->stylesheet_uri = WP_STYLESHEET_URL;

		$this->version = '1.0.0';

		// load include files
		$this->shortcode = new CuratorShortcode();
		if( is_admin() ) {
			$this->settings = new CuratorSettings();
		}

        add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'plugin_settings_link' ) );
	}

	public static function instance() {
		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new self();
		}

		return $instance;
	}

	public function includes() {
		require_once WP_DIR . 'inc/feed.php';
		require_once WP_DIR . 'inc/settings.php';
		require_once WP_DIR . 'inc/shortcode.php';
	}

	public function define_constants() {
		$defines = array(
			'WP_DIR' => plugin_dir_path( __FILE__ ),
			'WP_URI' => plugin_dir_url( __FILE__ ),
			'WP_TEMP_URL' => trailingslashit( get_template_directory_uri() ),
			'WP_STYLESHEET_DIR' => trailingslashit( get_stylesheet_directory() ),
			'WP_STYLESHEET_URL' => trailingslashit( get_stylesheet_directory_uri() ),
		);

		foreach( $defines as $k => $v ) {
			if ( !defined( $k ) ) {
				define( $k, $v );
			}
		}
	}

	public function init() {

	}

    function plugin_settings_link($links) {
        $url = get_admin_url() . 'admin.php?page=curator-settings';
        $settings_link = '<a href="'.$url.'">' . __( 'Settings', 'curator' ) . '</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }
}

$GLOBALS['CuratorPlugin'] = CuratorPlugin::instance();

function curator_feed($args = '')
{
    if (!is_array($args)) {
        $args = [
            'feed_id' => $args
        ];
    }

    $widget = new CuratorFeed();
    echo $widget->render ($args);
}

function curator_add_admin_class() {
    echo '<script type="text/javascript">
		jQuery(function($){
            $("#toplevel_page_curator-settings").find("img").css("width","18px");
        });
    </script>';
}

add_action('admin_footer', 'curator_add_admin_class');

endif;
