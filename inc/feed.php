<?php
if ( !class_exists( 'CuratorFeed' ) ) :

class CuratorFeed {

    public $DEMO_FEED_ID = "8558f0f9-043f-4bd9-bad1-037cf10a";
    public $options;
    public $args = [];
    public $feed_id = '';
    public $defaultOptions = [
        'powered_by' => 0
    ];
    public $allowed_html = array(
      'div' => array(
        'a' => array('href' => array(), 'target' => array(), 'class' => array()),
        'id' => array(), 'data-crt-feed-id' => array(), 'data-crt-source' => array()
      ),
    );

    public function __construct() {

        $options = get_option( 'curator_options' );

        if (!is_array($options)) {
            $options = [];
        }

        $this->options = array_merge($this->defaultOptions, $options);
        add_action( 'wp_footer', array( $this, 'curator_feed_js') );
    }

    /**
     * @param array $args
     * @return string
     */
    public function render($args = [])
    {
      if (!is_array($args)) {
          $args = [];
      }

      $this->args = array_merge($this->args, $args);
      $this->setFeed();

      $html = '<div id="curator-feed-default" data-crt-feed-id="'.$this->feed_id.'" data-crt-source="wordpress-plugin">';
      if ($this->options['powered_by']) {
          $html .= '<a href="https://curator.io" target="_blank" class="crt-logo">Powered by Curator.io</a>';
      }
      $html .= '</div>';

      return $html;
    }

    public function curator_feed_js()
    {
      $html = '<script>';
      $html .= '(function(){';
      $html .= 	'var i, e, d = document, s = "script";i = d.createElement("script");i.async = 1;';
      $html .= 	'i.src = "https://cdn.curator.io/published/'.$this->feed_id.'.js";';
      $html .= 	'e = d.getElementsByTagName(s)[0];e.parentNode.insertBefore(i, e);';
      $html .= '})();';
      $html .= '</script>';
      echo wp_kses($html, array('script' => array()));
    }

    private function setFeed()
    {
        if (!empty($this->args['feed_id'])) {
            $this->feed_id = $this->args['feed_id'];
        } else if (!empty($this->args['feed_public_key'])) {
            $this->feed_id = $this->args['feed_public_key'];
        } else if (isset($this->options) && !empty($this->options['default_feed_id'])) {
            $this->feed_id = $this->options['default_feed_id'];
        } else {
            $this->feed_id = $this->DEMO_FEED_ID;
        }
    }
}

endif;
