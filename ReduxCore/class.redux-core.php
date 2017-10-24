<?php

    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    if ( ! class_exists( 'ReduxCore' ) ) {

        class ReduxCore {

            public static $instance;

            public static $_version;
            public static $_dir;
            public static $_url;
            public static $_path;
            public static $_upload_dir = null;
            public static $_upload_url = null;
            public static $_is_plugin = true;
            public static $_installed = '';
            public static $_as_plugin = false;
            public static $_in_theme = false;
            public static $_pro_loaded = false;
            public static $_google_fonts = array();
            public static $_callers = array();

            public static $third_party_fixes = null;

            public static function instance() {
                if ( ! self::$instance ) {
                    self::$instance = new self;

                    self::$instance->includes();
                    self::$instance->init();
                    self::$instance->hooks();
                }

                return self::$instance;
            }

            private function init() {
                if ( class_exists( 'ReduxPro' ) && isset( ReduxPro::$_dir ) ) {
                    self::$_pro_loaded = true;
                }

                self::$_dir = $dir = trailingslashit( wp_normalize_path( dirname( realpath( __FILE__ ) ) ) );

                Redux_Helpers::generator();

                // See if Redux is a plugin or not
                if ( $plugin_info = Redux_Helpers::is_inside_plugin( __FILE__ ) ) {
                    self::$_installed = class_exists( 'ReduxFrameworkPlugin' ) ? 'plugin' : 'in_plugin';

                    self::$_is_plugin = class_exists( 'ReduxFrameworkPlugin' );
                    self::$_as_plugin = true;
                    self::$_url       = plugin_dir_url( __FILE__ );

                } elseif ( $theme_info = Redux_Helpers::is_inside_theme( __FILE__ ) ) {
                    self::$_url       = trailingslashit( $theme_info['url'] );
                    self::$_in_theme  = true;
                    self::$_installed = "in_theme";
                }

                self::$_url       = apply_filters( "redux/_url", self::$_url );
                self::$_dir       = apply_filters( "redux/_dir", self::$_dir );
                self::$_is_plugin = apply_filters( "redux/_is_plugin", self::$_is_plugin );

                $upload_dir        = wp_upload_dir();
                self::$_upload_dir = $upload_dir['basedir'] . '/redux/';
                self::$_upload_url = str_replace( array(
                    'https://',
                    'http://'
                ), '//', $upload_dir['baseurl'] . '/redux/' );

                self::$_upload_dir = apply_filters( "redux/_upload_dir", self::$_upload_dir );
                self::$_upload_url = apply_filters( "redux/_upload_url", self::$_upload_url );

                Redux_Instances::get_instance();
            }

            public static function core_construct( $parent, $args ) {
                new Redux_P();

                self::$third_party_fixes = new Redux_ThirdParty_Fixes( $parent );

                Redux_ThemeCheck::get_instance();

                self::tracking( $parent );
            }

            private static function tracking( $parent ) {
                if ( isset( $parent->args['allow_tracking'] ) && $parent->args['allow_tracking'] ) { // && Redux_Helpers::isTheme( __FILE__ ) )
                    if ( file_exists( ReduxCore::$_dir . '/inc/classes/class.redux_tracking.php' ) ) {
                        $tracking = Redux_Tracking::get_instance();
                        $tracking->load( $parent );
                    }
                }
            }

            private function includes() {

                require_once dirname( __FILE__ ) . '/inc/classes/class.redux_path.php';

                spl_autoload_register( array( $this, 'register_classes' ) );

                new Redux_Welcome();
            }

            public function register_classes( $class_name ) {
                if ( ! class_exists( $class_name ) ) {

                    // Backward compatibility for extensions sucks!
                    if ( $class_name == 'Redux_Instances' ) {
                        require_once( Redux_Path::get_path( '/inc/classes/class.redux_instances.php' ) );
                        require_once( Redux_Path::get_path( '/inc/lib/lib.redux_instances.php' ) );

                        return;
                    }

                    // Redux API
                    if ( $class_name == 'Redux' ) {
                        require_once( Redux_Path::get_path( '/inc/classes/class.redux_api.php' ) );

                        return;
                    }

                    // Redux extra theme checks
                    if ( $class_name == 'Redux_ThemeCheck' ) {
                        require_once( Redux_Path::get_path( '/inc/themecheck/class.redux_themecheck.php' ) );

                        return;
                    }

                    if ( $class_name == 'Redux_Welcome' ) {
                        require_once( Redux_Path::get_path( '/inc/welcome/class.redux_welcome.php' ) );

                        return;
                    }

                    // Everything else
                    $file = 'class.' . strtolower( $class_name ) . '.php';

                    $class_path = Redux_Path::get_path( '/inc/classes/' . $file );
                    if ( file_exists( $class_path ) ) {
                        include( $class_path );
                    }
                }

                do_action( 'redux/core/includes', $this );
            }

            private function hooks() {
                do_action( 'redux/core/hooks', $this );
            }

            public static function is_heartbeat() {
                // Disregard WP AJAX 'heartbeat'call.  Why waste resources?
                if ( isset ( $_POST ) && isset ( $_POST['action'] ) && $_POST['action'] == 'heartbeat' ) {

                    // Hook, for purists.
                    if ( ! has_action( 'redux/ajax/heartbeat' ) ) {
                        do_action( 'redux/ajax/heartbeat' );
                    }

                    // Buh bye!
                    return true;
                }

                return false;
            }
        }
    }