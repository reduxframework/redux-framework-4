<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'ReduxFramework_spacing' ) ) {
    
    class ReduxFramework_spacing extends Redux_Field {

        public function set_defaults() {
            $defaults = array(
                'units'          => 'px',
                'mode'           => 'padding',
                'top'            => true,
                'bottom'         => true,
                'all'            => false,
                'left'           => true,
                'right'          => true,
                'units_extended' => false,
                'display_units'  => true
            );

            $this->field = wp_parse_args( $this->field, $defaults );

            // Set default values
            $defaults = array(
                'top'    => '',
                'right'  => '',
                'bottom' => '',
                'left'   => '',
                'units'  => ''
            );

            $this->value = wp_parse_args( $this->value, $defaults );
        }
        
        /**
         * Field Render Function.
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @since ReduxFramework 1.0.0
         */
        function render() {
            /*
             * Acceptable values checks.  If the passed variable doesn't pass muster, we unset them
             * and reset them with default values to avoid errors.
             */

            $unit_arr = array('',false,'%','in','cm','mm','em','rem','ex','pt','pc','px','vh','vw','vmin','vmax','ch');
            
            // If units field has a value but is not an acceptable value, unset the variable
            if ( !Redux_Helpers::array_in_array( $this->field['units'], $unit_arr ) ) {
                $this->field['units'] = 'px';
            }

            //if there is a default unit value  but is not an accepted value, unset the variable
            if ( !Redux_Helpers::array_in_array( $this->value['units'], $unit_arr ) ) {
                $this->value['units'] = 'px';
            }

            if ( $this->field['units'] == false ) {
                $this->value == "";
            }

            if ( !in_array( $this->field['mode'], array('margin', 'padding') ) ) {
                if ( $this->field['mode'] == "absolute" ) {
                    $this->field['mode'] = "";
                } else {
                    $this->field['mode'] = "padding";
                }
            }

            $value = array(
                'top'    => isset( $this->value[ $this->field['mode'] . '-top' ] ) ? filter_var( $this->value[ $this->field['mode'] . '-top' ], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ) : filter_var( $this->value['top'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ),
                'right'  => isset( $this->value[ $this->field['mode'] . '-right' ] ) ? filter_var( $this->value[ $this->field['mode'] . '-right' ], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ) : filter_var( $this->value['right'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ),
                'bottom' => isset( $this->value[ $this->field['mode'] . '-bottom' ] ) ? filter_var( $this->value[ $this->field['mode'] . '-bottom' ], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ) : filter_var( $this->value['bottom'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ),
                'left'   => isset( $this->value[ $this->field['mode'] . '-left' ] ) ? filter_var( $this->value[ $this->field['mode'] . '-left' ], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ) : filter_var( $this->value['left'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION )
            );

            // if field units has a value and is NOT an array, then evaluate as needed.
            if ( !is_array( $this->field['units'] ) ) {

                //if units fields has a value and is not empty but units value does not then make units value the field value
                if ( $this->value['units'] == '' && ($this->field['units'] != "" || $this->field['units'] == false )) {
                    $this->value['units'] = $this->field['units'];

                    // If units field does NOT have a value and units value does NOT have a value, set both to blank (default?)
                } else if ( $this->field['units'] == '' && $this->value['units'] = '' ) {
                    $this->field['units'] = 'px';
                    $this->value['units'] = 'px';

                    // If units field has NO value but units value does, then set unit field to value field
                } else if ( $this->field['units'] == '' && $this->value['units'] != '' ) {
                    $this->field['units'] = $this->value['units'];

                    // if unit value is set and unit value doesn't equal unit field (coz who knows why)
                    // then set unit value to unit field
                } elseif ( $this->value['units'] != '' && $this->value['units'] !== $this->field['units'] ) {
                    $this->value['units'] = $this->field['units'];
                }

                // do stuff based on unit field NOT set as an array
            } elseif ( !empty ( $this->field['units'] ) && is_array( $this->field['units'] ) ) {
                // nothing to do here, but I'm leaving the construct just in case I have to debug this again.
            }

            if ( $this->field['units'] != '' ) {
                $value['units'] = $this->value['units'];
            }

            $this->value = $value;

            if ( $this->field['mode'] != '' ) {
                $this->field['mode'] = $this->field['mode'] . "-";
            }
            
            if ( isset( $this->field['select2'] ) ) {
                $this->field['select2'] = wp_parse_args($this->field['select2'], $this->select2_config);
            } else {
                $this->field['select2'] = $this->select2_config;
            }
            
            $this->field['select2'] = Redux_Functions::sanitize_camel_case_array_keys($this->field['select2']);
            
            $select2_data = Redux_Functions::create_data_string($this->field['select2']);            
            
            echo '<input type="hidden" name="' . esc_attr($this->field['name'] . $this->field['name_suffix']) . '[units]' . '" class="field-units" value="' . esc_attr($this->value['units']) . '">';

            if ( $this->field['all'] == true ) {
                $this->field['top']     = true;
                $this->field['right']   = true;
                $this->field['bottom']  = true;
                $this->field['left']    = true;
                
                $this->value['bottom'] = $this->value['top'];
                $this->value['left'] = $this->value['top'];
                $this->value['right'] = $this->value['top'];
                
                echo '<div class="field-spacing-input input-prepend">
                        <span class="add-on">
                            <i class="el el-fullscreen icon-large"></i>
                        </span>
                        <input 
                            type="text" 
                            class="redux-spacing-all redux-spacing-input mini ' . esc_attr($this->field['class']) . '" 
                            placeholder="' . esc_html__( 'All', 'redux-framework' ) . '" 
                            rel="' . esc_attr($this->field['id']) . '-all" 
                            value="' . esc_attr($this->value['top']) . '"
                        >
                      </div>';
            }

            if ( $this->field['top'] == true ) {
                echo '<input 
                        type="hidden" 
                        class="redux-spacing-value" 
                        id="' . esc_attr($this->field['id']) . '-top" 
                        name="' . esc_attr($this->field['name'] . $this->field['name_suffix'] . '[' . $this->field['mode']) . 'top]' . '" 
                        value="' . esc_attr($this->value['top']) . ( ! empty( $this->value['top'] ) ? esc_attr($this->value['units']) : '' ) . '"
                      >';
            }

            if ( $this->field['right'] == true ) {
                echo '<input 
                        type="hidden" 
                        class="redux-spacing-value" 
                        id="' . esc_attr($this->field['id']) . '-right" 
                        name="' . esc_attr($this->field['name'] . $this->field['name_suffix'] . '[' . $this->field['mode']) . 'right]' . '" 
                        value="' . esc_attr($this->value['right']) . ( ! empty( $this->value['right'] ) ? esc_attr($this->value['units']) : '' ) . '"
                      >';
            }

            if ( $this->field['bottom'] == true ) {
                echo '<input 
                        type="hidden" 
                        class="redux-spacing-value" 
                        id="' . esc_attr($this->field['id']) . '-bottom" 
                        name="' . esc_attr($this->field['name'] . $this->field['name_suffix'] . '[' . $this->field['mode']) . 'bottom]' . '" 
                        value="' . esc_attr($this->value['bottom']) . ( ! empty( $this->value['bottom'] ) ? esc_attr($this->value['units']) : '' ) . '"
                      >';
            }

            if ( $this->field['left'] == true ) {
                echo '<input 
                        type="hidden" 
                        class="redux-spacing-value" 
                        id="' . esc_attr($this->field['id']) . '-left" 
                        name="' . esc_attr($this->field['name'] . $this->field['name_suffix'] . '[' . $this->field['mode']) . 'left]' . '" 
                        value="' . esc_attr($this->value['left']) . ( ! empty( $this->value['left'] ) ? esc_attr($this->value['units']) : '' ) . '"
                      >';
            }

            if ( $this->field['all'] == false ) {
                /**
                 * Top
                 * */
                if ( $this->field['top'] == true ) {
                    echo '<div class="field-spacing-input input-prepend">
                            <span class="add-on">
                                <i class="el el-arrow-up icon-large"></i>
                            </span>
                            <input type="text" 
                                   class="redux-spacing-top redux-spacing-input mini ' . esc_attr($this->field['class']) . '"
                                   placeholder="' . esc_html__( 'Top', 'redux-framework' ) . '"
                                   rel="' . esc_attr($this->field['id']) . '-top"
                                   value="' . esc_attr($this->value['top']) . '"
                            >
                        </div>';
                }

                /**
                 * Right
                 * */
                if ( $this->field['right'] == true ) {
                    echo '<div class="field-spacing-input input-prepend">
                            <span class="add-on">
                                <i class="el el-arrow-right icon-large"></i>
                            </span>
                            <input type="text" 
                                   class="redux-spacing-right redux-spacing-input mini ' . esc_attr($this->field['class']) . '"
                                   placeholder="' . esc_html__( 'Right', 'redux-framework' ) . '"
                                   rel="' . esc_attr($this->field['id']) . '-right"
                                   value="' . esc_attr($this->value['right']) . '"
                            >
                        </div>';
                }

                /**
                 * Bottom
                 * */
                if ( $this->field['bottom'] == true ) {
                    echo '<div class="field-spacing-input input-prepend">
                            <span class="add-on">
                                <i class="el el-arrow-down icon-large"></i>
                            </span>
                            <input type="text" 
                                   class="redux-spacing-bottom redux-spacing-input mini ' . esc_attr($this->field['class']) . '"
                                   placeholder="' . esc_html__( 'Bottom', 'redux-framework' ) . '"
                                   rel="' . esc_attr($this->field['id']) . '-bottom"
                                   value="' . esc_attr($this->value['bottom']) . '"
                            >
                        </div>';
                }

                /**
                 * Left
                 * */
                if ( $this->field['left'] == true ) {
                    echo '<div class="field-spacing-input input-prepend">
                            <span class="add-on">
                                <i class="el el-arrow-left icon-large"></i>
                            </span>
                            <input type="text" 
                                   class="redux-spacing-left redux-spacing-input mini ' . esc_attr($this->field['class']) . '"
                                   placeholder="' . esc_html__( 'Left', 'redux-framework' ) . '"
                                   rel="' . esc_attr($this->field['id']) . '-left"
                                   value="' . esc_attr($this->value['left']) . '"
                            >
                        </div>';
                }
            }

            /**
             * Units
             * */
            if ( $this->field['units'] != false && /* is_array( $this->field['units'] ) && */ $this->field['display_units'] == true ) {
                echo '<div class="select_wrapper spacing-units" original-title="' . esc_html__( 'Units', 'redux-framework' ) . '">';
                echo '<select data-placeholder="' . esc_html__( 'Units', 'redux-framework' ) . '" class="redux-spacing redux-spacing-units select ' . esc_attr($this->field['class']) . '" original-title="' . esc_html__( 'Units', 'redux-framework' ) . '" id="' . esc_attr($this->field['id']) . '_units"' . esc_attr($select2_data) . '>';

                if ( $this->field['units_extended'] ) {
                    $testUnits = array( 'px', 'em', 'rem', '%', 'in', 'cm', 'mm', 'ex', 'pt', 'pc' );
                } else {
                    $testUnits = array( 'px', 'em', 'pt', 'rem', '%' );
                }

                if ( $this->field['units'] != "" || is_array( $this->field['units'] ) ) {
                    $testUnits = $this->field['units'];
                }

                echo '<option></option>';

                if (!is_array($this->field['units'])) {
                    echo '<option value="' . esc_attr($this->field['units']) . '" selected="selected">' . esc_attr($this->field['units']) . '</option>';
                } else {
                    foreach ( $testUnits as $aUnit ) {
                        echo '<option value="' . esc_attr($aUnit) . '" ' . selected( $this->value['units'], $aUnit, false ) . '>' . esc_html($aUnit) . '</option>';
                    }
                }
                
                echo '</select></div>';
            }
        }

        /**
         * Enqueue Function.
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         *
         * @since ReduxFramework 1.0.0
         */
        function enqueue() {
            wp_enqueue_style( 'select2-css' );
            
            wp_enqueue_script(
                'redux-field-spacing-js',
                ReduxCore::$_url . 'inc/fields/spacing/field_spacing' . Redux_Functions::isMin() . '.js',
                array( 'jquery', 'select2-js', 'redux-js' ),
                ReduxCore::$_version,
                true
            );

            if ($this->parent->args['dev_mode']) {
                wp_enqueue_style(
                    'redux-field-spacing-css',
                    ReduxCore::$_url . 'inc/fields/spacing/field_spacing.css',
                    array(),
                    time(),
                    'all'
                );
            }
        }

        public function css_style($data) {
            $style = '';
            
            if ( !in_array( $this->field['mode'], array( 'padding', 'absolute', 'margin' ) ) ) {
                $this->field['mode'] = "padding";
            }
            
            $mode  = ( $this->field['mode'] != "absolute" ) ? $this->field['mode'] : "";
            $units = isset( $data['units'] ) ? $data['units'] : "";
            
            foreach ( $data as $key => $value ) {
                if ( $key == "units" ) {
                    continue;
                }

                // Strip off any alpha for is_numeric test - kp
                $num_no_alpha = preg_replace('/[^\d.-]/', '', $value);

                // Output if it's a numeric entry
                if ( isset( $value ) && is_numeric( $num_no_alpha ) ) {
                    $style .= $key . ':' . $num_no_alpha . $units . ';';
                }

            }
            
            return $style;
        }
    }
}