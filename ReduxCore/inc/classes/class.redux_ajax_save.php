<?php

if ( !defined ( 'ABSPATH' ) ) {
    exit;
}

if (!class_exists('Redux_AJAX_Save')) {
    
    class Redux_AJAX_Save extends Redux_Class {

        public function __construct($parent){
            parent::__construct($parent);
            
            add_action( "wp_ajax_" . $this->args['opt_name'] . '_ajax_save', array( $this, "save" ) );
        }
        
        public function save() {
            $core = $this->core();
            
            if ( ! wp_verify_nonce( $_REQUEST['nonce'], "redux_ajax_nonce" . $this->args['opt_name'] ) ) {
                echo json_encode( array(
                    'status' => esc_html__( 'Invalid security credential.  Please reload the page and try again.', 'redux-framework' ),
                    'action' => ''
                ) );

                die();
            }

            if ( ! Redux_Helpers::current_user_can( $core->args['page_permissions'] ) ) {
                echo json_encode( array(
                    'status' => esc_html__( 'Invalid user capability.  Please reload the page and try again.', 'redux-framework' ),
                    'action' => ''
                ) );

                die();
            }

            $redux = Redux::instance($_POST['opt_name']);

            if ( ! empty ( $_POST['data'] ) && ! empty ( $redux->args['opt_name'] ) ) {

                $values = array();

                $_POST['data'] = stripslashes( $_POST['data'] );

                // New method to avoid input_var nonesense.  Thanks @harunbasic
                $values = $this->parse_str( $_POST['data'] );

                $values = $values[ $redux->args['opt_name'] ];

                if ( function_exists( 'get_magic_quotes_gpc' ) && get_magic_quotes_gpc() ) {
                    $values = array_map( 'stripslashes_deep', $values );
                }

                if ( ! empty ( $values ) ) {
                    try {
                        if ( isset ( $redux->validation_ran ) ) {
                            unset ( $redux->validation_ran );
                        }
                        $redux->options_class->set( $redux->options_class->_validate_options( $values ) );

                        $do_reload = false;
                        if ( isset( $core->reload_fields ) && ! empty( $core->reload_fields ) ) {
                            if ( ! empty( $core->transients['changed_values'] ) ) {
                                foreach ( $core->reload_fields as $idx => $val ) {
                                    if ( array_key_exists( $val, $core->transients['changed_values'] ) ) {
                                        $do_reload = true;
                                    }
                                }
                            }
                        }

                        if ( $do_reload || ( isset ( $values['defaults'] ) && ! empty ( $values['defaults'] ) ) || ( isset ( $values['defaults-section'] ) && ! empty ( $values['defaults-section'] ) ) ) {
                            echo json_encode( array( 'status' => 'success', 'action' => 'reload' ) );
                            die ();
                        }

                        $redux->enqueue_class->get_warnings_and_errors_array();

                        $return_array = array(
                            'status'   => 'success',
                            'options'  => $redux->options,
                            'errors'   => isset ( $redux->localize_data['errors'] ) ? $redux->localize_data['errors'] : null,
                            'warnings' => isset ( $redux->localize_data['warnings'] ) ? $redux->localize_data['warnings'] : null,
                            'sanitize' => isset ( $redux->localize_data['sanitize'] ) ? $redux->localize_data['sanitize'] : null,
                        );

                    } catch ( Exception $e ) {
                        $return_array = array( 'status' => $e->getMessage() );
                    }
                } else {
                    echo json_encode( array( 'status' => esc_html__( 'Your panel has no fields. Nothing to save.', 'redux-framework' ) ) );
                }
            }
            
            if ( isset ( $core->transients['run_compiler'] ) && $core->transients['run_compiler'] ) {

                $core->no_output = true;
                $core->output_class->enqueue();

                try {
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
                } catch ( Exception $e ) {
                    $return_array = array( 'status' => $e->getMessage() );
                }

                unset ( $core->transients['run_compiler'] );
                $core->transient_class->set();
            }

            if ( isset( $return_array ) ) {
                if ( $return_array['status'] == "success" ) {
                    $panel = new Redux_Panel ( $redux );
                    ob_start();
                    $panel->notification_bar();
                    $notification_bar = ob_get_contents();
                    ob_end_clean();
                    $return_array['notification_bar'] = $notification_bar;
                }

                echo json_encode( apply_filters( "redux/options/{$core->args['opt_name']}/ajax_save/response", $return_array ) );
            }

            die ();
        }
        
        /**
         * Parses the string into variables without the max_input_vars limitation.
         *
         * @since   3.5.7.11
         * @author  harunbasic
         * @access  private
         *
         * @param   string $string
         *
         * @return  array $result
         */
        private function parse_str( $string ) {
            if ( '' == $string ) {
                return false;
            }

            $result = array();
            $pairs  = explode( '&', $string );

            foreach ( $pairs as $key => $pair ) {
                // use the original parse_str() on each element
                parse_str( $pair, $params );

                $k = key( $params );

                if ( ! isset( $result[ $k ] ) ) {
                    $result += $params;
                } else {
                    $result[ $k ] = $this->array_merge_recursive_distinct( $result[ $k ], $params[ $k ] );
                }
            }

            return $result;
        }


        /**
         * Merge arrays without converting values with duplicate keys to arrays as array_merge_recursive does.
         * As seen here http://php.net/manual/en/function.array-merge-recursive.php#92195
         *
         * @since   3.5.7.11
         * @author  harunbasic
         * @access  private
         *
         * @param   array $array1
         * @param   array $array2
         *
         * @return  array $merged
         */
        private function array_merge_recursive_distinct( array $array1, array $array2 ) {
            $merged = array();
            
            $merged = $array1;

            foreach ( $array2 as $key => $value ) {
                if ( is_array( $value ) && isset( $merged[ $key ] ) && is_array( $merged[ $key ] ) ) {
                    $merged[ $key ] = $this->array_merge_recursive_distinct( $merged[ $key ], $value );
                } else if ( is_numeric( $key ) && isset( $merged[ $key ] ) ) {
                    $merged[] = $value;
                } else {
                    $merged[ $key ] = $value;
                }
            }

            return $merged;
        }
    }
}