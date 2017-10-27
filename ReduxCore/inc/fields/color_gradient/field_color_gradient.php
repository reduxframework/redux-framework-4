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
                'preview'           => false,
                'preview_height'    => '150px',
                );

            $this->field = wp_parse_args( $this->field, $defaults );
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
            echo '<div class="colorGradient">';
            echo    '<strong>' . esc_html__( 'From ', 'redux-framework' ) . '</strong>&nbsp;';
            echo
                    '<input
                        data-id="' . esc_attr($this->field['id']) . '"
                        id="' . esc_attr($this->field['id']) . '-from"
                        name="' . esc_attr($this->field['name'] . $this->field['name_suffix']) . '[from]' . '"
                        value="' . esc_attr($this->value['from']) . '"
                        class="color-picker redux-color redux-color-init ' . esc_attr($this->field['class']) . '"
                        type="text"
                        data-default-color="' . esc_attr($this->field['default']['from']) . '"
                        />';

            echo '<input type="hidden" class="redux-saved-color" id="' . esc_attr($this->field['id']) . '-saved-color' . '" value="">';

            if ( ! isset( $this->field['transparent'] ) || $this->field['transparent'] !== false ) {
                $tChecked = "";

                if ( $this->value['from'] == "transparent" ) {
                    $tChecked = ' checked="checked"';
                }

                echo '<label for="' . esc_attr($this->field['id']) . '-from-transparency" class="color-transparency-check">';
                echo      '<input type="checkbox" class="checkbox color-transparency ' . esc_attr($this->field['class']) . '" id="' . esc_attr($this->field['id']) . '-from-transparency" data-id="' . esc_attr($this->field['id']) . '-from" value="1"' . $tChecked . '> ' . esc_html__( 'Transparent', 'redux-framework' );
                echo '</label>';
            }
            echo "</div>";

            echo '<div class="colorGradient toLabel">';
            echo    '<strong>' . esc_html__( 'To ', 'redux-framework' ) . '</strong>&nbsp;';
            echo
                    '<input
                        data-id="' . esc_attr($this->field['id']) . '"
                        id="' . esc_attr($this->field['id']) . '-to"
                        name="' . esc_attr($this->field['name'] . $this->field['name_suffix']) . '[to]' . '"
                        value="' . esc_attr($this->value['to']) . '"
                        class="color-picker redux-color redux-color-init ' . esc_attr($this->field['class']) . '"
                        type="text"
                        data-default-color="' . esc_attr($this->field['default']['to']) . '"
                        />';

            if ( ! isset( $this->field['transparent'] ) || $this->field['transparent'] !== false ) {
                $tChecked = "";

                if ( $this->value['to'] == "transparent" ) {
                    $tChecked = ' checked="checked"';
                }

                echo '<label for="' . esc_attr($this->field['id']) . '-to-transparency" class="color-transparency-check">';
                echo     '<input type="checkbox" class="checkbox color-transparency" id="' . esc_attr($this->field['id']) . '-to-transparency" data-id="' . esc_attr($this->field['id']) . '-to" value="1"' . $tChecked . '> ' . esc_html__( 'Transparent', 'redux-framework' );
                echo '</label>';
            }

            echo "</div>";

            $css = '';
            if ( $this->field['preview'] == false ){
                $css .= 'display:none;';
            }

            $css .= $this->css_style($this->value);

            $css .= "height: " . $this->field['preview_height'] . ";";

            echo '<div class="redux-gradient-preview" style="' . esc_attr($css) . '"></div>';

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
            if (!wp_style_is('redux-nouislider-css') || $this->field['gradient-reach'] || $this->field['gradient-angle']) {
                wp_enqueue_style(
                    'redux-nouislider-css',
                    ReduxCore::$_url . 'assets/css/vendor/nouislider' . Redux_Functions::isMin() . '.css',
                    array(),
                    '5.0.0',
                    'all'
                );

                wp_register_script(
                    'redux-nouislider-js',
                    ReduxCore::$_url . 'assets/js/vendor/nouislider/redux.jquery.nouislider' . Redux_Functions::isMin() . '.js',
                    array( 'jquery' ),
                    '5.0.0',
                    true
                );
            }

            $dep_array = array( 'jquery', 'wp-color-picker', 'redux-js' );
            
            if ($this->field['gradient-type']) {
                if (!wp_style_is('select2-css')) {
                    wp_enqueue_style( 'select2-css' );
                }
                
                $dep_array[] = 'select2-js';
            }
            
            wp_enqueue_style( 'wp-color-picker' );

            wp_enqueue_script(
                'redux-field-color-gradient-js',
                ReduxCore::$_url . 'inc/fields/color_gradient/field_color_gradient' . Redux_Functions::isMin() . '.js',
                $dep_array,
                ReduxCore::$_version,
                true
            );

            if ($this->parent->args['dev_mode']) {
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

        }
}