<?php
if ( !class_exists( 'CuratorSettings' ) ) :

class CuratorSettings {

    public $TEST_FEED_ID = "8558f0f9-043f-4bd9-bad1-037cf10a";

	public function __construct(){
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
	}

	// Create admin menus for backend
	public function admin_menu(){
		// This page will be under "Settings"
		/*
		add_options_page(
			'Curator',
            'Curator',
            'manage_options',
            'curator-feed',
            array( $this, 'render' )
		);
		*/

		// add top level menu page
		add_menu_page(
			'Curator',
            'Curator',
            'manage_options',
            'curator-settings',
            array( $this, 'create_admin_page' ),
            WP_URI . 'images/Curator_Logomark2.svg',
            80
        );
	}

	/**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'curator_options' );
        ?>
        <div class="wrap">
            <h1>Curator <a href="https://curator.io" class="page-title-action" target="_blank">Go to Curator Site</a> <a href="https://app.curator.io" class="page-title-action" target="_blank">Go to Curator Dashboard</a></h1>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'curator_options' );
                do_settings_sections( 'curator-settings' );
                if (isset($_GET['tab']) && sanitize_text_field(wp_unslash($_GET['tab'])) === 'settings') {
                    submit_button();
                }
            ?>
            </form>
        </div>
        <?php
	}
	/**
     * Register and add settings
     */
    public function page_init()
    {
        register_setting(
            'curator_options', // Option group
            'curator_options', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            '', // Title
            array( $this, 'print_section_info' ), // Callback
            'curator-settings' // Page
        );
	}

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     * @return array
     */
    public function sanitize( $input )
    {
        $validOptions = [
            'default_feed_id',
            'powered_by',
        ];

        $new_input = array();
        foreach ($validOptions as $option) {
            if (isset($input[$option])) {
                $new_input[$option] = sanitize_text_field($input[$option]);
            }
        }

        return $new_input;
	}

	/**
     * Print the Section text
     */
    public function print_section_info()
    {
      $tabs = array(
        'setup' => 'Setup',
        'usage' => 'Usage',
        'settings' => 'Settings',
      );

      $current_tab = isset( $_GET['tab'] ) && isset( $tabs[$_GET['tab']] ) ? sanitize_text_field(wp_unslash($_GET['tab'])) : array_key_first( $tabs );
      ?>
        <nav class="nav-tab-wrapper">
          <?php
          foreach( $tabs as $tab => $name ){
            $current = $tab === $current_tab ? ' nav-tab-active' : '';
            $url = add_query_arg( array( 'page' => 'curator-settings', 'tab' => $tab ), '' );
            echo wp_kses("<a class=\"nav-tab{$current}\" href=\"{$url}\">{$name}</a>", array('a' => array('class' => array(), 'href' => array())));
          }
          ?>
        </nav>
        <div class="tab-content">
          <?php
            if ($current_tab === 'setup') {
                $this->setupContent();
            } else if ($current_tab === 'usage') {
                $this->usageContent();
            } else if ($current_tab === 'settings') {
                $this->settingsContent();
            }
          ?>
        </div>
    <?php
	}

    public function setupContent()
    {
        $html = '<h2>Setup</h2>';
        $html .= '<p>Sign up to the <a href="https://app.curator.io/" target="_blank">Curator Dashboard</a> to set up a social feed.</p>';
        $html .= '<p>You\'ll need your unique <code>FEED_PUBLIC_KEY</code> to use the widgets.<p>
           <p>You can find the <code>FEED_PUBLIC_KEY</code> here:</p>';
        $html .= '<img src="' . WP_URI . 'images/feed-public-key.png">';

        echo wp_kses($html, array(
          'h2' => array(),
          'p' => array(
                'a' => array('href' => array()),
          ),
          'code' => array(),
          'img' => array('src' => array()),
          ));
    }

    public function usageContent()
    {
        $html = '<h2>Usage</h2>';
        $html .= '<h4>Using shortcode in editor</h4>';
        $html .= '<p>Edit post/page and add the <code>[curator feed_public_key="FEED_PUBLIC_KEY"]</code> shortcode - where <code>FEED_PUBLIC_KEY</code> is the code from the Dashboard (detailed above).</p>';
        $html .= '<p>For example: <code>[curator feed_public_key="' . $this->TEST_FEED_ID . '"]</code><p/>';
        $html .= '<h4>In theme templates</h4>';
        $html .= '<p>To code the widget in a classic theme template you can use the following php function:</p>';
        $html .= '<code>echo curator_feed("FEED_PUBLIC_KEY");</code>';
        $html .= '<p>or</p>';
        $html .= '<code>echo do_shortcode("[curator feed_public_key="FEED_PUBLIC_KEY"]");</code>';
        $html .= '<p>To code the widget in a block based theme template you place the shorcode in between html <strong>wp:shortcode</strong> comment tags:</p>';
        $html .= '<code>[curator feed_public_key="FEED_PUBLIC_KEY"]</code>';

        echo wp_kses($html, array('h2' => array(), 'h4' => array(), 'p' => array(), 'code' => array(), 'strong' => array(), 'html_comment' => array()));
    }

    public function settingsContent()
    {
        $html = '<h2>Default Feed</h2>';
        $html .= '<p>Use the form below to define a default feed, after defining a default feed you can use the shortcode <code>[curator feed_public_key=""]</code></p>';

        add_settings_field(
            'default_feed_id', // ID
            'Default Feed Public Key', // Title
            array( $this, 'default_feed_id_callback' ), // Callback
            'curator-settings', // Page
            'setting_section_id', // Section
            array( 'label_for' => 'default_feed_id' )
        );

        add_settings_field(
            'powered_by', // ID
            'Show Powered by Curator.io', // Title
            array( $this, 'field_powered_by_callback' ), // Callback
            'curator-settings', // Page
            'setting_section_id', // Section
            array( 'label_for' => 'powered_by' )
        );

      echo wp_kses($html, array('h2' => array(), 'p' => array(), 'code' => array()));
    }

	/**
     * Get the settings option array and print one of its values
     */
    public function default_feed_id_callback()
    {
        $value = isset($this->options['default_feed_id']) ? $this->options['default_feed_id'] : '';
        print '<input type="text" id="default_feed_id" name="curator_options[default_feed_id]" value="' . esc_attr($value) . '" style="width:400px;max-width:400px"/>';
	}

    public function field_powered_by_callback()
    {
        $checked = isset($this->options['powered_by']) ? 1 : 0;
        print '<input type="checkbox" id="powered_by" name="curator_options[powered_by]" value="1" ' . ($checked ? 'checked' : '') . '/>';
    }
}
endif;
