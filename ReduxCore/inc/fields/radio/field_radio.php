<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'ReduxFramework_radio' ) ) {
    
    class ReduxFramework_radio extends Redux_Field {

        /**
         * Field Render Function.
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @since ReduxFramework 1.0.0
         */
        function render() {
            if ( ! empty( $this->field['data'] ) && empty( $this->field['options'] ) ) {
                if ( empty( $this->field['args'] ) ) {
                    $this->field['args'] = array();
                }
                $this->field['options'] = $this->parent->wordpress_data->get( $this->field['data'], $this->field['args'] );
            }

            $this->field['data_class'] = ( isset( $this->field['multi_layout'] ) ) ? 'data-' . $this->field['multi_layout'] : 'data-full';

            if ( ! empty( $this->field['options'] ) ) {
                echo '<ul class="' . esc_attr($this->field['data_class']) . '">';

                foreach ( $this->field['options'] as $k => $v ) {
                    echo '<li>';
                    echo '<label for="' . esc_attr($this->field['id'] . '_' . array_search( $k, array_keys( $this->field['options'] ) ) ) . '">';
                    echo '<input type="radio" class="radio ' . esc_attr($this->field['class']) . '" id="' . ($this->field['id'] . '_' . array_search( $k, array_keys( $this->field['options'] ) ) ) . '" name="' . esc_attr($this->field['name'] . $this->field['name_suffix']) . '" value="' . esc_attr($k) . '" ' . checked( $this->value, $k, false ) . '/>';
                    echo ' <span>' . esc_html($v) . '</span>';
                    echo '</label>';
                    echo '</li>';
                }

                echo '</ul>';
            }
        }
    }
}