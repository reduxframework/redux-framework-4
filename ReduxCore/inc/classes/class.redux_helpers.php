<?php

    // Exit if accessed directly
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    // Don't duplicate me!
    if ( ! class_exists( 'Redux_Helpers' ) ) {

        /**
         * Redux Helpers Class
         * Class of useful functions that can/should be shared among all Redux files.
         *
         * @since       1.0.0
         */
        class Redux_Helpers {

            public static function tabFromField( $parent, $field ) {
                foreach ( $parent->sections as $k => $section ) {
                    if ( ! isset( $section['title'] ) ) {
                        continue;
                    }

                    if ( isset( $section['fields'] ) && ! empty( $section['fields'] ) ) {
                        if ( Redux_Helpers::recursive_array_search( $field, $section['fields'] ) ) {
                            return $k;
                            continue;
                        }
                    }
                }
            }

            public static function isFieldInUseByType( $fields, $field = array() ) {
                foreach ( $field as $name ) {
                    if ( array_key_exists( $name, $fields ) ) {
                        return true;
                    }
                }

                return false;
            }

            public static function isFieldInUse( $parent, $field ) {
                foreach ( $parent->sections as $k => $section ) {
                    if ( ! isset( $section['title'] ) ) {
                        continue;
                    }

                    if ( isset( $section['fields'] ) && ! empty( $section['fields'] ) ) {
                        if ( Redux_Helpers::recursive_array_search( $field, $section['fields'] ) ) {
                            return true;
                            continue;
                        }
                    }
                }
            }

            public static function major_version( $v ) {
                $version = explode( '.', $v );
                if ( count( $version ) > 1 ) {
                    return $version[0] . '.' . $version[1];
                } else {
                    return $v;
                }
            }

            public static function isLocalHost() {
                return ( $_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['REMOTE_ADDR'] === 'localhost' ) ? 1 : 0;
            }

            public static function isWpDebug() {
                return ( defined( 'WP_DEBUG' ) && WP_DEBUG == true );
            }

            public static function generator() {
                add_action( 'wp_head', array( 'Redux_Helpers', 'meta_tag' ) );
            }

            public static function meta_tag() {
                echo '<meta name="framework" content="Redux ' . ReduxCore::$_version . '" />' . "\n";
            }

            public static function getTrackingObject() {
                global $wpdb;

                $hash = Redux_Helpers::get_hash();

                global $blog_id, $wpdb;
                $pts = array();

                foreach ( get_post_types( array( 'public' => true ) ) as $pt ) {
                    $count      = wp_count_posts( $pt );
                    $pts[ $pt ] = $count->publish;
                }

                $comments_count = wp_count_comments();
                $theme_data     = wp_get_theme();
                $theme          = array(
                    'version'  => $theme_data->Version,
                    'name'     => $theme_data->Name,
                    'author'   => $theme_data->Author,
                    'template' => $theme_data->Template,
                );

                if ( ! function_exists( 'get_plugin_data' ) ) {
                    if ( file_exists( ABSPATH . 'wp-admin/includes/plugin.php' ) ) {
                        require_once ABSPATH . 'wp-admin/includes/plugin.php';
                    }
                    if ( file_exists( ABSPATH . 'wp-admin/includes/admin.php' ) ) {
                        require_once ABSPATH . 'wp-admin/includes/admin.php';
                    }
                }

                $plugins = array();
                foreach ( get_option( 'active_plugins', array() ) as $plugin_path ) {
                    $plugin_info = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin_path );

                    $slug             = str_replace( '/' . basename( $plugin_path ), '', $plugin_path );
                    $plugins[ $slug ] = array(
                        'version'    => $plugin_info['Version'],
                        'name'       => $plugin_info['Name'],
                        'plugin_uri' => $plugin_info['PluginURI'],
                        'author'     => $plugin_info['AuthorName'],
                        'author_uri' => $plugin_info['AuthorURI'],
                    );
                }
                if ( is_multisite() ) {
                    foreach ( get_option( 'active_sitewide_plugins', array() ) as $plugin_path ) {
                        $plugin_info      = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin_path );
                        $slug             = str_replace( '/' . basename( $plugin_path ), '', $plugin_path );
                        $plugins[ $slug ] = array(
                            'version'    => $plugin_info['Version'],
                            'name'       => $plugin_info['Name'],
                            'plugin_uri' => $plugin_info['PluginURI'],
                            'author'     => $plugin_info['AuthorName'],
                            'author_uri' => $plugin_info['AuthorURI'],
                        );
                    }
                }


                $version = explode( '.', PHP_VERSION );
                $version = array(
                    'major'   => $version[0],
                    'minor'   => $version[0] . '.' . $version[1],
                    'release' => PHP_VERSION
                );

                $user_query     = new WP_User_Query( array( 'blog_id' => $blog_id, 'count_total' => true, ) );
                $comments_query = new WP_Comment_Query();

                $data = array(
                    '_id'       => $hash,
                    'localhost' => ( $_SERVER['REMOTE_ADDR'] === '127.0.0.1' ) ? 1 : 0,
                    'php'       => $version,
                    'site'      => array(
                        'hash'      => $hash,
                        'version'   => get_bloginfo( 'version' ),
                        'multisite' => is_multisite(),
                        'users'     => $user_query->get_total(),
                        'lang'      => get_locale(),
                        'wp_debug'  => ( defined( 'WP_DEBUG' ) ? WP_DEBUG ? true : false : false ),
                        'memory'    => WP_MEMORY_LIMIT,
                    ),
                    'pts'       => $pts,
                    'comments'  => array(
                        'total'    => $comments_count->total_comments,
                        'approved' => $comments_count->approved,
                        'spam'     => $comments_count->spam,
                        'pings'    => $comments_query->query( array( 'count' => true, 'type' => 'pingback' ) ),
                    ),
                    'options'   => apply_filters( 'redux/tracking/options', array() ),
                    'theme'     => $theme,
                    'redux'     => array(
                        'mode'      => ReduxCore::$_is_plugin ? 'plugin' : 'theme',
                        'version'   => ReduxCore::$_version,
                        'demo_mode' => get_option( 'ReduxFrameworkPlugin' ),
                    ),
                    'developer' => apply_filters( 'redux/tracking/developer', array() ),
                    'plugins'   => $plugins,
                );

                $parts    = explode( ' ', $_SERVER['SERVER_SOFTWARE'] );
                $software = array();
                foreach ( $parts as $part ) {
                    if ( $part[0] == "(" ) {
                        continue;
                    }
                    if ( strpos( $part, '/' ) !== false ) {
                        $chunk                               = explode( "/", $part );
                        $software[ strtolower( $chunk[0] ) ] = $chunk[1];
                    }
                }
                $software['full']             = $_SERVER['SERVER_SOFTWARE'];
                $data['environment']          = $software;
                $data['environment']['mysql'] = $wpdb->db_version();
                //                if ( function_exists( 'mysqli_get_server_info' ) ) {
                //                    $link = mysqli_connect() or die( "Error " . mysqli_error( $link ) );
                //                    $data['environment']['mysql'] = mysqli_get_server_info( $link );
                //                } else if ( class_exists( 'PDO' ) && method_exists( 'PDO', 'getAttribute' ) ) {
                //                    $data['environment']['mysql'] = PDO::getAttribute( PDO::ATTR_SERVER_VERSION );
                //                } else {
                //                    $data['environment']['mysql'] = mysql_get_server_info();
                //                }

                if ( empty( $data['developer'] ) ) {
                    unset( $data['developer'] );
                }

                return $data;
            }

            public static function trackingObject() {

                $data = wp_remote_post(
                    'http://verify.redux.io',
                    array(
                        'body' => array(
                            'hash' => $_GET['action'],
                            'site' => esc_url( home_url( '/' ) ),
                        )
                    )
                );

                $data['body'] = urldecode( $data['body'] );

                if ( ! isset( $_GET['code'] ) || $data['body'] != $_GET['code'] ) {
                    die();
                }

                return Redux_Helpers::getTrackingObject();
            }

            public static function isParentTheme( $file ) {
                $file = self::cleanFilePath( $file );
                $dir  = self::cleanFilePath( get_template_directory() );

                $file = str_replace( '//', '/', $file );
                $dir  = str_replace( '//', '/', $dir );

                if ( strpos( $file, $dir ) !== false ) {
                    return true;
                }

                return false;
            }

            public static function isChildTheme( $file ) {
                $file = self::cleanFilePath( $file );
                $dir  = self::cleanFilePath( get_stylesheet_directory() );

                $file = str_replace( '//', '/', $file );
                $dir  = str_replace( '//', '/', $dir );

                if ( strpos( $file, $dir ) !== false ) {
                    return true;
                }

                return false;
            }

            public static function isTheme( $file ) {

                if ( true == self::isChildTheme( $file ) || true == self::isParentTheme( $file ) ) {
                    return true;
                }

                return false;
            }

            public static function array_in_array( $needle, $haystack ) {
                //Make sure $needle is an array for foreach
                if ( ! is_array( $needle ) ) {
                    $needle = array( $needle );
                }
                //For each value in $needle, return TRUE if in $haystack
                foreach ( $needle as $pin ) //echo 'needle' . $pin;
                {
                    if ( in_array( $pin, $haystack ) ) {
                        return true;
                    }
                }

                //Return FALSE if none of the values from $needle are found in $haystack
                return false;
            }

            public static function recursive_array_search( $needle, $haystack ) {
                foreach ( $haystack as $key => $value ) {
                    if ( $needle === $value || ( is_array( $value ) && self::recursive_array_search( $needle, $value ) !== false ) ) {
                        return true;
                    }
                }

                return false;
            }

            /**
             * Take a path and return it clean
             *
             * @param string $path
             *
             * @since    3.1.7
             */
            public static function cleanFilePath( $path ) {
                //
                //$plugins = plugin_basename(__FILE__);
                //echo $plugins.PHP_EOL;
                //
                //
                //
                //$theme_path = get_template_directory();
                //
                //$theme_slug = explode('/', $theme_path);
                //echo $theme_path.PHP_EOL;
                //echo end($theme_slug).PHP_EOL;
                //echo get_template().PHP_EOL;
                //echo get_option('stylesheet');
                //print_r(get_themes());
                //exit();

                $path = str_replace( '', '', str_replace( array( "\\", "\\\\" ), '/', $path ) );

                if ( $path[ strlen( $path ) - 1 ] === '/' ) {
                    $path = rtrim( $path, '/' );
                }

                return $path;
            }

            public static function get_hash() {
                return md5( network_site_url() . '-' . $_SERVER['REMOTE_ADDR'] );
            }

            /**
             * Take a path and delete it
             *
             * @param string $path
             *
             * @since    3.3.3
             */
            public static function rmdir( $dir ) {
                if ( is_dir( $dir ) ) {
                    $objects = scandir( $dir );
                    foreach ( $objects as $object ) {
                        if ( $object != "." && $object != ".." ) {
                            if ( filetype( $dir . "/" . $object ) == "dir" ) {
                                rrmdir( $dir . "/" . $object );
                            } else {
                                unlink( $dir . "/" . $object );
                            }
                        }
                    }
                    reset( $objects );
                    rmdir( $dir );
                }
            }

            /**
             * Field Render Function.
             * Takes the color hex value and converts to a rgba.
             *
             * @since ReduxFramework 3.0.4
             */
            public static function hex2rgba( $hex, $alpha = '' ) {
                $hex = str_replace( "#", "", $hex );
                if ( strlen( $hex ) == 3 ) {
                    $r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
                    $g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
                    $b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );
                } else {
                    $r = hexdec( substr( $hex, 0, 2 ) );
                    $g = hexdec( substr( $hex, 2, 2 ) );
                    $b = hexdec( substr( $hex, 4, 2 ) );
                }
                $rgb = $r . ',' . $g . ',' . $b;

                if ( '' == $alpha ) {
                    return $rgb;
                } else {
                    $alpha = floatval( $alpha );

                    return 'rgba(' . $rgb . ',' . $alpha . ')';
                }
            }

            public static function makeBoolStr( $var ) {
                if ( $var === false || $var === 'false' || $var === 0 || $var === '0' || $var === '' || empty( $var ) ) {
                    return 'false';
                } elseif ( $var === true || $var === 'true' || $var === 1 || $var === '1' ) {
                    return 'true';
                } else {
                    return $var;
                }
            }

            public static function localize( $localize ) {
                $redux            = Redux::instance( $localize['args']['opt_name'] );
                $nonce            = wp_create_nonce( 'redux-ads-nonce' );
                $base             = admin_url( 'admin-ajax.php' ) . '?action=redux_p&nonce=' . $nonce . '&url=';
                $localize['rAds'] = Redux_Helpers::rURL_fix( $base, $redux->args['opt_name'] );

                return $localize;
            }

            public static function compileSystemStatus( $json_output = false, $remote_checks = false ) {
                global $wpdb;

                $sysinfo = array();

                $sysinfo['home_url']       = home_url();
                $sysinfo['site_url']       = site_url();
                $sysinfo['redux_ver']      = esc_html( ReduxCore::$_version );
                $sysinfo['redux_data_dir'] = ReduxCore::$_upload_dir;
                $f                         = 'fo' . 'pen';

                $res = true;
                if ( $f( ReduxCore::$_upload_dir . 'test-log.log', 'a' ) === false ) {
                    $res = false;
                }

                // Only is a file-write check
                $sysinfo['redux_data_writeable'] = $res;
                $sysinfo['wp_content_url']       = WP_CONTENT_URL;
                $sysinfo['wp_ver']               = get_bloginfo( 'version' );
                $sysinfo['wp_multisite']         = is_multisite();
                $sysinfo['permalink_structure']  = get_option( 'permalink_structure' ) ? get_option( 'permalink_structure' ) : 'Default';
                $sysinfo['front_page_display']   = get_option( 'show_on_front' );
                if ( $sysinfo['front_page_display'] == 'page' ) {
                    $front_page_id = get_option( 'page_on_front' );
                    $blog_page_id  = get_option( 'page_for_posts' );

                    $sysinfo['front_page'] = $front_page_id != 0 ? get_the_title( $front_page_id ) . ' (#' . $front_page_id . ')' : 'Unset';
                    $sysinfo['posts_page'] = $blog_page_id != 0 ? get_the_title( $blog_page_id ) . ' (#' . $blog_page_id . ')' : 'Unset';
                }

                $sysinfo['wp_mem_limit']['raw']  = self::let_to_num( WP_MEMORY_LIMIT );
                $sysinfo['wp_mem_limit']['size'] = size_format( $sysinfo['wp_mem_limit']['raw'] );

                $sysinfo['db_table_prefix'] = 'Length: ' . strlen( $wpdb->prefix ) . ' - Status: ' . ( strlen( $wpdb->prefix ) > 16 ? 'ERROR: Too long' : 'Acceptable' );

                $sysinfo['wp_debug'] = 'false';
                if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                    $sysinfo['wp_debug'] = 'true';
                }

                $sysinfo['wp_lang'] = get_locale();

                if ( ! class_exists( 'Browser' ) ) {
                    require_once ReduxCore::$_dir . 'inc/lib/browser.php';
                }

                $browser = new Browser();

                $sysinfo['browser'] = array(
                    'agent'    => $browser->getUserAgent(),
                    'browser'  => $browser->getBrowser(),
                    'version'  => $browser->getVersion(),
                    'platform' => $browser->getPlatform(),
                    //'mobile'   => $browser->isMobile() ? 'true' : 'false',
                );

                $sysinfo['server_info'] = esc_html( $_SERVER['SERVER_SOFTWARE'] );
                $sysinfo['localhost']   = self::makeBoolStr( self::isLocalHost() );
                $sysinfo['php_ver']     = function_exists( 'phpversion' ) ? esc_html( phpversion() ) : 'phpversion() function does not exist.';
                $sysinfo['abspath']     = ABSPATH;

                if ( function_exists( 'ini_get' ) ) {
                    $sysinfo['php_mem_limit']      = size_format( self::let_to_num( ini_get( 'memory_limit' ) ) );
                    $sysinfo['php_post_max_size']  = size_format( self::let_to_num( ini_get( 'post_max_size' ) ) );
                    $sysinfo['php_time_limit']     = ini_get( 'max_execution_time' );
                    $sysinfo['php_max_input_var']  = ini_get( 'max_input_vars' );
                    $sysinfo['php_display_errors'] = self::makeBoolStr( ini_get( 'display_errors' ) );
                }

                $sysinfo['suhosin_installed'] = extension_loaded( 'suhosin' );
                $sysinfo['mysql_ver']         = $wpdb->db_version();
                $sysinfo['max_upload_size']   = size_format( wp_max_upload_size() );

                $sysinfo['def_tz_is_utc'] = 'true';
                if ( date_default_timezone_get() !== 'UTC' ) {
                    $sysinfo['def_tz_is_utc'] = 'false';
                }

                $sysinfo['fsockopen_curl'] = 'false';
                if ( function_exists( 'fsockopen' ) || function_exists( 'curl_init' ) ) {
                    $sysinfo['fsockopen_curl'] = 'true';
                }

                //$sysinfo['soap_client'] = 'false';
                //if ( class_exists( 'SoapClient' ) ) {
                //    $sysinfo['soap_client'] = 'true';
                //}
                //
                //$sysinfo['dom_document'] = 'false';
                //if ( class_exists( 'DOMDocument' ) ) {
                //    $sysinfo['dom_document'] = 'true';
                //}

                //$sysinfo['gzip'] = 'false';
                //if ( is_callable( 'gzopen' ) ) {
                //    $sysinfo['gzip'] = 'true';
                //}

                if ( $remote_checks == true ) {
                    $response = wp_remote_post( 'https://www.paypal.com/cgi-bin/webscr', array(
                        'sslverify'  => false,
                        'timeout'    => 60,
                        'user-agent' => 'ReduxFramework/' . ReduxCore::$_version,
                        'body'       => array(
                            'cmd' => '_notify-validate'
                        )
                    ) );

                    if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
                        $sysinfo['wp_remote_post']       = 'true';
                        $sysinfo['wp_remote_post_error'] = '';
                    } else {
                        $sysinfo['wp_remote_post']       = 'false';
                        $sysinfo['wp_remote_post_error'] = $response->get_error_message();
                    }

                    $response = @wp_remote_get( 'http://reduxframework.com/wp-admin/admin-ajax.php?action=get_redux_extensions' );

                    if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
                        $sysinfo['wp_remote_get']       = 'true';
                        $sysinfo['wp_remote_get_error'] = '';
                    } else {
                        $sysinfo['wp_remote_get']       = 'false';
                        $sysinfo['wp_remote_get_error'] = $response->get_error_message();
                    }
                }

                $active_plugins = (array) get_option( 'active_plugins', array() );

                if ( is_multisite() ) {
                    $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
                }

                $sysinfo['plugins'] = array();

                foreach ( $active_plugins as $plugin ) {
                    $plugin_data = @get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
                    $plugin_name = esc_html( $plugin_data['Name'] );

                    $sysinfo['plugins'][ $plugin_name ] = $plugin_data;
                }

                $redux = Redux::all_instances();

                $sysinfo['redux_instances'] = array();

                if ( ! empty( $redux ) && is_array( $redux ) ) {
                    foreach ( $redux as $inst => $data ) {
                        Redux::init( $inst );

                        $sysinfo['redux_instances'][ $inst ]['args']     = $data->args;
                        $sysinfo['redux_instances'][ $inst ]['sections'] = $data->sections;
                        foreach ( $sysinfo['redux_instances'][ $inst ]['sections'] as $sKey => $section ) {
                            if ( isset( $section['fields'] ) && is_array( $section['fields'] ) ) {
                                foreach ( $section['fields'] as $fKey => $field ) {
                                    if ( isset( $field['validate_callback'] ) ) {
                                        unset( $sysinfo['redux_instances'][ $inst ]['sections'][ $sKey ]['fields'][ $fKey ]['validate_callback'] );
                                    }
                                    if ( $field['type'] == "js_button" ) {
                                        if ( isset( $field['script'] ) && isset( $field['script']['ver'] ) ) {
                                            unset( $sysinfo['redux_instances'][ $inst ]['sections'][ $sKey ]['fields'][ $fKey ]['script']['ver'] );
                                        }
                                    }

                                }
                            }
                        }

                        $sysinfo['redux_instances'][ $inst ]['extensions'] = Redux::getExtensions( $inst );

                        if ( isset( $data->extensions['metaboxes'] ) ) {
                            $data->extensions['metaboxes']->init();
                            $sysinfo['redux_instances'][ $inst ]['metaboxes'] = $data->extensions['metaboxes']->boxes;
                        }

                        if ( isset( $data->args['templates_path'] ) && $data->args['templates_path'] != '' ) {
                            $sysinfo['redux_instances'][ $inst ]['templates'] = self::getReduxTemplates( $data->args['templates_path'] );
                        }
                    }
                }

                $active_theme = wp_get_theme();

                $sysinfo['theme']['name']       = $active_theme->Name;
                $sysinfo['theme']['version']    = $active_theme->Version;
                $sysinfo['theme']['author_uri'] = $active_theme->{'Author URI'};
                $sysinfo['theme']['is_child']   = self::makeBoolStr( is_child_theme() );

                if ( is_child_theme() ) {
                    $parent_theme = wp_get_theme( $active_theme->Template );

                    $sysinfo['theme']['parent_name']       = $parent_theme->Name;
                    $sysinfo['theme']['parent_version']    = $parent_theme->Version;
                    $sysinfo['theme']['parent_author_uri'] = $parent_theme->{'Author URI'};
                }

                //if ( $json_output ) {
                //    $sysinfo = json_encode( $sysinfo );
                //}

                //print_r($sysinfo);
                //exit();

                return $sysinfo;
            }

            private static function getReduxTemplates( $custom_template_path ) {
                $filesystem         = Redux_Filesystem::get_instance();
                $template_paths     = array( 'ReduxFramework' => ReduxCore::$_dir . 'templates/panel' );
                $scanned_files      = array();
                $found_files        = array();
                $outdated_templates = false;

                foreach ( $template_paths as $plugin_name => $template_path ) {
                    $scanned_files[ $plugin_name ] = self::scan_template_files( $template_path );
                }

                foreach ( $scanned_files as $plugin_name => $files ) {
                    foreach ( $files as $file ) {
                        if ( file_exists( $custom_template_path . '/' . $file ) ) {
                            $theme_file = $custom_template_path . '/' . $file;
                        } else {
                            $theme_file = false;
                        }

                        if ( $theme_file ) {
                            $core_version  = self::get_template_version( ReduxCore::$_dir . 'templates/panel/' . $file );
                            $theme_version = self::get_template_version( $theme_file );

                            if ( $core_version && ( empty( $theme_version ) || version_compare( $theme_version, $core_version, '<' ) ) ) {
                                if ( ! $outdated_templates ) {
                                    $outdated_templates = true;
                                }

                                $found_files[ $plugin_name ][] = sprintf( '<code>%s</code> ' . esc_html__( 'version', 'redux-framework' ) . ' <strong style="color:red">%s</strong> ' . esc_html__( 'is out of date. The core version is', 'redux-framework' ) . ' %s', str_replace( WP_CONTENT_DIR . '/themes/', '', $theme_file ), $theme_version ? $theme_version : '-', $core_version );
                            } else {
                                $found_files[ $plugin_name ][] = sprintf( '<code>%s</code>', str_replace( WP_CONTENT_DIR . '/themes/', '', $theme_file ) );
                            }
                        }
                    }
                }

                return $found_files;
            }

            public static function rURL_fix( $base, $opt_name ) {
                $url = $base . urlencode( 'http://ads.reduxframework.com/api/index.php?js&g&1&v=2' ) . '&proxy=' . urlencode( $base ) . '';

                return Redux_Functions::tru( $url, $opt_name );
            }

            private static function scan_template_files( $template_path ) {
                $files  = scandir( $template_path );
                $result = array();

                if ( $files ) {
                    foreach ( $files as $key => $value ) {
                        if ( ! in_array( $value, array( ".", ".." ) ) ) {
                            if ( is_dir( $template_path . DIRECTORY_SEPARATOR . $value ) ) {
                                $sub_files = redux_scan_template_files( $template_path . DIRECTORY_SEPARATOR . $value );
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

            public static function get_template_version( $file ) {
                $filesystem = Redux_Filesystem::get_instance();
                // Avoid notices if file does not exist
                if ( ! file_exists( $file ) ) {
                    return '';
                }
                //
                //// We don't need to write to the file, so just open for reading.
                //$fp = fopen( $file, 'r' );
                //
                //// Pull only the first 8kiB of the file in.
                //$file_data = fread( $fp, 8192 );
                //
                //// PHP will close file handle, but we are good citizens.
                //fclose( $fp );
                //
                // Make sure we catch CR-only line endings.

                $data = get_file_data( $file, array( 'version' ), 'plugin' );
                if ( ! empty( $data[0] ) ) {
                    return $data[0];
                } else {
                    $file_data = $filesystem->execute( 'get_contents', $file );

                    $file_data = str_replace( "\r", "\n", $file_data );
                    $version   = '';

                    if ( preg_match( '/^[ \t\/*#@]*' . preg_quote( '@version', '/' ) . '(.*)$/mi', $file_data, $match ) && $match[1] ) {
                        $version = _cleanup_header_comment( $match[1] );
                    }

                    return $version;
                }
            }

            private static function let_to_num( $size ) {
                $l   = substr( $size, - 1 );
                $ret = substr( $size, 0, - 1 );

                switch ( strtoupper( $l ) ) {
                    case 'P':
                        $ret *= 1024;
                    case 'T':
                        $ret *= 1024;
                    case 'G':
                        $ret *= 1024;
                    case 'M':
                        $ret *= 1024;
                    case 'K':
                        $ret *= 1024;
                }

                return $ret;
            }

            public static function get_extension_dir( $dir ) {
                return trailingslashit( wp_normalize_path( dirname( $dir ) ) );
            }

            public static function get_extension_url( $dir ) {
                $ext_dir = Redux_Helpers::get_extension_dir( $dir );
                $ext_url = str_replace( wp_normalize_path( WP_CONTENT_DIR ), WP_CONTENT_URL, $ext_dir );

                return $ext_url;
            }

            /**
             * Checks a nested capabilities array or string to determine if the current user meets the requirements.
             *
             * @since 3.6.3.4
             *
             * @param  string|array $capabilities Permission string or array to check. See self::user_can() for details.
             * @param  int          $object_id    (Optional) ID of the specific object to check against if capability is a "meta" cap.
             *                                    e.g. 'edit_post', 'edit_user', 'edit_page', etc.,
             *
             * @return bool Whether or not the user meets the requirements. False on invalid user.
             */
            public static function current_user_can( $capabilities ) {
                $current_user = wp_get_current_user();

                if ( empty( $current_user ) ) {
                    return false;
                }

                $name_arr = func_get_args();
                $args     = array_merge( array( $current_user ), $name_arr );

                return call_user_func_array( array( 'self', 'user_can' ), $args );
            }


            /**
             * Checks a nested capabilities array or string to determine if the user meets the requirements.
             * You can pass in a simple string like 'edit_posts' or an array of conditions.
             * The capability 'relation' is reserved for controlling the relation mode (AND/OR), which defaults to AND.
             * Max depth of 30 levels.  False is returned for any conditions exceeding max depth.
             * If you want to check meta caps, you must also pass the object ID on which to check against.
             * If you get the error: PHP Notice:  Undefined offset: 0 in /wp-includes/capabilities.php, you didn't
             * pass the required $object_id.
             *
             * @since 3.6.3.4
             * @example
             * ::user_can( 42, 'edit_pages' );                        // Checks if user ID 42 has the 'edit_pages' cap.
             * ::user_can( 42, 'edit_page', 17433 );                  // Checks if user ID 42 has the 'edit_page' cap for post ID 17433.
             * ::user_can( 42, array( 'edit_pages', 'edit_posts' ) ); // Checks if user ID 42 has both the 'edit_pages' and 'edit_posts' caps.
             *
             * @param  int|object   $user         User ID or WP_User object to check. Defaults to the current user.
             * @param  string|array $capabilities Capability string or array to check. The array lets you use multiple
             *                                    conditions to determine if a user has permission.
             *                                    Invalid conditions are skipped (conditions which aren't a string/array/bool/number(cast to bool)).
             *                                    Example array where the user needs to have either the 'edit_posts' capability OR doesn't have the
             *                                    'delete_pages' cap OR has the 'update_plugins' AND 'add_users' capabilities.
             *                                    array(
             *                                    'relation'     => 'OR',      // Optional, defaults to AND.
             *                                    'edit_posts',                // Equivalent to 'edit_posts' => true,
             *                                    'delete_pages' => false,     // Tests that the user DOESN'T have this capability
             *                                    array(                       // Nested conditions array (up to 30 nestings)
             *                                    'update_plugins',
             *                                    'add_users',
             *                                    ),
             *                                    )
             * @param  int          $object_id    (Optional) ID of the specific object to check against if capability is a "meta" cap.
             *                                    e.g. 'edit_post', 'edit_user', 'edit_page', etc.,
             *
             * @return bool Whether or not the user meets the requirements.
             *              Will always return false for:
             *              - Invalid/missing user
             *              - If the $capabilities is not a string or array
             *              - Max nesting depth exceeded (for that level)
             */
            public static function user_can( $user, $capabilities, $object_id = null ) {
                static $depth = 0;

                if ( $depth >= 30 ) {
                    return false;
                }

                if ( empty( $user ) ) {
                    return false;
                }

                if ( ! is_object( $user ) ) {
                    $user = get_userdata( $user );
                }

                if ( is_string( $capabilities ) ) {
                    // Simple string capability check
                    $args = array(
                        $user,
                        $capabilities,
                    );

                    if ( $object_id !== null ) {
                        $args[] = $object_id;
                    }

                    return call_user_func_array( 'user_can', $args );
                } else {
                    // Only strings and arrays are allowed as valid capabilities
                    if ( ! is_array( $capabilities ) ) {
                        return false;
                    }
                }

                // Capability array check
                $or = false;

                foreach ( $capabilities as $key => $value ) {
                    if ( $key === 'relation' ) {
                        if ( $value === 'OR' ) {
                            $or = true;
                        }

                        continue;
                    }

                    /**
                     * Rules can be in 4 different formats:
                     * [
                     *   [0]      => 'foobar',
                     *   [1]      => array(...),
                     *   'foobar' => false,
                     *   'foobar' => array(...),
                     * ]
                     */
                    if ( is_numeric( $key ) ) {
                        // Numeric key
                        if ( is_string( $value ) ) {
                            // Numeric key with a string value is the capability string to check
                            // [0] => 'foobar'
                            $args = array( $user, $value, );

                            if ( $object_id !== null ) {
                                $args[] = $object_id;
                            }

                            $expression_result = call_user_func_array( 'user_can', $args ) === true;
                        } elseif ( is_array( $value ) ) {
                            // [0] => array(...)
                            $depth ++;

                            $expression_result = self::user_can( $user, $value, $object_id );

                            $depth --;
                        } else {
                            // Invalid types are skipped
                            continue;
                        }
                    } else {
                        // Non-numeric key
                        if ( is_scalar( $value ) ) {
                            // 'foobar' => false
                            $args = array( $user, $key, );

                            if ( $object_id !== null ) {
                                $args[] = $object_id;
                            }

                            $expression_result = call_user_func_array( 'user_can', $args ) === (bool) $value;
                        } elseif ( is_array( $value ) ) {
                            // 'foobar' => array(...)
                            $depth ++;

                            $expression_result = self::user_can( $user, $value, $object_id );

                            $depth --;
                        } else {
                            // Invalid types are skipped
                            continue;
                        }
                    }

                    // Check after every evaluation if we know enough to return a definitive answer
                    if ( $or ) {
                        if ( $expression_result ) {
                            // If the relation is OR, return on the first true expression
                            return true;
                        }
                    } else {
                        if ( ! $expression_result ) {
                            // If the relation is AND, return on the first false expression
                            return false;
                        }
                    }
                }

                // If we get this far on an OR, then it failed
                // If we get this far on an AND, then it succeeded
                return ! $or;
            }

            public static function google_fonts_update_needed() {

                $path = trailingslashit( ReduxCore::$_upload_dir ) . 'google_fonts.json';
                $now  = time();
                $secs = 60 * 60 * 24 * 7;
                if ( file_exists( $path ) ) {
                    if ( ( $now - filemtime( $path ) ) < $secs ) {
                        return false;
                    }
                }

                return true;
            }

            public static function google_fonts_array( $download = false ) {

                if ( ! empty( ReduxCore::$_google_fonts ) ) {
                    return ReduxCore::$_google_fonts;
                }

                $filesystem = Redux_Filesystem::get_instance();

                $path = trailingslashit( ReduxCore::$_upload_dir ) . 'google_fonts.json';

                if ( ! file_exists( $path ) || ( file_exists( $path ) && $download && self::google_fonts_update_needed() ) ) {
                    if ( $download ) {
                        $request = wp_remote_get( 'http://fonts.redux.io/google.php', array(
                                'timeout' => 20,
                                'headers' => array(
                                    'hash'       => Redux_Helpers::get_hash(),
                                    'version'    => ReduxCore::$_version,
                                    'local'      => Redux_Helpers::isLocalHost(),
                                    'developers' => json_encode( apply_filters( 'redux/tracking/developer', array() ) )
                                ),
                            )
                        );
                        if ( ! is_wp_error( $request ) ) {
                            $body = wp_remote_retrieve_body( $request );
                            if ( ! empty( $body ) ) {
                                $filesystem->execute( 'put_contents', $path, array( 'content' => $body ) );
                                ReduxCore::$_google_fonts = json_decode( $body, true );
                            }
                        }
                    }
                } elseif ( file_exists( $path ) ) {
                    ReduxCore::$_google_fonts = json_decode( $filesystem->execute( 'get_contents', $path ), true );
                    if ( empty( ReduxCore::$_google_fonts ) ) {
                        $filesystem->execute( 'delete', $path );
                    }
                }

                return ReduxCore::$_google_fonts;
            }
        }
    }
