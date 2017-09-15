<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'ReduxFramework_text' ) ) {
    
    class ReduxFramework_text extends Redux_Field {

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
                $this->field['class'] .= " hasOptions ";
            }

            if ( empty( $this->value ) && ! empty( $this->field['data'] ) && ! empty( $this->field['options'] ) ) {
                $this->value = $this->field['options'];
            }

            $qtip_title = isset( $this->field['text_hint']['title'] ) ? 'qtip-title="' . $this->field['text_hint']['title'] . '" ' : '';
            $qtip_text  = isset( $this->field['text_hint']['content'] ) ? 'qtip-content="' . $this->field['text_hint']['content'] . '" ' : '';

            $readonly       = ( isset( $this->field['readonly'] ) && $this->field['readonly']) ? ' readonly="readonly"' : '';
            $autocomplete   = ( isset($this->field['autocomplete']) && $this->field['autocomplete'] == false) ? ' autocomplete="off"' : ''; 

            if ( isset( $this->field['options'] ) && ! empty( $this->field['options'] ) ) {
                $placeholder = '';
                
                if ( isset( $this->field['placeholder'] ) ) {
                    $placeholder = $this->field['placeholder'];
                }                    

                foreach ( $this->field['options'] as $k => $v ) {
                    if ( ! empty( $placeholder ) ) {
                        $placeholder = ( is_array( $this->field['placeholder'] ) && isset( $this->field['placeholder'][ $k ] ) ) ? ' placeholder="' . esc_attr( $this->field['placeholder'][ $k ] ) . '" ' : '';
                    }

                    echo '<div class="input_wrapper">';
                    echo '<label for="' . esc_attr($this->field['id'] . '-text-' . $k) . '">' . esc_html($v) . '</label> ';
                    echo '<input ' . esc_attr($qtip_title) . esc_attr($qtip_text) . 'type="text" id="' . esc_attr($this->field['id'] . '-text-' . $k) . '" name="' . esc_attr($this->field['name'] . $this->field['name_suffix'] . '[' . $k) . ']' . '" ' . esc_attr($placeholder) . 'value="' . esc_attr( $this->value[ $k ] ) . '" class="regular-text ' . esc_attr($this->field['class']) . '"' . $readonly . $autocomplete . ' /><br />';
                    echo '</div>';
                }
            } else {
                $placeholder = ( isset( $this->field['placeholder'] ) && ! is_array( $this->field['placeholder'] ) ) ? ' placeholder="' . esc_attr( $this->field['placeholder'] ) . '" ' : '';
                
                echo '<input ' . esc_attr($qtip_title) . esc_attr($qtip_text) . 'type="text" id="' . esc_attr($this->field['id']) . '" name="' . esc_attr($this->field['name'] . $this->field['name_suffix']) . '" ' . esc_attr($placeholder) . 'value="' . esc_attr( $this->value ) . '" class="regular-text ' . esc_attr($this->field['class']) . '"' . $readonly . $autocomplete . ' />';
            }
        }

        /**
         * Enqueue Function.
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         *
         * @since ReduxFramework 3.0.0
         */
        function enqueue() {
            if ($this->parent->args['dev_mode']) {
                wp_enqueue_style(
                    'redux-field-text-css',
                    ReduxCore::$_url . 'inc/fields/text/field_text.css',
                    array(),
                    time(),
                    'all'
                );
            }
        }
    }
}