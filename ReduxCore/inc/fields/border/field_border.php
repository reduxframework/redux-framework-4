<?php

/**
 * Redux Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 * Redux Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with Redux Framework. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package     Redux_Field
 * @subpackage  Border
 * @version     3.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Don't duplicate me!
if ( ! class_exists( 'ReduxFramework_border' ) ) {
    
    class ReduxFramework_border extends Redux_Field {

        public function set_defaults() {
            // No errors please
            $defaults = array(
                'top'    => true,
                'bottom' => true,
                'all'    => true,
                'style'  => true,
                'color'  => true,
                'left'   => true,
                'right'  => true,
            );

            $this->field = wp_parse_args( $this->field, $defaults );

            $defaults = array(
                'top'    => '',
                'right'  => '',
                'bottom' => '',
                'left'   => '',
                'color'  => '',
                'style'  => '',
            );

            $this->value = wp_parse_args( $this->value, $defaults );
            
            if (ReduxCore::$_pro_loaded) {
                $this->field = apply_filters('redux/pro/border/field/set_defaults', $this->field);
            } else {
                $this->field['color_alpha'] = array();
                $this->field['color_alpha'] = false;
            }            
        }
        
        /**
         * Field Render Function.
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @since ReduxFramework 1.0.0
         */
        function render() {
            $value = array(
                'top'    => isset( $this->value['border-top'] ) ? filter_var( $this->value['border-top'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ) : filter_var( $this->value['top'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ),
                'right'  => isset( $this->value['border-right'] ) ? filter_var( $this->value['border-right'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ) : filter_var( $this->value['right'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ),
                'bottom' => isset( $this->value['border-bottom'] ) ? filter_var( $this->value['border-bottom'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ) : filter_var( $this->value['bottom'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ),
                'left'   => isset( $this->value['border-left'] ) ? filter_var( $this->value['border-left'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ) : filter_var( $this->value['left'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ),
                'color'  => isset( $this->value['border-color'] ) ? $this->value['border-color'] : $this->value['color'],
                'style'  => isset( $this->value['border-style'] ) ? $this->value['border-style'] : $this->value['style']
            );

            if ( ( isset( $this->value['width'] ) || isset( $this->value['border-width'] ) ) ) {
                if ( isset( $this->value['border-width'] ) && ! empty( $this->value['border-width'] ) ) {
                    $this->value['width'] = $this->value['border-width'];
                }

                $this->value['width'] = $this->stripAlphas($this->value['width']);

                $value['top']    = $this->value['width'];
                $value['right']  = $this->value['width'];
                $value['bottom'] = $this->value['width'];
                $value['left']   = $this->value['width'];
            }

            $this->value = $value;

            $defaults = array(
                'top'    => '',
                'right'  => '',
                'bottom' => '',
                'left'   => '',
            );

            $this->value = wp_parse_args( $this->value, $defaults );

            $this->select2_config['allowClear'] = true;
            
            if ( isset( $this->field['select2'] ) ) {
                $this->field['select2'] = wp_parse_args($this->field['select2'], $this->select2_config);
            } else {
                $this->field['select2'] = $this->select2_config;
            }
            
            $this->field['select2'] = Redux_Functions::sanitize_camel_case_array_keys($this->field['select2']);
            
            $select2_data = Redux_Functions::create_data_string($this->field['select2']);
            
            echo '<input type="hidden" class="field-units" value="px">';

            if ( isset( $this->field['all'] ) && $this->field['all'] == true ) {
                echo '<div class="field-border-input input-prepend"><span class="add-on"><i class="el el-fullscreen icon-large"></i></span><input type="text" class="redux-border-all redux-border-input mini ' . esc_attr($this->field['class']) . '" placeholder="' . esc_html__( 'All', 'redux-framework' ) . '" rel="' . esc_attr($this->field['id']) . '-all" value="' . esc_attr($this->value['top']) . '"></div>';
            }

            echo '<input type="hidden" class="redux-border-value" id="' . esc_attr($this->field['id']) . '-top" name="' . esc_attr($this->field['name'] . $this->field['name_suffix']) . '[border-top]" value="' . ( isset($this->value['top']) && $this->value['top'] != '' ? esc_attr($this->value['top']) . 'px' : '' ) . '">';
            echo '<input type="hidden" class="redux-border-value" id="' . esc_attr($this->field['id']) . '-right" name="' . esc_attr($this->field['name'] . $this->field['name_suffix']) . '[border-right]" value="' . ( isset($this->value['right']) && $this->value['right'] != '' ? esc_attr($this->value['right']) . 'px' : '' ) . '">';
            echo '<input type="hidden" class="redux-border-value" id="' . esc_attr($this->field['id']) . '-bottom" name="' . esc_attr($this->field['name'] . $this->field['name_suffix']) . '[border-bottom]" value="' . ( isset($this->value['bottom']) && $this->value['bottom'] != '' ? esc_attr($this->value['bottom']) . 'px' : '' ) . '">';
            echo '<input type="hidden" class="redux-border-value" id="' . esc_attr($this->field['id']) . '-left" name="' . esc_attr($this->field['name'] . $this->field['name_suffix']) . '[border-left]" value="' . ( isset($this->value['left']) && $this->value['left'] != '' ? esc_attr($this->value['left']) . 'px' : '' ) . '">';

            if ( ! isset( $this->field['all'] ) || $this->field['all'] !== true ) {
                /**
                 * Top
                 * */
                if ( $this->field['top'] === true ) {
                    echo '<div class="field-border-input input-prepend">
                            <span class="add-on">
                                <i class="el el-arrow-up icon-large"></i>
                            </span>
                            <input type="text" class="redux-border-top redux-border-input mini ' . esc_attr($this->field['class']) . '" placeholder="' . esc_html__( 'Top', 'redux-framework' ) . '" rel="' . esc_attr($this->field['id']) . '-top" value="' . esc_attr($this->value['top']) . '">
                         </div>';
                }

                /**
                 * Right
                 * */
                if ( $this->field['right'] === true ) {
                    echo '<div class="field-border-input input-prepend">
                            <span class="add-on">
                                <i class="el el-arrow-right icon-large"></i>
                            </span>
                            <input type="text" class="redux-border-right redux-border-input mini ' . esc_attr($this->field['class']) . '" placeholder="' . esc_html__( 'Right', 'redux-framework' ) . '" rel="' . esc_attr($this->field['id']) . '-right" value="' . esc_attr($this->value['right']) . '">
                        </div>';
                }

                /**
                 * Bottom
                 * */
                if ( $this->field['bottom'] === true ) {
                    echo '<div class="field-border-input input-prepend">
                            <span class="add-on">
                                <i class="el el-arrow-down icon-large"></i>
                            </span>
                            <input type="text" class="redux-border-bottom redux-border-input mini ' . esc_attr($this->field['class']) . '" placeholder="' . esc_html__( 'Bottom', 'redux-framework' ) . '" rel="' . esc_attr($this->field['id']) . '-bottom" value="' . esc_attr($this->value['bottom']) . '">
                        </div>';
                }

                /**
                 * Left
                 * */
                if ( $this->field['left'] === true ) {
                    echo '<div class="field-border-input input-prepend">
                            <span class="add-on">
                                <i class="el el-arrow-left icon-large"></i>
                            </span>
                            <input type="text" class="redux-border-left redux-border-input mini ' . esc_attr($this->field['class']) . '" placeholder="' . esc_html__( 'Left', 'redux-framework' ) . '" rel="' . esc_attr($this->field['id']) . '-left" value="' . esc_attr($this->value['left']) . '">
                        </div>';
                }
            }

            /**
             * Border-style
             * */
            if ( $this->field['style'] != false ) {
                $options = array(
                    'solid'  => esc_html__('Solid', 'redux-framework'),
                    'dashed' => esc_html__('Dashed', 'redux-framework'),
                    'dotted' => esc_html__('Dotted', 'redux-framework'),
                    'double' => esc_html__("Double", 'redux-framework'),
                    'none'   => esc_html__('None', 'redux-framework')
                );
                
                echo '<select data-placeholder="' . esc_html__( 'Border style', 'redux-framework' ) . '" id="' . esc_attr($this->field['id']) . '[border-style]" name="' . esc_attr($this->field['name'] . $this->field['name_suffix']) . '[border-style]" class="tips redux-border-style ' . esc_attr($this->field['class']) . '" rows="6" data-id="' . esc_attr($this->field['id']) . '"' . esc_attr($select2_data) . '>';
                
                foreach ( $options as $k => $v ) {
                    echo '<option value="' . esc_attr($k) . '"' . selected( $value['style'], $k, false ) . '>' . $v . '</option>';
                }
                
                echo '</select>';
            } else {
                echo '<input type="hidden" id="' . esc_attr($this->field['id']) . '[border-style]" name="' . esc_attr($this->field['name'] . $this->field['name_suffix']) . '[border-style]" value="' . esc_attr($this->value['style']) . '" data-id="' . esc_attr($this->field['id']) . '">';
            }

            /**
             * Color
             * */
            if ( $this->field['color'] != false ) {
                $default = isset( $this->field['default']['border-color'] ) ? $this->field['default']['border-color'] : '';

                if ( empty( $default ) ) {
                    $default = ( isset( $this->field['default']['color'] ) ) ? $this->field['default']['color'] : '#ffffff';
                }

                echo '<input 
                        name="' . esc_attr($this->field['name'] . $this->field['name_suffix']) . '[border-color]"
                        id="' . esc_attr($this->field['id']) . '-border"
                        class="color-picker redux-border-color redux-color redux-color-init ' . esc_attr($this->field['class']) . '"
                        type="text" 
                        value="' . esc_attr($this->value['color']) . '"
                        data-default-color="' . esc_attr($default) . '"
                        data-id="' . esc_attr($this->field['id']) . '"
                      />';
            } else {
                echo '<input type="hidden" id="' . esc_attr($this->field['id']) . '[border-color]" name="' . esc_attr($this->field['name'] . $this->field['name_suffix']) . '[border-color]" value="' . esc_attr($this->value['color']) . '" data-id="' . esc_attr($this->field['id']) . '">';
            }
        }

        /**
         * Enqueue Function.
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         *
         * @since ReduxFramework 1.0.0
         */
        function enqueue() {
            $min = Redux_Functions::isMin();
            
            if (!wp_style_is ( 'select2-css' )) {
                wp_enqueue_style( 'select2-css' );
            }
            
            if (!wp_style_is ( 'wp-color-picker' )) {
                wp_enqueue_style( 'wp-color-picker' );
            }
            
            $dep_array = array( 'jquery', 'select2-js', 'wp-color-picker', 'redux-js' );
            
            wp_enqueue_script(
                'redux-field-border-js',
                ReduxCore::$_url . 'inc/fields/border/field_border' . $min . '.js',
                $dep_array,
                ReduxCore::$_version,
                true
            );

            if ($this->parent->args['dev_mode']) {
                if (!wp_style_is ( 'redux-color-picker-css' )) {
                    wp_enqueue_style( 'redux-color-picker-css' );
                }
                
                wp_enqueue_style(
                    'redux-field-border-css',
                    ReduxCore::$_url . 'inc/fields/border/field_border.css',
                    array(),
                    time(),
                    'all'
                );
            }
        }

        public function css_style ($data) {
            $style = "";
            
            if ( isset( $this->field['all'] ) && true == $this->field['all'] ) {
                $borderWidth = isset( $data['border-width'] ) ? $data['border-width'] : '0px';
                $val         = isset( $data['border-top'] ) ? $data['border-top'] : $borderWidth;

                $data['border-top']    = $val;
                $data['border-bottom'] = $val;
                $data['border-left']   = $val;
                $data['border-right']  = $val;
            }

            $cleanValue = array(
                'color' => ! empty( $data['border-color'] ) ? $data['border-color'] : '',
                'style' => ! empty( $data['border-style'] ) ? $data['border-style'] : ''
            );

            $borderWidth = '';
            if ( isset( $data['border-width'] ) ) {
                $borderWidth = $data['border-width'];
            }

            $this->field['top']    = isset( $this->field['top'] ) ? $this->field['top'] : true;
            $this->field['bottom'] = isset( $this->field['bottom'] ) ? $this->field['bottom'] : true;
            $this->field['left']   = isset( $this->field['left'] ) ? $this->field['left'] : true;
            $this->field['right']  = isset( $this->field['right'] ) ? $this->field['right'] : true;

            if ( $this->field['top'] === true ) {
                $cleanValue['top'] = ! empty( $data['border-top'] ) ? $data['border-top'] : $borderWidth;
            }

            if ( $this->field['bottom'] == true ) {
                $cleanValue['bottom'] = ! empty( $data['border-bottom'] ) ? $data['border-bottom'] : $borderWidth;
            }

            if ( $this->field['left'] === true ) {
                $cleanValue['left'] = ! empty( $data['border-left'] ) ? $data['border-left'] : $borderWidth;
            }

            if ( $this->field['right'] === true ) {
                $cleanValue['right'] = ! empty( $data['border-right'] ) ? $data['border-right'] : $borderWidth;
            }

            //absolute, padding, margin
            if ( ! isset( $this->field['all'] ) || $this->field['all'] != true ) {
                foreach ( $cleanValue as $key => $value ) {
                    if ( $key == "color" || $key == "style" ) {
                        continue;
                    }
                    if (!empty($value)) {
                        $style .= 'border-' . $key . ':' . $value . ' ' . $cleanValue['style'] . ' ' . $cleanValue['color'] . ';';
                    }
                }
            } else {
                if (!empty($cleanValue['top'])) {
                    $style .= 'border:' . $cleanValue['top'] . ' ' . $cleanValue['style'] . ' ' . $cleanValue['color'] . ';';
                }
            }

            return $style;
        }
        
        private function stripAlphas($s) {

            // Regex is our friend.  THERE ARE FOUR LIGHTS!!
            return preg_replace('/[^\d.-]/', '', $s);
        }             
    }
}