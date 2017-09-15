<?php

if ( !defined ( 'ABSPATH' ) ) {
    exit;
}

if (!class_exists('Redux_Output')) {
    
    class Redux_Output extends Redux_Class {

        public function __construct ($parent) {
            parent::__construct($parent);
            
            // Output dynamic CSS
            // Frontend: Maybe enqueue dynamic CSS and Google fonts
            if ( empty ( $this->args['output_location'] ) || in_array( 'frontend', $this->args['output_location'] ) ) {
                add_action( 'wp_head',                  array( $this, 'output_css' ), 150 );
                add_action( 'wp_enqueue_scripts',       array( $this, 'enqueue' ), 150 );
            }

            // Login page: Maybe enqueue dynamic CSS and Google fonts
            if ( in_array( 'login', $this->args['output_location'] ) ) {
                add_action( 'login_head',               array( $this, 'output_css' ), 150 );
                add_action( 'login_enqueue_scripts',    array( $this, 'enqueue' ), 150 );
            }

            // Admin area: Maybe enqueue dynamic CSS and Google fonts
            if ( in_array( 'admin', $this->args['output_location'] ) ) {
                add_action( 'admin_head',               array( $this, 'output_css' ), 150 );
                add_action( 'admin_enqueue_scripts',    array( $this, 'enqueue' ), 150 );
            }
        }

        /**
         * Enqueue CSS and Google fonts for front end
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function enqueue() {
            $core = $this->core();

            if ( $core->args['output'] == false && $core->args['compiler'] == false ) {
                return;
            }

            /** @noinspection PhpUnusedLocalVariableInspection */
            foreach ( $core->sections as $k => $section ) {
                if ( isset ( $section['type'] ) && ( $section['type'] == 'divide' ) ) {
                    continue;
                }

                if ( isset ( $section['fields'] ) ) {
                    /** @noinspection PhpUnusedLocalVariableInspection */
                    foreach ( $section['fields'] as $fieldk => $field ) {
                        if ( isset ( $field['type'] ) && $field['type'] != "callback" ) {
                            $field_class = "ReduxFramework_{$field['type']}";
                            if ( ! class_exists( $field_class ) ) {
                                if ( ! isset ( $field['compiler'] ) ) {
                                    $field['compiler'] = "";
                                }

                                /**
                                 * Field class file
                                 * filter 'redux/{opt_name}/field/class/{field.type}
                                 *
                                 * @param       string        field class file
                                 * @param array $field        field config data
                                 */
                                $class_file = apply_filters( "redux/{$core->args['opt_name']}/field/class/{$field['type']}", ReduxCore::$_dir . "inc/fields/{$field['type']}/field_{$field['type']}.php", $field );

                                if ( $class_file && file_exists( $class_file ) && ! class_exists( $field_class ) ) {
                                    /** @noinspection PhpIncludeInspection */
                                    require_once $class_file;
                                }
                            }

                            if ( ! empty ( $core->options[ $field['id'] ] ) && class_exists( $field_class ) && method_exists( $field_class, 'output' ) && $this->can_output_css($core, $field ) ) {
                                $field = apply_filters( "redux/field/{$core->args['opt_name']}/output_css", $field );

                                if ( ! empty ( $field['output'] ) && ! is_array( $field['output'] ) ) {
                                    $field['output'] = array( $field['output'] );
                                }

                                $value   = isset ( $core->options[ $field['id'] ] ) ? $core->options[ $field['id'] ] : '';
                                $enqueue = new $field_class ( $field, $value, $core );

                                $style_data = '';
                                
                                if ( ( ( isset ( $field['output'] ) && ! empty ( $field['output'] ) ) || ( isset ( $field['compiler'] ) && ! empty ( $field['compiler'] ) ) || isset ( $field['media_query'] ) && ! empty ( $field['media_query'] ) || $field['type'] == "typography" || $field['type'] == "icon_select" ) ) {
                                    if ( method_exists($enqueue, 'css_style')) {
                                        $style_data = $enqueue->css_style($enqueue->value);
                                    }
                                }
                                
                                if ( ( ( isset ( $field['output'] ) && ! empty ( $field['output'] ) ) || ( isset ( $field['compiler'] ) && ! empty ( $field['compiler'] ) ) || $field['type'] == "typography" || $field['type'] == "icon_select" ) ) {
                                    $enqueue->output($style_data);
                                }
                                
                                if ( isset ( $field['media_query'] ) && ! empty ( $field['media_query'] ) ) {
                                    $enqueue->media_query($style_data);
                                }
                            }
                        }
                    }
                }
            }

            // For use like in the customizer. Stops the output, but passes the CSS in the variable for the compiler
            if ( isset ( $core->no_output ) ) {
                return;
            }

            if ( ! empty ( $core->typography ) && ! empty ( $core->typography ) && filter_var( $core->args['output'], FILTER_VALIDATE_BOOLEAN ) ) {
                $version    = ! empty ( $core->transients['last_save'] ) ? $core->transients['last_save'] : '';
                $typography = new ReduxFramework_typography ( null, null, $core );

                if ( $core->args['async_typography'] && ! empty ( $core->typography ) ) {
                    $families = array();
                    
                    foreach ( $core->typography as $key => $value ) {
                        $families[] = $key;
                    }
?>
                    <script>
                        if ( typeof WebFontConfig === "undefined" ) {
                            WebFontConfig = new Object();
                        }
                        
                        WebFontConfig['google'] = {families: [<?php echo $typography->make_google_web_font_string ( $core->typography ) ?>]};

                        (function(d) {
                            var wf = d.createElement('script')
                            var s = d.scripts[0];
                            wf.src = 'https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js';
                            wf.async = true;
                            s.parentNode.insertBefore(wf, s);
                        })(document);
                    </script>
<?php
                } elseif ( ! $core->args['disable_google_fonts_link'] ) {
                    $protocol = ( ! empty ( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443 ) ? "https:" : "http:";

                    wp_enqueue_style( 
                        'redux-google-fonts-' . $core->args['opt_name'],
                        $protocol . $typography->make_google_web_font_link( $core->typography ),
                        array(),
                        $version,
                        'all'
                    );
                }
            }
        }
        
        /**
         * Output dynamic CSS at bottom of HEAD
         *
         * @since       3.2.8
         * @access      public
         * @return      void
         */
        public function output_css() {
            $core = $this->core();
                    
            if ( $core->args['output'] == false && $core->args['compiler'] == false ) {
                return;
            }

            if ( isset ( $core->no_output ) ) {
                return;
            }

            if ( ! empty ( $core->outputCSS ) && ( $core->args['output_tag'] == true || ( isset ( $_POST['customized'] ) ) ) ) {
                echo '<style type="text/css" id="' . $core->args['opt_name'] . '-dynamic-css" title="dynamic-css" class="redux-options-output">' . $core->outputCSS . '</style>';
            }
        }
        
        /**
         * Can Output CSS
         * Check if a field meets its requirements before outputting to CSS
         *
         * @param $field
         *
         * @return bool
         */
        private function can_output_css( $core, $field ) {
            $return = true;

            $field = apply_filters( "redux/field/{$core->args['opt_name']}/_can_output_css", $field );
            if ( isset ( $field['force_output'] ) && $field['force_output'] == true ) {
                return $return;
            }

            if ( ! empty ( $field['required'] ) ) {
                if ( isset ( $field['required'][0] ) ) {
                    if ( ! is_array( $field['required'][0] ) && count( $field['required'] ) == 3 ) {
                        $parentValue = $GLOBALS[ $core->args['global_variable'] ][ $field['required'][0] ];
                        $checkValue  = $field['required'][2];
                        $operation   = $field['required'][1];
                        $return      = $core->required_class->compare_value_dependencies( $parentValue, $checkValue, $operation );
                    } else if ( is_array( $field['required'][0] ) ) {
                        foreach ( $field['required'] as $required ) {
                            if ( ! is_array( $required[0] ) && count( $required ) == 3 ) {
                                $parentValue = $GLOBALS[ $core->args['global_variable'] ][ $required[0] ];
                                $checkValue  = $required[2];
                                $operation   = $required[1];
                                $return      = $core->required_class->compare_value_dependencies( $parentValue, $checkValue, $operation );
                            }
                            if ( ! $return ) {
                                return $return;
                            }
                        }
                    }
                }
            }

            return $return;
        }
    }
}