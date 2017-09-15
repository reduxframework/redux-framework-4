<?php

if ( !defined ( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Redux_Validation_url' ) ) {
    
    class Redux_Validation_url extends Redux_Validate {

        /**
         * Field Render Function.
         * Takes the vars and validates them
         *
         * @since ReduxFramework 1.0.0
         */
        function validate() {
            $this->field['msg'] = ( isset( $this->field['msg'] ) ) ? $this->field['msg'] : esc_html__( 'You must provide a valid URL for this option.', 'redux-framework' );

            if ( filter_var( $this->value, FILTER_VALIDATE_URL ) == false ) {
                $this->value = ( isset( $this->current ) ) ? $this->current : '';
                $this->field['current'] = $this->value;

                $this->error = $this->field;
            } else {
                $this->value = esc_url_raw( $this->value );
            }
        }
    }
}