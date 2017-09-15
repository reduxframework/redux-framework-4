<?php

if ( !defined ( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Redux_Validation_numeric' ) ) {
    
    class Redux_Validation_numeric extends Redux_Validate {

        /**
         * Field Render Function.
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @since ReduxFramework 1.0.0
         */
        function validate() {
            $this->field['msg'] = ( isset( $this->field['msg'] ) ) ? $this->field['msg'] : esc_html__( 'You must provide a numerical value for this option.', 'redux-framework' );

            if ( ! is_numeric( $this->value ) ) {
                $this->value = ( isset( $this->current ) ) ? $this->current : '';
                $this->field['current'] = $this->value;

                $this->error = $this->field;
            }
        }
    }
}