<?php

    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    if ( ! class_exists( 'Redux_Field' ) ) {

        class Redux_Field {
            public $style = '';
            public $_dir = "";
            public $_url = "";

            public function __construct( $field = array(), $value = '', $parent ) {
                $this->parent = $parent;
                $this->field  = $field;
                $this->value  = $value;

                $this->select2_config = array(
                    'width'      => 'resolve',
                    'allowClear' => false,
                    'theme'      => 'default'
                );

                $this->set_defaults();

                $class_name = get_class( $this );
                $reflector  = new ReflectionClass( $class_name );
                $path       = $reflector->getFilename();
                $path_info  = Redux_Helpers::path_info( $path );
                $this->_dir = trailingslashit( dirname( $path_info['realpath'] ) );
                $this->_url = trailingslashit( dirname( $path_info['url'] ) );
            }

            protected function get_dir() {
                $rc = new Redux_Field( get_class( $this ) );

                return dirname( $rc->getFileName() );
            }

            public function media_query( $style_data = '' ) {
                //var_dump($this->field['media_query']);

                $query_arr = $this->field['media_query'];
                $css       = '';

                if ( isset( $query_arr['queries'] ) ) {
                    foreach ( $query_arr['queries'] as $idx => $query ) {
                        $rule      = isset( $query['rule'] ) ? $query['rule'] : '';
                        $selectors = isset( $query['selectors'] ) ? $query['selectors'] : array();

                        if ( ! is_array( $selectors ) && $selectors != '' ) {
                            $selectors = array( $selectors );
                        }

                        if ( $rule != '' && ! empty( $selectors ) ) {
                            $selectors = implode( ",", $selectors );

                            $css .= '@media ' . $rule . '{';
                            $css .= $selectors . '{' . $style_data . '}';
                            $css .= '}';
                        }
                    }
                } else {
                    return;
                }

                if ( isset( $query_arr['output'] ) && $query_arr['output'] ) {
                    $this->parent->outputCSS .= $css;
                }

                if ( isset( $query_arr['compiler'] ) && $query_arr['compiler'] ) {
                    $this->parent->compilerCSS .= $css;
                }
            }

            public function output( $style = '' ) {
                if ( $style != '' ) {
                    if ( ! empty( $this->field['output'] ) && is_array( $this->field['output'] ) ) {
                        $keys                    = implode( ",", $this->field['output'] );
                        $this->parent->outputCSS .= esc_attr( $keys . "{" . $style . '}' );
                    }

                    if ( ! empty( $this->field['compiler'] ) && is_array( $this->field['compiler'] ) ) {
                        $keys                      = implode( ",", $this->field['compiler'] );
                        $this->parent->compilerCSS .= esc_attr( $keys . "{" . $style . '}' );
                    }
                }
            }

            public function css_style( $data ) { }

            public function set_defaults() { }

            public function render() { }

            public function enqueue() { }

            public function localize( $field, $value = "" ) { }
        }
    }
