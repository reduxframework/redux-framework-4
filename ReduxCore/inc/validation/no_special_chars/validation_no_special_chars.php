<?php

if ( !defined ( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Redux_Validation_no_special_chars' ) ) {
    class Redux_Validation_no_special_chars extends Redux_Validate {

        /**
         * Field Render Function.
         * Takes the vars and validates them
         *
         * @since ReduxFramework 1.0.0
         */
        function validate() {
            $this->field['msg'] = ( isset( $this->field['msg'] ) ) ? $this->field['msg'] : esc_html__( 'You must not enter any special characters in this field, all special characters have been removed.', 'redux-framework' );

            if ( ! preg_match( '/[^a-zA-Z0-9_ -]/s', $this->value ) == 0 ) {
                $this->field['current'] = $this->current;
                
                $this->warning = $this->field;
            }

            $this->value = preg_replace( '/[^a-zA-Z0-9_ -]/s', '', $this->value );
        }
    }
}