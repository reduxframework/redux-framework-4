<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if (!class_exists('Redux_Args')) {

    class Redux_Args {

        public $get             = array();

        private $framework_url  = 'http://www.reduxframework.com/';
        private $parent         = null;

        public function __construct ($parent, $args) {
            $this->parent = $parent;

            $default = array(
                'opt_name'                  => '',
                'google_api_key'            => '',
                'google_update_weekly'      => false,
                'last_tab'                  => '',
                'menu_icon'                 => '',
                'menu_title'                => '',
                'page_title'                => '',
                'page_slug'                 => '',
                'page_permissions'          => 'manage_options',
                'menu_type'                 => 'menu',
                'page_parent'               => 'themes.php',
                'page_priority'             => null,
                'allow_sub_menu'            => true,
                'save_defaults'             => true,
                'footer_credit'             => '',
                'async_typography'          => false,
                'disable_google_fonts_link' => false,
                'class'                     => '',
                'admin_bar'                 => true,
                'admin_bar_priority'        => 999,
                'admin_bar_icon'            => '',
                'help_tabs'                 => array(),
                'help_sidebar'              => '',
                'database'                  => '',
                'customizer'                => false,
                'global_variable'           => '',
                'output'                    => true,
                'compiler'                  => true,
                'output_tag'                => true,
                'output_location'           => array( 'frontend' ),
                'transient_time'            => '',
                'default_show'              => false,
                'default_mark'              => '',
                'update_notice'             => true,
                'disable_save_warn'         => false,
                'open_expanded'             => false,
                'hide_expand'               => false,
                'network_admin'             => false,
                'network_sites'             => true,
                'hide_reset'                => false,
                'hide_save'                 => false,
                'hints'                     => array(
                    'icon'          => 'el el-question-sign',
                    'icon_position' => 'right',
                    'icon_color'    => 'lightgray',
                    'icon_size'     => 'normal',
                    'tip_style'     => array(
                        'color'   => 'light',
                        'shadow'  => true,
                        'rounded' => false,
                        'style'   => '',
                    ),
                    'tip_position'  => array(
                        'my' => 'top_left',
                        'at' => 'bottom_right',
                    ),
                    'tip_effect'    => array(
                        'show' => array(
                            'effect'   => 'slide',
                            'duration' => '500',
                            'event'    => 'mouseover',
                        ),
                        'hide' => array(
                            'effect'   => 'fade',
                            'duration' => '500',
                            'event'    => 'click mouseleave',
                        ),
                    ),
                ),
                'show_import_export'        => true,
                'show_options_object'       => true,
                'dev_mode'                  => true,
                'templates_path'            => '',
                'ajax_save'                 => true,
                'use_cdn'                   => true,
                'cdn_check_time'            => 1440,
                'options_api'               => true,
                'allow_tracking'            => true,
                'flyout_submenus'           => true,
                'admin_theme'               => 'wordpress'
            );

            $args = wp_parse_args( $args, $default );

            $args = $this->args($args);
            $args = $this->default_cleanup($args);

            $this->get = $args;

            $this->parent->args = $args;
                    
            $this->change_demo_defaults($args);
        }

        private function args($args) {
            $args = $this->no_errors_please($args);

            $this->parent->old_opt_name = $args['opt_name'];

            $args = $this->filters($args);

            $this->parent->core_instance    = chr(64 + rand( 1, 26 )) . time() . '_' . rand(0,1000000);
            $this->parent->core_thread      = chr(64 + rand( 1, 26 )) . time() . '_' . rand(0,1000000);

            if ( $args['opt_name'] == $this->parent->old_opt_name ) {
                $this->parent->old_opt_name = null;
                unset($this->parent->old_opt_name);
            }

            // Do not save the defaults if we're on a live preview!
            if ( $GLOBALS['pagenow'] == "customize" && isset( $_GET['theme'] ) && ! empty( $_GET['theme'] ) ) {
                $args['save_defaults'] = false;
            }

            $args = $this->shim($args);

            return $args;
        }

        private function filters($args) {
            /**
             * filter 'redux/args/{opt_name}'
             *
             * @param  array $args ReduxFramework configuration
             */
            $args = apply_filters( "redux/args/{$args['opt_name']}", $args );

            /**
             * filter 'redux/options/{opt_name}/args'
             *
             * @param  array $args ReduxFramework configuration
             */
            $args = apply_filters( "redux/options/{$args['opt_name']}/args", $args );

            return $args;
        }

        private function no_errors_please($args) {
            if ( empty ( $args['transient_time'] ) ) {
                $args['transient_time'] = 60 * MINUTE_IN_SECONDS;
            }

            if ( empty ( $args['footer_credit'] ) ) {
                $args['footer_credit'] = '<span id="footer-thankyou">' . sprintf( esc_html__( 'Psowered by  %1$s', 'redux-framework' ), '<a href="' . esc_url( $this->framework_url ) . '" target="_blank">' . esc_html__( 'Redux Framework', 'redux-framework' ) . '</a> v' . ReduxCore::$_version ) . '</span>';
            }

            if ( empty ( $args['menu_title'] ) ) {
                $args['menu_title'] = esc_html__( 'Options', 'redux-framework' );
            }

            if ( empty ( $args['page_title'] ) ) {
                $args['page_title'] = esc_html__( 'Options', 'redux-framework' );
            }

            // Auto create the page_slug appropriately
            if ( empty( $args['page_slug'] ) ) {
                if ( ! empty( $args['display_name'] ) ) {
                    $args['page_slug'] = sanitize_html_class( $args['display_name'] );
                } else if ( ! empty( $args['page_title'] ) ) {
                    $args['page_slug'] = sanitize_html_class( $args['page_title'] );
                } else if ( ! empty( $args['menu_title'] ) ) {
                    $args['page_slug'] = sanitize_html_class( $args['menu_title'] );
                } else {
                    $args['page_slug'] = str_replace( '-', '_', $args['opt_name'] );
                }
            }
            
            return $args;
        }

        private function shim($args){
            /**
             * SHIM SECTION
             * Old variables and ways of doing things that need correcting.  ;)
             * */

            // Variable name change
            if ( ! empty ( $args['page_cap'] ) ) {
                $args['page_permissions'] = $args['page_cap'];
                unset ( $args['page_cap'] );
            }

            if ( ! empty ( $args['page_position'] ) ) {
                $args['page_priority'] = $args['page_position'];
                unset ( $args['page_position'] );
            }

            if ( ! empty ( $args['page_type'] ) ) {
                $args['menu_type'] = $args['page_type'];
                unset ( $args['page_type'] );
            }

            return $args;
        }

        private function change_demo_defaults($args) {
            if ( $args['dev_mode'] || Redux_Helpers::isLocalHost() == true ) {
                if ( ! empty( $args['admin_bar_links'] ) ) {
                    foreach ( $args['admin_bar_links'] as $idx => $arr ) {
                        if ( is_array( $arr ) && ! empty( $arr ) ) {
                            foreach ( $arr as $x => $y ) {
                                if ( strpos( strtolower( $y ), 'redux' ) !== false ) {
                                    $msg = '<strong>' . esc_html__('Redux Framework Notice', 'redux-framework') . ' </strong>' . esc_html__('There are references to the Redux Framework support site in your config\'s ','redux-framework') . '<code>admin_bar_links</code> ' . esc_html__('argument.  This is sample data.  Please change or remove this data before shipping your product.', 'redux-framework' );
                                    $this->display_arg_change_notice( 'admin', $msg );
                                    $this->parent->omit_admin_items = true;
                                    continue;
                                }
                            }
                        }
                    }
                }

                if ( ! empty( $args['share_icons'] ) ) {
                    foreach ( $args['share_icons'] as $idx => $arr ) {
                        if ( is_array( $arr ) && ! empty( $arr ) ) {
                            foreach ( $arr as $x => $y ) {
                                if ( strpos( strtolower( $y ), 'redux' ) !== false ) {
                                    $msg = '<strong>' . esc_html__('Redux Framework Notice:', 'redux-framework') .  '</strong>' . esc_html__('There are references to the Redux Framework support site in your config\'s','redux-framework') . ' <code>share_icons</code> ' . esc_html__('argument.  This is sample data.  Please change or remove this data before shipping your product.', 'redux-framework' );
                                    $this->display_arg_change_notice( 'share', $msg );
                                    $this->parent->omit_share_icons = true;
                                }
                            }
                        }
                    }
                }
            }
        }

        private function display_arg_change_notice( $mode, $msg = '' ) {
            if ( $mode == 'admin' ) {
                if ( ! $this->parent->omit_admin_items ) {
                    $data = array(
                        'parent'    => $this->parent,
                        'type'      => 'error',
                        'msg'       => $msg,
                        'id'        => 'admin_config',
                        'dismiss'   => true
                    );

                    Redux_Admin_Notices::set_notice($data);
                }
            }

            if ( $mode == 'share' ) {
                if ( ! $this->parent->omit_share_icons ) {
                    $data = array(
                        'parent'    => $this->parent,
                        'type'      => 'error',
                        'msg'       => $msg,
                        'id'        => 'share_config',
                        'dismiss'   => true
                    );

                    Redux_Admin_Notices::set_notice($data);
                }
            }
        }

        private function default_cleanup($args) {

            // Fix the global variable name
            if ( $args['global_variable'] == "" && $args['global_variable'] !== false ) {
                $args['global_variable'] = str_replace( '-', '_', $args['opt_name'] );
            }

            // Force dev_mode on WP_DEBUG = true and if it's a local server
            if ( Redux_Helpers::isLocalHost() || ( Redux_Helpers::isWpDebug() ) ) {
                if ( $args['dev_mode'] != true ) {
                    $args['update_notice'] = false;
                }

                $this->parent->dev_mode_forced = true;
                $args['dev_mode'] = true;
            }

            if ( isset( $args['customizer_only'] ) && $args['customizer_only'] ) {
                $args['menu_type']      = 'hidden';
                $args['customizer']     = true;
                $args['admin_bar']      = false;
                $args['allow_sub_menu'] = false;
            }

            // Check if the Airplane Mode plugin is installed
            if ( class_exists( 'Airplane_Mode_Core' ) ) {
                $airplane = Airplane_Mode_Core::getInstance();
                if ( method_exists( $airplane, 'enabled' ) ) {
                    if ( $airplane->enabled() ) {
                        $args['use_cdn'] = false;
                    }
                } else if ( $airplane->check_status() == 'on' ) {
                    $args['use_cdn'] = false;
                }
            }

            return $args;
        }
    }
}
