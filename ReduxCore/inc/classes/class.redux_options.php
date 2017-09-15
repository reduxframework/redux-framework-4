<?php

/**
 * ->get_options(); This is used to get options from the database
 *
 * @since ReduxFramework 3.0.0
 */

if ( !defined ( 'ABSPATH' ) ) {
    exit;
}

if (!class_exists('Redux_Options')) {
    
    class Redux_Options extends Redux_Class {

        public function __construct ($parent) {
            parent::__construct($parent);
            
            add_action( 'admin_init', array( $this, 'register' ) );
        }
        
        public function get() {
            $core = $this->core();
            
            $defaults = false;

            if ( ! empty ( $core->defaults ) ) {
                $defaults = $core->defaults;
            }

            if ( $core->args['database'] === "transient" ) {
                $result = get_transient( $core->args['opt_name'] . '-transient' );
            } else if ( $core->args['database'] === "theme_mods" ) {
                $result = get_theme_mod( $core->args['opt_name'] . '-mods' );
            } else if ( $core->args['database'] === 'theme_mods_expanded' ) {
                $result = get_theme_mods();
            } else if ( $core->args['database'] === 'network' ) {
                $result = get_site_option( $core->args['opt_name'], array() );
                $result = json_decode( stripslashes( json_encode( $result ) ), true );
            } else {
                $result = get_option( $core->args['opt_name'], array() );
            }

            if ( empty ( $result ) && ! empty ( $defaults ) ) {
                $results = $defaults;
                $this->set( $results );
            } else {
                $core->options = $result;
            }

            /**
             * action 'redux/options/{opt_name}/options'
             *
             * @param mixed $value option values
             */
            $core->options = apply_filters( "redux/options/{$core->args['opt_name']}/options", $core->options );

            // Get transient values
            $core->transient_class->get();

            // Set a global variable by the global_variable argument.
            $this->set_global_variable($core);
        }
        
        /**
         * ->set_options(); This is used to set an arbitrary option in the options array
         *
         * @since ReduxFramework 3.0.0
         *
         * @param mixed $value the value of the option being added
         */
        public function set( $value = '' ) {
            $core = $this->core();
            
            $core->transients['last_save'] = time();

            if ( ! empty ( $value ) ) {
                $core->options = $value;

                if ( $core->args['database'] === 'transient' ) {
                    set_transient( $core->args['opt_name'] . '-transient', $value, $core->args['transient_time'] );
                } else if ( $core->args['database'] === 'theme_mods' ) {
                    set_theme_mod( $core->args['opt_name'] . '-mods', $value );
                } else if ( $core->args['database'] === 'theme_mods_expanded' ) {
                    foreach ( $value as $k => $v ) {
                        set_theme_mod( $k, $v );
                    }
                } else if ( $core->args['database'] === 'network' ) {
                    // Strip those slashes!
                    $value = json_decode( stripslashes( json_encode( $value ) ), true );
                    update_site_option( $core->args['opt_name'], $value );
                } else {
                    update_option( $core->args['opt_name'], $value );
                }

                // Store the changed values in the transient
                if ( $value != $core->options ) {
                    foreach ( $value as $k => $v ) {
                        if ( ! isset ( $core->options[ $k ] ) ) {
                            $core->options[ $k ] = "";
                        } else if ( $v == $core->options[ $k ] ) {
                            unset ( $core->options[ $k ] );
                        }
                    }
                    
                    $core->transients['changed_values'] = $core->options;
                }

                $core->options = $value;

                // Set a global variable by the global_variable argument.
                $this->set_global_variable($core);

                // Saving the transient values
                $core->transient_class->set();
            }
        }
        
        /**
         * Set a global variable by the global_variable argument
         *
         * @since   3.1.5
         * @return  bool          (global was set)
         */
        private function set_global_variable($core) {
            if ( $core->args['global_variable'] ) {
                $option_global = $core->args['global_variable'];
                /**
                 * filter 'redux/options/{opt_name}/global_variable'
                 *
                 * @param array $value option value to set global_variable with
                 */
                $GLOBALS[ $core->args['global_variable'] ] = apply_filters( "redux/options/{$core->args['opt_name']}/global_variable", $core->options );

                // Last save key
                if ( isset ( $core->transients['last_save'] ) ) {
                    $GLOBALS[ $core->args['global_variable'] ]['REDUX_LAST_SAVE'] = $core->transients['last_save'];
                }
                
                // Last compiler hook key
                if ( isset ( $core->transients['last_compiler'] ) ) {
                    $GLOBALS[ $core->args['global_variable'] ]['REDUX_LAST_COMPILER'] = $core->transients['last_compiler'];
                }

                return true;
            }

            return false;
        }
        
        /**
         * Register Option for use
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function register() {
            $core = $this->core();
            
            if ( $core->args['options_api'] == true ) {
                register_setting( $core->args['opt_name'] . '_group', $core->args['opt_name'], array(
                    $this,
                    '_validate_options'
                ) );
            }

            if ( is_null( $core->sections ) ) {
                return;
            }

            if ( empty( $core->options_defaults ) ) {
                $core->options_defaults = $core->_default_values();
            }

            $runUpdate = false;

            foreach ( $core->sections as $k => $section ) {
                if ( isset ( $section['type'] ) && $section['type'] == 'divide' ) {
                    continue;
                }

                $display = true;

                if ( isset ( $_GET['page'] ) && $_GET['page'] == $core->args['page_slug'] ) {
                    if ( isset ( $section['panel'] ) && $section['panel'] == false ) {
                        $display = false;
                    }
                }

                /**
                 * filter 'redux/options/{opt_name}/section/{section.id}'
                 *
                 * @param array $section section configuration
                 */
                if ( isset ( $section['id'] ) ) {
                    $section = apply_filters( "redux/options/{$core->args['opt_name']}/section/{$section['id']}", $section );
                }

                if ( empty ( $section ) ) {
                    unset ( $core->sections[ $k ] );
                    continue;
                }

                if ( ! isset ( $section['title'] ) ) {
                    $section['title'] = "";
                }

                if ( isset ( $section['customizer_only'] ) && $section['customizer_only'] == true ) {
                    $section['panel']     = false;
                    $core->sections[ $k ] = $section;
                }

                $heading = isset ( $section['heading'] ) ? $section['heading'] : $section['title'];

                if ( isset ( $section['permissions'] ) && $section['permissions'] !== false ) {
                    if ( ! Redux_Helpers::current_user_can( $section['permissions'] ) ) {
                        $core->hidden_perm_sections[] = $section['title'];

                        foreach ( $section['fields'] as $num => $field_data ) {
                            $field_type = $field_data['type'];

                            if ( $field_type != 'section' || $field_type != 'divide' || $field_type != 'info' || $field_type != 'raw' ) {
                                $field_id = $field_data['id'];
                                $default  = isset ( $core->options_defaults[ $field_id ] ) ? $core->options_defaults[ $field_id ] : '';
                                $data     = isset ( $core->options[ $field_id ] ) ? $core->options[ $field_id ] : $default;

                                $core->hidden_perm_fields[ $field_id ] = $data;
                            }
                        }

                        continue;
                    }
                }

                if ( ! $display || ! function_exists( 'add_settings_section' ) ) {
                    $core->no_panel_section[ $k ] = $section;
                } else {
                    add_settings_section( $core->args['opt_name'] . $k . '_section', $heading, array(
                        $core->render_class,
                        'section_desc'
                    ), $core->args['opt_name'] . $k . '_section_group' );
                }

                $sectionIndent = false;
                if ( isset ( $section['fields'] ) ) {
                    foreach ( $section['fields'] as $fieldk => $field ) {
                        if ( ! isset ( $field['type'] ) ) {
                            continue; // You need a type!
                        }

                        if ( $field['type'] == "info" && isset( $field['raw_html'] ) && $field['raw_html'] == true ) {
                            $field['type']                             = "raw";
                            $field['content']                          = $field['desc'];
                            $field['desc']                             = "";
                            $core->sections[ $k ]['fields'][ $fieldk ] = $field;
                        } else if ( $field['type'] == "info" ) {
                            if ( ! isset( $field['full_width'] ) ) {
                                $field['full_width']                       = true;
                                $core->sections[ $k ]['fields'][ $fieldk ] = $field;
                            }
                        }

                        if ( $field['type'] == "raw" ) {
                            if ( isset( $field['align'] ) ) {
                                $field['full_width'] = $field['align'] ? false : true;
                                unset( $field['align'] );
                            } else if ( ! isset( $field['full_width'] ) ) {
                                $field['full_width'] = true;
                            }
                            $core->sections[ $k ]['fields'][ $fieldk ] = $field;
                        }


                        /**
                         * filter 'redux/options/{opt_name}/field/{field.id}'
                         *
                         * @param array $field field config
                         */
                        $field = apply_filters( "redux/options/{$core->args['opt_name']}/field/{$field['id']}/register", $field );


                        $core->field_types[ $field['type'] ] = isset ( $core->field_types[ $field['type'] ] ) ? $core->field_types[ $field['type'] ] : array();

                        $core->field_sections[ $field['type'] ][ $field['id'] ] = $k;

                        $display = true;

                        if ( isset ( $_GET['page'] ) && $_GET['page'] == $core->args['page_slug'] ) {
                            if ( isset ( $field['panel'] ) && $field['panel'] == false ) {
                                $display = false;
                            }
                        }
                        if ( isset ( $field['customizer_only'] ) && $field['customizer_only'] == true ) {
                            $display = false;
                        }

                        if ( isset ( $section['customizer'] ) ) {
                            $field['customizer']                       = $section['customizer'];
                            $core->sections[ $k ]['fields'][ $fieldk ] = $field;
                        }

                        if ( isset ( $field['permissions'] ) && $field['permissions'] !== false ) {
                            if ( ! Redux_Helpers::current_user_can( $field['permissions'] ) ) {
                                $data = isset ( $core->options[ $field['id'] ] ) ? $core->options[ $field['id'] ] : $core->options_defaults[ $field['id'] ];

                                $core->hidden_perm_fields[ $field['id'] ] = $data;

                                continue;
                            }
                        }

                        if ( ! isset ( $field['id'] ) ) {
                            echo '<br /><h3>No field ID is set.</h3><pre>';
                            print_r( $field );
                            echo "</pre><br />";
                            continue;
                        }

                        if ( isset ( $field['type'] ) && $field['type'] == "section" ) {
                            if ( isset ( $field['indent'] ) && $field['indent'] == true ) {
                                $sectionIndent = true;
                            } else {
                                $sectionIndent = false;
                            }
                        }

                        if ( isset ( $field['type'] ) && $field['type'] == "info" && $sectionIndent ) {
                            $field['indent'] = $sectionIndent;
                        }

                        $th = $core->render_class->get_header_html( $field );

                        $field['name'] = $core->args['opt_name'] . '[' . $field['id'] . ']';

                        // Set the default value if present
                        $core->options_defaults[ $field['id'] ] = isset ( $core->options_defaults[ $field['id'] ] ) ? $core->options_defaults[ $field['id'] ] : '';

                        // Set the defaults to the value if not present
                        $doUpdate = false;

                        // Check fields for values in the default parameter
                        if ( ! isset ( $core->options[ $field['id'] ] ) && isset ( $field['default'] ) ) {
                            $core->options_defaults[ $field['id'] ] = $core->options[ $field['id'] ] = $field['default'];
                            $doUpdate = true;

                            // Check fields that hae no default value, but an options value with settings to
                            // be saved by default
                        } elseif ( ! isset ( $core->options[ $field['id'] ] ) && isset ( $field['options'] ) ) {

                            // If sorter field, check for options as save them as defaults
                            if ( $field['type'] == 'sorter' || $field['type'] == 'sortable' ) {
                                $core->options_defaults[ $field['id'] ] = $core->options[ $field['id'] ] = $field['options'];
                                $doUpdate = true;
                            }
                        }

                        // CORRECT URLS if media URLs are wrong, but attachment IDs are present.
                        if ( $field['type'] == "media" ) {
                            if ( isset ( $core->options[ $field['id'] ]['id'] ) && isset ( $core->options[ $field['id'] ]['url'] ) && ! empty ( $core->options[ $field['id'] ]['url'] ) && strpos( $core->options[ $field['id'] ]['url'], str_replace( 'http://', '', WP_CONTENT_URL ) ) === false ) {
                                $data = wp_get_attachment_url( $core->options[ $field['id'] ]['id'] );

                                if ( isset ( $data ) && ! empty ( $data ) ) {
                                    $core->options[ $field['id'] ]['url']       = $data;
                                    $data = wp_get_attachment_image_src( $core->options[ $field['id'] ]['id'], array( 150, 150 ) );
                                    $core->options[ $field['id'] ]['thumbnail'] = $data[0];
                                    $doUpdate = true;
                                }
                            }
                        }

                        if ( $field['type'] == "background" ) {
                            if ( isset ( $core->options[ $field['id'] ]['media']['id'] ) && isset ( $core->options[ $field['id'] ]['background-image'] ) && ! empty ( $core->options[ $field['id'] ]['background-image'] ) && strpos( $core->options[ $field['id'] ]['background-image'], str_replace( 'http://', '', WP_CONTENT_URL ) ) === false ) {
                                $data = wp_get_attachment_url( $core->options[ $field['id'] ]['media']['id'] );

                                if ( isset ( $data ) && ! empty ( $data ) ) {
                                    $core->options[ $field['id'] ]['background-image']   = $data;
                                    $data = wp_get_attachment_image_src( $core->options[ $field['id'] ]['media']['id'], array( 150, 150 ) );
                                    $core->options[ $field['id'] ]['media']['thumbnail'] = $data[0];
                                    $doUpdate = true;
                                }
                            }
                        }

                        if ( $field['type'] == "slides" ) {
                            if ( isset ( $core->options[ $field['id'] ] ) && is_array( $core->options[ $field['id'] ] ) && isset ( $core->options[ $field['id'] ][0]['attachment_id'] ) && isset ( $core->options[ $field['id'] ][0]['image'] ) && ! empty ( $core->options[ $field['id'] ][0]['image'] ) && strpos( $core->options[ $field['id'] ][0]['image'], str_replace( 'http://', '', WP_CONTENT_URL ) ) === false ) {
                                foreach ( $core->options[ $field['id'] ] as $key => $val ) {
                                    $data = wp_get_attachment_url( $val['attachment_id'] );

                                    if ( isset ( $data ) && ! empty ( $data ) ) {
                                        $core->options[ $field['id'] ][ $key ]['image'] = $data;
                                        $data = wp_get_attachment_image_src( $val['attachment_id'], array( 150, 150 ) );
                                        $core->options[ $field['id'] ][ $key ]['thumb'] = $data[0];
                                        $doUpdate = true;
                                    }
                                }
                            }
                        }
                        // END -> CORRECT URLS if media URLs are wrong, but attachment IDs are present.

                        if ( true == $doUpdate && ! isset ( $core->never_save_to_db ) ) {
                            if ( $core->args['save_defaults'] ) { // Only save that to the DB if allowed to
                                $runUpdate = true;
                            }
                        }

                        if ( ! isset ( $field['class'] ) ) { // No errors please
                            $field['class'] = "";
                        }
                        $id = $field['id'];

                        /**
                         * filter 'redux/options/{opt_name}/field/{field.id}'
                         *
                         * @param array $field field config
                         */
                        $field = apply_filters( "redux/options/{$core->args['opt_name']}/field/{$field['id']}", $field );

                        if ( empty ( $field ) || ! $field || $field == false ) {
                            unset ( $core->sections[ $k ]['fields'][ $fieldk ] );
                            continue;
                        }

                        if ( ! empty ( $core->folds[ $field['id'] ]['parent'] ) ) { // This has some fold items, hide it by default
                            $field['class'] .= " fold";
                        }

                        if ( ! empty ( $core->folds[ $field['id'] ]['children'] ) ) { // Sets the values you shoe fold children on
                            $field['class'] .= " foldParent";
                        }

                        if ( ! empty ( $field['compiler'] ) ) {
                            $field['class'] .= " compiler";
                            $core->compiler_fields[ $field['id'] ] = 1;
                        }

                        if ( isset ( $field['unit'] ) && ! isset ( $field['units'] ) ) {
                            $field['units'] = $field['unit'];
                            unset ( $field['unit'] );
                        }

                        $core->sections[ $k ]['fields'][ $fieldk ] = $field;

                        if ( isset ( $core->args['display_source'] ) ) {
                            $th .= '<div id="' . $field['id'] . '-settings" style="display:none;"><pre>' . var_export( $core->sections[ $k ]['fields'][ $fieldk ], true ) . '</pre></div>';
                            $th .= '<br /><a href="#TB_inline?width=600&height=800&inlineId=' . $field['id'] . '-settings" class="thickbox"><small>View Source</small></a>';
                        }

                        /**
                         * action 'redux/options/{opt_name}/field/field.type}/register'
                         */
                        do_action( "redux/options/{$core->args['opt_name']}/field/{$field['type']}/register", $field );

                        $core->required_class->check_dependencies( $field );
                        $core->field_head[ $field['id'] ] = $th;

                        if ( ! $display || isset ( $core->no_panel_section[ $k ] ) ) {
                            $core->no_panel[] = $field['id'];
                        } else {
                            if ( isset ( $field['hidden'] ) && $field['hidden'] ) {
                                $field['label_for'] = 'redux_hide_field';
                            }
                            if ( $core->args['options_api'] == true ) {
                                add_settings_field(
                                    "{$fieldk}_field", $th, array(
                                    $core->render_class,
                                    '_field_input'
                                ), "{$core->args['opt_name']}{$k}_section_group", "{$core->args['opt_name']}{$k}_section", $field );
                            }
                        }
                    }
                }
            }

            /**
             * action 'redux/options/{opt_name}/register'
             *
             * @param array option sections
             */
            do_action( "redux/options/{$core->args['opt_name']}/register", $core->sections );

            if ( $runUpdate && ! isset ( $core->never_save_to_db ) ) { // Always update the DB with new fields
                $this->set( $core->options );
            }

            if ( isset ( $core->transients['run_compiler'] ) && $core->transients['run_compiler'] ) {

                $core->no_output = true;
                $core->output_class->enqueue();

                /**
                 * action 'redux/options/{opt_name}/compiler'
                 *
                 * @param array  options
                 * @param string CSS that get sent to the compiler hook
                 */
                do_action( "redux/options/{$core->args['opt_name']}/compiler", $core->options, $core->compilerCSS, $core->transients['changed_values'] );

                /**
                 * action 'redux/options/{opt_name}/compiler/advanced'
                 *
                 * @param array  options
                 * @param string CSS that get sent to the compiler hook, which sends the full Redux object
                 */
                do_action( "redux/options/{$core->args['opt_name']}/compiler/advanced", $core );

                unset ( $core->transients['run_compiler'] );
                $core->transient_class->set();
            }
        }  
        
        /**
         * Get default options into an array suitable for the settings API
         *
         * @since       1.0.0
         * @access      public
         * @return      array $this->options_defaults
         */
        public function _default_values() {
            $core = $this->core();
            
            if ( ! is_null( $core->sections ) && is_null( $core->options_defaults ) ) {

                // fill the cache
                foreach ( $core->sections as $sk => $section ) {
                    if ( ! isset ( $section['id'] ) ) {
                        if ( ! is_numeric( $sk ) || ! isset ( $section['title'] ) ) {
                            $section['id'] = $sk;
                        } else {
                            $section['id'] = sanitize_title( $section['title'], $sk );
                        }
                        
                        $core->sections[ $sk ] = $section;
                    }
                    if ( isset ( $section['fields'] ) ) {
                        foreach ( $section['fields'] as $k => $field ) {
                            if ( empty ( $field['id'] ) && empty ( $field['type'] ) ) {
                                continue;
                            }

                            if ( in_array( $field['type'], array( 'ace_editor' ) ) && isset ( $field['options'] ) ) {
                                $core->sections[ $sk ]['fields'][ $k ]['args'] = $field['options'];
                                unset ( $core->sections[ $sk ]['fields'][ $k ]['options'] );
                            }

                            if ( $field['type'] == "section" && isset ( $field['indent'] ) && $field['indent'] == "true" ) {
                                $field['class'] = isset ( $field['class'] ) ? $field['class'] : '';
                                $field['class'] .= " redux-section-indent-start";
                                $core->sections[ $sk ]['fields'][ $k ] = $field;
                            }
                            $this->field_default_values( $field );
                        }
                    }
                }
            }
            
            /**
             * filter 'redux/options/{opt_name}/defaults'
             *
             * @param array $defaults option default values
             */
            $core->transients['changed_values'] = isset ( $core->transients['changed_values'] ) ? $core->transients['changed_values'] : array();
            $core->options_defaults             = apply_filters( "redux/options/{$core->args['opt_name']}/defaults", $core->options_defaults, $core->transients['changed_values'] );

            return $core->options_defaults;
        }
        
        public function field_default_values( $field ) {
            $core = $this->core();
            
            // Detect what field types are being used
            if ( ! isset ( $core->fields[ $field['type'] ][ $field['id'] ] ) ) {
                $core->fields[ $field['type'] ][ $field['id'] ] = 1;
            } else {
                $core->fields[ $field['type'] ] = array( $field['id'] => 1 );
            }

            if ( isset ( $field['default'] ) ) {
                $core->options_defaults[ $field['id'] ] = apply_filters( "redux/{$core->args['opt_name']}/field/{$field['type']}/defaults", $field['default'], $field );
            } elseif ( ( $field['type'] != "ace_editor" ) ) {
                // Sorter data filter

                if ( isset( $field['data'] ) && ! empty( $field['data'] ) ) {
                    if ( ! isset( $field['args'] ) ) {
                        $field['args'] = array();
                    }
                    if ( is_array( $field['data'] ) && ! empty( $field['data'] ) ) {
                        foreach ( $field['data'] as $key => $data ) {
                            if ( ! empty( $data ) ) {
                                if ( ! isset ( $field['args'][ $key ] ) ) {
                                    $field['args'][ $key ] = array();
                                }
                                $field['options'][ $key ] = $core->wordpress_data->get( $data, $field['args'][ $key ] );
                            }
                        }
                    } else {
                        $field['options'] = $core->wordpress_data->get( $field['data'], $field['args'] );
                    }
                }

                if ( $field['type'] == "sorter" && isset ( $field['data'] ) && ! empty ( $field['data'] ) && is_array( $field['data'] ) ) {
                    if ( ! isset ( $field['args'] ) ) {
                        $field['args'] = array();
                    }
                    foreach ( $field['data'] as $key => $data ) {
                        if ( ! isset ( $field['args'][ $key ] ) ) {
                            $field['args'][ $key ] = array();
                        }
                        $field['options'][ $key ] = $core->wordpress_data->get( $data, $field['args'][ $key ] );
                    }
                }

                if ( isset ( $field['options'] ) ) {
                    if ( $field['type'] == "sortable" ) {
                        $core->options_defaults[ $field['id'] ] = array();
                    } elseif ( $field['type'] == "image_select" ) {
                        $core->options_defaults[ $field['id'] ] = '';
                    } elseif ( $field['type'] == "select" ) {
                        $core->options_defaults[ $field['id'] ] = '';
                    } else {
                        $core->options_defaults[ $field['id'] ] = $field['options'];
                    }
                }
            }
        }
        
        /**
         * Validate the Options options before insertion
         *
         * @since       3.0.0
         * @access      public
         *
         * @param       array $plugin_options The options array
         *
         * @return array|mixed|string|void
         */
        public function _validate_options( $plugin_options ) {
            $core = $this->core();
            
            if ( isset ( $core->validation_ran ) ) {
                return $plugin_options;
            }

            $core->validation_ran = 1;

            // Save the values not in the panel
            if ( isset ( $plugin_options['redux-no_panel'] ) ) {
                $keys = explode( '|', $plugin_options['redux-no_panel'] );
                foreach ( $keys as $key ) {
                    $plugin_options[ $key ] = $core->options[ $key ];
                }
                if ( isset ( $plugin_options['redux-no_panel'] ) ) {
                    unset ( $plugin_options['redux-no_panel'] );
                }
            }

            if ( ! empty ( $core->hidden_perm_fields ) && is_array( $core->hidden_perm_fields ) ) {
                foreach ( $core->hidden_perm_fields as $id => $data ) {
                    $plugin_options[ $id ] = $data;
                }
            }

            if ( $plugin_options == $core->options ) {
                return $plugin_options;
            }

            $time = time();

            // Sets last saved time
            $core->transients['last_save'] = $time;

            // Import
            if ( ( isset( $plugin_options['import_code'] ) && ! empty( $plugin_options['import_code'] ) ) || ( isset( $plugin_options['import_link'] ) && ! empty( $plugin_options['import_link'] ) ) ) {
                $core->transients['last_save_mode'] = "import"; // Last save mode
                $core->transients['last_compiler']  = $time;
                $core->transients['last_import']    = $time;
                $core->transients['run_compiler']   = 1;

                if ( $plugin_options['import_code'] != '' ) {
                    $import = $plugin_options['import_code'];
                } elseif ( $plugin_options['import_link'] != '' ) {
                    $import = wp_remote_retrieve_body( wp_remote_get( $plugin_options['import_link'] ) );
                }

                if ( ! empty ( $import ) ) {
                    $imported_options = json_decode( $import, true );
                }

                if ( ! empty ( $imported_options ) && is_array( $imported_options ) && isset ( $imported_options['redux-backup'] ) && $imported_options['redux-backup'] == '1' ) {

                    $core->transients['changed_values'] = array();
                    foreach ( $plugin_options as $key => $value ) {
                        if ( isset ( $imported_options[ $key ] ) && $imported_options[ $key ] != $value ) {
                            $core->transients['changed_values'][ $key ] = $value;
                            $plugin_options[ $key ]                     = $value;
                        }
                    }

                    /**
                     * action 'redux/options/{opt_name}/import'
                     *
                     * @param  &array [&$plugin_options, redux_options]
                     */
                    do_action_ref_array( "redux/options/{$core->args['opt_name']}/import", array(
                        &$plugin_options,
                        $imported_options,
                        $core->transients['changed_values']
                    ) );

                    setcookie( 'redux_current_tab_' . $core->args['opt_name'], '', 1, '/', $time + 1000, "/" );
                    $_COOKIE['redux_current_tab_' . $core->args['opt_name']] = 1;

                    unset ( $plugin_options['defaults'], $plugin_options['compiler'], $plugin_options['import'], $plugin_options['import_code'] );
                    if ( $core->args['database'] == 'transient' || $core->args['database'] == 'theme_mods' || $core->args['database'] == 'theme_mods_expanded' || $core->args['database'] == 'network' ) {
                        $this->set( $plugin_options );

                        return;
                    }

                    $plugin_options = wp_parse_args( $imported_options, $plugin_options );

                    $core->transient_class->set();

                    return $plugin_options;
                }
            }

            // Reset all to defaults
            if ( ! empty ( $plugin_options['defaults'] ) ) {
                if ( empty ( $core->options_defaults ) ) {
                    $core->options_defaults = $core->_default_values();
                }

                /**
                 * apply_filters 'redux/validate/{opt_name}/defaults'
                 *
                 * @param  &array [ $this->options_defaults, $plugin_options]
                 */
                $plugin_options = apply_filters( "redux/validate/{$core->args['opt_name']}/defaults", $core->options_defaults );

                $core->transients['changed_values'] = array();

                if ( empty ( $core->options ) ) {
                    $core->options = $core->options_defaults;
                }

                foreach ( $core->options as $key => $value ) {
                    if ( isset ( $plugin_options[ $key ] ) && $value != $plugin_options[ $key ] ) {
                        $core->transients['changed_values'][ $key ] = $value;
                    }
                }

                $core->transients['run_compiler']   = 1;
                $core->transients['last_save_mode'] = "defaults"; // Last save mode

                $core->transient_class->set();

                return $plugin_options;
            }

            // Section reset to defaults
            if ( ! empty ( $plugin_options['defaults-section'] ) ) {
                if ( isset ( $plugin_options['redux-section'] ) && isset ( $core->sections[ $plugin_options['redux-section'] ]['fields'] ) ) {
                    foreach ( $core->sections[ $plugin_options['redux-section'] ]['fields'] as $field ) {
                        if ( isset ( $core->options_defaults[ $field['id'] ] ) ) {
                            $plugin_options[ $field['id'] ] = $core->options_defaults[ $field['id'] ];
                        } else {
                            $plugin_options[ $field['id'] ] = "";
                        }

                        if ( isset ( $field['compiler'] ) ) {
                            $compiler = true;
                        }
                    }

                    /**
                     * apply_filters 'redux/validate/{opt_name}/defaults_section'
                     *
                     * @param  &array [ $this->options_defaults, $plugin_options]
                     */
                    $plugin_options = apply_filters( "redux/validate/{$core->args['opt_name']}/defaults_section", $plugin_options );
                }

                $core->transients['changed_values'] = array();
                foreach ( $core->options as $key => $value ) {
                    if ( isset ( $plugin_options[ $key ] ) && $value != $plugin_options[ $key ] ) {
                        $core->transients['changed_values'][ $key ] = $value;
                    }
                }

                if ( isset ( $compiler ) ) {
                    $core->transients['last_compiler'] = $time;
                    $core->transients['run_compiler']  = 1;
                }

                $core->transients['last_save_mode'] = "defaults_section"; // Last save mode

                unset ( $plugin_options['defaults'], $plugin_options['defaults_section'], $plugin_options['import'], $plugin_options['import_code'], $plugin_options['import_link'], $plugin_options['compiler'], $plugin_options['redux-section'] );

                $core->transient_class->set();

                return $plugin_options;
            }

            $core->transients['last_save_mode'] = "normal"; // Last save mode

            /**
             * apply_filters 'redux/validate/{opt_name}/before_validation'
             *
             * @param  &array [&$plugin_options, redux_options]
             */
            $plugin_options = apply_filters( "redux/validate/{$core->args['opt_name']}/before_validation", $plugin_options, $core->options );

            // Validate fields (if needed)
            $plugin_options = $core->validate_class->validate( $plugin_options, $core->options, $core->sections );

            if ( ! empty ( $core->errors ) || ! empty ( $core->warnings ) || ! empty ( $core->sanitize ) ) {
                $core->transients['notices'] = array( 
                    'errors'    => $core->errors, 
                    'warnings'  => $core->warnings, 
                    'sanitize'  => $core->sanitize 
                );
            }

            if ( ! isset ( $core->transients['changed_values'] ) ) {
                $core->transients['changed_values'] = array();
            }

            /**
             * action 'redux/options/{opt_name}/validate'
             *
             * @param  &array [&$plugin_options, redux_options]
             */
            do_action_ref_array( "redux/options/{$core->args['opt_name']}/validate", array(
                &$plugin_options,
                $core->options,
                $core->transients['changed_values']
            ) );

            if ( ! empty ( $plugin_options['compiler'] ) ) {
                unset ( $plugin_options['compiler'] );

                $core->transients['last_compiler'] = $time;
                $core->transients['run_compiler']  = 1;
            }

            $core->transients['changed_values'] = array(); // Changed values since last save
            if ( !empty( $this->options ) ) {
                foreach ( $core->options as $key => $value ) {
                    if ( isset ( $plugin_options[ $key ] ) && $value != $plugin_options[ $key ] ) {
                        $core->transients['changed_values'][ $key ] = $value;
                    }
                }
            }
            
            unset ( $plugin_options['defaults'], $plugin_options['defaults_section'], $plugin_options['import'], $plugin_options['import_code'], $plugin_options['import_link'], $plugin_options['compiler'], $plugin_options['redux-section'] );
            if ( $core->args['database'] == 'transient' || $core->args['database'] == 'theme_mods' || $core->args['database'] == 'theme_mods_expanded' ) {
                $core->set( $plugin_options );

                return;
            }

            if ( defined( 'WP_CACHE' ) && WP_CACHE && class_exists( 'W3_ObjectCache' ) && function_exists( 'w3_instance' ) ) {
                //echo "here";
                $w3_inst = w3_instance( 'W3_ObjectCache' );
                $w3      = $w3_inst->instance();
                $key     = $w3->_get_cache_key( $core->args['opt_name'] . '-transients', 'transient' );
                $w3->delete( $key, 'transient', true );
            }

            $core->transient_class->set();

            return $plugin_options;
        }
        
        /**
         * ->_get_default(); This is used to return the default value if default_show is set
         *
         * @since       1.0.1
         * @access      public
         *
         * @param       string $opt_name The option name to return
         * @param       mixed  $default  (null)  The value to return if default not set
         *
         * @return      mixed $default
         */
        public function _get_default( $opt_name, $default = null ) {
            if ( $this->args['default_show'] == true ) {

                if ( empty ( $this->options_defaults ) ) {
                    $this->_default_values(); // fill cache
                }

                $default = array_key_exists( $opt_name, $this->options_defaults ) ? $this->options_defaults[ $opt_name ] : $default;
            }

            return $default;
        }

        /**
         * Get the default value for an option
         *
         * @since  3.3.6
         * @access public
         *
         * @param string $key       The option's ID
         * @param string $array_key The key of the default's array
         *
         * @return mixed
         */
        public function get_default_value( $key, $array_key = false ) {
            if ( empty ( $this->options_defaults ) ) {
                $this->options_defaults = $this->_default_values();
            }

            $defaults = $this->options_defaults;
            $value    = '';

            if ( isset ( $defaults[ $key ] ) ) {
                if ( $array_key !== false && isset ( $defaults[ $key ][ $array_key ] ) ) {
                    $value = $defaults[ $key ][ $array_key ];
                } else {
                    $value = $defaults[ $key ];
                }
            }

            return $value;
        }
    }
}