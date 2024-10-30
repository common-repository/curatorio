<?php
if ( !class_exists( 'CuratorSettings' ) ) :

class CuratorSettings {

    var $TEST_FEED_ID = "8558f0f9-043f-4bd9-bad1-037cf10a";

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
            WP_URI . "/images/Curator_Logomark2.svg",
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
                if (isset($_GET['tab']) && $_GET['tab'] == 'settings') {
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
//        var_dump($input);die;
        $validOptions = [
            'default_feed_id',
            'powered_by',
        ];

        $new_input = array();
        foreach ($validOptions as $option) {
            if (isset($input[$option])) {
                $new_input[$option] = $input[$option];
            }
        }

        return $new_input;
	}

	/**
     * Print the Section text
     */
    public function print_section_info()
    {
        $tab = $_GET['tab'] ?? 'setup';
        $setupSelected = '';
        $usageSelected = '';
        $settingsSelected = '';
        if ($tab == 'setup') {
            $setupSelected = 'nav-tab-active';
        } else  if ($tab == 'usage') {
            $usageSelected = 'nav-tab-active';
        } else if ($tab == 'settings') {
            $settingsSelected = 'nav-tab-active';
        }

        $html = '<nav class="nav-tab-wrapper">
            <a href="?page=curator-settings" class="nav-tab '.$setupSelected.'">Setup</a>
            <a href="?page=curator-settings&tab=usage" class="nav-tab '.$usageSelected.'">Usage</a>
            <a href="?page=curator-settings&tab=settings" class="nav-tab '.$settingsSelected.'">Settings</a>
        </nav>';

        $html .= '<div class="tab-content">';
            if ($tab == 'setup') {
                $html .= $this->setupContent();
            } else if ($tab == 'usage') {
                $html .= $this->usageContent();
            } else if ($tab == 'settings') {
                $html .= $this->settingsContent();
            }
        $html .= '</div>';

        echo $html;
	}

    public function setupContent()
    {
        $html = '<div class="description" id="default_feed_id" style="margin-top:10px;">';
        $html .= '<h2>Setup</h2>';
        $html .= 'Sign up to the <a href="https://app.curator.io/" target="_blank">Curator Dashboard</a> to set up a social feed.<br/><br/>';
        $html .= 'You\'ll need your unique <code>FEED_PUBLIC_KEY</code> to use the widgets.<br/><br/>
            You can find the <code>FEED_PUBLIC_KEY</code> here:<br><br/>';
        $html .= '<img src="' . WP_URI . 'images/feed-public-key.png"><br/<br/>';
        $html .= '</div>';

        return $html;
    }

    public function usageContent()
    {
        $html = '<h2>Usage</h2>';
        $html .= '<h4>Using shortcode</h4>';
        $html .= 'Edit post/page and add the <code>[curator feed_public_key="FEED_PUBLIC_KEY"]</code> shortcode - where <code>FEED_PUBLIC_KEY</code> is the code from the Dashboard (detailed above). <br/>';

        $html .= 'For example: <code>[curator feed_public_key="'.$this->TEST_FEED_ID.'"]</code> <br>';
        $html .= '<h4>Using PHP</h4>';
        $html .= 'To display the widget outside of a post or page (eg in template code) you can use the following php function:<br/>';
        $html .= '<code>curator_feed(\'FEED_PUBLIC_KEY\');</code><br/>';
        $html .= 'eg:<br/><code>&lt?php curator_feed( \'FEED_PUBLIC_KEY\' ); ?&gt</code><br/><br/>';

        return $html;
    }

    public function settingsContent()
    {
        $html = '<h2>Default Feed</h2>';
        $html .= 'Use the form below to define a default feed, after defining a default feed you can use the shortcode <code>[curator feed_public_key=""]</code><br>';

        add_settings_field(
            'default_feed_id', // ID
            'Default Feed Public Key', // Title
            array( $this, 'default_feed_id_callback' ), // Callback
            'curator-settings', // Page
            'setting_section_id' // Section
        );
        add_settings_field(
            'powered_by', // ID
            'Show Powered by Curator.io', // Title
            array( $this, 'field_powered_by' ), // Callback
            'curator-settings', // Page
            'setting_section_id', // Section
            array( 'label_for' => 'powered_by' )
        );

        return $html;
    }

	/**
     * Get the settings option array and print one of its values
     */
    public function default_feed_id_callback()
    {
        $value = isset($this->options['default_feed_id']) ? esc_attr($this->options['default_feed_id']) : '';
        printf('<input type="text" id="default_feed_id" name="curator_options[default_feed_id]" value="%s" style="width:400px;max-width:400px"/>', $value);
	}

    public function field_powered_by()
    {
        $checked = isset($this->options['powered_by']) ? 1 : 0;
        printf('<input type="checkbox" id="powered_by" name="curator_options[powered_by]" value="1" '.($checked?'checked':'').'/>');
    }

}
endif;
