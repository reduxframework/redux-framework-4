<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Redux_Panel' ) ) {
    /**
     * Class Redux_Panel
     */
    class Redux_Panel {
        /**
         * @var null
         */
        public $parent = null;
        /**
         * @var null|string
         */
        public $template_path = null;
        /**
         * @var null
         */
        public $original_path = null;

        /**
         * Sets the path from the arg or via filter. Also calls the panel template function.
         *
         * @param $parent
         */
        public function __construct( $parent ) {
            $this->parent             = $parent;
            $this->template_path      = $this->original_path = ReduxCore::$_dir . 'templates/panel/';

            if ( ! empty( $this->parent->args['templates_path'] ) ) {
                $this->template_path = trailingslashit( $this->parent->args['templates_path'] );
            }
            $this->template_path = trailingslashit( apply_filters( "redux/{$this->parent->args['opt_name']}/panel/templates_path", $this->template_path ) );
        }

        public function init() {
            $this->panel_template();
        }


        /**
         * Loads the panel templates where needed and provides the container for Redux
         */
        private function panel_template() {
            if ( $this->parent->args['dev_mode'] ) {
                $this->template_file_check_notice();
            }

            /**
             * action 'redux/{opt_name}/panel/before'
             */
            do_action( "redux/{$this->parent->args['opt_name']}/panel/before" );

            echo '<div class="wrap"><h2></h2></div>'; // Stupid hack for Wordpress alerts and warnings

            echo '<div class="clear"></div>';
            echo '<div class="wrap">';

            // Do we support JS?
            echo '<noscript><div class="no-js">' . esc_html__( 'Warning- This options panel will not work properly without javascript!', 'redux-framework' ) . '</div></noscript>';

            // Security is vital!
            echo '<input type="hidden" id="ajaxsecurity" name="security" value="' . wp_create_nonce( 'redux_ajax_nonce' . $this->parent->args['opt_name'] ) . '" />';

            /**
             * action 'redux/page/{opt_name}/form/before'
             *
             * @param object $this ReduxFramework
             */
            do_action( "redux/page/{$this->parent->args['opt_name']}/form/before", $this );

            $this->get_template( 'container.tpl.php' );

            /**
             * action 'redux/page/{opt_name}/form/after'
             *
             * @param object $this ReduxFramework
             */
            do_action( "redux/page/{$this->parent->args['opt_name']}/form/after", $this );
            echo '<div class="clear"></div>';
            echo '</div>';

//            require_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
//
//            // Before, try to access the data, check the cache.
//            if ( false === ($api = get_transient ( 'redux_framework_info' )) ) {
//                // The cache data doesn't exist or it's expired.
//                $api = plugins_api ( 'plugin_information', array( 'slug' => stripslashes ( 'redux-framework' ) ) );
//
//                if ( !is_wp_error ( $api ) ) {
//                    // cache isn't up to date, write this fresh information to it now to avoid the query for xx time.
//                    $myexpire = 60 * 15; // Cache data for 15 minutes
//                    set_transient ( 'redux_framework_info', $api, $myexpire );
//                }
//            }
//            
//            if ( !is_wp_error ( $api ) ) {
//                foreach ( (array) $api->sections as $section_name => $content ) {
//                    $api->sections[ $section_name ] = wp_kses_post ( $content );
//                }
//
//                foreach ( array( 'version', 'author', 'requires', 'tested', 'homepage', 'downloaded', 'slug' ) as $key ) {
//                    $api->$key = wp_kses_post ( $api->$key );
//                }
//
//                if ( !empty ( $api->rating ) ) {
//                }
//            }
            
            if ( $this->parent->args['dev_mode'] == true ) {
                echo '<br /><div class="redux-timer">' . get_num_queries() . ' queries in ' . timer_stop( 0 ) . ' seconds<br/>Redux is currently set to developer mode.</div>';
                
//                if ( !empty ( $api->downloaded ) ) {
//                    echo sprintf ( __ ( 'Downloaded %s times', 'redux-framework' ), number_format_i18n ( $api->downloaded ) );
//                    echo '.';
//                }
?>
<!--                <div class="redux-star-holder" title="<?php //echo esc_attr ( sprintf ( __ ( '(Average rating based on %s ratings)', 'redux-framework' ), number_format_i18n ( $api->num_ratings ) ) ); ?>">
                        <div class="redux-star redux-star-rating" style="width: <?php //echo esc_attr ( $api->rating ) ?>px"></div>
                        <div class="redux-star redux-star5"><img src="<?php //echo ReduxCore::$_url; ?>assets/img/star.png" alt="<?php //_e ( '5 stars', 'redux-framework' ) ?>" /></div>
                        <div class="redux-star redux-star4"><img src="<?php //echo ReduxCore::$_url; ?>assets/img/star.png" alt="<?php //_e ( '4 stars', 'redux-framework' ) ?>" /></div>
                        <div class="redux-star redux-star3"><img src="<?php //echo ReduxCore::$_url; ?>assets/img/star.png" alt="<?php //_e ( '3 stars', 'redux-framework' ) ?>" /></div>
                        <div class="redux-star redux-star2"><img src="<?php //echo ReduxCore::$_url; ?>assets/img/star.png" alt="<?php //_e ( '2 stars', 'redux-framework' ) ?>" /></div>
                        <div class="redux-star redux-star1"><img src="<?php //echo ReduxCore::$_url; ?>assets/img/star.png" alt="<?php //_e ( '1 star', 'redux-framework' ) ?>" /></div>
                    </div>
                    <small><?php //echo sprintf ( __ ( '(Average rating based on %s ratings)', 'redux-framework' ), number_format_i18n ( $api->num_ratings ) ); ?>
                        <a target="_blank" href="https://wordpress.org/support/view/plugin-reviews/redux-framework?rate=5#postform"> <?php// _e ( 'rate', 'redux-framework' ) ?></a>
                    </small>-->
<?php
            }

            /**
             * action 'redux/{opt_name}/panel/after'
             */
            do_action( "redux/{$this->parent->args['opt_name']}/panel/after" );

        }


        /**
         * Calls the various notification bars and sets the appropriate templates.
         */
        function notification_bar() {

            if ( isset( $this->parent->transients['last_save_mode'] ) ) {

                if ( $this->parent->transients['last_save_mode'] == "import" ) {
                    /**
                     * action 'redux/options/{opt_name}/import'
                     *
                     * @param object $this ReduxFramework
                     */
                    do_action( "redux/options/{$this->parent->args['opt_name']}/import", $this, $this->parent->transients['changed_values'] );

                    /**
                     * filter 'redux-imported-text-{opt_name}'
                     *
                     * @param string  translated "settings imported" text
                     */
                    echo '<div class="admin-notice notice-blue saved_notice"><strong>' . apply_filters( "redux-imported-text-{$this->parent->args['opt_name']}", esc_html__( 'Settings Imported!', 'redux-framework' ) ) . '</strong></div>';
                    //exit();
                } else if ( $this->parent->transients['last_save_mode'] == "defaults" ) {
                    /**
                     * action 'redux/options/{opt_name}/reset'
                     *
                     * @param object $this ReduxFramework
                     */
                    do_action( "redux/options/{$this->parent->args['opt_name']}/reset", $this );

                    /**
                     * filter 'redux-defaults-text-{opt_name}'
                     *
                     * @param string  translated "settings imported" text
                     */
                    echo '<div class="saved_notice admin-notice notice-yellow"><strong>' . apply_filters( "redux-defaults-text-{$this->parent->args['opt_name']}", esc_html__( 'All Defaults Restored!', 'redux-framework' ) ) . '</strong></div>';
                } else if ( $this->parent->transients['last_save_mode'] == "defaults_section" ) {
                    /**
                     * action 'redux/options/{opt_name}/section/reset'
                     *
                     * @param object $this ReduxFramework
                     */
                    do_action( "redux/options/{$this->parent->args['opt_name']}/section/reset", $this );

                    /**
                     * filter 'redux-defaults-section-text-{opt_name}'
                     *
                     * @param string  translated "settings imported" text
                     */
                    echo '<div class="saved_notice admin-notice notice-yellow"><strong>' . apply_filters( "redux-defaults-section-text-{$this->parent->args['opt_name']}", esc_html__( 'Section Defaults Restored!', 'redux-framework' ) ) . '</strong></div>';
                } else if ( $this->parent->transients['last_save_mode'] == "normal" ) {
                    /**
                     * action 'redux/options/{opt_name}/saved'
                     *
                     * @param mixed $value set/saved option value
                     */
                    do_action( "redux/options/{$this->parent->args['opt_name']}/saved", $this->parent->options, $this->parent->transients['changed_values'] );

                    /**
                     * filter 'redux-saved-text-{opt_name}'
                     *
                     * @param string translated "settings saved" text
                     */
                    echo '<div class="saved_notice admin-notice notice-green">' . apply_filters( "redux-saved-text-{$this->parent->args['opt_name']}", '<strong>'.esc_html__( 'Settings Saved!', 'redux-framework' ) ).'</strong>' . '</div>';
                }

                unset( $this->parent->transients['last_save_mode'] );

                $this->parent->transient_class->set();
            }

            /**
             * action 'redux/options/{opt_name}/settings/changes'
             *
             * @param mixed $value set/saved option value
             */
            do_action( "redux/options/{$this->parent->args['opt_name']}/settings/change", $this->parent->options, $this->parent->transients['changed_values'] );

            /**
             * filter 'redux-changed-text-{opt_name}'
             *
             * @param string translated "settings have changed" text
             */
            echo '<div class="redux-save-warn notice-yellow"><strong>' . apply_filters( "redux-changed-text-{$this->parent->args['opt_name']}", esc_html__( 'Settings have changed, you should save them!', 'redux-framework' ) ) . '</strong></div>';

            /**
             * action 'redux/options/{opt_name}/errors'
             *
             * @param array $this ->errors error information
             */
            do_action( "redux/options/{$this->parent->args['opt_name']}/errors", $this->parent->errors );
            
            echo '<div class="redux-field-errors notice-red"><strong><span></span> ' . esc_html__( 'error(s) were found!', 'redux-framework' ) . '</strong></div>';

            /**
             * action 'redux/options/{opt_name}/warnings'
             *
             * @param array $this ->warnings warning information
             */
            do_action( "redux/options/{$this->parent->args['opt_name']}/warnings", $this->parent->warnings );
            
            echo '<div class="redux-field-warnings notice-yellow"><strong><span></span> ' . esc_html__( 'warning(s) were found!', 'redux-framework' ) . '</strong></div>';

        }

        /**
         * Used to intitialize the settings fields for this panel. Required for saving and redirect.
         */
        function init_settings_fields() {
            // Must run or the page won't redirect properly
            settings_fields( "{$this->parent->args['opt_name']}_group" );
        }


        /**
         * Used to select the proper template. If it doesn't exist in the path, then the original template file is used.
         *
         * @param $file
         */
        function get_template( $file ) {
            if ( empty( $file ) ) {
                return;
            }

            if ( file_exists( $this->template_path . $file ) ) {
                $path = $this->template_path . $file;
            } else {
                $path = $this->original_path . $file;
            }

            do_action( "redux/{$this->parent->args['opt_name']}/panel/template/" . $file . '/before' );
            
            $path = apply_filters( "redux/{$this->parent->args['opt_name']}/panel/template/" . $file, $path );
            
            do_action( "redux/{$this->parent->args['opt_name']}/panel/template/" . $file . '/after' );

            require $path;

        }

        /**
         * Scan the template files
         *
         * @param string $template_path
         *
         * @return array
         */
        public function scan_template_files( $template_path ) {
            $files  = scandir( $template_path );
            $result = array();
            if ( $files ) {
                foreach ( $files as $key => $value ) {
                    if ( ! in_array( $value, array( ".", ".." ) ) ) {
                        if ( is_dir( $template_path . DIRECTORY_SEPARATOR . $value ) ) {
                            $sub_files = self::scan_template_files( $template_path . DIRECTORY_SEPARATOR . $value );
                            foreach ( $sub_files as $sub_file ) {
                                $result[] = $value . DIRECTORY_SEPARATOR . $sub_file;
                            }
                        } else {
                            $result[] = $value;
                        }
                    }
                }
            }

            return $result;
        }

        /**
         * Show a notice highlighting bad template files
         */
        public function template_file_check_notice() {
            if ( $this->template_path == $this->original_path ) {
                return;
            }

            $core_templates = $this->scan_template_files( $this->original_path );
            $outdated       = false;

            foreach ( $core_templates as $file ) {
                $developer_theme_file = false;

                if ( file_exists( $this->template_path . $file ) ) {
                    $developer_theme_file = $this->template_path . $file;
                }

                if ( $developer_theme_file ) {
                    $core_version      = Redux_Helpers::get_template_version( $this->original_path . $file );
                    $developer_version = Redux_Helpers::get_template_version( $developer_theme_file );

                    if ( $core_version && $developer_version && version_compare( $developer_version, $core_version, '<' ) ) {
?>
                        <div id="message" class="error redux-message">
                            <p><?php _e( '<strong>Your panel has bundled outdated copies of Redux Framework template files</strong> &#8211; if you encounter functionality issues this could be the reason. Ensure you update or remove them.', 'redux-framework' ); ?></p>
                        </div>
<?php
                        return;
                    }
                }

            }
        }

        /**
         * Outputs the HTML for a given section using the WordPress settings API.
         *
         * @param $k - Section number of settings panel to display
         */
        function output_section( $k ) {
            do_settings_sections( $this->parent->args['opt_name'] . $k . '_section_group' );
        }
    }
}