<?php

if ( !defined ( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Redux_Validation_comma_numeric' ) ) {
    
    class Redux_Validation_comma_numeric extends Redux_Validate {

        /**
         * Field Validation Function.
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @since ReduxFramework 1.0.0
         */
        function validate() {
            $this->field['msg'] = ( isset( $this->field['msg'] ) ) ? $this->field['msg'] : esc_html__( 'You must provide a comma separated list of numerical values for this option.', 'redux-framework' );

            if ( ! is_numeric( str_replace( ',', '', $this->value ) ) || strpos ( $this->value, ',' ) == false ) {
                $this->value = ( isset( $this->current ) ) ? $this->current : '';
                $this->field['current'] = $this->value;
                
                $this->error = $this->field;
            }
        }
    }
}