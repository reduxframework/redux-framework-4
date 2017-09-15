<?php

if ( !defined ( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Redux_Validation_html_custom' ) ) {
    class Redux_Validation_html_custom extends Redux_Validate {

        /**
         * Field Render Function.
         * Takes the vars and validates them
         *
         * @since ReduxFramework 1.0.0
         */
        function validate() {
            $this->field['msg'] = ( isset( $this->field['msg'] ) ) ? $this->field['msg'] : esc_html__( 'Unallowed HTML was found in this field and has been removed.', 'redux-framework' );

            if (isset($this->field['allowed_html'])) {
                $html = wp_kses( $this->value, $this->field['allowed_html'] );

                if ($html != $this->value) {
                    $this->field['current'] = $html;
                    $this->warning = $this->field;
                }
                
                $this->value = $html;
            } else {
                $this->value = $this->value;
            }
        }
    }
}