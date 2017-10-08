<?php

    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    if ( ! class_exists( 'Redux_Enqueue' ) ) {

        class Redux_Enqueue extends Redux_Class {

            private $min = '';
            private $timestamp = '';

            public function __construct( $parent ) {
                parent::__construct( $parent );

                // Enqueue the admin page CSS and JS
                if ( isset ( $_GET['page'] ) && $_GET['page'] == $parent->args['page_slug'] ) {
                    add_action( 'admin_enqueue_scripts', array( $this, 'init' ), 1 );
                }
            }

            public function init() {
                $core = $this->core();

                Redux_Functions::$_parent = $core;
                Redux_CDN::$_parent       = $core;

                $this->min = Redux_Functions::isMin();

                $this->timestamp = ReduxCore::$_version;
                if ( $core->args['dev_mode'] ) {
                    $this->timestamp .= '.' . time();
                }

                $this->register_styles( $core );
                $this->register_scripts( $core );

                add_thickbox();

                $this->enqueue_fields( $core );

                add_filter( "redux/{$core->args['opt_name']}/localize", array( 'Redux_Helpers', 'localize' ) );

                $this->set_localized_data( $core );

                /**
                 * action 'redux/page/{opt_name}/enqueue'
                 */
                do_action( "redux/page/{$core->args['opt_name']}/enqueue" );
            }

            private function register_styles( $core ) {

                //*****************************************************************
                // Redux Admin CSS
                //*****************************************************************

                if ( $core->args['admin_theme'] == 'wordpress' || $core->args['admin_theme'] == 'wp' ) {
                    $color_scheme = get_user_option( 'admin_color' );

                } elseif ( $core->args['admin_theme'] == 'classic' || $core->args['admin_theme'] == '' ) {
                    $color_scheme = 'classic';
                } else {
                    $color_scheme = $core->args['admin_theme'];
                }
                
                if (!file_exists(ReduxCore::$_dir . "assets/css/colors/$color_scheme/colors{$this->min}.css")) {
                    $color_scheme = 'fresh';
                }
                
                $css = ReduxCore::$_url . "assets/css/colors/$color_scheme/colors{$this->min}.css";

                $css = apply_filters( 'redux/enqueue/' . $core->args['opt_name'] . '/args/admin_theme/css_url', $css );

                wp_register_style(
                  'redux-admin-theme-css',
                  $css,
                  array(),
                  $this->timestamp,
                  'all'
                );

                wp_enqueue_style(
                  'redux-admin-css',
                  ReduxCore::$_url."assets/css/redux-admin{$this->min}.css",
                  array('redux-admin-theme-css'),
                  $this->timestamp,
                  'all'
                );

                //*****************************************************************
                // Redux Fields CSS
                //*****************************************************************
                if ( ! $core->args['dev_mode'] ) {
                    wp_enqueue_style(
                      'redux-fields-css',
                      ReduxCore::$_url . 'assets/css/redux-fields.min.css',
                      array(),
                      $this->timestamp,
                      'all'
                    );
                }

                //*****************************************************************
                // Select2 CSS
                //*****************************************************************
                Redux_CDN::register_style(
                  'select2-css',
                  "//cdn.jsdelivr.net/select2/4.0.3/css/select2{$this->min}.css",
                  array(),
                  '4.0.3',
                  'all'
                );

                //*****************************************************************
                // Spectrum CSS
                //*****************************************************************
                $css_file = 'redux-spectrum.css';

                wp_register_style(
                  'redux-spectrum-css',
                  ReduxCore::$_url . "assets/css/vendor/spectrum{$this->min}.css",
                  array(),
                  '1.3.3',
                  'all'
                );

                //*****************************************************************
                // Elusive Icon CSS
                //*****************************************************************
                wp_enqueue_style(
                  'redux-elusive-icon',
                  ReduxCore::$_url . "assets/css/vendor/elusive-icons{$this->min}.css",
                  array(),
                  $this->timestamp,
                  'all'
                );

                //*****************************************************************
                // QTip CSS
                //*****************************************************************

                wp_enqueue_style(
                  'qtip-css',
                  ReduxCore::$_url . "assets/css/vendor/qtip{$this->min}.css",
                  array(),
                  '2.2.0',
                  'all'
                );

                //*****************************************************************
                // JQuery UI CSS
                //*****************************************************************
                wp_enqueue_style(
                  'jquery-ui-css',
                  apply_filters( "redux/page/{$core->args['opt_name']}/enqueue/jquery-ui-css", ReduxCore::$_url . 'assets/css/vendor/jquery-ui-1.10.0.custom.css' ),
                  array(),
                  $this->timestamp,
                  'all'
                );

                //*****************************************************************
                // Iris CSS
                //*****************************************************************
                wp_enqueue_style( 'wp-color-picker' );

                if ( $core->args['dev_mode'] ) {

                    //*****************************************************************
                    // Color Picker CSS
                    //*****************************************************************
                    wp_register_style(
                      'redux-color-picker-css',
                      ReduxCore::$_url . 'assets/css/color-picker.css',
                      array( 'wp-color-picker' ),
                      $this->timestamp,
                      'all'
                    );

                    //*****************************************************************
                    // Media CSS
                    //*****************************************************************
                    wp_enqueue_style(
                      'redux-field-media-css',
                      ReduxCore::$_url . 'assets/css/media.css',
                      array(),
                      $this->timestamp,
                      'all'
                    );
                }

                //*****************************************************************
                // RTL CSS
                //*****************************************************************
                if ( is_rtl() ) {
                    wp_enqueue_style(
                      'redux-rtl-css',
                      ReduxCore::$_url . 'assets/css/rtl.css',
                      array( 'redux-admin-css' ),
                      $this->timestamp,
                      'all'
                    );
                }

            }

            private function register_scripts( $core ) {
                //*****************************************************************
                // JQuery / JQuery UI JS
                //*****************************************************************
                wp_enqueue_script( 'jquery' );
                wp_enqueue_script( 'jquery-ui-core' );
                wp_enqueue_script( 'jquery-ui-dialog' );

                //*****************************************************************
                // Select2 Sortable JS
                //*****************************************************************
                wp_register_script(
                  'redux-select2-sortable-js',
                  ReduxCore::$_url . 'assets/js/vendor/select2-sortable/redux.select2.sortable' . $this->min . '.js',
                  array( 'jquery' ),
                  $this->timestamp,
                  true
                );

                //*****************************************************************
                // Select2 JS
                //*****************************************************************

                // JWp6 plugin giving us problems.  They need to update.
//                if ( wp_script_is( 'jquerySelect2' ) ) {
//                    wp_deregister_script( 'jquerySelect2' );
//                    wp_dequeue_script( 'jquerySelect2' );
//                    wp_dequeue_style( 'jquerySelect2Style' );
//                }


                Redux_CDN::register_script(
                  'select2-js',
                  '//cdn.jsdelivr.net/select2/4.0.3/js/select2' . $this->min . '.js',
                  array( 'jquery', 'redux-select2-sortable-js' ),
                  '4.0.3',
                  true
                );

                //*****************************************************************
                // QTip JS
                //*****************************************************************
                wp_enqueue_script(
                  'qtip-js',
                  ReduxCore::$_url . 'assets/js/vendor/qtip/qtip' . $this->min . '.js',
                  array( 'jquery' ),
                  '2.2.0',
                  true
                );

                //*****************************************************************
                // Spectrum JS
                //*****************************************************************
                $js_file = 'redux-spectrum.min.js';
                if ( $core->args['dev_mode'] ) {
                    $js_file = 'redux-spectrum.js';
                }

                wp_register_script(
                  'redux-spectrum-js',
                  ReduxCore::$_url . 'assets/js/vendor/spectrum/' . $js_file,
                  array( 'jquery' ),
                  '1.3.3',
                  true
                );

                $depArray = array( 'jquery' );

                //*****************************************************************
                // Vendor JS
                //*****************************************************************
                wp_register_script(
                  'redux-vendor',
                  ReduxCore::$_url . 'assets/js/redux-vendors' . $this->min . '.js',
                  array( 'jquery' ),
                  $this->timestamp,
                  true
                );

                array_push( $depArray, 'redux-vendor' );


                //*****************************************************************
                // Redux JS
                //*****************************************************************
                wp_register_script(
                  'redux-js',
                  ReduxCore::$_url . 'assets/js/redux' . $this->min . '.js',
                  $depArray,
                  $this->timestamp,
                  true
                );

                if ( $core->args['async_typography'] ) {
                    wp_enqueue_script(
                      'webfontloader',
                      'https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js',
                      array( 'jquery' ),
                      '1.6.26',
                      true
                    );
                }
            }

            public function _enqueue_field( $core, $field ) {
                if ( isset( $field['type'] ) && $field['type'] != 'callback' ) {

                    $field_class = 'ReduxFramework_' . $field['type'];

                    /**
                     * Field class file
                     * filter 'redux/{opt_name}/field/class/{field.type}
                     *
                     * @param       string        field class file path
                     * @param array $field        field config data
                     */

                    $core_path = ReduxCore::$_dir . "inc/fields/{$field['type']}/field_{$field['type']}.php";

                    if (ReduxCore::$_pro_loaded) {
                        $pro_path = ReduxPro::$_dir . "inc/fields/{$field['type']}/field_{$field['type']}.php";
                        
                        if (file_exists( $pro_path ) ) {
                            $filter_path = $pro_path;
                        } else {
                            $filter_path = $core_path;
                        }
                    } else {
                        $filter_path = $core_path;
                    }
                    
                    $class_file = apply_filters( "redux/{$core->args['opt_name']}/field/class/{$field['type']}", $filter_path, $field );
                    
                    if ( $class_file ) {
                        if ( ! class_exists( $field_class ) ) {
                            if ( file_exists( $class_file ) ) {
                                require_once $class_file;
                            }
                        }

                        if ( ( method_exists( $field_class, 'enqueue' ) ) || method_exists( $field_class, 'localize' ) ) {

                            if ( ! isset( $core->options[ $field['id'] ] ) ) {
                                $core->options[ $field['id'] ] = "";
                            }

                            $theField = new $field_class( $field, $core->options[ $field['id'] ], $core );
                            
                            $data = array(
                                'field' => $field,
                                'value' => $core->options[ $field['id'] ],
                                'core' => $core,
                                'mode' => 'enqueue'
                            );
                            
                            Redux_Functions::load_pro_field($data);

                    if (ReduxCore::$_pro_loaded) {
                        $field_filter = ReduxPro::$_dir . 'inc/fields/' . $field['type'] . '/pro_field_' . $field['type'] . '.php';
                        
                        if (file_exists($field_filter)) {
                            require_once $field_filter;
                            
                            $filter_class_name = 'ReduxFramework_Pro_' . $field['type'];
                            
                            if (class_exists($filter_class_name)) {
                                $extend = new $filter_class_name ( $field, $core->options[ $field['id'] ], $core );
                                $extend->init('enqueue');
                            }
                        }
                    }  
                            
                            
                            // Move dev_mode check to a new if/then block
                            if ( ! wp_script_is( 'redux-field-' . $field['type'] . '-js', 'enqueued' ) && class_exists( $field_class ) && method_exists( $field_class, 'enqueue' ) ) {
                                $theField->enqueue();
                            }

                            if ( method_exists( $field_class, 'localize' ) ) {
                                $params = $theField->localize( $field );
                                if ( ! isset( $core->localize_data[ $field['type'] ] ) ) {
                                    $core->localize_data[ $field['type'] ] = array();
                                }

                                $core->localize_data[ $field['type'] ][ $field['id'] ] = $theField->localize( $field );
                            }

                            unset( $theField );
                        }
                    }
                }
            }

            private function enqueue_fields( $core ) {
                $data = array();

                foreach ( $core->sections as $section ) {
                    if ( isset( $section['fields'] ) ) {
                        foreach ( $section['fields'] as $field ) {
                            $this->_enqueue_field( $core, $field );
                        }
                    }
                }
            }

            private function build_local_array( $core, $type ) {
                if ( isset( $core->transients['last_save_mode'] ) && ! empty( $core->transients['notices'][ $type ] ) ) {
                    $the_total = 0;
                    $messages  = array();

                    foreach ( $core->transients['notices'][ $type ] as $msg ) {
                        $messages[ $msg['section_id'] ][ $type ][] = $msg;

                        if ( ! isset( $messages[ $msg['section_id'] ]['total'] ) ) {
                            $messages[ $msg['section_id'] ]['total'] = 0;
                        }

                        $messages[ $msg['section_id'] ]['total'] ++;
                        $the_total ++;
                    }

                    $core->localize_data[ $type ] = array(
                      'total'   => $the_total,
                      "{$type}" => $messages
                    );

                    unset( $core->transients['notices'][ $type ] );
                }
            }

            public function get_warnings_and_errors_array() {
                $core = $this->core();

                $this->build_local_array( $core, 'errors' );
                $this->build_local_array( $core, 'warnings' );
                $this->build_local_array( $core, 'sanitize' );

                if ( empty( $core->transients['notices'] ) ) {
                    unset( $core->transients['notices'] );
                }
            }

            private function set_localized_data( $core ) {
                if ( ! empty( $core->args['last_tab'] ) ) {
                    $core->localize_data['last_tab'] = $core->args['last_tab'];
                }

                $core->localize_data['core_instance']  = $core->core_instance;
                $core->localize_data['core_thread']    = $core->core_thread;
                
                $core->localize_data['required']       = $core->required;
                $core->localize_data['fonts']          = $core->fonts;
                $core->localize_data['required_child'] = $core->required_child;
                $core->localize_data['fields']         = $core->fields;

                if ( isset( $core->font_groups['google'] ) ) {
                    $core->localize_data['googlefonts'] = $core->font_groups['google'];
                }

                if ( isset( $core->font_groups['std'] ) ) {
                    $core->localize_data['stdfonts'] = $core->font_groups['std'];
                }

                if ( isset( $core->font_groups['customfonts'] ) ) {
                    $core->localize_data['customfonts'] = $core->font_groups['customfonts'];
                }

                if ( isset( $core->font_groups['typekitfonts'] ) ) {
                    $core->localize_data['typekitfonts'] = $core->font_groups['typekitfonts'];
                }

                $core->localize_data['folds'] = $core->folds;

                // Make sure the children are all hidden properly.
                foreach ( $core->fields as $key => $value ) {
                    if ( in_array( $key, $core->fieldsHidden ) ) {
                        foreach ( $value as $k => $v ) {
                            if ( ! in_array( $k, $core->fieldsHidden ) ) {
                                $core->fieldsHidden[] = $k;
                                $core->folds[ $k ]    = "hide";
                            }
                        }
                    }
                }

                $core->localize_data['fieldsHidden'] = $core->fieldsHidden;
                $core->localize_data['options']      = $core->options;
                $core->localize_data['defaults']     = $core->options_defaults;

                /**
                 * Save pending string
                 * filter 'redux/{opt_name}/localize/save_pending
                 *
                 * @param       string        save_pending string
                 */
                $save_pending = apply_filters( "redux/{$core->args['opt_name']}/localize/save_pending", esc_html__( 'You have changes that are not saved. Would you like to save them now?', 'redux-framework' ) );

                /**
                 * Reset all string
                 * filter 'redux/{opt_name}/localize/reset
                 *
                 * @param       string        reset all string
                 */
                $reset_all = apply_filters( "redux/{$core->args['opt_name']}/localize/reset", esc_html__( 'Are you sure? Resetting will lose all custom values.', 'redux-framework' ) );

                /**
                 * Reset section string
                 * filter 'redux/{opt_name}/localize/reset_section
                 *
                 * @param       string        reset section string
                 */
                $reset_section = apply_filters( "redux/{$core->args['opt_name']}/localize/reset_section", esc_html__( 'Are you sure? Resetting will lose all custom values in this section.', 'redux-framework' ) );

                /**
                 * Preset confirm string
                 * filter 'redux/{opt_name}/localize/preset
                 *
                 * @param       string        preset confirm string
                 */
                $preset_confirm = apply_filters( "redux/{$core->args['opt_name']}/localize/preset", esc_html__( 'Your current options will be replaced with the values of this preset. Would you like to proceed?', 'redux-framework' ) );
                
                global $pagenow;
                
                $core->localize_data['args'] = array(
                  'save_pending'          => $save_pending,
                  'reset_confirm'         => $reset_all,
                  'reset_section_confirm' => $reset_section,
                  'preset_confirm'        => $preset_confirm,
                  'please_wait'           => esc_html__( 'Please Wait', 'redux-framework' ),
                  'opt_name'              => $core->args['opt_name'],
                  'flyout_submenus'       => $core->args['flyout_submenus'],
                  'slug'                  => $core->args['page_slug'],
                  'hints'                 => $core->args['hints'],
                  'disable_save_warn'     => $core->args['disable_save_warn'],
                  'class'                 => $core->args['class'],
                  'ajax_save'             => $core->args['ajax_save'],
                  'menu_search'           => $pagenow . '?page=' . $core->args['page_slug'] . "&tab="
                );

                $core->localize_data['ajax'] = array(
                  'console' => esc_html__( 'There was an error saving. Here is the result of your action:', 'redux-framework' ),
                  'alert'   => esc_html__( 'There was a problem with your action. Please try again or reload the page.', 'redux-framework' ),
                );

                $core->localize_data = apply_filters( "redux/{$core->args['opt_name']}/localize", $core->localize_data );

                $this->get_warnings_and_errors_array();

                wp_localize_script(
                  'redux-js',
                  'redux',
                  array()
                );

                wp_localize_script(
                  'redux-js',
                  'redux_' . $core->args['opt_name'],
                  $core->localize_data
                );

                wp_enqueue_script( 'redux-js' ); // Enqueue the JS now

            }
        }
    }
