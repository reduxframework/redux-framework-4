<?php

if ( !defined ( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Redux_Validation_not_empty' ) ) {
    
    class Redux_Validation_not_empty extends Redux_Validate {

        /**
         * Field Validation Function.
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @since ReduxFramework 1.0.0
         */
        function validate() {
            $this->field['msg'] = ( isset( $this->field['msg'] ) ) ? $this->field['msg'] : esc_html__( 'This field cannot be empty. Please provide a value.', 'redux-framework' );

            if ( ! isset( $this->value ) || $this->value == '' || strlen($this->value) == 0 ) {
                $this->error = $this->field;
            }
        }
    }
}