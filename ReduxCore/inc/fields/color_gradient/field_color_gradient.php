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
 * @subpackage  Field_Color_Gradient
 * @author      Kevin Provance (kprovance)
 * @author      Dovy Paukstys
 * @version     4.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Don't duplicate me!
if ( ! class_exists( 'ReduxFramework_color_gradient' ) ) {

    /**
     * Main ReduxFramework_color_gradient class
     *
     * @since       1.0.0
     */
    class ReduxFramework_color_gradient extends Redux_Field {

        public function set_defaults() {
            // No errors please
            $defaults = array(
                'from' => '',
                'to'   => '',
            );

            $this->value = Redux_Functions::parse_args( $this->value, $defaults );

            $defaults = array(
                'preview'        => false,
                'preview_height' => '150px',
            );

            $this->field = wp_parse_args( $this->field, $defaults );

            if (ReduxCore::$_pro_loaded) {
                $this->field = apply_filters('redux/pro/color_gradient/field/set_defaults', $this->field);
                $this->value = apply_filters('redux/pro/color_gradient/value/set_defaults', $this->value);
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
            if (ReduxCore::$_pro_loaded) {
                echo apply_filters('redux/pro/color_gradient/render/gradient_type', null);
            }

            echo '<div class="colorGradient">';
            echo '<strong>' . esc_html__( 'From ', 'redux-framework' ) . '</strong>&nbsp;';
            echo '<input ';
            echo     'data-id="' . esc_attr( $this->field['id'] ) . '"';
            echo     'id="' . esc_attr( $this->field['id'] ) . '-from"';
            echo     'name="' . esc_attr( $this->field['name'] . $this->field['name_suffix'] ) . '[from]' . '"';
            echo     'value="' . esc_attr( $this->value['from'] ) . '"';
            echo     'class="color-picker redux-color redux-color-init ' . esc_attr( $this->field['class'] ) . '"';
            echo     'type="text"';
            echo     'data-default-color="' . esc_attr( $this->field['default']['from'] ) . '"';

            if (ReduxCore::$_pro_loaded) {
                $data = array(
                    'field' => $this->field,
                    'index' => 'from'
                );

                echo apply_filters('redux/pro/render/color_alpha', $data);
            }

            echo '/>';

            echo '<input type="hidden" class="redux-saved-color" id="' . esc_attr( $this->field['id'] ) . '-saved-color' . '" value="">';

            if ( ! isset( $this->field['transparent'] ) || $this->field['transparent'] !== false ) {
                $tChecked = "";

                if ( $this->value['from'] == "transparent" ) {
                    $tChecked = ' checked="checked"';
                }

                echo '<label for="' . esc_attr( $this->field['id'] ) . '-from-transparency" class="color-transparency-check">';
                echo '<input type="checkbox" class="checkbox color-transparency ' . esc_attr( $this->field['class'] ) . '" id="' . esc_attr( $this->field['id'] ) . '-from-transparency" data-id="' . esc_attr( $this->field['id'] ) . '-from" value="1"' . $tChecked . '> ' . esc_html__( 'Transparent', 'redux-framework' );
                echo '</label>';
            }
            echo "</div>";

            echo '<div class="colorGradient toLabel">';
            echo '<strong>' . esc_html__( 'To ', 'redux-framework' ) . '</strong>&nbsp;';
            echo '<input ';
            echo     'data-id="' . esc_attr( $this->field['id'] ) . '"';
            echo     'id="' . esc_attr( $this->field['id'] ) . '-to"';
            echo     'name="' . esc_attr( $this->field['name'] . $this->field['name_suffix'] ) . '[to]' . '"';
            echo     'value="' . esc_attr( $this->value['to'] ) . '"';
            echo     'class="color-picker redux-color redux-color-init ' . esc_attr( $this->field['class'] ) . '"';
            echo     'type="text"';
            echo     'data-default-color="' . esc_attr( $this->field['default']['to'] ) . '"';

            if (ReduxCore::$_pro_loaded) {
                $data = array(
                    'field' => $this->field,
                    'index' => 'to'
                );

                echo apply_filters('redux/pro/render/color_alpha', $data);
            }                

            echo '/>';

            if ( ! isset( $this->field['transparent'] ) || $this->field['transparent'] !== false ) {
                $tChecked = "";

                if ( $this->value['to'] == "transparent" ) {
                    $tChecked = ' checked="checked"';
                }

                echo '<label for="' . esc_attr( $this->field['id'] ) . '-to-transparency" class="color-transparency-check">';
                echo '<input type="checkbox" class="checkbox color-transparency" id="' . esc_attr( $this->field['id'] ) . '-to-transparency" data-id="' . esc_attr( $this->field['id'] ) . '-to" value="1"' . $tChecked . '> ' . esc_html__( 'Transparent', 'redux-framework' );
                echo '</label>';
            }

            echo "</div>";

            if (ReduxCore::$_pro_loaded) {
                echo apply_filters('redux/pro/color_gradient/render/preview', null);
                echo apply_filters('redux/pro/color_gradient/render/extra_inputs', null);
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

            wp_enqueue_script(
                'redux-field-color-gradient-js',
                ReduxCore::$_url . 'inc/fields/color_gradient/field_color_gradient' . Redux_Functions::isMin() . '.js',
                array( 'jquery', 'wp-color-picker', 'redux-js' ),
                ReduxCore::$_version,
                true
            );

            if (ReduxCore::$_pro_loaded) {
                do_action ('redux/pro/color_gradient/enqueue');
            }

            if ( $this->parent->args['dev_mode'] ) {
                wp_enqueue_style( 'redux-color-picker-css' );

                wp_enqueue_style(
                    'redux-field-color_gradient-css',
                    ReduxCore::$_url . 'inc/fields/color_gradient/field_color_gradient.css',
                    array(),
                    time(),
                    'all'
                );
            }
        }
        
        public function css_style($data) {
            
            if (ReduxCore::$_pro_loaded) {
                $pro_data = apply_filters('redux/pro/color_gradient/output', $data);

                return $pro_data;
            }            
        }
    }
}