<?php
if ( !class_exists( 'CuratorShortcode' ) ) :

class CuratorShortcode {
	private $shortcodes = array (
		'curator',
	);

	public function __construct() {
		add_shortcode( 'curator', array( $this, 'curator_feed') );
	}

	public function curator_feed( $atts ) {
    $widget = new CuratorFeed();
    $html = wp_kses($widget->render($atts), $widget->allowed_html);
		return apply_filters( 'wp-shortcode-curator-feed', $html);
	}
}
endif;