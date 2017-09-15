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
 * @subpackage  Field_Color
 * @author      Daniel J Griffiths (Ghost1227)
 * @author      Dovy Paukstys
 * @version     3.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Don't duplicate me!
if ( ! class_exists( 'ReduxFramework_color' ) ) {

    /**
     * Main ReduxFramework_color class
     *
     * @since       1.0.0
     */
    class ReduxFramework_color extends Redux_Field {

        public function set_defaults() {
            }
        
        /**
         * Field Render Function.
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @since         1.0.0
         * @access        public
         * @return        void
         */
        public function render() {
            echo '<input data-id="' . esc_attr($this->field['id']) . '"
                         name="' . esc_attr($this->field['name'] . $this->field['name_suffix']) . '"
                         id="' . esc_attr($this->field['id']) . '-color"
                         class="color-picker redux-color redux-color-init ' . esc_attr($this->field['class']) . '"
                         type="text" value="' . esc_attr($this->value) . '"
                         data-oldcolor=""
                         data-default-color="' . ( isset( $this->field['default'] ) ? esc_attr($this->field['default']) : "" ) . '"
                    />';
            
            echo '<input type="hidden" class="redux-saved-color" id="' . esc_attr($this->field['id']) . '-saved-color' . '" value="">';

            if ( ! isset( $this->field['transparent'] ) || $this->field['transparent'] !== false ) {
                $tChecked = "";

                if ( $this->value == "transparent" ) {
                    $tChecked = ' checked="checked"';
                }

                echo '<label for="' . esc_attr($this->field['id']) . '-transparency" class="color-transparency-check"><input type="checkbox" class="checkbox color-transparency ' . esc_attr($this->field['class']) . '" id="' . esc_attr($this->field['id']) . '-transparency" data-id="' . esc_attr($this->field['id']) . '-color" value="1"' . $tChecked . '> ' . esc_html__( 'Transparent', 'redux-framework' ) . '</label>';
            }
        }

        /**
         * Enqueue Function.
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         *
         * @since         1.0.0
         * @access        public
         * @return        void
         */
        public function enqueue() {
            if ($this->parent->args['dev_mode']) {
                wp_enqueue_style( 'redux-color-picker-css' );
            }

            wp_enqueue_style( 'wp-color-picker' );
            
            $dep_array = array( 'jquery', 'wp-color-picker', 'redux-js' );
            
            wp_enqueue_script(
                'redux-field-color-js',
                ReduxCore::$_url . 'inc/fields/color/field_color' . Redux_Functions::isMin() . '.js',
                $dep_array,
                ReduxCore::$_version,
                true
            );
        }

        public function output($style = '') {
            if ( ! empty( $this->value ) ) {
                $mode = ( isset( $this->field['mode'] ) && ! empty( $this->field['mode'] ) ? $this->field['mode'] : 'color' );

                $style .= $mode . ':' . $this->value . ';';

                if ( ! empty( $this->field['output'] ) && is_array( $this->field['output'] ) ) {
                    $css = Redux_Functions::parseCSS( $this->field['output'], $style, $this->value );
                    $this->parent->outputCSS .= $css;
                }

                if ( ! empty( $this->field['compiler'] ) && is_array( $this->field['compiler'] ) ) {
                    $css = Redux_Functions::parseCSS( $this->field['compiler'], $style, $this->value );
                    $this->parent->compilerCSS .= $css;

                }
            }
        }
    }
}