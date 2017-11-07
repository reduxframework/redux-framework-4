<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'ReduxFramework_spinner' ) ) {
    
    class ReduxFramework_spinner extends Redux_Field {

        public function set_defaults() {
            $params = array(
                'min'     => '',
                'max'     => '',
                'step'    => '',
                'default' => '',
            );

            $this->field = wp_parse_args( $this->field, $params );
        }
        
        /**
         * Field Render Function.
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @since ReduxFramework 3.0.0
         */
        function render() {
            $data_string = "";
            
            foreach($this->field as $key => $val) {
                if (in_array($key, array('min', 'max', 'step', 'default'))) {
                    $data_string.= " data-".$key.'="'.$val.'"';
                }
            }
            
            $data_string .= ' data-val="'.$val.'"';


            // Don't allow input edit if there's a step
            $readonly = "";
            if ( isset( $this->field['edit'] ) && $this->field['edit'] == false ) {
                $readonly = ' readonly="readonly"';
            }

            echo '<div id="' . esc_attr($this->field['id']) . '-spinner" class="redux_spinner" rel="' . esc_attr($this->field['id']) . '">';
            echo    '<input type="text" ' . esc_attr($data_string) . ' name="' . esc_attr($this->field['name'] . $this->field['name_suffix']) . '" id="' . esc_attr($this->field['id']) . '" value="' . esc_attr($this->value) . '" class="mini spinner-input ' . esc_attr($this->field['class']) . '"' . $readonly . '/>';
            echo '</div>';
        }

        /**
         * Clean the field data to the fields defaults given the parameters.
         *
         * @since Redux_Framework 3.1.1
         */
        function clean() {
            if ( empty( $this->field['min'] ) ) {
                $this->field['min'] = 0;
            } else {
                $this->field['min'] = intval( $this->field['min'] );
            }

            if ( empty( $this->field['max'] ) ) {
                $this->field['max'] = intval( $this->field['min'] ) + 1;
            } else {
                $this->field['max'] = intval( $this->field['max'] );
            }

            if ( empty( $this->field['step'] ) || $this->field['step'] > $this->field['max'] ) {
                $this->field['step'] = 1;
            } else {
                $this->field['step'] = intval( $this->field['step'] );
            }

            if ( empty( $this->value ) && ! empty( $this->field['default'] ) && intval( $this->field['min'] ) >= 1 ) {
                $this->value = intval( $this->field['default'] );
            }

            if ( empty( $this->value ) && intval( $this->field['min'] ) >= 1 ) {
                $this->value = intval( $this->field['min'] );
            }

            if ( empty( $this->value ) ) {
                $this->value = 0;
            }

            // Extra Validation
            if ( $this->value < $this->field['min'] ) {
                $this->value = intval( $this->field['min'] );
            } else if ( $this->value > $this->field['max'] ) {
                $this->value = intval( $this->field['max'] );
            }
        }

        /**
         * Enqueue Function.
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         *
         * @since ReduxFramework 3.0.0
         */
        function enqueue() {
            wp_enqueue_script(
                'redux-field-spinner-custom-js',
                ReduxCore::$_url . 'inc/fields/spinner/vendor/spinner_custom.js',
                array( 'jquery', 'redux-js' ),
                $this->timestamp,
                true
            );

            wp_enqueue_script(
                'redux-field-spinner-js',
                ReduxCore::$_url . 'inc/fields/spinner/field_spinner' . Redux_Functions::isMin() . '.js',
                array(
                    'jquery',
                    'redux-field-spinner-custom-js',
                    'jquery-ui-core',
                    'jquery-ui-dialog',
                    'redux-js'
                ),
                $this->timestamp,
                true
            );

            if ($this->parent->args['dev_mode']) {
                wp_enqueue_style(
                    'redux-field-spinner-css',
                    ReduxCore::$_url . 'inc/fields/spinner/field_spinner.css',
                    array(),
                    $this->timestamp,
                    'all'
                );
            }
        }
        
        public function output($style = '') {
            $style = '';

            if ( ! empty( $this->value ) ) {
                if ( ! empty( $this->field['output'] ) && is_array( $this->field['output'] ) ) {
                    $css = $this->parseCSS($this->value, $this->field['output']);
                    $this->parent->outputCSS .= esc_attr($css);
                }

                if ( ! empty( $this->field['compiler'] ) && is_array( $this->field['compiler'] ) ) {
                    $css = $this->parseCSS($this->value, $this->field['compiler']);
                    $this->parent->compilerCSS .= esc_attr($css);

                }
            }            
        }
        
        private function parseCSS($value, $output){
            // No notices
            $css = '';

            $unit = isset($this->field['output_unit']) ? $this->field['output_unit'] : 'px';
            
            // Must be an array
            if (is_numeric($value)) {
                if (is_array($output)) {
                    foreach($output as $selector => $mode) {
                        if ($mode != '' && $selector != '') {
                            $css .= $selector . '{' . $mode . ':' . $value . $unit . ';}';
                        }
                    }
                }
            }

            return $css;
        }
    }
}