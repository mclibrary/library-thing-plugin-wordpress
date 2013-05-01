<?php


class LibraryThingSettings {

    /* Class variables to be used throughout */
    private $pluginOptionName = 'libthing_plugin_settings';
    private $pluginSettingsPage = 'libthing_settings_page';

    /* This is the main settings variable */
    private $settings;

	/* Constructor */
	public function __construct(){
        /* Retrieve plugin option, returns false if not set */
        $this->settings     = get_option( $this->pluginOptionName );
		
        /* If the option hasn't been set */
        if( $this->settings == false ) {
            
            /* Add the option to DB */
            add_option( $this->pluginOptionName );
            
            /* Set defaults */
            $this->settings = array(
                'user_id'       => '',
                'dev_key'       => '',
                'collection'    => '',
                'responseType'  => 'json',
                'resultSets'    => 'books',
                'max_rows'      => 3,
                'books_per_row' => 5,
                'cache_length'  => 30,
                'coverWidth'    => '125px',
                'timestamp'     => 0,
            );

            /* Save defaults to DB */
            $this->saveSettings();

        }

        /* Add actions to admin area */
        if (is_admin()){
            add_action( 'admin_menu', array($this, 'register_libthing_settings_page'));
            add_action('admin_init', array($this, 'register_libthing_settings'));
        }
	}

    /* Registers all setting sections and setting fields */
	public function register_libthing_settings(){

        /* SECTION User Data */
        add_settings_section(
            'libthing_user_settings_section',
            'User Settings',
            array($this, 'libthing_user_settings_callback'),
            $this->pluginSettingsPage
        );

            /* FIELD User ID */
            add_settings_field(
                'user_id',
                'User ID',
                array($this, 'libthing_user_id_callback'),
                $this->pluginSettingsPage,
                'libthing_user_settings_section',
                array(
                    'Your LibraryThing Username'
                )
            );

            /* FIELD Developer Key */
            add_settings_field(
                'dev_key',
                'Developer Key',
                array($this, 'libthing_dev_key_callback'),
                $this->pluginSettingsPage,
                'libthing_user_settings_section',
                array(
                    'Your Developer API Key'
                )
            );

        /* SECTION Widget Settings */
        add_settings_section(
            'libthing_widget_settings_section',
            'Widget Settings',
            array($this, 'libthing_plugin_settings_callback'),
            $this->pluginSettingsPage
        );

            /* FIELD Collection */
            add_settings_field(
                'collection',
                'Collection',
                array($this, 'libthing_collection_callback'),
                $this->pluginSettingsPage,
                'libthing_widget_settings_section',
                array(
                    'Book Collection to Cache'
                )
            );

            /* FIELD Books Per Row */
            add_settings_field(
                'books_per_row',
                'Books per Row',
                array($this, 'libthing_books_per_row_callback'),
                $this->pluginSettingsPage,
                'libthing_widget_settings_section',
                array(
                    'The number of books to display per row'
                )
            );

            /* FIELD Max Rows */
            add_settings_field(
                'max_rows',
                'Rows to Display',
                array($this, 'libthing_max_rows_callback'),
                $this->pluginSettingsPage,
                'libthing_widget_settings_section',
                array(
                    'The Number of Rows to cache'
                )
            );

            /* FIELD Cache Length */
            add_settings_field(
                'cache_length',
                'Cache Length',
                array($this, 'libthing_cache_length_callback'),
                $this->pluginSettingsPage,
                'libthing_widget_settings_section',
                array(
                    'Cache length (in minutes).'
                )
            );

        /* SECTION Cache */
        add_settings_section(
            'libthing_cache_section',
            'Cache',
            array($this, 'libthing_cache_settings_callback'),
            $this->pluginSettingsPage
        );

            /* FIELD Last Cached / Timestamp */
            add_settings_field(
                'timestamp',
                'Cached at',
                array($this, 'libthing_timestamp_callback'),
                $this->pluginSettingsPage,
                'libthing_cache_section',
                array(
                    'Delete this value to clear the cache.'
                )
            );

            /* FIELD JSON Object */
            add_settings_field(
                'json_object',
                'Most Recent JSON Object',
                array($this, 'libthing_json_callback'),
                $this->pluginSettingsPage,
                'libthing_cache_section'
            );

        /* Register the actual options with the options page */
        register_setting(
            $this->pluginSettingsPage,
            $this->pluginOptionName
        ); 
    }

    /* Settings section callbacks (Empty) */
	public function libthing_user_settings_callback(){}
    public function libthing_plugin_settings_callback(){}
    public function libthing_cache_settings_callback(){}


    /* Setting field callbacks */
    public function libthing_user_id_callback($args) {
            $html = '<input type="text" id="user_id" name="libthing_plugin_settings[user_id]" value="' . $this->settings['user_id'] . '" />';
            $html .= '<label for="user_id"> '  . $args[0] . '</label>';
            echo $html;
    	}

    public function libthing_dev_key_callback($args) {
        $html = '<input type="text" id="dev_key" name="libthing_plugin_settings[dev_key]" value="' . $this->settings['dev_key'] . '" />';
        $html .= '<label for="dev_key"> '  . $args[0] . '</label>';
        echo $html;
    	}

    public function libthing_collection_callback($args) {
        $html = '<input type="text" id="collection" name="libthing_plugin_settings[collection]" value="' . $this->settings['collection'] . '" />';
        $html .= '<label for="collection"> '  . $args[0] . '</label>';
        echo $html;
    	}

    public function libthing_books_per_row_callback($args) {
        $html = '<input type="text" id="books_per_row" name="libthing_plugin_settings[books_per_row]" value="' . $this->settings['books_per_row'] . '" />';
        $html .= '<label for="books_per_row"> '  . $args[0] . '</label>';
        echo $html;
    	} 

    public function libthing_max_rows_callback($args) {
        $html = '<input type="text" id="max_rows" name="libthing_plugin_settings[max_rows]" value="' . $this->settings['max_rows'] . '" />';
        $html .= '<label for="max_rows"> '  . $args[0] . '</label>';
        echo $html;
    	}

    public function libthing_cache_length_callback($args) {
        $html = '<input type="text" id="cache_length" name="libthing_plugin_settings[cache_length]" value="' . $this->settings['cache_length'] . '" />';
        $html .= '<label for="cache_length"> '  . $args[0] . '</label>';
        echo $html;
    	}

    public function libthing_timestamp_callback($args) {
        if (!$this->settings['timestamp']){
            $html = '<code>NA</code>';
        } else {
        $html = '<code>' . date('j M Y g:i:s A', $this->settings['timestamp']) . '</code>';
        }
        echo $html;
    	}

    public function libthing_json_callback($args) {
        if (!$this->settings['json_object']){
            $html = '<code>No object cached. Make sure Username and Developer Key are correct.</code>';
        } else {
            $collection = json_decode(urldecode($this->settings['json_object']));
            $html  = "<table class='widefat wp-list-table'>\n";
            $html .= "<thead>\n<tr>\n";
            $html .= "<th>Title</th>\n</tr>\n</thead>\n<tbody>\n";
                foreach($collection as $id => $book){
                    $html .= "<tr>\n";
                    $html .= "<td>". $book->title ."</td>\n</tr>\n";
                }
            $html .= "</tbody></table>";
        }
        echo $html;
	    }


    /* Saves current settings to DB */
    public function saveSettings(){

        update_option($this->pluginOptionName, $this->settings);
    }

    /* Updates settings from DB */
    public function fetchSettings(){
        $this->settings = get_option( $this->pluginOptionName );
    }

    /* Update ans saves a setting */
    public function updateSetting($setting, $value){

        /* Reassign setting to new value*/
        $this->settings[$setting] = $value;

        /* Update database */
        update_option($this->pluginOptionName, $this->settings);
    }

    /* Returns all settings as array */
    public function getSettings(){
        return $this->settings;
    }

    /* Create Settings Page */
	public function register_libthing_settings_page() {
        add_options_page(
            'LibraryThing Cache',
            'LibraryThing Cache',
            'administrator',
            'libthing_plugin_settings_page',
            array($this, 'libthing_settings_page_callback')
        );
    }

    /* Option Menu Callback (HTML) */
    public function libthing_settings_page_callback() { ?>
            <!-- Create a header in the default WordPress 'wrap' container -->
            <div class="wrap">

                <div id="icon-themes" class="icon32"></div>
                <h2>LibraryThing Cache</h2>

                <form method="post" action="options.php">
                    <?php settings_fields( $this->pluginSettingsPage ); ?>
                    <?php do_settings_sections( $this->pluginSettingsPage ); ?>
                    <?php submit_button(); ?>
                </form>

            </div><!-- /.wrap -->
        <?php }
}

?>