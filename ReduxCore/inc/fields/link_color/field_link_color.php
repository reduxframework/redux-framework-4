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
 * @package     ReduxFramework
 * @subpackage  Field_Link_Color
 * @author      Luciano "WebCaos" Ubertini
 * @author      Kevin Provance (kprovance)
 * @author      Dovy Paukstys
 * @version     4.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Don't duplicate me!
if ( ! class_exists( 'ReduxFramework_link_color' ) ) {

    /**
     * Main ReduxFramework_link_color class
     *
     * @since       1.0.0
     */
    class ReduxFramework_link_color extends Redux_Field {

        public function set_defaults() {
            $defaults    = array(
                'regular' => true,
                'hover'   => true,
                'visited' => false,
                'active'  => true,
                'focus'   => false
            );
            
            $this->field = wp_parse_args( $this->field, $defaults );

            $defaults = array(
                'regular' => '',
                'hover'   => '',
                'visited' => '',
                'active'  => '',
                'focus'   => ''
            );

            $this->value = wp_parse_args( $this->value, $defaults );

            // In case user passes no default values.
            if ( isset( $this->field['default'] ) ) {
                $this->field['default'] = wp_parse_args( $this->field['default'], $defaults );
            } else {
                $this->field['default'] = $defaults;
            }
        }
        
        /**
         * Field Render Function.
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function render() {
            if ( $this->field['regular'] === true && $this->field['default']['regular'] !== false ) {
                echo '<span class="linkColor">';
                echo     '<strong>' . esc_html__( 'Regular', 'redux-framework' ) . '</strong>&nbsp;';
                echo     '<input ';
                echo         'id="' . esc_attr($this->field['id']) . '-regular" ';
                echo         'name="' . esc_attr($this->field['name'] . $this->field['name_suffix']) . '[regular]' . '"';
                echo         'value="' . esc_attr($this->value['regular']) . '"';
                echo         'class="color-picker redux-color redux-color-regular redux-color-init ' . esc_attr($this->field['class']) . '"';
                echo         'type="text"';
                echo         'data-default-color="' . esc_attr($this->field['default']['regular']) . '"';
                
                if (ReduxCore::$_pro_loaded) {
                    $data = array(
                        'field' => $this->field,
                        'index' => 'regular'
                    );

                    echo apply_filters('redux/pro/render/color_alpha', $data);
                }                
                
                echo     '/>';
                echo '</span>';
            }

            if ( $this->field['hover'] === true && $this->field['default']['hover'] !== false ) {
                echo '<span class="linkColor">';
                echo     '<strong>' . esc_html__( 'Hover', 'redux-framework' ) . '</strong>&nbsp;';
                echo     '<input ';
                echo         'id="' . esc_attr($this->field['id']) . '-hover"';
                echo         'name="' . esc_attr($this->field['name'] . $this->field['name_suffix']) . '[hover]' . '"';
                echo         'value="' . esc_attr($this->value['hover']) . '"';
                echo         'class="color-picker redux-color redux-color-hover redux-color-init ' . esc_attr($this->field['class']) . '"';
                echo         'type="text"';
                echo         'data-default-color="' . esc_attr($this->field['default']['hover']) . '"';
                
                if (ReduxCore::$_pro_loaded) {
                    $data = array(
                        'field' => $this->field,
                        'index' => 'hover'
                    );

                    echo apply_filters('redux/pro/render/color_alpha', $data);
                }                
                
                echo     '/>';
                echo '</span>';
            }

            if ( $this->field['visited'] === true && $this->field['default']['visited'] !== false ) {
                echo '<span class="linkColor">';
                echo     '<strong>' . esc_html__( 'Visited', 'redux-framework' ) . '</strong>&nbsp;';
                echo     '<input ';
                echo         'id="' . esc_attr($this->field['id']) . '-visited"';
                echo         'name="' . esc_attr($this->field['name'] . $this->field['name_suffix']) . '[visited]' . '"';
                echo         'value="' . esc_attr($this->value['visited']) . '"';
                echo         'class="color-picker redux-color redux-color-visited redux-color-init ' . esc_attr($this->field['class']) . '"';
                echo         'type="text"';
                echo         'data-default-color="' . esc_attr($this->field['default']['visited']) . '"';
                
                if (ReduxCore::$_pro_loaded) {
                    $data = array(
                        'field' => $this->field,
                        'index' => 'visited'
                    );

                    echo apply_filters('redux/pro/render/color_alpha', $data);
                }                
                
                echo     '/>';
                echo '</span>';
            }

            if ( $this->field['active'] === true && $this->field['default']['active'] !== false ) {
                echo '<span class="linkColor">';
                echo     '<strong>' . esc_html__( 'Active', 'redux-framework' ) . '</strong>&nbsp;';
                echo     '<input ';
                echo         'id="' . esc_attr($this->field['id']) . '-active"';
                echo         'name="' . esc_attr($this->field['name'] . $this->field['name_suffix']) . '[active]' . '"';
                echo         'value="' . esc_attr($this->value['active']) . '"';
                echo         'class="color-picker redux-color redux-color-active redux-color-init ' . esc_attr($this->field['class']) . '"';
                echo         'type="text"';
                echo         'data-default-color="' . esc_attr($this->field['default']['active']) . '"';
                
                if (ReduxCore::$_pro_loaded) {
                    $data = array(
                        'field' => $this->field,
                        'index' => 'active'
                    );

                    echo apply_filters('redux/pro/render/color_alpha', $data);
                }                
                
                echo     '/>';
                echo '</span>';
            }
            
            if ( $this->field['focus'] === true && $this->field['default']['focus'] !== false ) {
                echo '<span class="linkColor">';
                echo     '<strong>' . esc_html__( 'Focus', 'redux-framework' ) . '</strong>&nbsp;';
                echo     '<input ';
                echo         'id="' . esc_attr($this->field['id']) . '-focus"';
                echo         'name="' . esc_attr($this->field['name'] . $this->field['name_suffix']) . '[focus]' . '"';
                echo         'value="' . esc_attr($this->value['focus']) . '"';
                echo         'class="color-picker redux-color redux-color-focus redux-color-init ' . esc_attr($this->field['class']) . '"';
                echo         'type="text"';
                echo         'data-default-color="' . esc_attr($this->field['default']['focus']) . '"';
                
                if (ReduxCore::$_pro_loaded) {
                    $data = array(
                        'field' => $this->field,
                        'index' => 'focus'
                    );

                    echo apply_filters('redux/pro/render/color_alpha', $data);
                }                
                
                echo     '/>';
                echo '</span>';
            }
        }

        /**
         * Enqueue Function.
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function enqueue() {
            wp_enqueue_style( 'wp-color-picker' );
            
            $dep_array = array( 'jquery', 'wp-color-picker', 'redux-js' );
            
            wp_enqueue_script(
                'redux-field-link-color-js',
                ReduxCore::$_url . 'inc/fields/link_color/field_link_color' . Redux_Functions::isMin() . '.js',
                $dep_array,
                $this->timestamp,
                true
            );

            if (ReduxCore::$_pro_loaded) {
                do_action('redux/pro/enqueue/color_alpha', $this->field);
            }
            
            if ($this->parent->args['dev_mode']) {
                wp_enqueue_style( 'redux-color-picker-css' );

                wp_enqueue_style(
                    'redux-field-link_color-js',
                    ReduxCore::$_url . 'inc/fields/link_color/field_link_color.css',
                    array(),
                    $this->timestamp,
                    'all'
                );
            }
        }
        
        public function css_style($data) {
            $style = array();

            if ( ! empty( $this->value['regular'] ) && $this->field['regular'] === true && $this->field['default']['regular'] !== false ) {
                $style[] = 'color:' . $this->value['regular'] . ';';
            }

            if ( ! empty( $this->value['visited'] ) && $this->field['visited'] === true && $this->field['default']['visited'] !== false ) {
                $style['visited'] = 'color:' . $this->value['visited'] . ';';
            }

            if ( ! empty( $this->value['hover'] ) && $this->field['hover'] === true && $this->field['default']['hover'] !== false ) {
                $style['hover'] = 'color:' . $this->value['hover'] . ';';
            }

            if ( ! empty( $this->value['active'] ) && $this->field['active'] === true && $this->field['default']['active'] !== false ) {
                $style['active'] = 'color:' . $this->value['active'] . ';';
            }

            if ( ! empty( $this->value['focus'] ) && $this->field['focus'] === true && $this->field['default']['focus'] !== false ) {
                $style['focus'] = 'color:' . $this->value['focus'] . ';';
            }
            
            return $style;
        }

        public function output($style = '') {
            if ( !empty($style) ) {
                if ( ! empty( $this->field['output'] ) && is_array( $this->field['output'] ) ) {
                    $styleString = "";

                    foreach ( $style as $key => $value ) {
                        if ( is_numeric( $key ) ) {
                            $styleString .= implode( ",", $this->field['output'] ) . "{" . $value . '}';
                        } else {
                            if ( count( $this->field['output'] ) == 1 ) {
                                foreach($this->field['output'] as $sel => $elem) {
                                    continue;
                                }
                                
                                if (strpos($elem, ',') != false) {
                                    $selector_arr = explode(',',$elem);
                                    $sel_list = '';
                                    
                                    foreach($selector_arr as $idx => $selector) {
                                        $sel_list .= $selector . ":" . $key . ",";
                                    }
                                    
                                    $sel_list = rtrim($sel_list,',');
                                    $styleString .= $sel_list . "{" . $value . '}';
                                } else {
                                    $styleString .= $elem . ":" . $key . "{" . $value . '}';
                                }
                            } else {
                                $blah = '';
                                foreach($this->field['output'] as $k => $sel) {
                                    $blah .= $sel . ':' . $key . ',';
                                }

                                $blah = substr($blah, 0, strlen($blah) - 1);
                                $styleString .= $blah . '{' . $value . '}';

                            }
                        }
                    }

                    $this->parent->outputCSS .= $styleString;
                }

                if ( ! empty( $this->field['compiler'] ) && is_array( $this->field['compiler'] ) ) {
                    $styleString = "";

                    foreach ( $style as $key => $value ) {
                        if ( is_numeric( $key ) ) {
                            $styleString .= implode( ",", $this->field['compiler'] ) . "{" . $value . '}';

                        } else {
                            if ( count( $this->field['compiler'] ) == 1 ) {
                                $styleString .= $this->field['compiler'][0] . ":" . $key . "{" . $value . '}';
                            } else {
                                $blah = '';
                                foreach($this->field['compiler'] as $k => $sel) {
                                    $blah .= $sel . ':' . $key . ',';
                                }

                                $blah = substr($blah, 0, strlen($blah) - 1);
                                $styleString .= $blah . '{' . $value . '}';
                            }
                        }
                    }
                    $this->parent->compilerCSS .= esc_attr($styleString);
                }
            }
        }
    }
}