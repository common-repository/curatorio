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
        $html = $widget->render ($atts);

		return apply_filters( 'wp-shortcode-curator-feed', $this->sanitize_output( $html ) );
	}

	public function sanitize_output( $buffer ) {
		$search = array(
			'/\>[^\S ]+/s',  // strip whitespaces after tags, except space
			'/[^\S ]+\</s',  // strip whitespaces before tags, except space
			'/(\s)+/s',       // shorten multiple whitespace sequences
			"/\r/",
			"/\n/",
			"/\t/",
			'/<!--[^>]*>/s',
		);

		$replace = array(
			'>',
			'<',
			'\\1',
			'',
			'',
			'',
			'',
		);

		$buffer = preg_replace( $search, $replace, $buffer );
		return $buffer;
	}
}
endif;