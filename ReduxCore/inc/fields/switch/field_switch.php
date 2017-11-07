<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'ReduxFramework_switch' ) ) {
    
    class ReduxFramework_switch extends Redux_Field {

        /**
         * Field Render Function.
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @since ReduxFramework 0.0.4
         */
        function render() {
            $cb_enabled = $cb_disabled = ''; //no errors, please

            //Get selected
            if ( (int) $this->value == 1 ) {
                $cb_enabled = ' selected';
            } else {
                $cb_disabled = ' selected';
            }

            //Label ON
            $this->field['on'] = isset( $this->field['on'] ) ? $this->field['on'] : esc_html__( 'On', 'redux-framework' );

            //Label OFF
            $this->field['off'] = isset( $this->field['off'] ) ? $this->field['off'] : esc_html__( 'Off', 'redux-framework' );

            echo '<div class="switch-options">';
            echo '<label class="cb-enable' . $cb_enabled . '" data-id="' . esc_attr($this->field['id']) . '"><span>' . esc_html($this->field['on']) . '</span></label>';
            echo '<label class="cb-disable' . $cb_disabled . '" data-id="' . esc_attr($this->field['id']) . '"><span>' . esc_html($this->field['off']) . '</span></label>';
            echo '<input type="hidden" class="checkbox checkbox-input ' . esc_attr($this->field['class']) . '" id="' . esc_attr($this->field['id']) . '" name="' . esc_attr($this->field['name'] . $this->field['name_suffix']) . '" value="' . esc_attr($this->value) . '" />';
            echo '</div>';
        }

        /**
         * Enqueue Function.
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         *
         * @since ReduxFramework 0.0.4
         */
        function enqueue() {
            wp_enqueue_script(
                'redux-field-switch-js',
                ReduxCore::$_url . 'inc/fields/switch/field_switch' . Redux_Functions::isMin() . '.js',
                array( 'jquery', 'redux-js' ),
                $this->timestamp,
                true
            );

            if ($this->parent->args['dev_mode']) {
                wp_enqueue_style(
                    'redux-field-switch-css',
                    ReduxCore::$_url . 'inc/fields/switch/field_switch.css',
                    array(),
                    $this->timestamp,
                    'all'
                );
            }
        }
    }
}